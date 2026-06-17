<?

include_once('./_common.php');

$log_idx = @shell_exec("/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/test_log_start.exec.php {$_SERVER['REMOTE_ADDR']} {$_SERVER['SCRIPT_NAME']}");
$sdt = get_microtime();


include_once('./_head.php');


for($i=0,$j=1; $i<3; $i++,$j++) {
	echo $j . "<br/>\n";
	usleep(5000);
}


include_once('./_tail.php');

if($log_idx) {
	$thrSec = get_microtime() - $sdt;
	@shell_exec("/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/test_log_finish.exec.php {$log_idx} {$thrSec}");
}

exit;
?>