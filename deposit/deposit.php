<?
###############################################################################
## 투자내역 (최종수정 2018.02.09 배재수)
###############################################################################
include_once('./_common.php');
include_once(G5_LIB_PATH . "/insidebank.lib.php");
include_once(G5_LIB_PATH . "/function_prc.php");
include_once("query.php");

$g5['title'] = '투자내역';
$g5['top_bn'] = "/images/mypage/sub_loanlist.jpg";
$g5['top_bn_alt'] = "대출내역 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if (!$member["mb_id"]) { alert("로그인 후 이용 가능합니다.", G5_BBS_URL."/login.php?url=" . urlencode($_SERVER['PHP_SELF']."?tab=".$tab)); }

if ($co['co_include_head'])
	@include_once($co['co_include_head']);
else
	include_once('./_head.php');

// 회원일 경우..
if($is_member)
{

	//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') { print_rr($member, 'text-align:left;font-size:12px'); }


	$gstrNdate = DATE("Y-m-d H:i:s");

	$gstrMemberId          			= $member['mb_id'];
	$gstrMemberSeq              = $member['mb_no'];				// 멤버고유값
	$gstrMemberPoint            = $member['mb_point'];		// 멤버포인트
	$gstrWithdrawlPosibleAmount = 0;											// 출금가능금액 초긱화
	$gstrVirtualAccount      		= $member['virtual_account'];
	$gstrVaBankCode        			= $member['va_bank_code'];
	$gstrVirtualAccount2        = $member['virtual_account2'];
	$gstrBankCode               = $member['bank_code'];
	$gstrAccountNum             = $member['account_num'];
	$gstrBankPrivateName        = $member['bank_private_name'];
	$gstrVaBankCode2            = $member['va_bank_code2'];
	$gstrFinnqUserId            = $member['finnq_userid'];

	// 예치금 출금
	$CONF['withdrawal_bypass'] = fn_query_result("Count",ARRAY("CNT"),deposit_query("IBAuthWidthDrWal_CNT"));

	// 예치금 잔액
	$member_deposit_point = ($gstrMemberPoint) ? $gstrMemberPoint : 0;

	// 충전금액 합계
	$total_charge_amount0 = fn_query_result("Count",ARRAY("CNT"),deposit_query("TotalChargeAhst_CNT"));

	// 충전금액 합계(신한 제3자 예치시스템 적용)
	$total_charge_amount1 = fn_query_result("Count",ARRAY("CNT"),deposit_query("TotalChargeP2P_CNT"));

	// 투자금합계
	$total_invest_amount = fn_query_result("Count",ARRAY("TSUM"),deposit_query("TotalInvest_SUM"));

	$total_invest_amount = $total_invest_amount > 0 ? number_format($total_invest_amount) : 0;


	$total_charge_amount = $total_charge_amount0 + $total_charge_amount1;

	$bank_acct_registered    = ($gstrBankCode && $gstrAccountNum && $gstrBankPrivateName) ? true : false;
	$virtual_acct_registered = ($gstrVaBankCode2 && $gstrVirtualAccount2) ? true : false;

	$tab = $_GET['tab'];  //추천인 이벤트를 통한 가입자가 자동으로 본 페이지로 리다이렉션 될때의 파라미터

	/////////////////////////////////////////* 투자현황 시작*//////////////////////////////////
	$invest_list_total = array();
	$affect_num = 0;

	$affect_num = fn_query_result("Count",ARRAY("CNT"),deposit_query("CfProductInvest_CNT"));

	if(!$page) $page = 1;
	if(!$size) $size = 5;

	if($affect_num > 0) {

		if($page > ceil($affect_num / $size)) $page = ceil($affect_num / $size);
		$start_num = ($page - 1) * $size;

		$strColumn = ARRAY("CNT","TSUM1","TSUM2","INSUM");
		$strList = fn_query_result("Count",$strColumn,deposit_query("InvestList_STATE"));

		$invest_return_count = $strList[0];
		$invest_amount_total = $strList[1];
		$repayment_value	   = $strList[2];
		$invest_return_total = $strList[3];

		// 투자잔액
		$intInvestAmountTotalSum = $invest_amount_total-$repayment_value;
	}

	//총 상환이자
	$intRepayMentSum = fn_query_result("Count",ARRAY("TSUM"),deposit_query("RepaymentInterest_SUM"));

	/* 평균수익률  */
	$invest_return_average = ($invest_return_total) ? round(($invest_return_total/$invest_return_count),2) : 0;

	UNSET($strList);
	/*이벤트 리스트 시작*/
	$strColumn = ARRAY(
						"total_invest_amount","idx","amount","member_idx","product_idx",
						"invest_state","title","invest_profit","invest_period","recruit_period_start",
						"recruit_period_end","recruit_amount","start_date","end_date","invest_return",
						"invest_usefee","open_datetime","start_datetime","end_datetime","start_hour",
						"start_minute","start_second","end_hour","end_minute","end_second",
						"state","invest_end_date","total_return_amount"
					);
	$event_invest_list = fn_query_result("List",$strColumn,deposit_query("EventInvest_List"));

	FOR($i=0;$i<COUNT($event_invest_list);$i++)
	{
		IF($event_invest_list[$i]["invest_state"] == "Y")
		{
			$event_invest_amount_total += $event_invest_list[$i]["amount"];
			$event_invest_return_total += $event_invest_list[$i]["invest_return"];

			if($event_invest_list[$i]["state"]==2)
			{
				$event_repayment_value += ($event_invest_list[$i]["amount"] + $event_invest_list[$i]['invest_profit']);  // 총 상환금액
				$event_repayment_profit_value +=  $event_invest_list[$i]['invest_profit'];  // 총 상환이자
			}
		}
		$event_invest_count++;
	}
	/*이벤트 리스트 종료*/


	/* 이벤트 투자 평균수익률  */
	$event_invest_return_average = ($event_invest_return_total) ? round(($event_invest_return_total/$event_invest_count), 2) : 0;

	/////////////////////////////////////////* 투자현황 종료*//////////////////////////////////

	/////////////////////////////////////////* 예치금 현황 및 출금 시작*//////////////////////////////////
	// 미달성환불합계

	$total_recruit_fail_return_price = 0;
	$total_loan_cancel_return_price	 = 0;
	$total_withdraw_price0           = 0;
	$total_withdraw_price1           = 0;
	$total_withdraw_price            = 0;

	$total_recruit_fail_return_price = fn_query_result("Count",ARRAY("TSUM"),deposit_query("FailReturnPrice_SUM"));

	// 반환금 합계 (대출취소)
	$total_loan_cancel_return_price = fn_query_result("Count",ARRAY("TSUM"),deposit_query("TotalLoanCancelReturnPrice_SUM"));

	// 출금합계(일반출금)
	$total_withdraw_price0 = fn_query_result("Count",ARRAY("TSUM"),deposit_query("TotalWithdrawPrice_SUM"));

	// 출금합계(원리금출금)
	$total_withdraw_price1 = fn_query_result("Count",ARRAY("TSUM"),deposit_query("TotalReturnAmount_SUM"));

	$total_withdraw_price = $total_withdraw_price0 + $total_withdraw_price1;
	/////////////////////////////////////////* 예치금 현황 및 출금 종료*//////////////////////////////////



	// 가상계좌 등록내역 (세틀뱅크)
	UNSET($strList);
	$strColumn = ARRAY("bank_cd","acct_no","cmf_nm","acct_st");
	$VACT	   = fn_query_result("CountTxt",$strColumn,deposit_query("BankVal_1"));

	// 가상계좌 등록내역 (신한)
	UNSET($strList);
	$strColumn	= ARRAY("BANK_CODE","VR_ACCT_NO","CORP_NAME","USE_FLAG");
	$KSNET_VACT = fn_query_result("CountTxt",$strColumn,deposit_query("BankVal_2"));

}
else {
	//비회원 처리
}

