<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/repayHistory.do
## 13. 상환내역
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci']        = 'INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==';		//*** 복호화 필요
$REQUEST['prod_cd']   = '232';				// 상품코드
$REQUEST['start_num'] = '1';					// 시작번호
$REQUEST['end_num']   = '10';					// 종료번호
*/

$REQUEST['ci'] = urldecode($REQUEST['ci']);

$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }

$MB = get_member($mb_id);

$sql = "
	SELECT
		A.idx, A.product_idx, A.amount, A.prin_rcv_no,
		B.title, B.category, B.mortgage_guarantees, B.state, B.recruit_amount, B.invest_return, B.loan_start_date, B.loan_end_date,
		B.invest_period, B.invest_days,
		(SELECT COUNT(idx) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_count,
		(SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_principal,
		(SELECT IFNULL(SUM(interest),0) + IFNULL(SUM(interest_tax),0) + IFNULL(SUM(local_tax),0) + IFNULL(SUM(fee),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') AS give_interest,
		( (SELECT IFNULL(SUM(interest),0) + IFNULL(SUM(interest_tax),0) + IFNULL(SUM(local_tax),0) + IFNULL(SUM(fee),0) FROM cf_product_give WHERE invest_idx=A.idx AND is_overdue='N') / A.amount * 100 ) AS rate
	FROM
		cf_product_invest A
	INNER JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.product_idx='".$REQUEST['prod_cd']."'
		AND A.member_idx='".$MB['mb_no']."'
		AND A.invest_state='Y'
		AND A.syndi_id='".$_CONF['SYNDI_ID']."'
	ORDER BY
		A.idx DESC";
$INVEST = sql_fetch($sql);
//print_r($INVEST);


if( in_array($INVEST['state'], array('2','5')) ) {
	$tot_repay_cnt = $INVEST['give_count'];
}
else {
	if($INVEST['invest_days'] > 0) {
		$tot_repay_cnt = 1;
	}
	else {
		$tot_repay_cnt = $INVEST['invest_period'] + 1;
	}
}


$tot_cnt = $tot_repay_cnt;  //상환건수(조회된 상환 총 건수) 이게 뭘까요??

//원리금수취증서링크URL (?idx=투자번호&strtoupper(MD5('member_idx'))=회원번호)
//$doc_url = G5_URL."/deposit/principal_interest_certificate.php?idx=".$INVEST['idx']."&".strtoupper(MD5('member_idx'))."=".$MB['mb_no'];
$doc_url = G5_URL."/deposit/principal_interest_certificate_origin.php?idx=".$INVEST['idx']."&".strtoupper(MD5('member_idx'))."=".$MB['mb_no'];

$sql = "
	SELECT
		idx, `date`, principal, interest, interest_tax, local_tax, fee, turn, banking_date
	FROM
		cf_product_give
	WHERE
		invest_idx='".$INVEST['idx']."'
	ORDER BY
		idx DESC";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();
for($i=0; $i<$rows; $i++) {
	$row = sql_fetch_array($res);

	$LIST[$i]['repay_num'] = $row['turn'];
	$LIST[$i]['repay_dt']  = preg_replace("/-/", "", substr($row['banking_date'], 0, 10));

	$LIST[$i]['before_profit'] = $row['interest'] + $row['interest_tax'] + $row['local_tax'] + $row['fee'];
	$LIST[$i]['income_tax']    = $row['interest_tax'];
	$LIST[$i]['residence_tax'] = $row['local_tax'];
	$LIST[$i]['platform_fee']  = $row['fee'];
	$LIST[$i]['income_amt']    = $row['interest'];

}

$list_count = count($LIST);


$ARR['code'] = '0000';									// 결과코드
$ARR['msg']  = '정상처리되었습니다.';		// 메세지

//$ARR['data']
$ARR['data']['comp_cd']       = $_CONF['comp_cd'];					// 제휴코드
$ARR['data']['prod_cd']       = $REQUEST['prod_cd'];				// 상품코드
$ARR['data']['prod_nm']       = $INVEST['title'];						// 상품명
$ARR['data']['tot_repay_cnt'] = (string)$tot_repay_cnt;						// 총상환횟수
$ARR['data']['sum_repay_cnt'] = (string)$INVEST['give_count'];		// 누적상환횟수
$ARR['data']['repay_amt']     = (string)$INVEST['give_principal'];	// 상환액
$ARR['data']['rate']          = (string)@sprintf("%.2f", $INVEST['rate']);			// 수익률
$ARR['data']['invest_amt']    = (string)$INVEST['amount'];					// 투자금액
$ARR['data']['doc_url']       = $doc_url;										// 원리금수취증서URL 정보
$ARR['data']['tot_cnt']       = $tot_cnt;										// 상환건수

$ARR['data']['repay_list'] = array();								// [회차별상환정보 배열 시작]
for($i=0; $i<$list_count; $i++) {
	$ARR['data']['repay_list'][$i]['repay_num']     = (string)$LIST[$i]['repay_num'];				// 회차
	$ARR['data']['repay_list'][$i]['repay_dt']      = (string)$LIST[$i]['repay_dt'];				// 상환일(YYYYMMDD)
	$ARR['data']['repay_list'][$i]['before_profit'] = (string)$LIST[$i]['before_profit'];		// 세전이자
	$ARR['data']['repay_list'][$i]['income_tax']    = (string)$LIST[$i]['income_tax'];			// 소득세
	$ARR['data']['repay_list'][$i]['residence_tax'] = (string)$LIST[$i]['residence_tax'];		// 주민세
	$ARR['data']['repay_list'][$i]['platform_fee']  = (string)$LIST[$i]['platform_fee'];		// 플랫폼이용료
	$ARR['data']['repay_list'][$i]['income_amt']    = (string)$LIST[$i]['income_amt'];			// 실수입금액
}


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>