<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
?>

<header id="hd">
  <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>
  <div class="to_content"><a href="#container">본문 바로가기</a></div>
<?php
	if(defined('_INDEX_')) { // index에서만 실행
		include G5_MOBILE_PATH.'/newwin.inc.php'; // 팝업레이어
	}
?>

  <div id="hd_wrapper" style="position:fixed; width:100%; top:0; z-index:200; background:rgba(61,62,154,0.7);">
		<!--메뉴-->
		<?php include_once(G5_THEME_MOBILE_PATH.'/head.menu.php'); ?>
		<div id="logo"><img src="<?=G5_THEME_URL?>/img2/logo.png" alt="로고" class="main_logo" /></div>

<? if($is_member) { ?>
		<div id="top_login" class="btn_logout">로그아웃</div>
<? } else { ?>
		<div id="top_login" class="btn_login">로그인</div>
<? } ?>

	</div>

  <script>
	$('.main_logo').click(function() {
		$(location).attr('href', '<?=G5_URL?>');
	});

	$('#top_login').click(function(){
		<? if($is_member) { ?>
		if(confirm('로그아웃 하시겠습니까?')) {
			$(location).attr('href', '<?=G5_URL?>/bbs/logout.php');
		}
		<? } else { ?>
		$(location).attr('href', '<?=G5_URL?>/bbs/login.php');
		<? } ?>
	});
	</script>

</header>

<hr>

<div id="wrapper">
  <div id="container">
