<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
	include_once(G5_THEME_MOBILE_PATH.'/head.php');
	return;
}

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');

?>

<script>
$(document).ready(function(){
	Mainslider = $('.slider').bxSlider({
		mode:'fade',
		auto: true,
		pause: 3000,
		slideMargin: 0,
		controls:false,
		onSlideAfter: function(){
			// do mind-blowing JS stuff here
			Mainslider.startAuto();
		}
	});
});
</script>

<!-- 상단 시작 { -->
<div id="wrap">

	<div id="header">
    <div id="header_wrap">
			<?php
			if(defined('_INDEX_')) { // index에서만 실행
				include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
			}
			?>

			<h1 class="logo"><a href="<?=G5_URL?>"><img src="/images/logo.gif" alt="HELLO HUNDING" /></a></h1>

			<?php include_once(G5_THEME_PATH.'/head.menu.php'); ?>
    </div>
    </div>
<!-- } 상단 끝 -->

<?php
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
?>


<div id="container" style="<?php if(!defined("_INDEX_")){ ?>background-color:#FFFFFF;<?php } ?>">

<?php
if (!defined("_INDEX_")) {
	$now_url = $_SERVER["REQUEST_URI"];

	if($bo_table) {
		$navi_title = $board['bo_subject'];
	}else {
		$navi_title = $g5['title'];
	}

	//echo $navi_title;
}
?>
