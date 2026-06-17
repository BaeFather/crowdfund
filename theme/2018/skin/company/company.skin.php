<?

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
$NUJUK_STATUS['investAmount']       = price_cutting($NUJUK_CACHE["investAmount"]).'원';				// 누적대출액
$NUJUK_STATUS['repayPrincipal']     = price_cutting($NUJUK_CACHE["repayPrincipal"]).'원';			// 누적상환액
$NUJUK_STATUS['investIngAmount']    = price_cutting($NUJUK_CACHE["investIngAmount"]).'원';		// 대출잔액
$NUJUK_STATUS['averageReturn']      = floatRtrim($NUJUK_CACHE["averageReturn"]).'%';					// 평균수익률(연)
$NUJUK_STATUS['investSuccessCount'] = $NUJUK_CACHE["investSuccessCount"];											// 투자 성공건수
$NUJUK_STATUS['overduePerc']        = floatRtrim($NUJUK_CACHE["overduePerc"]).'%';						// 연체율
$NUJUK_STATUS['bankruptcy']         = floatRtrim($NUJUK_CACHE["bankruptcy"]).'%';							// 부실율
*/

$company_skin_dir = str_replace(G5_URL, '', $company_skin_url);

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('	<link rel="stylesheet" href="'.$company_skin_dir.'style.css?ver=20180713" />', 0);
?>

<style>
#content {background-image: none;}
#content .top_title {font-size:36px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 60px 0 10px; background-color: #fff;}
#content .top_title .sky {color:#33a5ed;}
#content .top_text {font-size:18px; color:#777; padding-bottom: 20px; font-family:'SpoqaHanSans','sanserif'}
</style>


<!-- 본문내용 START -->
<div id="content">
	<div>
		<h2 class="top_title">헬로펀딩 <span class="sky">회사소개</span></h2>
		<p class="top_text">헬로펀딩은 정직과 신뢰로 행복한 금융을 만듭니다.<br class="br"></p>
		<!--p class="top_text">모두가 이기는 행복한 금융을 만듭니다.<br class="br"></p-->
	</div>
	<div style="position:absolute;top:930px;width:100%;background:url('/images/company/comp_img_bg.jpg') center top no-repeat;min-width:1300px;"> <img src="/images/company/comp_img03.jpg"></div>
	<div class="content">

		<img src="/images/company/comp_img01.jpg" width="100%"/>
		<!-- <img src="/images/company/comp_img02.jpg" width="100%" id="d2"/>-->

		<div style="height:1060px;"></div>
		<img src="/images/company/comp_img07.jpg" width="100%"/>
		<!--헬로펀딩 투자자 데이터-->
		<div id="invest_data">

			<div class="tbg">
				<table>
					<tr>
						<td class="data_info" width="33%">
							평균 수익률(연)
							<span><?=$NUJUK_STATUS['averageReturn']?></span>
						</td>
						<td class="r_line">
						</td>
						<td class="data_info" width="33%">
							누적 대출액
							<span><?=$NUJUK_STATUS['investAmount']?></span>
						</td>
						<td class="r_line">
						</td>
						<td class="data_info" width="33%">
							평균 투자기간
							<span><?=$NUJUK_STATUS['averageInvMonth']?></span>
						</td>
					</tr>
				</table>

				<table>
					<tr>
						<td class="data_info" width="33%">
							회원 평균 누적 투자액
							<span><?=$NUJUK_STATUS['averageInvAmount']?></span>
						</td>
						<td class="r_line"></td>
						<td class="data_info" width="33%">
							<span>연체율 <b id="bankruptcy-claim-mark" class="claim-mark">!</b></span>
							<span><?=$NUJUK_STATUS['overduePerc']?></span>
						</td>
						<td class="r_line"></td>
						<td class="data_info"  width="33%">
							<span>부실율 <b id="overdue-claim-mark" class="claim-mark">!</b></span>
							<span><?=$NUJUK_STATUS['bankruptcy']?></span>
						</td>
					</tr>
				</table>
				<!--table>
					<tr>
						<td class="data_info2" width="50%">
							회원 평균 누적 투자액
							<span><?=$NUJUK_STATUS['averageInvAmount']?></span>
						</td>
						<td class="r_line">
						</td>
						<td class="data_info2" width="50%">
							<span>연체율 <b id="bankruptcy-claim-mark" class="claim-mark">!</b></span>
							<span><?=$NUJUK_STATUS['overduePerc']?></span>
						</td>

						<td class="data_info2" >
						</td>

