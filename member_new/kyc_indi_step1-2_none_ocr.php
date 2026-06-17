<?
// 개인회원 KYC
// 신분증 정보 확인 : OCR 결과정보 출력

//usleep(500000);

include_once("_common.php");

if($member['mb_id']=='sori9th') {
	echo "<div style='text-align:center;font-size:12px;color:#333'>". $_SERVER['PHP_SELF'] . "</div>\n";
}

?>
<div class="confirm_form">

	<div class="top-title">
		<h2>본인확인</h2>
		<div class="page-num">
			<span class="active">1</span>/4
		</div>
	</div>
	<hr />

	<form name="form1-2a" id="form1-2a" method="post" class="frm-identify">
		<input type="hidden" name="order_id">
		<input type="hidden" name="koreanYN">

		<h3>인적정보 입력</h3>
		<p>아래 내용을 등록하여 주십시요.</p>
		<div>
			<label for="userName">성명</label>
			<input type="text" name="userName" id="userName" value="<?=$member['mb_name']?>" />
		</div>

		<div>
			<label id="registNumTitle" for="juminNo">주민등록번호 / 외국인등록번호</label>
			<ul>
				<li style="float:left;width:49.5%">
					<input type="text" id="privateNo1" name="privateNo1" autocomplete="off" onKeyUp="onlyDigit(this);" maxlength="6" />
				</li>
				<li style="float:right;width:49.5%;maring-left:1%">
					<input type="password" id="privateNo2" name="privateNo2" onKeyUp="onlyDigit(this);" maxlength="7" />
				</li>
			</ul>
		</div>

		<ul class="btn-prev-next m-layout" style="margin:150px 0 0">
			<li><button type="button" id="KYCPrevButton" class="btn-prev">이전</button></li>
			<li><button type="button" id="KYCNextButton" class="btn-next">다음</button></li>
		</ul>

		<? if($member['mb_id']=='sori9th') { ?><button type="button" onClick="pageReload();" class="change-btn" style="background:brown">페이지 재호출</button><? } ?>

	</form>

</div>

<script>
$(document).ready(function() {
	$('#form1-2a input[name=koreanYN]').val($('#masterForm #f_koreanYN').val());
	var str = ( $('#masterForm #f_koreanYN').val()=='Y' ) ? '주민등록번호' : '외국인등록번호';
	$('#registNumTitle').html(str);
});

$('#KYCPrevButton').on('click', function() {
	$('#masterForm #f_fname').val('');
	$('#masterForm #f_fname2').val('');
	KYCLoadPage('/member_new/kyc_indi_step1-1_none_ocr.php');
});

$('#KYCNextButton').on('click', function() {

	if($('#privateNo1').val()=='') {
		alert('주민등록증/외국인등록번호 앞 여섯자리를 입력해주세요.');
		$('#privateNo1').focus();
		return;
	}
	if($('#privateNo2').val()=='') {
		alert('주민등록증/외국인등록번호 뒤 입곱자리를 입력해주세요.');
		$('#privateNo2').focus();
		return;
	}

	var order_id = $('#masterForm #f_order_id').val();
	$("#form1-2a input[name='order_id']").val(order_id);

	var formData = $('#form1-2a').serialize();

	$.ajax({
		url : 'kyc_indi_step1-2_none_ocr.proc.php',
		type : 'post',
		dataType : 'json',
		data : formData,
		success: function(data) {
			if(data.result=='success') {
				$('#masterForm #f_private_num').val(data.private_num);
				KYCLoadPage('/member_new/kyc_indi_step2-1.php');		// 1원인증페이지로 이동
			}
			else {
				alert(data.message);
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

function pageReload() {
	KYCLoadPage('/member_new/kyc_indi_step1-2_none_ocr.php');
}
</script>