<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');


$mb_id = $_SESSION['ss_mb_id'];


$query = " SELECT * FROM g5_member WHERE mb_id = '".$mb_id."' ";

$row = sql_fetch($query);

if($row['mb_id']){
	echo($row['mb_id'].'*:'.$row['member_type'].'*:'.$row['mb_no']);
}else{
	echo('x');	
}

?>