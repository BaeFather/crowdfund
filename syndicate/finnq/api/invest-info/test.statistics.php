<?
###############################################################################
## 투자통계조회
###############################################################################

include_once("../../syndication_config.php");
//include_once($syndi_base_path . "/inc_jsonPrint_head.php");		// 테스트시 주석처리
//include_once($syndi_base_path . "/inc_memberCheck.php");			// 테스트시 주석처리
include_once(G5_LIB_PATH . "/repay_calculation.php");



// 테스트
$REQUEST['data']['isTest'] = '1';
$MB = get_member('delict1@nate.com');



$ARR['data']['averageBeforeTaxProfitRate'] = '0';		// 평균세전 수익율
$ARR['data']['averageAfterTaxProfitRate']  = '0';		// 평균세후 수익율
$ARR['data']['deposit'] = $MB['mb_point'];					// 예치금

// 출금진행중금액 (출금요청하였으나 아직 출금하지 않은 금액)
$row = sql_fetch("SELECT IFNULL(SUM(req_price), 0) AS sum_req_price FROM g5_withdrawal WHERE mb_id='".$MB['mb_id']."' AND state='1'");
$ARR['data']['withdrawProgressAmount'] = ($row['sum_req_price'] > 0) ? $row['sum_req_price'] : 0;


// 투자한도 목록
$ARR['data']['investLimitList'] = array(
	array('investTargetTypeCode'=>'REAL_ESTATE', 'remainLimitAmount'=>$MB['invest_possible_amount_prpt']), // 부동산
	array('investTargetTypeCode'=>'EXCLUDE_REAL_ESTATE', 'remainLimitAmount'=>$MB['invest_possible_amount']), // 부동산외
);


$ARR['data']['waiting']  = array();		// 모집중인 상품에 대한 투자건 배열선언

// 진행중인 투자건 배열선언 (이자상환중)
$ARR['data']['progress'] = array(
	'investAmount'           => 0,
	'investCount'            => 0,
	'overdueCount'           => 0,
	'interestAfterTaxAmount' => 0,
	'repayCompleteAmount'    => 0,
	'repayOverdueAmount'     => 0,
	'repayExpectAmount'      => 0,
	'repayTotalAmount'       => 0
);

// 종료된 투자건 배열선언 (상환완료 및 부실, 기표 후 취소)
$ARR['data']['complete'] = array(
	'investAmount'           => 0,
	'investCount'            => 0,
	'defaultCount'           => 0,
	'interestAfterTaxAmount' => 0,
	'repayCompleteAmount'    => 0,
	'defaultPrincipal'       => 0
);


$where = " 1 ";
$where.= " AND A.member_idx='".$MB['mb_no']."' ";
$where.= " AND B.display='Y' ";



///////////////////////////////////////////////////////////////////////////////
// 1. 모집중인 상품에 대한 투자 합산내역
// ::: waiting
///////////////////////////////////////////////////////////////////////////////
$whereA = $where . " AND B.state='' AND A.invest_state='Y' ";

$sqlA = "
	SELECT
		COUNT(A.idx) AS cnt_idx,
		IFNULL(SUM(A.amount),0) AS sum_amount
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE
		$whereA";
$ROW  = sql_fetch($sqlA);
if($isTest=='1') { echo $sqlA."\n"; }

$ARR['data']['waiting']['investAmount'] = (int)$ROW['sum_amount'];
$ARR['data']['waiting']['investCount']  = (int)$ROW['cnt_idx'];

$TMP_SUM = array(
	'invest_amount'       => 0,
	'invest_interest'     => 0,
	'tax'                 => 0,
	'fee'                 => 0,
	'exclude_amount'      => 0,		// 제외할 금액(세전) (부도처리된 상품일 경우)
	'exclude_amount_real' => 0		// 제외할 금액(세후)
);



///////////////////////////////////////////////////////////////////////////////
// 진행중인(대출상품의 최종종료처리가 되지 않은) 투자건 통계
// :::  progress ( 1:이자상환중|4:부실(매각처리중)|8:연채 )
///////////////////////////////////////////////////////////////////////////////
$whereB = $where . " AND B.state IN ('1','4','8') AND A.invest_state='Y' ";

$sqlB = "SELECT A.product_idx FROM cf_product_invest A LEFT JOIN cf_product B ON A.product_idx=B.idx WHERE $whereB ORDER BY A.idx DESC";
$res  = sql_query($sqlB);
if($isTest=='1') { echo $sqlB."\n"; }
while($row = sql_fetch_array($res)) {
	$REPAYING[]	= $row['product_idx'];
}
sql_free_result($res);
//print_r($REPAYING);

