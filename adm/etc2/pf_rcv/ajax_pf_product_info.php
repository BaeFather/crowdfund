<?
include_once('./_common.php');

while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }

$idx = $_POST['opt'];

$PRDT = array();

// PF 최초 헬로상품 리스트 정보
$psql = "
	SELECT 
		A.idx, A.gr_idx, A.state, A.title, A.loan_interest_rate, A.overdue_rate, A.recruit_amount, A.loan_mb_no, A.loan_start_date,
		B.mb_no, B.member_type, B.mb_name, B.mb_co_name, B.mb_co_reg_num, B.mb_hp, B.corp_phone
	FROM 
		cf_product A
	LEFT JOIN
		g5_member B ON A.loan_mb_no = B.mb_no
	WHERE
		A.idx='".$idx."'
";
$prow = sql_fetch($psql);


// 상품상태
$state = '';
if($prow['state'] == '1') {
	$state = '이자상환중';
} else if($prow['state'] == '2') {
	$state = '상환완료';
} else if($prow['state'] == '5') {
	$state = '중도상환';
} else if($prow['state'] == '8') {
	$state = '연체중';
}

// 고유번호
$member_unique_num = getJumin($prow['mb_no']);

// 개인, 법인 회원 구분
$loan_name = '';
$unique_num = '';
$phone_num = '';
if($prow['member_type'] == '1') {
	$loan_name = $prow['mb_name'];
	$unique_num = $member_unique_num;
	$phone_num = masterDecrypt($prow['mb_hp']);
} else if($prow['member_type'] == '2') {
	$loan_name = $prow['mb_co_name'];
	$unique_num = $prow['mb_co_reg_num'];
	$phone_num = masterDecrypt($prow['corp_phone']);
}

// 암호화
$blind_unique_num = (strlen($unique_num) > 4) ? substr($unique_num, 0, strlen($unique_num)-4) . "●●●●" : $unique_num;
$blind_phone_num = (strlen($phone_num) > 4) ? substr($phone_num, 0, strlen($phone_num)-4) . "●●●●" : $phone_num;

// 권한 있는 사람만 확인 가능
if($member['mb_id']=='admin_ysm1351' || $member['mb_id']=='admin_youngsin1969' || $member['mb_id']=='admin_com482' || $member['mb_id']=='admin_kor11571' || $member['mb_id']=='admin_sundol4') {
	$uni_num = $unique_num;
	$ph_num = $phone_num;
} else {
	$uni_num = $blind_unique_num;
	$ph_num = $blind_phone_num;
}



// 해당 상품 동일 차주 상품 리스트 출력(여기서 데이터 가져오는 것 아님)
if($idx) {

	$PRDT = array(
		'list' => array(),
	);

	$sql = "
		SELECT 
			idx, state, title, recruit_amount, loan_start_date, loan_end_date, invest_period, loan_interest_rate, overdue_rate, repay_acct_no
		FROM
			cf_product
		WHERE
			category='2' AND mortgage_guarantees='' AND gr_idx='".$idx."' AND recruit_amount>=10000 AND isTest='' AND state IN(1,2,5,8)
		ORDER BY 
			idx desc
	";
	$res = sql_query($sql);

	while($row = sql_fetch_array($res)) {
		
		// 투자자수
		$INVEST_CNT = sql_fetch("
			SELECT COUNT(idx) AS cnt FROM cf_product_invest WHERE product_idx='".$row['idx']."' AND invest_state='Y'
		");

		// 상품상태
		$state = '';
		if($row['state'] == '1') {
			$state = '이자상환중';
		} else if($row['state'] == '2') {
			$state = '상환완료';
		} else if($row['state'] == '5') {
			$state = '중도상환';
		} else if($row['state'] == '8') {
			$state = '연체중';
		}

		// 이자율
		if($row['state']=='8') {
			$interest_rate = $row['overdue_rate']; 
		} else {
			$interest_rate = $row['loan_interest_rate']; 
		}

		// 총 대출금액
		$TOTAL_AMT = sql_fetch("
			SELECT SUM(recruit_amount) AS total_amount FROM cf_product WHERE gr_idx='".$row['idx']."'
		");


		// 출력
		$add_array = array(
			'idx'			  => (string)$row['idx'],
			'state'			  => (string)$state,
			'title'			  => (string)$row['title'],
			'recruit_amount'  => (string)$row['recruit_amount'],
			'loan_start_date' => (string)$row['loan_start_date'],
			'loan_end_date'	  => (string)$row['loan_end_date'],
			'invest_period'	  => (string)$row['invest_period'],
			'interest_rate'   => (string)$interest_rate,
			'invest_cnt'	  => (string)$INVEST_CNT['cnt'],
			'repay_acct_no'   => (string)$row['repay_acct_no'],
		);

		array_push($PRDT['list'], $add_array);

	}

}


// 출력
$PRDT['prdidx']				= (string)$prow['idx'];
$PRDT['state']				= (string)$state;
$PRDT['loan_interest_rate'] = (string)$prow['loan_interest_rate'];
$PRDT['overdue_rate']		= (string)$prow['overdue_rate'];
$PRDT['loan_start_date']	= (string)$prow['loan_start_date'];
$PRDT['loan_name']			= (string)$loan_name;
$PRDT['uni_num']			= (string)$uni_num;
$PRDT['ph_num']				= (string)$ph_num;



echo json_encode($PRDT, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

?>
