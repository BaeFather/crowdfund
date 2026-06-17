<?
################################################################################
## 신한 인사이드뱅크 통신용 함수
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
################################################################################

// 신한 인사이드뱅크 설정
if(!is_array($SHISDBK)) {
	$SHISDBK['target_host']       = "10.10.11.11";			// 실서버	(내부VPN 통신IP *외부공인IP : 222.231.31.120)
//$SHISDBK['target_host']       = "222.231.31.34";		//테스트서버
	$SHISDBK['000']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5000";  //TESTCALL
	$SHISDBK['128']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5001";
	$SHISDBK['128']['enc_key']    = "ECgYB1tH7pFPbDvT";
	$SHISDBK['256']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5002";
	$SHISDBK['256']['enc_key']    = "esYax1AADKlC7KmTjhdcd6itjLQ+2cyU";

	// 인사이드뱅크 서버로 URL호출을 하여 데이터 송신을 할 때 사용 URL
	$SHISDBK['001']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5010";
}


/////////////////////////////////////////////
// 파싱 함수
/////////////////////////////////////////////
function XML2Array($xmlObj) {
	$array = $xmlObj;

	$newArray = array();
	$array = (array)$array ;
	foreach ($array as $key => $value) {
		$value = (array)$value;
		if (isset($value[0])) {
			$newArray[$key] = trim($value[0]);
		}
		else {
			$newArray[$key] = XML2Array($value, true) ;
		}
	}
	return $newArray ;
}

