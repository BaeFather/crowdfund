<?
$sub_menu = "500100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql_common = " FROM {$g5['member_table']} ";

$sql_search = " WHERE 1=1 ";
$sql_search.= " AND mb_level BETWEEN 1 AND 8";
$sql_search.= " AND member_group='F'";

$field   = trim($_REQUEST['field']);
$keyword = trim($_REQUEST['keyword']);

if($field && $keyword) {
	if( $field == 'mb_no') {
		if( preg_match("/\,/", $keyword) ) {
			$sql_search.= " AND ".$field." IN(".preg_replace("/( |')/", "", $keyword).")";
		}
		else {
			$sql_search.= " AND ".$field." = '".$keyword."'";
		}
	}
	else if(in_array($field, array('mb_id','mb_email'))) {
		$sql_search.= " AND ".$field." = '".$keyword."'";
	}
	else if($field=='mb_hp') {
		$sql_search.= " AND ".$field." = '".masterEncrypt($keyword, false)."'";
	}
	else {
		$sql_search.= " AND ".$field." LIKE '%".$keyword."%' ";
	}
}


if( trim($_GET['syndi_id']) ) {
	$ARR_KEYS = array_keys($CONF['SYNDICATOR']);
	for($i=0; $i<count($CONF['SYNDICATOR']); $i++) {
		if( $ARR_KEYS[$i]==trim($_GET['syndi_id']) ) {
			$sql_search.= ($ARR_KEYS[$i]=='hktvwowstar') ? " AND wowstar_userid!=''" : " AND {$ARR_KEYS[$i]}_userid!=''";
			break;
		}
	}
}


$search_field = array('member_type','mb_mailling','mb_sms','member_group');
foreach ($_GET as $key => $val) {
	$sql_search.= (in_array($key, $search_field) && trim($val)) ? " AND {$key}='".trim($val)."'" : "";
}

if($_GET['mb_datetime_start'] && $_GET['mb_datetime_end']) {
	$sql_search.= " AND LEFT(mb_datetime, 10) BETWEEN '".$_GET['mb_datetime_start']."' AND '".$_GET['mb_datetime_end']."'";
}
else {
	if($_GET['mb_datetime_start']) $sql_search.= " AND LEFT(mb_datetime, 10)>='".$_GET['mb_datetime_start']."'";
	if($_GET['mb_datetime_end'])   $sql_search.= " AND LEFT(mb_datetime, 10)<='".$_GET['mb_datetime_end']."'";
}

if($_GET['mb_point_start'] && $_GET['mb_point_end']) {
	$sql_search.= " AND mb_point BETWEEN '".$_GET['mb_point_start']."' AND '".$_GET['mb_point_end']."'";
}
else {
	if($_GET['mb_point_start']) $sql_search.= " AND mb_point>='".$_GET['mb_point_start']."'";
	if($_GET['mb_point_end'])   $sql_search.= " AND mb_point<='".$_GET['mb_point_end']."'";
}

$sql_order = " ORDER BY mb_point DESC ";

$sql = "SELECT COUNT(mb_no) AS cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 50;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$total = array();

$sql = "SELECT SUM(mb_point) AS sum {$sql_common} {$sql_search}";
$row = sql_fetch($sql);
$total['예치금'] = $row['sum'];



$sql = "
	SELECT
		*,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=mb_no AND invest_state IN ('Y','R')) AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE member_idx=mb_no AND invest_state IN ('Y','R')) AS invest_amount
	{$sql_common}
	{$sql_search}
	{$sql_order}
LIMIT
	$from_record, $rows";
//echo '<pre>'.$sql.'</pre>';
$result = sql_query($sql);

$param = array();
foreach ((array)$_REQUEST as $key => $val) {
	if (empty($val) || in_array($key, array('page'))) {
		continue;
	}
	$param[] = $key.'='.$val;
}

$qstr = join('&amp;', $param);


$g5['title'] = '예치금관리';
include_once('./admin.head.php');

?>

