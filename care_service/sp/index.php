<?
include_once('./_common.php');


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

// 자동 카운트 js
add_javascript('<script src="../../theme/2018/js/jquery.counterup.min.js"></script>', 0);
add_javascript('<script src="../../theme/2018/js/wow.min.js"></script>', 0);
add_javascript('<script src="//cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>', 0);
add_javascript('<script src="//cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>', 0);
?>

<link rel="stylesheet" type="text/css" href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css">
<link rel="stylesheet" type="text/css" href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="style.css">


<div id="Corp">
	<!--------------------상단------------------------------------------------------------>	
	<div class="top">
		<h1 class="top_title"><img src="img/title.png"></h1>
		<h1 class="top_m_title"><img src="img/m_title.png"></h1>
		<button class="bt" onclick="bt('1')">무료상담 신청하기</button>
	</div>
	<div class="point">
		<div class="point_01">
			<div class="picto_img"><img src="img/picto_01.png"></div>
			<div class="title">투자 한도 무제한<br><span class="ex">한도 제한 없이 모든 상품에 투자 가능 <br class="br">(개인회원 : 업권 전체 3천만원)</span></div >
			
		</div>
		<div class="point_02">
			<div class="picto_img"><img src="img/picto_02.png"></div >
			<div class="title">세금 절감 혜택<br><span class="ex">법인세율 적용으로 세금 절감이 가능 <br class="br">(대부법인은 원천징수 면제)</span></div >
		</div>
		<div class="point_03">
			<div class="picto_img"><img src="img/picto_03.png"></div >
			<div class="title">세무법인 세무 상담<br><span class="ex">P2P 전문 세무 법인을 통한 법인 설립<span class="space">&nbsp;</span><br class="br">및 세무 상담 가능</span></div>
		</div>
	</div>

	
	<!--------------------투자현황---------------------------------------------------------->
	<div class="data">
		<h3>법인 투자 현황</h3>
		<p>헬로펀딩의 안정적인 상품 투자로 많은 법인이 이자수익을 얻고 있습니다.</p>
		<div class="contents">
			<!--div class="graph"><img src="img/graph.png"></div-->
			<div class="graph-wrap">
				<ul>
					<li>
						<div class="graph-data">
							<p>213</p>
						</div>
						<p class="graph-year">2017</p>
					</li>
					<li>
						<div class="graph-data">
							<p>522</p>
						</div>
						<p class="graph-year">2018</p>
					</li>
					<li>
						<div class="graph-data">
							<p>1,011</p>
						</div>
						<p class="graph-year">2019</p>
					</li>
					<li>
						<div class="graph-data">
							<p>1,808</p>
						</div>
						<p class="graph-year">2020</p>
					</li>
					<li class="active">
						<div class="graph-data">
							<p>2,394</p>
						</div>
						<p class="graph-year">2021</p>
					</li>
				</ul>
			</div>
			<div class="detail">
				<ul>
					<li>
						<p class="navy">법인 누적 투자금액</p>
						<p class="navy_font"><span class="counter">2,394</span>억원</p>
					</li>
					<li>
						<p class="purple">법인 재투자율</p>
						<p class="purple_font"><span class="counter">83</span>%</p>
					</li>
					<li>
						<p class="purple">연 평균 수익률</p>
						<p class="purple_font"><span class="counter">12.72</span>%</p>
					</li>
					<li>
						<p class="navy">법인 투자 상환율</p>
						<p class="navy_font"><span class="counter">95</span>%</p>
					</li>
				</ul>
				<span class="date">2021.11 기준</span>
			</div>	
			
		</div>	
	</div>
	
	<!--------------------자주하는질문-------------------------------------------------------->	
	<div class="qa">
		<h3>자주 하는 질문</h3>
			<ul id="accordion" class="accordion">
			  <li>
				<div class="link">법인회원 등록절차가 어떻게 되나요?<i class="fa fa-chevron-down"></i></div>
				<p class="answer">헬로펀딩 홈페이지를 통해 회원가입 후 법인확인 절차를 완료해 주시면 됩니다. <br><br><a href="https://www.hellofunding.co.kr/bbs/board.php?bo_table=notice&wr_id=998">고객확인 안내 바로가기</a></p>
			  </li>
			   <li>
				<div class="link">법인 투자한도는 업종 제한이 없나요?<i class="fa fa-chevron-down"></i></div>
				<p class="answer">법인투자자의 경우 업종 제한 없이 무제한(상품당 모집금액의 40%까지)으로 투자가 가능합니다.</p>
			  </li>
			   <li>
				<div class="link">법인의 투자세율은 어떻게 되나요?<i class="fa fa-chevron-down"></i></div>
				<p class="answer">헬로펀딩은 법인세법 제 73조에 의해 소득세 25%에 주민세 2.5%가 추가되어 총 27.5%의 세금을 원천징수하며 <br class="br">추후 법인세율 과세표준 구간에 따라 추가 납부된 세금은 환급받을 수 있습니다.<br><br>
				 *과세표준구간별 세율 <br>
				- 이익금액 2억 원 이하 : 11%(소득세 10% 지방세 1%)<br>
				- 이익금액 200억 원 이하 : 22%(소득세 20% 지방세 2%)</p>
			  </li>
			  <li>
				<div class="link">헬로펀딩에서 법인 설립이 가능한가요?<i class="fa fa-chevron-down"></i></div>
				<p class="answer">헬로펀딩과 제휴된 온투업 투자 전문 세무 법인에서 상담부터 설립까지 도와드리며 <br class="br">추후 세무처리에 필요한 원천징수영수증 및 기타 서류를 알아서 진행해 해드립니다.</p>
			  </li>
			</ul>
	</div>
	
	<!--------------------신청 폼---------------------------------------------------------->
	<div class="form_box"  id="request1">
		<h3>법인 투자 무료상담 신청</h3>
		<p>법인 설립 또는 투자 안내를 원하시면 지금 바로 상담 신청해주세요.</p>
		<div class="contents">
			<form id="counsel_request" name="counsel_request">
				<table class="apply_option">
					<tr>
						<th>성명</th>
						<td><input type="text" name="name" value="<?=$member['mb_name']?>" placeholder="예: 홍길동"></td>
					</tr>
					<tr>
						<th>연락처</th>
						<td><input type="text" name="phone" onKeyup="onlyDigit(this);" placeholder="‘-’을 제외하고 입력해주세요" maxlength="12"></td>
					</tr>
					<tr>
						<th>이메일</th>
						<td><input type="text" name="email"></td>
					</tr>
					<tr>
						<th>법인 설립여부</th>
						<td>
							<input type="radio" id="est_n" name="est" value="N"><label for="est_n">설립예정</label>&nbsp;&nbsp;&nbsp;
							<input type="radio" id="est_y" name="est" value="Y"><label for="est_y">설립완료</label>
						</td>
					</tr>
					<tr class="note">
						<th>문의내용</th>
						<td><textarea type="textarea" name="content" class="cont_area"></textarea>
					</tr>
				</table>
				<div class="agree-chk">
					<input type="checkbox" id="is_agree" value="1"><label for="is_agree">개인 정보 수집 및 이용에 동의합니다.</label>
				</div>
				<ul class="agree-txt">
					<li>개인정보의 수집 및 이용목적 : 투자상담 서비스 제공</li>
					<li>수집 및 이용할 개인정보의 내용 : 성명, 연락처, 이메일</li>
					<li>개인정보의 보유 및 이용기간 : 서비스 목적 달성시 까지</li>
				</ul>
				<a class="apply-btn" onclick="formSubmit();">무료상담 신청하기</a>
			</form>
		</div>
	</div>
