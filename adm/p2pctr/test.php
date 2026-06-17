<?
include_once('./_common.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');


$a1 = get_p2pord_no2();
$a2 = get_date_serial2("loan_register_id");
$a3 = get_date_serial2("goods_id");
$a4 = get_date_serial2("investment_register_id");
$a5 = get_date_serial2("loan_contract_id");
$a6 = get_date_serial2("contract_id");

echo $a1."<br/>";
echo $a2."<br/>";
echo $a3."<br/>";
echo $a4."<br/>";
echo $a5."<br/>";
echo $a6."<br/>";

function reset_odno_tbl() {
	echo "reset<br/>";
	sql_query("DELETE FROM p2pctr_order_no2 WHERE idx>0");
	sql_query("ALTER TABLE p2pctr_order_no2 AUTO_INCREMENT=1");

	
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='TXNO'");  // 거래번호
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='LNRG'");  // 대출 신청
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='GDRG'");  // 상품 모집
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='IVRG'");  // 투자 신청
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='LNCT'");  // 대출 계약
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='IVCT'");  // 투자 계약
}

function get_date_serial2($gbn) {

	if ($gbn=="loan_register_id") $gubun="LNRG";
	else if ($gbn=="goods_id") $gubun="GDRG";
	else if ($gbn=="investment_register_id") $gubun="IVRG";
	else if ($gbn=="loan_contract_id") $gubun="LNCT";
	else if ($gbn=="contract_id") $gubun="IVCT";

	$chk_sql = "SELECT count(*) chk_cnt FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
	$chk_row = sql_fetch($chk_sql);

	if (!$chk_row["chk_cnt"]) {
		reset_odno_tbl();
	}

	$sql = "INSERT INTO p2pctr_order_no2(ymd, gubun, odno)
			SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
	$res = sql_query($sql);
	$idx = sql_insert_id();


	$sql2 = "SELECT * FROM p2pctr_order_no2 WHERE idx='$idx'";
	$row2 = sql_fetch($sql2);
	$ret = $row2["odno"];

	return $ret;
}

function get_date_serial22222($gbn) {

	//chk_odno_row();

	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";

	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$row = sql_fetch_array($res);


	if ($gbn=="loan_register_id") {
		$ret = $row["loan_register_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET loan_register_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="goods_id") {
		$ret = $row["goods_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET goods_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="investment_register_id") {
		$ret = $row["investment_register_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET investment_register_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="loan_contract_id") {  // 대출 계약 번호
		$ret = $row["loan_contract_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET loan_contract_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="contract_id") {  // 투자 계약 번호
		$ret = $row["contract_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET contract_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	}

	return $ret;
}

function get_p2pord_no2() {

	//$hello_code = "M202112431";
	global $hello_code;



	$gubun = "TXNO";

	$sql = "SELECT * FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	if (!$cnt) reset_odno_tbl();

	if ($cnt) {

		$sql = "INSERT INTO p2pctr_order_no2(ymd, gubun, odno)
				SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
		$res = sql_query($sql);
		$idx = sql_insert_id();

		$sql2 = "SELECT * FROM p2pctr_order_no2 WHERE idx='$idx'";
		$row2 = sql_fetch($sql2);
		$no  = $row2["odno"];

	}

	//$odno = $hello_code. substr(date("Ymd"), 2). str_pad($no , 4, "0", STR_PAD_LEFT);
	$odno = $hello_code. str_pad($no , 10, "0", STR_PAD_LEFT);

	return $odno;

}
?>