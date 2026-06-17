<?php

include_once('../common.php');
include_once('../lib/common.lib.php');
include_once('../lib/sms.lib.php');


$oid = $member['mb_no']."-".microtime_float();

if (!$member['virtual_account2']) {
	die();
} else {
	/*
	if ($_SERVER['REMOTE_ADDR']=="220.117.134.129") {
	$kkp_res = Call_KakaoPay2();
	} else {
	$kkp_res = Call_KakaoPay();
	}
	*/
	$kkp_res = Call_KakaoPay2();

	$token = "";

	if ($kkp_res['next_send_ur']) {
		$tmp = explode("?",$kkp_res['next_send_ur']);
		$token = $tmp[1];
	}

	$sql = "insert into cf_kakao_remit set
				mb_no = '$member[mb_no]',
				mb_id = '$member[mb_id]',
				oid = '$oid',
				tid = '$kkp_res[tid]',
				insert_datetime = now()";
	sql_query($sql);
	echo json_encode($kkp_res);
}


?>

<?
function Call_KakaoPay() {

	global $member;
	global $oid;

	$kakao_pay_url = "https://money-api.kakao.com/ext/api/v1/transfer/partner";       // live

	$CID     = "MC0000D7FBL5CL6";
	$API_KEY = "aa2b94d273964fe9b452b1e48644257e";
	
	$kakao_params = array(
		'api_key' => $API_KEY,
		'cid' => $CID,
		'partner_order_id' => $oid,
		'description' => $member['mb_id'],
		'send_type' => 'BANK_ACCOUNT_SEND',
		'redirect_url' => 'https://hellofunding.co.kr/kakao_remit/check_input.php?oid='.$oid,
		'bank_code' => '088',
		'bank_account_number' => $member['virtual_account2']
	);

	//$strArrResult = request_curl($kakao_pay_url, http_build_query($kakao_params) );
	$strArrResult = request_curl($kakao_pay_url, $kakao_params );

	return $strArrResult;
}

function Call_KakaoPay2() {

	global $member;
	global $oid;

	$kakao_pay_url = "https://biz-dapi.kakaopay.com/money/v2/transfer/partner";       // live

	$CID     = "MC0000D7FBL5CL6";
	$API_KEY = "aa2b94d273964fe9b452b1e48644257e";
	
	$kakao_params = array(
		'api_key' => $API_KEY,
		'cid' => $CID,
		'partner_order_id' => $oid,
		'description' => $member['mb_id'],
		'send_type' => 'BANK_ACCOUNT_SEND',
		'redirect_url' => 'https://hellofunding.co.kr/kakao_remit/check_input.php?oid='.$oid,
		'bank_code' => '088',
		'bank_account_number' => $member['virtual_account2']
	);

	//$strArrResult = request_curl($kakao_pay_url, http_build_query($kakao_params) );
	$strArrResult = request_curl2($kakao_pay_url, $kakao_params );

	return $strArrResult;
}

function request_curl2($url,  $data=array()) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json;charset=utf-8','Authorization: PARTNER_KEY 1B44B4809FC1AA49CC6A72718F5277A9623C8A14'));

	$result[0] = curl_exec($ch);
	$result[1] = curl_errno($ch);
	$result[2] = curl_error($ch);
	$result[3] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);

	return json_decode($result[0], true);
	//return $result;
	
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

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec)*10000 ;
}
?>
