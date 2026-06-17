<?

/////////////////////////////////////////////////
// 신규 아파트 대출 요청 알림창
// admin.tail.php 에서 인클루드 됨
/////////////////////////////////////////////////

if( preg_match("/(개발|상품관리)/", $member['mb_name']) ) {
?>

<!-- 인사이드뱅크 데이터 전송요청 창 //-->
<div id="new_request_alim" style="position:fixed; z-index:1000; bottom:0px; right:0px; text-align:center; border:1px solid #000; width:230px;height:150px; padding:0; background:#FFFFCC; display:none;">
	<div style="text-align:right;margin:0;padding:0 6px;"><span onClick="newLoanRequestAlimClose();" style="color:#000;cursor:pointer">×</span></div>
	<div style="margin:auto;">
		<ul style="list-style:none;margin:20px auto;padding:0;font-size:14px">
			<li>신규 대출요청 <span id="new_request_count" style="color:#FF2222;font-weight:bold"><?=$new_request_count?></span>건이<br/>등록 되었습니다.</li>
			<li style="margin-top:10px;"><a href="/adm/loan_request/request.php?ST[]=1" style="font-weight:bold;color:#3366FF">등록내역확인</a></li>
		</ul>
	</div>
</div>

<script>
newLoanRequestAlimOpen = function() {
	$('#new_request_alim').fadeIn();
}

newLoanRequestAlimClose = function() {
	$('#new_request_alim').fadeOut();
}

newLoanRequestCheck = function() {
	if( $('#new_request_alim').css('display')=='none' ) {
		$.ajax({
			url:'/adm/loan_request/ajax_new_request_check.php',
			dataType:'json',
			success: function(data) {
				if( Number(data.new_request_count) > 0 ) {
					$('#new_request_count').html(data.new_request_count);
					newLoanRequestAlimOpen();
				}
			},
			error: function(e) { console.log(e); }
		});
	}
}

$(document).ready(function() {
	setInterval(newLoanRequestCheck, 30 * 1000);
});
</script>
<!-- 인사이드뱅크 데이터 전송요청 창 //-->

<?
}
?>