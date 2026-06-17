<?php

include_once('./_common.php');

session_unset();			// 모든 세션변수를 언레지스터 시켜줌
session_destroy();		// 세션해제함

// 자동로그인 해제 --------------------------------
set_cookie('ck_mb_id', '', 0);
set_cookie('ck_auto', '', 0);
// 자동로그인 해제 end --------------------------------

setcookie("pwdauth", "", time()-3600, "/", G5_COOKIE_DOMAIN, true, true);
setcookie("C20181018", "", 0, "/", G5_COOKIE_DOMAIN, true, true);

set_cookie('emergency_notice_view', '', -3600);
//setcookie("gongsiChk", "false", time()-3600, "/", G5_COOKIE_DOMAIN, true, true);

if($url) {

	$p = @parse_url($url);
	if ($p['scheme'] || $p['host']) {
		alert('url에 도메인을 지정할 수 없습니다.');
	}

	$link = ($url == 'shop') ? G5_SHOP_URL : $url;

}
else if ($bo_table) {

	$link = G5_BBS_URL.'/board.php?bo_table='.$bo_table;

}
else {

	$link = G5_URL;

}

goto_url($link);

?>