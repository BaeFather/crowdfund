<?
///////////////////////////////////////////////////////////////////////////////
// 2017-07-24
// 신한은행 가상계좌번호 받기, 환급계좌 등록
///////////////////////////////////////////////////////////////////////////////

//$bank_acct_registered    = ($member['bank_code'] && $member['account_num'] && $member['bank_private_name']) ? true : false;
//$virtual_acct_registered = ($member['va_bank_code2'] && $member['virtual_account2']) ? true : false;

if($member['member_type']=='2') {
	$load_page = "/bank_account/bank_account_c.php";
}
else {
	$load_page = "/bank_account/bank_account_p.php";
}

?>
<div id="vact_req_div" class="popbluetheme">
</div>

<script type="text/javascript">
function vaOpen() {
	<? if(!$bank_acct_registered || !$virtual_acct_registered) { ?>
	$('#vact_req_div').empty();
	if(confirm('설레는 투자의 첫걸음인 개인 가상계좌를 발급 받으시겠습니까?\n\n' +
					 '──────────────────────────────\n\n' +
					 '[제3자에 의한 예치금 신탁관리 안내]\n' +
					 '신한은행에서 발급 및 관리하는 가상계좌를 통하여\n' +
					 '안전한 예치금 신탁관리를 받으실 수 있습니다.')) {


		$.ajax({
			url: '<?=$load_page?>',
			success: function(data) {
				$('#ajax_return_txt').val(data);
				$('#vact_req_div').html(data);
			}
		});

		$.blockUI({
			message: $('#vact_req_div'),
			css: { top:'<?=(G5_IS_MOBILE)?"1%":"10%"?>', left:'<?=(G5_IS_MOBILE)?"1%":"33%"?>', width:'<?=(G5_IS_MOBILE)?"98%":"605px"?>', height:'<?=(G5_IS_MOBILE)?"98%":""?>', border:0, cursor:'default' }
		});
	}
	<? } ?>
}
<? if(!$bank_acct_registered || !$virtual_acct_registered) { ?>
$(document).ready(function() {
	setTimeout(vaOpen, 1*1000);
});
<? } ?>
</script>
