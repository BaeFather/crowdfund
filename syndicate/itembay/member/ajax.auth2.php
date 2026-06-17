<?
/**********************************************************************************************
	NICE평가정보 휴대폰 인증 서비스
	서버 네트웍크 및 방확벽 관련하여 아래 IP와 Port를 오픈해 주셔야 이용 가능합니다.
	IP : 121.131.196.200 / Port : 3700 ~ 3715

	step2. 입력된 인증번호의 일치 확인 - 인증 마무리
**********************************************************************************************/

include_once('_common.php');
//include_once(G5_PATH . '/mypage/crypt.php');


$sSiteCode    = 'AB917';									// 사이트 코드 (NICE평가정보에서 발급한 사이트코드)
$sSitePw      = '8vJBrEtmUvdb';						// 사이트 패스워드 (NICE평가정보에서 발급한 사이트패스워드)


$sResponseSeq = $_POST['ResponseSeq'];		// 응답고유번호
$sAuthno      = $_POST['Authno'];					// SMS인증번호
$sRequestSeq  = $_POST['RequestSeq'];			// 요청SEQ_식별값

$mcheckplus_path = "/home/crowdfund/NICE/CheckPlusSafe_SOCK_PHP/64bit/MCheckPlus";


//인자값 : CNFM 사이트코드 사이트패스워드 응답SEQ 인증번호 요청SEQ(option)
$tmp = "$mcheckplus_path CNFM $sSiteCode $sSitePw $sResponseSeq $sAuthno $sRequestSeq";
$sResultData = `$mcheckplus_path CNFM $sSiteCode $sSitePw $sResponseSeq $sAuthno $sRequestSeq`;

//echo "결과 : $sResultData";
/*
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

$res = array(
	"getReturnCode" => $res_tmp[0],
	"req_seq"       => $res_tmp[1],
	"res_seq"       => $res_tmp[2],
	"ci"            => $res_tmp[4],			// CI (88byte)
	"di"            => $res_tmp[5]			// DI (64byte) ... 어디에 쓰지???
);

if( $res_tmp[0] == '0000' ) {

	sql_query("
		UPDATE
			cf_auth_nice
		SET
			auth_finish='1'
		WHERE 1
			AND reqseq='".$sRequestSeq."'
			AND rescode='0000'
			AND resseq='".$sResponseSeq."'");

}

echo json_encode($res, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);


?>