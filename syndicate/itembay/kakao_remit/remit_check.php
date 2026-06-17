<?php

include_once('../hf_common.php');

$token = $_REQUEST['token'];
$oid   = $_REQUEST['oid'];

$chk_kakao = check_kakao_remit($token);



print_r($chk_kakao);

$sql = "update cf_kakao_remit set
			send_result = '$chk_kakao[send_status]',
			sent_amount = '$chk_kakao[sent_amount]'
		where oid = '$oid'";
echo "$sql";
sql_query($sql);

?>
<?
function check_kakao_remit($token) {

	$kakao_check_url = "https://money-api.kakao.com/ext/api/v1/transfer/partner/confirm";

	$CID     = "MC0000D7FBL5CL6";
	$API_KEY = "aa2b94d273964fe9b452b1e48644257e";

	$kakao_params = array(
		'api_key' => $API_KEY,
		'cid' => $CID,
		'token' => $token
	);

	$strArrResult = request_curl($kakao_check_url, $kakao_params );

	return $strArrResult;
}

function request_curl($url,  $data=array()) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json;charset=utf-8'));

	$result[0] = curl_exec($ch);
	$result[1] = curl_errno($ch);
	$result[2] = curl_error($ch);
	$result[3] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);

	return json_decode($result[0], true);
	//return $result;
	
}
?>