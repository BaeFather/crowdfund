<?php
###############################################################################
## 1건의 전자세금계산서 또는 현금영수증 (범용) 보기 팝업 URL을 반환합니다.
## - 반환된 URL은 보안정책으로 인해 30초의 유효시간을 갖습니다.
###############################################################################

include_once('_common.php');



$mgtKey = trim($_REQUEST['mgtKey']);		// 문서관리번호

if($mgtKey=='') msg_close('문서관리번호가 없습니다.');
if(substr($mgtKey, 0, 1)=='C') {
	$doc_type = 'Taxinvoice';
}
else if(substr($mgtKey, 0, 1)=='P') {
	$doc_type = 'Cashbill';
}
else {
	msg_close('문서관리번호 오류!!');
}

include_once(TAX_INVOICE_PATH . '/hellofunding/common.php');		// 링크허브(팝빌) 세금계산서발행 API용 설정


$CorpNum = preg_replace('/-/', '', $INVOICER['CorpNum']);	// 팝빌회원 사업자번호, '-' 제외 10자리
$UserID  = $INVOICER['userid'];		// 팝빌회원 아이디


//echo $mgtKey . "\n" . $doc_type . "\n";

if($doc_type=='Taxinvoice') {

	include_once(TAX_INVOICE_PATH . '/Popbill/PopbillTaxinvoice.php');	// 세금계산서 발행용(법인) 라이브러리

	$TaxinvoiceService = new TaxinvoiceService($LinkID, $SecretKey);
	$TaxinvoiceService->IsTest($test_mode);		// 연동환경 설정값, 개발용(true), 상업용(false)

	// 발행유형, ENumMgtKeyType::SELL:매출, ENumMgtKeyType::BUY:매입, ENumMgtKeyType::TRUSTEE:위수탁
	$mgtKeyType = ENumMgtKeyType::SELL;

	try {
		$url = $TaxinvoiceService->GetPopUpURL($CorpNum, $mgtKeyType, $mgtKey);
	}
	catch ( PopbillException $pe ) {
		$code = $pe->getCode();
		$message = $pe->getMessage();
	}

	$record_req_type = "세금계산서";

}
else if($doc_type=='Cashbill') {

	include_once(TAX_INVOICE_PATH . '/Popbill/PopbillCashbill.php');		// 현금영수증 발행용(개인) 라이브러리

	$CashbillService = new CashbillService($LinkID, $SecretKey);
	$CashbillService->IsTest($test_mode);			// 연동환경 설정값, 개발용(true), 상업용(false)

	try {
		$url = $CashbillService->GetPopUpURL($CorpNum, $mgtKey);
	}
	catch(PopbillException $pe) {
		$code = $pe->getCode();
		$message = $pe->getMessage();
	}

	$record_req_type = "현금영수증";

}



if($url) {
	msg_replace('', $url, 'window');
}
else {
	echo "code: " . $code . "<br>\n";
	echo "message: " . $message . "<br>\n";
}

exit;

?>