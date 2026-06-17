<?

add_stylesheet('	<link rel="stylesheet" href="/syndicate/oligo/web/css/investment_info_m.css?ver=20200128">', 0);

?>

	<input type="hidden" name="prd_idx" value="<?=$prd_idx;?>">
	<input type="hidden" name="invest_finished" id="invest_finished" value="<?=($invest_finished) ? 'true' : 'false'; ?>">

	<!-- 상품 슬라이드 시작 -->
	<div id="p_info">
		<div class="p_info_b" <? if($PRDT['title_image_url_m']) { ?> style="background:url('<?=$PRDT['title_image_url_m']?>') repeat center;" <? } ?>>
			<div class="p_info_bb">
				<div class="p_flags">
					<ul>
						<?=$cFlag?><?=$aiFlag?><?=$conFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
					</ul>
				</div>

				<div class="p_tit"><?=$PRDT['title']?></div>
				<div class="p_date">모집기간 : <?=$print_sdate;?> ~</div>

				<div class="p_info_total">
					<div>
						<span>투자수익률(연)</span>
						<?=$invest_return?><b>%</b>
					</div>
					<div>
						<span>투자기간</span>
						<?=$invest_period?><b><?=$invest_period_unit?></b>
					</div>
					<div>
						<span>모집금액</span>
						<?=$print_recruit_amount?><b>원</b>
					</div>
				</div>

				<div class="process_wrap">
					<div class="process">

						<div class="process_tag">
							<div class="process_tag_c">
								<span>투자모집률 / 모집된 금액</span>
								<strong class="p_t_n" id="progressData"><?=$product_invest_percent?>%</strong> / <strong class="p_t_t" id="totalRecruitValue"><?=price_cutting($PRDT["total_invest_amount"]+0);?>원</strong>
							</div>
						</div>
						<div id="progressBar" class="process_bar" style="width:<?=(($product_invest_percent <= 100)?$product_invest_percent:100).'%';?>"></div>
					</div>
				</div>

				<br/><br/>

				<? if($PRDT['product_summary']) echo $PRDT['product_summary']; ?>

				<div class="sns_share">
					<ul>
						<li>
							<a href="#" data-toggle="sns_share" data-service="facebook" data-title="페이스북 SNS공유">
								<img src="<?=G5_THEME_IMG_URL?>/sub/sns_f_btn01.png" alt="facebook" width="30">
							</a>
						</li>
						<li>
							<a href="#" data-toggle="sns_share" data-service="naver" data-title="네이버 SNS공유">
								<img src="<?=G5_THEME_IMG_URL?>/sub/sns_b_btn01.png" alt="naver" width="30">
							</a>
						</li>
						<li>
							<a href="#" data-toggle="sns_share" data-service="kakaostory" data-title="카카오스토리 SNS공유">
								<img src="<?=G5_THEME_IMG_URL?>/sub/sns_k_btn01.png" alt="kakao" width="30">
							</a>
						</li>
						<li>
							<a href="#" data-toggle="sns_share" data-service="url_copy" data-title="주소복사하기">
								<img src="<?=G5_THEME_IMG_URL?>/investment/url_icon.png" alt="url_copy" width="30">
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- 예상수익금 -->
	<div class="pre_earn clearfix">
		<ul class="pre_earn_c" style="padding-bottom:-52px;">
			<li>지금 이 상품에</li>
			<li><input type="text" name="principal_value" value="<?=number_format(5000000)?>" maxlength="11" placeholderaa="투자금액입력. 예)1,000,000원" onkeyup="formatNumber(this);simulation();"></li>
			<li>원을 투자시</li>
		</ul>
		<div class="earn_info">
			<p class="earn_btn" style="padding:10px 0; margin-top:10;">
				<span style="text-align:left;width:55%;display:inline-block;">예상 총 실수익금 (세후) <span id="earninfo1-claim-mark" class="claim-mark" >?</span></span>
				<span style="text-align:right;width:41%;display:inline-block;"><strong id="ajxTotalInterestPrice">0</strong>원</span>
			</p>
			<p class="earn_btn <?=($PRDT['open_datetime'] < '2018-08-31 09:00:00')?'blind':'';?>" style="padding:10px 0;">
				<span style="text-align:left;width:53%;display:inline-block;">은행예금 대비 수익 <span id="earninfo3-claim-mark" class="claim-mark">?</span></span>
				<span style="text-align:right;width:43%;display:inline-block;"><strong id="ajaDiffEarning">0</strong>배</span>
			</p>
		</div>
		<!--
		<div class="simulation_detail_btn" onClick="location.href='simulation.php?prd_idx=<?=$prd_idx?>';" style="padding:20px 0;">투자시뮬레이션 자세히보기 ></div>
		-->
	</div>
	<script type="text/javascript">
		var msg = "본 상품의 투자금액에 따른 수익금에서 세금과 플랫폼 이용료를 제외한 금액이며, 조기상환 등 투자기간 변동에 의해 실제와 다를 수 있습니다.";
		$('#earninfo1-claim-mark').webuiPopover({ title: "예상 총 실수익금(세후)", content: msg, closeable: true, width: 180, height: 90, trigger: "click", placement: 'bottom', backdrop: false});
		var msg = "투자기간 중 헬로펀딩이 매월 지급해 드리는 세후 수익금으로, 이자산정일에 따라 변동될 수 있습니다.";
		$('#earninfo2-claim-mark').webuiPopover({ title: "월 평균 지급수익금 ", content: msg, closeable: true, width: 160, height: 90, trigger: "click", placement: 'bottom', backdrop: false});
		var msg = "1금융권 정기예금 평균 금리 1.7% 대비 본 투자상품의 수익률입니다. (각 세후 실수익 기준)";
		$('#earninfo3-claim-mark').webuiPopover({ title: "은행에 예금시보다 ", content: msg, closeable: true, width: 160, height: 75, trigger: "click", placement: 'bottom', backdrop: false});
	</script>

	<? if($PRDT['core_invest_point']) { echo $PRDT['core_invest_point']; } ?>

	<!-- 상품개요 -->
	<div class="product_info">
		<div class="product_info_tit">상품 개요</div>
		<div>
			<ul class="p_i_t">
				<li>
					<p>투자모집액</p>
					<p><? echo price_cutting($PRDT['recruit_amount']);?>원</p>
				</li>
				<li>
					<p>투자수익률</p>
					<p>연 <?=$invest_return?>%</p>
				</li>
				<li>
					<p>투자기간</p>
					<p><?=$invest_period?><?=$invest_period_unit?></p>
				</li>
				<li>
					<p>상환방법</p>
					<p><?=$repay_pay_title?></p>
				</li>
			</ul>
			<div class="clearfix"></div>
			<? if($description = nl2br($PRDT['product_description'])) { // 상품설명 ?>
				<div class="p_i_t_i"><?=$description?></div>
			<? } ?>
		</div>
	</div>

	<!-- 실시간 현장 라이브 -->
	<? if($live_link) { ?>
	<div class="hello_tv">
		<img src="<?=G5_THEME_IMG_URL?>/sub_m/live_banner.jpg" alt="실시간 현장 방송" onClick="<?=$live_link?>">
	</div>
	<? } ?>

	<!-- 안전장치 업데이트 -->
	<?=(trim($PRDT['extend_8'])) ? $PRDT['extend_8'] : '';?>

	<div class="shinhan_ban"><img src="/theme/2018/img/sub_m/shinhan_ban01_m.jpg" width="100%" alt="신한은행"></div>

	<div id="detail_box" class="detail_box" >
		<?=(trim($PRDT['invest_summary_m']))? $PRDT['invest_summary_m'] : '';?>

		<!-- 증빙서류 -->
		<?=(trim($PRDT['extend_9'])) ? $PRDT['extend_9'] : '';?>

		<?=(trim($PRDT['extend_7'])) ? $PRDT['extend_7'] : '';?>
	</div>

