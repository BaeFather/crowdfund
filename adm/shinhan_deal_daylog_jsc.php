<?
$sub_menu = "500100";
include_once('./_common.php');


auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_GET)) { ${$key} = trim($value); }

$datetime_s = $sdate . ' 00:00:00';
$datetime_e = $edate . ' 23:59:59';

$sql_search = " 1=1 ";
$sql_search.= " AND RDATE > '2017-10-15 00:00:00'";
if($sdate) {
	$sql_search.= " AND TARGET_DATE BETWEEN '$sdate' AND '$edate'";
}
else {
	if($sdate) $sql_search.= " AND TARGET_DATE >= '$sdate'";
	if($edate) $sql_search.= " AND TARGET_DATE <= '$edate'";
}

$give_search = " 1=1 ";
$give_search.= " AND receive_method='2' ";
$give_search.= " AND banking_date > '2017-10-15 00:00:00'";
if($sdate && $edate) {
	$give_search.= " AND banking_date BETWEEN '$datetime_s' AND '$datetime_e'";
}
else {
	if($sdate) $give_search.= " AND banking_date >= '$datetime_s'";
	if($edate) $give_search.= " AND banking_date <= '$datetime_e'";
}

$give_search2 = " 1=1 ";
$give_search2.= " AND B.ib_trust='Y' ";
$give_search2.= " AND A.banking_date > '2017-10-15 00:00:00'";
if($sdate && $edate) {
	$give_search2.= " AND A.banking_date BETWEEN '$datetime_s' AND '$datetime_e'";
}
else {
	if($sdate) $give_search2.= " AND A.banking_date >= '$datetime_s'";
	if($edate) $give_search2.= " AND A.banking_date <= '$datetime_e'";
}


$sql = "
	SELECT
		COUNT(idx) AS cnt,
		IFNULL(SUM(BAL_DEP_CNT), 0) AS BAL_DEP_CNT,
		IFNULL(SUM(BAL_DEP_AMT), 0) AS BAL_DEP_AMT,
		IFNULL(SUM(BAL_RET_CNT), 0) AS BAL_RET_CNT,
		IFNULL(SUM(BAL_RET_AMT), 0) AS BAL_RET_AMT,
		IFNULL(SUM(REPAY_CNT), 0) AS REPAY_CNT,
		IFNULL(SUM(REPAY_AMT), 0) AS REPAY_AMT,
		IFNULL(SUM(LOAN_CNT), 0) AS LOAN_CNT,
		IFNULL(SUM(LOAN_AMT), 0) AS LOAN_AMT,
		IFNULL(SUM(PRIN_CNT), 0) AS PRIN_CNT,
		IFNULL(SUM(PRIN_AMT), 0) AS PRIN_AMT
	FROM
		IB_deal_daylog
	WHERE
		$sql_search";
$TOTAL = sql_fetch($sql);
$total_count = $TOTAL['cnt'];


// 원리금을 예치금으로 지급(충전)한 금액
$give_sql = "
	SELECT
		COUNT(idx) AS cnt,
		(IFNULL(SUM(interest), 0)+IFNULL(SUM(principal), 0)) AS amount
	FROM
		cf_product_give
	WHERE
		$give_search";
$GIVE_TOTAL = sql_fetch($give_sql);

$TOTAL['POINT_REPAY_CNT'] = $GIVE_TOTAL['cnt'];
$TOTAL['POINT_REPAY_AMT'] = $GIVE_TOTAL['amount'];


$give_sql2 = "
		SELECT
			COUNT(A.idx) AS cnt,
			(IFNULL(SUM(A.interest), 0)+IFNULL(SUM(A.principal), 0)) AS amount
		FROM
			cf_product_give A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE
			$give_search2";
$GIVE_TOTAL2 = sql_fetch($give_sql2);
$TOTAL['deposit_cnt2'] = $GIVE_TOTAL2['cnt'];
$TOTAL['deposit_amt2'] = $GIVE_TOTAL2['amount'];


$rows = 31;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "
	SELECT
		*
	FROM
		IB_deal_daylog A
	WHERE
		$sql_search
	ORDER BY
		idx DESC
	LIMIT
		$from_record, $rows";
