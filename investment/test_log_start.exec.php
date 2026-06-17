<?php
// php -q /home/crowdfund/public_html/investment/test_log_start.exec.php
// 로그등록 : /usr/local/php/bin/php -q /home/crowdfund/public_html/investment/test_log_start.exec.php $ip $path

set_time_limit(0);

$path = '/home/crowdfund/public_html';
include_once($path . '/config.cli.php');

if(!$CONF['loading_time_check']) { exit; }

include_once($path . '/lib/common.lib.php');
include_once($path . '/lib/loading_time.lib.php');
include_once($path . '/data/dbconfig.php');

$connect_db = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
$select_db  = sql_select_db(G5_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');
$g5['connect_db'] = $connect_db;
sql_set_charset('utf8', $connect_db);

if($_SERVER['argv'][1]) $ip = $_SERVER['argv'][1];
if($_SERVER['argv'][2]) $path = $_SERVER['argv'][2];


echo LoadingLogRegist('', $ip, $path, '');

sql_close();
exit;

?>