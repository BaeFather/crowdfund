<?

$AMLCF['report_domain'] = 'http://' . $_ENV['AML_DB_HOST'];
$AMLCF['report_port']   = '8080';
$AMLCF['aml_log_table'] = 'aml_curl_request_log';

$AMLCF['db_host']    = $_ENV['AML_DB_HOST'];
$AMLCF['db_port']    = $_ENV['AML_DB_PORT'];
$AMLCF['db_user']    = $_ENV['AML_DB_USER'];
$AMLCF['db_passwd']  = $_ENV['AML_DB_PASSWORD'];
$AMLCF['db_name']    = $_ENV['AML_DB_NAME'];

$amlDBConn   = sql_connect($AMLCF['db_host'], $AMLCF['db_user'], $AMLCF['db_passwd'], $AMLCF['db_name']) or die('AML MySQL Connect Error!!!');
$amlSelectDB = sql_select_db($AMLCF['db_name'], $amlDBConn) or die('AML MySQL DB Error!!!');

sql_set_charset('utf8', $amlDBConn);
if(defined('G5_MYSQL_SET_MODE') && G5_MYSQL_SET_MODE) sql_query("SET SESSION sql_mode = ''");

?>