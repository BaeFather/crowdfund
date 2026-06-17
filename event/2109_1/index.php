<?
include_once('./_common.php');


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2021-09-01";
$event_end_date   = "2021-09-30";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";

}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
}
else {
	$join_link = "/investment/invest_list.php";
}


?>


<link href="event.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>




<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->



<div id="content">

	<div id="event">
		<div class="event_ing">진행중 이벤트<span>2021.09.01 ~ 2021.09.30</span></div>
		<div class="event_title">헬로펀딩 2021년 09월 투자 이벤트</div>
		<div><img src="img/banner.jpg" alt="투자 이벤트"></div>
		
		<div class="event4">
			<ul>
				<li class="num"><img src="img/title_01.jpg" alt="첫투자 이벤트"></li>
				<li class="title_line"></li>
			</ul>
		</div>
		<div class="event_text">첫 투자로 주택담보 상품에 100만원 이상 투자 시<br><span>스타벅스 아메리카노 쿠폰 지급</span></div>
		<div class="event_date">
			<ul>
				<li class="sub">참여기간</li>
				<li class="text">2021년 09월 01일 ~ 2021년 09월 30일</li>
			</ul>
		</div>
		<div class="event_target">
			<ul>
				<li class="sub">참여대상</li>
				<li class="text">2021년 08월 31일까지 투자 이력이 없는 기존회원 및 신규회원<br><span>(부동산, 헬로페이 투자 이력이 있어도 주택담보상품 투자 이력이 없으면 OK)</span></li>
			</ul>
		</div>
		<div class="event_how">
			<ul>
				<li class="sub">참여방법</li>
				<li class="text">참여 기간 내 <span>주택담보 상품에 첫 투자로 100만원 이상</span> 투자완료 시 자동 참여</li>
			</ul>
		</div>
		<div class="event_pay">
			<ul>
				<li class="sub">지급방법</li>
				<li class="text">2021년 10월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		
		
		<div class="event10">
			<ul>
				<li class="num"><img src="img/title_02.jpg" alt="주택담보상품 투자 이벤트"></li>
				<li class="title_line"></li>
			</ul>
		</div>
		<div class="event_text2">주택담보 상품에 누적 300만원 이상 투자 시<br><span>CU모바일상품권 1만원 지급</span></div>
		<div class="event_date">
			<ul>
				<li class="sub">참여기간</li>
				<li class="text">2021년 09월 01일 ~ 2021년 09월 30일</li>
			</ul>
		</div>
		<div class="event_target">
			<ul>
				<li class="sub">참여대상</li>
				<li class="text">참여 기간 내 주택담보 상품에 누적 300만원 이상 투자한 기존회원 및 신규회원</span></li>
			</ul>
		</div>
		<div class="event_how">
			<ul>
				<li class="sub">참여방법</li>
				<li class="text">참여 기간 내 <span>주택담보 상품에 누적 300만원 이상</span> 투자완료 시 자동 참여</li>
			</ul>
		</div>
		<div class="event_pay">
			<ul>
				<li class="sub">지급방법</li>
				<li class="text">2021년 10월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		
		
		<div class="bt"><a href="<?=$join_link;?>"><img src="img/bt01.jpg" alt="투자하러 가기"></a></div>

		<div class="event_notice">꼭! 읽어주세요. </div>
		<div class="event_notice_text2">				
				<p>본 이벤트는 참여 기간 내 오픈한 주택담보 상품 투자만 해당되며, 중복 참여가 가능합니다.</p>
		</div>
		<div class="event_notice_text">				
			    <p>· 투자를 취소하거나 투자한 상품의 모집이 취소될 경우 해당 투자금은 이벤트 대상 투자금액에서 제외됩니다.</p>
				<p>· 투자금 적용은 투자상품의 투자 완료일을 기준으로 합니다.</p>
				<p>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타제휴 광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p>· 경품 지급 시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</p>
				<p>· 이벤트 경품 지급은 참여 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다.</p>
				<p>· 본 이벤트는 당사 사정에 의해 조기 종료 및 연장될 수 있습니다.</p>
				<p>· 준법감시인 심사필 제2021-C-2 (2021.08.30)</p>
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	
</div>			  





