<?
include_once("../../common.cli.php");

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }

$G_TYPE = array(
	'A' => '전체상품군',
	'1' => '부동산 PF',
	'2' => '주택담보',
	'3' => '매출채권',
	'4' => '동산'
);
$G_TYPE_KEY = array_keys($G_TYPE);

$g_type = ($g_type) ? $g_type : 'A';
$sdate = ($sdate) ? $sdate : date('Y-m').'-01';
$edate = ($edate) ? $edate : date('Y-m').'-'.date('t', strtotime(date('Y-m-d')));

$where = "";
$where.= " AND g_type = '".$g_type."'";
$where.= " AND tDate BETWEEN '".$sdate."' AND '".$edate."'";

$sql = "
	SELECT
		*
	FROM
		cf_loan_repay_status
	WHERE 1
		$where
	ORDER BY
		tDate DESC";
//print_rr($sql, 'font-size:12px');
$result = sql_query($sql);
$rcount = sql_num_rows($result);

$PERIOD_SUM = array(
	'loan_cnt' => 0,
	'loan_amt' => 0,
	'principal' => 0,
	'interest' => 0,
	'tax' => 0,
	'fee' => 0
);

for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);

	$PERIOD_SUM['loan_cnt']  += $LIST[$i]['loan_cnt'];
	$PERIOD_SUM['loan_amt']  += $LIST[$i]['loan_amt'];
	$PERIOD_SUM['principal'] += $LIST[$i]['principal'];
	$PERIOD_SUM['interest']  += $LIST[$i]['interest'];
	$PERIOD_SUM['tax']       += ($LIST[$i]['interest_tax'] + $LIST[$i]['local_tax']);
	$PERIOD_SUM['fee']       += $LIST[$i]['fee'];

}
sql_free_result($result);

$list_count = count($LIST);

$num = $list_count;


sql_close();

?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
<title>대출ㆍ상환 현황 | 헬로펀딩</title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?ver=20180826">
<link rel="stylesheet" type="text/css" href="/adm/css/admin.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="/adm/css/bootstrap.min.css">
<!--[if lte IE 8]>
<script src="/js/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js?v=20200619"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<script type="text/javascript" src="/adm/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/common_variables.js"></script>
<script type="text/javascript" src="/js/jquery.floatThead.js"></script>

<style>
th, td { padding: 4px }
</style>

</head>

<body>

<div id="wrapper">
	<div id="container">
		<div class="tbl_head02 tbl_wrap">

			<form id="frmSearch" name= "frmSearch" method="get" class="form-horizontal">
			<ul class="col col-md-* list-inline" style="padding:0;margin-bottom:5px">
				<li>
					<select id="g_type" name="g_type" class="form-control input-sm" style="width:150px">
<?
for($i=0; $i<count($G_TYPE); $i++) {
	$selected = ($G_TYPE_KEY[$i]==$g_type) ? 'selected' : '';
	echo "<option value='".$G_TYPE_KEY[$i]."' {$selected}>".$G_TYPE[$G_TYPE_KEY[$i]]."</option>\n";
}
?>
					</select>
				</li>
				<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" style="width:100px;text-align:center;"></li>
				<li>~</li>
				<li><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" style="width:100px;text-align:center;"></li>
				<li><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
				<li><button type="button" onclick="go_excel();" class="btn btn-sm btn-success" style="width:150px">검색결과 다운로드</button></li>
			</ul>
			<input type=hidden name="display_mode" value=""/>
			</form>

			<table id="table0" class="table-striped table-bordered table-hover" style="font-size:13px">
				<colgroup>
					<col style="width:6%">
					<col style="width:5%">
					<col style="width:5%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
					<col style="width:8.4%">
				</colgroup>
				<thead>
					<tr>
						<th rowspan="2" style="background:#F8F8EF;">DATE</th>
						<th colspan="4" style="background:#F8F8EF;">대출</th>
						<th colspan="2" style="background:#F8F8EF;">원금상환</th>
						<th colspan="2" style="background:#F8F8EF;">이자(세전)</th>
						<th colspan="2" style="background:#F8F8EF;">원천징수</th>
						<th colspan="2" style="background:#F8F8EF;">플랫폼이용료</th>
					</tr>
					<tr>
						<th style="background:#F8F8EF;">건수</th>
						<th style="background:#F8F8EF;">건수(누적)</th>
						<th style="background:#F8F8EF;">금액</th>
						<th style="background:#F8F8EF;">금액(누적)</th>

						<th style="background:#F8F8EF;">금액</th>
						<th style="background:#F8F8EF;">금액(누적)</th>

						<th style="background:#F8F8EF;">금액</th>
						<th style="background:#F8F8EF;">금액(누적)</th>

						<th style="background:#F8F8EF;">금액</th>
						<th style="background:#F8F8EF;">금액(누적)</th>

						<th style="background:#F8F8EF;">금액</th>
						<th style="background:#F8F8EF;">금액(누적)</th>
					</tr>
				</thead>
				<tbody>
