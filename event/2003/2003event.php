<?
include_once('./_common.php');

$g5['title'] = "3월 친구초대 첫투자 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2020-03-01";
$event_end_date   = "2020-03-31";

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


?>


<link href="2003event.css" rel="stylesheet" >


<!-- 본문내용 START -->
<div id="content">

      <div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	  <div id="event">
		  <ul>
			<li class="event box1"><img src="img/3event_01.jpg" alt="친구초대·첫투자 이벤트"></li>
			<li class="event box2"><img src="img/3event_02.jpg" alt="친구초대 이벤트"></li>
			<li class="event text">
		     	<ul>
					<li class="sub">[참여방법]</li>
					<li class="tx">이벤트 기간 동안 초대한 친구가 회원가입 후 <span>부동산, 주택담보 상품</span>에<br><span>누적 투자액 100만원 이상</span> 투자 완료시 친구도 나도 자동 참여</li>
				</ul>
		    </li>
			<li class="event box3"><img src="img/3event_03.jpg" alt="친구초대 이벤트 참여방법"></li>
			<li class="event text">
		     	<ul>
					<li class="mb"><span>●</span> 기존회원</li>
					<li class="tx2">내 아이디를 입력하고 가입 후 100만원 이상 투자한 신규회원 1명 당<br>예치금 5,000원씩 무한 지급!<br><p>(투자 이력이 없는 회원도 피추천인이 될 수 있습니다.)</p></li>
				</ul>
				<ul class="new">
					<li class="mb"><span>●</span> 신규회원</li>
					<li class="tx2">추천인 아이디를 입력하고 가입후 100만원 이상 투자하면 예치금 5,000원 지급!</li>
				</ul>
		    </li>
		  	<li class="event text">
		     	<ul class="date">
					<li class="sub">[참여기간]</li>
					<li class="tx4">2020년 03월 01일 ~ 2020년 03월 31일</li>
				</ul>
				<ul class="date">
					<li class="sub">[지급방법]</li>
					<li class="tx4">2020년 04월 10일 영업시간 내 일괄 지급</li>
				</ul>
		    </li>

			<li class="event box4"><p><a href="<?=$join_link;?>"><img src="img/3event_04.jpg" alt="친구초대 이벤트 바로가기"></a></p></li>



		   	<li class="bg"></li>



		    <li class="event box5"><img src="img/3event_05.jpg" alt="첫투자 이벤트"></li>
		    <li class="event text">
		     	<ul class="mb2">
					<li class="sub">[참여대상]</li>
					<li class="tx4">2020년 2월 29일까지 투자 이력이 없는 기존회원 및 신규회원</li>
				</ul>
				<ul class="use2">
					<li class="sub">[참여방법]</li>
					<li class="tx3">이벤트 기간 내 <span>부동산, 주택담보 상품</span>에 처음 투자한 투자자로 <br><span>누적 투자액 100만원 이상</span> 투자 완료시 자동 참여</li>
				</ul>
				<ul class="date2">
					<li class="sub">[참여기간]</li>
					<li class="tx4">2020년 03월 01일 ~ 2020년 03월 31일</li>
				</ul>
				<ul class="use3">
					<li class="sub">[지급방법]</li>
					<li class="tx4">2020년 04월 10일 영업시간 내 일괄 지급</li>
				</ul>
		    </li>

		    <li class="event box4"><p><a href="<?=$join_link2;?>"><img src="img/3event_06.jpg" alt="첫투자 이벤트 바로가기"></a></p></li>
			<li class="event box6">
				<ul>
				<li class="bold">※ 꼭 읽어주세요.</li>
				<li>· 투자 이력이 없는 회원도 피추천인이 될 수 있습니다.</li>
				<li>· 친구초대 이벤트의 경우, 2020년 03월 01일 이후 가입하고 2020년 03월 31일까지 투자를 완료한 친구 수를 기준으로 이벤트가 진행되며, 투자를 취소할 경우<br><span>지급되지 않습니다.</span> </li>
				<li>· 이벤트 기간 동안 부동산 상품, 주택담보 상품에 누적 투자액 100만원 이상 투자 시에만 적용됩니다.</li>
				<li>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며, 핀크, 한경TV, 올리고, 기타제휴광고 등을 통한 투자는 제외됩니다.</li>
				<li>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다. </span> </li>
				<li>· 경품 지급시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 미수신은 책임이 없습니다.</span> </li>
				<li>· 본 이벤트의 지급은 이벤트 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다. </li>
				<li>· 친구초대 이벤트와 첫 투자 이벤트는 중복 지급되지 않습니다.  </li>
				<li>· 본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다. </li>
				<li>· 이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </li>
				</ul>
			</li>
		</ul>
	</div>
</div>


