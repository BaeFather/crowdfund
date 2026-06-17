<?
include_once('../../common.php');
?>
<?
$cma_num = $_REQUEST["cma_num"];
$ret = array();
if ($member["mb_no"]) {

	$chk_sql = "select * from cf_event_nhCMA where mb_no='$member[mb_no]'";
	$chk_res = sql_query($chk_sql);
	$chk = sql_num_rows($chk_res);	

	if ($chk) {

		$chk_row = sql_fetch_array($chk_res);

		$ret["a1"] = substr($chk_row["insert_datetime"],0,5);
		$ret["a2"] = date("Y-m");

		if (substr($chk_row["insert_datetime"],0,7)==date("Y-m")) {
			$sql = "update cf_event_nhCMA set cma_num='$cma_num', insert_datetime=now()  where mb_no='$member[mb_no]'";
			sql_query($sql);
			$ret["res"] = "ok";
		} else {
			$ret["res"] = "old";
		}
	}

} else {
	$ret["res"] = "fail";
}
echo json_encode($ret);
?>