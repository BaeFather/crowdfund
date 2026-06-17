<?
###############################################################################
## 유스비 API 활용 함수
## 가용 API : 1원인증 / OCR / 마스킹 / 신분증진위여부
###############################################################################

///////////////////////////////////////////////////////////////////////////////
// 기본 통신 함수
///////////////////////////////////////////////////////////////////////////////
function usebCurl($apiGubun, $data=array(), $detailUrl, $title, $method, $token='',$file_mime_type='')
{
	global $g5;
	global $CONF;
	global $USEB;
	global $member;

	$ret = array();

	$header_string = "Content-Type: application/json";
//$header_string.= ($file_mime_type) ? ", {$file_mime_type}" : "";
	$header_string.= ";charset=utf-8;";

	$headers[] = $header_string;
	if($token) $headers[] = 'Authorization: ' . $token;


	$stimeStamp = time();

	// API 구분에 따른 요청도메인 ::: OCR만 계약되어있음.
	switch($apiGubun) {
		case 'OAUTH'   : $USEB['api_domain'] = "https://auth.useb.co.kr";				break;
		case 'KEYS'    : $USEB['api_domain'] = "https://auth.useb.co.kr";				break;
		case '1WON'    : $USEB['api_domain'] = "https://openapi.useb.co.kr";		break;
		case 'OCR'     : $USEB['api_domain'] = "https://api3.useb.co.kr";				break;
		case 'MASKING' : $USEB['api_domain'] = "https://api3.useb.co.kr";				break;
		case 'STATUS'  : $USEB['api_domain'] = "https://api3.useb.co.kr";				break;
		default        : $USEB['api_domain'] = "https://api3.useb.co.kr";				break;
	}

	//print_r($headers); exit;
	//print_r($data); exit;

	$url = $USEB['api_domain'] . $detailUrl;
	//echo $url; exit;

	$header_str = json_encode($headers, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
	$data_str   = json_encode($data, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
	//$data_str = http_build_query($data, '', '&');


	$logSql = "
		INSERT INTO
			useb_curl_request_log
		SET
			rdate      = CURDATE(),
			rtime      = CURTIME(),
			mb_no      = '".$member['mb_no']."',
			title      = '".$title."',
			toDomain   = '".$USEB['api_domain']."',
			toUrl      = '".$detailUrl."',
			sendHeader = '".$header_str."',
			sendJson   = '".$data_str ."',
			ip         = '".$_SERVER['REMOTE_ADDR']."'";

	sql_query($logSql);
	$log_id = sql_insert_id();		// 로그ID


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_PORT , $USEB['port']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$method = strtoupper($method);

	if($method=="PUT") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
	}
	else if($method=="DELETE") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
	}
	else if($method=="POST") {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_PORT , $USEB['port']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
	}
	else {		// DEFAULT GET
		$url = $USEB['api_domain'];
	//$url.= ':' . $USEB['port'];
		$url.= ($detailUrl) ? $detailUrl : '';
		$url.= '?' . $data_str;
	}
	curl_setopt($ch, CURLOPT_URL, $url);

	$result = curl_exec($ch);

	$http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header      = substr($result, 0, $header_size);
	$body        = substr($result, $header_size);

	curl_close($ch);


	$ret['http_code'] = $http_code;
	$ret['head']      = $header;
	$ret['body']      = json_decode($body, true);
//$ret['req_url']   = $url;

	//////////////////////
	// 로그 기록 마무리
	//////////////////////
	$result_json = json_encode($ret, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

	$thrSec = time() - $stimeStamp;

	$logSql = "
		UPDATE
			useb_curl_request_log
		SET
			recvJson       = '".$result_json."',
			transaction_id = '".$ret['body']['transaction_id']."',
			thrSec         = '".$thrSec."'
		WHERE
			idx = '".$log_id."'";
	sql_query($logSql);

	return $ret;

}


///////////////////////////////////////////////////////////////////////////////
// OAUTH
///////////////////////////////////////////////////////////////////////////////

//***** ClientID, Secret 조회 *****//
function usebGetClientID() {

	global $g5;
	global $USEB;
	global $CONF;

	$apiGubun  = 'OAUTH';
	$detailUrl = '/oauth/get-client-secret';
	$title     = 'ClientID, Secret 조회';
	$method    = 'POST';


	$ARR = array();

	if($USEB['client_id'] && $USEB['client_secret']) {

		$ARR['success']       = '2';
		$ARR['client_id']     = $USEB['client_id'];
		$ARR['client_secret'] = $USEB['client_secret'];

	}
	else {

		$data = array(
			'email'    => $USEB['token_email'],
			'password' => $USEB['token_passwd']
		);

		$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method);
		$RESULT = $CRES['body'];

		$ARR['success'] = $RESULT['success'];

		if($RESULT['success']) {
			$ARR['client_id']     = $RESULT['data']['client_id'];
			$ARR['client_secret'] = $RESULT['data']['client_secret'];
		}
		else {
			$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
		}

	}

	return $ARR;

}


