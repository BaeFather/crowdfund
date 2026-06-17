<?
include_once('./_common.php');


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2020-12-01";
$event_end_date   = "2020-12-31";

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
		<div class="event1">진행중 이벤트<span>2020.12.01 ~ 2020.12.31</span></div>
		<div class="event2">주택담보상품 첫 투자 이벤트</div>
		<div class="event3"><img src="img/banner.jpg" alt="주택담보상품 첫 투자 이벤트"></div>

		<div class="event5">주택담보상품에 처음으로 10만원 이상 투자하시는 분들께<br><span>스타벅스 아메리카노 쿠폰 지급</span></div>
		<div class="event6">
			<ul>
				<li class="title">참여방법</li>
				<li class="text">이벤트 기간 내 <span>주택담보상품에 처음으로 10만원 이상</span> 투자완료 시 자동 참여</li>
			</ul>
		</div>
		<div class="event8">
			<ul>
				<li class="title">참여대상</li>
				<li class="text">2020년 11월 30일까지 투자 이력이 없는 기존회원 및 신규회원<br><span>(주택담보상품 이외 부동산, 헬로페이, 동산 상품 투자 이력이 있어도 주택담보상품 투자 이력이 없으면 OK)</span></li>
			</ul>
		</div>
		<div class="event8">
			<ul>
				<li class="title">참여기간</li>
				<li class="text">2020년 12월 01일 ~ 2020년 12월 31일</li>
			</ul>
		</div>
		<div class="event9">
			<ul>
				<li class="title">지급방법</li>
				<li class="text">2021년 01월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="bt"><a href="<?=$join_link;?>"><img src="img/bt01.jpg" alt="투자하러 가기"></a></div>

		<div class="event16">꼭! 읽어주세요. </div>
		<div class="event17">
			    <p>· 이벤트 기간 내에 헬로펀딩 투자 상품군 중 주택담보 상품에 10만원 이상 투자 완료 시에만 상품이 지급됩니다.
					<br>&nbsp;&nbsp;&nbsp;(기존 회원 중 주택담보 상품에 투자 이력이 있다면 지급 대상이 아닙니다.) </p>
				<p>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 핀크, 올리고, 기타제휴 광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p>· 경품 지급 시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</p>
				<p>· 이벤트의 지급은 이벤트 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다.</p>
				<p>· 본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다.</p>
				<p>· 이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요.</p>
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>
</div>





<!--------------------모바일----------------------------------------------------------->


<div id="content_m">

	<div id="event_m">
		<div class="m_event1">진행중 이벤트<span>2020.12.01 ~ 2020.12.31</span></div>
		<div class="m_event2">주택담보상품 첫 투자 이벤트</div>
		<div class="m_event3"><img src="img/m_banner.jpg" alt="주택담보상품 첫 투자 이벤트">

		</div>
		<div class="m_event5">주택담보상품에 처음으로<br>10만원 이상 투자 시<br><span>스타벅스 아메리카노 쿠폰 지급</span></div>
		<div class="m_event6">
			<ul>
				<li class="m_title">참여방법</li>
				<li class="m_text">이벤트 기간 내 <span>주택담보 상품에<br>처음으로 10만원 이상</span> 투자완료 시 자동 참여</li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">참여대상</li>
				<li class="m_text">2020년 11월 30일까지 투자 이력이 없는<br>기존회원 및 신규회원<br><span>(다른 투자 이력이 있어도 주택담보상품 투자 이력이 없으면 OK)</span></li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">참여기간</li>
				<li class="m_text">2020년 12월 01일 ~ 2020년 12월 31일</li>
			</ul>
		</div>
		<div class="m_event9">
			<ul>
				<li class="m_title">지급방법</li>
				<li class="m_text">2021년 01월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="m_bt"><a href="<?=$join_link;?>"><img src="img/m_bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>

		<div class="m_event16">꼭! 읽어주세요. </div>
		<div class="m_event17">
			    <p class="notice dot color">이벤트 기간 내에 헬로펀딩 투자 상품군 중 주택담보 상품에 10만원 이상 투자 완료 시에만 상품이 지급됩니다. (기존 회원 중 주택담보 상품에 투자 이력이 있다면 지급 대상이 아닙니다.) </p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 핀크, 올리고, 기타제휴 광고 등을 통한 투자는 제외됩니다. </p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p class="notice dot color">경품 지급 시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</p>
				<p class="notice dot color">이벤트의 지급은 이벤트 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다. </p>
				<p class="notice dot color">본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다.</p>
				<p class="notice dot color">이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요.</p>
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
