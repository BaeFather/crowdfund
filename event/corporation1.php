<?php
include_once('./_common.php');


$g5['title'] = "헬로법인설립센터";
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
		<div style="width:100%; margin:0 auto; text-align:center;">
		 <p><img src="../images/investment/corporation1_m.jpg" width="100%"></p>
		 <p><img src="../images/investment/bottom_bg02.jpg" width="100%"></p>
			<p style="background:url('../images/investment/bottom_bg03.jpg') no-repeat; background-size:100%;height:180px;text-align:center;">
				<ul style="position:absolute;display:inline-block;font-size:14px;margin-top:-120px;left:10%;right:10%;">
					<li style="float:left;padding-right:5px;">
						
						<input type="text" value="2017" style="width:45px;height:30px;font-size:14px;text-align:center;border-radius:3px;border:1px solid #4b71be;"/>
						년
					</li>
					<li  style="float:left;padding-right:5px;">
						<select name="month" style="height:32px;font-size:14px;border-radius:3px;border:1px solid #4b71be;">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						</select>
						월
					</li>
					<li  style="float:left;padding-right:5px;">
						<select name="day" style="height:32px;font-size:14px;border-radius:3px;border:1px solid #4b71be;">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
						<option value="31">31</option>
						</select>
						일
					</li>
					<li  style="float:left;">
						<select name="year" style="height:32px;font-size:14px;border-radius:3px;border:1px solid #4b71be;">
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						</select>
						시
					</li>
					<a href="" style="margin:41px 0 0 30px;width:206px;height:40px;display:block;"></a>
				</ul>	
			</p>
		</div>
		
	</div>
<? } else { ?>
  <div style="width:80%; padding:10px 10% 10px 10%;">
		<div style="width:773px; margin:0 auto;">
			<p><img src="../images/investment/corporation1.jpg" width="100%"></p>
			<p style="background:url('../images/investment/bottom_bg01.jpg') no-repeat; width:100%; height:247px;">
				<ul style="position:relative;top:-195px;left:405px;display:inline-block;font-size:16px;">
					<li style="float:left;padding-right:5px;">
						<input type="text" value="2017" style="width:45px;height:30px;font-size:14px;text-align:center;border-radius:3px;border:1px solid #4b71be;"/>
						년
					</li>
					<li  style="float:left;padding-right:5px;">
						<select name="month" style="height:32px;font-size:16px;border-radius:3px;border:1px solid #4b71be;">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						</select>
						월
					</li>
					<li  style="float:left;padding-right:5px;">
						<select name="day" style="height:32px;font-size:16px;border-radius:3px;border:1px solid #4b71be;">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
						<option value="31">31</option>
						</select>
						일
					</li>
					<li  style="float:left;">
						<select name="year" style="height:32px;font-size:16px;border-radius:3px;border:1px solid #4b71be;">
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						</select>
						시
					</li>
					<a href="" style="margin:41px 0 0 30px;width:206px;height:40px;display:block;"></a>
				</ul>	
			</p>
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