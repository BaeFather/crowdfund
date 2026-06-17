<?
###############################################################################
## 핀크 월간 수수료 정산 목록 조회
##	※ 핀크를 통해 투자한 내역만을 대상으로 함
##  ※ 추출조건은 "상품번호 기준"
###############################################################################

include_once("../../../syndication_config.php");
//include_once($syndi_base_path . "/inc_jsonPrint_head.php");		// 테스트시 주석처리

/*
$REQUEST[data] = array(
	requestTimestamp	요청타임스탬프	string	Y	밀리초단위 타임스탬프
	aggregateDate			조회날짜				string	Y	yyyymm
	pageNumber				페이지번호			number	Y	최소:1
	pageSize					페이지크기			number	Y	최소:1, 최대:1000
);
*/

//$REQUEST['data']['aggregateDate'] = "201808";
if(!$REQUEST['data']['aggregateDate']) $REQUEST['data']['aggregateDate'] = date('Ym');

$aggregateDate = substr($REQUEST['data']['aggregateDate'], 0, 4) . '-' . substr($REQUEST['data']['aggregateDate'], 4);

$daycountOfMonth = date("t", strtotime($aggregateDate."-01"));
//echo $daycountOfMonth;

$where = " 1";
$where.= " AND B.state!='' AND LEFT(B.loan_start_date,7)='".$aggregateDate."'";
$where.= " AND A.invest_state IN('Y','R')";
$where.= " AND A.syndi_id='".$_CONF['SYNDI_ID']."'";
//$where.= " AND LEFT(A.insert_date,7)='".$aggregateDate."'";

$sql = "
	SELECT
		A.product_idx
	FROM
		cf_product_invest A
	INNER JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE
		$where
	GROUP BY
		A.product_idx";
$res = sql_query($sql);
$totalCount = sql_num_rows($res);		// !주의) 열수가 전체 카운트임
//echo "(".$totalCount.")\n\n";

$rows = ($REQUEST['data']['pageSize']) ? $REQUEST['data']['pageSize'] : 200;
$total_page  = ceil($totalCount / $rows);
$page = ($REQUEST['data']['pageNumber']=='') ? 1 : $REQUEST['data']['pageNumber'];
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$ARR['data']['totalCount']         = $totalCount;
$ARR['data']['totalInvestAmount']  = 0;
$ARR['data']['totalExcludeAmount'] = 0;
$ARR['data']['totalFee']           = 0;
$ARR['data']['adjustmentDate']     = $REQUEST['data']['aggregateDate'];
$ARR['data']['adjustmentList']     = array();

if($totalCount > 0) {

	$sql = "
		SELECT
			A.product_idx,
			COUNT(A.idx) AS cnt,
			IFNULL(SUM(A.amount), 0) AS amount,
			B.title, B.recruit_amount, B.state, B.open_datetime, B.start_datetime, B.loan_start_date, B.loan_end_date, B.loan_end_date_orig, B.cancel_date
		FROM
			cf_product_invest A
		INNER JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE
			$where
		GROUP BY
			A.product_idx
		ORDER BY
			B.open_datetime ASC,
			A.product_idx ASC
		LIMIT
			$from_record, $rows";
	//echo $sql."\n\n";

	$res    = sql_query($sql);
	$rcount = sql_num_rows($res);

	for($i=0; $i<$rcount; $i++) {

		$ROW = sql_fetch_array($res);

		//print_r($ROW);

		if($ROW['cnt']) {

			$LIST['productNumber'] = $ROW['product_idx'];
			$LIST['investAmount']  = $ROW['amount'];

			$LIST['fee']           = $ROW['amount'] * ($_CONF['syndication_fee'] / 100);


			// 전체 정산대상 일수 (정산상 종료일 = 대출종료일 - 1일)
			$SDATE         = new DateTime($ROW['loan_start_date']);
			$EDATE_ORIG    = new DateTime($ROW['loan_end_date_orig']);
			$INVEST_ORIG   = date_diff($SDATE, $EDATE_ORIG);
			$invest_days_o = $INVEST_ORIG->days;		// 최초계약상의 대출일수

			$feePerDay     = $LIST['fee'] / $invest_days_o;		// 일별 수수료

			$EDATE         = new DateTime($ROW['loan_end_date']);
			$INVEST_FNL    = date_diff($SDATE, $EDATE);
			$invest_days_f = $INVEST_FNL->days;		// 실제종료일까지의 대출일수


			// 호출기간 정산대상 일수
			$calcSDate     = (substr($ROW['loan_start_date'], 0, 7)==$aggregateDate) ? $ROW['loan_start_date'] : $aggregateDate."-01";
			$calcEDate     = (substr($ROW['loan_end_date'], 0, 7)==$aggregateDate) ? date('Y-m-d', strtotime($ROW['loan_end_date'])-86400) : $aggregateDate."-".date('t', $aggregateDate);	// 최종상환일의 1일은 정산대상일에서 제외함
			$calcEDateOrig = (substr($ROW['loan_end_date_orig'], 0, 7)==$aggregateDate) ? date('Y-m-d', strtotime($ROW['loan_end_date_orig']-86400)) : $aggregateDate."-".date('t', $aggregateDate);
			$adjustmentDateCount = ceil((strtotime($calcEDate)-strtotime($calcSDate)) / 86400) + 1;


			if($isTest=='1') {
				echo "[" . $ROW['product_idx'] . " : " . $ROW['state'] . "]\n";
				echo "계약기간 : " . $ROW['loan_start_date'] . " ~ " . $ROW['loan_end_date_orig'] . " (" . $invest_days_o ."일 수수료 " . floor($feePerDay * $invest_days_o) . "원)\n";
				echo "실제기간 : " . $ROW['loan_start_date'] . " ~ " . $ROW['loan_end_date'] . " (" . $invest_days_f ."일 수수료 " . floor($feePerDay * $invest_days_f) . "원)\n";
				echo "대상기간 : " . $calcSDate . " ~ " . $calcEDate . " (" . $adjustmentDateCount ."일 수수료 " . floor($feePerDay * $adjustmentDateCount) . "원)\n";
				echo "\n";
			}


			$LIST['excludeAmount'] = 0;
			if( in_array($ROW['state'], array('1','5')) ) {
				$sql2 = "
					SELECT
						IFNULL(SUM(A.principal), 0) AS repay_principal
					FROM
						cf_product_give A
					INNER JOIN
						cf_product_invest B  ON A.invest_idx=B.idx
					WHERE 1
						AND A.product_idx='".$ROW['product_idx']."'
						AND B.syndi_id='".$_CONF['SYNDI_ID']."'";
				//echo $sql2 . "\n\n";
				$ROWX = sql_fetch($sql2);
				$LIST['excludeAmount'] = $ROWX['repay_principal'];
			}


			$LIST['adjustmentDateCount'] = $adjustmentDateCount;

			$ARR['data']['totalInvestAmount']  += $ROW['amount'];
		//$ARR['data']['totalExcludeAmount'] += (int)$ROW['excludeAmount'];
			$ARR['data']['totalFee']           += $LIST['fee'];

			$calcStart = $calcEnd = $adjustmentDateCount = NULL;

			array_push($ARR['data']['adjustmentList'], $LIST);

		}
	}

}



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");


exit;
?>