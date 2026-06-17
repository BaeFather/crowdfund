<?php
include_once('./_common.php');

/*
if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');
*/

?>

<!-- 이미지버튼 영역 -->
<div class="quick_guide_area">
	<p><img id="quick_guide_btn" src="<?=G5_THEME_URL?>/img/invest_guide_icon.png" <?if(G5_IS_MOBILE){?>width="100%"<?}?> style="cursor:pointer" alt="투자순서안내"></p>
	<? if(!$is_member) { ?><p><img id="reqsms_btn" src="<?=G5_THEME_URL?>/img/sms_icon.png" <?if(G5_IS_MOBILE){?>width="100%"<?}?> style="cursor:pointer" alt="투자상품알림받기"></p><? } ?>
</div>


<!-- 투자상품 알림 신청 -->
<div id="alrim_sms_req" class="detail">
	<span class="close" /><img src="/images/cancel.png" style="opacity:0.5;"></span><br />
	<div class="text" style="color:#284893;">헬로펀딩 투자상품 알림 신청<br><br>
		<span style="font-size:0.66em;color:gray"><strong>SMS로 알림 받기</strong></span>
		<input type="text" id="sms_receive_no" placeholder="전화번호(숫자만) 입력" onKeyup="onlyDigit(this);" maxlength="11" style="width:50%; height:26px; line-height:26px; border:3px solid #AAA; font:bold 14px gulim;color:#0591EA; padding:0 0 0 4px;">
		<br>
		<div style="padding-top:20px; font:normal 0.50em dotum;color:#999"><input type="checkbox" id="receive_ok"> 투자정보 안내 수신 및 휴대폰번호 등록에 동의합니다.</div>
	</div>
	<div style="padding:18px;border-top:1px dotted #000;"><span id="sms_req_submit" class="btn_big_blue">신청하기</span></div>
</div>
<!-- 투자상품 알림 신청 -->

<!-- quick guide -->
<div id="quick_guide" class="detail">
	<span class="close" /><img src="/images/cancel.png" style="opacity:0.5;"></span><br />
  <div class="text"><span class="blue">헬로펀딩 <font color="#0171BD">투자순서</font></span>
		<table class="tbl1" border="0">
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>1. 회원가입</span><br />
					&nbsp; <span style='color:#AAA'>헬로펀딩에 회원가입 하세요.</span>
				</td>
				<td class="tbl1d2"><a href="<?=($member['mb_id'])?"javascript:alert('이미 회원이십니다.');":"/bbs/register_choice.php";?>"><span class="btn_blue">회원가입</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>2. 가상계좌 발급</span><br />
					&nbsp; <span style='color:#AAA'>가상계좌를 발급 받으세요.</span>
				</td>
				<td class="tbl1d2"><a href="javascript:;" onClick="<?=($member['mb_id'])?"location.href='/deposit/deposit.php';":"if(confirm('로그인 후 이용가능 합니다.\\n로그인하시겠습니까?')){ location.href='/bbs/login.php'; }";?>"><span class="btn_blue">계좌발급</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>3. 예치금 충전</span><br />
					&nbsp; <span style='color:#AAA'>가상계좌로 예치금을 입금하세요.</span>
				</td>
				<td class="tbl1d2"><span id="quick_charge_btn" class="btn_blue">충전하기</span></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>4. 투자하기</span><br />
					&nbsp; <span style='color:#AAA'>예치금으로 상품에 투자하세요.</span>
				</td>
				<td class="tbl1d2"><a href="/investment/invest_list.php"><span class="btn_blue">투자하기</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>5. 원천징수 정보입력</span><br />
					&nbsp; <span style='color:#AAA'>원천징수 정보를 입력하세요.</span>
				</td>
				<td class="tbl1d2"><a href="javascript:;" onClick="<?=($member['mb_id'])?"location.href='/bbs/member_confirm.php?url=".urlencode('/mypage/mypage.php#bank_edit')."';" : "if(confirm('로그인 후 이용가능 합니다.\\n로그인하시겠습니까?')){ location.href='/bbs/login.php'; }";?>"><span class="btn_blue">정보입력</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>6. 환급계좌 등록 및 변경</span><br />
					&nbsp; <span style='color:#AAA'>수익금 환급계좌를 등록해주세요.</span>
				</td>
				<td class="tbl1d2"><a href="javascript:;" onClick="<?=($member['mb_id'])?"location.href='/bbs/member_confirm.php?url=".urlencode('/mypage/mypage.php#bank_edit')."';" : "if(confirm('로그인 후 이용가능 합니다.\\n로그인하시겠습니까?')){ location.href='/bbs/login.php'; }";?>"><span class="btn_blue">계좌등록</span></a></td>
			</tr>
		</table>
    <center>
		<a href="<?=G5_URL?>/investment/invest_list.php"><span class="btn_big_green" style="width:90%">투자상품보기</span></a>
    </center>
	</div>
