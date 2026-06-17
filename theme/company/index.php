<?php
define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($_REQUEST['mode']=="debug") {
	setcookie("debug_mode", true, time()+3600*3, "/", G5_COOKIE_DOMAIN, true, true);
	echo "<script>location.href='/';</script>";
}

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_THEME_PATH.'/index_head.php');


// 메인 배너이미지 정보
$mb_sql = "select * from g5_main_banner where idx = '1' ";
$mb_r = sql_fetch($mb_sql);

$mb_img1 = $mb_r['bn_img1'];
$mb_img2 = $mb_r['bn_img2'];
$mb_img3 = $mb_r['bn_img3'];

$mb_dir = str_replace($_SERVER['DOCUMENT_ROOT'],'',G5_DATA_PATH."/main_banner/");

?>

<!-- MAIN START -->

<style>
	/* 배너이미지 3개 */
	#container .visual .slider li:nth-child(1) { background:url('<?=G5_URL?>/images/main/main_01.jpg') no-repeat left top; }
	/* #container .visual .slider li:nth-child(2) { background:url('<?=G5_URL?>/images/main/main_02.jpg') no-repeat left top; } */
	/* #container .visual .slider li:nth-child(3) { background:url('<?=G5_URL?>/images/main/main_03.jpg') no-repeat left top; } */
	#container .visual .slider li:nth-child(2) { background:url('<?=G5_URL?>/images/main/main_04_235event.jpg') no-repeat left top; }
	#container .visual .slider li:nth-child(3) { background:url('<?=G5_URL?>/images/main/main_pickme_event1128.jpg') no-repeat left top; }
	/*
	#container .visual .slider li:nth-child(1) { background:url('<?php echo $mb_dir.$mb_img1;?>') no-repeat left top; }
	#container .visual .slider li:nth-child(2) { background:url('<?php echo $mb_dir.$mb_img2;?>') no-repeat left top; }
	#container .visual .slider li:nth-child(3) { background:url('<?php echo $mb_dir.$mb_img3;?>') no-repeat left top; }
	*/
	.reqsms { position:absolute; z-index:1000; top:350px;left:424px; width:143px; height:32px; border:0px solid green; }
</style>

<!-- 비주얼 -->
	<div class="visual">
    <!--<div id="reqsms" class="reqsms"><a href="#"><img src="<?=G5_URL?>/images/main/alarm_btn.png"></a></div>-->
		<div class="visualArea">
			<ul class="slider">

		<!--<li><img src="<?=G5_URL?>/images/main/main_02.jpg" alt="visual02" /></li>-->
		<!--<li><img src="<?=G5_URL?>/images/main/main_03.jpg" alt="visual03" /></li>-->
		<!--<li><a href="/investment/investment.php?prd_idx=111"><img src="<?=G5_URL?>/images/main/main_no.12_event.jpg" alt="visual03" /></a></li>-->

				<?if(date("Y-m-d H:i:s") >= '2016-12-21 11:00:00' && date("Y-m-d H:i:s") <= '2016-12-25 23:59:59'){?><li><a href="/event/invest_epilogue.php"><img src="<?=G5_URL?>/images/main/reply.jpg" alt="visual05" /></a></li><?}?>

				<li><img src="<?=G5_URL?>/images/main/main_01.jpg" alt="visual01" /></li>
				<li><a href="/event_invest/event_invest.php?prd_idx=6"><img src="<?=G5_URL?>/images/main/main_04_235event.jpg" alt="visual04" /></a></li>
		<!--<li><a href="/event/recommend.php"><img src="<?=G5_URL?>/images/main/main_pickme_event1128.jpg" alt="visual05" /></a></li>-->
			</ul>
		</div>
		<?
		$sql = "SELECT * FROM cf_invest";
		$row = sql_fetch($sql);
		$average_return		= ($row["average_return"]) ? $row["average_return"] : 0;
		$total_invest			= ($row["total_invest"]) ? number_format($row["total_invest"]) : 0;
		$total_repay			= ($row["total_repay"]) ? number_format($row["total_repay"]) : 0;
		$bankruptc				= ($row["bankruptc"]) ? $row["bankruptc"] : 0;
		$invest_success_count	= ($row["invest_success_count"]) ? $row["invest_success_count"] : 0;
		$display				  = $row["display"];

		if($display=="Y") {
		?>
		<!-- 비주얼 하단내용 -->
		<ul class="info">
			<li>평균수익률<div>(연)<?=$average_return?>%</div></li>
			<li>누적투자액<div><?=$total_invest?>원</div></li>
			<li>누적상환액<div><?=$total_repay?>원</div></li>
			<li>부도율<div><?=$bankruptc?>%</div></li>
			<li onClick="location.href='<?=G5_URL?>/investment/invest_list.php?mode=success';" style="cursor:pointer">&nbsp;<div style="color:gold">성공사례</div></li>
		</ul>
		<?
		}
		?>
	</div>
	<!-- 투자하기 -->
	<div class="rMenu">
		<div class="rMenu1"><a href="/investment/invest_list.php"><img src="/images/menu01.gif" alt="투자하기" /></a></div>
		<div class="rMenu2"><a href="/loan/loan.php"><img src="/images/menu02.gif" alt="대출하기" /></a></div>
		<div class="rMenu3"><a href="/news/funding_news.php"><img src="/images/menu03.gif" alt="헬로펀딩스토리" /></a></div>
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
		<div class="xprdt_title"><img src="/images/main/invest_tit01.png"></div>
		<div class="boxArea" style="background-color:#fff;">
