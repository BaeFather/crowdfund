<?php
/**
 * 서버 도메인 및 상태값 알림
 * Created by PhpStorm.
 * User: 김국현
 * Date: 2018-02-20
 * Time: 오후 12:47
 */
$doc_root = dirname($_SERVER["SCRIPT_FILENAME"]);
chdir($doc_root);

include_once("../common.cli.php");
include_once("../lib/sms.lib.php");

check_demo();

// 초기화
$url = "https://hellofunding.co.kr";
$title = "[만료기간안내]\n\n";

$PHONES = array(
	'01064063972',		// 배재수
	'01086246176',		// 전승찬
	'01088944740'			// 이상규
);

$errors = array();

$today = date("Y-m-d", time());

$domainLastData      = date("Y-m-d", strtotime("2020-07-27", time()));		// 도메인 만료기간
$sslLastData		     = date("Y-m-d", strtotime("2019-11-07", time()));		// SSL인증서 만료일
$bankFintechLastData = date("Y-m-d", strtotime("2019-08-19", time()));		// 뱅킹인증서(인사이드뱅크-헬로핀테크)
$bankDaebuLastData   = date("Y-m-d", strtotime("2019-08-19", time()));		// 뱅킹인증서(인사이드뱅크-헬로크라우드대부)
$taxinvoiceLastData  = date("Y-m-d", strtotime("2019-08-21", time()));		// 팝빌세금계산서발급용 인증서

// 평일만 조회하고 주말은 스킵
if( in_array(date('w'), array(0, 6)) ) { exit; }

/*
// 사이트 접속 확인
$header = get_headers($url);
if(strpos($header[0], '404') === true){ // 사이트가 안열릴때 오류
	$errors[] = "사이트에 접속할 수 없습니다. 확인부탁드립니다.";
}
*/

// 만료기간 남은 일수
$leftDomainDay      = diff_date($today, $domainLastData);
$leftSslDay         = diff_date($today, $sslLastData);
$leftBankFintechDay = diff_date($today, $bankFintechLastData);
$leftBankDaebuDay   = diff_date($today, $bankDaebuLastData);
$leftTaxinvoiceDay  = diff_date($today, $taxinvoiceLastData);

//echo $leftDomainDay."<br/>";
//echo $leftSslDate."<br/>";

$msg = "";

$line = "\n\n-----------------\n\n";

// 도메인 만료일 알림
if($leftDomainDay <= 0) {
	$msg.= "도메인 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.".$line;
}
else {
	if( $leftDomainDay, 30 ) { $msg.= "도메인 사용 만료일이 ".$leftDomainDay."일 남았습니다.".$line; }
}

// SSL인증서 만료일 알림
if($leftSslDay <= 0) {
	$msg.= "SSL 인증서 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.".$line;
}
else {
	if( $leftSslDay <= 20 ) { $msg.= "SSL인증서 사용 만료일이 ".$leftSslDay."일 남았습니다.".$line; }
}

// 뱅킹인증서(인사이드뱅크-헬로핀테크) 만료일 알림, 매달 10일
if($leftBankFintechDay <= 0) {
	$msg.= "뱅킹인증서(인사이드뱅크-헬로핀테크) 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.".$line;
}
else {
	if( $leftBankFintechDay <= 20 ) { $msg.= "뱅킹인증서(인사이드뱅크-헬로핀테크) 사용 만료일이 ".$leftBankFintechDay."일 남았습니다.".$line; }
}

// 뱅킹인증서(인사이드뱅크-헬로크라우드대부) 만료일 알림
if($leftBankDaebuDay <= 0) {
	$msg.= "뱅킹인증서(인사이드뱅크-헬로크라우드대부) 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.".$line;;
}
else {
	if( $leftBankDaebuDay <= 20 ) { $msg.= "뱅킹인증서(인사이드뱅크-헬로크라우드대부) 사용 만료일이 ".$leftBankDaebuDay."일 남았습니다.".$line; }
}

// 팝빌세금계산서발급용 인증서 만료일이 알림
if($leftTaxinvoiceDay <= 0) {
	$msg.= "세금계산서발급용인증서 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.".$line;
}
else {
	if( $leftTaxinvoiceDay <= 20 ) { $msg.= "세금계산서발급용인증서 사용 만료일이 ".$leftTaxinvoiceDay."일 남았습니다.".$line; }
}



/**
 *
 * 2018-03-04 추가
	내부담당자에게 펌뱅킹수수료 지급 알림문자 발송

	발송일 : 매월 마지막주 공휴일을 제외한 첫영업일
	발송대상 : 개발담당자 및 운영담당자
	문자내용 : 신한인사이드뱅크 이용수수료를 확인하십시요.
*/

if($today == date("Y-m-d", strtotime("last Monday", mktime(0, 0, 0, intval(date('m'))+1, 0, intval(date('Y')))))) { // 마지막 주 월요일인가?
	if( !in_array(date('w'), array(0, 6)) ) {
		$msg.= "신한 인사이드뱅크 이용수수료를 확인하십시요.";
	}
}

// 확인알람 문자 전송
if($msg) {
	for($i=0; $i<count($PHONES); $i++) {
		unit_sms_send($_admin_sms_number, $PHONES[$i], $title.$msg);
	}
}

sql_close();
exit;

?>