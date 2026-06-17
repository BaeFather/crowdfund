<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once('./_common.php');

foreach($_POST as $k=>$v) { ${$k} = trim( @urldecode($v) ); }

$date_s = $sdate . ' 00:00:00';
$date_e = $edate . ' 23:59:59';

$where = "";
if($state) { $where.= " AND A.state='$state' "; }
if($sdate && $edate) {
	$where.= " AND A.regdate BETWEEN '$date_s' AND '$date_e' ";
}
else {
	if($sdate) { $where.= " AND A.regdate>='$date_s' "; }
	if($edate) { $where.= " AND A.regdate<='$date_e' "; }
}
if($field && $keyword) {
	if($field == 'mb_no') {
		if( preg_match("/\,/", $keyword) ) {
			$where.= " AND B.mb_no IN(".preg_replace("/( )/", "", $keyword).") ";
		}
		else {
			$where.= " AND B.mb_no='$keyword' ";
		}
	}
	else if($field == 'mb_id')   $where.= " AND B.mb_id LIKE '%{$keyword}%' ";
	else if($field == 'mb_name') $where.= " AND (B.mb_name LIKE '%{$keyword}%' OR B.mb_co_name LIKE '%{$keyword}%')";
	else if($field == 'mb_hp')   $where.= " AND B.mb_hp='".masterEncrypt($keyword, false)."' ";
}

$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		g5_withdrawal A
	LEFT JOIN
		g5_member B  ON A.mb_id=B.mb_id
	WHERE (1)
		$where";
//print_rr($sql);
$row = sql_fetch($sql);

$total_count = $row['cnt'];
$page_rows   = 10;
$total_page  = ceil($total_count / $page_rows);
$page        = ($page) ? $page : 1;
$from_record = ($page - 1) * $page_rows;

$sql = "
	SELECT
		A.*,
		B.member_type, B.mb_email, B.mb_name, B.mb_co_name, B.mb_hp, B.bank_code, B.bank_private_name, B.account_num
	FROM
		g5_withdrawal A
	LEFT JOIN
		g5_member B  ON A.mb_id=B.mb_id
	WHERE (1)
		$where
	ORDER BY
		A.regdate DESC,
		A.idx DESC
	LIMIT
		$from_record, $page_rows";
//echo $sql."<br><br>\n";
$res  = sql_query($sql);
$rcount = $res->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);
	$LIST[$i]['account_num'] = masterDecrypt($LIST[$i]['account_num'], false);
}

?>

<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

	<!-- 리스트 START -->
	<table class="table-striped table-hover" style="font-size:12px">
		<form id="form1" style="padding:0;">
		<colgroup>
			<col style="width:2%;">
			<col style="width:5%;">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:8%">
			<col style="width:8%">
			<col style="width:8%">
			<col style="width:9%">
			<col style="width:7%">
			<col style="width:9%">
			<col style="width:9%">
			<col style="width:">
		</colgroup>
		<thead>
		<tr align="center" style="background:#F8F8EF">
			<th scope="col" rowspan="2" style="width:2%;"><input type="checkbox" id="chkall" value="1"></th>
			<th scope="col" rowspan="2" style="text-align:center;">NO</th>
			<th scope="col" rowspan="2" style="text-align:center;">아이디</th>
			<th scope="col" rowspan="2" style="text-align:center;">성명/법인명</th>
			<th scope="col" rowspan="2" style="text-align:center;">연락처</th>
			<th scope="col" colspan="3" style="text-align:center;">입금계좌</th>
			<th scope="col" rowspan="2" style="text-align:center;">출금액</th>
			<th scope="col" rowspan="2" style="text-align:center;">상태</th>
			<th scope="col" rowspan="2" style="text-align:center;">신청일</th>
			<th scope="col" rowspan="2" style="text-align:center;">처리/수정일</th>
			<th scope="col" rowspan="2" style="text-align:center;">관리툴</th>
		</tr>
		<tr style="background:#F8F8EF">
			<th scope="col" style="text-align:center;">은행</th>
			<th scope="col" style="text-align:center;">계좌번호</th>
			<th scope="col" style="text-align:center;">예금주</th>
		</tr>
		</thead>
		<tbody>
