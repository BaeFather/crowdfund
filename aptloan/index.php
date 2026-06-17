<?
include_once('_common.php');
include_once(G5_PATH . '/pid_check.inc.php');		// pid 유입체크 및 쿠키생성이 필요한 페이지에만 include

// 이전 pid 수령 방식에 대응
if( trim($_REQUEST['p']) ) $req_pid = sql_real_escape_string(trim($_REQUEST['p']));
if($req_pid) {
	if(get_cookie("ck_pid")=='') {
		$cookie_life_time = strtotime(date('Y-m-d') . " 23:59:59") - time();
		$pid_cookie_time = $cookie_life_time;		// 쿠키인정시간
		$pid_join_url    = "/";
		set_cookie("ck_pid", $req_pid, $pid_cookie_time);
	}
}

/* 2022-03-30 주석처리 ------------
IF(!$_COOKIE['pid']) {
	$strP = $_GET['p'];
	IF($strP) {
		setcookie("pid",TRIM($strP),0,"/","");
	}
}
ELSE {
	$strP = $_COOKIE['pid'];
}
-----------------------------------*/


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

?>

<script>
jQuery(document).ready(function( $ ) {
	$('.counter').counterUp({
		delay: 3,
		time: 400
	});
});
</script>
<!--
<script>
var intscroll = false;
$("#loan").on('mousewheel',function(e) {
	var wheel = e.originalEvent.wheelDelta;

	if(wheel < 0) // down
	{
		if(intscroll == false)  // 1more
		{
			$(".fix_btn").show();
			intscroll = true;
		}
	}
});
</script>
-->

<link rel="stylesheet" type="text/css" href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css">
<link rel="stylesheet" type="text/css" href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css">
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@900&display=swap">
<link rel="stylesheet" type="text/css" href="/aptloan/css/animate.css">
<link rel="stylesheet" type="text/css" href="/aptloan/css/apt.css">


<script src="/aptloan/js/wow.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
<script src="/aptloan/js/jquery.counterup.min.js"></script>


<? include_once(G5_PATH . "/aptloan/inc_script.php"); ?>

<!-- 본문내용 START -->

