<?
###############################################################################
## 상환일정조회
###############################################################################

include_once("../../syndication_config.php");
//include_once($syndi_base_path . "/inc_jsonPrint_head.php");
//include_once($syndi_base_path . "/inc_memberCheck.php");


/*
$REQUEST['data']['memberNumber']	핀크회원번호
$REQUEST['data']['requestYearMonth'] 요청년월(yyyymm)
*/



// 테스트
$REQUEST['data']['isTest'] = '1';
$MB = get_member('delict1@nate.com');
$REQUEST['data']['requestYearMonth'] = '202103';


$requestYearMonth = substr($REQUEST['data']['requestYearMonth'], 0, 4) . "-" . substr($REQUEST['data']['requestYearMonth'], 4, 2);

// 이자정산지급일에 따른 세율
$CONF['interest_tax_ratio'] = 0.25;		// 이자소득세 : 25%
$CONF['local_tax_ratio']    = 0.1;		// 지방세: 이자소득세의 10% => 합계 27.5%
/*
if($BILL['schedule_date'] >= '2020-01-01') {
	$CONF['interest_tax_ratio'] = 0.14;		// 이자소득세 : 14%
	$CONF['local_tax_ratio']    = 0.1;		// 지방세: 이자소득세의 10% => 합계 15.4%
}
*/


//상환스케줄목록
$ARR['data']['repayScheduleList'] = array();



$sql = "
	SELECT
		A.idx, A.product_idx, A.amount, A.prin_rcv_no, A.insert_date,
		B.state AS product_state, B.loan_start_date, B.loan_end_date, B.loan_interest_rate, B.repay_type, B.invest_return, B.invest_days, B.overdue_rate, B.withhold_tax_rate, B.category, B.title
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.member_idx='".$MB['mb_no']."'
		AND A.invest_state='Y'
		AND (
			( B.state='1' AND (LEFT(B.loan_start_date,7) <= '".$requestYearMonth."' AND LEFT(B.loan_end_date,7) >= '".$requestYearMonth."') )
			OR ( B.state IN('2','5') AND (LEFT(B.loan_start_date,7) <= '".$requestYearMonth."' AND LEFT(B.loan_end_date,7) >= '".$requestYearMonth."') )
			OR ( B.state='8' AND LEFT(B.loan_start_date,7) < '".$requestYearMonth."' )
			OR ( B.state IN('4','9') AND LEFT(B.loan_start_date,7) < '".$requestYearMonth."' )
		)
	ORDER BY
		A.idx DESC";
$res  = sql_query($sql);
$rows = sql_num_rows($res);
//echo $sql; exit;

