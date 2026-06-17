<?

include_once("_common.php");


if(!$member['mb_no']) {
	$ARR = array('result' => 'fail_login', 'message' => '로그인이 필요합니다.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}


$mb_id = trim($_POST['mb_id']);

if(!$mb_id) {
	$ARR = array('result' => 'fail', 'message' => '아이디를 입력하십시요.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}
if($mb_id == $member['mb_id']) {
	$ARR = array('result' => 'fail', 'message' => '본인확인 작성중인 회원과 동일한 ID는 등록하실 수 없습니다.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}


$mb_id = sql_real_escape_string($mb_id);

$MB = sql_fetch("SELECT mb_id, mb_name, kyc_allow_cnt FROM g5_member WHERE mb_id='".$mb_id."' AND mb_level='1' AND member_group='F'");

if($MB['mb_id']) {
	if($MB['kyc_allow_cnt'] > 0) {

		$mb_name = mb_substr($MB['mb_name'], 0, 1) . "○";
		if(strlen($MB['mb_name']) >= 3) $mb_name.= mb_substr($MB['mb_name'], 2);


		$ARR = array('result' => 'success', 'message' => $mb_name);
		echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
	}
	else {
		$ARR = array('result' => 'fail', 'message' => '입력하신 ID를 소유한 회원에 대한 본인확인 심사이력이 없어 대리인으로 지정 하실 수 없습니다.');
		echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
	}
}
else {
	$ARR = array('result' => 'fail', 'message' => '존재하지 않는 ID 입니다.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
}

sql_close();
exit;


?>