<?php
/**
 * 서버 도메인 및 상태값 알림
 * Created by PhpStorm.
 * User: 김국현
 * Date: 2018-02-20
 * Time: 오후 12:47
 */

require_once "./common.php";
check_demo();

// 초기화
$url = "https://hellofunding.co.kr";
$title = "{$url} ";
$phones = [
        '이정환' => '010-5414-5128',
        '배재수' => '010-6406-3972',
        '이상규' => '010-8894-4740'];

$phones = ['010-3219-0414'];
$errors = array();

$today = date("Y-m-d", time());
$domainLastData = date("Y-m-d", strtotime("2019-08-27", time())); // 도메인 만료기간
$sslLastData    = date("Y-m-d", strtotime("2019-11-26", time())); // SSL 인증서 만료일


// 사이트 접속 확인
$header = get_headers($url);
if(strpos($header[0], '404') === true){ // 사이트가 안열릴때 오류
    $errors[] = "사이트에 접속할 수 없습니다. 확인부탁드립니다.";
}


// 만료기간 남은 일수
$leftDomainDay = diff_date($today, $domainLastData);
$leftSslDate   = diff_date($today, $sslLastData);

//echo $leftDomainDay."<br/>";
//echo $leftSslDate."<br/>";

// 도메인 만료기간이 7, 30일 남았을때 알림, 매달 10일
if($leftDomainDay <= 0){
    $errors[] = "도메인 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.";
}else if($leftDomainDay == 7 OR $leftDomainDay == 30 OR $leftDomainDay <= 7 OR date("d", time()) == 10){
    $errors[] = "도메인 사용 만료기간이 ".$leftDomainDay."일 남았습니다.";
}

// SSL 인증서 만료기간이 7, 30일 남았을때 알림, 매달 10일
if($leftSslDate <= 0){
    $errors[] = "SSL 인증서 사용기간이 만료되었습니다. 재연장 해주시기 바랍니다.";
}else if($leftSslDate == 7 OR $leftSslDate == 30 OR $leftSslDate <= 7 OR date("d", time()) == 10){
    $errors[] = "SSL 인증서 사용 만료기간이 ".$leftDomainDay."일 남았습니다.";
}

// 확인알람 문자 전송
if(count($errors) > 0)
{
    require_once "./lib/sms.lib.php";
    foreach($errors as $error){
        foreach($phones as $phone){
            unit_sms_send($_admin_sms_number, $phone, $title.$error);
            sleep(1);
        }
    }
}

unset($errors);
sql_close();