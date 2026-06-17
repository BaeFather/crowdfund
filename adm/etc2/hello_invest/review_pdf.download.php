<?
##########################
#		PDF 저장
##########################

include_once('./_common.php');
require_once G5_PLUGIN_PATH.'/tcpdf/tcpdf.php';

$idx = $_REQUEST['idx'];

$sql = "
	SELECT
		*
	FROM
		hello_self_review
	WHERE
		idx = '$idx'
";
$row = sql_fetch($sql);

// 파일 이름
$file_name = 'review_'.substr($row['h2_title'], 2, 6);


if($row['h2_type'] == '1') {
	$type = '주택담보대출';
} else if($row['h2_type'] == '2') {
	$type = '매출채권';
} else if($row['h2_type'] == '3') {
	$type = 'PF';
} else {
	$type = '동산';
}

$res1  = ($res1 == 'Y') ? '적정' : '부적정';
$res2  = ($res2 == 'Y') ? '적정' : '부적정';
$res3  = ($res3 == 'Y') ? '적정' : '부적정';
$res4  = ($res4 == 'Y') ? '적정' : '부적정';
$res5  = ($res5 == 'Y') ? '적정' : '부적정';
$resYN = ($row['resYN'] == 'Y') ? '적정' : '부적정';

if($row['reg_date']) {
	$reg_date = substr(str_replace('-', '.', $row['reg_date']), 0, -9);
}



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// font setting
$pdf->SetFont('nanumgothic', '', 10);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);


// Add a page
$pdf->AddPage();

// Set some content to print
$html = '
<style>
h4 {font-size: 12px;}
table {color: #333; border: 0.5px solid #ccc; text-align: center; font-size: 9px;}
table tr th {background-color: #eee;}
table th, table td {border: 0.5px solid #ccc;}
table tr td {line-height: 1.5;}
p.txt {font-size: 8px; line-height: 1.5; color: #666;}
p.date {text-align: center; font-size: 18px; line-height: 3;}
</style>';
$html.= '<h4>1. 자기자본 및 연체율 검토</h4>';
$html.= '
<table>
	<thead>
		<tr>
			<th colspan="2" width="30%">구분</th>
			<th width="25%">값</th>
			<th width="35%">기준</th>
			<th width="10%">판단</th>
		</tr>	</thead>
	<tbody>
		<tr>
			<td colspan="2" width="30%">최근 결산(2020.12) 기준 자기자본</td>
			<td align="right" width="25%">'.$invest_price.'</td>
			<td width="35%">자기자본 100%내의 범위에서만 투자 가능</td>
			<td width="10%"></td>
		</tr>
		<tr>
			<td rowspan="2" width="25%">검토 시점의 자기계산투자 총 잔액<br>(자기자본 대비 자기계산투자 잔액비)</td>
			<td width="5%">금</td>
			<td align="right">'.$remain.'</td>
			<td rowspan="2">자기자본 100%내의 범위에서만 투자 가능<br>(100% 초과 시 부적정)</td>
			<td rowspan="2">'.$res1.'</td>
		</tr>
		<tr>
			<td>비율</td>
			<td align="right">'.$perc.'%</td>
		</tr>
		<tr>
			<td colspan="2">검토 시점의 전체 연체율</td>
			<td align="right">'.$overdue_perc.'%</td>
			<td>연체율 10% 초과 시 부적정</td>
			<td>'.$res2.'</td>
		</tr>
	</tbody>
</table>';
$html.= '<br />';
$html.= '<h4>2. LTV 및 동일인 한도 검토</h4>';
$html.= '
<table>
	<thead>
		<tr>
			<th colspan="2" width="30%">구분</th>
			<th width="25%">값</th>
			<th width="35%">기준</th>
			<th width="10%">판단</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="2" width="30%">상품명</td>
			<td width="25%">'.$row['h2_title'].'</td>
			<td width="35%">-</td>
			<td width="10%"></td>
		</tr>
		<tr>
			<td colspan="2">상품구분</td>
			<td>'.$type.'</td>
			<td>구분 : 주택담보대출(주담대) / 매출채권 / PF</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="2">총 모집금액</td>
			<td align="right">'.$recruit_amount.'</td>
			<td>-</td>
			<td></td>
		</tr>
		<tr>
			<td width="25%">검토일 기준 모집된 금액</td>
			<td width="5%">금</td>
			<td align="right">'.$live_amount.'</td>
			<td rowspan="4">총 모집금액 대비 모집된 금액이 80%이상 시 투자가능</td>
			<td rowspan="4">'.$res3.'</td>
		</tr>
		<tr>
			<td>총 모집금액 대비 모집된 금액의 비율</td>
			<td>비율</td>
			<td align="right">'.$tot_perc.'%</td>
		</tr>
		<tr>
			<td>자기계산 투자 요청 금액</td>
			<td>금</td>
			<td align="right">'.$request_price.'</td>
		</tr>
		<tr>
			<td>총 모집금액 대비 자기계산 투자비율</td>
			<td>비율</td>
			<td align="right">'.$hello_perc.'%</td>
		</tr>
		<tr>
			<td colspan="2">LTV</td>
			<td align="right">'.$ltv.'%</td>
			<td>주택담보대출상품의 경우만 입력<br>LTV 70% 초과시 부적정</td>
			<td>'.$res4.'</td>
		</tr>
		<tr>
			<td colspan="2">차입자명</td>
			<td>'.$loan_mb_name.'</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>차입자에 대한 당사 자기계산투자 잔액</td>
			<td>금</td>
			<td align="right">'.$loan_remain.'</td>
			<td rowspan="2">동일차주에게 자기자본대비 5%이상 투자 부적정<br>(본 투자를 포함한 자기자본 대비 자기계산투자 잔액비가 5% 이하일 것)</td>
			<td rowspan="2">'.$res5.'</td>
		</tr>
		<tr>
			<td>(자기자본 대비 자기계산투자 잔액비)</td>
			<td>비율</td>
			<td align="right">'.$loan_perc.'%</td>
		</tr>
	</tbody>
</table>
';
$html.= '
<br /><br /><br />
<table width="20%">
	<tr>
		<th>검토결과</th>
	</tr>
	<tr>
		<td>'.$resYN.'</td>
	</tr>
</table>
<br />
';
$html.= '
<p class="txt">- 자기계산투자 적정성 검토서는 운영기획팀 작성한다.<br />
- 본 검토서 상 부적정 사항이 있는 경우 자기계산투자는 불가하다.<br />
- 검토자(작성자)는 본 내용을 정확하게 작성하여야 한다.<br />
- 검토자(작성자)는 본 내용을 작성하여 준법감시인에게 사전 승인을 얻어야한다.</p>
<br /><br /><br />
<p class="date">'.$reg_date.'</p>
';


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->lastPage();
// Close and output PDF document
$pdf->Output($file_name.'.pdf', 'D'); 
?>