<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once("./_common.php");

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) { ${$_POST[$k]} = trim($v); }

if($mode=='new') {

	if($comment) $comment = sql_real_escape_string($comment);

	$sqlx = "
		INSERT INTO
			hloan_comment_renew
		SET
			req_idx='".$idx."',
			writer='".$member['mb_name']."',
			mb_id ='".$member['mb_id']."',
			comment='".$comment."',
			regdate=NOW()";
	if( sql_query($sqlx) ) {
		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}
}

if($mode=='delete') {
	$sqlx = "DELETE FROM hloan_comment_renew WHERE idx='".$commidx."'";
	if( sql_query($sqlx) ) {
		$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
		echo json_encode($RESULT_ARR);
	}
}

sql_close();
exit;
?>