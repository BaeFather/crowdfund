<?
include_once("../common.php");

if( !preg_match("/(manager\.hellofunding)/i", $_SERVER['HTTP_REFERER']) ) {
	header("HTTP/1.0 404 Not Found"); exit;
}

$mb = sql_fetch("SELECT * FROM g5_member WHERE mb_no = '".$_REQUEST['mb_no']."' AND member_group = 'F'");
//print_r($mb);

if($mb) {

	// 승인요청중인 회원인지 체크
	if($mb['mb_level']=='0') {
		if($mb['member_type']=='2') {
			alert("업체명: ".$mb['mb_co_name']."\\n\\n\\n현재 법인회원 신청 승인 중입니다.");
		}
		else {
			alert('승인 되지 않은 회원입니다.');
		}
	}

	//session_unset();			// 모든 세션변수를 언레지스터 시켜줌
	//session_destroy();		// 세션해제함

	// 회원아이디 세션 생성
	set_session('ss_mb_id', $mb['mb_id']);
	set_session('ss_mb_key', md5($mb['mb_datetime'] . '0.0.0.0' . 'SYSTEMBOT'));
	set_session('ss_is_false', true);			// 관리자가 테스트함을 의미함

	/*
	if($mb['mb_level']==9) {
		$SADMIN = sql_fetch("SELECT privacy_auth FROM g5_sub_admin WHERE mb_no='".$mb['mb_no']."'");
		if($SADMIN['privacy_auth']=='Y') {
			// 개인정보 열람가능 권한 부여
			set_session('ss_accounting_admin', true);
		}
	}
	*/

	header("Location:".G5_URL);

}

exit;

?>