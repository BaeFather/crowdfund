<?

//exit;

if($is_member && !$is_admin && ($member['receive_method']=='' || $member['receive_method']=='1')) {
	if($member['bank_name']=='' || $member['bank_code']=='' || $member['bank_private_name']=='' || $member['account_num']=='') {
		$LOG1 = sql_fetch("SELECT COUNT(idx) AS invest_count FROM cf_product_invest WHERE member_idx='".$member['mb_no']."'");
		$LOG2 = sql_fetch("SELECT COUNT(idx) AS invest_count FROM cf_event_product_invest WHERE member_idx='".$member['mb_no']."'");
		if($LOG1['invest_count'] || $LOG2['invest_count']) {
?>

<!-- 환급계좌 등록안내 -->
<div id="bank_account_popup">
	<div class="title">환급계좌 등록안내 <img src="/images/btn_close.gif" class="close"></div>
	<div class="con">
		<span style="font-weight:bold"><?=$member['mb_name']?></span> 고객님<br>
		원리금 수취를 위한 <span style="color:#FF2222">환급계좌</span>를 등록해주세요.
	</div>
	<div class="btnArea"><span class="btn_big_blue" id="bank_account_popup_close" style="width:180px;">환급계좌 등록하기</span></div>
</div>
<!-- 환급계좌 등록안내 -->
<script>
$('#bank_account_popup_close').click(function() {
	$.ajax({
		url : "/mypage/ajax_simple_skip.php",
		type: "POST",
		success: function(data) {
			location.href='/mypage/mypage.php#mb_mailling';
		},
		error: function () {
			$.unblockUI();
			return false;
		}
	});
});
$(document).ready(function() {
	$.blockUI({
		message: $('#bank_account_popup'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'15%',width:'98%',height:'210px',border:'1px solid #AAA',cursor:'default', left:'1%' }
		<? } else { ?>
		css: { top:'20%',width:'600px',height:'220px',border:'1px solid #AAA',cursor:'default' }
		<? } ?>
	});
});
</script>

<?
		}
	}
}
?>