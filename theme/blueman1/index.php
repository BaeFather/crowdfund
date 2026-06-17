<?php
/**
 * 헬로핀테크 메인 페이지
 */
define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($_REQUEST['mode']=="debug") {
	setCookie("debug_mode", true, time()+3600*3, "/");
	echo "<script type='text/javascript'>window.location.href='/';</script>";
}

// 출력 통계수치
$row = sql_fetch("SELECT * FROM cf_invest");
$DP['average_return']    = ($row["average_return"]) ? $row["average_return"] : 0;
$DP['total_invest']      = ($row["total_invest"]) ? $row["total_invest"] : 0;
$DP['total_repay']       = ($row["total_repay"]) ? $row["total_repay"] : 0;
$DP['bankruptcy']        = ($row["bankruptcy"]=='0.00') ? 0 : $row["bankruptcy"];
$DP['overdue_perc']      = ($row["overdue_perc"]=='0.00') ? 0 : $row["overdue_perc"];
$DP['invest_ing_amount'] = ($row["invest_ing_amount"]) ? $row["invest_ing_amount"] : 0;
$DP['standard_date']     = $row["standard_date"];
$DP['display']           = $row["display"];
unset($row);

// 활성투자상품 리스트 배열화
include_once(G5_LIB_DIR.'/class_xmlparser.php');
$XMLOBJ = new XMLParser('./xml/active_product_list.xml');
$XMLARR = $XMLOBJ->data['child'][2]['child'];
for($i=0; $i<count($XMLARR); $i++) {
	for($k=0; $k<count($XMLARR[$i]['child']); $k++) {
		$ALIST[$i][$XMLARR[$i]['child'][$k]['name']] = trim($XMLARR[$i]['child'][$k]['data']);
	}

	if($ALIST[$i]['GUBUN']=='normal' && preg_match("/(B01|B02)/", $ALIST[$i]['STATE_CODE'])) $AALIST[] = $ALIST[$i]; //최신상품 리스트 (이벤트 제외)
    //if($ALIST[$i]['GUBUN']=='event' || preg_match("/(B01|B02)/", $ALIST[$i]['STATE_CODE'])) $AALIST[] = $ALIST[$i]; //최신상품 리스트 (이벤트 포함)
}
unset($XMLOBJ); unset($XMLARR);

// 메인 롤링 URL/이미지 배열화
$EVENT_ARR = array(
                //array('url'=>'/event/invest_epilogue.php', 'image'=>'/images/main/reply_event01.png', 'image_m'=>'/images/main/reply_m.jpg'),
                //array('url'=>'/event_invest/event_invest.php?prd_idx=6', 'image'=>'/images/main/235_event01.png', 'image_m'=>'/images/main/main_04_235event_m.jpg'),
                //array('url'=>'#', 'image'=>'/images/main/prod_img01.png', 'image_m'=>'/images/main/prod_img01_m.png'),
                //array('url'=>'/event/screenshot_event.php', 'image'=>'/images/main/new_event20170221.png', 'image_m'=>'/images/main/new_event20170221_m.png'),
                //array('url'=>'/event/invest_review_20170426.php', 'image'=>'/images/main/invest_review.png', 'image_m'=>'/images/main/invest_review_m.png'),
                //array('url'=>'/event/invitation.php', 'image'=>'/images/main/main_event20170329.png', 'image_m'=>'/images/main/new_event20170329.png')
                //array('url'=>'https://www.hellofunding.co.kr/event/corporation.php', 'image'=>'/images/main/main_170516.png', 'image_m'=>'/images/main/main_170516_m.png'),
                //array('url'=>'/event/invitation_20170830.php', 'image'=>'/images/main/trendshow20170830.png', 'image_m'=>'/images/main/trendshow20170830_m.png'),
                //array('url'=>'/event/join_event171129.php', 'image'=>'/images/event/join_event171129.png', 'image_m'=>'/images/event/join_event171129_m.png'),
                //array('url'=>'/event/invest_review_20170426.php', 'image'=>'/images/main/invest_review.png', 'image_m'=>'/images/main/invest_review_m.png'),
                array('url'=>'/event/notice1018.php', 'image'=>'/images/main/notice_slide.png', 'image_m'=>'/images/main/notice_slide_m.png'),
                array('url'=>'/event/invitation_20170919.php', 'image'=>'/images/main/join170918.png', 'image_m'=>'/images/main/join170918_m.png'),
                array('url'=>'/bbs/board.php?bo_table=notice&wr_id=198', 'image'=>'/images/main/live_tv_20170802.png', 'image_m'=>'/images/main/live_tv_20170802_m.png'),
                array('url'=>'/bbs/funding_story.php', 'image'=>'/images/main/funding_designer.png', 'image_m'=>'/images/main/funding_designer_m.png'),
                array('url'=>'/event/invitation_20170714.php', 'image'=>'/images/main/invitation/invite_20170714.png', 'image_m'=>'/images/main/invitation/invite_20170714_m.png'),
                //array('url'=>'/event/hello_musical.php', 'image'=>'/images/event/hellomusical.png', 'image_m'=>'/images/event/hellomusical_m.png'),
                array('url'=>'#', 'image'=>'/images/main/client_ban01.png', 'image_m'=>'/images/main/client_ban01_m.png'),
                //array('url'=>'/etc/epilogue.php', 'image'=>'/images/main/review_ban01.png', 'image_m'=>'/images/main/review_ban01_m.png')
                array('url'=>'/bbs/epilogue.php', 'image'=>'/images/main/review_20170522.png', 'image_m'=>'/images/main/review_20170522_m.png'),
                array('url'=>'#', 'image'=>'/images/main/sns_alram.png', 'image_m'=>'/images/main/sns_alram_m.png', 'image_id'=>'reqsms_banner')
            );