// 토큰발행
function usebGetToken() {				// usebGetToken('1won|OCR|masking|status', $transaction_id)

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = 'OAUTH';
	$detailUrl = '/oauth/token';
	$title     = '토큰발행';
	$method    = 'POST';

	$token = '';

	// 유효한 기존발행토큰 존재시 해당 토큰 사용. 미존재시 재발행요청
	$sql = "
		SELECT jwt
		FROM useb_token
		WHERE gubun = '".$apiGubun."' AND expiredate > NOW()
		ORDER BY expiredate DESC
		LIMIT 1";
	$TOKEN_LOG = sql_fetch($sql);

	if($TOKEN_LOG['jwt']) {

		$token = $TOKEN_LOG['jwt'];

	}
	else {

		$data = array(
			'email'    => $USEB['token_email'],
			'password' => $USEB['token_passwd']
		);

		$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method);
		$RESULT = $CRES['body'];
		//print_rr($RESULT, 'font-size:12px');

		if($RESULT['success']) {

			$sql = "
				INSERT INTO
					useb_token
				SET
					gubun      = '".$apiGubun."',
					jwt        = '".$RESULT['jwt']."',
					rdatetime  = NOW(),
					expiredate = '".$RESULT['expires_in']."'";
			sql_query($sql);

			$token = $RESULT['jwt'];

		}

	}

	return $token;

}


///////////////////////////////////////////////////////////////////////////////
// KEYS: 암호화 가이드
///////////////////////////////////////////////////////////////////////////////

//***** 공개키 개인키 생성 *****//
function usebGenerateKeyPair() {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = 'KEYS';
	$detailUrl = '/keys/generate-key-pair';
	$title     = 'RSA2048 공개키 개인키 생성';
	$method    = 'POST';

	$ARR = array();

	$generated = false;

	//echo "공개키 Size: " . fileSize($USEB['pbkey']) . " / 개인키 Size: " . fileSize($USEB['pvkey']);

/*
	if( fileSize($USEB['pbkey']) > 0 && fileSize($USEB['pvkey']) > 0 ) {
		$generated = true;
		$ARR['success'] = 2;
		$ARR['data']['public_key']  = file_get_contents($USEB['pbkey']);
		$ARR['data']['private_key'] = file_get_contents($USEB['pvkey']);
		return $ARR;
	}
*/

	if(!$generated) {

		$CLIENT = usebGetClientID();
		$data = array('client_id' => $CLIENT['client_id'], 'client_secret' => $CLIENT['client_secret']);

		$token = usebGetToken();

		$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
		$RESULT = $CRES['body'];

		//print_rr($CRES, 'font-size:12px');
		//print_rr($RESULT, 'font-size:12px');
		//exit;

		$ARR['success'] = $RESULT['success'];

		if($RESULT['success']) {

			// 공개키 개인키 생성 후 pbkey, pvkey에 저장해줘야 함.
			$fp = fopen($USEB['pbkey'], "w");
			fputs($fp, $RESULT['data']['public_key']);
			fclose($fp);

			$fp = fopen($USEB['pvkey'], "w");
			fputs($fp, $RESULT['data']['private_key']);
			fclose($fp);

			$ARR['public_key']  = $RESULT['data']['public_key'];
			$ARR['private_key'] = $RESULT['data']['private_key'];

		}
		else {
			$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
		}

	}

	return $ARR;

}

//***** 공개키 등록 *****//
function usebRegisterPublicKey() {

	global $g5;
	global $CONF;
	global $USEB;

	if( fileSize($USEB['pbkey'])==0 ) {

		$ARR = array('success' => false, 'error_message' => '공개키 비밀키가 없습니다.');
		return $ARR;

	}

	$apiGubun  = 'KEYS';
	$detailUrl = "/keys/register-public-key";
	$title     = '공개키 등록';
	$method    = 'POST';

	$token = usebGetToken();

	$data = array(
		'public_key'    => file_get_contents($USEB['pbkey']),
		'client_id'     => $USEB['client_id'],
		'client_secret' => $USEB['client_secret'],
	);

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];
	//print_rr($RESULT, 'font-size:12px');

}