for($i=0; $i<$rows; $i++) {

	$INVEST = sql_fetch_array($res);
	//print_r($INVEST);

	$shortTermProduct = ($INVEST['invest_days']>0) ? 1 : 0;
	$repayTotalTurn = repayTurnCount($INVEST['loan_start_date'], $INVEST['loan_end_date'], 0, $shortTermProduct);

	// 상환방법코드
	if($INVEST['repay_type']=='2')      $repayMethodCode = 'LEVEL_PAY';		// 원리금균등
	else if($INVEST['repay_type']=='3') $repayMethodCode = 'ETC_PAY';			// 기타
	else $repayMethodCode = 'BULLET_PAY';																	// 만기일시


	// 결제예정금액 추출
	$billTable = getBillTable($INVEST['product_idx']);

	$billSql = "
		SELECT
			product_idx, turn, turn_sno,
			repay_date AS schedule_date,
			partial_principal, remain_principal,
			IFNULL(SUM(day_interest),0) AS invest_interest,
			IFNULL(SUM(fee),0) AS fee
		FROM
			{$billTable}
		WHERE 1
			AND invest_idx='".$INVEST['idx']."'
			AND is_overdue='N'
			AND LEFT(repay_date,7)='".$requestYearMonth."'
		GROUP BY
			turn, turn_sno
		ORDER BY
			turn ASC, turn_sno ASC";
	$billRes = sql_query($billSql);
	while( $BILL = sql_fetch_array($billRes) ) {

		$BILL['invest_interest'] = floor(customRoundOff($BILL['invest_interest']));
		$BILL['fee']             = floor(customRoundOff($BILL['fee']));

		$giveSql = "
			SELECT
				idx, is_creditor, principal, interest, fee,
				LEFT(banking_date,10) AS banking_date
			FROM
				cf_product_give
			WHERE 1
				AND invest_idx='".$INVEST['idx']."'
				AND turn='".$BILL['turn']."' AND turn_sno='".$BILL['turn_sno']."'
				AND is_overdue='N'
				AND account_num!='' AND banking_date IS NOT NULL";
		$PAID = sql_fetch($giveSql);


		$repayDate = ($PAID['banking_date']) ? $PAID['banking_date'] : getUsableDate($BILL['schedule_date']);		// 상환일 (기상환일 또는 예정상환일)

		////////////////////////////////////////
		// 상환상태코드
		////////////////////////////////////////
		// $repayStateCode
		//   REPAY_EXPECT    상환예정
		//   REPAY_COMPLETE  상환완료
		//   REPAY_OVERDUE   상환연체
		//   REPAY_UNABLE    부도상품(상환불가처리)
		////////////////////////////////////////
		$repayStateCode = "";
		if(date('Y-m-d') <= $repayDate) {
			$repayStateCode = "REPAY_EXPECT";					// 상환예정
		}
		else {

			if($PAID['idx']) {
				$repayStateCode = "REPAY_COMPLETE";			// 상환완료
			}
			else {
				if( in_array($INVEST['product_state'],array('4','9')) ) {
					$repayStateCode = "REPAY_UNABLE";			// 상환불가
				}
				else {
					$repayStateCode = 'REPAY_OVERDUE';		// 상환연체
				}
			}

		}


		$repay_principal = $repay_interest = $interest_tax = $local_tax = $tax = $interest = 0;

		// 상환예정원금
		if($BILL['turn']==$repayTotalTurn) {
			$repay_principal = $BILL['remain_principal'];
		}

		// 상환예정이자
		$interest_tax = floor( ( ($BILL['invest_interest'] * $CONF['interest_tax_ratio']) / 10) ) * 10;		// 당월 이자소득세 = 이자수익 * 0.25
		$local_tax    = floor( ( ($interest_tax * $CONF['local_tax_ratio']) / 10) ) * 10;									// 당월 지방소득세(원단위 절사)
		$tax          = $interest_tax + $local_tax;
		$interest     = $BILL['invest_interest'] - $tax - $BILL['fee'];		// 세후이자

		//echo $repay_principal . " + " . $BILL['invest_interest'] . " - " . $tax . " - " . $BILL['fee'] . " = " . ($repay_principal + $interest) . "<br>\n";

		$repayCompleteAmount = $PAID['principal'] + $PAID['interest'];									// 상환완료금액 : 상환완료원금 + 상환완료이자(세후)
		$repayOverdueAmount  = $OVD_REPAY_SUM['interest'] - $OVD_PAIED['interest'];			// 상환연체금액 :	연체상환원금 + 이자금액 - 이자소득세금 - 수수료 - 보험료 + (연체이자금액 - 연체이자소득세금)
		$repayExpectAmount   = ($repay_principal + $interest) - $repayCompleteAmount;		// 상환예정금액 : 상환예정원금 + 상환예정이자(세후) (상환연체금액 제외)

		$TMP = array(
			'institutionInvestNumber' => $INVEST['idx'],
			'productNumber'           => $INVEST['product_idx'],
			'repayTurn'               => (int)$BILL['turn'],
			'repayTotalTurn'          => $repayTotalTurn,
			'productName'             => $INVEST['title'],
			'repayDate'               => preg_replace("/\-/", "", $repayDate),
			'repayMethodCode'         => $repayMethodCode,
			'repayStateCode'          => $repayStateCode,
			'repayCompleteAmount'     => $repayCompleteAmount,
			'repayOverdueAmount'      => $repayOverdueAmount,
			'repayExpectAmount'       => $repayExpectAmount
		);

		//$TMP['state'] = $INVEST['product_state'];
		//$TMP['loan_start_date'] = $INVEST['loan_start_date'];
		//$TMP['loan_end_date'] = $INVEST['loan_end_date'];

		array_push($ARR['data']['repayScheduleList'], $TMP);

	}

}

print_rr($ARR,'font-size:12px'); exit;
echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);


##############################
## 최종출력처리
##############################
//include_once($syndi_base_path . "/inc_jsonPrint_tail.php");


sql_close();
exit;

?>