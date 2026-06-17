<?
include_once('./_common.php');

$g5['title'] = "올리고 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


?>

<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {text-align:center;}
	
	
@media (max-width:750px) {
#event img {width:100%;}
}	
	
</style>


<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

		<div id="event">
			<div class="aa"><img src="img/oligo_01.jpg"></div>
			<div class="aa"><a href="http://www.oligo.kr" target="_blank"><img src="img/oligo_02.jpg"></a></div>
			<div class="aa"><img src="img/oligo_03.jpg"></div>
		</div>



	</div>
</div>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>