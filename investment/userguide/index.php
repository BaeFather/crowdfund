<?
include_once('./_common.php');


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


?>

<title>이용가이드</title>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>


<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
<link rel="stylesheet" type="text/css" href="style.css">



<!--웹 탭메뉴 스크립트-->
<script>
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


<body>
	<!--------------------웹 시작----------------------------------------------------------->
	<div class="userguide-wrap">
		<div class="page-title">
			<h3>이용가이드</h3>
			<p>헬로펀딩은 온라인투자연계금융 플랫폼입니다. 간편한 투자, 안정적인 수익을 경험해보세요.</p>
			<ul class="tabs">
				<li class="tab-link current" data-tab="tab-1">회원가입</li>
				<li class="tab-link" data-tab="tab-2">본인확인</li>
				<li class="tab-link" data-tab="tab-3">투자준비</li>
				<li class="tab-link" data-tab="tab-4">투자</li>
				<li class="tab-link" data-tab="tab-5">투자완료</li>
				<li class="tab-link" data-tab="tab-6">상환 및 출금</li>
			</ul>
		</div>
	
		<!------------회원가입------------------------------------------------------------------->
	  	<div id="tab-1" class="tab-content current">
			<div class="step-01">
				<p class="slide-tit">1. 회원가입</p>
				<p class="slide-txt">회원가입을 해주세요. 개인회원과 법인회원으로 구분됩니다.</p>
				<img src="img/step_01.png" alt="회원가입">
				<p class="info-txt">자세한 내용은 공지사항을 참조하여 주세요. <a href="/bbs/board.php?bo_table=notice&wr_id=998" target="_blank"><span class="red-font link-font">[공지사항 바로가기]</span></a></p>
			</div>
		</div>	
	
		<!------------본인확인------------------------------------------------------------------->		
		<div id="tab-2" class="tab-content" >
			<div class="step-02">
				<p class="slide-tit">2. 본인확인</p>
				<p class="slide-txt">개인 회원 가입 완료 후 금융거래를 위한 본인확인을 진행해 주세요. <br>
									<span class="red-font">신분증 확인, 본인 계좌 확인, 필수 정보 확인</span>등의 본인확인이 진행됩니다.
									<span class="small-font">(온투법 제21조 투자자에 대한 정보 확인 등)</span><br>
									<span class="red-font">본인확인이 완료되면 투자를 위한 가상 계좌가 발급</span>됩니다.
									<br><br>
									신분증 인식 불가, 은행 앱, 인터넷 뱅킹 사용 불가 등 본인확인이 어려운 회원님께서는
									<a href="/bbs/board.php?bo_table=notice&wr_id=1000" target="_blank"><span class="black-font link-font">공지사항</span></a>을 참고하여 본인확인 진행이 가능합니다.</p>
				<img src="img/step_02.png" alt="본인확인">
			</div>
		</div>	

		<!------------투자준비------------------------------------------------------------------->		
		<div id="tab-3" class="tab-content">
			<div class="step-03">
				<p class="slide-tit">3. 투자준비</p>
				<p class="slide-txt">가상 계좌를 발급받으셨다면 투자를 위한 예치금을 입금해주세요.<br>
									<span class="red-font">헬로펀딩의 가상 계좌는 신한은행 명의의 신탁계좌로 회원님의 투자 전용 가상 계좌</span>입니다.<br>
									입금된 예치금으로 상품에 투자가 가능합니다.</p>
				<img src="img/step_03.png" alt="투자준비">
			</div>
		</div>
		
		
		<!------------투자------------------------------------------------------------------->		
		<div id="tab-4" class="tab-content">
			<div class="step-04">
				<p class="slide-tit">4. 투자</p>
				<p class="slide-txt">예치금이 준비되었다면 투자를 진행해주세요.<br>
									헬로펀딩의 모든 투자상품은 모집시작 최소 1시간 전부터 최대 48시간 전에 공시되며 <br>
									<span class="red-font">카카오톡 플러스 친구를 추가하시면 상품 오픈일에 안내 메시지를 발송</span>해드립니다.
									<a href="https://pf.kakao.com/_xgAdWu" target="_blank"><span class="black-font link-font">(카카오톡 친구추가 바로가기)</span></a>
									<br><br>	
									투자는 상품 별 정해진 시간부터 가능하며 대출 실행 후 투자 취소는 불가능하오니 상품설명을 꼼꼼히 확인해 보시고<br>
						  			투자를 결정해 주세요.
									<br><br>
									투자금액은 최소 1만원부터 만원 단위로 설정이 가능하며 투자 가능 금액은 투자자마다 상이하므로<br>
						  			투자하기 전 투자 가능한도를 확인해 주세요.</p>
				<img src="img/step_04.png" alt="투자" usemap="#Map" border="0">
                <map name="Map">
                  <area shape="rect" coords="433,237,705,287" href="/bbs/faq.php?fm_id=4" target="_blank";>
                </map>
				<p class="info-txt">투자자 유형별 투자 한도를 확인해 주세요. <a href="/investment/investor/" target="_blank"><span class="red-font link-font">[투자 유형 안내 바로가기]</span></a></p>
			</div>
		</div>
		
		
		<!------------투자완료------------------------------------------------------------------->		
		<div id="tab-5" class="tab-content">
			<div class="step-05">
				<p class="slide-tit">5. 투자완료</p>
				<p class="slide-txt">투자가 완료되면 ‘투자현황’ 메뉴에서 확인이 가능합니다.<br>
									전액 모집 완료 이후 대출이 실행되므로 투자 완료일과 대출 실행일 간 차이가 발생할 수 있습니다.<br>
									대출이 실행되면 상환예정일과 수익금 지급 일정을 확인할 수 있습니다.</p>
				<img src="img/step_05.png" alt="투자완료">
			</div>
		</div>
		
		
		<!------------상환 및 출금------------------------------------------------------------------->		
		<div id="tab-6" class="tab-content">
			<div class="step-06">
				<p class="slide-tit">6. 상환 및 출금</p>
				<p class="slide-txt">헬로펀딩의 정기 수익금 지급일은 매달 5일에 예치금 형태로 지급되며 지급된 예치금은 즉시 재투자와 출금이 가능합니다.<br>
						 			<span class="small-font">(* SCF 상품은 상환 시 원금과 수익금이 함께 지급됩니다.)</span><br>
									예치금 출금 신청 시 사전에 등록된 본인 계좌로 출금되며 출금 계좌는 회원정보에서 언제든 변경이 가능합니다.</p>
				<img src="img/step_06.png" alt="상환 및 출금">
			</div>
		</div>
	</div>		
		
		
		
		
		

	
	<!--------------------모바일 시작----------------------------------------------------------->
	<div class="m-userguide-wrap">
		<div class="page-title">
			<h3>이용가이드</h3>
			<p>간편한 투자, 안정적인 수익을 경험해보세요.</p>
		</div>
		
		<div class="swiper mySwiper m-swiper">
			<div class="swiper-pagination"></div>	
			<div class="swiper-wrapper">
				<div class="swiper-slide">
					<div class="step-01">
						<p class="slide-tit">1. 회원가입</p>
						<p class="slide-txt">회원가입을 해주세요. 개인회원과 법인회원으로 구분됩니다.</p>
						<img src="img/m_step_01.png" alt="회원가입">
						<p class="info-txt">자세한 내용은 공지사항을 참조하여 주세요. <a href="/bbs/board.php?bo_table=notice&wr_id=998" target="_blank"><span class="red-font link-font">[공지사항 바로가기]</span></a></p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="step-02">
						<p class="slide-tit">2. 본인확인</p>
						<p class="slide-txt">개인 회원 가입 완료 후 금융거래를 위한 본인확인을 진행해 주세요. <span class="red-font">신분증 확인, 본인 계좌 확인, 필수 정보 확인</span>등의 본인확인이 진행됩니다.
											<span class="small-font">(온투법 제21조 투자자에 대한 정보 확인 등)</span><br><span class="red-font">본인확인이 완료되면 투자를 위한 가상 계좌가 발급</span>됩니다.
											<br><br>
											신분증 인식 불가, 은행 앱, 인터넷 뱅킹 사용 불가 등 본인확인이 어려운 회원님께서는 <a href="/bbs/board.php?bo_table=notice&wr_id=1000" target="_blank"><span class="black-font link-font">공지사항</span></a>을 참고하여 본인확인 진행이 가능합니다.</p>
						<img src="img/m_step_02.png" alt="본인확인">
					</div>
				</div>
				<div class="swiper-slide">
					<div class="step-03">
						<p class="slide-tit">3. 투자준비</p>
						<p class="slide-txt">가상 계좌를 발급받으셨다면 투자를 위한 예치금을 입금해주세요.<br>
											<span class="red-font">헬로펀딩의 가상 계좌는 신한은행 명의의 신탁계좌로 회원님의 투자 전용 가상 계좌</span>입니다.<br>
											입금된 예치금으로 상품에 투자가 가능합니다.</p>
						<img src="img/m_step_03.png" alt="투자준비">
					</div>
				</div>
				<div class="swiper-slide">
					<div class="step-04">
						<p class="slide-tit">4. 투자</p>
					  <p class="slide-txt">예치금이 준비되었다면 투자를 진행해주세요.<br>
											헬로펀딩의 모든 투자상품은 모집시작 최소 1시간 전부터 최대 48시간 전에 공시되며
						<span class="red-font">카카오톡 플러스 친구를 추가하시면 상품 오픈일에 안내 메시지를 발송</span>해드립니다. <br>
						<a href="https://pf.kakao.com/_xgAdWu" target="_blank"><span class="black-font link-font">(카카오톡 친구추가 바로가기)</span></a>
											<br><br>	
											투자는 상품 별 정해진 시간부터 가능하며 대출 실행 후 투자 취소는 불가능하오니
											상품설명을 꼼꼼히 확인해 보시고 투자를 결정해 주세요.
											<br><br>
											투자금액은 최소 1만원부터 만원 단위로 설정이 가능하며
											투자 가능 금액은 투자자마다 상이하므로 투자하기 전 투자 가능한도를 확인해 주세요.</p>
						<img src="img/m_step_04.png" alt="투자">
						<a href="/bbs/faq.php?fm_id=4" target="_blank"><img src="img/m_step_04_bt.jpg" alt="투자"></a>
						<img src="img/m_step_04_bottom.jpg" alt="투자">
						<p class="info-txt">투자자 유형별 투자 한도를 확인해 주세요. <a href="/investment/investor/"  target="_blank"><span class="red-font link-font">[투자 유형 안내 바로가기]</span></a></p>
					</div>
				</div>
				<div class="swiper-slide">
					<div class="step-05">
						<p class="slide-tit">5. 투자완료</p>
					  <p class="slide-txt">투자가 완료되면 ‘투자현황’ 메뉴에서 확인이 가능합니다.<br>
											전액 모집 완료 이후 대출이 실행되므로 투자 완료일과 대출 실행일 간 차이가 발생할 수 있습니다.<br>
											대출이 실행되면 상환예정일과 수익금 지급 일정을 확인할 수 있습니다.</p>
						<img src="img/m_step_05.png" alt="투자완료">
					</div>
				</div>
				<div class="swiper-slide">
					<div class="step-06">
						<p class="slide-tit">6. 상환 및 출금</p>
					  <p class="slide-txt">헬로펀딩의 정기 수익금 지급일은 매달 5일에 예치금 형태로 지급되며 지급된 예치금은 즉시 재투자와 출금이 가능합니다.<br>
						 				   <span class="small-font">(* SCF 상품은 상환 시 원금과 수익금이 함께 지급됩니다.)</span><br>
										   예치금 출금 신청 시 사전에 등록된 본인 계좌로 출금되며 출금 계좌는 회원정보에서 언제든 변경이 가능합니다.</p>
						<img src="img/m_step_06.png" alt="상환 및 출금">
					</div>
				</div>

			</div>
    	</div>
	</div>
	<!--------------------모바일 끝----------------------------------------------------------->
	<!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

	<!--슬라이드 볼 버튼-->
	<script>
	var menu = ['회원가입', '본인확인', '투자준비', '투자', '투자완료', '상환 및 출금']
	var mySwiper = new Swiper ('.m-swiper', {
    // If we need pagination
    pagination: {
      el: '.swiper-pagination',
			clickable: true,
        renderBullet: function (index, className) {
          return '<span class="' + className + '">' + (menu[index]) + '</span>';
        },
    },

    // Navigation arrows
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  })
	</script>
	
	
</body>


<!------------웹------------------------------------------------------------------->



<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>