$bank_edit_url = "/bbs/member_confirm.php?url=".urlencode('/mypage/mypage.php#bank_edit');		// 환급계좌 등록 리다이렉션 URL

$acct_script_on = false;		// 가상계좌발급 팝업액션 스크립트

if($bank_acct_registered) {

	// 본인 가상계좌가 존재하는 경우
	if($KSNET_VACT['USE_FLAG']=='Y') {
		$ib_vact_status  = '정상';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'가상계좌는 재발급 및 정보수정을 허용하지 않습니다.\');" class="btn_gray">가상계좌 재발급</button>';

		// 특정 회원은 재발급 가능하도록 요청이 올 경우 사용
		if( in_array($member['mb_id'], array('sori9th')) ) {
			//$acct_script_on = true;
			//$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="vaOpen();" class="btn_blue">가상계좌 재발급</button>';
		}
	}
	// 본인 가상계좌가 존재하지만 사용가능여부 플래그가 닫힌 경우
	else if($KSNET_VACT['USE_FLAG']=='N') {
		$ib_vact_status = '거래불가';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'거래불가코드가 등록되었습니다.\\n고객센터로 문의하십시요.\');" class="btn_blue">가상계좌 발급받기</button>';
	}
	// 본인 가상계좌가 미존재시
	else {
		$ib_vact_status = '미발급';
		$vact_reg_button = '';
		if( in_array($member['mb_id'], array('ncmlee60')) ) {
			$acct_script_on = true;
			$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="vaOpen();" class="btn_blue">가상계좌 발급받기</button>';
		}
	}

}
else {
	$ib_vact_status = '미발급';
	$vact_reg_button = '';  //$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'원리금을 상환 받으실 환급계좌를 먼저 등록 하셔야 합니다.\');location.href=\''.$bank_edit_url.'\'" class="btn_blue_dis">가상계좌 발급받기</button>';

	// 특정 회원은 재발급 가능하도록 요청이 올 경우 사용
	if( in_array($member['mb_id'], array('sori9th')) ) {
		//$acct_script_on = true;
		//$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="vaOpen();" class="btn_blue">가상계좌 발급받기</button>';
	}

}


