<!-- 공지 팝업 시작 //-->
<style>
<? if(G5_IS_MOBILE) { ?>
#popup { display:none; position:absolute; width:100%; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:5px; top:5px; cursor:pointer; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'spoqahansans';  }
<? } else { ?>
#popup { display:none; position:absolute; width:100%; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:5px; top:5px; width:18px; cursor:pointer; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'spoqahansans';  }
<? } ?>
</style>
<div id="popup">
	<img src="/images/btn_close_black.png" alt="close" class="close" />
	<div><img src="/popup/images/holiday_pop.jpg" style="width:100%;"></div>
	<div style="background:#eee;text-align:right; padding:6px 16px 6px;font-family:'spoqahansans'; ">
		<input type="checkbox" id="popupClose" value="1">
		<label for="popupClose">오늘하루 열지 않음</label>
		<span id="closeLayer" style="margin-left:30px;font-family:'spoqahansans';  cursor:pointer;">×닫기</span>
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
			css: { top:'5%', width:'640px', height:'700px', border:0, cursor:'default', left:'31%', transform:'translateX(-50%,-50%)' }
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
<!-- 공지 팝업 끝 -->