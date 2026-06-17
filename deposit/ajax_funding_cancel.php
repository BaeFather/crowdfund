<?
include_once('./_common.php');
include_once('../lib/insidebank.lib.php');

if($_SERVER["REQUEST_METHOD"]!="POST") { echo "ERROR-DATA"; exit; }
if(!$member["mb_id"]) { echo "ERROR-LOGIN"; exit; }

// 금결원 점검시간 진입금지 --------------------------------------------------------------
if( date('H:i') >= $CONF['P2PCTR_PAUSE']['STIME'] || date('H:i') <= $CONF['P2PCTR_PAUSE']['ETIME'] ) { echo "ERROR-P2PCTR_PAUSE"; exit; }


$invest_idx = $_POST['invest_idx'];

$sql = "
	SELECT
		A.amount, A.product_idx, A.prin_rcv_no, A.syndi_id,
		B.*
	FROM
		cf_product_invest A
	INNER JOIN
		cf_product B  ON A.product_idx = B.idx
	WHERE 1
		AND A.idx='".$invest_idx."'
		AND A.invest_state='Y'
		AND A.member_idx='".$member['mb_no']."'";

$INVEST = sql_fetch($sql);
if(!$INVEST) { echo "ERROR-DATA"; exit; }

$TMP = sql_fetch("SELECT IFNULL(SUM(amount),0) AS total_invest_amount FROM cf_product_invest WHERE product_idx = '".$INVEST['product_idx']."' AND invest_state = 'Y'");
$INVEST['total_invest_amount'] = $TMP['total_invest_amount'];


$product_open_date    = preg_replace("/(-|:| )/", "", $INVEST['open_datetime']);			// 상품공개일시
$product_invest_sdate = preg_replace("/(-|:| )/", "", $INVEST['start_datetime']);			// 투자마감일시
$product_invest_edate = preg_replace("/(-|:| )/", "", $INVEST['end_datetime']);				// 투자종료일시


//if($product_invest_sdate<=date("YmdHis") && $product_invest_edate>=date("YmdHis")){
if($product_open_date<=date("YmdHis") && $product_invest_edate>=date("YmdHis")) {
	if($INVEST['recruit_amount'] <= $INVEST['total_invest_amount']) { echo "ERROR-END"; exit; }
}
else { echo "ERROR-DATE"; exit; }


$mb_no         = $member['mb_no'];
$prd_idx       = $INVEST['product_idx'];
$invest_amount = $INVEST['amount'];
$prin_rcv_no   = $INVEST['prin_rcv_no'];

$update_sql = "
	UPDATE
		cf_product_invest
	SET
		invest_state = 'N',
		cancel_date = NOW(),
		cancel_by = 'user'
	WHERE 1
		AND member_idx = '".$mb_no."'
		AND invest_state = 'Y'
		AND idx = '".$invest_idx."'";

if( sql_query($update_sql) ) {

	//////////////////////////////
	// 투자내역상세정보 변경
	//////////////////////////////
	$update_sql2 = "
		UPDATE
			cf_product_invest_detail
		SET
			invest_state = 'N',
			cancel_date = NOW()
		WHERE 1
			AND member_idx = '".$mb_no."'
			AND invest_state = 'Y'
			AND invest_idx = '".$invest_idx."'";

	$result2 = sql_query($update_sql2);

	$po_content = $INVEST["title"]. "-투자 취소";
	insert_point($member["mb_id"], $invest_amount , $po_content, '@cancel', $member['mb_id'], $member['mb_id'].'-'.uniqid(''), 0);

	//////////////////////////////////////////////////////////////////////////
	// (!중요)상품관리테이블에 실시간 모집금액 반영하기 :: 2021-02-15 추가
	//////////////////////////////////////////////////////////////////////////
	sql_query("UPDATE cf_product SET live_invest_amount = live_invest_amount - {$invest_amount} WHERE idx = '".$prd_idx."'");
	//////////////////////////////////////////////////////////////////////////


	// 올리고에서 투자한 건을 헬로펀딩에서 취소한 경우 올리고측으로 자료 전송.
	//if($INVEST['syndi_id']=='oligo') {
	//	@shell_exec("/usr/local/php/bin/php -q " . G5_PATH . "/syndicate/oligo/report/investCancelReport.php " . $invest_idx);
	//	@shell_exec("/usr/local/php/bin/php -q " . G5_PATH . "/syndicate/oligo/report/productStateReport.php " . $prd_idx);
	//}


	//////////////////////////////////////////////////////////////////////
	// 금결원 중앙기록관리 투자신청취소 전송
	//////////////////////////////////////////////////////////////////////
	$p2pctr_canc_result = p2pctr_invest_register_canc($mb_no, $prd_idx, $invest_idx);
	if($p2pctr_canc_result) {

		////////////////////////////////////
		// 투자한도 업데이트 실행
		////////////////////////////////////
		$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/get_p2pctr_limit_amt.exec.php " .  $member['mb_no'];
		$exec_result = shell_exec($exec_str);

	}


	echo "SUCCESS";

}
else {

	echo "ERROR-DATA";

}

sql_close();
exit;

?>