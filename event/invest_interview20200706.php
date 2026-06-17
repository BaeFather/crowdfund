<?


// redirect_url 이 설정된 페이지일 경우 실 URL로 접속시 redirect_url로 이동
if($_SERVER['REDIRECT_URL']=='') {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /special_interview/20200706");
	exit;
}

include_once('./_common.php');

$g5['title'] = $EVENT['title'];
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$event_idx = 4;

?>

<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">


<style>

/*web*/	
*{margin:0 auto; font-family: 'Noto Sans KR', sans-serif; padding:0; border:0; }
#content_web {margin:0 auto; width:1000px; text-align: left;}
p{font-weight:300!important;}	
.sub {color:#0b8964; font-size:28px; font-weight: 600; line-height: 32px; word-spacing: -1px; letter-spacing: -0.8px;}
.text {font-size:18px; color:#222; line-height: 28px; padding-top: 24px; font-weight: 300; word-spacing: -1px; letter-spacing: -0.8px;}		
.bd_top {border-top:1px solid #0b8964; margin-left: 0;}	
.line01 {width:270px;}
.line02 {width:525px;}
.line03 {width:600px;}
.line04 {width:315px;}
.line05 {width:445px;}
.line06 {width:310px;}
.line07 {width:735px;}
.line08 {width:435px;}	
.line09 {width:350px;}
.line10 {width:230px;}
.line11 {width:325px;}
.line12 {width:280px;}	
.line13 {width:315px;}
.line14 {width:355px;}	
.line15 {width:415px;}	
.line16 {width:655px;}		

.pdt10 {margin-top:10px;}
.pdt40 {margin-top:40px;}	
.img {padding-top:55px;}
.brown {color:#c5712b; font-weight: 500!important;}
.newtype1 {width:524px; margin-left:0; display: inline-block;}	
.newtype2 {display: inline-block; float: right;}
.newtype3 {width:494px; margin-left:0; display: inline-block;}	
	

	
	
/*mobile*/		
#content_m {margin:0 auto; width:100%; text-align: left;}	
#content_m img {width:100%;}	
.m_sub {color:#0b8964; font-size:26px; font-weight: 600; line-height: 30px; word-spacing: -2px; letter-spacing: -0.8px; padding: 0 5%;}
.m_text {font-size:18px; color:#222; line-height: 28px; padding: 24px 5% 0; font-weight: 300; word-spacing: -1px; letter-spacing: -0.8px;}		
.m_bd_top {border-top:1px solid #0b8964; margin-left: 0; margin: 0 5%;}		
.m_line01 {width:250px;}
.m_line02 {width:250px;}
.m_line03 {width:230px;}
.m_line04 {width:285px;}
.m_line05 {width:245px;}
.m_line06 {width:160px;}
.m_line07 {width:280px;}
.m_line08 {width:120px;}	
.m_line09 {width:195px;}
.m_line10 {width:230px;}
.m_line11 {width:205px;}
.m_line12 {width:280px;}	
.m_line13 {width:220px;}
.m_line14 {width:180px;}	
.m_line15 {width:130px;}	
.m_line16 {width:175px;}
.m_line17 {width:255px;}	
.m_line18 {width:120px;}	
.m_line19 {width:200px;}
.m_line20 {width:210px;}
.m_line21 {width:240px;}	
.m_line22 {width:175px;}
.m_line23 {width:205px;}		
.m_line24 {width:255px;}
.m_line25 {width:130px;}		
.m_line26 {width:125px;}		
.m_line27 {width:250px;}
.m_line28 {width:230px;}		
				
	
	
	
	
	
#content_web {display:block;}
#content_m {display:none;}

@media all and (max-width: 900px){
	#content_web {display:none;}
	#content_m {display:block;}
}
	
	
	
	
</style>

<!-- 본문내용 START -->

<div id="content_web">
<div><img src="/images/event/web_01.jpg"></div>
<div class="sub" style="padding-top: 55px">본인 소개 부탁드립니다.</div>
<div class="line01 bd_top"></div>	
<div class="text">안녕하세요? 반갑습니다.<br>저는 미생물 보존센터 연구직으로 재직하고 있는 4년 차 연구원 김** 입니다.</div>
<div class="sub" style="padding-top: 50px">평소 재테크/자산관리는 어떻게 하고 계시나요?</div>
<div class="line02 bd_top"></div>	
<div class="text">평소 재테크는 예금과 적금을 주로 하고 주식투자를 조금 하고 있습니다.<br>
예금과 적금은 금리가 작긴 하지만 위험도가 낮기 때문에 필수적이라고 생각하고 주식은 시작한 지 얼마 안 돼 아직 손해를 본 적은 없지만<br>
위험성은 충분히 인지하고 있어요. 주식은 배우는 단계라 열심히 공부하고 있습니다.
<p class="pdt10">그리고 이번에 헬로펀딩을 새롭게 알게 되어 소액으로 투자를 시작하고 있습니다.</p>
</div>	
	
<div class="img"><img src="/images/event/web_02.jpg"></div>	
	
<div class="sub" style="padding-top: 50px">헬로펀딩에 투자하게 된 계기와 헬로펀딩의 좋은 점은?</div>
<div class="line03 bd_top"></div>	
<div class="text">
재테크에 워낙 관심이 많다 보니 주식이나 펀드 말고 또 자산을 불릴 수 있는 수단이 없을까 찾아보다가 우연히 재테크 카페에서 P2P라는 단어를<br>
처음 접했습니다.
<p class="pdt10" style="letter-spacing:-0.58px">알고 보니 주변 지인분들도 P2P 투자를 많이 하고 계셨고, 아버지께서도 부동산 업종에 종사하고 계셔서  여유자금이 생기면 그냥 두는 것 보다<br>
아버지와 주변 분들의 도움을 받아 P2P 투자를 해보자는 생각을 가지게 되었습니다.</p>
<p class="pdt10" style="letter-spacing:-0.64px">그 후 P2P 금융의 정의부터 P2P 업체까지 며칠 동안 P2P 금융에 대해 찾아보고 공부하며 제 나름대로 업체 선정 기준을 뒀고, 그 중 헬로펀딩이<br>
가장 믿음이 가는 괜찮은 업체라 생각해 투자를 시작하게 되었습니다.</p>
<p class="pdt10">생각해보면 주식은 통장 개설, 예치금 입금 등 시작할 때 너무 복잡하고 어려웠던 기억이 있어요. <br>
하지만 P2P 투자는 쉽게 접근할 수 있고 투자할 수 있어 좋은 것 같습니다.<br>
<p class="pdt40 brown">헬로펀딩의 좋은 점은 투자자들 입장에서 노력하는 점들이 보인다는 것이에요.
<p class="pdt10">우선 홈페이지를 보면 P2P를 잘 모르는 저와 같은 초보자들도 쉽게 투자할 수 있도록 자세한 설명과 자료들이 신뢰감이 가고 또 사업 성과와 사업<br> 
진행 방향을 투자자들에게 문자로 보내주는 것을 보고 투명성과 진정성이 느껴졌습니다. <br>
저는 아직까지 P2P 투자는 헬로펀딩에만 투자를 하고 있습니다 :)	
</div>
	
<div class="sub" style="padding-top: 50px">P2P 투자 회사 선택 기준은?</div>
<div class="line04 bd_top"></div>	
<div class="text">아무래도 연체율과 부실률을 가장 많이 보고 저와 같은 P2P 투자 초보자들은  후기를 정말 많이 찾아보기 때문에 후기도 중요한 것 같습니다.<br>
헬로펀딩을 선택한 이유는 후기를 하나하나 읽으며 그분들의 생각이 공감되고  최대한 투자자의 알 권리를 위해 노력하는 것이 느껴졌고,<br> 
<p class="brown">오픈 이후 연체가 단 한 번도 없는 점과 수익을 확실하게 달성한다는 점에 헬로펀딩을 더 신뢰하게 된 것 같습니다.</p>
</div>		

<div>
	<div class="newtype1">
		<div class="sub" style="padding-top: 50px">투자할 때 가장 중요하게 생각하는 것은?</div>
		<div class="line05 bd_top"></div>	
		<div class="sub" style="padding-top: 5px">나만의 투자 원칙이 있다면?</div>
		<div class="line06 bd_top"></div>
		<div class="text">투자할 때는 안전성을 가장 중요하게 생각합니다. 특히 P2P 투자라는 것이<br>
		위험부담이 있기 때문에 연체율에 따라 신뢰도가 달라지는 것 같습니다.<br>
		<p class="pdt10" style="letter-spacing:-1.45px">근데 처음에는 안전하게 운영하자는 생각으로 시작했는데 막상 투자 상품들을<br>
		선택하다 보면 수익률을 전혀 안 볼 수가 없긴 했습니다.</p> 
		<p class="pdt40 brown" style="letter-spacing:-1.1px">저의 투자 원칙은 투자 업체에 대한 충분한 자료조사로 위험도를 최소화하고,<br>
		투자금액도 부담되지 않는 금액을 선정하여 투자하는 것입니다.</p>
		<p class="pdt10" style="letter-spacing:-1.8px">주식은 주로 우량주에 투자를 하고 P2P 투자는 안정성 있는 투자 기업을 선택하되<br>
		수익은 너무 낮지도 높지도 않은 중간 상품을 선호하는 편입니다.</p>
		</div>		
	</div>
	<div class="img newtype2"><img src="/images/event/web_03.jpg"></div>	
</div>	
<div class="img"><img src="/images/event/web_04.jpg"></div>		
<div class="sub" style="padding-top: 50px">P2P에 투자 시(헬로펀딩 투자 시) 가장 중요하게 생각하는 부분은?</div>
<div class="line07 bd_top"></div>	
<div class="sub" style="padding-top: 5px">P2P투자의 자신만의 노하우가 있다면?</div>
<div class="line08 bd_top"></div>		
<div class="text">헬로펀딩에 투자할 때는 홈페이지에서 수익률과 투자 기간을 가장 중요시했습니다.<br>
투자 상품을 보면 투자금액과 수익률, 기간에 따른 이자가 나오는데 투자 상품을 선택할 때 정말 많은 도움이 되었어요.<br>
투자하면서 알아가다 보니 수익률은 높지만 기간이 짧아 기간이 긴 상품보다 이자가 낮다는 사실을 알고 저는 장기 투자의 목적을 가지고 수익률과 <br>
기간을 모두 고려하여 상품을 선택하고 투자했습니다.
<p class="pdt40 brown" style="letter-spacing:-1.25px">헬로펀딩 투자 상품 중 제가 선호하는 상품은 부동산 상품과 아파트 담보 상품인데요, 장기 투자지만 더 안정성이 있다고 생각해요.<br>
부동산 이슈는 뉴스나 포털사이트에서 제가 필요한 정보를 쉽게 확인할 수 있고 헬로라이브TV로 진행 상태를 실시간 확인 할수 있어 선호하는 편입니다. </p></div>
	
	
<div>
	<div class="newtype3">
		<div class="sub" style="padding-top: 50px">헬로펀딩에 다달이 받는 이자는</div>
		<div class="line09 bd_top"></div>	
		<div class="sub" style="padding-top: 5px">어디에 쓰고 있나요?</div>
		<div class="line10 bd_top"></div>
		<div class="text">지금은 잘 모으고 있어요.<br>
차곡차곡 모아서 그 돈으로 또 투자를 할 생각이에요.<br>
<p style="letter-spacing:-1.5px">투자도 공부라고 생각하기 때문에 소액으로 조금씩 투자 경험을 늘린다면</p>
나중에는 큰 도움이 될 거라고 생각합니다.<br>
사고 싶은 것은 많은데 급여는 고정 지출과 생활비로 거의 소비가 되니 <br>
<p style="letter-spacing:-0.47px">사회 초년생인 저에게는 사고 싶은 것을 다 산다는 건 약간 사치인 것</p>
같아요. 재테크로 차곡차곡 모아서 제가 사고픈 것도 사보고 싶어요 :)</div>	
		<div class="sub" style="padding-top: 50px">P2P 투자로 여유가 생긴다면</div>
		<div class="line11 bd_top"></div>	
		<div class="sub" style="padding-top: 5px">무엇을 하고 싶으신가요?</div>
		<div class="line12 bd_top"></div>
		<div class="text">
  <p style="letter-spacing:-0.55px">결혼 전까지는 결혼 자금으로 모아두고 결혼 후에는 일정 부분은 다시</p>
P2P 투자와 주식 투자를 할 거예요. <br>
결혼 후에는 지금보다 큰 금액으로 투자를 할 생각입니다.</div>	
	</div>
	<div class="img newtype2"><img src="/images/event/web_05.jpg"></div>	
</div>		

	
<div class="sub" style="padding-top: 50px">헬로펀딩에 투자하고자 하는</div>
<div class="line13 bd_top"></div>	
<div class="sub" style="padding-top: 5px">고객분들께 한 말씀해주신다면?</div>
<div class="line14 bd_top"></div>		
<div class="text">요즘 제 또래들은 재테크나 투자에 많은 관심을 가지고 있는 것 같습니다.<br>
은행 금리가 너무 낮아 통장에는 돈을 넣어 두는 게 아니다..라고까지 말하고 있잖아요.<br>
<p class="pdt10">저 같은 사회 초년생들은 부모님께 무조건 안정적인 예/적금을 해라라는 말을 많이 듣는데요. 친구들과는 조금씩 재테크나<br>
투자를 해서 돈을 모으자라고 얘기를 많이 합니다.
</p>
<p class="pdt10">실제로 오늘만 해도 제 친구에게 헬로펀딩을 소개해 주고 저의 재테크 방법을 알려줬어요^^</p>
이런 상품이나 투자가 있는지도 몰랐다고 하더라고요.
<p class="pdt10 brown">일단 소액으로라도 투자를 시작해서 경험도 쌓고 이자도 받아보셨으면 좋겠습니다. <br>
직접 투자를 하고 예치금이 쌓이는 걸 확인하면 믿고 투자하실 수 있을 것 같아요. </p>
<p style="letter-spacing:-1.15px">헬로펀딩에 투자하게 된다면 본인이 가지고 있는 돈을 단/장기상품에 분산투자하여 이자를 주기적으로 받을 수 있도록 하는 것을 권해드리고 싶습니다.</p></div>
	
	
<div class="img"><img src="/images/event/web_06.jpg"></div>	
	
	
<div class="sub" style="padding-top: 50px">ㅇㅇㅇ님께 헬로펀딩은? ㅇㅇㅇ이다.</div>
<div class="line15 bd_top"></div>		
<div class="text">헬로펀딩은 저에게 ‘뜻밖의 이득’인 것 같아요.<br>
금리가 낮은 요즘 예/적금을 하는 사람들보다는 주식에 관심을 많이 갖는 것 같습니다.<br>
저는 주기적으로 확인하고 꾸준히 공부하고 매 순간 선택해야 하는 주식보다는 헬로펀딩에 투자해 매달 5일 날 이자 지급 문자를 받고 <br>
예치금이 쌓이는 것을 보면 진짜 생각지도 못한 뜻밖의 돈이 들어오는 느낌이에요 :)</div>
	
<div class="sub" style="padding-top: 50px">헬로펀딩에 하고 싶은 말이 있으시면 한 말씀 부탁드립니다.</div>
<div class="line16 bd_top"></div>		
<div class="text">좋은 상품으로 투자자들에게 신뢰를 주고, 효율적인 투자방식을 고안하여 투자자들에게 편함을 주는 헬로펀딩에게 감사드립니다. <br> 
저도 주변 사람들에게 좋은 상품이 있으면 전파하도록 하겠습니다.  <br>
그리고 헬로펀딩이 앞으로 더 성공해서 모르는 사람이 없었으면 좋겠습니다*^^*</div>
<br><br>		
		
<div class="img"><img src="/images/event/web_07.jpg"></div>	
<br><br>	
<div style="text-align: center;"><a href="https://www.hellofunding.co.kr/review"><img src="/images/event/bt.jpg"></a></div>	
<br><br><br><br>	
</div>	

	
	


<div id="content_m">
<div><img src="/images/event/m_01.jpg"></div>
<div class="m_sub" style="padding-top: 55px">본인 소개 부탁드립니다.</div>
<div class="m_line01 m_bd_top"></div>	
<div class="m_text">안녕하세요? 반갑습니다.<br>저는 미생물 보존센터 연구직으로 재직하고 있는 4년 차 연구원 김** 입니다.</div>
<div class="m_sub" style="padding-top: 50px">평소 재테크/자산관리는</div>
<div class="m_line02 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">어떻게 하고 계시나요?</div>
<div class="m_line03 m_bd_top"></div>		
	
<div class="m_text">평소 재테크는 예/적금을 주로 하고 주식투자를 조금 하고 있습니다.<br>
예금과 적금은 금리가 작긴 하지만 위험도가 낮기 때문에 필수적이라고 생각하고 주식은 시작한 지 얼마 안 돼 아직 손해를 본 적은 없지만
위험성은 충분히 인지하고 있어요.<br>주식은 배우는 단계라 열심히 공부하고 있습니다.
<p class="pdt10">그리고 이번에 헬로펀딩을 새롭게 알게 되어 소액으로 투자를 시작하고 있습니다.</p>
</div>	
	
<div class="img"><img src="/images/event/m_02.jpg"></div>	
<div class="img"><img src="/images/event/m_title01.jpg"></div>	
	
<div class="m_sub" style="padding-top: 50px">P2P 투자 회사 선택 기준은?</div>
<div class="m_line04 m_bd_top"></div>	
<div class="m_text">아무래도 연체율과 부실률을 가장 많이 보고 저와 같은 P2P 투자 초보자들은 후기를 정말 많이 찾아보기 때문에 후기도 중요한 것 같습니다.<br>
헬로펀딩을 선택한 이유는 후기를 하나하나 읽으며 그분들의 생각이 공감되고 최대한 투자자의 알 권리를 위해 노력하는 것이 느껴졌고, 오픈 이후 연체가 단 한 번도 없는 점과 수익을 확실하게 달성한다는 점에 헬로펀딩을 더 신뢰하게 된 것 같습니다.
</div>		

<div class="m_sub" style="padding-top: 50px">투자할 때 가장 중요하게</div>
<div class="m_line05 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">생각하는 것은?</div>
<div class="m_line06 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">나만의 투자 원칙이 있다면?</div>
<div class="m_line07 m_bd_top"></div>
<div class="m_text">투자할 때는 안전성을 가장 중요하게 생각합니다. 특히 P2P 투자라는 것이
위험부담이 있기 때문에 연체율에 따라 신뢰도가 달라지는 것 같습니다.<br>
<p class="pdt10">근데 처음에는 안전하게 운영하자는 생각으로 시작했는데 막상 투자 상품들을
선택하다 보면 수익률을 전혀 안 볼 수가 없긴 했습니다.</p> 
<p class="pdt10">저의 투자 원칙은 투자 업체에 대한 충분한 자료조사로 위험도를 최소화하고,
투자금액도 부담되지 않는 금액을 선정하여 투자하는 것입니다.</p>
<p class="pdt10">주식은 주로 우량주에 투자를 하고 P2P 투자는 안정성 있는 투자 기업을 선택하되
수익은 너무 낮지도 높지도 않은 중간 상품을 선호하는 편입니다.</p>
</div>		
<div class="img" style="width:70%; margin-left:5%;"><img src="/images/event/m_03.jpg"></div>	
	
	
	
<div class="m_sub" style="padding-top: 50px">헬로펀딩에</div>
<div class="m_line08 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">투자하게 된 계기와</div>
<div class="m_line09 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">헬로펀딩의 좋은 점은?</div>
<div class="m_line10 m_bd_top"></div>	
<div class="m_text">
재테크에 워낙 관심이 많다 보니 주식이나 펀드 말고 또 자산을 불릴 수 있는 수단이 없을까 찾아보다가 우연히 재테크 카페에서 P2P라는 단어를
처음 접했습니다.
<p class="pdt10">알고 보니 주변 지인분들도 P2P 투자를 많이 하고 계셨고, 아버지께서도 부동산 업종에 종사하고 계셔서 여유자금이 생기면 그냥 두는 것 보다
아버지와 주변 분들의 도움을 받아 P2P 투자를 해보자는 생각을 가지게 되었습니다.</p>
<p class="pdt10">그 후 P2P 금융의 정의부터 P2P 업체까지 며칠 동안을 P2P 금융에 대해 찾아보고 공부하며 제 나름대로 업체 선정 기준을 뒀고, 그 중 헬로펀딩이
가장 믿음이 가는 괜찮은 업체라 생각해 투자를 시작하게 되었습니다.</p>
	
<div class="img" style="padding-bottom: 50px"><img src="/images/event/m_title02.jpg"></div>	
<p class="pdt10">생각해보면 주식은 통장 개설, 예치금 입금 등 시작할 때 너무 복잡하고 어려웠던 기억이 있어요.
하지만 P2P 투자는 쉽게 접근할 수 있고 투자할 수 있어 좋은 것 같습니다.</p>
<p class="pdt10">헬로펀딩의 좋은 점은 투자자들 입장에서 노력하는 점들이 보인다는 것이에요.</p>
<p class="pdt10">우선 홈페이지를 보면 P2P를 잘 모르는 저와 같은 초보자들도 쉽게 투자할 수 있도록 자세한 설명과 자료들이 신뢰감이 가고 또 사업 성과와 사업
진행 방향을 투자자들에게 문자로 보내주는 것을 보고 투명성과 진정성이 느껴졌습니다.</p>
<p class="pdt10">저는 아직까지 P2P 투자는 헬로펀딩에만 투자를 하고 있습니다 :)</p>
</div>
	
<div class="img"><img src="/images/event/m_04.jpg"></div>		
	

<div class="m_sub" style="padding-top: 50px">P2P에 투자 시 가장</div>
<div class="m_line11 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">중요하게 생각하는 부분은?</div>
<div class="m_line12 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">P2P투자의 자신만의</div>
<div class="m_line13 m_bd_top"></div>		
<div class="m_sub" style="padding-top: 5px">노하우가 있다면?</div>
<div class="m_line14 m_bd_top"></div>	
<div class="m_text">헬로펀딩에 투자할 때는 홈페이지에서 수익률과 투자 기간을 가장 중요시했습니다.
투자 상품을 보면 투자금액과 수익률, 기간에 따른 이자가 나오는데 투자 상품을 선택할 때 정말 많은 도움이 되었어요.
<p class="pdt10">투자하면서 알아가다 보니 수익률은 높지만 기간이 짧아 기간이 긴 상품보다 이자가 낮다는 사실을 알고 저는 장기 투자의 목적을 가지고 수익률과
기간을 모두 고려하여 상품을 선택하고 투자했습니다.</p>
<p class="pdt10">헬로펀딩 투자 상품 중 제가 선호하는 상품은 부동산 상품과 아파트 담보 상품인데요, 장기 투자지만 더 안정성이 있다고 생각해요.<br>
부동산 이슈는 뉴스나 포털사이트에서 제가 필요한 정보를 쉽게 확인할 수 있고 헬로라이브TV로 진행 상태를 실시간 확인 할수 있어 선호하는 편입니다. </p></div>
	

	
<div class="m_sub" style="padding-top: 50px">P2P 투자로</div>
<div class="m_line15 m_bd_top"></div>
<div class="m_sub" style="padding-top: 5px">여유가 생긴다면</div>
<div class="m_line16 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">무엇을 하고 싶으신가요?</div>
<div class="m_line17 m_bd_top"></div>
<div class="m_text">결혼 전까지는 결혼 자금으로 모아두고 결혼 후에는 일정 부분은 다시
P2P 투자와 주식 투자를 할 거예요.<br>결혼 후에는 지금보다 큰 금액으로 투자를 할 생각입니다.</div>	
	

	
<div class="m_sub" style="padding-top: 50px">헬로펀딩에</div>
<div class="m_line18 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">다달이 받는 이자는</div>
<div class="m_line19 m_bd_top"></div>		
<div class="m_sub" style="padding-top: 5px">어디에 쓰고 있나요?</div>
<div class="m_line20 m_bd_top"></div>
<div class="m_text">지금은 잘 모으고 있어요.<br>
차곡차곡 모아서 그 돈으로 또 투자를 할 생각이에요.<br>
투자도 공부라고 생각하기 때문에 소액으로 조금씩 투자 경험을 늘린다면
나중에는 큰 도움이 될 거라고 생각합니다.<br>
<p class="pdt10">사고 싶은 것은 많은데 급여는 고정 지출과 생활비로 거의 소비가 되니
사회 초년생인 저에게는 사고 싶은 것을 다 산다는 건 약간 사치인 것 같아요.<br>
재테크로 차곡차곡 모아서 제가 사고픈 것도 사보고 싶어요 :)</p></div>		
	

	
<div class="img"><img src="/images/event/m_05.jpg"></div>		
	
	
<div class="m_sub" style="padding-top: 50px">헬로펀딩에 투자하고자</div>
<div class="m_line21 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px"> 하는 고객분들께</div>
<div class="m_line22 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">한 말씀해주신다면?</div>
<div class="m_line23 m_bd_top"></div>	
<div class="m_text">요즘 제 또래들은 재테크나 투자에 많은 관심을 가지고 있는 것 같습니다.
은행 금리가 너무 낮아 통장에는 돈을 넣어 두는 게 아니다..라고까지 말하고 있잖아요.
<p class="pdt10">저 같은 사회 초년생들은 부모님께 무조건 안정적인 예/적금을 해라라는 말을 많이 듣는데요.</p>
친구들과는 조금씩 재테크나 투자를 해서 돈을 모으자라고 얘기를 많이 합니다.
<p class="pdt10">실제로 오늘만 해도 제 친구에게 헬로펀딩을 소개해 주고 저의 재테크 방법을 알려줬어요^^</p>
이런 상품이나 투자가 있는지도 몰랐다고 하더라고요.
<p class="pdt10">일단 소액으로라도 투자를 시작해서 경험도 쌓고 이자도 받아보셨으면 좋겠습니다.<br>
직접 투자를 하고 예치금이 쌓이는 걸 확인하면 믿고 투자하실 수 있을 것 같아요.<br>
헬로펀딩에 투자하게 된다면 본인이 가지고 있는 돈을 단/장기상품에 분산투자하여 이자를 주기적으로 받을 수 있도록 하는 것을 권해드리고 싶습니다.</p></div>
	
	
<div class="img"><img src="/images/event/m_06.jpg"></div>
<div class="img"><img src="/images/event/m_title03.jpg"></div>		
	
	
<div class="m_sub" style="padding-top: 50px">ㅇㅇㅇ님께 헬로펀딩은?</div>
<div class="m_line24 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">ㅇㅇㅇ이다.</div>
<div class="m_line25 m_bd_top"></div>		
<div class="m_text">헬로펀딩은 저에게 ‘뜻밖의 이득’인 것 같아요.<br>
금리가 낮은 요즘 예/적금을 하는 사람들보다는 주식에 관심을 많이 갖는 것 같습니다.<br>
저는 주기적으로 확인하고 꾸준히 공부하고 매 순간 선택해야 하는 주식보다는 헬로펀딩에 투자해 매달 5일 날 이자 지급 문자를 받고
예치금이 쌓이는 것을 보면 진짜 생각지도 못한 뜻밖의 돈이 들어오는 느낌이에요 :)</div>
	
<div class="m_sub" style="padding-top: 50px">헬로펀딩에</div>
<div class="m_line26 m_bd_top"></div>	
<div class="m_sub" style="padding-top: 5px">하고 싶은 말이 있으시면</div>
<div class="m_line27 m_bd_top"></div>
<div class="m_sub" style="padding-top: 5px">한 말씀 부탁드립니다.</div>
<div class="m_line28 m_bd_top"></div>	
<div class="m_text">좋은 상품으로 투자자들에게 신뢰를 주고, 효율적인 투자방식을 고안하여 투자자들에게 편함을 주는 헬로펀딩에게 감사드립니다. <br> 
저도 주변 사람들에게 좋은 상품이 있으면 전파하도록 하겠습니다.  <br>
그리고 헬로펀딩이 앞으로 더 성공해서 모르는 사람이 없었으면 좋겠습니다*^^*</div>
			
<div class="img"><img src="/images/event/jang_8.jpg"></div>
<br><br>	
<div style="text-align: center;"><a href="https://www.hellofunding.co.kr/review"><img src="/images/event/m_bt.jpg"></a></div>	
<br><br>	
</div>



<!-- 본문내용 E N D -->
<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');


if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	//print_rr($_SERVER,'font-size:12px');
}

?>