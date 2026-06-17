<?
include_once('./_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2020-08-01";
$event_end_date   = "2020-08-31";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";

}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";

}
else {
	$join_link = "/investment/invest_list.php?CA=A2";
}


?>


<link href="2008_1event.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>




<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->



	<div id="event">
		<div class="event1">진행중 이벤트<span>2020.08.01 ~ 2020.08.31</span></div>
		<div class="event2">헬로펀딩 8월 주택담보상품 투자자 이벤트</div>
		<div class="event3"><img src="img/banner.jpg" alt="헬로펀딩 8월 투자자 이벤트"></div>
		<div class="event4" id="event1">헬로펀딩 8월 주택담보상품 투자자 이벤트</div>
		<div class="event5">
			<p class="event_num">#이벤트 1</p>
			<p>8월 오픈 [주택담보상품]에 누적 500만원 이상 투자 시<br><span>국산 덴탈 마스크 1 Box(50매) 증정</span></p>
		</div>
		<div class="event5">
			<p class="event_num">#이벤트 2</p>
			<p>8월 오픈 [주택담보상품]에 누적 1,000만원 투자 시<br><span>국산 덴탈 마스크 1 Box(50매) 추가 증정</span></p>
		</div>

		<div class="event8">
			<ul>
				<li class="title">참여기간</li>
				<li class="text">2020년 08월 01일 ~ 2020년 08월 31일</li>
			</ul>
		</div>
		<div class="event8">
			<ul>
				<li class="title">참여방법</li>
				<li class="text">8월 오픈 <span>주택담보상품</span>에 500만원 ~ 1,000만원 투자 시 자동 참여</li>
			</ul>
		</div>	
		<div class="event7">
			<ul>
				<li class="title">참여경품</li>
				<li>
					<ul class="mask">
						<li><img src="img/mask.png"><p><span>누적 500만원 이상 투자 시</span><br> 국산 덴탈 마스크 <span>1Box (50매) 증정</span></p></li>
						<li>+</li>
						<li><img src="img/mask.png"><p><span>누적 1,000만원 투자 시</span><br> 국산 덴탈 마스크 <span>1Box (50매) 추가 증정</span></p></li>
					</ul>
				</li>
			</ul>
		</div>	
		<div class="event9">
			<ul>
				<li class="title">경품지급</li>
				<li class="text">이벤트 기간 내 목표 금액 달성 시 해당 경품 발송</li>
			</ul>
		</div>
		<div class="bt"><a href="<?=$join_link;?>"><img src="img/bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>

		<div class="event16">꼭! 읽어주세요. </div>
		<div class="event17">				
			    <p>· 이벤트 기간 동안 오픈 된 주택담보상품에 누적 투자액 500만원 이상 투자 시 에만 적용됩니다.</p>
				<p>· 투자를 취소하거나 투자한 상품의 모집이 취소될 경우 해당 투자금은 이벤트 대상 투자금에서 제외됩니다.</p>
				<p>· 투자금 적용은 투자상품의 투자 완료일을 기준으로 합니다. </p>
				<p>· 본 이벤트는 헬로펀딩 투자자 대상 이벤트로 핀크, 올리고, 기타제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 경품 지급 대상자는 회원정보에 입력된 휴대폰 번호로 개별 연락 및 안내를 드립니다. </p>
				<p>· 개인정보 오기입 및 경품지급 연락 미수신 등의 사유로 미수신시에는 경품 수령 의사가 없는 것으로 간주, 경품 지급이 취소됩니다. </p>
				<p>· 경품은 교환 또는 재발송이 불가하며, 경품 분실/ 파손/ 지연배송 등 배송과정에서 발생한 문제는 헬로펀딩이 책임지지 않습니다. </p>
				<p>· 본 이벤트는 2020년 7월부터 이벤트 종료 시까지 최대 2회 참여 가능합니다. </p>
				<p>· 본 이벤트는 당사 사정에 의해 조기 종료 될 수 있습니다. </p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다. </p>
				<p>· 이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요.</p>
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	




