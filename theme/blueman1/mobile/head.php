<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');

if(defined('_INDEX_')) { // index에서만 실행
	include G5_MOBILE_PATH.'/newwin.inc.php'; // 팝업레이어
}

?>

<header_blue1>
	<div id="header_nav">
		<div class="header" id="aside_btn"><span class="btn_menu"><img src="<?=G5_THEME_URL?>/img/mobile/navi_icon.png" alt="메뉴버튼" /></span></div>
		<h1 class="logo"><img id="main_logo" src="<?=G5_THEME_URL?>/img/mobile/logo.png" height="26" alt="HELLOFUNDING" /></h1>
<? if($is_member) { ?>
		<div class="logout"><img id="top_login" src="<?=G5_THEME_URL?>/img/mobile/logout_btn.png" alt="로그아웃" /></div>
<? } else { ?>
		<div class="login"><img id="top_login" src="<?=G5_THEME_URL?>/img/mobile/login_btn.png" alt="로그인" /></div>
<? } ?>
	</div>
	<div id="head_top" style="display:none;"></div>
</header_blue1>

<? include_once(G5_THEME_MOBILE_PATH.'/head.menu.php'); ?>

<script>
$('#main_logo').click(function() {
	$(location).attr('href', '/');
});

$('#top_login, #left_login').click(function(){
	<? if($is_member) { ?>
	if(confirm('로그아웃 하시겠습니까?')) {
		$(location).attr('href', '<?=G5_URL?>/bbs/logout.php');
	}
	<? } else { ?>
	$(location).attr('href', '<?=G5_URL?>/bbs/login.php');
	<? } ?>
});
</script>


