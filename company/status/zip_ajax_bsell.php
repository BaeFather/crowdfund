<?
include_once('./_common.php');
error_reporting(0); error_reporting(E_ERROR);

while(list($key, $value) = each($_REQUEST)) { if(!is_array(${$key})) ${$key} = trim($value); }

$ret = array();

$ret["list_cnt"] = 0;

echo json_encode($ret);
?>