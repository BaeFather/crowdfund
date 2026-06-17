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
				<li class="ball_01 regular">1</li>
				<li class="ball_02 regular">2</li>
			</ul>
		</div>

		
		
		<div class="result_loan">
			<div class="loan_form">
				
			<div class="loan_value">
			<h3>대출 예상 한도 금액</h3>
			<p>대출 가능 한도가 없습니다</p>
			</div>
				
				<table>
					<tr>
						<th class="first"><span class="del">아파트</span> 정보</th>
						<td class="first">화성 반송동 동탄나루마을 월드메르디앙반도유보라 아파트<br>102동 1301호 (13층)</td>
					</tr>
					<tr>
						<th><span class="del">아파트</span> 면적</th>
						<td>141.54m²</td>
					</tr>
					<tr>
						<th>담보시세</th>
						<td>20억 5,000만원 (국토부 실거래가 기준)</td>
					</tr>
					<tr>
						<th>가능금액</th>
						<td>10억 2,500만원</td>
					</tr>
				</table>
	
		

		<div class="fail_loan4">
			<p class="text">
				회원님이 선택하신 아파트의 경우 대출가능 한도가 없는 것으로 확인됩니다.<br>
				<span>선순위 대출이 한도를 초과</span>하였거나, <span>대출승인 지역이 아닌 경우</span> 한도가 조회 되지 않습니다.<br>
				다시 조회를 원하시거나 내용 수정이 필요하신 경우 아래 버튼을 클릭해주세요.
			</p>	
		</div>
		
		<div class="btn3">
			<a href="javascript:void(0);" id="btn3">다시조회하기</a>
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
		<div class="m_call">
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