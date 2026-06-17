#!/usr/local/php/bin/php -c /etc/php.ini -q
<?

exit;

set_time_limit(0);

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', false);
define('G5_MYSQLI_USE', true);

$base_path = "/home/crowdfund/public_html";
include_once($base_path . "/common.php");


$table1 = 'g5_member';
$table2 = 'g5_member_x';

$sql = "SELECT * FROM $table1 ORDER BY mb_no";
$res = sql_query($sql);
$rows = $res->num_rows;
debug_flush($rows);

$i = 0;
while( $row = sql_fetch_array($res) ) {

	if($row['po_content']) $row['po_content'] = sql_real_escape_string($row['po_content']);
	if($row['po_memo']) $row['po_memo'] = sql_real_escape_string($row['po_memo']);
	if($row['mb_name']) $row['mb_name'] = sql_real_escape_string($row['mb_name']);
	if($row['mb_co_name']) $row['mb_co_name'] = sql_real_escape_string($row['mb_co_name']);
	if($row['mb_addr1']) $row['mb_addr1'] = sql_real_escape_string($row['mb_addr1']);
	if($row['mb_addr2']) $row['mb_addr2'] = sql_real_escape_string($row['mb_addr2']);
	if($row['mb_addr_jibeon']) $row['mb_addr_jibeon'] = sql_real_escape_string($row['mb_addr_jibeon']);
	if($row['bank_name']) $row['bank_name'] = sql_real_escape_string($row['bank_name']);
	if($row['bank_private_name']) $row['bank_private_name'] = sql_real_escape_string($row['bank_private_name']);
	if($row['va_private_name2']) $row['va_private_name2'] = sql_real_escape_string($row['va_private_name2']);

	$sqlx = "
		UPDATE
			$table2
		SET
			mb_name        = '".$row['mb_name']."',
			mb_co_name     = '".$row['mb_co_name']."',
			mb_addr1       = '".$row['mb_addr1']."',
			mb_addr2       = '".$row['mb_addr2']."',
			mb_addr_jibeon = '".$row['mb_addr_jibeon']."',
			bank_name      = '".$row['bank_name']."',
			bank_private_name = '".$row['bank_private_name']."',
			va_private_name2 = '".$row['va_private_name2']."'
		WHERE
			mb_no = '".$row['mb_no']."'";

	if(sql_query($sqlx)){
		debug_flush($i . "\n");
		$i++;
	}

}


echo "Finished\n";

exit;

?>