<? if(false) { ?>
						<!--
						<td class="r_line"></td>
						<td class="data_info">
							<span>부실율 <b id="overdue-claim-mark" class="claim-mark">!</b></span>
							<span><? echo $bankruptcy; ?>%</span>
						</td>
						-->
<? } ?>
					<!--/tr>
				</table-->
			</div>
			<div style="text-align:center;padding-top:10px;"><span style="font-size:16px;"><?=preg_replace("/-/", ".", G5_TIME_YMD)?> 기준</span></div>
			<!--div class="data_bot">
				※ 헬로펀딩은 투자심의위원회의 심의를 통과한 담보 상품만을 출시하여 서비스 오픈 후<br>
				현재까지 연체율 0%를 기록하고 있습니다.
			</div-->
			<div class="data_bot">
				※ 헬로펀딩은 투자심의위원회의 심의를 통과한 담보 상품만을 출시하여 2016년 서비스 오픈 후<br>
				현재까지 5년간 연체율 0%를 기록하고 있습니다.
			</div>
		</div>
		<img src="/images/company/comp_img04N.jpg" width="100%"/>

		<div style="position:absolute;margin-top:519px;margin-left:395px;width:75px;height:20px;cursor:pointer;" onmouseover="view1(true)" onmouseout="view1(false)"></div>
		<div id="member_info1" style="display:none;position:absolute;margin-top:540px;margin-left:240px; z-index:10;">
			<img src="/images/company/member01.png" width="100%"/>
		</div>

		<div style="position:absolute;margin-top:519px;margin-left:693px;width:75px;height:20px;cursor:pointer;" onmouseover="view2(true)" onmouseout="view2(false)"></div>
		<div id="member_info2" style="display:none;position:absolute;margin-top:540px;margin-left:485px; z-index:10;">
			<img src="/images/company/member02.png" width="100%"/>
		</div>

		<div style="position:absolute;margin-top:807px;margin-left:250px;width:75px;height:20px;cursor:pointer;" onmouseover="view3(true)" onmouseout="view3(false);"></div>
		<div id="member_info3" style="display:none;position:absolute;margin-top:828px;margin-left:100px; z-index:10;">
			<img src="/images/company/member04.png" width="100%"/>
		</div>

		<div style="position:absolute;margin-top:807px;margin-left:540px;width:75px;height:20px;cursor:pointer;" onmouseover="view4(true)" onmouseout="view4(false);"></div>
		<div id="member_info4" style="display:none;position:absolute;margin-top:828px;margin-left:390px; z-index:10;">
			<img src="/images/company/member05.png" width="100%"/>
		</div>

		<div style="position:absolute;margin-top:807px;margin-left:845px;width:75px;height:20px;cursor:pointer;" onmouseover="view5(true)" onmouseout="view5(false);"></div>
		<div id="member_info5" style="display:none;position:absolute;margin-top:828px;margin-left:690px; z-index:10;">
			<img src="/images/company/member07.png" width="100%"/>
		</div>

