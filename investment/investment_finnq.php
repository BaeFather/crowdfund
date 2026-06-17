<?

switch($PRDT['category']) {
	case '1' : $cFlag = '<li class="p_ca-B">동산</li>'; break;
	case '2' : $cFlag = ($PRDT['mortgage_guarantees']=='1') ? '<li class="p_ca-A2">주택담보대출</li>' : '<li class="p_ca-A">부동산</li>'; break;
	case '3' : $cFlag = '<li class="p_ca-C">확정매출채권</li>'; break;
	default  : $cFlag = ''; break;
}

$aiFlag  = ($PRDT['ai_grp_idx']>0) ? '<li class="p_ai">자동투자</li>' : '';
$newFlag = ($PRDT['new_flag']=='Y') ? '<li class="p_new">N</li>' : '';
$srmFlag = ($PRDT["stream_url1"] || $PRDT["stream_url2"]) ? '<li class="p_live_tv"><i class="fa fa-tv"></i> LIVE TV</li>' : '';
$adiFlag = ($PRDT['advance_invest']=='Y') ? '<li class="p_adir">사전투자 ' . floatRtrim($PRDT['advance_invest_ratio']).'% <i class="fa fa-question-circle" id="question_1"></i></li>' : '';
$pgFlag  = ($PRDT['purchase_guarantees']=='Y' && preg_match("/dev\.hello/", G5_URL)) ? '<li class="p_pg">채권매입계약</li>' : '';
$adpFlag = ($PRDT['advanced_payment']=='Y') ? '<li class="p_adpy">이자 선지급</li>' : '';

