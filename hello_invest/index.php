<?php
///////////////////////////////////////////////////////////////////////////////
// 기간을 설정하지 않은 이벤트(키워드광고등...) 랜딩페이지
///////////////////////////////////////////////////////////////////////////////

include_once("_common.php");
include_once(G5_PATH . '/pid_check.inc.php');		// pid 체크


$join_url = "/member/join_info.php?tab=p";


include_once(G5_LIB_PATH.'/nujuk_state.lib.php');

// 헬로펀딩 투자현황 (통계데이터)
$NUJUK_CACHE = getNujukState();
$NUJUK_STATUS['investAmount']   = price_cutting($NUJUK_CACHE["investAmount"]);				// 누적대출액
$NUJUK_STATUS['repayPrincipal'] = price_cutting($NUJUK_CACHE["repayPrincipal"]);			// 누적상환액
$NUJUK_STATUS['overduePerc']    = floatRtrim($NUJUK_CACHE["overduePerc"]);						// 연체율

$strIncludeKind = true;
if(G5_IS_MOBILE) {
  $strInCludeUrl = "./main.m.php";
}
else {
  $strInCludeUrl = "./main.php";
}

include_once($strInCludeUrl);


///////////////////////////////////////////////////////////////////////////
// [외부 통계측정 스크립트]
//   디버깅 항목이 발생하여 2020-11-30 head.sub.php 에서 tail.sub.php 로 이전함.
///////////////////////////////////////////////////////////////////////////
?>
<script src="//wcs.naver.net/wcslog.js"></script> <!-- NAVER PRIMIEUMLOG LIBRARY -->
<!-- Naver Primieum log //-->
<script type="text/javascript">
if(!wcs_add) var wcs_add={};
wcs_add["wa"] = "s_3fccd9ab394f";
if(!_nasa) var _nasa={};
wcs.inflow();
wcs_do(_nasa);
</script>
<!-- Naver analytics //-->
<script type="text/javascript">
if(!wcs_add) var wcs_add = {};
wcs_add["wa"] = "e1cd2274b7f94";
wcs_do();
</script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script type="text/javascript" async src="//www.googletagmanager.com/gtag/js?id=UA-96465943-1"></script>
<script type="text/javascript">
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'UA-96465943-1');	// 애널리틱스용 ID
gtag('config', 'AW-869613409');		// 애드워즈용 ID
</script>

<!-- AceCounter Log Gathering Script V.8.0.2019080601 -->
<script language='javascript'>
	var _AceGID=(function(){var Inf=['gtp3.acecounter.com','8080','BH2A44011879482','AW','0','NaPm,Ncisy','ALL','0']; var _CI=(!_AceGID)?[]:_AceGID.val;var _N=0;var _T=new Image(0,0);if(_CI.join('.').indexOf(Inf[3])<0){ _T.src ="https://"+Inf[0]+'/?cookie'; _CI.push(Inf);  _N=_CI.length; } return {o: _N,val:_CI}; })();
	var _AceCounter=(function(){var G=_AceGID;var _sc=document.createElement('script');var _sm=document.getElementsByTagName('script')[0];if(G.o!=0){var _A=G.val[G.o-1];var _G=(_A[0]).substr(0,_A[0].indexOf('.'));var _C=(_A[7]!='0')?(_A[2]):_A[3];var _U=(_A[5]).replace(/\,/g,'_');_sc.src='https:'+'//cr.acecounter.com/Web/AceCounter_'+_C+'.js?gc='+_A[2]+'&py='+_A[4]+'&gd='+_G+'&gp='+_A[1]+'&up='+_U+'&rd='+(new Date().getTime());_sm.parentNode.insertBefore(_sc,_sm);return _sc.src;}})();
</script>
<!-- AceCounter Log Gathering Script End -->
