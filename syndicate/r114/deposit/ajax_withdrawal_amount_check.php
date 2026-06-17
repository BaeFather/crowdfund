<?
################################################################################
# 실시간 포인트 출금가능금액 체크
# http://hellofunding.co.kr/investment/deposit.php 에서 ajax로 호출
################################################################################

//set_time_limit(0);

// 자사 도메인이 아닌곳에서 호출된 경우 exit;
$allow_domain  = "hellofunding.co.kr";
$allow_domain2 = "hellofunding.kr";

if(isset($_SERVER['HTTP_REFERER'])) {
	if(!preg_match("/$allow_domain/i", $_SERVER['HTTP_REFERER']) and !preg_match("/$allow_domain2/i", $_SERVER['HTTP_REFERER'])) {
		header('HTTP/1.1 404 Not Found');
	}
}
else {
	if($_REQUEST['mode']!='debug') {
		header('HTTP/1.1 404 Not Found');
	}
}


include_once("_common.php");

if(!$_SESSION['ss_mb_id']) { header('HTTP/1.1 404 Not Found'); exit; }

echo ($member['withdrawal_posible_amount']) ? $member['withdrawal_posible_amount'] : 0;

?>