
<link rel="stylesheet" href="/theme/blueman1/css/flexslider2_m.css" type="text/css" media="screen">

<div id="content">
	<div class="location_top">
		<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
		<div class="content">

			<div class="review_tit_r">
				<p style="width:100%;text-align:left">헬로펀딩 투자후기 <a href="/etc/epilogue.php"><img src="/images/review/btn04.png" height="30"></a></p>
				<p style="width:100%;text-align:left;font-size:13px;color:#5e5e5e;padding-top:10px;">헬로펀딩에 투자하신 회원님들께서 블로그,SNS,카페 등에 남긴 생생한 후기입니다.</p>
			</div>
			<div id="reviews_area_r">
				<div style="padding-top:20px;text-align:center;"><img src="../images/review/tit_img02.png" height="50"></div>
				<div class="slider">
				<div class="reviews_cont_r carousel" id="flexslider">
					<ul class="slides">

						<!--베스트후기 시작-->
<? for($i=0; $i<count($BEST); $i++) { ?>
						<li>
							<div style="width:100%;background-color:#fff;display:block;border-radius:10px;">
								<p style="padding:15px 0 0 15px;color:#4a6fe2;font-size:20px;font-family:'NGB';"><?=$LIST[$BEST[$i]]['title']?></p>
								<p style="padding:3px 0 0 15px;font-size:16px;font-family:'NGB';"><?=$LIST[$BEST[$i]]['writer']?></p>
								<p style="padding:8px 8px;"><img src="<?=$LIST[$BEST[$i]]['image']?>" width="100%"></p>
								<p style="padding:3px 0 10px 15px;line-height:20px;font-size:14px;"><?=$LIST[$BEST[$i]]['content']?></p>
							</div>
							<div style="padding-top:15px;text-align:center;height:35px;"><a href="<?=$LIST[$BEST[$i]]['url']?>" target="_blank"><img src="/images/review/btn05.png" height="35"></a></div>
						</li>
<? } ?>
						<!--베스트후기 끝-->
					</ul>

					<script defer src="/js/jquery.flexslider.js"></script>
					<script type="text/javascript">
					$(window).load(function() {
						$('#flexslider').flexslider({
						animation: "slide",
						animationLoop: false,
						itemWidth: 380,
						itemMargin: 40
						});
					});
					</script>
				</div>
				</div>
			</div>
			<!-- 투자후기 팝업 -->
			<div id="epilogue_popup">
				<div class="title">헬로펀딩 투자후기 <img src="/images/btn_close.gif" alt="close" class="close"></div>
				<div class="gap"></div>
				<div class="con" id="epilogue_con">
					<!-- 내용 -->
				</div>
			</div>

	   <!-- 투자후기 리스트-->
			<div class="review_list">
				<ul style="display:inline-block; width:99%; margin:10px auto 0;">
<?
for($i=0,$j=1; $i<count($LIST); $i++,$j++) {
	$left_margin = (($i%2)>0) ? 'margin-left:5px;' : '';
	$float = (($i%2)>0) ? 'right' : 'left';
	//$top_margin = 'margin-top:30px;';
?>
					<li style="float:<?=$float?>; width:48.5%; border:1px solid #EAEAEA; <?=$left_margin?>">
						<p style="padding:6px;color:#4a6fe2; height:40px; font-size:18px;font-family:'NGB';"><?=$LIST[$i]['title']?></p>
						<p style="padding:0 6px; margin:6px 0 10px; height:14px;overflow:hidden; font-size:12px;font-family:'NGB';"><span style="vertical-align:middle;display:inline-block;"><?=$LIST[$i]['writer']?></span></p>
						<p style="padding:4px 4px;"><img src="<?=$LIST[$i]['image']?>" style="width:100%"></p>
						<p style="padding:6px 6px; height:68px;line-height:18px;font-size:14px; overflow:hidden;"><?=$LIST[$i]['content']?></p>
						<p style="padding:10px auto 8px;width:100%;margin:10px 0 10px; text-align:center;"><a href="<?=$LIST[$i]['url']?>" target="_blank"><img src="/images/review/btn05.png" height="28"></a></p>
					</li>
<?
	if($j < count($LIST)) {
		if(($j%2)==0) {
			echo '				</ul>' . PHP_EOL;
			echo '				<ul style="display:inline-block; width:99%; margin:10px auto 0;">' . PHP_EOL;
		}
	}
	else {
		echo '				</ul>' . PHP_EOL;
	}

}
?>
			<!-- 투자후기 리스트 끝-->



				</ul>
			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
//질문 클릭시 내용 오픈
$(document).ready(function(){
	$('.review_list:eq(0)').show();
	$('.review_list dl').click(function(){
		$(this).css({background:'url(/images/bbs/arrow_down.gif) no-repeat right top'}).find('dd').slideToggle();
		//$(this).css({background:'url(/images/bbs/arrow_up.gif) no-repeat right top'}).find('dd').slideDown('fast');
		//$(this).siblings().css({background:'url(/images/bbs/arrow_down.gif) no-repeat right top'}).find('dd').slideUp('fast');
	});
});

// 레이어 온 (투자후기)
$('#review1, #review2, #review3').click(function() {
	var idx = $(this).attr('data-idx');
	$.ajax({
		url : "<?=G5_THEME_URL?>/ajax_invest_epilogue.php",
		type: "POST",
		data: {idx:idx},
		success: function(data){
			$('#epilogue_con').html(data);
			$.blockUI({
				message: $('#epilogue_popup'),
				<? if(G5_IS_MOBILE) { ?>
				css: { top:'10%',width:'98%',height:'80%',border:'1px solid #AAA',cursor:'default', left:'1%' }
				<? } else { ?>
				css: { top:'16%',width:'600px',height:'680px',border:'1px solid #AAA',cursor:'default' }
				<? } ?>
			});
		},
		error: function () {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
		}
	});
});
</script>

<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>