$res = sql_query($sql);
$list_count = sql_num_rows($res);

$PTOTAL = array(
	'BAL_DEP_CNT'     => 0,
	'BAL_DEP_AMT'     => 0,
	'POINT_REPAY_CNT' => 0,
	'POINT_REPAY_AMT' => 0,
	'BAL_RET_CNT'     => 0,
	'BAL_RET_AMT'     => 0,
	'REPAY_CNT'       => 0,
	'REPAY_AMT'       => 0,
	'LOAN_CNT'        => 0,
	'LOAN_AMT'        => 0,
	'PRIN_CNT'        => 0,
	'PRIN_AMT'        => 0
);


for($i=0; $i<$list_count;$i++) {

	$LIST[$i] = sql_fetch_array($res);

	$PTOTAL['BAL_DEP_CNT'] += $LIST[$i]['BAL_DEP_CNT'];  // 페이지 합계 - 예치금 입금 건수
	$PTOTAL['BAL_DEP_AMT'] += $LIST[$i]['BAL_DEP_AMT'];  // 페이지 합계 - 에치금 입금 금액

	$PTOTAL['BAL_RET_CNT'] += $LIST[$i]['BAL_RET_CNT'];
	$PTOTAL['BAL_RET_AMT'] += $LIST[$i]['BAL_RET_AMT'];

	$PTOTAL['POINT_REPAY_CNT'] += $LIST[$i]['POINT_REPAY_CNT'];
	$PTOTAL['POINT_REPAY_AMT'] += $LIST[$i]['POINT_REPAY_AMT'];

	$PTOTAL['REPAY_CNT']   += $LIST[$i]['REPAY_CNT'];
	$PTOTAL['REPAY_AMT']   += $LIST[$i]['REPAY_AMT'];

	$PTOTAL['LOAN_CNT']    += $LIST[$i]['LOAN_CNT'];
	$PTOTAL['LOAN_AMT']    += $LIST[$i]['LOAN_AMT'];

	$PTOTAL['PRIN_CNT']    += $LIST[$i]['PRIN_CNT'];
	$PTOTAL['PRIN_AMT']    += $LIST[$i]['PRIN_AMT'];

	$PTOTAL['deposit_cnt2'] += $LIST[$i]['deposit_cnt2'];
	$PTOTAL['deposit_amt2'] += $LIST[$i]['deposit_amt2'];

}

$deposit_url    = "/adm/vact_log_shinhan.php";
$withdrawal_url = "/adm/withdrawal_list.php";
$balance_url    = "/adm/balance_detail.php";

$g5['title'] = '신한 일별거래내역';
include_once('./admin.head.php');

?>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/jquery-ui.min.css" rel="stylesheet">
<script src="js/jquery-ui.min.js"></script>