// ▼ 예치금 출금 버튼 동작설정▼ ---------------------------------------------------
if(!$bank_acct_registered) {
	$withdrawal_button = '<button type="button" class="btn_blue" onClick="alert(\'환급 계좌 등록 후 출금 가능합니다.\');location.href=\''.$bank_edit_url.'\';">예치금 출금</button>';
}
else if(!$virtual_acct_registered) {
	$withdrawal_button = '<button type="button" class="btn_blue" onClick="alert(\'가상 계좌 발급 후 출금 가능합니다.\');location.href=\'?tab=4\';">예치금 출금</button>';
}
else {

	if( $CONF['withdrawal_bypass'] > 0) {
		$withdrawal_button = '<button type="button" class="btn_big_blue" id="withdrawal">예치금 출금</button>';
	}
	else if( $gstrFinnqUserId!='' ) {
		$withdrawal_button = '<button type="button" class="btn_big_blue" id="withdrawal">예치금 출금</button>';
	}
	else {

		//$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="alert(\'[예치금 출금 안내]\n\n신한은행 제3자 예치금관리 시스템 적용이 완료되었으며,\n현재 신한은행 가상계좌에서 회원별 예치금 매칭이 진행중입니다.\n예치금 매칭이 완료되는 2017년 10월 16일 12시 이후 출금신청이 가능한 점 양해부탁드립니다.\');">예치금 출금</button>';

		// 원리금 배당 이력
		$REPAYLOG = fn_query_result("Count",ARRAY("CNT"),deposit_query("CfProductGive"));

		if($REPAYLOG > 0) {

			// 원리금 배당 이력이 있으면 출금승인처리 및 바로 출금 가능

			$strColumn	=	ARRAY("mb_no","account_num","auth_admin","rdate");
			$strValues  =   ARRAY($gstrMemberSeq ,$gstrAccountNum, "system",$gstrNdate);
			fn_general_query_update("save",$strColumn, $strValues, "IB_auth_withdrawal","","","",$connect);

			$withdrawal_button = '<button type="button" class="btn_big_blue" id="withdrawal">예치금 출금</button>';

		}
		else {

			$LAST_DEPOSIT = fn_query_result("CountTxt",ARRAY("tr_amt","trans_dt"),deposit_query("LastDeposit"));

			if($LAST_DEPOSIT['trans_dt']) {
				$deposit_able_time = strtotime($LAST_DEPOSIT['trans_dt']) + ($CONF['deposit_request_limit_day'] * 86400);
				if(date('YmdHi') < date('YmdHi', $deposit_able_time)) {

					$tmp_msg = "안전한 투자환경 조성을 위해 예치금 입금일시 기준 24시간 후부터 출금이 가능합니다.\\n\\n" .
										 "[".$member['mb_name']."님의 예치금 출금일시 정보]\\n" .
										 "- 최종 예치금 입금일시 : ".substr($LAST_DEPOSIT['trans_dt'], 0, 16)."\\n" .
										 "- 출금가능 일시 : ".date('Y-m-d H:i', $deposit_able_time)."\\n\\n" .
										 "출금가능 일시에 출금을 신청하여 주시기 바랍니다.\\n" .
										 "감사합니다.";
					$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="alert(\''.$tmp_msg.'\');">예치금 출금</button>';

				}
				else {
					$withdrawal_button = '<button type="button" class="btn_big_blue" id="withdrawal">예치금 출금</button>';
				}
			}
			else {
				$withdrawal_button = '<button type="button" class="btn_big_blue" id="withdrawal">예치금 출금</button>';
			}

		}

		// 전환대상자 출금 버튼 재설정
		if($member['insidebank_after_trans_target']=='1') {
			$tmp_msg = '2017년 10월 15일 18시 이전에 예치금을 보유하였으나 환급계좌가 없으셨던 분들은 신한은행 가상계좌 발급 후 신한은행으로 기존 예치금이 이관된 이후 출금이 가능합니다.\n\n(신한은행 가상계좌 발급 후 신한은행으로 예치금 이관에 소요되는 시간은  영업일 48시간 이내입니다.)';
			$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="alert(\''.$tmp_msg.'\');">예치금 출금</button>';
		}

	}

	///////////////////////////////////////////////
	// KYC 팝업 오픈 : 2022-01-01 부터 시행
	///////////////////////////////////////////////
//if( $office_connect ) {				// if( in_array($member['mb_id'], $kyc_test_member) ) {
		if($member['kyc_allow_yn'] == 'Y') {
			if( date('Y-m-d H:i:s') >= $CONF['BANK_STOP_SDATE'] && date('Y-m-d H:i:s') < $CONF['BANK_STOP_EDATE'] ) {
				$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="KYCPopup();">예치금 출금</button>';		// 신한은행 점검공지 팝업 KYCPopup 에 귀속
			}
		}
		else {
			$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="KYCPopup();">예치금 출금</button>';
		}
//}

}
// ▲ 예치금 출금 버튼 동작설정 ▲ ---------------------------------------------------


$tab = ($tab) ? $tab : 0;

// 모바일 분기
if(G5_IS_MOBILE){
	include_once('./deposit_m.php');
	return;
}
?>