<div class="row">
	<div class="col-lg-12">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading text-center">검색설정</div>
				<div class="panel-body" style="height:140px">
	        <form method="get" class="form-horizontal">
						<ul class="list-inline col-sm-12">
							<li>예치금</li>
							<li>
								<div class="form-inline">
									<input type="number" name="mb_point_start" value="<?=$_GET['mb_point_start']?>" class="form-control input-sm" placeholder="0"> ~
									<input type="number" name="mb_point_end" value="<?=$_GET['mb_point_end']?>" class="form-control input-sm" placeholder="10,000,000">
								</div>
							</li>
						</ul>
						<ul class="list-inline col-sm-12">
							<li>
								<select name="member_type" class="form-control input-sm">
									<option value="">::회원구분::</option>
									<option value="1" <?=($_GET['member_type'] == '1') ? 'selected' : ''; ?>>개인회원</option>
									<option value="2" <?=($_GET['member_type'] == '2') ? 'selected' : ''; ?>>법인회원</option>
									<option value="3" <?=($_GET['member_type'] == '3') ? 'selected' : ''; ?>>SNS회원</option>
								</select>
							</li>
							<li>
								<select name="syndi_id" class="form-control input-sm">
									<option value="">::신디케이션회원::</option>
<?
$ARR_KEYS = array_keys($CONF['SYNDICATOR']);
for($i=0; $i<count($CONF['SYNDICATOR']); $i++) {
?>
									<option value="<?=$ARR_KEYS[$i]?>" <?=($ARR_KEYS[$i]==$_GET['syndi_id'])?'selected':'';?>><?=$CONF['SYNDICATOR'][$ARR_KEYS[$i]]['name']?></option>
<?
}
?>
								</select>
							</li>
						</ul>
						<ul class="list-inline col-sm-12">
							<li>
								<select name="field" class="form-control input-sm">
									<option value="">필드선택</option>
									<option value="mb_no" <?=($_GET['field'] == 'mb_no') ? 'selected' : ''; ?>>회원번호</option>
									<option value="mb_id" <?=($_GET['field'] == 'mb_id') ? 'selected' : ''; ?>>아이디</option>
									<option value="mb_name" <?=($_GET['field'] == 'mb_name') ? 'selected' : ''; ?>>성명</option>
									<option value="mb_co_name" <?=($_GET['field'] == 'mb_co_name') ? 'selected' : ''; ?>>법인명</option>
									<option value="mb_email" <?=($_GET['field'] == 'mb_email') ? 'selected' : ''; ?>>이메일</option>
									<option value="mb_hp" <?=($_GET['field'] == 'mb_hp') ? 'selected' : ''; ?>>휴대폰</option>
								</select>
							</li>
							<li>
								<input type="text" name="keyword" value="<?=$_GET['keyword']?>" class="form-control input-sm">
							</li>
							<li>
								<button type="submit" class="btn btn-primary btn-sm" style="margin-top:-2px;">검색</button>
							</li>
						</ul>
					</form>
				</div>
			</div>
		</div>

		<form name="point_form" id="point_form" class="form-horizontal">
		<div class="col-sm-6 text-center">
			<div class="panel panel-default">
				<div class="panel-heading">예치금 보정</div>
				<div class="panel-body" style="height:140px">
					<ul class="list-inline">
						<li>
							<select id="target_range" name="target_range" class="form-control input-sm" required>
								<option value="">:: 처리 대상 ::</option>
								<option value="3">선택한 회원</option>
								<!--<option value="1">전체 회원</option>-->
							</select>
						</li>
						<li>
							<div class="form-inline">
								<input type="text" id="amount" name="amount" class="form-control input-sm" required style="text-align:right;width:150px;" onKeyUp="onlyDigit(this);"> 원
							</div>
						</li>
						<li>
							<select id="proc" name="proc" class="form-control input-sm" required>
								<option value="">::지급/차감 선택::</option>
								<option value="charge">지급</option>
								<option value="discharge">차감</option>
							</select>
						</li>
						<li>
							<span id="submit1" class="btn btn-danger btn-sm" style="cursor:pointer;">확인</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel-body" style="clear:both">
			<ul class="list-inline col-sm-12">
				<li>등록 : <?=number_format($total_count)?>건</li>
				<li>예치금 : <?=number_format($total['예치금'])?>원</li>
				<li style="float:right"><a href="balance_detail.php"><button type="button" class="btn btn-success btn-sm">상세내역전체보기</button></a></li>
			</ul>
			<div class="dataTable_wrapper">
				<table id="dataList" class="table table-striped table-bordered table-hover table-condensed" style="font-size:12px">
					<thead>
						<tr class="bg-primary" style="font-size:13px">
							<th width="2%" class="text-center;"><input type="checkbox" id="chkall" value="1"></th>
							<th width="5%" class="text-center;">NO.</th>
							<th width="12%" class="text-center;">회원구분</th>
							<th width="12%" class="text-center;">회원번호</th>
							<th width="12%" class="text-center;">아이디</th>
							<th width="12%" class="text-center;">성명.법인명</th>
							<th width="12%" class="text-center;">예치금</th>
							<th width="12%" class="text-center;">누적투자수</th>
							<th width="12%" class="text-center;">누적투자금액</th>
							<th width="%" class="text-center;">상세내역</th>
						</tr>
					</thead>
					<tbody>
