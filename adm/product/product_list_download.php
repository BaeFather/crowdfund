<?
/**
 * 투자상품 목록 다운로드
 */
$sub_menu = "600100";
include_once('./_common.php');



while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$CATEGORY['1']  = " AND A.category='1'";
$CATEGORY['2']  = " AND A.category='2'";
$CATEGORY['2A'] = " AND A.category='2' AND A.mortgage_guarantees=''";
$CATEGORY['2B'] = " AND A.category='2' AND A.mortgage_guarantees='1'";
$CATEGORY['3']  = " AND A.category='3'";
$CATEGORY['3A'] = " AND A.category='3' AND A.category2='1'";
$CATEGORY['3B'] = " AND A.category='3' AND A.category2='2'";

$ST = $_REQUEST['ST'];
$st_count = count($ST);

$sql_search = " 1=1 ";
if($ai_grp_idx) $sql_search.= " AND A.ai_grp_idx='$ai_grp_idx'";
if($gr_idx)    $sql_search.= " AND A.gr_idx='$gr_idx'";

$date = date('Y-m-d H:i:s');
if ($prd_ready=="Y") {
	$sql_srch1 = " (A.open_datetime <= '$date' AND A.invest_end_date='' AND A.end_datetime >= '$date' AND A.start_datetime>'$date') " ; // 대기중 상품
}
if ($prd_ready2=="Y") {
	$sql_srch2 = " (A.open_datetime > '$date') " ; // 상품 준비중
	if ($sql_srch1) $sql_srch2 = " OR ".$sql_srch2;
}
if ($prd_inving=="Y") {
	$sql_srch3 = " (A.open_datetime <= '$date' AND A.start_datetime<'$date' AND A.end_datetime>'$date' AND A.state='' AND A.invest_end_date='') " ; // 투자금 모집중
	if ($sql_srch1 || $sql_srch2) $sql_srch3 = " OR ".$sql_srch3;
}
if ($prd_invend=="Y") {
	$sql_srch4 = " (A.open_datetime <= '$date' AND A.start_datetime<'$date' AND A.end_datetime>'$date' AND A.state='' AND A.invest_end_date!='') " ; // 투자금 모집완료
	if ($sql_srch1 || $sql_srch2 || $sql_srch3) $sql_srch4 = " OR ".$sql_srch4;
}
if ($sql_srch1 || $sql_srch2 || $sql_srch3 || $sql_srch4) {
	if (!$st_count) {
		$sql_search .= " AND ($sql_srch1 $sql_srch2 $sql_srch3 $sql_srch4) ";
	} else {
		$n_stat_str = " OR ($sql_srch1 $sql_srch2 $sql_srch3 $sql_srch4) ";
	}
}

$st_str = "";
if($st_count) {
	$sql_search.= " AND (A.state IN(";
	$st_str.="&";
	for($i=0,$j=1;$i<$st_count;$i++,$j++) {
		$sql_search.= "'".$ST[$i]."'";
		$sql_search.= ($j<$st_count) ? ",":"";

		$st_str.= "ST[]={$ST[$i]}";
		$st_str.= ($j<$st_count) ? "&" : "";
	}
	$sql_search.= ") ".$n_stat_str . " )";
}

