	<!-- 모바일 실명인증 가이드창 //-->
	<style>
	<? if(G5_IS_MOBILE) { ?>
	#product_notice { display:none; position:relative; width:100%; height:100%; text-align:left; margin-top:34px; background-color:none;}
	#product_notice > .title { width:100%; line-height:42px; text-indent:20px; color:#fff; font-size:18px; background-color:#284893; }
	#product_notice .close { position:absolute; right:15px; top:12px; cursor:pointer; }
	#product_notice .con { display:inline-block; }
	<? } else { ?>
	#product_notice { display:none; position:relative; width:720px; height:862px; text-align:left;  }
	#product_notice > .title { line-height:42px; text-indent:20px; color:#fff; font-size:18px; background-color:#284893; }
	#product_notice .close { position:absolute; right:15px; top:12px; cursor:pointer; }
	#product_notice .con { display:inline-block; }
	<? } ?>
	</style>
	<div id="product_notice">
		<div class="title">공지사항 <img src="/images/btn_close.gif" alt="close" class="close"></div>
		<div class="con" id="con"><img src="/popup/images/pop358.jpg" width="100%"></div>
	</div>
	<script>
	$('document').ready(function(){
		$.blockUI({
			message: $('#product_notice'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'2%',width:'98%', left:'1%', cursor:'default',  border:'1px solid #AAA' }
			<? } else { ?>
			css: { top:'2%',width:'720px', left:'33%', cursor:'default',  border:'1px solid #AAA' }
			<? } ?>
		});
	});
	</script>
	<!-- 모바일 실명인증 가이드창 //-->