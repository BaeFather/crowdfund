<?
###############################################################################
## 회사소개
###############################################################################

include_once('./_common.php');
include_once(G5_LIB_PATH.'/nujuk_state.lib.php');
include_once(G5_LIB_PATH.'/review.lib.php');




while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }

$g5['title'] = '회사소개';
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "당신의 설레는 내일, 헬로펀딩";


// 헬로펀딩 투자현황 (통계데이터)
$NUJUK_CACHE = getNujukState();
$NUJUK_STATUS['totalProductCount']  = number_format($NUJUK_CACHE['totalProductCount']).'건';
$NUJUK_STATUS['investAmount']       = price_cutting($NUJUK_CACHE['investAmount']).'원';				// 누적대출액
$NUJUK_STATUS['repayPrincipal']     = price_cutting($NUJUK_CACHE['repayPrincipal']).'원';			// 누적상환액
$NUJUK_STATUS['investIngAmount']    = price_cutting($NUJUK_CACHE['investIngAmount']).'원';		// 대출잔액
$NUJUK_STATUS['averageReturn']      = floatRtrim($NUJUK_CACHE['averageReturn']).'%';					// 평균수익률(연)
$NUJUK_STATUS['investSuccessCount'] = $NUJUK_CACHE['investSuccessCount'];											// 투자 성공건수
$NUJUK_STATUS['overduePerc']        = floatRtrim($NUJUK_CACHE['overduePerc']).'%';						// 연체율
$NUJUK_STATUS['bankruptcy']         = floatRtrim($NUJUK_CACHE['bankruptcy']).'%';							// 부실율
$NUJUK_STATUS['averageInvMonth']    = sprintf('%.1f', $NUJUK_CACHE['averageInvMonth']).'개월';		// 평균투자기간
$NUJUK_STATUS['averageInvAmount']   = price_cutting($NUJUK_CACHE['averageInvAmount']).'원';		// 평균투자금액

include ("sawon_array.php");
shuffle($sawon);


// 스킨설정
if (G5_IS_MOBILE) {
	$company_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/company/'.$match[1];
	$company_skin_url = str_replace(G5_PATH, G5_URL, $company_skin_path);
}
else {
	$company_skin_path = G5_THEME_PATH . '/' . G5_SKIN_DIR . '/company/' . $match[1];
	$company_skin_url = str_replace(G5_PATH, G5_URL, $company_skin_path);
}


include_once('./_head.php');

include_once($company_skin_path.'/company.skin.php');

include_once('./_tail.php');

?>