<?

include_once("_common.php");

if(!$member['mb_id']) { msg_replace('', '/'); exit; }

while(list($k,$v) = each($_POST)) { ${$k} = sql_real_escape_string(trim($v)); }

$sql = "
	INSERT INTO
		cf_care_service_request
	SET
		name       = '".$name."',
		phone      = '".$phone."',
		email      = '".$email."',
		is_est	   = '".$est."',
		content    = '".$content."',
		regdate      = NOW(),
		member_idx = '".$member['mb_no']."'";

if( sql_query($sql) ) {
	$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
	echo json_encode($RESULT_ARR);
}
else {
	$msg = "등록 오류가 발생하였습니다. 관리자에게 문의하십시오.";
	$RESULT_ARR = array('result' => 'FAIL', 'message' => $msg);
	echo json_encode($RESULT_ARR);
}

exit;

?>