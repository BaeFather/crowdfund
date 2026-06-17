
<!-- 본문내용 START -->
<div id="content">
	<div class="location"><span>예치금정보</span><b class="blue">펀딩취소하기</b></div>

	<div class="content">
		<form method="post" name="frm" id="frm">
			<input type="hidden" name="invest_idx" value="<?=$invest_idx?>">
		</form>

		<div class="deposit">
			<div class="type03_2 mb10">
				<table>
					<colgroup>
						<col width="30%">
						<col width="70%">
					</colgroup>
					<tr>
						<th>상품명</th>
						<td><?=$INVEST["title"]?></td>
					</tr>
					<tr>
						<th>기간</th>
						<td><?=$INVEST["recruit_period_start"]?> ~ <?=$INVEST["recruit_period_end"]?></td>
					</tr>
					<tr>
						<th>투자금</th>
						<td><?=number_format($INVEST["amount"])?>원</td>
					</tr>
				</table>
			</div>
			<div class="btnArea">
				<span class="btn_big_blue" id="btn_cancel">편딩취소하기</span>
			</div>
		</div>

	</div>
</div>

<!-- 레이어 팝업 내용 -->
<div id="complete" class="deposit">
	<img src="../images/btn_close.gif" alt="close" class="close">
	<div class="title">투자취소</div>
	<div class="text"><?=$INVEST["title"]?> 투자를 취소하시겠습니까?</div>
	<span id="yes" class="btn_big_blue" style="width:30%">예</span> &nbsp;
	<span id="no" class="btn_big_link" style="width:30%">아니오</span>
</div>

<div id="complete3" class="deposit">
	<!--img src="../images/btn_close.gif" alt="close" class="close"-->
	<div class="title">투자취소완료</div>
	<div class="text">
		<span class="blue"><?=$INVEST["title"]?></span><br/>
		<span class="red">투자취소</span>가 완료되었습니다</span><br>
		예치금으로 환불처리 됩니다.
	</div>
	<a href="/deposit/deposit.php" id="main" class="btn_big_link">돌아가기</a>
</div>

<script>
$(document).ready(function(){

	$('#btn_cancel').click(function() {
		$.blockUI({
			message: $('#complete'),
			css: { border:0, cursor:'default' ,top:'6%',left:'1%', width:'98%' }
		});
	});

	$('#complete #no, #complete .close, #complete3 .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#complete #yes').click(function() {

		ajax_data = $("#frm").serialize();

		$.ajax({
			url : "./ajax_funding_cancel.php",
			type: "POST",
			data : ajax_data,
			success: function(data) {
				$('#ajax_return_txt').val(data);
				if(data=="SUCCESS") {
					$.blockUI({
						message: $('#complete3'),
						css: { border:0, cursor:'default', top:'6%',left:'1%', width:'98%' }
					});
				}
				else if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
				else if(data=="ERROR-DATE") { alert("펀딩 투자 기간이 아닙니다. 펀딩 취소는 투자 기간안에만 가능 합니다."); return; }
				else if(data=="ERROR-END")  { alert("펀딩 투자가 완료되어 취소가 불가능 합니다."); return; }
				else if(data=="ERROR-P2PCTR_PAUSE") { alert("중앙기록관리기관 점검 시간(23:20~00:40)에는 투자 신청 및 취소, 한도 조회가 불가능합니다."); return; }
				else { alert(data); return; }
			},
			error: function(e) {
				alert('네트워크 에러 입니다. 잠시 후 다시 시도 하십시요.'); return;
			}
		});

	});

});
</script>

<?
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>