//***** 키교환 *****//
function usebExchangeKeys() {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = 'KEYS';
	$detailUrl = "/keys/exchange-keys";
	$title     = '키교환';
	$method    = 'POST';

	$ARR = array();

	$sql = "
		SELECT
			encrypted_ses_key, encrypted_sym_key, expiry_date
		FROM
			useb_key_log
		WHERE 1
			AND expiry_date > NOW()
		ORDER BY
			expiry_date DESC
		LIMIT 1";
	//print_rr($sql);
	$R = sql_fetch($sql);

	//unset($R);		// ▶▶▶▶▶▶▶ 계속 신규 발급되도록 할때

	if($R['encrypted_ses_key'] && $R['encrypted_sym_key']) {

		$ARR['success'] = '2';
		$ARR['encrypted_ses_key'] = $R['encrypted_ses_key'];
		$ARR['encrypted_sym_key'] = $R['encrypted_sym_key'];
		$ARR['expiry_date'] = $R['expiry_date'];

	}
	else {

		$token = usebGetToken();

		if($USEB['client_id'] && $USEB['client_secret']) {
			$data = array('client_id' => $USEB['client_id'], 'client_secret' => $USEB['client_secret']);
		}
		else {
			$CLIENT = usebGetClientID();
			$data = array('client_id' => $CLIENT['client_id'], 'client_secret' => $CLIENT['client_secret']);
		}

		$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
		$RESULT = $CRES['body'];
		//print_rr($RESULT, 'font-size:12px'); exit;

		$ARR['success'] = $RESULT['success'];

		if($RESULT['success']) {
			$sql = "
				INSERT INTO
					useb_key_log
				SET
					encrypted_ses_key = '".$RESULT['data']['encrypted_ses_key']."',
					encrypted_sym_key = '".$RESULT['data']['encrypted_sym_key']."',
					expiry_date = '".$RESULT['data']['expiry_date']."'";
			sql_query($sql);

			$ARR['encrypted_ses_key'] = $RESULT['data']['encrypted_ses_key'];
			$ARR['encrypted_sym_key'] = $RESULT['data']['encrypted_sym_key'];
			$ARR['expiry_date']       = $RESULT['data']['expiry_date'];
		}
		else {
			$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
		}

	}

	return $ARR;

}

//***** 대칭키(sym_key) 추출 *****//
function usebExtractSymKey() {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = 'KEYS';
	$detailUrl = "/keys/extract-keys";
	$title     = '대칭키추출';
	$method    = 'POST';

	$ARR = array("success"=>true, "sym_key"=>"617ff69ea67b5a7c005e0534d4e31770");		// ▶▶▶▶▶▶▶ 테스트용 고정값
	return $ARR;


	$ARR = array();

	if( filesize($USEB['pvkey']) == 0 ) {
		$ARR = array('success' => false, 'error_message' => '개인키가 없습니다.');
		return $ARR;
	}

	$KEY = usebExchangeKeys();
	if($KEY['encrypted_ses_key']=='' || $KEY['encrypted_sym_key']=='') {
		$ARR = array('success' => false, 'error_message' => '세션키 미발급');
		return $ARR;
	}

	$data = array(
		'encrypted_ses_key' => $KEY['encrypted_ses_key'],
		'encrypted_sym_key' => $KEY['encrypted_sym_key'],
		'private_key'       => file_get_contents($USEB['pvkey'])
	);

	// 기 발급된 대칭키 조회
	$sql = "
		SELECT
			sym_key
		FROM
			useb_key_log
		WHERE 1
			AND expiry_date > NOW()
			AND encrypted_ses_key = '".$KEY['encrypted_ses_key']."'
			AND encrypted_sym_key = '".$KEY['encrypted_sym_key']."'
		ORDER BY
			expiry_date DESC
		LIMIT 1";
	$R = sql_fetch($sql);

	//unset($R);		// ▶▶▶▶▶▶▶ 계속 신규 발급되도록 할때

	if($R['sym_key']) {

		$ARR['success'] = '2';
		$ARR['sym_key'] = $R['sym_key'];

	}
	else {

		$token = usebGetToken();

		$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
		$RESULT = $CRES['body'];

		$ARR['success'] = $RESULT['success'];

		if($RESULT['success']) {

			$ARR['sym_key'] = $RESULT['data']['sym_key'];

			$sql = "
				UPDATE useb_key_log
				SET sym_key = '".$RESULT['data']['sym_key']."'
				WHERE encrypted_ses_key = '".$KEY['encrypted_ses_key']."' AND encrypted_sym_key = '".$KEY['encrypted_sym_key']."'";
			sql_query($sql);

		}
		else {
			$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
		}

	}

	return $ARR;

}

