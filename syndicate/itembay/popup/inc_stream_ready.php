<?
if( in_array($prd_idx, array('153','154','156')) )  {									// 부산 정관신도시 일신메디컬센터 유동화자금
	$popup_image_url = "/popup/images/live_finished_20171212.png";
}
else if( in_array($prd_idx, array('175','176','177','206')) )  {			// 일산 대화동 다세대 주택 건축자금
	$popup_image_url = "/popup/images/live_finished_20180627.jpg";
}
else if( in_array($prd_idx, array('149','151','157','168','172','178')) ) {		// 울산 우정동
	$popup_image_url = "/popup/images/live_finished_20180326.jpg";
}
else if( in_array($prd_idx, array('205','207')) ) {		// 대구 이시아폴리스 메가맥스타워 유동화자금
	$popup_image_url = "/popup/images/install0816.jpg";
}
else if( in_array($prd_idx, array('174','212','301')) ) {		// 남양주시 별내동 다가구 주택 건축자금
	$popup_image_url = "/popup/images/live_finished_20180827.jpg";
}
else if( in_array($prd_idx, array('228')) ) {		// 인천 신흥동 오피스텔 준공자금
	$popup_image_url = "/popup/images/live_finished_20180830.jpg";
}
else {
	$popup_image_url = "/popup/images/stream_ready_notice.jpg";
}
?>

<!-- 실시간 스트림 준비중 안내 -->
<style>
#stream_ready_notice { margin:auto auto; display:none; }
<? if(G5_IS_MOBILE) { ?>
#stream_ready_notice .close { position:absolute; right:15px; top:12px; cursor:pointer; }
<? } else { ?>
#stream_ready_notice .close { position:absolute; right:15px; top:12px; cursor:pointer; }
<? } ?>
</style>
<div id="stream_ready_notice">
	<div class="title"><img src="/images/btn_close.png" alt="close" class="close"></div>
	<img id="stream_ready_image" src="<?=$popup_image_url?>" width="100%">
</div>

<script type="text/javascript">
$('#stream_ready_image').click(function() {
	$.unblockUI();
	return false;
});

function openStreamReady() {
	$.blockUI({
		message: $('#stream_ready_notice'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'20%', left:'1%', width:'98%', border:0, cursor:'default' }
		<? } else { ?>
		css: { top:'20%', width:'569px', margin:'auto auto', border:0, cursor:'default' }
		<? } ?>
	});
}
</script>
<!-- 실시간 입금안내 -->