<?
###############################################################################
## 출금요청처리
###############################################################################

include_once('./_common.php');
include_once(G5_PATH.'/lib/insidebank.lib.php');
include_once(G5_PATH."/lib/wlf.lib.php");
include_once(G5_PATH."/lib/sms.lib.php");

while( list($k, $v) = each($_POST) ) { ${$k} = trim($v); }


if(!$is_member) {
	$RESULT_ARR = array('result' => 'FAIL', 'message' => '회원정보확인불가');		//ERROR-LOGIN
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}

if($mb_id != $member['mb_id']) {
	$RESULT_ARR = array('result' => 'FAIL', 'message' => '비정상접속');
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}


/////////////////////////////////////////////////
// Watch List Filtering (금일 실행내역 없을 경우에만)
/////////////////////////////////////////////////
if( $member['wlf_dd'] == '' || $member['wlf_dd'] < date('Y-m-d') ) {

	if( $WLF_RES = WLFSend($member['mb_no'], 'WLF 요청자료 전송 - 출금') ) {

		if($WLF_RES['SUCCESS_YN']=='Y') {
			if($WLF_RES['CODE'] == '900') {
				// 정상
			}
			else {
				if($WLF_RES['CODE'] == '200') {
					// 거래불가처리 확정
				}
				else if($WLF_RES['CODE'] == '400') {
					// 거래불가처리
				}
			}
		}
		else {
			// AML 데이터가 부정확하거나 없음.
		}

		$sql = "
			UPDATE
				g5_member
			SET
				wlf_cd   = '".$WLF_RES['CODE']."'
				,wlf_idx = '".$WLF_RES['LOG_IDX']."'
				,wlf_dd  = CURDATE()
			WHERE
				mb_no = '".$member['mb_no']."'";
		if( sql_query($sql) ) {
			//member_edit_log($member['mb_no']);		// 회원정보변경기록
		}

	}

}


///////////////////////////////
// KYC 현황 체크
///////////////////////////////
//if( $office_connect ) {				// if( in_array($member['mb_id'], $kyc_test_member) ) {
	/*
	if( in_array($member['kyc_allow_yn'], array('W','I')) ) {
		$RESULT_ARR = array('result' => 'KYC_ING', 'message' => 'KYC 검토중');
		echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
	}
	else {
		if( $member['kyc_allow_yn']=='N' ) {
			$RESULT_ARR = array('result' => 'KYC_START', 'message' => 'KYC 시작');
			echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
		}
	}
	*/
//}

if($member['bank_code']=='' || $member['account_num']=='') {
	$RESULT_ARR = array('result' => 'FAIL', 'message' => '신한은행 가상계좌 발급 내역이 존재하지 않습니다.');
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}

if($member['va_bank_code2']=='' || $member['virtual_account2']=='') {
	$RESULT_ARR = array('result' => 'FAIL', 'message' => '예치금 환급계좌 미등록 상태입니다.');
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}

// 신한은행 점검시간 진입금지 -------------------------------------------------------------
if( date('Y-m-d H:i:s') >= $CONF['BANK_STOP_SDATE'] && date('Y-m-d H:i:s') < $CONF['BANK_STOP_EDATE'] ) {
	$message = "금융기관 점검시간 입니다.\n점검시간: ".substr($CONF['BANK_STOP_SDATE'],0,16)." ~ ".substr($CONF['BANK_STOP_EDATE'],0,16);
	$RESULT_ARR = array('result' => 'BANK_STOP', 'message' => $message);
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}

// 신한은행 일일정기점검시간 진입금지 -------------------------------------------------------------
if( date('H:i:s') >= $CONF['DAY_BANK_STOP_STIME'] && date('H:i:s') < $CONF['DAY_BANK_STOP_ETIME'] ) {
	$message = "금융기관 정기점검시간 입니다.\n점검시간: ".substr($CONF['DAY_BANK_STOP_STIME'],0,5)." ~ ".substr($CONF['DAY_BANK_STOP_ETIME'],0,5);
	$RESULT_ARR = array('result' => 'BANK_STOP', 'message' => $message);
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}


// 잔액체크
$sum_point = get_point_sum($member['mb_id']);