</div>
<!-- quick guide -->

<?
if($member['mb_id']) {
	// 가상계좌 등록내역 조회
	if(	$TMP_VACS = sql_fetch("SELECT bank_cd, acct_no, cmf_nm FROM vacs_vact WHERE bank_cd='".$member['va_bank_code']."' AND acct_no='".$member['virtual_account']."' AND acct_st='1'") ) {
		// $VBANK 배열은 config.php 에 등록되어있음.
?>
<div id="quick_charge">
	<img src="/images/btn_close.gif" alt="close" class="close" />
	<div class="title">예치금입금</div>
	<div class="con">
		<div class="title">예치금 계좌정보</div>
		<div class="type01">
			<table>
				<tbody>
					<tr>
						<td style="width:60px"><b>은행명</b></td>
						<td><?=$VBANK[$TMP_VACS['bank_cd']]?></td>
					</tr>
					<tr>
						<td><b>예금주</b></td>
						<td><?=$TMP_VACS['cmf_nm']?></td>
					</tr>
					<tr>
						<td><b>계좌번호</b></td>
						<td><?php echo $member['virtual_account'];?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="info"><span class="green">*</span> 위 가상계좌로 투자금을 입금하시면 충전된 예치금으로 투자가 가능합니다. </div>
		<div class="title">예치금입금 가이드</div>
		<div class="box">
      헬로펀딩을 통해 발급된 가상계좌에 투자금을 입금하신 후 투자가 시작되는 상품에 투자를 진행하여 주시기 바랍니다.
		</div>
	</div>
</div>
<?
	}
}
?>

<script>
// 레이어 오프
$(document).on("click", "#no, .close", function(){
	$.unblockUI();
	return false;
});

// 레이어 온 (투자알림받기)
$('#reqsms, #reqsms_btn').click(function() {
	$.blockUI({
		message: $('#alrim_sms_req'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'10%',width:'320px',height:'270px',border:'1px solid #AAA',cursor:'default', left:'5%' }
		<? } else { ?>
		css: { top:'16%',width:'500px',height:'290px',border:'1px solid #AAA',cursor:'default' }
		<? } ?>
	});
});

$('#sms_req_submit').click(function() {
	var text = $('#sms_receive_no').val();
	if(text=='' || text.length < 10 ) {
		alert('문자메세지를 수신할 전화번호를 정확히 입력하여 주십시요.');
		$('#sms_receive_no').focus();
		return;
	}
	else if($('#receive_ok').is(':checked')==false) {
		alert('투자정보 안내 수신 및 휴대폰번호 등록에 동의하셔야 합니다.');
		$('#receive_ok').focus();
		return;
	}
	else {
		$.ajax({
			url : "/member/ajax_sms_request.php",
			type: "POST",
			data: {phone_no : text},
			success: function(data){
				if(data=="ERROR"){
					alert("시스템 에러입니다. 관리자에 문의해주세요.");
				}
				else if(data=="2"){
					alert("문자 수신이 가능한 모바일 번호가 아닙니다.\n문자메세지를 수신할 전화번호를 정확히 입력하여 주십시요.");
				}
				else {
					alert("정상 등록 되었습니다.");
					$('#sms_receive_no').val('');
					$.unblockUI();
				}
			},
			error: function () {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			}
		});
	}
});

// 레이어 온 (퀵가이드)
$('#quick_guide_btn').click(function() {
	$.blockUI({
		message: $('#quick_guide'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'10%',left:'5%',width:'90%',border:'1px solid #AAA', cursor:'default' }
		<? } else { ?>
		css: { top:'16%',width:'500px',height:'570px',border:'1px solid #AAA', cursor:'default' }
		<? } ?>
	});
});

$('#quick_charge_btn').click(function() {
<? if($member['mb_id']) { ?>
	$.blockUI({
		message: $('#quick_charge'),
		<? if(G5_IS_MOBILE) { ?>
		css: { top:'5%',left:'3%', width:'94%',border:0, cursor:'default' }
		<? } else { ?>
		css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
		<? } ?>
	});
<? } else { ?>
	if(confirm('로그인 후 이용가능 합니다.\n로그인하시겠습니까?')){
		location.href='/bbs/login.php';
	}
<? } ?>
});
</script>
<!-- quick guide //-->

<?php
/*
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
*/
?>