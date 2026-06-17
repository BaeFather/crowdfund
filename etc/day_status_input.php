<?
###############################################################################
## 대출 및 상환 일별 합산액 기록 (일대사)
## 2,30,50 0 * * *  /usr/local/php/bin/php -q /home/crowdfund/public_html/etc/day_status_input.php [2021-10-14]		// 파라미터 없으면 어제를 기준일로 정한다.
## 0 12,18 * * *  /usr/local/php_new/bin/php -q /home/crowdfund/public_html/etc/day_status_input.php &
###############################################################################

$base_path = "/home/crowdfund/public_html";
include_once($base_path . '/common.cli.php');


function getLoanRepayDayStatus($tDate, $gb='A') {

	$main_table = "cf_loan_repay_status";
	$serviceSdd = "2016-09-01";
	$serviceSdt = $serviceSdd . " 00:00:00";

	$tDate = ($tDate) ? $tDate : date('Y-m-d', strtotime('-1 day'));
	$tDateSdt = $tDate . " 00:00:00";
	$tDateEdt = $tDate . " 23:59:59";

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
	else if($gb == "4") {
		// 동산
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = " AND B.category = '1'";
	}
	else {
		$status_add_qry  = " AND g_type = '".$gb."'";
		$product_add_qry = "";
	}



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
			AND B.isTest = '' AND B.recruit_amount > 10000
			AND B.state IN(1,2,4,5,8,9)
			AND B.loan_start_date = '".$tDate."'
			$product_add_qry";
	//echo $sql.";\n";
	$DATA1 = sql_fetch($sql);

	/* 누적데이터 */
	$sql = "
		SELECT
			COUNT(B.idx) AS cnt,
			IFNULL(SUM(B.recruit_amount),0) AS amt
		FROM
			cf_product B
		WHERE 1
			AND B.isTest = '' AND B.recruit_amount > 10000
			AND B.state IN(1,2,4,5,8,9)
			AND B.loan_start_date BETWEEN '".$serviceSdd."' AND '".$tDate."'
			$product_add_qry";
	//echo $sql.";\n";
	$DATA2 = sql_fetch($sql);

	$LOAN['cnt']     = $DATA1['cnt'];
	$LOAN['amt']     = $DATA1['amt'];
	$LOAN['cnt_sum'] = $DATA2['cnt'];
	$LOAN['amt_sum'] = $DATA2['amt'];


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
			AND A.banking_date BETWEEN '".$tDateSdt."' AND '".$tDateEdt."'
			$product_add_qry";
	//echo $sql.";\n";
	$DATA3 = sql_fetch($sql);

	/* 누적 데이터 */
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
			AND A.banking_date BETWEEN '".$serviceSdt."' AND  '".$tDateEdt."'
			$product_add_qry";
	//echo $sql.";\n";
	$DATA4 = sql_fetch($sql);

	$REPAY = $DATA3;

	$REPAY['principal_sum']    = $DATA4['principal'];
	$REPAY['interest_sum']     = $DATA4['interest'];
	$REPAY['interest_tax_sum'] = $DATA4['interest_tax'];
	$REPAY['local_tax_sum']    = $DATA4['local_tax'];
	$REPAY['fee_sum']          = $DATA4['fee'];


	$remain_amt = $LOAN['amt_sum'] - $REPAY['principal_sum'];		// 대출잔액

	/* 연체원금 추출 */
	/*
	$sql = "
		SELECT
			B.idx, B.recruit_amount, B.overdue_start_date,
			(SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE product_idx=B.idx AND LEFT(banking_date,10) <= '".$tDate."') AS paid_principal
		FROM
			cf_product B
		WHERE 1
			AND B.isTest = '' AND B.recruit_amount > 10000
			AND B.state = '8'
			$product_add_qry
		ORDER BY
			B.idx";
	*/

	// overdue_start_date 가져오는 방법 변경
	$sql = "
		SELECT
			B.idx, B.recruit_amount,
			(SELECT IFNULL(MIN(overdue_start_date),'') FROM cf_product_success WHERE product_idx=B.idx AND (overdue_end_date IS NULL OR overdue_end_date = '0000-00-00')) AS overdue_start_date,
			(SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE product_idx=B.idx AND banking_date BETWEEN '".$serviceSdt."' AND  '".$tDateEdt."') AS paid_principal
		FROM
			cf_product B
		WHERE 1
			AND B.isTest = '' AND B.recruit_amount > 10000
			AND B.state = '8'
			$product_add_qry
		ORDER BY
			B.idx";

	$res  = sql_query($sql);
	$rows = $res->num_rows;

	$overdue_product_count = $sum_overdue_principal = 0;

	for($i=0; $i<$rows; $i++) {

		$R = sql_fetch_array($res);

		if($R['overdue_start_date']) {
			$overdue_day_count = max(0, @floor((strtotime($tDate) - strtotime($R['overdue_start_date'])) / 86400));

			echo "수집대상일: " . $tDate ."\n";
			echo "연체등록일: " . $R['overdue_start_date'] ."\n";
			echo "연체일로부터 ".$overdue_day_count."일 경과\n\n";

			if($overdue_day_count >= 30) {
				if( in_array($R['idx'], array('8068','8081','8109')) ) {
					// 매출채권 K상품 연체처리 방지 (내부 협의로 결정된 사안 : 2022-03-08 류재영본부장,이기륜과장 배석)
				}
				else {
					$overdue_product_count += 1;
					$overdue_principal = $R['recruit_amount'] - $R['paid_principal'];
					$sum_overdue_principal += $overdue_principal;
				}
			}

		}

	}

	$OVERDUE['overdue_count']     = $overdue_product_count;
	$OVERDUE['overdue_principal'] = $sum_overdue_principal;
	$OVERDUE['overdue_perc']      = @(($sum_overdue_principal / $remain_amt) * 100);


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
		'overdue_count'    => (int)$OVERDUE['overdue_count'],
		'overdue_principal'=> (int)$OVERDUE['overdue_principal'],
		'overdue_perc'     => $OVERDUE['overdue_perc']
	);

	return $RETURN_VAL;

}



