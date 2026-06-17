<?php
include_once('./_common.php');

$g5['title'] = '언론보도';

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');


?>
<!-- 본문내용 START -->

<link href="hello_news.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>


<div id="news_content">

	<div>
		<h2 class="top_title">헬로펀딩 <span class="sky">TALK</span></h2>
		<p class="top_text">헬로펀딩에서 열심히 일하고 있는 직원들을 소개합니다.<br class="br"></p>
	</div>
	
	<div class="content">
		<div class="list_div">
		<div class="list-line"></div>	

			<div class="list_warp">
				<div class="list_obj">
					<div class="thumbnail"><a href="/magazine/m_interview/02/"><img src="img/board_01.jpg" /></a></div>
					<div class="subject"><a href="/magazine/m_interview/02/">앞으로 더욱 고도화된 금융 상품을 투자자분들께 보여드리고 싶습니다.</a> </div>
					<div class="contents">
						<span class="name">미래금융팀 박재현대리</span>
						<br/>
						<a href="/magazine/m_interview/02/">안녕하세요 저는 헬로펀딩 미래금융팀의 박재현 대리라고 합니다.<br>
현재 기업이나 소상공인의 매출채권을 유동화하는 투자상품 출시를 담당하고 있고 이외 금융기관과의 투자나 협업·협의 등의 업무를 맡고 있습니다.</a>	
					</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				<div class="list-line"></div>
			<div class="list_warp">
				<div class="list_obj">
					<div class="thumbnail"><a href="/magazine/m_interview/01/"><img src="img/board_02.jpg" /></a></div>
					<div class="subject"><a href="/magazine/m_interview/01/">은행에서의 36년, 헬로펀딩에서 좋은 밑거름이 되었습니다.</a> </div>
					<div class="contents">
						<span class="name">준법감시인 김인이사</span>
						<br/>
						<a href="/magazine/m_interview/01/">준법감시인은 금융기관의 임직원이 직무를 수행함에 있어 따라야 할 법령, 내부통제 기준 준수 여부를 사전적으로 점검하고 관리하면서 위반 가능성을 해소한다.
직원교육을 통해 조직으로 정비하는 역할 뿐만 아니라 넓게는 금융소비자를 보호할 수 있는 감시자 역할도 맡는다.</a>	
					</div>
					</div>
				</div>
			<div style="clear:both;"></div>
				<div class="list-line"></div>
			</div>
		
		<br><br><br><br><br><br><br>

		</div>

	</div>
	<br><br>

</div>





<!-- 본문내용 E N D -->

<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>