<? if(false) { ?>
		<!--최윤현 위원-->
		<div style="position:absolute;margin-top:807px;margin-left:845px;width:75px;height:20px;cursor:pointer;" onmouseover="view6(true)" onmouseout="view6(false);"></div>
		<div id="member_info6" style="display:none;position:absolute;margin-top:828px;margin-left:680px; z-index:10;">
			<img src="/images/company/member03.png" width="100%"/>
		</div>-->
		<!--이정우 위원-->
		<div style="position:absolute;margin-top:862px;margin-left:385px;width:75px;height:20px;cursor:pointer;" onmouseover="view6(true)" onmouseout="view6(false);"></div>
		<div id="member_info6" style="display:none;position:absolute;margin-top:889px;margin-left:223px; z-index:10;">
			<img src="/images/company/member06.png" width="100%"/>
		</div>
<? } ?>
		<!--투자심의위원회 / 투자심의자문위원회
		<img src="/images/company/comp_img05.jpg" width="100%"/>
		<img src="/images/company/comp_img08.jpg" width="100%"/>-->
		<img src="/images/company/comp_img05_2.jpg" width="100%"/>
		<!--사무실 전경 슬라이드-->
		<img src="/images/company/comp_img10.jpg" width="100%"/>
		   <div class="comp-view-list">
				 <div class="swiper-container s8">
					<div class="swiper-wrapper">
						<div class="swiper-slide content comp-view"><img src="/images/company/comp001.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp007.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp006.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp008.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp013.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp005.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp003.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp009.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp004.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp015.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp010.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp014.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp011.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp012.jpg" alt="" /></div>
						<div class="swiper-slide content comp-view"><img src="/images/company/comp002.jpg" alt="" /></div>
					</div>
				</div>
				<div class="swiper-button-next comp-view-list-button-next"></div>
				<div class="swiper-button-prev comp-view-list-button-prev"></div>
			</div>

			<!--임직원프로필영역-->
			<img src="/images/company/comp_img13.jpg" width="100%"/>

			<div class="profile">
				<ul>
