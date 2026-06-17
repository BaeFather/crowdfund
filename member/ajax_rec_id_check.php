<?
include_once("_common.php");

$rec_mb_id = trim($_REQUEST['rec_mb_id']);
$pid	   = trim($_REQUEST['pid']);

IF(!$pid) { $pid = get_cookie('ck_pid'); }

if($rec_mb_id=='admin') {
	$RESULT_ARR = array('result' => '3', 'message' => '* 추천인으로 선정할 수 있는 아이디가 아닙니다.');
	echo json_encode($RESULT_ARR);
	exit;
}

IF($pid == "naverpay") {
	$RESULT_ARR = array('result' => '1', 'message' => '* 추천 가능한 추천인코드 입니다.');
	echo json_encode($RESULT_ARR);
	exit;
}

$sql = "SELECT mb_no, mb_id, mb_level FROM g5_member WHERE mb_id='$rec_mb_id' AND mb_level>'0'";
$ROW = sql_fetch($sql);

if($ROW['mb_no']) {
	if($ROW['mb_level']=='0' || $ROW['mb_level'] > 8) {
		$RESULT_ARR = array('result' => '3', 'message' => '* 추천인으로 선정할 수 있는 아이디가 아닙니다.');
		echo json_encode($RESULT_ARR);
		exit;
	}
	else {
		$RESULT_ARR = array('result' => '1', 'message' => '* 추천 가능한 아이디 입니다.');
		echo json_encode($RESULT_ARR);
		exit;
	}
}
else {
	$RESULT_ARR = array('result' => '2', 'message' => '* 추천 가능한 아이디가 아닙니다.');
	echo json_encode($RESULT_ARR);
	exit;
}

sql_close();

?>