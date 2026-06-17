<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/join.do
## 6. 회원가입
##   ※ 기존회원인 데이터로 가입요청이 들어오면 mb_ci만 없데이트 하고 code 9999 처리함.
##   invested_mailling (투자설명서발급(메일)동의여부 자동동의 설정 : 2020-08-20
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");
include_once(G5_LIB_PATH."/insidebank.lib.php");
include_once(G5_LIB_PATH.'/sms.lib.php');

/*
$REQUEST['ci']												// 본인인증 CI 값
$REQUEST['user_nm']										// 이름
$REQUEST['user_hp']										// 연락처(AES256)
$REQUEST['email']											// 이메일
$REQUEST['postcode']									// 우편번호
$REQUEST['addr_info']									// 기본주소
$REQUEST['addr_detail']								// 상세주소
$REQUEST['acc_user_nm']								// 예금주
$REQUEST['bank_cd']										// 은행코드
$REQUEST['acc_no']										// 계좌번호 :::: 올리고에서 계좌 인증처리된 계좌만 전송해줌
$REQUEST['jumin_no']									// 주민번호(AES256)
$REQUEST['terms_list']								// 약관동의여부
$REQUEST['terms_list']['terms_cd']		// 약관코드
$REQUEST['terms_list']['agree_yn']		// 동의여부

결과코드 예시
{"code":"0000","msg":"정상처리되었습니다." ,"comp_cd":"xxxxxxxxx","bank_cd":"88","acc_no":"xxxxxxxxxxx"}
*/

$ARR = array('code'=>'9999', 'msg'=>'회원가입불가'); echo printJson($ARR); exit;


$REQUEST['ci'] = urldecode($REQUEST['ci']);

if(!$REQUEST['ci']) { $ARR = array('code'=>'9999', 'msg'=>'CI 누락'); echo printJson($ARR); exit; }
if(!$REQUEST['email']) { $ARR = array('code'=>'9999', 'msg'=>'이메일주소 누락'); echo printJson($ARR); exit; }
if(!$REQUEST['user_nm']) { $ARR = array('code'=>'9999', 'msg'=>'이름 누락'); echo printJson($ARR); exit; }
if(!$REQUEST['user_hp']) { $ARR = array('code'=>'9999', 'msg'=>'휴대번호 누락'); echo printJson($ARR); exit; }
if(!$REQUEST['bank_cd']) { $ARR = array('code'=>'9999', 'msg'=>'은행코드 누락'); echo printJson($ARR); exit; }
if(!$REQUEST['acc_no']) { $ARR = array('code'=>'9999', 'msg'=>'계좌번호 누락'); echo printJson($ARR); exit; }

if( strlen($REQUEST['user_hp']) <> 32 ) { $ARR = array('code'=>'9999', 'msg'=>'휴대폰번호 암호화 오류'); echo printJson($ARR); exit; }
if( strlen($REQUEST['acc_no']) <> 32 ) { $ARR = array('code'=>'9999', 'msg'=>'계좌번호 암호화 오류'); echo printJson($ARR); exit; }
if( strlen($REQUEST['jumin_no']) <> 32 ) { $ARR = array('code'=>'9999', 'msg'=>'주민등록번호 암호화 오류'); echo printJson($ARR); exit; }

///////////////////////////////////////////////////////////////////////////////
// 주민번호 저장용 DB접속
///////////////////////////////////////////////////////////////////////////////
$link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);
///////////////////////////////////////////////////////////////////////////////


$REQUEST['user_hp']  = $crypto->deCrypt($REQUEST['user_hp']);
$REQUEST['acc_no']   = $crypto->deCrypt($REQUEST['acc_no']);
$REQUEST['jumin_no'] = $crypto->deCrypt($REQUEST['jumin_no']);

