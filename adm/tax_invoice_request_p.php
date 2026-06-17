<?
###############################################################################
## 현금영수증 발행
###############################################################################

set_time_limit(0);

include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출
include_once(TAX_INVOICE_PATH . '/hellofunding/common.php');		// 링크허브(팝빌) 세금계산서발행 API용 설정
include_once(TAX_INVOICE_PATH . '/Popbill/PopbillCashbill.php');		// 현금영수증 발행용(개인) 라이브러리


$CashbillService = new CashbillService($LinkID, $SecretKey);
$CashbillService->IsTest($test_mode);			// 연동환경 설정값, 개발용(true), 상업용(false)


$prd_idx = trim($_REQUEST['idx']);
$turn    = trim($_REQUEST['turn']);
$overdue = trim($_REQUEST['overdue']);

if($test_mode) {
	///////////////////////////
	// 테스트용 자료
	///////////////////////////
	$INI['repay_count'] = 1;
	$PRDT['invest_usefee']='1.20';
	$REPAY[0]['repay_num']    = 1;
	$REPAY[0]['repay_date']   = '2018-01-05';
	$REPAY[0]['target_sdate'] = '2017-12-22';
	$REPAY[0]['target_edate'] = '2017-12-31';
	$REPAY[0]['day_count'] = 10;
	$REPAY[0]['LIST'] = array(
			0=>array('invest_idx'=>'1115', 'mb_no'=>'228', 'mb_id'=>'akorea', 'mb_name'=>'(주)에이코리아대부', 'jumin'=>'6178182380', 'member_type'=>'2', 'remit_fee'=>'', 'invest_usefee'=>10000, 'paied'=>'Y', 'paied_date'=>'2018-01-05 12:00:00'),
			1=>array('invest_idx'=>'1111', 'mb_no'=>'817', 'mb_id'=>'sori9th', 'mb_name'=>'배재수', 'jumin'=>'7509031114220', 'member_type'=>'1', 'remit_fee'=>'', 'invest_usefee'=>5500, 'paied'=>'Y', 'paied_date'=>'2018-01-05 12:00:00'),
			2=>array('invest_idx'=>'1112', 'mb_no'=>'74', 'mb_id'=>'yr4msp', 'mb_name'=>'이정환', 'jumin'=>'7808021019318', 'member_type'=>'1', 'remit_fee'=>'', 'invest_usefee'=>6000, 'paied'=>'Y', 'paied_date'=>'2018-01-05 12:00:00'),
			3=>array('invest_idx'=>'1113', 'mb_no'=>'107', 'mb_id'=>'hellosiesta', 'mb_name'=>'이상규', 'jumin'=>'8509091904429', 'member_type'=>'1', 'remit_fee'=>'', 'invest_usefee'=>6500, 'paied'=>'Y', 'paied_date'=>'2018-01-05 12:00:00'),
			4=>array('invest_idx'=>'1114', 'mb_no'=>'168', 'mb_id'=>'gsh0201', 'mb_name'=>'고상희', 'jumin'=>'7505212090318', 'member_type'=>'1', 'remit_fee'=>'', 'invest_usefee'=>7000, 'paied'=>'Y', 'paied_date'=>'2018-01-05 12:00:00')
	);
}
else {
	///////////////////////////
	// 실 데이터
	///////////////////////////
	$INV_ARR   = repayCalculation($prd_idx);
	$INI       = $INV_ARR['INI'];
	$PRDT      = $INV_ARR['PRDT'];
	$INVEST    = $INV_ARR['INVEST'];
	$REPAY     = $INV_ARR['REPAY'];
}
//print_r($REPAY);


if($PRDT['invest_usefee']=='0.00') {
	$RESULT_ARR = array('result' => 'ERROR', 'message' => '본 상품은 플랫폼 수수료를 수취하는 상품이 아닙니다.');
	echo json_encode($RESULT_ARR);
	exit;
}

$CorpNum = preg_replace('/-/', '', $INVOICER['CorpNum']);	// 팝빌회원 사업자번호, '-' 제외 10자리
$UserID  = $INVOICER['userid'];		// 팝빌회원 아이디

