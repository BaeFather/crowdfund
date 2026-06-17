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
<link href="S-Core-Dream-light/s-core-dream.css" rel="stylesheet">
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
			<p>대출신청시 안내 가능합니다.</p>
			</div>
				
				<table>
					<tr>
						<th class="first"><span class="del">아파트</span> 정보</th>
						<td class="first">화성 반송동 동탄나루마을 월드메르디앙반도유보라 아파트<br>102동 1301호 (13층)</td>
					</tr>
					<tr>
						<th>담보시세</th>
						<td>실거래가 확인중</td>
					</tr>
				</table>
			
	
		
		
		<form name="write_form" id="form" enctype="">
		<div class="re_loan4">
			<p class="title">대출신청 <span class="info">*대출가능 금액은 10억 2,500만원입니다.</span></p>
			<ul>
				<li class="td sum"><input class="loansum2" type="text" name="loansum2" value="" placeholder="대출신청 금액을 입력해주세요"><div class="won">만원</div></li>	
			</ul>
			<ul>
				<li><input class="name" type="text" name="name" value="" placeholder="이름을 입력해주세요"></li>
				<li><input class=tel type="text" name="tel" value="" placeholder="연락처를 '-' 없이 입력해주세요"></li>
			</ul>
			<ul>
				<li class="re_label_check"><label><input type="checkbox" name="check01" id="check01" value=""><span>개인정보 수집 및 이용에 동의합니다.</span></label></li>
			</ul>
				</li>
			</ul>
		</div>
		
		<div class="btn2">
			<a href="javascript:void(0);" id="btn2">대출신청하기</a>
		</div>
		</form>
		
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
