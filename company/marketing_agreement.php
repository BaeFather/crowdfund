<?php
###############################################################################
## 마케팅 정보 선택적 수집·이용 동의서
###############################################################################

include_once('./_common.php');

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

?>

<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/footer_contents.css" />


<!-- 본문내용 START -->
<div id="content">
	<h2 class="title">마케팅 정보 선택적 수집·이용 동의서</h2>
	<div class="content">
		<br /><br />
		<p>귀하는 개인(신용)정보의 선택적인 수집∙이용, 제공에 대한 동의를 거부할 수 있습니다.<br>
		다만, 동의하지 않을 경우 관련 편의제공(투자상품 출시, 이벤트, 경품지급 등)안내 등에 제한이 있을 수 있으며 그 밖에 불이익은 없습니다.<br>또한 동의 후 에도 동의를 철회하실 수 있습니다.
		</p>
		<p class="head">마케팅 정보 선택적 수집·이용</p>
		<ul>
			<li>가. 마케팅 정보 선택적 수집·이용 목적
				<ul>
					<li>편의제공, 마케팅 활동, 시장조사 등을 목적으로 수집·이용 합니다.</li>
				</ul>
			</li>
			<li>나. 수집·이용할 마케팅 정보의 항목
				<ul>
					<li>개인식별정보: 휴대전화번호, e-mail 등</li>
				</ul>
			</li>
			<li>다. 수집이용하는 마케팅 정보의 보유 및 이용기간 : 동의일로부터 회원 탈퇴 혹은 마케팅 동의 해제시 까지 보유·이용합니다.</li>
		</ul>
	</div>
</div>


<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>