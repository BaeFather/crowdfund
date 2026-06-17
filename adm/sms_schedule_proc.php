<?
include_once('./_common.php');

$link3      = sql_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3);
$select_db3 = sql_select_db(G5_MYSQL_DB3, $link3) or die('MySQL DB Error!!!');
sql_set_charset('utf8', $link3);

auth_check($auth[$sub_menu], "w");

if(!$msgtype) $msgtype = 'mms';
if(!in_array($msgtype, array('mms','sms'))) { alert('!!!!'); }

//print_r($_REQUEST); exit;

// POST 받은 데이터를 변수화
foreach($_POST as $k=>$v) { ${$_POST[$k]} = $v; }


if($mode=='select_delete') {

	$chk_count = count($_POST['chk']);

	$idx_arr = "";
	for($i=0,$j=1; $i<$chk_count; $i++,$j++) {
		$idx_arr.= $_POST['chk'][$i].",";
	}
	$idx_arr = substr($idx_arr, 0, strlen($idx_arr)-1);

	$sql = "DELETE FROM agent_msgqueue WHERE id IN($idx_arr)";
	$res = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);
	$delete_count = sql_affected_rows($link3);
	echo number_format($chk_count). "건 요청 중 " . number_format($delete_count) ."건 삭제";

}
else if($mode=='search_delete') {

	if(!$target_month) $target_month = 'now';

	if($target_month=='now') {
		$now_flag = true;
		$target_month = date('Ym');
	}

	if($target_month > date('Ym')) { alert('도래하지 않는 대상년월 입니다.'); }

	$where = " 1=1 ";
	$where.= ( preg_match('/dev\.hello/i', $_SERVER['HTTP_HOST']) ) ? " AND etc1='dev' " : "";
	$where.= ($msgtype=='mms') ? " AND kind='1' " : " AND kind='0' ";
	if($isReserved) $where.= " AND isReserved='$isReserved' ";
	if($date_field) {
		if($reqdateS) $where.= " AND LEFT($date_field, 10)>='$reqdateS' ";
		if($reqdateE) $where.= " AND LEFT($date_field, 10)<='$reqdateE' ";
	}
	if($receiveNo) $where.= " AND receiveNo LIKE '%$receiveNo%' ";
	if($cont) $where.= " AND message LIKE '%$cont%' ";


	$sql = "DELETE FROM agent_msgqueue WHERE $where";
	$res = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);
	$delete_count = sql_affected_rows($link3);
	echo number_format($delete_count) ."건 삭제";

}
else if($mode=='search_cancel') {
	$chk_count = count($_POST['chk']);

	$idx_arr = "";
	for($i=0,$j=1; $i<$chk_count; $i++,$j++) {
		$idx_arr.= $_POST['chk'][$i].",";
	}
	$idx_arr = substr($idx_arr, 0, strlen($idx_arr)-1);

	//$sql = "UPDATE cf_agent_msgqueue SET deleted='Y' WHERE id IN($idx_arr)";
	//$res = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);
	//$cancel_count = sql_affected_rows($link3);

	$sql = "UPDATE cf_agent_msgqueue SET deleted='Y' WHERE send_id IN($idx_arr)";
	$res = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);
	$cancel_count = sql_affected_rows($link3);

	echo number_format($chk_count). "건 요청 중 " . number_format($cancel_count) ."건 취소 $sql";
}

exit;

?>