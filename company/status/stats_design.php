
<?
include_once('./_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

?>

<link href="stats.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>


<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->



	<div id="web">
		<div class="tops">사업 정보 공시</div>
		<div class="structure">
			<p class="h3">대출구조</p>
			<p><img src="img/structure.jpg" alt="헬로펀딩 구조도"></p>
				 
		</div>
		<div class="title">
			<ul>
				<li>
					<span class="h3">투자현황</span>
				</li>
				<li class="date">
					<select id="date_type" name="date_type" class="dates">
						<option value="monthly">월별</option>
						<option value="yearly">연도별</option>
					</select>
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
					<select id="monthly" name="monthly" class="month">
						<option value="202008">2020.08</option>
						<option value="202007">2020.07</option>
					</select>	
				</li>
			</ul>		 
		</div>
		<table class="investment">
			<thead>
				<tr>
					<th>누적 대출금액</th>
					<th>누적 상환금액</th>
					<th>대출잔액</th>
					<th>연체율 <span>(%)</span></th>
					<th>연체건수 <span>(건)</span></th>
					<th>부실률 <span>(%)</span></th>	
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>4,124<span>억</span> 9,971<span>만원</span></td>
					<td>3,669<span>억</span> 3,993<span>만원</span></td>
					<td>455<span>억</span> 5,978<span>만원</span></td>
					<td>0</td>
					<td>0</td>
					<td>0</td>			
			 	</tr>
			</tbody>
		
		</table>
		<div class="title">
			<ul>
				<li>
					<span class="h3">유형별 투자현황</span>
				</li>
				<li class="date">
					<select id="date_type" name="date_type" class="dates">
						<option value="monthly">월별</option>
						<option value="yearly">연도별</option>
					</select>
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
					<select id="monthly" name="monthly" class="month">
						<option value="202008">2020.08</option>
						<option value="202007">2020.07</option>
					</select>	
				</li>
			</ul>		 
		</div>
		<table class="type_investment">
			<thead>
				<tr>
					<th>상품 유형</th>
					<th>누적 대출금액 <span>(원)</span></th>
					<th>대출잔액 <span>(원)</span></th>
					<th>연체율 <span>(%)</span></th>
					<th>연체건수 <span>(건)</span></th>
					<th>부실채권 매각 <span>(건)</span></th>	
				</tr>	
			</thead>
			<tbody>
				<tr>
					<th>부동산PF</th>
					<td>139,392,500,000</td>
					<td>24,486,000,000</td>
					<td>0</td>
					<td>0</td>
					<td>0</td>	
				</tr>
				<tr>
					<th>부동산 담보</th>
					<td>94,234,200,000</td>
					<td>13,305,000,000</td>
					<td>0</td>
					<td>0</td>
					<td>0</td>	
				</tr>
				<tr>
					<th>매출채권</th>
					<td>172,133,010,000</td>
					<td>7,768,780,000</td>
					<td>0</td>
					<td>0</td>
					<td>0</td>	
				</tr>
				<tr>
					<th>동산</th>
					<td>6,740,000,000</td>
					<td>0</td>
					<td>5</td>
					<td>0</td>
					<td>0</td>	
				</tr>
				<tr>
					<th>합계</th>
					<td>412,499,710,000</td>
					<td>45,559,780,000</td>
					<td>0</td>
					<td>0</td>
					<td>0</td>	
				</tr>
			<tbody>			
		
		</table>
		
		<div class="title">
			<ul>
				<li>
					<span class="h3">부실채권 매각내역</span>
				</li>
				<li class="date">
					<select id="date_type" name="date_type" class="dates">
						<option value="monthly">월별</option>
						<option value="yearly">연도별</option>
					</select>
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
					<select id="monthly" name="monthly" class="month">
						<option value="202008">2020.08</option>
						<option value="202007">2020.07</option>
					</select>	
				</li>
			</ul>		 
		</div>
		<table class="disposal">
			<thead>
				<tr>
					<th>상품 유형</th>
					<th>상품 호번</th>
					<th>채권 원금</th>
					<th>매각 금액</th>
					<th>매각처</th>
					<th>매각 일자</th>	
				</tr>	
			</thead>
			<tbody>
				<tr>
					<th>부동산</th>
					<td>1000호</td>
					<td>10,000,000</td>
					<td>10,000,000</td>
					<td>A사</td>
					<td>2020-07-29</td>	
				</tr>
				<tr>
					<th>PF</th>
					<td>1001호</td>
					<td>20,000,000</td>
					<td>20,000,000</td>
					<td>B사</td>
					<td>2020-07-29</td>
				</tr>
				<tr>
					<td colspan="6">부실채권 매각내역이 없습니다.</td>
				</tr>
			<tbody>			
		</table>
		
		<div class="title">
			<ul>
				<li>
					<span class="h3">재무 현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
				</li>
			</ul>		 
		</div>
		<table class="finance">
			<tbody>
				<tr>
					<th>2019년 감사보고서</th>
					<td><a href=""><img src="img/view.png"></a></td>
				</tr>	
			</tbody>		
		</table>
		
		<div class="title">
			<ul>
				<li>
					<span class="h3">임직원 현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
				</li>
			</ul>		 
		</div>
		<table class="member">
			<thead>
				<tr>
					<th>임직원</th>
					<th>여신심사역</th>
					<th>전문인</th>
				</tr>	
			</thead>
			<tbody>
				<tr>
					<td>31명</td>
					<td>5명</td>
					<td>1명</td>
				</tr>
			<tbody>			
		</table>
		
		
		<div class="title">
			<ul>
				<li>
					<span class="h3">대주주 현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
				</li>
			</ul>		 
		</div>
		<table class="stockholder">
			<tbody>
				<tr>
					<td>(주) 헬로핀테크의 최대주주는 ooo 입니다.</td>
				</tr>	
			</tbody>		
		</table>
		
		
	</div>	


<!--------------------모바일----------------------------------------------------------->

	<div id="mobile">
			<div class="tops">사업 정보 공시</div>
			<div class="structure">
				<span class="h3">대출구조</span>
				<p><img src="img/m_structure.jpg" alt="헬로펀딩 구조도"></p>

			</div>
			<div class="title">
				<ul>
					<li>
						<span class="h3">투자현황</span>
					</li>
					<li class="date">
						<select id="date_type" name="date_type" class="dates">
							<option value="monthly">월별</option>
							<option value="yearly">연도별</option>
						</select>
						<select id="yearly" name="yearly" class="year"   style="display: none;">
							<option value="2020">2020</option>
							<option value="2019">2019</option>
						</select>
						<select id="monthly" name="monthly" class="month">
							<option value="202008">2020.08</option>
							<option value="202007">2020.07</option>
						</select>	
					</li>
				</ul>		 
			</div>
			<div class="wrap_investment">
				<table class="investment">
					<tr>
						<th>누적 대출금액</th>
						<td>4,124<span>억</span> 9,971<span>만원</span></td>
					</tr>
					<tr>
						<th>누적 상환금액</th>
						<td>3,669<span>억</span> 3,993<span>만원</span></td>
					</tr>
					<tr>
						<th>대출잔액</th>
						<td>455<span>억</span> 5,978<span>만원</span></td>
					</tr>
					<tr>
						<th>연체율 <span>(%)</span></th>
						<td>0</td>
					</tr>
					<tr>
						<th>연체건수 <span>(건)</span></th>
						<td>0</td>
					</tr>
					<tr>
						<th>부실률 <span>(%)</span></th>
						<td>0</td>
					</tr>
				</table>
		</div>
		<div class="title">
			<ul>
				<li>
					<span class="h3">유형별 투자현황</span>
				</li>
				<li class="date">
					<select id="date_type" name="date_type" class="dates">
						<option value="monthly">월별</option>
						<option value="yearly">연도별</option>
					</select>
					<select id="yearly" name="yearly" class="year"   style="display: none;">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
					<select id="monthly" name="monthly" class="month">
						<option value="202008">2020.08</option>
						<option value="202007">2020.07</option>
					</select>	
				</li>
			</ul>		 
		</div>
		<div class="box_type_investment">
			<div class="wrap_type_investment">
				<table class="type_investment">
					<thead>
						<tr>
							<th>상품 유형</th>
							<th>누적 대출금액 <span>(원)</span></th>
							<th>대출잔액 <span>(원)</span></th>
							<th>연체율 <span>(%)</span></th>
							<th>연체건수 <span>(건)</span></th>
							<th>부실채권 매각 <span>(건)</span></th>	
						</tr>	
					</thead>
					<tbody>
						<tr>
							<th>부동산PF</th>
							<td>139,392,500,000</td>
							<td>24,486,000,000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>	
						</tr>
						<tr>
							<th>부동산 담보</th>
							<td>94,234,200,000</td>
							<td>13,305,000,000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>	
						</tr>
						<tr>
							<th>매출채권</th>
							<td>172,133,010,000</td>
							<td>7,768,780,000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>	
						</tr>
						<tr>
							<th>동산</th>
							<td>6,740,000,000</td>
							<td>0</td>
							<td>5</td>
							<td>0</td>
							<td>0</td>	
						</tr>
						<tr>
							<th>합계</th>
							<td>412,499,710,000</td>
							<td>45,559,780,000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>	
						</tr>
					<tbody>			
				</table>
			</div>
		</div>	
		<div class="title">
			<ul>
				<li>
					<span class="h3">부실채권 매각내역</span>
				</li>
				<li class="date">
					<select id="date_type" name="date_type" class="dates">
						<option value="monthly">월별</option>
						<option value="yearly">연도별</option>
					</select>
					<select id="yearly" name="yearly" class="year"  style="display: none;">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
					<select id="monthly" name="monthly" class="month">
						<option value="202008">2020.08</option>
						<option value="202007">2020.07</option>
					</select>	
				</li>
			</ul>		 
		</div>
		<div class="box_disposal">
			<div class="wrap_disposal">
				<table class="disposal">
					<thead>
						<tr>
							<th>상품 유형</th>
							<th>상품 호번</th>
							<th>채권 원금</th>
							<th>매각 금액</th>
							<th>매각처</th>
							<th>매각 일자</th>	
						</tr>	
					</thead>
					<tbody>
						<tr>
							<th>부동산</th>
							<td>1000호</td>
							<td>10,000,000</td>
							<td>10,000,000</td>
							<td>A사</td>
							<td>2020-07-29</td>	
						</tr>
						<tr>
							<th>PF</th>
							<td>1001호</td>
							<td>20,000,000</td>
							<td>20,000,000</td>
							<td>B사</td>
							<td>2020-07-29</td>	
						</tr>
						<tr>
							<td colspan="6">부실채권 매각내역이 없습니다.</td>
						</tr>
					<tbody>			
				</table>
			</div>
		</div>
		<div class="title">
			<ul>
				<li>
					<span class="h3">재무현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
				</li>
			</ul>		 
		</div>
		<div class="wrap_finance">
			<table class="finance">
				<tbody>
					<tr>
						<th>2019년 감사보고서</th>
						<td><a href=""><img src="img/view.png"></a></td>
					</tr>	
				</tbody>		
			</table>
		</div>
		<div class="title">
			<ul>
				<li>
					<span class="h3">임직원현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
				</li>
			</ul>		 
		</div>
		<div class="wrap_member">
			<table class="member">
				<tr>
					<th>임직원</th>
					<td>31<span>명</span></td>
				</tr>
				<tr>
					<th>여신심사역</th>
					<td>5<span>명</span></td>
				</tr>
				<tr>
					<th>전문인</th>
					<td>1<span>명</span></td>
				</tr>
			</table>
		</div>
		<div class="title">
			<ul>
				<li>
					<span class="h3">대주주현황</span>
				</li>
				<li class="date">
					<select id="yearly" name="yearly" class="year">
						<option value="2020">2020</option>
						<option value="2019">2019</option>
					</select>
				</li>
			</ul>		 
		</div>
		<div class="wrap_stockholder">
			<table class="stockholder">
				<tbody>
					<tr>
						<td>(주) 헬로핀테크의 최대주주는<br><span>헬로펀딩, 헬로핀테크</span> 입니다.</td>
					</tr>	
				</tbody>		
			</table>
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
