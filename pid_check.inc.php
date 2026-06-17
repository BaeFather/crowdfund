<?
///////////////////////////////////////////////////////////////////////////////
// ★★★★★ 파트너ID 사전처리 ★★★★★
// pid 체크가 필요한 모든파일에 include 할 것
///////////////////////////////////////////////////////////////////////////////

include_once("_common.php");

if( trim($_REQUEST['p']) || trim($_REQUEST['pid']) ) {

	$pid = (trim($_REQUEST['p'])) ? sql_real_escape_string(trim($_REQUEST['p'])) : sql_real_escape_string(trim($_REQUEST['pid']));
	$pid_referer = ( trim($_SERVER['HTTP_REFERER']) ) ? sql_real_escape_string(trim($_SERVER['HTTP_REFERER'])) : "";

	if( !get_cookie("ck_pid") ) {
		//로그기록
		sql_query("INSERT INTO event_landing_check (rd, rtime, pid, referer, ip) VALUES (CURDATE(), CURTIME(), '".$pid."', '".$pid_referer."', '".$_SERVER['REMOTE_ADDR']."')");
		sql_query("DELETE FROM event_landing_check WHERE rd <= '".date('Y-m-d', strtotime('-7 days'))."'");
	}

}


if($pid) {

	$cookie_life_time = 86400;
	$pid_cookie_time = $cookie_life_time;		// 쿠키인정시간
	$pid_join_url    = "/";

	set_cookie("ck_pid", $pid, $pid_cookie_time);

}
else {

	$pid = get_cookie("ck_pid");

}

if($pid) {

	// 기간이 있는 이벤트 pid 인경우 유효기간 체크 -> 기간 만료시 pid 값 삭제
	$ingEventSql = "SELECT event_no, sdate, edate FROM cf_partner_event_config WHERE pid='".$pid."' ORDER BY idx DESC LIMIT 1";
	$ingEvent = sql_fetch($ingEventSql);
	if($ingEvent['event_no']) {
		if($ingEvent['edate'] < G5_TIME_YMD) {
			set_cookie("ck_pid", "", -1);		// 기간종료시 쿠키 제거
			unset($pid);
		}
	}

}

?>