/////////////////////////////////////////////
// 전송 및 리턴함수 (결과는 배열로...)
/////////////////////////////////////////////
function insidebank_request($enc_bit='000', $data_arr='', $mode='') {
	global $g5;
	global $link;
	global $_SESSION;
	global $SHISDBK;
	global $_REQUEST;

	//if($mode=='debug') print_r($data_arr);

	//거래고유번호 생성 (IB_make_fbseq 테이블을 이용)
	sql_query("INSERT INTO IB_make_fbseq SET rdate=NOW()");
	$seq = sql_insert_id($link);
	$fb_seq = 'HEL' . sprintf("%07d", $seq);
	sql_query("DELETE FROM IB_make_fbseq WHERE LEFT(rdate,10)='".date('Y-m-d', strtotime('-1 day'))."'");		// 오늘것만 저장

	/*
	//거래고유번호 생성
	$LOG = sql_fetch("SELECT MAX(idx) AS max_idx FROM IB_request_log");
	$next_idx = $LOG['max_idx'] + 1;
	if($next_idx <= 9999999) {
		$fb_seq = 'HEL' . sprintf("%07d", $next_idx);
	}
	else {
		$ARR_ALNUM = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','K','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		shuffle($ARR_ALNUM);
		$fb_seq = 'HEL';
		for($i=0; $i<7; $i++) {
			$fb_seq.= $ARR_ALNUM[$i];
		}
	}
	*/

	if($enc_bit=='001') {
		// 지급 요청시 $data_arr은 지급회차(01, 02, 03)가 들어온다.
		$reg_seq = $data_arr;
		$data = "REQ_SEQ=" . $reg_seq;
	}
	else {
		$data = "FB_SEQ=".$fb_seq;
		while( list($key, $value) = @each($data_arr) ) {
			$data.= '&'.$key.'='.@trim($value);

			if($key=='REQ_NUM')    $tmp_req_num    = trim($value);
			if($key=='SUBMIT_GBN') $tmp_submit_gbn = trim($value);

		}
	}

	//요청 자료중 로그등록용 데이터 추출
	if($tmp_req_num=='010') {
		switch($tmp_submit_gbn) {
			case '01' :	$REQ = array('request_code'=>'1100', 'request_summary'=>'고객정보등록');    break;
			case '02' :	$REQ = array('request_code'=>'1200', 'request_summary'=>'고객정보변경');    break;
			case '03' :	$REQ = array('request_code'=>'1300', 'request_summary'=>'고객정보해지');    break;
			case '04' :	$REQ = array('request_code'=>'1400', 'request_summary'=>'고객정보조회');    break;
		}
	}
	else if($tmp_req_num=='020') {
		switch($tmp_submit_gbn) {
			case '01' :	$REQ = array('request_code'=>'2100', 'request_summary'=>'대출등록');         break;
			case '02' :	$REQ = array('request_code'=>'2200', 'request_summary'=>'대출투자자등록');   break;
			case '03' :	$REQ = array('request_code'=>'2300', 'request_summary'=>'대출실행');         break;
			case '04' :	$REQ = array('request_code'=>'2400', 'request_summary'=>'대출취소');         break;
			case '05' :	$REQ = array('request_code'=>'2500', 'request_summary'=>'대출정보변경');     break;
			case '06' :	$REQ = array('request_code'=>'2600', 'request_summary'=>'투자자정보변경');   break;
			case '07' :	$REQ = array('request_code'=>'2600', 'request_summary'=>'투자자취소');       break;
			case '08' :	$REQ = array('request_code'=>'2700', 'request_summary'=>'대출상환완료');     break;
		}
	}
	else if($tmp_req_num=='032') $REQ = array('request_code'=>'3200', 'request_summary'=>'예치금출금');
	else if($tmp_req_num=='040') $REQ = array('request_code'=>'4000', 'request_summary'=>'수취인조회');
	else if($tmp_req_num=='041') $REQ = array('request_code'=>'4100', 'request_summary'=>'고객정보조회(예치금)');
	else if($tmp_req_num=='044') $REQ = array('request_code'=>'4400', 'request_summary'=>'집계조회');



	$host = $SHISDBK['target_host'];
	if( trim($data) ) {
		if($enc_bit=='128') {
			$path = '/IFX5001';
			$enc_key = $SHISDBK[$enc_bit]['enc_key'];
			$encode_str = aes128Encrypt($enc_key, $data);
			$decode_str = aes128Decrypt($enc_key, $encode_str);
		}
		else if($enc_bit=='256') {
			$path = '/IFX5002';
			$enc_key = $SHISDBK[$enc_bit]['enc_key'];
			$encode_str = aes256Encrypt($enc_key, $data);
			$decode_str = aes256Decrypt($enc_key, $encode_str);
		}
		else {
			if($enc_bit=='001') {
				$path = '/IFX5010';
				$REQ = array('request_code'=>'B2500', 'request_summary'=>'원리금지급요청');
			}
			else {
				$path = '/IFX5000';
				if($tmp_submit_gbn=='04') {
					$REQ = array('request_code'=>'8400', 'request_summary'=>'결번조회요청');
				}
				else {
					$REQ = array('request_code'=>'8900', 'request_summary'=>'TESTCALL');
				}
			}
			$encode_str = $data;
			$decode_str = $encode_str;
		}
	}


	if($mode=='debug') {
		echo "원본: ". $data . "\n";
		echo "인코딩: ". $encode_str . "\n";
		echo "디코딩: ". $decode_str . "\n\n";
	}

	if($_SESSION['ss_mb_id']) {
		$mb_id = $_SESSION['ss_mb_id'];
	}
	else {
		if(@$data_arr['CUST_ID']) {
			$TMP = sql_fetch("SELECT mb_id FROM g5_member WHERE mb_no='".$data_arr['CUST_ID']."'");
			$mb_id = $TMP['mb_id'];
		}
	}

	// 실행로그 :: 기록 시작
	$log_sql = "
		INSERT INTO
			IB_request_log
		SET
			request_arr     = '".($data)."',
			request_code    = '".$REQ['request_code']."',
			request_summary = '".sql_real_escape_string($REQ['request_summary'])."',
			mb_id           = '".$mb_id."',
			exec_path       = '".sql_real_escape_string($_SERVER['PHP_SELF'])."',
			referer         = '".sql_real_escape_string($_SERVER['HTTP_REFERER'])."',
			regdate         = NOW()";
	$log_res = sql_query($log_sql);
	$log_idx = sql_insert_id();

	$sock_timeout = ($tmp_req_num=='032') ? 30 : 15;		// 예치금출금시 타임아웃 30초 그 외 15초

	$fp = @fsockopen($host, 80, $errno, $errstr, $sock_timeout);
//$fp = @fsockopen($host, 80, $errno, $errstr, 30);		    // open a socket connection on port 80 - timeout: 15sec
	if($fp) {
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($encode_str) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $encode_str);
		$result = '';
		while(!feof($fp)) {
			$result.= fgets($fp);
		}
	}
	else {
		$RETURN_ARR['ERRMSG'] = 'ERROR: ' . $errstr . " (" . $errno . ")";		// connection error
	}
	@fclose($fp);

	if($result) {
		$result  = explode("\r\n\r\n", $result, 2);
		$header  = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';

		if($mode=='debug') {
			print_r( array('status' => 'ok', 'header' => $header, 'content' => $content) );
		}

		if(preg_match("/HTTP\/1\.1 200 OK/", $header)) {

			$obj = @simplexml_load_string($content);

			if($obj === false) {
				$RETURN_ARR['ERRMSG'] = "ERROR: FAILED LOADING XML";
			}
			else {
				$ARRAY = XML2Array($obj);		// 오브젝트로 받은 데이터를 배열로 전환
				foreach($ARRAY as $k=>$v) {
					$RETURN_ARR[$k] = $v['@attributes']['value'];
				}


				$RETURN_ARR['FB_SEQ'] = $fb_seq;

				if((string)$RETURN_ARR['RCODE']=='00000000') {
					//
				}
				else {
					// 에러메세지가 전달되지 않았을 경우 코드에 따른 대체
					if(!$RETURN_ARR['ERRMSG']) {
						switch($RETURN_ARR['RCODE']) {
							case "AGE00010" :	$RETURN_ARR['ERRMSG'] = "ERROR:전문오류 (FORMAT ERROR)"; break;
							case "AGE00011" :	$RETURN_ARR['ERRMSG'] = "ERROR:서비스 불가"; break;
							case "AGE00012" :	$RETURN_ARR['ERRMSG'] = "ERROR:해당 이용기관 정보 없음"; break;
							case "AGE00013" :	$RETURN_ARR['ERRMSG'] = "ERROR:DB처리오류"; break;
							case "AGE00014" :	$RETURN_ARR['ERRMSG'] = "ERROR:통신장애"; break;
							case "AGE00015" :	$RETURN_ARR['ERRMSG'] = "ERROR:응답코드오류"; break;
							case "AGE00016" :	$RETURN_ARR['ERRMSG'] = "ERROR:시스템오류"; break;
							case "AGE00017" :	$RETURN_ARR['ERRMSG'] = "ERROR:시스템오류(공통모듈)"; break;
							case "AGE00018" :	$RETURN_ARR['ERRMSG'] = "ERROR:시스템오류(날짜,시간)"; break;
							case "AGE00100" :	$RETURN_ARR['ERRMSG'] = "ERROR:계좌오류"; break;
							case "AGE00101" :	$RETURN_ARR['ERRMSG'] = "ERROR:잔액부족"; break;
							case "AGE00102" :	$RETURN_ARR['ERRMSG'] = "ERROR:원거래없음"; break;
							case "AGE00103" :	$RETURN_ARR['ERRMSG'] = "ERROR:기 처리 오류(이미 처리완료)"; break;
							case "AGE00104" :	$RETURN_ARR['ERRMSG'] = "ERROR:거래 금액오류"; break;
							case "AGE00105" :	$RETURN_ARR['ERRMSG'] = "ERROR:원장/거래내역 미존재"; break;
							case "AGE00107" :	$RETURN_ARR['ERRMSG'] = "ERROR:거래정보오류"; break;
							case "AGE00200" :	$RETURN_ARR['ERRMSG'] = "ERROR:입력값오류(내부)"; break;
							case "AGE00201" :	$RETURN_ARR['ERRMSG'] = "ERROR:입력값오류(외부)"; break;
							case "AGE09999" :	$RETURN_ARR['ERRMSG'] = "ERROR:기타오류"; break;
							default : "ERROR:UNKNOWN"; break;
						}
					}
					else {
						// 메세지가 애매모호 할 경우 메세지 추가
						if($RETURN_ARR['RCODE'] == "AGE00301") $RETURN_ARR['ERRMSG'] = "ERROR:출금지연제한 (" . $RETURN_ARR['ERRMSG'] . ")";
					}

				}

			}

		}
		else {
			$RETURN_ARR['ERRMSG'] = 'ERROR:RESULT_HEADER';
		}

	}
	else {
		$RETURN_ARR['ERRMSG'] = 'ERROR:EMPTY_DATA';
	}


	// 실행로그 :: 결과 기록
	$log_sql2 = "
		UPDATE
			IB_request_log
		SET
			rcode = '".$RETURN_ARR['RCODE']."',
			msg   = '".$RETURN_ARR['ERRMSG']."',
			edate = NOW()
		WHERE
			idx = '$log_idx'";
	sql_query($log_sql2);


	//$limit_date = date("Y-m-d H:i:s", strtotime("-10day"));
	//sql_query("DELETE FROM IB_request_log where regdate < '$limit_date'");


	if($RETURN_ARR)	{
		return $RETURN_ARR;
	}
	else {
		$RETURN_ARR['ERRMSG'] = 'ERROR:EMPTY_RESULT';
		return $RETURN_ARR;
	}

}


