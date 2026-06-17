<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">', 0);

?>

<style>
	#content {background-image: none; margin-bottom:100px;}
	#content .top_title {font-size:30px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 40px 0 10px; background-color: #fff;}
	#content .top_text {font-size:17px; color:#777; padding-bottom: 20px; font-family:'SpoqaHanSans','sanserif'}
	#content .content {margin:0 auto;}
	#content .tab_type02 {padding:30px 0 50px;}
</style>

<!-- FAQ 시작 { -->

<div id="content">
	<!--div class="location"><span><a href="<?=G5_URL?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">도움말</b></div-->
	<div>
		<h2 class="top_title">헬로펀딩 도움말</h2>
		<p class="top_text">헬로펀딩이나 투자에 대해 궁금한 사항들을 확인할 수 있습니다.<br class="br"></p>
	</div>
	
	<div class="content">

		<ul class="tab_type02">
<?php
foreach( $faq_master_list as $v ){
	$category_msg = '';
?>
      <li <?php if($fm_id==$v['fm_id']){ ?>class="on"<?php } ?> onclick="javascript:location.href='<?php echo $category_href;?>?fm_id=<?php echo $v['fm_id'];?>';"><?php echo $category_msg.$v['fm_subject'];?></li>
	 
<?php
}
?>
    </ul>

    <!-- 투자 -->
    <div class="FAQ" style="display:block;">
<?php
foreach($faq_list as $key=>$v){
	if(empty($v))
		continue;
?>
      <dl>
        <dt class="title"><?php echo conv_content($v['fa_subject'], 1); ?></dt>
        <dd class="text"><?php echo conv_content($v['fa_content'], 1); ?></dd>
      </dl>

<?php
}
?>
    </div>

  </div>

  <?php echo get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page='); ?>
	
	
	<div style="margin: 30px 0 100px;">
		<img src="../img/f&q_info.jpg" alt="헬로펀딩 상담안내" usemap="#Map">
        <map name="Map">
          <area shape="rect" coords="646,33,878,76" href="http://pf.kakao.com/_xgAdWu/chat" target="_blank">
        </map>
	</div>
	
	
	
</div>

<!-- } FAQ 끝 -->
<script>
$(document).ready(function(){
	// faq 질문 리스트 클릭 시 내용 보여주기
	$('.FAQ dt').on('click', function(){
		var $dl = $(this).parent();

		$dl.find('dd').slideToggle('fast');
		$dl.toggleClass('list-on');

		$dl.siblings().find('dd').slideUp('fast');
		$dl.siblings().removeClass('list-on');
	});

/*
	//탭 기능
	$('.FAQ:eq(0)').show();

	$('.tab_type02 li').click(function(){
		var cur = $(this).index();
		$(this).addClass('on').siblings().removeClass('on');
		$('.FAQ').hide();
		$('.FAQ:eq('+cur+')').show();
	});
*/

});
</script>