<?php
include_once('./_common.php');

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');


// 모바일 분기
if(G5_IS_MOBILE){
	include_once('./test_popup_m.php');
	return;
}

?>

<div id="content" style="border:1px solid red;">

	<div class="quick_guide_area"><span id="quick_guide_btn" style="cursor:hand;"><img src="/images/guide.png" style="opacity:0.9"></span></div>

	<div class="location"><b class="blue">테스트페이지</b></div>

  <div class="content" style="height:300px;">
		<div style="margin:0 auto;">
			<span id="complete_btn"  class="btn_big_blue">팝업1</span>
			<span id="complete2_btn" class="btn_big_blue">팝업2</span>
			<span id="complete3_btn" class="btn_big_blue">팝업3</span>
		</div>
	<div>

</div>

<div id="quick_guide" class="detail">
	<span class="close" />×</span>
  <div class="text"><span class="blue">헬프만 따라하면 당신도 <font color="#0171BD">투자 마스터</font></span>
		<table class="tbl1" border="0">
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>1. 회원가입</span><br />
					&nbsp; <span style='color:#AAA'>- 투자의 첫걸음은 회원가입부터 해주세요.</span>
				</td>
				<td class="tbl1d2"><a href="<?=($member['mb_id'])?"javascript:alert('이미 회원이십니다.');":"/bbs/register_choice.php";?>"><span class="btn_blue">바로가기</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>2. 가상계좌 발급</span><br />
					&nbsp; <span style='color:#AAA'>- 예치금 입금을 위한 가상계좌를 발급 받으세요.</span>
				</td>
				<td class="tbl1d2"><a href="/bbs/login.php?url=<?=urlencode('/deposit/deposit.php')?>"><span class="btn_blue">바로가기</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>3. 예치금 충전</span><br />
					&nbsp; <span style='color:#AAA'>- 발급 받은 가상계좌로 예치금을 입금해 주세요.</span>
				</td>
				<td class="tbl1d2"><span id="quick_charge_btn" class="btn_blue">바로가기</span></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>4. 투자하기</span><br />
					&nbsp; <span style='color:#AAA'>- 충전해 둔 예치금으로 상품에 투자해주세요.</span>
				</td>
				<td class="tbl1d2"><a href="/investment/invest_list.php"><span class="btn_blue">바로가기</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>5. 원천징수 정보 입력</span><br />
					&nbsp; <span style='color:#AAA'>- 투자가 완료되면 원천징수 정보를 입력해주세요.</span>
				</td>
				<td class="tbl1d2"><a href="/bbs/member_confirm.php?url=<?=urlencode('/mypage/mypage.php#bank_edit')?>"><span class="btn_blue">바로가기</span></a></td>
			</tr>
			<tr>
				<td class="tbl1d1"><span style='font:bold 1.1em Nanum Gothic'>6. 환급계좌 등록 및 변경</span><br />
					&nbsp; <span style='color:#AAA'>- 투자 수익금을 받을 환급계좌를 등록해주세요.</span>
				</td>
				<td class="tbl1d2"><a href="/bbs/member_confirm.php?url=<?=urlencode('/mypage/mypage.php#bank_edit')?>"><span class="btn_blue">바로가기</span></a></td>
			</tr>
		</table>
  </div>
</div>

<?
	// 가상계좌 등록내역 조회
	if(	$VACS = sql_fetch("SELECT bank_cd, acct_no, cmf_nm FROM vacs_vact WHERE bank_cd='".$member['va_bank_code']."' AND acct_no='".$member['virtual_account']."' AND acct_st='1'") ) {
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
						<td><?=$VBANK[$VACS['bank_cd']]?></td>
					</tr>
					<tr>
						<td><b>예금주</b></td>
						<td><?=$VACS['cmf_nm']?></td>
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
?>


<div id="complete2" class="detail">
	<img src="../images/btn_close.gif" alt="close" class="close" />
	<div class="title">타이틀2 타이틀2 타이틀2</div>
	<div class="text">텍스트2 텍스트2 텍스트2 텍스트2 텍스트2</div>
	<span id="yes" class="btn_big_blue">확인</span> &nbsp;
	<span id="no"  class="btn_big_link">취소</span>
</div>

<div id="complete3" class="detail">
	<img src="../images/btn_close.gif" alt="close" class="close" />
	<div class="title">타이틀3 타이틀3 타이틀3</div>
	<div class="text">텍스트3 텍스트3 텍스트3 텍스트3 텍스트3</div>
	<span id="yes" class="btn_big_blue">확인</span> &nbsp;
	<span id="no"  class="btn_big_link">취소</span>
</div>


<script>
// 레이어 오프
$(document).on("click", "#no, .close", function(){
	$.unblockUI();
	return false;
});

// 레이어 온
$('#quick_guide_btn').click(function() {
	$.blockUI({
		message: $('#quick_guide'),
		css:{ top:'10%', width:'500px',height:'530px',border:0, cursor:'default' }
	});
});

$('#quick_charge_btn').click(function() {
<? if($member['mb_id']) { ?>
	$.blockUI({
		message: $('#quick_charge'),
		css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
	});
<? } else { ?>
  $(location).attr("href", "/bbs/login.php?url=<?=urlencode('/deposit/deposit.php')?>");
<? } ?>
});
</script>


<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>