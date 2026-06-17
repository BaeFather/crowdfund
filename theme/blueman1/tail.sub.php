<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div id="loading" style="position:fixed; z-index:10001; top:0px; left:0px; width:100%; height:100%; display:none;">
	<table width="100%" height="100%">
	  <tr>
		  <td height="100%" align="center"><img src="/images/loading/ani_load.gif" width="24"><br>loading...</td>
		</tr>
	</table>
</div>

<?

////////////////////////
// 이벤트 레이어 팝업
////////////////////////
if(preg_match("/\/index.php/is", $_SERVER['PHP_SELF'])) {
/*
	if(date(YmdH)>=2016103100 && date(YmdH)<=2016111117) include_once(G5_PATH."/popup/20161031.php"); //보고싶습니다 이벤트
	if(date(Ymd)>=20161229 && date(Ymd)<=20170101)  include_once(G5_PATH."/popup/20161229.php"); //보고싶습니다 이벤트
	if(date(Ymd)>=20170112 && date(Ymd)<=20170114)  include_once(G5_PATH."/popup/20170112.php"); //워크샵 공지
	if(date(Ymd)>=20170126 && date(Ymd)<=20170130)  include_once(G5_PATH."/popup/20170126.php"); //설연휴 공지
	if(date(Ymd)>=20170303 && date(Ymd)<=20170310)  include_once(G5_PATH."/popup/20170303.php"); //P2P금융협회 가입 알림
	include_once(G5_PATH."/popup/20170324.php"); //투자위험고지 기능 추가 알림
	if(date(Ymd)>=20170427 && date(Ymd)<=20170507) include_once(G5_PATH."/popup/20170427.php"); //5월연휴 공지

	include_once(G5_PATH."/popup/20170518.php"); //투자체험단 모집
	include_once(G5_PATH."/popup/20170602.php");
	include_once(G5_PATH."/popup/20170809.php"); //여름휴가 공지

	if(preg_match("/\/index.php/is", $_SERVER['PHP_SELF'])) {
		if(date(Ymd)>=20171011 && date(Ymd)<=20171015) include_once(G5_PATH."/popup/20171011.php"); //추석 연휴 공지
	}

	if(date(Ymd)>=20171010 && date(Ymd)<=20171015)  include_once(G5_PATH."/popup/20171011.php"); //신한 제3자예치 시스템 적용공지팝업
	include_once(G5_PATH."/popup/20171016.php"); //헬로펀딩 6가지 이유


include_once(G5_PATH."/popup/20171129.php"); //회원가입 이벤트
include_once(G5_PATH."/popup/20180212.php"); //새해 인사말 팝업*/
}
////////////////////////
// 투자순서안내
////////////////////////
require_once(G5_PATH.'/popup/inc_quick_guide.php');


if( $is_member && !$is_admin && !preg_match('/wowstar/i', $_COOKIE['PHPSESSID']) ) {

	////////////////////////////////////////////////////////////////////////
	// 가상계좌 미발급 알림 및 환급계좌설정용 팝업호출 (인덱스에서만 출력)
	////////////////////////////////////////////////////////////////////////
	if(preg_match("/\/index\.php|\/deposit\/deposit\.php/is", $_SERVER['PHP_SELF'])) include_once(G5_PATH . "/popup/inc_bank_account.php");
//if(preg_match("/\/index.php/is", $_SERVER['PHP_SELF'])) include_once(G5_PATH . "/popup/inc_virtual_account_form.php");

	/////////////////////////////////
	// 실시간 입금안내
	/////////////////////////////////
	include_once(G5_PATH . "/popup/inc_deposit_check_insidebank.php");	// 인사이드뱅크 데이터 기준
//include_once(G5_PATH . "/popup/inc_deposit_check.php");							// 세틀뱅크 데이터 기준
//include_once(G5_PATH . "/popup/inc_deposit_check_ksnet.php");				// KSNET 데이터 기준

}

////////////////////////
// 환급계좌 등록안내 (구)
////////////////////////
//if(preg_match("/\/index.php|\/deposit\/deposit\.php/is", $_SERVER['PHP_SELF']))
//require_once(G5_PATH . '/popup/inc_bank_account_check.php');

?>

<div id="emergency1" class="popbluetheme" style="height:<?=(G5_IS_MOBILE)?"98%":"520px"?>"></div>
<script>
$(document).ready(function() {
	$.ajax({
		url:'/shinhan/ajax_member_emergency_msg.php',
		success: function(data) {
			if(data) {
				$('#emergency1').html(data);
				$.blockUI({
					message: $('#emergency1'),
					css: { top:'<?=(G5_IS_MOBILE)?"1%":"10%"?>', left:'<?=(G5_IS_MOBILE)?"1%":"33%"?>', width:'<?=(G5_IS_MOBILE)?"98%":"605px"?>', border:0, cursor:'default' }
				});
			}
		},
		error: function(e) { return; }
	});
});
</script>

<script type="text/javascript">
// 레이어 오프
$(document).on("click", "#no, #closeLayer, .close, .close_button", function(){
	$.unblockUI();
	return false;
});

