<?
include_once('./_common.php');


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2020-11-01";
$event_end_date   = "2020-11-30";

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
		<div class="event1">진행중 이벤트<span>2020.11.01 ~ 2020.11.30</span></div>
		<div class="event2">헬로펀딩 투자지원금 이벤트</div>
		<div class="event3"><img src="img/banner.jpg" alt="헬로펀딩 투자지원금 이벤트"></div>
		<div class="event5">주택담보 상품에 누적 100만원 이상 투자 시<br><span>투자금액 구간에 따라 ‘최대 5만원’ </span>투자지원금 드림</div>
		<div class="event6">
			<ul>
				<li class="title">참여방법</li>
				<li class="text">이벤트 기간 내 오픈한 <span>주택담보 상품에 누적 100만원 이상</span>  투자한 투자자 전원 자동 참여</li>
			</ul>
		</div>
		<div class="event7">
			<p>투자금액별 투자지원금 안내</p>
				<table class="table">
					<thead>
						<tr>
							<th>투자금액 구간</th>
							<th>투자지원금</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>100만원 이상</td>
							<td>5,000원</td>
						</tr>
						<tr>
							<td>200만원 이상</td>
							<td>10,000원</td>
						</tr>
						<tr>
							<td>300만원 이상</td>
							<td>15,000원</td>
						</tr>
						<tr>
							<td>400만원 이상</td>
							<td>20,000원</td>
						</tr>
						<tr>
							<td>500만원 투자</td>
							<td>50,000원</td>
						</tr>
					</tbody>
				</table>
			<p class="ex">ex. 100만원~199만원 투자 시 5천원 투자지원금 지급</p>
		</div>
		<div class="event8">
			<ul>
				<li class="title">참여기간</li>
				<li class="text">2020년 11월 01일 ~ 2020년 11월 30일</li>
			</ul>
		</div>
		<div class="event9">
			<ul>
				<li class="title">지급방법</li>
				<li class="text">2020년 12월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="bt"><a href="<?=$join_link;?>"><img src="img/bt01.jpg" alt="투자하러 가기"></a></div>

		<div class="event16">꼭! 읽어주세요. </div>
		<div class="event17">				
			    <p>· 본 이벤트는 이벤트 기간 내 오픈한 주담대 상품 투자만 해당됩니다.</p>
				<p>· 이벤트 기간 내 주택담보 상품에 누적 100만원 이상 투자 완료 시 자동 참여되며, 투자구간에 따라 지원금이 차등 지급됩니다.</p>
				<p>· 투자지원금은 이벤트 종료 후 영업일 기준 10일 이내 일괄 지급되며, 헬로펀딩 본인 가상계좌로 입금됩니다.</p>
				<p>· 개인 일반회원 최대 투자 한도는 500만원입니다. </p>
				<p>· 본 이벤트는 개인 일반회원만 참여 가능하며, 소득적격자/전문투자자/법인투자자는 이벤트 대상이 아닙니다. </p>
				<p>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 핀크, 올리고, 기타제휴 광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
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
		<div class="m_event1">진행중 이벤트<span>2020.11.01 ~ 2020.11.30</span></div>
		<div class="m_event2">헬로펀딩 투자지원금 이벤트</div>
		<div class="m_event3"><img src="img/m_banner.jpg" alt="헬로펀딩 2020년 11월 첫 투자 이벤트">

		</div>

		<div class="m_event5">주택담보 상품에<br>누적 100만원 이상 투자 시<br><span>투자금액 구간에 따라 <br>‘최대 5만원’</span> 투자지원금 드림</div>
		<div class="m_event6">
			<ul>
				<li class="m_title">참여방법</li>
				<li class="m_text">이벤트 기간 내 오픈한  <br><span>주택담보 상품에 누적 100만원 이상</span><br> 투자한 투자자 전원 자동 참여</li>
			</ul>
		</div>
		<div class="m_event7">
			<p>투자금액별 투자지원금 안내</p>
				<table class="table">
					<thead>
						<tr>
							<th>투자금액 구간</th>
							<th>투자지원금</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>100만원 이상</td>
							<td>5,000원</td>
						</tr>
						<tr>
							<td>200만원 이상</td>
							<td>10,000원</td>
						</tr>
						<tr>
							<td>300만원 이상</td>
							<td>15,000원</td>
						</tr>
						<tr>
							<td>400만원 이상</td>
							<td>20,000원</td>
						</tr>
						<tr>
							<td>500만원 투자</td>
							<td>50,000원</td>
						</tr>
					</tbody>
				</table>
			<p class="ex">ex. 100만원~199만원 투자 시 5천원 투자지원금 지급</p>
		</div>

		<div class="m_event8">
			<ul>
				<li class="m_title">참여기간</li>
				<li class="m_text">2020년 11월 01일 ~ 2020년 11월 30일</li>
			</ul>
		</div>
		<div class="m_event9">
			<ul>
				<li class="m_title">지급방법</li>
				<li class="m_text">2020년 12월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="m_bt"><a href="<?=$join_link;?>"><img src="img/m_bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>

		<div class="m_event16">꼭! 읽어주세요. </div>
		<div class="m_event17">				
			    <p class="notice dot color">본 이벤트는 이벤트 기간 내 오픈한 주담대 상품 투자만 해당됩니다. </p>
				<p class="notice dot color">이벤트 기간 내 주택담보 상품에 누적 100만원 이상 투자 완료 시 자동 참여되며, 투자구간에 따라 지원금이 차등 지급됩니다.</p>
				<p class="notice dot color">투자지원금은 이벤트 종료 후 영업일 기준 10일 이내 일괄 지급되며, 헬로펀딩 본인 가상계좌로 입금됩니다.</p>
				<p class="notice dot color">개인 일반회원 최대 투자 한도는 500만원입니다. </p>
				<p class="notice dot color">본 이벤트는 개인 일반회원만 참여 가능하며, 소득적격자/전문투자자/법인투자자는 이벤트 대상이 아닙니다. </p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 핀크, 올리고, 기타제휴 광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
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

