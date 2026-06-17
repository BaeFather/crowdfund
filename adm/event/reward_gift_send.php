<?

include_once('./_common.php');

$sub_menu = "900100";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


###################################
## 이벤트 개요 설정
###################################
$_CONF['event_no']        = "1";
$_CONF['event_title']     = "픽미픽미 헬로업 - 추천인 보상";
$_CONF['event_sub_title'] = "추천인 아이디를 등록한 신규회원";
$_CONF['event_sdate']     = "2016-11-29";
$_CONF['event_edate']     = "2016-12-09";
$_CONF['event_gift']      = "영화티켓(에코무비)";

$field = trim($_REQUEST['field']);
$keyword = trim($_REQUEST['keyword']);

$where = " 1=1 ";
$where.= " AND A.mb_leave_date='' ";
$where.= " AND (LEFT(A.mb_datetime, 10) BETWEEN '".$_CONF['event_sdate']."' AND '".$_CONF['event_edate']."') ";
$where.= " AND A.va_bank_code!='' ";
$where.= " AND (A.rec_date IS NOT NULL AND A.rec_date!='0000-00-00 00:00:00')";
$where.= ($field && $keyword) ? " AND $field LIKE '%$keyword%' " : "";

$sql = "
	SELECT
		COUNT(A.mb_no) AS cnt_mb_no
	FROM
		g5_member A
	WHERE
		$where";
$row = sql_fetch($sql);
$total_count = $row['cnt_mb_no'];

$page_rows  = $config['cf_page_rows'];
$total_page = ceil($total_count / $page_rows);							// 전체 페이지 계산
if ($page < 1) $page = 1;																		// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;										// 시작 열을 구함

$sql = "
	SELECT
		A.mb_no, A.mb_id, A.member_type, A.mb_name, A.mb_hp, A.mb_datetime, A.rec_mb_no, A.rec_mb_id, A.rec_date,
		(SELECT coupon_no FROM event_reward_coupon WHERE mb_no=A.mb_no AND event_no='{$_CONF['event_no']}') AS coupon_no,
		(SELECT give_date FROM event_reward_coupon WHERE mb_no=A.mb_no AND event_no='{$_CONF['event_no']}') AS give_date
	FROM
		g5_member A
	WHERE
		$where
	ORDER BY
		mb_no	DESC
	LIMIT
		$from_record, $page_rows";

$res = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);
}
//print_rr($LIST, "font-size:11px");



$g5['title'] = $menu["menu900"][3][1];
$g5['title'].= " > " . $_CONF['event_title'];

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">
			<div style="margin:4px 0 20px 0; padding:4px 20px 4px 20px; border:1px solid #ddd; border-radius:15px; background-color:#ffebcc;">
			이벤트명 : <?=$_CONF['event_title']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			기간 : <?=preg_replace("/-/", ".", $_CONF['event_sdate'])?> ~ <?=preg_replace("/-/", ".", $_CONF['event_edate'])?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			지급대상 : <?=$_CONF['event_sub_title']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			지급상품 : <?=$_CONF['event_gift']?>
			</div>

			<div class="form-group">
				<form method="get" class="form-horizontal" style="margin:0">
				<ul style="list-style:none;">
					<li style="float:left;">
						<select name="field" class="form-control">
							<option value="">::선택::</option>
							<option value="A.mb_id" <?=($field=='A.mb_id')?'selected':''?>>아이디</option>
							<option value="A.mb_name"  <?=($field=='A.mb_name')?'selected':''?>>성명</option>
							<option value="A.mb_hp"  <?=($field=='A.mb_hp')?'selected':''?>>연락처</option>
							<option value="A.rec_mb_id"  <?=($field=='A.rec_mb_id')?'selected':''?>>추천인 ID</option>
						</select>
					</li>
					<li style="float:left;margin-left:4px;"><input type="text" name="keyword" value="<?=$keyword?>" class="form-control"></li>
					<li style="float:left;margin-left:4px;"><button type="submit" class="btn btn-primary">검색</button></li>
				</ul>
				</form>
			</div>
			<br><br>

			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center"><input type="checkbox" name="chkall" value="1"></th>
							<th class="text-center">NO</th>
							<th class="text-center">아이디</th>
							<th class="text-center">성명</th>
							<th class="text-center">연락처</th>
							<th class="text-center">가입일시</th>
							<th class="text-center">추천인 ID</th>
							<th class="text-center">추천확정일시</th>
							<th class="text-center">영화티켓 쿠폰</th>
							<th class="text-center">지급처리</th>
						</tr>
					</thead>
					<form id="fList">
					<tbody>
<?
$list_num = $total_count - ($page - 1) * $page_rows;
for($i=0,$j=1; $i<count($LIST);$i++) {

	if($LIST[$i]['member_type']=='2') {
		$print_mb_name = $LIST[$i]['mb_co_name'];
		$print_mb_hp   = $LIST[$i]['mb_hp'];
	}
	else {
		$print_mb_name = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_name'] : hanStrMasking($LIST[$i]['mb_name']);
		$print_mb_hp   = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_hp'] : substr($LIST[$i]['mb_hp'],0,strlen($LIST[$i]['mb_hp'])-4)."****";;
	}

	$give_state = ($LIST[$i]['coupon_no']) ? "지급완료<br>\n".preg_replace("/-/", ".", substr($LIST[$i]['give_date'], 0, 16)) : '<a href="javascript:;" onClick="proc(\''.$LIST[$i]['mb_no'].'\',\''.$_CONF['event_no'].'\');" class="btn btn-warning">지급처리</a>';

?>
						<tr class="odd">
							<td align="center"><? if(!$LIST[$i]['coupon_no']) { ?><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['mb_no']?>"><? } ?></td>
							<td align="center"><?=$list_num?></td>
							<td align="center"><?=$LIST[$i]['mb_id']?></td>
							<td align="center"><?=$print_mb_name?></td>
							<td align="center"><?=$print_mb_hp?></td>
							<td align="center"><?=preg_replace("/-/", ".", substr($LIST[$i]['mb_datetime'], 0, 16))?></td>
							<td align="center"><?=$LIST[$i]['rec_mb_id']?></td>
							<td align="center"><?=preg_replace("/-/", ".", substr($LIST[$i]['rec_date'], 0, 16))?></td>
							<td align="center"><?=$LIST[$i]['coupon_no']?></td>
							<td align="center"><?=$give_state?></td>
						</tr>
<?
	$list_num--;
}
?>
					</tbody>
					</form>
				</table>
			</div>
		</div>
		<!-- /.panel-body -->
		<div style="width:100%; text-align: center;">
			<ul class="pagination">
				<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
			</ul>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(function() {
	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

function proc(mb_no, event_no) {
	if(confirm('지급처리 하시겠습니까?')) {
		$.ajax({
			url : "ajax_reward_gift_send_proc.php",
			type: "POST",
			data: { mb_no:mb_no, event_no:event_no },
			success: function(data) {
				$('#ajax_return_txt').val(data);
				if(data=='SUCCESS') {
					alert('지급 처리 되었습니다.'); document.location.reload();
				}
				else if(data=='ERROR:DUP_REQUEST') {
					alert('이미 지급 처리된 요청 입니다.');
				}
				else if(data=='ERROR:TYPE1') {
					alert('지급 가능한 쿠폰이 부족합니다.');
				}
			},
			error: function () {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			}
		});
	}
}
</script>

<?
include_once (G5_ADMIN_PATH . '/admin.tail.php');
?>