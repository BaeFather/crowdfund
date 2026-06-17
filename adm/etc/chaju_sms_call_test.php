<?
include_once('./_common.php');

$prd_idx = "8427";



// 기표 안내 문자 차주에게 발송
$exec_path4    = "/usr/local/php/bin/php -q " . G5_ADMIN_PATH . "/mortgage/chaju_sms.php " . $prd_idx . " 1";
$exec_result4  = shell_exec($exec_path4);

echo $exec_result4;
?>