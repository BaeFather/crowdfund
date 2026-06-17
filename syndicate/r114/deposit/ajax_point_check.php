<?
################################################################################
# 실시간 포인트 변동사항 체크 및 적용
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
	if(!$_COOKIE['debug_mode']) {
		header('HTTP/1.1 404 Not Found');
	}
}


include_once("_common.php");

if(!$_SESSION['ss_mb_id']) {
	header('HTTP/1.1 404 Not Found');
	exit;
}

//print_r($member);
//exit;

// 포인트 체크 (실시간 추출된 포인트와 셋팅된 포인트가 다르면 포인트정보 업데이트 후 출력)
if($config['cf_use_point']) {
	$sum_point = get_point_sum($member['mb_id']);
	if($member['mb_point'] <> $sum_point) {
		$sql = "UPDATE {$g5['member_table']} SET mb_point = '$sum_point' WHERE mb_id = '{$member['mb_id']}'";
		sql_query($sql);
	}
}

echo ($sum_point) ? $sum_point : '0';


?>