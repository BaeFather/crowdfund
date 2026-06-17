<?
include_once("_common.php");

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

$start_num = ( preg_match("/\[제/", $ttl) && preg_match("/호/", $ttl) ) ? @str_f6($ttl, "[제", "호") : "";

$sql = "SELECT COUNT(idx) cnt FROM cf_product WHERE start_num='$start_num' AND state!=''";
$row = sql_fetch($sql);
$chk = ($row["cnt"]) ? 'N' : 'Y';

$json_data = array();
$json_data["start_num"] = $start_num;
$json_data["check_result"] = $chk;
$json_data["check_count"] = $row["cnt"];

echo json_encode($json_data);

?>