<?php
################################################################################
## 비밀번호 찾기
##   - 재설정된 비밀번호를 문자로 발송함
################################################################################

include_once('./_common.php');
include_once('../lib/sms.lib.php');


if ($_SERVER["REQUEST_METHOD"]!="POST") {
	echo "ERROR-DATA";
	exit;
}

$mb_id   = $_POST['mb_id'];
$mb_name = $_POST['mb_name'];
$mb_hp   = $_POST['mb_hp'];
$mb_hp   = preg_replace('/[^0-9]*/s', '', $mb_hp);

$query = "
	SELECT
		mb_no
	FROM
		g5_member
	WHERE
		mb_id = '$mb_id'
		AND mb_name = '$mb_name'
		AND mb_hp = '".masterEncrypt($mb_hp, false)."'";
//echo $query."<br>";
$row = sql_fetch($query);
if($row['mb_no']){
	$ary_cell = array(
	              0=>'a',  1=>'b',  2=>'c',  3=>'d',  4=>'e',  5=>'f',  6=>'g',  7=>'h',  8=>'i',  9=>'j',
	              10=>'0', 11=>'1', 12=>'2', 13=>'3', 14=>'4', 15=>'5', 16=>'6', 17=>'7', 18=>'8', 19=>'9'
	            );

	$imsi_pass = '';
	for($i=1; $i<=8; $i++) {
		$num = rand(0, 19);
		if($imsi_pass == '') {
			$imsi_pass = $ary_cell[$num];
		}
		else{
			$imsi_pass.= $ary_cell[$num];
		}
	}

//$sms_query = "SELECT * FROM `g5_sms_userinfo_default` WHERE use_yn='1' AND idx = '21' ";
	$sms_row   =  sql_fetch("SELECT * FROM `g5_sms_userinfo` WHERE use_yn='1' AND idx = '21'");

	if($sms_row["msg"]) {

		$encrypt_imsi_pass = get_encrypt_string2($imsi_pass);		// 20181212 : SHA256 적용
		//$encrypt_imsi_pass = get_encrypt_string($imsi_pass);

		$update_query = "
			UPDATE
				g5_member
			SET
				mb_password = '$encrypt_imsi_pass',
				edit_datetime = NOW()
			WHERE
				mb_no = '".$row['mb_no']."'";
		//echo $update_query; exit;
		$update_result = sql_query($update_query);

		if($update_result) {

			member_edit_log($row['mb_no']);  // 회원정보변경기록
			$sms_msg = str_replace("{USER_NAME}",$mb_name,$sms_row["msg"]);
			$sms_msg = str_replace("{NEW_PW}",$imsi_pass,$sms_msg);
			$rst = unit_sms_send($_admin_sms_number, $mb_hp, $sms_msg);

			if($rst == 1) {
				echo "SUCCESS"; exit;
			}
			else{
				echo "ERROR-DATA"; exit;
			}
		}
		else {
			echo "ERROR-DATA"; exit;
		}
	}
	else {
		echo "ERROR-DATA"; exit;
	}
}
else {
	echo "ERROR-NO-DATA"; exit;
}

?>