for($x=0; $x<count($REPAYING); $x++) {

	$INV_ARR = repayCalculation($REPAYING[$x], $MB['mb_id']);
	$INVEST        = $INV_ARR['INVEST'];
	$PRDT          = $INV_ARR['PRDT'];
	$REPAY_SUM     = $INV_ARR['REPAY_SUM'];
	$PAIED_SUM     = $INV_ARR['PAIED_SUM'];
	$OVD_REPAY_SUM = $INV_ARR['OVD_REPAY_SUM'];
	$OVD_PAIED_SUM = $INV_ARR['OVD_PAIED_SUM'];

	$SUM['investAmount']           += $INVEST[0]['amount'];																																			// 투자금액
	$SUM['investCount']            += 1;																																												// 투자건수
	$SUM['overdueCount']           += ($PRDT['state']=='8') ? 1 : 0;																														// 연체건수 (진행중인 투자 중 연체 총 건수)
	$SUM['interestAfterTaxAmount'] += $REPAY_SUM['interest'] + $OVD_REPAY_SUM['interest'];																			// 예상세후이자 : 세후정상이자 + 세후연체이자

	$repayCompleteAmount = $PAIED_SUM['repay_principal'] + $PAIED_SUM['interest'] + $OVD_PAIED_SUM['interest'];

	$SUM['repayCompleteAmount']    += $repayCompleteAmount;																																		// 상환완료금액 : 상환완료원금 + 상환완료세후이자 + 상환완료세후연체이자
	$SUM['repayOverdueAmount']     += ($PRDT['state']=='') ? 0 : $OVD_REPAY_SUM['interest'] - $OVD_PAIED_SUM['interest'];			// 상환연체금액 : 연체상환원금 + 세후이자 + 세후연체이자
	$SUM['repayExpectAmount']      += ($REPAY_SUM['repay_principal'] + $REPAY_SUM['interest']) - $repayCompleteAmount;				// 상환예정금액 : 상환예정원금 + 상환예정이자(세후) (상환연체금액 제외)
	$SUM['repayTotalAmount']       += $REPAY_SUM['repay_principal'] + $REPAY_SUM['interest'] + $OVD_REPAY_SUM['interest'];		// 전체예상상환금액 : 진행중 투자건의 (상환원금 + 세후이자 + 세후연체이자)

	$ARR['data']['progress']['investAmount']           = $SUM['investAmount'];
	$ARR['data']['progress']['investCount']            = $SUM['investCount'];
	$ARR['data']['progress']['overdueCount']           = $SUM['overdueCount'];
	$ARR['data']['progress']['interestAfterTaxAmount'] = $SUM['interestAfterTaxAmount'];
	$ARR['data']['progress']['repayCompleteAmount']    = $SUM['repayCompleteAmount'];
	$ARR['data']['progress']['repayOverdueAmount']     = $SUM['repayOverdueAmount'];
	$ARR['data']['progress']['repayExpectAmount']      = $SUM['repayExpectAmount'];
	$ARR['data']['progress']['repayTotalAmount']       = $SUM['repayTotalAmount'];

	$invest_interest = $INVEST[0]['amount'] * $PRDT['invest_return'] / 100;

	$TMP_SUM['invest_amount']   += $INVEST[0]['amount'];
	$TMP_SUM['invest_interest'] += $invest_interest;
	$TMP_SUM['tax']             += $invest_interest * 0.275;
	$TMP_SUM['fee']             += $invest_interest * 0.0012;
	//echo $invest_interest * 0.0012."\n";

	unset($repayCompleteAmount);

}
//array_push($ARR['data']['progress'], $SUM);
unset($INV_ARR); unset($PRDT); unset($INVEST); unset($REPAY_SUM); unset($PAIED_SUM); unset($OVD_REPAY_SUM); unset($OVD_PAIED_SUM); unset($SUM);

//print_rr($TMP_SUM, 'font-size:12px');



///////////////////////////////////////////////////////////////////////////////
// 종료된(대출상품의 최종종료 처리완료) 투자건 통계
// ::: complete	( 2:상환완료(투자종료)|3:투자금모집실패|5:중도상환|6:대출취소(기표전)|7:대출취소(기표후)|9:부도(상환불가) )
///////////////////////////////////////////////////////////////////////////////
$whereC = $where . " AND B.state IN('2','3','5','6','7','9') AND A.invest_state IN('Y','R') ";

$sqlC = "SELECT A.product_idx FROM cf_product_invest A LEFT JOIN cf_product B ON A.product_idx=B.idx WHERE $whereC ORDER BY A.idx ASC";
$res = sql_query($sqlC);		// 대출취소시 투자취소처리 및 예치금으로 환불 되므로 대출취소는 제외
if($isTest=='1') { echo $sqlC."\n"; }
while($row = sql_fetch_array($res)) {
	$ENDED[]	= $row['product_idx'];
}
sql_free_result($res);
//print_r($ENDED);

