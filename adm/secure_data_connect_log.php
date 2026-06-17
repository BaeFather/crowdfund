<?
include_once("_common.php");

if($_SESSION['ss_accounting_admin']) {

	$linkX = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);

	$X_req_url = G5_URL . $_SERVER['REQUEST_URI'];
	$Xsql = "
		INSERT INTO connect_log
		SET
			mb_no = '".$member['mb_no']."',
			mb_id = '".$member['mb_id']."',
			request_url = '$X_req_url',
			ip = '".$_SERVER['REMOTE_ADDR']."',
			rdate = NOW()";
	sql_query($Xsql, '', $linkX);

//$X_target_date = date("Y-m-d", strtotime("-7 month"));
//sql_query("DELETE FROM connect_log WHERE LEFT(`rdate`, 10) < '$X_target_date'", '', $linkX);

	$X_req_url = $Xsql = $X_target_date = NULL;

	sql_close($linkX);

}

?>