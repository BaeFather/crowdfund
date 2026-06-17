<?
###############################################################################
## 대출 및 상환 일별 합산액 기록 (일대사)
##
##
###############################################################################

$base_path = "/home/crowdfund/public_html";
include_once($base_path . '/common.cli.php');


function getLoanRepayDayStatus($tDate, $gb='A') {

	$main_table = "cf_loan_repay_status";

	if(!$tDate) $tDate = date('Y-m-d', strtotime("-1 day"));
	if(!$gb) $gb = 'A';

	$TDATE  = explode("-", $tDate);
	$tYear  = $TDATE[0];
	$tMonth = $TDATE[1];
	$tDay   = $TDATE[2];

	if( !checkdate($tMonth, $tDay, $tYear) ) { return false; }

	if($gb == "1") {
		// 부동산 PF
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = " AND (B.category = '2' AND B.mortgage_guarantees = '')";
	}
	else if($gb == "2") {
		// 주택담보
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = " AND (B.category = '2' AND B.mortgage_guarantees = '1')";
	}
	else if($gb == "3") {
		// 매출채권
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = " AND B.category = '3'";
	}
	else if($gb=="4") {
		// 동산
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = " AND B.category = '1'";
	}
	else {
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = "";
	}


	// 전일까지의 누적기록 항목 가져오기
	$tDatePrevDate = date('Y-m-d', strtotime($tDate . ' -1 day'));

	$sql = "
		SELECT
			loan_cnt_sum, loan_amt_sum, principal_sum, interest_sum, interest_tax_sum, local_tax_sum, fee_sum
		FROM
			{$main_table}
		WHERE 1
			AND tDate = '".$tDatePrevDate."'
			$status_add_qry";
	//echo $sql.";\n";
	$PREV_DATA = sql_fetch($sql);


	/////////////////////////////////////////////////
	// 대출 실행 내역
	//	진행현황 1:이자상환중 2:상환완료(투자종료) 3:투자금모집실패 4:매각 5:중도상환 6:대출취소(기표전) 7:대출취소(기표후) 8:연체 9:부도(상환불가)
	/////////////////////////////////////////////////
	/* 일별 데이터 */
	$sql = "
		SELECT
			COUNT(B.idx) AS cnt,
			IFNULL(SUM(B.recruit_amount),0) AS amt
		FROM
			cf_product B
		WHERE 1
			AND B.isTest = '' AND B.recruit_amount >= 10000
			AND B.state IN(1,2,4,5,8,9)
			AND B.loan_start_date = '".$tDate."'
			$product_add_qry";
	//echo $sql.";\n";
	$LOAN = sql_fetch($sql);

	/* 대상일 전일 까지의 누적 데이터 */
	$LOAN['cnt_sum'] = $PREV_DATA['loan_cnt_sum'] + $LOAN['cnt'];
	$LOAN['amt_sum'] = $PREV_DATA['loan_amt_sum'] + $LOAN['amt'];


	/////////////////////////////////////////////////
	// 상환내역
	/////////////////////////////////////////////////
	/* 일별 데이터 */
	$sql = "
		SELECT
			IFNULL(SUM(principal),0) AS principal,
			IFNULL(SUM(interest),0) AS interest,
			IFNULL(SUM(interest_tax),0) AS interest_tax,
			IFNULL(SUM(local_tax),0) AS local_tax,
			IFNULL(SUM(fee),0) AS fee
		FROM
			cf_product_give A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND LEFT(banking_date,10) = '".$tDate."'
			$product_add_qry";
	//echo $sql.";\n";
	$REPAY = sql_fetch($sql);

	$REPAY['principal_sum']    = $PREV_DATA['principal_sum'] + $REPAY['principal'];
	$REPAY['interest_sum']     = $PREV_DATA['interest_sum'] + $REPAY['interest'];
	$REPAY['interest_tax_sum'] = $PREV_DATA['interest_tax_sum'] + $REPAY['interest_tax'];
	$REPAY['local_tax_sum']    = $PREV_DATA['local_tax_sum'] + $REPAY['local_tax'];
	$REPAY['fee_sum']          = $PREV_DATA['fee_sum'] + $REPAY['fee'];

	$remain_amt = $LOAN['amt_sum'] - $REPAY['principal_sum'];		// 대출잔액

	$RETURN_VAL = array(
		'tDate'            => $tDate,
		'tYear'            => $tYear,
		'tMonth'           => $tMonth,
		'tDay'             => $tDay,
		'g_type'           => $gb,
		'loan_cnt'         => (int)$LOAN['cnt'],
		'loan_cnt_sum'     => (int)$LOAN['cnt_sum'],
		'loan_amt'         => (int)$LOAN['amt'],
		'loan_amt_sum'     => (int)$LOAN['amt_sum'],
		'principal'        => (int)$REPAY['principal'],
		'principal_sum'    => (int)$REPAY['principal_sum'],
		'remain_amt'       => (int)$remain_amt,
		'interest'         => (int)$REPAY['interest'],
		'interest_sum'     => (int)$REPAY['interest_sum'],
		'interest_tax'     => (int)$REPAY['interest_tax'],
		'interest_tax_sum' => (int)$REPAY['interest_tax_sum'],
		'local_tax'        => (int)$REPAY['local_tax'],
		'local_tax_sum'    => (int)$REPAY['local_tax_sum'],
		'fee'              => (int)$REPAY['fee'],
		'fee_sum'          => (int)$REPAY['fee_sum'],
		'fee'              => (int)$REPAY['fee']
	);

	return $RETURN_VAL;

}



