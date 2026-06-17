<?php
###############################################################################
## 세금계산서 삭제
###############################################################################
##  문서관리번호 삭제후 재사용 하고자 할 경우 :
##  - CancelIssue.php -> Delete.php 과정으로 실행
###############################################################################

include_once('_common.php');
include_once(TAX_INVOICE_PATH . '/hellofunding/common.php');				// 링크허브(팝빌) 세금계산서발행 API용 설정
include_once(TAX_INVOICE_PATH . '/Popbill/PopbillTaxinvoice.php');	// 세금계산서 발행용(법인) 라이브러리	include_once('_common.php');

$TaxinvoiceService = new TaxinvoiceService($LinkID, $SecretKey);
$TaxinvoiceService->IsTest($test_mode);		// 연동환경 설정값, 개발용(true), 상업용(false)


	// 팝빌 회원 사업자번호, '-' 제외 10자리
	$CorpNum = preg_replace('/-/', '', $INVOICER['CorpNum']);	// 팝빌회원 사업자번호, '-' 제외 10자리

	// 문서관리번호
	$MGTKEY = array("5782_001", "5758_001", "5736_001", "5735_001");

	// 발행유형, ENumMgtKeyType::SELL:매출, ENumMgtKeyType::BUY:매입, ENumMgtKeyType::TRUSTEE:위수탁
	$mgtKeyType = ENumMgtKeyType::SELL;

	for($i=0; $i<count($MGTKEY); $i++) {

		$mgtKey = $MGTKEY[$i];

		try {
			$result = $TaxinvoiceService->Delete($CorpNum, $mgtKeyType, $mgtKey);
			$code = $result->code;
			$message = $result->message;
		}
		catch(PopbillException $pe) {
			$code = $pe->getCode();
			$message = $pe->getMessage();
		}

		$log_sql = "INSERT INTO TaxinvoiceLog (mgtKey, req_type, action, code, msg, proc_mb_id, req_date) VALUES ('{$mgtKey}', '세금계산서', '삭제', '{$code}', '{$message}', '{$_SESSION['ss_mb_id']}', NOW())";
		sql_query($log_sql);

		echo "Response.code : " . $code . "\n";
		echo "Response.message : " . $message. "\n";

	}

?>
