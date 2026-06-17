<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/checkJuminNo.do
## 4. 주민번호 유효성 검사
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

//$REQUEST['user_nm'];				// 이름
//$REQUEST['jumin_no'];				// 주민번호 (AES256)

$jumin_no = preg_replace("/(-| )/", "", $crypto->deCrypt($REQUEST['jumin_no']));			// 복호화
if(strlen($jumin_no)!=13) { $ARR = array('code'=>'9999', 'message'=>'주민번호 오류입니다.'); echo printJson($ARR); exit; }

if(checkJumin($jumin_no)) {
	$ARR['code'] = "0000";
	$ARR['msg'] = "정상처리되었습니다.";
}
else {
	$ARR['code'] = "9999";
	$ARR['msg'] = "유효하지 않은 주민번호 입니다.";
}

##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>