<?
###############################################################################
## 핀크 TEST CURL 전문 생성기
###############################################################################

$base_path = "/home/crowdfund/public_html";
$syndi_base_path = $base_path . "/syndicate/finnq";

include_once($base_path . "/common.php");

$_CONF['reqPubKey'] = file_get_contents($syndi_base_path . '/keys/hello_rsa_pub.20180627.pem');  //헬로 공개키
//$_CONF['rstPriKey'] = file_get_contents($syndi_base_path . '/keys/finnq_rsa_stg_20180426.pri');  //핀크 개인키


include_once($syndi_base_path . '/request_test/test_local_common.lib.php');
include_once($syndi_base_path . '/request_test/test_crypt.lib.php');


$ARR['head'] = array(
  'requestInstitutionCode'  => 'FNNQ',
  'responseInstitutionCode' => 'HLLO',
	'requestHash'             => ''
);
$ARR['data'] = array();
$ARR['finnq']['data_end'] = true;	// 데이터절 분기를 위해 임의 생성

$ARR['data']['requestTimestamp'] = (string)milliseconds();



/*
// 회원가입 -----------------------------------------------------------------------
$request_url      = "https://www.hellofunding.co.kr/api/member/join";

$memberNumber    = "C444444";
$memberName      = "배재수";
$jumin           = "7509031114220";
$email           = "sori9th@aaaa.com";
$bankCode        = "";
$bankAcctNo      = "";
$telephoneNumber = "01064063972";
$connectingInformation = "";

$key = time();
$encryptionKey          = rsa_encrypt(base64_encode($key));			// $key = base64_decode(rsa_decrypt($REQUEST['data']['encryptionKey']));
$personalSecurityNumber = aes128_encrypt($key, $jumin);					// 주민번호암호화. 복호화 ===> aes128_decrypt($REQUEST['data']['personalSecurityNumber'], $key);
$repayAccountNumber     = aes128_encrypt($key, $bankAcctNo);		// 계좌번호암호화. 복호화 ===> aes128_decrypt($REQUEST['data']['repayAccountNumber'], $key)

$ARR['data']['memberNumber']          = $memberNumber;
$ARR['data']['memberName']            = $memberName;
$ARR['data']['personalSecurityNumber']= $personalSecurityNumber;
$ARR['data']['encryptionKey']         = $encryptionKey;
$ARR['data']['basicAddress']          = '';
$ARR['data']['detailAddress']         = '';
$ARR['data']['zipCode']               ='';
$ARR['data']['email']                 = $email;
$ARR['data']['repayAccountBankCode']  = $bankCode;
$ARR['data']['repayAccountNumber']    = $repayAccountNumber;
$ARR['data']['telephoneNumber']       = $telephoneNumber;
$ARR['data']['connectingInformation'] = $connectingInformation;
$ARR['data']['marketingReceiveAgreeYnList'][] = array('marketingTypeCode'=>'EMAIL','agreeYn'=>'Y');
$ARR['data']['marketingReceiveAgreeYnList'][] = array('marketingTypeCode'=>'PUSH','agreeYn'=>'Y');
$ARR['data']['marketingReceiveAgreeYnList'][] = array('marketingTypeCode'=>'SMS','agreeYn'=>'Y');
// 회원가입 -----------------------------------------------------------------------
*/

/*
// 회원정보조회 --------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/member/info";
$memberNumber = "C001242559";
$ARR['data']['memberNumber'] = $memberNumber;
// 회원정보조회 --------------------------------------------------------------------
*/

/*
// 상품리스트 ----------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/product/list";
// 상품리스트 ----------------------------------------------------------------------
*/

/*
// 상품상세조회 --------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/product/detail";
$productNumber = "271";
$memberNumber = "C001242559";
$ARR['data']['productNumber'] = $productNumber;
$ARR['data']['memberNumber'] = $memberNumber;
// 상품상세조회 --------------------------------------------------------------------
*/