<?
if($PRDT['loadview_url'] && preg_match('/\bkakao\b/i', $PRDT['loadview_url'] ,$matches)) {
	$tmp1 = explode("?",$PRDT['loadview_url']);
	parse_str($tmp1[1]);
	/*
	echo "panoid (고유값) = > $panoid<br/>";
	echo "pan (수평각) => $pan<br/>";
	echo "tile (수직각) => $tilt<br/>";
	echo "zoom (확대) => $zoom<br/>";
	*/
?>
	<script>
	function isFlashEnabled()
	{
		var hasFlash = false;
		try
		{
			var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
			if(fo) hasFlash = true;
		}
		catch(e)
		{
			if(navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) hasFlash = true;
		}
		return hasFlash;
	}
	var flash_yn = isFlashEnabled();
	</script>
	<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=a1a12feb2e53aac7f2424691b4532110"></script>
	<script>
		$(".prdt_summ").append("<div id='kakao_roadview' style='width:90%; height:300px;border:1px solid black;margin:10px auto 5px;'></div><div style='width:320px;margin:5px auto 30px;text-align:center;'>화면을 클릭한 후 상하좌우로 움직여서 현장을 확인하세요 !</div>");
		//로드뷰를 표시할 div
		var roadviewContainer = document.getElementById('kakao_roadview');
		flash_yn=true;
		if (flash_yn) {
			//로드뷰 객체를 생성한다
			var roadview = new kakao.maps.Roadview(roadviewContainer, {
				panoId : <?=$panoid?>, // 로드뷰 시작 지역의 고유 아이디 값
				pan: <?=$pan?>, // 로드뷰 처음 실행시에 바라봐야 할 수평 각
				tilt: <?=$tilt?>, // 로드뷰 처음 실행시에 바라봐야 할 수직 각
				zoom: <?=$zoom?> // 로드뷰 줌 초기값
			});
		} else {
			$("#kakao_roadview").css('background-image','url("/images/bg_pattern.jpg")');
			$("#kakao_roadview").html("<div style='text-align:center;width:90%;height:86px;margin:20% auto;'><b>로드뷰 서비스를 이용하시려면<br/>Adobe Flash Player 설치 및 허용이 필요합니다.<br/><br/><a href='http://get.adobe.com/flashplayer/' target='_blank'>[최신버전 다운로드]</a></b></div>");
		}
	</script>
<?
}
?>

