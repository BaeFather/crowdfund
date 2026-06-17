<?php

include_once('../common.cli.php');

$ymd = trim($_REQUEST['ymd']);
if(!$ymd) { die(); exit; }

$ymd2 = preg_replace('/-/', '', $ymd);
$ARR['t_date'] = $ymd2;

$ymd_sdatetime = $ymd . " 00:00:00";
$ymd_sdatetime2 = preg_replace('/(-| |:)/', '', $ymd_sdatetime);

$ymd_edatetime = $ymd . " 23:59:59";
$ymd_edatetime2 = preg_replace('/(-| |:)/', '', $ymd_edatetime);


// 예치금 입금
$sql = "
	SELECT
		COUNT(FB_SEQ) AS deposit_cnt,
		IFNULL(SUM(CAST(TR_AMT AS unsigned)), 0) AS deposit_amt
	FROM
		IB_FB_P2P_IP
	WHERE 1
		AND SR_DATE = '".$ymd2."'
		AND TR_AMT_GBN = '10'";
$IB_DETAIL_LOG = sql_fetch($sql);

$ARR["deposit_cnt"] = $IB_DETAIL_LOG["deposit_cnt"];
$ARR["deposit_amt"] = $IB_DETAIL_LOG["deposit_amt"];


// 예치금 출금 (인사이드뱅크 로그에서 가져옴)
$withdrawal_log_table = (substr($ymd2, 0, 4)==date('Y')) ? "IB_request_log" : "IB_request_log_" . substr($ymd2, 0, 4);
$sql = "
	SELECT
		request_arr
	FROM
		$withdrawal_log_table
	WHERE 1
		AND request_code='3200' AND rcode='00000000' AND LEFT(regdate,10)='".$ymd."'
	ORDER BY
		idx";
//echo $sql;
$res = sql_query($sql);
$rows = $res->num_rows;

$ARR['withdrawal_cnt'] = $ARR['withdrawal_amt'] = '0';
for($i=0; $i<$rows; $i++) {
	$ROW = sql_fetch_array($res);
	if( preg_match("/\&TRAN_AMT\=/", $ROW['request_arr']) ) {
		$amt = str_f6($ROW['request_arr'], "&TRAN_AMT=", "&");

		$ARR['withdrawal_cnt'] += 1;
		$ARR['withdrawal_amt'] += $amt;
	}
}
sql_free_result($res);
$ARR['withdrawal_cnt'] = (string)$ARR['withdrawal_cnt'];
$ARR['withdrawal_amt'] = (string)$ARR['withdrawal_amt'];


// 상환금 입금
$sql = "
	SELECT
		COUNT(FB_SEQ) AS deposit_cnt,
		IFNULL(SUM(CAST(TR_AMT AS unsigned)), 0) AS deposit_amt
	FROM
		IB_FB_P2P_IP
	WHERE 1
		AND SR_DATE = '".$ymd2."'
		AND TR_AMT_GBN = '20'";
$IB_DETAIL_LOG2 = sql_fetch($sql);

$ARR["loaner_deposit_cnt"] = $IB_DETAIL_LOG2["deposit_cnt"];
$ARR["loaner_deposit_amt"] = $IB_DETAIL_LOG2["deposit_amt"];



// 대출실행
$sql = "
	SELECT
		DC_NB,
		IFNULL(SUM(CAST(DCA_IP_AMT AS unsigned)), 0) AS loan_start_amt
	FROM
		IB_FB_P2P_DC_IP
	WHERE 1
		AND SR_DATE = '".$ymd2."' AND EXEC_YN='Y' AND ERR_CD='00000000'
	GROUP BY
		DC_NB";
$res = sql_query($sql);
$rows = $res->num_rows;

$ARR['loan_start_cnt'] = 0;
$ARR['loan_start_amt'] = 0;
for($i=0; $i<$rows; $i++) {
	$ROW = sql_fetch_array($res);
	if($ROW['DC_NB']) {
		$ARR['loan_start_cnt'] += 1;
		$ARR['loan_start_amt'] += $ROW['loan_start_amt'];
	}
}
$ARR['loan_start_cnt'] = (string)$ARR['loan_start_cnt'];
$ARR['loan_start_amt'] = (string)$ARR['loan_start_amt'];


// 원리금 배분처리
$divide_table = (substr($ymd2, 0, 4)==date('Y')) ? "IB_FB_P2P_REPAY_REQ" : "IB_FB_P2P_REPAY_REQ_" . substr($ymd2, 0, 4);
$sql = "
	SELECT
		IFNULL(SUM(TOTAL_S_CNT),0) AS cnt,
		IFNULL(SUM(TOTAL_TR_AMT), 0) AS TOTAL_TR_AMT_SUM
	FROM
		$divide_table
	WHERE 1
		AND SDATE = '".$ymd2."' AND RESP_CODE='00000000'
	ORDER BY
		SDATE DESC";
//echo $sql;
$DIVIDE = sql_fetch($sql);
$ARR['divide_cnt'] = $DIVIDE['cnt'];
$ARR['divide_amt'] = $DIVIDE['TOTAL_TR_AMT_SUM'];



// 원리금→예치금전환
$sql = "
	SELECT
		COUNT(idx) AS cnt,
		IFNULL((SUM(interest) + SUM(principal)), 0) AS amount
	FROM
		cf_product_give
	WHERE 1
		AND receive_method = '2'
		AND banking_date BETWEEN '".$ymd_sdatetime."' AND '".$ymd_edatetime."'
	ORDER BY
		banking_date DESC";
//print_r($sql."\n");
$GIVE1 = sql_fetch($sql);

$ARR["point_repay_cnt"] = $GIVE1['cnt'];
$ARR["point_repay_amt"] = $GIVE1['amount'];



// 원리금→환급계좌이체
$sql = "
	SELECT
		COUNT(idx) AS cnt,
		IFNULL((SUM(interest) + SUM(principal)), 0) AS amount
	FROM
		cf_product_give
	WHERE 1
		AND receive_method = '1'
		AND banking_date BETWEEN '".$ymd_sdatetime."' AND '".$ymd_edatetime."'
	ORDER BY
		banking_date DESC";
$GIVE2 = sql_fetch($sql);
$ARR['cash_repay_cnt'] = $GIVE2['cnt'];
$ARR['cash_repay_amt'] = $GIVE2['amount'];






echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

sql_close();
exit;


?>