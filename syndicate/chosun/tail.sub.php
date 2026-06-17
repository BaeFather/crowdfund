<?
if( $is_member && !$is_admin && !preg_match('/wowstar/i', $_COOKIE['PHPSESSID']) ) {

	////////////////////////////////////////////////////////////////////////
	// 가상계좌 미발급 알림 및 환급계좌설정용 팝업호출 (인덱스에서만 출력)
	////////////////////////////////////////////////////////////////////////
	if(preg_match("/\/index\.php|\/deposit\/deposit\.php|\/invest_list\.php/is", $_SERVER['PHP_SELF'])) {
		include_once(HF_PATH . "/popup/inc_bank_account.php");
	}

	/////////////////////////////////
	// 실시간 입금안내
	/////////////////////////////////
	//include_once(G5_PATH . "/popup/inc_deposit_check_insidebank.php");	// 인사이드뱅크 데이터 기준

}
?>
<?
require_once(HF_PATH.'/popup/inc_quick_guide.php');
?>


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
			if(key == 116) { return false; }
		});
<?
}
?>
	</script>

</body>
</html>

<? if($is_member) { ?>
<script type="text/javascript" src="<?=($_SERVER['REQUEST_SCHEME']=='https')?"https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js":"http://dmaps.daum.net/map_js_init/postcode.v2.js";?>"></script>
<? } ?>

<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다. ?>