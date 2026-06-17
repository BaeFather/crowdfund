<?

include_once("_common.php");

$DATA['idx']  = "164";
$DATA['path']  = "/home/crowdfund/public_html/shinhan/repay_request_send.php";
$DATA['param'] = "yes";
$DATA['rdate'] = "2019-04-26 15:58:39";

$data = json_encode($DATA, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

echo $data."\n\n";
echo addSlashes($data)."\n\n";
echo sql_escape_string($data);

?>