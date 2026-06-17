<?
include_once('./_common.php');

/*
test_func();
echo "program end<br/>";

function test_func() {
	sleep(3);
	echo "function end<br/>";
}

die();
*/

$oddd = get_p2pord_no_testtt();
if ($oddd=="K2105000310000000000") $err=" ERROR";
else $err="";
echo "$i $oddd $err<br/>";


for ($i=0 ; $i<30 ; $i++) {
	$oddd = get_p2pord_no_testtt();
	if ($oddd=="K2105000310000000000") $err=" ERROR";
	else $err="";
	echo "$i $oddd $err<br/>";
	sleep(1);
}

?>
<?
function get_p2pord_no_testtt() {

	//$hello_code = "M202112431";
	global $hello_code;

	$gubun = "TXNO";

	$sql = "SELECT * FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun' LIMIT 1";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);


	if ($cnt) {

		$sql = "INSERT INTO p2pctr_order_no2_test(ymd, gubun, odno)
				SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
		$res = sql_query($sql);
		$idx = sql_insert_id();

		$sql2 = "SELECT * FROM p2pctr_order_no2_test WHERE idx='$idx'";
		$row2 = sql_fetch($sql2);
		$no  = $row2["odno"];

		/*
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
		*/

	}

	//$odno = $hello_code. substr(date("Ymd"), 2). str_pad($no , 4, "0", STR_PAD_LEFT);
	$odno = $hello_code. str_pad($no , 10, "0", STR_PAD_LEFT);

	return $odno;

}
?>