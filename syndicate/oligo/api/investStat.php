<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/investStat.do
## 10. 투자현황
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

//$REQUEST['ci']        = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
//$REQUEST['start_num'] = '5';		// 시작번호
//$REQUEST['end_num']   = '15';		// 종료번호


$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

$MB = get_member($mb_id);


$sql = "
	SELECT
		A.idx, A.product_idx, A.amount, A.prin_rcv_no,
		B.title, B.category, B.mortgage_guarantees, B.state, B.recruit_amount, B.invest_return, B.loan_start_date, B.loan_end_date,
		(SELECT IFNULL(MAX(turn),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS last_turn,
		(SELECT COUNT(idx) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_count,
		(SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_principal,
		(SELECT IFNULL(SUM(interest),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_last_interest,
		(SELECT IFNULL(SUM(interest),0) + IFNULL(SUM(interest_tax),0) + IFNULL(SUM(local_tax),0) + IFNULL(SUM(fee),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_interest,
		( (SELECT IFNULL(SUM(interest),0) + IFNULL(SUM(interest_tax),0) + IFNULL(SUM(local_tax),0) + IFNULL(SUM(fee),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') / A.amount * 100 ) AS rate
	FROM
		cf_product_invest A
	INNER JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.member_idx='".$MB['mb_no']."'
		AND A.invest_state='Y'
		AND A.syndi_id='".$_CONF['SYNDI_ID']."'
	ORDER BY
		A.idx DESC";
if($REQUEST['start_num'] && $REQUEST['end_num']) {
	$start_num = $REQUEST['start_num'] - 1;
	$get_row = $REQUEST['end_num'] - $REQUEST['start_num'];
	$sql.= " LIMIT ".$start_num.", " . $get_row;
}
//echo $sql."\n\n";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$tot_cnt     = $rows;
$tot_amt     = 0;				// 총투자금
$sum_rtn_amt = 0;				// 누적상환수익
$sum_rtn_pri = 0;				// 누적상환원금

$LIST = array();
for($i=0,$j=1; $i<$rows; $i++,$j++) {

	$row = sql_fetch_array($res);

	$tot_amt     += $row['amount'];
	$sum_rtn_amt += $row['give_last_interest'];	// 지급이자(세후) 합계(적용일시:2020-03-17 17:19, 올리고 황지성 과장 요청)
//$sum_rtn_amt += $row['give_interest'];			// 지급이자(세전) 합계
	$sum_rtn_pri += $row['give_principal'];			// 상환원금 합계


	$PSTATE = investStatement($row['product_idx'], $row['amount'], '', '', $row['idx']);

	$total_turn = count($PSTATE['REPAY']);

	$LIST[$i]['prod_cd']   = $row['product_idx'];
	$LIST[$i]['prod_nm']   = $row['title'];
	$LIST[$i]['status']    = getProductStatOligo($row['product_idx']);
	$LIST[$i]['prod_cate'] = ($row['category']=='2') ? 'real' : 'etc';

	$LIST[$i]['tot_repay_cnt']  = $total_turn;
	$LIST[$i]['sum_repay_cnt']  = $row['give_count'];
	$LIST[$i]['repay_amt']      = $row['give_interest'];
	$LIST[$i]['rate']           = @floatRtrim(sprintf("%.2f", $row['invest_return']));
//$LIST[$i]['rate']           = @floatRtrim(sprintf("%.2f", $row['rate']));					// 지급이자기준 수익율
	$LIST[$i]['invest_amt']     = $row['amount'];
	$LIST[$i]['next_repay_amt'] = ($PSTATE['REPAY'][$row['last_turn']]['send_price']) ? $PSTATE['REPAY'][$row['last_turn']]['send_price'] : '0';	// 총지급예정액 맞나요 다음 예정액이 아니고?

	$LIST[$i]['repay_st_dt']    = preg_replace("/-/", "", $PSTATE['INI']['loan_start_date']);
	$LIST[$i]['repay_ed_dt']    = preg_replace("/-/", "", $PSTATE['INI']['loan_end_date']);

	if($row['last_turn'] < $total_turn) {
		$LIST[$i]['repay_next_dt']  = @preg_replace("/-/", "", $PSTATE['REPAY'][$row['last_turn']]['repay_day']);
	}
	else {
		$LIST[$i]['repay_next_dt']  = @preg_replace("/-/", "", $PSTATE['REPAY'][$total_turn-1]['repay_day']);
	}

	//print_r($PSTATE);
	unset($PSTATE);

}

$list_count = count($LIST);


$ARR['code'] = '0000';									// 결과코드
$ARR['msg']  = '정상처리되었습니다.';		// 메세지

//$ARR['data']
$ARR['data']['comp_cd']     = $_CONF['comp_cd'];																	// 제휴코드
$ARR['data']['tot_cnt']     = (string)$tot_cnt;																		// 투자건수	-> 노출안함.
$ARR['data']['tot_amt']     = (string)$tot_amt;																		// 총 투자금
$ARR['data']['tot_rate']    = (string)@floatRtrim(sprintf("%.2f", ($sum_rtn_amt / $tot_amt) * 100));	// 총 수익률 (계산법??) -> 노출안함.
$ARR['data']['sum_rtn_amt'] = (string)$sum_rtn_amt;																// 누적상환수익
$ARR['data']['sum_rtn_pri'] = (string)$sum_rtn_pri;																// 누적상환원금
$ARR['data']['current_amt'] = (string)$MB['mb_point'];														// 출금가능금액
//$ARR['data']['current_amt'] = (string)$MB['withdrawal_posible_amount'];				// 출금가능금액 (협의 필요)


$ARR['data']['prod_incom_list'] = array();										// [수익정보 배열시작]
for($i=0; $i<$list_count; $i++) {
	$ARR['data']['prod_incom_list'][$i]['prod_cd']       = $LIST[$i]['prod_cd'];									// 상품코드
	$ARR['data']['prod_incom_list'][$i]['prod_nm']       = $LIST[$i]['prod_nm'];									// 상품명
	$ARR['data']['prod_incom_list'][$i]['status']        = (string)$LIST[$i]['status'];						// 상태값
	$ARR['data']['prod_incom_list'][$i]['prod_cate']     = $LIST[$i]['prod_cate'];								// 상품카테고리(cred:신용/real:부동산/bond:어음/etc:기타)
	$ARR['data']['prod_incom_list'][$i]['tot_repay_cnt'] = (string)$LIST[$i]['tot_repay_cnt'];		// 총상환횟수
	$ARR['data']['prod_incom_list'][$i]['sum_repay_cnt'] = (string)$LIST[$i]['sum_repay_cnt'];		// 누적상환횟수 (연체지급회차 불포함)
	$ARR['data']['prod_incom_list'][$i]['repay_amt']     = (string)$LIST[$i]['repay_amt'];				// 회차수익금
	$ARR['data']['prod_incom_list'][$i]['rate']          = (string)sprintf("%.2f", $LIST[$i]['rate']);						// 수익률
	$ARR['data']['prod_incom_list'][$i]['invest_amt']    = (string)$LIST[$i]['invest_amt'];				// 투자금액
	$ARR['data']['prod_incom_list'][$i]['next_repay_amt']= (string)$LIST[$i]['next_repay_amt'];		// 총지급예정액 -> 다음 지급예정액 아닌가?
	$ARR['data']['prod_incom_list'][$i]['repay_st_dt']   = (string)$LIST[$i]['repay_st_dt'];			// 상환시작일(YYYYMMDD)
	$ARR['data']['prod_incom_list'][$i]['repay_ed_dt']   = (string)$LIST[$i]['repay_ed_dt'];			// 상환종료일(YYYYMMDD)
	$ARR['data']['prod_incom_list'][$i]['repay_next_dt'] = (string)$LIST[$i]['repay_next_dt'];		// 다음상환일(YYYYMMDD)
}



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>