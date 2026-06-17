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

if($action=='invalid') {
	if($chk_count > 0) {
		$sql = "
			UPDATE
				event_entry_log
			SET
				invalid = '1',
				invalid_date = '".G5_TIME_YMDHIS."',
				last_edit_name = '".$member['mb_name']."'
			WHERE
				idx IN (".$chk_idx_array.")";
		if( sql_query($sql) ) {
			$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>'');
			echo json_encode($RETURN_ARR);
		}
	}
	else {
		$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'대상자를 선택하십시요.');
		echo json_encode($RETURN_ARR);
	}
}

else if($action=='paid') {
	if($chk_count > 0) {
		for($i=0; $i<$chk_count; $i++) {
			$row = sql_fetch("SELECT idx FROM event_entry_log WHERE idx='".$_POST['chk'][$i]."' AND paid=''");
			if($row['idx']) {
				$sql = "
					UPDATE
						event_entry_log
					SET
						paid = '1',
						paid_date = '".G5_TIME_YMDHIS."',
						last_edit_name = '".$member['mb_name']."'
					WHERE
						idx = '".$_POST['chk'][$i]."'";
				sql_query($sql);
			}
		}
		$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>'');
		echo json_encode($RETURN_ARR);
	}
	else {
		$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'대상자를 선택하십시요.');
		echo json_encode($RETURN_ARR);
	}
}

else if($action=='paid_cancel') {
	if($chk_count > 0) {
		for($i=0; $i<$chk_count; $i++) {
			$row = sql_fetch("SELECT idx, memo FROM event_entry_log WHERE idx='".$_POST['chk'][$i]."' AND paid='1'");
			if($row['idx']) {

				$memo = "";
				$memo.= ($row['memo']) ? $row['memo']."\\n[".G5_TIME_YMDHIS."] 지급기록 초기화" : "[".G5_TIME_YMDHIS."] 지급기록 초기화";

				$sql = "
					UPDATE
						event_entry_log
					SET
						paid = '',
						paid_date = NULL,
						memo = '".$memo."',
						last_edit_name = '".$member['mb_name']."'
					WHERE
						idx = '".$_POST['chk'][$i]."'";
				sql_query($sql);
			}
		}
		$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>'');
		echo json_encode($RETURN_ARR);
	}
	else {
		$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'대상자를 선택하십시요.');
		echo json_encode($RETURN_ARR);
	}
}

else {

	$RETURN_ARR = array('result'=>'ERROR:ACTION_EMPTY', 'message'=>'처리항목을 선택하십시요.');
	echo json_encode($RETURN_ARR);

}




exit;

?>