if($category) $sql_search.= $CATEGORY[$category];
if($loan_interest_type) {	$sql_search.= ($loan_interest_type=='def') ? " AND A.loan_interest_type='0'" : " AND A.loan_interest_type='$loan_interest_type'"; } // 대출이자수급방식
if($loan_usefee_type) $sql_search.= " AND A.loan_usefee_type='$loan_usefee_type'";					// 대출자 플랫폼수수료 징수방식
if($invest_usefee_type) $sql_search.= " AND A.invest_usefee_type='$invest_usefee_type'";		// 투자자 플랫폼수수료 징수방식
if($loan_mb_no) $sql_search.= " AND A.loan_mb_no = '$loan_mb_no'";
if($display) $sql_search.= " AND A.display = '$display'";
if($platform) {
	$sql_search.= ($flatform=='null') ? " AND A.platform=''" : " AND A.platform LIKE '%".$platform."%'";
}
if($auto_invest) $sql_search.= " AND A.ai_grp_idx!=''";		//자동투자
if($purchase_guarantees) $sql_search.= " AND A.purchase_guarantees='$purchase_guarantees'";		//채권매입보증
if($advanced_payment) $sql_search.= " AND A.advanced_payment='$advanced_payment'";			//선지급상품
if($portfolio) $sql_search.= " AND A.portfolio='$portfolio'";			//선지급상품
if($success_example) $sql_search.= " AND A.success_example='$success_example'";	 //투자성공사례지정상품
if($popular_goods) $sql_search.= " AND A.popular_goods='$popular_goods'";	 //인기상품
if($advance_invest) $sql_search.= " AND A.advance_invest='$advance_invest'";	 //사전투자설정상품
if($isConsor) $sql_search.= " AND A.isConsor='$isConsor'";	 //사전투자설정상품
if($ib_trust) $sql_search.= " AND A.ib_trust='$ib_trust'";	 //신한예치금신탁운용상품
if($only_vip) $sql_search.= " AND A.only_vip='$only_vip'";	 //투자자지정상품
if($isTest) $sql_search.= " AND A.isTest='$isTest'";	 //테스트상품여부
if($ptl_repay_prdt) $sql_search.= " AND (SELECT COUNT(idx) FROM cf_partial_redemption WHERE product_idx=A.idx) > 0";	 //원금일부상환상품
if($samount) $sql_search.= " AND A.recruit_amount >= $samount";
if($eamount) $sql_search.= " AND A.recruit_amount <= $eamount";
if($date_field) {
	if($sdate) $sql_search.= " AND $date_field >= '$sdate' ";
	if($edate) $sql_search.= " AND $date_field <= '$edate' ";
}
if($field && $keyword) {
	if( in_array($field, array('A.idx','A.start_num')) ) {
		$sql_search.= ( preg_match("/\,/", $keyword) ) ? " AND $field IN(".$keyword.")" : " AND $field='".$keyword."'";
	}
	else if($field=='B.mb_id') {
		$sql_search.= " AND B.mb_id LIKE '%".$keyword."%'";
	}
	else if($field=='mb_title') {
		$sql_search.= " AND (B.mb_name LIKE '%".$keyword."%' OR B.mb_co_name LIKE '%".$keyword."%')";
	}
	else {
		$sql_search.= ($field=='address') ? " AND (A.address LIKE '%$keyword%' OR A.address_detail LIKE '%$keyword%')" : " AND $field LIKE '%$keyword%' ";
	}
}


$sql_order = "";
if($sort_field) $sql_order.= $sort_field." ".$sort.", ";
$sql_order.= "A.idx DESC";


