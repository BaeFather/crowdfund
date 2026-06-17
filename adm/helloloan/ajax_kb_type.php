<?
include_once('./_common.php');
?>
<?
$sql = "SELECT ju_seri, jm, ju_seri2 FROM hello_apt_kb WHERE mg_id='$mg_id' ORDER BY ju_seri";
$res = sql_query($sql);
$cnt = $res->num_rows;

$retn = array();
$retn["tp"] = array();

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	$tp = array();

	$tp["ju_seri"] = $row["ju_seri"];
	$tp["jm"] = $row["jm"];

	
	$retn["tp"][$i] = $tp;
}

echo json_encode($retn, JSON_UNESCAPED_SLASHES);
?>