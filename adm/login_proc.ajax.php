<?php

include_once('../common.php');
include_once('../lib/sms.lib.php');

check_demo();

if(!$member || empty($member)){
	exit(json_encode('로그인 하십시오.', G5_BBS_URL.'/login.php?url=' . urlencode(G5_ADMIN_URL)));
}

if($_SESSION['ss_is_admin']){
	goto_url(G5_URL);
	exit;
}

$mb = $member;

$is_admin      = trim($_POST['is_admin']);
$input_auth_no = trim($_POST['input_auth_no']);
$mb_name       = trim($_POST['mb_name']);
$phone         = "";

if(!$mb_name) { alert('이름을 입력해주세요.'); }
if(!in_array($mb['mb_level'], array('9','10'))) {
	exit(json_encode(array("error" => "1", "message" => '관리자만 접근할 수 있습니다.')));
}

if($mb['mb_level']=='9') {
	$phone = $mb['mb_hp'];
	if(!$phone){
		exit(json_encode(array("error" => "1", "message" => '관리자만 접근할 수 있습니다.')));
	}
}

$res = sql_query("SELECT mb_name, mb_hp FROM g5_member WHERE mb_level='9' ORDER BY mb_no");
while( $r = sql_fetch_array($res) ) {
	$phone_data[$r['mb_name']] = preg_replace('/(-| )/', '', masterDecrypt($r['mb_hp'],false));
}


if(!isset($is_admin) OR empty($is_admin)){ // 인증번호 요청을 했는지
	unset($_SESSION["last_activity"]);
	unset($_SESSION["ss_auth_no"]);
}

// 인증확인
if( !get_session("ss_auth_no") ) {

	// 인증번호 생성
	srand((double)microtime() * 1000000);
	$auth_no = substr((rand(0, 32000)), 0, 5);

	set_session('ss_auth_no', $auth_no);
	set_session('last_activity', time());
	unit_sms_send($_admin_sms_number, $phone, "HF 관리자 접속 인증번호는 [".$auth_no."] 입니다.");
	exit(json_encode(array("check" => "1", "message" => '인증번호가 회원 휴대폰으로 전송되었습니다.')));

}
else if( $auth_no = get_session("ss_auth_no") ) {

	// 인증번호 확인
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 180)) {							// 3분이 초과되면 인증번호 파기
		exit(json_encode(array("limited" => "1", "message" => '인증번호 입력시간이 초과되었습니다.')));
		unset($_SESSION["last_activity"]);
		unset($_SESSION["ss_auth_no"]);
	}

	if(!$input_auth_no) {
		exit(json_encode(array("error" => "1", "message" => '인증번호를 입력해주세요.')));
	}

	if($auth_no !== $input_auth_no) {
		exit(json_encode(array("not_auth_no" => "1", "message" => '입력하신 인증번호가 일치하지 않습니다.')));
	}

}

set_session('ss_is_admin', true);

if($mb['mb_level'] == '9') {

	// 로그인 기록
	$insert_sql = "
		INSERT INTO
			g5_admin_login_log
		SET
			mb_no        = '".$mb['mb_no']."',
			mb_name      = '".$mb['mb_name']."',
			all_datetime = NOW(),
			all_ip       = '".$_SERVER['REMOTE_ADDR']."',
			all_device   = '".getDevice()."';";
	sql_query($insert_sql);
	$login_idx = sql_insert_id();
	if($login_idx) set_session('ss_admin_login_idx', $login_idx);

}


exit(json_encode(array("success" => "1")));

?>