//***** 대칭키 암호화 *****//
function usebEncrypt($plaintext) {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = 'KEYS';
	$detailUrl = "/keys/encrypt";
	$title     = '대칭키 암호화';
	$method    = 'POST';

	$token = usebGetToken();

	$EXTSYMKEY = usebExtractSymKey();

	$data = array(
		'sym_key'   => $EXTSYMKEY['sym_key'],			// 대칭키(AES256)
		'plaintext' => $plaintext									// 개인정보 텍스트 또는 신분증 이미지(base64)
	);

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];

	if($RESULT['success']) {

		if(strlen($plaintext) <= 64) $plaintext = sql_real_escape_string($plaintext);

		$sql = "
			INSERT INTO
				useb_encrypt_log
			SET
				ciphertext = '".$RESULT['data']['ciphertext']."',
				sym_key = '".$EXTSYMKEY['sym_key']."',
				rdatetime = NOW()";
		sql_query($sql);

		$ciphertext = $RESULT['data']['ciphertext'];
	}
	else {
		//$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $ciphertext;

}

//***** 대칭키 복호화 *****//
function usebDecrypt($ciphertext) {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = 'KEYS';
	$detailUrl = "/keys/decrypt";
	$title     = '대칭키 복호화';
	$method    = 'POST';

	$token = usebGetToken();

	$EXTSYMKEY = usebExtractSymKey();

	$data = array(
		'sym_key'    => $EXTSYMKEY['sym_key'],
		'ciphertext' => $ciphertext
	);

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];

	$ARR['success'] = $RESULT['success'];

	if($RESULT['success']) {
		$plaintext = $RESULT['data']['plaintext'];
	}
	else {
		//$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $plaintext;

}


///////////////////////////////////////////////////////////////////////////////
// 1원인증   *** 세틀뱅크API로 사용할 예정이므로 본 API는 사용하지 않는다.
///////////////////////////////////////////////////////////////////////////////

//***** 1원입금이체 (안씀) *****//
function useb1wonSend($data=array()) {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun  = '1WON';
	$detailUrl = "/send";
	$title     = '1원 계좌인증 - 1원입금이체';
	$method    = 'POST';

	if(!$data['bank_code']) { $ARR = array('success' => false, 'error_message' => '오류: 은행코드'); return $ARR; }
	if(!$data['account_num']) { $ARR = array('success' => false, 'error_message' => '오류: 계좌번호'); return $ARR; }
	if(!$data['account_holder_name']) { $ARR = array('success' => false, 'error_message' => '오류: 예금주'); return $ARR; }
	if(!$data['code_type']) { $ARR = array('success' => false, 'error_message' => '오류: 인증코드타입'); return $ARR; }

	$token = usebGetToken();

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];

	$ARR = array();
	$ARR['success'] = $RESULT['success'];

	if($RESULT['success']) {
		$ARR['message']        = $RESULT['message'];									// 메시지 예) "1won sent successfully ",
		$ARR['print_content']  = $RESULT['data']['print_content'];		// 인증코드
		$ARR['transaction_id'] = $RESULT['transaction_id'];						// 트랜젝션ID
	}
	else {
		$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $ARR;

}

//***** 인증코드검증 (안씀) *****//
function useb1wonVerify($parent_transaction_id, $auth_code) {

	global $g5;
	global $CONF;
	global $USEB;

	// $parent_transaction_id ::: 1원입금이제 전문의 transaction_id

	$ARR = array();

	$apiGubun  = '1WON';
	$detailUrl = "/verify";
	$title     = '1원 계좌인증 - 인증코드검증';
	$method    = 'POST';

	if(!$send_transaction_id) { $ARR = array('success' => false, 'error_message' => '오류: 1원입금이체건의 트랜젝션ID 미전송'); return $ARR; }
	if(!$auth_code) { $ARR = array('success' => false, 'error_message' => '오류: 인증코드 미전송'); return $ARR; }

	$data = array(
		'transaction_id' => $parent_transaction_id,
		'print_content'  => $auth_code
	);

	$token = usebGetToken();

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];

	$ARR = array();
	$ARR['success'] = $RESULT['success'];

	if($RESULT['success']) {
		$ARR['pair_transaction_id'] = $RESULT['data']['pair_transaction_id'];
		$ARR['print_content']       = $RESULT['data']['print_content'];
	}
	else {
		$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $ARR;

}


