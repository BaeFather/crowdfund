<?

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

define('SERVER_TIME', time());
define('DATE_YMDHIS', date('Y-m-d H:i:s', SERVER_TIME));
define('DATE_YMD',    substr(DATE_YMDHIS, 0, 10));
define('DATE_HIS',    substr(DATE_YMDHIS, 11, 8));


$inputJSON = file_get_contents('php://input');
//echo "\n\n"; echo $inputJSON; echo "\n\n"; exit;

// 로그기록 시작 --------------------------------------
if( preg_match("/\/api\/member\//i", $_SERVER['REQUEST_URI']) )                $log_table = "finnq_request_member_log";				// 회원가입 요청 로그
else if( preg_match("/\/api\/product\//i", $_SERVER['REQUEST_URI']) )	         $log_table = "finnq_request_product_log";			// 상품조회 요청 로그
else if( preg_match("/\/api\/invest\//i", $_SERVER['REQUEST_URI']) )           $log_table = "finnq_request_invest_log";				// 투자관련 요청 로그
else if( preg_match("/\/api\/invest\-info\//i", $_SERVER['REQUEST_URI']) )     $log_table = "finnq_request_investinfo_log";	// 투자현황 및 정산현황 요청 로그 :: 테이블명주의
else if( preg_match("/\/api\/withdrawal\//i", $_SERVER['REQUEST_URI']) )       $log_table = "finnq_request_withdrawal_log";		// 출금관련 요청 로그
else if( preg_match("/\/api\/adjustment\/list\//i", $_SERVER['REQUEST_URI']) ) $log_table = "finnq_request_adjustment_log";		// 정산목록조회 요청 로그
else	$log_table = "finnq_request_data_log";		// 일반데이터 요청 로그

$input = "curl -X POST -H \"Content-Type:application/json\" -d '" . $inputJSON . "' " . G5_URL . $_SERVER['REQUEST_URI'];
$sql = "
	INSERT INTO
		$log_table
	SET
		ip    = '".$_SERVER['REMOTE_ADDR']."',
		path  = '".$_SERVER['REQUEST_URI']."',
		input = '".sql_real_escape_string($input)."',
		rdate = SYSDATE()";
//if( preg_match("/isTest\=1/", $_SERVER['REQUEST_URI']) ) echo $sql."\n";
$res = sql_query($sql);
$log_idx = sql_insert_id();
// 로그기록 시작 --------------------------------------

$OBJ = json_decode($inputJSON);


while(list($k, $v) = @each($OBJ->head)) {
	if(!is_array($k)) {
		$REQUEST['head'][$k] = $v;   //${$k} = @trim($v);
	}
}

while(list($k, $v) = @each($OBJ->data)) {
	if(!is_array($k)) {
		$REQUEST['data'][$k] = $v;   //${$k} = @trim($v);
	}
}

/*
$REQUEST['head']['requestInstitutionCode']  ===> $OBJ->head['requestInstitutionCode'];	// 요청기관코드
$REQUEST['head']['responseInstitutionCode'] ===> $OBJ->head['responseInstitutionCode'];	// 응답기관코드
$REQUEST['head']['requestHash']             ===> $OBJ->head['requestHash'];							// 요청해시
$REQUEST['head']['requestTimestamp']        ===> $OBJ->data['requestTimestamp'];		    // 요청일시
*/

$requestData = '{' . str_f6($inputJSON, "\"data\":{", "}}") . '}';
//echo $requestData."\n"; exit;


$verify_result = openssl_verify($requestData, base64_decode($REQUEST['head']['requestHash']), $_CONF['reqPubKey'], "SHA256");		// 데이터 검증
/*
실행내용 출력
echo "\n";
echo "openssl_verify(\n";
echo "  ".$requestData."\n";
echo " ,".base64_decode($REQUEST['head']['requestHash'])."\n";
echo " ,".$_CONF['reqPubKey']."\n";
echo " ,\"SHA256\"";
echo "\n);\n";
echo "\n>>>>>>>>>>>>>>>>>>>>>>>> " . $verify_result . "\n";
exit;
*/

$ARR['head'] = array(
  'requestInstitutionCode'  => $REQUEST['head']['requestInstitutionCode'],
  'responseInstitutionCode' => $REQUEST['head']['responseInstitutionCode'],
	'responseHash'            => ''
);

$ARR['data'] = array();
$ARR['hello']['data_end'] = true;	// 데이터절 분기를 위해 임의 생성
$ARR['data']['responseTimestamp'] = milliseconds();
$ARR['head']['responseHash'] = resultSignature($ARR);


if(!$verify_result) {
	$ARR['error'] = array('code'=>'COMMON_HEADER_INVALID', 'message'=>'해시불일치오류입니다.'); echo printJson($ARR); exit;
}


if($_CONF['REQ_IST_CODE'] != $REQUEST['head']['requestInstitutionCode']) {
	$ARR['error'] = array('code'=>'COMMON_HEADER_INVALID', 'message'=>'요청기관코드 오류입니다.'); echo printJson($ARR); exit;
}


if($_CONF['RST_IST_CODE'] != $REQUEST['head']['responseInstitutionCode']) {
	$ARR['error'] = array('code'=>'COMMON_HEADER_INVALID', 'message'=>'응답기관코드 오류입니다.'); echo printJson($ARR); exit;
}

/*
if($REQUEST['data']['isTest']!='1' && $_REQUEST['isTest']!='1') {
	$requestTimestamp = floor($REQUEST['data']['requestTimestamp'] / 1000);
	$elapsedTime = G5_SERVER_TIME - $requestTimestamp;
	//echo $elapsedTime."\n";
	if($elapsedTime > 300) {
		$ARR['error'] = array('code'=>'COMMON_HEADER_INVALID', 'message'=>'제한시간초과요청 오류입니다.'); echo printJson($ARR); exit;
	}
}
*/

?>