<!-- 본문내용 START -->
<div id="content">
	<div class="content">

		<div class="deposit">

			<h2 class="small">
				예치금잔액 <span id="realtime_point1" class="red"><?=number_format($gstrMemberPoint)?></span>원<br/>
				<span style="10px;font-size:0.7em; color:#0071BC">투자 전 미리 예치금을 입금하세요.</span>
			</h2>

			<!-- 탭메뉴 -->
			<ul class="tab_type03">
				<li id="invest_status"   data-gubun="tab1" <?=($tab==0)?'class="on"':''?>>투자 현황</li>
				<li id="interest_status" data-gubun="tab2" <?=($tab==1)?'class="on"':''?>>수익금 현황</li>
				<li id="money_status"    data-gubun="tab3" <?=($tab==2)?'class="on"':''?>>예치금 현황 및 출금</li>
				<li id="va_info"         data-gubun="tab4" <?=($tab==3)?'class="on"':''?>>가상계좌 정보</li>
				<li id="invest_limit"    data-gubun="tab5" <?=($tab==4)?'class="on"':''?> style="display:none">투자한도</li>
				<li id="auto_invest"     data-gubun="tab6" <?=($tab==5)?'class="on"':''?> style="display:none">자동투자 설정</li>
				<li id="rec_info"        data-gubun="tab7" <?=($tab==6)?'class="on"':''?>>추천인 현황</li>
				<?php IF($member["member_type"]=="2") { ?>
				<li id="withholding"     data-gubun="tab8" <?=($tab==7)?'class="on"':''?>>원천징수영수증 신청</li>
				<?php } ?>
			</ul>
			<script>
			// 탭 기능
			$(document).ready(function(){
				$(this).addClass('on').siblings().removeClass('on');
				$('.boxArea').hide();
				$('.boxArea:eq(<?=$tab?>)').show();

				$('.tab_type03 li').click(function() {
					$(this).addClass('on').siblings().removeClass('on');
					var cur = $(this).index();
					$('.boxArea').hide();
					$('.boxArea:eq('+cur+')').show();
				});
			});
			</script>

			<!-- 투자 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box">
					<h3>투자 현황</h3>
					<div id="invest_status">
						<div class="type03">
							<table>
								<tbody>
								<tr>
									<th>구분</th>
									<th>총 투자금액</th>
									<th>총 상환금액</th>
									<th>총 상환이자</th>
									<th>투자잔액</th>
								</tr>
								<tr>
									<td>투자</td>
									<td><?=number_format($invest_amount_total)?>원</td>
									<td><?=number_format($repayment_value)?>원</td>
									<td><?=number_format($intRepayMentSum)?>원</td>
									<td><?=number_format($intInvestAmountTotalSum)?>원</td>
								</tr>
								<tr>
									<td>이벤트</td>
									<td><?=number_format($event_invest_amount_total)?>원</td>
									<td><?=number_format($event_repayment_value)?>원</td>
									<td><?=number_format($event_repayment_profit_value)?>원</td>
									<td>&nbsp;</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>

					<p>&nbsp;</p>

					<h3>투자 내역</h3>
					<div id="invest_list_area"></div>
					<script>
					load_invest_list = function(arg1, arg2) {
						var page = arg1;
						var search_state = arg2;
						$.ajax({
							url: '/deposit/ajax_invest_list.php?' + Math.floor(+ new Date() / 1000),
							type: 'GET',
							dataType: 'html',
							data: {
								mode:'<?=$_REQUEST['mode']?>',
								page:page,
								search_state:search_state
							},
							success: function(data) {
								if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
								else if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); return; }
								else {
									$('#ajax_return_txt').val(data);
									$('#invest_list_area').empty();
									$('#invest_list_area').html(data);
								}
							},
							beforeSend: function() { $('#loading').css('display','block'); },
							complete: function() { $('#loading').css('display','none'); },
							error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
							timeout: 10000
						});
					}

					$(document).on('click', '.invest_list_paging > span.btn_paging', function() {
						var page = $(this).attr('data-page');
						var search_state = $('#search_state').val();
						load_invest_list(page, search_state);
					});

					$('#invest_status').click(function() { load_invest_list('',''); });
					<? if($tab==0) { ?>$('document').ready(function() { load_invest_list('',''); });<? } ?>
					</script>

					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>

					<div style="padding:10px; font-size:10pt;color:#222;text-align:left; background-color:#E9EDF7;border:1px solid #999;border-radius:5px;">
						<ol style="margin-left:20px;line-height:16px">
							<li style="list-style-type:decimal;">
								투자수익으로 인해 발생된 세금을 국세청에 원천징수 할 때에는 원단위를 절사합니다.<br/>
								이 때 절사된 금액을 '실 지급액'에 합산하여 투자자분에게 지급하므로 실 지급액은 계산된 금액보다 클 수 있습니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								<? //투자 원금은 대출자의 원금 상환 후 영업일 5일 이내에 월이자와 함께 지급됩니다.?>
								투자 원금은 대출자의 원금 상환 후 영업일 5일 이내에 원금 상환일까지 발생한 미지급 이자와 함께 지급됩니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								이자 선지급 상품의 경우 각 회차별 지급예정일과 지급상태 표기일이 다를 수 있습니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								만기일시상환을 기준으로 표기된 회차별 이자는 조기상환 등의 이유로 변동될 수 있습니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								매월 투자원금의 0.1% 를 플랫폼 이용료로 수취합니다. (단, 면제상품은 플랫폼 이용료를 수취하지 않습니다.)
								<table style="width:97%;">
									<colgroup>
										<col style="width:14%">
										<col>
									</colgroup>
									<tr>
										<td valign="top" style="padding:2px 6px 2px 0">※ 플랫폼 이용료 산정식 :</td>
										<td valign="top" style="padding:2px 6px 2px 0">투자금액의 연 1.2%(<strong>월 0.1%</strong>) 의 금액을 365일로 나눈 금액(˚일별플랫폼이용료)에 상환회차월별 일수를 곱한 금액을 산정합니다.</td>
									</tr>
									<tr>
										<td valign="top" style="padding:2px 6px 2px 0">※ 원천징수액 산정식 :</td>
										<? if($member['member_type']=='2') { ?>
										<td valign="top" style="padding:2px 6px 2px 0">투자수익에 소득세(25%)와 주민세(2.5%가)가 추가되어 27.5%가 세금으로 산정됩니다.</td>
										<? } else if($member['member_type']=='1') { ?>
										<td valign="top" style="padding:2px 6px 2px 0">투자수익에 소득세(14%)와 주민세(1.4%가)가 추가되어 15.4%가 세금으로 산정됩니다.</td>
										<? } else { ?>
										<td style="line-height: 1.5;">
										투자수익에 소득세(14%)와 주민세(1.4%가)가 추가되어 15.4%가 세금으로 산정됩니다.<br />
										투자수익에 소득세(25%)와 주민세(2.5%가)가 추가되어 27.5%가 세금으로 산정됩니다.
										</td>
										<? } ?>
									</tr>
								</table>
							</li>
						</ol>
					</div>

				</div>
			</div>
			<!-- 투자 현황 끝 ------------------------------------------------------------------------------------->

			<!-- 수익금 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="interest_status_area"></div>
			</div>
			<script>
			load_repay_stats = function() {
				$.ajax ({
					url : '/deposit/ajax_repay_stats.php',
					type: 'GET',
					data: {type:2},
					success: function(data) {
						if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
						else if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); return; }
						else {
							$('#ajax_return_txt').val(data);
							$('#interest_status_area').html(data);
						}
					},
					beforeSend: function() { $('#loading').css('display','block'); },
					complete: function() { $('#loading').css('display','none'); },
					error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
					timeout: 10000
				});
			}
			$('#interest_status').click(function() { load_repay_stats(); });
			$(document).ready(function() { load_repay_stats(); });
			</script>
			<!-- 수익금 현황 끝 ------------------------------------------------------------------------------------->


			<!-- 예치금 현황 및 출금 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box">

					<h3>예치금 현황</h3>
					<div class="type03 mb30">
						<table>
							<tbody>
							<tr>
								<th>입금합계</th>
								<th>투자금합계</th>
								<th>예치금잔액</th>
								<th>미달성환불합계</th>
								<th>반환금합계</th>
								<th>출금합계</th>
								<th>출금가능금액</th>
							</tr>
							<tr>
								<td><?=number_format($total_charge_amount)?>원</td>
								<td><?=number_format($invest_amount_total+$event_invest_amount_total)?>원</td>
								<td><span id="realtime_point2"><?=number_format($gstrMemberPoint)?></span>원</td>
								<td><?=number_format($total_recruit_fail_return_price);?>원</td>
								<td><?=number_format($total_loan_cancel_return_price);?>원</td>
								<td><?=number_format($total_withdraw_price)?>원</td>
								<td style="color:#3366FF"><span class="realtime_withdrawal_possible_point"><?=number_format($gstrWithdrawlPosibleAmount)?></span>원</td>
							</tr>
							</tbody>
						</table>
						<p style="font-size:12px; color:brown; text-align:right">※ "출금가능금액"은 예치금잔액중 24시간내 입금하신 예치금을 제외한 금액입니다.</p>

						<div style="margin:15px 0;border:0px solid #CCC;border-radius:2px; text-align:center; background:#ccffff;font-size:14px;color:#000;line-height:30px;">
							<<< 안전한 금융환경 조성을 위하여 <u>최종 예치금 입금일시 기준 24시간 후 출금이 가능</u>합니다. (신한은행 예치금 출금 정책) >>>
						</div>

						<? /******* 출금버튼 **********/ ?>
						<div style="margin:8px 0 8px;">
							<center><?=$withdrawal_button?></center>
						</div>

					</div>

					<h3>상세내역</h3>
					<div class="type03" id="money_status_area"></div>
					<script>
					load_point_log = function(arg1) {
						var page = arg1;
						$.ajax ({
							url : '/deposit/ajax_point_log.php?' + Math.floor(+ new Date() / 1000),
							type: 'GET',
							dataType: 'html',
							data: {page:page},
							success: function(data) {
								if(data=='ERROR-DATA') { alert('시스템 에러입니다. 관리자에 문의해주세요.'); return; }
								else if(data=='ERROR-LOGIN') { alert('로그인후 이용 가능 합니다.'); return; }
								else {
									$('#ajax_return_txt').val(data);
									$('#money_status_area').html(data);
								}
							},
							beforeSend: function() { $('#loading').css('display','block'); },
							complete: function() { $('#loading').css('display','none'); },
							error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
							timeout: 5000
						});
					}

					$(document).on('click', '.point_log_paging > span.btn_paging', function() {
						var page = $(this).attr('data-page');
						load_point_log(page, search_state);
					});

					$('#money_status').click(function() { load_point_log(); });
					<? if($tab=='2') { ?>$(document).ready(function() { load_point_log(); });<? } ?>
					</script>
				</div>
			</div>
			<!-- 예치금 현황 및 출금 끝 ------------------------------------------------------------------------------------->

			<!-- 가상계좌정보 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box">
					<div class="title"><span class="blue"><?=$member['mb_name']?></span> 님 반갑습니다.</div>
					* 헬로펀딩의 투자전용 예치금 계좌(가상계좌)입니다.<br/>
					* 발급 받으신 예치금 계좌로 예치금을 충전하신 후 투자가 가능합니다.
					<p>&nbsp;</p>

					<h3>신한은행 가상계좌 정보 <span style="color:#3366FF">(고객님의 소중한 자산은 신한은행의 자금 신탁 관리를 통하여 안전하게 운용됩니다.)</span></h3>
					<div class="type05 mb30">
						<table>
							<colgroup>
								<col width='20%'>
								<col width='80%'>
							</colgroup>
							<tbody>
							<tr>
								<th>계좌번호</th>
								<td style="text-align:left"><span style="color:#153FA1"><?=$BANK[$KSNET_VACT['BANK_CODE']]?> &nbsp; <?=$KSNET_VACT['VR_ACCT_NO']?> &nbsp; <?=$KSNET_VACT['CORP_NAME']?></span></td>
							</tr>
							<tr>
								<th>거래상태</th>
								<td style="text-align:left"><?=$ib_vact_status?></td>
							</tr>
							</tbody>
						</table>
						<p align='center' style="padding-top:9px">
							<?=$vact_reg_button?>
							<? if(false) { ?><!--<button type="button" onClick="location.href='<?=$bank_edit_url?>';" class="btn_green2">환급계좌 등록.변경</button>--><? } ?>
						</p>
						<? if($_REQUEST['mode']=='debug') { ?><p align='center' style="padding-top:9px"><a href="javascript:;" id="withdrawal2" class="btn_blue">가상계좌 발급받기</a></p><? } ?>
					</div>

