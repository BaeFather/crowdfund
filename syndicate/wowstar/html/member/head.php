<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


$begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title']   = $config['cf_title'];
    $g5_head_title = $g5['title'];
}
else {
    $g5_head_title = $g5['title']; // 상태바에 표시될 제목
    $g5_head_title.= " | ".$config['cf_title'];
}

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location']) { $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI'])); }
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if(strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

$conversion_request_uri = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<?
if (G5_IS_MOBILE) {
	echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
	echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
	echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
	echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">'.PHP_EOL;
}

if($config['cf_add_meta']) echo $config['cf_add_meta'].PHP_EOL;
?>
<meta name="title" content="헬로펀딩 | 대한민국 P2P금융의 표준">
<meta name="subject" content="헬로펀딩 | 대한민국 P2P금융의 표준">
<meta name="description" content="투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼. 새로운 투자채널. 합리적인 대출금리.">
<meta name="keywords" content="헬로펀딩,크라우드펀딩,부동산크라우드펀딩,동산크라우드펀딩,P2P투자,P2P대출">
<meta name="writer" content="(주)헬로핀테크">
<meta name="author" content="(주)헬로핀테크">
<meta name="copyright" content="(주)헬로핀테크">
<meta name="robots" content="ALL">
<meta name="naver-site-verification" content="115879cf0ae96194f27ce1b9ea0db9db7d0dc955">
<meta property="og:type" content="website">
<meta property="og:title" content="헬로펀딩 | 대한민국 P2P금융의 표준">
<meta property="og:description" content="헬로펀딩은 투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼으로 투자자에게는 새로운 투자채널을, 대출자에게는 합리적인 대출금리를 제공합니다.">
<meta property="og:image" content="<?=G5_URL?>/images/CI64.png">
<meta property="og:url" content="<?=G5_URL?>">
<!--
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
-->
<title>헬로펀딩, 대한민국 P2P 금융의 표준, P2P투자, P2P대출, 소액투자의 시작 헬로펀딩</title>

<? if( preg_match("/\/adm\//i", $_SERVER['PHP_SELF']) ) { ?>
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/default<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/frame_layout<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/main_layout<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<? } else if( preg_match("/\/product_detail/i", $_SERVER['PHP_SELF']) ) { ?>
<?		if($prd_idx <= 243) { ?>
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/default<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/main_layout<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<?		} else { ?>
<link rel="stylesheet" type="text/css" href="/theme/2018/css/<?=(G5_IS_MOBILE)?'mobile.css':'default.css'?>">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/default<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/2018/css/layout<?=(G5_IS_MOBILE)?'_m':''?>.css">
<?		} ?>
<? } ?>
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/popup<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="/theme/blueman1/css/flexslider<?=(G5_IS_MOBILE)?'_mobile':''?>.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

<!--[if lte IE 9]>
<script type="text/javascript" src="<?=G5_THEME_JS_URL?>/html5shiv.js"></script>
<![endif]-->
<!--[if lte IE 9]>
<div class="browse_happy">
  오래된 버전의 인터넷 브라우저를 사용하고 계십니다. 정상적인 이용을 위해
  <a href="http://browsehappy.com/" target="_blank">브라우저를 최신 버전으로 업데이트</a>
  해주시기 바랍니다.
</div>
<![endif]-->

<script type="text/javascript">
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
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/jquery.bxslider.min.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<script type="text/javascript" src="/js/modernizr.custom.70111.js"></script>
</head>

<body class="loading">

<style>
#content { background:url(''); }
</style>

<!-- 콘텐츠 시작 { -->
<div id="wrapper">
  <div id="container">
