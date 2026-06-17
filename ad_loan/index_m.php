<?

add_stylesheet('<link href="css/m_main.css" rel="stylesheet">', 0);
add_javascript('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css" rel="stylesheet" type="text/css">', 0);
add_javascript('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css" rel="stylesheet" type="text/css">', 0);

?>
<link rel="stylesheet" href="css/m_main.css">
<script type="text/javascript">
function Rradio_OnOff(id)
{
	if(id == "Radio_On")
	{
		document.all["Radio_On"].style.display = '';
		document.all["Radio_Off"].style.display = 'none';
	}
	else
	{
		document.all["Radio_On"].style.display = 'none';
		document.all["Radio_Off"].style.display = '';
	}
}
</script>

<script type="text/javascript">
	var si =	"";
	var gu =	"";
	var dong =	"";
	var danji =	"";
	var strlink = "";
</script>

<script type="text/javascript" src="./aptloan.js"></script>

<script>
	check_form_proc("kind=si", event);
</script>

<div id="loan">

	<div class="top_call">
		<ul>
			<li><img src="img/logo.png"></li>
			<li class="cs_call"><a href="tel:1588-5210">대출문의 <span>1588-5210</span></a></li>
		</ul>
	</div>

	<div class="visual_img"><img src="img/visual.png"></div>

	<form name="wform" id="wform">
		<input type="hidden" name="kind" value="auth" />
		<input type="hidden" name="price" value="" />
		<input type="hidden" name="rprice" value="" />
		<input type="hidden" name="pid" value="<?php ECHO $strP;?>" />


		<div class="loan4">
			<div class="loan_form">
			<div class="title"><p class="step">지역정보</p></div>
			<ul>
				<li class="step1">
					<select id="si" name="si" class="citys" OnChange="check_form_send('gu',this.value);" required itemname="광역시/도를 선택하여 주세요">
						<option value="">광역시/도 선택</option>
					</select>
				</li>
				<li class="step2">
					<select id="gu" name="gu" class="gus" OnChange="check_form_send('dong',this.value);" required itemname="시/구를 선택하여 주세요">
						<option value="">시/구 선택</option>
					</select>
				</li>
				<li class="step3">
					<select id="dong" name="dong" class="dongs" OnChange="check_form_send('apt_name',this.value);" required itemname="동을 선택하여 주세요">
						<option value="">동 선택</option>
					</select>
				</li>
			</ul>

			<ul class="radiobt1">
				<li><p class="step">아파트정보</p></li>
				<li><label class="apt_radio"><input type="radio" name="rdo_apt" value="1" onclick="Rradio_OnOff('Radio_On');" checked><span>선택</span></label></li>
				<li class="pd"><label class="apt_radio"><input type="radio" name="rdo_apt" value="2" onclick="Rradio_OnOff('Radio_Off');"><span>직접입력</span></label></li>
			</ul>

			<ul id="Radio_On" style="display:;">
				<li class="step1 clear">
					<select id="apt_name" name="apt_name" class="b_names" OnChange="check_form_send('apt_area',this.value);">
						<option value="">아파트 선택</option>
					</select>
				</li>
				<li class="step2">
					<select id="apt_area" name="apt_area" class="areas">
						<option value="">면적 선택 (㎡)</option>
					</select>
				</li>
				<li class="step3"><input class="dong_num" type="text" name="dong_num" value="" placeholder="동 입력"></li>
				<li class="step2"><input class="floor_num" type="text" name="floor_num" value="" placeholder="층수 입력"></li>
				<li class="step3 last"><input class="ho_num" type="text" name="ho_num" value="" placeholder="호수 입력"></li>
			</ul>

			<ul id="Radio_Off" style="display: none;">
				<li class="step1 clear last">
					<input class="detail_add" type="text" name="apt_name2" value="" placeholder="ex) 홍길동아파트 105동 11호">
				</li>
			</ul>

			<div class="btn1">
				<a href="javascript:void(0);" id="btn1" OnClick="check_w_form('wform',event);">30초만에 한도 확인 가능, <span>클릭▶</span></a>
			</div>
		</form>
	</div>

</div>

<div class="bar"></div>

<div class="reason">
	<h2><span>많은 분들이 헬로펀딩 아파트 담보대출을</span><br>선호하는 이유?</h2>
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<div class="swiper-slide">
				<ul>
					<li class="title purple fw600">신용등급, 재직여부 상관없이</li>
					<li class="text gray">only, 담보가치만 평가</li>
					<li><img src="img/m_box_01.png"></li>
				</ul>
			</div>
			<div class="swiper-slide">
				<ul>
					<li class="title purple fw600">대출한도 최대 10억</li>
					<li class="text gray">LTV 83%까지, 금융권의 2배 이상</li>
					<li><img src="img/m_box_02.png"></li>
				</ul>
			</div>
			<div class="swiper-slide">
				<ul>
					<li class="title purple fw600">낮은 금리 5.9% ~ 9.9%</li>
					<li class="text gray">합리적인 중금리 지향</li>
					<li><img src="img/m_box_03.png"></li>
				</ul>
			</div>
			<div class="swiper-slide">
				<ul>
					<li class="title purple fw600">쉽고 빠른 대출</li>
					<li class="text gray">30초 대출신청, 심사 후 평균 2일 이내</li>
					<li><img src="img/m_box_04.png"></li>
				</ul>
			</div>
		</div>
		<div class="swiper-pagination"></div>
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
	</div>
</div>

<script>
var mySwiper = new Swiper('.swiper-container', {
	// 슬라이드를 버튼으로 움직일 수 있습니다.
	navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev',
	},

	// 현재 페이지를 나타내는 점이 생깁니다. 클릭하면 이동합니다.
	pagination: {
		el: '.swiper-pagination',
		type: 'bullets',
	},

	// 3초마다 자동으로 슬라이드가 넘어갑니다. 1초 = 1000
	autoplay: {
		delay: 2000,
	},
});
</script>
