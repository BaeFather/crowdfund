<?php
include_once('./_common.php');


$g5['title'] = 'ARS 인증 테스트';

$g5['top_bn'] = "/images/bbs/sub_cooperate.jpg";
$g5['top_bn_alt'] = "제휴문의 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

?>

<!-- 본문내용 START -->
<div id="content">
	<div class="location"><b class="blue">ARS 테스트</b></div>

	<div class="content">
	

<?php

require_once "./seed128/Seed128Cipher.php";

$text_Key = 'YWtvcmVhYWNzc3Zj';

function ars_auth_proc() {

	$message .= PHP_EOL.$url;

	$headers = array('Content-Type:application/x-www-urlencoding');
	$to_url = '61.100.189.101:4001?id='.$ibizplus_id;
	$to_url .= '&pwd='.$ibizplus_pw;
	$to_url .= '&from='.$from_phone;
	$to_url .= '&to_country=82&to='.$to_phone;
	$to_url .= '&message='.urlencode($message).'&report_req=0'; 
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $to_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arr));
	$response = curl_exec($ch);
	curl_close($ch);

	echo $response;

}


$text_Data = "hello!!";

$seed = new Seed128Cipher();

$encryptText = $seed->base64_encrypt($text_Data, $text_Key, 'UTF-8');
echo "Encrypt Text \t:: " . $encryptText . "\n";

$decryptText = $seed->base64_decrypt($encryptText, $text_Key);
echo "Decrypt Text \t:: " . $decryptText . "\n";



?>

	
	</div>
</div>
<!-- 본문내용 E N D -->

<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>
