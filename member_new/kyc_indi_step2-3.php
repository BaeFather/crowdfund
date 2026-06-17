<?
// 개인회원 KYC
// 본인계좌 인증 확인 -> 환급계좌정보 출력

include_once("_common.php");


if($member['mb_id']=='sori9th') {
	echo "<div style='text-align:center;font-size:12px;color:#333'>". $_SERVER['PHP_SELF'] . "</div>\n";
}

$order_id = trim($_REQUEST['order_id']);

$AUTH_LOG  = sql_fetch("SELECT bankCode, acntNo  FROM stbk_acnt_auth_log WHERE mb_no='".$member['mb_no']."' AND order_id='".$order_id."' ORDER BY idx DESC LIMIT 1");
$BANK_INFO = sql_fetch("SELECT bank FROM bank_info WHERE bank_code='".$AUTH_LOG['bankCode']."'");

?>
<div class="confirm_form">

	<div class="top-title">
		<h2>본인확인</h2>
		<div class="page-num">
			<span class="active">2</span>/4
		</div>
	</div>
	<hr />

	<form name="form2-3" id="form2-3" method="post" class="frm-identify">
		<h3>본인 계좌 확인</h3>
		<p style="color:#3366FF">등록하신 계좌의 인증이 완료되었습니다.</p>
		<div class="acc-confirm-box">
			<p>환급계좌</p>
			<p><?=$BANK_INFO['bank']?> <?=$AUTH_LOG['acntNo']?></p>
		</div>
		<div class="btn-mg2">
			<button type="button" id="KYCNextButton" class="btn-confirm-next">다음</button>
			<? if($member['mb_id']=='sori9th'||$member['mb_id']=='ysm1351') { ?><button type="button" onClick="pageReload();" class="change-btn" style="background:brown;width:50%">페이지 재호출</button><? } ?>
		</div>
	</form>

</div>

<script>
$('#KYCNextButton').on('click', function() {
	KYCLoadPage('/member_new/kyc_indi_step3-1.php');
});

function pageReload() {
	KYCLoadPage('/member_new/kyc_indi_step2-3.php?order_id=<?=$order_id?>');
}
</script>