///////////////////////////////////////////////////////////////////////////////
// OCR (광학 문자 인식)
///////////////////////////////////////////////////////////////////////////////
function usebOCR($gubun, $idcard_path) {

	global $g5;
	global $CONF;
	global $USEB;

	$apiGubun = 'OCR';
	$title = 'OCR';
	$method = 'POST';

	if($gubun=='idcard') {
		$title.= ' - 주민등록증';
		$detailUrl = "/ocr/idcard-driver";
	}
	else if($gubun=='driver') {
		$title.= ' - 운전면허증';
		$detailUrl = "/ocr/idcard-driver";
	}
	//else if($gubun=='alien') {
	//	$title.= ' - 외국인등록증';
	//	$detailUrl = "/ocr/alien";
	//}
	//else if($gubun=='bizz') {
	//	$title.= ' - 사업자등록증';
	//	$detailUrl = "/ocr/ocr-doc";
	//}
	else {
		return false;
	}



	$ext = substr($idcard_path, -3, 3);		// 확장자
	$image_info = getImageSize($idcard_path);
	$mimeType = $image_info['mime'];		// 마임타입 얻어옴

	$image_text   = base64_encode(file_get_contents($idcard_path));			// 이미지 파일을 base64
	$image_base64 = usebEncrypt($image_text);														// AES-256 암호화(함수 내부에서 대칭키 추출하여 암호화)

	$data['image_base64'] = $image_base64;		// 주민등록증사진 예) 이미지 base64코드
	$data['image']        = '';						// 주민등록증사진
	$data['is_color']     = true;						// 복사본 여부 확인 예) true, false
	$data['secret_mode']  = true;						// AES256 암호화 적용 예) true, false

	$token = usebGetToken();
	$CRES  = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token, $mimeType);
	$RESULT = $CRES['body'];

	//print_r($CRES); exit;
	//print_r($RESULT); exit;

	$ARR = array();
	$ARR['result'] = $RESULT['success'];

	if($RESULT['success']) {
		if($gubun=='idcard') {
			$ARR['idType']    = $RESULT['data']['idType'];				// 신분증종류 예) 1 (주민등록증)
			$ARR['userName']  = usebDecrypt($RESULT['data']['userName']);			// 이름 예) 황희준
			$ARR['juminNo1']  = usebDecrypt($RESULT['data']['juminNo1']);			// 생년월일 예) 821122
			$ARR['juminNo2']  = usebDecrypt($RESULT['data']['juminNo2']);			// 주민등록번호 뒷자리 예) 1056914
			$ARR['_juminNo2'] = usebDecrypt($RESULT['data']['_juminNo2']);		// 주민등록번호 뒷자리 처리 예) 1******
			$ARR['issueDate'] = usebDecrypt($RESULT['data']['issueDate']);		// 발급일자 예)20140703
		}
		else if($gubun=='driver') {
			$ARR['idType']    = $RESULT['data']['idType'];				// 신분증종류 예) 2 (운전면허증)
			$ARR['userName']  = usebDecrypt($RESULT['data']['userName']);			// 이름 예) 황희준
			$ARR['driverNo']  = usebDecrypt($RESULT['data']['driverNo']);			// 운전면허번호 예) 11-16-044390-60
			$ARR['juminNo1']  = usebDecrypt($RESULT['data']['juminNo1']);			// 생년월일 예) 821122
			$ARR['juminNo2']  = usebDecrypt($RESULT['data']['juminNo2']);			// 주민등록번호 뒷자리 예) 1056914
			$ARR['_juminNo2'] = usebDecrypt($RESULT['data']['_juminNo2']);		// 주민등록번호 뒷자리 처리 예) 1******
		//$ARR['issueDate'] = usebDecrypt($RESULT['data']['issueDate']);		// 발급일자 예)20140703
		}
		/*
		else if($gubun=='alien') {
			$ARR['idType']    = $RESULT['data']['idType'];				// 신분증종류 예) 5-1 (외국인등록증), 5-2 (외국국적동포 국내거소신고증), 5-3 (영주증)
			$ARR['country']   = $RESULT['data']['country'];				// 국가 예) VIETNAM
			$ARR['visa']      = $RESULT['data']['visa'];					// 비자 예) 일반연수(D-4)
			$ARR['userName']  = $RESULT['data']['userName'];			// 이름 예) NGUYEN HONG
			$ARR['issueNo']   = $RESULT['data']['issueNo'];				// 외국인등록번호 예) 11-16-044390-60
			$ARR['issueNo1']  = $RESULT['data']['issueNo1'];			// 외국인등록번호 앞자리 예) 000507
			$ARR['issueNo2']  = $RESULT['data']['issueNo2'];			// 외국인등록번호 뒷자리 예) 7780024
			$ARR['_juminNo2'] = $RESULT['data']['_juminNo2'];			// 외국인등록번호 뒷자리 처리 예) 1******
			$ARR['issueDate'] = $RESULT['data']['issueDate'];			// 발급일자 예)20140703
		}
		else if($gubun=='bizz') {
			$ARR['docSize']        = $RESULT['data']['docSize'];						// 문서 사이즈 예) 1700*2338
			$ARR['docType']        = $RESULT['data']['docType'];						// 사업자 종류 예) 법인사업자
			$ARR['companyName']    = $RESULT['data']['companyName'];				// 회사명 예) 거제영농조합법인
			$ARR['ownerName']      = $RESULT['data']['ownerName'];					// 대표명 예) 김수근
			$ARR['businessRegNum'] = $RESULT['data']['businessRegNum'];			// 사업자등록번호 예) 612-81-17074
			$ARR['businessCorpNum']= $RESULT['data']['businessCorpNum'];		// 법인등록번호 예) 194971-0001444
			$ARR['companyAddr']    = $RESULT['data']['companyAddr'];				// 사업장소재지 예) 경상남도 거제시 거제면 옥산금성길 21
			$ARR['HQAddr']         = $RESULT['data']['HQAddr'];							// 본점소재지 예) 경상남도 거제시 거제면 옥산금성길 21
			$ARR['openDate']       = $RESULT['data']['openDate'];						// 개업연원일 예) 2003년 1월 17일
			$ARR['businessType1']  = $RESULT['data']['businessType1'];			// 업태 예) 제조, 제조업, 도소매
			$ARR['businessType2']  = $RESULT['data']['businessType2'];			// 종목 예) 축산물가공, 건강보조식품
		}
		*/
	}
	else {
		$ARR['error_code']    = $RESULT['error_code'];
		$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $ARR;

}


