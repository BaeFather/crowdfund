<?

include_once('./_common.php');
include_once('./quest_config.php');

usleep(1500000);


if($ECONF['balance_point']==0) {
	$ARR['data']['result'] = "FINISHED_EVENT";

	$json = json_encode($ARR, JSON_PRETTY_PRINT);
	echo $json;

	exit;
}

while( list($k, $v) = each($_POST) ) { ${$k} = trim($v); }

if(!$member['mb_id']) {
	$ARR['data']['result'] = "LOGIN_PLEASE";

	$json = json_encode($ARR, JSON_PRETTY_PRINT);
	echo $json;

	exit;
}


if( in_array($is_entered, array('ready', '1')) ) {

	if($is_entered=='ready') {
		$ARR['data']['result'] = "DUPLICATE_ENTRY";
	}
	else {
		$ARR['data']['result'] = "DUPLICATE_ENTRY";
	}

}
else {

	$standard_answer = "4층";
	$values  = array('500','1000','1500','2000','2500','3000');

	// 본 이벤트로 가입한 회원의 추첨율 변화
	if($member['event_id'] == $ECONF['event_id']) {
		$weights = array(2,26,26,26,10,10);		// 가중퍼센티지
		$index = weighted_random($weights);
		$point = $values[$index];
	}
	else {
		$weights = array(55,20,15,6,3,1);
		$index = weighted_random($weights);
		$point = $values[$index];
	}

	if($ECONF['balance_point'] <= 3000) $point = $ECONF['balance_point'];
	$str = pointMsg($point);

	if($answer == $standard_answer) {

		$entry_key = strtoupper(uniqid($ECONF['event_id'].'_'));
		set_cookie('ck_entry_key', $entry_key, 3600);

		// 청남이 테스트만 가능하도록 결과저장 하지 않음.
		if($member['mb_id']=='judero831') {
			$ARR['data']['result'] = 'SUCCESS';
			$ARR['data']['point']  = number_format($point);
			$ARR['data']['msg']    = $str;
			$ARR['data']['va']     = ($member['virtual_account2']) ? '1' : '';
		}
		else {

			// 정답일 경우에만 로그 남김
			$sql = "
				INSERT INTO
					event_entry_log
				SET
						event_id = '".$ECONF['event_id']."'
					, entry_key = '".$entry_key."'
					, point = '".$point."'
					, regdate = NOW()";
			if($member['virtual_account2']) {
				$sql.= ", hp = '".masterEncrypt($member['mb_hp'], false)."'";
				$sql.= ", member_idx = '".$member['mb_no']."'";
			}

			if( $result = sql_query($sql) ) {
				$ARR['data']['result'] = 'SUCCESS';
				$ARR['data']['point']  = number_format($point);
				$ARR['data']['msg']    = $str;
				$ARR['data']['va']     = ($member['virtual_account2']) ? '1' : '';
			}

		}

	}
	else {

		$ARR['data']['result'] = 'FAIL';

	}

}

header('Cache-Control: no-cache');
header('Pragma: no-cache');
header("Content-Type:application/json");

$json = json_encode($ARR, JSON_PRETTY_PRINT);
echo $json;


sql_close();
exit;

?>