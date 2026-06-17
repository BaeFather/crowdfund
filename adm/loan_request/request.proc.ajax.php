<?
###############################################################################
##	주담대신청심사로그처리
###############################################################################

include_once("./_common.php");

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) { ${$_POST[$k]} = trim($v); }


$g5['title'] = "주담대신청심사로그 처리";
if($member['mb_level'] == '9') include_once(G5_ADMIN_PATH."/inc_sub_admin_access_check.php");		// 부관리자 접속로그 등록


if($mode=='new') {

	if($comment) $comment = sql_real_escape_string($comment);

	$sqlx = "
		INSERT INTO
			cf_apat_loan_request_judge_log
		SET
			req_idx='".$idx."',
			writer='".$member['mb_id']."',
			comment='".$comment."',
			regdate=NOW()";
	if( sql_query($sqlx) ) {
		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}
}

if($mode=='change_state') {

	$DATA = sql_fetch("SELECT judge_state FROM cf_apat_loan_request WHERE idx='".$idx."'");

	$JUDGE_STATE = array(
		'1'=>'대기중',
		'2'=>'진행중',
		'3'=>'부결',
		'4'=>'승인');

	$comment = "심사현황 수정: " . $JUDGE_STATE[$DATA['judge_state']] . " → ". $JUDGE_STATE[$state] . "\n수정처리: " . $member['mb_name'];


	$sql = "
		UPDATE
			cf_apat_loan_request
		SET
			judge_state='".$state."',
			judge_name='".$member['mb_name']."',
			last_editdate=NOW()
		WHERE
			idx='".$idx."'";
	if(sql_query($sql)) {
		$sqlx = "
			INSERT INTO
				cf_apat_loan_request_judge_log
			SET
				req_idx='".$idx."',
				writer='system',
				comment='".$comment."',
				regdate=NOW()";
		sql_query($sqlx);

		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}

}

if($mode=='change_judge') {

	$DATA = sql_fetch("SELECT judge_state FROM cf_apat_loan_request WHERE idx='".$idx."'");

	$comment = "물건 담당자 변경: " . $DATA['judge_name'] . " → ". $judge_name . "\n수정처리: " . $member['mb_name'];


	$sql = "
		UPDATE
			cf_apat_loan_request
		SET
			judge_name='".$judge_name."',
			last_editdate=NOW()
		WHERE
			idx='".$idx."'";
	if(sql_query($sql)) {
		$sqlx = "
			INSERT INTO
				cf_apat_loan_request_judge_log
			SET
				req_idx='".$idx."',
				writer='system',
				comment='".$comment."',
				regdate=NOW()";
		sql_query($sqlx);

		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => $comment);
		echo json_encode($RESULT_ARR);
	}


}

if($mode == "del") {
	$sqld = "DELETE FROM cf_apat_loan_request WHERE idx='".$idx."'";
	sql_query($sqld);

	$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
	echo json_encode($RESULT_ARR);
}

if($mode=='delete') {
	$sqlx = "DELETE FROM cf_apat_loan_request_judge_log WHERE idx='".$commidx."'";
	if( sql_query($sqlx) ) {
		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}
}

sql_close();
exit;

?>