<?
###############################################################################
## 문자발송처리
###############################################################################

include_once('_common.php');
include_once(G5_LIB_PATH.'/sms.lib.php');

// POST로 받은 데이터를 변수화
while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }			//foreach($_REQUEST as $k=>$v) { ${$k} = trim($v); }

//print_rr($_REQUEST); exit;

if(!$mode) {
	$DATA = array('result'=>'FAIL', 'msg'=>'발송불가: 동작모드가 전송되지 않았습니다.');
	echo json_encode($DATA);
	sql_close(); exit;
}
if(!$sms_msg) {
	$DATA = array('result'=>'FAIL', 'msg'=>'발송불가: 메세지 내용이 없습니다.');
	echo json_encode($DATA);
	sql_close(); exit;
}
if(!$from_hp) {
	$DATA = array('result'=>'FAIL', 'msg'=>'발송불가: 발신자 번호가 없습니다.');
	echo json_encode($DATA);
	sql_close(); exit;
}
if(!$to_hp) {
	$DATA = array('result'=>'FAIL', 'msg'=>'발송불가: 수신자 번호가 없습니다.');
	echo json_encode($DATA);
	sql_close(); exit;
}

if($reserve_send=='1') {
	$send_date = $send_ymd.' '.$send_h.':'.$send_i.':00';

	$timeDiff = strtotime($send_date) - time();

	if($timeDiff < 1800) {
		$DATA = array('result'=>'FAIL', 'msg'=>'예약발송 일시는 현재보다 최소 30분 이후의 시간대를 설정하십시요.');
		echo json_encode($DATA);
		sql_close(); exit;
	}

}
else {
	$send_date = '';
}

//echo $timeDiff; exit;

$send_id = get_sms_send_id();
$sms_msg = sql_real_escape_string($sms_msg);

if( $sms_res = unit_sms_send_smtnt($from_hp, $to_hp, $sms_msg, $send_date, $send_id) ) {

	// 채권관리 페이지에서 전달된 SMS발송요청인 경우 개별 로그 저장
	if($hcseq) {

		$smssql = "
				INSERT INTO
					hloan_comment
				SET
					divi	  = 'sms',
					req_idx = '$hcseq',
					writer  = '".$member['mb_name']."',
					mb_id   = '".$member['mb_id']."',
					comment = '$sms_msg',
					regdate = NOW()";
		sql_query($smssql);

	}


	$DATA = array('result'=>'SUCCESS', 'msg'=>'');
	echo json_encode($DATA);
}

sql_close();
exit;

?>