if($product_invest_percent < 100) {
	@header('Cache-Control: no-cache, no-store, must-revalidate');
	@header('Pragma: no-cache');
	@header('Expires: -1');
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<? if($product_invest_percent < 100) { ?>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<? } ?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">

<title>헬로펀딩, 대한민국 P2P 금융의 표준, P2P투자, P2P대출, 소액투자의 시작 헬로펀딩</title>

<link rel="stylesheet" type="text/css" href="/theme/2018/css/mobile.css?ver=20180724">
<link rel="stylesheet" type="text/css" href="/theme/2018/css/layout_m.css?ver=20180724">
<link rel="stylesheet" type="text/css" href="/theme/2018/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="/theme/2018/css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="/theme/2018/css/popup_mobile.css">
<link rel="stylesheet" type="text/css" href="/theme/2018/css/jquery.webui-popover.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/investment/css/investment_info_m.css?ver=20180724">

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/theme/2018/js/swiper.min.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<script type="text/javascript" src="/theme/2018/js/jquery.webui-popover.min.js"></script>
<script type="text/javascript" src="/theme/2018/js/iscroll.js"></script>
<script type="text/javascript" src="/theme/2018/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script type="text/javascript" src="/js/jquery.blink.js"></script>
<script type="text/javascript" src="/js/modernizr.custom.70111.js"></script> <!--overflow scroll 감지//-->

</head>
<body>


<style>
#container { margin-top:-50px; }

/*** 투자 시 유의사항 ***/
.invest_notice .faq2 {display:block;margin: 0 auto;font-family:'spoqahansans';}
.invest_notice .faq2 li {list-style:none; border-bottom:1px solid #cdd6e9;font-family:'spoqahansans';padding:0 7% 0 0;}
.invest_notice .faq2 li a {width:100%;display:block;overflow:hidden;font-size:24px;font-weight:200;font-family:'spoqahansans';color:#1e89ec;line-height:80px;border: none;}
.invest_notice .faq2 li a span {padding-left:15px;font-family:'spoqahansans';}
.invest_notice .faq2 li a span strong {font-size:24px;font-weight:200;font-family:'spoqahansans';color:#1e89ec;}
.invest_notice .faq2 li p {width:100%;padding:0 0 30px 40px;font-size:16px;font-weight:300;font-family:'spoqahansans';color:#000;display:block;line-height:28px;text-overflow:clip;}
.invest_notice .faq2 li p strong {font-size:24px;font-weight:200;font-family:'spoqahansans';color:#1e89ec;}
</style>

<!-- 본문내용 START -->
<div id="wrapper">
	<div id="container" class="container" style="margin-top:-50px">

		<input type="hidden" name="prd_idx" value="<?=$prd_idx;?>">
		<input type="hidden" name="invest_finished" id="invest_finished" value="<?=($invest_finished) ? 'true' : 'false'; ?>">

		<!-- 상품 슬라이드 시작 -->
		<div id="p_info">
			<div class="p_info_b" <? if($PRDT['title_image_url_m']) { ?> style="background:url('<?=$PRDT['title_image_url_m']?>') repeat center;" <? } ?>>
				<div class="p_info_bb">
					<div class="p_flags">
						<ul>
							<?=$cFlag?><?=$aiFlag?><?=$newFlag?><?=$srmFlag?><?=$adiFlag?><?=$pgFlag?><?=$adpFlag?>
						</ul>
					</div>

					<div class="p_tit"><?=$PRDT['title']?></div>
					<div class="p_date">모집기간 : <?=$print_sdate;?> ~</div>

					<div class="p_info_total">
						<div>
							<span>투자수익률(연)</span>
							<?=$invest_return?><b>%</b>
						</div>
						<div>
							<span>투자기간</span>
							<?=$invest_period?><b><?=$invest_period_unit?></b>
						</div>
						<div>
							<span>모집금액</span>
							<?=$print_recruit_amount?><b>원</b>
						</div>
					</div>

					<div class="process_wrap">
						<div class="process">
							<div class="process_tag">
								<div class="process_tag_c">
									<span>투자모집률 / 모집된 금액</span>
									<strong class="p_t_n" id="progressData"><?=$product_invest_percent?>%</strong> / <strong class="p_t_t" id="totalRecruitValue"><?=price_cutting($PRDT["total_invest_amount"]+0);?>원</strong>
								</div>
							</div>
							<div id="progressBar" class="process_bar" style="width:<?=(($product_invest_percent <= 100)?$product_invest_percent:100).'%';?>"></div>
						</div>
					</div>

					<?=($PRDT['product_summary']) ? $PRDT['product_summary'] : '';?>

				</div>
			</div>
		</div>

<?
		if(trim($PRDT['core_invest_point'])) {
			//echo $PRDT['core_invest_point'];

			$core_invest_point = $PRDT['core_invest_point'];

			// 이미지를 프래임 내부에서 호출되도록 하기 위한 작업
			$str = preg_replace("/<p>/i", "", $core_invest_point);
			$_ARR = explode("</p>", $str);
			for($i=0; $i<count($_ARR); $i++) {
				$target_string = trim( str_f6($_ARR[$i], "<a href=\"", "\" rel=\"noopener noreferrer\" target=\"_blank\">") );
				$target_string = preg_replace("/(\\r|\\n)/", "", $target_string);

				if($target_string) {
					$change_string = "/finnq/image/" . preg_replace("/\=/", "hello", base64_encode($target_string))."&".time()."\n";

					$arg0 = "/".preg_replace("/\//", "\/", $target_string)."/i";

					$core_invest_point = preg_replace($arg0, $change_string, $core_invest_point);
				}
			}
			//$core_invest_point = preg_replace("/_blank/i", "_self", $core_invest_point);
			$core_invest_point = preg_replace("/(href=\"#\"|href='#')/i", "href='javascript:;'", $core_invest_point);

			echo $core_invest_point."\n";


		}
?>

		<!-- 상품개요 -->
		<div class="product_info">
			<div class="product_info_tit">상품 개요</div>
			<div>
				<ul class="p_i_t">
					<li>
						<p>투자모집액</p>
						<p><?=price_cutting($PRDT['recruit_amount'])?>원</p>
					</li>
					<li>
						<p>투자수익률</p>
						<p>연 <?=$invest_return?>%</p>
					</li>
					<li>
						<p>투자기간</p>
						<p><?=$invest_period?><?=$invest_period_unit?></p>
					</li>
					<li>
						<p>상환방법</p>
						<p><?=$repay_pay_title?></p>
					</li>
				</ul>
				<div class="clearfix" style="display:block"></div>
				<div class="p_i_t_i">
<?
	if($PRDT['product_description']) { // 상품설명
		$description = nl2br($PRDT['product_description']);
		$description = preg_replace("/_blank/i", "_self", $description);
		$description = preg_replace("/(href=\"#\"|href='#')/i", "href='javascript:;'", $description);

		echo $description;
	}
?>
				</div>
			</div>
		</div>

		<!-- 안전장치 업데이트 -->
		<?
		if($PRDT['extend_8']) {

			$extend_8 = $PRDT['extend_8'];

			// 이미지를 프래임 내부에서 호출되도록 하기 위한 작업
			$str = preg_replace("/<p>/i", "", $extend_8);
			$_ARR = explode("</p>", $str);
			for($i=0; $i<count($_ARR); $i++) {
				$target_string = trim( str_f6($_ARR[$i], "<a class=\"fr-file\" href=\"", "\" target=\"_blank\">") );
				$target_string = preg_replace("/(\\r|\\n)/", "", $target_string);

				if($target_string) {
					$change_string = "/finnq/image/" . preg_replace("/\=/", "hello", base64_encode($target_string))."&".time()."\n";

					$arg0 = "/".preg_replace("/\//", "\/", $target_string)."/i";
					$extend_8 = preg_replace($arg0, $change_string, $extend_8);
				}
			}
			$extend_8 = preg_replace("/_blank/i", "_self", $extend_8);
			$extend_8 = preg_replace("/(href=\"#\"|href='#')/i", "href='javascript:;'", $extend_8);

			echo $extend_8;

		}

		?>

		<br>

		<div id="detail_box" class="detail_box" style="display:block">

<?
			if($PRDT['invest_summary_m']) {

				$invest_summary_m = $PRDT['invest_summary_m'];

				// 이미지를 프래임 내부에서 호출되도록 하기 위한 작업
				$str = preg_replace("/<p>/i", "", $invest_summary_m);
				$_ARR = explode("</p>", $str);
				for($i=0; $i<count($_ARR); $i++) {
					$target_string = trim( str_f6($_ARR[$i], "<a class=\"fr-file\" href=\"", "\" target=\"_blank\">") );
					$target_string = preg_replace("/(\\r|\\n)/", "", $target_string);

					if($target_string) {
						$change_string = "/finnq/image/" . preg_replace("/\=/", "hello", base64_encode($target_string))."&".time()."\n";

						$arg0 = "/".preg_replace("/\//", "\/", $target_string)."/i";
						$invest_summary_m = preg_replace($arg0, $change_string, $invest_summary_m);
					}
				}
				$invest_summary_m = preg_replace("/_blank/i", "_self", $invest_summary_m);
				$invest_summary_m = preg_replace("/(href=\"#\"|href='#')/i", "href='javascript:;'", $invest_summary_m);

				echo $invest_summary_m."\n";

			}


			//-- 증빙자료 ----------------------
			if($PRDT['extend_9']) {

				$extend_9 = $PRDT['extend_9'];

				// 이미지를 프래임 내부에서 호출되도록 하기 위한 작업
				$str = preg_replace("/<p>/i", "", $extend_9);
				$_ARR = explode("</p>", $str);
				for($i=0; $i<count($_ARR); $i++) {
					$target_string = trim( str_f6($_ARR[$i], "href=\"", "\" target=\"_blank\">") );
					$target_string = preg_replace("/(\\r|\\n)/", "", $target_string);

					if($target_string) {
						$change_string = "/finnq/image/" . preg_replace("/\=/", "hello", base64_encode($target_string))."&".time()."\n";

						$arg0 = "/".preg_replace("/\//", "\/", $target_string)."/i";
						$extend_9 = preg_replace($arg0, $change_string, $extend_9);
					}
				}

				$extend_9 = preg_replace("/_blank/i", "_self", $extend_9);
				$extend_9 = preg_replace("/(href=\"#\"|href='#')/i", "href='javascript:;'", $extend_9);

				echo $extend_9;

			}


			//-- 투자시 유의사항 ---------------
			if($PRDT['extend_7']) {

				// 링크 및 토글기능 제거
				$extend_7 = $PRDT['extend_7'];
				$extend_7 = preg_replace("/class=\"faq\"/i", "class=\"faq2\"", $extend_7);
				$extend_7 = preg_replace("/href=\"#\"/i", "", $extend_7);
				$extend_7 = preg_replace("/target=\"_blank\"/i", "", $extend_7);

				echo $extend_7;
			}
?>
		</div>

	</div>	<!-- id="wrapper" -->
</div>	<!-- id="container" -->

<script type="text/javascript">
// FAQ 토글
$(".faq a").on('click', function() {
	$(this).next().slideToggle("fast").parent().siblings().children("dd").hide();
	return false;
});
</script>

<?
if($prd_idx == '119') {
	include_once(G5_PATH.'/popup/inc_product_119_notice.php');
}
?>

</body>
</html>