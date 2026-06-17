<?
include_once('./_common.php');

$g5['title'] = "전문투자자/법인회원 첫 투자 이벤트";

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
$event_start_date = "2021-10-01";
$event_end_date   = "2022-12-31";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
}
else if( date('Y-m-d') > $event_end_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_end_date)) . "」에 종료 되었습니다.\\n다음 이벤트도 많은 참여 부탁드립니다.');";
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 전문투자자/법인회원 이벤트입니다.');" : "/member/join_info.php?tab=p";
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


<link href="investor.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>



<script>
$(function(){
    $('.tab li').click(function(){
        var tabType = $(this).index();
            $('.tab li').each(function(index){
            $(this).find('img').attr('src', $(this).find('img').attr('src').replace('_off','_on'));
            if(tabType != index){
                $(this).find('img').attr('src', $(this).find('img').attr('src').replace('_on','_off'));
            }
        });
            $('.con_wrap>div').eq(tabType).show().siblings('div').hide();
    });
 });
</script>
<script>
	$(function(){
    $('.mtab li').click(function(){
        var tabType = $(this).index();
            $('.mtab li').each(function(index){
            $(this).find('img').attr('src', $(this).find('img').attr('src').replace('_off','_on'));
            if(tabType != index){
                $(this).find('img').attr('src', $(this).find('img').attr('src').replace('_on','_off'));
            }
        });
            $('.mcon_wrap>div').eq(tabType).show().siblings('div').hide();
    });
 });
</script>


<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->




	<div id="event">
		<div class="event1">헬로펀딩 전문투자자/법인회원 첫투자 이벤트<span><?=$s_date?> ~ <?=$e_date?></span></div>
		<div class="event3"><img src="img/web_01.jpg" alt="헬로펀딩 전문투자자 이벤트"></div>
		<div class="event4">
			<div><img src="img/web_02_title.jpg"></div>
			<div class="event_date">
				<ul>
					<li class="sub">참여기간</li>
					<li class="text">2022년 04월 01일 ~ 2022년 04월 30일</li>
				</ul>
			</div>
			<div class="event_how">
				<ul>
					<li class="sub">참여방법</li>
					<li class="text">참여 기간 내 전문투자자 가입/전환 또는 법인회원 가입 후<br>
									<span class="bold">주택담보 상품에 100만원 이상 누적 투자 완료시 자동 참여</span><br>
									<span class="smail_font">(부동산, 소상공인확정매출채권 상품 제외)</span></li>
				</ul>
			</div>
			
			<div class="event_pay">
				<ul>
					<li class="sub">지급방법</li>
					<li class="text">이벤트 참여 완료일의 익월 10일 이내 일괄 지급</li>
				</ul>
			</div>
		</div>
		
		
		<div class="event5"><img src="img/web_03.jpg" alt="헬로펀딩 전문투자자 투자한도"></div>
		<ul class="tab"> 
			<li><img alt="" src="img/tab_01_on.jpg"></li> 
			<li><img alt="" src="img/tab_02_off.jpg"></li> 
		</ul> 

		<div class="con_wrap"> 
			<div><img src="img/web_tab_01_con.jpg" usemap="#Map">
              <map name="Map">
                <area shape="rect" coords="327,997,824,1076" href="https://www.hellofunding.co.kr/investment/investor/?tab=2">
              </map>
			</div>
				
			<div><img src="img/web_tab_02_con.jpg" usemap="#Map2">
              <map name="Map2">
                <area shape="rect" coords="328,1000,824,1082" href="https://www.hellofunding.co.kr/care_service/">
              </map>
			</div>
		</div>
		<div class="event6"><img src="img/notice.jpg" alt="헬로펀딩 전문투자자 유의사항"></div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	




