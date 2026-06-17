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
	$join_link = "http://naver.me/5L0S64nC";
}


?>


<link href="4000event.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>




<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->



	<div id="event">
		<div class="event1">진행중 이벤트<span>2020.08.01 ~ 2020.08.31</span></div>
		<div class="event2">창립 4주년, 누적대출액 4,000억 돌파 기념 이벤트</div>
		<div class="event3"><img src="img/banner.jpg" alt="창립 4주년, 누적대출액 4,000억 돌파 기념 이벤트"></div>
		<div class="event5">투자자님의 생생한 <span>헬로펀딩 투자 후기와 응원 메시지를 공유</span>해 주세요.<br>작성해 주신 분들께 <span>국산마스크 1Box(50매)</span>를 드립니다.
</div>
		<div class="event4">#이벤트안내 </div>
		<div class="event6">
			<ul>
				<li class="title">참여대상</li>
				<li class="text"><span>8월 오픈 부동산/주택담보 상품에 100만원이상 투자 이력이 있는 모든 투자자</span></li>
			</ul>
		</div>
		<div class="event8">
			<ul>
				<li class="title">참여기간</li>
				<li class="text">2020년 08월 01일 ~ 2020년 08월 31일</li>
			</ul>
		</div>
		<div class="event8">
			<ul>
				<li class="title">지급경품</li>
				<li class="text">국산마스크 1Box(50매)</li>
			</ul>
		</div>
		<div class="event8">
			<ul>
				<li class="title">경품발송</li>
				<li class="text">2020년 09월 10일 개별안내 및 문자발송</li>
			</ul>
		</div>
		<div class="event9">
			<ul>
				<li class="title">해시태그</li>
				<li class="text">#헬로펀딩 #P2P투자 #소액투자 #P2P금융 #재테크</li>
			</ul>
		</div>
		
		
		<div class="event44">#참여방법 </div>
		<div class="event88 guide1">
			<ul>
				<li class="guide"><img src="img/guide01.jpg"></li>
				<li class="text">
					<p>STEP 01</p>
					<p>나의 투자내역 캡처하기</p>
					<p>- [마이페이지> 투자내역]<br>
					- 투자 상품명과 수익률이 포함된 화면 캡처</p>
				</li>
			</ul>
		</div>
		<div class="event88 guide2">
			<ul>
				<li class="guide"><img src="img/guide02.jpg"></li>
				<li class="text">
					<p>STEP 02</p>
					<p>자신의 블로그 채널에 투자 후기 작성하기</p>
					<p>- 투자 내역 캡처 이미지 업로드<br>
						- 300자 이상 작성 <br>
						- 전체공개 필수</p>
					<p>- SNS채널 : 네이버 블로그, 다음 블로그, 카카오 브런치, 티스토리<br>
						- 필수해시태그 : #헬로펀딩 #P2P투자 #소액투자 #P2P금융 #재테크</p>
					
				</li>
			</ul>
		</div>
		<div class="event88 guide3">
			<ul>
				<li class="guide"><img src="img/guide03.jpg"></li>
				<li class="text">
					<p>STEP 03</p>
					<p>이벤트 페이지에서 URL 입력하기</p>
					<p>- 헬로펀딩 홈페이지 접속<br>
					   - 로그인 후 이벤트 페이지 가기<br>
					   - 투자후기 응모하기 버튼 클릭<br>
					   - 정확한 URL입력과 자신의 사진첨부 하기</p>
				</li>
			</ul>
		</div>
		
		
		<div class="bt"><a href="<?=$join_link;?>" target="_blank"><img src="img/bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>

		<div class="event16">꼭! 읽어주세요. </div>
		<div class="event17">				
			    <p>· 이벤트 참여 대상 조건 미충족 시 이벤트 참여가 되지 않습니다.</p>
				<p>· 본 이벤트는 헬로펀딩 사이트를 통해 부동산 상품, 주택담보 상품 투자 시에만 적용되며, 핀크, 올리고 등 기타 제휴사를 통한 투자자는 제외됩니다.</p>
				<p>· 이벤트 기간 내 여러 번 공유 후 응모하셨다라도 경품은 1회만 지급됩니다.</p>
				<p>· 응모 후 투자 후기 게시물을 삭제하거나 비공개 전환 시 경품 지급이 취소될 수 있습니다.</p>
				<p>· 응모된 투자 후기 게시글과 사진은 마케팅 자료로 활용될 수 있습니다.</p>
				<p>· 해당 이벤트는 당사 사정에 따라 조기 종료될 수 있음을 알려드립니다.</p>
				<p>· 다음과 같은 경우에는 참여 대상 및 지급대상에서 제외 됩니다. </p>
				<span>- 실제로 투자가 이루어지지 않은 경우</span><br>
				<span>- 참여방법에 포함된 요소가 누락된 경우</span><br>
				<span>- 헬로펀딩 회원이 아니거나 정확한 회원가입 정보(성명, 아이디, 휴대폰번호)로  응모하지 않은 경우</span><br>
				<span>- 중복된 개인 정보 혹은 후기로 여러 번 응모했을 경우</span><br>
				<span>- 정확하지 않은 URL을 입력하여 후기 게시물 확인이 어려운 경우</span><br>

 
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	




