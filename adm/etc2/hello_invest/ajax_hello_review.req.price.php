<?
include_once('./_common.php');

$ARR = array();

$ARR['opt'] = $_REQUEST['opt'];
$ARR['requestPrice'] = $_REQUEST['requestPrice'];


$tot_amount = sql_fetch("
	SELECT recruit_amount FROM cf_product WHERE idx='$opt'
")['recruit_amount'];

$ARR['tot_amount'] = $tot_amount;

$perc = ($ARR['requestPrice']/$ARR['tot_amount']) * 100;
$perc = floatRtrim(floatCutting($perc, 2));

$ARR['perc'] = $perc; 

echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

sql_close();
exit;

?>