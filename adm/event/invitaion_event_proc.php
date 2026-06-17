<?

$sub_menu = "900500";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }

$DATA = sql_fetch("SELECT COUNT(idx) AS cnt FROM invitation_event_request WHERE idx='$idx'");
if(!$DATA) {
	echo "NONE";
}
else {
	$admin_memo = sql_real_escape_string($admin_memo);
	$sql = "
		UPDATE
			invitation_event_request
		SET
			view_flag = '$view_flag',
			admin_memo = '".$admin_memo."'
		WHERE
			idx='$idx'";
	if(sql_query($sql)) {
		echo "OK";
	}
}

?>