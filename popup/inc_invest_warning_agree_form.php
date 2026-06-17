<!-- 투자위험고지 팝업 시작 -->
<div id="invest_warning_agree" class="popbluetheme" style="height:auto;">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">※ 투자위험고지</div>
	<div class="con">
		<div class="con1">
			본 투자상품은 원금이 보장되지 않습니다. 모든 투자상품은 현행 법률 상 ‘유사수신 행위의 규제에 관한 법률’에 의거하여 원금과 수익을 보장할 수 없습니다.<br/>
			또한 차입자가 원금의 전부 또는 일부를 상환하지 못할 경우 발생하게 되는 투자금 손실 등 투자위험은 투자자가 부담하게 됩니다.
		</div>
		<div class="con2">
			<div>나 <font color="#153FA1"><?=$member['mb_name']?></font>은(는) 상기 내용을 확인하였으며 그 내용에</div>
			<div style="padding-top:10px;">
				<form name="invest_warning_agree_form" id="invest_warning_agree_form" onSubmit="return fSend('<?=$PRDT_STATE['code']?>');">
					<input type="hidden" name="prd_idx" value="<?=$prd_idx?>">
					<input type="hidden" name="page"    value="<?=$page?>">
					<input type="hidden" name="agree">
					<input type="text" name="str" maxlength="3" onKeyup="strCheck(this.value);" class="text1">
				</form>
			</div>
		</div>
		<div class="con3">※ 빈칸에 '동의함' 입력 시 투자 가능합니다.</div>
		<div class="btnArea">
			<span id="invest_warning_agree_btn" class="btn_big_blue2 off">확인</span>
		</div>
	</div>
</div>

<script type="text/javascript">
function invest_warning_agree_open() {
	$.blockUI({
		message: $('#invest_warning_agree'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'1%',left:'1%', width:'98%', border:0, cursor:'default' }
		<? } else { ?>
		css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
		<? } ?>
	});
}

$('#invest_warning_agree.close').click(function() {
	$.unblockUI();
	return false;
});

function strCheck(arg) {
	if(arg=='동의함') {
		$('input[name=agree]').attr('onFocus','this.blur()');
		var agree_val = 'Y';
		var class_val = 'btn_big_blue2';
	}
	else {
		var agree_val = 'N';
		var class_val = 'btn_big_blue2 off';
	}
	$('#invest_warning_agree_btn').attr('class', class_val);
	$('input[name=agree]').val(agree_val);
}

$('#invest_warning_agree_btn').click(function() {
	fSend('<?=$PRDT_STATE['code']?>');
});

/*
	$("input:text[name='str']").keyup(function(event) {
		if (event.which == 13) {
			fSend('<?=$PRDT_STATE['code']?>');
			return false;
		}
	});
*/

function fSend(arg) {
	var agree_val= $('input[name=agree]').val();
	if(agree_val=='Y') {
		ajax_data =  $('#invest_warning_agree_form').serialize();
		$.ajax({
			url : "/investment/ajax_invest_warning_agree.php",
			type: "POST",
			data : ajax_data,
			success: function(data){
				$('#ajax_return_txt').val(data);
				if(data==1) {
					alert('투자위험고지에 동의하셨습니다.');
					$.unblockUI();
					if(arg=='B02') { $(window).attr('location', '/investment/detail.php?prd_idx=<?=$prd_idx?>'); }
				}
				else {
					alert('데이터 전송 오류 : 관리자에게 문의하십시요!');
				}
			},
			error: function()	{
				alert('데이터 전송 오류 : 관리자에게 문의하십시요!');
			}
		});
	}
	else {
		alert('폼에서 요구하는 문장을 입력하시기 합니다.');
		$('input[name=str]').focus();
	}
	return false;
}
</script>
<!-- 투자위험고지 팝업 끝 -->