<?

// 회원용 메뉴일 경우 인클루드 할것

$memberNumber = $REQUEST['data']['memberNumber'];

if(!$memberNumber) {

	$ARR['head']['responseHash'] = resultSignature($ARR);
	$ARR['error'] = array('code'=>'MEMBER_INVALID', 'message'=>'회원번호누락'); echo printJson($ARR); exit;

}
else {

	$row = sql_fetch("SELECT * FROM g5_member WHERE oligo_userid='".$memberNumber."'");
	$row['mb_hp'] = masterDecrypt($row['mb_hp'], false);
	$row['account_num'] = masterDecrypt($row['account_num'], false);

	if($row['mb_id']) {
		if($row['mb_level']=='1') {
			//$MB = get_member($row['mb_id']);
		}
		else if($row['mb_level']=='200') {
			$ARR['error'] = array('code'=>'MEMBER_ALREADY_WITHDRAW', 'message'=>'탈퇴회원'); echo printJson($ARR); exit;
		}
		else {
			$ARR['error'] = array('code'=>'MEMBER_INVALID', 'message'=>'오류회원'); echo printJson($ARR); exit;
		}
	}
	else {
		// 탈퇴회원 추적
		$row2 = sql_fetch("SELECT mb_id FROM g5_member_drop WHERE mb_level='1' AND oligo_userid='".$memberNumber."'");
		if($row2['mb_id']) {
			$ARR['error'] = array('code'=>'MEMBER_ALREADY_WITHDRAW', 'message'=>'탈퇴회원'); echo printJson($ARR); exit;
		}
		else {
			$ARR['error'] = array('code'=>'MEMBER_NOT_FOUND', 'message'=>'미존재회원'); echo printJson($ARR); exit;
		}
	}

	$MB = get_member($row['mb_id']);
	$MB['mb_point'] = get_point_sum($MB['mb_id']);		// 포인트 다시 가져옴
	unset($row);

}

?>