<?
include_once("_common.php");

if($CONF['flatform']=='') {
?>

<style>
	#appSetupPopup { display:none; position:fixed; left:0; top:0; margin:0; padding-top:19vh; width:100%; height:100vh; }
</style>
<div id="appSetupPopup">
	<img id="btn_app_setup" src="/images/popup/app_popup.png" style="width:280px;height:335;" usemap="#map20190925">
	<map id="map20190925" name="map20190925">
		<area shape="ract" coords="12,280,268,324" href="<?=$CONF['app_install_url']?>" target="_blank" alt="앱설치하기">
	</map>
	<span id="close1day" style="display:block;margin:10px;font-size:1.2em;color:#FAFBC9">24시간 동안 열지 않습니다.</span>
</div>

<script>
var _appSetupBreker = get_cookie('appSetupBreker');
if( _appSetupBreker==false ) {
	$(document).ready(function(){
		setInterval(
		$.blockUI({
			message: $('#appSetupPopup'),css:{ 'border':'0', 'position':'fixed' }
		})
		, 2500);
	});
}
$('#close1day').click(function() {
	set_cookie('appSetupBreker', 1, 24, g5_cookie_domain);
	$.unblockUI();
});
$('#appSetupPopup').click(function() {
	$.unblockUI();
});
</script>

<?
}
else if(@$CONF['flatform']=='app') {

	$app_version = base64_decode($_GET[md5('ver')]);
	$app_token   = base64_decode($_GET[md5('token')]);

	//echo $app_version . " : " . $CONF['app_latest_ver'] . "<br><br><br><br>";

	if($app_version && $app_version < $CONF['app_latest_ver']) {

		echo "
			<script>
			var app_install_url = '{$CONF['app_install_url']}';
			$(document).ready(function() {
				if( confirm('신규 업데이트가 있습니다. 이동하시겠습니까?') ) {
					$(location).attr('href', app_install_url);
				}
			});
			</script>\n";

	}

}

?>