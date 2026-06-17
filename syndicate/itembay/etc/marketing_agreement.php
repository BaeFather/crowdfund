<?php
include_once('./_common.php');

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
	include_once(HF_PATH.'/hf_head.php');

?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><span></span><b class="blue">헬로펀딩 마케팅 정보 선택적 수집·이용 동의서</b></div>
	<div class="content">
		<div style="width:100%; min-height:350px; padding:30px 1% 0 1%;margin:0 auto;text-align:left;font-size:14px;font-family:<?=(G5_IS_MOBILE)?'Spoqa Han Sans':'spoqahansans'?>;font-weight:400;color:#212121;">
				<p style="color:#000;font-size:18px;">[마케팅 정보 선택적 수집·이용 동의서]</p><br/>


				귀하는 개인(신용)정보의 선택적인 수집∙이용, 제공에 대한 동의를 거부할 수 있습니다.<br>
				다만, 동의하지 않을 경우 관련 편의제공(투자상품 출시, 이벤트, 경품지급 등)안내 등에 제한이 있을 수 있으며 그 밖에 불이익은 없습니다. 또한 동의 후 에도 동의를 철회하실 수 있습니다.<br/><br/>

				<p style="color:#000;font-size:15px;font-weight:400;font-family:<?=(G5_IS_MOBILE)?'Spoqa Han Sans':'spoqahansans'?>;">가. 마케팅 정보 선택적 수집·이용 목적</p>
				편의제공, 마케팅 활동, 시장조사 등을 목적으로 수집·이용 합니다.<br/><br/>

				<p style="color:#000;font-size:15px;font-weight:400;font-family:<?=(G5_IS_MOBILE)?'Spoqa Han Sans':'spoqahansans'?>;">나. 수집·이용할 마케팅 정보의 항목<br/></p>
				- 개인식별정보: 휴대전화번호, e-mail 등<br/><br/>

				<p style="color:#000;font-size:15px;font-weight:400;font-family:<?=(G5_IS_MOBILE)?'Spoqa Han Sans':'spoqahansans'?>;">다. 수집이용하는 마케팅 정보의 보유 및 이용기간 : 동의일로부터 회원 탈퇴 혹은 마케팅 동의 해제시 까지 보유·이용합니다.<br/></p>
				<br/><br/>

		</div>
	</div>
</div>



<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
	include_once(HF_PATH.'/_tail.php');
?>