<?php

include_once('./_common.php');

if(!$is_member) { goto_url("/"); exit; }


// 예치금 조회
if($member["mb_point"] > 0) { alert("예치금이 남아있어 탈퇴할 수 없습니다. 관리자에게 문의해주세요.", "/"); exit; }

// 투자 내역 조회
$invest_query = "
	SELECT
		COUNT(b.idx) AS invest_count
	FROM
		cf_product a
	INNER JOIN
		cf_product_invest b
	ON
		a.idx=b.product_idx
	WHERE 1=1
		AND b.invest_state='Y'
		AND a.state='1'
		AND b.member_idx='".$member["mb_no"]."'";
$invest_row = sql_fetch($invest_query);
if($invest_row['invest_count']) { alert("상환중인 투자내역이 있어 탈퇴할 수 없습니다. 관리자에게 문의해주세요.", "/"); exit; }



$g5['title'] = '회원탈퇴';
$g5['top_bn'] = "/images/member/sub_break.jpg";
$g5['top_bn_alt'] = "회원정보 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";
if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once(HF_PATH.'/hf_head.php');


// 모바일 분기
if(G5_IS_MOBILE){
	include_once('./break_m.php');
	return;
}


?>


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
								<td><input type="password" class="text" name="check_pw"></td>
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
			<img src="../images/btn_close.gif" alt="close" class="close" >
			<div class="title">회원탈퇴완료</div>
			<div class="text pT50">회원탈퇴가 정상적으로 완료되었습니다.</div>
			<a href="/" class="btn_big_link">메인으로</a>
		</div>
<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once(HF_PATH.'/_tail.php');
?>
<script>
$(document).ready(function(){

	$('#check').click(function() {
		$.blockUI({
			message: $('#complete'),
			css: { border:0, cursor:'default' } });

		/*
		$.ajax({
			url: 'wait.php',
			cache: false,
			complete: function() {
				// unblock when remote call returns
				$.unblockUI();
			}
		});
		*/

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
				url : "/root_mypage/ajax_break.php",
				type: "POST",
				data : ajax_data,
				success: function(data, textStatus, jqXHR){
					$('#ajax_return_txt').val(data);
					if(data=="SUCCESS"){
						$.blockUI({
							message: $('#complete'),
							css: { border:0, cursor:'default' } });
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
						alert("상환중인 투자내역이 있어 탈퇴할 수 없습니다. 관리자에게 문의해주세요.");
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
