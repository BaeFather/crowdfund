<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_MOBILE_PATH.'/head.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_JS_URL.'/swiper.min.css">', 0);
add_javascript('<script src="'.G5_THEME_JS_URL.'/swiper.min.js"></script>', 10);

?>
<script>
$(document).ready(function(){
	$('.slider').bxSlider({
		mode: 'fade',
		speed:600,
		controls:false,
		pause:5500
	});
});
</script>

<style>
	.reqsms { position:absolute; z-index:1000; margin:0 auto; left:50%; top:50%; width:120px; margin-left:-60px; margin-top:1.5%; }
</style>

	<div class="visual">
		<!--<div id="reqsms" class="reqsms"><img src="<?=G5_URL?>/images/main/alarm_btn.png" width="120"></div>-->
		<div class="visualArea">
			<ul class="slider">

		<!--<li><img src="<?=G5_URL?>/images/main/main_02_m.jpg" alt="visual02" /></li>-->
		<!--<li><img src="<?=G5_URL?>/images/main/main_03_m.jpg" alt="visual03" /></li>-->
		<!--<li><a href="<?=G5_URL?>/investment/investment.php?prd_idx=111"><img src="<?=G5_URL?>/images/main/main_no.12_event_m.jpg" alt="visual03" /></a></li>-->

				<?if(date("Y-m-d H:i:s") >= '2016-12-21 11:00:00' && date("Y-m-d H:i:s") <= '2016-12-25 23:59:59'){?><li><a href="/event/invest_epilogue.php"><img src="<?=G5_URL?>/images/main/reply_m.jpg" alt="visual05" /></a></li><?}?>
				<li><img src="<?=G5_URL?>/images/main/main_01_m.jpg" alt="visual01" /></li>
				<li><a href="/event_invest/event_invest.php?prd_idx=6"><img src="<?=G5_URL?>/images/main/main_04_235event_m.jpg" alt="visual04" /></a></li>
		<!--<li><a href="/event/recommend.php"><img src="<?=G5_URL?>/images/main/main_pickme_event1128_m.jpg" alt="visual05" /></a></li>-->
			</ul>
		</div>

		<!-- 비주얼 하단내용 -->
		<?
		$sql = "select * from cf_invest";
		$row = sql_fetch($sql);
		$average_return		= ($row["average_return"]) ? $row["average_return"] : 0;
		$total_invest			= ($row["total_invest"]) ? number_format($row["total_invest"]) : 0;
		$total_repay			= ($row["total_repay"]) ? number_format($row["total_repay"]) : 0;
		$bankruptc				= ($row["bankruptc"]) ? $row["bankruptc"] : 0;
		$invest_success_count	= ($row["invest_success_count"]) ? $row["invest_success_count"] : 0;
		$display				  = $row["display"];
		if($display=="Y") {
		?>
		<ul class="info1">
			<li>평균수익률<div>(연)<?=$average_return?>%</div></li>
			<span>|</span>
			<li>누적투자액<div><?=$total_invest?>원</div></li>
		</ul>
		<ul class="info2">
			<li>누적상환액<div><?=$total_repay?>원</div></li>
			<span>|</span>
			<li>부도율<div><?=$bankruptc?>%</div></li>
			<span>|</span>
			<li onClick="location.href='<?=G5_URL?>/investment/invest_list.php?mode=success';" style="cursor:pointer">&nbsp;<div style="font-size:16px;color:gold">성공사례</div></li>
		</ul>
		<?
		}
		?>
	</div>
	<div id="mainb">
		<div id="mainb1" class="mainb" style="height:100px"><img src="images/m/mboxt1_.png" style="width:140px"><img src="images/m/mboxi1.png" style="width:50px; float:right;" alt="투자상품보기"></div>
		<div id="mainb2" class="mainb" style="height:100px"><img src="images/m/mboxt2_.png" style="width:140px"><img src="images/m/mboxi2.png" style="width:50px; float:right;" alt="대출신청하기"></div>
		<div id="mainb3" class="mainb" style="height:100px"><img src="images/m/mboxt3_.png" style="width:140px"><img src="images/m/mboxi3.png" style="width:50px; float:right;" alt="헬로펀딩스토리"></div>
	</div>


	<!-- 활성 투자상품 리스트 -->
<?
// 활성투자상품 리스트 배열화
include_once(G5_LIB_DIR.'/class_xmlparser.php');
$XMLOBJ = new XMLParser(G5_URL.'/xml/active_product_list.xml');
$XMLARR = $XMLOBJ->data['child'][2]['child'];
for($i=0; $i<count($XMLARR); $i++) {
	for($k=0; $k<count($XMLARR[$i]['child']); $k++) {
		$ALIST[$i][$XMLARR[$i]['child'][$k]['name']] = trim($XMLARR[$i]['child'][$k]['data']);
	}
}
$XMLOBJ = $XMLARR = NULL;

