<?php
/**
 * 원리금 수취권 증서 보기
 * 2018-03-07 PDF 다운로드 기능 추가
 * 2018-06-27 핀크를 요청시 출력기능 추가
 */
include_once("_common.php");
require_once G5_PLUGIN_PATH."/tcpdf/tcpdf.php";

// 신디케이션사에서 넘어올 경우 회원정보 가져오기
if($_REQUEST[strtoupper(MD5('member_idx'))]) {
	$tmp = sql_fetch("SELECT mb_id, mb_level FROM g5_member WHERE mb_no='".$_REQUEST[strtoupper(MD5('member_idx'))]."'");
	$member = get_member($tmp['mb_id']);
	if(!$member['mb_level']) {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
}

$invest_query = "
	SELECT
		A.amount, A.member_idx, A.product_idx, A.prin_rcv_no,
		B.*
	FROM
		cf_product_invest A
	INNER JOIN
		cf_product B ON A.product_idx=B.idx
	WHERE 1
		AND A.idx='".$_REQUEST['idx']."'
		AND A.invest_state='Y'
		AND A.member_idx='".$member['mb_no']."'";
$INVEST = sql_fetch($invest_query);
if(!$INVEST) {
	echo "<script>alert('해당 투자내역이 존재하지 않습니다.'); self.close();</script>"; exit;
}


// 회원코드
$mbr_code  = "M".sprintf("%05d", $INVEST['member_idx']);

// 증서번호
//   160번 상품까지 : 제 대출실행일(YYYYmmdd)-P투자상품번호5자리-M회원번호5자리
//   이후부터 : 제 M[회원번호]P[상품번호]I[투자번호]	:::: 2018-03-20 변경
$cert_idx = "제 ";
if($INVEST['prin_rcv_no']) {
	$cert_idx.= $INVEST['prin_rcv_no'];
}
else {
	$cert_idx.= preg_replace("/-|:| /", "", $INVEST['loan_start_date']);
	$cert_idx.= "-";
	$cert_idx.= "P".sprintf("%05d", $INVEST['product_idx']);
	$cert_idx.= "-";
	$cert_idx.= $mbr_code;
}

// 상품코드
$prdt_code = substr($INVEST['loan_start_date'], 2, 2)."-P-".sprintf("%05d", $INVEST['product_idx']);


// 투자 수익율
if($INVEST["invest_return"]>0){
	$invest_return = $INVEST["invest_return"];
}

// 투자기간
if($INVEST["invest_period"]>0){
	$invest_period = $INVEST["invest_period"];
}

// 원천징수세율 세율
if($INVEST["withhold_tax_rate"]>0){
	$withhold_tax_rate = $INVEST["withhold_tax_rate"];
}

// 투자자 플랫폼 이용료
if($invest_row["invest_usefee"]>0){
	$invest_usefee = $invest_row["invest_usefee"];
}

// 투자금액
$invest_amount =($INVEST["amount"]) ? $INVEST["amount"] : 0;

$sql_common = " FROM cf_product_invest a, cf_product b, g5_member c ";
$sql_search = " WHERE a.product_idx = b.idx AND a.member_idx = c.mb_no AND a.member_idx = '".$member['mb_no']."' AND a.idx = '".$_GET['idx']."' AND a.invest_state = 'Y' ";
$sql_order  = " ORDER BY a.insert_date";

$sql = "
	SELECT
		a.*,
		b.withhold_tax_rate, b.invest_return, b.invest_period, b.invest_usefee,
		c.mb_id, c.mb_name, c.bank_name, c.account_num, c.member_type
	$sql_common
	$sql_search
	$sql_order";
$result = sql_query($sql);

$sql = "
	SELECT
		COUNT(distinct a.product_idx) AS cnt,
		b.*,
		SUM(a.amount) AS amount,
		SUM(a.amount * (b.invest_return / 100) / 12 * b.invest_period) AS invest_interest,
		SUM(a.amount * (b.loan_interest_rate / 100) / 12 * b.invest_period) AS loan_interest
	$sql_common
	$sql_search";
$PRDT = sql_fetch($sql);

switch($PRDT['repay_type']) {
	case '1' : $repay_type = "원금만기 일시상환";break;
	case '2' : $repay_type = "원리금 균등상환";break;
	case '3' : $repay_type = "원금 균등상환";break;
}

// 첫 이자지급일
$pay_date_day = 5;
$first_repay_date = substr(date('Y-m-d', strtotime('+1 month', strtotime($PRDT['loan_start_date']))), 0, 7);
$first_repay_date.= "-" . sprintf('%02d', $pay_date_day);

$print_mb_name = ($member['member_type']==1) ? $member["mb_name"] : $member["mb_co_name"];

// 우선수익자 표기명 변경
if($member['mb_id']=='fintech01')     $special_print_name = "NH투자증권 <span style='font-size:13px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
if($member['mb_id']=='fintech02')     $special_print_name = "NH투자증권 <span style='font-size:13px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제2호 신탁업자 지위)</span>";
if($member['mb_id']=='fintech03')     $special_print_name = "NH투자증권 <span style='font-size:13px'>(피델리스 대신 P2P 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
if($member['mb_id']=='fintech04')     $special_print_name = "피델리스 P2P 전문투자형 사모투자신탁 제1호";
if($member['mb_id']=='fintech05')     $special_print_name = "피델리스 핀테크인컴 전문투자형 사모투자신탁 제1호";
if($member['mb_id']=='directlending') $special_print_name = "메리츠Direct Lending전문투자형사모투자신탁[혼합자산]";
if($member['mb_id']=='hanilfirst')    $special_print_name = "NH투자증권 (한일퍼스트 전문투자형 사모혼합자산투자신탁 제4호 신탁업자 지위)";
if($member['mb_id']=='gbliberof1')    $special_print_name = "골든브릿지리베로(F)전문투자형사모투자신탁 1호";
if($member['mb_id']=='gbliberog1')    $special_print_name = "골든브릿지리베로(G)전문투자형사모투자신탁 1호";





$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set header and footer fonts
$pdf->SetTitle('원리금 수취권 증서');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(0, 0, 0); //SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT)
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// get the current page break margin
$bMargin = $pdf->getBreakMargin();
// get current auto-page-break mode
$auto_page_break = $pdf->getAutoPageBreak();
// disable auto-page-break
$pdf->SetAutoPageBreak(false, 0);

$img_file = '/images/deposit/certificate2_bg.jpg';
$pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
// restore auto-page-break status
$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
// set the starting point for the page content
$pdf->setPageMark();

// Print text using writeHTMLCell()
$pdf->SetXY(142, 10);
$pdf->SetFont('cid0kr', '', 10);
$pdf->Write(0, "증서번호: ".$cert_idx);

$pdf->SetXY(45, 33);
$pdf->SetFont('cid0kr', 'B', 36);
$pdf->Write(0, '원리금  수취권  증서', '', 0, 'L', true, 0, false, false, 0);

////////////////////////
// 타이틀 영역
////////////////////////
$pdf->SetFont('cid0kr', '', 11);

$pdf->SetXY(15, 63);
$pdf->Write(0, '우선수익자');

$pdf->SetXY(15, 74.5);
$pdf->Write(0, '수익금액');

$pdf->SetXY(10, 95);
$pdf->Write(0, '대출 채권정보');

$pdf->SetXY(15, 107);
$pdf->Write(0, '상품코드');

$pdf->SetXY(15, 118.5);
$pdf->Write(0, '상환방식');

$pdf->SetXY(15, 130.2);
$pdf->Write(0, '대출금액');

$pdf->SetXY(15, 142);
$pdf->Write(0, '대출기간');

$pdf->SetXY(127, 107);
$pdf->Write(0, '구분');

$pdf->SetXY(127, 118.5);
$pdf->Write(0, '대출기간');

$pdf->SetXY(127, 130.2);
$pdf->Write(0, '대출금리');

$pdf->SetXY(127, 142);
$pdf->Write(0, '대출실행일');

$pdf->SetXY(15, 165.5);
$pdf->Write(0, '채권자');

$pdf->SetXY(15, 177.5);
$pdf->Write(0, '투자금액');

$pdf->SetXY(15, 189);
$pdf->Write(0, '모집기간');

$pdf->SetXY(15, 200.7);
$pdf->Write(0, '증서 발행자');

$pdf->SetXY(15, 212.5);
$pdf->Write(0, '증서 판매자');

$pdf->SetXY(127, 165.5);
$pdf->Write(0, '헬로펀딩 ID');

$pdf->SetXY(127, 177.5);
$pdf->Write(0, '투자금리');

$pdf->SetXY(127, 189);
$pdf->Write(0, '대출실행일');

$pdf->SetFont('cid0kr', '', 9);
$pdf->SetXY(116, 224);
$pdf->Write(0, "* 조기상환시 사전고지 없이 상환일이 변동될 수 있습니다.");

$pdf->SetFont('cid0kr', '', 16);
$pdf->SetXY(44, 240);
$pdf->Write(0, '원리금 수취권 구매에 의하여 본증서를 발행합니다.');

$pdf->SetFont('cid0kr', '', 14);
$pdf->SetXY(86, 252);
$pdf->Write(0, date("Y년  m월  d일", strtotime($PRDT['loan_start_date'])));
//$pdf->Write(0, date("Y년  m월  d일"));

$pdf->SetFont('cid0kr', 'B', 22);
$pdf->SetXY(79.5, 270);
$pdf->Write(0, '(주) 헬로핀테크');



////////////////////////
// 내용영역
////////////////////////
$pdf->SetFont('cid0kr', '', 11);
$pdf->SetTextColor(74, 74, 74);

// 우선수익자
$name = ($special_print_name) ? $special_print_name : $print_mb_name;
$pdf->SetXY(49, 63);
$pdf->Write(0, $name);

// 수익금액 : 내용
$pdf->SetXY(49, 74.5);
$pdf->Write(0, number2korean($invest_amount).' 원 + 이자');

// 상품코드 : 내용
$pdf->SetXY(49, 107);
$pdf->Write(0, $prdt_code);

// 상환방식 : 내용
$pdf->SetXY(49, 118.5);
$pdf->Write(0, $repay_type);

// 대출금액 : 내용
$pdf->SetXY(49, 130.2);
$pdf->Write(0, '금 '.number2korean($INVEST['recruit_amount']).' 원');

// 대출기간 : 내용
$pdf->SetXY(49, 142);
$pdf->Write(0, date("Y.m.d", strtotime($PRDT['loan_start_date'])) . ' ~ ' . date("Y.m.d", strtotime($PRDT['loan_end_date'])));
//$pdf->Write(0, (($PRDT['loan_end_date'] > $first_repay_date) ? date("Y.m.d", strtotime($first_repay_date)) : '') . ' ~ ' . date("Y.m.d", strtotime($PRDT['loan_end_date'])));

// 구분 : 내용
$pdf->SetXY(160, 107);
$pdf->Write(0, ($PRDT['category']=='2')?'부동산':'부동산 외');

// 대출기간 : 내용
$pdf->SetXY(160, 118.5);
if($PRDT['invest_period'] <= 1) $pdf->Write(0, $PRDT['invest_days'].'일');
else                            $pdf->Write(0, $PRDT['invest_period'].'개월');

// 대출금리 : 내용
$pdf->SetXY(160, 130.2);
$pdf->Write(0, sprintf("%0.2f", $INVEST['loan_interest_rate']).'%');

// 대출실행일 : 내용
$pdf->SetXY(160, 142);
$pdf->Write(0, date("Y.m.d", strtotime($PRDT['loan_start_date'])));

//채권자 : 내용
$pdf->SetXY(49, 165.5);
$pdf->Write(0, $name);

//투자금액 : 내용
$pdf->SetXY(49, 177.5);
$pdf->Write(0, '금 '.number2korean($INVEST['amount']).' 원');

//모집기간 : 내용
$pdf->SetXY(49, 189);
$pdf->Write(0, date("Y.m.d", strtotime($PRDT['start_date'])) . ' ~ ' . date("Y.m.d", strtotime($PRDT['invest_end_date'])));

//수익증서발행자 : 내용
$pdf->SetXY(49, 200.7);
$pdf->Write(0, "\"".$PRDT['title']."\" 의 차입자");

//수익증서판매자 : 내용
$pdf->SetXY(49, 212.5);
if ($PRDT['loan_start_date'] >= '2021-08-27') {
	$pdf->Write(0, "(주)헬로핀테크");
} else if ($PRDT['loan_start_date'] < '2021-08-27') {
	$pdf->Write(0, "(주)헬로크라우드대부");
}

//헬로펀딩ID : 내용
$pdf->SetXY(160, 165.5);
$pdf->Write(0, $member['mb_id']);

//투자금리 : 내용
$pdf->SetXY(160, 177.5);
$pdf->Write(0, sprintf("%0.2f", $PRDT['invest_return']).'%');

//대출실행일 : 내용
$pdf->SetXY(160, 189);
$pdf->Write(0, date("Y.m.d", strtotime($PRDT['loan_start_date'])));

$pdf->lastPage();
//header('Content-Type: application/pdf; charset=utf-8');
//header('Content-disposition: inline; filename="원리금수취증서.pdf"', true);
//header('Content-type: application/pdf');
$pdf->Output($cert_idx.'.pdf', 'I');