<?
// 개인회원 KYC
// 출금계좌 등록폼 -> 1원인증 시작

include_once("_common.php");

$sql = "SELECT bank_code, bank FROM bank_info WHERE display='1' ORDER BY favorite DESC, bank_code ASC";
$res = sql_query($sql);
$bank_count = $res->num_rows;


if($member['mb_id']=='sori9th') {
	echo "<div style='text-align:center;font-size:12px;color:#333'>". $_SERVER['PHP_SELF'] . "</div>\n";
}

?>
<div class="confirm_form">

	<div class="top-title">
		<h2>본인확인</h2>
		<div class="page-num">
			<span class="active">2</span>/4
		</div>
	</div>
	<hr />

	<form name="form2-1" id="form2-1" class="frm-identify">
		<input type="hidden" name="order_id">
		<input type="hidden" id="mode" name="mode" value="authRequest">

		<h3>출금 계좌 등록</h3>
		<p>본인확인이 가능한 본인명의 계좌정보를 입력해주세요.<br />인증된 계좌는 예치금 환급계좌로 사용됩니다.</p>
		<label for="bankName" style="margin-top: 0;">은행</label>
		<select name="fBankCode" id="fBankCode" class="sel-code">
			<option value="">은행을 선택하세요</option>
			<?
			for($i=0; $i<$bank_count; $i++) {
				$R = sql_fetch_array($res);
				$selected = ($R['bank_code']==sprintf("%03d", $member["bank_code"])) ? 'selected' : '';
				echo "<option value='".$R['bank_code']."' $selected>".$R['bank']."</option>\n";
			}
			?>
		</select>
		<label for="fAcntNo">계좌번호</label>
		<input type="text" name="fAcntNo" id="fAcntNo" value="<?=$member['account_num']?>" onKeyup="onlyDigit(this);" placeholder="'-'를 제외한 계좌번호 입력" style="color:#CCC" />

		<div class="btn-mg1">
			<button type="button" id="KYCNextButton" class="btn-account">내 계좌로 1원 보내기</button>
			<? if($member['mb_id']=='sori9th') { ?><button type="button" onClick="pageReload();" class="change-btn" style="background:brown;width:50%;">페이지 재호출</button><? } ?>
		</div>
	</form>

</div>

<script>
$('#KYCNextButton').on('click', function() {

	if( $.trim($('#fBankCode').val()) == '' ) {
		alert('은행을 선택 해주세요.'); $('#fBankCode').focus(); return;
	}
	else if( $.trim($('#fAcntNo').val()) == '' ) {
		alert('계좌번호를 입력 해주세요.'); $('#fAcntNo').focus(); return;
	}


	var order_id = $('#masterForm #f_order_id').val();
	$("#form2-1 input[name='order_id']").val(order_id);

	var formData = $('#form2-1').serialize();

	// 예금주명 확인 및 인증번호 발송
	$.ajax({
		url : '/member_new/kyc_indi_step2_acnt_auth.php',
		type : 'post',
		data : formData,
		dataType : 'json',
		success: function(data) {
			if(data.result=='success') {

				$('#masterForm #f_trdNo').val(data.authReq_trdNo);
				$('#masterForm #f_mchtTrdNo').val(data.authReq_mchtTrdNo);
				$('#masterForm #f_bank_code').val(data.bank_code);
				$('#masterForm #f_account_num').val(data.account_num);

				KYCLoadPage('/member_new/kyc_indi_step2-2.php');
			}
			else {
				alert(data.message); return;
			}
		},
		beforeSend: function() { $('#loading').css('display','block'); },
		complete: function() { $('#loading').css('display','none'); },
		error: function() {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			$('#loading').css('display','none');
			return;
		}
	});

});
</script>

<script>
function pageReload() {
	KYCLoadPage('/member_new/kyc_indi_step2-1.php');
}
</script>