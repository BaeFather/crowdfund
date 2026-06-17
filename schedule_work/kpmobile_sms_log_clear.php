#!/usr/local/php/bin/php -c /etc/php.ini -q
<?
########################################################
## KP모바일 로그 이동
## 매일 1회 실행 (00시 30분)
## 0 6 * * * /usr/local/php/bin/php -q /home/crowdfund/schedule_work/kpmobile_sms_log_clear.php yes
########################################################

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', true);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/common.cli.php');

$action = (@$_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : 'debug';

$link3 = sql_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3);
sql_set_charset('utf8', $link3);

$toDate       = date('Y-m-d');
$targetDate   = date("Y-m-d", strtotime($toDate . " -1 day"));
$targetDateYm = substr($targetDate, 0, 7);
$targetYm     = preg_replace("/-/", "", $targetDateYm);


$fromTable = "agent_msgresult";
$toTable   = "agent_msgresult_".$targetYm;

$sql2 = "INSERT INTO $toTable (SELECT * FROM $fromTable WHERE LEFT(sendTime, 7)='".$targetDateYm."' ORDER BY id ASC)";
$sql3 = "DELETE FROM $fromTable WHERE LEFT(sendTime, 7)='".$targetDateYm."'";
if($action=='yes') {
	if( sql_query($sql2, "", $link3) ) {
		echo $targetDate . " DATA MOVE SUCCESS\n";
		if( sql_query($sql3, "", $link3) ) { echo $targetDate . " DATA DELETE SUCCESS\n"; }
	}
}
else {
	echo $sql2 . "\n";
	echo $sql3 . "\n";
}


//////////////////////////
// 로그테이블 생성
//////////////////////////
$new_table = "agent_msgresult_".date('Ym', strtotime($toDate . 'first day of +1 month'));

$res  = sql_query("SHOW TABLES LIKE '%".$new_table."%'", "", $link3);
$rows = sql_num_rows($res);
if(!$rows) {
	$creat_sql = "CREATE TABLE ".$new_table." LIKE ".$fromTable;

	if($action=="yes") {
		if( sql_query($creat_sql, "", $link3) ) echo $targetDate . " CREATE TABLE SUCCESS\n";
	}
	else {
		echo $creat_sql . "\n";
	}
}


sql_close($link3);

exit;

?>