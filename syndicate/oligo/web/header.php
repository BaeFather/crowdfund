<?
$conversion_request_uri = ($_SERVER['QUERY_STRING']) ? htmlSpecialChars(addslashes(clean_xss_tags($_SERVER['REQUEST_URI']))) : addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="viewport" content="target-densitydpi=device-dpi, user-scalable=0, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, width=device-width" />
<?
	if(G5_IS_MOBILE) {
		echo '	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes" />'.PHP_EOL;
		echo '	<meta name="HandheldFriendly" content="true" />'.PHP_EOL;
		echo '	<meta name="format-detection" content="telephone=no" />'.PHP_EOL;
		echo '	<meta name="theme-color" content="#073190" />'.PHP_EOL;
	}
	else {
		echo '	<meta http-equiv="X-UA-Compatible" content="IE=edge" />'.PHP_EOL;
		echo '	<meta http-equiv="imagetoolbar" content="no" />'.PHP_EOL;
	}
?>
	<meta name="title" content="<?=$head_title?>" />
	<meta name="subject" content="헬로펀딩 | 대한민국 P2P금융의 표준" />
	<meta name="description" content="투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼. 새로운 투자채널. 합리적인 대출금리." />
	<meta name="keywords" content="헬로펀딩,크라우드펀딩,부동산크라우드펀딩,동산크라우드펀딩,P2P투자,P2P대출" />
	<meta name="writer" content="(주)헬로핀테크" />
	<meta name="author" content="(주)헬로핀테크" />
	<meta name="copyright" content="(주)헬로핀테크" />
	<meta name="robots" content="ALL" />
	<title>헬로펀딩 | 대한민국 P2P금융의 표준</title>

	<link rel="shortcut icon" type="image/x-icon" href="<?=G5_URL?>/favicon.ico?ver=20180826" />
<? if(!preg_match("#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer|mg)|l(?:ayer|ink)|meta|object|s(?:cript|tyle|rc)|title|xml)[^>]*+>#i", $conversion_request_uri)) { /* ?>
	<link rel="canonical" href="<?=G5_URL.$conversion_request_uri?>" />
<? */ } ?>
	<link rel="stylesheet" href="/theme/2018/css/<?=(G5_IS_MOBILE) ? 'mobile.css?ver=20190103' : 'default.css'; ?>" />
	<link rel="stylesheet" href="/theme/2018/css/layout<?=(G5_IS_MOBILE) ? '_m' : '';?>.css?ver=20190528" />
	<link rel="stylesheet" href="/theme/2018/js/jquery-ui-1.12.1/jquery-ui.min.css" />
	<link rel="stylesheet" href="/theme/2018/css/swiper.min.css" />
	<link rel="stylesheet" href="/theme/2018/css/popup<?=(G5_IS_MOBILE)?'_mobile':'';?>.css?ver=20190306c" />
	<link rel="stylesheet" href="/theme/2018/css/jquery.webui-popover.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" />

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha384-rY/jv8mMhqDabXSo+UCggqKtdmBfd3qC2/KvyTDNQ6PcUJXaxK1tMepoQda4g5vB" crossorigin="anonymous"></script>
	<script src="/theme/2018/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
	<script src="/theme/2018/js/jquery.dimensions.js"></script>
	<script src="/theme/2018/js/swiper.min.js"></script>
	<script src="/js/jquery.blockUI.js"></script>
	<script src="/js/jquery.menu.js"></script>
	<script src="/js/common.js?ver=20190218"></script>
	<script src="/js/wrest.js"></script>
	<script src="/theme/2018/js/jquery.webui-popover.min.js"></script>
	<script src="/theme/2018/js/iscroll.js"></script>
	<script src="/js/jquery.validation-1.19.0/dist/jquery.validate.min.js"></script>
	<? if(G5_IS_MOBILE) { ?><script src="/js/modernizr.custom.70111.js"></script><? } echo "\n"; /* overflow scroll 감지 */ ?>
	<script src="/js/jquery.blink.js"></script>
	<script>
	var g5_url       = "<?=G5_URL?>";
	var g5_bbs_url   = "<?=G5_BBS_URL?>";
	var g5_is_member = "<?=isset($is_member)?$is_member:'';?>";
	var g5_is_admin  = "<?=isset($is_admin)?$is_admin:'';?>";
	var g5_is_mobile = "<?=G5_IS_MOBILE?>";
	var g5_bo_table  = "<?=isset($bo_table)?$bo_table:'';?>";
	var g5_sca       = "<?=isset($sca)?$sca:'';?>";
	var g5_editor    = "<?=($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:'';?>";
	var g5_cookie_domain = "<?=G5_COOKIE_DOMAIN?>";

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
	<script>
	$(document).ready(function() {
		$("body").delay(300).animate({ opacity: 1 }, 500);
		$(".quick_guide_area").delay(1000).animate({ opacity: 1 }, 500);
	});
	</script>
<? } ?>

</head>

<body>

<div class="container">
