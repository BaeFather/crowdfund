<?
###############################################################################
## 상품목록조회 (투자 실패내역은 출력하지 말것)
###############################################################################

include_once("../../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

$ARR['data']['productList'] = array();

$end_datetime_limit = date("Y-m-d H:i:s", strtotime("+1 day"));
$end_date_limit     = substr($end_datetime_limit, 0, 10);

$where = " 1=1 ";
$where.= " AND A.state IN('','1','2','5','8','9') ";															// 투자실패건을 제외한
$where.= " AND A.display='Y' AND A.isTest='' AND A.only_vip='' ";									// 기본출력이 허용된, 테스트상품이 아닌, 특별투자상품이 아닌
$where.= " AND A.scrap_out='' AND A.platform LIKE '%".$_CONF['SYNDI_ID']."%' ";		// 외부스크래핑이 허용된 상품중 당 신디케이션사에게 노출이 허락된
$where.= " AND A.open_datetime <= NOW() ";																				// 노출시작시간이 이후 부터
$where.= " AND A.end_datetime >= '".$end_datetime_limit."' ";											// 모집종료시간 +1일이 경과되지 않은 상품 리스트
//$where.= " AND A.invest_end_date=''";																							// 투자 완료가 되지 않은 상품 리스트


$row = sql_fetch("SELECT COUNT(idx) AS cnt FROM cf_product A WHERE $where");
$total_count   = $row['cnt'];
/*
$rows_per_page = 1000;
$total_page    = ceil($total_count / $rows_per_page);
$page          = ($page < 1) ? 1 : $page;
$from_record   = ($page - 1) * $rows_per_page;
*/

$sort = "A.cancel_date ASC";
$sort.= ", invest_percent ASC";
$sort.= ", A.open_datetime DESC";
$sort.= ", A.idx DESC";

$sql = "
	SELECT
		A.idx, A.gr_idx, A.ai_grp_idx, A.state, A.category, A.mortgage_guarantees, A.title, A.main_image
		, A.invest_return, A.invest_period, A.invest_days, A.recruit_period_start, A.recruit_period_end, A.recruit_amount, A.repay_type
		, A.open_datetime, A.start_datetime, A.end_datetime, A.invest_end_date, A.loan_start_date, A.loan_end_date, A.cancel_date
		, A.advance_invest, A.advance_invest_ratio
		, (SELECT IFNULL(SUM(amount), 0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS total_invest_amount
		, ((((SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y'))/A.recruit_amount)*100) AS invest_percent
	FROM
		cf_product A
	WHERE
		$where
	ORDER BY
		$sort";
//$sql.= " LIMIT $from_record, $rows_per_page";
//if($REQUEST['data']['isTest']=='1' || $_REQUEST['isTest']=='1') { echo $sql."\n"; }

$res  = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$PLIST[$i] = sql_fetch_array($res);

	if($PLIST[$i]['main_image']) {
		$main_image_path = G5_DATA_PATH."/product/".$PLIST[$i]['main_image'];
		if($main_image_path) {
			$target_str     = preg_replace("/\//", "\/", G5_DATA_PATH);
			$main_image_url = preg_replace('/'.$target_str.'/', G5_DATA_URL, $main_image_path);
			$PLIST[$i]['main_image'] = $main_image_url;
		}
		unset($main_image_path);
	}
}
sql_free_result($res);

if($rows) {

	for($i=0,$j=1; $i<$rows; $i++,$j++) {

		$nowDateTime  = preg_replace('/(-| |:)/', '', G5_TIME_YMDHIS);

		$investStartDateTime = ($PLIST[$i]['advance_invest']=='Y') ? preg_replace('/(-| |:)/', '', $PLIST[$i]['open_datetime']) : preg_replace('/(-| |:)/', '', $PLIST[$i]['start_datetime']);		// 사전투자일 경우 상품 노출시간 기준으로 투자 가능
		$investEndDateTime = preg_replace('/(-| |:)/', '', $PLIST[$i]['end_datetime']);

		$productTypeCode = '';
		switch($PLIST[$i]['category']) {
			case '1' : $productTypeCode = 'MOVABLE_PROPERTY';		break;		// 포트폴리오(동산)
		//case '2' : $productTypeCode = 'REAL_ESTATE';				break;		// 부동산(주담대)  : 부동산PF
			case '2' : $productTypeCode = ( $PLIST[$i]['mortgage_guarantees']=='1' || preg_match("/(NPL|배당금)/", $PLIST[$i]['title']) ) ? 'REAL_ESTATE' : 'PROJECT_FINANCING';				break;		// 부동산(주담대)  : 부동산PF
			case '3' : $productTypeCode = 'TRADE_RECEIVABLES';	break;		// 매출채권
		//case '4' : $productTypeCode = 'PERSONAL_CREDIT';		break;		// 개인신용
		//case '5' : $productTypeCode = 'BIZ_CREDIT';					break;		// 사업자신용
		}

		$repayMethodCode = '';
		switch($PLIST[$i]['repay_type']) {
			case '1' : $repayMethodCode = 'BULLET_PAY';	break;		// 만기일시
			case '2' : $repayMethodCode = 'LEVEL_PAY';	break;		// 원리금균등
			case '3' : $repayMethodCode = 'ETC_PAY';		break;		// 기타
		//default  : $repayMethodCode = 'MIX_PAY';		break;		// 혼합
		}


		/*
		투자상태코드
			모집예고      : COLLECT_NOTICE
			사전모집중    : PRE_COLLECT_PROGRESS (2018-08-23 추가)
			사전모집완료  : PRE_COLLECT_COMPLETE (2018-08-23 추가)
			모집중        : COLLECT_PROGRESS
			모집성공      : COLLECT_SUCCESS
			모집취소      : COLLECT_CANCEL
			모집완료      : COLLECT_SUCCESS
			상환중        : REPAY_PROGRESS
			상환완료      : REPAY_COMPLETE)
		*/
		$investStateCode = '';
		switch($PLIST[$i]['state']) {
			case '1' : $investStateCode = 'REPAY_PROGRESS';		break;		// 상환중
			case '2' : $investStateCode = 'REPAY_COMPLETE';		break;		// 상환완료
			case '3' : $investStateCode = 'COLLECT_CANCEL';		break;		// 투자금모집실패 (모집취소 상태코드를 넣어둠)
			case '4' : $investStateCode = 'REPAY_POOR';				break;		// 상환불가(부실)
			case '5' : $investStateCode = 'REPAY_COMPLETE';		break;		// 상환완료
			case '6' : $investStateCode = 'COLLECT_CANCEL1';	break;		// 대출취소(기표전) (모집취소 상태코드를 넣어둠)
			case '7' : $investStateCode = 'COLLECT_CANCEL1';	break;		// 대출취소(기표후) (모집취소 상태코드를 넣어둠)
			case '8' : $investStateCode = 'REPAY_OVERDUE';		break;		// 연체
			case '9' : $investStateCode = 'REPAY_UNABLE';			break;		// 부도(상환불가)
			default  :

					if($investStartDateTime > $nowDateTime) {
						$investStateCode = 'COLLECT_NOTICE';		// 모집예고
					}
					else {

						if($PLIST[$i]['advance_invest']=='Y') {		// 사전투자 상품 설정 ------------------

							// 본투자 시작시간전
							if(G5_TIME_YMDHIS < $PLIST[$i]['start_datetime']) {
								$advence_recruit_amt = $PLIST[$i]['recruit_amount'] * ($PLIST[$i]['advance_invest_ratio']/100);		// 사전투자모집목표금액
								if( $PLIST[$i]['total_invest_amount'] < $advence_recruit_amt ) {
									$investStateCode = 'PRE_COLLECT_PROGRESS';		// 사전모집중
								}
								else {
									$investStateCode = 'PRE_COLLECT_COMPLETE';		// 사전모집완료
									$investStartDateTime = preg_replace('/(-| |:)/', '', $PLIST[$i]['start_datetime']);		// 본투자 표기시간을 위해 $investStartDateTime 값 변경
								}
							}
							else {
								if($PLIST[$i]['total_invest_amount'] < $PLIST[$i]['recruit_amount']) {
									$investStateCode = ($nowDateTime < $investEndDateTime) ? 'COLLECT_PROGRESS' : 'COLLECT_CANCEL';		// 모집중 : 모집실패
								}
								else {
									$investStateCode = 'COLLECT_SUCCESS';		// 모집완료
								}
								$investStartDateTime = preg_replace('/(-| |:)/', '', $PLIST[$i]['start_datetime']);		// 본투자 표기시간을 위해 $investStartDateTime 값 변경
							}

						}
						else {		// 정상투자 상품 설정 ------------------

							if($PLIST[$i]['total_invest_amount'] < $PLIST[$i]['recruit_amount']) {
								$investStateCode = ($nowDateTime < $investEndDateTime) ? 'COLLECT_PROGRESS' : 'COLLECT_CANCEL';		// 모집중 : 모집실패
							}
							else {
								$investStateCode = 'COLLECT_SUCCESS';		// 모집완료
							}

						}
					}

			break;
		}

		$repayDate = $repayScheduledDate = '';
		if($PLIST[$i]['loan_end_date'] > '0000-00-00') {
			$repayDate = preg_replace('/-/', '', $PLIST[$i]['loan_end_date']);
		}
		else {
			$repayScheduledDate = ($PLIST[$i]['invest_days'] > 0) ? date('Ymd', strtotime('+'.$PLIST[$i]['invest_days'].' day')) : date('Ymd', strtotime('first day of +'.$PLIST[$i]['invest_period'].' month'));
		}

		$collectCancelDateTime   = '';		// 모집취소 설정시 각 케이스별 액션일 설정
		if($investStateCode=='COLLECT_CANCEL') {
			if($PLIST[$i]['cancel_date']>'0000-00-00') {
				$collectCancelDateTime = ($PLIST[$i]['cancel_date']>'0000-00-00') ? preg_replace('/(-| |:)/', '', $PLIST[$i]['cancel_date'])."120000" : preg_replace('/(-| |:)/', '', $PLIST[$i]['end_datetime']);
			}
		}

		// 최종투자자의 투자시간 가져오기
		$collectCompleteDateTime = '';
		if($PLIST[$i]['recruit_amount'] <= $PLIST[$i]['total_invest_amount']) {
			$LASTINVEST = sql_fetch("SELECT idx, insert_date, insert_time FROM cf_product_invest WHERE product_idx='".$PLIST[$i]['idx']."' ORDER BY idx DESC LIMIT 1");
			if($LASTINVEST['idx'])
			$collectCompleteDateTime = $LASTINVEST['insert_date'] . " " . $LASTINVEST['insert_time'];
			$collectCompleteDateTime = preg_replace('/(-| |:)/', '', $collectCompleteDateTime);
		}



		$detailPageUrl = G5_URL . '/finnq/product/'. $PLIST[$i]['idx'];

		$productList[$i]['productNumber']            = $PLIST[$i]['idx'];
		$productList[$i]['productTypeCode']          = $productTypeCode;
		$productList[$i]['productName']              = $PLIST[$i]['title'];
		$productList[$i]['investStartDateTime']      = $investStartDateTime;
		$productList[$i]['profitRate']               = floatRtrim($PLIST[$i]['invest_return']);
		$productList[$i]['repayMethodCode']          = $repayMethodCode;
		$productList[$i]['investMonthCount']         = $PLIST[$i]['invest_period'];
		$productList[$i]['collectAmount']            = $PLIST[$i]['recruit_amount'];
		$productList[$i]['collectParticipateAmount'] = (int)$PLIST[$i]['total_invest_amount'];
		$productList[$i]['investStateCode']          = $investStateCode;
		$productList[$i]['repayDate']                = $repayDate;
		$productList[$i]['repayScheduledDate']       = $repayScheduledDate;
		$productList[$i]['imageThumbnailUrl']        = $PLIST[$i]['main_image'];
		$productList[$i]['detailPageUrl']            = $detailPageUrl;
		$productList[$i]['additionalAgreementUseYn'] = 'N';
		$productList[$i]['collectCancelDateTime']    = $collectCancelDateTime;			// 모집취소일시
		$productList[$i]['collectCompleteDateTime']  = $collectCompleteDateTime;		// 모집완료일시
		$productList[$i]['rewardYn']                 = 'N';													// 리워드상품여부 (2018-12-28 추가)
		$productList[$i]['minInvestAmount']          = $_CONF['min_invest_limit'];	// 최소투자금액 (최소투자금액을 별도로 사용하는 상품인 경우 사용. 값이 없으면 핀크에서 10,000원으로 설정함)
		if($productTypeCode == 'PROJECT_FINANCING') {
			$productList[$i]['maxInvestAmount'] = $_CONF['pf_max_invest_limit'];	// 부동산PF 상품일 경우 최대투자제한금액 출력
		}

		$productList[$i]['adjustmentStartDate']      = (isSet($PLIST[$i]['state']) && $PLIST[$i]['state'] > '0000-00-00') ? preg_replace("/-/", '', $PLIST[$i]['loan_start_date']) : '';		// 정산시작일 (2018-12-28 추가)
		$productList[$i]['adjustmentEndDate']        = '';													// 정산종료일 : 당사같은 수수료일시 지급방식의 계약사는 내용 필요 없음. (2018-12-28 추가)

		array_push($ARR['data']['productList'], $productList[$i]);

	}
}



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");


@sql_close();
exit;

?>