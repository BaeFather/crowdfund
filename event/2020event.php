<?
include_once('./_common.php');

$g5['title'] = "1월 친구초대 첫투자 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2020-01-01";
$event_end_date   = "2020-01-31";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
	$join_link2 = $join_link;
}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
	$join_link2 = $join_link;
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 신규가입자 이벤트 입니다.');" : "/member/join_info.php?tab=p";
	$join_link2 = "/investment/invest_list.php";
}


if(G5_IS_MOBILE) {
	include_once("2020event_m.php");
	return;
}

?>


<link href="2020event.css" rel="stylesheet" >


<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div>

	  <div id="event">
			<div class="event box1"><img src="/images/event/2020event/1event_01_2.jpg" alt="친구초대·첫투자 이벤트"></div>
			<div class="event box2">
				<div>

					<ul>
						<li><img src="/images/event/2020event/1event_05.png" alt="EVENT1"></li>
						<li class="atext"><a href="#event1">추천 친구가 100만원 이상 투자하면 <br><span> 너도 나도 예치금 5,000원 지급 </span></a></li>
						<li class="arrow"><img src="/images/event/2020event/arrow.png" alt="바로가기 "></li>
					</ul>
	
					<ul class="line"><li></li></ul>

					<ul>
						<li><img src="/images/event/2020event/1event_06.png" alt="EVENT2"></li>
						<li class="atext"><a href="#event2">첫 투자로 누적 투자액 100만원 이상시 <br> <span>스타벅스 아메리카노 쿠폰 지급 </span></a></li>
						<li class="arrow"><img src="/images/event/2020event/arrow.png" alt="바로가기 "></li>
					</ul>

				</div>
		  </div>
			<div class="event box3" id="event1"><p class="num">EVENT 01</p></div>
			<div class="event box4"><P class="titleA">추천한 친구가 100만원 이상 투자하면</P><P class="titleB">친구도, 나도 예치금 5,000원씩!</P></div>
			<div class="event box5">
				<ul>
					<li class="sub">참여방법</li>
					<li class="btext">이벤트 기간 동안 추천한 친구가 회원가입 후 부동산, 주택담보 상품에 <span>누적 투자액 100만원 이상</span> 투자 완료시 <Br> 친구도 나도 자동 참여</li>
				</ul>
			</div>
			<div class="event box6">
				<div>
					<ul>
						<li><img src="/images/event/2020event/1event_01_3_A.jpg" alt="참여방법1"></li>
						<li class="step">회원가입시 추천인 ID 입력하기</li>
						<li class="stepw">친구가 회원가입시 추천인 ID에 <span>내 아이디를 입력</span> 후 <br>회원 가입을 완료합니다.</li>
					</ul>
					<ul>
						<li><img src="/images/event/2020event/1event_01_3_B.jpg" alt="참여방법2"></li>
						<li class="step">100만원 이상 투자하기</li>
						<li class="stepw">회원가입한 친구가  <span>부동산, 주택담보 상품</span>에 <br> <span>100만원 이상</span> 투자합니다.</li>
					</ul>
					<ul class="last">
						<li><img src="/images/event/2020event/1event_01_3_C.jpg" alt="참여방법3"></li>
						<li class="step">친구와 나 모두 예치금 받기</li>
						<li class="stepw">투자를 완료한 친구도 추천한 나도 모두<span> 예치금을 <br> 각각 5천원씩</span> 지급받습니다. </li>
					</ul>
				</div>	
		  
		  
		  	</div>
			<div class="event box7">
				<p><span>▶ 기존회원</span> - 내 아이디를 입력하고 가입 후 100만원 이상 투자한 신규회원 1명당 예치금 5,000원씩 무한 지급!</p>
				<p><span>▶ 신규회원</span> - 추천인 아이디를 입력하고 가입 후 100만원 이상 투자하면 예치금 5,000원 지급!</p>
			
			</div>
			<div class="event box8">
				<ul>
					<li class="sub">참여기간</li>
					<li class="btext">2020년 01월 01일 ~ 2020년 01월 31일 </li>
				</ul>
				<ul>
					<li class="sub">지급방법</li>
					<li class="btext">2020년 02월 10일 영업시간 내 일괄 지급</li>
				</ul>
			</div>
			<div class="event box09"><a href="<?=$join_link;?>"><div class="bt"><span>친구 아이디 입력</span>하고 회원가입 하러가기&nbsp;&nbsp;&nbsp;▶</div></a></div>
			<div class="event box10" id="event2"><p class="num">EVENT 02</p></div>
			<div class="event box11"><P class="titleA">헬로펀딩 첫 투자자로 부동산, 주담대 누적 100만원 이상 투자 시</P><P class="titleB">스타벅스 아메리카노 쿠폰 지급!</P></div>
			<div class="event box12">
				<ul>
					<li class="sub">참여방법</li>
					<li class="wtext">이벤트 기간 내 부동산, 주택담보 상품에 처음 투자한 투자자로 <span>누적 투자액 100만원 이상</span> 투자 완료시 <Br> 자동 참여</li>
				</ul>
			</div>
			<div class="event box13">
				<ul>
					<li class="sub">참여기간</li>
					<li class="wtext">2020년 01월 01일 ~ 2020년 01월 31일</li>
				</ul>
				<ul>
					<li class="sub">지급방법</li>
					<li class="wtext">2020년 02월 10일 영업시간 내 일괄 지급</li>
				</ul>

			</div>
			<div class="event box14"><a href="<?=$join_link2;?>"><div class="bt"><span>첫투자</span>하고 이벤트 자동응모하기&nbsp;&nbsp;&nbsp;▶</div></a></div>
			<div class="event box15">
				<p class="bold">※ 꼭 읽어주세요.</p>
				<p>· 본 이벤트는 이벤트 기간 내 회원가입 시 친구초대 아이디 입력, 누적 투자액 100만원 이상 투자 완료 시 자동응모 됩니다. </p>
				<p>· 본 이벤트는 부동산 상품, 주택담보 상품 투자 시에만 적용됩니다. </p>
				<p>· 본 이벤트는 이벤트 기간 내 헬로펀딩 사이트에서 투자 시에만 이벤트 대상에 해당되며, 핀크, 한경TV등 기타 제휴사를 통한 투자는 제외됩니다. </p>
				<p>· 본 이벤트 기간 내 투자하신 상품에만 해당되며, 투자를 취소할 경우 리워드는 지급되지 않습니다. </p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다. </p>
				<p>· 경품 지급시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다. </p>
				<p>· 본 이벤트의 지급은 이벤트 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다. </p>
				<p>· 친구초대 이벤트와 첫 투자 이벤트는 중복 지급되지 않습니다. </p>
				<p>· 본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다. </p>
			
			</div>
		</div>


	</div>
</div>


<!-- 내부링크 애니메이션효과 -->
<script>
$("a[href^='#']").click(function(event) {
	event.preventDefault();
	var target = $(this.hash);
	$('html, body').animate({scrollTop: target.offset().top}, 500);
});

</script>



<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>