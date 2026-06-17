<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once('./_common.php');
include_once(G5_LIB_PATH.'/sms.lib.php');

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) { $$_POST[$k] = $v; }

$send_date = ($send_time == 'r') ? $send_ymd.' '.$send_h.':'.$send_i.':00' : NULL;

$send_id = get_sms_send_id();

if($member_all) {

	///////////////////////
	// 전체발송
	///////////////////////
	$sql = "
		SELECT
			mb_hp
		FROM
			g5_member
		WHERE 1
			AND mb_level IN(1,2,3,4,5)
			AND is_rest = 'N'
			AND mb_hp != ''
			AND mb_sms = '1'
			AND (chosun_userid = '' OR chosun_userid is NULL)
		GROUP BY
			mb_hp
		ORDER BY
			mb_10 DESC, mb_no DESC";
	$res = sql_query($sql);

	$send_proc_count = 0;
	while( $LIST = sql_fetch_array($res) ) {
		$LIST['mb_hp'] = masterDecrypt($LIST['mb_hp'], false);

		if( $sms_res = unit_sms_send($from_hp, $LIST['mb_hp'], $sms_msg, $send_date, $send_id) ) $send_proc_count++;
		//echo 'unit_sms_send('.$from_hp.', '.$LIST['mb_hp'].', '.$sms_msg.', '.$send_date.')<br>'.PHP_EOL;
	}

	$DATA = array(
		'result'=>'SUCCESS',
		'msg'=>number_format($send_proc_count).'건 처리 완료'
	);
	echo json_encode($DATA);

}
else {

	///////////////////////
	// 선택발송
	///////////////////////

	if(count($chk) == 0) {

		$DATA = array(
			'result'=>'FAIL',
			'msg'=>'수신번호 없음.'
		);

	}
	else {
		$send_proc_count = 0;

		foreach($chk as $k => $v) {
			$to_hp = $v;
			if($to_hp != '') {
				if( $sms_res = unit_sms_send($from_hp, $to_hp, $sms_msg, $send_date,$send_id) ) $send_proc_count++;
			}
		}

		$DATA = array(
			'result'=>'SUCCESS',
			'msg'=>number_format($send_proc_count) . '건 처리 완료'
		);
	}

	echo json_encode($DATA);

}

exit;

?>