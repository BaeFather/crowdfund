<?php
include_once('./_common.php');


$g5['title'] = "투자설명회 신청서";
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
			<img src="../images/invest_request/top_img01_m.jpg" width="100%">
		</div>
		<div style="width:100%; margin:0 auto; background:url('/images/invest_request/center_bg01_m.jpg') repeat-y center top; background-size:100%;">
			<div style="text-align:center;padding:30px 0 20px 0;"><img src="../images/invest_request/img01_m.jpg" width="80%"></div>
			<ul style="width:85%;margin:0 auto;border-top:2px solid #0e2974;">
				<li style="padding:10px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:16px;color:#000;">업체명</span>
					<span style="padding-left:25px;"><input type="text" value="" style="width:63%;height:40px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span>
				</li>
				<li style="padding:10px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:16px;color:#000;">담당자명</span>
					<span style="padding-left:10px;"><input type="text" value="" style="width:63%;height:40px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span> 
				</li>
				<li style="padding:10px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:16px;color:#000;">연락처</span>
					<span style="padding-left:24px;">
						<input type="text" value="" style="width:21%;height:40px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
						<input type="text" value="" style="width:21%;height:40px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
						<input type="text" value="" style="width:21%;height:40px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
					</span> 
				</li>
			</ul>
			<div style="text-align:center;padding:20px 0;"><a href="#"><img src="../images/invest_request/btn01_m.jpg" width="50%"></a></div>
		</div>
		<div style="width:100%; margin:0 auto;">
			<img src="../images/invest_request/bottom_bg01_m.jpg" width="100%">
		</div>
		
	</div>
<? } else { ?>
  <div style="width:80%; padding:40px 10% 40px 10%;">

		<div style="width:773px; margin:0 auto;">
			<img src="../images/invest_request/top_img01.jpg" >
		</div>
		<div style="width:773px; margin:0 auto; background:url('/images/invest_request/center_bg01.jpg') repeat-y center top;">
			<div style="text-align:center;padding:40px 0 20px 0;"><img src="../images/invest_request/img01.jpg"></div>
			<ul style="width:600px;margin:0 auto;border-top:2px solid #0e2974;">
				<li style="padding:15px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:18px;color:#000;">업체명</span>
					<span style="padding-left:50px;"><input type="text" value="" style="width:253px;height:45px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span>
				</li>
				<li style="padding:15px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:18px;color:#000;">담당자명</span>
					<span style="padding-left:33px;"><input type="text" value="" style="width:253px;height:45px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span> 
				</li>
				<li style="padding:15px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:18px;color:#000;">연락처</span>
					<span style="padding-left:48px;">
						<input type="text" value="" style="width:123px;height:45px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
						<input type="text" value="" style="width:123px;height:45px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;margin-left:10px;">
						<input type="text" value="" style="width:123px;height:45px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;margin-left:10px;">
					</span> 
				</li>
			</ul>
			<div style="text-align:center;padding:20px 0;"><a href="#"><img src="../images/invest_request/btn01.jpg" ></a></div>
		</div>
		<div style="width:773px; margin:0 auto;">
			<img src="../images/invest_request/bottom_bg01.jpg" >
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