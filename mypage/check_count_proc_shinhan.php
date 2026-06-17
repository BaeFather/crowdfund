<?
///////////////////////////////////////////////////////////////////////////////
// 계좌 예금주 확인 (신한모듈)
///////////////////////////////////////////////////////////////////////////////

include_once('_common.php');
include_once(G5_LIB_PATH . '/insidebank.lib.php');

while(list($k, $v)=each($_REQUEST)) { ${$k} = trim($v); }

//print_r($_POST); exit;


if(!$strBankCode || !$strAccountNo) {
	$ResultCD = 'XXXX';
	$Msg = '필수데이터 누락';
}
else {

	// 수취인조회(4000, 예금주명 리턴)
	$ARR['REQ_NUM'] = '040';
	$ARR['BANK_CD'] = $strBankCode;
	$ARR['ACCT_NB'] = $strAccountNo;
	$insidebank_result = insidebank_request('256', $ARR);

}

//echo "응답코드   : " . $insidebank_result['RCODE'] . "<br>";
//echo "응답메시지 : " . $insidebank_result['ERRMSG'] . "<br>";
//echo "주문번호   : " . $insidebank_result['FB_SEQ'] . "<br>";
//echo "예금주명   : " . $insidebank_result['ACCT_OWNER_NM'] . "<br>";

echo json_encode($insidebank_result, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

sql_close();
exit;

?>