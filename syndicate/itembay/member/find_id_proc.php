<?php
include_once('./_common.php');
include_once('../lib/sms.lib.php');


if ($_SERVER["REQUEST_METHOD"]!="POST") {
	echo "ERROR-DATA";
	exit;
}

$mb_name	= $_POST['mb_name'];
$mb_hp		= $_POST['mb_hp'];
$mb_hp		= preg_replace('/[^0-9]*/s', '', $mb_hp);

if($mb_name=="") {
	echo "ERROR-DATA";
	exit;
}
if($mb_hp=="") {
	echo "ERROR-DATA";
	exit;
}

$query = "
	SELECT
		mb_id
	FROM
		g5_member
	WHERE
		mb_name = '$mb_name'
		AND mb_hp = '".masterEncrypt($mb_hp, false)."'
	ORDER BY
		mb_datetime DESC
	LIMIT 1";
$row = sql_fetch($query);

if($row['mb_id']) {

//$sms_query = "SELECT * FROM `g5_sms_userinfo_default` where use_yn='1' and idx = '20' ";
	$sms_row = sql_fetch("SELECT * FROM `g5_sms_userinfo` WHERE use_yn='1' AND idx='20'");

	if($sms_row["msg"]) {
		$sms_msg = str_replace("{USER_NAME}", $mb_name, $sms_row["msg"]);
		$sms_msg = str_replace("{USER_ID}", $row['mb_id'], $sms_msg);
		$rst = unit_sms_send($_admin_sms_number, $mb_hp, $sms_msg);
		if($rst == 1) {
			echo "SUCCESS"; exit;
		}
		else {
			echo "ERROR-DATA"; exit;
		}
	}

}
else {

	echo "ERROR-NO-DATA"; exit;

}
?>