	<!-- 모바일 실명인증 가이드창 //-->
	<style>
	<? if(G5_IS_MOBILE) { ?>
	#mobile_auth_guide { display:none; position:relative; width:100%; height:100%; text-align:left; background-color:#fff; }
	#mobile_auth_guide > .title { width:100%; line-height:42px; text-indent:20px; color:#fff; font-size:18px; background-color:#284893; }
	#mobile_auth_guide .close { position:absolute; right:15px; top:12px; cursor:pointer; }
	#mobile_auth_guide .con { height:84%; padding:10px; }
	<? } else { ?>
	#mobile_auth_guide { display:none; position:relative; width:407px; height:693px; text-align:left; background-color:#fff; }
	#mobile_auth_guide > .title { line-height:42px; text-indent:20px; color:#fff; font-size:18px; background-color:#284893; }
	#mobile_auth_guide .close { position:absolute; right:15px; top:12px; cursor:pointer; }
	#mobile_auth_guide .con { height:651px; }
	<? } ?>
	</style>
	<div id="mobile_auth_guide">
		<div class="title">모바일 본인인증 안내 <img src="/images/btn_close.gif" id="closeX01" class="close" onClick="fnPopup();"></div>
		<div class="con" id="epilogue_con"><a href="javascript:;" id="auth_close" onClick="fnPopup();"><img src="/images/member/mobile_auth_guide<?=(G5_IS_MOBILE)?'_m':''?>.jpg" width="100%"></a></div>
	</div>
	<script type="text/javascript">
	$('#btn_certi').click(function(){
		$.blockUI({
			message: $('#mobile_auth_guide'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'10%',width:'98%', left:'1%', cursor:'default',  border:'1px solid #AAA' }
			<? } else { ?>
			css: { top:'10%',width:'407px', left:'35%', cursor:'default',  border:'1px solid #AAA' }
			<? } ?>
		});
	});
	$('#auth_close, #closeX01').click(function(){
		$.unblockUI();
		return false;
	});
	</script>
	<!-- 모바일 실명인증 가이드창 //-->