<?
$response_idx = $_REQUEST["response_idx"];

if (!$response_idx) die();


$curl_res = toomics_curl($response_idx);

echo $curl_res;

?>
<?
function toomics_curl($response_idx) {

	$data = array(
		'response_idx' => $response_idx
	);

	$ch = curl_init();
	//curl_setopt($ch , CURLOPT_URL, 'http://hello-pay.kr:8001/test_toomics.php');
	curl_setopt($ch , CURLOPT_URL, 'https://www.toomics.com/mypage/receiver_hellofunding');
	curl_setopt($ch , CURLOPT_POST, 1);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch , CURLOPT_POSTFIELDS, http_build_query($data));
	//curl_setopt($ch , CURLOPT_POSTFIELDS, 't_from_date='.$t_from_date);
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, 0);
	$result = curl_exec($ch);
	//print_r(curl_getinfo($ch));
	curl_close($ch);

	return $result;
}
?>