///////////////////////////////////////////////////////////////////////////////
// 마스킹
///////////////////////////////////////////////////////////////////////////////
function useb_idcard_mask($gubun, $idcard_path) {

	$apiGubun = 'MAKING';
	$title    = '마스킹';
	$method   = 'POST';

	if($gubun=='idcard') {
		$title.= ' - 주민등록증';
		$detailUrl = "/masking/idcard";
	}
	else if($gubun=='driver') {
		$title.= ' - 운전면허증';
		$detailUrl = "/masking/driver";
	}
	/*
	else if($gubun=='alien') {
		$title.= ' - 외국인등록증';
		$detailUrl = "/masking/alien";
	}
	*/
	else {
		return false;
	}

	if(!$idcard_path) { return false; }


	$ext        = substr($idcard_path, -3, 3);		// 확장자
	$image_info = getImageSize($idcard_path);
	$mimeType   = $image_info['mime'];		// 마임타입 얻어옴

	$image_text   = base64_encode(file_get_contents($idcard_path));			// 이미지 파일을 base64
	$image_base64 = usebEncrypt($image_text);														// AES-256 암호화(함수 내부에서 대칭키 추출하여 암호화)

	$data['image_base64'] = $image_base64;		// 주민등록증사진 예) 이미지 base64코드
	$data['image']        = '';								// 쯩 사진 예) jpg, png
	$data['secret_mode']  = true;							// AES256 암호화 적용 예) true, false


	$token = usebGetToken();

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];

	$ARR = array();
	$ARR['RESULT'] = $RESULT['success'];

	if($RESULT['success']) {
		$ARR['image_base64_mask'] = $RESULT['data']['image_base64_mask'];			// 마스킹된 쯩 사진 예) 이미지 base64코드
		$ARR['mimeType'] = $mimeType;
	}
	else {
		$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $ARR;

}


