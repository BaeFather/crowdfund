<style>
.tblX { width:100%; }
.tblX th { padding:2px 4px; border-right:1px solid #ccc;border-bottom:1px solid #ccc; font-size:1.1em; text-align:center; background:#F7F7F7; }
.tblX td { padding:2px 4px; border-right:1px solid #ccc;border-bottom:1px solid #ccc; font-size:1.0em; background:#FCFCFC; }
.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
.btn_gray_s2 { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-size:12px; color:#fff; border-radius:3px; background-color:#888888; border:0; vertical-align:middle; cursor:pointer; }
span.left  { float:left; }
span.right { float:right; }
</style>

<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue">투자 내역</b></div>
	<div class="content">
		<div class="deposit">

			<!-- 탭메뉴 -->
			<ul class="tab_type03">
				<li id="invest_status"   data-gubun="tab1" <?=($tab==0)?'class="on"':''?>>투자현황</li>
				<li id="interest_status" data-gubun="tab2" <?=($tab==1)?'class="on"':''?>>수익금현황</li>
				<li id="money_status"    data-gubun="tab3" <?=($tab==2)?'class="on"':''?>>예치금현황 및 출금</li>
				<li id="va_info"         data-gubun="tab4" <?=($tab==3)?'class="on"':''?>>가상계좌정보</li>
				<li id="invest_limit"    data-gubun="tab5" <?=($tab==4)?'class="on"':''?>>투자한도</li>
			</ul>
			<script>
			// 탭 기능
			$(document).ready(function(){
				$(this).addClass('on').siblings().removeClass('on');
				$('.boxArea').hide();
				$('.boxArea:eq(<?=$tab?>)').show();

				$('.tab_type03 li').click(function(){
					$(this).addClass('on').siblings().removeClass('on');
					var cur = $(this).index();
					$('.boxArea').hide();
					$('.boxArea:eq('+cur+')').show();
					Size2ParentSend();
				});
			});
			</script>

<!-- 투자 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<p>&nbsp;</p>
				<h3>투자 현황</h3>
				<div class="type03 mb30">
					<table class="tblX table_invest_state">
						<colgroup>
							<col style="width:33%">
							<col style="width:33%">
							<col style="width:34%">
						</colgroup>
						<tbody>
							<tr>
								<td rowspan="4" style="background-color:#EFEFEF;text-align:center;">투자</td>
								<td style='text-align:center;background-color:#F7F7F7'>총투자금액</td>
								<td style='text-align:right;'><?=number_format($invest_amount_total)?>원</td>
							</tr>
							<tr>
								<td style='text-align:center;background-color:#F7F7F7'>총상환금액</td>
								<td style='text-align:right;'><?=number_format($repayment_value)?>원</td>
							</tr>
							<tr>
								<td style='text-align:center;background-color:#F7F7F7'>총상환이자</td>
								<td style='text-align:right;'><?=number_format($repayment_interest_row["repayment_interest"])?>원</td>
							</tr>
							<tr>
								<td style='text-align:center;background-color:#F7F7F7'>투자잔액</td>
								<td style='text-align:right;'><?=number_format($invest_amount_total-$repayment_value)?>원</td>
							</tr>
							<tr>
								<td rowspan="4" style="background-color:#EFEFEF;text-align:center;">이벤트</td>
								<td style='text-align:center;background-color:#F7F7F7'>총투자금액</td>
								<td style='text-align:right;'><?=number_format($event_invest_amount_total)?>원</td>
							</tr>
							<tr>
								<td style='text-align:center;background-color:#F7F7F7'>총상환금액</td>
								<td style='text-align:right;'><?=number_format($event_repayment_value)?>원</td>
							</tr>
							<tr>
								<td style='text-align:center;background-color:#F7F7F7'>총상환이자</td>
								<td style='text-align:right;'><?=number_format($event_repayment_profit_value)?>원</td>
							</tr>
							<tr>
								<td style='text-align:center;background-color:#F7F7F7'>투자잔액</td>
								<td style='text-align:right;'><?=number_format($event_invest_amount_total-$event_repayment_value)?>원</td>
							</tr>
						</tbody>
					</table>
				</div>

				<h3>투자내역</h3>

				<div id="invest_list_area"></div>
				<script>
				load_invest_list = function(arg1, arg2) {
					var page = arg1;
					var search_state = arg2;
					$.ajax({
						url: '/root_deposit/ajax_invest_list.php',
						type: 'GET',
						data: {page:page, search_state:search_state},
						success: function(data) {
							if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
							else if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); return; }
							else {
								$('#ajax_return_txt').val(data);
								$('#invest_list_area').empty();
								$('#invest_list_area').html(data);
							}
						},
						error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
					});
				}

				$(document).on('click', '#paging_span span.btn_paging', function() {
					var page = $(this).attr('data-page');
					var search_state = $('#search_state').val();
					load_invest_list(page, search_state);
				});

				$('#invest_status').click(function() { load_invest_list('',''); });
				<? if($tab==0) { ?>$('document').ready(function() { load_invest_list('',''); });<? } ?>
				</script>

				<p>&nbsp;</p>

				<div id="event_invest_list">
					<h3>이벤트 투자내역</h3>
					<div style="margin:4px 0 4px 0; font-size:9pt;text-align:left;">
						이삼오(2.3.5) 이벤트의 원리금은 투자일 기준 <b>익주 월요일</b>에 지급됩니다.
					</div>
					<div class="type03 mb30">
						<table class="table_invest_state">
							<tbody>
								<tr>
									<th style='width:30%;text-align:center;'>No</th>
									<th style='width:40%;text-align:center;'>상품명</th>
									<th style='width:30%;text-align:center;'>모집기간</th>
								</tr>
								<tr>
									<th style='width:30%;text-align:center;'>투자금액</th>
									<th style='width:40%;text-align:center;'>지급(예정)금액</th>
									<th style='width:30%;text-align:center;'>이자율(회)</th>
								</tr>
<?
	if($event_invest_list != null){

		foreach($event_invest_list as $RowsE) {

			//echo "<pre style='font-size:9pt'>"; print_r($RowsE); echo "</pre>";

			$event_product_open_date    = str_replace(" ","",str_replace(":","",str_replace("-","",$RowsE["open_datetime"])));   // 상점오픈 (투자시작가능)
			$event_product_invest_sdate = str_replace(" ","",str_replace(":","",str_replace("-","",$RowsE["start_datetime"])));  // 상품오픈 (투자시작가능)
			$event_product_invest_edate = str_replace(" ","",str_replace(":","",str_replace("-","",$RowsE["end_datetime"])));    // 상품종료 (투자마감)

			$event_recruit_amount      = $RowsE["recruit_amount"];
			$event_total_invest_amount = $RowsE["total_invest_amount"];
			$event_invest_end_date     = str_replace("-", "", $RowsE["invest_end_date"]);
			$event_product_state = get_product_state(
															$RowsE["recruit_period_start"],
															$RowsE["recruit_period_end"],
															$event_product_open_date,
															$event_product_invest_sdate,
															$event_product_invest_edate,
															$RowsE["state"],
															$event_recruit_amount,
															$event_total_invest_amount,
															$event_invest_end_date);

?>
								<tr>
									<td style='text-align:center;'><?=$event_invest_count?></td>
									<td style='text-align:center;'><a href="/event_invest/event_invest.php?prd_idx=<?=$RowsE['product_idx']?>"><?=$RowsE['title']?></a></td>
									<td style='text-align:center;'><?=preg_replace("/-/", ".", $RowsE['recruit_period_start'])?><br>~ <?=preg_replace("/-/", ".", $RowsE['recruit_period_end'])?></td>
								</tr>
								<tr>
									<td style='text-align:center;'><?=number_format($RowsE['amount'])?>원</td>
									<td style='text-align:center;'><?=number_format($RowsE['total_return_amount'])?>원</td>
									<td style='text-align:center;' class="rate"><?=$RowsE['invest_return']?>%</td>
								</tr>
<?
			$event_invest_count--;
		}
	}
?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
<!-- 투자 현황 끝 ------------------------------------------------------------------------------------->

<!-- 수익금 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<p>&nbsp;</p>
				<h3>수익금 현황</h3>
				<div id="interest_status_area"></div>
			</div>
			<script>
			load_repay_stats = function() {
				$.ajax ({
					url : "/root_deposit/ajax_repay_stats.php",
					type: "GET",
					data: {type:1},
					success: function(data) {
						if(data=="ERROR-DATA")       { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
						else if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); return; }
						else {
							$('#ajax_return_txt').val(data);
							$('#interest_status_area').html(data);
						}
					},
					error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
				});
			}
			$('#interest_status').click(function() { load_repay_stats(); });
			<? if($tab=='1') { ?>$(document).ready(function() { load_repay_stats(); });<? } ?>
			</script>
