<?

set_time_limit(0);

include_once('./_common.php');
auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

//print_r($_POST); exit;

while( list($k, $v) = each($_POST) ) { if(!is_array(${$k})) ${$k} = trim($v); }


if(!$event_no) {
	$ARR = array('result'=>'FAIL', 'message'=>'이벤트번호가 없음!!');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); sql_close(); exit;
}


$chk_count = count($_POST['chk']);
if(!$chk_count) {
	$ARR = array('result'=>'CHK_EMPTY', 'message'=>'대상자 선택값 없음!!');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); sql_close(); exit;
}


// 이벤트 설정값
$EVENT_CONF = sql_fetch("SELECT * FROM cf_partner_event_config WHERE event_no = '".$event_no."' AND is_real='1'");


$now_dt = date('Y-m-d H:i:s');

/////////////////////////////////////////////////
// 보상대상자 확정 플래그 처리
/////////////////////////////////////////////////
if($action=='approved') {

	$succ_cnt = $fail_cnt = 0;

	for($i=0; $i<$chk_count; $i++) {

		$TARGET_MB  = sql_fetch("SELECT * FROM g5_member WHERE mb_no='".$_POST['chk'][$i]."' AND mb_level IN('1','2','3','4','5')");						// 리워드 대상자 정보
		$REWORD_LOG = sql_fetch("SELECT idx, approved FROM cf_partner_event_reward_log WHERE event_no='".$event_no."' AND member_idx='".$TARGET_MB['mb_no']."'");

		if($REWORD_LOG['idx'] && $REWORD_LOG['approved']=='') {

			$sql = "
				UPDATE
					cf_partner_event_reward_log
				SET
					invest_rwd_amt    = '".$EVENT_CONF['invest_rwd_amt']."',
					approved          = '1',
					approved_datetime = '".$now_dt."',
					bank_code         = '".$TARGET_MB['va_bank_code2']."',
					bank_acct         = '".$TARGET_MB['virtual_account2']."',
					bank_private_name = '".$TARGET_MB['va_private_name2']."',
					last_edit_name    = '".$member['mb_name']."'
				WHERE
					idx='".$REWORD_LOG['idx']."'";
			$result = sql_query($sql);
			print_r($sql);
		}

		if( sql_affected_rows() ) {
			$succ_cnt += 1;
		}
		else {
			$fail_cnt += 1;
		}

	}	//end for

	$message = $chk_count . "건의 요청중 ".$succ_cnt."건 처리 완료";
	$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>$message);
	echo json_encode($RETURN_ARR);

}

/////////////////////////////////////////////////
// 무효 플래그 처리
/////////////////////////////////////////////////
else if($action=='invalid') {

	$succ_cnt = $fail_cnt = 0;

	for($i=0; $i<$chk_count; $i++) {

		$TARGET_MB  = sql_fetch("SELECT mb_no, rec_mb_no FROM g5_member WHERE mb_no='".$_POST['chk'][$i]."' AND mb_level IN('1','2','3','4','5')");						// 추천인
		$REWORD_LOG = sql_fetch("SELECT idx, invalid, paid FROM cf_partner_event_reward_log WHERE event_no='".$event_no."' AND member_idx='".$TARGET_MB['mb_no'] ."'");

		if($REWARD_LOG['idx'] && $REWARD_LOG['invalid']=='' && $REWARD_LOG['paid']=='') {
			$sql = "
				UPDATE
					cf_partner_event_reward_log
				SET
					invalid = '1',
					invalid_datetime = '".$now_dt."',
					last_edit_name = '".$member['mb_name']."'
				WHERE
					idx='".$REWARD_LOG['idx']."'";
			$result = sql_query($sql);
		}

		if( sql_affected_rows() ) {
			$succ_cnt += 1;
		}
		else {
			$fail_cnt += 1;
		}

	}	//end for

	$message = $chk_count . "건의 요청중 ".$succ_cnt."건 처리 완료";
	$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>$message);
	echo json_encode($RETURN_ARR);

}

/////////////////////////////////////////////////
// 지급완료 플래그 처리
/////////////////////////////////////////////////
else if($action=='paid') {

	$succ_cnt = $fail_cnt = 0;

	for($i=0; $i<$chk_count; $i++) {

		$TARGET_MB   = sql_fetch("SELECT mb_no, rec_mb_no FROM g5_member WHERE mb_no='".$_POST['chk'][$i]."'");						// 추천인
		$REWARD_LOG = sql_fetch("SELECT idx, invalid, paid FROM cf_partner_event_reward_log WHERE event_no='".$event_no."' AND member_idx='".$TARGET_MB['mb_no'] ."'");

		if($REWARD_LOG['idx'] && $REWARD_LOG['invalid']=='' && $REWARD_LOG['paid']=='') {
			$sql = "
				UPDATE
					cf_partner_event_reward_log
				SET
					paid = '1',
					paid_datetime = '".$now_dt."',
					last_edit_name = '".$member['mb_name']."'
				WHERE
					idx='".$REWARD_LOG['idx']."'";
			$result = sql_query($sql);
		}

		if( sql_affected_rows() ) {
			$succ_cnt += 1;
		}
		else {
			$fail_cnt += 1;
		}

	}	//end for

	$message = $chk_count . "건의 요청중 ".$succ_cnt."건 처리 완료";
	$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>$message);
	echo json_encode($RETURN_ARR);

}

else {

	$RETURN_ARR = array('result'=>'ACTION_EMPTY', 'message'=>'파라미터 전송에러');
	echo json_encode($RETURN_ARR);

}



sql_close();
exit;

?>