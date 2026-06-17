<?php
###############################################################################
## 세금계산서 발행취소
###############################################################################
##   [발행완료] 상태의 세금계산서를 [발행취소] 처리합니다.
##   - [발행취소]는 국세청 전송전에만 가능합니다.
##   - 발행취소된 세금계산서는 국세청에 전송되지 않습니다.
##   - 발행취소 세금계산서에 사용된 문서관리번호를 재사용 하기 위해서는 삭제(Delete API)를 호출하여 해당세금계산서를 삭제해야 합니다.
##   - 문서관리번호 삭제후 재사용 하고자 할 경우 : CancelIssue.php -> Delete.php 과정으로 실행
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

		// 메모
		$memo = '매출관련정보변경';

		try {
			$result  = $TaxinvoiceService->CancelIssue($CorpNum, $mgtKeyType, $mgtKey, $memo);
			$code    = $result->code;
			$message = $result->message;
		}
		catch (PopbillException $pe) {
			$code    = $pe->getCode();
			$message = $pe->getMessage();
		}

		$log_sql = "INSERT INTO TaxinvoiceLog (mgtKey, req_type, action, code, msg, proc_mb_id, req_date) VALUES ('{$mgtKey}', '세금계산서', '발행취소', '{$code}', '{$message}', '{$_SESSION['ss_mb_id']}', NOW())";
		sql_query($log_sql);


		echo "Response.code : " . $code . "\n";
		echo "Response.message : " . $message. "\n";

	}

?>