<?
	for($i=0,$j=1; $i<count($ALIST); $i++,$j++) {

		$ALIST[$i]["TOTAL_INVEST_AMOUNT"] = (!$ALIST[$i]["TOTAL_INVEST_AMOUNT"]) ? 0 : $ALIST[$i]["TOTAL_INVEST_AMOUNT"];
		$ALIST[$i]["INVEST_PERCENT"] = (!$ALIST[$i]["INVEST_PERCENT"]) ? 0 : $ALIST[$i]["INVEST_PERCENT"];

		$main_image_tag      = ($ALIST[$i]['TITLE_IMAGE_URL']) ? "<img src='".$ALIST[$i]['TITLE_IMAGE_URL']."' width='100%' height='100%'>" : "";
		$total_invest_amount = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["TOTAL_INVEST_AMOUNT"]) : price_cutting($ALIST[$i]["TOTAL_INVEST_AMOUNT"]);
		$recruit_amount      = ($ALIST[$i]["GUBUN"]=='event') ? number_format($ALIST[$i]["RECRUIT_AMOUNT"]) : price_cutting($ALIST[$i]["RECRUIT_AMOUNT"]);

?>
			<div class="box">
				<div class="imgArea" onClick="location.href='<?=$ALIST[$i]['DETAIL_URL']?>';">
					<div class="main_image"><?=$main_image_tag?></div>
					<? if(!$main_image_tag){ ?><a href='<?=$ALIST[$i]['DETAIL_URL']?>' class='btn_more'>더보기</a><? echo "\n"; }?>
				</div>
				<div class="con">
					<div class="title"><?=$ALIST[$i]['TITLE']?></div>
					<? if($ALIST[$i]['PURCHASE_GUARANTEES']=='Y') { ?><div class="flag_green">채권매입계약</div><? echo "\n"; }?>
					<? if($ALIST[$i]['ADVANCED_PAYMENT']=='Y') { ?><div class="flag_orange">이자 선지급</div><? echo "\n"; } ?>
					<div class="subtext">
						투자시작일 : <?=date("Y년 m월 d일",strtotime($ALIST[$i]["RECRUIT_PERIOD_START"]))?>
					</div>
					<ul class="info">
						<li style="background-color:#ECF5FA;">
							<div class="subject">수익률(연)</div>
							<div class="value"><?=$ALIST[$i]["INVEST_RETURN"]?>%</div>
						</li>
						<li style="background-color:#DDEEF6;">
							<div class="subject">기간</div>
							<div class="value"><?=$ALIST[$i]["INVEST_PERIOD"]?></div>
						</li>
						<li style="background-color:#ECF5FA;">
							<div class="subject">목표금액</div>
							<div class="value"><?=$recruit_amount?>원</div>
						</li>
						<li class="end" style="background-color:#DDEEF6;">
							<div class="subject">모집금액</div>
							<div class="value"><?=$total_invest_amount?>원</div>
						</li>
					</ul>
					<ul class="progress">
						<li>참여진행률<b><?=$ALIST[$i]["INVEST_PERCENT"]?>%</b>
							<div class="rate"><img src="../images/investment/rate_blue.gif" alt="진행률" style="width:<?=$ALIST[$i]["INVEST_PERCENT"]?>%" /></div>
						</li>
					</ul>
					<div style="width:100%;text-align:center;">
						<a href="<?=$ALIST[$i]['DETAIL_URL']?>" class='btn_big_blue' style='margin:0;'>상품상세보기</a>
					</div>
				</div>
			</div>
			<? if($j < count($ALIST)) { ?><div class="box_end"></div><? } ?>

<?
	}
?>
		</div>
	</div>
<?
}
?>
	<!-- 활성 투자상품 리스트 -->


<!-- MAIN E N D -->

<? if(date(YmdH)>=2016103100 && date(YmdH)<=2016111117) { ?>
	<style>
	#popup { display:none; position:relative; width:462px; background-color:#fff; }
	#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
	#popup .close { position:absolute; right:0px; top:0px; cursor:pointer; }
	#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
	</style>
	<!-- 보고싶습니다 이벤트 -->
	<div id="popup">
		<img src="/images/btn_close.gif" alt="close" class="close" />
		<div><a href="/event/invitation.php?event_idx=1"><img src="/images/main/invitation/invitation_event.jpg" width="462" height="370"></a></div>
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
				css: { top:'20%',left:'33%',width:'462px',border:0, cursor:'default' }
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
	// 레이어 오프
	$(document).on("click", "#no, .close, #closeLayer", function(){
		$.unblockUI();
		return false;
	});
	</script>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
