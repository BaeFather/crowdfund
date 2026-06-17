<?php

set_time_limit(0);

include_once('./_common.php');
auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

//print_r($_POST); exit;

while( list($k, $v) = each($_POST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

// 이벤트 설정값
$EVENT_CONF = sql_fetch("SELECT * FROM recommend_event_config WHERE event_no = '".$event_no."' AND is_real='1'");

$chk_count = count($_POST['chk']);


if($action=='approved') {	// 보상확정
	if($chk_count > 0) {
		$succ_cnt = $fail_cnt = 0;

		for($i=0; $i<$chk_count; $i++) {

			$RECMDER_MB  = sql_fetch("SELECT * FROM g5_member WHERE mb_no='".$_POST['chk'][$i]."'");						// 추천인
			$RECMDER_LOG = sql_fetch("SELECT idx, approved FROM recommend_reward_log WHERE event_no='".$event_no."' AND member_idx='".$RECMDER_MB['mb_no']."' AND `position`='recmder' AND target_member_idx='".$RECMDER_MB['rec_mb_no']."'");

			$recmder_bankcode = $recmder_acct = $recmder_acct_name = '';
			if($EVENT_CONF['recmder_reward_type']=='1') {
				$recmder_bankcode  = $RECMDER_MB['va_bank_code2'];
				$recmder_acct      = $RECMDER_MB['virtual_account2'];
				$recmder_acct_name = $RECMDER_MB['va_private_name2'];
			}

			$RECMDEE_MB  = sql_fetch("SELECT * FROM g5_member WHERE mb_no='".$RECMDER_MB['rec_mb_no']."'");		// 피추천인
			$RECMDEE_LOG = sql_fetch("SELECT idx, approved FROM recommend_reward_log WHERE event_no='".$event_no."' AND member_idx='".$RECMDEE_MB['mb_no']."' AND `position`='recmdee' AND target_member_idx='".$RECMDER_MB['mb_no']."'");

			$recmdee_bankcode = $recmdee_acct = $recmdee_acct_name = '';
			if($EVENT_CONF['recmdee_reward_type']=='1') {
				$recmdee_bankcode  = $RECMDEE_MB['va_bank_code2'];
				$recmdee_acct      = $RECMDEE_MB['virtual_account2'];
				$recmdee_acct_name = $RECMDEE_MB['va_private_name2'];
			}

			if($RECMDER_LOG['idx'] && $RECMDER_LOG['approved']=='') {
				$sql1 = "
					UPDATE
						recommend_reward_log
					SET
						reward_amount     = '".$EVENT_CONF['recmder_reward_point']."',
						approved          = '1',
						approved_datetime = NOW(),
						bank_code         = '".$recmder_bankcode."',
						bank_acct         = '".$recmder_acct."',
						bank_private_name = '".$recmder_acct_name."',
						last_edit_name    = '".$member['mb_name']."',
						recm_kind		      = '".$EVENT_CONF['recmder_reward_type']."'
					WHERE
						idx='".$RECMDER_LOG['idx']."'";
				$result1 = sql_query($sql1);

				// 추천인 승인시 피추천인 정보등록 및 승인 처리
				if($result1) {
					$sql2 = "
						INSERT INTO
							recommend_reward_log
						SET
							event_no          = '".$event_no."',
							member_idx        = '".$RECMDEE_MB['mb_no']."',
							position          = 'recmdee',
							target_member_idx ='".$RECMDER_MB['mb_no']."',
							rdatetime         = NOW(),
							reward_amount     = '".$EVENT_CONF['recmdee_reward_point']."',
							approved          = '1',
							approved_datetime = NOW(),
							bank_code         = '".$recmdee_bankcode."',
							bank_acct         = '".$recmdee_acct."',
							bank_private_name = '".$recmdee_acct_name."',
							last_edit_name    = '".$member['mb_name']."',
							recm_kind		      = '".$EVENT_CONF['recmdee_reward_type']."'";
					$result2 = sql_query($sql2);
				}

			}
			/*
			else {
				// 추천인 로그에 없을때 추천인 로그를 만들어 준다. (pid로 들어온 유저)

				$sql1 = "
						INSERT INTO
							recommend_reward_log
						SET
							event_no          = '".$event_no."',
							member_idx        = '".$RECMDER_MB['mb_no']."',
							position          = 'recmder',
							target_member_idx = '',
							rdatetime         = '".$RECMDER_MB['mb_datetime']."',
							reward_amount     = '".$EVENT_CONF['recmder_reward_point']."',
							approved          = '1',
							approved_datetime = NOW(),
							bank_code         = '".$recmder_bankcode."',
							bank_acct         = '".$recmder_acct."',
							bank_private_name = '".$recmder_acct_name."',
							last_edit_name    = '".$member['mb_name']."',
							recm_kind		      = '".$EVENT_CONF['recmder_reward_type']."'";
				$result1 = sql_query($sql1);

				$result2 = true;
			}
			*/

			if( $result1 ) {
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
		$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'대상자를 선택하십시요.');
		echo json_encode($RETURN_ARR);
	}
}

else if($action=='invalid') {	// 무효처리

	if($chk_count > 0) {

		$succ_cnt = $fail_cnt = 0;

		for($i=0; $i<$chk_count; $i++) {

			$RECMDER_MB  = sql_fetch("SELECT mb_no, rec_mb_no FROM g5_member WHERE mb_no='".$_POST['chk'][$i]."'");						// 추천인

			$RECMDER_LOG = sql_fetch("SELECT idx, invalid, paid FROM recommend_reward_log WHERE event_no='".$event_no."' AND member_idx='".$RECMDER_MB['mb_no'] ."' AND `position`='recmder' AND target_member_idx='".$RECMDER_MB['rec_mb_no']."'");
			$RECMDEE_LOG = sql_fetch("SELECT idx, invalid, paid FROM recommend_reward_log WHERE event_no='".$event_no."' AND member_idx='".$RECMDER_MB['rec_mb_no']."' AND `position`='recmdee' AND target_member_idx='".$RECMDER_MB['mb_no']."'");

			if($RECMDER_LOG['idx'] && $RECMDER_LOG['invalid']=='' && $RECMDER_LOG['paid']=='') {
				$sql1 = "
					UPDATE
						recommend_reward_log
					SET
						invalid = '1',
						invalid_datetime = NOW(),
						last_edit_name = '".$member['mb_name']."'
					WHERE
						idx='".$RECMDER_LOG['idx']."'";
				$result1 = sql_query($sql1);
			}

			if($RECMDEE_LOG['idx'] && $RECMDEE_LOG['invalid']=='' && $RECMDEE_LOG['paid']=='') {
				$sql2 = "
					UPDATE
						recommend_reward_log
					SET
						invalid = '1',
						invalid_datetime = NOW(),
						last_edit_name = '".$member['mb_name']."'
					WHERE
						idx='".$RECMDEE_LOG['idx']."'";
				$result2 = sql_query($sql2);
			}

			if( $result1 && $result2 ) {
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
		$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'대상자를 선택하십시요.');
		echo json_encode($RETURN_ARR);
	}

}


else if($action=='paid') {	// 지급완료

	if($chk_count > 0) {

		$succ_cnt = $fail_cnt = 0;

		for($i=0; $i<$chk_count; $i++) {

			$RECMDER_MB  = sql_fetch("SELECT mb_no, rec_mb_no FROM g5_member WHERE mb_no='".$_POST['chk'][$i]."'");						// 추천인

			$RECMDER_LOG = sql_fetch("SELECT idx, invalid, paid FROM recommend_reward_log WHERE event_no='".$event_no."' AND member_idx='".$RECMDER_MB['mb_no'] ."' AND `position`='recmder' AND target_member_idx='".$RECMDER_MB['rec_mb_no']."'");
			$RECMDEE_LOG = sql_fetch("SELECT idx, invalid, paid FROM recommend_reward_log WHERE event_no='".$event_no."' AND member_idx='".$RECMDER_MB['rec_mb_no']."' AND `position`='recmdee' AND target_member_idx='".$RECMDER_MB['mb_no']."'");

			if($RECMDER_LOG['idx'] && $RECMDER_LOG['invalid']=='' && $RECMDER_LOG['paid']=='') {
				$sql1 = "
					UPDATE
						recommend_reward_log
					SET
						paid = '1',
						paid_datetime = NOW(),
						last_edit_name = '".$member['mb_name']."'
					WHERE
						idx='".$RECMDER_LOG['idx']."'";
				$result1 = sql_query($sql1);
			}

			if($RECMDEE_LOG['idx'] && $RECMDEE_LOG['invalid']=='' && $RECMDEE_LOG['paid']=='') {
				$sql2 = "
					UPDATE
						recommend_reward_log
					SET
						paid = '1',
						paid_datetime = NOW(),
						last_edit_name = '".$member['mb_name']."'
					WHERE
						idx='".$RECMDEE_LOG['idx']."'";
				$result2 = sql_query($sql2);
			}

			if( $result1 && $result2 ) {
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
		$RETURN_ARR = array('result'=>'ERROR:CHK_EMPTY', 'message'=>'대상자를 선택하십시요.');
		echo json_encode($RETURN_ARR);
	}

}

else {

	$RETURN_ARR = array('result'=>'ERROR:ACTION_EMPTY', 'message'=>'파라미터 전송에러');
	echo json_encode($RETURN_ARR);

}




exit;

?>