<!--------------------모바일----------------------------------------------------------->


	<div id="event_m">
		<div class="m_event1">진행중 이벤트<span>2020.08.01 ~ 2020.08.31</span></div>
		<div class="m_event2">헬로펀딩 8월 투자자 이벤트</div>
		<div class="m_event3"><img src="img/m_banner.jpg" alt="헬로펀딩 8월 투자자 이벤트">

		</div>
		<div class="m_event4">
			<ul>
				<li class="1_title">헬로펀딩 8월 주택담보상품 투자 이벤트</li>
			</ul>
		
		</div>
		<div class="m_event5">
			<p class="m_event_num">#이벤트 1</p>
			<p>8월 오픈 [주택담보상품]에<br>누적 500만원 이상 투자 시<br><span>국산 덴탈 마스크 1 Box(50매) 증정</span></p>
		</div>
		<div class="m_event5">
			<p class="m_event_num">#이벤트 2</p>
			<p>8월 오픈 [주택담보상품]에<br>누적 1,000만원 투자 시<br><span>국산 덴탈 마스크 1 Box(50매) 추가 증정</span></p>
		</div>

		<div class="m_event9">
			<ul>
				<li class="m_title">참여기간</li>
				<li class="m_text">2020년 08월 01일 ~ 2020년 08월 31일</li>
			</ul>
		</div>
		<div class="m_event9">
			<ul>
				<li class="m_title">참여방법</li>
				<li class="m_text">8월 오픈 <span>주택담보상품</span>에 500만원 ~ 1,000만원 투자 시 자동 참여</li>
			</ul>
		</div>		
		<div class="m_event7">
			<ul>
				<li class="m_title">참여경품</li>
				<li>
					<ul class="mask">
						<li>
							<ul class="mask2">
								<li><img src="img/mask.png"></li>
								<li class="mask_text"><span>누적 500만원 이상 투자 시</span><br> 국산 덴탈 마스크 <span>1Box (50매) 증정</span></li>
							</ul>
						</li>
						<li class="plus">+</li>
						<li>
							<ul class="mask2">
								<li><img src="img/mask.png"></li>
								<li class="mask_text"><span>누적 1,000만원 투자 시</span><br> 국산 덴탈 마스크 <span>1Box (50매) 추가 증정</span></li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</div>


		<div class="m_event9">
			<ul>
				<li class="m_title">경품지급</li>
				<li class="m_text">이벤트 기간 내 목표 금액 달성 시 해당 경품 발송</li>
			</ul>
		</div>

		<div class="m_bt"><a href="<?=$join_link;?>"><img src="img/m_bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>
		<div class="m_event16">꼭! 읽어주세요. </div>
		<div class="m_event17">				
			    <p class="notice dot color">이벤트 기간 동안 오픈 된 주택담보상품에 누적 투자액 500만원 이상 투자 시 에만 적용됩니다.</p>
				<p class="notice dot color">투자를 취소하거나 투자한 상품의 모집이 취소될 경우 해당 투자금은 이벤트 대상 투자금에서 제외됩니다.</p>
				<p class="notice dot color">투자금 적용은 투자상품의 투자 완료일을 기준으로 합니다.</p>
				<p class="notice dot color">본 이벤트는 헬로펀딩 투자자 대상 이벤트로 핀크, 올리고, 기타제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">경품 지급 대상자는 회원정보에 입력된 휴대폰 번호로 개별 연락 및 안내를 드립니다. </p>
				<p class="notice dot color">개인정보 오기입 및 경품지급 연락 미수신 등의 사유로 미수신시에는 경품 수령 의사가 없는 것으로 간주, 경품 지급이 취소됩니다.</p>
				<p class="notice dot color">경품은 교환 또는 재발송이 불가하며, 경품 분실/ 파손/ 지연배송 등 배송과정에서 발생한 문제는 헬로펀딩이 책임지지 않습니다.</p>
				<p class="notice dot color">본 이벤트는 2020년 7월부터 이벤트 종료 시까지 최대 2회 참여 가능합니다. </p>
				<p class="notice dot color">본 이벤트는 당사 사정에 의해 조기 종료 될 수 있습니다. </p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p class="notice dot color">이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요.</p>
		</div>
		<div class="m_bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/m_bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	
		  



<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>

