<?

//ini_set('zlib.output_compression_level', 3);
//ob_start("ob_gzhandler");
ob_start();

// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$begin_time = get_microtime();

if( !isset($g5['title']) ) {
	$g5['title']   = $config['cf_title'];
	$g5_head_title = $g5['title'];
}
else {
	$g5_head_title = $g5['title']; // 상태바에 표시될 제목
	$g5_head_title.= " | ".$config['cf_title'];
}


if($_SERVER['PHP_SELF'] != $_SERVER['SCRIPT_NAME']) {
	$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
}
if($_SERVER['QUERY_STRING'] == '') {
	if($_SERVER['REQUEST_URI'] != $_SERVER['SCRIPT_NAME']) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	}
}


// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if(!$g5['lo_location']) { $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI'])); }
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if(strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

$head_title = "헬로펀딩 | 대한민국 P2P금융의 표준";

$conversion_request_uri = ($_SERVER['QUERY_STRING']) ? htmlSpecialChars(addslashes(clean_xss_tags($_SERVER['REQUEST_URI']))) : addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));


// 페이징 캐시처리
include_once(G5_LIB_PATH.'/Cache/Lite/Output.php');

// 페이지 캐싱
$cacheOption = array(
	'caching' => true,
	'liteTime' => 900,
	'cacheDir' => G5_DATA_PATH."/cache/pages/",
	'automaticCleaningFactor' => 900,
	'hashedDirectoryLevel' => 0
);

if(!is_dir($cacheOption['cacheDir'])) mkdir($cacheOption['cacheDir'], 0755, true);

$cacheId = (G5_IS_MOBILE) ? $_SERVER['REQUEST_URI']."_m" : $_SERVER['REQUEST_URI'];

$cacheLite = new Cache_Lite_Output($cacheOption);
if($html = $cacheLite->get($cacheId)) {
	//echo $html; exit;
}
else {
	//$cacheLite->start($cacheId);
}


/////////////////////////////////////////
// 메뉴 배열화 : 2020-12-17 추가
/////////////////////////////////////////
$res  = sql_query("SELECT me_id, me_code, me_name, me_link, me_target FROM g5_menu WHERE me_mobile_use = '1' AND LENGTH(me_code) = '2' ORDER BY me_code");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$ME[$i] = sql_fetch_array($res);

	$res2  = sql_query("SELECT me_id, me_code, me_name, me_link, me_target FROM g5_menu WHERE me_mobile_use = '1' AND LENGTH(me_code) = '4' AND LEFT(me_code, 2) = '".$ME[$i]['me_code']."' ORDER BY me_code");
	$rows2 = sql_num_rows($res2);
	for($k=0; $k<$rows2; $k++) {
		$ME[$i]['LIST'][$k] = sql_fetch_array($res2);
	}
}
//print_rr($LIST, 'font-size:12px;');

/////////////////////////////////////////
// 메뉴 배열화(구)
/////////////////////////////////////////
$menu_1 = array();
$menu_2 = array();
$sql = "SELECT * FROM g5_menu WHERE me_use = '1' ORDER BY me_code";
//$sql = "SELECT * FROM ".$g5['menu_table']." WHERE me_use = '1' ORDER BY me_order, me_id";
$result = sql_query($sql, false);
$gnbZindex = 999; // gnb_1dli z-index 값 설정용

if(count($result) > 0) {
	for($i = 0; $row = sql_fetch_array($result); $i++) {
		$me_code = substr($row['me_code'], 0, 2);
		if(strlen($row['me_code']) > 2) {
			$menu_2[$me_code][$i] = $row;
		}
		else{
			$menu_1[$me_code] = $row;
		}
	}
}

/*
// 특별 투자자 자격 갱신 안내 페이지로 이동
if(preg_match("/^\/index.php/", $_SERVER['PHP_SELF'])) {
	if( $member['member_investor_type']>'1' && $member['special_investor']['valid_days'] <= 30 ) {
		if(get_cookie("emergency_notice_view")=='') header("Location: " . G5_URL."/emergency_notice.php");
	}
}
*/

