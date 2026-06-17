<?

include_once("_common.php");

if( trim($_GET['response_idx']) ) {
	$url = "https://api.cash-cow.co.kr/event/hellofunding/receiver?response_idx=" . trim($_GET['response_idx']);
	msg_go("", $url);
}

?>