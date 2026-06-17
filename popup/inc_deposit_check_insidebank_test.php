<? /* 실시간 입금안내 (인사이드뱅크 데이터 기준) */ ?>
<div id="deposit_popup">
	<div class="title">실시간 입금 확인 <img src="/images/btn_close.gif" class="close"></div>
	<div class="con" id="deposit_con" style="padding:10px 20px;"><!-- 내용 --></div>
	<div class="btnArea"><button class="btn_blue close_button" style="width:100px;">확 인</button></div>
</div>
<script type="text/javascript">
check_deposit_insidebank = function() {
	$.ajax({
		url : "/deposit/ajax_deposit_check_insidebank_test.php",
		type: "post",
		dataType: "json",
		cache: false,
		success: function(data) {

			if(data) {

				if(data.result=='LOGIN_PLEASE') {
					$(location).attr('href', '/bbs/login.php');
				}
				else if(data.result=='FAIL') {
					alert(data.message); return;
				}
				else if(data.result=='OK') {
					$.blockUI({
						message: $('#deposit_popup'),
						css: <? if(G5_IS_MOBILE) { ?>{ top:'20%',left:'1%', width:'98%', border:0, cursor:'default' }<? } else { ?>{ display:'inline-block', top:'30%',left:'39%', border:0, cursor:'default' }<? } echo "\n"; ?>
					});
					$('#deposit_con').html(data.message);
				}
				else if(data.result=='NONE') {
					return;
				}

			}
			else {
				console.log(data);
			}

		}
	});
}
$(document).ready(function() {
	check_deposit_insidebank();
	setInterval(function() { check_deposit_insidebank(); }, 20*1000);
});
</script>
