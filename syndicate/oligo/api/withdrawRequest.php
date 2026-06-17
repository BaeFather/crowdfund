<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/withdrawRequest.do
## 11. 출금요청
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");
include_once(G5_LIB_PATH."/insidebank.lib.php");
include_once(G5_LIB_PATH.'/sms.lib.php');


//$REQUEST['ci']     = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
//$REQUEST['amount'] = '100000';


$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

$MB = get_member($mb_id);

if($REQUEST['amount'] <= 0) {
	$ARR = array('code'=>'9999', '출금금액오류');
	echo printJson($ARR); exit;
}

if(!$MB['mb_point']) $MB['mb_point'] = 0;

if($MB['mb_point'] <= 0) {
	$ARR = array('code'=>'9999', 'msg'=>'출금금액오류');
	echo printJson($ARR); exit;
}

if($MB['withdrawal_posible_amount'] < $REQUEST['amount']) {
	$ARR = array('code'=>'9000', 'msg'=>'예치금 관리기관 출금정책에 의하여, 24시간 내 입금하신 예치금은 만1일 경과 후 출금 가능합니다.');
	echo printJson($ARR); exit;
}

if($MB['bank_code'] == '' || $MB['account_num'] == '' || $MB['bank_private_name'] == '') {
	$ARR = array('code'=>'9999', '출금계좌오류');
	echo printJson($ARR); exit;
}


// 처리중인 출금요청 조회 (10분전 이전 출금요청된 로그중 처리결과가 없는 내역 확인)
$chk_edate = date('Y-m-d H:i:s');
$chk_sdate = date('Y-m-d H:i:s', strtotime($chk_edate)-1800);