$sql = "
	SELECT
		A.*
		, B.mb_id
		, IF(B.member_type=2,B.mb_co_name,B.mb_name) AS mb_title
		, C.receiver, C.broker, C.commission_fee
		, ( SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS invest_count
		, ( SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS invest_amount
		, ( (A.recruit_amount/(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y'))*100 ) AS invest_percent
		, ( SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE product_idx=A.idx AND banking_date > '0000-00-00 00:00:00') AS paid_principal
		, ( SELECT IFNULL(SUM(interest),0) FROM cf_product_give WHERE product_idx=A.idx AND banking_date > '0000-00-00 00:00:00') AS paid_interest
		, ( SELECT IFNULL(SUM(fee),0) FROM cf_product_give WHERE product_idx=A.idx AND banking_date > '0000-00-00 00:00:00') AS paid_invest_usefee
		-- , ( SELECT IFNULL(SUM(amount),0) FROM cf_partial_redemption WHERE product_idx=A.idx ) AS ptl_repay_amount
	FROM
		cf_product A
	LEFT JOIN
		g5_member B  ON A.loan_mb_no=B.mb_no
	LEFT JOIN
		cf_product_container C  ON A.idx=C.product_idx
	WHERE
		$sql_search
	ORDER BY
		$sql_order";
//print_rr($sql, 'font-size:12px');
$result = sql_query($sql);

$list_count = $result->num_rows;


$SUM['recruit_amount']       = 0;  // 대출금
$SUM['invest_count']         = 0;  // 투자자수
$SUM['invest_amount']        = 0;  // 투자금액
$SUM['paid_principal']       = 0;  // 상환원금
$SUM['remain_principal']     = 0;  // 잔여원금
$SUM['paid_interest']        = 0;  // 상환이자
$SUM['paid_invest_usefee']   = 0;  // 상환투자자플랫폼이용료 합계
$SUM['commission_amount']    = 0;  // 중계수수료 합산
$SUM['invest_usefee_amount'] = 0;  // 투자자수수료 예정액 합계
$SUM['loan_usefee_amount']   = 0;  // 대출자수수료 예정액 합계

for($i=0; $i<$list_count; $i++) {
	$ROW = sql_fetch_array($result);

	$sYear = substr($ROW['loan_start_date'], 0, 4);		// 대출시작일 년도
	$eYear = substr($ROW['loan_end_date'], 0, 4);			// 대출종료일 년도

	$LIST[$i]['idx']                = $ROW['idx'];
	$LIST[$i]['title']              = $ROW['title'];

	$LIST[$i]['recruit_amount']     = $ROW['recruit_amount'];
	$LIST[$i]['invest_count']       = $ROW['invest_count'];
	$LIST[$i]['invest_amount']      = $ROW['invest_amount'];
	$LIST[$i]['paid_principal']     = $ROW['paid_principal'];
	$LIST[$i]['remain_principal']   = $ROW['recruit_amount'] - $ROW['paid_principal'];
	$LIST[$i]['paid_invest_usefee'] = $ROW['paid_invest_usefee'];
	//$LIST[$i]['ptl_repay_amount']   = $ROW['ptl_repay_amount'];

	$LIST[$i]['mb_title']   = $ROW['mb_title'];

	$LIST[$i]['recruit_period_start'] = $ROW['recruit_period_start'];
	$LIST[$i]['recruit_period_end']   = $ROW['recruit_period_end'];
	$LIST[$i]['invest_end_date']      = $ROW['invest_end_date'];

	$SUM['recruit_amount']     += $ROW['recruit_amount'];
	$SUM['invest_count']       += $ROW['invest_count'];
	$SUM['invest_amount']      += $ROW['invest_amount'];
	$SUM['paid_principal']     += $ROW['paid_principal'];
	$SUM['remain_principal']   += $LIST[$i]['remain_principal'];
	$SUM['paid_invest_usefee'] += $ROW['paid_invest_usefee'];
	//$SUM['ptl_repay_amount']   += $ROW['ptl_repay_amount'];


	$LIST[$i]['loan_interest_rate'] = floatRtrim($ROW['loan_interest_rate']).'%';

	switch($ROW['loan_interest_type']) {
		case '1' : $LIST[$i]['loan_interest_type'] = '선취';				break;
		case '2' : $LIST[$i]['loan_interest_type'] = '부분선취';		break;
		default  : $LIST[$i]['loan_interest_type'] = '후취';				break;
	}

	$LIST[$i]['loan_usefee_type']   = ($ROW['loan_usefee_type'] == 'A') ? "후취" : '선취';		// A:후취(월별분할징수) : B:선취(일시징수)


	/////////////////////////////////////////////////////////////////////////////
	// 투자자 플랫폼이용료 (주!! 월별 정산시 발생하는 원단위미만 절사를 이곳에서는 생략하므로 예정금액과 실처리액 간 약간의 차액이 발생함에 주의)
	// :: 산출방식 ::
	//  - ((대출금 x 설정요율 / 100) / 연일수) x 투자일수
	//  - 원단위 미만 절사
	/////////////////////////////////////////////////////////////////////////////
	$total_invest_usefee_amount = $ROW['recruit_amount'] * $ROW['invest_usefee'] / 100;		// 플랫폼수수료 총액 (투자자용)
	$loan_usefee_amount = 0;
	$stdDayCnt = $repayDayCnt = $dayInvestUsefeeAmt = NULL;

	if($ROW['invest_usefee'] > 0) {

		if($sYear==$eYear) {

			$stdDayCnt   = ( in_array($sYear, $CONF['LEAP_YEAR']) ) ? 366 : 365;
			$repayDayCnt = repayDayCount($ROW['loan_start_date'], $ROW['loan_end_date']);		// 대출기간일수

			$dayInvestUsefeeAmt = $total_invest_usefee_amount / $stdDayCnt;		// 일별수수료
			$invest_usefee_amount = $dayInvestUsefeeAmt * $repayDayCnt;

		}
		else {

			$invest_usefee_amount = 0;
			for($y=$sYear; $y<=$eYear; $y++) {

				$stdDayCnt = ( in_array($y, $CONF['LEAP_YEAR']) ) ? 366 : 365;
				$dayInvestUsefeeAmt = $total_invest_usefee_amount / $stdDayCnt;			// 일별수수료

				if($y < $eYear) {
					$repaySdate  = ($y==$sYear) ? $ROW['loan_start_date'] : $y.'-01-01';
					$repayEdate  = $y.'-12-31';
					$repayDayCnt = repayDayCount($repaySdate, $repayEdate) + 1;
				}
				else {
					$repaySdate  = $y.'-01-01';
					$repayEdate  = $ROW['loan_end_date'];
					$repayDayCnt = repayDayCount($repaySdate, $repayEdate);
				}

				$invest_usefee_amount += $dayInvestUsefeeAmt * $repayDayCnt;

			}

		}

	}

	$LIST[$i]['invest_usefee_amount'] = floor($invest_usefee_amount);
	$SUM['invest_usefee_amount'] += $LIST[$i]['invest_usefee_amount'];



	/////////////////////////////////////////////////////////////////////////////
	// 대출자 플랫폼이용료 (주!! 월별 정산시 발생하는 원단위미만 절사를 이곳에서는 생략하므로 예정금액과 실처리액 간 약간의 차액이 발생함에 주의)
	// :: 산출방식 ::
	//  - 소상공인 매출채권 : ((대출금 x 설정요율 / 100) / 연일수) x 투자일수
	//  - 그외 대출금 x 설정요율 / 100
	//  - 원단위 미만 절사
	/////////////////////////////////////////////////////////////////////////////

	$LIST[$i]['loan_usefee'] = $ROW['loan_usefee'];

	$loan_usefee_amount = 0;
	$total_loan_usefee_amount = $ROW['recruit_amount'] * $ROW['loan_usefee'] / 100;				// 플랫폼수수료 총액 (대출자용)

	if($ROW['category']=='3' && $ROW['category2']=='1') {

		$stdDayCnt = $repayDayCnt = $dayLoanUsefeeAmt = NULL;

		if($sYear==$eYear) {

			//대출실행일과 종료일이 동일년도일 경우
			$stdDayCnt   = ( in_array($sYear, $CONF['LEAP_YEAR']) ) ? 366 : 365;
			$repayDayCnt = repayDayCount($ROW['loan_start_date'], $ROW['loan_end_date']);		// 대출기간일수

			$dayLoanUsefeeAmt = $total_loan_usefee_amount / $stdDayCnt;
			$loan_usefee_amount = $dayLoanUsefeeAmt * $repayDayCnt;
			//echo "<span style='font-size:12px;line-height:13px;color:#2222FF'>" . $ROW['idx'] . " ::: " . $dayLoanUsefeeAmt . " * " . $repayDayCnt . " = " . $loan_usefee_amount . "</span><br/>\n";

		}
		else {

			for($y=$sYear; $y<=$eYear; $y++) {

				$stdDayCnt = ( in_array($y, $CONF['LEAP_YEAR']) ) ? 366 : 365;
				$dayLoanUsefeeAmt = $total_loan_usefee_amount / $stdDayCnt;			// 일별수수료

				if($y < $eYear) {
					$repaySdate  = ($y==$sYear) ? $ROW['loan_start_date'] : $y.'-01-01';
					$repayEdate  = $y.'-12-31';
					$repayDayCnt = repayDayCount($repaySdate, $repayEdate) + 1;
				}
				else {
					$repaySdate  = $y.'-01-01';
					$repayEdate  = $ROW['loan_end_date'];
					$repayDayCnt = repayDayCount($repaySdate, $repayEdate);
				}

				$loan_usefee_amount += $dayLoanUsefeeAmt * $repayDayCnt;
				//echo "<span style='font-size:12px;line-height:13px;color:#FF2222'>" . $ROW['idx'] . " ::: " . $dayLoanUsefeeAmt . " * " . $repayDayCnt . " = " . $loan_usefee_amount . "</span><br/>\n";

			}

		}

		$LIST[$i]['loan_usefee_amount'] = floor($loan_usefee_amount);

	}
	else {
		$LIST[$i]['loan_usefee_amount'] = floor($total_loan_usefee_amount);
	}

	$SUM['loan_usefee_amount'] += $LIST[$i]['loan_usefee_amount'];




	if($ROW['loan_usefee_type'] == 'A') {
		$LIST[$i]['loan_usefee_repay_count']  = $ROW['loan_usefee_repay_count'];
		$LIST[$i]['loan_usefee_amount_month'] = floor((($ROW['loan_usefee'] * $ROW['recruit_amount']) / 100) / $ROW['loan_usefee_repay_count']);		// 후취시 전체 플랫폼이용료를 24개월로 나눈 값을 월별로 수취한다.
	}
	else {
		$LIST[$i]['loan_usefee_repay_count']  = 0;
		$LIST[$i]['loan_usefee_amount_month'] = 0;
	}


	// 투자자 플랫폼이용료
	$LIST[$i]['invest_usefee'] = floatRtrim($ROW['invest_usefee'])."%";



	$LIST[$i]['state'] = '';
	if($ROW['state']) {
		if($ROW['state'] == '1')      $LIST[$i]['state'] = '상환중';
		else if($ROW['state'] == '2') $LIST[$i]['state'] = '상환';
		else if($ROW['state'] == '3') $LIST[$i]['state'] = '모집실패';
		else if($ROW['state'] == '4') $LIST[$i]['state'] = '부실';
		else if($ROW['state'] == '5') $LIST[$i]['state'] = '상환';
		else if($ROW['state'] == '6') $LIST[$i]['state'] = '취소(기표전)';
		else if($ROW['state'] == '7') $LIST[$i]['state'] = '취소(기표후)';
	}
	else {
		if($LIST[$i]['open_datetime'] > G5_TIME_YMDHIS) {
			$LIST[$i]['state'] = '상품준비중';
		}
		else {
			if($LIST[$i]['end_datetime'] < G5_TIME_YMDHIS) {
				if($LIST[$i]['recruit_amount'] > $ROW['invest_amount']) $LIST[$i]['state'] = '모집실패';
			}
			else {
				if($LIST[$i]['start_datetime'] < G5_TIME_YMDHIS && $LIST[$i]['end_datetime'] > G5_TIME_YMDHIS) {
					$LIST[$i]['state'] = ($LIST[$i]['recruit_amount'] > $ROW['invest_amount']) ? '모집중' : '모집완료';
				}
			}
		}
	}

	$LIST[$i]['loan_start_date'] = $ROW['loan_start_date'];
	$LIST[$i]['loan_end_date']   = $ROW['loan_end_date'];

	if($ROW['invest_days'] > 0 && $ROW['invest_days'] < 30) {
		$LIST[$i]['invest_period'] = $ROW['invest_days'] . '일';
		$LIST[$i]['repay_turn'] = repayTurnCount($ROW['loan_start_date'], $ROW['loan_end_date'], false, true);		// 이자상환회차
	}
	else {
		$LIST[$i]['invest_period'] = $ROW['invest_period'] . '개월';
		$LIST[$i]['repay_turn'] = repayTurnCount($ROW['loan_start_date'], $ROW['loan_end_date'], false, false);		// 이자상환회차
	}



	$LIST[$i]['broker'] = $ROW['broker'];

	// 중계수수료금액
	$LIST[$i]['commission_fee'] = floatRtrim($ROW['commission_fee'])."%";

	if( $ROW['commission_fee'] > 0 ) {
		$LIST[$i]['commission_fee_amount'] = ($ROW['recruit_amount'] * $ROW['commission_fee']) / 100;
	}
	else {
		$LIST[$i]['commission_fee_amount'] = 0;
	}

	$SUM['commission_fee_amount'] += $LIST[$i]['commission_fee_amount'];


	$LIST[$i]['receiver'] = $ROW['receiver'];

	unset($ROW);
}

$repay_amount_perc =  @( ($SUM['paid_principal'] / $SUM['invest_amount']) * 100 );
$remain_amount_perc  =  @( (($SUM['remain_principal']) / $SUM['invest_amount']) * 100 );


sql_free_result($result);

//print_rr($LIST, 'font-size:8pt'); exit;


$filename = "헬로펀딩 상품 진행현황_" . date('YmdHis');
$filename = iconv('UTF-8', 'EUC-KR', $filename);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
header('Cache-Control: max-age=0');



?>
<table border='1' style="border-collapse:collapse;font-size:10pt;">
	<tr>
		<td colspan="6" rowspan="3" style="text-align:center;font-size:16pt">헬로펀딩 상품 진행현황</td>
		<td style="text-align:center;background:#D8D8D8;">대출총액</td>
		<td style="text-align:right;"><?=number_format($SUM['recruit_amount'])?></td>
		<td style="text-align:center;">-</td>
	</tr>
	<tr>
		<td style="text-align:center;background:#D8D8D8;">상환완료</td>
		<td style="text-align:right;"><?=number_format($SUM['paid_principal'])?></td>
		<td style="text-align:right;"><?=floatRtrim(sprintf('%.2f', $repay_amount_perc))?>%</td>
	</tr>
	<tr>
		<td style="text-align:center;background:#D8D8D8;">대출잔액</td>
		<td style="text-align:right;"><?=number_format($SUM['remain_principal'])?></td>
		<td style="text-align:right;"><?=floatRtrim(sprintf('%.2f', $remain_amount_perc))?>%</td>
	</tr>
</table>
<br>

<table border='1' style="border-collapse:collapse;font-size:10pt;">
	<tr>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">NO</td>

		<td colspan="2" style="text-align:center;background:#D8D8D8">대출상품</td>

		<td colspan="3" style="text-align:center;background:#D8D8D8">대출금</td>

		<td rowspan="2" style="text-align:center;background:#D8D8D8">이자율</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">이자<br/>징수방식</td>

		<td colspan="5" style="text-align:center;background:#D8D8D8">대출자 플랫폼 수수료</td>

		<td rowspan="2" style="text-align:center;background:#D8D8D8">대출실행일</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">대출종료일</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">모집시작일</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">모집종료일</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">마감일</td>

		<td rowspan="2" style="text-align:center;background:#D8D8D8">대출<br/>계약기간</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">전체<br/>상환회차</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">상태</td>

		<td colspan="3" style="text-align:center;background:#D8D8D8">중개수수료</td>

		<td rowspan="2" style="text-align:center;background:#D8D8D8">접수자</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">투자자수</td>

		<td colspan="3" style="text-align:center;background:#D8D8D8">투자자 플랫폼 수수료</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">차주명</td>
		<td rowspan="2" style="text-align:center;background:#D8D8D8">태그</td>
	</tr>
	<tr>
		<td style="text-align:center;background:#D8D8D8">품번</td>
		<td style="text-align:center;background:#D8D8D8">품명</td>

		<td style="text-align:center;background:#D8D8D8">총액</td>
		<td style="text-align:center;background:#D8D8D8">납입액</td>
		<td style="text-align:center;background:#D8D8D8">잔액</td>

		<td style="text-align:center;background:#D8D8D8">구분</td>
		<td style="text-align:center;background:#D8D8D8">요율</td>
		<td style="text-align:center;background:#D8D8D8">총액</td>
		<td style="text-align:center;background:#D8D8D8">분납회수</td>
		<td style="text-align:center;background:#D8D8D8">분납액</td>

		<td style="text-align:center;background:#D8D8D8">중개자</td>
		<td style="text-align:center;background:#D8D8D8">요율</td>
		<td style="text-align:center;background:#D8D8D8">금액</td>

		<td style="text-align:center;background:#D8D8D8">요율</td>
		<td style="text-align:center;background:#D8D8D8">예정</td>
		<td style="text-align:center;background:#D8D8D8">수취</td>

	</tr>

	<tr>
		<td style="background:#FDE9D9;text-align:center;color:red;">합계</td>

		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>

		<td style="background:#FDE9D9;text-align:right;color:red;"><?=number_format($SUM['recruit_amount'])?></td>
		<td style="background:#FDE9D9;text-align:right;color:red;"><?=number_format($SUM['paid_principal'])?></td>
		<td style="background:#FDE9D9;text-align:right;color:red;"><?=number_format($SUM['remain_principal'])?></td>

		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>

		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style='background:#FDE9D9;text-align:right;color:red;'><?=number_format($SUM['loan_usefee_amount'])?></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>

		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>

		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;"></td>
		<td style='background:#FDE9D9;text-align:right;color:red;'><?=number_format($SUM['commission_fee_amount'])?></td>

		<td style="background:#FDE9D9;"></td>
		<td style='background:#FDE9D9;text-align:right;color:red;'><?=number_format($SUM['invest_count'])?></td>

		<td style="background:#FDE9D9;"></td>
		<td style="background:#FDE9D9;text-align:right;color:red;"><?=number_format($SUM['invest_usefee_amount'])?></td>
		<td style='background:#FDE9D9;text-align:right;color:red;'><?=number_format($SUM['paid_invest_usefee'])?></td>

		<td style='background:#FDE9D9;text-align:right;color:red;'></td>
		<td style='background:#FDE9D9;text-align:right;color:red;'></td>
	</tr>

<?
if($list_count) {
	$num = $list_count;
	for($i=0; $i<$list_count; $i++) {

		//$print_loan_start_date = ($LIST[$i]['loan_start_date']>'0000.00.00') ? preg_replace("/-/", ".", $LIST[$i]['loan_start_date']) : '';
		//$print_loan_end_date   = ($LIST[$i]['loan_end_date']>'0000.00.00') ? preg_replace("/-/", ".", $LIST[$i]['loan_end_date']) : '';

		$print_loan_usefee_repay_count = ($LIST[$i]['loan_usefee_repay_count'] > 0) ? $LIST[$i]['loan_usefee_repay_count'] . '회' : '';

		$print_repay_turn = ($LIST[$i]['repay_turn'] > 0) ? $LIST[$i]['repay_turn'] . '회차' : '';

		echo "	<tr>
		<td style='text-align:center;'>".$num."</td>

		<td style='text-align:center;'>".$LIST[$i]['idx']."</td>
		<td>".$LIST[$i]['title']."</td>

		<td style='text-align:right;' >".number_format($LIST[$i]['recruit_amount'])."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['paid_principal'])."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['remain_principal'])."</td>


		<td style='text-align:right;' >".$LIST[$i]['loan_interest_rate']."</td>
		<td style='text-align:center;'>".$LIST[$i]['loan_interest_type']."</td>

		<td style='text-align:center;'>".$LIST[$i]['loan_usefee_type']."</td>
		<td style='text-align:right;' >".floatRtrim(sprintf('%.3f', $LIST[$i]['loan_usefee']))."%</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['loan_usefee_amount'])."</td>
		<td style='text-align:center;'>".$print_loan_usefee_repay_count."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['loan_usefee_amount_month'])."</td>

		<td style='text-align:center;'>".$LIST[$i]['loan_start_date']."</td>
		<td style='text-align:center;'>".$LIST[$i]['loan_end_date']."</td>
		<td style='text-align:center;'>".$LIST[$i]['recruit_period_start']."</td>
		<td style='text-align:center;'>".$LIST[$i]['recruit_period_end']."</td>
		<td style='text-align:center;'>".$LIST[$i]['invest_end_date']."</td>
		<td style='text-align:center;'>".$LIST[$i]['invest_period']."</td>
		<td style='text-align:center;'>".$print_repay_turn."</td>
		<td style='text-align:center;'>".$LIST[$i]['state']."</td>

		<td style='text-align:center;'>".$LIST[$i]['broker']."</td>
		<td style='text-align:right;' >".$LIST[$i]['commission_fee']."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['commission_fee_amount'])."</td>

		<td style='text-align:center;'>".$LIST[$i]['receiver']."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['invest_count'])."</td>

		<td style='text-align:right;' >".floatRtrim(sprintf('%.3f', $LIST[$i]['invest_usefee']))."%</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['invest_usefee_amount'])."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['paid_invest_usefee'])."</td>
		<td style='text-align:center;' >".$LIST[$i]['mb_title']."</td>
		<td style='text-align:left;' ></td>
	</tr>\n";

		$num--;
	}
}

echo "</table>\n";



sql_close();
exit;

?>