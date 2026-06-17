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
	<div class="location"><span><a href="">헬로펀딩 신규회원가입 이벤트</a></span></div>

<? if(G5_IS_MOBILE) { ?>
  <div style="width:96%; padding:10px 2% 10px 2%; ">
		<div style="width:100%; margin:0 auto;">
		   <a href="/bbs/register_choice.php"><img src="/images/event/join_event.jpg" width="100%"></a>
		</div>
	</div>
<? } else { ?>
  <div style="width:80%; padding:10px 10% 10px 10%;">
		<div style="width:750px; margin:0 auto;">
			<div style="display:block;position:absolute;width:275px;height:47px;margin-left:230px;margin-top:1186px;cursor:pointer;"onclick="location.href='/bbs/register_choice.php';">
			
			</div>
			<img src="/images/event/join_event.jpg" width="100%">
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