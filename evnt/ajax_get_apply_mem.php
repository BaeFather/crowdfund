<?
include_once('_common.php');

$srch_ymd = $_POST["srch_ymd"];
$res["ymd"] = $srch_ymd;
$sort = $_POST["sort"];
if ($sort=="money") $order = " order by answer";
else if ($sort=="id") $order = " order by mb_id";
else $order = " order by idx desc";

$sqlM = "select * from cf_event_10bM where ymd='$srch_ymd'";
$resM = sql_query($sqlM);
$rowM = sql_fetch_array($resM);
$res["m"] = $rowM;

$sql = "select * from cf_event_10bS where ymd='$srch_ymd' $order";
$res2 = sql_query($sql);
$cnt = sql_num_rows($res2);
$res["cnt"] = $cnt;
//$res["list"] = array();

$i=0;

while ($row=sql_fetch_array($res2)) {

	$res["list"][$i] = $row;
	$i++;

}
?>
<?
echo json_encode($res);
?>