// 헬로펀딩 스토리 배열화
//$STORY = array('url'=>'http://www.wowtv.co.kr/newscenter/news/view.asp?bcode=T30001000&artid=A201703290289', 'image'=>'/images/main/tv_show01.jpg', 'subject'=>'헬로펀딩, 한국경제TV에 소개되다..', 'target'=>'_blank');
//$STORY = array('url'=>'http://blog.naver.com/hellofunding/220987784296', 'image'=>'', 'subject'=>'헬로펀딩 1호 상품 차주 인터뷰', 'target'=>'_blank', 'media_url'=>'https://serviceapi.nmv.naver.com/flash/convertIframeTag.nhn?vid=C36681D6644FD67D378D81E760F310062BC7&outKey=V12455a02786275eec42c094d0787416655bf49c48969e613ecdd094d0787416655bf&width=544&height=306');
$STORY = array('url'=>'http://blog.naver.com/hellofunding/221133848659', 'image'=>'', 'subject'=>'헬로펀딩, 아직도 아이 해봤니~?', 'target'=>'_blank', 'media_url'=>'https://serviceapi.nmv.naver.com/flash/convertIframeTag.nhn?vid=585734F6FF675093AFF2B915120ECAA4C9CF&outKey=V1279eecac04ce8bf250dc2262953fcca04ad36b390477eb2a4d2c2262953fcca04ad&width=544&height=306');

// 언론보도 배열화
//$PRESS = array('url'=>'http://news.heraldcorp.com/view.php?ud=20170306000408', 'image'=>'/data/funding_news/1488789981_thumbnail', 'subject'=>'“이자 많이 벌었는데”…뿔난 P2P 큰손들', 'target'=>'_blank');
//$PRESS = array('url'=>'http://www.wowtv.co.kr/newscenter/news/view.asp?bcode=T30001000&artid=A201705260205', 'image'=>'/images/main/report20170523.jpg', 'subject'=>'P2P금융 헬로펀딩, ‘투자심의위원회’ 강화', 'target'=>'_blank');
$PRESS = array('url'=>'http://news.mk.co.kr/newsRead.php?year=2018&no=94095', 'image'=>'/images/main/report20180209.jpg', 'subject'=>'헬로펀딩,시행이익 유동화 상품 성공적 안착', 'target'=>'_blank');

// 공지사항리스트 배열화
$BRD = sql_fetch("SELECT bo_notice FROM g5_board");
$sql = "SELECT wr_id, wr_subject, LEFT(wr_datetime, 10) AS wr_datetime FROM g5_write_notice WHERE wr_id IN(".$BRD['bo_notice'].") ORDER BY wr_datetime DESC LIMIT 5";
$res = sql_query($sql);
while($r = sql_fetch_array($res)){
	$NOTICE[] = $r;
}
unset($BRD); unset($r);

