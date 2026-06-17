<?

///////////////////////////////////////
// 월별 투자금액 추출 (전체상품)
///////////////////////////////////////
function get_loan_amt($ym) {

	$ret_val = array();

	$sql = "
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			AND state IN(1,2,4,5,8,9)
			AND isTest = '' AND recruit_amount > 10000
			AND LEFT(loan_start_date,7) = '".$ym."'";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row = sql_fetch($sql);

	$ret_val['loan_amt'] = $row['amt'];
	$ret_val['loan_cnt'] = $row['cnt'];

	return $ret_val;
}


///////////////////////////////////////
// 월별 투자금액 추출 (카테고리별)
///////////////////////////////////////
function get_loan_amt_t($ym, $gb) {

	if(!$gb) return;

	$ret_val = array();

	if($gb=="1")      $add_qry = " AND (category = '2' AND mortgage_guarantees = '') ";			// 부동산 PF
	else if($gb=="2") $add_qry = " AND (category = '2' AND mortgage_guarantees = '1') ";		// 주택담보
	else if($gb=="3") $add_qry = " AND (category = '3') ";																	// 매출채권
	else if($gb=="4") $add_qry = " AND (category = '1') ";																	// 동산

	$sql = "
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			$add_qry
			AND state IN(1,2,4,5,8,9)
			AND isTest = '' AND recruit_amount > 10000
			AND LEFT(loan_start_date,7) = '".$ym."'";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row = sql_fetch($sql);

	$ret_val['loan_cnt'] = $row['cnt'];
	$ret_val['loan_amt'] = $row['amt'];

	return $ret_val;

}


///////////////////////////////////////
// 월별 상환액 (전체상품)
///////////////////////////////////////
function get_repay_amt($ym) {

	$ret_val = array();

	$sql = "
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			AND isTest = '' AND recruit_amount > 10000
			AND state IN(1,2,4,5,8,9)
			AND IF(down_date > '0000-00-00', LEFT(down_date,7), LEFT(loan_end_date,7)) = '".$ym."'";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row = sql_fetch($sql);

	// 부분상환액 추출
	$sql2 = "
		SELECT
			IFNULL(SUM(principal), 0) AS partial_repay_amt
		FROM
			cf_product_give
		WHERE 1
			AND turn_sno > 0
			AND is_overdue = 'N'
			AND LEFT(`date`, 7) = '".$ym."'";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row2 = sql_fetch($sql2);

	$ret_val['repay_cnt'] = $row['cnt'];		// 상환카운트에 부분상환은 포함 하지 않는다.
	$ret_val['repay_amt'] = $row['amt'];		// 정상상환금액
	$ret_val['partial_repay_amt'] = $row2['partial_repay_amt'];
	$ret_val['last_repay_amt'] = $row['amt'] + $row2['partial_repay_amt'];

	return $ret_val;

}


///////////////////////////////////////
// 월별 상환액 (카테고리별)
///////////////////////////////////////
function get_repay_amt_t($ym, $gb) {

	if(!$gb) return;

	$ret_val = array();

	if($gb=="1") {		// 부동산 PF
		$add_qry  = " AND (category = '2' AND mortgage_guarantees = '') ";
		$add_qry2 = " AND (B.category = '2' AND B.mortgage_guarantees = '') ";
	}
	else if($gb=="2") {		// 주택담보
		$add_qry  = " AND (category = '2' AND mortgage_guarantees = '1') ";
		$add_qry2 = " AND (B.category = '2' AND B.mortgage_guarantees = '1') ";
	}
	else if($gb=="3") {		// 매출채권
		$add_qry  = " AND (category = '3') ";
		$add_qry2 = " AND (B.category = '3') ";
	}
	else if($gb=="4") {		// 동산
		$add_qry  = " AND (category = '1') ";
		$add_qry2 = " AND (B.category = '1') ";
	}

	$sql = "
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			AND isTest = '' AND recruit_amount > 10000
			AND state IN(1,2,4,5,8,9)
			AND IF(down_date > '0000-00-00', LEFT(down_date,7), LEFT(loan_end_date,7)) = '".$ym."'
			$add_qry";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row = sql_fetch($sql);

	// 부분상환액 추출
	$sql2 = "
		SELECT
			IFNULL(SUM(principal), 0) AS partial_repay_amt
		FROM
			cf_product_give A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.turn_sno > 0
			AND A.is_overdue = 'N'
			AND LEFT(A.`date`, 7) = '".$ym."'
			$add_qry2";
	//print_rr($sql2, 'font-size:11px;line-height:12px;');
	$row2 = sql_fetch($sql2);

	$ret_val['repay_cnt'] = $row['cnt'];
	$ret_val['repay_amt'] = $row['amt'];
	$ret_val['partial_repay_amt'] = $row2['partial_repay_amt'];
	$ret_val['last_repay_amt'] = $row['amt'] + $row2['partial_repay_amt'];

	return $ret_val;

}


