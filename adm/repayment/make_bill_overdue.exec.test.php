<?
###############################################################################
## 연체 정산내역 생성
## php -q /home/crowdfund/public_html/adm/repayment/make_bill_overdue.exec.test.php [품번] [대상일] [mode]
## 연체판별기준 : 빌링테이블의 정상적인 일별 정산내역상 최대회차수와 지급테이블의 지급최대회차수를 비교하여 판별
###############################################################################

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";
include_once($base_path . '/common.cli.php');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }


$prd_idx = $_SERVER['argv'][1];
if(!$prd_idx) exit;

$bill_table = getBillTable($prd_idx);

$today     = date('Y-m-d');
$exec_date = ($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : date('Y-m-d');			// 실행일
$bill_date = date("Y-m-d", strtotime($exec_date . " -1 day"));								// 정산대상일



// 1. 연체상품인지 검증
$is_overdue = false;

$PRDT = sql_fetch("SELECT * FROM cf_product WHERE idx = '".$prd_idx."'");

if(!$PRDT['idx']) { echo "상품이 없습니다."; sql_close(); exit; }

if($PRDT['calc_type']=='2') {
	$last_bill_date = getUsableDate($PRDT['loan_end_date']);		// 말일산입	: 대출시작일을 정산대상에서 제외)
}
else {
	$last_bill_date = getUsableDate(date("Y-m-d", strtotime($PRDT['loan_end_date'] . " -1 day")));		// 초일산입	: 대출종료일을 정산대상에서 제외
}

$total_turn = sql_fetch("SELECT IFNULL(MAX(turn),0) AS turn FROM {$bill_table} WHERE product_idx='".$prd_idx."'")['turn'];

$last_paid_turn = sql_fetch("SELECT IFNULL(MAX(turn),0) AS turn FROM cf_product_give WHERE product_idx='".$prd_idx."'")['turn'];


if( $PRDT['state']=='8' ) {		// 연체일을 수동을 찍어둔 상품만 연체 계산 프로세스 작동
	$is_overdue = true;
}

echo "exec_date : " . $exec_date . "\n";
echo "bill_date : " . $bill_date . "\n";
echo "last_bill_date : " . $last_bill_date . "\n";
echo "total_turn : "  . $total_turn . "\n";
echo "last_paid_turn : " . $last_paid_turn . "\n";
echo "is_overdue : " . $is_overdue . "\n";
exit;

if(!$is_overdue) { exit; }







sql_close();
exit;

?>