// 투자상품 관련소식 배열화
$sql = "SELECT wr_id, wr_subject, LEFT(wr_datetime, 10) AS wr_datetime FROM g5_write_notice WHERE wr_1='Y' ORDER BY wr_id DESC, wr_datetime DESC LIMIT 6";
$res = sql_query($sql);
while($r = sql_fetch_array($res)){
	$ALIM[] = $r;
}
unset($r);


// 인기상품 배열화
$XMLOBJ = new XMLParser('./xml/success_popular_product_list.xml');
$XMLARR = $XMLOBJ->data['child'][2]['child'];
for($i=0; $i<count($XMLARR); $i++) {
	for($k=0; $k<count($XMLARR[$i]['child']); $k++) {
		$POPLIST[$i][$XMLARR[$i]['child'][$k]['name']] = trim($XMLARR[$i]['child'][$k]['data']);
	}
}

unset($XMLOBJ); unset($XMLARR);


if (G5_IS_MOBILE) {
	if($_GET['mode']=='test') {
		include_once(G5_THEME_MOBILE_PATH.'/index_test.php');
	}
	else {
		include_once(G5_THEME_MOBILE_PATH.'/index.php');
	}
	return;
}

include_once(G5_THEME_PATH.'/index_head.php');

?>

<!-- 메인 슬라이드 시작 -->
<section>
	<div id="main" role="main">
		<div id="invest" class="invest">
			<article class="slider">

				<div class="flexslider">
					<!-- 활성 투자상품 및 이벤트 배너 리스트 -->
					<ul class="slides">
                        
                        <?php
                        // 활성투자상품 리스트 출력
                        if(count($ALIST)) {
                            for($i=0,$j=1; $i<count($ALIST); $i++,$j++) {
                        
                                $ALIST[$i]["TOTAL_INVEST_AMOUNT"] = (!$ALIST[$i]["TOTAL_INVEST_AMOUNT"]) ? 0 : $ALIST[$i]["TOTAL_INVEST_AMOUNT"];
                                $ALIST[$i]["INVEST_PERCENT"] = (!$ALIST[$i]["INVEST_PERCENT"]) ? 0 : $ALIST[$i]["INVEST_PERCENT"];
                        
                                $main_image_tag      = ($ALIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$ALIST[$i]['TITLE_IMAGE_URL']."' width='100%' height='100%'/>" : "";
                                $total_invest_amount = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["TOTAL_INVEST_AMOUNT"]) : price_cutting($ALIST[$i]["TOTAL_INVEST_AMOUNT"]);
                                $_ALIST[$i]['recruit_amount'] = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["RECRUIT_AMOUNT"]) : price_cutting($ALIST[$i]["RECRUIT_AMOUNT"]);
                                $print_date          = ($ALIST[$i]["START_DATETIME"]) ? date("Y년 m월 d일 H시 i분", strtotime($ALIST[$i]["START_DATETIME"])) : date("Y년 m월 d일", strtotime($ALIST[$i]["RECRUIT_PERIOD_START"]));
                        
                        ?>
                        
						<li><?php if(false): ?><!-- onClick="location.href='<?php echo $ALIST[$i]['DETAIL_URL'];?>';" style="cursor:pointer"//--><?php endif; ?>
							<div class="invest_tit">
								NEW 투자상품 안내 – [ <?php echo $print_sdate?> 투자시작 ]

								<div class="flag_red" style="display:<?php echo ($ALIST[$i]['STEREAM_URL1'])?'block':'none'?>"><img src="/images/investment/live_icon01.gif"/></div>
								<div class="flag_green" style="display:<?php echo ($ALIST[$i]['ADVANCE_INVEST_RATIO'])?'block':'none'?>">사전투자 <?php echo (int)$ALIST[$i]['ADVANCE_INVEST_RATIO'];?>%</div>
							</div>
							<div class="invest_contents">
								<div class="img"><?php echo $main_image_tag?></div>
								<div class="info">
									<ul class="info_tit">
										<li style="height:24px;overflow:hidden;"><?php echo $ALIST[$i]['TITLE'];?></li>
										<li>모집기간 : <?php echo preg_replace("/-/", ".", $print_date)?> ~</li>
									</ul>
									<table summary="표의정보" border="0">
										<caption>상황</caption>
										<colgroup>
											<col width="109">
											<col width="109">
											<col width="109">
											<col width="109">
										</colgroup>
										<thead>
											<tr>
												<th scope="col"><img src="<?php echo G5_THEME_URL;?>/img/m_icon1.png"/></th>
												<th scope="col" class="tb_bg"><img src="<?php echo G5_THEME_URL;?>/img/m_icon2.png"/></th>
												<th scope="col"><img src="<?php echo G5_THEME_URL;?>/img/m_icon3.png"/></th>
												<th scope="col" class="tb_bg"><img src="<?php echo G5_THEME_URL;?>/img/m_icon4.png"/></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>투자자 수익률(<?php echo ($ALIST[$i]['GUBUN']=='event')?'회':'연'?>)</td>
												<td class="tb_bg">모집금액</td>
												<td>투자기간</td>
												<td class="tb_bg">참여진행률</td>
											</tr>
											<tr class="f20">
												<td class="f20"><?php echo $ALIST[$i]["INVEST_RETURN"];?>%</td>
												<td class="tb_bg f20"><?php echo $_ALIST[$i]['recruit_amount'];?></td>
												<td class="f20"><?php echo $ALIST[$i]["INVEST_PERIOD"];?></td>
												<td class="tb_bg f20"><?php echo $ALIST[$i]["INVEST_PERCENT"];?>%</td>
											</tr>
										</tbody>
									</table>
									<div class="btn"><a href="<?php echo $ALIST[$i]['DETAIL_URL'];?>"><span class="btn_links">상품상세보기</span></a></div>
								</div>
							</div>
						</li>
                        
                        <?php
                            }
                        }
                        
                        // 이벤트 배너 출력 리스트
                        for($i=0; $i<count($EVENT_ARR); $i++) {
                            echo "<li onClick=\"location.href='{$EVENT_ARR[$i]['url']}'\" style='cursor:pointer;'><img src='{$EVENT_ARR[$i]['image']}' id='{$EVENT_ARR[$i]['image_id']}'/></li>\n";
                        }
                        ?>
                        
					</ul>
					<!-- 활성 투자상품 및 이벤트 배너 리스트 -->
				</div>

			</article>
		</div>

		<!--펀딩 현황표 시작-->
		<ul id="list_info1">
			<li><p><?php echo $PRNT_SUBJECT['average_return'];?></p><p><?php echo $DP['average_return'];?>%</p></li>
			<li><p><?php echo $PRNT_SUBJECT['total_invest'];?></p><p><?php echo price_cutting($DP['total_invest']);?>원</p></li>
			<li><p><?php echo $PRNT_SUBJECT['total_repay'];?></p><p><?php echo price_cutting($DP['total_repay']);?>원</p></li>
			<li><p><?php echo $PRNT_SUBJECT['invest_ing_amount'];?></p><p><?php echo price_cutting($DP['invest_ing_amount']);?>원</p></li>
			<li>
                <!--연체율이란//-->
                <p>연체율 <span class="claim-mark">!</span></p>
                <div id="overdue-rate">
                    <div class="pop_content">
                        <div class="close-content">x</div>
                        <strong >연체율</strong><br/>
                        약정된 상환이 일부 혹은 전부 지연되기<br/>
                        시작해 30일 이상, 90일 미만 경과한 대출<br/>
                        연체율 = 연체잔여원금 / 대출잔여원금 <br/>
                        (P2P금융협회 기준)
                    </div>
                </div>
                <p><?php echo $DP['overdue_perc'];?>%</p>
            </li>
            <li>
                <!--부실률이란//-->
                <p>부실률 <span class="claim-mark">!</span></p>
                <div id="default-rate">
                    <div class="pop_content">
                        <div class="close-content">x</div>
                        <strong >부실률</strong><br/>
                        약정된 상환이 일부 혹은 전부 지연되기<br/>
                        시작해 90일 이상 경과한 대출<br/>
                        부실률 = 부실잔여원금 / 총 누적대출액<br/>
                        (P2P금융협회 기준)
                    </div>
                </div>
                <p><?php echo $DP['bankruptcy'];?>%</p>
            </li>

			<li>
				<!--<p>펀딩성공</p>//-->
				<p class="success-case-btn" onClick="location.href='<?php echo G5_URL;?>/investment/invest_list.php?mode=success';">성공사례</p>
				<p class="success-case-date"><?php echo preg_replace('/-/', '.', $DP['standard_date']);?> 기준</p>
			</li>
		</ul>
		<!--펀딩 현황표 끝-->
	</div>