<!-- 수익금 현황 끝 ------------------------------------------------------------------------------------->

<!-- 예치금 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<p>&nbsp;</p>
				<h3>예치금 현황</h3>
				<div class="type03 mb30">
					<table class="tblX deposit_state">
						<tbody>
							<tr>
								<th style="width:33.34%;text-align:center">입금합계</th>
								<th style="width:33.33%;text-align:center">투자금합계</th>
								<th style="width:33.33%;text-align:center">예치금잔액</th>
							</tr>
							<tr>
								<td style="width:33.34%;text-align:center"><?=number_format($total_charge_amount)?></td>
								<td style="width:33.33%;text-align:center"><?=number_format($invest_amount_total+$event_invest_amount_total)?></td>
								<td style="width:33.33%;text-align:center"><span id="realtime_point2"><?=number_format($member['mb_point'])?></span></td>
							</tr>
							<tr>
								<th style="width:33.34%;text-align:center">미달성환불합계</th>
								<th style="width:33.33%;text-align:center">반환금합계</th>
								<th style="width:33.33%;text-align:center">출금합계</th>
							</tr>
							<tr>
								<td style="width:33.34%;text-align:center"><?=number_format($total_recruit_fail_return_price)?></td>
								<td style="width:33.33%;text-align:center"><?=number_format($total_loan_cancel_return_price)?></td>
								<td style="width:33.33%;text-align:center"><?=number_format($total_withdraw_price)?></td>
							</tr>
							<tr>
								<th style="width:33.34%;text-align:center">출금가능금액</th>
								<td colspan="3" style="width:33.34%;text-align:center"><span style="color:#FF2222;"><?=number_format($member['withdrawal_posible_amount'])?>원</span> / <?=number_format($member['mb_point'])?>원</td>
							</tr>
						</tbody>
					</table>
					<p style="font-size:12px; color:brown; text-align:center">※ "출금가능금액"은 예치금잔액중 24시간내 입금하신 예치금을<br/>제외한 금액입니다.</p>

					<div style="margin:8px 0;padding:10px 10px;border:0px solid #CCC;border-radius:2px; text-align:center; background:#9EF8FF;font-size:14px;color:#000;line-height:20px;">
						! 안전한 금융환경 조성을 위하여 <u>최종 예치금 입금일시 기준 24시간 후 출금이 가능</u>합니다.<br/>(신한은행 예치금 출금 정책)
					</div>

					<div style="margin:8px 0 8px;">
						<center><?=$withdrawal_button?></center>
					</div>

				</div>

				<h3>상세 내역</h3>
				<div id="money_status_area"></div>
				<script>
					load_point_log = function() {
						$.ajax ({
							url : "/root_deposit/ajax_point_log.php",
							type: "GET",
							success: function(data) {
								if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
								else if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); return; }
								else {
									$('#ajax_return_txt').val(data);
									$('#money_status_area').html(data);
								}
							},
							error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
						});
					}
					$('#money_status').click(function() { load_point_log(); });
					<? if($tab=='2') { ?>$(document).ready(function() { load_point_log(); });<? } ?>
				</script>
			</div>
