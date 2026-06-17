<?

exit;

?>
<!-- 실시간 입금안내 (KSNET 데이터 기준) -->
<div id="deposit_popup">
	<div class="title">실시간 입금 확인 <img src="/images/btn_close.gif" class="close"></div>
	<div class="con" id="deposit_con"><!-- 내용 --></div>
	<div class="btnArea"><button class="btn_blue close_button">확 인</button></div>
</div>
<script type="text/javascript">
//실시간 입금내역 확인
check_deposit_ksnet = function() {
	$.ajax({
		url : "/deposit/ajax_deposit_check_ksnet.php",
		type: "GET",
		success: function(data){
			if(data!='') {
				$.blockUI({
					message: $('#deposit_popup'),
					<? if(G5_IS_MOBILE) { ?>
					css: { top:'20%',left:'1%', width:'98%', border:0, cursor:'default' }
					<? } else { ?>
					css: { top:'30%',left:'39%',width:'585px', border:0, cursor:'default' }
					<? } ?>
				});
				$('#deposit_con').html(data);
			}
		}
	});
}
$(document).ready(function(){
	check_deposit_ksnet();
	setInterval(function() { check_deposit_ksnet();	}, 20*1000);
});
</script>
<!-- 실시간 입금안내 (KSNET 데이터 기준) -->