<?php
if (!defined('_GNUBOARD_')) exit;

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
	else {						// 사용자 발송메세지일 경우
		$tbl_name = 'g5_sms_userinfo';
	}

	$sms_sql = "SELECT msg FROM {$tbl_name} WHERE send_type_no='{$send_type_no}' AND use_yn='1'";
	$sms_r = sql_fetch($sms_sql);

	if($sms_r['msg'] && $sms_r['msg'] != '') {

		/* 전화번호에서 숫자만 추출 */
		$to_hp   = preg_replace('/[^0-9]*/s', '', $to_hp);
		$from_hp = preg_replace('/[^0-9]*/s', '', $from_hp);

		/* msg 치환 */
		if(count($replace_arr) > 0) {
			foreach($replace_arr as $k=>$v) {
				$sms_r['msg'] = str_replace($k, $v, $sms_r['msg']);
			}
		}

		if(strlen($sms_r['msg']) <= 130) {
			$send_sql = "INSERT INTO SC_TRAN (TR_SENDDATE , TR_SENDSTAT ,TR_MSGTYPE ,TR_PHONE ,TR_CALLBACK , TR_MSG) VALUES (NOW(), '0', '0', '{$to_hp}', '{$from_hp}', '{$sms_r['msg']}');";				// SMS
		}
		else {
			$send_sql = "INSERT INTO MMS_MSG (SUBJECT, PHONE, CALLBACK, STATUS, REQDATE, MSG, TYPE) VALUES ('헬로펀딩 메세지', '{$to_hp}', '{$from_hp}', '0', NOW(), '{$sms_r['msg']}', '0');";			// MMS
		}

		sql_query($send_sql, $error=G5_DISPLAY_SQL_ERROR, $link3);

		return 1;

	}
	else {

		return -1;		// 설정된 메세지가 없음

	}

}


// 단순 SMS 전송
function unit_sms_send($from_hp, $to_hp, $send_msg, $send_date=null) {

	global $link3;

	if($send_msg && $send_msg != '') {

		if($send_date == null) {
			$send_date = date('Y-m-d H:i:s');
		}

		/* 전화번호에서 숫자만 추출 */
		$to_hp   = preg_replace('/[^0-9]*/s', '', $to_hp);
		$from_hp = preg_replace('/[^0-9]*/s', '', $from_hp);

		if(strlen($send_msg) <= 130) {
			$send_sql = "INSERT INTO SC_TRAN (TR_SENDDATE , TR_SENDSTAT ,TR_MSGTYPE ,TR_PHONE ,TR_CALLBACK , TR_MSG) VALUES ('{$send_date}', '0', '0', '{$to_hp}', '{$from_hp}', '{$send_msg}');";				// SMS
		}
		else {
			$send_sql = "INSERT INTO MMS_MSG (SUBJECT, PHONE, CALLBACK, STATUS, REQDATE, MSG, TYPE) VALUES ('헬로펀딩 메세지', '{$to_hp}', '{$from_hp}', '0', '{$send_date}', '{$send_msg}', '0' );";			// MMS
		}

		sql_query($send_sql, $error=G5_DISPLAY_SQL_ERROR, $link3);

		return 1;

	}
	else {
		return -1;		// 설정된 메세지가 없음
	}

}

?>