<!--
					<h3 style="opacity:0.6">가상계좌 정보 (구)</h3>
					<div class="type05 mb30" style="opacity:0.6">
						<table>
							<colgroup>
								<col width='20%'>
								<col width='80%'>
							</colgroup>
							<tbody>
								<tr>
									<th>계좌번호</th>
									<td style="text-align:left"><?=$BANK[$VACT['bank_cd']]?> &nbsp; <?=$VACT['acct_no']?> &nbsp; <?=$VACT['cmf_nm']?></td>
								</tr>
								<tr>
									<th>거래상태</th>
									<td style="text-align:left">거래불가</td>
								</tr>
							</tbody>
						</table>
					</div>
//-->

					<div style='height:20px;'></div>

					<h3>예치금 가이드</h3>
					<div class="guide_box">
						* 투자수익금은 회원님이 선택하신 원리금 수취방식에 따라 예치금 또는 환급계좌로 지급됩니다.<br/>
						* 예치금 계좌(가상계좌)는 최초 1회만 발급되며, 변경은 불가능합니다.<br/>
						* 예치금 계좌 입금 반영 시간은 약 1~10분 사이입니다.<br/>
						* 예치금 입금 시 입금자명과 예치금 계좌의 회원명이 상이한 경우 헬로펀딩 관리자 승인을 통해 반영이 가능합니다.<br/>
						* 예치금 출금 신청 시 회원정보에 등록된 환급계좌로 실시간 지급됩니다.<br/>
						* 안전한 금융환경 조성을 위하여 예치금 입금일시 기준 24시간 후 출금이 가능합니다. (신한은행 예치금 출금 정책)<br/>
						&nbsp;&nbsp;(금융감독원 및 헬로펀딩 예치금 신탁관리사인 신한은행 보안지침 적용)
						<!-- * 23:30 ~ 00:30분 사이에는 은행망 점검 시간으로 거래가 불가할 수 있습니다.<br/> -->
					</div>
				</div>

			</div>
			<!-- 가상계좌정보 끝 ------------------------------------------------------------------------------------->

			<!-- 투자한도 및 스케쥴 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="invest_limit_area"></div>
			</div>
			<script>
			load_invest_limit = function() {
				$.ajax({
					url : './ajax_invest_limit.php',
					type: 'GET',
					cache: false,
					success: function(data) {
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#invest_limit_area').html(data); }
					},
					error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
					timeout : 5000
				});
			}
			$('#invest_limit').click(function() { load_invest_limit(); });
			<? if($tab=='4') { ?>$(document).ready(function() { load_invest_limit(); });<? } ?>
			</script>
			<!-- 투자한도 및 스케쥴 끝 ------------------------------------------------------------------------------------->

			<!-- 자동투자 설정 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="auto_invest_area"></div>
			</div>
			<? //if ($gstrMemberId=="test9898" or 2>1) include_once(G5_PATH.'/auto_invest/auto_inv_popup.php'); ?>
			<?
			$auto_inv_dir = "auto_invest";
			include_once(G5_PATH.'/auto_invest/auto_inv_popup.php');
			?>
			<script>
			auto_invest_config = function(){
				$.ajax({
					url : '/<?=$auto_inv_dir?>/ajax_auto_invest_config2.php',
					type: 'POST',
					cache: false,
					success: function(data) {
						$('#ajax_return_txt').val(data);
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#auto_invest_area').html(data); }
					},
					error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
				});
			}
			$('#auto_invest').click(function(){ auto_invest_config(); });
			<? if($tab==5) { ?>$(document).ready(function(){ auto_invest_config(); });<? } ?>
			</script>
			<!-- 자동투자 설정 끝 ------------------------------------------------------------------------------------->

			<!-- 추천인 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="recommender_area"></div>
			</div>
			<script>
			getRecommender = function() {
				$.ajax({
					url : './ajax_recommender.php',
					type: 'GET',
					success: function(data) {
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#recommender_area').html(data); }
					},
					error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
					timeout : 5000
				});
			}
			$('#rec_info').click(function() { getRecommender(); });
			<? if($tab=='6') { ?>$(document).ready(function() { getRecommender(); });<? } ?>
			</script>
			<!-- 추천인 현황 끝 ------------------------------------------------------------------------------------->

			<?php IF($member["member_type"]=="2") { ?>
			<!-- 원천징수 영수증 신청//-->
			<div class="boxArea">
				<div class="box" id="withholding_area"></div>
			</div>
			<script>
			withholding = function() {
				$.ajax({
					url : './ajax_withholding.php',
					type: 'GET',
					cache: false,
					success: function(data) {
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#withholding_area').html(data); }
					},
					error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
					timeout : 5000
				});
			}
			$('#withholding').click(function() { withholding(); });
			<? if($tab=='7') { ?>$(document).ready(function() { withholding(); });<? } ?>
			</script>
			<!-- 원천징수 영수증 신청 끝 //-->
			<?php } ?>
		</div>

	</div>