<div id="content_m">
      <div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	  <div id="m_event">
		  <ul>
			<li><img src="img/m_3event_01.jpg" alt="친구초대·첫투자 이벤트"></li>
			<li><img src="img/m_3event_02.jpg" alt="친구초대 이벤트"></li>
			<li class="mtext">
		     	<ul>
					<li class="m_sub">[참여방법]</li>
					<li class="m_tx">이벤트 기간 동안 초대한 친구가 <br>회원가입 후 <span>부동산, 주택담보 상품</span>에<br><span>누적 투자액 100만원 이상</span><br> 투자 완료시 친구도 나도 자동 참여</li>
				</ul>
		    </li>
			<li><img src="img/m_3event_03.jpg" alt="친구초대 이벤트 참여방법"></li>
			<li class="mtext2">
		     	<ul>
					<li class="mb">기존회원<br>예치금 5,000원씩 무한지급!</li>
					<li class="tx2">내 아이디를 입력하고 가입 후 100만원 이상 투자한 신규회원 1명 당 예치금 5,000원씩 무한 지급! <br><p>(투자 이력이 없는 회원도 피추천인이 될 수 있습니다.)</p></li>
				</ul>
				<ul class="new2">
					<li class="mb">신규회원<br>예치금 5,000원 지급!</li>
					<li class="tx2">추천인 아이디를 입력하고 가입후 100만원 이상 투자하면 예치금 5,000원 지급!</li>
				</ul>
		    </li>
			<li class="mtext">
		     	<ul>
					<li class="m_sub2">[참여기간]</li>
					<li class="m_tx2">2020년 03월 01일 ~ 2020년 03월 31일</li>
				</ul>
		    </li>
			<li class="mtext">
		     	<ul>
					<li class="m_sub2">[지급방법]</li>
					<li class="m_tx2">2020년 04월 10일 영업시간 내 일괄 지급</li>
				</ul>
		    </li>
			<li class="bt"><a href="<?=$join_link;?>"><img src="img/m_3event_04.jpg" alt="친구초대 이벤트 참여하기"></a></li>
			<li><img src="img/m_3event_05.jpg" alt="첫투자 이벤트"></li>
			<li class="mtext" style="margin-top: -20px">
		     	<ul>
					<li class="m_sub2">[참여대상]</li>
					<li class="m_tx2">2020년 2월 29일까지 투자 이력이<br>없는 기존회원 및 신규회원</li>
				</ul>
		    </li>
			<li class="mtext3">
		     	<ul>
					<li class="m_sub">[참여방법]</li>
					<li class="m_tx">이벤트 기간 내 <span>부동산, 주택담보 상품</span>에 <br>처음 투자한 투자자로 <br><span>누적 투자액 100만원 이상</span><br>투자 완료시 자동 참여</li>
				</ul>
		    </li>
			<li class="mtext">
		     	<ul>
					<li class="m_sub2">[참여기간]</li>
					<li class="m_tx2">2020년 03월 01일 ~ 2020년 03월 31일</li>
				</ul>
		    </li>
			<li class="mtext">
		     	<ul>
					<li class="m_sub2">[지급방법]</li>
					<li class="m_tx2">2020년 04월 10일 영업시간 내 일괄 지급</li>
				</ul>
		    </li>
			<li><img src="img/m_3event_06.jpg" alt="스타벅스이미지"></li>
			<li class="bt2"><a href="<?=$join_link2;?>"><img src="img/m_3event_07.jpg" alt="첫투자 이벤트 참여하기"></a></li>
			<li class="notice">
				<ul>
				<li class="bold">※ 꼭 읽어주세요.</li>
				<li class="lip dot color">투자 이력이 없는 회원도 피추천인이 될 수 있습니다.</li>
				<li class="lip dot color">친구초대 이벤트의 경우, 2020년 03월 01일 이후 가입하고 2020년 03월 31일까지 투자를 완료한 친구 수를 기준으로<span>이벤트가 진행되며, 투자를 취소할 경우 지급되지 않습니다.</span> </li>
				<li class="lip dot color">이벤트 기간 동안 부동산 상품, 주택담보 상품에 누적 투자액 100만원 이상 투자 시에만 적용됩니다.</li>
				<li class="lip dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며, 핀크, 한경TV, 올리고, 기타제휴광고 등을 통한 투자는 제외됩니다.</li>
				<li class="lip dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 <span>등의 불이익이 있을 수 있습니다. </span> </li>
				<li class="lip dot color">경품 지급시 회원정보에 입력된 휴대폰 번호로 안내 및 모바일 쿠폰을 발송해 드리며, 휴대폰 번호 오류 및 미변경에 의한 <span>미수신은 책임이 없습니다.</span> </li>
				<li class="lip dot color">본 이벤트의 지급은 이벤트 기간 종료 후 영업일 기준 10일 이내 일괄 지급합니다. </li>
				<li class="lip dot color">친구초대 이벤트와 첫 투자 이벤트는 중복 지급되지 않습니다.  </li>
				<li class="lip dot color">본 이벤트는 당사 사정에 의해 조기 종료 및 연장 될 수 있습니다. </li>
				<li class="lip dot color">이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </li>
				</ul>
			</li>

		</ul>

	</div>
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

