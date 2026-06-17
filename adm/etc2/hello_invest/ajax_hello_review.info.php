<?

include_once('./_common.php');

$PRDT = array();

$product_idx = $_POST['opt'];

$hello_mb_no = '48343';


//echo json_encode($result);

// 상품구분, 총 모집금액, LTV, 차입자, 검토일 기준 모집된 금액 및 비율
$sql = "
	SELECT
		A.idx, A.title, A.category, A.mortgage_guarantees, A.recruit_amount, A.loan_mb_no, A.ltv, A.live_invest_amount,
		B.mb_f_no, B.member_type, B.mb_name, B.mb_co_name
	FROM
		cf_product A
	LEFT JOIN
		g5_member B  ON (A.loan_mb_no=B.mb_no)
	WHERE
		A.idx='".$product_idx."'";

$row = sql_fetch($sql);

$mb_name = ($row['member_type']=='2') ? $row['mb_co_name'] : $row['mb_name'];

if($row['category'] == '2' && $row['mortgage_guarantees'] == '') {
	$category = "PF";
}
else if($row['category'] == '2' && $row['mortgage_guarantees'] == '1') {
	$category = "주택담보대출";
}
else if($row['category'] == '3') {
	$category = "매출채권";
}
else if($row['category'] == '1') {
	$category = "동산";
}

$live_invest_perc = ($row['live_invest_amount'] / $row['recruit_amount']) * 100;
$live_invest_perc = floatRtrim(floatCutting($live_invest_perc, 2));


// 해당 차주의 회원번호 리스트 추출
$sql = "SELECT mb_no FROM g5_member WHERE mb_f_no='".$row['mb_f_no']."' AND mb_level='1' AND member_group='L' ORDER BY mb_no";
$res = sql_query($sql);
$rows = $res->num_rows;
$mb_no_set = "";
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$R = sql_fetch_array($res);
	$mb_no_set.= $R['mb_no'];
	$mb_no_set.= ($j < $rows) ? ',' : '';
}
//echo $mb_no_set ."\n";

if(!$mb_no_set) {
	$ARR = array('result' => false, 'message' => '차입자 정보가 없습니다.');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
	sql_close();
	exit;
}

// 해당 차주의 상품번호 리스트 추출
$sql = "SELECT idx FROM cf_product WHERE loan_mb_no IN(".$mb_no_set.") AND state NOT IN('3','4','6','7','9') ORDER BY start_num ASC";	// 진행현황(3:투자금모집실패,4:매각,6:대출취소(기표전),7:대출취소(기표후),9:부도(상환불가))
$res = sql_query($sql);
$rows = $res->num_rows;
$product_idx_set = "";
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$R = sql_fetch_array($res);
	$product_idx_set.= $R['idx'];
	$product_idx_set.= ($j < $rows) ? ',' : '';
}
//echo $product_idx_set ."\n";

$ARR = array(
	'result'             => true,
	'category'           => '',
	'recruit_amount'     => '0',
	'live_invest_amount' => '0',
	'live_invest_perc'   => '0',
	'ltv'                => '0',
	'mb_name'            => '',
	'remain_principal'	 => '0',
	'remain_perc'	       => '0'
);

// 당 차주에 대한 헬로핀테크 투자금액 (자기계산 투자금액)		*** 주의: 상품기준이 아닌 동일차주 기준임
$sql = "
	SELECT
		(
			SELECT IFNULL(SUM(amount),0)
			FROM cf_product_invest
			WHERE 1
				AND product_idx IN('".$product_idx_set."')
				AND member_idx='".$hello_mb_no."'
				AND invest_state='Y'
		) AS invest_amount,
		(
			SELECT IFNULL(SUM(principal),0)
			FROM cf_product_give
			WHERE 1
				AND product_idx IN('".$product_idx_set."')
				AND member_idx='".$hello_mb_no."'
				AND (banking_date IS NOT NULL OR banking_date > '0000-00-00 00:00:00')
		) AS paid_amount";

$HELLO = sql_fetch($sql);


$hello_remain_principal = $HELLO['invest_amount'] - $HELLO['paid_amount'];

// 자기자본 가져오기
$hello_capital = sql_fetch("SELECT price FROM hello_self_invest WHERE 1 ORDER BY idx DESC LIMIT 1")['price'];

$hello_remain_perc = floatRtrim(floatCutting($hello_remain_principal / $hello_capital *100, 2));



$ARR['category']           = (string)$category;								// 상품구분
$ARR['recruit_amount']     = (string)$row['recruit_amount'];				// 총 모집금액
$ARR['live_invest_amount'] = (string)$row['live_invest_amount'];			// 검토일 기준 모집된 금액
$ARR['live_invest_perc']   = (string)$live_invest_perc;						// 검토일 기준 모집된 비율
$ARR['ltv']                = (string)$row['ltv'];							// LTV
$ARR['mb_name']            = (string)$mb_name;								// 차주자명
$ARR['remain_principal']   = (string)$hello_remain_principal;				// 차주상품에 대한 헬로핀테크계정 투자 잔액
$ARR['remain_perc']	       = (string)$hello_remain_perc;					// 자기자본대비 자기계산투자 잔액비


echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

sql_close();
exit;
?>