<?
include_once('../../common.cli.php');


while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

$ret = array();

for($i=1; $i<=4; $i++) {

	$tot_loan_amt = 0;
	$tot_repay_amt = 0;
	$tot_remain_amt = 0;
	$overdue_rate = 0;
	$overdue_cnt = 0;

	if( strlen($ym) > 4 ) {

		//////////////////////////
		// 월 검색
		//////////////////////////

		$tmp_date = $ym.'-'.date('t', strtotime($ym.'-01'));		// 월의 말일

		$sql = "
			SELECT
				loan_amt_sum, principal_sum, remain_amt
			FROM
				cf_loan_repay_status
			WHERE 1
				AND tDate = '".$tmp_date."'
				AND g_type = '".$i."'";
		$row = sql_fetch($sql);

		$loan_amt = $row['loan_amt_sum'];
		$repay_amt = $row['principal_sum'];
		$remain_amt = $row['remain_amt'];

		//$overdue_rate = 0;
		//$overdue_cnt = 0;

		// 연체


	}
	else {

		//////////////////////////
		// 연도별 검색
		//////////////////////////

		$todate = date('Y-m');

		$sql = "
			SELECT
				loan_amt_sum, principal_sum, remain_amt
			FROM
				cf_loan_repay_status
			WHERE 1
				AND tYear = '".$ym."'
				AND g_type = '".$i."'
				AND LEFT(tDate,7) < '".$todate."'
			ORDER BY
				tDate DESC
			LIMIT 1";
		$row = sql_fetch($sql);

		$loan_amt = $row['loan_amt_sum'];
		$repay_amt = $row['principal_sum'];
		$remain_amt = $row['remain_amt'];

		//$overdue_rate = 0;
		//$overdue_cnt = 0;

	}


	$ret['ym'] = $ym;

	$ret[$i]['loan_amt'] = $loan_amt;
	$ret[$i]['repay_amt'] = $repay_amt;
	$ret[$i]['remain_amt'] = $remain_amt;

	$ret[$i]['overdue_rate'] = $overdue_rate;
	$ret[$i]['overdue_cnt'] = $overdue_cnt;
	$ret[$i]['bsell_cnt'] = 0;						// 부실매각채권수

	$ret['tot']['loan_amt'] += $loan_amt;
	$ret['tot']['repay_amt'] += $repay_amt;
	$ret['tot']['remain_amt'] += $remain_amt;

	//$ret['tot']['overdue_rate'] = 0;
	//$ret['tot']['overdue_cnt'] = 0;
	$ret['tot']['bsell_cnt'] += 0;

}



echo json_encode($ret);


sql_close();
exit;

?>