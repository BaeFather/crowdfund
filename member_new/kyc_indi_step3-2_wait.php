<?
// 개인회원 KYC
// 본인 정보 입력완료 후 KYC 검토 대기
// 대상자 : 개인사업자(사업자등록증,대부업등록증), 대리인등록자(대리인회원의 KYC진행여부), 외국인(외국인등록증)

include_once("_common.php");


if($member['mb_id']=='sori9th') {
	echo "<div style='text-align:center;font-size:12px;color:#333'>". $_SERVER['PHP_SELF'] . "</div>\n";
}

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
		<h3>본인확인 서류 제출 완료</h3>
		<p>
			제출하신 본인확인 서류 관리자 검토 후 본인확인이 완료됩니다.<br />
			본인확인 완료 후 서비스 이용이 가능합니다.<br />
			<br />
			영업일 기준 1~3일 정도 소요됩니다.
		</p>

		<img src="/theme/2018/img/member/step15.jpg" alt="step" class="img-process"/>

		<div class="btn-mg2">
			<button type="button" id="closeButton" class="btn-main">메인으로 가기</button>
			<? if($member['mb_id']=='sori9th') { ?><button type="button" onClick="pageReload();" class="change-btn" style="background:brown">페이지 재호출</button><? } ?>
		</div>

	</div>

</div>

<script>
$('#closeButton').on('click', function() {
	window.location.replace('/');
});

function pageReload() {
	KYCLoadPage('/member_new/kyc_indi_step3-2_wait.php');
}
</script>