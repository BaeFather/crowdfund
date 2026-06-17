<?

include_once("_common.php");
include_once(G5_PATH . '/useb/useb.config.php');
include_once(G5_PATH . '/useb/useb.lib.php');

while( list($k, $v) = each($_POST) ) { ${$k} = trim($v); }

//$OCRLOG = sql_fetch("SELECT * FROM useb_ocr_log WHERE mb_no='".$member['mb_no']."' ORDER BY regdate DESC LIMIT 1");


$OCRUserName  = sql_real_escape_string($OCRUserName);
$OCRIssueDate = sql_real_escape_string($OCRIssueDate);


if( $OCRIdType == '' || !in_array($OCRIdType, array('1','2')) ) {
	$ARR = array('result' => 'fail', 'message' => '신분증 구분 정보가 없습니다..');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}
if($OCRUserName == '') {
	$ARR = array('result' => 'fail', 'message' => '이름를 확인하여 주세요.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}
if($juminNo1 == '' || strlen($juminNo1) <> 6) {
	$ARR = array('result' => 'fail', 'message' => '주민등록번호 앞 여섯자리를 확인하여 주세요.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}
if($juminNo2 == '' || strlen($juminNo2) <> 7) {
	$ARR = array('result' => 'fail', 'message' => '주민등록번호 뒤 일곱자리를 확인하여 주세요.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}
if($OCRIdType == '2') {
	if($OCRDriverNo =='') {
		$ARR = array('result' => 'fail', 'message' => '면허번호를 확인하여 주세요.');
		echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
	}
}


$juminNo =  $juminNo1.$juminNo2;

if( !checkJumin($juminNo) ) {
	$ARR = array('result' => 'fail', 'message' => "주민등록번호 형식에 맞지 않습니다.");
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); exit;
}

$BIRTH = getBirthGender($juminNo);
$birthDate = preg_replace("/-/", "", $BIRTH[0]);

// 주민등록증
if($OCRIdType == '1') {
	$zzeung_gubun = 'idcard';
	$DATA['identity']  = $juminNo;				// 주민등록번호 예) 8811211056911
	$DATA['issueDate'] = $OCRIssueDate;		// 발급일자 예) 20000301
	$DATA['userName']  = $OCRUserName;		// 이름 예) 홍길동
}
// 운전면허증
else if($OCRIdType == '2') {
	$zzeung_gubun = 'driver';
	$DATA['juminNo']   = $juminNo;				// 주민등록번호 유효성 검사 예) 9211211056915
	$DATA['userName']  = $OCRUserName;		// 이름 예) 홍길동
	$DATA['birthDate'] = $birthDate;			// 생년월일 예) 19821120
	$DATA['licenseNo'] = $OCRDriverNo;		// 운전면허번호 예) 11-16-044391-61
}


// 정상인 경우 신분증 진위여부확인API 실행
$IDENTIFY = useb_idcard_identify($zzeung_gubun, $DATA);
//print_r($IDENTIFY); exit;

if($IDENTIFY['RESULT']=='1') {

	// OCR 및 진위여부 결과 로그
	$sql = sql_query("UPDATE useb_ocr_log SET status='1' WHERE mb_no='".$member['mb_no']."' AND order_id='".$order_id."'");

	$private_num_enc = masterEncrypt($juminNo, false);

	$ARR = array(
		'result' => 'success',
		'private_num_enc' => $private_num_enc,
		'message' => '신분증 진위여부 정상'
	);

}
else {
	$ARR = array('result' => 'fail', 'message' => "진위판독불가: ".$IDENTIFY['error_message']);
}

echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);


sql_close();
exit;

?>