#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
###############################################################################
## /home/crowdfund/public_html/syndicate/oligo/report/investCancelReport.php 103517
## 16. 투자취소발생시 제휴사로 보고
##		- ★★ 올리고에서 투자한 내역을 헬로펀딩에 로그인하여 취소할 경우 올리고에 레포팅하기 ★★
##		- 사용자가 헬로펀딩에서 직접취소할 경우 /deposit/ajax_funding_cancel.php를 통하여 본 파일을 실행한다.
##		- 레포팅URL : https://m.mycereal.co.kr:8443/matcs/external/api/investCancel.do
##		- 기표전 과투자로 인한 투자취소건에 대해서도 폰 파일을 실행해줄 것!!
###############################################################################

$base_path = "/home/crowdfund/public_html";
$syndi_base_path = $base_path . "/syndicate/oligo";
include_once($syndi_base_path . "/syndication_config.php");

//print_r($_SERVER); exit;

if(!$_SERVER['argv'][1]) { echo "투자번호가 없습니다."; exit; }

$invest_idx = $_SERVER['argv'][1];

if(!$invest_idx) exit;

$INVEST = sql_fetch("
	SELECT
		amount, member_idx, product_idx, cancel_by, cancel_date
	FROM
		cf_product_invest
	WHERE (1)
		AND idx='".$invest_idx."'
		AND syndi_id='".$_CONF['SYNDI_ID']."'");
if(!$INVEST) { echo "투자내역이 없습니다."; exit; }

$MB = sql_fetch("SELECT mb_no, mb_id, mb_ci FROM g5_member WHERE mb_no='".$INVEST['member_idx']."'");
$MB['mb_point'] = get_point_sum($MB['mb_id']);
if(!$MB['mb_no']) { echo "회원정보가 없습니다."; exit; }


$CANCEL_DATE = explode(" ", $INVEST['cancel_date']);
$cancel_dt = preg_replace("/-/", "", $CANCEL_DATE[0]);
$cancel_tm = preg_replace("/:/", "", $CANCEL_DATE[1]);

$REQUEST_ARR['ci']             = urlencode($MB['mb_ci']);
$REQUEST_ARR['prod_cd']        = (string)$INVEST['product_idx'];
$REQUEST_ARR['comp_cd']        = $_CONF['comp_cd'];
$REQUEST_ARR['cancel_amount']  = (string)$INVEST['amount'];
$REQUEST_ARR['balance_amount'] = (string)$MB['mb_point'];
$REQUEST_ARR['cancel_dt']      = (string)$cancel_dt;
$REQUEST_ARR['cancel_tm']      = (string)$cancel_tm;



///////////////////////////////////////////////////////////////////////////////
// 레포팅 및 로그 기록
///////////////////////////////////////////////////////////////////////////////
$url = $_CONF['syndi_url'] . "/matcs/external/api/investCancel.do";

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
		title = '투자취소알림',
		path  = '/syndicate/oligo/report/investCancelReport.php',
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