?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
<?
	if(G5_IS_MOBILE) {
		echo '	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes" />'.PHP_EOL;
		echo '	<meta name="HandheldFriendly" content="true" />'.PHP_EOL;
		echo '	<meta name="format-detection" content="telephone=no" />'.PHP_EOL;
		echo '	<meta name="theme-color" content="#222" />'.PHP_EOL;
	}
	else {
		echo '	<meta http-equiv="X-UA-Compatible" content="IE=edge" />'.PHP_EOL;
	//echo '	<meta http-equiv="imagetoolbar" content="no" />'.PHP_EOL;
	}

	if($config['cf_add_meta']) echo $config['cf_add_meta'].PHP_EOL;


	$OG['title']       = "헬로펀딩 | 기술과 금융으로 만드는 보다 나은 세상";
	$OG['description'] = "온라인투자연계금융플랫폼(P2P), 투자자 신뢰도 1위, 상환율 1위 헬로펀딩은 투자자에게는 안정적인 수익을, 대출자에게는 합리적인 대출금리를 제공합니다.";
	$OG['image']       = G5_URL . "/images/meta-hello-red.png";
	$OG['url']         = G5_URL;

	// 상품상세보기 페이지에서 og:title, og:description, og:image 태그 변경
	if( preg_match("/(\/investment\/invest\_list\.php|\/investment\/investment\.php)/i", $_SERVER['SCRIPT_FILENAME']) ) {
		$OG['title']       = "투자상품보기 | 헬로펀딩";
	//$OG['description'] = "";
		$OG['image']       = G5_URL . "/images/meta-invest-03.png";
		$OG['url']         = G5_URL . $_SERVER['REQUEST_URI'];
	}

?>
	<meta name="title" content="<?=$head_title?>" />
	<meta name="subject" content="헬로펀딩 | 대한민국 P2P금융의 표준" />
	<meta name="description" content="온라인투자연계금융플랫폼(P2P), 투자자 신뢰도 1위, 상환율 1위 헬로펀딩은 투자자에게는 안정적인 수익을, 대출자에게는 합리적인 대출금리를 제공합니다." />
	<meta name="keywords" content="헬로펀딩,크라우드펀딩,부동산크라우드펀딩,동산크라우드펀딩,P2P투자,P2P대출,P2P금융,온투업,온라인투자연계금융,담보투자,부동산투자,재테크,소액투자,직장인투자,간편투자,모바일간편투자" />
	<meta name="robots" content="ALL" />
	<meta name="naver-site-verification" content="115879cf0ae96194f27ce1b9ea0db9db7d0dc955" />

	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?=$OG['title']?>" />
	<meta property="og:description" content="<?=$OG['description']?>" />
	<meta property="og:image" content="<?=$OG['image']?>" />
	<meta property="og:url" content="<?=$OG['url'] ?>" />

	<title>헬로펀딩, 온라인투자연계금융 헬로핀테크, P2P금융의 표준, P2P투자, P2P 대출</title>

	<link rel="shortcut icon" type="image/x-icon" href="<?=G5_URL?>/favicon.ico?ver=<?=date('ymd')?>" />
<?
if(!preg_match("#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer|mg)|l(?:ayer|ink)|meta|object|s(?:cript|tyle|rc)|title|xml)[^>]*+>#i", $conversion_request_uri)) {
	//echo "	<link rel=\"canonical\" href=\"".G5_URL.$conversion_request_uri."\" />\n";
}

// 메인용 스타일 규정
if(defined('_INDEX_')) {
	$layout_css = (G5_IS_MOBILE) ? "layout_main_m.css" : "layout_main.css";
}
else {
	$layout_css = (G5_IS_MOBILE) ? "layout_m.css" : "layout.css";
}

$swiper_src_tag = "<script src=\"//cdnjs.cloudflare.com/ajax/libs/Swiper/6.4.1/swiper-bundle.min.js\" integrity=\"sha512-MzZOTeyE0I70H6Pc2DcdhuPcGAeoGeT7yifrbR/cx92UTT7lG+OLXu70kPtJzqPrnmOjfmUHzklwT0GOqrYcZw==\" crossorigin=\"anonymous\"></script>";
//if(preg_match("/Windows NT/i", $_SERVER['HTTP_USER_AGENT'])) { $swiper_src_tag = "<script src=\"/theme/2018/js/swiper.min.js\"></script>"; }
?>
	<link rel="stylesheet" href="/theme/2018/css/default<?=(G5_IS_MOBILE)?'_m':''?>.css?ver=<?=date("YmdH");?>" />
	<link rel="stylesheet" href="/theme/2018/css/<?=$layout_css?>?ver=<?=date("YmdHi");?>">
	<link rel="stylesheet" href="/theme/2018/js/jquery-ui-1.12.1/jquery-ui.min.css?ver=<?=date("YmdH");?>" />
	<link rel="stylesheet" href="/theme/2018/css/popup<?=(G5_IS_MOBILE)?'_mobile':'';?>.css?ver=<?=date("YmdH")?>" />
	<link rel="stylesheet" href="/theme/2018/css/jquery.webui-popover.css" />
	<link rel="stylesheet" href="/theme/2018/css/swiper.min.css" />
