<?php
include_once('./_common.php');

if(!$is_admin) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

$g5['title'] = '현재접속자';
include_once('./_head.php');

if(G5_IS_MOBILE) {
	include_once($connect_skin_path.'/current_connect.skin.php');
}
else {
	include_once($connect_skin_path.'/current_connect.skin_2.php');
}

include_once('./_tail.php');

?>