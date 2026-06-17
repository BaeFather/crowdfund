<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {width:100%;text-align:center;}
</style>


<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

		<div id="event">
			<div class="aa"><img src="/images/event/2012event/12event_1_m.png" style="width:100%"></div>
			<div class="aa"><img src="/images/event/2012event/12event_2_m.png" style="width:100%"></div>
			<div class="aa"><a href="<?=$join_link;?>" onFocus="blur();"><img src="/images/event/2012event/12event_3_m.png" style="width:100%"></a></div>
			<div class="aa"><a href="<?=$join_link2;?>" onFocus="blur();"><img src="/images/event/2012event/12event_4_m.png" style="width:100%"></a></div>
			<div class="aa"><img src="/images/event/2012event/12event_5_m.png" style="width:100%"></div>
		</div>

	</div>
</div>
<!-- 본문내용 E N D -->

<?
if($co['co_include_tail']){
	@include_once($co['co_include_tail']);
} else {
	include_once('./_tail.php');
}
?>