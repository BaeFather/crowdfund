<?
if( !in_array($_SERVER['REMOTE_ADDR'], array('183.98.101.114','183.98.101.115')) ) {
	header("http/1.0 404 not found");
}


include_once('_common.php');
//if(!$office_connect) { header('HTTP/1.0 404 Not Found'); exit; }

define('_INDEX_', true);
if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


if($_REQUEST['mode']=="debug") {
	setcookie("debug_mode", true, time()+3600*3, "/", G5_COOKIE_DOMAIN, true, true);
	echo "<script>location.href='/';</script>";
}

if($_REQUEST['mode']=="renewal") {
	setcookie("renewal_mode", true, 0, "/", G5_COOKIE_DOMAIN, true, true);
	echo "<script>location.href='/';</script>";
}


// 무조건 기본테마(PC용) 인덱스로 보냄
$_CONF['theme_path'] = G5_THEME_PATH;
//$_CONF['theme_path'] = (G5_IS_MOBILE) ? G5_THEME_MOBILE_PATH : G5_THEME_PATH;

//include_once('popup_open2.php');

include_once($_CONF['theme_path'] . '/index.test2.php');
return;


?>