<!-- 예치금 현황 끝 ------------------------------------------------------------------------------------->

<!-- 가상계좌 정보 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<p>&nbsp;</p>
				<div class="title"><span class="blue"><?=$member['mb_name']?></span> 님 반갑습니다.</div>
				<ul class="tmp1">
					<li style="margin-bottom:4px">헬로펀딩의 투자전용 예치금 계좌(가상계좌)입니다.</li>
					<li style="margin-bottom:4px">발급 받으신 예치금 계좌로 예치금을 충전하신 후 투자가 가능합니다.</li>
					<li style="margin-bottom:30px;color:#3366FF">고객님의 소중한 자산은 신한은행 자금 신탁 관리를 통하여 안전하게 운용됩니다.</li>
				</ul>

				<h3>가상계좌 정보</h3>
				<div class="type05 mb30">
					<table id="table_deposit_bankinfo">
						<colgroup>
							<col width='20%'>
							<col width='80%'>
						</colgroup>
						<tbody>
							<tr>
								<th>계좌번호</th>
								<td><span style="color:#153FA1"><?=$BANK[$KSNET_VACT['BANK_CODE']]?> <?=$KSNET_VACT['VR_ACCT_NO']?></span></td>
							</tr>
							<tr>
								<th>예금주</th>
								<td><span style="color:#153FA1"><?=$KSNET_VACT['CORP_NAME']?></span></td>
							</tr>
							<tr>
								<th>거래상태</th>
								<td><?=$ib_vact_status?></td>
							</tr>
						</tbody>
					</table>
					<p align='center' style="padding-top:9px">
						<?=$vact_reg_button?>
						<button type="button" onClick="location.href='<?=$bank_edit_url?>';" class="btn_green2">환급계좌 등록.변경</button>
					</p>

					<? if($_REQUEST['mode']=='debug') { ?><p align='center' style="padding-top:9px"><a href="javascript:;" id="withdrawal2" class="btn_blue">가상계좌 발급받기</a></p><? } ?>
				</div>

        <div style='height:30px;'></div>

				<h2 class="small">
				  예치금잔액 <span id="realtime_point1" class="red"><?=number_format($member['mb_point'])?></span>원<br>
				 <span style="10px;font-size:0.7em; color:#0071BC">투자 전 미리 예치금을 입금하세요.</span>
				</h2>

        <div style='height:30px;'></div>

				<h3>예치금 가이드</h3>
				<div class="box">
					<ul class="tmp2" style="color:brown">
						<li>투자수익금은 회원님이 선택하신 원리금 수취방식에 따라 예치금 또는 환급계좌로 지급됩니다.</li>
						<li>예치금 계좌(가상계좌)는 최초 1회만 발급되며, 변경은 불가능합니다.</li>
						<li>예치금 계좌 입금 반영 시간은 약 1~10분 사이입니다.</li>
						<li>예치금 입금 시 입금자명과 예치금 계좌의 회원명이 상이한 경우 헬로펀딩 관리자 승인을 통해 반영이 가능합니다.</li>
						<li>예치금 출금 신청 시 회원정보에 등록된 환급계좌로 실시간 지급됩니다.</li>
						<li>안전한 금융환경 조성을 위하여 예치금 입금일시 기준 24시간 후 출금이 가능합니다. (신한은행 예치금 출금 정책)</li>
						<li>(금융감독원 및 헬로펀딩 예치금 신탁관리사인 신한은행 보안지침 적용)</li>
					</ul>
				</div>
			</div>
