<?
include_once('../common.cli.php');


while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

$ret = array();

$d = mktime(0,0,0, date("m"), 1, date("Y"));
$prev_date = strtotime("-1 month", $d);
$tmp_date = date('Y-m-t', $prev_date);
$ym = substr($tmp_date, 0, 7);

$sql = "
	SELECT
		loan_amt_sum, remain_amt, overdue_perc, overdue_count 
	FROM
		cf_loan_repay_status
	WHERE 1
		AND tDate = '".$tmp_date."'
		AND g_type = 'A'";
$row = sql_fetch($sql);

$tot_loan_amt = $row['loan_amt_sum'];	// 누적 대출금액
$tot_remain_amt = $row['remain_amt'];	// 대출 잔액
$overdue_rate = $row['overdue_perc'];	// 연체율
$overdue_cnt = $row['overdue_count'];	// 연체건수

// 헬로 자기자본 투자금액 (누적투자금액)
$hello_nujuk_invest_amt = sql_fetch("
	SELECT
		IFNULL(SUM(A.amount),0) AS amount
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B ON A.product_idx=B.idx
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
		cf_product B ON A.product_idx=B.idx
	WHERE 1
		AND A.member_idx='48343' AND A.invest_state='Y' AND A.insert_date <= '".$tmp_date."'
		AND B.state IN('1','8')
")['amount'];


// 헬로 자기자본 투자금액 (누적투자금액)
$nujuk_invest_amt = sql_fetch("
	SELECT
		IFNULL(SUM(A.amount),0) AS amount
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.member_idx='48343' AND A.invest_state='Y'
		AND B.state IN('1','2','4','5','8','9')
		AND B.loan_start_date <= '".$tmp_date."'
	")['amount'];

$paid_principal = sql_fetch("
	SELECT
		IFNULL(SUM(principal),0) AS principal
	FROM
		cf_product_give
	WHERE 1
		AND member_idx='48343'
		AND banking_date <= '$tmp_date 23:59:59'
	")['principal'];

$remain_amt = $nujuk_invest_amt - $paid_principal;

$ret['ym'] = $ym;
$ret['loan_amt'] = $tot_loan_amt;
$ret['tot_remain_amt'] = $tot_remain_amt;
$ret['overdue_rate'] = $overdue_rate;
$ret['overdue_cnt'] = $overdue_cnt;
$ret['nujuk_invest_amt'] = $nujuk_invest_amt;
$ret['remain_amt'] = $remain_amt;

echo json_encode($ret);


sql_close();
exit;

?>