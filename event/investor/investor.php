<?
include_once('./_common.php');


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


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
		<div class="event1">진행중 이벤트<span>2020.09.01 ~ 종료시까지</span></div>
		<div class="event2">헬로펀딩 전문투자자/법인회원 첫 투자 이벤트</div>
		<div class="event3"><img src="img/web_01.jpg" alt="헬로펀딩 전문투자자 이벤트"></div>
		<div class="event4"><img src="img/web_02.jpg" alt="헬로펀딩 전문투자자 이벤트 소개"></div>
		<div class="event5"><img src="img/web_03.jpg" alt="헬로펀딩 전문투자자 투자한도"></div>
		<ul class="tab"> 
			<li><img alt="" src="img/tab_01_on.jpg"></li> 
			<li><img alt="" src="img/tab_02_off.jpg"></li> 
		</ul> 

		<div class="con_wrap"> 
			<div><img src="img/web_tab_01_con.jpg" usemap="#Map1">
			
			<map name="Map1">
                <area shape="rect" coords="329,975,823,1056" href="https://www.hellofunding.co.kr/investment/investor/?tab=2" onFocus="blur()">
              </map>
			</div>
				
			<div><img src="img/web_tab_02_con.jpg" usemap="#Map">
              <map name="Map">
                <area shape="rect" coords="328,755,823,835" href="https://www.hellofunding.co.kr/care_service/request.php" onFocus="blur()">
              </map>
			</div>
		</div>
		<div class="event6"><img src="img/notice.jpg" alt="헬로펀딩 전문투자자 유의사항"></div>
		<div class="bt"><a href="https://www.hellofunding.co.kr/hevent/"><img src="img/bt03.jpg" alt="목록으로 돌아가기"></a></div>
		<br><br><br>
	</div>	




<!--------------------모바일----------------------------------------------------------->



	<div id="event_m">
		<div class="m_event1">진행중 이벤트<span>2020.09.01 ~ 종료시까지</span></div>
		<div class="m_event2">전문투자자/법인회원 첫투자 이벤트</div>
		<div class="m_event3"><img src="img/mobile_bn.jpg" alt="전문투자자/법인회원 첫 투자 이벤트">

		</div>
		<div class="m_event4">
			<ul>
				<li class="1_title">이벤트 안내</li>
			</ul>
		
		</div>
		<div class="m_event5">전문투자자 신규/전환 후 첫 투자 완료시,<br>또는 법인회원 등록 후 첫 투자 완료시<br><span>신세계상품권 5만원 지급</span></div>
		<div class="m_event6">
			<ul>
				<li class="m_title">참여대상</li>
				<li class="m_text">이벤트 기간 내 전문투자자로 신규/전환 후<br>첫 투자 완료 시, 또는 법인회원 가입 후<br> 첫 투자 완료 시 자동 참여<br>(소상공인확정매출채권 상품 제외)</li>
			</ul>
		</div>
		<div class="m_event8">
			<ul>
				<li class="m_title">참여기간</li>
				<li class="m_text">2020년 09월 02일 ~ 종료시까지</li>
			</ul>
		</div>
		<div class="m_event9">
			<ul>
				<li class="m_title">지급방법</li>
				<li class="m_text">참여 완료일의 익월 10일 이내 일괄 지급</li>
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
				<a href="https://www.hellofunding.co.kr/care_service/request.php"><img src="img/bt.jpg"></a>
			</div>


		</div>
		
	
		<div class="m_event16">꼭! 읽어주세요. </div>
		<div class="m_event17">				
			    <p class="notice dot color">이벤트 기간 내에 전문투자자/법인회원으로 가입/전환 후 첫 투자까지 완료 시에만 상품이 지급됩니다. (소상공인확정매출채권 상품에 첫투자를 하고 다른 상품군에 투자 시 이벤트 지급 대상이 아닙니다.)</p>
				<p class="notice dot color">투자한 상품이 이자상환중으로 상태가 변경되면 자동 참여가 완료됩니다.</p>
				<p class="notice dot color">경품지급은 이벤트 참여 완료일의 익월 영업일 10일 이내 회원정보에 입력된 휴대폰 번호로 개별 지급됩니다.</p>
				<p class="notice dot color">본 이벤트는 헬로펀딩 투자자 대상 이벤트로 핀크, 올리고 등 기타 제휴광고 등을 통한 투자는 제외됩니다.</p>
				<p class="notice dot color">가입한 통신사에 따라 스팸차단서비스를 이용 시 문자 수신이 불가능할 수 있으니 경품 지급전 해당 서비스 이용 여부를 확인 부탁드립니다.</p>
				<p class="notice dot color">아래의 경우 경품 지급이 취소되며, 재발급이 되지 않습니다.</p>
				<p class="notice color">- &nbsp;투자한 상품의 투자를 취소한 경우</p>
				<p class="notice color">- &nbsp;회원정보내 연락처 오류 및 미변경으로 인한 미수신</p>
				<p class="notice color">- &nbsp;마케팅 수신거부, 광고문자 수신거부, 휴대폰 설정<br>&nbsp;&nbsp;&nbsp;(080, 통신사 무료 스팸어플)으로 인한 미수신</p>
				<p class="notice dot color">본 이벤트는 상시 이벤트로 당사 사정에 의해 조기 종료 될 수 있습니다.</p>
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

