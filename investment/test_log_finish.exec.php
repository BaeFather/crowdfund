<?php
// php -q /home/crowdfund/public_html/investment/test_log_finish.exec.php
// 로그마감 : /usr/local/php/bin/php -q /home/crowdfund/public_html/investment/test_log_finish.exec.php $log_idx $thrSec

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

if($_SERVER['argv'][1]) $log_idx = $_SERVER['argv'][1];
if($_SERVER['argv'][2]) $thrSec  = $_SERVER['argv'][2];


echo LoadingLogRegist($log_idx, '', '', $thrSec);

sql_close();
exit;

?>