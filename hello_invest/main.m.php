<?php IF(!@$strIncludeKind) { exit; } ?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="format-detection" content="telephone=no, address=no, email=no" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<title>온라인투자금융사 | 헬로펀딩</title>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>

<link href="css/GFA.css" rel="stylesheet" >
<link rel="stylesheet" type="text/css" href="css/animate.css" />
<link rel="stylesheet" type="text/css" href="css/swiper.min.css">

<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/wow.min.js"></script>
<script>
jQuery(document).ready(function( $ ) {
	$('.counter').counterUp({
		delay: 10,
		time: 1000
	});
});
</script>


</head>
<!-- 본문내용 START -->

<div id="event">
	<div class="visual">
		<ul>
			<li><img src="img/logo_w.png"></li>
			<li><a href="tel:1588-6760">투자문의 1588-6760</a></li>
		</ul>
		<p class="title01 wow text01"><img src="img/title_01.png" alt="1분투자"></p>
		<p class="title02 wow text02"><img src="img/title_02.png" alt="연12.9%수익"></p>
	</div>

<!--투자현황--------------------------------------------------------------------------->
	<div class="status wow slides">
		<h2>투자 현황</h2>
		<ul>
			<li class="status-box">
				<ul>
					<li>누적 대출액</li>
					<li><span class="purple fw600 counter"><?=explode("억", $NUJUK_STATUS['investAmount'])[0]?></span><span class="black">억</span></li>
				</ul>
			</li>
			<li class="status-box">
				<ul>
					<li>누적 상환액</li>
					<li><span class="purple fw600 counter"><?=explode("억", $NUJUK_STATUS['repayPrincipal'])[0]?></span><span class="black">억</span></li>
				</ul>
			</li>
			<li class="status-box">
				<ul>
					<li>상환율</li>
					<li><span class="purple fw600 counter"><?=floatRtrim($NUJUK_CACHE['paidRatio'])?></span><span class="black">%</span></li>
				</ul>
			</li>
		</ul>
	</div>

<!--이유있는 선택--------------------------------------------------------------------------->
	<div class="safe wow slides">
		<h4>헬로를 선택하는 특별한 이유를 알려드립니다.</h4>
		<h2>이유있는 선택</h2>
		<ul>
			<li class="safe-box">
				<ul>
					<li><img src="img/praise_1.png"></li>
					<li><span class="orange fw600">은행을 통한 자금관리</span></li>
					<li>예치금, 투자금, 상환금<br>신한은행 신탁관리</li>
				</ul>
			</li>
			<li class="safe-box left">
				<ul>
					<li><img src="img/praise_2.png"></li>
					<li><span class="orange fw600">담보상품 관리</span></li>
					<li>담보여력 충분한 상품 출시<br>리스크 최소화</li>
				</ul>
			</li>
			<li class="safe-box">
				<ul>
					<li><img src="img/praise_3.png"></li>
					<li><span class="orange fw600">최상급 보안 시스템</span></li>
					<li>통신정보 보안수준<br>A+등급 획득</li>
				</ul>
			</li>
			<li class="safe-box left">
				<ul>
					<li><img src="img/praise_4.png"></li>
					<li><span class="orange fw600">다양한 채권보전 시스템</span></li>
					<li>다각도의 안전장치 마련,<br>모든 서류 투명 오픈</li>
				</ul>
			</li>
			<li class="safe-box">
				<ul>
					<li><img src="img/praise_5.png"></li>
					<li><span class="orange fw600">국내 최초 현장 LIVE</span></li>
					<li>실시간 현장 확인 가능한<br>스트리밍 서비스 구축</li>
				</ul>
			</li>
			<li class="safe-box left">
				<ul>
					<li><img src="img/praise_6.png"></li>
					<li><span class="orange fw600">투자심의위원회 운영</span></li>
					<li>다각도의 분석으로 선별된<br>상품만 출시</li>
				</ul>
			</li>
		</ul>
	</div>

<!--이벤트--------------------------------------------------------------------------->
	<div class="event wow slides">
		<h4>지금 핫 이슈!  다양한 혜택을 드립니다.</h4>
		<h2>이벤트</h2>
		<div>
			<div class="event_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/event/2111_2/')";>
				<img src="img/event01.jpg">
				<p>회원가입 후 첫 투자 시 예치금 지급!</p>
				</a>
			</div>
		</div>
		<div>
			<div class="event_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/event/2111_1/')";>
				<img src="img/event02.jpg">
				<p>주택담보상품에 투자 시 상품권 지급!</p></a>
			</div>
		</div>
	</div>

<!--투자상품--------------------------------------------------------------------------->
	<div class="product wow slides">
		<h4>투자자가 꼭 알아야할 투자의 모든 것!</h4>
		<h2>투자 상품</h2>

		<div>
			<div class="product_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/investment/invest_list.php?CA=A')";>
					<ul>
						<li><img src="img/product_001.jpg"></li>
						<li><h3 class="fw600">부동산PF</h3>
							<h5 class="fw600">연 12~17% 수익률</h5>
							<p>우선수익권, 근저당권, 질권설정 등 담보설정으로 안전장치마련</p></li>
					</ul>
				</a>
			</div>
		</div>
		<div>
			<div class="product_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/investment/invest_list.php?CA=A2')";>
					<ul>
						<li><img src="img/product_002.jpg"></li>
						<li><h3 class="fw600">주택담보</h3>
							<h5 class="fw600">연 8~12% 수익률</h5>
							<p>서울, 수도권, 6대 광역시 등 주요지역 아파트 담보</p></li>
					</ul>
				</a>
			</div>
		</div>
		<div>
			<div class="product_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/investment/invest_list.php?CA=C')";>
					<ul>
						<li><img src="img/product_003.jpg"></li>
						<li><h3 class="fw600">확정매출채권</h3>
							<h5 class="fw600">연 8~12% 수익률</h5>
							<p>우량기업/ 파트너사의 기발생한 카드 정산대금을 직접 지급상환</p></li>
					</ul>
				</a>
			</div>
		</div>
	</div>


<!--하단정보--------------------------------------------------------------------------->

	<div class="footer">
		<h3 class="fw600">헬로펀딩은 투자원금과 수익을 보장하지 않으며,<br>투자손실에 대한 책임은 모두 투자자에게 있습니다.<br><span style="display: block; margin-top:10px;">준법감시인 심사필 제2021-C-3 (2021.08.30)</span></h3>
		<p class="gray">(주)헬로핀테크<br>대표 : 최수석 | 사업자번호 : 789-81-00529<br>주소 : 서울시 강남구 대치동 945-10 (테헤란로 98길 8)<br>KT&G 대치타워 5층</p>
	</div>

</div>

<button type="button" class="join_btn tjoin_bt" onClick="location.href='<?=$join_url?>';">신규회원<span class="yellow"> 가입하기</span></button>

<!-- 본문내용 E N D -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
<script src="js/jquery.counterup.min.js"></script>
<script src="js/swiper.min.js"></script>
<script>
new WOW().init();
</script>

<form name="mregfm" id="mregfm">
	<input type="hidden" name="q" value="" />
	<input type="hidden" name="pid" value="<?=$pid?>" />
</form>

<!--
<script type="text/javascript">
function check_click_check(q)
{
	$("input[name='q']").val(q);
	var pid = $("input[name='pid']").val();

	$("#mregfm").attr("method","post");
	$("#mregfm").attr("target","_blank");
	$("#mregfm").attr("action","/member/mpid.php");
	$("#mregfm").submit();
}
</script>
//-->