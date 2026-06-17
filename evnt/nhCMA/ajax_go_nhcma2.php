<?
include_once('../../common.php');
?>
<?
$cma_num = $_REQUEST["cma_num"];
$ret = array();
if ($member["mb_no"]) {

	$chk_sql = "select count(*) chk_cnt from cf_event_nhCMA where mb_no='$member[mb_no]'";
	$chk_res = sql_query($chk_sql);
	$chk_row = sql_fetch_array($chk_res);
	$chk = $chk_row["chk_cnt"];

	if ($chk) {
		$ret["res"] = "dup";
	} else {
		$sql = "insert into cf_event_nhCMA set mb_no='$member[mb_no]', mb_id='$member[mb_id]', cma_num='$cma_num', insert_datetime=now() ";
		sql_query($sql);
		$ret["res"] = "ok";
	}

} else {
	$ret["res"] = "fail";
}
echo json_encode($ret);
?>