if(count($ALIST)) {
?>
	<div id="prdtwrap">
		<div class="xprdt_title_image"><img src="<?=G5_URL?>/images/main/invest_tit01_m.png" width="100%"></div>
<?
	for($i=0; $i<count($ALIST); $i++) {

		$ALIST[$i]["TOTAL_INVEST_AMOUNT"] = (!$ALIST[$i]["TOTAL_INVEST_AMOUNT"]) ? 0 : $ALIST[$i]["TOTAL_INVEST_AMOUNT"];
		$ALIST[$i]["INVEST_PERCENT"] = (!$ALIST[$i]["INVEST_PERCENT"]) ? 0 : $ALIST[$i]["INVEST_PERCENT"];

		$main_image_tag      = ($ALIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$ALIST[$i]['TITLE_IMAGE_URL']."' width='100%'>" : "";
		$total_invest_amount = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["TOTAL_INVEST_AMOUNT"]) : price_cutting($ALIST[$i]["TOTAL_INVEST_AMOUNT"]);
		$recruit_amount      = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["RECRUIT_AMOUNT"]) : price_cutting($ALIST[$i]["RECRUIT_AMOUNT"]);

?>
		<div class="xlistwrap">
			<div class="xcontentwrap">
				<div class="ximage_area" onClick="location.href='<?=$ALIST[$i]['DETAIL_URL']?>'">
					<div class="main_image"><?=$main_image_tag?></div>
					<div class="flag_green" style="display:<?=($ALIST[$i]['PURCHASE_GUARANTEES']=='Y') ? '' : 'none'; ?>">채권매입계약</div>
					<div class="flag_orange" style="display:<?=($ALIST[$i]['ADVANCED_PAYMENT']=='Y') ? '' : 'none'; ?>">이자 선지급</div>
					<? if(!$main_image_tag){ ?><a href='<?=$ALIST[$i]['DETAIL_URL']?>' class='btn_more'>더보기</a><? echo "\n"; }?>
				</div>
				<div class="xtext_area">
					<div class="xtitle_text1"><?=$ALIST[$i]['TITLE']?></div>
					<div class="xtitle_text2">투자시작일 : <?=date("Y년 m월 d일",strtotime($ALIST[$i]["RECRUIT_PERIOD_START"]))?></div>
					<ul>
						<li class="xtext">수익률<?=($ALIST[$i]["GUBUN"]=='normal')?'(연)':''?> <span class="xval"><?=$ALIST[$i]["INVEST_RETURN"]?>%</span></li>
						<li class="xtext">기간 <span class="xval"><?=$ALIST[$i]["INVEST_PERIOD"]?></span></li>
						<li class="xtext">모집현황 <span class="xval"><?=$total_invest_amount?>원 / <?=$recruit_amount?>원</span></li>
						<li>참여진행률 <span class="xval blue"><?=$ALIST[$i]["INVEST_PERCENT"]?>%</span></li>
					</ul>
					<div class="xrate"><img src="/images/investment/rate_blue.gif" width="<?=($ALIST[$i]["INVEST_PERCENT"])?$ALIST[$i]["INVEST_PERCENT"]:0.2;?>%" height="12px"></div>
					<div class="xbutton_area">
						<a href="<?=$ALIST[$i]['DETAIL_URL']?>" class="xbtn_blue">상품상세보기</a>
					</div>
				</div>
			</div>
		</div>
<?
	}
?>
  </div>
<?
}
?>
	<!-- 활성 투자상품 리스트 -->


<? if(date(Ymd)>=20161031 && date(Ymd)<=20161111) { ?>
	<style>
	#popup { display:none; position:relative; width:100%; background-color:#fff; }
	#popup .title { line-height:36px; text-indent:15px; text-align:left; color:#fff; font-size:15px; background-color:#284893; }
	#popup .close { position:absolute; right:0px; top:0px; width:20px; cursor:pointer; }
	#popup .text { padding:20px 10px 15px 0px; font-size:14px; line-height:18px; color:#202020; font-family:'NGB'; }
	</style>
	<!-- 보고싶습니다 이벤트 -->
	<div id="popup">
		<img src="/images/btn_close.gif" alt="close" class="close" />
		<div><a href="/event/invitation.php?event_idx=1"><img src="/images/main/invitation/invitation_event.jpg" width="100%"></a></div>
		<div style="babkground:#ccc;text-align:right; padding:6px 16px 6px;">
		  <input type="checkbox" id="popupClose" value="1">
			<label for="popupClose">오늘하루 열지 않음</label>
			<span id="closeLayer" style="margin-left:30px; cursor:pointer;">×닫기</span>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		if(get_cookie('popupOpen')==false) {
			$.blockUI({
				message: $('#popup'),
				css: { top:'15%',left:'1%',width:'98%',border:0, cursor:'default' }
			});
		}
	});

	$('#popupClose').on('click', function(){
		if($('#popupClose').is(':checked')) {
			set_cookie('popupOpen', true, 12, '/');
		}
		else {
			delete_cookie('popupOpen');
		}
		//alert(get_cookie('popupOpen'));
	});
	</script>
	<!-- 보고싶습니다 이벤트 -->
<? } ?>

	<script type="text/javascript">
	$(function(){
		$('#mainb1').click(function(){ $(location).attr('href', '<?=G5_URL?>/investment/invest_list.php'); });
		$('#mainb2').click(function(){ $(location).attr('href', '<?=G5_URL?>/loan/loan.php'); });
		$('#mainb3').click(function(){ $(location).attr('href', '<?=G5_URL?>/news/funding_news.php'); });
	});
	</script>

	<script type="text/javascript">
	// 레이어 오프
	$(document).on("click", "#no, .close, #closeLayer", function(){
		$.unblockUI();
		return false;
	});
	</script>

<?php
include_once(G5_THEME_MOBILE_PATH.'/tail.php');
?>