<?
for ($i=1 ; $i<=count($sawon) ; $i++) {
	if ($i%4=="1" and $i<>1) echo "</ul><ul>";
	?>
					<li>
						<div class="member_info_flow_on" onmouseover="view_name('<?=$i?>',true)" onmouseout="view_name('<?=$i?>',false)"></div>
						<div id="amember_info<?=$i?>" class="member_info_flow"><?=$sawon[$i-1][1]?> <br/> <p><?=$sawon[$i-1][3]?></p></div>
						<img src="/images/company/profile/<?=$sawon[$i-1][2]?>" alt="<?=$sawon[$i-1][1]?>"/>
					</li>
	<?
}
?>
<? if ($i%4=="1" and $i<>1) echo "</ul><ul>"; ?>

					<li>
						<div class="member_info_flow_on" onmouseover="view_name('<?=$i?>',true)" onmouseout="view_name('<?=$i?>',false)"></div>
						<div id="amember_info<?=$i?>" class="member_info_flow">미래의 주인공 <br/> <p>헬로펀딩의 비전을 이해하고 함께할 수 있는 당신</p></div>
						<img src="/images/company/profile/26.jpg" alt="미래의 주인공"/>
					</li>

				</ul>
			</div>
			<!--div style="padding-top:30px;clear:both;"><img src="/images/company/profile/all_sawon.jpg" width="100%"/></div-->

			<!--헬로펀딩 스토리영역
			<img src="/images/company/comp_img11.jpg" width="100%"/>
			<div style="position:absolute;width:420px;height:80px;left:39.5%;cursor:pointer;" onclick="window.open('http://blog.naver.com/PostList.nhn?blogId=hellofunding&from=postList&categoryNo=14')"></div>
			<img src="/images/company/comp_img11_btn.jpg" width="100%" >-->
			<img src="/images/company/hello_story_tit.jpg" width="100%"/>
			   <div class="comp-view-list2">
					 <div class="swiper-container s9">
						<div class="swiper-wrapper">
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2020.02</span><br/>헬로펀딩 2020 "윤리강령 선포식"</div><img src="/images/company/workshop16.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.12</span><br/>함께해서 좋은 임직원 전체회식</div><img src="/images/company/workshop15.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.12</span><br/>한 해를 되돌아 본 2019년 종무식</div><img src="/images/company/workshop14.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.12</span><br/>2019 핀테크 & 블록체인 채용설명회</div><img src="/images/company/workshop13.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span class="s_tit_t">2019.08</span><br/>창립 3주년 기념행사</div><img src="/images/company/workshop12.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.06</span><br/>2019년 헬로펀딩워크샵</div><img src="/images/company/workshop11.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.05</span><br/>부동산 실무자소양교육</div><img src="/images/company/workshop10.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2019.05</span><br/>2019 서울머니쇼</div><img src="/images/company/workshop09.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.09</span><br/>동아 재테크 핀테크쇼</div><img src="/images/company/workshop08.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.07</span><br/>헬로펀딩 2018 "윤리강령 선포식"</div><img src="/images/company/workshop07.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.05</span><br/>성장과 발전의 2018년 워크샵</div><img src="/images/company/workshop06.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2018.05</span><br/>투자자와의 만남 '2018서울머니쇼'</div><img src="/images/company/workshop05.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2017.05</span><br/>체육대회의 꽃 시상식</div><img src="/images/company/workshop04.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2017.05</span><br/>화합과 단합을 위한 전직원 체육대회</div><img src="/images/company/workshop03.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2017.01</span><br/>도약을 위한 2017년 첫 워크샵</div><img src="/images/company/workshop02.jpg" alt="" /></div>
							<div class="swiper-slide  comp-view2"><div class="s_tit"><span>2016.11</span><br/>헬로펀딩 가족 식사 모임</div><img src="/images/company/workshop01.jpg" alt="" /></div>

						</div>
					</div>
					<div class="swiper-button-next comp-view-list2-button-next"></div>
					<div class="swiper-button-prev comp-view-list2-button-prev"></div>
				</div>
				<div class="blog_btn" onClick="window.open('https://blog.naver.com/hellofunding')"><span>블로그 바로가기 클릭!</span><br/>더 많은 스토리는 블로그에서 확인하세요.</div>
			<!--헬로펀딩 연혁-->
			<img src="/images/company/history_tit01.jpg" width="100%"/>
			<ul class="tabs">
				<li class="tab01 on" rel="history_tab1"><a onClick="go_2021()" style="cursor:pointer;">2021</a></li>
				<li class="line">|</li>
				<li class="tab02 off" rel="history_tab2"><a onClick="go_2020();" style="cursor:pointer;">2020</a></li>
				<li class="line">|</li>
				<li class="tab03 off" rel="history_tab3"><a onclick="go_2019();" style="cursor:pointer;">2019</a></li>
				<li class="line">|</li>
				<li class="tab04 off" rel="history_tab4"><a onclick="go_2018();" style="cursor:pointer;">2018</a></li>
				<li class="line">|</li>
				<li class="tab05 off" rel="history_tab5"><a onclick="go_2017();" style="cursor:pointer;">2017</a></li>
				<li class="line">|</li>
				<li class="tab06 off" rel="history_tab6"><a onclick="go_2016();" style="cursor:pointer;">2016</a></li>

			</ul>

		
		
			<div id="history_tab1" >
				<span class="months">3월</span> <span>헬로페이 누적이용액 2,000억원 달성</span><br/><br/>
				<span class="months">1월</span> <span>누적투자금액 5,000억원 달성</span><br/><br/>

			</div>

			<div id="history_tab2"  style="display:none;">
				<!-- <span class="months">7월</span> <span>누적대출액 1,600억원 달성</span><br/><br/> -->
				<span class="months">7월</span> <span>누적투자금액 4,000억원 달성</span><br/><br/>
				<span class="months">3월</span> <span>누적투자금액 3,000억원 달성</span><br/><br/>
				<span class="months">2월</span> <span>헬로펀딩 2020 윤리강령 선포식</span><br/><br/>
			</div>

			<div id="history_tab3" style="display:none;" >
				<!-- <span class="months">7월</span> <span>누적대출액 1,600억원 달성</span><br/><br/> -->
				<span class="months">12월</span> <span>2019 핀테크 & 블록체인 채용설명회</span><br/><br/>
				<span class="months">10월</span> <span>누적투자금액 2,000억원 달성</span><br/><br/>
				<span class="months">8월</span> <span>창립 3주년 기념행사</span><br/><br/>
				<span class="months">6월</span> <span>누적투자금액 1,500억원 달성</span><br/><br/>
				<span class="months">5월</span> <span style="display:inline-block;">부동산개발실무소양교육 부동산PF특강<br/>
				2019 서울 머니쇼 부스참여 (코엑스)<br/>
				자산운용사 누적투자금액 200억원 달성
			</span><br/><br/>
				<!-- <span class="months">5월</span> <span>2019 서울 머니쇼 부스참여 (코엑스)</span><br/><br/>
				<span class="months">5월</span> <span>자산운용사 누적투자금액 200억원 돌파</span><br/><br/> -->
				<!-- <span class="months">4월</span> <span>누적투자금액 1,300억원 달성</span><br/><br/> -->
				<!-- <span class="months">2월</span> <span>누적투자금액 1,100억원 달성</span><br/><br/> -->
			</div>
			<div id="history_tab4" style="display:none;">
				<span class="months">12월</span> <span>누적투자금액 1,000억원 달성</span><br/><br/>
				<span class="months">11월</span> <span>면세점 매출채권 상품출시 카카오페이 간편송금</span><br/><br/>
				<span class="months">8월</span> <span>금융플랫폼 핀크(Finnq) 제휴</span><br/><br/>
				<span class="months">7월</span> <span>헬로펀딩 2018 윤리강령 선포식</span><br/><br/>
				<span class="months">6월</span> <span>한국 핀테크 산업협회 가입</span><br/><br/>
				<span class="months">5월</span> <span style="display:inline-block;">자산운용사 누적투자금액 100억원 달성<br/>
												매일경제 주최 2018 서울 머니쇼 부스참여 (코엑스)<br/>
												확정매출채권 상품 출시</span><br/><br/>
				<span class="months">2월</span> <span style="display:inline-block;">금융감독원 등록 <br/>
					누적투자금액 500억원 달성 </span><br/><br/>
				<span class="months">1월</span> <span>외감대상법인</span><br/><br/>
			</div>
			<div id="history_tab5" style="display:none;">
				<span class="months">11월</span>
				<span>킨텍스 주최 인사이드핀테크 부스참여 (킨텍스)</span><br/><br/>
				<span class="months">10월</span>
				<span>썬앤트리자산운용 업무제휴 협약 체결 (기업평가 완료)</span><br/><br/>
				<span class="months">9월</span>
				<span style="display:inline-block;">조선일보 주최 2017 부동산 트렌드쇼 부스참여 (서울무역전시관)<br/>동아일보 주최 2017 동아재테크· 핀테크쇼 부스참여 (코엑스)</span><br/><br/>

			    <span class="months">8월</span>
				<span style="display:inline-block;">교보리얼코(CM) 업무협약 체결<br/>법무법인 에이펙스 원리금수취대행계약 체결<br/>(만일의 헬로핀테크 부도시 원리금수취업무 대행)<br/>P2P금융최초 PF현장 라이브 스트리밍 서비스 ‘헬로라이브TV’ 오픈<br/>누적투자금액 300억원 달성</span><br/><br/>

			    <span class="months">7월</span>
				<span>상시 투자자 방문신청 ‘보고싶습니다.’ 지속운영시작</span><br/><br/>

				<span class="months">6월</span>
				<span style="display:inline-block;">한국경제TV 주최 2017 부동산엑스포 부스참여 (코엑스)<br/>피델리스자산운용 업무제휴 협약 체결 (기업평가 완료)<br/>한국부동산리츠투자자문협회 업무제휴 협약 체결</span><br/><br/>

			    <span class="months">5월</span>
				<span>신한은행 P2P자금 신탁계약 체결</span><br/><br/>

				<span class="months">4월</span>
				<span>외부회계검사 무결점 완료 (신승회계법인)</span><br/><br/>

				<span class="months">3월</span>
				<span style="display:inline-block;">한국P2P금융협회 회원사 가입<br/>한국경제TV ‘예스P2P’ 생방송시작 (최수석 부대표 출연)<br/>누적투자금액 100억원 달성</span><br/><br/>
			</div>
			<div id="history_tab6" style="display:none;">
				<span class="months">11월</span>  창성건설㈜ 업무제휴 협약 체결<br/><br/>
				<span class="months">10월</span><span style="display:inline-block;">서울신용평가(주) 업무제휴 협약 체결<br/>
					 한국인터넷진흥원 웹보안점검 최우수등급 획득 (SSL report A+)<br/>
					 (주)하나자산신탁 MOU체결</span><br/><br/>
			    <span class="months">9월</span><span style="display:inline-block;">헬로펀딩 공식오픈<br/>
					헬로펀딩 투자심의위원회 출범<br/>
					'크라우드 펀딩 투자 보호 시스템’ 특허 출원<br/>
					(주)경일감정평가법인 업무제휴 협약 체결</span><br/><br/>
			</div>
			<script>
				function go_2020() {
					$("#history_tab1").css("display","");
					$("#history_tab2").css("display","none");
					$("#history_tab3").css("display","none");
					$("#history_tab4").css("display","none");
					$("#history_tab5").css("display","none");
				}
				function go_2019() {
					$("#history_tab1").css("display","none");
					$("#history_tab2").css("display","");
					$("#history_tab3").css("display","none");
					$("#history_tab4").css("display","none");
					$("#history_tab5").css("display","none");
				}
				function go_2018() {
					$("#history_tab1").css("display","none");
					$("#history_tab2").css("display","none");
					$("#history_tab3").css("display","");
					$("#history_tab4").css("display","none");
					$("#history_tab5").css("display","none");
				}
				function go_2017() {
					$("#history_tab1").css("display","none");
					$("#history_tab2").css("display","none");
					$("#history_tab3").css("display","none");
					$("#history_tab4").css("display","");
					$("#history_tab5").css("display","none");
				}
				function go_2016() {
					$("#history_tab1").css("display","none");
					$("#history_tab2").css("display","none");
					$("#history_tab3").css("display","none");
					$("#history_tab4").css("display","none");
					$("#history_tab5").css("display","");
				}
			</script>

			<!--찾아오시는길-->
			<img src="/images/company/comp_img09.jpg" width="100%"/>

	</div>
