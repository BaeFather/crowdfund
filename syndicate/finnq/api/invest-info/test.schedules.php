<?
###############################################################################
## 상환일정조회
###############################################################################

include_once("../../syndication_config.php");
//include_once($syndi_base_path . "/inc_jsonPrint_head.php");
//include_once($syndi_base_path . "/inc_memberCheck.php");
//include_once(G5_LIB_PATH . "/repay_calculation.php");			// (구)라이브러리 (투자시뮬레이션에만 사용)
include_once(G5_LIB_PATH . "/repay_calculation_new.php");		// 기표 후 상품에 적용


/*
$REQUEST['data']['memberNumber']	핀크회원번호
$REQUEST['data']['requestYearMonth'] 요청년월(yyyymm)
*/



// 테스트
$REQUEST['data']['isTest'] = '1';
$MB = get_member('delict1@nate.com');
$REQUEST['data']['requestYearMonth'] = '202102';


$requestYearMonth = substr($REQUEST['data']['requestYearMonth'], 0, 4) . "-" . substr($REQUEST['data']['requestYearMonth'], 4, 2);


//상환스케줄목록
//  진행현황(1:이자상환중|2:상환완료(투자종료)|3:투자금모집실패|4:부실(매각처리중)|5:중도상환|6:대출취소(기표전)|7:대출취소(기표후)|8:연채|9:부도(상환불가))
$ARR['data']['repayScheduleList'] = array();


$sql = "
	SELECT
		A.product_idx, B.state, B.loan_start_date, B.loan_end_date
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.member_idx='".$MB['mb_no']."'
		AND A.invest_state='Y'
		AND B.state IN('1','2','5','8','9')
	ORDER BY
		A.idx DESC";
$res  = sql_query($sql);
//echo $sql; exit;


