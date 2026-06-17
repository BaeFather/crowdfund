<?

set_time_limit(0);

include_once("_common.php");
include_once(G5_LIB_PATH . "/sms.lib.php");

while(list($k, $v)=each($_REQUEST)) { ${$k} = trim($v); }


if(!$event_no) {
	$ARR = array('result'=>'FAIL', 'message'=>'이벤트번호가 없음!!');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
}

$today = date('Y-m-d');

$TARGET_EVENT = sql_fetch("
	SELECT
		A.*,
		(SELECT COUNT(idx) FROM cf_partner_coupon_bank WHERE is_real='1' AND pid=A.pid AND avail_sdd<='".$today."' AND avail_edd>='".$today."' AND mb_no='' AND give_dt IS NULL AND msg IS NULL) AS useble_coupon_count
	FROM
		cf_partner_event_config A
	WHERE 1
		AND A.event_no='".$event_no."'");

//print_r($TARGET_EVENT);


///////////////////////////////////////////////////////////////////////////////
// 쿠폰 배정 (개인회원만 대상으로 함)
///////////////////////////////////////////////////////////////////////////////

if($mode=='coupon_set') {

	$where = "";
	$where.= " AND A.event_no = '".$event_no."'";
	$where.= " AND A.coupon_serial_no = ''";
	$where.= " AND B.member_type='1' AND B.mb_level IN('1','2','3','4','5')";

	$sql = "
		SELECT
			COUNT(A.idx) AS cnt
		FROM
			cf_partner_event_reward_log A
		LEFT JOIN
			g5_member B  ON A.member_idx=B.mb_no
		WHERE 1
			$where";
	//print_r($sql);

	$need_coupon_count = sql_fetch($sql)['cnt'];

	if($need_coupon_count > $TARGET_EVENT['useble_coupon_count']) {

		$message = "";
		$message.= "여유쿠폰이 부족합니다.\n";
		$message.= "필요쿠폰 : " . number_format($need_coupon_count)."개\n";
		$message.= "잔여쿠폰 : " . number_format($TARGET_EVENT['useble_coupon_count'])."개";

		$ARR = array('result'=>'FAIL', 'message'=>$message);
		echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
		sql_close(); exit;

	}


	// 쿠폰지급대상자 추출 (탈퇴자 제외)
	$sql = "
		SELECT
			A.idx, A.member_idx
		FROM
			cf_partner_event_reward_log A
		LEFT JOIN
			g5_member B  ON A.member_idx=B.mb_no
		WHERE 1
			$where
		ORDER BY
			idx ASC";
	$res = sql_query($sql);
	$rows = $res->num_rows;

	$update_count = 0;
	$give_dt = date('Y-m-d H:i:s');

	for($i=0,$j=1; $i<$rows; $i++,$j++) {

		$REWARD_LOG = sql_fetch_array($res);

		// 미사용 유효쿠폰 추출
		$COUPON_DATA = sql_fetch("
			SELECT idx, coupon_serial_no
			FROM cf_partner_coupon_bank
			WHERE pid='".$TARGET_EVENT['pid']."' AND avail_sdd<='".$today."' AND avail_edd>='".$today."' AND mb_no='' AND give_dt IS NULL
			ORDER BY idx DESC
			LIMIT 1");

		if($COUPON_DATA['idx'] && $COUPON_DATA['coupon_serial_no']) {

			sql_query("START TRANSACTION");

			// 쿠폰배분처리
			$sqlX1 = "UPDATE cf_partner_event_reward_log SET coupon_serial_no = '".$COUPON_DATA['coupon_serial_no']."' WHERE idx='".$REWARD_LOG['idx']."'";
			//echo $sqlX1 . "\n";
			$resX1 = sql_query($sqlX1);

			// 쿠폰데이터 재사용불가처리
			$sqlX2 = "
				UPDATE
					cf_partner_coupon_bank
				SET
					event_no = '".$event_no."',
					mb_no    = '".$REWARD_LOG['member_idx']."',
					give_dt  = '".$give_dt."'
				WHERE
					idx = '".$COUPON_DATA['idx']."'";
			//echo $sqlX2 . "\n";
			$resX2 = sql_query($sqlX2);

			if($resX1 && $resX2) {

				if( sql_affected_rows() ) $update_count += 1;
				sql_query("COMMIT");

			}
			else {

				sql_query("ROLLBACK");

				$message = "쿠폰 발급처리중 오류 발생 (".number_format($update_count). "/" . number_format($rows) . "건 처리 완료)";
				$ARR = array('result'=>'FAIL', 'message'=>$message);
				echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
				sql_close(); exit;

			}

		}

	}
	sql_free_result($res);

	$sqlXX = "UPDATE cf_partner_event_config SET coupon_set = '1' WHERE event_no = '".$event_no."'";
	if( sql_query($sqlXX) ) {

		$message = "쿠폰 발급 완료 (" . number_format($update_count) . "/" . number_format($rows) . "건)";

		$ARR = array('result'=>'SUCCESS', 'message'=>$message);
		echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

	}
	else {

		$ARR = array('result'=>'FAIL', 'message'=>'쿠폰발급완료 플래그 등록 오류!!!');
		echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

	}

}


///////////////////////////////////////////////////////////////////////////////
// 쿠폰 발송
///////////////////////////////////////////////////////////////////////////////

if($mode=='coupon_send') {

	// 문자 발송 시작시간 (20분후 예약발송. 1000개당 30분 텀으로 발송)
	$send_timestamp = time() + 1200;
	$send_date = date("Y-m-d H:i", $send_timestamp) . ':00';

	$where = "";
	$where.= " AND A.event_no = '".$event_no."'";
	$where.= " AND A.coupon_serial_no != ''";
	$where.= " AND B.member_type='1' AND B.mb_level IN('1','2','3','4','5')";
	//$where.= " AND C.phone='' AND (C.sms_dt IS NULL OR sms_dt='0000-00-00 00:00:00')"; <=== 고쳐

	$sql = "
		SELECT
			A.coupon_serial_no, A.member_idx,
			B.mb_hp,
			C.idx AS coupon_bank_idx,
			C.avail_sdd, C.avail_edd
		FROM
			cf_partner_event_reward_log A
		LEFT JOIN
			g5_member B  ON A.member_idx=B.mb_no
		LEFT JOIN
			cf_partner_coupon_bank C  ON A.coupon_serial_no=C.coupon_serial_no
		WHERE 1
			$where
		ORDER BY
			B.mb_10 DESC,
			A.idx ASC";

	$res  = sql_query($sql);
	$rows = $res->num_rows;

	$update_count = 0;
	$give_dt = date('Y-m-d H:i:s');
	$send_id = $event_no;

	for($i=0,$j=1; $i<$rows; $i++,$j++) {

		$REWARD_LOG = sql_fetch_array($res);
		$REWARD_LOG['mb_hp'] = masterDecrypt($REWARD_LOG['mb_hp'], false);

		// 테스트 치환
		//if($j==1) $REWARD_LOG['mb_hp'] = '01064063972';
		//if($j==2) $REWARD_LOG['mb_hp'] = '01032809295';


		//print_r($REWARD_LOG); echo "\n";

		$send_msg = $TARGET_EVENT['message'];
		$send_msg = preg_replace("/\"/", "＂", $send_msg);
		$send_msg = preg_replace("/\'/", "＂", $send_msg);
		$send_msg = preg_replace("/\{coupon_point\}/", number_format($TARGET_EVENT['coupon_point']), $send_msg);
		$send_msg = preg_replace("/\{coupon_serial_no\}/", $REWARD_LOG['coupon_serial_no'], $send_msg);
		$send_msg = preg_replace("/\{edate\}/", date("Y년 m월 d일", strtotime($REWARD_LOG['avail_edd'])), $send_msg);

		//if($j == 1) print_r($send_msg); echo "\n";

		$sms_reg = "";
		$sms_reg = unit_sms_send_smtnt($_admin_sms_number, $REWARD_LOG['mb_hp'], $send_msg, $send_date, $send_id);

		if( $sms_reg ) {
			// 쿠폰발송완료처리 (cf_partner_coupon_bank)
			$sqlX2 = "
				UPDATE
					cf_partner_coupon_bank
				SET
					phone  = '".$REWARD_LOG['mb_hp']."',
					sms_dt = '".$send_date."',
					msg    = '".$send_msg."'
				WHERE
					idx = '".$REWARD_LOG['coupon_bank_idx']."'";
			$resX2 = sql_query($sqlX2);

			if($resX2) {
				if( sql_affected_rows() ) $update_count += 1;
			}
			else {
				$message = "쿠폰 발급처리중 오류 발생 (".number_format($update_count). "/" . number_format($rows) . "건 처리 완료)";
				$ARR = array('result'=>'FAIL', 'message'=>$message);
				echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
				sql_close(); exit;
			}

		}

		// 천천히 유입되도록 발송시간 텀을 부여한다.
		if( ($j%1000) == 0 ) {
			$send_timestamp += 180;
			$send_date = date("Y-m-d H:i", $send_timestamp) . ':00';
		}

	}
	sql_free_result($res);

	if($update_count) {

		$sqlXX = "UPDATE cf_partner_event_config SET coupon_send = '1' WHERE event_no = '".$event_no."'";
		if( sql_query($sqlXX) ) {
			$message = "쿠폰 문자발송 완료 (" . number_format($update_count) . "/" . number_format($rows) . "건)";

			$ARR = array('result'=>'SUCCESS', 'message'=>$message);
			echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

		}
		else {

			$ARR = array('result'=>'FAIL', 'message'=>'쿠폰발급완료 플래그 등록 오류!!!');
			echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

		}

	}

}


sql_close();
exit;



/*
	UPDATE
		cf_partner_event_reward_log
	SET
		coupon_serial_no = ''
	WHERE
		event_no IN ('11','12');

	UPDATE
		cf_partner_coupon_bank
	SET
		event_no = '',
		mb_no = '',
		give_dt = NULL,
		phone = '',
		sms_dt = NULL,
		msg = NULL
	WHERE
		event_no IN ('11','12');

	UPDATE
		cf_partner_event_config
	SET
		coupon_set = '',
		coupon_send = ''
	WHERE
		event_no IN ('11','12');
*/

?>