<!-- 가상계좌 정보 끝 ------------------------------------------------------------------------------------->


<!-- 투자한도 및 스케쥴 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<p>&nbsp;</p>
				<div id="invest_limit_area"></div>
			</div>
			<script>
			load_invest_limit = function() {
				$.ajax({
					url : '/root_deposit/ajax_invest_limit.php',
					type: 'GET',
					success: function(data) {
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#invest_limit_area').html(data); }
					},
					error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
				});
			}
			$('#invest_limit').click(function() { load_invest_limit(); });
			<? if($tab=='4') { ?>$(document).ready(function() { load_invest_limit(); });<? } ?>
			</script>
<!-- 투자한도 및 스케쥴 끝 ------------------------------------------------------------------------------------->

		</div>

	</div>
</div>


<!-- 충천 - 예치금입금 -->
<div id="charge">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">예치금입금</div>
	<div class="con">
		<div class="title">예치금 계좌정보</div>
		<div class="type01">
			<table>
				<tbody>
				<tr>
					<td style="width:60px"><b>은행명</b></td>
					<td><?=$BANK[$KSNET_VACT['BANK_CODE']]?></td>
				</tr>
				<tr>
					<td><b>예금주</b></td>
					<td><?=$KSNET_VACT['CORP_NAME']?></td>
				</tr>
				<tr>
					<td><b>계좌번호</b></td>
					<td><?=$KSNET_VACT['VR_ACCT_NO']?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="info"><span class="green">*</span> 위 가상계좌로 투자금을 입금하시면 충전된 예치금으로 투자가 가능합니다. </div>
		<div class="title">예치금입금 가이드</div>
		<div class="box">헬로펀딩을 통해 발급된 가상계좌에 투자금을 입금하신 후 투자가 시작되는 상품에 투자를 진행하여 주시기 바랍니다.</div>
	</div>