<!--------------------모바일----------------------------------------------------------->


	<div id="event_m">
		<div class="m_event1">진행중 이벤트<span>2020.08.01 ~ 2020.08.31</span></div>
		<div class="m_event2">창립 4주년,<br>누적대출액 4천억 돌파기념 이벤트</div>
		<div class="m_event3"><img src="img/m_banner.jpg" alt="창립 4주년, 누적대출액 4,000억 돌파 기념 이벤트">

		</div>
		<div class="m_event5">투자자님의 생생한 <span>투자 후기와<br>응원 메시지를 공유</span>해 주세요.<br>작성해 주신 분들께<br><span>국산마스크 1Box(50매)</span>를 드립니다.
</div>
		<div class="m_event4">
			<ul>
				<li class="1_title">#이벤트안내</li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">참여대상</li>
				<li class="m_text"><span>8월 오픈 부동산/주택담보 상품에<br>100만원이상 투자 이력이 있는 모든 투자자</span></li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">참여기간</li>
				<li class="m_text">2020년 08월 01일 ~ 2020년 08월 31일</li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">지급경품</li>
				<li class="m_text">국산마스크 1Box(50매)</li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">경품발송</li>
				<li class="m_text">2020년 09월 10일 개별안내 및 문자발송</li>
			</ul>
		</div>
		<div class="m_event9">
			<ul>
				<li class="m_title">해시태그</li>
				<li class="m_text">#헬로펀딩 #P2P투자 #소액투자 #P2P금융 #재테크</li>
			</ul>
		</div>
		<br>
		<div class="m_event4">
			<ul>
				<li class="1_title">#참여방법</li>
			</ul>
		</div>	
		<div class="m_event7">
			<div><img src="img/guide01.jpg"></div>
			<div>
				<p>STEP 01</p>
				<p>나의 투자내역 캡처</p>
				<p>- [마이페이지> 투자내역]<br>
					- 투자 상품명과 수익률이 포함된 화면 캡처</p>
			</div>
			<br><br>
			<div><img src="img/guide02.jpg"></div>
			<div>
				<p>STEP 02</p>
				<p>본인 블로그 채널에 투자후기 작성</p>
				<p>- 투자 내역 캡처 이미지 업로드<br>
						- 300자 이상 작성 <br>
						- 전체공개 필수<br><br></p>				
				<p>SNS채널 : <br>네이버 블로그, 다음 블로그, 카카오 브런치, 티스토리<br><br>
				필수해시태그 : <br>#헬로펀딩 #P2P투자 #소액투자 #P2P금융 #재테크</p>
			</div>
			<br><br>
			<div><img src="img/guide03.jpg"></div>
			<div>
				<p>STEP 03</p>
				<p>이벤트 페이지에서 URL 입력</p>
				<p>- 헬로펀딩 홈페이지 접속<br>
					   - 로그인 후 이벤트 페이지 가기<br>
					   - 투자후기 응모하기 버튼 클릭<br>
					   - 정확한 URL입력과 자신의 사진첨부 하기</p>
			
			</div>
			<br><br>
		</div>	
		
		
		<div class="m_bt"><a href="<?=$join_link;?>"  target="_blank"><img src="img/m_bt01.jpg" alt="친구초대 이벤트 바로가기"></a></div>
		<div class="m_event16">꼭! 읽어주세요. </div>
		<div class="m_event17">				
			    <p class="notice dot color">이벤트 참여 대상 조건 미충족 시 이벤트 참여가 되지 않습니다.</p>
				<p class="notice dot color">본 이벤트는 헬로펀딩 사이트를 통해 부동산 상품, 주택담보 상품 투자 시에만 적용되며, 핀크, 올리고 등 기타 제휴사를 통한 투자자는 제외됩니다.</p>
				<p class="notice dot color">이벤트 기간 내 여러 번 공유 후 응모하셨다라도 경품은 1회만 지급됩니다.</p>
				<p class="notice dot color">응모 후 투자 후기 게시물을 삭제하거나 비공개 전환 시 경품 지급이 취소될 수 있습니다.</p>
				<p class="notice dot color">응모된 투자 후기 게시글과 사진은 마케팅 자료로 활용될 수 있습니다. </p>
				<p class="notice dot color">해당 이벤트는 당사 사정에 따라 조기 종료될 수 있음을 알려드립니다.</p>
				<p class="notice dot color">다음과 같은 경우에는 참여 대상 및 지급대상에서 제외 됩니다. </p>
				<p class="notices dots color">실제로 투자가 이루어지지 않은 경우</p>
				<p class="notices dots color">참여방법에 포함된 요소가 누락된 경우</p>
				<p class="notices dots color">헬로펀딩 회원이 아니거나 정확한 회원가입 정보(성명, 아이디, 휴대폰번호)로  응모하지 않은 경우</p>
				<p class="notices dots color">중복된 개인 정보 혹은 후기로 여러 번 응모했을 경우</p>
				<p class="notices dots color">정확하지 않은 URL을 입력하여 후기 게시물 확인이 어려운 경우</p>
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