/////////////////////////////////////////////////
// 기록시작
/////////////////////////////////////////////////

//if( strlen($_SERVER['argv'][1]) <> 10 ) { exit; }
//$date  = $_SERVER['argv'][1];
$date = "2016-09-30";

$G_TYPE = array('A','1','2','3','4');

$no = 1;
while($date < '2020-11-25') {

	for($i=0; $i<count($G_TYPE); $i++) {

		$DUPLICATE = sql_fetch("SELECT COUNT(tDate) AS cnt FROM cf_loan_repay_status WHERE tDate='".$date."' AND g_type='".$G_TYPE[$i]."'");

		if($DUPLICATE['cnt']) {

			echo $date . " : DUPLICATE DATA\n";

		}
		else {

			$STATUS_DATA = getLoanRepayDayStatus($date, $G_TYPE[$i]);
			//print_r($STATUS_DATA);

			$sql = "
				INSERT INTO
					cf_loan_repay_status
				SET
					tDate            = '".$STATUS_DATA['tDate']."',
					tYear            = '".$STATUS_DATA['tYear']."',
					tMonth           = '".$STATUS_DATA['tMonth']."',
					tDay             = '".$STATUS_DATA['tDay']."',
					g_type           = '".$STATUS_DATA['g_type']."',
					loan_cnt         = '".$STATUS_DATA['loan_cnt']."',
					loan_cnt_sum     = '".$STATUS_DATA['loan_cnt_sum']."',
					loan_amt         = '".$STATUS_DATA['loan_amt']."',
					loan_amt_sum     = '".$STATUS_DATA['loan_amt_sum']."',
					principal        = '".$STATUS_DATA['principal']."',
					principal_sum    = '".$STATUS_DATA['principal_sum']."',
					remain_amt       = '".$STATUS_DATA['remain_amt']."',
					interest         = '".$STATUS_DATA['interest']."',
					interest_sum     = '".$STATUS_DATA['interest_sum']."',
					interest_tax     = '".$STATUS_DATA['interest_tax']."',
					interest_tax_sum = '".$STATUS_DATA['interest_tax_sum']."',
					local_tax        = '".$STATUS_DATA['local_tax']."',
					local_tax_sum    = '".$STATUS_DATA['local_tax_sum']."',
					fee              = '".$STATUS_DATA['fee']."',
					fee_sum          = '".$STATUS_DATA['fee_sum']."'";
			//echo $sql.";\n";
			sql_query($sql);

			echo $no . " : " . $STATUS_DATA['tDate'] . " " . $G_TYPE[$i] . " " . $STATUS_DATA['loan_cnt_sum'] . " " . $STATUS_DATA['principal_sum'] . " " . $STATUS_DATA['remain_amt'] . "\n";

			$no++;
			usleep(10000);

		}

	}

	$date = date('Y-m-d', strtotime($date . ' +1 day'));

}

sql_close();
exit;

?>