</div>
<script type="text/javascript">
	var msg = "대출잔액 대비 상환일이 30일 이상 지연된 잔여원금 비율 (한국P2P금융협회 기준)";
	$('#bankruptcy-claim-mark').webuiPopover({ title: "연체율", content: msg, closeable: true, width: 330, height: 80, trigger: "click", placement: 'bottom', backdrop: false});
	var msg = "약정된 상환이 일부 혹은 전부 지연되기 시작해 90일 이상 경과한 대출 부실률 = 부실잔여원금 / 총 누적대출액 (P2P금융협회 기준)";
	$('#overdue-claim-mark').webuiPopover({ title: "부실률", content: msg, closeable: true, width: 330, height: 80, trigger: "click", placement: 'bottom', backdrop: false});

	var swiper_1, swiper_3, swiper_4;
	var beforeActiveTab = "";
	var slideNo = false;
	$(document).on("click", "ul.tabs li[rel]", function(e)
	{
		var index = $(this).index();
		var activeTab = $(this).attr("rel");
		var tabs = $(this);
		if(beforeActiveTab == activeTab) return false;

		tabs.siblings().not('.line').removeClass("on").addClass("off");
		$(this).not('.line').toggleClass(function(){
			if($(this).hasClass('on')){
				tabs.removeClass('on');
				return 'off';
			}else{
				tabs.removeClass('off');
				return 'on';
			}
		});
		if(activeTab.indexOf("product") != -1 && !slideNo){
			swiper_4.slideTo(activeTab.replace( /^\D+/g, '') - 1);
		}else{
			tabs.siblings().not('.line').each(function(e){
				var activeTab = $(this).attr("rel");
				$("#"+activeTab).hide();
			});
			$("#"+activeTab).fadeIn();
		}
		beforeActiveTab = activeTab;
		slideNo = false;
	});

	$(document).on("click", "#smsRequestSubmit", function()
	{
		var text = $('#smsReceiveNo').val();
		if(text=='' || text.length < 10 ) {
			alert('문자메세지를 수신할 전화번호를 정확히 입력하여 주십시요.');
			$('#smsReceiveNo').focus();
			return false;
		}
		else if($('#receiveOk').is(':checked') == false) {
			alert('투자정보 안내 수신 및 휴대폰번호 등록에 동의하셔야 합니다.');
			$('#receiveOk').focus();
			return false;
		}
		else {
			$.ajax({
				url : "/member/ajax_sms_request.php",
				type: "POST",
				data: {phone_no : text},
				success: function(data){
					if(data=="ERROR"){
						alert("시스템 에러입니다. 관리자에 문의해주세요.");
					}
					else if(data=="2"){
						alert("문자 수신이 가능한 모바일 번호가 아닙니다.\n문자메세지를 수신할 전화번호를 정확히 입력하여 주십시요.");
					}
					else {
						alert("정상 등록 되었습니다.");
						$('#smsReceiveNo').val('');
						$.unblockUI();
					}
				},
				error: function () {
					alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
				}
			});
		}
	});

	$(document).ready(function(){
		swiper_1 = new Swiper('.s1', {
			loop: true,
			slidesPerView: 'auto',
			slidesPerGroup: 3,
			observer: true,
			observeParents: true,
			navigation: {
				nextEl: '.funding-news-button-next',
				prevEl: '.funding-news-button-prev'
			}
		});

		swiper_3 = new Swiper ('.s3', {
			loop: true,
			slidesPerView: 'auto',
			slidesPerGroup: 1,
			pagination: {
				el: '.active-product-list-pagination',
				type: 'bullets',
				clickable: true
			},
			navigation: {
				nextEl: '.active-product-list-button-next',
				prevEl: '.active-product-list-button-prev'
			},
				 autoplay: {
					delay: 3000,
				  },
		});
		swiper_4 = new Swiper ('.s4', {
			loop: false,
			slidesPerView: 1,
			slidesPerGroup: 1,
			spaceBetween: 10,
			pagination: {
				el: '.popular-product-list-pagination',
				type: 'bullets',
				clickable: true
			},
			navigation: {
				nextEl: '.popular-product-list-button-next',
				prevEl: '.popular-product-list-button-prev'
			}
		});
		// 회사소개 슬라이드
		swiper_8 = new Swiper('.s8', {
			loop: true,
			loopedSlides: 15,
			speed: 800,
			slidesPerView: '3',
			slidesPerGroup: 3,

			navigation: {
				nextEl: '.comp-view-list-button-next',
				prevEl: '.comp-view-list-button-prev'
			},
			autoplay: {
					delay: 3000,
				  },
		});
					// 헬로스토리 슬라이드
		swiper_9 = new Swiper('.s9', {
			loop: true,
			loopedSlides: 11,
			speed: 800,
			slidesPerView: '3',
			slidesPerGroup: 1,

			navigation: {
				nextEl: '.comp-view-list2-button-next',
				prevEl: '.comp-view-list2-button-prev'
			},
			autoplay: {
					delay: 3000,
				  },
		});
		swiper_4.on("slideChange", function(){
			var index = swiper_4.activeIndex;
			var tabs = $("ul.tabs li[rel]");
			tabs.siblings().not('.line').removeClass("on").addClass("off");
			tabs.eq(index).not('.line').toggleClass(function(){
				if($(this).hasClass('on')){
					tabs.eq(index).removeClass('on');
					return 'off';
				}else{
					tabs.eq(index).removeClass('off');
					return 'on';
				}
			});
		});

		$(document).on('click', ".popular-product-list-button-next, .popular-product-list-button-prev", function () {
			var index = swiper_4.activeIndex;
			var tabs = $("ul.tabs li[rel]");
			tabs.siblings().not('.line').removeClass("on").addClass("off");
			tabs.eq(index).not('.line').toggleClass(function(){
				if($(this).hasClass('on')){
					tabs.eq(index).removeClass('on');
					return 'off';
				}else{
					tabs.eq(index).removeClass('off');
					return 'on';
				}
			});

			/*
			var index = swiper_4.activeIndex;
			slideNo = true;
			$("li[rel=product_tab"+(index + 1)+"]").trigger("click");*/
		});

		$(window).scroll( function(){
			$('.fadeInAmate').each( function(i){
				var bottom_of_object = $(this).offset().top + $(this).outerHeight();
				var bottom_of_window = $(window).scrollTop() + $(window).height();
				if( bottom_of_window > bottom_of_object ){
					$(this).animate({'opacity':'1'}, 1000);
				}else if(bottom_of_object > bottom_of_window){
				}
			});
		});
	});
