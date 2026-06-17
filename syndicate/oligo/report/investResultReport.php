#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
###############################################################################
## /home/crowdfund/public_html/syndicate/oligo/report/investResultReport.php {투자번호}
## 9. 투자결과 리포팅
##		전달을 받아야 투자가 정상적으로 처리되었는지 확인가능하고 결과를 PUSH로 전송해줌
##		전송URL : https://m.mycereal.co.kr:8443/matcs/external/api/investResult.do
##    정상투자건 : 기표시(기관에 투자자등록시) 각투자건별 본스크립트 실행
##    제한시간(5분)내 미입금 투자건 : 투자실패시 본스크립트 실행
###############################################################################

$base_path = "/home/crowdfund/public_html";
$syndi_base_path = $base_path . "/syndicate/oligo";
include_once($syndi_base_path . "/syndication_config.php");

//print_r($_SERVER); exit;

if(!$_SERVER['argv'][1]) { echo "투자번호가 없습니다."; exit; }

$invest_idx = $_SERVER['argv'][1];

if(!$invest_idx) exit;

//////////////////////////////////////
// 전달된 투자번호로 투자내역 확인
//////////////////////////////////////
$INVEST = sql_fetch("
	SELECT
		idx, amount, member_idx, product_idx, invest_state, insert_date, insert_time, cancel_date, cancel_by
	FROM
		cf_product_invest
	WHERE (1)
		AND idx='".$invest_idx."' AND syndi_id='".$_CONF['SYNDI_ID']."'");
if(!$INVEST) { echo "투자내역이 없습니다."; exit; }
//print_r($INVEST);

//////////////////////////////////////////
// 전달된 투자번호가 자동투자인지 체크
//////////////////////////////////////////
if( $INVEST['idx'] && $INVEST['invest_state']=='Y' ) {
	$INVEST_DETAIL = sql_fetch("
		SELECT
			is_auto_invest
		FROM
			cf_product_invest_detail
		WHERE (1)
			AND invest_idx = '".$INVEST['idx']."'
			AND syndi_id = '".$_CONF['SYNDI_ID']."'
			AND insert_date = '".$INVEST['insert_date']."' AND insert_time = '".$INVEST['insert_time']."'");
}


$invest_success_amt = ($INVEST['invest_state']=='Y') ? $INVEST['amount'] : '0';
if($INVEST['invest_state']=='Y') {
	$result_cd = '0000';
	$result_msg = '성공하였습니다.';
}
else {
	$result_cd = '9999';

	if($INVEST['cancel_by']=='system') $result_msg = '투자취소-입금시간초과등의 사유';
	else if( in_array($INVEST['cancel_by'], array('user','user-api')) ) $result_msg = '투자취소-투자자에 의한 취소';
	else $result_msg = '투자취소-기타사유';
}

$MB = sql_fetch("SELECT mb_no, mb_id, mb_ci FROM g5_member WHERE mb_no='".$INVEST['member_idx']."'");
$MB['mb_point'] = get_point_sum($MB['mb_id']);

$PRDT = sql_fetch("
	SELECT
		A.idx, A.title, A.recruit_amount, A.start_datetime, A.end_datetime, invest_end_date,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product A
	WHERE (1)
		AND A.idx='".$INVEST['product_idx']."'");

$inve_yn = 'N';		// 투자가능여부 초기화
if(!$PRDT['idx']) { $inve_yn = 'N'; }
else if($PRDT['start_datetime'] > DATE_YMDHIS) { $inve_yn = 'N'; }
else if($PRDT['end_datetime'] < DATE_YMDHIS) { $inve_yn = 'N'; }
else if($PRDT['invest_end_date']!='') { $inve_yn = 'N'; }
else { $inve_yn = 'Y'; }

if( $PRDT['recruit_amount'] <= $PRDT['total_invest_amount']) {
	$invest_finished = true;
}
$status  = ($invest_finished) ? '04' : getProductStatOligo($INVEST['product_idx']);
$inve_rate = floor(($PRDT['total_invest_amount'] / $PRDT['recruit_amount']) * 100);

$auto_inve_yn = ($INVEST_DETAIL['is_auto_invest']=='1') ? 'Y' : 'N';

$REQUEST_ARR['ci']           = urlencode($MB['mb_ci']);
$REQUEST_ARR['prod_cd']      = $INVEST['product_idx'];														// 상품코드
$REQUEST_ARR['comp_cd']      = $_CONF['comp_cd'];																	// 제휴코드
$REQUEST_ARR['result_cd']    = $result_cd;																				// 투자결과( 성공(0000), 부분투자(1000), 투자취소(9999) )
$REQUEST_ARR['result_msg']   = $result_msg;																				// 투자결과메세지 (성공하였습니다.)
$REQUEST_ARR['invest_dt']    = preg_replace("/-/", "", $INVEST['insert_date']);		// 투자일자
$REQUEST_ARR['invest_tm']    = preg_replace("/:/", "", $INVEST['insert_time']);		// 투자시간
$REQUEST_ARR['invest_amt']   = (string)$INVEST['amount'];													// 투자금액
$REQUEST_ARR['invest_success_amt'] = (string)$invest_success_amt;									// 투자성공금액
$REQUEST_ARR['inve_yn']      = $inve_yn;																					// 투자가능여부
$REQUEST_ARR['status']       = $status;																						// 상태값(01:모집예정/02:모집중/03:모집취소/04:모집완료/05:상환중/06:상환완료/07:상환지연/08:연체중(단기)/09:연체중(장기)/10:부도/11:상환완료(연체))
$REQUEST_ARR['inve_num']     = $PRDT['total_invest_count'];												// 투자자수
$REQUEST_ARR['inve_amt']     = (string)$PRDT['total_invest_amount'];							// 투자금액
$REQUEST_ARR['inve_rate']    = (string)$inve_rate;																// 투자모집율
$REQUEST_ARR['auto_inve_yn'] = $auto_inve_yn;																			// 자동투자여부


///////////////////////////////////////////////////////////////////////////////
// 레포팅 및 로그 기록
///////////////////////////////////////////////////////////////////////////////
$url = $_CONF['syndi_url'] . "/matcs/external/api/investResult.do";

$exec_string = "curl -X POST";
$exec_string.= " -k -s";
$exec_string.= " -H 'Expect:'";
$exec_string.= " -H 'Content-Type: application/json'";
$exec_string.= " -A 'Mozilla/5.0'";
$exec_string.= " -d '".printJson($REQUEST_ARR)."'";
$exec_string.= " " . $url;

$log_table = 'oligo_send_report_log_' . date('Ym');

// 발송로그기록시작
$log_res = sql_query("
	INSERT INTO
		{$log_table}
	SET
		title = '투자정보알림',
		path  = '/syndicate/oligo/report/investResultReport.php',
		input = '".sql_real_escape_string($exec_string)."',
		rdate = NOW()");
$log_idx = sql_insert_id();

// 실행
$exec_result = @exec($exec_string);

// 발송결과 저장
$log_res = sql_query("UPDATE {$log_table} SET output='".sql_real_escape_string($exec_result)."', edate=NOW() WHERE idx='".$log_idx."'");


sql_close();
exit;

?>