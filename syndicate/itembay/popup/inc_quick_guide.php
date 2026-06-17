<!-- -------- 투자상품 알림 신청 -------- //-->
<div id="alrim_sms_req" class="detail" style="border:5px solid #0d4ab9;">

	<div style="color:#284893;">
		<p style="background-color:#0d4ab9;color:#fff;padding:5px 0;font-size:17px;font-family:'NGB';line-height:28px;">
	    투자심의위원회의 승인으로 출시된<br>
		신규 투자상품 <strong style="color:#eef10b;">무료 문자 알림 신청</strong></p>
		<p class="close" style="position:absolute;top:-35px;right:0;"><img src="/images/cancel_w.png" height="24" style="opacity:1;"></p>


		<input type="text" id="sms_receive_no" placeholder="전화번호(숫자만) 입력" onKeyup="onlyDigit(this);" maxlength="11" style="width:60%; height:45px; line-height:26px; border:2px solid #AAA; font:bold 14px gulim;color:#000; padding:0 0 0 4px;margin:20px 0;">
		<br>
		<!-- <div style="font-size:1.0625em;color:#1c1c1c;"><strong>투자상품 안내는 무료입니다.</strong></div> //-->
        <div style="padding-top:10px; font-size:0.937em;color:#1c1c1c;"><input type="checkbox" id="receive_ok"> <strong><label for="receive_ok">투자정보 안내 수신 및 휴대폰번호 등록에 동의합니다.</label></strong></div>
	</div>
	<div style="padding-top:18px;"><span id="sms_req_submit" class="btn_blue"><strong style="font-size:18px;">투자상품 무료 알림받기 신청</strong></span></div>
</div>
<!-- -------- 투자상품 알림 신청 끝 -------- //-->

<script>
// 레이어 온 (투자알림받기)
$('#reqsms, #reqsms_btn, #reqsms_btn2, #reqsms_banner').click(function() {
	$.blockUI({
        centerY: false,
        centerX: false,
        css:{
            position: 'fixed',
            margin: 'auto'
        },
		message: $('#alrim_sms_req'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'10%',width:'320px',height:'270px',border:'1px solid #AAA',cursor:'default', left:'7%' }
		<? } else { ?>
		css: { top:'16%',width:'500px',height:'290px',border:'1px solid #AAA',cursor:'default', left:'50%', transform:'translateX(-50%)' }
		<? } ?>
	});
    $('.blockUI.blockMsg').center();
});
$(window).resize(function() {
   // $('.blockUI.blockMsg').center();
});


$('#sms_req_submit').click(function() {
	var text = $('#sms_receive_no').val();
	if(text=='' || text.length < 10 ) {
		alert('문자메세지를 수신할 전화번호를 정확히 입력하여 주십시요.');
		$('#sms_receive_no').focus();
		return;
	}
	else if($('#receive_ok').is(':checked')==false) {
		alert('투자정보 안내 수신 및 휴대폰번호 등록에 동의하셔야 합니다.');
		$('#receive_ok').focus();
		return;
	}
	else {
		$.ajax({
			url : "/member/ajax_sms_request.php",
			type: "POST",
			data: {phone_no : text},
			success: function(data){
				alert(data);
				if(data=="ERROR"){
					alert("시스템 에러입니다. 관리자에 문의해주세요.");
				}
				else if(data=="2"){
					alert("문자 수신이 가능한 모바일 번호가 아닙니다.\n문자메세지를 수신할 전화번호를 정확히 입력하여 주십시요.");
				}
				else {
					alert("정상 등록 되었습니다.");
					$('#sms_receive_no').val('');
					$.unblockUI();
				}
			},
			error: function () {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			}
		});
	}
});
</script>