for($x=0; $x<count($ENDED); $x++) {

	$INV_ARR = repayCalculation($ENDED[$x], $MB['mb_id']);
	$PRDT          = $INV_ARR['PRDT'];
	$INVEST        = $INV_ARR['INVEST'];
	$REPAY_SUM     = $INV_ARR['REPAY_SUM'];
	$PAIED_SUM     = $INV_ARR['PAIED_SUM'];
	$OVD_REPAY_SUM = $INV_ARR['OVD_REPAY_SUM'];
	$OVD_PAIED_SUM = $INV_ARR['OVD_PAIED_SUM'];


	$SUM['investAmount']            += $INVEST[0]['amount'];																																	// 투자금액 (누적 투자금액이 아닌 총 잔여투자원금)
	$SUM['investCount']             += 1;																																											// 투자건수
	$SUM['defaultCount']            += ($PRDT['state']=='9') ? 1 : 0;																													// 부도건수 (종료 투자 중 연체 총 건수)
	$SUM['interestAfterTaxAmount']  += $PAIED_SUM['interest'] + $OVD_PAIED_SUM['interest'];																		// 세후이자금액 : 세후정상이자 + 세후연체이자
	$SUM['repayCompleteAmount']     += $PAIED_SUM['repay_principal'] + $PAIED_SUM['interest'] + $OVD_PAIED_SUM['interest'];		// 상환완료금액	: 상환완료원금 + 상환완료세후이자 + 상환완료세후연체이자
	$SUM['defaultPrincipal']        += ($PRDT['state']=='9') ? $REPAY_SUM['repay_principal'] : 0;															// 부도원금

	$ARR['data']['complete']['investAmount']           = $SUM['investAmount'];
	$ARR['data']['complete']['investCount']            = $SUM['investCount'];
	$ARR['data']['complete']['defaultCount']           = $SUM['defaultCount'];
	$ARR['data']['complete']['interestAfterTaxAmount'] = $SUM['interestAfterTaxAmount'];
	$ARR['data']['complete']['repayCompleteAmount']    = $SUM['repayCompleteAmount'];

	if( in_array($PRDT['state'], array('3','6','7')) ) {
		// 환불액 추출
		$RETURN = sql_fetch("SELECT amount FROM cf_product_invest WHERE member_idx='".$MB['mb_no']."' AND product_idx='".$PRDT['idx']."' AND invest_state='R'");
		$ARR['data']['complete']['repayCompleteAmount'] = $ARR['data']['complete']['repayCompleteAmount'] + $RETURN['amount'];			// repayCompleteAmount 에 환불액을 넣어줌.
	}

	$ARR['data']['complete']['defaultPrincipal']       = $SUM['defaultPrincipal'];


	$invest_interest = $INVEST[0]['amount'] * $PRDT['invest_return'] / 100;
	$tax             = $invest_interest * 0.275;
	$fee             = $invest_interest * 0.0012;

	$TMP_SUM['invest_amount']   += $INVEST[0]['amount'];
	$TMP_SUM['invest_interest'] += $invest_interest;
	$TMP_SUM['tax']             += $tax;
	$TMP_SUM['fee']             += $fee;

	if($PRDT['state']=='9') {
		$TMP_SUM['exclude_amount']      += $INVEST[0]['amount'] + $invest_interest - $PAIED_SUM['invest_interest'];
		$TMP_SUM['exclude_amount_real'] += $INVEST[0]['amount'] + ($invest_interest - $tax - $fee) - $PAIED_SUM['interest'];
	}

}

unset($INV_ARR); unset($PRDT); unset($INVEST); unset($REPAY_SUM); unset($PAIED_SUM); unset($OVD_REPAY_SUM); unset($OVD_PAIED_SUM); unset($SUM);

// 예상수익율(세전, 연체이자불포함)
$ARR['data']['averageBeforeTaxProfitRate'] = @sprintf('%.2f', (($TMP_SUM['invest_interest'] - $TMP_SUM['exclude_amount']) / $TMP_SUM['invest_amount']) * 100);

// 예상수익율(세후, 연체이자불포함)
$ARR['data']['averageAfterTaxProfitRate']  = @sprintf('%.2f', (($TMP_SUM['invest_interest'] - $TMP_SUM['tax'] - $TMP_SUM['fee'] - $TMP_SUM['exclude_amount_real']) / $TMP_SUM['invest_amount']) * 100);


print_rr($TMP_SUM, 'font-size:12px');

echo "예상수익율(세전) : (({$TMP_SUM['invest_interest']} - {$TMP_SUM['exclude_amount']}) / {$TMP_SUM['invest_amount']}) * 100 = {$ARR['data']['averageBeforeTaxProfitRate']}<br/>\n";
echo "예상수익율(세후) : (({$TMP_SUM['invest_interest']} - {$TMP_SUM['tax']} - {$TMP_SUM['fee']} - {$TMP_SUM['exclude_amount_real']}) / {$TMP_SUM['invest_amount']}) * 100 = {$ARR['data']['averageAfterTaxProfitRate']}\n";




##############################
## 최종출력처리
##############################
print_rr($ARR, 'font-size:12px');
//include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

sql_close();
exit;

?>