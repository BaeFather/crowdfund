<?
##########################
#		EXCEL 저장
##########################

$filename = "헬로펀딩 자기계산 투자관리_".date('Ymd');
$filename = iconv('UTF-8', 'EUC-KR', $filename);

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
header('Cache-Control: max-age=0');

include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }


$POST_SQL = stripslashes($_POST['sql']);
//echo '<pre>'.$POST_SQL.'</pre>';

$result = sql_query($POST_SQL);
$rcount = sql_num_rows($result);

// 총 투자잔액
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);

	$remain_price = $LIST[$i]['invest_amount'] - $LIST[$i]['paid_amount'];	
	$tot_remain += $remain_price;
	$tot_recruit_price += $LIST[$i]['recruit_amount'];
	$tot_invest_price += $LIST[$i]['invest_amount'];
}


$EXCEL_STR = '
<table border="1">
	<thead style="text-align: center;">
		<tr style="background-color: #ffeb3b;">
			<th>NO</th>
			<th>카테고리</th>
			<th>품번</th>
			<th>상품명</th>
			<th>차입자</th>
			<th>LTV</th>
			<th>진행상태</th>
			<th>투자기간</th>
			<th>투자일</th>
			<th>모집금액</th>
			<th>투자금액</th>
			<th>투자비율</th>
			<th>투자잔액</th>
		</tr>
	</thead>
	<tbody>
		<tr align="right" style="background-color: #ddd; color: red;">
			<td colspan="9">총 '.$rcount.'건</td>
			<td>'.number_format($tot_recruit_price).'</td>
			<td>'.number_format($tot_invest_price).'</td>
			<td></td>
			<td>'.number_format($tot_remain).'</td>
		</tr>
	';
	
	$num = $rcount;

	for($i=0, $j=$num; $i<$rcount; $i++,$j--) {
		
		if($LIST[$i]['state']) {
			switch($LIST[$i]['state']) {
				case '1': $state='상환중'; break;
				case '2': $state='정상상환'; break;
				case '3': $state='모집실패'; break;
				case '4': $state='부실'; break;
				case '5': $state='중도상환'; break;
				case '6': $state='대출취소(기표전)'; break;
				case '7': $state='대출취소(기표후)'; break;
				case '8': $state='연체'; break;
				case '9': $state='부도(상환불가)'; break;
			}
		}

		if($LIST[$i]['category'] == '2' && $LIST[$i]['mortgage_guarantees'] == '') {
			$category = "PF";
		} else if($LIST[$i]['category'] == '2' && $LIST[$i]['mortgage_guarantees'] == '1') {
			$category = "주택담보대출";
		} else if($LIST[$i]['category'] == '3') {
			$category = "매출채권";
		} else if($LIST[$i]['category'] == '1') {
			$category = "동산";
		}
		
		$remain_price = $LIST[$i]['invest_amount'] - $LIST[$i]['paid_amount'];

		$EXCEL_STR.= '
			<tr align="center">
				<td>'.$j.'</td>
				<td>'.$category.'</td>
				<td>'.$LIST[$i]["product_idx"].'</td>
				<td align="left">'.$LIST[$i]["title"].'</td>
				<td>'.$LIST[$i]["mb_name"].'</td>
				<td align="right">'.$LIST[$i]["ltv"].'%</td>
				<td>'.$state.'</td>
				<td>'.$LIST[$i]["loan_start_date"].'~'.$LIST[$i]["loan_end_date"].'</td>
				<td>'.$LIST[$i]["insert_date"].'</td>
				<td align="right">'.number_format($LIST[$i]["recruit_amount"]).'</td>
				<td align="right">'.number_format($LIST[$i]["invest_amount"]).'</td>
				<td align="right">'.number_format($LIST[$i]["invest_perc"]).'%</td>
				<td align="right">'.number_format($remain_price).'</td>
			</tr>
			';
	}

$EXCEL_STR.= '
	</tbody>
</table>
';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;

?>