</script>
<script type="text/javascript">
	function view1(opt) {
		if (opt) {
			member_info1.style.display = "block";
		}
		else {
			member_info1.style.display = "none";
		}
	}
	function view2(opt) {
		if (opt) {
			member_info2.style.display = "block";
		}
		else {
			member_info2.style.display = "none";
		}
	}
	function view3(opt) {
		if (opt) {
			member_info3.style.display = "block";
		}
		else {
			member_info3.style.display = "none";
		}
	}

	function view4(opt) {
		if (opt) {
			member_info4.style.display = "block";
		}
		else {
			member_info4.style.display = "none";
		}
	}
	function view5(opt) {
		if (opt) {
			member_info5.style.display = "block";
		}
		else {
			member_info5.style.display = "none";
		}
	}
	function view6(opt) {
		if (opt) {
			member_info6.style.display = "block";
		}
		else {
			member_info6.style.display = "none";
		}
	}
	function view7(opt) {
		if (opt) {
			member_info7.style.display = "block";
		}
		else {
			member_info7.style.display = "none";
		}
	}
</script>
<script type="text/javascript">
	function view_name(cls_name,opt) {
		if (opt) {
			document.getElementById('amember_info'+cls_name).style.display = "block";
		} else {
			document.getElementById('amember_info'+cls_name).style.display = "none";
		}
	}

