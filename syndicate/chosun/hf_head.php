<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
<?
if (G5_IS_MOBILE) {
	echo '	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
	echo '	<meta name="HandheldFriendly" content="true">'.PHP_EOL;
	echo '	<meta name="format-detection" content="telephone=no">'.PHP_EOL;
}
else {
	echo '	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">'.PHP_EOL;
	echo '	<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
}
?>
	<meta name="title" content="헬로펀딩 | 대한민국 P2P금융의 표준">
	<meta name="subject" content="헬로펀딩 | 대한민국 P2P금융의 표준">
	<meta name="description" content="투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼. 새로운 투자채널. 합리적인 대출금리.">
	<meta name="keywords" content="헬로펀딩,크라우드펀딩,부동산크라우드펀딩,동산크라우드펀딩,P2P투자,P2P대출">
	<meta name="writer" content="(주)헬로핀테크">
	<meta name="author" content="(주)헬로핀테크">
	<meta name="copyright" content="(주)헬로핀테크">
	<meta name="robots" content="ALL">
	<meta name="naver-site-verification" content="115879cf0ae96194f27ce1b9ea0db9db7d0dc955">
	<meta property="og:type" content="website">
	<meta property="og:title" content="헬로펀딩 | 대한민국 P2P금융의 표준">
	<meta property="og:description" content="헬로펀딩은 투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼으로 투자자에게는 새로운 투자채널을, 대출자에게는 합리적인 대출금리를 제공합니다.">
	<meta property="og:image" content="<?=HF_URL?>/images/CI64.png">
	<meta property="og:url" content="<?=HF_URL?>">

	<link rel="shortcut icon" type="image/x-icon" href="/img2/favicon.ico">
	<title>헬로펀딩, 대한민국 P2P 금융의 표준, P2P투자, P2P대출, 소액투자의 시작 헬로펀딩</title>

	


	<link rel="stylesheet" type="text/css" href="/css/<?=(G5_IS_MOBILE?'default_m':'default')?>.css">
	<link rel="stylesheet" type="text/css" href="/css/<?=(G5_IS_MOBILE?'investment_info_m':'investment_info')?>.css">
	<link rel="stylesheet" type="text/css" href="/css/layout<?=(G5_IS_MOBILE) ? '_m' : '';?>.css?ver=20180917">
	<link rel="stylesheet" type="text/css" href="/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="/css/popup<?=(G5_IS_MOBILE)?'_mobile':'';?>.css">
	<link rel="stylesheet" type="text/css" href="/css/jquery.webui-popover.css">
	
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<!--<script type="text/javascript" src="<?=G5_THEME_JS_URL?>/jquery.dimensions.js"></script>-->
	<script type="text/javascript" src="/js/swiper.min.js"></script>
	<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="/js/jquery.menu.js"></script>
	<script type="text/javascript" src="/js/common.js"></script>
	<script type="text/javascript" src="/js/wrest.js"></script>
	<script type="text/javascript" src="/js/jquery.webui-popover.min.js"></script>
	<script type="text/javascript" src="/js/iscroll.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/npm/hls.js@latest"></script>

	<script type="text/javascript" src="http://realty.chosun.com/realty/resources/js/realty_common.js"></script>
	<script type="text/javascript" src="http://realty.chosun.com/realty/resources/js/swiper.js"></script>
	

	<? if(G5_IS_MOBILE) { ?><script type="text/javascript" src="/js/modernizr.custom.70111.js"></script><!--overflow scroll 감지//--><? } ?>

	<? if(preg_match("/\/index.php/is", $_SERVER['PHP_SELF'])) {?>
	<script type="text/javascript" src="<?=($_SERVER['REQUEST_SCHEME']=='https')?"https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js":"http://dmaps.daum.net/map_js_init/postcode.v2.js";?>"></script>
	<? } ?>

	<script type="text/javascript">
		var g5_url       = "<?=HF_URL?>";
		var g5_bbs_url   = "<?=G5_BBS_URL?>";
		var g5_is_member = "<?=isset($is_member)?$is_member:'';?>";
		var g5_is_admin  = "<?=isset($is_admin)?$is_admin:'';?>";
		var g5_is_mobile = "<?=G5_IS_MOBILE?>";
		var g5_bo_table  = "<?=isset($bo_table)?$bo_table:'';?>";
		var g5_sca       = "<?=isset($sca)?$sca:'';?>";
		var g5_editor    = "<?=($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:'';?>";
		var g5_cookie_domain = "<?=G5_COOKIE_DOMAIN?>";
		<? if($is_admin) { ?>var g5_admin_url = "<?=G5_ADMIN_URL?>";<? } ?>

		$(document).ready(function(){
			$.validator.setDefaults({
				onkeyup: false
				, onclick: false
				, onfocusout: false
				, showErrors: function(errorMap, errorList) {
					if(errorList.length < 1)
						return;
					alert(errorList[0].message);
				}
			});
		});
	</script>

	<? if(!G5_IS_MOBILE) { ?>
		<script type="text/javascript">
			$(document).ready(function() {
				$("body").delay(300).animate({ opacity: 1 }, 500);
				$(".quick_guide_area").delay(1000).animate({ opacity: 1 }, 500);
			});
		</script>
	<? } ?>

	<script type="text/javascript">
	// 부동산 소액투자 카테고리 id 값 노출 
	var CatID = "8";
	</script>

</head>
<body>
<!-- REAL SERVER -->
<?

if(defined('_INDEX_')) { // index에서만 실행
	include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
}

if($is_member) {

	if($member['mb_id']=='fintech01') $special_print_name = "<span style='color:#153FA1;'>NH투자증권<br><span style='font-size:12px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech02') $special_print_name = "<span style='color:#153FA1;'>NH투자증권<br><span style='font-size:12px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제2호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech03') $special_print_name = "<span style='color:#153FA1;'>NH투자증권<br><span style='font-size:12px'>(피델리스 대신 P2P 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech04') $special_print_name = "<span style='color:#153FA1;'>피델리스 P2P 전문투자형<br>사모투자신탁 제1호</span>";
	if($member['mb_id']=='fintech05') $special_print_name = "<span style='color:#153FA1;'>피델리스 핀테크인컴 전문투자형<br>사모투자신탁 제1호</span>";

	if($member['member_type']=='1') {
		$print_mb_name = "<a>".$member["mb_name"]."</a>";
		$invest_possible_amount = (in_array($member['member_investor_type'], array('1','2'))) ? price_cutting($member['invest_possible_amount'])."원" : "제한 없음";
		$invest_possible_amount_prpt = (in_array($member['member_investor_type'], array('1'))) ? price_cutting($member['invest_possible_amount_prpt'])."원" : "제한 없음";
	} else {
		$print_mb_name = "<a>".$member["mb_co_name"]."</a>";
		$invest_possible_amount = "제한 없음";
		$invest_possible_amount_prpt = "제한 없음";
	}

	if($member['bank_code'] && $member['account_num'] && $member['va_bank_code2'] && $member['virtual_account2']) $bank_ok = true;
}