<?
$num = $total_count - $from_record;
if($rcount > 0) {
	for($i=0; $i<$rcount; $i++) {

		$print_mb_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];

		if($LIST[$i]['member_type']=='2') {
			$print_mb_name = $LIST[$i]['mb_co_name'];
			$print_mb_hp   = $LIST[$i]['mb_hp'];
			$print_account_num = $LIST[$i]['account_num'];
		}
		else {
			$print_mb_name = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_name'] : hanStrMasking($LIST[$i]['mb_name']);
			$print_mb_hp   = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_hp'] : substr($LIST[$i]['mb_hp'],0,strlen($LIST[$i]['mb_hp'])-4)."****";;
			$print_account_num = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['account_num'] : substr($LIST[$i]['account_num'],0,strlen($LIST[$i]['account_num'])-4)."****";;
		}

		switch($LIST[$i]['state']) {
			case '1' : $state_txt = '<span style="color:#3333FF">출금 신청</span>';  break;
			case '2' : $state_txt = '출금 완료';  break;
			case '3' : $state_txt = '출금 신청 취소';  break;
			default  : $state_txt = 'Unknown';  break;
		}

		$bgcolor = ($LIST[$i]['state']==3) ? "#FFDDDD" : "";

?>
		<tr style="background:<?=$bgcolor?>" align="center">
			<td><? if($LIST[$i]['state']==1){ ?><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['idx']?>"><? } ?></td>
			<td><?=number_format($num)?></td>
			<td><a href="member/member_view.php?mb_id=<?=$LIST[$i]['mb_id']?>"><?=$LIST[$i]['mb_id']?></td>
			<td><?=$print_mb_name?></td>
			<td><?=$print_mb_hp?></td>
			<td><?=$BANK[$LIST[$i]['bank_code']]?></td>
			<td><?=$print_account_num?></td>
			<td><?=$LIST[$i]['bank_private_name']?></td>
			<td align="right"><?=number_format($LIST[$i]['req_price'])?>원</td>
			<td><?=$state_txt?></td>
			<td><?=str_replace('-','.',substr($LIST[$i]['regdate'],0,16))?></td>
			<td><?=str_replace('-','.',substr($LIST[$i]['admin_editdate'],0,16))?></td>
			<td>
				<button type="button" onClick="withdrawal_modi('<?=$LIST[$i]['idx']?>');" class="btn btn-sm btn-info">신청정보 상세보기</button>
				<!--<button type="button" onclick="withdrawal_dele('<?=$LIST[$i]['idx']?>');" class="btn btn-sm btn-danger">삭제</button>-->
			</td>
		</tr>
<?
		$num--;
	}
}else {
?>

		<tr>
			<td colspan="15" align="center" height="100px";>검색된 데이터가 없습니다.</td>
		</tr>

<?
}
?>
		<tbody>
		</form>
	</table>

	<div style="clear:both;margin:10px 0 6px;">
		<button type="button" id="submit1" class="btn btn-sm btn-warning">선택항목 출금처리</button>
	</div>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;">
<?
	paging($total_count, $page, $page_rows, 10);

//$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
//echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');

?>
	</div>

<script>
$("#chkall").click(function() {
	$("input[name='chk[]']").prop('checked', this.checked);
});

$("#submit1").on('click', function() {
	var fdata = $('#form1').serialize();
	if(fdata) {
		if(confirm('선택된 출금 신청에 대하여 출금처리 하시겠습니까?')) {
			$.ajax({
				url : "withdrawal_form_update.php",
				type: "POST",
				data: fdata + '&mode=list_state_update',
				success: function(data) {
					$('#ajax_return_txt').val(data);
					alert(data);
					document.location.reload();
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
	else {
		alert('선택된 항목이 없습니다.');
	}
});

// 출금신청 정보 삭제
function withdrawal_dele(idx) {
	if(confirm('삭제하시겠습니까?')) {
		document.location.href = './withdrawal_delete.php?<?=$_SERVER['QUERY_STRING']?>idx='+idx;
	}else {
		return false;
	}
}

// 출금신청 정보 수정
function withdrawal_modi(idx) {
	document.location.href = './withdrawal_form.php?<?=$_SERVER['QUERY_STRING']?>&idx='+idx;
}
</script>