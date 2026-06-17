<?php
//if (!defined('_GNUBOARD_')) exit;

/*************************************************************************
**  SMS 함수 모음
*************************************************************************/

define('ADMIN_HP','15886760');


$link3      = sql_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3);
$select_db3 = sql_select_db(G5_MYSQL_DB3, $link3) or die('MySQL DB Error!!!');
sql_set_charset('utf8', $link3);

// 관리자툴에 설정된 사용자/관리자발송 메세지 전송
function select_sms_send($type, $send_type_no, $replace_arr=array(), $to_hp, $from_hp=ADMIN_HP) {

	global $link3;

	if($type == 'admin') {		// 관리자 발송메세지일 경우
		$tbl_name = 'g5_sms_admininfo';
	}
	else {										// 사용자 발송메세지일 경우
		$tbl_name = 'g5_sms_userinfo';
	}

	$sms_sql = "SELECT msg FROM {$tbl_name} WHERE send_type_no='{$send_type_no}' AND use_yn='1'";
	$sms_r = sql_fetch($sms_sql);

	if($sms_r['msg'] && $sms_r['msg'] != '') {

		$to_hp   = preg_replace('/[^0-9]*/s', '', $to_hp);
		$from_hp = preg_replace('/[^0-9]*/s', '', $from_hp);

		// msg 치환
		if(count($replace_arr) > 0) {
			foreach($replace_arr as $k=>$v) {
				$sms_r['msg'] = str_replace($k, $v, $sms_r['msg']);
			}
		}

		$str_volume = mb_strwidth($sms_r['msg'], 'UTF-8');
		$msg_gubun = ($str_volume > 90) ? '1' : '0';		// : 0:SMS, 1:LMS

		$subject = ($msg_gubun=='1') ? '헬로펀딩 메세지' : '';
		$etc1 = (preg_match('/dev\.hello/i', $_SERVER['HTTP_HOST'])) ? 'dev' : '';

		$sql = "
			INSERT INTO
				agent_msgqueue
			SET
				kind='$msg_gubun',
				callbackNo='$from_hp',
				receiveNo='$to_hp',
				subject='$subject',
				message='".$sms_r['msg']."',
				registTime=NOW(),
				etc1='$etc1'";

		sql_query($send_sql, '', $link3);

		return 1;

	}
	else {

		return -1;		// 설정된 메세지가 없음

	}

}


// 문자전송
function unit_sms_send($from_hp, $to_hp, $send_msg, $send_date=null) {

	global $link3;

	$send_msg = trim($send_msg);

	if($send_msg && $send_msg != '') {

		//$str_volume = mb_strwidth($send_msg, 'UTF-8');
		$str_volume = mb_strlen($send_msg, 'EUC-KR');
		$msg_gubun = ($str_volume <= 86) ? '0' : '1';		// : 0:SMS, 1:LMS

		/*if($_SERVER['REMOTE_ADDR']=="220.117.134.164") {
			echo $send_msg."\n\n";
			echo $str_volume."byte\n\n";
			return;
		}*/

		$to_hp   = preg_replace('/[^0-9]*/s', '', $to_hp);
		$from_hp = preg_replace('/[^0-9]*/s', '', $from_hp);

		$subject = ($msg_gubun=='1') ? '헬로펀딩 메세지' : '';
		$etc1 = (preg_match('/dev\.hello/i', $_SERVER['HTTP_HOST'])) ? 'dev' : '';
		$isReserved = ($send_date=='') ? 'N' : 'Y';

		$sql = "
			INSERT INTO
				agent_msgqueue
			SET
				kind='".$msg_gubun."',
				callbackNo='".$from_hp."',
				receiveNo='".$to_hp."',
				subject='".$subject."',
				message='".$send_msg."',
				isReserved='".$isReserved."',
				registTime=NOW(),
				etc1='".$etc1."'";

		if($isReserved=='Y') $sql.=", reservedTime='".$send_date."'";

		//echo $sql."\n\n"; exit;

		sql_query($sql, false, $link3);

		return 1;

	}
	else {
		return -1;		// 설정된 메세지가 없음
	}

}

// 문자전송
function unit_sms_send_v2($from_hp, $to_hp, $send_msg, $send_date=null) {

	global $link3;

	$send_msg = trim($send_msg);

	if($send_msg && $send_msg != '') {

		//$str_volume = mb_strwidth($send_msg, 'UTF-8');
		$str_volume = mb_strlen($send_msg, 'EUC-KR');
		$msg_gubun = ($str_volume <= 86) ? '0' : '1';		// : 0:SMS, 1:LMS

		/*if($_SERVER['REMOTE_ADDR']=="220.117.134.164") {
			echo $send_msg."\n\n";
			echo $str_volume."byte\n\n";
			return;
		}*/

		$to_hp   = preg_replace('/[^0-9]*/s', '', $to_hp);
		$from_hp = preg_replace('/[^0-9]*/s', '', $from_hp);

		$subject = ($msg_gubun=='1') ? '헬로펀딩 메세지' : '';
		$etc1 = (preg_match('/dev\.hello/i', $_SERVER['HTTP_HOST'])) ? 'dev' : '';
		$isReserved = ($send_date=='') ? 'N' : 'Y';

		$insert_id = 0; 
		$moved = "N";

		if ($isReserved) {
			$sql = "
				INSERT INTO
					agent_msgqueue
				SET
					kind='".$msg_gubun."',
					callbackNo='".$from_hp."',
					receiveNo='".$to_hp."',
					subject='".$subject."',
					message='".$send_msg."',
					isReserved='".$isReserved."',
					registTime=NOW(),
					etc1='".$etc1."'";
			if($isReserved=='Y') $sql.=", reservedTime='".$send_date."'";

			sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);

			$insert_id = sql_insert_id($link3);
			$moved = "Y";
		}

		$sql = "
			INSERT INTO
				cf_agent_msgqueue
			SET
				id='".$insert_id."',
				kind='".$msg_gubun."',
				callbackNo='".$from_hp."',
				receiveNo='".$to_hp."',
				subject='".$subject."',
				message='".$send_msg."',
				isReserved='".$isReserved."',
				registTime=NOW(),
				moved='".$moved."',
				etc1='".$etc1."'";
		if($isReserved=='Y') $sql.=", reservedTime='".$send_date."'";

		//echo $sql."\n\n"; exit;

		sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);

		return 1;

	}
	else {
		return -1;		// 설정된 메세지가 없음
	}

}

?>