<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">', 0);
?>

<!-- FAQ 시작 { -->
<!-- 비주얼 -->
<?php if(false) { ?>
        <!--<img src="<?php echo G5_THEME_URL;?>/img2/bbs/sub_faq.jpg" width="100%" alt="FAQ 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." />//-->
<?php } ?>



<style>
	#content {background-image: none; width:100%; margin:0 auto;}
	#content .top_title {font-size:24px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 20px 0 10px; background-color: #fff; text-align: center;}
	#content .top_title .sky {color:#33a5ed;}
	#content .top_text {font-size:14px; color:#777; font-family:'SpoqaHanSans','sanserif'; text-align: center;}
	#content .top_text .del {display:none;}
	#content .tab_type02 {padding:5px 0 20px 0; display: flex;}

</style>

<!-- FAQ 시작 { -->
<div id="content">
	<!--div class="location"><span><a href="<?php echo G5_URL;?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">도움말</b></div-->
	<div>
		<h2 class="top_title">헬로펀딩 도움말</h2>
		<p class="top_text"><span class="del">헬로펀딩이나</span>투자에 대해 궁금한 사항들을 확인할 수 있습니다.<br class="br"></p>
	</div>
	<div class="content">

		<ul class="tab_type02">
		<?php
            foreach( $faq_master_list as $v ){
                $category_msg = '';
        ?>
            <li <?php if($fm_id==$v['fm_id']){ ?>class="on"<?php } ?> onclick="javascript:location.href='<?php echo $category_href;?>?fm_id=<?php echo $v['fm_id'];?>';">
                <?php echo $category_msg.$v['fm_subject'];?>
            </li>
        <?php } ?>
        </ul>

		<!-- 투자 -->
		<div class="FAQ">
            <?php
            foreach($faq_list as $key=>$v){
                if(empty($v))
                    continue;
            ?>
			<dl>
				<dt class="title"><?php echo conv_content($v['fa_subject'], 1); ?></dt>
				<dd class="text"><?php echo conv_content($v['fa_content'], 1); ?></dd>
			</dl>
            <?php } ?>
        </div>

	</div>

	<?php echo get_paging($page_rows, $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page='); ?>
</div>
<!-- } FAQ 끝 -->

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	//faq 질문 클릭시 내용 오픈
	var cnt = 0;
	$('.FAQ dl').click(function(){
        var title = $(this);
        var content = $(this).find("dd.text");
		$(".text").click(function() {
			cnt = 1;
		});
		$(".title").click(function() {
			cnt = 0;
		});

		if(cnt == 0)
		{
        content.slideToggle("fast", function(e){
            if ($(this).is(':visible')){
                title.css({background:'url("../../../theme/2018/img/bbs/arrow_up.gif") no-repeat right 1px', 'background-size': '25px auto'});
            }else{
                title.css({background:'url("../../../theme/2018/img/bbs/arrow_down.gif") no-repeat right 1px', 'background-size': '25px auto'});
                $(this).viewimageresize2();

            }
        return false;
		});
		}
	});

});
</script>