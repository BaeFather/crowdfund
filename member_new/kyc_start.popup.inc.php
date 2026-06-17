<?
if( $member['kyc_next_dd']=='' || $member['kyc_next_dd'] <= date('Y-m-d') ) {
?>
<!----------- 투자회원 본인확인 레이어 팝업 ----------->
<div id="infoConfirmPop" class="common-pop invest-mem-pop">
	<div class="inner-box">
		<img src="/images/btn_close_black.png" class="close" alt="close" />
		<div class="main-popup-box">
			<div class="popup-content">

<? if($member['member_type']=='2') { ?>

				<h2>투자회원 <span>법인확인</span></h2>
				<p>온라인투자연계금융업 및 이용자 보호에<br />
					관한 법률 제21조(투자자에 대한 정보확인)에 의한<br />
					투자 회원의 법인확인 인증절차 입니다.<br />
					<br />
					법인확인 정보 미제공 또는 실패 시<br />
					<span style="color:red;font-weight:bold;">투자 및 서비스(출금, 상환 등) 이용이 제한</span>됩니다.<br />
					<br />
					<? if($member['va_bank_code2']=='' || $member['virtual_account2']=='') { ?>법인확인 완료 후 가상계좌가 발급됩니다.<? } ?>
				</p>
				<a href="/member_new/kyc_corp.php" class="mem-chk">법인확인하기</a>

<? } else { ?>

				<h2>투자회원 <span>본인확인</span></h2>
				<p>온라인투자연계금융업 및 이용자 보호에<br />
					관한 법률 제21조(투자자에 대한 정보확인)에 의한<br />
					투자 회원의 본인확인 인증절차 입니다.<br />
					<br />
					본인확인 정보 미제공 또는 실패 시<br />
					<span style="color:red;font-weight:bold;">투자 및 서비스(출금, 상환 등) 이용이 제한</span>됩니다.<br />
					<br />
					<? if($member['va_bank_code2']=='' || $member['virtual_account2']=='') { ?>본인확인 완료 후 가상계좌가 발급됩니다.<? } ?>
				</p>
				<a href="/member_new/kyc_indi.php" class="mem-chk">본인확인하기</a>

<? } ?>
			</div>
		</div>
	</div>
</div>
<!----------- 투자회원 본인확인 레이어 팝업 끝----------->

<script type="text/javascript">
function KYCPopup() {
	var $layer = $('#infoConfirmPop');

	$("body").prepend("<div id='infoMask' style='z-index:99999;position:fixed;background:black;background:rgba(0,0,0,0.5);width:100%;top:0;display:none;'></div>");
	$("body").append($layer);

	$layer.css({
		"z-index":"100000",
		"display":"flex"
	}).addClass("info-confirm-pop");

	wrapWindowByMask();

	function wrapWindowByMask() {
		var $mask = $('#infoMask');

		// 화면의 높이와 너비
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();

		$mask.css({'width':'100%','height':'100%'});
		$mask.show();
	}

	$('.info-confirm-pop .close').on('click', function() {
		$($layer).hide();		// 레이어 팝업 창
		$('#infoMask').hide();  // 뒷 배경
	});
}

<? if( preg_match("/\/public_html\/index\.php/", $_SERVER['SCRIPT_FILENAME']) ) { ?>$(document).ready(function(){ KYCPopup(); });<? } ?>

</script>

<?
}
?>