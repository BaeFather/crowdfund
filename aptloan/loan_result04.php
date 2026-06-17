<?
include_once('./_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}
?>
<link href="css/loan.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>

<!-- 본문내용 START -->
<!--------------------웹----------------------------------------------------------->

<div id="content">


	<div id="loan">
		<div class="loan1">
			<h2>대출한도조회 및 대출 신청</h2>
			<p>한도조회는 신용등급에 영향을 주지 않습니다.</p>
		</div>
		<div class="loan3">
			<ul>
				<li class="ball_07 regular">1</li>
				<li class="ball_08 regular">2</li>
				<li class="ball_09 regular">3</li>
			</ul>
		</div>



		<div class="result_loan">
			<div class="loan_form">

				<div class="finish_loan4">
					<p><img src="img/finish.png"></p>
					<p class="text">
						헬로펀딩 아파트 담보대출을<span class="br"></span>신청해 주셔서 감사합니다.<br>
영업일 1일 이내 담당자 확인 후<span class="br"></span>연락드리겠습니다.
					</p>
				</div>

				<div class="btn3">
					<a href="https://www.hellofunding.co.kr/">확인 완료</a>
				</div>

				<div class="call">
					<ul>
						<li>
							아파트 담보대출상담이 필요하시면 언제든지 연락주세요!<br>
							<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
						</li>
						<li>
							<img src="img/call.png">
						</li>
						<li>
							1588-5210
						</li>

					</ul>
				</div>
				<div class="loan_warning">
					연계대출 이자율 연19.9%이내(연체금리 연 20%이내), 연계대출 시 법무비 등 부가비용이 발생할 수 있으며 신용점수가 하락될 수 있습니다.&nbsp;
					대출유형에 따라 중도상환수수료 등 조기상환 조건이 적용될 수 있습니다. &nbsp;플랫폼 이용 시 플랫폼이용수수료가 발생할 수 있습니다.&nbsp;
					과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.
				</div>		
				<div class="m_call">
					<div class="m_loan_warning">
						연계대출 이자율 연19.9%이내(연체금리 연 20%이내), 연계대출 시 법무비 등 부가비용이 발생할 수 있으며 신용점수가 하락될 수 있습니다.&nbsp;
						대출유형에 따라 중도상환수수료 등 조기상환 조건이 적용될 수 있습니다. &nbsp;플랫폼 이용 시 플랫폼이용수수료가 발생할 수 있습니다.&nbsp;
						과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.
					</div>	
					<ul>
						<li>
							상담이 필요하시면 언제든지 연락주세요!<br>
							<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
						</li>
						<li>
							<img src="img/call.png"><span>1588-5210</span>
						</li>

					</ul>
				</div>

			</div>
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