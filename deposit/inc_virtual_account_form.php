<?
#######################################
## 가상계좌번호 받기 팝업
########################################
?>
<div id="withdraw2" style="height:auto;" class="popbluetheme">
	<img src="/images/btn_close.gif" alt="close" class="close" />
	<div class="title">가상계좌번호 받기</div>
	<div class="con">
		<div class="type01">
			<table>
				<tbody>
				<tr>
					<td style="width:100px"><b>은행</b></td>
					<td>
						<select id='bank_cd'>
							<option value=''>:: 선택 ::</option>
<?
$VBANK_KEYS = array_keys($VBANK);
for($i=1; $i<count($VBANK); $i++) {
	echo "							<option value='".$VBANK_KEYS[$i]."'>".$VBANK[$VBANK_KEYS[$i]]."</option>\n";
}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width:100px"><b>가상계좌번호</b></td>
					<td><span id='vbank_result'>발급받기 버튼을 클릭하십시요.</span></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="btnArea"><span id="with_btn2" class="btn_big_blue">발급받기</span></div>
		<div class="title">예치금출금 가이드</div>
		<div class="box">
			* 헬로펀딩의 투자전용 예치금 계좌(가상계좌)입니다.<br/>
			* 헬로펀딩은 예치금을 통해서 투자 참여가 가능합니다.<br/>
			* 발급 받으신 예치금 계좌로 예치금을 충전하신 후 투자가 가능합니다.<br/>
		</div>
	</div>
</div>

<script>
//가상계좌발급
$('#with_btn2').click(function(){
	var bank_cd = $('#bank_cd').val();
	if(bank_cd == '') {
		alert('가상계좌 발급 은행을 선택 하십시요.');
	}
	else {
		$('#vbank_result').empty();
		$.ajax({
			type: 'post',
			url: '/deposit/ajax_virtual_account_request.php',
			data: { mode:'new', bank_cd:bank_cd },
			success: function(vals) {
				$('#ajax_return_txt').val(vals);
				var arr = vals.split(':');
				if(arr[0]==1) {
				  $('#with_btn2').attr('class','btn_big_gray');
					alert('가상계좌가 발급되었습니다.');
					location.href='/deposit/deposit.php?tab=2';
				}
				else {
					$('#vbank_result').append('<font color=red>'+arr[1]+'</font>');
				}
			}
		});
	}
});
</script>