<?
###############################################################################
## 투자집계조회
## - 핀크앱을 통한 투자내역만 추출하여야 함 (2018-07-25 이승린 매니저 확인)
###############################################################################

include_once("../../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['data']['aggregateDate']		//집계일(YYYYmmdd)
$REQUEST['data']['pageNumber']			//페이지번호 (최소:1)
$REQUEST['data']['pageSize']				//페이지번호 (최대:1000)
*/

$aggregateDate = $REQUEST['data']['aggregateDate'];
$_aggregateDate = substr($aggregateDate, 0, 4).'-'.substr($aggregateDate, 4, 2).'-'.substr($aggregateDate, 6);


$where = " AND A.syndi_id='".$_CONF['SYNDI_ID']."' ";
$where.= " AND ( A.insert_date='".$_aggregateDate."' OR LEFT(A.cancel_date, 10)='".$_aggregateDate."' )";

$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		cf_product_invest A
	WHERE 1
		$where";
$total_data_rows = sql_fetch_array($sql);

$get_rows = ($REQUEST['data']['pageSize']) ? $REQUEST['data']['pageSize'] : 100;
$total_page = ceil($total_data_rows / $get_rows);

$page = $REQUEST['data']['pageNumber'];
if($page < 1) $page = 1;
$from_record = ($page - 1) * $get_rows; // 시작 열


$sql = "
	SELECT
		A.idx, A.amount, A.member_idx, A.product_idx, A.invest_state, A.insert_date, A.insert_time, A.cancel_date, A.cancel_by,
		(SELECT state FROM cf_product WHERE idx=A.product_idx) AS state,
		(SELECT finnq_userid FROM g5_member WHERE mb_no=A.member_idx) AS finnq_userid,
		(SELECT deposit FROM finnq_deposit_check WHERE invest_idx=A.idx) AS deposit,
		(SELECT amount FROM finnq_deposit_check WHERE invest_idx=A.idx) AS deposit_amount,
		(SELECT check_date FROM finnq_deposit_check WHERE invest_idx=A.idx) AS deposit_check_date
	FROM
		cf_product_invest A
	WHERE 1
		$where
	ORDER BY
		A.invest_state ASC,
		A.cancel_date ASC,
		A.idx DESC
	LIMIT
		$from_record, $get_rows";

if($REQUEST['data']['isTest']) { debug_flush($sql."\n"); }

$res = sql_query($sql);
$totalCount = sql_num_rows($res);

$ARR['data']['totalCount']               = $totalCount;		// 전체 건수 (총투자건수 + 총철회건수 + 총상품취소건수)
$ARR['data']['totalAmount']              = 0;							// 전체 금액 (총투자금액 + 총철회금액 + 총상품취소금액)
$ARR['data']['totalCancelCount']         = 0;							// 총철회건수(투자가가 요청한 철회 건수)
$ARR['data']['totalCancelAmount']        = 0;							// 총철회금액
$ARR['data']['totalTransferCount']       = 0;							// 총이체건수
$ARR['data']['totalTransferAmount']      = 0;							// 총이체금액
$ARR['data']['totalProductCancelCount']  = 0;							// 총상품취소건수(업체에서 상품취소/삭제로 인한 투자철회 건수)
$ARR['data']['totalProductCancelAmount'] = 0;							// 총상품취소금액
$ARR['data']['investList']               = array();

for($i=0,$j=1; $i<$totalCount; $i++,$j++) {

	$ROW = sql_fetch_array($res);

	$cancel_date = substr($ROW['cancel_date'], 0, 10);

	$ARR['data']['totalAmount'] += $ROW['amount'];

	$ARR['data']['investList'][$i]['memberNumber']            = $ROW['finnq_userid'];
	$ARR['data']['investList'][$i]['institutionInvestNumber'] = $ROW['idx'];
	$ARR['data']['investList'][$i]['productNumber']           = $ROW['product_idx'];


	//////////////////////////////////////////////////////
	//	$cancelYn => 투자철회여부 :
	//		투자가가 철회요청한 경우:Y
	//		투자가가 철회요청한 이후 취소/삭제한 경우:Y
	//		철회하지 않은 경우:N
	//		업체에서 취소/삭제한 경우 : N
	//////////////////////////////////////////////////////
	$cancelYn = "N";
	if($ROW['invest_state']=='R') {
		$cancelYn = 'N';
	}
	else if($ROW['invest_state']=='N') {
		if($_aggregateDate == $cancel_date) {
			$cancelYn = ($ROW['cancel_by']=='user') ? 'Y' : 'N';
		}
		else {
			$cancelYn = 'N';
		}
	}


	if($cancelYn=='Y') {
		$ARR['data']['totalCancelCount']  += 1;
		$ARR['data']['totalCancelAmount'] += $ROW['amount'];
	}
	$ARR['data']['investList'][$i]['cancelYn'] = $cancelYn;


	//////////////////////////////////////////////////////
	//	$productCancelYn => 상품취소여부 :
	//		회원이 투자한 상품을 업체에서 취소/삭제한 경우:Y
	//////////////////////////////////////////////////////
	$productCancelYn = ( in_array($ROW['state'], array('3','6','7')) ) ? 'Y' : 'N';
	if($productCancelYn=='Y') {
		$ARR['data']['totalProductCancelCount']  += 1;
		$ARR['data']['totalProductCancelAmount'] += $ROW['amount'];
	}
	$ARR['data']['investList'][$i]['productCancelYn'] = $productCancelYn;

	$ARR['data']['investList'][$i]['investAmount']    = $ROW['amount'];

	$investDateTime = $ROW['insert_date'] . " " . $ROW['insert_time'];
	$investDateTime = preg_replace("/(-| |:)/", "", $investDateTime);
	$ARR['data']['investList'][$i]['investDateTime']  = $investDateTime;
	unset($investDateTime);


	$ARR['data']['investList'][$i]['transferSuccessYn'] = "N";
	if($ROW['deposit']=='Y') {
		$ARR['data']['investList'][$i]['transferSuccessYn'] = 'Y';				// 해당투자에 대한 이체성공여부
		$ARR['data']['totalTransferCount'] += 1;													// 총이체건수
		$ARR['data']['totalTransferAmount'] += $ROW['deposit_amount'];		// 총이체금액
	}

}
sql_free_result($res);


// 신디사로부터 이체된 이체건수 및 금액
$sql2 = "
	SELECT
		COUNT(idx) AS cnt,
		IFNULL(SUM(amount),0) AS sum_amount
	FROM
		finnq_deposit_check
	WHERE 1
		AND deposit='Y'
		AND LEFT(rdate,10)='".$_aggregateDate."'";
if($REQUEST['data']['isTest']) { debug_flush($sql2."\n"); }
$SYNDI_DEPOSIT = sql_fetch($sql2);

$ARR['data']['totalTransferCount']   = $SYNDI_DEPOSIT['cnt'];							// 총이체건수
$ARR['data']['totalTransferAmount']  = $SYNDI_DEPOSIT['sum_amount'];			// 총이체금액


##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");


@sql_close();
exit;

?>