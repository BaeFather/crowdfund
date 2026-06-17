<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/investCancel.do
## 15. 투자취소
##		- APRO에서 투자취소를 하고 싶을 때 투자상태가 모집중일 때 투자취소를 제휴사로 호출
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci']      = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
$REQUEST['prod_cd'] = '2134';
$REQUEST['comp_cd'] = 'CP-2da586e964b4472e8ec65a7cf6f1b5df';
*/

$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }
if($REQUEST['comp_cd'] != $_CONF['comp_cd']) { $ARR = array('code'=>'9999', 'msg'=>'업체코드오류'); echo printJson($ARR); exit; }

$MB = get_member($mb_id);

$sql = "
	SELECT
		A.idx, A.amount, A.invest_state, A.product_idx,
		B.state, B.title, B.invest_end_date, B.recruit_amount,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.product_idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B ON A.product_idx=B.idx
	WHERE 1
		AND A.product_idx = '".$REQUEST['prod_cd']."'
		AND A.member_idx = '".$MB['mb_no']."'
		AND A.syndi_id = '".$_CONF['SYNDI_ID']."'
	ORDER BY
		A.idx DESC
	LIMIT 1";
$INVEST = sql_fetch($sql);
//print_r($INVEST);

if(!$INVEST['idx']) { $ARR = array('code'=>'9999', 'msg'=>'투자내역없음.'); echo printJson($ARR); exit; }
if($INVEST['invest_state'] != 'Y') { $ARR = array('code'=>'9999', 'msg'=>'철회불가(이미 취소처리된 투자건)'); echo printJson($ARR); exit; }
if($INVEST['state'] != '' && $INVEST['invest_end_date'] != '') { $ARR = array('code'=>'9999', 'msg'=>'철회불가(투자종료상품 1)'); echo printJson($ARR); exit; }
if($INVEST['recruit_amount'] <= $INVEST['total_invest_amount']) { $ARR = array('code'=>'9999', 'msg'=>'철회불가(투자종료상품 2)'); echo printJson($ARR); exit; }

$cancel_by = "user-api";

// 투자취소처리
$update_sql1 = "UPDATE cf_product_invest SET invest_state='N', prin_rcv_no='', cancel_date=NOW(), cancel_by='".$cancel_by."' WHERE idx='".$INVEST['idx']."'";
$update_sql2 = "UPDATE cf_product_invest_detail SET invest_state='N', cancel_date=NOW() WHERE invest_idx='".$INVEST['idx']."'";

if( sql_query($update_sql1) ) {

	sql_query($update_sql2);		// 상세투자내역 취소

	$po_content = $INVEST['title']. '-투자 취소';
	insert_point($MB['mb_id'], $INVEST['amount'], $po_content, '@cancel', $MB['mb_id'], $MB['mb_id'].'-'.uniqid(''), 0);		// 투자금액 예치금으로 돌려줌

	////////////////////////////////////////////////////////////////////
	// 상품관리테이블에 실시간 모집금액 반영하기 :: 2021-02-15 추가
	////////////////////////////////////////////////////////////////////
	sql_query("UPDATE cf_product SET live_invest_amount = live_invest_amount - {$INVEST['amount']} WHERE idx = '".$INVEST['product_idx']."'");
	////////////////////////////////////////////////////////////////////


	$MB['mb_point'] = get_point_sum($MB['mb_id']);

	$ARR['code']           = '0000';
	$ARR['msg']            = '정상처리되었습니다,';
	$ARR['cancel_amount']  = $INVEST['amount'];					// 취소투자금액
	$ARR['balance_amount'] = (string)$MB['mb_point'];		// 반환금액 포함된 예치금
	$ARR['cancel_dt']      = (string)date('Ymd');				// 취소일
	$ARR['cancel_tm']      = (string)date('His');				// 취소시간


	// 올리고에 결과 전송 --------------------
	@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/investCancelReport.php " . $INVEST['idx']);
	@shell_exec("/usr/local/php/bin/php -q " . $syndi_base_path . "/report/productStateReport.php " . $INVEST['product_idx']);
	// ---------------------------------------

}
else {

	$ARR = array('code'=>'9999', 'message'=>'DB처리오류'); echo printJson($ARR); exit;

}



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>