while($INVESTED = sql_fetch_array($res)) {

	$INV_ARR       = repayCalculationNew($INVESTED['product_idx'], $MB['mb_id']);
	$PRDT          = $INV_ARR['PRDT'];
	$INVEST        = $INV_ARR['INVEST'];
	$REPAY         = $INV_ARR['REPAY'];
	$REPAY_SUM     = $INV_ARR['REPAY_SUM'];
	$PAIED_SUM     = $INV_ARR['PAIED_SUM'];
	$OVD_REPAY_SUM = $INV_ARR['OVD_REPAY_SUM'];
	$OVD_PAIED_SUM = $INV_ARR['OVD_PAIED_SUM'];

	$repay_count = count($REPAY);
	for($i=0,$j=1; $i<$repay_count; $i++,$j++) {

		if(substr($REPAY[$i]['repay_schedule_date'], 0, 7)==$requestYearMonth) {

			$TARGET_REPAY = $REPAY[$i]['LIST'][0];
			$OVERDUE_LIST = $REPAY[$i]['OVERDUE_LIST'][0];



			$TMP['institutionInvestNumber'] = $TARGET_REPAY['invest_idx'];		// 업체고유투자번호
			$TMP['productNumber']           = $INVESTED['product_idx'];				// 상품번호
			$TMP['repayTurn']               = $j;				// 상환회차
			$TMP['repayTotalTurn']          = $repay_count;										// 전체상환회차
			$TMP['productName']             = $PRDT['title'];									// 상품명
			$TMP['repayDate']               = ($TARGET_REPAY['paied']=='Y') ? preg_replace("/(-| |:)/", "", $TARGET_REPAY['paied_date']) : preg_replace("/(-| |:)/", "", $REPAY[$i]['repay_schedule_date']);		// 상환일 (기상환일 또는 예정상환일)

			// 상환방법코드
			switch($PRDT['repay_type']) {
				case '1' : $repayMethodCode = 'BULLET_PAY';			break;		// 만기일시
				case '2' : $repayMethodCode = 'LEVEL_PAY';			break;		// 원리금균등
				case '3' : $repayMethodCode = 'ETC_PAY';				break;		// 기타
			//default  : $repayMethodCode = 'MIX_PAY';				break;		// 혼합
			}
			$TMP['repayMethodCode'] = $repayMethodCode;		// 상환방법코드 (원리금균등:LEVEL_PAY, 만기일시: BULLET_PAY, 혼합: MIX_PAY, 기타: ETC_PAY)

			/*
			// 상환상태코드
			개별 상환회차 기준.
			상환예정: REPAY_EXPECT
			상환연체: REPAY_OVERDUE
			상환완료: REPAY_COMPLETE
			상환불가: REPAY_UNABLE
			*/

			$repayStateCode = '';
			if($TARGET_REPAY['paied']=='Y') {
				$repayStateCode = "REPAY_COMPLETE";		// 상환완료
			}
			else {

				//$repayStateCode = ($REPAY[$i]['repay_date'] >= G5_TIME_YMD) ? 'REPAY_EXPECT' : 'REPAY_OVERDUE';		// 상환예정 : 상환연체

				// 2018-08-06 자동연체표기 추가 -------------------------------------------
				if($REPAY[$i]['repay_schedule_date'] >= G5_TIME_YMD) {
					$repayStateCode = 'REPAY_EXPECT';		// 상환예정
				}
				else {

					$todayTimeStamp = strtotime(G5_TIME_YMD);
					$repayTimeStamp = strtotime($REPAY[$i]['repay_date']);	// 연체구분일은 repay_schedule_date 를 따르고 연체계산시작일은 repay_date 를 따른다.
					$dateDiff = ceil( ($todayTimeStamp - $repayTimeStamp) / 86400 );

					if($dateDiff > 0) {
						$yesterDay = date("Y-m-d", strtotime(G5_TIME_YMD . " -1 day"));
						$yesterDayNo = date("w", strtotime($yesterDay));

						if( in_array($yesterDayNo, array('0','6')) || in_array($yesterDay, $CONF['STATIC_HOLYDAY']) || in_array($yesterDay, $CONF['DYNAMIC_HOLYDAY']) ) {
							$repayStateCode = 'REPAY_EXPECT';			// 상환예정
						}
						else {
							$repayStateCode = 'REPAY_OVERDUE';		// 상환연체
						}
					}

				}
				// 2018-08-06 자동연체표기 추가 -------------------------------------------

				if($PRDT['state']=='9') $repayStateCode = "REPAY_UNABLE";		// 부도상품 상환불가 처리

			}

			$TMP['repayStateCode'] = $repayStateCode;		// 상환상태코드 (개별 상환회차 기준 -  상환예정: REPAY_EXPECT, 상환연체: REPAY_OVERDUE, 상환완료: REPAY_COMPLETE, 상환불가: REPAY_UNABLE)

			$paied_principal = ($TARGET_REPAY['paied']=='Y') ? $TARGET_REPAY['repay_principal'] : 0;
			$paied_interest  = ($TARGET_REPAY['paied']=='Y') ? $TARGET_REPAY['interest'] : 0;


			$TMP['repayCompleteAmount'] = $paied_principal + $paied_interest;																															// 상환완료금액 : 상환완료원금 + 이자금액 - 이자소득세금 - 수수료 - 보험료
			$TMP['repayOverdueAmount']  = $OVD_REPAY_SUM['interest'] - $OVD_PAIED_SUM['interest'];																				// 상환연체금액 :	연체상환원금 + 이자금액 - 이자소득세금 - 수수료 - 보험료 + (연체이자금액 - 연체이자소득세금)
			$TMP['repayExpectAmount']   = ($TARGET_REPAY['repay_principal'] + $TARGET_REPAY['interest']) - $TMP['repayCompleteAmount'];		// 상환예정금액 : 상환예정원금 + 상환예정이자(세후) (상환연체금액 제외)

			$TMP['state']           = $INVESTED['state'];
			$TMP['loan_start_date'] = $INVESTED['loan_start_date'];
			$TMP['loan_end_date']   = $INVESTED['loan_end_date'];

			array_push($ARR['data']['repayScheduleList'], $TMP);

			unset($repayCompleteAmount);

		}
	}

	count($ARR['data']['repayScheduleList']);

	unset($INV_ARR);
	unset($PRDT);
	unset($INVEST);
	unset($REPAY);
	unset($REPAY_SUM);
	unset($PAIED_SUM);
	unset($OVD_REPAY_SUM);
	unset($OVD_PAIED_SUM);
	unset($TARGET_REPAY);
	unset($TMP);

}

print_rr($ARR,'font-size:12px'); exit;
echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES); exit;


##############################
## 최종출력처리
##############################
//include_once($syndi_base_path . "/inc_jsonPrint_tail.php");


sql_close();
exit;

?>