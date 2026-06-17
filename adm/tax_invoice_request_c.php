<?
###############################################################################
## 세금계산서 발행
###############################################################################

set_time_limit(0);

include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출
include_once(TAX_INVOICE_PATH . '/hellofunding/common.php');		// 링크허브(팝빌) 세금계산서발행 API용 설정
include_once(TAX_INVOICE_PATH . '/Popbill/PopbillTaxinvoice.php');	// 세금계산서 발행용(법인) 라이브러리


$TaxinvoiceService = new TaxinvoiceService($LinkID, $SecretKey);
$TaxinvoiceService->IsTest($test_mode);		// 연동환경 설정값, 개발용(true), 상업용(false)


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
//print_r($REPAY[2]);


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


// 발행일 계산
/*
if($turn < $INI['repay_count']) {
	//이자상환차수일 경우
	$timestmp = strtotime($REPAY[$i]['target_sdate']);
	$write_date = date('Ym', $timestmp) . date('t', $timestmp);
}
else {
	//원금상환차수일 경우
	$write_date = preg_replace('/-/', '', $REPAY[$i]['repay_date']);
}
*/
// 발행일을 전문전송일과 동일하게 변경 : 2018-02-07 (배석:이정환 차장, 고상희 차장)
$write_date = date('Ymd');