<!--------------------모바일----------------------------------------------------------->



	<div id="event_m">
		<div class="m_event1">전문투자자/법인회원 첫투자 이벤트<span><?=$s_date?> ~ <?=$e_date?></span></div>
		<div class="m_event3"><img src="img/mobile_bn.jpg" alt="전문투자자/법인회원 첫투자 이벤트">

		</div>
		<div class="m_event4">
			<ul>
				<li class="1_title">이벤트 안내</li>
			</ul>
		
		</div>
		<div class="m_event5">헬로펀딩 전문투자자 가입/전환<br>또는 법인회원 가입 후<br>100만원 이상 누적 투자 완료 시<br><span>신세계상품권 5만원 지급</span></div>
		<div class="m_event6">
			<ul>
				<li class="m_title">참여대상</li>
				<li class="m_text">참여 기간 내 전문투자자<br>가입/전환 또는 법인회원 가입 후<br><b>주택담보 상품에 100만원 이상</b>
<br> 누적 투자 완료 시 자동 참여<br>(부동산, 소상공인확정매출채권 상품 제외)</li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">참여기간</li>
				<li class="m_text">2022년 04월 01일 ~ 2022년 04월 30일</li>
			</ul>
		</div>
		<div class="m_event9">
			<ul>
				<li class="m_title">지급방법</li>
				<li class="m_text">이벤트 참여 완료일의 익월 10일 이내 일괄 지급</li>
			</ul>
		</div>
		<div class="m_event4">
			<ul>
				<li class="1_title">투자자 한도 안내</li>
			</ul>
		</div>
		<div><img src="img/mobile_compare.jpg" alt="투자자 한도 비교"></div>
		
		<div class="m_event4">
			<ul>
				<li class="1_title">투자자 가입 및 전환안내</li>
			</ul>
		</div>
		<ul class="mtab"> 
			<li><img alt="" src="img/mtab_01_on.jpg"></li> 
			<li><img alt="" src="img/mtab_02_off.jpg"></li> 
		</ul> 

		<div class="mcon_wrap"> 
			<div>
				<img src="img/m_tab_01_con.jpg">
				<a href="https://www.hellofunding.co.kr/investment/investor/?tab=2"><img src="img/bt02.jpg"></a>
			</div>
			<div>
				<img src="img/m_tab_02_con.jpg">
				<a href="https://www.hellofunding.co.kr/care_service/"><img src="img/bt.jpg"></a>
			</div>


		</div>
		
	
		<div class="m_event16">꼭! 읽어주세요. </div>
		<div class="m_event17">				
			    <p class="notice dot color">참여 기간 내 전문투자자로 가입/전환 또는 법인회원으로 가입 후 해당 상품에 누적 100만원 이상 투자 완료 시에만 상품이 지급됩니다. (갱신 회원은 지급 대상에 포함되지 않습니다.)</p>
				<p class="notice dot color">투자한 상품이 이자상환 중으로 상태가 변경되면 자동 참여가 완료됩니다.</p>
				<p class="notice dot color">헬로펀딩 사이트를 통해 투자 시 이벤트 대상이 되며 기타 제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">경품 지급은 이벤트 참여 완료일의 익월 영업일 10일 이내 회원정보에 입력된 휴대폰 번호로 개별 지급됩니다.</p>
				<p class="notice dot color">가입한 통신사에 따라 스팸 차단 서비스를 이용 시 문자 수신이 불가능할 수 있으니 경품 지급전 해당 서비스 이용 여부를 확인 부탁드립니다.</p>
				<p class="notice dot color">아래의 경우 경품 지급이 취소되며, 재발급이 되지 않습니다.</p>
				<p class="notice color">- &nbsp;투자한 상품의 투자를 취소한 경우</p>
				<p class="notice color">- &nbsp;회원정보내 휴대폰 번호 오류 및 미변경으로 인한 미수신</p>
				<p class="notice color">- &nbsp;마케팅 수신거부, 광고 문자 수신거부, 휴대폰 설정<br>&nbsp;&nbsp;&nbsp;(080, 통신사 무료 스팸어플)으로 인한 미수신</p>
				<p class="notice dot color">본 이벤트는 상시 이벤트로 당사 사정에 의해 조기 종료 될 수 있습니다.</p>
				<p class="notice dot color">준법감시인 심사필 제2021-C-2 (2021.08.30)</p>
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