<div id="loan">
	<div class="tops">
		<div class="visual">
			<div><h2>가장 편하고 빠른 헬로펀딩<br><span>아파트 담보대출</span></h2></div>
			<div class="boxs">
				<ul>
					<li class="b_box wow slides">
						<ul>
							<li class="box_title">시중은행대비 <br class="br2">대출한도</li>
							<li class="box_point num_two"><span></span>2배</li>
						</ul>
					</li>
					<li class="b_box wow slides">
						<ul>
							<li class="box_title">한도조회 시<br>신용점수 영향</li>
							<li class="box_point chinese">無</li>
						</ul>
					</li>
					<li class="b_box wow slides">
						<ul>
							<li class="box_title">합리적인<br>금리</li>
							<li class="box_point num_six"><span>연</span>5.9<span>%~</span></li>
						</ul>
					</li>
					<li class="b_box wow slides">
						<ul>
							<li class="box_title">DTI &<br>DSR</li>
							<li class="box_point">미적용</li>
						</ul>
					</li>
					<li class="b_box wow slides">
						<ul>
							<li class="box_title">심사 후<br>대출실행까지</li>
							<li class="box_point">3일</li>
						</ul>
					</li>
				</ul>
			</div>
			<button type="button" class="yellow_btn wow" onClick="location.href='loan.php'">대출한도 알아보기<br><span>(30초이내, 신용점수 영향 無)</span></button>
		</div>
	</div>

	<div class="call">
		<ul>
			<li>
				아파트 담보대출상담이 필요하시면 연락주세요!<br>
				<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
			</li>
			<li>1588-5210</li>
			<li>
				<img src="/aptloan/img/call.png">
			</li>
		</ul>
	</div>
	<div class="m_call">
		<ul>
			<li>
				상담이 필요하시면 언제든지 연락주세요!<br>
				<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
			</li>
			<li>
				<img src="/aptloan/img/call.png"><span>1588-5210</span>
			</li>
		</ul>
	</div>
	<div class="contents">
			<h2>많은 분들이 헬로펀딩 <br><span>아파트 담보대출</span>을 선호하는 이유?</h2>
			<div class="num1">
				<ul>
					<li>
						<?php IF(IN_ARRAY($p, ARRAY("naverbrand"))) { ?>
						<p class="num_title wow">1. 대출한도 <span>2배 이상</span></p>
						<p class="num_text wow">LTV 80%, 최대 10억원까지<br>시중은행 대비 대출 한도 2배 이상 대출이 가능합니다.</p>
						<?php } ELSE { ?>
						<p class="num_title wow">1. 대출한도 <span>2배 이상</span></p>
						<p class="num_text wow">시중은행 대비 대출 한도의 2배 이상 <br>대출이 가능합니다.</p>
						<?php } ?>
					</li>
					<li class="wow progress">
						<div class="gray_bar"></div>
						<div class="wow graph">
							<div class="wow blue_bar"><span>2배</span></div>
						</div>
						<p class="graph_text">시중은행권<span>헬로펀딩</span></p>
						<p class="mgraph_text">시중은행권<span>&emsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;헬로펀딩&emsp;</span></p>
					</li>
				</ul>
			</div>
			<div class="line"></div>
			<div class="num2">
				<ul>
					<li>
						<p class="num_title wow">2. 한도조회 시 <span>신용점수 영향 無</span></p>
						<p class="num_text wow">여러 번 한도를 조회해도~ <br class="br">신용점수에 영향을 주지 않습니다.</p>
					</li>
					<li class="wow up"><img src="/aptloan/img/num2.png" alt="신용점수 영향無"></li>
				</ul>
			</div>
		    <div class="line"></div>
			<div class="num3">
				<ul>
					<li>
						<p class="num_title wow">3. 연 <span>5.9% ~ 9.9%</span> 수준의 금리</p>
						<p class="num_text wow">헬로펀딩은 합리적인 중금리를 지향합니다.</p>
					</li>
					<li>
						<div class="rates">
							<div class="min">최저<span class="counter wow">5.9</span>%</div>
							<div class="max">최고<span class="counter wow">9.9</span>%</div>
						</div>
						<div class="wow up"><img src="/aptloan/img/bar.png"></div>
						<div class="text wow up">대부 업체 대환대출<br>최적 금리</div>
					</li>
				</ul>
			</div>
		    <div class="line"></div>
		    <div class="num4">
				<ul>
					<li>
						<p class="num_title wow">4. DTI, DSR <span>무관</span></p>
						<p class="num_text wow">한도산출 기준인 DTI, DSR과 무관하게<br>대출 신청이 가능합니다.</p>
					</li>
					<li class="wow up">
						<p>DTI, DSR<span class="typewriter">미적용</span></p>
					</li>
				</ul>
			</div>
		    <div class="line"></div>
			<div class="num5">
				<ul>
					<li>
						<p class="num_title wow">5. 쉽고 <span>빠른 대출</span></p>
						<p class="num_text wow">대출 신청 및 심사 후 평균 3일 이내<Br>대출이 가능합니다.</p>
					</li>
					<li>
						<div class="step wow up">
							<span class="wow step1 slides circles">대출신청</span>
							<span class="wow slides triangle">▶</span>
							<span class="wow step2 slides circles">심사 및 승인</span>
							<span class="wow slides triangle">▶</span>
							<span class="wow step3 slides circles">대출실행</span>
						</div>
					</li>
				</ul>
			</div>
			<div class="line"></div>
		</div>
		<div class="fix_btn">
			<button type="button" class="white_btn" onClick="location.href='/aptloan/loan.php';">대출한도 알아보기<br><span>(30초이내, 신용점수 영향 無)</span></button>
		</div>
	</div>

	<script>
		new WOW().init();
	</script>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>