<script type="text/javascript">
simulation(5000000);

// faq
$(".faq a").click(function() {
	$(this).next().slideToggle("fast").parent().siblings().children("dd").hide();
	return false;
});

// 상품자세히 보기 슬라이드
function detailShow() {
	var imgSrc = $("img.upDownBtn");
	$("#detail_box").slideToggle("fast", function(e) {
		if($(this).is(':visible')) {
			$("div#detail_btn a").css("background", "#1E88EC").css("color", "#FFF");
			imgSrc.attr("src", imgSrc.attr("src").replace("up_btn01", "down_btn01"));
		}
		else {
			$("div#detail_btn a").css("background", "#e7f3ff").css("color", "#000");
			imgSrc.attr("src", imgSrc.attr("src").replace("down_btn01", "up_btn01"));
		}
	});
}

// 라이브 티비
function popupOpen() {
	var popUrl = "live.html"; //팝업창에 출력될 페이지 URL
	var popOption = "width=640, height=494, top=250, left=600, resizable=no, scrollbars=no, status=no;"; //팝업창 옵션(optoin)
	window.open(popUrl,"",popOption);
}

var galleryTop;
var galleryThumbs;
$(document).ready(function() {
	// 사전투자 설명
	var msg = "펀딩오픈 시간에 투자참여가 어려운 회원분들을 위하여 사전에 투자할 수 있는 서비스입니다. <br><br> <strong>사전 투자 유의사항</strong> <br><br>본 상품은 사전 투자가 가능한 상품으로 목표금액의 <? echo (int)$PRDT['advance_invest_ratio']?>%까지 사전 투자가 진행됩니다. \
				<p>1. 사전 투자는 가상계좌의 예치금으로 투자 가능합니다.</p> \
				<p>2. 사전 투자는 신청순으로 적용됩니다.</p>";

	$('#question_1').webuiPopover({
		title: "사전 투자 서비스란?",
		content: msg,
		closeable: true,
		width: 330,
		trigger: "click",
		placement: 'bottom',
		backdrop: false
	});

	galleryTop = new Swiper('#gallery', {
		spaceBetween: 10,
		onSlideChangeEnd: function() {
			$(document).trigger("slide-change");
		},
		loopedSlides: $("#gallery .swiper-wrapper .swiper-slide").length,
		effect: "fade",
		observer: true,
		observeParents: true,
	});

	galleryThumbs = new Swiper('#gallery-thumbs', {
		spaceBetween: 10,
		centeredSlides: false,
		slidesPerView: 3,
		observer: true,
		observeParents: true,
	});

	// galleryTop.controller.control = galleryThumbs;
	// galleryThumbs.controller.control = galleryTop;

	$(document).on("click", "#gallery-thumbs .swiper-slide", function(e) {
		var index = $(this).index();
		galleryTop.slideTo(index);
	});

	setInterval(function() {
		if($('#invest_finished').val() == 'false') {
			$.ajax({
				type: "GET",
				url: "/investment/ajax_investment.php",
				dataType: "json",
				data: {prd_idx:<?=$prd_idx;?>},
				success: function(json) {
					// 3초간 데이터 조회
					// 바뀌는 값들 모집금액, 투자모집률, 남은 모집금액, 버튼들
					$('#invest_finished').val(json.data.invest_finished); // 현재진행상태
					$('#progressBar').attr('style', "width:" + json.data.progress_width); // 진행률 표시
					$('#progressData').text(json.data.progress); // 진행률
					$('#totalRecruitValue').text(json.data.total_invest_amount_k); // 현재 모집금액
				},
				error: function(e) { }
			});
		}
	}, 3 * 1000);
});

