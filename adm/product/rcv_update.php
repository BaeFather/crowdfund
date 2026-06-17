<?php
$sub_menu = "920100";
include_once('./_common.php');

//print_rr($_REQUEST); exit;

if($w == 'u') {
	check_demo();
}

auth_check($auth[$sub_menu], 'w');

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

switch($_REQUEST['action_mode']) {

	case 'right_update' :

		if ($_REQUEST['right_display']) $right_display=$_REQUEST['right_display'];
		else $_REQUEST['right_display']="N";

		$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

		if ($_REQUEST["right_pic_del"]=="Y") {
			if($_REQUEST['right_pic_ori']) @unlink(G5_DATA_PATH.'/product/'.$_REQUEST['right_pic_ori']);
			$sql_common.= ", right_pic = '' ";
		} else {
			if (is_uploaded_file($_FILES['right_pic']['tmp_name'])) {

				//@mkdir(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);
				//@chmod(G5_DATA_PATH.'/product', G5_DIR_PERMISSION);

				shuffle($chars_array);
				$shuffle = substr(implode('', $chars_array), 0, 10);
				$dest_path = G5_DATA_PATH.'/product/'.$shuffle;

				move_uploaded_file($_FILES['right_pic']['tmp_name'], $dest_path);
				chmod($dest_path, G5_FILE_PERMISSION);
				$sql_common.= ", right_pic = '".$shuffle."' ";
			}
		}

		if ($_REQUEST["deposit_pic_del"]=="Y") {
			if($_REQUEST['deposit_pic_ori']) @unlink(G5_DATA_PATH.'/product/'.$_REQUEST['deposit_pic_ori']);
			$sql_common.= ", deposit_pic = '' ";
		} else {
			if (is_uploaded_file($_FILES['deposit_pic']['tmp_name'])) {
				shuffle($chars_array);
				$shuffle = substr(implode('', $chars_array), 0, 10);
				$dest_path = G5_DATA_PATH.'/product/'.$shuffle;

				move_uploaded_file($_FILES['deposit_pic']['tmp_name'], $dest_path);
				chmod($dest_path, G5_FILE_PERMISSION);
				$sql_common.= ", deposit_pic = '".$shuffle."' ";
			}
		}

		if ($_REQUEST["field_pic_del"]=="Y") {
			if($_REQUEST['field_pic_ori']) @unlink(G5_DATA_PATH.'/product/'.$_REQUEST['field_pic_ori']);
			$sql_common.= ", field_pic = '' ";
		} else {
			if (is_uploaded_file($_FILES['field_pic']['tmp_name'])) {
				shuffle($chars_array);
				$shuffle = substr(implode('', $chars_array), 0, 10);
				$dest_path = G5_DATA_PATH.'/product/'.$shuffle;

				move_uploaded_file($_FILES['field_pic']['tmp_name'], $dest_path);
				chmod($dest_path, G5_FILE_PERMISSION);
				$sql_common.= ", field_pic = '".$shuffle."' ";
			}
		}

		$up_sql = "
			UPDATE
				cf_product_container
			SET
				right_set_date='".$_REQUEST['right_set_date']."',
				right_display='".$_REQUEST['right_display']."'
				$sql_common
			WHERE
				product_idx='".$_REQUEST['idx']."'";

		if( sql_query($up_sql) ) {
			msg_replace('저장이 완료되었습니다.', 'rcv_list.php');
		}
}

?>