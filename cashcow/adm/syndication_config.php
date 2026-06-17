<?

// &APPLICATION_ID=AP001&CORP_KEY=c9b0e0b2395deaab7140074f40da34bb&USER_UNIQUE_KEY=user_unque_key&SESS_KEY=session_key&MESSAGE_ID=AUTH0002&PASSWORD=password

//세션경로 설정할것

/****************************************************
오류코드	오류내용
0					성공
401				인증 실패
402				세션 만료
405				통신 method 오류
406				접근 권한 없음
411				인증 실패
431				필수 항목 누락
450				계좌정보 미등록 회원
451				출금계좌 수정 비밀번호 오류
500~			오류
****************************************************/

ini_set("always_populate_raw_post_data", "-1");

include_once("_common.php");

while(list($k, $v) = each($_REQUEST)) { ${$k} = trim($v); }


$_CONF['SYNDI_URL']   = '';
$_CONF['SYNDI_ID']    = "cashcow";
$_CONF['SYNDI_ID_AS'] = "";
$_CONF['SYNDI_PW']    = "1234!@#";
$_CONF['CORP_KEY']    = md5($_CONF['SYNDI_ID']);
//$_CONF['SESS_ID']   = uniqid('wowstar'.date('ymd'));  // common.php 의 '와우스타 요청에 대한 별도 세션 발행' 파트 참조

?>