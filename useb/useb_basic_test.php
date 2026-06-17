<?

include_once('_common.php');

include_once(G5_PATH . '/useb/useb.config.php');
include_once(G5_PATH . '/useb/useb.lib.test.php');

echo "USEB : ";
print_rr($USEB, 'color:#AAA');

/*
echo "Oauth - Client ID, Client Secret 조회 : ";
$usebClientID = usebGetClientID();
print_rr($usebClientID, 'color:#FF2222');
echo "-----------------------------------------------------------------------------------<br>\n";
exit;
*/

/*
echo "토큰 생성(OCR, 마스킹, 진위확인, 1원계좌인증, 안면인증) : ";
$usebToken = usebGetToken();		// OAUTH - 토큰생성 / 로그기록: useb_token
print_rr($usebToken, 'font-size:12px;');
echo "-----------------------------------------------------------------------------------<br>\n";
exit;
*/

/*
echo "RSA2048 공개키 비밀키 생성 : ";
$CRES = usebGenerateKeyPair();
print_rr($CRES, 'font-size:12px;');
echo "-----------------------------------------------------------------------------------<br>\n";
exit;
*/

/*
echo "공개키 등록";
$CRES = usebRegisterPublicKey();
print_rr($CRES, 'font-size:12px;');
echo "-----------------------------------------------------------------------------------<br>\n";
exit;
*/

/*
echo "키교환 : ";
$CRES = usebExchangeKeys();
print_rr($CRES, 'font-size:12px');
echo "-----------------------------------------------------------------------------------<br>\n";
exit;
*/

/*
echo "대칭키추출 : ";
$CRES = usebExtractSymKey();
print_rr($CRES, 'font-size:12px');
echo "-----------------------------------------------------------------------------------<br>\n";
//exit;
*/

/*
echo "대칭키 암호화 : ";
$plaintext = '황다빈';
$ciphertext = usebEncrypt($plaintext);
echo $plaintext . " ::::::::::::::: " . $ciphertext  . "<br>\n";

//$ciphertext = "8f789e8e577fcad9c++CsGg1lM+dFZZkF/9aaQ==";
echo "대칭키 복호화 : ";
$plaintext = usebDecrypt($ciphertext);
echo $ciphertext . " ::::::::::::::: " . $plaintext . "<br>\n";
echo "-----------------------------------------------------------------------------------<br>\n";
//exit;
*/

/*
echo "신분증OCR : ";
$gubun = 'driver';
$upload_file_path = "/home/crowdfund/public_html/data/kyc_tmp/test.png";
$OCR = usebOCR($gubun, $upload_file_path);
print_rr($OCR);


echo "신분증 진위여부";
//$gubun = 'driver';

$juminNo = $OCR['juminNo1'].$OCR['juminNo2'];
$BIRTH = getBirthGender($juminNo);
$birthDate = preg_replace("/-/", "", $BIRTH[0]);

$udata['juminNo']     = $juminNo;							// 주민등록번호 유효성 검사 예) 9211211056915
$udata['userName']    = $OCR['userName'];			// 이름 예) 홍길동
$udata['birthDate']   = $birthDate;						// 생년월일 예) 19821120
$udata['licenseNo']   = $OCR['driverNo'];			// 운전면허번호 예) 11-16-044391-61
$udata['secret_mode'] = true;									// AES256 암호화 적용 예) true, false (암호화 적용시 암호화 가이드를 반드시 숙지하고 테스트 하시기 바랍니다)

$RESULT = useb_idcard_identify($gubun, $udata);
print_rr($RESULT, 'font-size:12px');


echo "신분증 마스킹 : ";
$gubun = "driver";		// 주민증:idcard, 운전면허증:driver
if(!$upload_file_path) $upload_file_path = "/home/crowdfund/public_html/data/kyc_tmp/test.png";

$RESULT = useb_idcard_mask($gubun, $upload_file_path);
print_rr($RESULT,'color:brown');

if($RESULT['RESULT']=='1') {

	$image_source = base64_decode(usebDecrypt($RESULT['image_base64_mask']));

	$image_save_path = "/home/crowdfund/public_html/data/kyc_tmp/test2.png";
	file_put_contents($image_save_path, $image_source);

	$image_save_url = "/data/kyc_tmp/test2.png";
	echo "<img src='".$image_save_url."'>";

}
*/

?>