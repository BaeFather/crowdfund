<?
$client_id = "1e41c442-f37c-46d8-a75f-9f535929f45f";
$client_secret = "9fbc245341d64056f0678e18197b1183";
$product_id = "2101466024";

$nice_access_token = "62c97cb2-712b-44a9-8555-bf2f757e82c8";

$nice_host = "https://svc.niceapi.co.kr:22001";
?>
<?
function nice_curl($url, $headers, $data) {

	$json_data = json_encode($data,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);

	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = SUBSTR($result, 0, $header_size);
	$body = SUBSTR($result, $header_size);

	curl_close($ch);

	$res_arr = json_decode($result, true);

	return $res_arr;

}
?>