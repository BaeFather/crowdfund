<?

$base_path = "/home/crowdfund/public_html";

//상용 계정
$USEB['token_email']  = "arpino123@hellofunding.co.kr";
$USEB['token_passwd'] = "hellofunding";
$USEB['port']         = "443";

// https://api3.useb.co.kr/keys/get-client-secret 를 통해 발급받은 client_id, client_secret
$USEB['client_id']     = "c3d95bf9a1ed085e54d5c66609465b46";
$USEB['client_secret'] = "f8a5c15f5cea9b23d44b14c79a28615b";

// https://api3.useb.co.kr/keys/generate-key-pair 를 통해 발급받은 RSA2048 공개키+개인키
$USEB['pbkey'] = $base_path . "/useb/pbkey";		// 공개키
$USEB['pvkey'] = $base_path . "/useb/pvkey";		// 개인키

include_once($base_path . '/common.php');

?>