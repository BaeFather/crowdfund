<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_MOBILE_PATH.'/head.php');

add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_JS_URL.'/swiper.min.css">', 0);
add_javascript('<script src="'.G5_THEME_JS_URL.'/swiper.min.js"></script>', 10);

?>

<!-- FlexSlider -->
<script type="text/javascript" src="<?=G5_THEME_JS_URL?>/jquery.flexslider.js"></script>
<script type="text/javascript">
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
</script>

<!-- 메인 슬라이드 시작 -->
<section>
	<div id="main" role="main">
		<div id="main_info" style="width:100%;max-height:375px;overflow:hidden; background:#000">
			<div class="slider">

				<div class="flexslider">
					<!-- 활성 투자상품 및 이벤트 배너 리스트 -->
					<ul class="slides">

<?
// 활성투자상품 리스트 출력
if(count($ALIST)) {
	for($i=0,$j=1; $i<count($ALIST); $i++,$j++) {

		$ALIST[$i]["TOTAL_INVEST_AMOUNT"] = (!$ALIST[$i]["TOTAL_INVEST_AMOUNT"]) ? 0 : $ALIST[$i]["TOTAL_INVEST_AMOUNT"];
		$ALIST[$i]["INVEST_PERCENT"] = (!$ALIST[$i]["INVEST_PERCENT"]) ? 0 : $ALIST[$i]["INVEST_PERCENT"];

		$main_image_tag      = ($ALIST[$i]['TITLE_IMAGE_URL_M']) ? "<img src='".$ALIST[$i]['TITLE_IMAGE_URL_M']."' style='width:100%;height:100%'>" : "";
		$total_invest_amount = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["TOTAL_INVEST_AMOUNT"]) : price_cutting($ALIST[$i]["TOTAL_INVEST_AMOUNT"]);
		$_ALIST[$i]['recruit_amount'] = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["RECRUIT_AMOUNT"]) : price_cutting($ALIST[$i]["RECRUIT_AMOUNT"]);
		$print_date          = ($ALIST[$i]["START_DATETIME"]) ? date("Y년 m월 d일 H시 i분", strtotime($ALIST[$i]["START_DATETIME"])) : date("Y년 m월 d일", strtotime($ALIST[$i]["RECRUIT_PERIOD_START"]));

		if($ALIST[$i]["GUBUN"]=="event") {
			echo "						<li class='prod_bg1' onClick=\"location.href='{$ALIST[$i]['DETAIL_URL']}'\" style='cursor:pointer;' >".$main_image_tag."</li>\n";
		}
		else {

?>
						<!--상품 시작-->
						<li>
							<div class="prod">
								<div style="width:100%">
									<div class="flag_green" style="display:<?=($AALIST[$i]['ADVANCE_INVEST']=='Y')?'block':'none'?>">사전투자 <?=(int)$AALIST[$i]['ADVANCE_INVEST_RATIO']?>%</div>
									<div class="flag_onair" style="display:<?=($AALIST[$i]['STREAM_URL1']=='Y')?'block':'none'?>"><img src="/images/investment/live_icon01.gif"></div>
									<div style="margin:0 2% 0 2%; height:22px;font-size:15px;line-height:19px;font-family:'NGB'; overflow:hidden;"><strong><?=$ALIST[$i]['TITLE']?></strong></div>
									<span style="padding:8px 0 8px; font-size:12px">모집기간 : <?=preg_replace("/-/", ".", $print_date)?> ~<span>
								</div>
								<center>
								<table summary="표의정보">
									<caption>상황</caption>
									<colgroup>
										<col width="15%">
										<col width="15%">
										<col width="15%">
										<col width="15%">
									</colgroup>
									<thead>
										<tr>
											<th scope="col" style="text-align:center;"><img src="<?=G5_THEME_URL?>/img/mobile/m_icon1.png"></th>
											<th scope="col" class="tb_bg"><img src="<?=G5_THEME_URL?>/img/mobile/m_icon2.png"></th>
											<th scope="col"><img src="<?=G5_THEME_URL?>/img/mobile/m_icon3.png"></th>
											<th scope="col" class="tb_bg"><img src="<?=G5_THEME_URL?>/img/mobile/m_icon4.png"></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:center;font-family:'NGB';">투자자 수익률(<?=($ALIST[$i]['GUBUN']=='event')?'회':'연'?>)</td>
											<td style="text-align:center;font-family:'NGB';">모집금액</td>
											<td style="text-align:center;font-family:'NGB';">투자기간</td>
											<td style="text-align:center;font-family:'NGB';">참여진행률</td>
										</tr>
										<tr class="f24">
											<td style="text-align:center;font-family:'NGB';"><?=$ALIST[$i]["INVEST_RETURN"]?>%</td>
											<td style="text-align:center;font-family:'NGB';"><?=$_ALIST[$i]['recruit_amount']?></td>
											<td style="text-align:center;font-family:'NGB';"><?=$ALIST[$i]["INVEST_PERIOD"]?></td>
											<td style="text-align:center;font-family:'NGB';"><?=$ALIST[$i]["INVEST_PERCENT"]?>%</td>
										</tr>
									</tbody>
								</table>
								</center>
								<div class="btn"><a href="<?=$ALIST[$i]['DETAIL_URL']?>">상품상세보기</a></div>
							</div>
							<span class="prod_bg"><?=$main_image_tag?></span>
							<!--<span class="prod_bg" ><img src="<?=G5_THEME_URL?>/img/mobile/main_bg02.jpg" alt=""></span>-->
						</li>
						<!--상품 끝-->
<?
		}
	}
}

