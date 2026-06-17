<?php
include_once('./_common.php');



$g5['title'] = $EVENT['title'];
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$event_idx = 4;

?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><span><a href="">헬로펀딩이 핀크(Finnq)와 함께합니다.</a></span></div>

<? if(G5_IS_MOBILE) { ?>
  <div style="width:100%; padding:10px 2% 10px 2%; ">
		<div style="width:100%; margin:0 auto; border:1px solid #e8e8e8;">
			 <img src="/images/event/mou_img01.jpg" width="100%">
		</div>
	</div>
<? } else { ?>
  <div style="width:80%; padding:10px 10% 10px 10%;">
		<div style="width:750px; margin:0 auto;">
			
			
			<img src="/images/event/mou_img01.jpg" width="100%">
			
		</div>
	</div>
<? } ?>

</div>



<!-- 본문내용 E N D -->
<?php

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>