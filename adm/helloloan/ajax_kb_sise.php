<?
include_once('./_common.php');
?>
<?
$sql = "SELECT * FROM hello_apt_kb WHERE mg_id='$mg_id' AND ju_seri='$ju_seri' order by kijun desc";
$res = sql_query($sql);
$row = sql_fetch_array($res);

//echo "<pre>"; print_r($row); echo "</pre>";
echo json_encode($row, JSON_UNESCAPED_SLASHES);
?>