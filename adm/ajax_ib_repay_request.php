<?
$nowtime = date('H:i:s');
?>
	<div style="margin:6% auto; width:600px;background-color:#fff; border:3px solid #284893">
		<span style="float:right; padding:8px 8px 18px;">
			<a href="javascript:;" onClick="requestPopup();"><img src="/images/cancel.png" alt="닫기"></a>
		</span>

		<br>

		<form id="frmIBReq" name="frmIBReq">
			<input type="hidden" name="action" value="loan_interest_give_ib_request_send">

		<h3>금융기관(신한은행) 원리금 이체</h3>

		<div style="margin:20px auto 0; width:96%;"><b>- 지급요청 요약</b></div>
		<div style="margin:4px auto 0; width:96%; min-height:40px;max-height:200px; overflow-x:hidden; border:1px solid #222">
			<div id="ib_wait_list" style="width:100%"></div>
		</div>

		<div style="margin:20px auto 0; width:96%;"><b>- 스케쥴 설정</b></div>
		<div style="margin:4px auto 0; width:96%;">
			<table class="table table-bordered">
				<tr>
					<th bgcolor="#FAFAFA">전송일시</th>
					<td>
						<ul style="margin:0;padding:0;list-style:none">
							<li style="float:left;"><input type="text" id="req_sdate" name="req_sdate" value="<?=date('Y-m-d');?>" placeholder="일자선택" class="form-control datepicker" style="width:100px" required></li>
							<li style="float:left;margin-left:10px;"><select id="req_stime" name="req_stime" class="form-control" style="width:90px" required>
<?
for($i=9; $i<=18; $i++) {
	$hour = sprintf("%02d", $i);
	for($j=0; $j<=11; $j++) {
		$min = sprintf('%02d', $j * 5);
		$His = $hour.':'.$min.':00';
		$printHis = $hour.':'.$min;
		if($His > '18:00:00') {
			$printHis.= " (비상용)";
			$fcolor = "brown";
		}
		else if($His > '17:30:00') {
			$printHis.= " (기관확인요망)";
			$fcolor = "#FF2222";
		}
		echo '<option value="'.$His.'" style="color:'.$fcolor.'">'.$printHis.'</option>' . PHP_EOL;
	}
}
?>
								</select>
							</il>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<div style="padding-bottom:10px;text-align:center;">
			<button type="button" class="btn btn-danger" id="req_start_btn" onClick="loanInterestGiveIBRequestSend()" style="width:150px">전문발송등록</button>
		</div>
		<div style="margin:0 auto 10px; width:96%; background-color:#FDFECB; border:1px dotted #000; border-radius:5px;">
			<ul style="padding-top:10px;font-size:13px;color:brown;">
				<li>전송일시는 헬로펀딩의 상환자료가 인사이드뱅크 서버로 전송처리 되는 시간임.</li>
				<li>권장 처리시간: 10:00 ~ 16:30 (신한은행 입금처리가능시간: 평일 05:30 ~ 17:00)</li>
				<li>권장 처리시간 이후 지급요청건이 발생한 경우, 기관측에 가능여부 확인 요망</li>
				<li>금융기관 휴무일은 자동이체 불가.</li>
			</ul>
		</div>
	</div>

<script>
loanInterestGiveIBRequestSend = function() {

	var formData = $('#frmIBReq').serialize();

	if($('input:checkbox[name="PRDT_TURN[]"]:checked').length > 0) {

		var msg = "전송일시: " + $('#req_sdate').val() + " " + $('select[name="req_stime"]').val() + "\n\n원리금지급요청전문 발송요청을 등록 하시겠습니까?";

		if(confirm(msg)) {
			$.ajax({
				url: 'product_calculate_proc.php',
				type: 'post',
				dataType: 'json',
				data: formData,
				success: function(data) {
					if(data.result=='SUCCESS') { alert("발송요청이 등록 되었습니다."); window.location.reload(); }
					else { alert(data.message); }
				},
				error: function() { alert('통신 에러입니다.'); }
			});
		}

	}
	else {
		alert('선택된 지급요청이 없습니다.');
	}
}
</script>