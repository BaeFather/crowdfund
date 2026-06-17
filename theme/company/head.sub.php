<?
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$begin_time = get_microtime();

if (!isset($g5['title'])) {
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
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<?
if (G5_IS_MOBILE) {
	echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
	echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
	echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
	echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">'.PHP_EOL;
}


if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;
?>
<meta name="title" content="헬로펀딩 | 대한민국 P2P금융의 표준">
<meta name="subject" content="헬로펀딩 | 대한민국 P2P금융의 표준">
<meta name="description" content="투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼. 새로운 투자채널. 합리적인 대출금리.">
<meta name="keywords" content="헬로펀딩,크라우드펀딩,부동산크라우드펀딩,동산크라우드펀딩,P2P투자,P2P대출">
<meta name="writer" content="(주)헬로핀테크">
<meta name="author" content="(주)헬로핀테크">
<meta name="copyright" content="(주)헬로핀테크">
<meta name="robots" content="ALL">
<meta name="naver-site-verification" content="115879cf0ae96194f27ce1b9ea0db9db7d0dc955"/>
<meta property="og:type" content="website">
<meta property="og:title" content="헬로펀딩 | 대한민국 P2P금융의 표준">
<meta property="og:description" content="헬로펀딩은 투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼으로 투자자에게는 새로운 투자채널을, 대출자에게는 합리적인 대출금리를 제공합니다.">
<meta property="og:image" content="<?=G5_URL?>/images/CI64.png">
<meta property="og:url" content="<?=G5_URL?>">
<title>헬로펀딩, 대한민국 P2P 금융의 표준, P2P투자, P2P대출, 소액투자의 시작 헬로펀딩</title>
<link rel="canonical" href="<?=G5_URL.$_SERVER['REQUEST_URI']?>">
<link rel="shortcut icon" type="image/x-icon" href="/favicon.png">


<?if( G5_IS_MOBILE ){ ?>
	<link rel="stylesheet" type="text/css" href="<?echo G5_THEME_CSS_URL; ?>/mobile.css">
	<!-- hellofunding 전용 START -->
	<link rel="stylesheet" type="text/css" href="<?echo G5_THEME_CSS_URL; ?>/layout_mobile.css">
	<!-- hellofunding 전용 E N D -->
<?}else{ ?>
	<link rel="stylesheet" type="text/css" href="<?echo G5_THEME_CSS_URL; ?>/default.css">
	<!-- hellofunding 전용 START -->
	<link rel="stylesheet" type="text/css" href="<?echo G5_THEME_CSS_URL; ?>/layout.css">
	<!-- hellofunding 전용 E N D -->
<? } ?>

<script type="text/javascript" src="<?=G5_JS_URL?>/jquery-1.9.1.min.js"></script>

<!--[if lte IE 8]>
<script src="<?echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "<?=G5_URL?>";
var g5_bbs_url   = "<?=G5_BBS_URL?>";
var g5_is_member = "<?=isset($is_member)?$is_member:''; ?>";
var g5_is_admin  = "<?=isset($is_admin)?$is_admin:''; ?>";
var g5_is_mobile = "<?=G5_IS_MOBILE?>";
var g5_bo_table  = "<?=isset($bo_table)?$bo_table:''; ?>";
var g5_sca       = "<?=isset($sca)?$sca:''; ?>";
var g5_editor    = "<?=($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var g5_cookie_domain = "<?=G5_COOKIE_DOMAIN?>";
<? if ($is_admin) { ?>
var g5_admin_url = "<?=G5_ADMIN_URL?>";
<? } ?>
</script>

<? if(!G5_IS_MOBILE){ ?>
<script>
$(window).scroll(function(){
		if($(window).scrollTop() > $('#header').offset().top){
			$("#header_wrap").css('position','fixed');
	} else {
			$("#header_wrap").css('position','relative');
	}
});
</script>
<? } ?>

<script type="text/javascript" src="<?=G5_JS_URL?>/jquery.bxslider.js"></script>
<script type="text/javascript" src="<?=G5_JS_URL?>/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?=G5_JS_URL?>/jquery.menu.js"></script>
<script type="text/javascript" src="<?=G5_JS_URL?>/common.js"></script>
<script type="text/javascript" src="<?=G5_JS_URL?>/wrest.js"></script>
<?
if(G5_IS_MOBILE) {
    echo '<script type="text/javascript" src="'.G5_JS_URL.'/modernizr.custom.70111.js"></script>'.PHP_EOL; // overflow scroll 감지
}
?>
</head>
<body class="body">
<?
/*
if ($is_member) { // 회원이라면 로그인 중이라는 메세지를 출력해준다.
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
    else if ($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
    else if ($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";

    echo '<div id="hd_login_msg">'.$sr_admin_msg.get_text($member['mb_nick']).'님 로그인 중 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
}
*/
?>