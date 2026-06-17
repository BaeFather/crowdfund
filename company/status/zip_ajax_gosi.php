<?
include_once('../../common.cli.php');


while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

$ret = array();

if( strlen($ym) > 4 ) {

	/////////////////////
	// 월 검색
	/////////////////////

	$tmp_date = $ym.'-'.date('t', strtotime($ym.'-01'));  // 월의 말일

	$sql = "
		SELECT
			loan_amt_sum, principal_sum, remain_amt, overdue_perc, overdue_count 
		FROM
			cf_loan_repay_status
		WHERE 1
			AND tDate = '".$tmp_date."'
			AND g_type = 'A'";
	$row = sql_fetch($sql);

	$tot_loan_amt = $row['loan_amt_sum'];
	$tot_repay_amt = $row['principal_sum'];
	$tot_remain_amt = $row['remain_amt'];
	$overdue_rate = $row['overdue_perc'];
	$overdue_cnt = $row['overdue_count'];

	// 헬로 자기자본 투자금액 (누적투자금액)
	$hello_nujuk_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y' AND A.insert_date <= '".$tmp_date."'
			AND B.state IN('1','2','4','5','8','9')
	")['amount'];

	// 헬로 자기자본 투자금액 (투자잔액)
	$hello_live_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y' AND A.insert_date <= '".$tmp_date."'
			AND B.state IN('1','8')
	")['amount'];

}
else {

	/////////////////////
	// 연도별 검색
	/////////////////////

	$todate = date('Y-m');

	$sql = "
		SELECT
			loan_amt_sum, principal_sum, remain_amt, overdue_perc, overdue_count 
		FROM
			cf_loan_repay_status
		WHERE 1
			AND tYear = '".$ym."'
			AND g_type = 'A'
			AND LEFT(tDate,7) < '".$todate."'
		ORDER BY
			tDate DESC
		LIMIT 1";
	//print_r($sql);
	$row = sql_fetch($sql);

	$tot_loan_amt = $row['loan_amt_sum'];
	$tot_repay_amt = $row['principal_sum'];
	$tot_remain_amt = $row['remain_amt'];
	$overdue_rate = $row['overdue_perc'];
	$overdue_cnt = $row['overdue_count'];

	// 헬로 자기자본 투자금액 (누적투자금액)
	$hello_nujuk_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y' AND LEFT(A.insert_date,4) <= '".$todate."'
			AND B.state IN('1','2','4','5','8','9')
	")['amount'];

	// 헬로 자기자본 투자금액 (투자잔액)
	$hello_live_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y' AND LEFT(A.insert_date,4) <= '".$todate."'
			AND B.state IN('1','8')
	")['amount'];

}

$ret['ym'] = $ym;
$ret['loan_amt'] = $tot_loan_amt;
$ret['repay_amt'] = $tot_repay_amt;
$ret['remain_amt'] = $tot_remain_amt;
$ret['overdue_rate'] = $overdue_rate;
$ret['overdue_cnt'] = $overdue_cnt;
$ret['hello_nujuk_invest_amt'] = $hello_nujuk_invest_amt;
$ret['hello_live_invest_amt'] = $hello_live_invest_amt;
//$ret['sql1'] = $sql;

echo json_encode($ret);


sql_close();
exit;

?>