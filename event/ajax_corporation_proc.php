<?
###########################################
## 투자상담예약 처리
###########################################

include_once('_common.php');

if($_SERVER["REQUEST_METHOD"]!="POST") { exit; }

if(!$is_member) { echo 'X01'; exit; }
//if($member['member_type']=='2' && $member['is_creditor']=='Y') { echo 'X02'; exit; }

$event_idx = 3;

//$r = sql_fetch("SELECT COUNT(idx) AS cnt FROM invitation_event_request WHERE event_idx='$event_idx' AND mb_no='".$member['mb_no']."' AND LEFT(rdate, 10)='".date('Y-m-d')."'");
//if($r[cnt]) {
//	echo 'X03'; exit;
//}

$schedule_req_date = $schedule_req_date." ".sprintf("%02d", $schedule_req_time).":00:00";
$device = (G5_IS_MOBILE) ? "MOBILE" : "PC";

$sql = "
	INSERT INTO
		invitation_event_request
	SET
		event_idx = '$event_idx',
		mb_no = '".$member['mb_no']."',
		nm_name = '".$member['mb_name']."',
		schedule_req_date = '".$schedule_req_date."',
		nm_phone = '".$member['mb_hp']."',
		ip = '".$_SERVER['REMOTE_ADDR']."',
		device = '$device',
		view_flag = 'N',
		rdate = NOW()";

if(sql_query($sql)) {
	echo '1'; exit;
}
else {
	echo '2'; exit;
}

exit;

?>