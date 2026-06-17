<?
include "_common.php";
$hello_code    = "K210500031";
?>
<?
//if (function_exists(get_p2pord_no)) echo "있다";

$aa = get_p2pord_no_ttest();
echo $aa;

?>
<?
function get_p2pord_no_ttest() {

	//$hello_code = "M202112431";
	global $hello_code;

	//chk_odno_row();

	$gubun = "TXNO";

	$sql = "SELECT * FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun' LIMIT 1";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	//if (!$cnt) reset_odno_tbl();

	$sql = "SELECT * FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun' LIMIT 1";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	if ($cnt) {

		$sql = "INSERT INTO p2pctr_order_no2_test(ymd, gubun, odno)
				SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
		$res = sql_query($sql);
		echo $res;
		$idx = sql_insert_id();

		$sql2 = "SELECT * FROM p2pctr_order_no2_test WHERE idx='$idx'";
		$row2 = sql_fetch($sql2);
		$no  = $row2["odno"];

		if (!$no) {
			sleep(1);
			$sql = "INSERT INTO p2pctr_order_no2_test(ymd, gubun, odno)
					SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
			$res = sql_query($sql);
			$idx = sql_insert_id();

			$sql2 = "SELECT * FROM p2pctr_order_no2_test WHERE idx='$idx'";
			$row2 = sql_fetch($sql2);
			$no  = $row2["odno"];
		}

	}

	//$odno = $hello_code. substr(date("Ymd"), 2). str_pad($no , 4, "0", STR_PAD_LEFT);
	$odno = $hello_code. str_pad($no , 10, "0", STR_PAD_LEFT);

	return $odno;

}
?>