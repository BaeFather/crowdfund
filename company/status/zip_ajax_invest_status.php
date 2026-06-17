<?
include_once('../../common.cli.php');


while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

$ret = array();

$self_overdue_rate = 0;  // 연체율
$self_overdue_cnt = 0;   // 연체건수

if( strlen($ym) > 4 ) {

	//////////////////////////
	// 월 검색
	//////////////////////////

	$tmp_date = $ym.'-'.date('t', strtotime($ym.'-01'));  // 월의 말일

	// 헬로 자기자본 투자금액 (누적투자금액)
	$nujuk_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y'
			AND B.state IN('1','2','4','5','8','9')
			AND B.loan_start_date <= '".$tmp_date."'
		")['amount'];

	$paid_principal = sql_fetch("
		SELECT
			IFNULL(SUM(principal),0) AS principal
		FROM
			cf_product_give
		WHERE 1
			AND member_idx='48343'
			AND banking_date <= '$tmp_date 23:59:59'
		")['principal'];

	$remain_amt = $nujuk_invest_amt - $paid_principal;

}
else {

	//////////////////////////
	// 연도별 검색
	//////////////////////////

	//$todate = date('Y-m');

	if($ym==date('Y')) $ym = date('Y-m', strtotime("first day of -1month"));

	// 헬로 자기자본 투자금액 (누적투자금액)
	$nujuk_invest_amt = sql_fetch("
		SELECT
			IFNULL(SUM(A.amount),0) AS amount
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B  ON A.product_idx=B.idx
		WHERE 1
			AND A.member_idx='48343' AND A.invest_state='Y'
			AND B.state IN('1','2','4','5','8','9')
			AND LEFT(B.loan_start_date,".strlen($ym).") <= '".$ym."'
		")['amount'];

	$paid_principal = sql_fetch("
		SELECT
			IFNULL(SUM(principal),0) AS principal
		FROM
			cf_product_give
		WHERE 1
			AND member_idx='48343'
			AND LEFT(banking_date, ".strlen($ym).") <= '".$ym."'
		")['principal'];

	// 헬로 자기자본 투자금액 (투자잔액)
	$remain_amt = $nujuk_invest_amt - $paid_principal;

}

$ret['ym']                = (string)$ym;
$ret['nujuk_invest_amt']  = (string)$nujuk_invest_amt;
$ret['remain_amt']        = (string)$remain_amt;
$ret['self_overdue_rate'] = (string)$self_overdue_rate;
$ret['self_overdue_cnt']  = (string)$self_overdue_cnt;
//$ret['qry1']  = "SELECT IFNULL(SUM(A.amount),0) AS amount FROM cf_product_invest A LEFT JOIN cf_product B  ON A.product_idx=B.idx WHERE 1 AND A.member_idx='48343' AND A.invest_state='Y' AND B.state IN('1','2','4','5','8','9') AND LEFT(B.loan_start_date,".strlen($ym).") <= '".$ym."'";
//$ret['qry2']  = "SELECT IFNULL(SUM(principal),0) AS principal FROM cf_product_give WHERE 1 AND member_idx='48343' AND LEFT(banking_date, ".strlen($ym).") <= '".$ym."'";

echo json_encode($ret, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);


sql_close();
exit;

?>