// 이벤트 배너 출력 리스트
for($i=0; $i<count($EVENT_ARR); $i++) {
	echo "						<li class='prod_bg1' onClick=\"location.href='{$EVENT_ARR[$i]['url']}'\" style='cursor:pointer;' ><img src='{$EVENT_ARR[$i]['image_m']}' id='{$EVENT_ARR[$i]['image_id']}' width='100%'></li>\n";
}
?>
					</ul>
					<!-- 활성 투자상품 및 이벤트 배너 리스트 끝 -->
				</div>
			</div>
		</div>
	</div>

	<div class="txt_info">
		<ul class="txt_info1">
			<li>
				<p><?=$PRNT_SUBJECT['total_invest']?></p>
				<p><?=price_cutting($DP['total_invest'])?>원</p>
			</li>
			<li>
				<p><?=$PRNT_SUBJECT['total_repay']?></p>
				<p><?=price_cutting($DP['total_repay'])?>원</p>
			</li>
			<li>
				<p><?=$PRNT_SUBJECT['invest_ing_amount']?></p>
				<p><?=price_cutting($DP['invest_ing_amount'])?>원</p>
			</li>
		</ul>
		<ul class="txt_info2">
			<li>
				<p><?=$PRNT_SUBJECT['average_return']?></p>
				<p><?=$DP['average_return']?>%</p>
			</li>
			<li>
				<p>연체율
				<span id="btn1" style="display:inline-block;border-radius:100px;background-color:#fff;width:15px;height:15px;color:#000;font-size:13px;font-family:'NGB';font-weight:500;text-align:center;cursor:pointer;">!</span>
				<!--연체율이란-->
				<div id="conts1" style="position:absolute;margin-top:2px;margin-left:-20%;padding:15px 15px;border-radius:10px;border:1px solid #d6d6d6;background-color:#fff;color:#000;font-size:14px;text-align:left;line-height:22px;display:none;z-index:150;">
				<div id="close1" style="position:absolute;right:0;top:0;margin:5px 5px 0 0;font-size:18px;font-family:'verdana';cursor:pointer;width:25px;height:25px;background-color:#e5e5e5;text-align:center;">x</div>
				<strong style="font-size:14px;font-family:'NGB';">연체율</strong><br/>
				약정된 상환이 일부 혹은 전부 지연되기<br/>
				시작해 30일 이상, 90일 미만 경과한 대출<br/>
				연체율 = 연체잔여원금 / 대출잔여원금 <br/>
				(P2P금융협회 기준)
				</div>
				<script type="text/javascript">
				$('#btn1').on('click', function() {
					$('#conts1').fadeToggle('slow');
				});
				</script>
				<script type="text/javascript">
				  $('#close1').click(function(){
						$('#conts1').css('display','none');
				  });
				</script>
				</p>
				<p><?=$DP['overdue_perc']?>%</p>
			</li>
			<li>
				<p>부실률
				<span id="btn2" style="display:inline-block;border-radius:100px;background-color:#fff;width:15px;height:15px;color:#000;font-size:13px;font-family:'NGB';font-weight:500;text-align:center;cursor:pointer;">!</span>
				<!--부실률이란-->
				<div id="conts2" style="position:absolute;margin-top:2px;margin-left:-25%;padding:15px 15px;border-radius:10px;border:1px solid #d6d6d6;background-color:#fff;color:#000;font-size:14px;text-align:left;line-height:22px;display:none;z-index:150;">
				<div id="close2" style="position:absolute;right:0;top:0;margin:5px 5px 0 0;font-size:18px;font-family:'verdana';cursor:pointer;width:25px;height:25px;background-color:#e5e5e5;text-align:center;">x</div>
				<strong style="font-size:14px;font-family:'NGB';">부실률</strong><br/>
				약정된 상환이 일부 혹은 전부 지연되기<br/>
				시작해 90일 이상 경과한 대출<br/>
				부실률 = 부실잔여원금 / 총 누적대출액<br/>
				(P2P금융협회 기준)
				</div>
				<script type="text/javascript">
				$('#btn2').on('click', function() {
					$('#conts2').fadeToggle('slow');
				});
				</script>
				<script type="text/javascript">
				  $('#close2').click(function(){
						$('#conts2').css('display','none');
				  });
				</script>
				</p>
				<p><?=$DP['bankruptcy']?>%</p>
			</li>
			<li>
				<p onClick="location.href='<?=G5_URL?>/investment/invest_list.php?mode=success';" style="margin:-2px 0 0 5px;width:80px;font-size:16px;padding:3px 0;color:gold;border-radius:3px;border:1px solid gold;">
				성공사례</p>
				<p style="font-size:12px;"><?=preg_replace('/-/', '.', substr($DP['standard_date'],2))?> 기준</p>
			</li>
		</ul>
  </div>