</section>
<!-- 메인 슬라이드 끝 -->

<article>
	<div id="container">
        
        <!--공지사항 영역//-->
		<div class="notice">
			<div class="notices">
				<div><img src="<?php echo G5_THEME_URL;?>/img/notice_icon.jpg"/></div>
				<div id="rolling_area" class="rolling_area">
					<ul>
                        <?php for($i=0; $i<count($NOTICE); $i++) { ?>
						<li>
							<span class="subject"><a href="/bbs/board.php?bo_table=notice&wr_id=<?php echo $NOTICE[$i]['wr_id'];?>"><?php echo htmlSpecialChars($NOTICE[$i]['wr_subject']);?></a></span>
							<span class="datetime"><?php echo preg_replace("/-/", ".", $NOTICE[$i]['wr_datetime']);?></span>
						</li>
                        <? } ?>
					</ul>
				</div>
			</div>
		</div>

        <?php
        /*
        if(count($AALIST)) {
        ?>
                <!--신상품 노출 시작-->
                <div class="new_info">
        <?
            for($i=0,$j=1; $i<count($AALIST); $i++,$j++) {
        ?>
                    <ul>
                        <li><img src="<?php echo G5_THEME_URL;?>/img/new_icon01.jpg"><div class="t01"> <?php echo $AALIST[$i]['TITLE']?/></div></li>
                        <li>
                            <span><img src="<?php echo G5_THEME_URL;?>/img/new_info_icon01.png"> 투자자 수익률(<?php echo ($AALIST[$i]['GUBUN']=='event')?'회':'연'?>) <b><?php echo $AALIST[$i]["INVEST_RETURN"]?/>%</b></span>
                            <span><img src="<?php echo G5_THEME_URL;?>/img/new_info_icon02.png"> 투자기간 <b><?php echo $AALIST[$i]["INVEST_PERIOD"]?/></b></span>
                            <span><img src="<?php echo G5_THEME_URL;?>/img/new_info_icon03.png"> 모집금액 <b><?php echo $_AALIST[$i]['recruit_amount']?/></b></span>
                        </li>
                        <li><a href="<?php echo $AALIST[$i]['DETAIL_URL'];?>">투자상품 바로가기</a></li>
                    </ul>
        <?
            }
        ?>
                </div>
                <!--신상품 노출 끝-->
        <?
        }*/
        ?>

