<?
include_once('_common.php');

$tymd = $_POST['target_ymd'];
$res["target_ymd"] = $tymd;

$sqlM = "select * from cf_event_10bM where ymd='$tymd'";
$resM = sql_query($sqlM);
$rowM = sql_fetch_array($resM);
if ($rowM["funding_money"]) $win_money = $rowM["funding_money"]/10000;
else {
	$res["win_cnt"] = 0;
	echo json_encode($res);
	die();
}

$sql = "select * from cf_event_10bS where ymd='$tymd' and answer=$win_money";
$res["sql"] = $sql;
$sql_res = sql_query($sql);
$cnt = $sql_res->num_rows;

$res["win_cnt"] = $cnt;

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($sql_res);
	$res["win"][$i] = $row;
}
?>
<?
echo json_encode($res);
?>