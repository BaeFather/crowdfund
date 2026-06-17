<?

include_once('./_common.php');

$sub_menu = "900200";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


###################################
## 이벤트 개요 설정
###################################
$_CONF['event_no']         = "1";
$_CONF['event_title']      = "픽미픽미 헬로업 - 피추천인 보상";
$_CONF['event_sub_title']  = "신규회원의 추천을 받은 회원";
$_CONF['event_sdate']      = "2016-11-29";
$_CONF['event_edate']      = "2016-12-09";
$_CONF['event_gift']       = "추천인 1인당 예치금 1000원 충전";
$_CONF['event_gift_point'] = "1000";  // $_CONF['event_gift_point'] * 추천인수 만큼 지급
$_CONF['point_title']      = "추천인 보상(".$_CONF['event_no']."차)";


$where = " 1=1 ";
$where.= " AND rec_mb_id!='' ";
$where.= " AND mb_leave_date='' ";
$where.= " AND (LEFT(mb_datetime, 10) BETWEEN '".$_CONF['event_sdate']."' AND '".$_CONF['event_edate']."') ";
$where.= " AND va_bank_code!='' ";
$where.= " AND (rec_date IS NOT NULL AND rec_date!='0000-00-00 00:00:00') ";

$sql = "
	SELECT
		rec_mb_no, rec_mb_id,
		COUNT(mb_no) AS recommend_count
	FROM
		g5_member
	WHERE
		$where
	GROUP BY
		rec_mb_no
	ORDER BY
		recommend_count DESC";
$res = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$LIST[] = sql_fetch_array($res);
}

$sql2 = "
	SELECT
		rec_mb_no, rec_mb_id,
		COUNT(mb_no) AS recommend_count
	FROM
		g5_member_drop
	WHERE
		".preg_replace('/( AND mb_leave_date=\'\')/i', '', $where)."
	GROUP BY
		rec_mb_no
	ORDER BY
		recommend_count DESC";
//echo $sql2;
$res2 = sql_query($sql2);
$rows2 = $res2->num_rows;
for($i=0; $i<$rows2; $i++) {
	$LIST[] = sql_fetch_array($res2);
}
//print_rr($LIST, 'font-size:9pt');
$list_num = count($LIST);

for($i=0; $i<$list_num; $i++) {

	$R = sql_fetch("SELECT member_type, mb_name, mb_co_name, mb_hp FROM g5_member WHERE mb_no='".$LIST[$i]['rec_mb_no']."'");
	$R['mb_hp'] = masterDecrypt($R['mb_hp'], false);

	if($LIST[$i]['member_type']=='2') {
		$LIST[$i]['rec_mb_name'] = $R['mb_co_name'];
		$LIST[$i]['rec_mb_hp']   = $R['mb_hp'];
	}
	else {
		$LIST[$i]['rec_mb_name'] = ($_SESSION['ss_accounting_admin']) ? $R['mb_name'] : hanStrMasking($R['mb_name']);
		$LIST[$i]['rec_mb_hp']   = ($_SESSION['ss_accounting_admin']) ? $R['mb_hp'] : substr($R['mb_hp'],0,strlen($R['mb_hp'])-4)."****";;
	}

	$R2 = sql_fetch("SELECT po_id, po_datetime FROM g5_point WHERE mb_id='".$LIST[$i]['rec_mb_id']."' AND po_content='".$_CONF['point_title']."'");
	$LIST[$i]['send_point']      = ($R2['po_id']) ? "Y" : "N";
	$LIST[$i]['send_point_date'] = ($R2['po_id']) ? $R2['po_datetime'] : "";

	$LIST[$i]['gift_point']   = $LIST[$i]['recommend_count'] * $_CONF['event_gift_point'];

	$TOTAL['recommend_count'] += $LIST[$i]['recommend_count'];
	$TOTAL['gift_point']      += $LIST[$i]['gift_point'];

}


$g5['title'] = $menu["menu900"][4][1];
$g5['title'].= " > " . $_CONF['event_title'];

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">
			<div style="margin:4px 0 4px 0; padding:4px 20px 4px 20px; border:1px solid #ddd; border-radius:15px; background-color:#ffebcc;">
			이벤트명 : <?=$_CONF['event_title']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			기간 : <?=preg_replace("/-/", ".", $_CONF['event_sdate'])?> ~ <?=preg_replace("/-/", ".", $_CONF['event_edate'])?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			지급대상 : <?=$_CONF['event_sub_title']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			지급상품 : <?=$_CONF['event_gift']?>
			</div>
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center"><input type="checkbox" name="chkall" value="1"></th>
							<th class="text-center">번호</th>
							<th class="text-center">아이디</th>
							<th class="text-center">성명</th>
							<th class="text-center">연락처</th>
							<th class="text-center">추천인 수</th>
							<th class="text-center">보상수익금(원)</th>
							<th class="text-center">지급처리</th>
						</tr>
					</thead>
					<tbody>
						<tr style="background:#EDF4FC">
							<td colspan="6" align="right"><?=number_format($TOTAL['recommend_count'])?></td>
							<td align="right"><?=number_format($TOTAL['gift_point'])?></td>
							<td></td>
						</tr>
<?
$list_num = count($LIST);
for($i=0,$j=1; $i<count($LIST);$i++) {

	$event_title = addSlashes($_CONF['point_title'] . ":::" . $LIST[$i]['recommend_count']);  //구분자 주의 (액션파일에서 구분자로 사용)
	$give_state = ($LIST[$i]['send_point']=='Y') ? "지급완료<br>\n".preg_replace("/-/", ".", substr($LIST[$i]['send_point_date'], 0, 16)) : "<a href='javascript:;' onClick=\"proc('{$LIST[$i]['rec_mb_no']}','{$gift_point}','{$event_title}');\" class='btn btn-warning'>지급처리</a>";

?>
						<tr class="odd">
							<td align="center"><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['rec_mb_no']?>"></td>
							<td align="center"><?=$list_num?></td>
							<td align="center"><?=$LIST[$i]['rec_mb_id']?></td>
							<td align="center"><?=$LIST[$i]['rec_mb_name']?></td>
							<td align="center"><?=$LIST[$i]['rec_mb_hp']?></td>
							<td align="right"><a href="reward_gift_send.php?field=A.rec_mb_id&keyword=<?=$LIST[$i]['rec_mb_id']?>"><?=$LIST[$i]['recommend_count']?></a></td>
							<td align="right"><?=number_format($LIST[$i]['gift_point'])?></td>
							<td align="center"><?=$give_state?></td>
						</tr>
<?
	$list_num--;
}
?>
					</tbody>
				</table>
			</div>
		</div>
		<!-- /.panel-body -->
		<div style="width:100%; text-align: center;">
			<ul class="pagination">
				<!--
				<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
				-->
			</ul>
		</div><!-- /.panel-body -->
		</form>
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

function proc(mb_no, point, content) {
	if(confirm('지급처리 하시겠습니까?')) {
		$.ajax({
			url : "ajax_reward_point_send_proc.php",
			type: "POST",
			data: { mb_no:mb_no, point:point, content:content },
			success: function(data) {
				$('#ajax_return_txt').val(data);
				if(data=='SUCCESS') {
					alert('지급 처리 되었습니다.'); document.location.reload();
				}
				else if(data=='ERROR:DUP_REQUEST') {
					alert('이미 지급 처리된 요청 입니다.');
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