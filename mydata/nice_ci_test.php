<?
$client_id = "1e41c442-f37c-46d8-a75f-9f535929f45f";
$client_secret = "9fbc245341d64056f0678e18197b1183";



$headers = array();
ARRAY_PUSH($headers,"Content-Type: application/json;");
ARRAY_PUSH($headers,"Authorization: "."Basic " . base64_encode($client_id.":".$client_secret));
ARRAY_PUSH($headers,"productID : "."2101466024");

$url = " https://svc.niceapi.co.kr:22001/digital/niceid/cert/v1.0/ipin/addinfo/ci";

$d = array();
$d["site_code"] = "";
$d["info_req_type"] = "1";  // 정보요청유형 1 : CI제공
$d["jumin_id"] = "7008031111110";
$d["req_no"] = "";

$data = array();
$data["symkey_version"] = "";  // 대칭기 버전 string 50
$data["enc_data"] = json_encode($d,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE) ;  // JSON암호화 값(요청정보 대칭키 암호화 값) string 1000
$data["integrity_value"] = "";  // 무결성 체크 값 (end_data HMAC 및 Base64인코딩) string 44

echo "<pre>"; print_r($data); echo "</pre>";
die();
?>
<?
$json_data = json_encode($data,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRESERVE_ZERO_FRACTION);

$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = SUBSTR($result, 0, $header_size);
$body = SUBSTR($result, $header_size);

curl_close($ch);

echo "<pre>"; echo $result; echo "</pre>";
?>