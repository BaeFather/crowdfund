<?

add_stylesheet('<link href="css/main.css" rel="stylesheet">', 0);
add_javascript('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css" rel="stylesheet" type="text/css">', 0);
add_javascript('<link href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css" rel="stylesheet" type="text/css">', 0);

?>
<link rel="stylesheet" href="css/main.css">
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

<script>
$(function() {
		var Accordion = function(el, multiple) {
				this.el = el || {};
				this.multiple = multiple || false;

				var links = this.el.find('.article-title');
				links.on('click', {
						el: this.el,
						multiple: this.multiple
				}, this.dropdown)
		}

		Accordion.prototype.dropdown = function(e) {
				var $el = e.data.el;
				$this = $(this),
						$next = $this.next();

				$next.slideToggle();
				$this.parent().toggleClass('open');

				if (!e.data.multiple) {
						$el.find('.accordion-content').not($next).slideUp().parent().removeClass('open');
				};
		}
		var accordion = new Accordion($('.accordion-container'), false);
});

$(document).on('click', function (event) {
  if (!$(event.target).closest('#accordion').length) {
    $(this).parent().toggleClass('open');
  }
});
</script>

<div id="web_loan">
	<div class="top_call">
		<ul>
			<li><a href="http://hellofunding.co.kr" target="_blank"><img src="img/logo.png"></a></li>
			<li class="call">대출문의<span> 1588-5210</span></li>
		</ul>
	</div>
	<div class="top_text">
		<p class="h4"><img src="img/h4.png"></p>
		<p class="h1"><img src="img/h1.png"></p>
		<p class="icon"><img src="img/icon.png"></p>
	</div>

	<div class="form">
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
					<li class="step1">
						<select id="gu" name="gu" class="gus" OnChange="check_form_send('dong',this.value);" required itemname="시/구 를 선택하여 주세요">
							<option value="">시/구 선택</option>
						</select>
					</li>
					<li class="step3">
						<select id="dong" name="dong" class="dongs" OnChange="check_form_send('apt_name',this.value);" required itemname="동을 선택하여 주세요">
							<option value="">동 선택</option>
						</select>
					</li>
				</ul>

				<div class="title">
					<ul class="radiobt1">
						<li class="step">아파트정보</li>
						<li class="pd"><label class="apt_radio"><input type="radio" name="rdo_apt" value="1" onclick="Rradio_OnOff('Radio_On');" checked><span>선택</span></label></li>
						<li class="pd"><label class="apt_radio"><input type="radio" name="rdo_apt" value="2" onclick="Rradio_OnOff('Radio_Off');"><span>직접입력</span></label></li>
					</ul>
				</div>

				<ul id="Radio_On" style="display:;">
					<li class="step2 clear">
						<select id="apt_name" name="apt_name" class="b_names" OnChange="check_form_send('apt_area',this.value);">
							<option value="">아파트 선택</option>
						</select>
					</li>
					<li class="step3">
						<select id="apt_area" name="apt_area" class="areas">
							<option value="">전용면적 선택 (㎡)</option>
						</select>
					</li>
					<li class="step2"><input class="dong_num" type="text" name="dong_num" value="" placeholder="동을 입력해주세요"></li>
					<li class="step2"><input class="floor_num" type="text" name="floor_num" value="" placeholder="층수을 입력해주세요"></li>
					<li class="step3"><input class="ho_num" type="text" name="ho_num" value="" placeholder="호수를 입력해주세요"></li>
				</ul>

				<ul id="Radio_Off" style="display: none;">
					<li class="step2 clear step2_off">
						<input class="detail_add" type="text" name="apt_name2" value="" placeholder="아파트명 및 상세주소를 입력해주세요  ex) 홍길동아파트 105동 11호">
					</li>
				</ul>

				<div class="btn1">
					<a href="javascript:void(0);" id="btn1" OnClick="check_w_form('wform',event);"><img src="img/top_bt.png"></a>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="box_reason">
	<p><img src="img/text_01.png"></p>
	<ul>
		<li><img src="img/box_01.png"></li>
		<li><img src="img/box_02.png"></li>
		<li><img src="img/box_03.png"></li>
		<li><img src="img/box_04.png"></li>
	</ul>
</div>

<div class="center_call">
	<p><img src="img/cs_call.png"></p>
</div>

<div id="acc_content">
	<p class="title"><img src="img/text_02.png"></p>
	<a href="https://www.hellofunding.co.kr/bbs/faq.php?fm_id=2" target="_blank"><p class="more">더보기 ></p></a>
	<div id="accordion" class="accordion-container">
		<article class="content-entry">
			<h4 class="article-title"><i></i>대출심사 신청요건은 무엇인가요?</h4>
			<div class="accordion-content">
				<p>헬로펀딩은 투자자 안전을 위해 담보의 권리설정이 가능한 부동산, 동산, 준공자금, 확정매출채권 등 위주로 투자상품을 출시하며, 해당 자료를 홈페이지의 '대출신청하기' 페이지에서 접수하시면 신청이 완료됩니다.</p>
			</div>
		</article>
		<article class="content-entry">
			<h4 class="article-title"><i></i>대출신청시 어떤 서류를 제출해야 하나요?</h4>
			<div class="accordion-content">
				<p>홈페이지의 '대출신청하기'를 통해 대출신청서를 작성해 주시면, 대출 심사팀에서 검토 후 개별 연락드립니다.<br>
					 개별 연락을 통해 대출상품에 따른 필요 정보 및 제반 서류 안내를 해드리며,
					 제출하신 서류를 토대로 최종심사를 진행하게 됩니다.</p>
			</div>
		</article>

		<article class="content-entry">
			<h4 class="article-title"><i></i>한도조회 또는 대출을 받으면 신용등급에 영향을 주나요?</h4>
			<div class="accordion-content">
				<p>P2P를 통한 대출신청 및 대출시 신용조희 기록이 남지 않고, 신용 등급에도 영향을 주지 않습니다.<br>
				다만, 연체 발생시 투자자 보호를 위해 금융기관에 보고되며, 신용등급 하락 등 기타 불이익을 받을 수 있습니다.</p>
			</div>
		</article>
	</div>
</div>

<div class="btn2">
	<a href="javascript:void(0);" id="btn2" OnClick="check_w_form('wform',event);"><img src="img/top_bt.png"></a>
</div>