<?
include_once('./_common.php');
include_once(G5_PATH . '/pid_check.inc.php');		// pid 유입체크 및 쿠키생성이 필요한 페이지에만 include

//while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }
//while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = preg_replace("/(\'|\"|\#|\=|\(|\)|\+|\%|\*)/iu", "$1;", $v); }
while( list($k, $v) = each($_REQUEST) ) {
	if(!is_array($k) ) {
		${$k} = addslashes(clean_xss_tags(trim($v)));
		${$k} = preg_replace("/(\'|\"|\#|\=|\(|\)|\+|\%|\*)/iu", "$1;", ${$k});
	}
}


if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


$tab = $_REQUEST['tab'];

?>
<link rel="stylesheet" type="text/css" href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css">
<link rel="stylesheet" type="text/css" href="//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css">
<link rel="stylesheet" type="text/css" href="/investment/investor/investor.css">

<script>
/*탭메뉴*/
$(document).ready(function() {
	$('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	});

	<? if ($tab>0 and !G5_IS_MOBILE) { ?>
	$('#pc_tab<?=$_REQUEST["tab"]?>').click();
	<? } ?>
})
</script>

<script>
/*탭메뉴*/
$(document).ready(function() {
	$('ul.m-tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.m-tabs li').removeClass('mcurrent');
		$('.m-tab-content').removeClass('mcurrent');

		$(this).addClass('mcurrent');
		$("#"+tab_id).addClass('mcurrent');
	});

	<? if ($tab>0 and G5_IS_MOBILE) { ?>
	$('#mb_tab<?=$_REQUEST["tab"]?>').click();
	<? } ?>
})
</script>

<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->
<div id="investor_wrap">
	<div class="top_container">
		<h2 class="title">투자자 유형안내</h2>
		<p class="top_text">P2P 투자자 유형과 유형에 맞는 투자한도를 확인하세요.</p>
		<ul class="tabs">
			<li id="pc_tab1" class="tab-link current mg-r20" data-tab="tab-1">소득적격 투자자</li>
			<li id="pc_tab2" class="tab-link mg-r20" data-tab="tab-2">전문 투자자</li>
			<li id="pc_tab3" class="tab-link" data-tab="tab-3">법인 투자자</li>
		</ul>
	</div>
<!------------웹------------------------------------------------------------------->

