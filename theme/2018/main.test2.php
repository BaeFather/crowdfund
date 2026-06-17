<?
// 자바스크립트 헤더에 포함
add_javascript('<script src="/theme/2018/js/jquery.counterup.min.js"></script>', 0);
add_javascript('<script src="/theme/2018/js/wow.min.js"></script>', 0);
add_javascript('<script src="//cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>', 0);
?>

<script type="text/javascript">
/* 헤더 투명 */
$(document).ready(function() {
	var rollHeader = 150;
	$(window).scroll(function() {
	var scroll = getCurrentScroll();
		if( scroll >= rollHeader ) {
			$('header').addClass('roll');
			$("#logo0").attr('src','<?=G5_THEME_IMG_URL?>/new/c_logo.png');
			$(".f_color").css("color", "#555");
			$(".login a").css("color", "#222");
			$(".login").css("border", "1px solid #222");
		}
		else {
			$('header').removeClass('roll');
			$("#logo0").attr('src','<?=G5_THEME_IMG_URL?>/new/w_logo.png');
			$(".f_color").css("color", "#fff");
			$(".login a").css("color", "#fff");
			$(".login").css("border", "1px solid #dedede");
		}
	});
	function getCurrentScroll() {
		return window.pageYOffset || document.documentElement.scrollTop;
	}
});
</script>

<script type="text/javascript">
$(document).ready(function() {
	$('.counter').counterUp({
		delay: 10,
		time: 2000
	});
	$('.counter2').counterUp({
		delay: 10,
		time: 2000
	});
});
</script>

<div id="main_top">
	<h2>투자자와 함께하는
	<span class="counter2 z1"><?=number_format($NUJUK_CACHE['leftDayCount'])?></span>일<br/>
	<span class="counter2 z2"><?=number_format($NUJUK_CACHE['totalProductCount'])?></span>개의 상품과
	<span class="counter2 z3"><?=number_format($NUJUK_CACHE['investCount'])?></span>건의 투자</h2>
	<p>헬로펀딩은 안전한 투자를 지원합니다.</p>
</div>

<div class="invest_num" style="width: 100%; background-color: #f5f5f5; border: 0; margin: 0; height: 110px;">
	<ul style="width: 1150px; margin: auto;">
		<li style="padding-top: 20px;">누적대출액<br><span class="counter"><?=number_format(floor($NUJUK_CACHE['investAmount']/100000000))?></span>억원</li>
		<li style="padding-top: 20px;">대출잔액<br><span class="counter"><?=number_format(floor($NUJUK_CACHE['investIngAmount']/100000000))?></span>억원</li>
		<li style="padding-top: 20px;">평균수익률(연)<br><span class="counter"><?=floatRtrim($NUJUK_CACHE['averageReturn'])?></span>%</li>
		<li style="padding-top: 20px;">연체율<b id="overdue-claim-mark" class="claim-mark">!</b><br><span class="counter"><?=floatRtrim($NUJUK_CACHE['overduePerc'])?></span>%</li>
		<li style="padding-top: 20px;">부실률<b id="bankruptcy-claim-mark" class="claim-mark">!</b><br><span class="counter"><?=floatRtrim($NUJUK_CACHE['bankruptcy'])?></span>%</li>
	</ul>
</div>

<div id="event"  style="margin: 25px auto 0;">
	<div class="swiper-container">
		<div class="swiper-wrapper">
<? for($i=0; $i<count($EVENT); $i++) { ?>
			<div class="swiper-slide">
				<ul class="event_obj">
					<li><a href="<?=$EVENT[$i]['linkurl']?>" target="<?=$EVENT[$i]['target']?>"><img src="<?=$EVENT[$i]['imgurl']?>" style="width:100%;height:100%"></a></li>
				</ul>
			</div>
<? } ?>
		</div>
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
	</div>
</div>

<script>
var eventSwiper = new Swiper('.swiper-container', {
	loop: true,
	slidesPerView:  3,
	slidesPerGroup: 1, //<?=ceil(count($EVENT))?>,
	spaceBetween: 27,
	loopedSlides: 3,
	loopAdditionalSlides: true,
	navigation: {
		prevEl: '.swiper-button-prev',
		nextEl: '.swiper-button-next'
	},
	autoplay: {
		delay: 3000,
	},
});
</script>