$("a[data-toggle='sns_share']").click(function(e) {
	e.preventDefault();
	var current_url = window.location.href;
	var _this       = $(this);
	var sns_type    = _this.attr('data-service');
	var href        = current_url;
	var title       = _this.attr('data-title');
	var img         = $("meta[name='og:image']").attr('content');
	var loc         = "";

	if( ! sns_type || !href || !title) return;

	if(sns_type == 'facebook') { loc = '//www.facebook.com/sharer/sharer.php?u='+href+'&t='+title; }
	else if(sns_type == 'twitter') { loc = '//twitter.com/home?status='+encodeURIComponent(title)+' '+href; }
	else if(sns_type == 'google') { loc = '//plus.google.com/share?url='+href; }
	else if(sns_type == 'pinterest') { loc = '//www.pinterest.com/pin/create/button/?url='+href+'&media='+img+'&description='+encodeURIComponent(title); }
	else if(sns_type == 'kakaostory') { loc = 'https://story.kakao.com/share?url='+encodeURIComponent(href); }
	else if(sns_type == 'band') { loc = 'http://www.band.us/plugin/share?body='+encodeURIComponent(title)+'%0A'+encodeURIComponent(href); }
	else if(sns_type == 'naver') { loc = "http://share.naver.com/web/shareView.nhn?url="+encodeURIComponent(href)+"&title="+encodeURIComponent(title); }
	else if(sns_type == 'url_copy') { copy_trackback(href); }
	else if(sns_type == 'instagram') { alert("현재 지원하지 않는 기능입니다."); loc = ""; return false; }
	else { return false; }

	if(sns_type != 'url_copy') { window.open(loc); }

	return false;
});

function copy_trackback(trb) {
	var IE=(document.all)?true:false;
	if(IE) {
		if(confirm("이 글의 트랙백 주소를 클립보드에 복사하시겠습니까?"))
			window.clipboardData.setData("Text", trb);
	} else {
		temp = prompt("이 글의 트랙백 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", trb);
	}
}

$(document).on("keyup", 'input:text[name="principal_value"]', function() {
	var earn_btn = $("p.earn_btn");
	if(earn_btn.css("display") == "block") {
		earn_btn.hide();
	}
});

// 예상수익금 계산
function simulation(price) {
	var price = (price || '0');
	var pattern = /^[0-9]+$/;
	var prd_idx = ($("input:hidden[name='prd_idx']").val() || 0);
	var principal_value = ($("input:text[name='principal_value']").val() || price).replace(/[\D\s\._\-]+/g, "");
	var min_invest_limit = (<?=$CONF['min_invest_limit']?> || 0);

	if(principal_value == "") {
		alert("투자 금액을 입력해주새요");
		$("input:text[name='principal_value']").focus();
		return;
	}
	if(!pattern.test(principal_value) ) {
		alert("투자 금액에 사용할수 없는 문자가 있습니다. 숫자만  입력해주세요.");
		$("input:text[name='principal_value']").focus();
		return;
	}

/*
	if(principal_value < min_invest_limit) {
		alert("최소 금액은 " + number_format(min_invest_limit) + "원 이상 입니다.");
		$("input:text[name='principal_value']").focus();
		return;
	}
*/

	if(principal_value >= <?=$CONF['min_invest_limit']?>) {
		$.ajax({
			url : g5_url + "/investment/ajax_simulation.php",
			type: "POST",
			data : {prd_idx: prd_idx, ajax_principal_value: principal_value, onlyInterest: 'Y'},
			success: function(data, textStatus, jqXHR)
			{
				if(data == "ERROR") {
					alert("시스템 오류입니다. 관리자에 문의해주세요.");
				}
				else if(data == "ERROR-MIN-PRICE") {
					alert("최소 금액은 " + number_format(min_invest_limit) + "원 이상 입니다.");
					$("input[name='principal_value']").focus();
					return;
				}
				else{
					var data = JSON.parse(data);
					if(data.success) {
						$("p.earn_btn").show();
						$("#ajxTotalInterestPrice").text(data.totalInterestPrice);
						$("#ajxInvestMonth").text(data.investMonth);
						$("#ajxMonthAvrPrice").text(data.monthAvrPrice);
						$("#ajaDiffEarning").text(data.diffEarning);
					}
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {

			}
		});
	}
}

function formatNumber(numberString) {
	var selection = window.getSelection().toString();
	if(selection !== '') {
		return;
	}

	if( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
		return;
	}
	var input = numberString.value;
	var input = input.replace(/[\D\s\._\-]+/g, "");
	input = input ? parseInt( input, 10 ) : 0;
	numberString.value = (input === 0 ) ? "" : input.toLocaleString('ko-KR', {maximumSignificantDigits : 21});
}
</script>

<?

// 라이브스트림 준비중 팝업
if($PRDT['stream_url1'] == 'ready') {
	include_once(G5_PATH.'/popup/inc_stream_ready.php');
}

include_once(G5_PATH . "/syndicate/oligo/web/tail_m.php");

?>