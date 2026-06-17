#!/usr/local/php/bin/php -q
<?
exit;

set_time_limit(0);

$sdate = '2021-05-01';

$ets = time();
$sts = strtotime($sdate);

$days = ceil(($ets - $sts) / 86400);

for($i=0; $i<$days; $i++) {

	$tdate = date("Y-m-d", strtotime($sdate . " + {$i}day"));

	$exec = "/usr/local/php/bin/php -q /home/crowdfund/public_html/etc/day_status_input.test.php " . $tdate;
	//echo $tdate . ":\n";
	echo shell_exec($exec);
	echo "\n\n";

}

exit;

?>