////////////////////////////////////
// 신한 가상계좌 정보등록
////////////////////////////////////
function sh_make_account($mb_no) {
	global $g5;
	global $link;
	//global $link2;

	$sqlx = "
		SELECT
			mb_no, mb_id, mb_name, mb_co_name, mb_co_reg_num, mb_co_owner, mb_hp, mb_hp_ineb,
			member_type, is_creditor, bank_name, bank_code, bank_private_name, bank_private_name_sub,
			account_num, account_num_ineb, va_bank_code2, virtual_account2, va_private_name2
		FROM
			g5_member
		WHERE
			mb_no = '".$mb_no."'";
	$MB = sql_fetch($sqlx);

	//$MB['mb_hp']       = @preg_replace("/(-| )/", "", masterDecrypt($MB['mb_hp'], false));
	//$MB['account_num'] = @preg_replace("/(-| )/", "", masterDecrypt($MB['account_num'], false));

	if($MB['mb_hp'] || $MB['mb_hp_ineb']) {
		$MB['mb_hp'] = ($MB['mb_hp_ineb']) ? DGuardDecrypt($MB['mb_hp_ineb']) : masterDecrypt($MB['mb_hp'], false);
		$MB['mb_hp'] = @preg_replace("/(-| )/", "", $MB['mb_hp']);
	}

	if($MB['account_num'] || $MB['account_num_ineb']) {
		$MB['account_num'] = ($MB['account_num_ineb']) ? DGuardDecrypt($MB['account_num_ineb']) : masterDecrypt($MB['account_num'], false);
		$MB['account_num'] = @preg_replace("/(-| )/", "", $MB['account_num']);
	}

	$MB['mb_co_name']    = trim($MB['mb_co_name']);
	$MB['mb_co_owner']   = trim($MB['mb_co_owner']);
	$MB['mb_co_reg_num'] = @preg_replace("/(-| )/", "", $MB['mb_co_reg_num']);

	$CUST_ID = $MB['mb_no'];	// 고객아이디 = 자사회원고유번호

	$SUBMIT_GBN = ($MB['va_bank_code2']=='088' && $MB['virtual_account2']) ? '02' : '01';	//거래구분 (01:등록 / 02:수정)

	if($mb_no=='6504') $SUBMIT_GBN = '02';

	if($MB['mb_id']=='KJHInvest1019') {
		$CORP_NAME_CUSTOM = "(주) 케이제이에이치인베";
	}

	// 사업자번호 및 개인사업자구분
	if($MB['member_type']=='2') {		//법인 사업자 설정

		$CUST_NM = ($CORP_NAME_CUSTOM) ? $CORP_NAME_CUSTOM : $MB['mb_co_name'];
		$CUST_NM = preg_replace("/ /","", trim($CUST_NM));

		if( preg_match("/주식회사/", $CUST_NM) ) {
			$CUST_NM = preg_replace("/주식회사/","", $CUST_NM);
			$CUST_NM = "(주)".$CUST_NM;
		}
		else if( preg_match("/(유한회사|유한책임회사)/", $CUST_NM) ) {
			$CUST_NM = preg_replace("/(유한회사|유한책임회사)/","", $CUST_NM);
			$CUST_NM = "(유)".$CUST_NM;
		}

		if(strlen($CUST_NM) > 30) $CUST_NM = mb_substr($CUST_NM, 0, 10);			// (30Byte제한)10자리로 고정함

		$CUST_SUB_NM = $CUST_NM;
		$REP_NM      = $MB['mb_co_owner'];
		$SUP_REG_NB  = $MB['mb_co_reg_num'];
		$PRI_SUP_GBN = '2';
	}
	else {
		$CUST_NM = $MB['mb_name'];
		if($MB['is_creditor']=='Y' && $MB['mb_co_reg_num']) {		//개인사업자 설정
			$CUST_SUB_NM = $MB['mb_name'];
			$SUP_REG_NB  = $MB['mb_co_reg_num'];
			$PRI_SUP_GBN = '2';
		}
		else {		// 일반 개인 설정
			$CUST_SUB_NM = "";
			$SUP_REG_NB  = '';
			$PRI_SUP_GBN = '1';
		}

		// 주민번호 가져옴 (본 함수 사용전에 정상적인 데이터 유무를 확인할 필요 있음)
		//$row   = sql_fetch("SELECT * FROM member_private WHERE mb_no='$mb_no' ORDER BY idx DESC LIMIT 1", '', $link2);
		//$jumin = decrypt($row['regist_number'], 'jumin');
		$jumin = getJumin($mb_no);
		$jumin = substr($jumin, 0, 6);
		$BIRTH_DATE = (substr($jumin, 6, 1) > 2) ? '20'.$jumin : '19'.$jumin;	//생년월일

	}

	$KSNET_CORP_NAME = ($CORP_NAME_CUSTOM) ? $CORP_NAME_CUSTOM : $CUST_NM."(헬로펀딩)";		// KSNET용 업체명 (고객입금시 보여지는 예금주명)

	$HP_NO1 = substr($MB['mb_hp'], 0, 3);
	$HP_NO2 = substr($MB['mb_hp'], 3, -4);
	$HP_NO3 = substr($MB['mb_hp'], -4);

	$VA_BANK_CODE = '088';

	$ACCT_NB = $MB['account_num'];

	// 유휴가상계좌정보 할당
	$VACT = sql_fetch("SELECT acct_no FROM IB_vact WHERE acct_st=0 ORDER BY acct_no ASC LIMIT 1");
	if($VACT) {

		// 고객정보등록(1100) 전문 발송
		$ARR['REQ_NUM']     = "010";								//전문번호
		$ARR['SUBMIT_GBN']  = $SUBMIT_GBN;					//거래구분 (01:등록|02:변경)
		$ARR['CUST_ID']     = $CUST_ID;							//고객ID
		$ARR['CUST_NM']     = $CUST_NM;							//고객명 (법인사업자는 사업자명)
		$ARR['CUST_SUB_NM'] = $CUST_SUB_NM;					//고객부기명
		$ARR['REP_NM']      = $REP_NM;							//대표자고객명
		$ARR['BIRTH_DATE']  = $BIRTH_DATE;					//생년월일자 YYYYMMDD
		$ARR['SUP_REG_NB']  = $SUP_REG_NB;					//사업자번호
		$ARR['PRI_SUP_GBN'] = $PRI_SUP_GBN;					//개인사업자구분
		$ARR['HP_NO1']      = $HP_NO1;							//휴대폰지역번호
		$ARR['HP_NO2']      = $HP_NO2;							//휴대폰국번호
		$ARR['HP_NO3']      = $HP_NO3;							//휴대폰일련번호
		$ARR['BANK_CD']     = $MB['bank_code'];			//은행코드
		$ARR['ACCT_NB']     = $ACCT_NB;							//은행계좌
		$ARR['CMS_NB']      = $VACT['acct_no'];			//가상계좌번호

		//print_rr($ARR); exit;

		$RETURN_ARR = insidebank_request('256', $ARR);
		if($RETURN_ARR['RCODE']=='00000000') {

			// 본사 가상계좌원장 할당정보 기록
			$sql = "
				UPDATE
					IB_vact
				SET
					FB_SEQ  = '".$RETURN_ARR['FB_SEQ']."',
					CUST_ID = '".$CUST_ID."',
					cmf_nm  = '".$CUST_NM."',
					acct_st = '1',
					open_il = '".date('Ymd')."'
				WHERE 1
					AND acct_no = '".$VACT['acct_no']."'";
			$res = sql_query($sql);

			// 본사 기존 가상계좌원장 할당정보 수정 (해지)
			$sql = "
				UPDATE
					IB_vact
				SET
					acct_st = '9',
					close_il = '".date('Ymd')."'
				WHERE 1
					AND CUST_ID = '".$CUST_ID."'
					AND acct_no = '".$MB['virtual_account2']."'";
			$res = sql_query($sql);

			// KSNET 가상계좌원장 할당정보 기록
			$sql = "
				INSERT INTO
					KSNET_VR_ACCOUNT
				SET
					BANK_CODE  = '".$VA_BANK_CODE."',
					VR_ACCT_NO = '".$VACT['acct_no']."',
					CORP_NAME  = '".$KSNET_CORP_NAME."',
					USE_FLAG   = 'Y'";
			$res = sql_query($sql);

			// KSNET 기존 가상계좌원장 할당정보 수정 (해지)
			$sql = "
				UPDATE
					KSNET_VR_ACCOUNT
				SET
					FINAL_DATE = '".date('Ymd')."',
					USE_FLAG = 'N'
				WHERE 1
					AND VR_ACCT_NO = '".$MB['virtual_account2']."'";
			$res = sql_query($sql);

			// 회원정보 테이블에 기록 (KSNET 가상계좌원장 정보에 기록된 이름으로 등록)
			$sql = "
				UPDATE
					g5_member
				SET
					va_bank_code2 = '".$VA_BANK_CODE."',
					virtual_account2 = '".$VACT['acct_no']."',
					va_private_name2 = '".$KSNET_CORP_NAME."'
				WHERE 1
					AND mb_no = '".$mb_no."'";
			$res = sql_query($sql);

			if( sql_affected_rows() ) member_edit_log($mb_no);	// ▶▶▶▶ 회원변경로그 기록

		}
		else {
			// 계좌할당승인 실패시 해당 계좌 롹킹 처리
			$sql = "
				UPDATE
					IB_vact
				SET
					acct_st = '9',
					open_il = '".date('Ymd')."',
					close_il = '".date('Ymd')."'
				WHERE 1
					AND acct_no = '".$VACT['acct_no']."'";
			$res = sql_query($sql);
		}

	}
	else {
		$RETURN_ARR['ERRMSG'] = "ERROR:SH_VA_INSUFFICIENCY"; //계좌여유부족
	}

	return $RETURN_ARR;

}