<div class="invest_product">
	<ul>
		<li class="title">투자상품</li>
		<a href="https://pf.kakao.com/_xgAdWu" target="_blank"><li class="bt">투자알림 신청하기</li></a>
	</ul>
	<ul class="product_list">
		<li>
			<a href="/investment/invest_list.php?CA=A2"><div>
				<p class="pro_name">주택담보</p>
				<p class="text">LTV 85% 이하의 서울, 수도권, 주요 대도시의<br>주거용 부동산 담보</p>
				<p class="product_num">모집 상품 <span><?=$NUJUK_CACHE['ingPrdtCountA2']?></span></p>
			</div></a>
		</li>
		<li>
			<a href="/investment/invest_list.php?CA=A"><div>
				<p class="pro_name">부동산</p>
				<p class="text">토지, 건물 등 부동산 담보와 부동산 PF 상품</p>
				<p class="product_num">모집 상품 <span><?=$NUJUK_CACHE['ingPrdtCountA']?></span></p>
			</div></a>
		</li>
		<li>
			<a href="/investment/invest_list.php?CA=C"><div>
				<p class="pro_name">매출채권</p>
				<p class="text">카드매출담보 매일 출시, 투자기간 평균 3일</p>
				<p class="product_num">모집 상품 <span><?=$NUJUK_CACHE['ingPrdtCountC']?></span></p>
			</div></a>
		</li>
		</a>
	</ul>
</div>

<!-- 인터뷰 : 최근 등록 된 컨텐츠 중 4개 랜덤 -->
<?php
	
	$isql = "
		SELECT 
			A.*
		FROM 
			(SELECT
				id, thumbnail, subject, contents, reg_date, round(RAND()*100) rnd_num
			FROM
				epilogue_list
			WHERE 
				section = '1' AND display_yn = 'Y'
			ORDER BY
				id DESC
			LIMIT 4) A
		ORDER BY rnd_num
	";

	$ires = sql_query($isql);
	$icnt = sql_num_rows($ires);
?>
<?
	for($i=0; $i<$icnt; $i++) {

		$LIST[$i] = sql_fetch_array($ires);

		if($i == 0) {
?>
<div class="interview roll">
	<ul class="box">
		<li><a href="/review/?RD=2&S=1&SE=<?=$LIST[$i]['id']?>"><img src="../img/review/<?=$LIST[$i]['thumbnail']?>" style="width: 430px; height: 300px;" /></a></li>
		<li>
			<span>투자자 인터뷰</span>
			<p class="title"><?=stripslashes($LIST[$i]['subject'])?></p>
			<p class="text"><?=$LIST[$i]['contents']?></p>
			<a href="/review/?RD=2&S=1&SE=<?=$LIST[$i]['id']?>"><bt>자세히 보기</bt></a>
		</li>
	</ul>
</div>
<?
		}
	}
?>


<div class="line_bn">
	<div class="swiper-container">
		<div>
			<div>
				<a href="/bbs/board.php?bo_table=notice&wr_id=198"><img src="<?=G5_THEME_IMG_URL?>/new/livetv.png"></a>
			</div>
		</div>
	</div>
</div>

<div class="board_list">
	<ul>
		<li class="news">
			<ul>
				<li class="title">언론보도</li>
				<li class="bt"><a href="/news/funding_news.php"><img src="<?=G5_THEME_IMG_URL?>/new/plus.png"></a></li>
			</ul>
			<div>
<? for($i=0; $i<count($FNEWS); $i++) { ?>
				<p>
					<ul>
						<li class="dot"><img src="<?=G5_THEME_IMG_URL?>/new/dot.jpg"></li>
						<li class="subject"><a href="<?=$FNEWS[$i]['news_link']?>" target="_blank"><?=$FNEWS[$i]['subject']?></a></li>
						<li class="date"><?=preg_replace("/-/", ".", $FNEWS[$i]['show_date'])?></li>
					</ul>
				</p>
<? } ?>
			</div>
		</li>
		<li class="notice">
			<ul>
				<li class="title">공지사항</li>
				<li class="bt"><a href="/bbs/board.php?bo_table=notice"><img src="<?=G5_THEME_IMG_URL?>/new/plus.png"></a></li>
			</ul>
			<div>
<? for($i=0; $i<count($NOTI); $i++) { ?>
				<p>
					<ul>
						<li class="dot"><img src="<?=G5_THEME_IMG_URL?>/new/dot.jpg"></li>
						<li class="subject"><a href="/bbs/board.php?bo_table=notice&wr_id=<?=$NOTI[$i]['wr_id']?>"><?=$NOTI[$i]['wr_subject']?></a></li>
						<li class="date"><?=preg_replace("/-/", ".", $NOTI[$i]['wr_datetime'])?></li>
					</ul>
				</p>
<? } ?>
			</div>
		</li>
	</ul>
</div>

<script>
var msg = "대출잔액 대비 상환일이 30일 이상 지연된 잔여원금 비율 (한국P2P금융협회 기준)";
$('#overdue-claim-mark').webuiPopover({ title: "연체율", content: msg, closeable: true, width: 330, height: 50, trigger: "click", placement: 'bottom', backdrop: false});
var msg = "약정된 상환이 일부 혹은 전부 지연되기 시작해 90일 이상 경과한 대출 부실률 = 부실잔여원금 / 총 누적대출액 (P2P금융협회 기준)";
$('#bankruptcy-claim-mark').webuiPopover({ title: "부실률", content: msg, closeable: true, width: 330, height: 80, trigger: "click", placement: 'bottom', backdrop: false});
</script>

<script>
new WOW().init();
</script>