/*
// 2018-01-10 것만 임의 설정 (고차장 요청)
if(date('Y-m-d')=='2018-01-10') {
	$write_date = date('Ymd');
}
*/


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

		//*** 문서관리번호 설정 ************///
		$mgtKey = "C_".$REPAY[$i][$list_gubun][$j]['invest_idx'];						// 세금계산서/현금영수증 문서관리번호 ::: (C_투자번호_상환회차번호) - (최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성)
		$mgtKey.= ($test_mode) ? '_' . sprintf('%03d', rand(1, 100)) : '_' . sprintf('%03d', $turn);
		$mgtKey.= ($overdue) ? '_OVD' : '';
		//*** 문서관리번호 설정 ************///

		if($mgtKey=="C_8777_001") $mgtKey = $mgtKey."_N";


		$MEM = sql_fetch("SELECT mb_email, mb_hp, mb_co_name, mb_co_reg_num, mb_co_owner FROM g5_member WHERE mb_no='".$REPAY[$i][$list_gubun][$j]['mb_no']."'");


		$is_target = false;
		if($REPAY[$i][$list_gubun][$j]['member_type']=='2') {
			$is_target = true;
		}
		else {
			if($REPAY[$i][$list_gubun][$j]['is_owner_operator']=='1') $is_target = true;
		}

		/////////////////////////////////////////////////////
		// 기업회원 (법인+개인사업자) - 세금계산서 발행
		/////////////////////////////////////////////////////
		if($is_target) {

			if($REPAY[$i][$list_gubun][$j]['is_owner_operator']=='1') {
				// 개인사업자의 정산정보 임시 전환
				$REPAY[$i][$list_gubun][$j]['jumin']   = $MEM['mb_co_reg_num'];
				$REPAY[$i][$list_gubun][$j]['mb_name'] = $MEM['mb_co_name'];
				$REPAY[$i][$list_gubun][$j]['owner']   = $MEM['mb_co_owner'];
			}
			else {
				$REPAY[$i][$list_gubun][$j]['owner'] = $MEM['mb_co_owner'];
			}
			$REPAY[$i][$list_gubun][$j]['email'] = $MEM['mb_email'];
			$REPAY[$i][$list_gubun][$j]['jumin'] = preg_replace('/-/', '', $REPAY[$i][$list_gubun][$j]['jumin']);


			$forceIssue         = false;									// 지연발행 강제여부
			$memo               = '';											// 즉시발행 메모
			$emailSubject       = '';											// 안내메일 제목, 미기재시 기본제목으로 전송
			$writeSpecification = false;									// 거래명세서 동시작성 여부
			$dealInvoiceMgtKey  = '';											// 거래명세서 동시작성시 명세서 관리번호 - 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성

			// 공급받는자 정보 정리
			$INVOICEE['Type']        = '사업자';
			$INVOICEE['CorpNum']     = $REPAY[$i][$list_gubun][$j]['jumin'];
			$INVOICEE['CorpName']    = $REPAY[$i][$list_gubun][$j]['mb_name'];
			$INVOICEE['CorpOwner']   = $REPAY[$i][$list_gubun][$j]['owner'];
			$INVOICEE['CorpAddr']    = '';
			$INVOICEE['BizType']     = '';
			$INVOICEE['BizClass']    = '';
			$INVOICEE['ContactName'] = '';
			$INVOICEE['Email']       = $REPAY[$i][$list_gubun][$j]['email'];
			$INVOICEE['TEL']         = '';
			$INVOICEE['HP']          = '';


			//////////////////////////////////////////////////////////////
			//                       세금계산서 정보
			//////////////////////////////////////////////////////////////
			$Taxinvoice = new Taxinvoice();																// 세금계산서 객체 생성

			$Taxinvoice->writeDate       = $write_date;										// [필수] 작성일자, 형식(yyyyMMdd) 예)20150101
			$Taxinvoice->issueType       = '정발행';											// [필수] 발행형태, '정발행', '역발행', '위수탁' 중 기재
			$Taxinvoice->chargeDirection = '정과금';											// [필수] 과금방향 - '정과금'(공급자 과금), '역과금'(공급받는자 과금) 중 기재, 역과금은 역발행시에만 가능.
			$Taxinvoice->purposeType     = '영수';												// [필수] '영수', '청구' 중 기재
			$Taxinvoice->taxType         = '과세';												// [필수] 과세형태, '과세', '영세', '면세' 중 기재
			$Taxinvoice->issueTiming     = '직접발행';										// [필수] 발행시점, 발행예정시 동작, '직접발행', '승인시자동발행' 중 기재

			//////////////////////////////////////////////////////////////
			//                        공급자 정보
			//////////////////////////////////////////////////////////////
			$Taxinvoice->invoicerCorpNum     = $CorpNum;									// [필수] 공급자 사업자번호
			$Taxinvoice->invoicerTaxRegID    = '';												//        공급자 종사업장 식별번호, 4자리 숫자 문자열
			$Taxinvoice->invoicerCorpName    = $INVOICER['CorpName'];			// [필수] 공급자 상호
			$Taxinvoice->invoicerMgtKey      = $mgtKey;										// [필수] 공급자 문서관리번호, 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
			$Taxinvoice->invoicerCEOName     = $INVOICER['CorpOwner'];		// [필수] 공급자 대표자성명
			$Taxinvoice->invoicerAddr        = $INVOICER['CorpAddr'];			//        공급자 주소
			$Taxinvoice->invoicerBizClass    = $INVOICER['BizClass'];			//        공급자 종목
			$Taxinvoice->invoicerBizType     = $INVOICER['BizType'];			//        공급자 업태
			$Taxinvoice->invoicerContactName = $INVOICER['ContactName'];	//        공급자 담당자 성명
			$Taxinvoice->invoicerEmail       = $INVOICER['Email'];				//        공급자 담당자 메일주소
			$Taxinvoice->invoicerTEL         = $INVOICER['Tel'];					//        공급자 담당자 연락처
			$Taxinvoice->invoicerHP          = $INVOICER['HP'];						//        공급자 휴대폰 번호
			$Taxinvoice->invoicerSMSSendYN   = false;											//        정발행시 공급받는자 담당자에게 알림문자 전송여부 - 안내문자 전송시 포인트가 차감되며 전송실패시 환불처리 됩니다.

			//////////////////////////////////////////////////////////////
			//                     공급받는자 정보
			//////////////////////////////////////////////////////////////
			$Taxinvoice->invoiceeType         = $INVOICEE['Type'];						// [필수] 공급받는자 구분, '사업자', '개인', '외국인' 중 기재
			$Taxinvoice->invoiceeCorpNum      = $INVOICEE['CorpNum'];					// [필수] 공급받는자 사업자번호
			$Taxinvoice->invoiceeTaxRegID     = '';														//        공급받는자 종사업장 식별번호, 4자리 숫자 문자열
			$Taxinvoice->invoiceeCorpName     = $INVOICEE['CorpName'];				// [필수] 공급자 상호
			$Taxinvoice->invoiceeMgtKey       = '';														// [역발행시 필수] 공급받는자 문서관리번호, 최대 24자리 숫자, 영문, '-', '_' 조합으로 사업자별로 중복되지 않도록 구성
			$Taxinvoice->invoiceeCEOName      = $INVOICEE['CorpOwner'];				// [필수] 공급받는자 대표자성명
			$Taxinvoice->invoiceeAddr         = $INVOICEE['CorpAddr'];				//        공급받는자 주소
			$Taxinvoice->invoiceeBizType      = $INVOICEE['BizType'];					//        공급받는자 업태
			$Taxinvoice->invoiceeBizClass     = $INVOICEE['BizClass'];				//        공급받는자 종목
			$Taxinvoice->invoiceeContactName1 = $INVOICEE['ContactName'];			//        공급받는자 담당자 성명
			$Taxinvoice->invoiceeEmail1       = $INVOICEE['Email'];						//        공급받는자 담당자 메일주소
			$Taxinvoice->invoiceeTEL1         = $INVOICEE['TEL'];							//        공급받는자 담당자 연락처
			$Taxinvoice->invoiceeHP1          = $INVOICEE['HP'];							//        공급받는자 담당자 휴대폰 번호
			$Taxinvoice->invoiceeSMSSendYN    = false;												//        역발행요청시 공급자 담당자에게 알림문자 전송여부 - 문자전송지 포인트가 차감되며, 전송실패시 포인트 환불처리됩니다.

			//////////////////////////////////////////////////////////////
			//                      세금계산서 기재정보
			//////////////////////////////////////////////////////////////
			$Taxinvoice->supplyCostTotal = $supply_price;						// [필수] 공급가액 합계
			$Taxinvoice->taxTotal        = $tax;										// [필수] 세액 합계
			$Taxinvoice->totalAmount     = $price;									// [필수] 합계금액, (공급가액 합계 + 세액 합계)
			$Taxinvoice->serialNum       = '';											//        기재상 '일련번호'항목
			$Taxinvoice->cash            = '';											//        기재상 '현금'항목
			$Taxinvoice->chkBill         = '';											//        기재상 '수표'항목
			$Taxinvoice->note            = '';											//        기재상 '어음'항목
			$Taxinvoice->credit          = '';											//        기재상 '외상'항목

			$Taxinvoice->remark1         = '';											//        기재상 '비고' 항목1
			$Taxinvoice->remark2         = '';											//        기재상 '비고' 항목2
			$Taxinvoice->remark3         = '';											//        기재상 '비고' 항목3

			$Taxinvoice->kwon            = '0';											//        기재상 '권' 항목, 최대값 32767
			$Taxinvoice->ho              = '0';											//        기재상 '호' 항목, 최대값 32767

			$Taxinvoice->businessLicenseYN = false;									//        사업자등록증 이미지파일 첨부여부
			$Taxinvoice->bankBookYN = false;												//        통장사본 이미지파일 첨부여부

			//////////////////////////////////////////////////////////////
			//                    수정 세금계산서 기재정보
			// - 수정세금계산서 관련 정보는 연동매뉴얼 또는 개발가이드 링크 참조
			// - [참고] 수정세금계산서 작성방법 안내 - http://blog.linkhub.co.kr/650
			//////////////////////////////////////////////////////////////

			//$Taxinvoice->modifyCode = '';													// 수정사유코드, 수정사유에 따라 1~6중 선택기재
			//$Taxinvoice->originalTaxinvoiceKey = '';							// 원본세금계산서 ItemKey 기재, 문서확인 (GetInfo API)의 응답결과(ItemKey 항목) 확인

			//////////////////////////////////////////////////////////////
			//                      상세항목(품목) 정보
			//////////////////////////////////////////////////////////////
			$Taxinvoice->detailList = array();

			$Taxinvoice->detailList[] = new TaxinvoiceDetail();
			$Taxinvoice->detailList[0]->serialNum  = 1;								// [상세항목 배열이 있는 경우 필수] 일련번호 1~99까지 순차기재,
			$Taxinvoice->detailList[0]->purchaseDT = $write_date;			// 거래일자
			$Taxinvoice->detailList[0]->itemName   = $itemName;				// 품명
			$Taxinvoice->detailList[0]->spec       = '';							// 규격
			$Taxinvoice->detailList[0]->qty        = '';							// 수량
			$Taxinvoice->detailList[0]->unitCost   = '';							// 단가
			$Taxinvoice->detailList[0]->supplyCost = $supply_price;		// 공급가액
			$Taxinvoice->detailList[0]->tax        = $tax;						// 세액
			$Taxinvoice->detailList[0]->remark      = '';							// 비고

			//$Taxinvoice->detailList[] = new TaxinvoiceDetail();
			//$Taxinvoice->detailList[1]->serialNum = 2;				      // [상세항목 배열이 있는 경우 필수] 일련번호 1~99까지 순차기재,
			//$Taxinvoice->detailList[1]->purchaseDT = '20161102';	  // 거래일자
			//$Taxinvoice->detailList[1]->itemName = '품목명2번';	  	// 품명
			//$Taxinvoice->detailList[1]->spec = '';									// 규격
			//$Taxinvoice->detailList[1]->qty = '';										// 수량
			//$Taxinvoice->detailList[1]->unitCost = '';							// 단가
			//$Taxinvoice->detailList[1]->supplyCost = '100000';		  // 공급가액
			//$Taxinvoice->detailList[1]->tax = '10000';				      // 세액
			//$Taxinvoice->detailList[1]->remark = '';								// 비고

			//////////////////////////////////////////////////////////////
			//                      추가담당자 정보
			// - 세금계산서 발행안내 메일을 수신받을 공급받는자 담당자가 다수인 경우
			// 추가 담당자 정보를 등록하여 발행안내메일을 다수에게 전송할 수 있습니다. (최대 5명)
			//////////////////////////////////////////////////////////////
			$Taxinvoice->addContactList = array();

			$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
			$Taxinvoice->addContactList[0]->serialNum   = 1;											// 일련번호 1부터 순차기재
			$Taxinvoice->addContactList[0]->email       = 'sori9th@gmail.com';		// 이메일주소
			$Taxinvoice->addContactList[0]->contactName	= '배재수';								// 담당자명

			//$Taxinvoice->addContactList[] = new TaxinvoiceAddContact();
			//$Taxinvoice->addContactList[1]->serialNum   = 2;											// 일련번호 1부터 순차기재
			//$Taxinvoice->addContactList[1]->email       = 'test2@test.com';				// 이메일주소
			//$Taxinvoice->addContactList[1]->contactName	= '테스트';								// 담당자명


			try {
				$result = $TaxinvoiceService->RegistIssue($CorpNum, $Taxinvoice, $UserID, $writeSpecification, $forceIssue, $memo, $emailSubject, $dealInvoiceMgtKey);
				$code    = $result->code;
				$message = $result->message;
			}
			catch(PopbillException $pe) {
				$code    = $pe->getCode();
				$message = $pe->getMessage();
			}
			//echo '[' . $code . '] ' . $message . PHP_EOL;

			$message = sql_real_escape_string($message);

			$log_sql = "INSERT INTO TaxinvoiceLog (mgtKey, req_type, action, code, msg, proc_mb_id, req_date) VALUES ('".$mgtKey."', '세금계산서', '발행', '".$code."', '".$message."', '".$_SESSION['ss_mb_id']."', NOW())";
		//echo $log_sql."\n";
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