</div>

<!-- 출금 - 예치금 출금 팝업 -->
<div id="withdraw" style="height:auto;" class="popbluetheme">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">예치금 출금</div>
	<div class="con">
		<div class="notes">출금 가능금액 <span id="realtime_point3" class="blue"><?=number_format($member['withdrawal_posible_amount'])?></span> 원</div>
		<div class="type01">
			<table id="table_withdraw">
				<tbody>
				<tr>
					<td style="width:70px"><b>출금요청액</b></td>
					<td>
						<input type="text" name="req_price" id="req_price" class="text" style="text-align:right;" placeholder="0" maxlength="15" onKeyUp="NumberFormat(this);"> 원
						<input type="<?=($mode=='debug')?'text':'hidden'?>" name="now_point" id="now_point" value="<?=$member['withdrawal_posible_amount']?>">
						<input type="hidden" name="mb_id" id="mb_id" value="<?=$member['mb_id']; ?>">
					</td>
				</tr>
				<tr>
					<td><b>은행명</b></td>
					<td><?=$BANK[$member['bank_code']]?></td>
				</tr>
				<tr>
					<td><b>계좌번호</b></td>
					<td><?=substr($member['account_num'],0,strlen($member['account_num'])-4)."****"?></td>
				</tr>
				<tr>
					<td><b>예금주</b></td>
					<td><?=$member['bank_private_name']?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="btnArea"><span id="with_btn" class="btn_big_blue">출금신청</span></div>
		<!--<div class="title">예치금출금 가이드</div>
		<div class="box">* 예치금은 <span style="color:red;font-weight:bold;">영업일 기준으로 24시 이전까지 출금 신청 시 다음 영업일 오전 12시</span>에 일괄 지급처리 됩니다.</div>-->
	</div>
</div>

<script>
function btn_event(arg) {
	if(arg=='send') {
		$('#with_btn').removeClass('btn_big_blue').addClass('btn_big_gray');
		$('#with_btn').text('전송중 >>>');
		$('#with_btn').attr('disabled', 'disabled');
	}
	else if(arg=='exit') {
		$('#with_btn').removeAttr('disabled');
		$('#with_btn').text('출금신청');
		$('#with_btn').removeClass('btn_big_gray').addClass('btn_big_blue');
	}
}

