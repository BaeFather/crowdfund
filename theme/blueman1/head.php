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

//echo "head.php";

?>

<header role="navigation">
	<h1><a href="/"><img src="<?=G5_THEME_URL?>/img/logo.jpg" alt="HELLOHUNDING" /></a></h1>
<?
	if($_REQUEST['menu']=='test') {
		include_once(G5_THEME_PATH.'/head.menu_test.php');
	}
	else {
		include_once(G5_THEME_PATH.'/head.menu.php');
	}
?>
</header>
<!-- } 상단 끝 -->

<hr>

<!-- 콘텐츠 시작 { -->
<div id="wrapper">
	<div id="sub_tit">
		<h2 id="ctn_title"><? echo get_text((isset($bo_table) && $bo_table) ? $board['bo_subject'] : $g5['title']); ?></h2>
	</div>
  <div id="container">
    <? if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?=$g5['title']?></div><? } ?>
