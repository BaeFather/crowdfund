<?
/**********************************************************************************************
	NICE평가정보 휴대폰 인증 서비스
	서버 네트웍크 및 방확벽 관련하여 아래 IP와 Port를 오픈해 주셔야 이용 가능합니다.
	IP : 121.131.196.200 / Port : 3700 ~ 3715

	step1. 개인정보를 받아 인증번호를 발송한다.
**********************************************************************************************/

include_once('_common.php');
include_once(G5_LIB_PATH . '/crypt.lib.php');
include_once(G5_PATH . '/mypage/crypt.php');


$sSiteCode   = 'AB917';																			// 사이트 코드 (NICE평가정보에서 발급한 사이트코드)
$sSitePw     = '8vJBrEtmUvdb';															// 사이트 패스워드 (NICE평가정보에서 발급한 사이트패스워드)
$key         = 'jumin';

$iBirth      = $_POST['Mbirth'];														// 생년월일 19880609
$iSex        = $_POST['Msex'];
$sBirth      = substr($iBirth, 2, 6) . $iSex;								// 주민번호 앞 7자리

$oName       = sql_real_escape_string(trim($_POST['Mname']));
$sName       = iconv("utf-8", "euc-kr", $oName);						// 사용자 성명
$sName       = "'".$sName."'";															// 공백이 들어가는 이름(외국인명)도 인증이 적용될 수 있도록 quote 처리 해준다.
$sHP         = $_POST['Mhp'];																// 휴대폰번호
$sHPComp     = $_POST['Mhpcomp'];														// 이통사
$sRequestSeq = date('YmdHis')."-".rand(100,999);						// 요청SEQ_식별값
$use         = $_POST['Muse'];															// 인증의 용도 (ex 단순인증=simple_auth)

$already = '';

if($use=='') {
	// 가입자 핸드폰 체크
	$sql = "
		SELECT
			COUNT(*) AS cnt
		FROM
			g5_member
		WHERE 1
			AND mb_level='1'
			AND mb_name='".$oName."'
			AND member_type='1'
			AND mb_hp='".masterEncrypt($sHP, false)."'";
	$ROW = sql_fetch($sql);
	$ROW['cnt'] = 0;
	if($ROW['cnt']) {
		$res['getReturnCode'] = "DUPLICATE";
		echo json_encode($res, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
		exit;
	}
}


// 기 인증내역 체크 (1시간내의 인증 성공 내역)
$sql = "
	SELECT
		*
	FROM
		cf_auth_nice
	WHERE 1
		AND auth_name      = '".$oName."'
		AND auth_birth     = '".encrypt($_POST['Mbirth'], $key)."'
		AND auth_sex       = '".$iSex."'
		AND auth_telecom   = '".$sHPComp."'
		AND auth_phone_num = '".$sHP."'
		AND rescode        = '0000'
		AND auth_finish    = '1'
		AND insert_date   >= NOW()-INTERVAL 1 HOUR
	ORDER BY
		insert_date DESC
	LIMIT 1";
$chk_row = sql_fetch($sql);

if($chk_row['idx']) {

	$already = "Y";
	$sResultData = $chk_row['rescode']."|".$chk_row['reqseq']."|".$chk_row['resseq'];

}
else {

	// 소켓인증 실행
	// MCheckPlus AUTH 사이트코드 사이트비밀번호 주민등록번호 이름 이통사구분(1/2/3) 휴대전화번호 요청고유번호(option)

	$mcheckplus_path = "/home/crowdfund/NICE/CheckPlusSafe_SOCK_PHP/64bit/MCheckPlus";

	$nice_send_data = "";
	$nice_send_data = "$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $oName $sHPComp $sHP $sRequestSeq";

	$sResultData    = "";
	$sResultData    = `$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $sName $sHPComp $sHP $sRequestSeq`;

}

//echo "결과 : $sResultData";
/*
결과 : 응답코드|인증시간|요청SEQ|응답SEQ
연동 결과 코드
	0 : 정상
	-1 ~ -6 : 암/복호화 오류
	-7 ~ -8 : 통신 오류
	-9, 12 : 입력값 오류

	응답 코드(getReturnCode)
	0000 : 인증번호 확인 성공
	0001 : 인증번호 불일치
	0031 : 응답 고유번호 확인 불가
	0032 : 주민번호 불일치
	0033 : 요청 고유번호 불일치
	0034 : 기 인증 완료 건
*/

$res_tmp = explode("|", $sResultData);

$insert_idx = 0;

if($chk_row['idx']) {

	$insert_idx = $chk_row['idx'];

}
else {

	$sql = "
		INSERT INTO
			cf_auth_nice
		SET
			auth_name      = '".$oName."',
			auth_birth     = '". encrypt($_POST['Mbirth'], $key)."',
			auth_sex       = '".$iSex."',
			auth_telecom   = '".$sHPComp."',
			auth_phone_num = '".$sHP."',
			reqseq         = '".$res_tmp[1]."',
			rescode        = '".$res_tmp[0]."',
			resseq         = '".$res_tmp[2]."',
			insert_date    = NOW()";
		sql_query($sql);
		$insert_idx = sql_insert_id();

}

$res = array(
	"src"           => $nice_send_data,
	"res"           => $sResultData,
	"getReturnCode" => $res_tmp[0],
	"req_seq"       => $res_tmp[1],
	"res_seq"       => $res_tmp[2],
	"already"       => $already,
	"sql_id"        => $insert_idx
);


if($chk_row['idx']) {
	$res['checked'] = 'Y';
}

echo json_encode($res, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);


?>