///////////////////////////////////////
// 대출 잔액 (전체상품)
///////////////////////////////////////
function get_remain_amt($ym) {

	$ret_val = array();

	$sql = "
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			AND isTest = '' AND recruit_amount > 10000
			AND state IN(1,2,4,5,8,9)
			AND LEFT(loan_start_date,7) <= '".$ym."'
			AND ( IF(down_date > '0000-00-00', LEFT(down_date,7), LEFT(loan_end_date,7)) > '".$ym."' )";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row = sql_fetch($sql);

	// 부분상환액 추출 (대상월(말)이 대출종료일 이전인 것만)
	$sql2 = "
		SELECT
			IFNULL(SUM(A.principal), 0) AS partial_repay_amt
		FROM
			cf_product_give A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.turn_sno > 0
			AND A.is_overdue = 'N'
			AND LEFT(B.loan_start_date,7) <= '".$ym."'
			AND ( IF(B.down_date > '0000-00-00', LEFT(B.down_date,7), LEFT(B.loan_end_date,7)) > '".$ym."' )
			AND LEFT(A.`date`,7) = '".$ym."'";
	//print_rr($sql2, 'font-size:11px;line-height:12px;');
	$row2 = sql_fetch($sql2);

	$ret_val['remain_cnt'] = $row['cnt'];
	$ret_val['remain_amt'] = $row['amt'];
	$ret_val['last_remain_amt'] = $row['amt'] - $row2['partial_repay_amt'];


	return $ret_val;

}


///////////////////////////////////////
// 대출 잔액 (카테고리별)
///////////////////////////////////////
function get_remain_amt_t($ym, $gb) {

	$ret_val = array();

	if($gb=="1") {		// 부동산 PF
		$add_qry  = " AND (category = '2' AND mortgage_guarantees = '') ";
		$add_qry2 = " AND (B.category = '2' AND B.mortgage_guarantees = '') ";
	}
	else if($gb=="2") {		// 주택담보
		$add_qry  = " AND (category = '2' AND mortgage_guarantees = '1') ";
		$add_qry2 = " AND (B.category = '2' AND B.mortgage_guarantees = '1') ";
	}
	else if($gb=="3") {		// 매출채권
		$add_qry  = " AND (category = '3') ";
		$add_qry2 = " AND (B.category = '3') ";
	}
	else if($gb=="4") {		// 동산
		$add_qry  = " AND (category = '1') ";
		$add_qry2 = " AND (B.category = '1') ";
	}

	$month_last_date = $ym . '-' . date('t', strtotime($ym.'-01'));

	$sql = "
		SELECT
			COUNT(idx) cnt,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			$add_qry
			AND isTest = '' AND recruit_amount > 10000
			AND state IN(1,2,4,5,8,9)
			AND LEFT(loan_start_date,7) <= '".$ym."'
			AND ( IF(down_date > '0000-00-00', LEFT(down_date,7), LEFT(loan_end_date,7)) > '".$ym."' )";
	//print_rr($sql, 'font-size:11px;line-height:12px;');
	$row = sql_fetch($sql);

	// 부분상환액 추출 (대상월(말)이 대출종료일 이전인 것만
	$sql2 = "
		SELECT
			IFNULL(SUM(principal), 0) AS partial_repay_amt
		FROM
			cf_product_give A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			$add_qry2
			AND A.turn_sno > 0
			AND A.is_overdue = 'N'
			AND LEFT(B.loan_start_date,7) <= '".$ym."'
			AND ( IF(B.down_date > '0000-00-00', LEFT(B.down_date,7), LEFT(B.loan_end_date,7)) > '".$ym."' )
			AND LEFT(A.`date`,7) = '".$ym."'";
	//print_rr($sql2, 'font-size:11px;line-height:12px;');
	$row2 = sql_fetch($sql2);

	$ret_val['remain_cnt'] = $row['cnt'];
	$ret_val['remain_amt'] = $row['amt'];
	$ret_val['last_remain_amt'] = $row['amt'] - $row2['partial_repay_amt'];

	return $ret_val;

}


///////////////////////////////////////
// 연체율 (전체)
///////////////////////////////////////
function get_late_per($ym) {

	$ret_val = array();

	$month_last_date = $ym . '-' . date('t', strtotime($ym.'-01'));

	if($ym < "2020-08") {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	}
	else {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	}

	$sql = "
		SELECT
			COUNT(idx) AS cnt ,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			AND state IN(1,2,5,8)
			AND loan_start_date > '0000-00-00' AND loan_start_date <= '".$month_last_date."'
			AND loan_end_date_orig <= '".$month_last_date."'
			AND down_date >= DATE_ADD(loan_end_date_orig , INTERVAL 30 DAY)";

	return $ret_val;

}


///////////////////////////////////////
// 연체율 (카테고리별)
///////////////////////////////////////
function get_late_per_t($ym, $gb) {

	$ret_val = array();

	$month_last_date = $ym . '-' . date('t', strtotime($ym.'-01'));

	if ($ym < "2020-08") {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	}
	else {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	}

	$sql = "
		SELECT
			COUNT(idx) AS cnt ,
			IFNULL(SUM(recruit_amount),0) AS amt
		FROM
			cf_product
		WHERE 1
			AND state IN(1,2,5,8)
			AND loan_start_date > '0000-00-00' AND loan_start_date <= '".$month_last_date."'
			AND loan_end_date_orig <= '".$month_last_date."'
			AND down_date >= DATE_ADD(loan_end_date_orig , INTERVAL 30 DAY)";

	return $ret_val;

}

?>