<?
###############################################################################
## 고유식별정보 처리 동의서
###############################################################################

include_once('./_common.php');

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

?>

<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/footer_contents.css" />

<div id="content">
	<h2 class="title">고유식별정보 처리 동의서</h2>
	<div class="content">
			<br><br>
			<p>주식회사 헬로핀테크(이하 “회사”라 함)는 투자 서비스 이용을 위하여 「개인정보 보호법」 및 「신용정보의 이용 및 보호에 관한 법률」에 따라 아래와 같이 고유식별정보 처리 동의를 받고자 합니다.<br><br>
			본인(고객)은 회사가 아래와 같이 본인의 고유식별정보를 수집 이용하는 것에 동의합니다.
			</p>

			<p class="head">고유식별정보 수집·이용 내역</p>
			<table>
				<tbody>
					<tr>
						<td>수집·이용 항목</td>
						<td>
							<ul>
								<li>1. 개인
									<ul>
										<li>- 성명, 주민등록번호</li>
									</ul>
								</li>
								<li>2. 법인
									<ul>
										<li>- 법인명, 사업자등록번호, 법인등록번호 및 고유번호, 소재지 및 대표자의 성명 및 고유식별정보</li>
									</ul>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>수집·이용 목적</td>
						<td>
							<ul>
								<li>1. 투자회원
									<ul>
										<li>- 원천징수 의무 이행</li>
										<li>- 중앙기록관리기관(금융결제원) 거래정보 등록</li>
										<li>- 실명확인</li>
									</ul>
								</li>
								<li>2. 대출회원
									<ul>
										<li>- 중앙기록관리기관(금융결제원) 거래정보 등록</li>
										<li>- 신용정보집중기관(한국신용정보원) 대출실행정보 등록</li>
										<li>- 신용정보조회</li>
										<li>- 실명확인</li>
									</ul>
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>보유 및 이용기간</td>
						<td>고유식별정보는 (금융)거래 종료일로부터 5년까지 보유·이용됩니다.<br />
							(금융)거래 종료일 후에는 금융사고 조사, 분쟁 해결, 민원처리, 법령상 의무이
							행만을 위하여 보유·이용됩니다.<br />
							<p class="ex">*(금융)거래 종료일이란 회사와 거래중인 모든 계약(대출, 투자상품 등)해지 및
							서비스(전자금융거래 등)가 종료된 날을 뜻합니다.</p>
						</td>
					</tr>
					<tr>
						<td>동의를 거부할 권리 및 불이익</td>
						<td>고객님은 동의를 거부할 권리가 있습니다.<br />
							다만, 동의를 거부할 경우 회원가입 및 서비스 이용이 불가능 합니다.
						</td>
					</tr>
				</tbody>
			</table>
			
			<p class="end-agree-txt">주식회사 헬로핀테크 귀중</p>

			<p class="end-txt">준법감시인 심사필 제2021-A-5 (2021.11.05)</p>
 
		</div>
	</div>
</div>



<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>