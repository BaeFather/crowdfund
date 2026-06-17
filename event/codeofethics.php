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
	<div class="location"><span></span><b class="blue">헬로핀테크 윤리강령</b></div>

<? if(G5_IS_MOBILE) { ?>
  <div style="width:96%; padding:10px 2% 10px 2%; ">
		<div style="width:100%; margin:0 auto; border:1px solid #e8e8e8;">
			<p><img src="/images/event/notice180105_m.jpg" width="100%"></p>
			<p>
				<img src="/images/event/btn001_m.jpg" width="100%" style="cursor:pointer;" onclick="window.open('/images/event/file1.jpg')">
			</p>
			<p style="padding-top:15px;">
				<img src="/images/event/btn002_m.jpg" width="100%" style="cursor:pointer;" onclick="window.open('/images/event/file2.jpg')">
			</p>
			<p style="padding-top:15px;">
				<img src="/images/event/btn003_m.jpg" width="100%" style="cursor:pointer;" onclick="window.open('/images/event/file3.jpg')">
			</p>
		   <p><img src="/images/event/notice180105_m_1.jpg" width="100%"></p>
		</div>
	</div>
<? } else { ?>
  <div style="width:1150px; padding:30px 10% 0 10%;margin:0 auto;text-align:left;font-size:14px;font-family:'spoqahansans';font-weight:400;color:#212121;">
		<p style="color:#000;font-size:18px;">헬로핀테크의 윤리강령은 P2P금융과 핀테크 산업의 건전한 발전을 기하기 위해 제정되었습니다.</p><br/>

		<p style="color:#000;font-size:15px;font-weight:400;">제 1조 (신의)</p>
		1) 투자상품의 영업, 출시, 자금모집, 상환 시까지의 전 과정에 대하여 법률과 취급 가이드를 준수합니다.<br/>
		2) 투자자의 '알 권리'를 위해 투자상품의 상태, 권리설정 등에 대한 증빙자료를 최대한 공개합니다.<br/>
		3) 어떠한 경우라도 공시된 투자상품에 대한 정보는 임의수정, 삭제를 하지 않습니다.<br/>
		4) 투자자의 자산을 안전하게 지키기 위해 신한은행 투자금 신탁관리, 운영 프로세스의 시스템화 등과 같이 주요업무에 대한 구조적 안전장치를 마련합니다.<br/>
		5) 고객의 개인정보에 대해 정책적, 시스템적 안전장치를 마련하고 이를 안전하게 유지하기 위해 만전을 기합니다.<br/><br/>

		<p style="color:#000;font-size:15px;font-weight:400;">제 2조 (공정)</p>
		1) 영업, 이해관계자와 심사간 철저한 절연을 통해 투자심의의 객관성이 유지될 수 있도록 합니다.<br/>
		2) 부당한 이득을 위해 접대, 금품수수 등을 하거나 받지 않습니다.<br/>
		3) 맡은 업무에 대해 객관적이고 공정하게 심사, 관리, 감독합니다.<br/><br/>

		<p style="color:#000;font-size:15px;font-weight:400;">제 3조 (준법)</p>
		1) 대한민국 법률을 준수합니다.<br/>
		2) 금감원의 P2P대출 가이드라인을 준수합니다.<br/>
		3) 모든 공시자료에 대해 허위, 과장하지 않습니다.<br/>
		4) 거짓,과장, 기타 오인할 소지가 있는 광고를 하지 않습니다.<br/><br/>

		<p style="color:#000;font-size:15px;font-weight:400;">제 4조 (예절)</p>
		1) 모든 고객분들께 항상 예를 갖추어 정직하게 응대합니다.<br/>
		2) 서로를 공경하고 예의와 배려를 다하도록 합니다.<br/><br/>

		<p style="color:#000;font-size:15px;font-weight:400;">제 5조 (가치관)</p>
		1) 옳지 못한 행동에서 얻은 '득(得)'은 결국 '실(失)'이 되어 돌아옴을 가슴으로 이해하도록 합니다.<br/>
		2) 정직과 신뢰를 최우선가치로 실천하겠습니다.<br/>
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