<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">', 0);

?>



<!-- 비주얼 -->
<!--<img src="<?=G5_THEME_URL?>/img2/bbs/sub_faq.jpg" width="100%" alt="FAQ 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." />-->

<!-- FAQ 시작 { -->
<div id="content">
	<div class="location"><span><a href="<?=G5_URL?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">도움말</b></div>

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
</div>

<!-- } FAQ 끝 -->
<script>
$(document).ready(function(){
	//faq 질문 클릭시 내용 오픈
	$('.FAQ dl').click(function(){
		$(this).css({background:'url(../images/bbs/arrow_up.gif) no-repeat right top'}).find('dd').slideDown('fast');
		$(this).siblings().css({background:'url(../images/bbs/arrow_down.gif) no-repeat right top'}).find('dd').slideUp('fast');
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