if($req_price) {

	if($req_price > $sum_point) {

		if($sum_point > 0) {
			$RESULT_ARR = array('result' => 'FAIL', 'message' => '보유 예치금보다 많은 금액을 청구하였습니다.');
			echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
		}
		else {
			$RESULT_ARR = array('result' => 'FAIL', 'message' => '보유 예치금이 없습니다.');
			echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
		}

	}
	else {

		// [2021-05-18 추가] 처리중인 전체 출금요청 카운팅 - 10분내 출금요청처리결과수신대기중인 데이터가 전체 20개 이상일 경우 출금 요청 차단
		$WAIT_ALL = sql_fetch("
			SELECT
				COUNT(idx) AS cnt
			FROM
				IB_request_log
			WHERE 1
				AND regdate >= DATE_ADD(NOW(), INTERVAL -10 MINUTE)
				AND request_code = '3200'
				AND rcode = ''
				AND edate IS NULL");
		if($WAIT_ALL['cnt'] > 20) {
			$RESULT_ARR = array('result' => 'FAIL', 'message' => '현재 동시출금처리건이 많습니다.\n잠시 후 다시 요청하십시요.');
			echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
		}
		// [2021-05-18 추가] 처리중인 전체 출금요청 카운팅

		//echo "출금요청처리결과수신대기중건 :" . $WAIT_ALL['cnt'] . "\n";

		// [2021-05-18 쿼리함수 변경] 처리중인 본인 출금요청 조회
		$WAIT_ME = sql_fetch("
			SELECT
				COUNT(idx) AS cnt
			FROM
				IB_request_log
			WHERE 1
				AND mb_id = '".$member['mb_id']."'
				AND request_code = '3200'
				AND rcode = ''
				AND regdate >= DATE_ADD(NOW(), INTERVAL -10 MINUTE)
				AND edate IS NULL");
		if($WAIT_ME['cnt'] > 0) {
			$RESULT_ARR = array('result' => 'FAIL', 'message' => '기관측 출금결과를 대기중인 요청이 있음.');
			echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
		}
		// [2021-05-18 쿼리함수 변경] 처리중인 본인 출금요청 조회

		//echo "처리중인 본인 출금요청건 :" . $WAIT_ME['cnt'] . "\n";


		/*
		///////////////////////////////////////
		// 통신상태 사전 점검
		///////////////////////////////////////
		$RESULT = insidebank_request('000');
		if(!$RESULT || $RESULT['RCODE']!='00000000' || $RESULT['ERRMSG']=='ERROR:EMPTY_DATA') {
			echo "BANK_COMMUNICATION_ERROR"; exit;
		}
		*/

		$CUST_ID = $member['mb_no'];								// 투자자고객ID

		///////////////////////////////////////
		// 유효잔액 조회(4100)
		///////////////////////////////////////
		$ARR['REQ_NUM'] = "041";
		$ARR['CUST_ID'] = $CUST_ID;
		$RETURN_ARR = insidebank_request('256', $ARR);

		if($RETURN_ARR['RCODE']=='00000000') {

			if($RETURN_ARR['BALANCE_AMT']=='0') {
				$RESULT_ARR = array('result' => 'FAIL', 'message' => '보유 예치금이 없습니다.');
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
			}
			if($RETURN_ARR['WITH_AMT']=='0') {
				$RESULT_ARR = array('result' => 'FAIL', 'message' => '현재 출금 가능금액이 없습니다.');
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
			}

			//★★★★★ 투자금액(기표전) 추출 ★★★★★//
			$LOCK = sql_fetch("
				SELECT
					IFNULL(SUM(A.amount),0) AS amount
				FROM
					cf_product_invest A
				LEFT JOIN
					cf_product B  ON A.product_idx=B.idx
				WHERE 1
					AND A.member_idx='".$member['mb_no']."'
					AND A.invest_state='Y'
					AND B.state=''
					AND B.display='Y'");

			$avail_amount = $RETURN_ARR['BALANCE_AMT'] - $LOCK['amount'];		// 유효출금가능금액 체크

			if($req_price > $avail_amount || $req_price > $RETURN_ARR['WITH_AMT'])	{
				$RESULT_ARR = array('result' => 'FAIL', 'message' => '출금 가능 예치금보다 많은 금액이 신청되었습니다.\n출금 가능한 금액은 ' . number_format($avail_amount) . '원 입니다.');
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
			}

		}
		else {

			if($RETURN_ARR['RCODE']=='') {
				$RESULT_ARR = array('result' => 'FAIL', 'message' => '금융 전산망과 통신이 지연되고 있습니다.\n잠시 후 다시 시도하십시요.');
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
			}
			else {
				$RESULT_ARR = array('result' => 'FAIL', 'message' => $RETURN_ARR['ERRMSG']);
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
			}

		}

		$ARR = $RETURN_ARR = NULL;


		///////////////////////////////////////
		// 인사이드뱅크 출금요청 시작
		///////////////////////////////////////
		$thisRndNo = sprintf("%04d", rand(0,9999));

		$REQ_NUM         = '032';														// 전문번호(출금: 032)
		$TRAN_BANK_CD    = $member['bank_code'];						// 이체은행코드(출금신청한 예치금을 입금받을 은행코드)
		$TRAN_ACCT_NB    = preg_replace("/-| /", "", $member['account_num']);					// 이체계좌번호(출금신청한 예치금을 입금받을 계좌번호)

		// 2021-11-24 오현석 회원(회원번호 : 25063)의 예치금 166,307원을 상속자 김선화의 계좌(국민은행, 666225-96-103394)로 이체 처리
		if($member['mb_id']=='ailzoh') {
			$TRAN_BANK_CD = '004';
			$TRAN_ACCT_NB = preg_replace("/-| /", "", "666225-96-103394");
		}


		$TRAN_REMITEE_NM = '헬로(' . $thisRndNo . ')';			// 이체계좌 성명이라고 하지만 이체계좌표기명 인것 같음. (아무명으로 대입해도 정상처리 되었으며, 이체처리 후 이체계좌에 보낸사람 이름으로 표기됨)
																												// - (동일계좌 동일금액의 연속된 이체요청은 받아들여지지 않아 예금주명 뒤에 숫자를 다르게 붙여준다. - 신한은행 이승우주임 김진부과장 guide)

		$TRAN_AMT        = $req_price;											// 이체금액
		$TRAN_MEMO       = '헬로펀딩';											// 이체계좌통장메모 (입금 계좌에 찍히는 내용 : 한글로 7글자. 인사이드뱅크는 한글을 2Byte로 취급한다!!!)
		$GUAR_MEMO       = '출금(' . $thisRndNo . ')';			// 지급계좌통장메모 (예치금모계좌)
		if( strlen($GUAR_MEMO) > 18 ) { $GUAR_MEMO = substr($GUAR_MEMO, 0, 18); }		// UTF8 한글 1자 = 3byte
		$FUND_KIND       = '10';														// 자금성격(10:예치금)

		$ARR['REQ_NUM']         = $REQ_NUM;
		$ARR['CUST_ID']         = $CUST_ID;
		$ARR['TRAN_BANK_CD']    = $TRAN_BANK_CD;
		$ARR['TRAN_ACCT_NB']    = $TRAN_ACCT_NB;
		$ARR['TRAN_REMITEE_NM'] = $TRAN_REMITEE_NM;
		$ARR['TRAN_AMT']        = $TRAN_AMT;
		$ARR['TRAN_MEMO']       = $TRAN_MEMO;
		$ARR['GUAR_MEMO']       = $GUAR_MEMO;
		$ARR['FUND_KIND']       = $FUND_KIND;
		//print_rr($ARR); exit;

		$RETURN_ARR = insidebank_request('256', $ARR);  // 인사이드뱅크 예치금 출금신청(전문번호: 3200) 발송

		$resultOK = false;

		if($RETURN_ARR['RCODE']=='00000000') {

			$resultOK = true;
			$GUAR_SEQ = $RETURN_ARR['GUAR_SEQ']; // 예치금 모계좌 지급거래번호

		}
		else if($RETURN_ARR['RCODE']=='AGE00301') {

				$RESULT_ARR = array('result' => 'FAIL', 'message' => '최종 입금내역 발생기준 24시간 경과 후 부터 출금이 가능합니다.');
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;

		}
		else if( $RETURN_ARR['RCODE']=='IS0102') {		// 인사이드뱅크-KSNET간 통신지연코드를 받은 경우

			// IS0102 발생시 콜백함수로는 정확히 출금 처리에 관한 정확한 경과까지는 알 수 없다고 함 (인사이드뱅크 측 답변)
			// 금융기관쪽으로 전문번호로 기관에 처리결과여부를 의뢰해야한다.

			$LAST_ORDER = sql_fetch("
				SELECT
					idx, request_arr, regdate
				FROM
					IB_request_log
				WHERE 1
					AND mb_id='".$member['mb_id']."'
					AND request_code='3200' AND rcode='IS0102'
				ORDER BY
					idx DESC LIMIT 1");

			if( $LAST_ORDER['idx'] ) {

				//$REQUEST_ARR = explode("&", $LAST_ORDER['request_arr']);
				//$last_fbseq = preg_replace("/FB_SEQ=/", "", $REQUEST_ARR[0]);

				$last_fbseq = str_f6($LAST_ORDER['request_arr'], "FB_SEQ=", "&");

				if($last_fbseq) {
					// 결번요청(8400)  -> 전문 실행 결과값 재전송받기
					$ARR2['SUBMIT_GBN'] = "04";						//전문번호
					$ARR2['TRAN_DATE']  = preg_replace("/-/", "", substr($LAST_ORDER['regdate'],0,10));
					$ARR2['ORI_FB_SEQ'] = $last_fbseq;

					$RETURN_ARR2 = insidebank_request("000", $ARR2);
					if( $RETURN_ARR2['ORI_FB_REQCODE']=='00000000' ) {
						sql_query("UPDATE IB_request_log SET rcode='00000000' WHERE idx='".$LAST_ORDER['idx']."'");
						$resultOK = true;
						$GUAR_SEQ = $last_fbseq;
					}
					else {
						$RESULT_ARR = array("result" => "FAIL", "message" => "금융기관으로의 전송이 정상적으로 실행되지 않았습니다.\n출금 요청을 다시 실행 하여주십시요.");
						echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
					}
				}

			}

		}
		else {

			$RESULT_ARR = array('result' => 'FAIL', 'message' => $RETURN_ARR['ERRMSG']);		// 금융기관 오류메세지
			echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;

		}



		if( $resultOK ) {
			$sql = "
				INSERT INTO
					g5_withdrawal
				SET
					state     = '2',
					mb_no     = '".$member['mb_no']."',
					mb_id     = '".$mb_id."',
					req_price = '".$req_price."',
					regdate   = NOW(),
					GUAR_SEQ  = '".$GUAR_SEQ."'";
			if(sql_query($sql)) {

				$mb_id   = $mb_id;
				$point   = (int)$req_price * -1;
				$content = ($member['va_bank_code2'] && $member['virtual_account2']) ? '예치금 출금' : '예치금 출금 대기';		// 신한가상계좌 미발급 회원의 출금요청은 예치금 출금 대기 처리하여 관리자가 입금처리하도록 한다.

				//예치금 출금: 회원포인트(예치금) 차감
				if( insert_point($mb_id, $point, $content, '@withdrawal', $member['mb_id'], $member['mb_id'].'-'.uniqid(''), 0) ) {
					/*카카오톡 알림톡 추가*/
					$tcode = "hello004";
					$member["mb_point"] = $member["mb_point"] + $point;

					$KaKao_Message_Send = new KaKao_Message_Send();
					$KaKao_Message_Send->WIDTHDRW_MONEY = $req_price;
					$KaKao_Message_Send->MEMBER = $member;	// common.lib member 환경변수
					$KaKao_Message_Send->kakao_insert($tcode);
					/*카카오톡 알림톡 추가*/
				}

				$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
				echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;

			}
		}

	}

}
else {
	$RESULT_ARR = array('result' => 'FAIL', 'message' => '출금액을 입력하여 주십시요.');
	echo json_encode($RESULT_ARR, JSON_UNESCAPED_UNICODE); sql_close(); exit;
}



@exit;

?>