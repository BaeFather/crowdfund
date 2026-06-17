<?

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($_COOKIE['renewal_mode']) {
	include_once(G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/company/company.skin.test.php');
	return;
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" type="text/css" href="'.$company_skin_url.'/style.css?ver=20210127">', 0);
add_javascript('<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-rwdImageMaps/1.6/jquery.rwdImageMaps.min.js"></script>', 0);
?>

<style>
#content {background-image: none; width:100%; margin:0 auto;}
#content .top_title {font-size:24px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 20px 0 10px; background-color: #fff; text-align: center;}
#content .top_title .sky {color:#33a5ed;}
#content .top_text {font-size:14px; color:#777; font-family:'SpoqaHanSans','sanserif'; text-align: center; padding-bottom: 40px;}
</style>

<div id="content" class="lazy">
	<div>
		<h2 class="top_title">헬로펀딩 <span class="sky">회사소개</span></h2>
		<p class="top_text">헬로펀딩은 정직과 신뢰로 행복한 금융을 만듭니다.<br class="br"></p>
		<!--p class="top_text">모두가 이기는 행복한 금융을 만듭니다.<br class="br"></p-->
	</div>
	<div class="content2">

		<img class="lazy" data-src="/images/company/comp_img01_1m.jpg" width="100%">
		<img class="lazy" data-src="/images/company/comp_img01_2m.jpg" width="100%">
		<img class="lazy" data-src="/images/company/comp_img01_3m.jpg" width="100%">
		<img class="lazy" data-src="/images/company/comp_img03_1m.jpg" width="100%">
		<img class="lazy" data-src="/images/company/comp_img03_2m.jpg" width="100%">
		<img class="lazy" data-src="/images/company/comp_img07_m.jpg" width="100%">

		<!--헬로펀딩 투자자 데이터-->
		<div id="invest_data">
			<div class="tbg">
				<table style="border-bottom:1px solid #41c3ff;">
					<tr>
						<td class="data_info" width="32%">
							평균 수익률(연)
							<span><?=$NUJUK_STATUS['averageReturn']?></span>
						</td>
						<td class="r_line"><td>
						<td class="data_info" width="36%">
							누적 대출액
							<span><?=$NUJUK_STATUS['investAmount']?></span>
						</td>
						<td class="r_line"><td>
						<td class="data_info" width="31%">
							평균 투자기간
							<span><?=$NUJUK_STATUS['averageInvMonth']?></span>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td class="data_info" width="36%">
							회원 평균 누적 투자액
							<span><?=$NUJUK_STATUS['averageInvAmount']?></span>
						</td>
						<td class="r_line"><td>
						<td class="data_info" width="32%">
							연체율 <b id="overdue-claim-mark" class="claim-mark">!</b>
							<span><?=$NUJUK_STATUS['overduePerc']?></span>
						</td>
						<td class="r_line"><td>
						<td class="data_info"  width="31%">
							부실율 <b id="bankruptcy-claim-mark" class="claim-mark">!</b>
							<span><?=$NUJUK_STATUS['bankruptcy']?></span>
						</td>
					</tr>
				</table>
			</div>
			<div style="text-align:center;padding-top:6px;font-size:12px;"><?=preg_replace("/-/", ".", G5_TIME_YMD)?> 기준</div>
			<div class="data_bot">
				※ 헬로펀딩은 투자심의위원회의 심의를 통과한 담보 상품만을 출시하여 서비스 오픈 후
				현재까지 연체율 0%를 기록하고 있습니다.
			</div>
		</div>

		<img class="lazy" data-src="/images/company/comp_img04_2mN.jpg" width="100%">


		<div id="vip_member">
			<!--위원약력 2017.09.20-->
			<!--남기중 위원장-->
			<div id="member_m01"></div>
			<div id="member_m_info1"><img src="/images/company/member01_m.png" width="100%"></div>

			<!--최수석 위원-->
			<div id="member_m02"></div>
			<div id="member_m_info2"><img src="/images/company/member02_m.png" width="100%"></div>

			<!--채영민 위원-->
			<div id="member_m03"></div>
			<div id="member_m_info3"><img src="/images/company/member04_m.png" width="100%"></div>

			<!--김인 위원-->
			<div id="member_m04"></div>
			<div id="member_m_info4"><img src="/images/company/member05_m.png" width="100%"></div>

			<!--김숙현 위원-->
			<div id="member_m05"></div>
			<div id="member_m_info5"><img src="/images/company/member07_m.png" width="100%"></div>

			<img class="lazy" data-src="/images/company/comp_img05_m2.jpg" width="100%">
		</div>

		<script type="text/javascript">
		$('#member_m01').on('click',function(event) { event.stopPropagation(); reset_man5(1); $('#member_m_info1').toggle(); });
		$('#member_m02').on('click',function(event) { event.stopPropagation(); reset_man5(2); $('#member_m_info2').toggle(); });
		$('#member_m03').on('click',function(event) { event.stopPropagation(); reset_man5(3); $('#member_m_info3').toggle(); });
		$('#member_m04').on('click',function(event) { event.stopPropagation(); reset_man5(4); $('#member_m_info4').toggle(); });
		$('#member_m05').on('click',function(event) { event.stopPropagation(); reset_man5(5); $('#member_m_info5').toggle(); });

		$(document).on('click',function() { $('#member_m_info1, #member_m_info2, #member_m_info3, #member_m_info4, #member_m_info5').hide(); });

		function reset_man5(ex) {
			for(var i=1 ; i<=6 ; i++) {
				if(i!=ex) $('#member_m_info'+i).hide();
			}
		}
		</script>


		<img class="lazy" data-src="/images/company/comp_img10_m.jpg" width="100%">

		<!--회사전경-->
		<div class="comp-view-list">
			<div class="swiper-container s8">
				<div class="swiper-wrapper">
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp001_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp007_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp006_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp008_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp013_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp005_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp003_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp009_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp004_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp015_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp010_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp014_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp011_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp012_m.jpg" width="100%"></div>
					<div class="swiper-slide content comp-view"><img class="swiper-lazy" data-src="/images/company/comp002_m.jpg" width="100%"></div>
				</div>
			</div>
			<div class="swiper-button-next comp-view-list-button-next" onFocus="blur();"></div>
			<div class="swiper-button-prev comp-view-list-button-prev" onFocus="blur();"></div>
		</div>
		<script type="text/javascript">
		// 회사소개 슬라이드
		swiper_8 = new Swiper('.s8', {
			loop: true,
			loopedSlides: 15,
			speed: 800,
			slidesPerView: 1,
			slidesPerGroup: 1,
			preloadImages: false,
			lazy: {
				loadPrevNext: true,
				loadPrevNextAmount: true
			},
			navigation: {
				nextEl: '.comp-view-list-button-next',
				prevEl: '.comp-view-list-button-prev'
			}
			//,autoplay: { delay: 3000 },
		});
		</script>

		<!--임직원프로필영역-->
		<img class="lazy" data-src="/images/company/comp_img13_m.jpg" width="100%" style="clear:both;padding-top:35px;">

		<div class="profile">
			<ul>
<?
for($i=0,$j=1; $i<$sawon_count; $i++,$j++) {
	if($j%2==1 && $j<>1) echo "</ul><ul>";
?>
				<li>
					<div id="amember_m<?=$j?>" class="member_m" onClick="view_name('<?=$j?>');"><div id="amember_m_info<?=$j?>" class="member_m_info"><?=$sawon[$i][1]?><p><?=$sawon[$i][3]?></p></div></div>
					<img class="lazy" data-src="/images/company/profile/<?=$sawon[$i][2]?>" width="100%">
				</li>
<?
}
?>
				<li style="padding-top:0;">
					<div id="amember_m<?=$j?>" class="member_m" onclick="view_name('<?=$j?>');"><div id="amember_m_info<?=$j?>" class="member_m_info">미래의 주인공<p>헬로펀딩의 비전을 이해하고 함께할 수 있는 당신</p></div></div>
					<img class="lazy" data-src="/images/company/profile/26.jpg" width="100%">
				</li>
			</ul>
			<script>
			function view_name(idx) {
				reset_name(idx);
				event.stopPropagation();
				$('#amember_m_info'+idx).toggle();
			}
			function reset_name(idx) {
<?
for($i=0,$j=1; $i<$sawon_count; $i++,$j++) {
?>
				if(idx!="<?=$j?>") $('#amember_m_info<?=$j?>').hide();
<?
}
?>
			}
			</script>

			<!--div style="clear:both;padding:2% 0 0 0;margin:0 auto;"><img class="lazy" data-src="/images/company/profile/all_sawon_m.jpg" width="100%"></div-->
			<!--헬로스토리-->
			<img class="lazy" data-src="/images/company/comp_img11_m.jpg" width="100%">
			<div class="comp-view-list2">
			<div class="swiper-container s9">
				<div class="swiper-wrapper">
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2020.02</span><br/>헬로펀딩 2020 "윤리강령 선포식"</div><img class="swiper-lazy" data-src="/images/company/workshop16_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.12</span><br/>함께해서 좋은 임직원 전체회식</div><img class="swiper-lazy" data-src="/images/company/workshop15_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.12</span><br/>한 해를 되돌아 본 2019년 종무식</div><img class="swiper-lazy" data-src="/images/company/workshop14_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.12</span><br/>2019 핀테크 & 블록체인 채용설명회</div><img class="swiper-lazy" data-src="/images/company/workshop13_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.08</span><br/>창립 3주년 기념행사</div><img class="swiper-lazy" data-src="/images/company/workshop12_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.06</span><br/>2019년 헬로펀딩워크샵</div><img class="swiper-lazy" data-src="/images/company/workshop11_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.05</span><br/>부동산 실무자소양교육</div><img class="swiper-lazy" data-src="/images/company/workshop10_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.05</span><br/>2019 서울머니쇼</div><img class="swiper-lazy" data-src="/images/company/workshop09_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.09</span><br/>동아 재테크 핀테크쇼</div><img class="swiper-lazy" data-src="/images/company/workshop08_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.07</span><br/>헬로펀딩 2018 "윤리강령 선포식"</div><img class="swiper-lazy" data-src="/images/company/workshop07_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.05</span><br/>성장과 발전의 2018년 워크샵</div><img class="swiper-lazy" data-src="/images/company/workshop06_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.05</span><br/>투자자와의 만남 '2018서울머니쇼'</div><img class="swiper-lazy" data-src="/images/company/workshop05_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2017.05</span><br/>체육대회의 꽃 시상식</div><img class="swiper-lazy" data-src="/images/company/workshop04_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2017.05</span><br/>화합과 단합을 위한 전직원 체육대회</div><img class="swiper-lazy" data-src="/images/company/workshop03_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2017.01</span><br/>도약을 위한 2017년 첫 워크샵</div><img class="swiper-lazy" data-src="/images/company/workshop02_m.jpg" width="100%"></div>
					<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2016.11</span><br/>헬로펀딩 가족 식사 모임</div><img class="swiper-lazy" data-src="/images/company/workshop01_m.jpg" width="100%"></div>
				</div>
			</div>
			<div class="swiper-button-next comp-view-list2-button-next" onFocus="blur();"></div>
			<div class="swiper-button-prev comp-view-list2-button-prev" onFocus="blur();"></div>
		</div>
		<script type="text/javascript">
		// 헬로스토리
		swiper_9 = new Swiper('.s9', {
			loop: true,
			loopedSlides: 15,
			speed: 800,
			slidesPerView: 1,
			slidesPerGroup: 1,
			preloadImages: false,
			lazy: {
				loadPrevNext: true,
				loadPrevNextAmount: true
			},
			navigation: {
				nextEl: '.comp-view-list2-button-next',
				prevEl: '.comp-view-list2-button-prev'
			}
			//,autoplay: { delay: 3000 },
		});
		</script>
		<div class="blog_btn" onClick="window.open('https://blog.naver.com/hellofunding')"><span>블로그 바로가기 클릭!</span><br/>더 많은 스토리는 블로그에서 확인하세요.</div>

		<!-- 기업연혁-->
			<p style="height:30px;clear:both;"></p>
			<img class="lazy" data-src="/images/company/comp_img12_m.jpg" width="100%" style="padding-top:60px;">
			<ul class="tabs">
				<li class="tab01 on" rel="history_tab1"><a onClick="go_2021()" style="cursor:pointer;">2021</a></li>
				<li class="line">|</li>
				<li class="tab02 off" rel="history_tab1"><a onClick="go_2020()" style="cursor:pointer;">2020</a></li>
				<li class="line">|</li>
				<li class="tab03 off" rel="history_tab2"><a onClick="go_2019();" style="cursor:pointer;">2019</a></li>
				<li class="line">|</li>
				<li class="tab04 off" rel="history_tab3"><a onclick="go_2018();" style="cursor:pointer;">2018</a></li>
				<li class="line">|</li>
				<li class="tab05 off" rel="history_tab4"><a onclick="go_2017();" style="cursor:pointer;">2017</a></li>
				<li class="line">|</li>
				<li class="tab06 off" rel="history_tab4"><a onclick="go_2016();" style="cursor:pointer;">2016</a></li>
			</ul>

			
			
			<div id="history_tab1" >
				<span class="months">3월</span> <span>헬로페이 누적이용액 2,000억원 달성</span><br/><br/>
				<span class="months">1월</span> <span>누적투자금액 5,000억원 달성</span><br/><br/>

			</div>
			
			<div id="history_tab2" style="display:none;" >
				<span class="months">7월</span> <span>누적투자금액 4,000억원 달성</span><br/><br/>
				<span class="months">3월</span> <span>누적투자금액 3,000억원 달성</span><br/><br/>
				<span class="months">2월</span> <span>헬로펀딩 2020 윤리강령 선포식</span><br/><br/>
			</div>

			<div id="history_tab3" style="display:none;" >
				<span class="months">12월</span> <span>2019 핀테크 & 블록체인 채용설명회</span><br/><br/>
				<span class="months">10월</span> <span>누적투자금액 2,000억원 달성</span><br/><br/>
				<span class="months">8월</span> <span>창립 3주년 기념행사</span><br/><br/>
				<span class="months">6월</span> <span>누적투자금액 1,500억원 달성</span><br/><br/>
				<span class="months">5월</span> <span style="display:inline-block;">자산운용사 누적투자금액 200억원 달성<br/>
				부동산개발실무소양교육 부동산PF특강<br/>
				2019 서울 머니쇼 부스참여 (코엑스)</span><br/>
			</div>
			<div id="history_tab4" style="display:none;">
				<span class="months">12월</span> <span>누적투자금액 1,000억원 달성</span><br/><br/>
				<span class="months">11월</span> <span style="display:inline-block;">면세점 매출채권 상품출시 카카오페이 <br/>간편송금</span><br/><br/>
				<span class="months">8월</span> <span>금융플랫폼 핀크(Finnq) 제휴</span><br/><br/>
				<span class="months">7월</span> <span>헬로펀딩 2018 윤리강령 선포식</span><br/><br/>
				<span class="months">6월</span> <span>한국 핀테크 산업협회 가입</span><br/><br/>
				<span class="months">5월</span> <span style="display:inline-block;">자산운용사 누적투자금액 100억원 달성<br/>
				매일경제 주최 2018 서울 머니쇼 <br/>부스참여 (코엑스)<br/>
				확정매출채권 상품 출시</span><br/><br/>
				<span class="months">2월</span> <span style="display:inline-block;">금융감독원 등록 <br/>
				누적투자금액 500억원 달성 </span><br/><br/>
				<span class="months">1월</span> <span>외감대상법인</span><br/><br/>
			</div>
			<div id="history_tab5" style="display:none;">
				<span class="months">11월</span>
				<span>킨텍스 주최 인사이드핀테크 부스참여 (킨텍스)</span><br/><br/>
				<span class="months">10월</span>
				<span style="display:inline-block;">썬앤트리자산운용 업무제휴 협약 체결 <br/>(기업평가 완료)</span><br/><br/>
				<span class="months">9월</span>
				<span style="display:inline-block;">조선일보 주최 2017 부동산 트렌드쇼<br/> 부스참여(서울무역전시관)<br/>동아일보 주최 2017 동아재테크· 핀테크쇼 <br/>부스참여(코엑스)</span><br/><br/>

			  <span class="months">8월</span>
				<span style="display:inline-block;">교보리얼코(CM) 업무협약 체결<br/>법무법인 에이펙스 원리금수취대행계약 체결<br/>(만일의 헬로핀테크 부도시 원리금수취업무<br/> 대행)<br/>P2P금융최초 PF현장 라이브 스트리밍 서비스<br/> ‘헬로라이브TV’ 오픈<br/>누적투자금액 300억원 달성</span><br/><br/>

			  <span class="months">7월</span>
				<span style="display:inline-block;">상시 투자자 방문신청 ‘보고싶습니다.’<br/> 지속운영시작</span><br/><br/>

				<span class="months">6월</span>
				<span style="display:inline-block;">한국경제TV 주최 2017 부동산엑스포<br/> 부스참여 (코엑스)<br/>피델리스자산운용 업무제휴 협약 체결<br/> (기업평가 완료)<br/>한국부동산리츠투자자문협회 업무제휴<br/> 협약 체결</span><br/><br/>

			  <span class="months">5월</span>
				<span>신한은행 P2P자금 신탁계약 체결</span><br/><br/>

				<span class="months">4월</span>
				<span>외부회계검사 무결점 완료 (신승회계법인)</span><br/><br/>

				<span class="months">3월</span>
				<span style="display:inline-block;">한국P2P금융협회 회원사 가입<br/>한국경제TV ‘예스P2P’ 생방송시작 <br/>(최수석 부대표 출연)<br/>누적투자금액 100억원 달성</span><br/><br/>
			</div>
			<div id="history_tab6" style="display:none;">
				<span class="months">11월</span>  창성건설㈜ 업무제휴 협약 체결<br/><br/>
				<span class="months">10월</span><span style="display:inline-block;">서울신용평가(주) 업무제휴 협약 체결<br/>
					 한국인터넷진흥원 웹보안점검 <br/> 최우수등급 획득 (SSL report A+)<br/>
					 (주)하나자산신탁 MOU체결</span><br/><br/>
			    <span class="months">9월</span><span style="display:inline-block;">헬로펀딩 공식오픈<br/>
					헬로펀딩 투자심의위원회 출범<br/>
					'크라우드 펀딩 투자 보호 시스템’ 특허 출원<br/>
					(주)경일감정평가법인 업무제휴 협약 체결</span><br/><br/>
			</div>
			<script>
					function go_2021() {
					$("#history_tab1").css("display","");
						$('.tab01').removeClass('off');
						$('.tab01').removeClass('on');
						$('.tab01').addClass('on');
					$("#history_tab2").css("display","none");
						$('.tab02').removeClass('off');
						$('.tab02').removeClass('on');
						$('.tab02').addClass('off');
					$("#history_tab3").css("display","none");
						$('.tab03').removeClass('off');
						$('.tab03').removeClass('on');
						$('.tab03').addClass('off');
					$("#history_tab4").css("display","none");
						$('.tab04').removeClass('off');
						$('.tab04').removeClass('on');
						$('.tab04').addClass('off');
					$("#history_tab5").css("display","none");
						$('.tab05').removeClass('off');
						$('.tab05').removeClass('on');
						$('.tab05').addClass('off');
					$("#history_tab6").css("display","none");
						$('.tab06').removeClass('off');
						$('.tab06').removeClass('on');
						$('.tab06').addClass('off');	
				}
				function go_2020() {
					$("#history_tab1").css("display","none");
						$('.tab01').removeClass('off');
						$('.tab01').removeClass('on');
						$('.tab01').addClass('off');
					$("#history_tab2").css("display","");
						$('.tab02').removeClass('off');
						$('.tab02').removeClass('on');
						$('.tab02').addClass('on');
					$("#history_tab3").css("display","none");
						$('.tab03').removeClass('off');
						$('.tab03').removeClass('on');
						$('.tab03').addClass('off');
					$("#history_tab4").css("display","none");
						$('.tab04').removeClass('off');
						$('.tab04').removeClass('on');
						$('.tab04').addClass('off');
					$("#history_tab5").css("display","none");
						$('.tab05').removeClass('off');
						$('.tab05').removeClass('on');
						$('.tab05').addClass('off');
					$("#history_tab6").css("display","none");
						$('.tab06').removeClass('off');
						$('.tab06').removeClass('on');
						$('.tab06').addClass('off');
				}
				function go_2019() {
					$("#history_tab1").css("display","none");
						$('.tab01').removeClass('off');
						$('.tab01').removeClass('on');
						$('.tab01').addClass('off');
					$("#history_tab2").css("display","none");
						$('.tab02').removeClass('off');
						$('.tab02').removeClass('on');
						$('.tab02').addClass('off');
					$("#history_tab3").css("display","");
						$('.tab03').removeClass('off');
						$('.tab03').removeClass('on');
						$('.tab03').addClass('on');
					$("#history_tab4").css("display","none");
						$('.tab04').removeClass('off');
						$('.tab04').removeClass('on');
						$('.tab04').addClass('off');
					$("#history_tab5").css("display","none");
						$('.tab05').removeClass('off');
						$('.tab05').removeClass('on');
						$('.tab05').addClass('off');
					$("#history_tab6").css("display","none");
						$('.tab06').removeClass('off');
						$('.tab06').removeClass('on');
						$('.tab06').addClass('off');
				}
				function go_2018() {
					$("#history_tab1").css("display","none");
						$('.tab01').removeClass('off');
						$('.tab01').removeClass('on');
						$('.tab01').addClass('off');
					$("#history_tab2").css("display","none");
						$('.tab02').removeClass('off');
						$('.tab02').removeClass('on');
						$('.tab02').addClass('off');
					$("#history_tab3").css("display","none");
						$('.tab03').removeClass('off');
						$('.tab03').removeClass('on');
						$('.tab03').addClass('off');
					$("#history_tab4").css("display","");
						$('.tab04').removeClass('off');
						$('.tab04').removeClass('on');
						$('.tab04').addClass('on');
					$("#history_tab5").css("display","none");
						$('.tab05').removeClass('off');
						$('.tab05').removeClass('on');
						$('.tab05').addClass('off');
					$("#history_tab6").css("display","none");
						$('.tab06').removeClass('off');
						$('.tab06').removeClass('on');
						$('.tab06').addClass('off');
				}
				function go_2017() {
					$("#history_tab1").css("display","none");
						$('.tab01').removeClass('off');
						$('.tab01').removeClass('on');
						$('.tab01').addClass('off');
					$("#history_tab2").css("display","none");
						$('.tab02').removeClass('off');
						$('.tab02').removeClass('on');
						$('.tab02').addClass('off');
					$("#history_tab3").css("display","none");
						$('.tab03').removeClass('off');
						$('.tab03').removeClass('on');
						$('.tab03').addClass('off');
					$("#history_tab4").css("display","none");
						$('.tab04').removeClass('off');
						$('.tab04').removeClass('on');
						$('.tab04').addClass('off');
					$("#history_tab5").css("display","");
						$('.tab05').removeClass('off');
						$('.tab05').removeClass('on');
						$('.tab05').addClass('on');
					$("#history_tab6").css("display","none");
						$('.tab06').removeClass('off');
						$('.tab06').removeClass('on');
						$('.tab06').addClass('off');
				}
				function go_2016() {
					$("#history_tab1").css("display","none");
						$('.tab01').removeClass('off');
						$('.tab01').removeClass('on');
						$('.tab01').addClass('off');
					$("#history_tab2").css("display","none");
						$('.tab02').removeClass('off');
						$('.tab02').removeClass('on');
						$('.tab02').addClass('off');
					$("#history_tab3").css("display","none");
						$('.tab03').removeClass('off');
						$('.tab03').removeClass('on');
						$('.tab03').addClass('off');
					$("#history_tab4").css("display","none");
						$('.tab04').removeClass('off');
						$('.tab04').removeClass('on');
						$('.tab04').addClass('off');
					$("#history_tab5").css("display","none");
						$('.tab05').removeClass('off');
						$('.tab05').removeClass('on');
						$('.tab05').addClass('off');
					$("#history_tab6").css("display","");
						$('.tab06').removeClass('off');
						$('.tab06').removeClass('on');
						$('.tab06').addClass('on');
				}
			</script>
		</div>

		<!-- 헬로펀딩 투자후기-->
		<div class="review">
			<div class="review_t">
				<p>
					헬로펀딩 투자후기
					<span></span>
				</p>
			</div>
			<?=review('theme/basic', 6, 3600); /* 1시간단위 갱신 */ ?>
		</div>

		<div class="partner_logo">
			<p>
				제휴사<span></span>
			</p>
			<p style="height:30px;"></p>
			<ul>
				<li><a href="https://www.shinhan.com/index.jsp" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo01_m.jpg" alt="신한은행" ></a></li>
				<li><a href="http://p2plending.or.kr/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo02_m.jpg" alt="한국P2P금융협회" ></a></li>
				<li><a href="http://www.hanatrust.com/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo03_m.jpg" alt="하나자산신탁" ></a></li>
			</ul>
			<ul>
				<li><a href="http://www.scri.co.kr/index.jsp" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo04_m.jpg" alt="SCR서울신용평가" ></a></li>
				<li><a href="http://www.karic.or.kr/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo05_m.jpg" alt="한국부동산리츠투자자문협회" ></a></li>
				<li><a href="https://www.kyoborealco.co.kr/realco/indexRealco.html" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo07_m.jpg" alt="교보리얼코" ></a></li>
			</ul>
			<ul>
				<li><a href="http://www.apexlaw.co.kr/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo09_m.jpg" alt="법무법인 에이펙스" ></a></li>
				<li><a href="http://www.fidelisam.co.kr/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo11_m.jpg" alt="피델리스" ></a></li>
				<li><a href="http://www.kyungilnet.co.kr/main/main.php" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo12_m.jpg" alt="경일감정평가법인" ></a></li>
			</ul>
			<ul>
				<li><a href="http://korfin.kr/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo14_m.jpg" alt="한국핀테크산업협회"></a></li>
				<li><a href="https://www.finnq.com/" target="_blank"><img class="lazy" data-src="<?=G5_THEME_IMG_URL?>/main/client_logo13_m.jpg" alt="핀크"></a></li>
				<li><a href="https://www.sci.co.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo15_m.jpg" alt="sci평가정보"></a></li>
			</ul>
			<p style="height:30px;clear:both;"></p>
		</div>

	</div>
</div>

<script type="text/javascript">
var msg = "대출잔액 대비 상환일이 30일 이상 지연된 잔여원금 비율 (한국P2P금융협회 기준)";
$('#overdue-claim-mark').webuiPopover({ title: "연체율", content: msg, closeable: true, width: 220, arrow: true, offsetLeft: -30, trigger: "click", placement: 'bottom', backdrop: false});
var msg = "약정된 상환이 일부 혹은 전부 지연되기 시작해 90일 이상 경과한 대출 <br><br>부실률 = 부실잔여원금 / 총 누적대출액 (P2P금융협회 기준)";
$('#bankruptcy-claim-mark').webuiPopover({ title: "부실률", content: msg, closeable: true, width: 330, arrow: true, offsetLeft: -2, trigger: "click", placement: 'bottom-left', backdrop: false});
</script>
<script type="text/javascript">
function view1(opt) { member_info1.style.display = (opt) ? "block" : "none"; }
function view2(opt) { member_info2.style.display = (opt) ? "block" : "none"; }
function view3(opt) { member_info3.style.display = (opt) ? "block" : "none"; }
function view4(opt) { member_info4.style.display = (opt) ? "block" : "none"; }
function view5(opt) { member_info5.style.display = (opt) ? "block" : "none"; }
function view6(opt) { member_info6.style.display = (opt) ? "block" : "none"; }
function view7(opt) { member_info7.style.display = (opt) ? "block" : "none"; }
</script>