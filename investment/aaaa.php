<?

exit;


include_once('_common.php');

$prd_idx = '9298';

$PRDT = sql_fetch("SELECT * FROM cf_product WHERE idx='".$prd_idx."'");

$po_content = $PRDT['title']. "-투자";


$sql = "SELECT * FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y' AND member_idx!='57912' ORDER BY idx";
$res = sql_query($sql);

$no = 1;
while($R = sql_fetch_array($res)) {

	echo $no." :: ";

	$MB = sql_fetch("SELECT mb_no, mb_id FROM g5_member WHERE mb_no='".$R['member_idx']."'");

/*
	// 포인트 차감
	$point_amount = $R['amount'] * (-1);
	echo "insert_point({$MB['mb_id']}, {$point_amount}, {$po_content}, '@invest', {$MB['mb_id']}, {$MB['mb_id']}.'-'.uniqid(''), 0) ::: ";
	echo insert_point($MB['mb_id'], $point_amount, $po_content, '@invest', $MB['mb_id'], $MB['mb_id'].'-'.uniqid(''), 0);
	echo "\n";
*/

/*
	$prin_rcv_no = "I". $R['idx'];
	$sqlx = "UPDATE cf_product_invest SET prin_rcv_no= '".$prin_rcv_no."' WHERE idx='".$R['idx']."' AND prin_rcv_no=''";
	echo $sqlx;
	echo sql_query($sqlx);
	echo "\n";
*/

/*
	//////////////////////////////////////////////////////////////////////
	// 금결원 중앙기록관리 투자신청 기록
	//////////////////////////////////////////////////////////////////////
	$p2pctr_reg_result = p2pctr_invest_register($R['member_idx'], $prd_idx);

	if($p2pctr_reg_result) {

		////////////////////////////////////
		// 투자한도 업데이트 실행 2
		////////////////////////////////////
		$exec_str = "/usr/local/php/bin/php -q /home/crowdfund/public_html/investment/get_p2pctr_limit_amt.exec.php " .  $R['member_idx'];
		$exec_result = shell_exec($exec_str);
*/

	}


	$no++;

}

sql_close();


?>