<!-- 투자위험고지 기능 추가 알림 팝업 시작 //-->
<style>
<? if(G5_IS_MOBILE) { ?>
#popup2 { display:none; position:relative; width:100%; background-color:#fff; }
#popup2 .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup2 .close { position:absolute; right:0px; top:0px; cursor:pointer; }
#popup2 .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
<? } else { ?>
#popup2 { display:none; position:relative; width:100%; background-color:#fff; }
#popup2 .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup2 .close { position:absolute; right:0px; top:0px; width:18px; cursor:pointer; }
#popup2 img { width:100%; }
#popup2 .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
<? } ?>
</style>
<div id="popup2">
	<img id="blose_btn2" src="/images/btn_close.gif" alt="close" class="close" />
	<div style="width:100%;"><img src="/popup/images/vocation20170427.jpg" style="width:100%;"></div>
	<div style="background:#eee;text-align:right; padding:6px 16px 6px;">
		<input type="checkbox" id="popupClose2" value="24">
		<label for="popupClose2">오늘하루 열지 않음</label>
		<span id="closeLayer" style="margin-left:30px; cursor:pointer;">× 닫기</span>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	if(get_cookie('popupOpen2')==false) {
		$.blockUI({
			message: $('#popup2'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'2%',left:'1%',width:'98%', border:0, cursor:'default' }
			<? } else { ?>
			css: { top:'15%',left:'30%',width:'640px', border:0, cursor:'default' }
			<? } ?>
		});
	}
});

$('#popupClose2').on('click', function(){
	if($('#popupClose2').is(':checked')) {
		var exptime = $('#popupClose2').val();
		set_cookie('popupOpen', true, exptime, g5_cookie_domain);
	}
	else {
		delete_cookie('popupOpen2');
	}
});
</script>
<!-- 투자위험고지 기능 추가 알림 팝업 끝 -->