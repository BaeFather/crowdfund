<?
// 개인회원 KYC
// 본인 정보 입력 및 KYC 등록 성공

include_once("_common.php");


if($member['mb_id']=='sori9th') {
	echo "<div style='text-align:center;font-size:12px;color:#333'>". $_SERVER['PHP_SELF'] . "</div>\n";
}

$member = get_member($member['mb_id']);

?>
<div class="confirm_form">

	<div class="top-title">
		<h2>본인확인</h2>
		<div class="page-num">
			<span class="active">4</span>/4
		</div>
	</div>
	<hr />

	<div class="info-txt">
		<h3>본인 확인 완료</h3>
		<p>
			온라인투자연계금융업 및 이용자 보호에<br />
			관한 법률 제21조(투자자에 대한 정보확인)에 의한<br />
			본인확인 인증절차가 완료되었습니다.<br />
			<br />
			투자 및 서비스(출금, 상환 등) 이용이 가능합니다.
		</p>

		<? if($_REQUEST['vacct']) { ?>
		<p>헬로펀딩 가상계좌가 정상적으로 발급되었습니다.<br />가상계좌에 예치금 입금 후 투자해주세요. 감사합니다.</p>
		<div class="acc-confirm-box">
			<p>예치금 가상계좌</p>
			<p><?=$BANK[$member['va_bank_code2']]?> <?=$member['virtual_account2']?><span class="btn-copy" onClick="copyVacct('<?=$member['virtual_account2'];?>')">복사</span></p>
		</div>
		<? } ?>

		<div class="btn-mg1">
			<button type="button" id="closeButton" class="btn-main">메인으로 가기</button>
			<? if($member['mb_id']=='sori9th'||$member['mb_id']=='ysm1351') { ?><button type="button" onClick="pageReload();" class="change-btn" style="background:brown">페이지 재호출</button><? } ?>
		</div>
	</div>

</div>

<script>
$('#closeButton').on('click', function() {
	window.location.replace('/');
});

function pageReload() {
	KYCLoadPage('/member_new/kyc_indi_step3-2.php');
}
</script>