////////////////////////////////////
// 원리금 지급요청 (안씀)
////////////////////////////////////
function ib_repay_request($prd_idx, $repay_amount, $repay_turn) {

	global $g5;
	global $link;
	global $_SESSION;
	global $SHISDBK;
	global $_REQUEST;

	// 해당 상품건의 원리금수납가상계좌번호 가져오기
	$PRDT = sql_fetch("SELECT repay_acct_no, loan_usefee, invest_usefee FROM cf_product WHERE idx='".$prd_idx."'");		//loan_usefee 대출자수수료율 invest_usefee 투자자수수료율

	$TR_AMT_GBN = '20';		//자금성격(10:예치금 20:상환금)

	// 대출자로부터 입금된 원리금 존재 유무 확인 (대출상품정보 판별은 가상계좌번호로 함)
	$sql = "
		SELECT
			SR_DATE, FB_SEQ, CUST_ID, BANK_ID, ACCT_NB, TR_AMT, REMITTER_NM,
			COUNT(FB_SEQ) AS cmt,
			SUM(TR_AMT) AS sum_tr_amt
		FROM
			IB_FB_P2P_IP
		WHERE 1
			AND ACCT_NB='".$PRDT['repay_acct_no']."'
			AND TR_AMT_GBN='".$TR_AMT_GBN."'
		ORDER BY
			ERP_TRANS_DT
		LIMIT 1";
	$res = sql_query($sql);


	$sql = "
		INSERT INTO
			IB_FP_P2P_REPAY_REQ
		SET
			SDATE = '',
			REG_SEQ = '',
			PARTNER_CD = '',
			STIME = '',
			TOTAL_CNT = '',
			TOTAL_TR_AMT = '',
			TOTAL_TR_AMT_P = '',
			TOTAL_CTAX_AMT = '',
			TOTAL_FEE = '',
			TRAN_DATE = '',
			TRAN_TIME = '',`
			TOTAL_S_CNT = '',
			TOTAL_E_CNT = '',
			RESP_CODE = '',
			RESP_MSG = '',
			EXEC_STATUS = ''";
	$res = sql_query($sql);

/*
	for($i=0; $i<count(투자자); $++) {
		INSERT INTO
			IB_FB_P2P_REPAY_REQ_DETAIL
		SET
			SDATE = '',
			REG_SEQ = '',
			SEQ = '',
			PARTNER_CD = '',
			DC_NB = '',
			CUST_ID = '',
			TR_AMT = '',
			TR_AMT_P = '',
			CTAX_AMT = '',
			FEE = '',
			REPAY_RECEIPT_NB = '',
			JI_DATE = '',
			JI_TIME = '',
			RESP_CODE = ''";
	}
*/

	// 인사이드뱅크로 '데이터 송신요청 전문' 발송
	$ARR['REQ_SEQ'] = '01';
	insidebank_request('001', $ARR);

}

?>