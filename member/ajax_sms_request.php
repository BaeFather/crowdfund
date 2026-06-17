<?php
include_once("_common.php");

// 기존회원 체크

while(list($key, $value)=each($_POST)) { ${$key} = trim($value); }

if( !preg_match("/(010|011|016|017|018|019)/", substr($phone_no, 0, 3)) ) {
	echo "2";
	exit;
}


$DATA = sql_fetch("
	SELECT
		mb_no, mb_hp
	FROM
		g5_member
	WHERE 1
		AND mb_hp='".masterEncrypt($phone_no)."'
		AND mb_leave_date=''
");
$DATA['mb_hp'] = masterDecrypt($DATA['mb_hp'], false);

if($DATA['mb_no']) {
	echo "1";
}
else {

	$device = (G5_IS_MOBILE) ? 'MOBILE' : 'PC';

	$lastLog = $_SERVER['REMOTE_ADDR']. "|" . $device;

	$DATA2 = sql_fetch("SELECT idx FROM sms_request_phone WHERE phone_no='$phone_no'");
	if($DATA2['idx']) {
		$sql = "
			UPDATE
			  sms_request_phone
			SET
			  phone_no = '".$phone_no."',
			  receive_agreement = 'Y',
			  last_agreement_date = NOW(),
				lastLog = '".$lastLog."'
			WHERE
				idx='".$DATA2['idx']."'";
	}
	else {
		$sql = "
			INSERT INTO
			  sms_request_phone
			SET
			  phone_no = '$phone_no',
			  receive_agreement = 'Y',
			  rdate = NOW(),
			  last_agreement_date = NOW(),
				ip = '".$_SERVER['REMOTE_ADDR']."',
				device = '".$device."'";
	}

	if($res = query($sql)) {
		echo "1";
	}
	else {
		echo "ERROR";
	}

}
exit;