</section>

<script type="text/javascript" src="<?=G5_THEME_JS_URL?>/jquery.vticker-min.js"></script>
<script type="text/javascript">
$(function(){
	$('#rolling_area').vTicker({
		speed: 600,
		pause: 6000,
		animation: 'fade',
		mousePause: true,
		showItems: 1
	});
});
</script>

<article>
	<div class="notice">
		<div class="notices">
			<div style="float:left;margin-top:10px;"><img src="<?=G5_THEME_URL?>/img/mobile/notice_icon.png" height="18"></div>
			<div id="rolling_area" class="rolling_area">
				<ul style="text-align:left;line-height:18px; font-size:13px; overflow:hidden;">
<? for($i=0; $i<count($NOTICE); $i++) { ?>
					<li>
						<span><a href="/bbs/board.php?bo_table=notice&wr_id=<?=$NOTICE[$i]['wr_id']?>" style="color:#3C61C9;"><?=htmlSpecialChars($NOTICE[$i]['wr_subject'])?></a></span>
						<span style="padding-left:10px;color:#3C61C9;"><?=preg_replace("/-/", ".", $NOTICE[$i]['wr_datetime'])?></span>
					</li>
<? } ?>
				</ul>
			</div>
		</div>
	</div>

<?
/*
if(count($ALIST)) {
?>
	<!--신상품 노출 시작-->
	<div class="new_info">
<?
	for($i=0,$j=1; $i<count($ALIST); $i++,$j++) {
?>
		<ul>
			<li><img src="<?=G5_THEME_URL?>/img/mobile/new_icon.png"> <?=$ALIST[$i]['TITLE']?></li>
			<li class="list_info1">
				<div>
					<p><img src="<?=G5_THEME_URL?>/img/mobile/new_info_icon01m.png" /></p>
					<p>투자자 수익률(<?=($ALIST[$i]['GUBUN']=='event')?'회':'연'?>)<br/><b><?=$ALIST[$i]["INVEST_RETURN"]?>%</b></p>
				</div>

				<div>
					<p><img src="<?=G5_THEME_URL?>/img/mobile/new_info_icon02m.png" style="width:19px;"/></p>
					<p>투자기간<br/><b><?=$ALIST[$i]["INVEST_PERIOD"]?></b></p>
				</div>

				<div>
					<p><img src="<?=G5_THEME_URL?>/img/mobile/new_info_icon03m.png" /></p>
					<p>모집금액<br/><b><?=$_ALIST[$i]['recruit_amount']?>원</b></p>
				</div>
			</li>
			<li><a href="<?=$ALIST[$i]['DETAIL_URL']?>">투자상품 바로가기</a></li>
		</ul>
<?
	}
?>
	</div>
	<!--신상품 노출 끝-->
<?
}
*/
?>

