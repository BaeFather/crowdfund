<?
include_once('./_common.php');
?>
<?
$sql = "SELECT addr,jm, d_name, dj_name FROM hello_apt_kb WHERE mg_id='$mg_id' AND ju_seri='$ju_seri'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

$retn = array();

$retn["juso"] = $row["addr"];
$retn["jm"] = $row["jm"];
$retn["d_name"] = $row["d_name"];
$retn["dj_name"] = $row["dj_name"];

echo json_encode($retn, JSON_UNESCAPED_SLASHES);
?>