<style>
.borderR { border-right:1px solid #999; }
.table { font-size:12px; }
</style>

<script>
var PTOTAL_POINT_REPAY_CNT = 0;
var PTOTAL_POINT_REPAY_AMT = 0;
var PTOTAL_deposit_cnt2 = 0;
var PTOTAL_deposit_amt2 = 0;

function check_data(i,tdate) {
	$.ajax({
		type : "POST",
		url : "ajax_check_day.php",
		data : {"ymd" : tdate},
		success:function(ret_data) {

			res = JSON.parse(ret_data);


			if ($('#deposit_cnt_'+i).text()!=res.deposit_cnt+"건") $('#deposit_cnt_'+i).css("color","#FF2222");
			if ($('#deposit_amt_'+i).text().replace(/,/gi,"")!=res.deposit_amt+"원") $('#deposit_amt_'+i).css("color","#FF2222");

			$('#point_repay_cnt_'+i).text(res.POINT_REPAY_CNT+"건");
			$('#point_repay_amt_'+i).text(number_format(res.POINT_REPAY_AMT+"원"));
			PTOTAL_POINT_REPAY_CNT = PTOTAL_POINT_REPAY_CNT + Number(res.POINT_REPAY_CNT);
			PTOTAL_POINT_REPAY_AMT = PTOTAL_POINT_REPAY_AMT + Number(res.POINT_REPAY_AMT);
			$('#PTOTAL_POINT_REPAY_CNT').text(number_format(PTOTAL_POINT_REPAY_CNT));
			$('#PTOTAL_POINT_REPAY_AMT').text(number_format(PTOTAL_POINT_REPAY_AMT));

			$('#deposit_cnt2_'+i).text(res.deposit_cnt2+"건");
			$('#deposit_amt2_'+i).text(number_format(res.deposit_amt2+"원"));
			PTOTAL_deposit_cnt2 = PTOTAL_deposit_cnt2 + Number(res.deposit_cnt2);
			PTOTAL_deposit_amt2 = PTOTAL_deposit_amt2 + Number(res.deposit_amt2);
			$('#PTOTAL_deposit_cnt2').text(number_format(PTOTAL_deposit_cnt2));
			$('#PTOTAL_deposit_amt2').text(number_format(PTOTAL_deposit_amt2));

			if (res.POINT_REPAY_CNT!=res.point_repay_cnt) $('#point_repay_cnt_'+i).css("color","#FF2222");
			if (res.POINT_REPAY_AMT!=res.point_repay_amt) $('#point_repay_amt_'+i).css("color","#FF2222");

			//console.log($('#deposit_amt_'+i).text().replace(/,/gi,"")+res.deposit_amt);
			//console.log($('#deposit_cnt_'+i).text()+res.deposit_cnt);
			//console.log(res);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
}
</script>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<div>
		<form id="frmSearch" method="get" class="form-horizontal" style="margin:0;">
		<div class="form-group">
			<ul class="col-sm-10 list-inline" style="width:100%;">
				<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
				<li>~</li>
				<li><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
				<li><button type="submit" class="btn btn-sm btn-primary" style="display:table-cell;">검색</button></li>
				<li style="float:right;font-size:12px;color:brown">
					※ '신한은행 인사이드뱅크'를 통하여 시간단위로 당일 거래 내역 리포트를 수집합니다. &nbsp;
					<button type="button" id="live_data_button" class="btn btn-sm btn-warning">실시간정보갱신</button>
				</li>
			</ul>
		</div>
		</form>
	</div>
	<!-- 검색영역 E N D -->

	<table class="table table-striped table-bordered table-hover" style="margin:0">
		<tr align="center" style="background:#F8F8EF">
			<td rowspan="2" style="border-right:1px solid #999;">대상일자</td>
			<td colspan="2" style="border-right:1px solid #999;">예치금입금</td>
			<td colspan="2" style="border-right:1px solid #999;">원리금→예치금전환</td>
			<td colspan="2" style="border-right:1px solid #999;">예치금출금</td>
			<td colspan="2" style="border-right:1px solid #999;">상환금입금</td>
			<td colspan="2" style="border-right:1px solid #999;">대출실행</td>
			<td colspan="2" style="border-right:1px solid #999;">원리금처리</td>
			<!--<td colspan="2" style="border-right:1px solid #999;">회수금집금</td>//-->
			<td rowspan="2" style="border-right:1px solid #999;">예치금총잔액</td>
			<td rowspan="2">예치금<br>신탁계좌잔액</td>
		</tr>
		<tr align="center" style="background:#F8F8EF">
			<td alt="예치금입금">건수</td>
			<td style="border-right:1px solid #999;" alt="예치금입금액">금액</td>
			<td alt="예치금전환">건수</td>
			<td style="border-right:1px solid #999;" alt="예치금전환금액">금액</td>
			<td alt="예치금출금">건수</td>
			<td style="border-right:1px solid #999;" alt="예치금출금액">금액</td>
			<td alt="상환금입금">건수</td>
			<td style="border-right:1px solid #999;" alt="상환금입금액">금액</td>
			<td alt="대출실행건수">건수</td>
			<td style="border-right:1px solid #999;" alt="대출실행금액">금액</td>
			<td alt="원리금처리">건수</td>
			<td style="border-right:1px solid #999;" alt="원리금처리금액">금액</td>
			<!--
			<td alt="회수금집금건수">건수</td>
			<td style="border-right:1px solid #999;" alt="회수금집금액">금액</td>
			//-->
		</tr>

		<!-- 전체합산데이터 -->
		<tr align="right" style="background:#EFEFEF;color:royalblue">
			<td style="text-align:center;border-right:1px solid #999;">합계</td>

			<td><?=number_format($TOTAL['BAL_DEP_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['BAL_DEP_AMT'])?>원</td>

			<td><?=number_format($TOTAL['POINT_REPAY_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['POINT_REPAY_AMT'])?>원</td>

			<td><?=number_format($TOTAL['BAL_RET_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['BAL_RET_AMT'])?>원</td>
			<td><?=number_format($TOTAL['REPAY_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['REPAY_AMT'])?>원</td>
			<td><?=number_format($TOTAL['LOAN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['LOAN_AMT'])?>원</td>

			<!--
			<td><?=number_format($TOTAL['PRIN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['PRIN_AMT'])?>원</td>
			//-->

			<!-- 원리금 처리 -->
			<td><?=number_format($TOTAL['deposit_cnt2'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['deposit_amt2'])?>원</td>

			<td style="border-right:1px solid #999;"></td>
			<td></td>
		</tr>
		<!-- 전체합산데이터 -->

<?
	if($total_page > 1 && $list_count > 1) {
?>
		<!-- 페이지별 합산데이터 -->
		<tr align="right" style="background:#EFEFEF;color:brown">
			<td style="text-align:center;border-right:1px solid #999;">페이지 합계</td>

			<!-- 예치금 입금 페이지 합계 -->
			<td><?=number_format($PTOTAL['BAL_DEP_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($PTOTAL['BAL_DEP_AMT'])?>원</td>

			<!-- 원리금 => 예치금전환 페이지 합계 -->
			<td><span id="PTOTAL_POINT_REPAY_CNT"><?=number_format($PTOTAL['POINT_REPAY_CNT'])?></span>건</td>
			<td style="border-right:1px solid #999;"><span id="PTOTAL_POINT_REPAY_AMT"><?=number_format($PTOTAL['POINT_REPAY_AMT'])?></span>원</td>

			<!-- 예치금 출금 페이지 합계 -->
			<td><span id="BAL_RET_CNT_<?=$i?>"><?=number_format($PTOTAL['BAL_RET_CNT'])?></span>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($PTOTAL['BAL_RET_AMT'])?>원</td>

			<!-- 상환금 입금 페이지 합계 -->
			<td><?=number_format($PTOTAL['REPAY_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($PTOTAL['REPAY_AMT'])?>원</td>

			<!-- 대출 실행 페이지 합계 -->
			<td><?=number_format($PTOTAL['LOAN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($PTOTAL['LOAN_AMT'])?>원</td>

			<!--
			<td><?=number_format($PTOTAL['PRIN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($PTOTAL['PRIN_AMT'])?>원</td>
			//-->

			<!-- 원리금 처리 페이지 합계 -->
			<td><span id="PTOTAL_deposit_cnt2"><?=number_format($PTOTAL['deposit_cnt2'])?></span>건</td>
			<td style="border-right:1px solid #999;"><span id="PTOTAL_deposit_amt2"><?=number_format($PTOTAL['deposit_amt2'])?></span>원</td>

			<td style="border-right:1px solid #999;"></td>

			<td></td>
		</tr>
		<!-- 페이지별 합산데이터 -->
<?
	}

if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$DETAIL['deposit']    = $deposit_url."?sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE'];
		$DETAIL['withdrawal'] = $withdrawal_url."?ib_regist=1&receive_method_all=Y&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE'];
		$DETAIL['principal']  = $balance_url."?sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE']."&is_repay=1";

		$tr_bgcolor = (date('w', strtotime($LIST[$i]['TARGET_DATE']))=='0') ? '#FFEEEE' : '';

		//$FCOLOR['deposit_cnt'] = ($LIST[$i]['deposit_cnt']!=$LIST[$i]['BAL_DEP_CNT']) ? '#FF2222' : '';
		//$FCOLOR['deposit_amt'] = ($LIST[$i]['deposit_amt']!=$LIST[$i]['BAL_DEP_AMT']) ? '#FF2222' : '';

		//$FCOLOR['point_repay_cnt'] = ($LIST[$i]['point_repay_cnt']!=$LIST[$i]['POINT_REPAY_CNT']) ? '#FF2222' : '';
		//$FCOLOR['point_repay_amt'] = ($LIST[$i]['point_repay_amt']!=$LIST[$i]['POINT_REPAY_AMT']) ? '#FF2222' : '';

?>
		<tr align="right" style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;border-right:1px solid #999;"><?=$LIST[$i]['TARGET_DATE']?></td>

			<!-- 예치금 입금 -->
			<td><a href="<?=$DETAIL['deposit']?>" style="color:<?=$FCOLOR['deposit_cnt']?>" id="deposit_cnt_<?=$i?>"><?=number_format($LIST[$i]['BAL_DEP_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;"><a href="<?=$DETAIL['deposit']?>" style="color:<?=$FCOLOR['deposit_amt']?>" id="deposit_amt_<?=$i?>"><?=number_format($LIST[$i]['BAL_DEP_AMT'])?>원</a></td>

			<!-- 원리금 =>예치금 전환 -->
			<td><a href="<?=$DETAIL['principal']?>" style="color:<?=$FCOLOR['point_repay_cnt']?>" id="point_repay_cnt_<?=$i?>"><?//=number_format($LIST[$i]['POINT_REPAY_CNT'])?></a></td>
			<td style="border-right:1px solid #999;"><a href="<?=$DETAIL['principal']?>" style="color:<?=$FCOLOR['point_repay_amt']?>" id="point_repay_amt_<?=$i?>"><?//=number_format($LIST[$i]['POINT_REPAY_AMT'])?></a></td>

			<!-- 예치금 출금 -->
			<td><a href="<?=$DETAIL['withdrawal']?>"><?=number_format($LIST[$i]['BAL_RET_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;"><a href="<?=$DETAIL['withdrawal']?>"><?=number_format($LIST[$i]['BAL_RET_AMT'])?>원</a></td>

			<!-- 상환금 입금 -->
			<td><?=number_format($LIST[$i]['REPAY_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($LIST[$i]['REPAY_AMT'])?>원</td>

			<!-- 대출 실행 -->
			<td><?=number_format($LIST[$i]['LOAN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($LIST[$i]['LOAN_AMT'])?>원</td>

			<!--
			<td><?=number_format($LIST[$i]['PRIN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($LIST[$i]['PRIN_AMT'])?>원</td>
			//-->

			<!-- 원리금 처리 -->
			<td><span id="deposit_cnt2_<?=$i?>"><?//=number_format($LIST[$i]['deposit_cnt2'])?></span></td>
			<td style="border-right:1px solid #999;"><span id="deposit_amt2_<?=$i?>"><?//=number_format($LIST[$i]['deposit_amt2'])?></span></td>

			<!-- 예치금 충전 잔액 -->
			<td style="border-right:1px solid #999;"><?=number_format($LIST[$i]['BAL_ALLAMT'])?>원</td>

			<!-- 예치금 신탁 계좌 잔액 -->
			<td><?=number_format($LIST[$i]['BAL_TRUAMT'])?>원</td>
		</tr>
		<script>
		check_data("<?=$i?>","<?=$LIST[$i]['TARGET_DATE']?>");

		</script>
		<?
	}

} else {
	?>
		<tr align="right">
			<td align="center" colspan="20">데이터가 없습니다.</td>
		</tr>
<?
}
?>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

</div>
<?
$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
?>

<script>
$("input[name=chkall]").click(function() {
	$("input[name='chk[]']").prop('checked', this.checked);
});


$("#live_data_button").click(function() {
	if(confirm('금일 데이터를 갱신 하시겠습니까?')) {
		$.ajax({
			url : "ajax_ib_live_data_crowling.php",
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			success:function(data) {
				if(data=='OK') { window.location.reload(); }
			},
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
});


$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['PHP_SELF']?>'
	        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});

$(document).ajaxStop(function() {
  //alert("ok");
});
</script>

<?php
include_once ('./admin.tail.php');
?>