</div>


<script>

$(document).ready(function() {
	// 숫자 자동 증가 카운트
	$('.counter').counterUp({ delay: 10, time: 2000 });

	// 그래프 효과
	$(window).scroll(function() {
		var ofs = $(".data").offset();

		if(ofs) {
			$('.graph-data').each(function(i) {
				$('.graph-data:eq(0)').css({'transition':'all ease 0.5s', 'height':'50px'}).find('p').css('opacity','1');
				$('.graph-data:eq(1)').css({'transition':'all ease 0.8s', 'height':'90px'}).find('p').css('opacity','1');
				$('.graph-data:eq(2)').css({'transition':'all ease 1.1s', 'height':'140px'}).find('p').css('opacity','1');
				$('.graph-data:eq(3)').css({'transition':'all ease 1.4s', 'height':'180px'}).find('p').css('opacity','1');
				$('.graph-data:eq(4)').css({'transition':'all ease 1.7s', 'height':'230px'}).find('p').css('opacity','1');
			});
		}
		
		

	});
});


// 자주 하는 질문 
$(function() {
	var Accordion = function(el, active) {
		this.el = el || {};
		active = active || 0;
		var that = this;
		var links = this.el.find('.link');
		links.each(function(i){
			var link = links.eq(i);
			if (link.next().length === 0) { link.find('.fa').hide(); }
			link.on('click', { link: link }, that.dropdown);
		});
		if (active > 0) {
		   links.eq(active - 1).trigger('click');     
		}
	}

	Accordion.prototype.dropdown = function(e) {
		e.preventDefault();
		var $this = e.data.link;
		$this.parent()
			.siblings('.open').find('.answer').slideUp()
		.addBack().removeClass('open');
		$this.parent()
			.toggleClass('open')
			.find('.answer').stop().slideToggle();
	};

	var accordion = new Accordion($('#accordion'), 0);
});

// 버튼 클릭시 스크롤 속도
function bt(seq){
	var offset = $("#request" + seq).offset(); 
	$('html, body').animate({scrollTop : offset.top}, 400);
}

// 이메일 형식 체크
function email_check(email) {    
    var regex=/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return (email != '' && email != 'undefined' && regex.test(email)); 
}

</script>


<script type="text/javascript">
function formSubmit() {
	var f = document.counsel_request;
	_name = f.name.value;
	_phone = f.phone.value;
	_email = f.email.value;
	_est = f.est.value;
	_content = f.content.value;

	var params = $('#counsel_request').serialize();

	if(!trim(_name)) { alert("성명을 입력해 주세요."); f.name.focus(); return; }
	else if(!trim(_phone)) { alert("연락처를 입력해 주세요."); f.phone.focus(); return; }
	else if(!trim(_email)) { alert("이메일을 입력해 주세요."); f.email.focus(); return; }
	else if(!email_check(_email)) {alert("잘못된 형식의 이메일 주소입니다."); f.email.focus(); return; }
	else if(!f.est[0].checked && !f.est[1].checked) { alert("법인 설립여부를 체크해 주세요."); f.est[0].focus(); return; }
	else if(!trim(_content)) { alert("문의내용을 입력해 주세요."); f.content.focus(); return; }
	else if($("input:checkbox[id='is_agree']").is(":checked")==false) { alert('개인 정보 수집 및 이용에 동의해 주세요.'); return; }
	else {
		if( confirm('무료상담 신청을 등록하시겠습니까?') ) {
			$.ajax({
				url:'./request_proc.php',
				type:'POST',
				dataType:'JSON',
				data:params
			})
			.done(function(data) {
				if(data.result=='SUCCESS') {
					alert('등록 되었습니다.\n\n내용 확인 후 빠른 회신 드리겠습니다.\n\n감사합니다.');
					f.reset();
					return;
				}
				else { alert(data); return; }
			})
		}
	}
}
</script>


<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>