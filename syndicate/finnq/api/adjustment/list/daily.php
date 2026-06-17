<?
###############################################################################
## 핀크 일별 수수료 정산 목록 조회
##	※ 핀크를 통해 투자한 내역만을 대상으로 함
##  ※ 추출조건은 "투자번호 기준"
###############################################################################

include_once("../../../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST[data] = array(
	requestTimestamp	요청타임스탬프	string	Y	밀리초단위 타임스탬프
	aggregateDate			조회날짜				string	Y	yyyymmdd
	pageNumber				페이지번호			number	Y	최소:1
	pageSize					페이지크기			number	Y	최소:1, 최대:1000
);
*/

/*
curl -X POST -H "Content-Type:application/json" -d '{"head":{"requestInstitutionCode":"FNNQ","responseInstitutionCode":"HLLO","requestHash":"WFbUkV1SXb7/LdWuWqCQbrMfiPgZ6pBkHpZbubV9qorZFuOhezdIiQHi1EOu92xscspYq363UKEPugpjPcZ0SnbA89Wgeb+0bWsZOuQAru1ukoJ2L8gOvb9tJ55LUjkmqj19tpg+LQkYTJu3+qfjBFBlyWe/kfMQw44W8UhoeycyhXuh5jjrd5MCzYODMqFditFB+XuHlW9tVB81mcSLZjBNFSZzHAf6AyRdwFLYb75VXf9QZtrAgQphTt0WEzZLSuqVYty8zYkC5UP/exbWhJZHsgqOli1UB4NjCa2bIYGrBa1oSx2S7qQT7xkeODGm/mNmxEAGl8OBWCilCyyDSA=="},"data":{"aggregateDate":"20181001","pageNumber":"1","pageSize":"10"}}' https://www.hellofunding.co.kr/api/adjustment/list/daily
*/

$contract_date = "2019-01-23";	// 계약기준일

if(!$REQUEST['data']['aggregateDate']) $REQUEST['data']['aggregateDate'] = date('Ymd');

$aggregateDate = substr($REQUEST['data']['aggregateDate'], 0, 4) . '-' . substr($REQUEST['data']['aggregateDate'], 4, 2) . '-' . substr($REQUEST['data']['aggregateDate'], 6);

$where = " 1";
$where.= " AND B.state!='' AND B.loan_start_date='".$aggregateDate."'";
$where.= " AND A.invest_state IN ('Y','R')";
$where.= " AND A.syndi_id='".$_CONF['SYNDI_ID']."'";
$where.= " AND A.insert_date>='".$contract_date."'";
//$where.= " AND A.insert_date='".$aggregateDate."'";


$sql = "SELECT COUNT(A.idx) AS totalCount FROM cf_product_invest A WHERE $where";
$R = sql_fetch($sql);
//echo $sql . " :::: ". $R['totalCount'] ."\n\n";

$rows = ($REQUEST['data']['pageSize']) ? $REQUEST['data']['pageSize'] : 200;
$total_page  = ceil($R['totalCount'] / $rows);
$page = ( empty($REQUEST['data']['pageNumber']) || $REQUEST['data']['pageNumber']==0 ) ? 1 : $REQUEST['data']['pageNumber'];
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$ARR['data']['totalCount'] = (int)$R['totalCount'];
$ARR['data']['adjustmentList'] = array();

if($R['totalCount'] > 0) {

	$sql = "
		SELECT
			A.idx AS invest_idx, A.product_idx, A.member_idx, A.amount, A.insert_date,
			B.state, B.loan_start_date, B.loan_end_date, B.loan_end_date_orig, B.cancel_date
		FROM
			cf_product_invest A
		INNER JOIN
			cf_product B  ON A.product_idx = B.idx
		WHERE
			$where
		ORDER BY
			A.idx DESC,
			B.open_datetime DESC
		LIMIT
			$from_record, $rows";
	//echo $sql."\n\n";

	$res    = sql_query($sql);
	$rcount = sql_num_rows($res);

	for($i=0; $i<$rcount; $i++) {
		$ROW = sql_fetch_array($res);

		$ROW['exclude_amount'] = 0;

		if($ROW['invest_idx']) {

			if(in_array($ROW['state'], array('6','7'))) {
				$ROW['exclude_amount'] = $ROW['amount'];
			}
			else {
				if($ROW['loan_end_date'] < date('Y-m-d')) {
					$sql2 = "SELECT IFNULL(SUM(principal), 0) AS repay_principal FROM cf_product_give WHERE invest_idx='".$ROW['invest_idx']."' AND LEFT(banking_date,10)<='".$aggregateDate."'";
					$ROWX = sql_fetch($sql2);
					$ROW['exclude_amount'] = $ROWX['repay_principal'];
				}
			}

			$LIST['productNumber']   = $ROW['product_idx'];
			$LIST['institutionInvestNumber'] = $ROW['invest_idx'];
			$LIST['investAmount']    = $ROW['amount'];
			$LIST['excludeAmount']   = $ROW['exclude_amount'];		// 중도상환등의 이유로 정산에서 제외되는 금액
			$LIST['feeRate']         = sprintf("%.2f", $_CONF['syndication_fee']);
			$LIST['adjustmentDate']  = $REQUEST['data']['aggregateDate'];		// 정산일(조회한 날)

			array_push($ARR['data']['adjustmentList'], $LIST);

		}

	}
	sql_free_result($res);

}



##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

@sql_close();
exit;

?>