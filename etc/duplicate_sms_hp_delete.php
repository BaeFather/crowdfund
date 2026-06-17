<?

set_time_limit(0);

include_once("_common.php");

$res = sql_query("SELECT idx, phone_no FROM sms_request_phone GROUP BY phone_no ORDER BY idx");

$x = 1;
while( $row = sql_fetch_array($res) ) {

	$sql2 = "SELECT mb_no FROM g5_member WHERE mb_hp='".masterEncrypt($row['phone_no'],false)."' AND mb_level='1' AND member_group='F' ORDER BY mb_no DESC LIMIT 1";
	$DATA = sql_fetch($sql2);

	if($DATA['mb_no']) {
		$sqlx = "DELETE FROM sms_request_phone WHERE idx='".$row['idx']."'";
		debug_flush($x . " : " . $sqlx. ";\n" . $DATA['mb_no'] . "<br/>\n");
		//sql_query($sqlx);
		$x++;
	}

}

sql_free_result($res);

sql_close();
exit;

?>