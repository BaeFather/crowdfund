<?

include_once('_common.php');

include_once(G5_LIB_PATH . '/settlebank.lib.php');


$bank_code = '020';
$acnt_no   = '1002859732587';
$mchtCustNm = '';

echo "예금주명 확인: ";
$RESULT = stbkAcntOwnerCheck($bank_code, $acnt_no, $mchtCustNm);
print_rr($RESULT,'font-size:12px');
echo "<hr/>\n";


/*
echo "계좌점유인증 요청: ";
$bankCd = '020';
$custAcntNo ='1002859732587';
$acntOwnerName = '배재수';

$RESULT = stbkAuthRequest($bankCd, $custAcntNo, $acntOwnerName);
print_rr($RESULT,'font-size:12px');
echo "<hr/>\n";
*/

/*
echo "계좌점유인증 확인";
$orgMchtTrdNo = 'HF20211015M817T151616';
$orgTrdNo = 'STFP_FIRMM219404100211015151616M1131866';
$authNo = '4757';
$RESULT = stbkAuthCheck($orgMchtTrdNo, $orgTrdNo, $authNo);
print_rr($RESULT,'font-size:12px');
echo "<hr/>\n";
*/

/*
echo "계좌점유인증 내역 조회: ";
$orgTrdDt = '20211015';
$orgMchtTrdNo = 'HF20211015M817T151616';
$orgTrdNo = 'STFP_FIRMM219404100211015151616M1131866';
$RESULT = stbkAuthTransList($orgTrdDt, $orgMchtTrdNo, $orgTrdNo);
print_rr($RESULT,'font-size:12px');
echo "<hr>";
*/

/*
echo "은행 점검시간 확인: ";
$RESULT = stbkbankTimeCheck('020');
print_rr($RESULT,'font-size:12px');
echo "<hr>";
*/


/*
$CONF['STLBANK']['TEST']['authkey'] = 'SETTLEBANKISGOODSETTLEBANKISGOOD';		// 개인정보 암호키 (32byte)

$key  = $CONF['STLBANK']['TEST']['authkey'];
$pText = "배재수";

$cText = aes256ECBEncrypt($key, $pText);
echo $cText;

$pText = aes256ECBDecrypt($key, $cText);
echo $pText;
*/


?>