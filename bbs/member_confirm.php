<?php
include_once('./_common.php');

if ($is_guest) {
    alert('로그인 한 회원만 접근하실 수 있습니다.', G5_BBS_URL.'/login.php');
}

// 기 인증 회원은 바이패스 시킴
if($_COOKIE['pwdauth']) {
		header('Location:' . $url);
}

/*
if ($url)
    $urlencode = urlencode($url);
else
    $urlencode = urlencode($_SERVER[REQUEST_URI]);
*/

$g5['title'] = '회원 비밀번호 확인';

$url = clean_xss_tags($_GET['url']);

// url 체크
check_url_host($url);

$url = get_text($url);


include_once(G5_PATH.'/head.php');

include_once($member_skin_path.'/member_confirm.skin.php');

include_once(G5_PATH.'/tail.php');

?>
