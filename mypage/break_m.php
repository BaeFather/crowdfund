
		<img src="<?=G5_THEME_URL?>/img2/member/sub_break.jpg" alt="탈퇴안내 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." />

		<div id="content">
			<div class="location"><b class="blue">탈퇴안내</b></div>

			<div class="content">
				<!-- 탈퇴안내 -->
				<div class="break">
					<div class="title">헬로펀딩은 고객의 소리에 귀 기울이겠습니다.</div>
					<ul>
						<li><span class="green">＊ </span>예치금잔액, 투자중인 상품이 있으면 탈퇴 불가합니다.</li>
						<li><span class="green">＊ </span>대출 진행 건이 있으면 탈퇴 불가합니다.</li>
					</ul>
				</div>
				<!-- 탈퇴안내 -->
				<form method="post" name="frm" id="frm">
				<div class="type01">
					<table>
						<tbody>
							<tr>
								<th>현재비밀번호</th>
								<td><input type="password" class="text" name="check_pw"/></td>
							</tr>
							<tr>
								<th>탈퇴사유</th>
								<td><textarea class="textArea" name="check_reason"></textarea></td>
							</tr>
						</tbody>
					</table>
				</div>
				</form>
				<div class="btnArea">
					<a href="#" class="btn_big_blue" id="btn_break">전송</a>
				</div>
			</div>

		</div>
<div id="complete" class="break">
	<img src="../images/btn_close.gif" alt="close" class="close" />
	<div class="title">회원탈퇴완료</div>
	<div class="text pT50">회원탈퇴가 정상적으로 완료되었습니다.</div>
	<a href="/" class="btn_big_link">메인으로</a>
</div>
<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>
<script>
$(document).ready(function(){

	$('#check').click(function() {
		$.blockUI({
			message: $('#complete'),
			css: { border:0, cursor:'default' } });
	});

	$('#complete #yes, #complete .close').click(function() {
		$.unblockUI();
		return false;
	});

	// 레이어 팝업 = 확인 클릭시
	$('#btn_break').click(function() {

		if($("input[name='check_pw']").val()==""){  
			alert("패스워드를 입력해 주세요.");
			$("input[name='check_pw']").focus();
			return;
		}

		if($("textarea[name='check_reason']").val()==""){
			alert("탈퇴사유를  입력해 주세요.");
			$("textarea[name='check_reason']").focus();
			return;
		}
		if(confirm("탈퇴 하시겠습니까?")){

			ajax_data = $("#frm").serialize();
			$.ajax({
				url : "./ajax_break.php",
				type: "POST",
				data : ajax_data,
				success: function(data, textStatus, jqXHR){
					if(data=="SUCCESS"){
						$.blockUI({
							message: $('#complete'),
							css: { border:0, cursor:'default', top:'5%',left:'1%', width:'98%' } 
						});
					}
					else if(data=="ERROR-DATA"){
						alert("시스템 에러입니다. 관리자에 문의해주세요.");
						return;
					}
					else if(data=="ERROR-PASSWORD"){
						alert("패스워드가 일치 하지 않습니다.");
						return;
					}
					else if(data=="ERROR-BALANCE"){
						alert("예치금이 남아있어 탈퇴할 수 없습니다. 관리자에게 문의해주세요..");
						return;
					}
					else if(data=="ERROR-LOGIN"){
						alert("로그인 후 이용 가능합니다.");
						return;
					}
					else if(data=="ERROR-INVEST"){
						alert("투자내역이 있어 탈퇴할 수 없습니다. 관리자에게 문의해주세요.");
						return;
					}
					else{
						alert("시스템 에러입니다. 관리자에 문의해주세요.");
						return;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)	{

				}
			});
		}
	});


});
</script>

</body>
</html>