$itemName = "플랫폼 이용료";

$i = $turn-1;

$list_gubun = ($overdue) ? 'OVERDUE_LIST' : 'LIST';
$list_count = count($REPAY[$i][$list_gubun]);


//발행일 계산 (현금영수증은 발행일 또는 작성일 설정이 불가함
/*if($turn < $INI['repay_count']) {
	//이자상환차수일 경우
	$timestmp = strtotime($REPAY[$i]['target_sdate']);
	$write_date = date('Ym', $timestmp) . date('t', $timestmp);
}
else {
	//원금상환차수일 경우
	$write_date = preg_replace('/-/', '', $REPAY[$i]['repay_date']);
}*/
//echo $write_date; //exit;


for($j=0,$num=$list_count; $j<$list_count; $j++,$num--) {

	if($PRDT['invest_usefee']>'0.00' && $REPAY[$i][$list_gubun][$j]['remit_fee']=='') {

		//echo $REPAY[$i][$list_gubun][$j]['mb_no']."\n";
		//echo $REPAY[$i][$list_gubun][$j]['member_type']."\n";
		//echo $REPAY[$i][$list_gubun][$j]['mb_name']."\n";
		//echo $REPAY[$i][$list_gubun][$j]['jumin']."\n";
		//echo $REPAY[$i][$list_gubun][$j]['invest_usefee']."\n";
		//echo $REPAY[$i][$list_gubun][$j]['paied']."\n";
		//echo $REPAY[$i][$list_gubun][$j]['paied_date']."\n\n\n";
		//echo $invoicerMgtKey . '::: ' . $REPAY[$i][$list_gubun][$j]['invest_usefee'] . ' ' . $price . ' ' . $tax . PHP_EOL;

		$price        = $REPAY[$i][$list_gubun][$j]['invest_usefee'];	// 거래금액
		$supply_price = ceil($price / 1.1);												// 공급가액
		$tax          = $price - $supply_price;										// 부가세

		//*** 문서관리번호 설정 ************//
		$mgtKey = "P_".$REPAY[$i][$list_gubun][$j]['invest_idx'];						// 세금계산서/현금영수증 문서관리번호 ::: (P_투자번호_상환회차번호) - (최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성)
		$mgtKey.= ($test_mode) ? '_' . sprintf('%03d', rand(1, 100)) : '_' . sprintf('%03d', $turn);
		$mgtKey.= ($overdue) ? '_OVD' : '';
		//*** 문서관리번호 설정 ************//


		$MEM = sql_fetch("SELECT mb_email, mb_hp, mb_co_name, mb_co_reg_num, mb_co_owner FROM g5_member WHERE mb_no='".$REPAY[$i][$list_gubun][$j]['mb_no']."'");

		$is_target = ($REPAY[$i][$list_gubun][$j]['member_type']=='1' && $REPAY[$i][$list_gubun][$j]['is_owner_operator']=='') ? true : false;

		/////////////////////////////////////////////////////
		// 개인회원 - 현금영수증 발행
		/////////////////////////////////////////////////////
		if($is_target) {

			$REPAY[$i][$list_gubun][$j]['jumin'] = preg_replace('/-/', '', $REPAY[$i][$list_gubun][$j]['jumin']);

			$forceIssue         = false;									// 지연발행 강제여부
			$memo               = '';											// 즉시발행 메모
			$emailSubject       = '';											// 안내메일 제목, 미기재시 기본제목으로 전송
			$writeSpecification = false;									// 거래명세서 동시작성 여부
			$dealInvoiceMgtKey  = '';											// 거래명세서 동시작성시 명세서 관리번호 - 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성

			$memo = "현금영수증 즉시발행 메모";


			$Cashbill = new Cashbill();		// 현금영수증 객체 생성

			$Cashbill->mgtKey            = $mgtKey;															// [필수] 현금영수증 문서관리번호,
			$Cashbill->tradeType         = '승인거래';													// [필수] 거래유형, (승인거래, 취소거래) 중 기재
			$Cashbill->orgConfirmNum     = '';																	// [취소 현금영수증 발행시 필수] 원본 현금영수증 국세청 승인번호 - 국세청 승인번호는 GetInfo API의 ConfirmNum 항목으로 확인할 수 있습니다.
			$Cashbill->orgTradeDate      = '';																	// [취소 현금영수증 발행시 필수] 원본 현금영수증 거래일자 - 현금영수증 거래일자는 GetInfo API의 TradeDate 항목으로 확인할 수 있습니다.
			$Cashbill->identityNum       = $REPAY[$i][$list_gubun][$j]['jumin'];			// [필수] 거래처 식별번호, 거래유형에 따라 작성 (소득공제용 - 주민등록/휴대폰/카드번호 기재가능, 지출증빙용 - 사업자번호/주민등록/휴대폰/카드번호 기재가능)
			$Cashbill->taxationType      = '과세';															// [필수] 과세, 비과세 중 기재
			$Cashbill->supplyCost        = $supply_price;												// [필수] 공급가액, ','콤마 불가 숫자만 가능
			$Cashbill->tax               = $tax;																// [필수] 세액, ','콤마 불가 숫자만 가능
			$Cashbill->serviceFee        = '0';																	// [필수] 봉사료, ','콤마 불가 숫자만 가능
			$Cashbill->totalAmount       = $price;															// [필수] 거래금액, ','콤마 불가 숫자만 가능
			$Cashbill->tradeUsage        = '소득공제용';												// [필수] 소득공제용, 지출증빙용 중 기재
			$Cashbill->franchiseCorpNum  = $CorpNum;														// [필수] 발행자 사업자번호
			$Cashbill->franchiseCorpName = $INVOICER['CorpName'];								// 발행자 상호
			$Cashbill->franchiseCEOName  = $INVOICER['CorpOwner'];							// 발행자 대표자 성명
			$Cashbill->franchiseAddr     = $INVOICER['CorpAddr'];								// 발행자 주소
			$Cashbill->franchiseTEL      = $INVOICER['Tel'];										// 발행자 연락처

			$Cashbill->customerName      = $REPAY[$i][$list_gubun][$j]['mb_name'];		// 고객명
			$Cashbill->itemName          = $itemName;														// 상품명
			$Cashbill->orderNumber       = $mgtKey;															// 주문번호
			$Cashbill->email             = $MEM['mb_email'];										// 고객 메일주소
			$Cashbill->hp                = '';																	// 고객 휴대폰 번호
			$Cashbill->smssendYN         = false;																// 발행시 알림문자 전송여부

			try {
				$result = $CashbillService->RegistIssue($CorpNum, $Cashbill, $memo);
				$code    = $result->code;
				$message = $result->message;
			}
			catch(PopbillException $pe) {
				$code    = $pe->getCode();
				$message = $pe->getMessage();
			}
			//echo '[' . $code . '] ' . $message . PHP_EOL;

			$log_sql = "INSERT INTO TaxinvoiceLog (mgtKey, req_type, action, code, msg, proc_mb_id, req_date) VALUES ('".$mgtKey."', '현금영수증', '발행', '".$code."', '".$message."', '".$_SESSION['ss_mb_id']."', NOW())";
			sql_query($log_sql);

			if($code=='1') {
				$give_idx = $REPAY[$i][$list_gubun][$j]['give_idx'];
				sql_query("UPDATE cf_product_give SET mgtKey='".$mgtKey."' WHERE idx='".$give_idx."'");
			}

			//print_r($REPAY[$i][$list_gubun][$j]);
			//echo "$write_date $mgtKey $price $supply_price $tax \n";

		}

		unset($mgtKey);
		unset($price);
		unset($supply_price);
		unset($tax);
		unset($MEM);
		unset($is_target);
		unset($code);
		unset($message);

	}

}

$RESULT_ARR = array('result' => 'SUCCESS', 'message' => '');
echo json_encode($RESULT_ARR);

exit;

?>