<!------------소득적격 컨텐츠---------------------------------------------------------------->
	<div id="tab-1" class="tab-content current">
		<div class="visual_box">
			<div class="visual_img">
				<h3>소득적격 투자자란?</h3>
				<p>소득적격 투자자는 '소득요건 구비 등 적격투자자'에 해당하는 개인 투자자로<br>
				<span>1억원 한도 내에서</span> 자유롭게 투자 가능합니다.</p>
			</div>
		</div>
		<div class="limit_box">
			<ul>
				<li>소득적격 투자자<br/><span>투자한도</span></li>
				<li>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>일반 투자자</th>
								<th>소득적격 투자자</th>
								<th>전문/법인 투자자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>투자한도</th>
								<td>3,000만원</td>
								<td>1억원</td>
								<td rowspan="2">총 한도 없음 <br><span>* 상품당 모집금액의 40% 이내<br>(여신 및 금융기관의 경우 부동산 담보 상품은 상품당 모집금액의 20%)</span></td>
							</tr>
							<tr>
								<th>동일차주 투자한도</th>
								<td>500만원</td>
								<td>2,000만원</td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		<div class="ondition_box">
			<ul>
				<li>소득적격 투자자<br><span>자격요건</span></li>
				<li>
					<p class="onditon_text">1. 이자 및 배당소득 2천만원 초과<br>2. 사업 및 근로소득 1억원 초과<br><span class="onditon_point">* 위 2가지 조건 중 1가지라도 충족되면 전환 가능!</span></p>
				</li>
			</ul>
		</div>
		<div class="document_box">
			<ul>
				<li>소득적격 투자자<br><span>필요서류</span></li>
				<li class="interest">
					<p class="interest_title">이자 / 배당소득 해당자</p>
					<p class="interest_text">&emsp;종합소득과세표준확정신고서 <br>&emsp;종합소득세신고서접수증</p>
				</li>
				<li class="business">
					<p class="business_title">사업 / 근로소득 해당자</p>
					<p class="business_text">&emsp;사업 - 종합소득과세표준확정신고서<br>&emsp;&emsp;&emsp;&ensp;&nbsp;종합소득세신고서접수증<br>&emsp;근로 - 근로소득원천징수영수증</p>
				</li>
			</ul>
			<p class="notice">*&ensp;공통사항&ensp;:&ensp;①&ensp;발급처 : 홈텍스 홈페이지 발급 가능 <a href="https://www.hometax.go.kr" target="_blank";>(https://www.hometax.go.kr/ )</a>&emsp;&emsp;②&ensp;직전과세기간 기준</p>
		</div>
		<div class="change_box">
			<div class="change">
				<ul>
					<li>소득적격 투자자<br><span>변경방법</span></li>
					<li><img src="/investment/investor/img/step_01.png" alt="01.헬로펀딩 홈페이지 가입 > 02.회원정보 투자자 유형에서 소득적격 투자자 선택 > 03. 투자자 유형 변경 후 증빙서류 첨부 > 04. 증빙서류 검토 후 소득적격 투자자로 전환"></li>
				</ul>
			</div>
		</div>
		<!--div class="eventbn_box"><a href="/event/investor_event2/investor2.php"><img src="/investment/investor/img/event_bn01.png" alt="소득적격투자자 이벤트"></a></div-->
	</div>

<!------------전문 컨텐츠------------------------------------------------------------------->
	<div id="tab-2" class="tab-content" name="no2">
		<div class="visual_box">
			<div class="visual_img">
				<h3>전문 투자자란?</h3>
				<p>금융투자에 대한 전문지식과 소유자산 규모 등을 기준으로 금융투자회사로부터<br>자격을 부여받은 개인 투자자로 <span>투자한도 없이</span> 자유롭게 투자 가능합니다.</p>
			</div>
		</div>
		<div class="limit_box">
			<ul>
				<li>전문 투자자<br><span>투자한도</span></li>
				<li>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>일반 투자자</th>
								<th>소득적격 투자자</th>
								<th>전문/법인 투자자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>투자한도</th>
								<td style="border-right:1px solid #ddd">3,000만원</td>
								<td>1억원</td>
								<td rowspan="2">총 한도 없음 <br><span>* 상품당 모집금액의 40% 이내<br>(여신 및 금융기관의 경우 부동산 담보 상품은 상품당 모집금액의 20%)</span></td>
							</tr>
							<tr>
								<th>동일차주 투자한도</th>
								<td style="border-right:1px solid #ddd">500만원</td>
								<td>2,000만원</td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		<div class="ondition_box">
			<ul>
				<li>전문 투자자<br><span>자격요건</span></li>
				<li class="essential">
					<ul>
						<li><p class="essential_title">필수조건</p>	</li>
						<li><p class="essential_text">최근 5년 중 1년 이상 금융투자상품 계좌를 유지하고,<br>월말 평균잔고 기준 5천만원 이상 보유 </p></li>
					</ul>
				</li>
				<br>
				<li class="select">
					<ul>
						<li><p class="select_title">선택조건 (택1)</p></li>
						<li><p class="select_text">1. 본인의 연 소득 1억원 또는 부부합산 연 소득 1.5억원 이상<br>2. 순자산(거주주택) 5억원 이상<br>3. 금융관련 전문성</p>	</li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="document_box">
			<ul>
				<li>전문 투자자<br><span>필요서류</span></li>
				<li>
					<p class="document_text">금융투자회사(증권사)에서 발급한 전문 투자자 확인증<br><span class="document_point">* 전문 투자자 확인증(등록증) 발급 절차는 각 증권사 별로 상이할 수 있으니<br>
					&ensp;&nbsp;신청 또는 문의는 각 증권사로 문의하시면 더욱 정확한 답변을 받으실 수 있습니다.</span></p>
				</li>
			</ul>
		</div>
		<div class="change_box">
			<div class="change">
				<ul>
					<li>전문 투자자<br><span>변경방법</span></li>
					<li><img src="/investment/investor/img/step_02.png" alt="01.헬로펀딩 홈페이지 가입 > 02.회원정보 투자자 유형에서 전문 투자자 선택 > 03. 투자자 유형 변경 후 증빙서류 첨부 > 04. 증빙서류 검토 후 전문 투자자로 전환"></li>
				</ul>
			</div>
		</div>
		<!--div class="eventbn_box"><a href="/event/investor_event2/investor.php"><img src="/investment/investor/img/event_bn02.png" alt="전문투자자 이벤트"></a></div-->
	</div>

<!------------법인 컨텐츠------------------------------------------------------------------->
	<div id="tab-3" class="tab-content">
		<div class="visual_box">
			<div class="visual_img company">
				<h3>법인 투자자란?</h3>
				<p>법에 의하여 권리/의무의 주체로서의 자격을 부여받은 사람으로<br>온라인투자연계금융업법 상
					<span>투자한도 없이</span> 투자자로서 자유롭게 투자 가능합니다.</p>
				<p class="bt"><a href="/care_service/">헬로펀딩 법인투자안내 바로가기</a></p>
			</div>
		</div>
		<div class="limit_box">
			<ul>
				<li>법인 투자자<br><span>투자한도</span></li>
				<li>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>일반 투자자</th>
								<th>소득적격 투자자</th>
								<th>전문/법인 투자자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>투자한도</th>
								<td style="border-right:1px solid #ddd">3,000만원</td>
								<td>1억원</td>
								<td rowspan="2">총 한도 없음 <br><span>* 상품당 모집금액의 40% 이내<br>(여신 및 금융기관의 경우 부동산 담보 상품은 상품당 모집금액의 20%)
</span></td>
							</tr>
							<tr>
								<th>동일차주 투자한도</th>
								<td style="border-right:1px solid #ddd">500만원</td>
								<td>2,000만원</td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		<div class="document_box">
			<ul>
				<li>법인 투자자<br><span>필요서류</span></li>
				<li>
					<p class="document_text">법인 등기사항전부증명서(말소사항포함) / 사업자등록증 / <br>주주명부 / 법인 인감증명서 / 대표자 신분증 사본 / 실제소유자 양식 / 법인 통장 사본
					<br><span class="document_point">* 비영리 법인의 경우 위 증빙서류 외 정관<br>* 대부법인인 경우에는 대부업등록증 포함<br>* 모든 서류는 3개월 이내의 것</span>
					</p>

				</li>
			</ul>
		</div>
		<div class="change_box">
			<div class="change">
				<ul>
					<li>법인 투자자<br><span>변경방법</span></li>
					<li><img src="/investment/investor/img/step_03.png" alt="01.헬로펀딩 홈페이지에서 법인회원 가입 > 02.법인회원 가입에 필요한 증빙서류 첨부 > 03. 증빙서류 검토 후 법인회원 가입 승인 *개인회원에서 법인회원으로 전환은 되지 않습니다."></li>
				</ul>
			</div>
		</div>
		<!--div class="eventbn_box"><a href="/event/investor_event2/investor.php"><img src="/investment/investor/img/event_bn02.png" alt="법인회원 이벤트"></a></div-->
	</div>
</div>


<!------------모바일------------------------------------------------------------------>
<div id="m_investor_wrap">
	<div class="m_top_container">
		<h2 class="title">투자자 유형안내</h2>
		<p class="top_text">P2P 투자자 유형과 유형에 맞는<br>투자한도를 확인하세요.</p>
		<ul class="m-tabs">
			<li id="mb_tab1" class="m-tab-link mcurrent mg-r20" data-tab="m-tab-1">소득적격 투자자</li>
			<li id="mb_tab2" class="m-tab-link mg-r20" data-tab="m-tab-2">전문 투자자</li>
			<li id="mb_tab1" class="m-tab-link" data-tab="m-tab-3">법인 투자자</li>
		</ul>
	</div>

<!------------소득적격 컨텐츠---------------------------------------------------------------->
	<div id="m-tab-1" class="m-tab-content mcurrent">
		<div class="m_visual_box">
			<div class="m_visual_img">
				<h3>소득적격 투자자란?</h3>
				<p>소득적격 투자자는<br>'소득요건 구비 등 적격투자자'에<br>해당하는 개인 투자자로<br>
				<span>1억원 한도 내에서</span><br>자유롭게 투자 가능합니다.</p>
			</div>
		</div>
		<div class="m_limit_box">
			<ul>
				<li><p>소득적격 투자자 <span>투자한도</span></p></li>
				<li>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>일반 투자자</th>
								<th>소득적격 투자자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>투자한도</th>
								<td>3,000만원</td>
								<td>1억원</td>
							</tr>
							<tr>
								<th>동일차주<br>투자한도</th>
								<td>500만원</td>
								<td>2,000만원</td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		<div class="m_ondition_box">
			<ul>
				<li><p>소득적격 투자자 <span>자격요건</span></p></li>
				<li>
					<p class="onditon_text">1. 이자 및 배당소득 2천만원 초과<br>2. 사업 및 근로소득 1억원 초과<br><span class="onditon_point">* 위 2가지 조건 중 1가지라도 충족되면 전환 가능!</span></p>
				</li>
			</ul>
		</div>
		<div class="m_document_box">
			<ul>
				<li><p>소득적격 투자자 <span>필요서류</span></p></li>
				<li class="interest">
					<p class="interest_title">이자 / 배당소득 해당자</p>
					<p class="interest_text">종합소득과세표준확정신고서 <br>종합소득세신고서접수증</p>
				</li>
				<li class="business">
					<p class="business_title">사업 / 근로소득 해당자</p>
					<p class="business_text">사업 - 종합소득과세표준확정신고서<br>&emsp;&emsp;&ensp;&nbsp;종합소득세신고서접수증<br>근로 - 근로소득원천징수영수증</p>
				</li>
			</ul>
			<p class="notice">*&ensp;공통사항<br>
				① 발급처 : 홈텍스 발급 가능 <a href="https://www.hometax.go.kr" target="_blank";>(https://www.hometax.go.kr/ )</a><br>
				② 직전과세기간 기준</p>
		</div>
		<div class="m_change_box">
			<div class="m_change">
				<ul>
					<li class="ch"><p>소득적격 투자자 <span>변경방법</span></p></li>
					<li>
						<div class="new_m">
							<ul>
								<li><p><span>01</span>&ensp; 헬로펀딩 회원가입</p></li>
								<li><p><span>02</span>&ensp; 회원정보 투자자 유형에서 소득적격 투자자 선택</p></li>
								<li><p><span>03</span>&ensp; 투자자 유형 변경 후 증빙서류 첨부</p></li>
								<li><p><span>04</span>&ensp; 증빙서류 검토 후 소득적격 투자자로 전환</p></li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<!--div class="m_eventbn_box"><a href="/event/investor_event2/investor2.php"><img src="/investment/investor/img/m_event_bn01.png" alt="소득적격투자자 이벤트"></a></div-->
	</div>

<!------------전문 컨텐츠------------------------------------------------------------------->
	<div id="m-tab-2" class="m-tab-content">
		<div class="m_visual_box">
			<div class="m_visual_img">
				<h3>전문 투자자란?</h3>
				<p>금융투자에 대한 전문지식과<br>소유자산 규모 등을 기준으로<br>금융투자회사로부터 자격을 부여받은<br>개인 투자자로
				<span>투자한도 없이</span><br>자유롭게 투자 가능합니다.</p>
			</div>
		</div>

		<div class="m_limit_box">
			<ul>
				<li><p>전문 투자자 <span>투자한도</span></p></li>
				<li>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>일반 투자자</th>
								<th>전문/법인 투자자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>투자한도</th>
								<td>3,000만원</td>
								<td rowspan="2">총 한도 없음 <br><span>* 상품당 모집금액의 40% 이내(여신 및 금융기관의 경우 부동산 담보 상품은 상품당 모집금액의 20%)
</span></td>
							</tr>
							<tr>
								<th>동일차주<br>투자한도</th>
								<td>500만원</td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		<div class="m_document_box">
			<ul>
				<li><p>전문 투자자 <span>자격요건</span></p></li>
				<li class="interest">
					<p class="interest_title">필수조건</p>
					<p class="interest_text">최근 5년 중 1년 이상 금융투자상품 계좌를 유지하고, 월말 평균잔고 기준 5천만원 이상 보유 </p>
				</li>
				<li class="business">
					<p class="business_title">선택조건 (택1)</p>
					<p class="business_text">1. 본인의 연 소득 1억원 또는 부부합산 연 소득 1.5억원 이상<br>2. 순자산(거주주택) 5억원 이상<br>3. 금융관련 전문성</p>
				</li>
			</ul>
		</div>
		<div class="m_ondition_box">
			<ul>
				<li><p>전문 투자자 <span>필요서류</span></p></li>
				<li>
					<p class="onditon_text">금융투자회사(증권사)에서 발급한<br>전문 투자자 확인증<br><br><span>* 전문 투자자 확인증(등록증) 발급 절차는 각 증권사 별로 상이할 수 있으니 신청 또는 문의는 각 증권사로 문의하시면 더욱 정확한 답변을 받으실 수 있습니다.</span></p>
				</li>
			</ul>
		</div>
		<div class="m_change_box">
			<div class="m_change">
				<ul>
					<li class="ch"><p>전문 투자자 <span>변경방법</span></p></li>
					<li>
						<div class="new_m">
							<ul>
								<li><p><span>01</span>&ensp; 헬로펀딩 회원가입</p></li>
								<li><p><span>02</span>&ensp; 회원정보 투자자 유형에서 전문 투자자 선택</p></li>
								<li><p><span>03</span>&ensp; 투자자 유형 변경 후 증빙서류 첨부</p></li>
								<li><p><span>04</span>&ensp; 증빙서류 검토 후 전문 투자자로 전환</p></li>
							</ul>
						</div>
					</li>

				</ul>
			</div>
		</div>
		<!--div class="m_eventbn_box"><a href="/event/investor_event2/investor.php"><img src="/investment/investor/img/m_event_bn02.png" alt="전문투자자 이벤트"></a></div-->
	</div>

<!------------법인 컨텐츠------------------------------------------------------------------->
	<div id="m-tab-3" class="m-tab-content">
		<div class="m_visual_box">
			<div class="m_visual_img">
				<h3>법인 투자자란?</h3>
				<p>법에 의하여 권리/의무의 주체로서<br>자격을 부여받은 사람으로<br>온라인투자연계금융업법 상
					<span>투자한도 없이</span><br>투자자로서 자유롭게 투자 가능합니다.</p>
				<p class="m_bt"><a href="/care_service/">법인투자안내 바로가기</a></p>
			</div>
		</div>
		<div class="m_limit_box">
			<ul>
				<li><p>법인 투자자 <span>투자한도</span></p></li>
				<li>
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>일반 투자자</th>
								<th>전문/법인 투자자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>투자한도</th>
								<td>3,000만원</td>
								<td rowspan="2">총 한도 없음 <br><span>* 상품당 모집금액의 40% 이내(여신 및 금융기관의 경우 부동산 담보 상품은 상품당 모집금액의 20%)
</span></td>
							</tr>
							<tr>
								<th>동일차주<br>투자한도</th>
								<td>500만원</td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		<div class="m_ondition_box">

			<ul>
				<li><p>법인 투자자 <span>필요서류</span></p></li>
				<li>
					<p class="onditon_text">법인 등기사항전부증명서(말소사항포함) / 사업자등록증 / 주주명부 / 법인 인감증명서 / 대표자 신분증 사본 / 실제소유자 양식 / 법인 통장 사본
<br><br><span>* 비영리 법인의 경우 위 증빙서류 외 정관<br>* 대부법인인 경우에는 대부업등록증 포함<br>* 모든 서류는 3개월 이내의 것
</span></p>
				</li>
			</ul>

		</div>
		<div class="m_change_box">
			<div class="m_change">
				<ul>
					<li class="ch"><p>법인 투자자 <span>변경방법</span></p></li>
					<li>
						<div class="new_m">
							<ul>
								<li><p><span>01</span>&ensp; 헬로펀딩 법인회원 가입</p></li>
								<li><p><span>02</span>&ensp; 법인회원 가입에 필요한 증빙서류 첨부</p></li>
								<li><p><span>03</span>&ensp; 증빙서류 검토 후 법인회원 가입 승인</p></li>
							</ul>
						</div>
						<br>
						<p class="notice">* 개인회원에서 법인회원 전환은 되지 않습니다.</p>
					</li>
				</ul>
			</div>
		</div>
		<!--div class="m_eventbn_box"><a href="/event/investor_event2/investor.php"><img src="/investment/investor/img/m_event_bn03.png" alt="법인회원 이벤트"></a></div-->
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