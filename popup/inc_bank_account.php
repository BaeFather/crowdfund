<div id="vact_req_div" class="popbluetheme"></div>
<?
///////////////////////////////////////////////////////////////////////////////
//  2017-07-24 : 신한은행 가상계좌번호 받기, 환급계좌 등록
//  2019-01-21 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
///////////////////////////////////////////////////////////////////////////////


// $acct_script_on 값은 /deposit/deposit.php 에서 설정한다!!!!!

$acct_registered  = ( empty($member['bank_code']) || empty($member['account_num']) || empty($member['bank_private_name']) ) ? false : true;
$vacct_registered = ( empty($member['va_bank_code2']) || empty($member['virtual_account2']) ) ? false : true;

$load_page = ($member['member_type']=='2') ? "/bank_account/bank_account_c.php" : "/bank_account/bank_account_p.php";

$birthdate = $member['mb_birth'];

if($birthdate) {
	$age = getFullAge($birthdate);
}
$adult = ($age < 19) ? 'N' : 'Y';		// 만 19세 기준


//if($acct_registered==false || $vacct_registered==false) {
if($acct_script_on) {

	$acct_msg = "가상계좌를 발급 받으시겠습니까?";
	if($vacct_registered) {
		$acct_msg = "가상계좌를 재발급 받으시겠습니까?";
	}

?>


<script type="text/javascript">
function vaOpen() {
	$('#vact_req_div').empty();
	if(confirm('<?=$acct_msg?>')) {

<?
	if($member['tvtalk_userid']) {

		include_once(G5_PATH.'/mypage/crypt.php');
		$ad = tvtalk_get_adult();

		if ($ad=="N") {
?>
				alert("미성년자 회원 투자 시 \n\n법정대리인 동의서,\n미성년자가 기재된 주민등록등본,\n법정 대리인 신분증 사본\n\n이 필요합니다.\n미성년자 회원가입에 필요한 각각의 서류가\n첨부되지 않은 경우 회원 가입 승인이 되지 않습니다.\n\n문의 : 1588-6760\n카카오톡 문의 : 카카오톡 플러스 친구ID : 헬로펀딩\n서류접수 : hellofunding@gmail.com");
				return;
<?
		}
	}
?>

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
}

<? if( !preg_match("/\/event\/quest/i", $_SERVER['PHP_SELF']) ) {	?>
//$(document).ready(function() { setTimeout(vaOpen, 1*1000); });
<? }	?>
</script>
<?
}
?>