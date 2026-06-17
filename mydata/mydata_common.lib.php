<?
$org_code = "L1AARR0000";



// 거래번호 x-api-tran-id
function get_tran_id() {
	global $org_code;

	$mls = "H".milliseconds();
	$trx_id = $org_code."S".$mls;

	return $trx_id;
}

function get_prd_type($cat, $cat2) {

	$type = "";
	
	if ($cat=="1") { // 동산

		$type = "04";

	} else if ($cat=="2") { // 부동산

		if ($cat2=="1") {  // 부동산 주택담보대출
			$type = "02";  
		} else {
			$type = "01";  // 부동산 PF
		}

	} else if ($cat=="3") { // 부동산

		$type = "03"; // 어음 매출채권 담보대출

	}

	return $type;

}

function milliseconds() {
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}

function getBillTable_simple($prd_idx) {
	$range = "";
	if($prd_idx) {
		$range = floor($prd_idx/1000) * 1000;
		$range = sprintf('%05d', $range);		// 테이블생성명은 만단위
		$table = "cf_product_bill_" . $range;
	}
	return $table;
}
?>