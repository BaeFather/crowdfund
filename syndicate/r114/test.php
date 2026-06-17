<?php
include_once('./hf_common.php');

$gg_captcha_sskey = "6LeVVmcUAAAAALzX8kaOe1CyxhyT0Gmejwlx6H1R";

$mb_password = "1111";
$aa =  get_encrypt_string2($mb_password);

echo "ddd : ".$aa;
//phpinfo();
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
function Size2Parent() {
	var intWidth = 500;
	var intHeight = 100;
	//parent.postMessage(document.body.scrollHeight,'https://www.r114.com');
	parent.postMessage("{'height':"+ intHeight+",'width':"+intWidth+"}",'https://www.hellofunding.co.kr');
}

/* 상위프레임(와우스타측) 으로 본 페이지 사이즈 전송 */
Size2Parent();
</script>