<?
$special_user = ($is_admin=='super' || in_array($member['mb_id'], array('akorea','yr4msp','hellosiesta','sori9th','hellofunding','test070'))) ? true : false;
if(!$special_user) {
?>
$(document).keydown(function(e) {
    key = (e) ? e.keyCode : event.keyCode;
    if (key == 116) {
			return false;
    }
});
<?
}
?>
</script>

</body>
</html>

<!-------------------- LiveLog TrackingCheck Script Start -------------------->
<script type="text/javascript">
var LLscriptPlugIn = new function () { this.load = function(eSRC,fnc) { var script = document.createElement('script'); script.type = 'text/javascript'; script.charset = 'utf-8'; script.onreadystatechange= function () { if((!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete') && fnc!=undefined && fnc!='' ) { eval(fnc); }; }; script.onload = function() { if(fnc!=undefined && fnc!='') { eval(fnc); }; }; script.src= eSRC; document.getElementsByTagName('head')[0].appendChild(script); }; }; var ref = encodeURIComponent(document.referrer); if( ref=='undefined' || ref=='' ) { rPara = ""; sessionStorage.removeItem('rPara'); } else { rPara = "&r="+ref+""; sessionStorage.setItem('rPara', ref); }; LoadURL = "MjcIMTQIMQgzMQg0CDExCDYINQg5CA"; LLscriptPlugIn.load('//livelog.co.kr/js/plugShow.php?'+LoadURL+rPara, 'sg_check.playstart()');
</script>
<!-------------------- LiveLog TrackingCheck Script End -------------------->

<!-------------------- google analytics -------------------->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-96465943-1', 'auto');   //이전계정: UA-80996386-2
  ga('send', 'pageview');
</script>
<!-------------------- google analytics -------------------->

<!-------------------- naver analytics -------------------->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script>
<script type="text/javascript">
if(!wcs_add) var wcs_add = {};
wcs_add["wa"] = "e1cd2274b7f94";
wcs_do();
</script>
<!-------------------- naver analytics -------------------->

<!-------------------- daum clicks -------------------->
<script type="text/javascript">
//<![CDATA[
var DaumConversionDctSv="type=M,orderID=,amount=";
var DaumConversionAccountID="86LlDRte2zvPHjF8Xic1-A00";
if(typeof DaumConversionScriptLoaded=="undefined"&&location.protocol!="file:"){
 	var DaumConversionScriptLoaded=true;
 	document.write(unescape("%3Cscript%20type%3D%22text/javas"+"cript%22%20src%3D%22"+(location.protocol=="https:"?"https":"http")+"%3A//t1.daumcdn.net/cssjs/common/cts/vr200/dcts.js%22%3E%3C/script%3E"));
}
//]]>
</script>
<!-------------------- daum clicks -------------------->

<!-------------------- google adwords -------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 869613409;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "dve3CP-Bxm8Q4f7UngM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<!-------------------- google adwords -------------------->

<!-------------------- Naver Primieum log -------------------->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"> </script>
<script type="text/javascript">
if (!wcs_add) var wcs_add={};
wcs_add["wa"] = "s_3fccd9ab394f";
if (!_nasa) var _nasa={};
wcs.inflow();
wcs_do(_nasa);
</script>
<!-------------------- Naver Primieum log -------------------->

<!-------------------- Facebook Pixel Code -------------------->
<script type="text/javascript">
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '262223700896964'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=262223700896964&ev=PageView&noscript=1"></noscript>
<!-------------------- End Facebook Pixel Code -------------------->

<!-------------------- ShowGet Widget Script Start -------------------->
<!--
<script>var SGscriptPlugIn = new function () { StarADPayment=''; this.loadSBox = function(eSRC,fnc) { var script = document.createElement('script'); script.type = 'text/javascript'; script.charset = 'utf-8'; script.onreadystatechange= function () { if((!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete') && fnc!=undefined && fnc!='' ) { eval(fnc); }; }; script.onload = function() { if(fnc!=undefined && fnc!='') { eval(fnc); }; }; script.src= eSRC; document.getElementsByTagName('head')[0].appendChild(script); }; };</script>
<script>SGscriptPlugIn.loadSBox('//showget.co.kr/js/plugShow.php?hellofunding','sg_paycheck.playstart()');</script>
//-->
<!-------------------- ShowGet Widget Script End -------------------->

<!-------------------- ShowGet SCON Script -------------------->
<!--
<script>SGscriptPlugIn.loadSBox('//showget.co.kr/showcorn/js/showconbar.js.php?pid=hellofunding',"showconbar.code=new Array('hellofunding');showconbar.SCon();");</script>
//-->
<!-------------------- ShowGet SCON Script -------------------->

<?
echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.
@sql_close($g5['connect_db']);
?>
<? if ($_COOKIE['debug_mode']) {  ?>
<div style='float:left; text-align:center;'>RUN TIME : <? echo get_microtime()-$begin_time; ?><br></div>
<? }  ?>