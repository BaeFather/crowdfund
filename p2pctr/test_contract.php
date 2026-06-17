<?
include_once('./_common.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr.lib.php');

$url  = $p2p_host_test;
$url .= "investments/contract/"."M202112431_210809_IC_1534085757";

$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
$strApiTrxDtm = get_dtm_no();   // 거래일시 (밀리세컨드)

echo $strApiTrxNo. " - ".$strApiTrxDtm;
die();

$method = "GET";

$headers[] = "Content-Type: application/json; charset=UTF-8";
ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

$data = array();


echo $url."<br/>".$method."<br/>";
echo "<pre>"; print_r($headers); echo "</pre>";
echo "<pre>"; print_r($data); echo "</pre>";

$res = curl_p2pctr($url , $method , $data , $headers);

echo "<br/><br/>------------------------- res ------------------------<br/><br/>";
echo "<pre>";print_r($res); echo "</pre>";
?>


