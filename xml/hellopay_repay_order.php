<?
###############################################################################
## 헬로페이용 금일 상환 상품 (카드/PG 매출채권) 내역서 제작을 위한 JSON
###############################################################################
## 1. 헬로페이 카테고리 상품
## 2. 면세점상품이 아닌 ★★★
## 3. 대출종료일이 오늘인 상품
## 항목 : 상환은행코드	상환계좌번호	고객관리성명	입금액	출금통장표시내용	입금통장표시내용
## 원금 이자를 각각의 레코드로 출력한다.
## * 대출종료일을 특정하려 할 경우  ?targetdate=YYYYmmdd 를 쓴다.
###############################################################################

include_once("../common.php");
include_once("../lib/common.lib.php");
include_once("../lib/repay_calculation.php");

while(list($k, $v) = each($_GET)) {
	${$k} = trim($v);
}

if(!$targetdate) {
	$target_date = date('Y-m-d');
}
else {
	if( strlen($targetdate)==8 ) {
		$target_date = substr($targetdate, 0, 4)."-".substr($targetdate, 4, 2)."-".substr($targetdate, 6, 2);
	}
	else {
		$target_date = date('Y-m-d');
	}
}

$sql = "
	SELECT
		A.idx, A.start_num, A.title, A.invest_return, A.recruit_amount, A.loan_start_date, A.loan_end_date, A.repay_acct_no,
		B.bank_cd, B.acct_no, B.cmf_nm
	FROM
		cf_product A
	LEFT JOIN
		IB_vact_hellocrowd B  ON A.loan_mb_no=B.CUST_ID
	WHERE 1
		AND A.category='3'
		AND A.loan_end_date='".$target_date."'
	-- AND A.gr_idx!='437'
	-- AND A.invest_period=1 AND A.invest_days > 0
	ORDER BY
		A.idx";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);

for($i=0; $i<$rows; $i++) {

	$row = sql_fetch_array($res);
	//print_rr($row, 'font-size:12px');

	$LIST[$i]['product_idx'] = $row['idx'];
	$LIST[$i]['bank_cd'] = $row['bank_cd'];
	$LIST[$i]['acct_no'] = $row['acct_no'];
	$LIST[$i]['loaner']  = $row['cmf_nm'];

	$INV_ARR = repayCalculation($LIST[$i]['product_idx']);
	$REPAY_SUM = $INV_ARR['REPAY_SUM'];
	//print_rr($REPAY_SUM, 'font-size:12px'); exit;

	$LIST[$i]['product_start_num'] = $row['start_num'];
	//$LIST[$i]['turn'] = $INV_ARR['REPAY'][0]['SUCCESS']['turn'];

	$LIST[$i]['before_tax_interest'] = $REPAY_SUM['invest_interest'];
	$LIST[$i]['after_tax_interest']  = $REPAY_SUM['interest'];
	$LIST[$i]['principal'] = $REPAY_SUM['repay_principal'];

	unset($row);
	unset($INV_ARR);
	unset($REPAY_SUM);

}

//print_rr($LIST, 'font-size:12px'); exit;

$now_date  = date('Ymd');
$file_name = $now_date . "_헬로매출채권_이체요청서(".$target_date.").xls";
$file_name = iconv("utf-8", "euc-kr", $file_name);

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-description: PHP5 Generated Data" );


$list_count = count($LIST);


echo "<table border=1>
	<tr>
		<td style='text-align:center;background:#C5D9F1'>입금은행</td>
		<td style='text-align:center;background:#C5D9F1'>입금계좌</td>
		<td style='text-align:center;background:#C5D9F1'>고객관리성명</td>
		<td style='text-align:center;background:#C5D9F1'>세전이자</td>
		<td style='text-align:center;background:#C5D9F1'>세후이자</td>
		<td style='text-align:center;background:#C5D9F1'>출금통장표시내용</td>
		<td style='text-align:center;background:#C5D9F1'>입금통장표시내용</td>
	</tr>";

for($i=0; $i<$list_count; $i++) {

	if($LIST[$i]['before_tax_interest']) {

		$print_title = $LIST[$i]['product_start_num'] . "호-이자";

		echo "
	<tr>
		<td style='text-align:center;mso-number-format:\"@\"'>".$LIST[$i]['bank_cd']."</td>
		<td style='text-align:center;mso-number-format:\"@\"'>".$LIST[$i]['acct_no']."</td>
		<td style='text-align:center'>".$LIST[$i]['loaner']."</td>
		<td style='text-align:right'>".number_format($LIST[$i]['before_tax_interest'])."</td>
		<td style='text-align:right'>".number_format($LIST[$i]['after_tax_interest'])."</td>
		<td style='text-align:center'>".$print_title."</td>
		<td style='text-align:center'>".$print_title."</td>
	</tr>";
	}

	if($LIST[$i]['principal']) {

		$print_title = $LIST[$i]['product_start_num'] . "호-원금";

		echo "
	<tr>
		<td style='text-align:center;mso-number-format:\"@\"'>".$LIST[$i]['bank_cd']."</td>
		<td style='text-align:center;mso-number-format:\"@\"'>".$LIST[$i]['acct_no']."</td>
		<td style='text-align:center'>".$LIST[$i]['loaner']."</td>
		<td style='text-align:right'>".number_format($LIST[$i]['principal'])."</td>
		<td style='text-align:right'>".number_format($LIST[$i]['principal'])."</td>
		<td style='text-align:center'>".$print_title."</td>
		<td style='text-align:center'>".$print_title."</td>
	</tr>";
	}

	unset($print_title);

}

echo "\n</table>";

?>
