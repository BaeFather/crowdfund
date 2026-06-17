<?php
include_once('./_common.php');


$g5['title'] = "제18호 투자상품 찍고 영화예매권 받자!";
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
		  <img src="../images/investment/new_event_detail20170221_m.jpg" width="100%" >
		</div>
		
	</div>
<? } else { ?>
  <div style="width:80%; padding:40px 10% 40px 10%;">
		<div style="width:773px; margin:0 auto;">
			<img src="../images/investment/new_event_detail20170221.jpg" width="100%">
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