///////////////////////////////////////////////////////////////////////////////
// 신분증 진위여부
///////////////////////////////////////////////////////////////////////////////
function useb_idcard_identify($gubun='idcard', $udata=array()) {

	$apiGubun = 'STATUS';
	$title    = '신분증진위여부';
	$method = 'POST';

	if($gubun=='idcard') {		// 주민등록증
		$title.= ' - 주민등록증';
		$detailUrl = "/status/idcard";
	}
	else if($gubun=='driver') {		// 운전면허증
		$title.= ' - 운전면허증';
		$detailUrl = "/status/driver";
	}
//else if($gubun=='alien') {		// 외국인등록증
//	$title.= ' - 외국인등록증';
//	$detailUrl = "/ocr/alien";
//}
	else {
		return false;
	}


	$secret_mode = true;

	if($gubun=='idcard') {		// 주민등록증

		if($secret_mode) {
			$identity    = usebEncrypt($udata['identity']);
			$issueDate   = usebEncrypt($udata['issueDate']);
			$userName    = usebEncrypt($udata['userName']);
		}
		else {
			$identity    = $udata['identity'];
			$issueDate   = $udata['issueDate'];
			$userName    = $udata['userName'];
		}

		$data['identity']    = $identity;				// 주민등록번호 예) 8811211056911
		$data['issueDate']   = $issueDate;			// 발급일자 예) 20000301
		$data['userName']    = $userName;				// 이름 예) 홍길동
		$data['secret_mode'] = $secret_mode;		// AES256 암호화 적용 예) true, false (암호화 적용시 암호화 가이드를 반드시 숙지하고 테스트 하시기 바랍니다)

	}
	else if($gubun=='driver') {		// 운전면허증

		if($secret_mode) {
			$juminNo     = usebEncrypt($udata['juminNo']);
			$userName    = usebEncrypt($udata['userName']);
			$birthDate   = usebEncrypt($udata['birthDate']);
			$licenseNo   = usebEncrypt($udata['licenseNo']);
		}
		else {
			$juminNo     = $udata['juminNo'];
			$userName    = $udata['userName'];
			$birthDate   = $udata['birthDate'];
			$licenseNo   = $udata['licenseNo'];
		}

		$data['juminNo']     = $juminNo;				// 주민등록번호 유효성 검사 예) 9211211056915
		$data['userName']    = $userName;				// 이름 예) 홍길동
		$data['birthDate']   = $birthDate;			// 생년월일 예) 19821120
		$data['licenseNo']   = $licenseNo;			// 운전면허번호 예) 11-16-044391-61
		$data['secret_mode'] = $secret_mode;		// AES256 암호화 적용 예) true, false (암호화 적용시 암호화 가이드를 반드시 숙지하고 테스트 하시기 바랍니다)

	}
	/*
	else if($gubun=='alien') {		// 외국인등록증
		$data['issueNo']     = $udata['issueNo'];				// 외국인등록번호 예) 000507-7780026
		$data['issueDate']   = $udata['issueDate'];			// 발급일자 예) 20190404
		$data['secret_mode'] = '';											// AES256 암호화 적용 예) true, false (암호화 적용시 암호화 가이드를 반드시 숙지하고 테스트 하시기 바랍니다)
	}
	*/


	$token = usebGetToken();

	$CRES = usebCurl($apiGubun, $data, $detailUrl, $title, $method, $token);
	$RESULT = $CRES['body'];


	$ARR = array();
	$ARR['RESULT'] = $RESULT['success'];

	if($RESULT['success']) {
		$ARR['message'] = $RESULT['message'];
	}
	else {
		$ARR['error_message'] = usebErrMsg($RESULT['error_code'], $RESULT['message']);
	}

	return $ARR;

}


