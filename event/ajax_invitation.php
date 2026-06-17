<?
// 초대이벤트 등록 프로세스

include_once("_common.php");

$device = (G5_IS_MOBILE) ? 'mobile' : 'pc';

while( list($k, $v)=each($_REQUEST) ) { ${$k} = trim($v); }


$EVENT = sql_fetch("SELECT * FROM invitation_event WHERE idx='$event_idx' AND cancel='N'");
if($EVENT) {
	if( $EVENT['sdate'] > G5_TIME_YMD ) {
		echo "wait";
		exit;
	}
	else if( $EVENT['edate'] < G5_TIME_YMD ) {
		echo "finished";
		exit;
	}
}
else {
	exit;
}

if($event_idx=='4') {

	//금일 중복신청자만 체크
	$sql = "SELECT idx FROM invitation_event_request WHERE event_idx='$event_idx' AND mb_no='".$member['mb_no']."' AND LEFT(rdate, 10)='".date('Y-m-d')."'";
	$row = sql_fetch($sql);

	if($row['idx']) {

		echo "2";		// 중복등록

	}
	else {
		$sql3 = "
			INSERT INTO
				invitation_event_request
			SET
				event_idx  = '$event_idx',
				mb_no      = '".$member['mb_no']."',
				ip         = '".$_SERVER['REMOTE_ADDR']."',
				device     = '$device',
				rdate      = NOW()";
		$res3 = sql_query($sql3);
		if($res3) {
			echo "1";		// 정상등록
		}
	}

}
else {

	$sql = "SELECT idx FROM invitation_event_request WHERE event_idx='$event_idx' AND nm_co_name='$nm_co_name' AND nm_name='$nm_name' AND nm_phone='$nm_phone'";
//$sql = "SELECT idx FROM invitation_event_request WHERE event_idx='{$_POST['event_idx']}' AND mb_idx='".$member['mb_no']."'";
	$row = sql_fetch($sql);

	if($row['idx']) {

		echo "2";		// 중복등록

	}
	else {

		//투자이력 확인
		//$sql2 = "SELECT COUNT(idx) AS cnt_idx FROM cf_product_invest WHERE member_idx='".$member['mb_no']."' AND invest_state='Y'";
		//$row2 = sql_fetch($sql2);
		//if($row2['cnt_idx'] > 0) {
			$sql3 = "
				INSERT INTO
					invitation_event_request
				SET
					event_idx  = '$event_idx',
					mb_no      = '".$member['mb_no']."',
					nm_co_name = '$nm_co_name',
					nm_name    = '$nm_name',
					nm_phone   = '$nm_phone',
					ip         = '".$_SERVER['REMOTE_ADDR']."',
					device     = '$device',
					rdate      = NOW()";

			$res3 = sql_query($sql3);

			if($res3) {
				echo "1";		// 정상등록
			}
		//}
		//else {
		//	echo "3";  // 투자이력 없음
		//}

	}

}

exit;

?>