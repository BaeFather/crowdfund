<?
$sub_menu = "500800";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') {
	alert('최고관리자만 접근 가능합니다.');
}

if (!$token) die("토큰값 없음");

$chk_kakao = check_kakao_remit($token);
//print_r($chk_kakao);
//echo "<br/>------------------------------<br/>";
?>
<link rel="stylesheet" type="text/css" href="/adm/css/bootstrap.min.css">

<br/><br/><br/>
<center>
<table class="table table-striped table-bordered table-hover table-condensed" style="font-size:14px; width:95%;">
	<tr>
		<td>요청결과</td>
		<td><?=$chk_kakao["status"]?> (<?=($chk_kakao["status"]=="200")?"성공":"ERROR"?>)</td>
	</tr>
	<tr>
		<td>헬로펀딩 거래번호</td>
		<td><?=$chk_kakao["tid"]?></td>
	</tr>
	<tr>
		<td>카카오 거래번호</td>
		<td><?=$chk_kakao["partner_order_id"]?></td>
	</tr>
	<tr>
		<td>송금액</td>
		<td><?=number_format($chk_kakao["sent_amount"])?> 원</td>
	</tr>
	<tr>
		<td>송금완료시각</td>
		<td><?=$chk_kakao["approved_at"]?></td>
	</tr>
	<tr>
		<td>송금 결과</td>
		<td><?=$chk_kakao["send_status"]?></td>
	</tr>
	<tr>
		<td>실패 사유</td>
		<td><?=$chk_kakao["error_message"]?></td>
	</tr>
</table>



<?
function check_kakao_remit($token) {

	//$kakao_check_url = "https://money-api.kakao.com/ext/api/v1/transfer/partner/confirm";
	//$kakao_check_url = "https://biz-dapi.kakaopay.com/money/v2/transfer/partner/confirm";       // live
	//$CID     = "MC0000D7FBL5CL6";
	//$API_KEY = "aa2b94d273964fe9b452b1e48644257e";

	$kakao_check_url = "https://moneyapi.dozn.co.kr/api/money/transfer/confirm";       // live
	$CID     = "ID000002";
	$API_KEY = "0754dd3b-5af7-4b00-96c1-14c3600fd377";

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
	//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode(json_encode($data , JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)));
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json;charset=utf-8'));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json;charset=utf-8','Authorization: PARTNER_KEY 1B44B4809FC1AA49CC6A72718F5277A9623C8A14'));

	$result[0] = curl_exec($ch);
	$result[1] = curl_errno($ch);
	$result[2] = curl_error($ch);
	$result[3] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);
//echo "<pre>";print_r($result); echo "</pre>";
	return json_decode($result[0], true);
	//return $result;
	
}
?>