</div>

<!-- 충천 - 예치금입금 팝업 -->
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
		<div class="notes">출금 가능금액 <span class="realtime_withdrawal_possible_point blue"><?=number_format($gstrWithdrawlPosibleAmount)?></span> 원</div>
		<div class="type01">
			<table>
				<tbody>
				<tr>
					<td style="width:70px"><b>출금요청액</b></td>
					<td>
						<input type="text" name="req_price" id="req_price" class="text" style="text-align:right;" placeholder="0" maxlength="15" onKeyUp="NumberFormat(this);" autocomplete="off"> 원
						<input type="<?=($mode=='debug')?'text':'hidden'?>" name="now_point" id="now_point" value="<?=$gstrWithdrawlPosibleAmount?>">
						<input type="hidden" name="mb_id" id="mb_id" value="<?=$gstrMemberId?>">
					</td>
				</tr>
				<tr>
					<td><b>계좌번호</b></td>
					<td><?=$BANK[$gstrBankCode]?> <?=substr($gstrAccountNum,0,strlen($gstrAccountNum)-4)."****"?></td>
				</tr>
				<tr>
					<td><b>예금주</b></td>
					<td><?=$gstrBankPrivateName?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="btnArea">
			<button type="button" id="with_btn" class="btn_big_blue">출금신청</button>
		</div>
		<!--<div class="title">예치금출금 가이드</div>
		<div class="box">* 예치금은 <span style="color:red;font-weight:bold;">영업일 기준으로 24시 이전까지 출금 신청 시 다음 영업일 오전 12시</span>에<br/> 일괄 지급처리 됩니다.</div>//-->
	</div>
