<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/balanceCheck.do
## 11. 예치금 잔액조회
##  shell : curl -X POST -H 'Content-Type:application/json' -d '{"ci":"I8WW04F29MJZjmXsmnnpDZJh1E4T5NEC/xzvQe9tDE8tIrpnuIzviyMGMf9N5j4WQATtFQm3xQjJ1fGGp11m0Q==","comp_cd":"CP-2da586e964b4472e8ec65a7cf6f1b5df"}' https://www.hellofunding.co.kr/external/api/balanceCheck.do
##	prompt: curl -X POST -H "Content-Type:application/json" -d "{\"ci\":\"I8WW04F29MJZjmXsmnnpDZJh1E4T5NEC/xzvQe9tDE8tIrpnuIzviyMGMf9N5j4WQATtFQm3xQjJ1fGGp11m0Q==\",\"comp_cd\":\"CP-2da586e964b4472e8ec65a7cf6f1b5df\"}" https://www.hellofunding.co.kr/external/api/balanceCheck.do
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

//$REQUEST['ci'] = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
//$REQUEST['comp_cd']		// 제휴코드


$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

$MB = get_member($mb_id);


$ARR['code'] = '0000';										// 결과코드
$ARR['msg']  = '정상처리되었습니다.';			// 메세지

$ARR['current_amt']       = (string)$MB['mb_point'];											// 총예치금
$ARR['available_bal_amt'] = (string)$MB['withdrawal_posible_amount'];			// 출금가능금액
$ARR['bank_cd']           = (string)$MB['va_bank_code2'];									// 제휴사 예치금 잔액 충전용 가상계좌정보
$ARR['acc_no']            = (string)$crypto->enCrypt($MB['virtual_account2']);		// 제휴사 예치금 잔액 충전용 가상계좌정보



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>