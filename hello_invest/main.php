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

<link href="css/web.css" rel="stylesheet" >
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
		<div class="top_bg">
			<ul>
				<li><img src="img/web_logo.png"></li>
				<li>투자문의 1588-6760</li>
			</ul>

			<div class="Calculator">
				헬로펀딩에&nbsp;
				<select id="principal" onChange="simulation(this.value);">
					<option value="500000">50만원</option>
					<option value="1000000" selected>100만원</option>
					<option value="3000000">300만원</option>
					<option value="5000000">500만원</option>
					<option value="10000000">1,000만원</option>
				</select>
				&nbsp;투자한다면?
				<h3 style="margin-top:25px">예상 총 수익<span class="color" id="smoney"  style="font-weight: 600; padding-left:20px;"></span><span class="color">원</span></h3>
				<h3>은행예금대비<span class="color"  style="font-weight: 600;  padding-left: 16px;">7.5배</span><span class="color">수익</span></h3>
				<p style="font-size:18px; margin-top:10px;">12개월 만기 상품 투자 시, 세전수익률 기준<br><span style="font-size:16px;">(1금융권 정기예금 평균 금리 1.7% 대비)</span></p>
			</div>

			<button type="button" id="join_open" class="join_btn tjoin_bt wow bounceInUp" onClick="location.href='<?=$join_url?>';">재테크 하러가기</span></button>
		</div>
	</div>


<!--투자현황--------------------------------------------------------------------------->
	<div class="status">
		<p><img src="img/web_sub_01.png" alt="투자현황"></p>
		<ul>
			<li class="status-box">
				<ul>
					<li>누적 대출액</li>
					<li><span class="purple fw600 counter"><?=explode("억", $NUJUK_STATUS['investAmount'])[0]?></span><span class="black">억</span></li>
				</ul>
			</li>
			<li class="line"></li>
			<li class="status-box">
				<ul>
					<li>누적 상환액</li>
					<li><span class="purple fw600 counter"><?=explode("억", $NUJUK_STATUS['repayPrincipal'])[0]?></span><span class="black">억</span></li>
				</ul>
			</li>
			<li class="line"></li>
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
		<div class="safe_part">
			<p><img src="img/web_sub_02.png" alt="헬로펀딩을 선택하는 이유"></p>
			<ul>
				<li class="safe-box">
					<ul>
						<li><img src="img/web_praise_1.png"></li>
						<li><span class="orange fw600">은행을 통한 자금관리</span></li>
						<li>예치금, 투자금, 상환금<br>신한은행 신탁관리</li>
					</ul>
				</li>
				<li class="safe-box">
					<ul>
						<li><img src="img/web_praise_2.png"></li>
						<li><span class="orange fw600">담보상품 관리</span></li>
						<li>담보여력이 충분한 상품 출시<br>리스크 최소화</li>
					</ul>
				</li>
				<li class="safe-box right">
					<ul>
						<li><img src="img/web_praise_3.png"></li>
						<li><span class="orange fw600">최상급 보안 시스템</span></li>
						<li>통신정보 보안수준<br>A+등급 획득</li>
					</ul>
				</li>
				<li class="safe-box">
					<ul>
						<li><img src="img/web_praise_4.png"></li>
						<li><span class="orange fw600">다양한 채권보전 시스템</span></li>
						<li>다각도의 안전장치 마련,<br>모든 서류 투명 오픈</li>
					</ul>
				</li>
				<li class="safe-box">
					<ul>
						<li><img src="img/web_praise_5.png"></li>
						<li><span class="orange fw600">국내 최초 현장 LIVE</span></li>
						<li>실시간 현장 확인 가능한<br>스트리밍 서비스 구축</li>
					</ul>
				</li>
				<li class="safe-box right">
					<ul>
						<li><img src="img/web_praise_6.png"></li>
						<li><span class="orange fw600">투자심의위원회 운영</span></li>
						<li>다각도의 분석으로 선별된<br>상품만 출시</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>

<!--이벤트--------------------------------------------------------------------------->
	<div class="event wow slides">
		<p><img src="img/web_sub_03.png" alt="헬로펀딩 이벤트"></p>
		<ul>
			<li class="event_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/event/2111_2/')"><img src="img/web_event01.jpg">
				<p>회원가입 후 첫 투자 시 예치금 지급!</p></a>
			</li>
			<li class="event_box">
				<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/event/2111_1/')"><img src="img/web_event02.jpg">
				<p>주택담보상품에 투자 시 상품권 지급!</p></a>
			</li>
		</ul>
	</div>

<!--투자상품--------------------------------------------------------------------------->
	<div class="product wow slides">
		<div class="product_part">
			<p><img src="img/web_sub_04.png" alt="헬로펀딩 투자상품"></p>
			<ul>
				<li class="product_box">
					<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/investment/invest_list.php?CA=A')">
						<ul>
							<li><img src="img/web_product_001.jpg"></li>
							<li><h3 class="fw600">부동산PF</h3>
								<h5 class="fw600">연 12~17% 수익률</h5>
								<p>우선수익권, 근저당권, 질권설정 등<br>담보설정으로 안전장치마련</p></li>
						</ul>
					</a>
				</li>
				<li class="product_box">
					<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/investment/invest_list.php?CA=A2')">
						<ul>
							<li><img src="img/web_product_002.jpg"></li>
							<li><h3 class="fw600">주택담보</h3>
								<h5 class="fw600">연 8~12% 수익률</h5>
								<p>서울, 수도권, 6대 광역시 등<br>주요지역 아파트 담보</p></li>
						</ul>
					</a>
				</li>
				<li class="product_box">
					<a href="#none" onClick="check_click_check('https://www.hellofunding.co.kr/investment/invest_list.php?CA=C')">
						<ul>
							<li><img src="img/web_product_003.jpg"></li>
							<li><h3 class="fw600">확정매출채권</h3>
								<h5 class="fw600">연 8~12% 수익률</h5>
								<p>우량기업/ 파트너사의 기발생한<br>카드 정산대금을직접 지급상환</p></li>
						</ul>
					</a>
				</li>
			</ul>
			<button type="button" id="join_open" class="join_btn bjoin_bt" onClick="location.href='<?=$join_url?>';">신규회원<span class="yellow"> 가입하기</button>
		</div>
	</div>


<!--하단정보--------------------------------------------------------------------------->

	<div class="footer">
		<h3 class="fw600">헬로펀딩은 투자원금과 수익을 보장하지 않으며, 투자손실에 대한 책임은 모두 투자자에게 있습니다.<br><span style="display: block; margin-top:10px;">준법감시인 심사필 제2021-C-3 (2021.08.30)</span></h3>
		<p class="gray">(주)헬로핀테크 | 대표 : 최수석 | 사업자번호 : 789-81-00529<br>주소 : 서울시 강남구 대치동 945-10 (테헤란로 98길 8)<br>KT&G 대치타워 5층</p>
	</div>

</div>

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
<script>
function check_click_check(q) {
	//f = document.mregfm;

	$("input[name='q']").val(q);

	var pid = $("input[name='pid']").val();
  $("#mregfm").attr("method","post");
  $("#mregfm").attr("target","_blank");
  $("#mregfm").attr("action","/member/mpid.php");
  $("#mregfm").submit();
}
</script>
-->

<script type="text/javascript">
function simulation(obj) {
	var ret = obj * 0.129;
	$("#smoney").html(number_format(ret));
}
simulation(1000000);
</script>