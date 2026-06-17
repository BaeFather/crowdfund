<!-- 보고싶습니다 이벤트 팝업 -->
<style>
#popup { display:none; position:relative; width:462px; background-color:#fff; }
#popup .title { line-height:42px; text-indent:20px; text-align:left; color:#fff; font-size:18px; background-color:#284893; }
#popup .close { position:absolute; right:0px; top:0px; cursor:pointer; }
#popup .text { padding:40px 0 30px 0; font-size:22px; line-height:28px; color:#202020; font-family:'NGB';  }
</style>
<!-- 보고싶습니다 이벤트 2016103100 ~ 2016111117 -->
<div id="popup">
	<img src="/images/btn_close.gif" alt="close" class="close" />
	<div><a href="/event/invitation.php?event_idx=1"><img src="/images/main/invitation/invitation_event.jpg" width="462" height="370"></a></div>
	<div style="babkground:#ccc;text-align:right; padding:6px 16px 6px;">
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
			css: { top:'20%',left:'33%',width:'462px',border:0, cursor:'default' }
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
	//alert(get_cookie('popupOpen'));
});
</script>
<!-- 보고싶습니다 이벤트 팝업 끝 -->