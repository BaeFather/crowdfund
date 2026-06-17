<?
################################################################################
## 비밀번호 찾기
##   - 재설정된 비밀번호를 문자로 발송함
##   - 2019-01-21 전화번호 암호화 추가
################################################################################

include_once('./_common.php');
include_once('../lib/sms.lib.php');


if($_SERVER["REQUEST_METHOD"]!="POST") { echo "ERROR-DATA"; exit; }

$mb_id   = sql_real_escape_string(trim($_POST['mb_id']));
$mb_name = sql_real_escape_string(trim($_POST['mb_name']));
$mb_hp   = sql_real_escape_string(trim($_POST['mb_hp']));
$mb_hp   = preg_replace('/[^0-9]*/s', '', $mb_hp);
$join_group = trim($_POST['join_group']);		// 투자회원, 법인회원 구분 : 차후 추가하시오

if($mb_name=="") { echo "ERROR-DATA"; exit; }
if($mb_hp=="") { echo "ERROR-DATA"; exit; }

if( in_array($join_group, array('','F','L')) ) {
	$join_group = ($join_group == '') ? 'F' : $join_group;
}
else {
	echo "ERROR-DATA"; exit;
}

$sql = "
	SELECT mb_no
	FROM g5_member
	WHERE 1
		AND mb_id = '$mb_id'
		AND mb_name = '$mb_name'
		AND mb_hp = '".masterEncrypt($mb_hp, false)."'
	ORDER BY
		mb_datetime DESC
	LIMIT 1";

$row = sql_fetch($sql);
if($row['mb_no']) {
	$ary_cell = array('a','b','c','d','e','f','g','h','i','j','0','1','2','3','4','5','6','7','8','9');

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
			$sms_msg = str_replace("{USER_NAME}", $mb_name, $sms_row["msg"]);
			$sms_msg = str_replace("{NEW_PW}", $imsi_pass, $sms_msg);
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