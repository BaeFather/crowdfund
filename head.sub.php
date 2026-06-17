<?
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 테마 head.sub.php 파일
if(!defined('G5_IS_ADMIN') && defined('G5_THEME_PATH') && is_file(G5_THEME_PATH.'/head.sub.php')) {
	require_once(G5_THEME_PATH.'/head.sub.php');
	return;
}

$begin_time = get_microtime();

if(!isset($g5['title'])) {
	$g5['title'] = $config['cf_title'];
	$g5_head_title = $g5['title'];
}
else {
	$g5_head_title = $g5['title']; // 상태바에 표시될 제목
	$g5_head_title .= " | ".$config['cf_title'];
}

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if(!$g5['lo_location'])
	$g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if(strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<?
if(G5_IS_MOBILE) {
	echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">'.PHP_EOL;
	echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
	echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
	echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
	echo '<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">'.PHP_EOL;
}

if($config['cf_add_meta'])
	echo $config['cf_add_meta'].PHP_EOL;
?>
<title><? echo $g5_head_title; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?ver=<?=date('ymd')?>">
<?
if(defined('G5_IS_ADMIN')) {
	if(!defined('_THEME_PREVIEW_')) {
		echo '<link rel="stylesheet" type="text/css" href="/adm/css/admin.css">'.PHP_EOL;
	}
}
else {
	echo '<link rel="stylesheet" type="text/css" href="'.G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile':'default').'.css">'.PHP_EOL;
}

if(defined('G5_IS_ADMIN')) {
	echo '<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">'.PHP_EOL;
	echo '<link rel="stylesheet" type="text/css" href="/adm/css/bootstrap.min.css">'.PHP_EOL;
}
?>
<!--[if lte IE 8]>
<script src="<?=G5_JS_URL?>/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<? if(defined('G5_IS_ADMIN')) { echo '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>'.PHP_EOL; } ?>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js?v=20200619"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<?
if(!defined('G5_IS_ADMIN')) { echo $config['cf_add_script'] ."\n"; }
if(G5_IS_MOBILE) { echo '<script type="text/javascript" src="/js/modernizr.custom.70111.js"></script>'.PHP_EOL; /* overflow scroll 감지 */ }
if(defined('G5_IS_ADMIN')) {
	echo '<script type="text/javascript" src="/adm/js/jquery.form.js"></script>'.PHP_EOL;
	echo '<script type="text/javascript" src="/js/jquery.blockUI.js"></script>'.PHP_EOL;
}
?>
<script type="text/javascript" src="/common_variables.js"></script>
<script type="text/javascript" src="/js/jquery.floatThead.js"></script>
</head>
<body<?=isset($g5['body_script']) ? $g5['body_script'] : ''; ?>>
<?
if($is_member) {
	// 회원이라면 로그인 중이라는 메세지를 출력해준다.
	$sr_admin_msg = '';
	if($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
	else if($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
	else if($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";

	//echo '<div id="hd_login_msg">'.$sr_admin_msg.get_text($member['mb_nick']).'님 로그인 중 ';
	//echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
}
?>