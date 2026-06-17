<?
include_once("_common.php");


$prd_idx = trim($_POST['idx']);
if(!$prd_idx) {
	echo "상품번호 미전달";
	exit;
}


$PRDT = sql_fetch("
	SELECT
		A.title, A.recruit_amount, category,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_amount,
		isEtcCost
	FROM
		cf_product A
	WHERE
		A.idx='".$prd_idx."'");

if($PRDT['invest_count']) {
	echo "기투자내역이 존재함. 투자내역 재확인 요망";
	exit;
}

// 최종투자자 선정
$invest_finisher = $CONF['INVEST_FINISHER'];
if($PRDT['isEtcCost']=='1') {
	$invest_finisher = $CONF['INVEST_FINISHER2'];
}


$MB = get_member($invest_finisher);
if(!$MB['mb_point']) {
	echo "투자가능한 예치금이 없습니다.";
	exit;
}

if($MB['mb_point'] < $PRDT['recruit_amount']) {
	echo "예치금이 부족합니다. 모집금액을 줄이시던지....";
	exit;
}


$invest_amount = $PRDT['recruit_amount'] - $PRDT['invest_amount'];		// 잔여 모집액 만큼 투자시킴


// 투자 잔여한도 가져오기
$MB_P2PCTR = sql_fetch("SELECT p2pctr_all_limit, p2pctr_imv_limit, p2pctr_mv_limit FROM g5_member WHERE mb_no='".$MB['mb_no']."'");

$my_balance_limit = ($PRDT['category']=='2') ? $MB_P2PCTR['p2pctr_imv_limit'] : $MB_P2PCTR['p2pctr_mv_limit'];

if($my_balance_limit === 0 || $my_balance_limit < $invest_amount) {
	echo "투자한도 부족";
	exit;
}


$invest_try_count = sql_fetch("SELECT COUNT(idx) AS cnt FROM cf_product_invest_detail WHERE member_idx='".$MB['mb_no']."' AND product_idx='".$prd_idx."'")['cnt'];		// 투자시도수

// 투자내역(원장) 등록
$query = "
	INSERT INTO
		cf_product_invest
	SET
		amount       = '".$invest_amount."',
		member_idx   = '".$MB['mb_no']."',
		product_idx  = '".$prd_idx."',
		invest_state = 'Y',
		insert_date  = '".G5_TIME_YMD."',
		insert_time  = '".G5_TIME_HIS."'";
$result = sql_query($query);
$invest_idx = sql_insert_id();

if($invest_idx) {

	// 원리금 수취권 번호 업데이트 ---------------
	$prin_rcv_no = 'I' . $invest_idx;			// 원리금 수취권 번호: I투자번호
	if($invest_try_count > 0) $prin_rcv_no.= '_' . ($invest_try_count+1);

	sql_query("UPDATE cf_product_invest SET prin_rcv_no='".$prin_rcv_no."' WHERE idx='".$invest_idx."'");
	// 원리금 수취권 번호 업데이트 ---------------


	// 상세투자내역 등록
	$query2 = "
		INSERT INTO
			 cf_product_invest_detail
		 SET
			 invest_idx   = '".$invest_idx."',
			 amount       = '".$invest_amount."',
			 member_idx   = '".$MB['mb_no']."',
			 product_idx  = '".$prd_idx."',
			 invest_state = 'Y',
			 insert_date  = '".G5_TIME_YMD."',
			 insert_time  = '".G5_TIME_HIS."'";
	$result2 = sql_query($query2);
	$invest_detail_idx = sql_insert_id();

	//////////////////////////////////////////////////////////////////////
	// 금결원 중앙기록관리 투자신청 기록
	//////////////////////////////////////////////////////////////////////
	$p2pctr_reg_result = p2pctr_invest_register($MB['mb_no'], $prd_idx);

	if($p2pctr_reg_result) {

		////////////////////////////////////
		// 투자한도 업데이트 실행 2
		////////////////////////////////////
		$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/get_p2pctr_limit_amt.exec.php " .  $MB['mb_no'];
		$exec_result = shell_exec($exec_str);

	}
	else {

		///////////////////////////////////////////////////////////////////////
		// 2022-01-22 추가 : 중앙기록관리 결과가 정상이 아닌 경우  투자취소실행
		///////////////////////////////////////////////////////////////////////

		$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/emergency_investment_cancel.proc.php " . $invest_detail_idx . " " . $MB['mb_no'];
		$exec_result = shell_exec($exec_str);

		echo "ERROR-P2PCTR-FAIL-CANCEL";
		@sql_close();
		exit;

	}

	// 모집완료 처리
	$last_update_sql = "
		UPDATE
			cf_product
		SET
			live_invest_amount = live_invest_amount + {$invest_amount},
			invest_end_date = '".G5_TIME_YMD."'
		WHERE
			idx = '".$prd_idx."'";
	sql_query($last_update_sql);

	// 예치금에서 투자금액 차감
	$po_content = $PRDT["title"]. "-투자(상환계좌등록용)";
	insert_point($MB['mb_id'], $invest_amount * (-1), $po_content, '@invest', $MB['mb_id'], $MB['mb_id'].'-'.uniqid(''), 0);

	echo "ok";

}

sql_close();
exit;

?>