</script>
<div class="container">
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
		<div style="height:60px;"></div>
		<ul>
			<li><a href="https://www.shinhan.com/index.jsp" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo01.jpg" alt="신한은행"></a></li>
			<li><a href="http://p2plending.or.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo02.jpg" alt="한국P2P금융협회"></a></li>
			<li><a href="http://www.hanatrust.com/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo03.jpg" alt="하나자산신탁"></a></li>

		</ul>
		<ul>
			<li><a href="http://www.scri.co.kr/index.jsp" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo04.jpg" alt="SCR서울신용평가"></a></li>
			<li><a href="http://www.karic.or.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo05.jpg" alt="한국부동산리츠투자자문협회"></a></li>
			<!--li><a href="http://hyunlaw.co.kr/renew/kor/main/main.asp" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo06.jpg" alt="법무법인현"></a></li>-->
			<li><a href="https://www.kyoborealco.co.kr/realco/indexRealco.html" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo07.jpg" alt="교보리얼코"></a></li>

		</ul>
		<ul>

			<!--li><a href="http://seinacct.co.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo08.jpg" alt="세인세무법인"></a></li-->
			<li><a href="http://www.apexlaw.co.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo09.jpg" alt="법무법인 에이펙스"></a></li>
			<li><a href="http://www.fidelisam.co.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo11.jpg" alt="피델리스"></a></li>
			<li><a href="http://www.kyungilnet.co.kr/main/main.php" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo12.jpg" alt="경일감정평가법인"></a></li>
		</ul>
		<ul>
			<!--li><a href="http://wowtv.co.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo10.jpg" alt="한국경제티브이"></a></li-->
			<li><a href="http://korfin.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo14.jpg" alt="한국핀테크산업협회"></a></li>
			<li><a href="https://www.finnq.com/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo13.jpg" alt="핀크"></a></li>
			<li><a href="https://www.sci.co.kr/" target="_blank"><img src="<?=G5_THEME_IMG_URL?>/main/client_logo15.jpg" alt="sci평가정보"></a></li>

		</ul>
		<p style="height:50px;"></p>
	</div>
</div>
