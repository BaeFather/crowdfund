<?
include_once('_common.php');

$srch_ymd = $_POST["srch_ymd"];
$res["ymd"] = $srch_ymd;


$sql = "select * from cf_event_10bS where ymd='$srch_ymd' order by idx desc";
$res2 = sql_query($sql);
$cnt = sql_num_rows($res2);
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