$mb_name   = sql_real_escape_string($REQUEST['user_nm']);
$mb_hp     = preg_replace('/(-| )/', '', $REQUEST['user_hp']);
$mb_hp_key = substr($mb_hp, -4);
$enc_mb_hp = masterEncrypt($mb_hp, false);										// 헬로 개인정보 저장용 암호화 진행
$personalSecurityNumber = $REQUEST['jumin_no'];
$encJumin  = masterEncrypt($personalSecurityNumber, true);		// 헬로 개인정보 저장용 암호화 진행
$encAcctNo = masterEncrypt($REQUEST['acc_no'], false);				// 헬로 개인정보 저장용 암호화 진행


$joinType = 'new';		// $joinType ==> new: 신규, update: 헬로 기가입자중 올리고플래그가 없는 경우, exists: 헬로 기가입자중 올리고플래그가 있는 경우


/*
if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	print_r($REQUEST);
	$JUMIN = getBirthGender($personalSecurityNumber);
	print_r($JUMIN);
	exit;
}
*/



// 주민번호 사용 회원정보 가져오기
$sql = "
	SELECT
		mb_no, mb_ci, mb_id, mb_name, mb_hp, bank_name, bank_code, bank_private_name, account_num, va_bank_code2, virtual_account2, va_private_name2, oligo_userid
	FROM
		g5_member
	WHERE 1
		AND mb_level='1' AND member_group='F'
		AND ( mb_ci='".$REQUEST['ci']."' OR (mb_name='".$mb_name."' AND mb_hp='".$enc_mb_hp."') )
	ORDER BY
		mb_no ASC
	LIMIT 1";
$JOINED = sql_fetch($sql);

if($JOINED['mb_no']) {

	if($JOINED['oligo_userid']==$_CONF['SYNDI_ID']) { $ARR = array('code'=>'9000', 'msg'=>'기가입자'); echo printJson($ARR); exit; }	// 황과장 요청으로 기가입자는 9000번 출력함

	// CI값이 없을 경우 업데이트
	if($JOINED['mb_ci']=='') {
		$sql1 = "UPDATE g5_member SET mb_ci='".$REQUEST['ci']."' WHERE mb_no='".$JOINED['mb_no']."'";
		$res1 = sql_query($sql1);
		if(!$res1) { $ARR = array('code'=>'9000', 'msg'=>'CI업데이트 오류'); echo printJson($ARR); exit; }
	}

	// 환급계좌가 없는 회원일 경우 업데이트
	if($JOINED['bank_code']=='' || $JOINED['account_num']=='') {
		$sql2 = "
			UPDATE
				g5_member
			SET
				bank_name='".$BANK[$REQUEST['bank_cd']]."',
				bank_code='".$REQUEST['bank_cd']."',
				account_num='".$encAcctNo."',
				bank_private_name='".$JOINED['mb_name']."'
			WHERE
				mb_no='".$JOINED['mb_no']."'";
		$res2 = sql_query($sql2);
		if(!$res2) { $ARR = array('code'=>'9999', 'msg'=>'계좌번호 업데이트 오류'); echo printJson($ARR); exit; }
	}

	// 가상계좌가 없는 회원일 경우 -> 주민번호가 없는 경우 이므로 주민번호 저장 처리
	if($JOINED['va_bank_code2']=='' || $JOINED['virtual_account2']=='') {
		$JUMIN_REGIST = sql_fetch("SELECT idx FROM member_private WHERE mb_no='".$JOINED['mb_no']."' AND regist_number='".$encJumin."'", G5_DISPLAY_SQL_ERROR, $link2);
		if(!$JUMIN_REGIST['idx']) {
			$sql = "
				INSERT INTO
					member_private
				SET
					mb_no = '".$JOINED['mb_no']."',
					regist_number = '".$encJumin."',
					5dm = '".strtoupper(md5($REQUEST['jumin_no']))."'";
			$res = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link2);
		}
	}

	$joinType = 'update';			// 기존회원인데 올리고에서 이용하는 사용자를 위해 기존 회원정보에 신디케이션 정보만 업데이트 한다.

}


