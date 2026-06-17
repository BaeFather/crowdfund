#!/usr/local/php/bin/php -c /etc/php.ini -q
<?

exit;

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', false);
define('G5_MYSQLI_USE', true);

$base_path = "/home/crowdfund/public_html";
include_once($base_path . "/common.php");


$table1 = 'cf_product_history';
$table2 = 'cf_product_history_x';

$sql  = "SELECT * FROM $table1 WHERE idx > 0 ORDER BY idx, insert_date";
$res  = sql_query($sql);
$rows = $res->num_rows;
debug_flush($rows);

sql_query("TRUNCATE TABLE $table2");

for($i=0; $i<$rows; $i++) {

	$row = sql_fetch_array($res);

	$sqlxx = "";
	while( list($key, $value) = each($row) ) {
		$sqlxx.= "'".sql_real_escape_string($value)."',";
	}

	$sqlxx = substr($sqlxx, 0, strlen($sqlxx)-1);

	$sqlx = " INSERT INTO $table2 VALUES (".$sqlxx.")";

	debug_flush($i. " :\n" . $sqlx . "\n\n");
	sql_query($sqlx);


	usleep(10000);

}
sql_free_result($res);


echo "Finished\n";

exit;

?>