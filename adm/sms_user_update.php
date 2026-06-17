<?php
$sub_menu = '800100';
include_once('./_common.php');



$data = $_POST;


/* sql 조합 */

for($i=0; $i<count($data['idx']); $i++) {

	$use_yn_arr = 'use_yn_'.$data['idx'][$i];

	if(!isset($data[$use_yn_arr]) || $data[$use_yn_arr] == '') {
		$use_yn = 0;
	}else {
		$use_yn = $data[$use_yn_arr];
	}

	$sql = "
			UPDATE g5_sms_userinfo SET
				`use_yn` = '".$use_yn."' ,
				`msg` = '".$data['msg'][$i]."'
			WHERE
				`idx` = '".$data['idx'][$i]."' ;
			";

	//echo $sql."<br />";
	sql_query($sql);

}

alert("수정되었습니다.","./sms_user_form.php");



?>