<!--------------------모바일----------------------------------------------------------->


<div id="content_m">

	<div id="event_m">
		<div class="event_ing">진행중 이벤트<span>2021.09.01 ~ 2021.09.30</span></div>
		<div class="event_title">헬로펀딩 09월 투자 이벤트</div>
		<div><img src="img/m_banner.jpg" alt="투자 이벤트"></div>
		<div class="m_event4">
			<ul>
				<li class="num">1</li>
				<li class="1_title">첫투자 이벤트</li>
			</ul>
		
		</div>
		<div class="event_text">첫 투자로 주택담보 상품에<br>100만원 이상 투자 시<br><span>스타벅스 아메리카노 쿠폰 지급</span></div>
		
		<div class="event_date">
			<ul>
				<li class="m_sub">참여기간</li>
				<li class="m_text">2021년 09월 01일 ~ 2021년 09월 30일</li>
			</ul>
		</div>
		
		<div class="event_target">
			<ul>
				<li class="m_sub">참여대상</li>
				<li class="m_text">2021년 08월 31일까지 투자 이력이 없는<br>기존회원 및 신규회원<br><span>(부동산, 헬로페이 투자 이력이 있어도 주택담보상품 투자 이력이 없으면 OK)</span></li>
			</ul>
		</div>
		<div class="event_how">
			<ul>
				<li class="m_sub">참여방법</li>
				<li class="m_text">참여 기간 내 <span>주택담보 상품에<br>첫 투자로 100만원 이상</span> 투자완료 시 자동 참여</li>
			</ul>
		</div>	
		<div class="event_pay">
			<ul>
				<li class="m_sub">지급방법</li>
				<li class="m_text">2021년 10월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		
		
		<br><br>
		
		<div class="m_event10">
			<ul>
				<li class="num">2</li>
				<li class="1_title">주택담보상품 투자 이벤트</li>
			</ul>
		
		</div>
		<div class="event_text2">주택담보 상품에<br>누적 300만원 이상 투자 시<br><span>CU모바일상품권 1만원 지급</span></div>
		
		<div class="event_date">
			<ul>
				<li class="m_sub">참여기간</li>
				<li class="m_text">2021년 09월 01일 ~ 2021년 09월 30일</li>
			</ul>
		</div>
		
		<div class="event_target">
			<ul>
				<li class="m_sub">참여대상</li>
				<li class="m_text">참여 기간 내 주택담보 상품에 <br>누적 300만원 이상 투자한 기존회원 및 신규회원</li>
			</ul>
		</div>
		<div class="event_how2">
			<ul>
				<li class="m_sub">참여방법</li>
				<li class="m_text">참여 기간 내 <span>주택담보 상품에<br>누적 300만원 이상</span> 투자완료 시 자동 참여</li>
			</ul>
		</div>	
		<div class="event_pay">
			<ul>
				<li class="m_sub">지급방법</li>
				<li class="m_text">2021년 10월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="m_bt"><a href="<?=$join_link;?>"><img src="img/m_bt01.jpg" alt="투자 이벤트 바로가기"></a></div>

		<div class="event_notice">꼭! 읽어주세요. </div>
		<div class="event_notice_text2">본 이벤트는 참여 기간 내 오픈한 주택담보 상품 투자만 해당되며, 중복 참여가 가능합니다.</div>
		<div class="event_notice_text">				
			    <p class="notice dot color">투자를 취소하거나 투자한 상품의 모집이 취소될 경우 해당 투자금은 이벤트 대상 투자금액에서 제외됩니다.</p>
				<p class="notice dot color">투자금 적용은 투자상품의 투자 완료일을 기준으로 합니다.</p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p class="notice dot color">경품 지급 시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</p>
				<p class="notice dot color">이벤트 경품 지급은 참여 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다.</p>
				<p class="notice dot color">본 이벤트는 당사 사정에 의해 조기 종료 및 연장될 수 있습니다.</p>
				<p class="notice dot color">준법감시인 심사필 제2021-C-2 (2021.08.30)</p>
		</div>
		<div class="m_bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/m_bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	
</div>			  


<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>

