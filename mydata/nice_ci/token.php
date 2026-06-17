<?
include_once($_SERVER["DOCUMENT_ROOT"]."/mydata/mydata.common.lib.php");
?>
<?
$old_result = '{  "dataHeader": {    "GW_RSLT_CD": "1200",    "GW_RSLT_MSG": "오류 없음"  },  "dataBody": {    "access_token": "62c97cb2-712b-44a9-8555-bf2f757e82c8",    "token_type": "bearer",    "expires_in": 1.576983049E9,    "scope": "default"  }}';
$old_result = '{  "dataHeader": {    "GW_RSLT_CD": "1200",    "GW_RSLT_MSG": "오류 없음"  },  "dataBody": {    "access_token": "62c97cb2-712b-44a9-8555-bf2f757e82c8",    "token_type": "bearer",    "expires_in": 1.576818014E9,    "scope": "default"  }}';
$old_arr = json_decode($old_result, true);

//echo "<pre>"; print_r($old_arr); echo "</pre>";
//die();
?>
<?
$url = "https://svc.niceapi.co.kr:22001/digital/niceid/oauth/oauth/token";

$headers = array();
ARRAY_PUSH($headers,"Content-Type: application/x-www-form-urlencoded;");
ARRAY_PUSH($headers,"Authorization: "."Basic " . base64_encode($client_id.":".$client_secret));

$data = array();
$data["grant_type"] = "client_credentials";
$data["scope"] = "default";

echo "<pre>"; print_r($headers); echo "</pre>";
echo "<pre>"; print_r($data); echo "</pre>";
echo "<br/><br/>=========================================================<br/><br/>";
?>
<?
/*
$json_data = json_encode($data,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);

$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = SUBSTR($result, 0, $header_size);
$body = SUBSTR($result, $header_size);

curl_close($ch);

echo "<pre>"; echo $result; echo "</pre>";
*/
?>