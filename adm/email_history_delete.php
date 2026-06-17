<?php

include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_demo();


// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}


if(!$idx || $idx <= 0) {
	alert('잘봇된 접근입니다.','./email_all_send.php');
	exit;
}else {

	$sql = "
			DELETE FROM g5_mailling_list WHERE idx = {$idx}
	";

	sql_query($sql);
}

?>