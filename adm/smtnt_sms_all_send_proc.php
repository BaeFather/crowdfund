<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

set_time_limit(0);

include_once('./_common.php');
include_once(G5_LIB_PATH.'/sms.lib.php');

// post로 받은 데이터를 변수화
while( list($k, $v) = each($_POST) ) { if(!is_array($_POST[$k])) ${$k} = trim($v); }

//print_r($_REQUEST); exit;


// 예약발송 시간설정
if($send_time == 'r') {
	$send_date = $send_ymd.' '.$send_h.':'.$send_i.':00';
	$send_timestamp = strtotime($send_date);
}


$send_id = get_sms_send_id_smtnt();


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
		GROUP BY
			mb_hp
		ORDER BY
			mb_no DESC";
	$res = sql_query($sql);
	$rows = $res->num_rows;

	// 500건 이상의 발송 + 직발요청인 경우 20분후 예약전송하는것으로...
	if($rows > 500) {
		if(!$send_date) {
			$send_timestamp = time() + 1200;
			$send_date = date("Y-m-d H:i", $send_timestamp) . ':00';
		}
	}

	$send_proc_count = 0;

	for($i=0,$j=1; $i<$rows; $i++,$j++) {

		$LIST = sql_fetch_array($res);

		$LIST['mb_hp'] = masterDecrypt($LIST['mb_hp'], false);

		if(($j%500)==0)	{
			$send_timestamp += 300;
			$send_date = date("Y-m-d H:i", $send_timestamp) . ':00';		// 500건 이상 발송건 5분텀 에
		}

		if( $sms_res = unit_sms_send_smtnt($from_hp, $LIST['mb_hp'], $sms_msg, $send_date, $send_id) ) $send_proc_count++;
		//echo "unit_sms_send('".$from_hp."', '".$LIST["mb_hp"]."', '".$sms_msg."', '".$send_date."', '".$send_id."');\n";

	}

	$DATA = array(
		'result' => 'SUCCESS',
		'msg'    => number_format($send_proc_count).'건 처리 완료'
	);

	echo json_encode($DATA);

}
else {

	///////////////////////
	// 선택발송
	///////////////////////

	$chk_count = count($chk);

	if($chk_count == 0) {

		$DATA = array(
			'result'=>'FAIL',
			'msg'=>'수신번호 없음.'
		);

	}
	else {

		$send_proc_count = 0;

		// 500건 이상의 발송 + 직발요청인 경우 20분후 예약전송하는것으로...
		if($chk_count > 500) {
			if(!$send_date) {
				$send_timestamp = time() + 1200;
				$send_date = date("Y-m-d H:i", $send_timestamp) . ':00';
			}
		}

		for($i=0,$j=1; $i<$chk_count; $i++,$j++) {

			if(($j%500)==0)	{
				$send_timestamp += 300;
				$send_date = date("Y-m-d H:i", $send_timestamp) . ':00';		// 500건 이상 발송건 5분텀 에
			}

			if( $sms_res = unit_sms_send_smtnt($from_hp, $chk[$i], $sms_msg, $send_date, $send_id) ) $send_proc_count++;
			//echo "unit_sms_send('".$from_hp."', '".$chk[$i]."', '".$sms_msg."', '".$send_date."', '".$send_id."');\n";

		}


		$DATA = array(
			'result' => 'SUCCESS',
			'msg'    => number_format($send_proc_count) . '건 처리 완료'
		);

	}

	echo json_encode($DATA);

}

exit;

?>