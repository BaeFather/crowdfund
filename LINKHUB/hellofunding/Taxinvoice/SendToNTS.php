<?php
###############################################################################
## 국세청 즉시전송
###############################################################################
##  [발행완료] 상태의 세금계산서를 국세청으로 즉시전송합니다.
##  - 국세청 즉시전송을 호출하지 않은 세금계산서는 발행일 기준 익일 오후 3시에 팝빌 시스템에서 일괄적으로 국세청으로 전송합니다.
##  - 익일전송시 전송일이 법정공휴일인 경우 다음 영업일에 전송됩니다.
##  - 국세청 전송에 관한 사항은 "[전자세금계산서 API 연동매뉴얼] > 1.4 국세청 전송 정책" 을 참조하시기 바랍니다.
###############################################################################

include_once('_common.php');
include_once(TAX_INVOICE_PATH . '/hellofunding/common.php');				// 링크허브(팝빌) 세금계산서발행 API용 설정
include_once(TAX_INVOICE_PATH . '/Popbill/PopbillTaxinvoice.php');	// 세금계산서 발행용(법인) 라이브러리	include_once('_common.php');

$TaxinvoiceService = new TaxinvoiceService($LinkID, $SecretKey);
$TaxinvoiceService->IsTest($test_mode);		// 연동환경 설정값, 개발용(true), 상업용(false)


  // 팝빌 회원 사업자번호, '-' 제외 10자리
	$CorpNum = preg_replace('/-/', '', $INVOICER['CorpNum']);	// 팝빌회원 사업자번호, '-' 제외 10자리

	// 문서관리번호
	$MGTKEY = array(
		"5782_001", "5758_001", "5736_001", "5735_001", "5734_001", "5733_001", "5732_001", "5716_001", "5645_001", "5637_001",
		"5635_001", "5609_001", "5570_001", "5554_001", "5506_001", "5485_001", "5482_001", "5467_001", "5463_001", "5459_001",
		"5452_001", "5442_001", "5423_001", "5392_001", "5355_001", "5384_001"
	);

	// 발행유형, ENumMgtKeyType::SELL:매출, ENumMgtKeyType::BUY:매입, ENumMgtKeyType::TRUSTEE:위수탁
	$mgtKeyType = ENumMgtKeyType::SELL;

	for($i=0; $i<count($MGTKEY); $i++) {

		$mgtKey = $MGTKEY[$i];

		try {
			$result = $TaxinvoiceService->SendToNTS($CorpNum, $mgtKeyType, $mgtKey);
			$code = $result->code;
			$message = $result->message;
		}
		catch(PopbillException $pe) {
			$code = $pe->getCode();
			$message = $pe->getMessage();
		}

		$log_sql = "INSERT INTO TaxinvoiceLog (mgtKey, req_type, action, code, msg, proc_mb_id, req_date) VALUES ('{$mgtKey}', '세금계산서', '국세청즉시전송', '{$code}', '{$message}', '{$_SESSION['ss_mb_id']}', NOW())";
		sql_query($log_sql);

		echo "Response.code : " . $code . "\n";
		echo "Response.message : " . $message. "\n";

	}

?>