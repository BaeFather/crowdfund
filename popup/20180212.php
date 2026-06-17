<!-- 추석연휴 공지 팝업 시작 //-->
<style>
<? if(G5_IS_MOBILE) { ?>
#popup { display:none; position:relative; width:100%; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:5px; top:5px; cursor:pointer; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
<? } else { ?>
#popup { display:none; position:relative; width:100%; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:5px; top:5px; width:18px; cursor:pointer; }
#popup img { width:100%; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
<? } ?>
</style>
<div id="popup">
	<img src="/images/btn_close.png" alt="close" class="close" />
	<div style="width:100%;"><img src="/images/popup/holiday_20180212.jpg" style="width:100%;"></div>
	<div style="background:#eee;text-align:right; padding:6px 16px 6px;">
		<input type="checkbox" id="popupClose" value="24">
		<label for="popupClose">오늘 하루 열지 않음</label>
		<span id="closeLayer" style="margin-left:30px; cursor:pointer;">×닫기</span>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	if(get_cookie('popupOpen')==false) {
		$.blockUI({
			message: $('#popup'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'10%',left:'3%',width:'93%', border:0, cursor:'default' }
			<? } else { ?>
			css: { top:'10%',left:'33%',width:'500px', border:0, cursor:'default', left:'50%', transform:'translateX(-50%)' }
			<? } ?>
		});
	}
});

$('#popupClose').on('click', function(){
	if($('#popupClose').is(':checked')) {
		var exptime = $('#popupClose').val();
		set_cookie('popupOpen', true, exptime, g5_cookie_domain);
	}
	else {
		delete_cookie('popupOpen');
	}
});
</script>
<!-- 추석연휴 공지 팝업 끝 -->