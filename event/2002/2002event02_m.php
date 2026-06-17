<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {width:100%;text-align:center;}
</style>


<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

		<div id="event">
			<div class="aa"><img src="img/2event_m_01.jpg" style="width:100%"></div>
			<div class="aa"><img src="img/2event_m_02.jpg" style="width:100%"></div>
			<div class="aa"><a href="<?=$join_link2;?>" onFocus="blur();"><img src="img/2event_m_03.jpg" style="width:100%"></a></div>
			<div class="aa"><img src="img/2event_m_04.jpg" style="width:100%"></div>
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