</div>
<br/><br/>

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
	var mb_id	    = $('#mb_id').val();

	if(req_price == '') { alert('출금요청금액을 입력해주세요.'); return; }

	// 숫자단위 쉽표 제거
	req_price_len = req_price.length;
	for (i=0; i<req_price_len; i++) {
		req_price = req_price.replace(',', '');
	}

	req_price = Number(req_price);
	now_point = Number(now_point);

	if(req_price > now_point) {
		alert('요청금액이 출금가능금액보다 큽니다.\n\n(신한은행 출금정책에 의하여, 24시간내 입금하신 예치금은 만1일 경과후 출금 가능합니다.)');
		return;
	}

	if(req_price!='' && req_price > 0) {
		$.ajax({
			url: 'withdrawal_request_proc.php',
			dataType: 'JSON',
			type: 'POST',
			data: {
				'req_price':req_price,
				'mb_id':mb_id
			},
			cache: false,
			beforeSend: function() { btn_event('send'); },
			complete: function() { btn_event('exit'); }
		}).done(function(data) {
			if(data.result == 'SUCCESS') {
				alert('출금요청이 전송되었습니다.');
				$('#req_price').val('');
				$(location).attr('href','/deposit/deposit.php?tab=2');		// 목록갱신
				$.ajax ({
					url : "/deposit/ajax_point_log.php",
					type: "GET",
					cache: false,
					success: function(data2) {
						if(data2=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); }
						else if(data2=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); location.href='/bbs/login.php'; }
						else { $('#money_status_area').html(data2); }
					}
				});
				$.unblockUI();
				return;
			}
			else if(data.result == 'BANK_STOP') {
				$.unblockUI();
				setTimeout(function() { alert(data.message); }, 0.35*1000);
				return;
			}
			else if(data.result == "KYC_ING" || data.result == "KYC_START") { KYCPopup(); return; }
			else if(data.result == "ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); location.href='/bbs/login.php'; }
			else { alert(data.message); return; }
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

	$(document).on('click', '#detail #no, #detail .close', function() {
		$.unblockUI();
		return false;
	});

	// 원리금 수취증서 프린트
	$('.certificate_print_btn').click(function() {
		var url = '/deposit/principal_interest_certificate.php?idx='+$(this).attr("data-idx");
		popup_window(url, 'certificate', 'width=936,height=768,left=0,top=0,scrolling=no');
	});

	$(document).on('click','#detail #no, #detail .close',function(){
		$.unblockUI();
		return false;
	});

	$('#detail #no, #detail .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#withdraw .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#withdrawal2, #charging_dis').click(function() {
		$.blockUI({
			message: $('#withdraw2'),
			css: { border:'0', cursor:'default', top:'16%',left:'33%',width:'605px', position:'fixed' }
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


	//충전
	$('#charging').click(function() {
		$.blockUI({
			message: $('#charge'),
			css: { border:'0', cursor:'default', top:'16%',left:'33%',width:'605px', position:'fixed' }
		});
	});

	//출금
	$('#withdrawal').click(function() {
<?
/*
$imsi_sql = "SELECT count(idx) cnt1 FROM cf_product_invest WHERE product_idx='3194' AND member_idx='$member[mb_no]' AND invest_state='Y'";
$imsi_res = sql_query($imsi_sql);
$imsi_row = sql_fetch_array($imsi_res);
if ($imsi_row['cnt1']>0) {
?>
	//alert("헬로펀딩 이자 정산으로 출금처리가 지연되고 있어 잠시 후 출금요청 부탁드립니다.");
	alert("출금 시스템 일시 점검중입니다. 잠시 후 다시 시도해주세요.");
	return;
<?
}
*/
?>
		realtime_withdrawal_amount_check();
		$.blockUI({
			message: $('#withdraw'),
			css: { border:'0', cursor:'default', top:'16%',left:'33%',width:'605px', position:'fixed' }
		});
	});

});
</script>

<script type="text/javascript">
// 포인트 갱신
function realtime_point_check() {
	$.ajax({
		url : '<?=API_URL?>/deposit/ajax_point_check.php?' + Math.floor(+ new Date() / 1000),
		type: 'post',
		data:{mb_id:'<?=$member['mb_id']?>'},
		success: function(data) {
			$('#realtime_point1,#realtime_point2').empty();
			$('#realtime_point1,#realtime_point2').append(number_format(data));
		},
		error: function(jqXHR, textStatus, errorThrown) { <?if($office_connect){?>console.log(textStatus);<?}?> },
		timeout: 10000
	});
}
$(document).ready(function() {
	setTimeout(realtime_point_check, 5*1000);
	setInterval(realtime_point_check, 15*1000);
});


// 출금가능금액 체크
function realtime_withdrawal_amount_check() {
	$.ajax({
		url: '<?=API_URL?>/deposit/ajax_withdrawal_amount_check.php?' + Math.floor(+ new Date() / 1000),
		type: 'post',
		data: {mb_no:'<?=$member['mb_no']?>'},
		dataType: 'json',
		success: function(data) {
			if(data.result=='success') {
				$('.realtime_withdrawal_possible_point').html(number_format(data.wp_amt));
				$('#now_point').empty();
				$('#now_point').val(data.wp_amt);
			}
			else {
				location.reload();
			}
		},
		beforeSend: function() { $('#loading').css('display','block'); },
		complete: function() { $('#loading').css('display','none'); },
		timeout: 20000
	});
}

$('#money_status').on('click', function() {
	setTimeout(realtime_withdrawal_amount_check, 0.3*1000);
});

<? if( preg_match("/\?tab\=2/", $_SERVER['REQUEST_URI']) ) { ?>
$(document).ready(function() {
	setTimeout(realtime_withdrawal_amount_check, 0.3*1000);
});
<? } ?>

</script>

<!-- 본문내용 E N D -->

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