<? if(false) { ?><!--link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" /--><? echo "\n"; } ?>
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/font-kopub@1.0/kopubdotum.min.css">
<?
// integrity 생성 예제 (KISA 취약점보고서에 올라와서 작업함. 생성 /etc/make_sri_hash.php 참조)
// # openssl dgst -sha384 -binary js파일경로 | openssl base64 -A
?>

	<!--script src="/common_variables.js"></script-->
	<script>
	var g5_url       = "<?=G5_URL?>";
	var g5_bbs_url   = "<?=G5_BBS_URL?>";
	var g5_is_member = "<?=isset($is_member) ? $is_member : '';?>";
	var g5_is_admin  = "<?=isset($is_admin) ? $is_admin : '';?>";
	var g5_is_mobile = "<?=G5_IS_MOBILE?>";
	var g5_bo_table  = "<?=isset($bo_table) ? $bo_table : '';?>";
	var g5_sca       = "<?=isset($sca) ? $sca : '';?>";
	var g5_editor    = "<?=($config['cf_editor'] && $board['bo_use_dhtml_editor']) ? $config['cf_editor'] : '';?>";
	var g5_cookie_domain = "<?=G5_COOKIE_DOMAIN?>";
	<? if($is_admin) { echo "var g5_admin_url = \"".G5_ADMIN_URL."\";\n"; } ?>
	</script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha384-rY/jv8mMhqDabXSo+UCggqKtdmBfd3qC2/KvyTDNQ6PcUJXaxK1tMepoQda4g5vB" crossorigin="anonymous"></script>
	<script src="/theme/2018/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
	<script src="/js/jquery.blockUI.js"></script>
	<script src="/js/jquery.menu.js"></script>
	<script src="/js/common.js?ver=<?=date("YmdH");?>"></script>
	<script src="/js/wrest.js"></script>
	<script src="/theme/2018/js/jquery.webui-popover.min.js"></script>
	<script src="/theme/2018/js/iscroll.js"></script>
	<script src="/js/jquery.validation-1.19.0/dist/jquery.validate.min.js"></script>
	<?=$swiper_src_tag."\n";?>
	<? if(G5_IS_MOBILE) { ?><script src="/js/modernizr.custom.70111.js"></script><? } echo "\n"; /* overflow scroll 감지 */ ?>
	<? if( $_SERVER['REQUEST_SCHEME']=='http' && defined('_INDEX_') ) {?><script src="//cdn.jsdelivr.net/npm/hls.js@latest" integrity="sha384-88XISbHEfmll13ZKalTTbq24KBW7RhSwRkyARjGw/rhGva98X1gWj0AxFRz131io" crossorigin="anonymous"></script><? echo "\n"; } ?>

	<? if($prd_idx > 243) { ?><script src="/theme/2018/js/jquery.dimensions.js"></script><? echo "\n"; /* v2 상품상세보기페이지에서 load 금지 */ } ?>

	<script>
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

<?
if(false) {
	if(G5_IS_MOBILE) {
		if($CONF['flatform']!='app') {
			$lazy_effect = "{ effect: 'fadeIn', effectTime: 600, threshold: 0 }";
		}

		add_javascript("<script src='/js/jquery.lazy-master/jquery.lazy.min.js'></script>", 0);
		add_javascript("<script src='/js/jquery.lazy-master/jquery.lazy.plugins.min.js'></script>", 0)
?>
	<script>
	$(document).ready(function() {
		$("img.lazy").lazy(<?=$lazy_effect?>);
	});
	</script>
<?
	}
}
?>

<?=G5_POSTCODE_JS; /* 다음 주소 js 호출 */ ?>

	<script src="//t1.daumcdn.net/adfit/static/kp.js" type="text/javascript" charset="UTF-8"></script><!-- 다음전환통계용 //-->

</head>

<body>

<? if(!G5_IS_MOBILE) { ?><a href="#container" id="skip_navigation">본문 바로가기</a><? } ?>
