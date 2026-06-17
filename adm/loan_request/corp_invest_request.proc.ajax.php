<?
###############################################################################
##  법인투자케어서비스 PROC
###############################################################################

include_once("./_common.php");

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) { ${$_POST[$k]} = trim($v); }

$g5['title'] = "법인투자케어서비스 처리";
if($member['mb_level'] == '9') include_once(G5_ADMIN_PATH."/inc_sub_admin_access_check.php");		// 부관리자 접속로그 등록

///////////////////////////////////////
// 본 글  등록
///////////////////////////////////////
if($mode=='update') {

	$ROW = sql_fetch("SELECT admin_content FROM cf_care_service_request WHERE idx = '".$idx."'");

	$admin_content = sql_real_escape_string($admin_content);

	if( $admin_content != sql_real_escape_string($ROW['admin_content']) ) {

		$sqlx = "
			UPDATE
				cf_care_service_request
			SET
				check_admin_id = '".$member['mb_id']."',
				admin_content     = '".$admin_content."',
				last_editdate  = NOW()
			WHERE
				idx = '".$idx."'";
		if( sql_query($sqlx) ) {
			$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
			echo json_encode($RESULT_ARR);
		}

	}
	else {
		$RESULT_ARR = array('result' => 'ERROR', 'message' => '변동사항없음');
		echo json_encode($RESULT_ARR);
	}

}


///////////////////////////////////////
// 본게시글 삭제
///////////////////////////////////////
if($mode == "drop") {

	$sqld = "
		UPDATE
			cf_care_service_request
		SET
			is_drop = '1',
			drop_admin_id = '".$member['mb_id']."',
			last_editdate = NOW()
		WHERE
			idx = '".$idx."'";
	sql_query($sqld);

	$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
	echo json_encode($RESULT_ARR);

}


///////////////////////////////////////
// 코멘트 등록
///////////////////////////////////////
if($mode=='cnew') {

	if($comment) $comment = sql_real_escape_string($comment);

	$sqlx = "
		INSERT INTO
			cf_care_service_request_comment
		SET
			req_idx = '".$idx."',
			writer = '".$member['mb_id']."',
			comment = '".$comment."',
			regdate = NOW()";
	if( sql_query($sqlx) ) {
		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}

}


///////////////////////////////////////
// 코멘트글 삭제
///////////////////////////////////////
if($mode=='cdelete') {

	$sqlx = "
		DELETE FROM
			cf_care_service_request_comment
		WHERE
			idx='".$commidx."'";
	if( sql_query($sqlx) ) {
		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}

}

sql_close();
exit;

?>