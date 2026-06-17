<?php
/**
 * 원리금 수취권 증서 보기
 * 2018-03-07 PDF 다운로드 기능 추가
 * 2018-06-27 핀크를 요청시 출력기능 추가
 */
include_once("_common.php");
//if(!$member['mb_id']) msg_close();

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
		AND A.idx='".$idx."'
		AND A.invest_state='Y'";
$invest_query.= ($_REQUEST[strtoupper(MD5('member_idx'))]) ? " AND A.member_idx='".$_REQUEST[strtoupper(MD5('member_idx'))]."'" : " AND A.member_idx='".$member['mb_no']."'";
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
	case '1' : $repay_type = "원금만기 일시상환"; break;
	case '2' : $repay_type = "원리금 균등상환";   break;
	case '3' : $repay_type = "원금 균등상환";     break;
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

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>:::원리금수취증서:::</title>
    <script type="text/javascript" src="<?=G5_URL?>/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?=G5_URL?>/js/printThis-master/printThis.js"></script>
    <style>
        @charset "utf-8";
        @import url(//fonts.googleapis.com/earlyaccess/notosanskr.css);
        body { margin:0; background-color:#F0F0F0; }

        #wrap { width:100%; display:inline-block; }

        .menu { width:100%; height:30px; background-color:#364098; padding:10px 0 10px 0; text-align:left; }
        .menu .title { float:left;margin-left:18px; font-family:'Noto Sans KR';font-size:24px;line-height:24px;color:#fff; }
        .menu .btnArea { float:right; margin-right:35px; }
        .menu .btnArea a.btn_print { display:inline-block; min-width:80px; padding:0 15px; line-height:30px; text-align:center; font-size:15px; color:#555; border-radius:3px; background-color:#eee; cursor:pointer; }
        .menu .btnArea a.btn_print:active,
        .menu .btnArea a.btn_print:link,
        .menu .btnArea a.btn_print:hover { color:#000; background-color:#CCCCCC; }

        .menu .btnArea { float:right; margin-right:35px; }
        .menu .btnArea a.btn_download { display:inline-block; min-width:80px; padding:0 15px; line-height:30px; text-align:center; font-size:15px; color:#555; border-radius:3px; background-color:#eee; cursor:pointer; }
        .menu .btnArea a.btn_download:active,
        .menu .btnArea a.btn_download:link,
        .menu .btnArea a.btn_download:hover { color:#000; background-color:#CCCCCC; }

        .main { position:relative; width:100%; height:700px; margin-top:8px; overflow-x:hidden; overflow-y:scroll; }
        #printArea { position:relative; margin:0 auto; width:900px; height:1273px; }
        #printArea .main_image { position:absolute; z-index:1; width:100%; height:100%; }
        #printArea .cover      { position:absolute; z-index:2; width:100%; height:100%; display:inline-block; }

        .font01 { font-family:"Noto Sans KR"; font-size:24px; line-height:24px; font-weight:500; color:#000; }
        .font02 { font-family:"Noto Sans KR"; font-size:20px; line-height:20px; font-weight:500; color:#000; }
        .font03 { font-family:"Noto Sans KR"; font-size:16px; line-height:16px; font-weight:400; color:#000; }
        .font04 { font-family:"Noto Sans KR"; font-size:62px; line-height:62px; font-weight:500; color:#000; letter-spacing:0.2em; }
        .font05 { font-family:"Noto Sans KR"; font-size:16px; line-height:16px; font-weight:400; color:#5d5652; }
    </style>
</head>

<body onContextMenu="return false" onDragStart="return false" onSelectStart="return false">

<div id="wrap">
    <div class="menu">
        <div class="title"><img src="/images/deposit/printer.png" height="32" align="absmiddle"/> 원리금 수취권 증서 인쇄</div>
        <div class="btnArea">
            <a class="btn_download" onclick="download(<?php echo $idx;?>);">PDF 저장</a>
            <a class="btn_print">인쇄하기</a>
        </div>
    </div>
    <div class="main">
        <div id="printArea">
            <div class="main_image"><img src="/images/deposit/certificate_bg.jpg"/></div>
            <div class="cover">
                <div style="margin:150px 0 0 141px;"><span class="font01">증서번호: <?=$cert_idx;?></span></div>
                <div style="margin:50px auto; text-align:center;"><span class="font04">원리금 수취권 증서</span></div>
                <div style="margin:64px 0 0 141px;"><span class="font02">우선수익자: <?=($special_print_name) ? $special_print_name : $print_mb_name;?></span></div>
                <div style="margin:14px auto;">
                    <table width="611" align="center" border="0" style="border-collapse:collapse;" cellspacing="0" cellpadding="0">
                        <colgroup>
                            <col width="130">
                            <col width="481">
                        <colgroup>
                        <tr>
                            <th height="55" class="font03" scope="col">수익금액</th>
                            <th class="font03" scope="col">금 <?=number2korean($invest_amount);?> 원 + 이자</th>
                        </tr>
                        <tr>
                            <th rowspan="6" class="font03">대출 채권정보</th>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="212" scope="col" class="font03">상품코드</th>
                                        <th width="113" scope="col" class="font03">구분</th>
                                        <th width="152" scope="col" class="font03">상환방식</th>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="212" scope="col" class="font03" alt="상품코드"><?=$prdt_code;?></th>
                                        <th width="113" scope="col" class="font03" alt="상품구분"><?=($PRDT['category']=='2')?'부동산':'부동산외';?></th>
                                        <th width="152" scope="col" class="font03" alt="상환방식"><?=$repay_type;?></th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="212" scope="col" class="font03">대출금액</th>
                                        <th width="113" scope="col" class="font03">대출금리</th>
                                        <th width="152" scope="col" class="font03">대출기간</th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="212" scope="col" class="font03" alt="대출금액">금 <?=number2korean($INVEST['recruit_amount']);?> 원</th>
                                        <th width="113" scope="col" class="font03" alt="대출금리"><?=sprintf("%0.2f", $INVEST['loan_interest_rate']);?>%</th>
                                        <th width="152" scope="col" class="font03" alt="대출기간"><?=$PRDT['invest_period'];?>개월</th>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="329" class="font03" scope="col">대출기간</th>
                                        <th width="152" scope="col" class="font03">대출실행일</th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="329" class="font03" scope="col" alt="대출기간">
                                            <?=date("Y.m.d", strtotime($PRDT['loan_start_date']))?> ~
                                            <?=date("Y.m.d", strtotime($PRDT['loan_end_date']))?>
                                        </th>
                                        <th width="152" scope="col" class="font03" alt="대출실행일"><?=date("Y.m.d", strtotime($PRDT['loan_start_date']));?></th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th height="40" class="font03" scope="col">채권자</th>
                            <th class="font03" scope="col" alt="채권자"><?=($special_print_name) ? $special_print_name : $print_mb_name;?></th>
                        </tr>
                        <tr>
                            <th height="40" class="font03" scope="col">헬로펀딩 ID</th>
                            <th class="font03" scope="col" alt="헬로펀딩 ID"><?=$member['mb_id'];?></th>
                        </tr>
                        <tr>
                            <th height="40" class="font03" scope="col">투자금액</th>
                            <th height="40"><table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="220" class="font03" scope="col">금 <?=number2korean($INVEST['amount']);?> 원</th>
                                        <th width="113" class="font03" scope="col">투자금리</th>
                                        <th width="144" class="font03" scope="col"><?=sprintf("%0.2f", $PRDT['invest_return']);?>%</th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th height="40" class="font03" scope="col">모집기간</th>
                            <th height="40">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <th width="220" class="font03" scope="col"><?=date("Y.m.d", strtotime($PRDT['start_date']));?> ~ <?=date("Y.m.d", strtotime($PRDT['invest_end_date']));?></th>
                                        <th width="113" class="font03" scope="col">대출실행일</th>
                                        <th width="144" class="font03" scope="col"><?=date("Y.m.d", strtotime($PRDT['loan_start_date']));?></th>
                                    </tr>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th height="40" class="font03" scope="col">수익증서 발행자</th>
                            <th class="font03" scope="col">"<?=$PRDT['title'];?>"의 차입자</th>
                        </tr>
                        <tr>
                            <th height="40" class="font03" scope="col">수익증서 판매자</th>
                            <th class="font03" scope="col"><? if ($PRDT['loan_start_date'] >= '2021-08-27') echo "(주)헬로핀테크"; else if ($PRDT['loan_start_date'] < '2021-08-27') echo "(주)헬로크라우드대부"; ?></th>
                        </tr>
                    </table>
                </div>
                <div style="margin:20px auto; text-align:center;"><span class="font03">원리금 수취권 구매에 의하여 본증서를 발행합니다.</span></div>
                <div style="margin:20px auto; text-align:center;"><span class="font03"><?=date("Y년 m월 d일", strtotime($PRDT['loan_start_date']));?></span></div>
                <div style="margin:138px 0 0 141px;"><span class="font05">*조기상환시 사전고지 없이 상환일이 변동될 수 있습니다.</span></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
	$('.btn_print').click(function() {
		$('#printArea').printThis({
			debug: false,
			importCSS: false,
			importStyle: true,
			printContainer: true,
			pageTitle: '원리금 수취권 증서',
			removeInline: false
		});
	});
});

function download(idx){
		window.location = './principal_interest_certificate.php?idx='+idx;
}
</script>

</body>
</html>