<? if($list_count > 1) { ?>
					<tr align='center' style="background:#DDDDFF;color:#2222FF">
						<td>조건합계</td>
						<td align='right'><?=number_format($PERIOD_SUM['loan_cnt'])?>건</td>
						<td align='right'>-</td>
						<td align='right'><?=number_format($PERIOD_SUM['loan_amt'])?>원</td>
						<td align='right'>-</td>
						<td align='right'><?=number_format($PERIOD_SUM['principal'])?>원</td>
						<td align='right'>-</td>
						<td align='right'><?=number_format($PERIOD_SUM['interest'])?>원</td>
						<td align='right'>-</td>
						<td align='right'><?=number_format($PERIOD_SUM['tax'])?>원</td>
						<td align='right'>-</td>
						<td align='right'><?=number_format($PERIOD_SUM['fee'])?>원</td>
						<td align='right'>-</td>
					</tr>
<?} ?>
<?
if($list_count) {

	for($i=0; $i<$list_count; $i++) {

		$tax     = $LIST[$i]['interest_tax'] + $LIST[$i]['local_tax'];
		$tax_sum = $LIST[$i]['interest_tax_sum'] + $LIST[$i]['local_tax_sum'];

?>
					<tr align='center'>
						<td><?=$LIST[$i]['tDate']?></td>
						<td align='right'><?=number_format($LIST[$i]['loan_cnt'])?>건</td>
						<td align='right'><?=number_format($LIST[$i]['loan_cnt_sum'])?>건</td>
						<td align='right'><?=number_format($LIST[$i]['loan_amt'])?>원</td>
						<td align='right'><?=number_format($LIST[$i]['loan_amt_sum'])?>원</td>
						<td align='right'><?=number_format($LIST[$i]['principal'])?>원</td>
						<td align='right'><?=number_format($LIST[$i]['principal_sum'])?>원</td>
						<td align='right'><?=number_format($LIST[$i]['interest'])?>원</td>
						<td align='right'><?=number_format($LIST[$i]['interest_sum'])?>원</td>
						<td align='right'><?=number_format($tax)?>원</td>
						<td align='right'><?=number_format($tax_sum)?>원</td>
						<td align='right'><?=number_format($LIST[$i]['fee'])?>원</td>
						<td align='right'><?=number_format($LIST[$i]['fee_sum'])?>원</td>
					</tr>
<?
	}
}
else {
	echo "<tr align='center'><td colspan='20'>데이터가 없습니다.</td></tr>";
}
?>

				</tbody>
			</table>

			<br/><br/>

		</div><!-- .tbl_head02 .tbl_wrap -->
	</div><!-- #container -->
</div><!-- #wrapper -->

</body>
</html>

<script>
$('#year').on('change', function() {
	$(location).attr('href','?year=' + $('#year').val());
});

$(document).ready(function() {
	$('#table0').floatThead();
});
</script>

<script src="/adm/admin.js"></script>
<script>
$(function(){
	$(".datepicker").datepicker({
		dateFormat      : 'yy-mm-dd',
		changeYear      : true,
		changeMonth     : true,
		monthNamesShort : ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin     : ['일' ,'월', '화', '수', '목', '금', '토']
	});
});
</script>