$WAITING_DATA = sql_fetch("
	SELECT
		COUNT(idx) AS cnt
	FROM
		IB_request_log
	WHERE 1
		AND request_code = '3200'
		AND mb_id = '".$MB['mb_id']."'
		AND rcode = ''
		AND regdate BETWEEN '".$chk_sdate."' AND '".$chk_edate."'
		AND edate IS NULL");

if($WAITING_DATA['idx']) {
	$ARR = array('code'=>'9999', '기관측 출금결과를 대기중인 요청이 있음.');
	echo printJson($ARR); exit;
}


/*
// 통신상태 사전 점검
$RESULT = insidebank_request('000');
if(!$RESULT || $RESULT['RCODE']!='00000000' || $RESULT['ERRMSG']=='ERROR:EMPTY_DATA') {
	$ARR = array('code'=>'9999', '기관통신오류');
	echo printJson($ARR); exit;
}
*/

///////////////////////////////////////
// 유효잔액 조회(4100)
///////////////////////////////////////
$REQUEST_ARR['REQ_NUM'] = '041';
$REQUEST_ARR['CUST_ID'] = $MB['mb_no'];
$RETURN_ARR = insidebank_request('256', $REQUEST_ARR);

if($RETURN_ARR['RCODE']=='00000000') {

	if($RETURN_ARR['BALANCE_AMT']=='0') {
		$ARR['error'] = array('code' => '9999', 'message' => '출금금액초과(예치잔액 없음)');
		echo printJson($ARR); exit;
	}
	if($RETURN_ARR['WITH_AMT']=='0') {
		$ARR['error'] = array('code' => '9999', 'message' => '출금금액초과(출금제한 해제 금액 없음)');
		echo printJson($ARR); exit;
	}

	// 투자금액(기표전)
	$LOCK = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='".$MB['mb_no']."'
			AND A.invest_state='Y'
			AND B.state=''
			AND B.display='Y'");

	$avail_amount = $RETURN_ARR['BALANCE_AMT'] - $LOCK['amount'];

	if($REQUEST['data']['withdrawAmount'] > $avail_amount || $REQUEST['data']['withdrawAmount'] > $RETURN_ARR['WITH_AMT'])	{
		$ARR['error'] = array('code' => '9999', 'message' => '출금금액초과(' . number_format($avail_amount) . '원 )');
		echo printJson($ARR); exit;
	}

}
else {

	if($RETURN_ARR['RCODE']=='') {
		$ARR['error'] = array('code' => '9999', 'message' => '금융 전산망 통신 지연');
		echo printJson($ARR); exit;
	}
	else {
		$ARR['error'] = array('code' => '9999', 'message' => $RETURN_ARR['ERRMSG']);
		echo printJson($ARR); exit;
	}

}


$REQUEST_ARR = $RETURN_ARR = NULL;

/////////////////////////////////
// 인사이드뱅크 출금요청 시작
/////////////////////////////////
$thisRndNo = sprintf("%04d", rand(0,9999));

$REQUEST_ARR['REQ_NUM']         = '032';																						// 전문번호(출금: 032)
$REQUEST_ARR['CUST_ID']         = $MB['mb_no'];																			// 투자자고객ID
$REQUEST_ARR['TRAN_BANK_CD']    = $MB['bank_code'];																	// 이체은행코드(출금신청한 예치금을 입금받을 은행코드)
$REQUEST_ARR['TRAN_ACCT_NB']    = preg_replace("/-| /", "", $MB['account_num']);		// 이체계좌번호(출금신청한 예치금을 입금받을 계좌번호)
$REQUEST_ARR['TRAN_REMITEE_NM'] = "헬로(" . $thisRndNo . ")";												// 이체계좌에 보낸사람 이름으로 표기될 내용
$REQUEST_ARR['TRAN_AMT']        = $REQUEST['amount'];																// 이체금액
$REQUEST_ARR['TRAN_MEMO']       = "올리고-헬로";																		// 이체계좌통장메모
$REQUEST_ARR['GUAR_MEMO']       = '출금('.$thisRndNo.')';														// 지급계좌통장메모 (예치금모계좌)
$REQUEST_ARR['FUND_KIND']       = '10';																							// 자금성격(10:예치금)
//print_r($REQUEST_ARR);

$RETURN_ARR = insidebank_request('256', $REQUEST_ARR);  // 인사이드뱅크 예치금 출금신청(전문번호: 3200) 발송

$banking_ok = false;

if($RETURN_ARR['RCODE']=='00000000') {

	$banking_ok = true;
	$GUAR_SEQ = $RETURN_ARR['GUAR_SEQ']; // 예치금 모계좌 지급거래번호

}
else if($RETURN_ARR['RCODE']=='AGE00301') {

	$ARR = array('code' => '9999', 'message' => '출금금액초과(최종 입금내역 발생기준 24시간 경과 후 부터 출금가능)');
	echo printJson($ARR);	exit;

}
else if( $RETURN_ARR['RCODE']=='IS0102') {		// 인사이드뱅크-KSNET간 통신지연코드를 받은 경우

	$LAST_ORDER = sql_fetch("
		SELECT
			idx, request_arr, regdate
		FROM
			IB_request_log
		WHERE 1
			AND mb_id='".$MB['mb_id']."'
			AND request_code='3200' AND rcode='IS0102'
		ORDER BY
			idx DESC LIMIT 1");

	if( $LAST_ORDER['idx'] ) {

		$REQUEST_ARR = explode("&", $LAST_ORDER['request_arr']);
		$last_fbseq = preg_replace("/FB_SEQ=/", "", $REQUEST_ARR[0]);

		if($last_fbseq) {
			// 결번요청(8400)  -> 전문 실행 결과값 재전송받기
			$REQUEST_ARR2['SUBMIT_GBN'] = "04";						//전문번호
			$REQUEST_ARR2['TRAN_DATE']  = preg_replace("/-/", "", substr($LAST_ORDER['regdate'],0,10));
			$REQUEST_ARR2['ORI_FB_SEQ'] = $last_fbseq;

			$RETURN_ARR2 = insidebank_request("000", $REQUEST_ARR2);
			if($RETURN_ARR2['ORI_FB_REQCODE']=='00000000') {

				$banking_ok = true;
				$GUAR_SEQ = $last_fbseq;		// 최종 전문에 대한 결과값만 주므로 지급거래번호는 알 수 없어 마지막 실패한 전문번호로 대체함.

				sql_query("UPDATE IB_request_log SET rcode='00000000' WHERE idx='".$LAST_ORDER['idx']."'");

			}
			else {

				$RESULT_ARR = array('code' => '9999', "message" => "금융기관으로의 전송이 정상적으로 실행되지 않았습니다.\n출금 요청을 다시 실행 하여주십시요.");
				echo json_encode($RESULT_ARR);
				exit;

			}
		}

	}

}
else {

	$error_msg = '('.$RETURN_ARR['RCODE'].')'.$RETURN_ARR['ERRMSG'];
	$ARR = array('code' => '9999', 'message' => addSlashes($error_msg));
	echo printJson($ARR); exit;

}

///////////////////////////////////////////
// 정상이체 후 로깅
///////////////////////////////////////////
if($banking_ok) {

	$sql = "
		INSERT INTO
			g5_withdrawal
		SET
			state     = '2',
			mb_no     = '".$MB['mb_no']."',
			mb_id     = '".$MB['mb_id']."',
			req_price = '".$REQUEST['amount']."',
			regdate   = NOW(),
			GUAR_SEQ  = '".$GUAR_SEQ."'";
	if(sql_query($sql)) {

		$withdrawRequestNo = sql_insert_id();

		$point   = (int)$REQUEST['amount'] * -1;
		$content = '예치금 출금';

		//예치금 출금: 회원포인트(예치금) 차감
		insert_point($MB['mb_id'], $point, $content, '@withdrawal', $_CONF['SYNDI_ID'], $_CONF['SYNDI_ID'].'-'.uniqid(''), 0);

	}

	$ARR['code']           = '0000';
	$ARR['msg']            = '정상처리되었습니다.';
	$ARR['amount']         = (string)$REQUEST['amount'];
	$ARR['balance_amount'] = (string)get_point_sum($MB['mb_id']);			// 예치금잔액
	$ARR['withdraw_dt']    = (string)date('Ymd');
	$ARR['withdraw_tm']    = (string)date('His');
	$ARR['bank_cd']        = (string)$MB['bank_code'];
	$ARR['acc_no']         = $crypto->enCrypt($MB['account_num']);
	$ARR['acc_user_nm']    = $MB['bank_private_name'];

}


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>