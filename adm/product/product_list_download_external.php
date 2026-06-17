<?
/*
 * 대외보고자료 엑셀 다운로드
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
	if ($sql_srch1) $sql_srch2 = " or ".$sql_srch2;
}
if ($prd_inving=="Y") {
	$sql_srch3 = " (A.open_datetime <= '$date' AND A.start_datetime<'$date' AND A.end_datetime>'$date' AND A.state='' AND A.invest_end_date='') " ; // 투자금 모집중
	if ($sql_srch1 or $sql_srch2) $sql_srch3 = " or ".$sql_srch3;
}
if ($prd_invend=="Y") {
	$sql_srch4 = " (A.open_datetime <= '$date' AND A.start_datetime<'$date' AND A.end_datetime>'$date' AND A.state='' AND A.invest_end_date!='') " ; // 투자금 모집완료
	if ($sql_srch1 || $sql_srch2 || $sql_srch3) $sql_srch4 = " OR ".$sql_srch4;
}
if ($sql_srch1 or $sql_srch2 or $sql_srch3 or $sql_srch4) {
	if (!$st_count) {
		$sql_search .= " and ($sql_srch1 $sql_srch2 $sql_srch3 $sql_srch4) ";
	} else {
		$n_stat_str = " or ($sql_srch1 $sql_srch2 $sql_srch3 $sql_srch4) ";
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
		, B.mb_id, B.member_type
		, IF(B.member_type=2,B.mb_co_name,B.mb_name) AS mb_title
		, C.receiver, C.broker, C.commission_fee
		, D.mb_legal_num, D.credit_score, D.rating_cp, D.psnl_num1, D.psnl_num2, D.limit_amt
		, ( SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS invest_count
		, ( SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS invest_amount
		, ( SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE product_idx=A.idx ) AS paid_principal
		, ( SELECT IFNULL(SUM(interest),0) FROM cf_product_give WHERE product_idx=A.idx ) AS paid_interest
		, ( SELECT IFNULL(SUM(fee),0) FROM cf_product_give WHERE product_idx=A.idx ) AS paid_invest_usefee
		, ( SELECT IFNULL(SUM(amount),0) FROM cf_partial_redemption WHERE product_idx=A.idx ) AS ptl_repay_amount
	FROM
		cf_product A
	LEFT JOIN
		g5_member B  ON A.loan_mb_no=B.mb_no
	LEFT JOIN
		cf_product_container C  ON A.idx=C.product_idx
	LEFT JOIN
		cf_chaju D ON B.mb_no=D.mb_no
	WHERE
		$sql_search
	ORDER BY
		$sql_order";
//print_rr($sql, 'font-size:12px');
$result = sql_query($sql);

//echo "<pre>".$sql."</pre>"; die();

$list_count = sql_num_rows($result);


$SUM['invest_usefee_amount'] = 0;  // 투자자수수료 예정액 합계
$SUM['loan_usefee_amount']   = 0;  // 대출자수수료 예정액 합계

for($i=0; $i<$list_count; $i++) {
	$ROW = sql_fetch_array($result);

	$sYear = substr($ROW['loan_start_date'], 0, 4);		// 대출시작일 년도
	$eYear = substr($ROW['loan_end_date'], 0, 4);			// 대출종료일 년도

	$LIST[$i]['member_type']        = $ROW['member_type'];

	$LIST[$i]['idx']                = $ROW['idx'];
	$LIST[$i]['title']              = $ROW['title'];
	$LIST[$i]['recruit_amount']     = $ROW['recruit_amount'];
	$LIST[$i]['invest_count']       = $ROW['invest_count'];
	$LIST[$i]['invest_amount']      = $ROW['invest_amount'];
	$LIST[$i]['paid_principal']     = $ROW['paid_principal'];
	$LIST[$i]['paid_invest_usefee'] = $ROW['paid_invest_usefee'];
	$LIST[$i]['ptl_repay_amount']   = $ROW['ptl_repay_amount'];
	$LIST[$i]['category']			= $ROW['category'];
	$LIST[$i]['mortgage_guarantees']= $ROW['mortgage_guarantees'];
	$LIST[$i]['mb_title']			= $ROW['mb_title'];
	$LIST[$i]['mb_legal_num']		= $ROW['mb_legal_num'];
	$LIST[$i]['psnl_num1']			= $ROW['psnl_num1'];
	$LIST[$i]['psnl_num2']			= $ROW['psnl_num2'];
	$LIST[$i]['credit_score']		= $ROW['credit_score'];
	$LIST[$i]['rating_cp']			= $ROW['rating_cp'];
	$LIST[$i]['repay_acct_no']		= $ROW['repay_acct_no'];
	$LIST[$i]['loan_dep_acct_memo1'] = $ROW['loan_dep_acct_memo1'];
	$LIST[$i]['limit_amt']			= $ROW['limit_amt'];
	$LIST[$i]['overdue_rate']		= floatRtrim($ROW['overdue_rate']).'%';

	$LIST[$i]['loan_interest_rate'] = floatRtrim($ROW['loan_interest_rate']).'%';


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


	// 투자자 플랫폼이용료
	$LIST[$i]['invest_usefee'] = floatRtrim($ROW['invest_usefee'])."%";

	// 상태
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

	$LIST[$i]['loan_start_date'] = preg_replace("/-/", ".", $ROW['loan_start_date']);
	$LIST[$i]['loan_end_date']   = preg_replace("/-/", ".", $ROW['loan_end_date']);

}


sql_free_result($result);

//print_rr($LIST, 'font-size:8pt'); exit;


// 엑셀 다운로드
$filename = "헬로펀딩 대외보고자료_" . date('YmdHis');
$filename = iconv('UTF-8', 'EUC-KR', $filename);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
header('Cache-Control: max-age=0');


?>

<table border='1' style="border-collapse:collapse;font-size:8pt;">
	<tr>
		<td style="text-align:center;background:#9dc2e2">구분</td>

		<td rowspan="2" style="text-align:center;background:#9dc2e2">IDX</td>

		<td colspan="7" style="text-align:center;background:#9dc2e2">차주정보</td>

		<td colspan="10" style="text-align:center;background:#9dc2e2">상품정보</td>

		<td colspan="2" style="text-align:center;background:#9dc2e2">플랫폼 이용 수수료</td>

		<td rowspan="2" style="text-align:center;background:#9dc2e2">상태</td>

		<td rowspan="2" style="text-align:center;background:#9dc2e2">태그</td>

		<td rowspan="2" style="text-align:center;background:#9dc2e2">비고</td>
	</tr>
	<tr>
		<td style="text-align:center;background:#9dc2e2">NO</td>

		<td style="text-align:center;background:#9dc2e2">차주명</td>
		<td style="text-align:center;background:#9dc2e2">차주실명번호</td>
		<td style="text-align:center;background:#9dc2e2">차주구분</td>
		<td style="text-align:center;background:#9dc2e2">신용점수</td>
		<td style="text-align:center;background:#9dc2e2">평가회사</td>
		<td style="text-align:center;background:#9dc2e2">차입금 지급계좌</td>
		<td style="text-align:center;background:#9dc2e2">계좌주</td>

		<td style="text-align:center;background:#9dc2e2">상품구분</td>
		<td style="text-align:center;background:#9dc2e2">상품명</td>
		<td style="text-align:center;background:#9dc2e2">대출구분</td>
		<td style="text-align:center;background:#9dc2e2">한도금액</td>
		<td style="text-align:center;background:#9dc2e2">대출금액</td>
		<td style="text-align:center;background:#9dc2e2">이자율</td>
		<td style="text-align:center;background:#9dc2e2">연체이자율</td>
		<td style="text-align:center;background:#9dc2e2">투자자 수</td>
		<td style="text-align:center;background:#9dc2e2">대출실행일</td>
		<td style="text-align:center;background:#9dc2e2">대출종료일</td>

		<td style="text-align:center;background:#9dc2e2">투자자</td>
		<td style="text-align:center;background:#9dc2e2">차입자</td>
	</tr>

<?
if($list_count) {
	$num = $list_count;
	for($i=0; $i<$list_count; $i++) {

		$print_loan_start_date = ($LIST[$i]['loan_start_date']>'0000.00.00') ? preg_replace("/-/", ".", $LIST[$i]['loan_start_date']) : '';
		$print_loan_end_date   = ($LIST[$i]['loan_end_date']>'0000.00.00') ? preg_replace("/-/", ".", $LIST[$i]['loan_end_date']) : '';

		$print_loan_usefee_repay_count = ($LIST[$i]['loan_usefee_repay_count'] > 0) ? $LIST[$i]['loan_usefee_repay_count'] . '회' : '';

		$print_repay_turn = ($LIST[$i]['repay_turn'] > 0) ? $LIST[$i]['repay_turn'] . '회차' : '';

		// 차주실명번호, 차주구분
		$chaju_num  = '';
		$chaju_divi = '';

		if($LIST[$i]['mb_legal_num'] || $LIST[$i]['member_type'] == '2') {
			$chaju_num  = $LIST[$i]['mb_legal_num'];  // 법인등록번호
			$chaju_divi = '법인';  // 차주구분

			//echo $chaju_divi; die();

		} else if($LIST[$i]['psnl_num1'] && $LIST[$i]['psnl_num2']) {
			$chaju_num = $LIST[$i]['psnl_num1'].'-'.$LIST[$i]['psnl_num2'].'******';  // 생년월일, 성별

			if($LIST[$i]['psnl_num2'] == '1' || $LIST[$i]['psnl_num2'] == '3') {
				$chaju_divi = '개인 남';
			} else {
				$chaju_divi = '개인 여';
			}
		}

		// 상품구분
		if($LIST[$i]['category'] == '3') {
			$LIST[$i]['category'] = '매출채권';
		} else if($LIST[$i]['category'] == '2' && $LIST[$i]['mortgage_guarantees'] == '1') {
			$LIST[$i]['category'] = '주택담보';
		} else if($LIST[$i]['category'] == '2' && $LIST[$i]['mortgage_guarantees'] == '') {
			$LIST[$i]['category'] = 'PF';
		} else if($LIST[$i]['category'] == '1') {
			$LIST[$i]['category'] = '동산';
		} else {
			$LIST[$i]['category'] = '';
		}

		// 대출구분
		if($LIST[$i]['limit_amt']) {
			$loan_divi = '한도대출';
		} else {
			$loan_divi = '약정대출';
		}


		echo "	<tr>
		<td style='text-align:center;'>".$num."</td>

		<td style='text-align:center;'>".$LIST[$i]['idx']."</td>

		<td style='text-align:center;' >".$LIST[$i]['mb_title']."</td>
		<td style='text-align:center;' >".$chaju_num."&nbsp;</td>
		<td style='text-align:center;' >".$chaju_divi."</td>
		<td style='text-align:center;' >".$LIST[$i]['credit_score']."</td>
		<td style='text-align:center;' >".$LIST[$i]['rating_cp']."</td>
		<td style='text-align:center;' >".$LIST[$i]['repay_acct_no']."&nbsp;</td>
		<td style='text-align:center;' >".$LIST[$i]['loan_dep_acct_memo1']."</td>

		<td style='text-align:center;' >".$LIST[$i]['category']."</td>
		<td style='text-align:center;' >".$LIST[$i]['title']."</td>
		<td style='text-align:center;' >".$loan_divi."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['limit_amt'])."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['recruit_amount'])."</td>
		<td style='text-align:center;' >".$LIST[$i]['loan_interest_rate']."</td>
		<td style='text-align:center;' >".$LIST[$i]['overdue_rate']."</td>
		<td style='text-align:center;' >".number_format($LIST[$i]['invest_count'])."명</td>
		<td style='text-align:center;' >".$LIST[$i]['loan_start_date']."</td>
		<td style='text-align:center;' >".$LIST[$i]['loan_end_date']."</td>

		<td style='text-align:right;' >".number_format($LIST[$i]['invest_usefee_amount'])."</td>
		<td style='text-align:right;' >".number_format($LIST[$i]['loan_usefee_amount'])."</td>

		<td style='text-align:center;' >".$LIST[$i]['state']."</td>
		<td>"."</td>
		<td>"."</td>

	</tr>\n";

		$num--;
	}
}

echo "</table>\n";



sql_close();
exit;

?>