/////////////////////////////////////////////////
// 기록시작
/////////////////////////////////////////////////

// 기준일 설정
if(@$_SERVER['argv'][1]) {
	if( strlen($_SERVER['argv'][1]) <> 10 ) { exit; }
	$date = date("Y-m-d", strtotime($_SERVER['argv'][1] . " -1 day"));
}
else {
	$date = date("Y-m-d", strtotime("-1 day"));		// 기준일 기본 값은 현재일의 전일
}


$G_TYPE = array('A','1','2','3','4');

for($i=0; $i<count($G_TYPE); $i++) {

	$STATUS_DATA = getLoanRepayDayStatus($date, $G_TYPE[$i]);
	print_r($STATUS_DATA);

	$RECORDED = sql_fetch("SELECT tDate, g_type FROM cf_loan_repay_status WHERE tDate='".$date."' AND g_type='".$G_TYPE[$i]."'");

	if($RECORDED['tDate'] && $RECORDED['g_type']) {
		// ★★★★ 기존자료의 업데이트는 외부팀의 요청이 왔을 경우이거나 중대한 오류로 데이터를 수정해야 할 경우에만 활성화 합시다. ★★★★

		$sql = "
			UPDATE
				cf_loan_repay_status
			SET
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
				fee_sum          = '".$STATUS_DATA['fee_sum']."',
				overdue_count    = '".$STATUS_DATA['overdue_count']."',
				overdue_principal= '".$STATUS_DATA['overdue_principal']."',
				overdue_perc     = '".$STATUS_DATA['overdue_perc']."'
			WHERE 1
				AND tDate = '".$STATUS_DATA['tDate']."' AND g_type = '".$STATUS_DATA['g_type']."'";

	}
	else {

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
				fee_sum          = '".$STATUS_DATA['fee_sum']."',
				overdue_count    = '".$STATUS_DATA['overdue_count']."',
				overdue_principal= '".$STATUS_DATA['overdue_principal']."',
				overdue_perc     = '".$STATUS_DATA['overdue_perc']."'";

	}

	//print_r($sql);
	if($sql) $result = sql_query($sql);


	echo "[".$STATUS_DATA['tDate'] . " 기준 데이터 수집]\n";
	echo "  G_TYPE: " . $G_TYPE[$i] . "\n";
	echo "  누적대출상품수: " . $STATUS_DATA['loan_cnt_sum'] . "\n";
	echo "  누적대출금액: " . $STATUS_DATA['principal_sum'] . "\n";
	echo "  대출잔액: " . $STATUS_DATA['remain_amt'] . "\n";
	echo "  연체건수: " . $STATUS_DATA['overdue_count'] . "\n";
	echo "  연체금액: " . $STATUS_DATA['overdue_principal'] . "\n";
	echo "  연체율: " . $STATUS_DATA['overdue_perc'] . "\n";
	echo "  데이터 등록/수정: " . sql_affected_rows() . "\n\n";


}

sql_close();
exit;

?>