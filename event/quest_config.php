<?

if( date('YmdH')>='2019073118' ) { msg_go("종료된 이벤트 입니다.\n감사합니다."); }

$ECONF['event_id'] = "100BEVENT2";
$ECONF['title']    = "1000억돌파 감사이벤트";
$ECONF['purpose_point'] = 3000000;		// 이벤트 목표 금액
$ECONF['event_sdate'] = "";
$ECONF['event_edate'] = "";

$R = sql_fetch("SELECT IFNULL(SUM(point),0) AS point FROM event_entry_log WHERE event_id='".$ECONF['event_id']."' AND invalid=''");

$ECONF['entered_point'] = $R['point'];			// 참여하여 분배된 금액
$balance_point = $ECONF['purpose_point']-$R['point'];
$ECONF['balance_point'] = ($balance_point > 0) ? $balance_point : 0;	// 이벤트 잔여금액


$ck_entry_key = get_cookie('ck_entry_key');


//************** 가상계좌 미발급 참여자 1시간 후 내역 무효화 **************//
$tsql = "SELECT idx FROM event_entry_log WHERE member_idx='' AND invalid='' AND regdate < '".date('Y-m-d H:i:s', time()-3600)."'";
$tres = sql_query($tsql);
$trows = sql_num_rows($tres);
for($i=0; $i<$trows; $i++) {
	$trow = sql_fetch_array($tres);
	sql_query("
		UPDATE
			event_entry_log
		SET
			invalid='1',
			invalid_date=NOW()
		WHERE
			idx='".$trow['idx']."'");
}
sql_free_result($tres);
//************** 가상계좌 미발급 참여자 1시간 후 내역 무효화 **************//


// 참여여부 판별
$is_entered = '';
if($member['mb_no'] && $member['mb_level']=='1') {

	$DATA = sql_fetch("SELECT idx, point, paid FROM event_entry_log WHERE event_id='".$ECONF['event_id']."' AND hp!='' AND hp='".masterEncrypt($member['mb_hp'], false)."' AND invalid=''");

	if($DATA['idx']) {
		$is_entered = '1';
	}
	else {
		if($ck_entry_key) {
			$DATA = sql_fetch("SELECT idx, point FROM event_entry_log WHERE entry_key='".$ck_entry_key."' AND member_idx='' AND invalid=''");
			if($DATA['idx']) $is_entered = 'ready';
		}
	}

}
else {

	if($ck_entry_key) {
		$DATA = sql_fetch("SELECT idx, point FROM event_entry_log WHERE entry_key='".$ck_entry_key."' AND member_idx='' AND invalid=''");
		if($DATA['idx']) {
			$is_entered = 'ready';
		}
	}

}


function pointMsg($point) {
	if($point==500)        $msg = "어익후 이런~ 똥손이셨네요;;;<br/>안타깝습니다 <img src='/images/event/emoticon03.png' height='38'>";
	else if($point==1000)  $msg = "이것밖에 못드려 죄송해요 <img src='/images/event/emoticon02.png' height='38'>";
	else if($point==1500)  $msg = "어머~ 소확행 열차에 타셨네요 <img src='/images/event/emoticon06.png' height='38'>";
	else if($point==2000)  $msg = "어머~ 소확행 열차에 타셨네요 <img src='/images/event/emoticon01.png' height='38'>";
	else if($point==2500)  $msg = "우와!! 오늘 로또도 구매해 보심이.. <img src='/images/event/emoticon04.png' height='38'>";
	else if($point==3000)  $msg = "뙇!! 황금손이시네요~ <img src='/images/event/emoticon05.png' height='38'>";

	return $msg;
}

?>