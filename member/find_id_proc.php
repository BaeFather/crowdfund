<?
################################################################################
## 아이디 찾기
##   - 아이디를 문자로 발송함
##   - 2019-01-21 전화번호 암호화 추가
################################################################################

include_once('./_common.php');
include_once('../lib/sms.lib.php');


if($_SERVER["REQUEST_METHOD"]!="POST") { echo "ERROR-DATA"; exit; }


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
	SELECT mb_id
	FROM g5_member
	WHERE 1
		AND mb_name = '".$mb_name."'
		AND member_group = '".$join_group."'
		AND mb_hp = '".masterEncrypt($mb_hp, false)."'
	ORDER BY
		mb_datetime DESC
	LIMIT 1";

$row = sql_fetch($sql);

if($row['mb_id']) {

	$sms_row = sql_fetch("SELECT * FROM `g5_sms_userinfo` WHERE use_yn = '1' AND idx = '20'");

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


sql_close();

?>