///////////////////////////////////////////////////////////////////////////////
// 유스비 에러코드
///////////////////////////////////////////////////////////////////////////////
function usebErrMsg($useb_error_code, $useb_error_msg='') {

	if(!$useb_error_code) { return false; }

	$msg = '';

	switch($useb_error_code) {
		// common
		case 'COO1' : $msg = "잘못된 Content-type으로 요청"; break;
		case 'COO2' : $msg = "Json형식으로 보내지 않은 경우"; break;
		case 'C011' : $msg = "토큰이 없거나 잘못된 값인 경우"; break;
		case 'C013' : $msg = "다른 서버에서 생성한 토큰인 경우"; break;
		case 'CO21' : $msg = "이용하고자 하는 API 서비스가 사용 가능 scope 에 포함되어 있지 않은 경우"; break;
		case 'CO22' : $msg = "HTTPS 가 아닌 HTTP 호출을 한 경우"; break;
		case 'CO31' : $msg = "잘못된 URL로 요청한 경우"; break;
		case 'CO41' : $msg = "잘못된 Method로 호출한 경우"; break;
		case 'CO61' : $msg = "정상 호출을 요청했으나 기관 서버 점검 시간이거나 API 서버 오류, 기관 서버 오류인 경우"; break;

		// Oauth
		case 'T001' : $msg = "client_id, client_secret 정보가 잘못된 경우"; break;

		// 암호화가이드
		case 'KC001' :
		case 'KS001' :
		case 'KR001' :
		case 'KX001' : $msg = "client_id, client_secret 정보가 잘못된 경우"; break;
		case 'KT001' : $msg = "private key 정보가 잘못된 경우"; break;
		case 'KD001' : $msg = "sym_key가 잘못된 경우"; break;

		// 1원인증
		case 'S001' : $msg = "은행코드, 계좌번호, 예금주명 중 하나라도 없는 경우"; break;
		case 'S011' : $msg = $useb_error_msg; break;
		case 'V001' : $msg = "인증 코드 미입력 또는 불일치한 경우"; break;
		case 'V021' : $msg = "최대 시도 횟수 초과(5회까지 시도 가능)"; break;
		case 'V031' : $msg = "인증 코드가 만료되었을 때(발급 후 ?분 후 만료)"; break;

		// OCR
		case 'OOO1' : $msg = "파일의 크기가 3MB 이상일 경우"; break;
		case 'OOO2' : $msg = "파일 형식이 올바르지 않은 경우(jpg, png가 아닌 경우)"; break;
		case 'O003' : $msg = "다른 신분증을 시도한 경우"; break;
		case 'O004' : $msg = "가로 길이가 500px 이하일 때 (OCR 성능을 위해서 500~1000px 권장)"; break;
		case 'O005' : $msg = "흑백복사본으로 인식\n\n피사체보다 배경색이 많은 비중을 차지할 경우 발생하는 인식 오류입니다.\n신분증외 배경이 적은 이미지를 등록해주세요."; break;
	//case 'O005' : $msg = "흑백 컬러의 복사한 신분증일 때"; break;
		case 'OO11' : $msg = "파일의 크기가 3MB 이상일 경우"; break;
		case 'OO12' : $msg = "파일 형식이 올바르지 않은 경우(jpg, png가 아닌 경우)"; break;
		case 'OO13' : $msg = "다른 문서 종류를 요청했을 경우"; break;
		case 'O014' : $msg = "가로 길이가 800px 이하일 때 (OCR 성능을 위해서 800~1000px 권장)"; break;

		// 마스킹
		case 'M001' : $msg = "파일 크기가 너무 큰 경우(3MB 이상)"; break;
		case 'M002' : $msg = "파일 형식이 올바르지 않은 경우(jpg, png가 아닌 경우)"; break;
		case 'M003' : $msg = "마스킹 영역이 제대로 인식되지 않았을 경우"; break;
		case 'M004' : $msg = "가로 길이가 500px 미만일 때"; break;

		// 신분증진위여부
		case 'A001' : $msg = "주민등록번호가 틀리거나 자릿수가 맞지 않는 경우"; break;
		case 'A002' : $msg = "발급일자의 자릿수가 맞지 않는 경우"; break;
		case 'A003' : $msg = $useb_error_msg; break;
		case 'A011' : $msg = "주민등록번호 유효성 검사 실패인 경우"; break;
		case 'A013' : $msg = $useb_error_msg; break;
		case 'A021' : $msg = "여권번호가 누락되거나 틀린 경우"; break;
		case 'A023' : $msg = $useb_error_msg; break;
		case 'A024' : $msg = "여권정보가 일치하지만 만료되거나 분실신고된 여권인 경우"; break;
		case 'A033' : $msg = $useb_error_msg; break;

		case 'A041' : $msg = "외국인등록번호에 일부 숫자가 누락된 경우"; break;
		case 'A043' : $msg = $useb_error_msg; break;

		// 안면인증
		case 'FOO1' : $msg = "파일의 크기가 5MB 이상일 경우"; break;
		case 'F002' : $msg = "얼굴이 없는 사진을 입력한 경우, 입력 데이터가 없는 경우"; break;
	}
	if(!$msg) $msg = $useb_error_msg;

	return $msg;

}

?>