// 메뉴 조회
$menu_1 = array();
$menu_2 = array();
$sql = "SELECT * FROM {$g5["menu_table"]}
		WHERE me_use = '1'
		ORDER BY me_order, me_id";
$result = sql_query($sql, false);
$gnbZindex = 999; // gnb_1dli z-index 값 설정용

if(count($result) > 0){
	for ($i = 0; $row = sql_fetch_array($result); $i++) {
		$me_code = substr($row["me_code"], 0, 2);
		if(strlen($row["me_code"]) > 2){
			$menu_2[$me_code][$i] = $row;
		}else{
			$menu_1[$me_code] = $row;
		}
	}
}


?>

<header role="navigation">

</header>

<div class="container">

	<script src="http://realty.chosun.com/realty/resources/js/header_pkg.js?v_2017" charset="UTF-8"></script>

	<div class="cho">
<?
if (G5_IS_MOBILE) {
	?>
		<section style="padding:5px 15px 0 0; color:#999999 ; font-size:12px ; text-align:center ; word-break:keep-all; ">
			<div>
			※본 페이지는 땅집고와 제휴된 <span>헬로핀딩</span>에서 제공하는 서비스입니다.
			</div>
		</section>
	<?
} else {
	?>
		<section style="padding:5px 15px 0 0; color:#999999 ; font-size:14px ; text-align:right ; word-break:keep-all; ">
			<div>
			※본 페이지는 땅집고와 제휴된 <span>헬로핀딩</span>에서 제공하는 서비스입니다.
			</div>
		</section>
	<?
}
?>
<?

if (G5_IS_MOBILE) {
	include "left_menu_m.php";
} else {
	include "left_menu.php";
}

?>

		<script type="text/javascript">
			// 부동산 상품 설명레이어
			$('#d_flag_btn, #d_flag_close').on('click', function() { $('#d_flag').fadeToggle('slow'); });

			// 사용자 레이어
			$('#name_zone, #invest_close').on('click', function(e) {
				$('#invest_zone').stop().fadeToggle('slow');
			});
			// 레이어 닫기
			$(document).mouseup(function(e) {
				var my_layer = $("#invest_zone");
				if(e.target.className =="invest_zone"){return false;}
				if (my_layer.css("display") == "block") {
					if (!my_layer.is(e.target) && my_layer.has(e.target).length === 0 && e.target.className != "invest_zone") {
						my_layer.hide();
					}
				}
				e.preventDefault();
			});
			$(window).scroll(function(){
				var sticky = $('.header_menu'),
					scroll = $(window).scrollTop();

				if (scroll >= 10) sticky.addClass('fixed');
				else sticky.removeClass('fixed');
			});
		</script>
