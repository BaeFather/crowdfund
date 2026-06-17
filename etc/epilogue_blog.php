<?
include_once('./_common.php');
$inc_head = ($co['co_include_head']) ? $co['co_include_head'] : './_head.php';
include_once($inc_head);


$g5['title'] = '투자후기';
//$g5['top_bn'] = "/images/investment/sub_investment.jpg";
$g5['top_bn_alt'] = "투자후기";


include_once("epilogue_blog_inc.php");


//모바일용 출력페이지
if(G5_IS_MOBILE){
	include_once("epilogue_blog_m.php");
	return;
}

?>
<!-- 본문내용 START -->
<link rel="stylesheet" href="/theme/blueman1/css/flexslider2.css" type="text/css" media="screen">

<div id="content">
	<div class="location_top">
		<div class="location"><span><a href="">이용안내</a></span><b class="blue"><?=$g5['title']?></b></div>
		<div class="content">

			<div class="review_tit_r">
				<p style="width:100%;text-align:left">헬로펀딩 투자후기 <span><a href="/etc/epilogue.php"><img src="/images/review/btn01.jpg" style="cursor:pointer"></a></span></p>
				<p style="width:100%;text-align:left;font-size:15px;color:#5e5e5e;padding-top:10px;">헬로펀딩에 투자하신 회원님들께서 블로그,SNS,카페 등에 남긴 생생한 후기입니다.</p>
			</div>
			<div id="reviews_area_r">
				<div style="margin-top:30px;text-align:center;"><img src="/images/review/tit_img01.png"></div>
				<div class="slider">
				<div class="reviews_cont_r carousel" id="flexslider">
					<ul class="slides">

						<!--베스트후기 시작-->
<? for($i=0; $i<count($BEST); $i++) { ?>
						<li>
							<div style="height:10px;"><img src="/images/review/top_bg01.jpg"></div>
							<div style="width:100%;background-color:#fff;display:block;padding:8px 0 10px 0;">
								<p style="padding-left:15px;color:#4a6fe2;font-size:20px;font-family:'NGB';"><?=$LIST[$BEST[$i]]['title']?></p>
								<p style="padding:3px 0 0 15px;font-size:16px;font-family:'NGB';"><?=$LIST[$BEST[$i]]['writer']?></p>
							</div>
							<div style="width:100%;text-align:center;background-color:#fff;display:block;"><img src="<?=$LIST[$BEST[$i]]['image']?>"></div>
							<div style="width:100%;background-color:#fff;display:block;padding:15px 0 8px 0;">
								<p style="padding:0 0 0 15px;line-height:20px;font-size:14px;"><?=$LIST[$BEST[$i]]['content']?></p>
							</div>
							<div><img src="/images/review/bottom_bg01.jpg"></div>
							<div style="padding-top:10px;text-align:center;height:33px;"><a href="<?=$LIST[$BEST[$i]]['url']?>" target="_blank"><img src="/images/review/btn02.jpg"></a></div>
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
						itemWidth: 332,
						itemMargin: 35
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
			<ul style="display:inline-block; width:1050px; margin:30px 50px 0;">
<?
for($i=0,$j=1; $i<count($LIST); $i++,$j++) {
	$left_margin = (($i%3)>0) ? 'margin-left:35px;' : '';
	//$top_margin = 'margin-top:30px;';
?>
				<li style="float:left;border:1px solid #eaeaea;<?=$left_margin?>">
					<p style="padding:15px 0 0 15px;color:#4a6fe2;font-size:20px;font-family:'NGB';"><?=$LIST[$i]['title']?></p>
					<p style="padding:5px 0 5px 15px;"><span style="vertical-align:middle;display:inline-block;font-size:14px;font-family:'NGB';"><?=$LIST[$i]['writer']?></span></p>
					<p style="padding:5px 5px; text-align:cetner;"><img src="<?=$LIST[$i]['image']?>" style="width:313px;height:203px"></p>
					<p style="padding:5px 15px 10px; width:294px; line-height:20px;font-size:14px"><?=$LIST[$i]['content']?></p>
					<p style="padding:0 0 15px 15px;font-size:16px;font-family:'NGB';"><span style="width:100%;text-align:center;display:inline-block;"><a href="<?=$LIST[$i]['url']?>" target="_blank"><img src="/images/review/btn03.jpg"></a></span></p>
				</li>
<?
	if($j < count($LIST)) {
		if(($j%3)==0) {
			echo '			</ul>' . PHP_EOL;
			echo '			<ul style="display:inline-block; width:1050px; margin:30px 50px 0;">' . PHP_EOL;
		}
	}
	else {
		echo '			</ul>' . PHP_EOL;
	}

}
?>
			<!-- 투자후기 리스트 끝-->

		</div>
	</div>
</div>

<!-- 본문내용 E N D -->

<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');

?>