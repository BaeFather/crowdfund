<!-- 워크샵 업무시간안내 팝업 20170112 ~ 20170114 //-->
<style>
<? if(G5_IS_MOBILE) { ?>
#popup { display:none; position:relative; width:100%; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:0px; top:0px; cursor:pointer; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
<? } else { ?>
#popup { display:none; position:relative; width:100%; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:0px; top:0px; cursor:pointer; }
#popup img { width:100%; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
<? } ?>
</style>
<div id="popup">
	<!--<img src="/images/btn_close.gif" alt="close" class="close" />-->
	<div style="width:100%;"><img src="/popup/images/notice_workshop<?=(G5_IS_MOBILE)?'_m':''?>.jpg" style="width:100%;"></div>
	<div style="background:#eee;text-align:right; padding:6px 16px 6px;">
		<input type="checkbox" id="popupClose" value="1">
		<label for="popupClose">오늘하루 열지 않음</label>
		<span id="closeLayer" style="margin-left:30px; cursor:pointer;">×닫기</span>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	if(get_cookie('popupOpen')==false) {
		$.blockUI({
			message: $('#popup'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'15%',left:'1%',width:'98%', border:0, cursor:'default' }
			<? } else { ?>
			css: { top:'15%',left:'33%',width:'500px', border:0, cursor:'default' }
			<? } ?>
		});
	}
});

$('#popupClose').on('click', function(){
	if($('#popupClose').is(':checked')) {
		set_cookie('popupOpen', true, exptime, g5_cookie_domain);
	}
	else {
		delete_cookie('popupOpen');
	}
});
</script>
<!-- 종무식 업무시간안내 팝업 끝 -->