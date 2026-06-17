<?
include_once('./_common.php');

$g5['title'] = "8월 친구초대 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2021-08-01";
$event_end_date   = "2021-08-31";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 신규가입자 이벤트 입니다.');" : "/investment/invest_list.php";
}


?>


<link href="event.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>




<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->




	<div id="event">
		<div class="event_ing">진행중 이벤트<span>2021.08.01 ~ 2021.08.31</span></div>
		<div class="event_title">헬로펀딩 2021년 08월 친구초대 이벤트</div>
		<div><img src="img/banner.jpg" alt="헬로펀딩 6월 이벤트"></div>
		<div class="event_text">추천한 친구가 투자시 <span class="green">스타벅스 아메리카노 쿠폰 무제한 지급! </span><br>가입한 친구에게는 <span class="purple">CU모바일상품권 5천원~</span></div>
		
		<div class="event_date">
			<ul>
				<li class="sub">참여기간</li>
				<li class="text">2021년 08월 01일 ~ 2021년 08월 31일</li>
			</ul>
		</div>
		<div class="event_how">
			<ul>
				<li class="sub">참여방법</li>
				<li class="text">참여 기간 동안 초대한 친구가 회원가입 후<br><span>주택담보 상품</span>에
                               <span>누적 투자액 100만원 이상</span> 투자 완료시 친구도 나도 자동 참여</li>
			</ul>
		</div>
		<div class="event_how_img"><img src="img/guide.jpg" alt="참여방법가이드"></div>
	
		<div class="event_pay">
			<ul>
				<li class="sub">지급방법</li>
				<li class="text">2021년 09월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="bt"><a href="<?=$join_link;?>"><img src="img/bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>
	
		<div class="event_notice">꼭! 읽어주세요. </div>
		<div class="event_notice_text">				
			    <p>· 투자 이력이 없는 회원도 피추천인이 될 수 있습니다.</p>
				<p>· 2021년 08월 01일 이후 가입하고 2021년 08월 31일까지 투자를 완료한 친구 수를 기준으로 이벤트가 진행되며, 투자를 취소할 경우 지급되지 않습니다.</p>
				<p>· 참여 기간 동안 주택담보 상품에 누적 투자액 100만원 이상 투자 시에만 적용됩니다.</p>
				<p>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 올리고, 기타제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.  </p>
				<p>· 경품 지급시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</p>
				<p>· 이벤트 경품 지급은 참여 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다.</p>
				<p>· 본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다. </p>
				<p>· 이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </p>
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	




<!--------------------모바일----------------------------------------------------------->



	<div id="event_m">
		<div class="event_ing">진행중 이벤트<span>2021.08.01 ~ 2021.08.31</span></div>
		<div class="event_title">헬로펀딩 8월 친구초대 이벤트</div>
		<div><img src="img/m_banner.jpg" alt="헬로펀딩 5월 이벤트"></div>
		<div class="event_text">추천한 친구가 투자시 <br><span class="green">스타벅스 아메리카노 쿠폰 무제한 지급! </span><br>가입한 친구에게는<br><span class="purple">CU모바일상품권 5천원~</span></div>
		
		<div class="event_date">
			<ul>
				<li class="m_sub">참여기간</li>
				<li class="m_text">2021년 08월 01일 ~ 2021년 08월 31일</li>
			</ul>
		</div>
		
		<div class="event_how">
			<ul>
				<li class="m_sub">참여방법</li>
				<li class="m_text">참여 기간 동안 초대한 친구가 회원가입 후 <br><span>주택담보 상품</span>에<br>
                               <span>누적 투자액 100만원 이상</span><br> 투자 완료시 친구도 나도 자동 참여</li>
			</ul>
		</div>
		<div class="event_how_img"><img src="img/m_guide.jpg" alt="참여방법가이드"></div>
		
		<div class="event_pay">
			<ul>
				<li class="m_sub">지급방법</li>
				<li class="m_text">2021년 09월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="m_bt"><a href="<?=$join_link;?>"><img src="img/m_bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>
		<div class="event_notice">꼭! 읽어주세요. </div>
		<div class="event_notice_text">				
			    <p class="notice dot color">투자 이력이 없는 회원도 피추천인이 될 수 있습니다.</p>
				<p class="notice dot color">2021년 08월 01일 이후 가입하고 2021년 08월 31일까지 투자를 완료한 친구 수를 기준으로 이벤트가 진행되며,
					 투자를 취소할 경우 지급되지 않습니다.</p>
				<p class="notice dot color">참여 기간 동안 주택담보 상품에 누적 투자액 100만원 이상 투자 시에만 적용됩니다.</p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 올리고, 기타제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.  </p>
				<p class="notice dot color">경품 지급시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</p>
				<p class="notice dot color">이벤트 경품 지급은 참여 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다. </p>
				<p class="notice dot color">본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다. </p>
				<p class="notice dot color">이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </p>
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

