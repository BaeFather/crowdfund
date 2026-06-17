<?
if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(G5_IS_MOBILE) {
	if(defined('G5_THEME_PATH')) {
		include_once(G5_THEME_MOBILE_PATH.'/head.php');
		return;
	}
}
else {
	if(defined('G5_THEME_PATH')) {
		include_once(G5_THEME_PATH.'/head.php');
		return;
	}
}