// 출금신청
$("#with_btn").click(function() {
	var req_price = $('#req_price').val();
	var now_point = $('#now_point').val();
	var mb_id     = $('#mb_id').val();

	if(req_price == '') { alert('출금요청금액을 입력해주세요.'); return }

	// 숫자단위 쉽표 제거
	req_price_len = req_price.length;
	for (i=0; i<req_price_len; i++) {
		req_price = req_price.replace(',', '');
	}

	req_price = Number(req_price);
	now_point = Number(now_point);

	if(req_price > now_point) {
		alert('요청금액이 출금가능금액보다 큽니다.\n\n(신한은행 출금정책에 의하여, 24시간내 입금하신 예치금은 만1일 경과후 출금요이 가능합니다.)');
		return;
	}

	if(req_price!='' && req_price > 0) {
		$.ajax({
			url: './withdrawal_request_proc.php',
			dataType: 'JSON',
			type: 'POST',
			data: {
				'req_price':req_price,
				'mb_id':mb_id
			},
			beforeSend: function() { btn_event('send'); },
			complete: function() { btn_event('exit'); }
		}).done(function(data) {
			if(data.result == 'SUCCESS') {
				alert('출금요청이 전송되었습니다.');
				$('#req_price').val('');
				$(location).attr('href','/deposit/deposit.php?tab=2');		// 목록갱신
				$.ajax ({
					url : "/root_deposit/ajax_point_log.php",
					type: "GET",
					success: function(data2) {
						if(data2=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); }
						else if(data2=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); location.href='/bbs/login.php'; }
						else { $('#money_status_area').html(data2); }
					}
				});
				$.unblockUI();
				return;
			}
			else {
				alert(data.message); return;
			}
		});
	}
	else {
		alert('출금 요청 금액을 입력하십시요.'); return;
	}
});
</script>


<?
// 가상계좌번호 받기 팝업 (구)
//include_once(G5_PATH . "/deposit/inc_virtual_account_form.php");
?>

<!-- 투자내역 상세보기 -->
<div id="detail"></div>

<script>
$(document).ready(function(){

	$(document).on('click', '#detail_close, #detail #no, #detail .close', function() {
		$.unblockUI();
		return false;
	});

	$('#withdraw .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#withdrawal2, #charging_dis').on('click', function() {
		$.blockUI({
			message: $('#withdraw2'),
			css: { top:'6%',left:'1%', width:'98%',border:0, cursor:'default' }
		});
	});

	$('#withdraw2 .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#charge .close').click(function() {
		$.unblockUI();
		return false;
	});

	//충전 팝업
	$('#charging').click(function() {
		$.blockUI({
			message: $('#charge'),
			css: { top:'6%', left:'1%', width:'98%', border:0, cursor:'default' }
		});
	});
	//출금
	$('#withdrawal').click(function() {
		$.blockUI({
			message: $('#withdraw'),
			css: { top:'6%', left:'1%', width:'98%', border:0, cursor:'default' }
		});
	});
});
</script>

<script type="text/javascript">
//실시간 포인트 갱신
$(document).ready(function(){
	setInterval(function() {
		$.ajax({
			url : "/root_deposit/ajax_point_check.php",
			success: function(data) {
				$('#realtime_point1,#realtime_point2').empty();
				$('#realtime_point1,#realtime_point2').append(number_format(data));
			}
		});
	}, 10*1000);
});

//출금가능금액 체크
$(document).ready(function() {
	setInterval(function() {
		$.ajax({
			url : "/root_deposit/ajax_withdrawal_amount_check.php",
			success: function(data) {
				$('#realtime_point3').html(number_format(data));
				$('#now_point').empty();
				$('#now_point').val(data);
			}
		});
	}, 10*1000);
});
</script>

<!-- 본문내용 E N D -->
<?
include_once(HF_PATH.'/_tail.php');
?>