///////////////////////////////////////////////////////////////////////////////
// 등록처리 시작
///////////////////////////////////////////////////////////////////////////////

if($joinType == 'new') {

	/////////////////////////////////////////////
	// 정상 신규회원 처리
	/////////////////////////////////////////////
	$mb_id                = trim($REQUEST['email']);
	$mb_password          = preg_replace('/(-|:| )/', '', G5_TIME_YMDHIS);

	$basicAddress         = sql_real_escape_string(trim($REQUEST['addr_info']));
	$detailAddress        = sql_real_escape_string(trim($REQUEST['addr_detail']));
	$email                = trim($REQUEST['email']);
	$zipCode              = trim($REQUEST['postcode']);

	$repayAccountBankCode = trim($REQUEST['bank_cd']);
	$repayAccountNumber   = $REQUEST['acc_no'];
	$enc_account_num      = masterEncrypt($repayAccountNumber, false);
	$account_num_key      = substr($repayAccountNumber, -4);
	$account_name         = $mb_name;		// $REQUEST['acc_user_nm']

	$receive_method       = '2';
	$mb_ip                = ($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

	// 주민번호에서 생년월일 및 성별 추출
	$JUMIN = getBirthGender($personalSecurityNumber);
	$birthdate = $JUMIN[0];
	$gender    = $JUMIN[1];

	// 마케팅동의에 관한 파라미터가 없으므로 일단 모두 블로킹한다.
	$mb_mailling  = 'N';
	$mb_push      = 'N';
	$mb_sms       = 'N';

	$sql = "
		INSERT INTO
			g5_member
		SET
			mb_id        = '".$email."',
			mb_level     = '1',
			member_type  = '1',
			member_investor_type = '1',
			mb_name      = '".$mb_name."',
			mb_email     = '".$email."',
			mb_hp        = '".$enc_mb_hp."',
			mb_hp_key    = '".$mb_hp_key."',
			mb_birth     = '".$birthdate."',
			mb_sex       = '".$gender."',
			zip_num      = '".$zipCode."',
			mb_addr1     = '".$basicAddress."',
			mb_addr2     = '".$detailAddress."',
			mb_mailling  = '".$mb_mailling."',
			mb_sms       = '".$mb_sms."',
			invested_mailling = '1',
			receive_method = '".$receive_method."',
			bank_name    = '".$BANK[$repayAccountBankCode]."',
			bank_code    = '".$repayAccountBankCode."',
			account_num  = '".$enc_account_num."',
			account_num_key = '".$account_num_key."',
			bank_private_name = '".$account_name."',
			oligo_userid = '".$_CONF['SYNDI_ID']."',
			oligo_rdate  = '".G5_TIME_YMDHIS."',
			mb_ci        = '".$REQUEST['ci']."',
			mb_ip        = '".$mb_ip."',
			mb_datetime  = '".G5_TIME_YMDHIS."',
			mb_password  = '".get_encrypt_string2($mb_password)."'";
	//echo $sql . "\n\n"; exit;

	$res = sql_query($sql);
	$mb_no = sql_insert_id();

	if($mb_no) {

		////////////////////////////////////////////////////////////////////
		// 주민번호 저장
		////////////////////////////////////////////////////////////////////
		$sql2 = "
			INSERT INTO
				member_private
			SET
				mb_no = '".$mb_no."',
				regist_number = '".$encJumin."',
				5dm = '".strtoupper(md5($REQUEST['jumin_no']))."'";

		$res2 = sql_query($sql2, G5_DISPLAY_SQL_ERROR, $link2);


		////////////////////////////////////////////////////////////////////
		// 신한 가상계좌 생성 (원장정보는 함수 내부에서 입력/수정 처리됨)
		////////////////////////////////////////////////////////////////////
		$result = sh_make_account($mb_no);

		if($result['RCODE']=='00000000') {

			// SMS발송 --------------------------------------------------------------------------
			$sms_contents = "[헬로펀딩 서비스 이용안내]\n" .
											"고객님은 ".$_CONF['SYNDI_TITLE']."(".$_CONF['SYNDI_ID'].") 가입회원으로 헬로펀딩에 가입하셨습니다.\n" .
											"헬로펀딩에 직접 로그인시 필요한 아이디를 아래와 같이 안내해드립니다.\n\n" .
											"헬로펀딩 아이디 : {ID}\n\n" .
											"* 헬로펀딩 직접 로그인시 최초 1회는 반드시 비밀번호 찾기를 통해 임시비밀번호를 발급받으셔서 이용하시기 바랍니다.\n\n" .
											"비밀번호찾기☞ " . G5_URL . "/member/find_pw.php";
			$sms_contents = str_replace("{ID}", $mb_id, $sms_contents);					// 아이디 치환
			unit_sms_send($CONF['admin_sms_number'], $mb_hp, $sms_contents);		// 문자발송
			// SMS발송 --------------------------------------------------------------------------

			$MB = sql_fetch("SELECT va_bank_code2, virtual_account2 FROM g5_member WHERE mb_no='".$mb_no."'");

			$ARR = array(
				"code"    => "0000",
				"msg"     => "정상처리되었습니다.",
				"comp_cd" => $_CONF['comp_cd'],
				"bank_cd" => $MB['va_bank_code2'],		// 예치금 가상계좌번호
				"acc_no"  => $crypto->enCrypt($MB['virtual_account2'])	// *** 암호화 필요 ***
			);

		}
		else {
			// 가상계좌 발급 문제 발생시 입력한 회원정보 삭제
			sql_query("DELETE FROM g5_member WHERE mb_no='".$mb_no."'");
			sql_query("DELETE FROM member_private WHERE mb_no='".$mb_no."'", G5_DISPLAY_SQL_ERROR, $link2);

			$ARR = array("code"=>"9999", "msg"=>"가상계좌발급 오류입니다."); echo printJson($ARR); exit;
		}

	}
	else {
		$ARR = array("code"=>"9999", "msg"=>"시스템 오류-회원등록 실패"); echo printJson($ARR); exit;
	}

}
else if($joinType=='update') {

	/////////////////////////////////////////////////
	// 헬로펀딩 기 가입자 처리
	// - 기존회원정보에 신디케이션 정보만 추가!!
	// - 이메일정보는 가입안내메일발송시에만 사용
	/////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////
	// 신한 가상계좌 생성 (원장정보는 함수 내부에서 입력/수정 처리됨)
	////////////////////////////////////////////////////////////////////
	if($JOINED['virtual_account2']=='') {
		$result = sh_make_account($JOINED['mb_no']);
		if($result['RCODE']!='00000000') { $ARR = array("code"=>"9999", "msg"=>"가상계좌발급 오류"); echo printJson($ARR); exit; }
	}

	// 회원정보 다시 가져오기
	$MB = get_member($JOINED['mb_id']);

	$sql = "
		UPDATE
			g5_member
		SET
			oligo_userid  = '".$_CONF['SYNDI_ID']."',
			oligo_rdate   = '".G5_TIME_YMDHIS."',
			edit_datetime = '".G5_TIME_YMDHIS."'
		WHERE
			mb_no = '".$MB['mb_no']."'";

	if($res = sql_query($sql)) {
		$ARR = array(
			"code"    => "0000",
			"msg"     => "정상처리되었습니다.",
			"comp_cd" => $_CONF['comp_cd'],
			"bank_cd" => $MB['va_bank_code2'],
			"acc_no"  => $crypto->enCrypt($MB['virtual_account2'])	// *** 암호화 필요 ***
		);
	}
	else {
		$ARR = array("code"=>"9999", "msg"=>"DB업데이트 오류"); echo printJson($ARR); exit;
	}

}

sql_close($link2);


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>

