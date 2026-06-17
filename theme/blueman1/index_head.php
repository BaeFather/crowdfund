<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
	include_once(G5_THEME_MOBILE_PATH.'/head.php');
	return;
}

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');

add_javascript('<script src="'.G5_THEME_JS_URL.'/fancySelect.js"></script>', 10);

if(defined('_INDEX_')) { // index에서만 실행
	include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
}

?>

<!-- 상단 시작 { -->
<header role="navigation">
	<div class="header_menu">
		<h1><a href="<?=G5_URL?>"><img src="<?=G5_THEME_URL?>/img/logo.png" alt="HELLOFUNDING" /></a></h1>
<?
	if($_REQUEST['menu']=='test') {
		include_once(G5_THEME_PATH.'/head.menu_test.php');
	}
	else {
		include_once(G5_THEME_PATH.'/head.menu.php');
	}
?>
	</div>
</header>

<?
if($bo_table == 'notice') {
	$g5['top_bn'] = "/images/bbs/sub_notice.jpg";
	$g5['top_bn_alt'] = "공지사항 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";
}

if($fm_id > 0) {
	$g5['top_bn'] = "/images/bbs/sub_faq.jpg";
	$g5['top_bn_alt'] = "FAQ 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";
}


if($g5['top_bn'] && $g5['top_bn'] != "") {
	//페이지 상단 메인이미지 출력
	//echo "<img src='".$g5['top_bn']."' alt='".$g5['top_bn_alt']."' style='margin-top:20px;'/>\n";
}

if (!defined("_INDEX_")) {
	$now_url = $_SERVER["REQUEST_URI"];
	$navi_title = ($bo_table) ? $board['bo_subject'] : $g5['title'];
}
?>
