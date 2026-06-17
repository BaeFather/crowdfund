<?
include_once('../common.cli.php');


while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

$ret = array();

$d = mktime(0,0,0, date("m"), 1, date("Y"));
$prev_date = strtotime("-1 month", $d);
$tmp_date = date('Y-m-t', $prev_date);
$ym = substr($tmp_date, 0, 7);

for($i=1; $i<=4; $i++) {

	$sql = "
		SELECT
			loan_amt_sum, remain_amt, overdue_perc
		FROM
			cf_loan_repay_status
		WHERE 1
			AND tDate = '".$tmp_date."'
			AND g_type = '".$i."'";
	$row = sql_fetch($sql);

	// 합계 연체율
	$sql2 = "
		SELECT
			overdue_perc
		FROM
			cf_loan_repay_status
		WHERE 1
			AND tYear = '".$ym."'
			AND g_type = 'A'
			AND LEFT(tDate,7) < '".$tmp_date."'
		ORDER BY
			tDate DESC
		LIMIT 1";
	$row2 = sql_fetch($sql2);

	$loan_amt = $row['loan_amt_sum'];			// 누적 대출금액
	$remain_amt = $row['remain_amt'];			// 대출잔액
	$overdue_rate = $row['overdue_perc'];		// 연체율
	$overdue_rate_all = $row2['overdue_perc'];	// 합계 연체율

	
	$ret['ym'] = $ym;

	$ret[$i]['loan_amt'] = $loan_amt;
	$ret[$i]['remain_amt'] = $remain_amt;
	$ret[$i]['overdue_rate'] = $overdue_rate;

	$ret['tot']['loan_amt'] += $loan_amt;
	$ret['tot']['remain_amt'] += $remain_amt;
	$ret['tot']['overdue_rate'] = $overdue_rate_all;
}


echo json_encode($ret);


sql_close();
exit;

?>