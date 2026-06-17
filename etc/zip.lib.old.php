<?
function get_loan_amt($ym) {

	$ret_val = array();

	$sql = "
		SELECT
			COUNT(idx) cnt,
			sum(recruit_amount) amt
		FROM
			cf_product
		WHERE 1
			AND state in(1,2,5)
			AND (recruit_amount>=10000)
			AND loan_start_date LIKE '$ym-%'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret_val["loan_amt"] = $row["amt"]/1000;
	$ret_val["loan_cnt"] = $row["cnt"];

	return $ret_val;
}

function get_loan_amt_t($ym, $gb) {

	$ret_val = array();

	if ($gb=="1") {  // 부동산 PF
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='2' AND mortgage_guarantees='')
				AND loan_start_date LIKE '$ym-%'";
	} else if ($gb=="2") {  // 주택담보
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='2' AND mortgage_guarantees='1')
				AND loan_start_date LIKE '$ym-%'";
	} else if ($gb=="3") {  // 매출채권
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='3')
				AND loan_start_date LIKE '$ym-%'";
	} else if ($gb=="4") {  // 동산
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='1')
				AND loan_start_date LIKE '$ym-%'";
	}
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret_val["loan_amt"] = $row["amt"]/1000;
	$ret_val["loan_cnt"] = $row["cnt"];

	return $ret_val;
}

function get_repay_amt($ym) {   // 상환액

	$ret_val = array();

	//$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product WHERE state in(1,2,5) AND loan_end_date LIKE '$ym-%' AND recruit_amount>10000";
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
			FROM cf_product
			WHERE state in(1,2,5)
			AND if(down_date>'0000-00-00',down_date,loan_end_date) LIKE '$ym-%'
			AND recruit_amount>10000";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret_val["repay_amt"] = $row["amt"]/1000;
	$ret_val["repay_cnt"] = $row["cnt"];

	return $ret_val;

}

function get_repay_amt_t($ym, $gb) {   // 상환액

	$ret_val = array();

	//$state = "1,2,5";
	$state = "1,2,4,5,8,9";  // 1 이자상환중 , 2 상환완료 , 4 부실 , 5 중도상환 , 8 연체 , 9 부도
	//$enddate = "loan_end_date";
	$enddate = "if(down_date>'0000-00-00',down_date,loan_end_date)";

	if ($gb=="1") {  // 부동산 PF
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='2' AND mortgage_guarantees='')
				AND $enddate LIKE '$ym-%'
				AND recruit_amount>=10000";

	} else if ($gb=="2") {  // 주택담보
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='2' AND mortgage_guarantees='1')
				AND $enddate LIKE '$ym-%'
				AND recruit_amount>=10000";

	} else if ($gb=="3") {  // 매출채권
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='3')
				AND $enddate LIKE '$ym-%'
				AND recruit_amount>=10000";

	} else if ($gb=="4") {  // 동산
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND (category='1')
				AND $enddate LIKE '$ym-%'
				AND recruit_amount>=10000";
	}
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret_val["repay_amt"] = $row["amt"]/1000;
	$ret_val["repay_cnt"] = $row["cnt"];

	return $ret_val;

}

function get_remain_amt($ym) {   // 대출 잔액

	$ret_val = array();
	/*
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product
				WHERE state in(1,2,5,8)
				AND (recruit_amount>=10000)
				AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND (loan_end_date>'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')";
	*/
	/*
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product
				WHERE state in(1,2,4,5,8,9)
				AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND ( if(down_date>'0000-00-00',down_date,loan_end_date) >'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')
				AND recruit_amount>=10000";
	*/
	$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt FROM cf_product
				WHERE (
							(state in(1,2,4,5,8,9)
		  					AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
		 					AND ( if(down_date>'0000-00-00',down_date,loan_end_date) >'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')
							AND recruit_amount>=10000)
					OR (state IN(1,4,8,9) AND loan_start_date<'$ym-31'))";

	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret_val["remain_amt"] = $row["amt"]/1000;
	$ret_val["remain_cnt"] = $row["cnt"];

	return $ret_val;

}

function get_remain_amt_t($ym, $gb) {   // 대출 잔액

	$ret_val = array();

	//$state = "1,2,5";
	$state = "1,2,4,5,8,9";  // 1 이자상환중 , 2 상환완료 , 4 부실 , 5 중도상환 , 8 연체 , 9 부도
	//$enddate = "loan_end_date";
	$enddate = "if(down_date>'0000-00-00',down_date,loan_end_date)";

	if ($gb=="1") {  // 부동산 PF
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in($state)
				AND (category='2' AND mortgage_guarantees='')
				AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND ($enddate>'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')
				AND recruit_amount>=10000";

	} else if ($gb=="2") {  // 주택담보
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in($state)
				AND (category='2' AND mortgage_guarantees='1')
				AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND ($enddate>'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')
				AND recruit_amount>=10000";

	} else if ($gb=="3") {  // 매출채권
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE (state in($state)
				AND (category='3')
				AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND ($enddate>'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')
				AND recruit_amount>=10000)
				OR (state IN(1,4,8,9) AND category='3' AND loan_start_date<'$ym-31' AND recruit_amount>=10000)";

	} else if ($gb=="4") {  // 동산
		$sql = "SELECT COUNT(idx) cnt , sum(recruit_amount) amt
				FROM cf_product
				WHERE state in($state)
				AND (category='1')
				AND (loan_start_date<='$ym-31' AND loan_start_date>'0000-00-00')
				AND ($enddate>'$ym-31' OR loan_end_date='' OR loan_end_date='0000-00-00')
				AND recruit_amount>=10000";
	} else {
		return;
	}
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret_val["remain_amt"] = $row["amt"]/1000;
	$ret_val["remain_cnt"] = $row["cnt"];

	return $ret_val;

}

function get_late_per($ym) {

	$ret_val = array();

	if ($ym < "2020-08") {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	} else {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	}

	$sql = "SELECT COUNT(*) cnt , SUM(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND loan_start_date<= '$ym-31' AND loan_start_date<>'0000-00-00'
				AND loan_end_date_orig <= '$ym-31'
				AND down_date >= DATE_ADD(loan_end_date_orig , INTERVAL 30 DAY)
			";

	return $ret_val;
}

function get_late_per_t($ym, $gb) {

	$ret_val = array();

	if ($ym < "2020-08") {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	} else {
		$ret_val["cnt"] = 0;
		$ret_val["per"] = "0.00";
	}

	$sql = "SELECT COUNT(*) cnt , SUM(recruit_amount) amt
				FROM cf_product
				WHERE state in(1,2,5)
				AND loan_start_date<= '$ym-31' AND loan_start_date<>'0000-00-00'
				AND loan_end_date_orig <= '$ym-31'
				AND down_date >= DATE_ADD(loan_end_date_orig , INTERVAL 30 DAY)
			";

	return $ret_val;
}

?>