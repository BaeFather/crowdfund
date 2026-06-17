<?php
include_once('./_common.php');


$g5['title'] = "픽미픽미 헬로업";
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');


?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><b class="blue"><?=$g5['title']?></b></div>

	
	<? if(G5_IS_MOBILE) { ?>
  <div style="width:96%; padding:10px 2% 10px 2%;" >
		<div style="width:100%; margin:0 auto;">
		  <img src="../images/investment/recommend_img01_m.jpg" width="100%" >
		</div>
		<div style="position:relative; z-index:1; width:100%; margin:0 auto; padding:20px 0;  cursor:pointer;">
			<center><a href="https://hellofunding.co.kr/bbs/register_choice.php"><img src="../images/investment/btn01_m.jpg" width="55%"></a></center>
		</div>
	</div>
<? } else { ?>
  <div style="width:80%; padding:10px 10% 10px 10%;">
		<div style="width:773px; margin:0 auto;">
			<img src="../images/investment/recommend_img01.jpg" width="100%">
		</div>
		<div style="position:relative; z-index:1;  padding:20px 0; width:275px;height:49px; cursor:pointer; margin:0 auto; ">
			<a href="https://hellofunding.co.kr/bbs/register_choice.php"><img src="../images/investment/btn01.jpg" width="100%"></a>
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