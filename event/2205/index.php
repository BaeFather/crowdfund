<?
include_once('./_common.php');

$g5['title'] = "6월 친구초대 이벤트";

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

// 이벤트 참여기간 매월 자동 설정
$date = date('Y-m-d');								// 현재 기준 날짜 
$month_end_date = date('Y-m-t', strtotime($date));  // 현재 달 마지막날짜
$month_start_date = date('Y-m-01');					// 현재 달 시작날짜

// 이벤트 시작일, 종료일 설정
if( date('Y-m-d') < $month_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($month_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
}
else if( date('Y-m-d') > $month_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($month_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 신규가입자 이벤트 입니다.');" : "/member/join_info.php?tab=p";
}

// 날짜 설정 조건
if(!$idx) {
	$s_date = str_replace("-", ".", $month_start_date);
	$e_date = str_replace("-", ".", $month_end_date);
} else {
	$s_date = str_replace("-", ".", $row['sdate']);
	$e_date = str_replace("-", ".", $row['edate']);
}

if($row['edate'] == '9999-12-31') {
	$e_date = '종료시까지';
}

$y = date('Y', strtotime($month_end_date));
$m = date('m', strtotime($month_end_date));
$ed = date('t', strtotime($month_end_date));
$sd = date('d', strtotime($month_start_date));

$s_ymd = $y.'년 '.$m.'월 '.$sd.'일';
$e_ymd = $y.'년 '.$m.'월 '.$ed.'일';

?>


<link href="event.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>




<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->


<section class="event-friends">
	<div class="event-header">
		<div class="event-title-date">친구초대 이벤트<span><?=$s_date?> ~ <?=$e_date?></span></div>
		<div><img src="img/banner.jpg" alt="헬로펀딩 5월 친구초대 이벤트"></div>	
	
	</div>
	<div class="event-contents">
		<div class="main-title">추천한 친구가 투자 시, 친구도 나도<span class="red"> 예치금 5,000원씩 지급!</span></div>
		<div class="event-info">
			<div class="sub">참여기간</div>
			<div class="text"><?=$s_ymd?> ~ <?=$e_ymd?></div>
		</div>
		<div class="event-info">
			<div class="sub">참여방법</div>
			<div class="text">초대한 친구가 참여기간 내 회원가입 후<br>
							  <span> 누적 100만원이상 투자 완료 시 친구도 나도 자동 참여</span><br>
						      <img src="img/guide.jpg" alt="참여방법가이드"></div>
		</div>
		<div class="event-info">
			<div class="sub">지급방법</div>
			<div class="text">이벤트 참여 완료일의 익월 10일 이내 일괄 지급</div>
		</div>
		<div class="bt"><a href="<?=$join_link;?>"><img src="img/bt01.png" alt="친구초대 이벤트 바로가기"></a></div>
	</div>
	<div class="event-footer">
		<div class="event-notice">꼭! 읽어주세요. </div>
		<div class="event-notice-text">				
			    <p>· 투자 이력이 없는 회원도 피추천인이 될 수 있습니다.</p>
				<p>· <?=$s_ymd?> 이후 가입하고 <?=$e_ymd?>까지 투자를 완료한 친구 수를 기준으로 이벤트가 진행되며, <br><span style="padding-left:14px;"></span>투자를 취소하거나 투자한 상품의 모집이 취소될 경우 해당 투자금은 이벤트 대상 투자금액에서 제외됩니다.</p>
				<p>· 투자금 적용은 투자완료일을 기준으로 하며, 투자한 상품이 이자상환 중 상태로 변경되면 자동 참여 됩니다. </p>
				<p>· 예치금 지급은 참여기간 종료 후 영업일 기준 10일 이내 일괄 지급해 드리며, 마이페이지>예치금 현황 및 출금에서 확인하실 수 있습니다.</p>
				<p>· 안전한 금융환경 조성을 위하여 헬로펀딩 예치금 신탁관리사인 신한은행 보안지침을 기반으로 예치금 입금일시 기준 24시간 후부터 출금이 가능합니다.</p>
				<p>· 리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p>· 헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타 제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p>· 본 이벤트는 개인회원 대상 이벤트이며,  당사 사정에 의해 종료 또는 변경될 수 있습니다. </p>
				<p>· 이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </p>
				<p>· 준법감시인 심사필 제2022-C-2 (2022.04.28)</p>
		</div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.png" alt="목록으로 돌아가기"></a></div>
	</div>
</section>




<!--------------------모바일----------------------------------------------------------->


<section class="event-friends-m">
	<div class="event-header">
		<div class="event-title-date">친구초대 이벤트</div>
		<div><img src="img/m_banner.jpg" alt="헬로펀딩 5월 친구초대 이벤트"></div>	
	</div>
	
	<div class="event-contents">
		<div class="main-title">추천한 친구가 투자 시<br>친구도 나도<span class="red"> 예치금 5,000원씩 지급!</span></div>
		<div class="event-info">
			<div class="sub">참여기간</div>
			<div class="text"><?=$s_ymd?> ~ <?=$e_ymd?></div>
		</div>
		<div class="event-info">
			<div class="sub">참여방법</div>
			<div class="text">초대한 친구가 참여기간 내 회원가입 후<br>
							  <span> 누적 100만원이상 투자 완료 시<br>친구도 나도 자동 참여</span><br>
						      <img src="img/m_guide.jpg" alt="참여방법가이드"></div>
		</div>
		<div class="event-info">
			<div class="sub">지급방법</div>
			<div class="text">이벤트 참여 완료일의<br>익월 10일 이내 일괄 지급</div>
		</div>
		<div class="m_bt"><a href="<?=$join_link;?>"><img src="img/m_bt01.png" alt="친구초대 이벤트 바로가기"></a></div>
	</div>
	
	<div class="event-footer">
		<div class="event-notice">꼭! 읽어주세요. </div>
		<div class="event-notice-text">				
			    <p class="notice dot color">투자 이력이 없는 회원도 피추천인이 될 수 있습니다.</p>
				<p class="notice dot color"><?=$s_ymd?> 이후 가입하고 <?=$e_ymd?>까지 투자를 완료한 친구 수를 기준으로 이벤트가 진행되며,투자를 취소하거나 투자한 상품의 모집이 취소될 경우 해당 투자금은 이벤트 대상 투자금액에서 제외됩니다.</p>
				<p class="notice dot color">투자금 적용은 투자완료일을 기준으로 하며, 투자한 상품이 이자상환 중 상태로 변경되면 자동 참여 됩니다. </p>
				<p class="notice dot color">예치금 지급은 참여기간 종료 후 영업일 기준 10일 이내 일괄 지급해 드리며, 마이페이지>예치금 현황 및 출금에서 확인하실 수 있습니다.</p>
				<p class="notice dot color">안전한 금융환경 조성을 위하여 헬로펀딩 예치금 신탁관리사인 신한은행 보안지침을 기반으로 예치금 입금일시 기준 24시간 후부터 출금이 가능합니다.</p>
				<p class="notice dot color">리워드 수령을 목적으로 회원 탈퇴/재가입/사행성 행위 등 비정상적인 경로 적발 시 리워드 회수, 추후 이벤트 제외 등의 불이익이 있을 수 있습니다.</p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타 제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">본 이벤트는 개인회원 대상 이벤트이며,  당사 사정에 의해 종료 또는 변경될 수 있습니다. </p>
				<p class="notice dot color">이벤트 관련 문의사항은 카카오톡 플러스친구로 보내주세요. </p>
				<p class="notice dot color">준법감시인 심사필 제2022-C-2 (2022.04.28)</p>
		</div>
		<div class="m_bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/m_bt03.png" alt="목록으로 돌아가기"></a></div>
	</div>
	
	
	
</section>





<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>

