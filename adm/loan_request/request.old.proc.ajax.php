<?

include_once('./_common.php');
auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while( list($k, $v) = each($_POST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

$chk_count = count($_POST['chk']);
if($chk_count) {
	$chk_idx_array = "";
	for($i=0; $i<$chk_count; $i++) {
		$chk_idx_array.= "'".$_POST['chk'][$i]."',";
	}
	$chk_idx_array = substr($chk_idx_array, 0, strlen($chk_idx_array)-1);
}


if($chk_count > 0) {
	$sql = "
		UPDATE
			loan_info
		SET
			admin_id = '".$member['mb_id']."',
			admin_check_datetime = NOW()
		WHERE
			seq IN (".$chk_idx_array.")";
	//echo $sql;
	if( sql_query($sql) ) {
		$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>'');
		echo json_encode($RETURN_ARR);
	}
	else {
		$RETURN_ARR = array('result'=>'FAIL', 'message'=>'업데이트 오류 입니다.\n기술담당자에게 문의하십시요.');
		echo json_encode($RETURN_ARR);
	}
}
else {
	$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'업데이트 대상을 선택하십시요.');
	echo json_encode($RETURN_ARR);
}


exit;

?>