<?
for($i=0; $row=sql_fetch_array($result); $i++) {
	$list_num = $total_count - ($page - 1) * $rows;

	$print_member_type = '';
	switch($row['member_type']) {
		case '1' : $print_member_type = '개인'; $fcolor = "royalblue"; break;
		case '2' : $print_member_type = '법인'; $fcolor = "red";       break;
		case '3' : $print_member_type = 'SNS';  $fcolor = "green";     break;
	}

	$print_name = ($row['member_type']=='2') ? $row['mb_co_name'] : $row['mb_name'];

	if($row['is_creditor']=='Y') $print_member_type.= '-대부';

	if($row['mb_point'] > 0) {
		$fcolor1 = '#FF0000';
	}
	else if($row['mb_point'] == 0) {
		$fcolor1 = '#CCC';
	}
	else {
		$fcolor1 = 'brown';
	}

	$fcolor2 = ($row['invest_count'] > 0) ? '#555' : '#CCC';
	$fcolor3 = ($row['invest_amount'] > 0) ? '#555' : '#CCC';
?>
						<tr class="odd">
							<td align="center"><input type="checkbox" name="chk[]" value="<?=$row['mb_no']?>"></td>
							<td align="center"><?=($total_count - ($page - 1) * $rows - $i)?></td>
							<td align="center"><span style="color:<?=$fcolor?>"><?=$print_member_type?></span></td>
							<td align="center"><?=$row['mb_no']?></td>
							<td align="center"><a href="./member/member_view.php?&mb_id=<?=$row['mb_id']?>"><?=$row['mb_id']?></td>
							<td align="center"><?=$print_name?></td>
							<td align="right"><span style="color:<?=$fcolor1?>"><?=number_format($row['mb_point'])?>원</span></td>
							<td align="right"><span style="color:<?=$fcolor2?>"><?=number_format($row['invest_count'])?>건</span></td>
							<td align="right"><span style="color:<?=$fcolor3?>"><?=number_format($row['invest_amount'])?>원</span></td>
							<td align="center"><a href="balance_detail.php?field=mb_no&keyword=<?=$row['mb_no']?>" class="btn btn-sm btn-default">상세내역보기</a></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
		</div>
		</form>

		<!-- /.panel-body -->
		<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=')?>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$("input[id=chkall]").click(function() {
	$("input:checkbox[name='chk[]']").prop('checked', this.checked);
});

$("#submit1").on('click', function() {

	if( $('#target_range').val() == '' ) { alert('처리 대상을 선택하십시요.'); $('#target_range').focus(); return; }
	else if( $('#amount').val() == '') { alert('금액을 입력하십시요.'); $('#amount').focus(); return; }
	else if( $('#proc').val() == '') { alert('지급 또는 차감을 선택하십시요.'); $('#proc').focus(); return; }
	else {

		var proc_str = ($('#proc').val() == 'charge') ? '지급' : '차감';

		if( $('#target_range').val() == '1' ) {
			var msg = "검색 결과와 관계없이 전체회원을 대상으로「" + proc_str + "」처리 됩니다\n"
			       + "위험한 처리일 수 있으므로 실행에 신중을 기하십시요.\n"
						 + "실행하시겠습니까?";
		}
		else {
			if( $("input:checkbox[name='chk[]']").length > 0) {
				if( $("input:checkbox[name='chk[]']:checked").length > 0) {
					var msg = "선택한 회원에게 예치금을「" + proc_str + "」처리 됩니다.\n"
								  + "실행하시겠습니까?";
				}
				else {
					alert('선택된 대상이 없습니다.\n목록에서 처리할 대상을 선택하십시요.'); return;
				}
			}
			else {
				alert('결과목록이 없습니다.'); return;
			}
		}

		if( confirm(msg) ) {

			var fdata = $('#point_form').serialize();

			$.ajax({
				url : "balance_proc.php",
				type: "POST",
				dataType: "JSON",
				data: fdata,
				success:function(data) {
					if(data.result=='success') {
						alert(data.message); window.location.reload();
					}
					else {
						alert(data.message);
					}
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});

		}

	}

});

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<?
include_once ('./admin.tail.php');
?>