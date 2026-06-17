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

if(!$repay_date) {
	$repay_date = date('Y-m-d');
} else {
	if( strlen($repay_date)==8 ) {
		$repay_date = substr($repay_date, 0, 4)."-".substr($repay_date, 4, 2)."-".substr($repay_date, 6, 2);
	}
	else {
		$repay_date = date('Y-m-d');
	}
}

$resu['repay_date'] = $repay_date;
$resu['repay_bank_num'] = $repay_bank_num;

$sql = "
	SELECT
		A.idx, A.start_num, A.title, A.invest_return, A.recruit_amount, A.loan_start_date, A.loan_end_date, A.repay_acct_no,
		B.bank_cd, B.acct_no, B.cmf_nm
	FROM
		cf_product A
	LEFT JOIN
		IB_vact_hellocrowd B  ON A.loan_mb_no=B.CUST_ID
	WHERE 1
		AND A.gr_idx!='437'
		AND A.category='3'
		AND A.invest_period=1 AND A.invest_days > 0
		AND A.loan_end_date='$repay_date'
		AND B.acct_no='$repay_bank_num'
	ORDER BY
		A.idx desc";
//echo "$sql<br/><br/>";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

if ($rows) {
	$row = sql_fetch_array($res);

	$INV_ARR = repayCalculation($row['idx']);
	$REPAY_SUM = $INV_ARR['REPAY_SUM'];

	$resu['res'] = "succ";
	$resu['idx'] = $row['idx'];
	$resu['eja'] = $REPAY_SUM['withhold']+$REPAY_SUM['interest'];
	$resu['won'] = $REPAY_SUM['repay_principal'];

} else {
	$resu['res'] = "fail";
}
echo json_encode($resu);
?>
