<?
// 자바스크립트 헤더에 포함
add_javascript('<script src="/theme/2018/js/jquery.counterup.min.js"></script>', 0);
add_javascript('<script src="/theme/2018/js/wow.min.js"></script>', 0);
add_javascript('<script src="//cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>', 0);
add_javascript('<script src="//cdnjs.cloudflare.com/ajax/libs/jQuery-rwdImageMaps/1.6/jquery.rwdImageMaps.min.js"></script>', 0);
?>

<script type="text/javascript">
/* 헤더 투명 */
$(document).ready(function() {
	var rollHeader = 80;
	$(window).scroll(function() {
	var scroll = getCurrentScroll();
		if( scroll >= rollHeader ) {
			$('#hd_main').addClass('roll');
			$("#logo0").attr('src','<?=G5_THEME_IMG_URL?>/main_m/m_c_logo.png');
			$("#menu0").attr('src','<?=G5_THEME_IMG_URL?>/main_m/menu_icon_c.png');
			$("#my_page_icon").attr('src','<?=G5_THEME_IMG_URL?>/mobile/my_page_icon_b.png');
			$("#user_login_main").css("color", "#073190");
		}
		else {
			$('#hd_main').removeClass('roll');
			$("#logo0").attr('src','<?=G5_THEME_IMG_URL?>/main_m/m_w_logo.png');
			$("#menu0").attr('src','<?=G5_THEME_IMG_URL?>/main_m/menu_icon_w.png');
			$("#my_page_icon").attr('src','<?=G5_THEME_IMG_URL?>/mobile/my_page_icon_w.png');
			$("#user_login_main").css("color", "#FFF");
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

<script type="text/javascript">
/* 하단 탭메뉴처리 */
$(document).ready(function(){
	$('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	})
})
</script>

<script type="text/javascript">

// 레이어 팝업
function invest_apply_kakaoPopup() {

	var $layer = $('#investLayerPop');
	
	// 화면 중앙에 위치
	var left = ( $(window).scrollLeft() + ($(window).width() - $layer.width()) / 2 );
	var top = ( $(window).scrollTop() + ($(window).height() - $layer.height()) / 2 );

	$layer.css({"left":left, "top":top, "z-index":'110'}).addClass("invest_layer_pop");

	$("body").prepend("<div id='investMask' style='display:none;position:absolute;background:black;z-index:100;background:rgba(0,0,0,.5);'></div>");
	$("body").append($layer);
	
	$('img[usemap]').rwdImageMaps();  // 이미지 맵 모바일 해상도에 맞게 area coords 조정 

	wrapWindowByMask();
	$layer.show(); 
}

// 레이어 팝업 뒷 배경
function wrapWindowByMask() {
	var $mask = $('#investMask');

	// 화면의 높이와 너비
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();

	$mask.css({'width':maskWidth,'height':maskHeight});
	$mask.show();

	// 뒷 배경 클릭시 레이어 팝업 및 뒷 배경 모두 닫기
	$mask.click(function() {
		$('#investLayerPop').hide();
		$(this).hide();
	});
}

// 사이즈 리사이징
function ResizingLayer() {
    if($("#investMask").css("display") == "block") {

        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        // 마스크의 높이와 너비를 전체 화면 채움
        $("#investMask").css({'width':maskWidth,'height':maskHeight});  
    }
}

// 윈도우 창 크기가 변경될 때 발생하는 이벤트
window.onresize = ResizingLayer;  

// 레이어 팝업 닫기
$(document).ready(function() {
	$('.invest_layer_pop > .close').on('click', function() {
		$('#investLayerPop').hide();
		$('#investMask').hide();
	});
});

</script>

<div id="main_top">
	<h2>투자자와 함께하는
	<span class="counter2 z1"><?=number_format($NUJUK_CACHE['leftDayCount'])?></span>일<br/>
	<span class="counter2 z2"><?=number_format($NUJUK_CACHE['totalProductCount'])?></span>개의 상품과<br/>
	<span class="counter2 z3"><?=number_format($NUJUK_CACHE['investCount'])?></span>건의 투자</h2>
	<p>원금 상환 지수 1위<br>헬로펀딩은 안전한 투자를 지원합니다.</p>
</div>
<div class="m_wrap">

	<div class="invest_num">
		<div class="swiper-container s03">
			<div class="swiper-wrapper">
			  <div class="swiper-slide">
				  <ul>
						<li>누적대출액<span><?=number_format(floor($NUJUK_CACHE['investAmount']/100000000))?></span>억원</li>
						<li>대출잔액<span><?=number_format(floor($NUJUK_CACHE['investIngAmount']/100000000))?></span>억원</li>
					</ul>
			  </div>
			  <div class="swiper-slide">
				  <ul>
						<li>누적상환액<span><?=number_format(floor($NUJUK_CACHE['repayPrincipal']/100000000))?></span>억원</li>
						<li>평균수익률(연)<span><?=floatRtrim($NUJUK_CACHE['averageReturn'])?></span>%</li>
				 </ul>
			  </div>
			  <div class="swiper-slide">
				  <ul>
						<li>연체율<span id="overdue-claim-mark" class="t_i_p_icon">!</span><span><?=floatRtrim($NUJUK_CACHE['overduePerc'])?></span>%</li>
						<li>부실률<span id="bankruptcy-claim-mark" class="t_i_p_icon">!</span><span><?=floatRtrim($NUJUK_CACHE['bankruptcy'])?></span>%</li>
				 </ul>
			  </div>
			</div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		</div>
	</div>

	<div class="event">
		<div class="swiper-container s01">
			<div class="swiper-wrapper">
<? for($i=0; $i<count($EVENT); $i++) { ?>
				<div class="swiper-slide"><a href="<?=$EVENT[$i]['linkurl']?>" target="<?=$EVENT[$i]['target']?>"><img src="<?=$EVENT[$i]['imgurl']?>"></a></div>
<? } ?>
			</div>
		</div>
	</div>

	<div class="product">
		<ul>
			<li>투자상품</li>
			<li class="alram" ><a href="javascript:;" onclick="invest_apply_kakaoPopup();">투자알림 신청</a></li>
		</ul>
		<!-- 투자알림 신청하기 레이어팝업 -->
		<div id="investLayerPop" class="invest_layer_pop" style="display:none;position:absolute;width:82%;">
			<img src="/images/btn_close_black.png" alt="close" class="close" style="position:absolute;right:0;margin:4%;cursor:pointer;width:4%;" />
			<img src="../../../popup/images/invest_apply_kakao_popup.jpg" alt="투자알림 신청하기 카카오톡 상품알림" usemap="#link" style="width:100%;"/>
			<map name="link">
				<area shape="rect" coords="69,176,379,233" href="https://pf.kakao.com/_xgAdWu" alt="카카오톡 채널 추가하기" target="_blank"/>
			</map>
		</div>
		<div class="swiper-container clear s02">
			<div class="swiper-wrapper">
				<div class="swiper-slide product01" onclick="location.href='/investment/invest_list.php?CA=C'">
					<div class="product_bg">
						<p class="product_name">매출채권</p>
						<p class="product_text">카드매출담보 상품 매일 출시,<br>투자기간 평균 3일</p>
						<p class="product_num">모집상품<span><?=$NUJUK_CACHE['ingPrdtCountC']?></span></p>
					</div>
				</div>
				<div class="swiper-slide product02"  onclick="location.href='/investment/invest_list.php?CA=A'">
					<div class="product_bg">
						<p class="product_name">부동산PF</p>
						<p class="product_text">토지, 건물 등 부동산 담보와<br>부동산 PF 상품</p>
						<p class="product_num">모집상품<span><?=$NUJUK_CACHE['ingPrdtCountA']?></span></p>
					</div>
				</div>
				
				<div class="swiper-slide product03" onclick="location.href='/investment/invest_list.php?CA=A2'">
					<div class="product_bg">
						<p class="product_name">주택담보</p>
						<p class="product_text">LTV 85% 이하의 서울, 수도권,<br>주요 대도시의 주거용 부동산 담보</p>
						<p class="product_num">모집상품<span><?=$NUJUK_CACHE['ingPrdtCountA2']?></span></p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 인터뷰 : 최근 등록 된 컨텐츠 중 5개 랜덤 -->
	<?php
		
		$isql = "
			SELECT 
				A.*
			FROM 
				(SELECT
					id, thumbnail, subject_m, contents_m, reg_date, round(RAND()*100) rnd_num
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
			<li><a href="/review/?RD=2&S=1&SE=<?=$LIST[$i]['id']?>"><img src="../img/review/<?=$LIST[$i]['thumbnail']?>" /></a></li>
			<li>
				<p class="title"><?=stripslashes($LIST[$i]['subject_m'])?></p>
				<p class="text"><?=$LIST[$i]['contents_m']?></p>
				<a href="/review/?RD=2&S=1&SE=<?=$LIST[$i]['id']?>"><bt>자세히 보기</bt></a>
			</li>
		</ul>
	</div>
	<?
			}
		}
	?>

	<div class="bn">
		<div class="swiper-container s04">
			<div class="swiper-wrapper">
			  <div style="margin:0 5%; width:100%;"><a href="/bbs/board.php?bo_table=notice&wr_id=198"><img src="<?=G5_THEME_IMG_URL?>/new_m/m_livetv2.png"></a></div>
			</div>
		</div>
	</div>

	<div class="board">
		<ul class="tabs">
			<li class="tab-link current" data-tab="tab-1">언론보도</li>
			<li class="tab-link" data-tab="tab-2">공지사항</li>
		</ul>
		<div id="tab-1" class="tab-content current">
<? for($i=0; $i<count($FNEWS); $i++) { ?>
			<p>
				<ul>
					<li class="subject"><a href="<?=$FNEWS[$i]['news_link']?>" target="_blank"><?=$FNEWS[$i]['subject']?></a></li>
					<li class="date"><?=preg_replace("/-/", ".", $FNEWS[$i]['show_date'])?></li>
				</ul>
			</p>
<? } ?>
			<!--p class="more"><a href="/news/funding_news.php">더보기 ></a></p-->
		</div>
		<div id="tab-2" class="tab-content">
<? for($i=0; $i<count($NOTI); $i++) { ?>
			<p>
				<ul>
					<li class="subject"><a href="/bbs/board.php?bo_table=notice&wr_id=<?=$NOTI[$i]['wr_id']?>"><?=$NOTI[$i]['wr_subject']?></a></li>
					<li class="date"><?=preg_replace("/-/", ".", $NOTI[$i]['wr_datetime'])?></li>
				</ul>
			</p>
<? } ?>
			<!--p class="more"><a href="/bbs/board.php?bo_table=notice">더보기 ></a></p-->
		</div>
	</div>
</div>

<script>
var msg = "대출잔액 대비 상환일이 30일 이상 지연된 잔여원금 비율 (한국P2P금융협회 기준)";
$('#overdue-claim-mark').webuiPopover({ title: "연체율", content: msg, closeable: true, width: 200, arrow: true, offsetLeft: 0, offsetTop: -50, trigger: "click", placement: 'bottom', backdrop: false});
var msg = "약정된 상환이 일부 혹은 전부 지연되기 시작해 90일 이상 경과한 대출 <br><br>부실률 = 부실잔여원금 / 총 누적대출액 (P2P금융협회 기준)";
$('#bankruptcy-claim-mark').webuiPopover({ title: "부실률", content: msg, closeable: true, width: 200, arrow: true, offsetRight:0,  offsetTop: -50, trigger: "click", placement: 'bottom', backdrop: false});

var swiper = new Swiper('.s01', {
	loop: true,
	slidesPerView: 'auto',
	centeredSlides: true,
	direction: 'horizontal',
	pagination: {
		el: '.swiper-pagination',
		clickable: true,
		autopaly: true
	},
	loopAdditionalSlides: 1,
	autoplay: { delay: 3000 }
});

var swiper = new Swiper('.s02', {
	slidesPerView: 'auto',
	centeredSlides: true,
	pagination: {
		el: '.swiper-pagination',
		clickable: true,
	},
});

var swiper = new Swiper('.s03', {
	loop: true,
	slidesPerView: 'auto',
	centeredSlides: true,
	pagination: {
		el: '.swiper-pagination',
		 clickable: true,
	},
	navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev'
	},
	autoplay: { delay: 3000 }
});


var swiper = new Swiper('.s04', {
	loop: true,
	slidesPerView: 'auto',
	centeredSlides: true,
	direction: 'horizontal',
	pagination: {
		el: '.swiper-pagination',
		clickable: true,
		autopaly: true
	},
	loopAdditionalSlides: 1,
	autoplay: { delay: 3000 }
});
</script>

<script>
new WOW().init();
</script>