<?php if(count($AALIST)) { ?>
 
	<!-- 최신상품리스트 시작 -->
	<div class="product_list">
		<div class="plist_tit">헬로펀딩 최신상품</div>
        
        <?php
            for($i=0,$j=1; $i<count($AALIST); $i++,$j++) {
        
                $AALIST[$i]['title_image_tag'] = ($AALIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$AALIST[$i]['TITLE_IMAGE_URL']."' width='100%' height='100%'/>" : "";
                if(!$AALIST[$i]['TOTAL_INVEST_AMOUNT']) $AALIST[$i]['TOTAL_INVEST_AMOUNT'] = '0';
        
        ?>
        
		<div class="invest_list2">
			<div class="boxArea" id="list_area">
				<div class="box product_count" >
					<div class="imgArea" onClick="location.href='<?php echo $AALIST[$i]['DETAIL_URL'];?>';">
						<div class="main_image">
                            <?php
                            if($AALIST[$i]['CATEGORY']=='1') {
                                echo '<div class="cflag ca-B">동산</div>';
                            }
                            else if($AALIST[$i]['CATEGORY']=='2') {
                                echo ($AALIST[$i]['mortgage_guarantees']) ? '<div class="cflag ca-A2">주택담보대출</div>' : '<div class="cflag ca-A">부동산</div>';
                            }
                            else if($AALIST[$i]['CATEGORY']=='3') {
                                echo '<div class="cflag ca-C">확정매출채권</div>';
                            }
                            ?>
                            <?php if($AALIST[$i]["AUTO_INVEST_FLAG"] == 'Y'){ ?>
                                <div class="cflag ai">자동투자</div>
                            <?php } ?>
                            <?php if($AALIST[$i]["new_flag"] == 'Y') {?>
                                <div class="nflag">N</div>
                            <?php } ?>
                            <?php echo $AALIST[$i]['title_image_tag'];?>
                        </div>
					</div>
					<div class="con">
						<div class="title"><?php echo $AALIST[$i]['TITLE'];?></div>
						<div class="flag_red" style="display:<?php echo ($AALIST[$i]['PURCHASE_GUARANTEES']=='Y')?'block':'none'?>">채권매입계약</div>
						<div class="flag_green" style="display:<?php echo ($AALIST[$i]['ADVANCE_INVEST']=='Y')?'block':'none'?>">사전투자 <?php echo (int)$AALIST[$i]['ADVANCE_INVEST_RATIO'];?>%</div>
						<? if($AALIST[$i]['STREAM_URL1'] || $AALIST[$i]['STREAM_URL2']) { ?><div class="flag_red"><img src="/images/investment/live_icon01.gif"/></div><? echo "\n"; } ?>
						<div class="subtext">투자시작일 : <?php echo date("Y년 m월 d일", strtotime($AALIST[$i]['RECRUIT_PERIOD_START']))?></div>
						<ul class="info">
							<li>
								<div class="subject">투자자 수익률(<?php echo ($AALIST[$i]['GUBUN']=='event')?'회':'연';?>)</div>
								<div class="value"><?php echo $AALIST[$i]['INVEST_RETURN'];?>%</div>
							</li>
							<li>
								<div class="subject">투자기간</div>
								<div class="value"><?php echo $AALIST[$i]['INVEST_PERIOD'];?></div>
							</li>
							<li>
								<div class="subject">목표금액</div>
								<div class="value"><?php echo ($AALIST[$i]['GUBUN']=='event') ? number_format($AALIST[$i]['RECRUIT_AMOUNT']) : price_cutting($AALIST[$i]['RECRUIT_AMOUNT'])?>원</div>
							</li>
							<li class="end">
								<div class="subject">모집금액</div>
								<div class="value"><?php echo ($AALIST[$i]['GUBUN']=='event') ?  number_format($AALIST[$i]['TOTAL_INVEST_AMOUNT']) : price_cutting($AALIST[$i]['TOTAL_INVEST_AMOUNT'])?>원</div>
							</li>
						</ul>
						<ul class="progress">
							<li>참여진행률<b><?php echo ($AALIST[$i]['INVEST_PERCENT']) ? $AALIST[$i]['INVEST_PERCENT'] : '0';?>%</b>
								<div class="rate"><img src="<?php echo G5_THEME_URL;?>/img/rate_blue.gif" alt="진행률" style="width:<?php echo ($AALIST[$i]['INVEST_PERCENT']) ? $AALIST[$i]['INVEST_PERCENT'] : '0.2';?>%"/></div>
							</li>
						</ul>
						<div style="width:100%;text-align:center;">
							<a href='<?php echo $AALIST[$i]['DETAIL_URL'];?>' class='btn_big_blue' style='margin:0;'><?php echo $AALIST[$i]['BUTTON_CAPTION'];?></a>
						</div>
					</div>
				</div>
                
                <?php if(count($AALIST) > $j) { echo "<div class='box_end'></div>\n"; } ?>
                
			</div>
		</div>
<?php
	}
?>
	</div>
	<!-- 최신상품리스트 끝 -->
 
<?php } ?>

		<div class="news">
			<ul>
				<li>
					<div class="story_tit">
						<p>헬로펀딩 스토리</p>
						<p><a href="http://blog.naver.com/hellofunding"><img src="<?php echo G5_THEME_URL;?>/img/more_icon01.png" alt="더보기"/></a></p>
					</div>
					<div class="story">
                        <p><? if($STORY['media_url']) { ?><iframe src="<?php echo $STORY['media_url'];?>" style="width:323px;height:205px;margin-bottom:-12px;"></iframe><?} else { ?><a href="<?php echo $STORY['url'];?>" target="<?php echo $STORY['target'];?>"><img src="<?php echo $STORY['image'];?>" width="323" height="205"/></a><? } ?></p>
						<p><span><a href="<?php echo $STORY['url'];?>" target="<?php echo $STORY['target'];?>"><img src="<?php echo G5_THEME_URL;?>/img/new_icon01.jpg"/> <?php echo $STORY['subject'];?> &nbsp; <font style="font-size:12px;color:#3366FF">[크게보기]</font></a></span></p>
					</div>
				</li>
				<li>
					<div class="report_tit">
						<p>언론보도</p>
						<p><a href="/news/funding_news.php"><img src="<?php echo G5_THEME_URL;?>/img/more_icon01.png" alt="더보기"/></a></p>
					</div>
					<div class="report">
						<p><a href="<?php echo $PRESS['url'];?>" target="<?php echo $PRESS['target'];?>"><img src="<?php echo $PRESS['image'];?>" width="323" height="205"/></a></p>
						<p><span><a href="<?php echo $PRESS['url'];?>" target="<?php echo $PRESS['target'];?>"><img src="<?php echo G5_THEME_URL;?>/img/new_icon01.jpg"/> <?php echo $PRESS['subject'];?></a></span></p>
					</div>
				</li>
				<li>
					<div class="tip_tit">
						<p>투자상품 관련 소식</p>
						<p><a href="/bbs/board.php?bo_table=notice"><img src="<?php echo G5_THEME_URL;?>/img/more_icon01.png" alt="더보기"/></a></p>
					</div>
					<div class="tip">
					<table summary="표의정보">
						<caption>상황</caption>
						<colgroup>
							<col width="80%">
							<col width="20%">
						</colgroup>
						<tbody>
                        
                            <?php
                            for($i=0,$j=1; $i<count($ALIM); $i++,$j++) {
                                $thisClass = ($j%2==0) ? '' : 'list_bg';
                            ?>
                            
							<tr class="<?php echo $thisClass?>">
								<td style="height:25px;"><div style="height:25px;line-height:25px;overflow:hidden;cursor:pointer;"><a href="/bbs/board.php?bo_table=notice&wr_id=<?php echo $ALIM[$i]['wr_id'];?>"><?php echo htmlSpecialChars($ALIM[$i]['wr_subject'])?></a></div></td>
								<td><?php echo preg_replace("/-/", ".", $ALIM[$i]['wr_datetime'])?></td>
							</tr>
                            <? } ?>
                        
                        </tbody>
                    </table>
				</div>
			</li>
		</ul>
	</div>
	<!-- 인기상품리스트 시작 -->
	<div class="product_list">
		<div class="plist_tit">헬로펀딩 인기상품</div>

        <?php
        // 인기상품 리스트 출력
        if(count($POPLIST)) {
            for($i=0,$j=1; $i<count($POPLIST); $i++,$j++) {
        
                $title_image_tag = ($POPLIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$POPLIST[$i]['TITLE_IMAGE_URL']."' width='100%' height='100%'/>" : "";
                $repay_count = ($POPLIST[$i]['REPAY_COUNT'])?$POPLIST[$i]['REPAY_COUNT'] : 0;
        
        ?>
        
		<div class="invest_list2">
			<div class="boxArea" id="list_area">
				<div class="box product_count" >
					<div class="imgArea" onClick="location.href='<?php echo $POPLIST[$i]['DETAIL_URL'];?>';">
						<div class="main_image">
                            <?php
                                if($POPLIST[$i]['CATEGORY']=='1') {
                                    echo '<div class="cflag ca-B">동산</div>';
                                }
                                else if($POPLIST[$i]['CATEGORY']=='2') {
                                    echo ($POPLIST[$i]['mortgage_guarantees']) ? '<div class="cflag ca-A2">주택담보대출</div>' : '<div class="cflag ca-A">부동산</div>';
                                }
                                else if($POPLIST[$i]['CATEGORY']=='3') {
                                    echo '<div class="cflag ca-C">확정매출채권</div>';
                                }
                            ?>
                            <?php if($POPLIST[$i]["AUTO_INVEST_FLAG"] == 'Y'){ ?>
                                <div class="cflag ai">자동투자</div>
                            <?php } ?>
                            <?php if($POPLIST[$i]["new_flag"] == 'Y') {?>
                                <div class="nflag">N</div>
                            <?php } ?>
                            <?php echo $title_image_tag?>
                        </div>
						<div class="cover" style="display:block;"></div>
						<div class="cover_text" style="display:block;"><?php echo $POPLIST[$i]['COVER_CAPTION'];?></div>
						<div class="detail_state" style="display:block;"><?php echo $POPLIST[$i]['DETAIL_STATE'];?></div>
					</div>
					<div class="con">
						<div class="title"><?php echo $POPLIST[$i]['TITLE'];?></div>
						<!--<div class="flag_green" style="display:<?php echo ($POPLIST[$i]['PURCHASE_GUARANTEES']=='Y')?'block':'none'?>">채권매입계약</div>-->
						<? if($POPLIST[$i]['STREAM_URL1'] || $POPLIST[$i]['STREAM_URL2']) { ?><div class="flag_red"><img src="/images/investment/live_icon01.gif"/></div><? echo "\n"; } ?>
						<div class="subtext">투자시작일 : <?php echo date("Y년 m월 d일", strtotime($POPLIST[$i]['RECRUIT_PERIOD_START']))?></div>
						<ul class="info">
							<li>
								<div class="subject">투자자 수익률(연)</div>
								<div class="value"><?php echo $POPLIST[$i]['INVEST_RETURN'];?>%</div>
							</li>
							<li>
								<div class="subject">투자기간</div>
								<div class="value"><?php echo $POPLIST[$i]['INVEST_PERIOD'];?></div>
							</li>
							<li>
								<div class="subject">지급회차</div>
								<div class="value"><span style="color:<?php echo ($repay_count)?'#FF6633':'#AAA'?>"><?php echo $repay_count?></span> / <?php echo $POPLIST[$i]['TOTAL_REPAY_COUNT'];?></div>
							</li>
							<li class="end">
								<div class="subject">모집금액</div>
								<div class="value"><?php echo price_cutting($POPLIST[$i]['RECRUIT_AMOUNT'])?>원</div>
							</li>
						</ul>
						<ul class="progress">
							<li>참여진행률<b><?php echo $POPLIST[$i]['INVEST_PERCENT'];?>%</b>
								<div class="rate"><img src="<?php echo G5_THEME_URL;?>/img/rate_blue.gif" alt="진행률" style="width:<?php echo $POPLIST[$i]['INVEST_PERCENT'];?>%"/></div>
							</li>
						</ul>
						<div style="width:100%;text-align:center;">
							<a href='<?php echo $POPLIST[$i]['DETAIL_URL'];?>' class='btn_big_gray' style='margin:0;'><?php echo $POPLIST[$i]['BUTTON_CAPTION'];?></a>
						</div>
					</div>
				</div>
				<div class="box_end"></div>
			</div>
		</div>
        
        <?php
            }
        }
        ?>

	</div>
	<!-- 인기상품리스트 끝 -->
</article>
<!-- MAIN E N D -->

<script type="text/javascript" src="<?php echo G5_THEME_JS_URL;?>/jquery.flexslider.js"></script>
<script type="text/javascript" src="<?php echo G5_THEME_JS_URL;?>/jquery.vticker-min.js"></script>
<script type="text/javascript">
    $(function(){

        //$(function(){
        //	SyntaxHighlighter.all();
        //});
        $(window).load(function(){
            $('.flexslider').flexslider({
                animation: "slide",
                start: function(slider){
                    $('body').removeClass('loading');
                }
            });
        });


        $('#rolling_area').vTicker({
            speed: 600,
            pause: 6000,
            animation: 'fade',
            mousePause: true,
            showItems: 1
        });

        $(document).on('click', ".claim-mark", function() {
            var content = $(this).parent().parent().find(".pop_content");
            if(content.css("display") == "block"){
                content.hide();
            }else{
                $(".pop_content").not(this).hide();
                $(this).parent().parent().find(".pop_content").not(this).fadeToggle('slow');
            }
        });
        $('.close-content').click(function(){
            $('.pop_content').css('display','none');
        });
    });
</script>

<?php include_once(G5_THEME_PATH.'/tail.php'); ?>