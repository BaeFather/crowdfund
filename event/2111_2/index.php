<?
include_once('./_common.php');
include_once(G5_PATH . '/pid_check.inc.php');		// pid 유입체크 및 쿠키생성이 필요한 페이지에만 include

$g5['title'] = "신규회원 첫 투자 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

// 이벤트 idx값 가지고 오기
$idx = $_GET['SE'];

$sql = "SELECT idx, sdate, edate FROM hello_board WHERE idx='$idx'";
$row = sql_fetch($sql);

// 이벤트 시작일, 종료일 설정
$event_start_date = "2021-11-01";
$event_end_date   = "2022-12-31";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 신규가입자 이벤트 입니다.');" : "/member/join_info.php?tab=p";
}

// 이벤트 참여기간 매월 자동 설정
$date = date('Y-m-d');								// 현재 기준 날짜
$month_end_date = date('Y-m-t', strtotime($date));  // 현재 달 마지막날짜
$month_start_date = date('Y-m-01');					// 현재 달 시작날짜

// 날짜 설정 조건
if(!$idx) {
	$s_date = str_replace("-", ".", $event_start_date);
} else {
	$s_date = str_replace("-", ".", $row['sdate']);
}

if($row['edate'] == '9999-12-31' || $date != $month_end_date) {
	$e_date = '종료시까지';
}

$y = date('Y', strtotime($month_end_date));
$m = date('m', strtotime($month_end_date));
$ed = date('t', strtotime($month_end_date));
$sd = date('d', strtotime($month_start_date));

$s_ymd = $y.'년 '.$m.'월 '.$sd.'일';
$e_ymd = $y.'년 '.$m.'월 '.$ed.'일';

?>

<link href="/event/2111_2/event.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>


<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->




	<div id="event">
		<div class="event_ing">신규회원 첫투자 이벤트<span><?=$s_date?> ~ <?=$e_date?></span></div>
		<div><img src="/event/2111_2/img/banner.jpg" alt="신규회원 첫 투자 이벤트"></div>
		<div class="event_text">참여 기간 내 첫 투자 완료하신 분께 <span class="green">예치금 5,000원</span>을 지급해 드립니다. </div>

		<div class="event_date">
			<ul>
				<li class="sub">참여기간</li>
				<li class="text">2022년 04월 01일 ~ 2022년 04월 30일</li>
			</ul>
		</div>
		<div class="event_object">
			<ul>
				<li class="sub">참여대상</li>
				<li class="text">참여 기간 내 추천인 아이디에 "hello"를 입력 후 가입한 신규 회원</li>
			</ul>
		</div>
		<div class="event_how">
			<ul>
				<li class="sub">참여방법</li>
				<li class="text"><img src="/event/2111_2/img/guide.png" alt="참여방법가이드"></li>
			</ul>
		</div>
		<div class="event_pay">
			<ul>
				<li class="sub">지급방법</li>
				<li class="text">이벤트 참여 완료일의 익월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="bt"><a href="<?=$join_link;?>"><img src="/event/2111_2/img/bt01.png" alt="이벤트 참여하기"></a></div>

		<div class="event_notice">꼭! 읽어주세요. </div>
		<div class="event_notice_text">
			    <p>· 본 이벤트는 상시 진행 이벤트로 사전 예고없이 종료 또는 변경될 수 있습니다. </p>
				<p>· 추천인 아이디를 입력하고 회원가입 완료 후 첫 투자 완료 시 예치금을 지급해드립니다. <br>
				<span>(추천인 아이디 미입력 시 투자 완료하였더라도 리워드 지급이 안되오니 꼭 추천인 아이디를 입력해 주세요.)</span></p>
				<p>· 안전한 금융환경 조성을 위하여 헬로펀딩 예치금 신탁관리사인 신한은행 보안지침을 기반으로 예치금 입금일시 기준 24시간 후부터 출금이 가능합니다.</p>
				<p>· 투자한 상품이 이자상환 중 상태로 변경되어야 투자 완료로 간주됩니다.</p>
				<p>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타 제휴 광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다. </p>
				<p>· 예치금 지급은 매월 1일~ 말일까지 투자 완료하신 분들 확인 후 익월 10일 이내 지급해 드리며, 마이페이지>예치금 현황 및 출금에서 확인하실 수 있습니다. </p>
				<p>· 본 이벤트는 개인회원 대상 이벤트입니다.</p>
				<p>· 이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </p>
				<p>· 준법감시인 심사필 제2021-C-4 (2021.09.24)</p>
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="/event/2111_2/img/bt03.png" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>




<!--------------------모바일----------------------------------------------------------->



	<div id="event_m">
		<div class="event_ing">신규회원 첫투자 이벤트<span><?=$s_date?> ~ <?=$e_date?></span></div>
		<div><img src="/event/2111_2/img/m_banner.jpg" alt="신규회원 첫 투자 이벤트"></div>
		<div class="event_text">참여 기간 내 첫 투자 완료하신 분께 <br><span class="green">예치금 5,000원</span>을 지급해 드립니다.</div>

		<div class="event_date">
			<ul>
				<li class="m_sub">참여기간</li>
				<li class="m_text">2022년 04월 01일 ~ 2022년 04월 30일</li>
			</ul>
		</div>

		<div class="event_object">
			<ul>
				<li class="m_sub">참여대상</li>
				<li class="m_text">참여 기간 내 추천인 아이디에<br>"hello"를 입력 후 가입한 신규 회원</li>
			</ul>
		</div>


		<div class="event_how">
			<ul>
				<li class="m_sub">참여방법</li>
				<li class="m_text"><img src="/event/2111_2/img/m_guide.png" alt="참여방법가이드"></li>
			</ul>
		</div>



		<div class="event_pay">
			<ul>
				<li class="m_sub">지급방법</li>
				<li class="m_text">이벤트 참여 완료일의<br>익월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="m_bt"><a href="<?=$join_link;?>"><img src="/event/2111_2/img/m_bt01.png" alt="이벤트 참여하기"></a></div>
		<div class="event_notice">꼭! 읽어주세요. </div>
		<div class="event_notice_text">
			    <p class="notice dot color">본 이벤트는 상시 진행 이벤트로 사전 예고없이 종료 또는 변경될 수 있습니다.</p>
				<p class="notice dot color">추천인 아이디를 입력하고 회원가입 완료 후 첫 투자 완료 시 예치금을 지급해드립니다.(추천인 아이디 미입력 시 투자 완료하였더라도 리워드 지급이 안되오니 꼭 추천인 아이디를 입력해 주세요.)</p>
				<p class="notice dot color">안전한 금융환경 조성을 위하여 헬로펀딩 예치금 신탁관리사인 신한은행 보안지침을 기반으로 예치금 입금일시 기준 24시간 후부터 출금이 가능합니다.</p>
				<p class="notice dot color">투자한 상품이 이자상환 중 상태로 변경되어야 투자 완료로 간주됩니다.</p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타 제휴 광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.  </p>
				<p class="notice dot color">예치금 지급은 매월 1일~ 말일까지 투자 완료하신 분들 확인 후 익월 10일 이내 지급해 드리며, 마이페이지>예치금 현황 및 출금에서 확인하실 수 있습니다.</p>
				<p class="notice dot color">본 이벤트는 개인회원 대상 이벤트입니다. </p>
				<p class="notice dot color">이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </p>
				<p class="notice dot color">준법감시인 심사필 제2021-C-4 (2021.09.24)</p>
		</div>
		<div class="m_bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="/event/2111_2/img/m_bt03.png" alt="목록으로 돌아가기"></a></div>
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