<?
if(count($AALIST)) {
?>
	<div class="contents invest_list2" style="min-height:500px;">
		<!--인기상품 시작-->
		<div class="hit_tit">헬로펀딩 최신상품</div>
			<div class="boxArea" id="list_area">
<?
	for($i=0,$j=1; $i<count($AALIST); $i++,$j++) {

		$AALIST[$i]['title_image_tag'] = ($AALIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$AALIST[$i]['TITLE_IMAGE_URL']."' width='100%' height='100%'>" : "";
		if(!$AALIST[$i]['TOTAL_INVEST_AMOUNT']) $AALIST[$i]['TOTAL_INVEST_AMOUNT'] = '0';

		$start_timestamp = strtotime($AALIST[$i]['START_DATETIME']);
		$print_sdate = date('Y년 m월 d일', $start_timestamp);
		$print_sdate.= ' ' . get_yoil($AALIST[$i]['START_DATETIME']).'요일 ';
		$print_sdate.= (date(H, $start_timestamp) < 12) ? ' 오전' : ' 오후';
		$print_sdate.= date('H시', $start_timestamp);

?>
				<div class="box product_count">
					<div class="imgArea" onClick="location.href='<?=$AALIST[$i]['DETAIL_URL']?>';">
						<div class="main_image"><?=$AALIST[$i]['title_image_tag']?></div>
						<div class="flag_red" style="display:<?=($AALIST[$i]['PURCHASE_GUARANTEES']=='Y')?'block':'none'?>">채권매입계약</div>
						<div class="flag_green" style="display:<?=($AALIST[$i]['ADVANCE_INVEST']=='Y')?'block':'none'?>">사전투자 <?=(int)$AALIST[$i]['ADVANCE_INVEST_RATIO']?>%</div>
						<? if($AALIST[$i]['STREAM_URL1'] && $AALIST[$i]['STREAM_URL2']) { ?><div class="flag_onair"><img src="/images/investment/live_icon01.gif"></div><? } ?>
					</div>
					<div class="con">
						<div class="title"><?=$AALIST[$i]['TITLE']?></div>
						<div class="subtext">투자 시작 : <?=$print_sdate?></div>
						<ul class="info">
							<li>
								<div class="subject">투자자 수익률(<?=($AALIST[$i]['GUBUN']=='event')?'회':'연';?>)</div>
								<div class="value"><?=$AALIST[$i]['INVEST_RETURN']?>%</div>
							</li>
							<li class="right_end">
								<div class="subject">투자기간</div>
								<div class="value"><?=$AALIST[$i]['INVEST_PERIOD']?></div>
							</li>
							<li class="bottom_end">
								<div class="subject">목표금액</div>
								<div class="value"><?=($AALIST[$i]['GUBUN']=='event') ? number_format($AALIST[$i]['RECRUIT_AMOUNT']) : price_cutting($AALIST[$i]['RECRUIT_AMOUNT'])?>원</div>
							</li>
							<li class="right_end bottom_end">
								<div class="subject">모집금액</div>
								<div class="value"><?=($AALIST[$i]['GUBUN']=='event') ?  number_format($AALIST[$i]['TOTAL_INVEST_AMOUNT']) : price_cutting($AALIST[$i]['TOTAL_INVEST_AMOUNT'])?>원</div>
							</li>
						</ul>
						<ul class="progress">
							<li>
								진행률<b><?=($AALIST[$i]['INVEST_PERCENT']) ? $AALIST[$i]['INVEST_PERCENT'] : '0';?>%</b>
								<div class="rate"><img src="/images/investment/rate_blue.gif" alt="진행률" style="width:<?=($AALIST[$i]['INVEST_PERCENT']) ? $AALIST[$i]['INVEST_PERCENT'] : '0.2';?>%"></div>
							</li>
						</ul>
						<div style="width:100%;text-align:center;">
							<a href='<?=$AALIST[$i]['DETAIL_URL']?>' class='btn_big_blue' style='margin:0;'><?=$AALIST[$i]['BUTTON_CAPTION']?></a>
						</div>
					</div>
				</div>
				<div class='box_end'></div>
<?
	}
?>
			</div>
		</div>
		<!--인기상품 끝-->
	</div>
<?
}
?>

	<div class="contents">

		<!--헬로펀딩 스토리-->
		<div class="story">
			<div class="story_tit">헬로펀딩 스토리</div>
			<div class="story_cont">
				<p>
					<? if($STORY['media_url']) { ?>
					<embed src="<?=$STORY['media_url']?>" style="width:100%;height:100%;">
					<?} else { ?>
					<a href="<?=$STORY['url']?>" target="<?=$STORY['target']?>"><a href="<?=$STORY['url']?>" target="<?=$STORY['target']?>"><img src="<?=$STORY['image']?>"></a>
					<? } ?>
				</p>
				<p><img src="<?=G5_THEME_URL?>/img/mobile/new_icon.png"> <a href="<?=$STORY['url']?>" target="<?=$STORY['target']?>"><?=$STORY['subject']?></a></p>
			</div>
			<div class="story_more"><a href="/news/funding_news.php"><img src="<?=G5_THEME_URL?>/img/mobile/more_btn03.jpg"></a></div>
		</div>

		<!--헬로펀딩 리포트-->
		<div class="report">
			<div class="report_tit">시장동향 관련 보도자료 </div>
			<div class="report_cont">
				<p><a href="<?=$PRESS['url']?>" target="<?=$PRESS['target']?>"><img src="<?=$PRESS['image']?>"></a></p>
				<p><img src="<?=G5_THEME_URL?>/img/mobile/new_icon.png" alt=""> <a href="<?=$PRESS['url']?>" target="<?=$PRESS['target']?>"><?=$PRESS['subject']?></a></p>
			</div>
			<div class="report_more"><a href="/news/funding_news.php"><img src="<?=G5_THEME_URL?>/img/mobile/more_btn02.jpg"></a></div>
		</div>

		<!--헬로펀딩 투자상품 관련 소식-->
		<div class="inform">
			<div class="inform_tit">투자상품 관련 소식</div>
			<center>
			<table class="inform_list" summary="표의정보">
				<caption>상황</caption>
				<colgroup>
					<col width="70%">
					<col width="30%">
				</colgroup>
				<tbody>
<?
for($i=0,$j=1; $i<count($ALIM); $i++,$j++) {
	$thisClass = ($j%2==0) ? '' : 'list_bg';
?>
					<tr class="<?=$thisClass?>">
						<td style="text-align:left"><div style="height:25px;line-height:25px;overflow:hidden;"><a href="/bbs/board.php?bo_table=notice&wr_id=<?=$ALIM[$i]['wr_id']?>"><?=htmlSpecialChars($ALIM[$i]['wr_subject'])?></a></div></td>
						<td style="text-align:center"><?=preg_replace("/-/", ".", $ALIM[$i]['wr_datetime'])?></td>
					</tr>
<?
}
?>
				</tbody>
			</table>
			</center>
			<div class="inform_more"><a href="/bbs/board.php?bo_table=notice"><img src="<?=G5_THEME_URL?>/img/mobile/more_btn01.jpg"></a></div>
		</div>

	</div>


	<div class="contents invest_list2" style="min-height:500px;">

		<!--인기상품 시작-->
		<div class="hit_tit">헬로펀딩 인기상품</div>
			<div class="boxArea" id="list_area">
<?
// 인기상품 리스트 출력
if(count($POPLIST)) {
	for($i=0,$j=1; $i<count($POPLIST); $i++,$j++) {

		$title_image_tag = ($POPLIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$POPLIST[$i]['TITLE_IMAGE_URL']."' style='width:100%;min-height:100%'>" : "";
		$repay_count = ($POPLIST[$i]['REPAY_COUNT'])?$POPLIST[$i]['REPAY_COUNT'] : 0;

		$start_timestamp = strtotime($POPLIST[$i]['START_DATETIME']);
		$print_sdate = date('Y년 m월 d일', $start_timestamp);
		$print_sdate.= ' ' . get_yoil($POPLIST[$i]['START_DATETIME']).'요일 ';
		$print_sdate.= (date(H, $start_timestamp) < 12) ? ' 오전' : ' 오후';
		$print_sdate.= date('H시', $start_timestamp);

?>
				<div class="box product_count">
					<div class="imgArea" onClick="location.href='<?=$POPLIST[$i]['DETAIL_URL']?>';">
						<div class="main_image"><?=$title_image_tag?></div>
						<div class="cover" style="display:block;"></div>
						<div class="cover_text" style="display:block;"><?=$POPLIST[$i]['COVER_CAPTION']?></div>
						<div class="detail_state" style="display:block;"><?=$POPLIST[$i]['DETAIL_STATE']?></div>
						<!--<div class="flag_green" style="display:<?=($POPLIST[$i]['PURCHASE_GUARANTEES']=='Y')?'block':'none'?>">채권매입계약</div>-->
						<? if($POPLIST[$i]['STREAM_URL1'] && $POPLIST[$i]['STREAM_URL2']) { ?><div class="flag_onair"><img src="/images/investment/live_icon01.gif"></div><? } ?>
					</div>
					<div class="con">
						<div class="title"><?=$POPLIST[$i]['TITLE']?></div>
						<div class="subtext">투자 시작 : <?=$print_sdate?></div>
						<ul class="info">
							<li>
								<div class="subject">투자자 수익률(연)</div>
								<div class="value"><?=$POPLIST[$i]['INVEST_RETURN']?>%</div>
							</li>
							<li class="right_end">
								<div class="subject">투자기간</div>
								<div class="value"><?=$POPLIST[$i]['INVEST_PERIOD']?></div>
							</li>
							<li class="bottom_end">
								<div class="subject">지급회차</div>
								<div class="value"><span style="color:<?=($repay_count)?'#FF6633':'#AAA'?>"><?=$repay_count?></span> / <?=$POPLIST[$i]['TOTAL_REPAY_COUNT']?></div>
							</li>
							<li class="right_end bottom_end">
								<div class="subject">모집금액</div>
								<div class="value"><?=price_cutting($POPLIST[$i]['RECRUIT_AMOUNT'])?>원</div>
							</li>
						</ul>
						<ul class="progress">
							<li>
								진행률<b><?=$POPLIST[$i]['INVEST_PERCENT']?>%</b>
								<div class="rate"><img src="/images/investment/rate_blue.gif" alt="진행률" style="width:<?=$POPLIST[$i]['INVEST_PERCENT']?>%"></div>
							</li>
						</ul>
						<div style="width:100%;text-align:center;">
							<a href='<?=$POPLIST[$i]['DETAIL_URL']?>' class='btn_big_gray' style='margin:0;'><?=$POPLIST[$i]['BUTTON_CAPTION']?></a>
						</div>
					</div>
				</div>
				<div class="box_end"></div>
<?
	}
}
?>

			</div>
		</div>
		<!--인기상품 끝-->

	</div>

<?
include_once(G5_THEME_MOBILE_PATH.'/tail.php');
?>