<!doctype html>
<html lang="ko">
<head>
    <title>헬로펀딩, 대한민국 P2P 금융의 표준, 헬로핀테크, P2P투자, P2P대출, 소액투자의 시작 헬로펀딩</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="title" content="헬로펀딩 | 대한민국 P2P금융의 표준" />
	<meta name="subject" content="헬로펀딩 | 대한민국 P2P금융의 표준" />
	<meta name="description" content="투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼. 새로운 투자채널. 합리적인 대출금리." />
	<meta name="keywords" content="헬로펀딩,크라우드펀딩,부동산크라우드펀딩,동산크라우드펀딩,P2P투자,P2P대출" />
	<meta name="writer" content="(주)헬로핀테크" />
	<meta name="author" content="(주)헬로핀테크" />
	<meta name="copyright" content="(주)헬로핀테크" />
	<meta name="robots" content="ALL" />
	<meta name="naver-site-verification" content="115879cf0ae96194f27ce1b9ea0db9db7d0dc955" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="헬로펀딩 | 대한민국 P2P금융의 표준. 헬로핀테크" />
	<meta property="og:description" content="헬로펀딩은 (주)헬로핀테크에서 운영하는, 투자자와 대출자를 직접 연결시켜주는 P2P 금융플랫폼으로 투자자에게는 새로운 투자채널을, 대출자에게는 합리적인 대출금리를 제공합니다." />
	<meta property="og:image" content="https://www.hellofunding.co.kr/favicon.ico?ver=20180826" />
	<meta property="og:url" content="https://www.hellofunding.co.kr" />

	<link rel="stylesheet" href="<?php ECHO HelloContentLink;?>/css/jquery.bxslider.css">
	<link type="text/css" rel="stylesheet" href="<?php ECHO HelloContentLink;?>/css/main.css?ver=<?php ECHO time()?>" />
	<link rel="stylesheet" type="text/css" href="<?php ECHO HelloContentLink;?>/css/component.css?ver=<?php ECHO time()?>" /> <!-- modal(popup) css -->

	<? IF($kind == "99") { // account ?>
	<link type="text/css" rel="stylesheet" href="<?php ECHO HelloContentLink;?>/css/sub.css?ver=<?php ECHO time()?>" />
	<link type="text/css" rel="stylesheet" href="<?php ECHO HelloContentLink;?>/css/account.css?ver=<?php ECHO time()?>" />
    <link rel="stylesheet" type="text/css" href="<?php ECHO HelloContentLink;?>/css/component.css?ver=<?php ECHO time()?>" />
	<link type="text/css" rel="stylesheet" href="<?php ECHO HelloContentLink;?>/css/sub.css?ver=<?php ECHO time()?>" />
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<?php } ?>

	<meta name="format-detection" content="telephone=no">

	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="/m/js/common.js?ver=<?php ECHO time();?>"></script>

	<script src="<?php ECHO HelloContentLink;?>/js/jquery.bxslider.min.js"></script>
	<script>
		$(document).ready(function(){
			$('.payment_slider').bxSlider({
				pager: false
			});
		});
	</script>
</head>
<body>
	<!-- ### MainMenu Start -->
	<div id="MainMenu">
		<ul>
			<li<?php ECHO $gstrTopMenuActive1;?>><a href="<?php ECHO HelloMenu1;?>">나의현황</a></li>
			<li<?php ECHO $gstrTopMenuActive2;?>><a href="<?php ECHO HelloMenu2;?>">투자상품</a></li>
			<li<?php ECHO $gstrTopMenuActive3;?>><a href="<?php ECHO HelloMenu3;?>">리뷰&이벤트</a></li>
		</ul>
	</div>