/*
// 투자하기 ---------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest/request";
$ARR['data']['memberNumber'] = 'C000201830';
$ARR['data']['investNumber'] = '15000';
$ARR['data']['productNumber'] = '296';
$ARR['data']['investAmount'] = '10000';
// 투자하기 ---------------------------------------------------------------------
*/

// 투자통계조회 ---------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest-info/statistics";
$ARR['data']['memberNumber'] = 'C000388339';
// 투자통계조회 ---------------------------------------------------------------------


/*
// 투자목록 ----------------------------------------------------------------------
$memberNumber = "C000388339";
$request_url = "https://www.hellofunding.co.kr/api/invest-info/list";
$ARR['data']['memberNumber']            = $memberNumber;
$ARR['data']['investProgressStateCode'] = 'PROGRESS';						// PROGRESS:진행중 | COMPLETE: 종료
$ARR['data']['sortCode']                = 'INVEST_DATETIME';		// INVEST_DATETIME:일순 정렬 | INVEST_AMOUNT:금액순 정렬
$ARR['data']['startNumber']             = '1';
$ARR['data']['requestCount']            = '20';
// 투자목록 ----------------------------------------------------------------------
*/

/*
// 투자상세보기 ---------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest-info/detail";
$ARR['data']['memberNumber'] = 'C001242552';
$ARR['data']['institutionInvestNumber'] = '10945';
// 투자상세보기 ---------------------------------------------------------------------
*/

/*
// 상환일정조회 ---------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest-info/schedules";
$ARR['data']['memberNumber'] = 'C001242552';
$ARR['data']['requestYearMonth'] = '201807';
// 상환일정조회 ---------------------------------------------------------------------
*/

/*
// 투자한도조회 -----------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest-info/limit";
$ARR['data']['memberNumber'] = 'C001242573';
$ARR['data']['productNumber'] = '321';
// 투자집계조회 -----------------------------------------------------------------
*/

/*
// 투자집계조회 -----------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest-aggregate/list";
$ARR['data']['aggregateDate'] = '20180702';
$ARR['data']['pageNumber'] = '1';
$ARR['data']['pageSize'] = '200';
// 투자집계조회 -----------------------------------------------------------------
*/

/*
// 투자철회 ---------------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/invest/cancel";
$ARR['data']['memberNumber'] = 'C001242331';
$ARR['data']['institutionInvestNumber'] = '10772';
// 투자철회 ---------------------------------------------------------------------
*/

/*
// 출금요청 -----------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/withdrawal/request";
$ARR['data']['memberNumber'] = 'C001242559';
$ARR['data']['withdrawAmount'] = '9880000';
// 투자집계조회 -----------------------------------------------------------------
*/

/*
// 월별정산목록조회	-----------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/adjustment/list/monthly";
$ARR['data']['aggregateDate'] = '201810';
$ARR['data']['pageNumber'] = '1';
$ARR['data']['pageSize'] = '100';
// 월별정산목록조회	-----------------------------------------------------------------
*/

/*
// 일별정산목록조회	-----------------------------------------------------------------
$request_url = "https://www.hellofunding.co.kr/api/adjustment/list/daily";
$ARR['data']['aggregateDate'] = '20181001';
$ARR['data']['pageNumber'] = '1';
$ARR['data']['pageSize'] = '100';
// 일별정산목록조회	-----------------------------------------------------------------
*/

$ARR['data']['isTest'] = '1';


$ARR['head']['requestHash'] = resultSignature($ARR);		// 최종 사이닝

//$verify_result = openssl_verify($requestData, base64_decode($REQUEST['head']['requestHash']), $_CONF['reqPubKey'], "SHA256");

//echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES) . "\n\n"; exit;


$json_str = printJson($ARR);

echo "-------------------------------------------------------------------------------\n";
$exec_phrase = "curl -X POST -H \"Content-Type:application/json\" -d '";
$exec_phrase.= $json_str;
$exec_phrase.= "' " . $request_url;
echo $exec_phrase."\n-------------------------------------------------------------------------------\n";
//echo exec($exec_phrase);

?>