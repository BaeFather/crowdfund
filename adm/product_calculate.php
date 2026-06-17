<?
set_time_limit(0);
include_once('./_common.php');

ini_set('memory_limit','256M');


$sub_menu = '700100';
$g5['title'] = $menu['menu700'][1][1] . " > 정산상세";

include_once('./admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
$is_control_user = ( in_array($_SESSION['ss_mb_id'], array('admin_sori9th','admin_romrom','admin_andan77','admin_yr4msp','admin_hellosiesta','admin_sundol4')) ) ? true : false;

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출




$prd_idx            = trim($_REQUEST['idx']);											// 상품번호기준
$mb_id              = trim($_REQUEST['mb_id']);										// 특정 투자자만 조회 할 경우
//$invest_period      = trim($_REQUEST['invest_period']);						// (시뮬레이션용) 투자개월수
//$loan_start_date    = trim($_REQUEST['loan_start_date']);					// (시뮬레이션용) 투자시작일
//$loan_end_date      = trim($_REQUEST['loan_end_date']);						// (시뮬레이션용) 투자만기일
//$invest_usefee      = trim($_REQUEST['invest_usefee']);						// (시뮬레이션용) 플랫폼이용료율
//$invest_usefee_type = trim($_REQUEST['invest_usefee_type']);			// (시뮬레이션용) 플랫폼이용료 징수방식
//$turn               = trim($_REQUEST['turn']);


$INV_ARR   = repayCalculation($prd_idx, $mb_id);

$INI       = $INV_ARR['INI'];
$PRDT      = $INV_ARR['PRDT'];
$LOANER    = $INV_ARR['LOANER'];
$INVEST    = $INV_ARR['INVEST'];
$MTOTAL_INVEST_SUM = $INV_ARR['MTOTAL_INVEST_SUM'];
$REPAY     = $INV_ARR['REPAY'];
$REPAY_SUM = $INV_ARR['REPAY_SUM'];
$PAIED_SUM = $INV_ARR['PAIED_SUM'];

//print_rr($REPAY,'font-size:12px;line-height:14px;'); exit;


$ib_trust = ($PRDT['ib_trust']=='Y' && $PRDT['ib_product_regist']=='Y') ? true : false;

$date  = G5_TIME_YMDHIS;
$state = '';
if($PRDT['state']) {
	if($PRDT['state']=='1') { $state = '이자상환중'; $state_code = '2'; }
	if($PRDT['state']=='2') { $state = '상품마감'; }
	if($PRDT['state']=='4') { $state = '부실'; }
	if($PRDT['state']=='5') { $state = '중도상환'; $state_code = '2'; }
	if($PRDT['state']=='6') { $state = '대출계약취소(기표전)'; }
	if($PRDT['state']=='7') { $state = '대출계약취소(기표후)'; }
	if($PRDT['state']=='8') { $state = '연체'; }
	if($PRDT['state']=='9') { $state = '부도(상환불가)'; }
}
else {
	if ($PRDT['open_datetime'] > $date) { $state = '투자대기중'; }
	if ($PRDT['start_datetime'] < $date && $PRDT['end_datetime'] > $date && $PRDT['invest_end_date'] == '') { $state = '투자모집중'; }
	if ($PRDT['end_datetime'] < $date && $PRDT['invest_end_date'] == '') { $state = '투자금 모집실패'; $state_code = '3'; }
	if ($PRDT['invest_end_date'] != '' && $PRDT['state'] == '') { $state = '대기중'; $state_code = '1'; }
}

//$PRDT['state']='1';

$loan_date_range = ($PRDT['state']=='') ? $PRDT['invest_period'].'개월' : preg_replace('/-/', '.', $PRDT['loan_start_date']).' ~ '.preg_replace('/-/', '.', $INI['loan_end_date']);
//$loan_date_range = ($PRDT['loan_start_date']=="" || $PRDT['loan_start_date']=="0000-00-00") ? '' : preg_replace('/-/', '.', $PRDT['loan_start_date']).' ~ '.preg_replace('/-/', '.', $INI['loan_end_date']);

$loaner = ($LOANER['member_type']=='2') ? $LOANER['mb_co_name'] : $LOANER['mb_name'];

//대출정보 - 누적납입
$ROW = sql_fetch("SELECT SUM(invest_amount) AS sum_invest_amount FROM cf_product_give WHERE product_idx='$prd_idx'");
$LOAN['plus_loan_interest'] = $ROW['sum_invest_amount'];

//대출정보 - 당월납입이자
$ROW2 = sql_fetch("SELECT SUM(invest_amount) AS sum_invest_amount FROM cf_product_give WHERE product_idx='$prd_idx' AND LEFT(date, 7)='".date('Y-m')."'");
$LOAN['month_loan_interest'] = $ROW2['sum_invest_amount'];


if($ib_trust && $PRDT['invest_end_date']) {

	// 투자자수와 신한정상등록투자자수 비교
	$ROW3 = sql_fetch("SELECT COUNT(idx) AS cnt_idx FROM cf_product_invest WHERE product_idx='$prd_idx' AND invest_state='Y' AND ib_regist='1'");

	if($ROW3['cnt_idx'] > 0) {
		if($PRDT['invest_count']==$ROW3['cnt_idx']) {
			$ib_investor_regist_button = '<button type="button" class="btn btn-gray">등록완료</button>';
		}
		else {
			$none_regist_count = $PRDT['invest_count'] - $ROW3['cnt_idx'];
			$ib_investor_regist_button = '<button type="button" id="ib_investor_reg_btn" class="btn btn-success">추가등록실행('.$none_regist_count.'건)</button>';
		}
	}
	else {
		$ib_investor_regist_button = '<button type="button" id="ib_investor_reg_btn" class="btn btn-success">등록실행</button>';
	}


	//대출정보 - 대출금 지급 처리 (펌뱅킹 대출금 입금 통지내역 정의 테이블 조회)
	$ROW4 = sql_fetch("SELECT SUM(DCA_IP_AMT) AS SUM_DCA_IP_AMT FROM IB_FB_P2P_DC_IP WHERE DC_NB='$prd_idx' AND EXEC_YN='Y' AND ERR_CD='00000000'");
	if($ROW4['SUM_DCA_IP_AMT']) {
		$dc_ip_result_button = ($ROW4['SUM_DCA_IP_AMT']==$PRDT['recruit_amount']) ? '<button type="button" class="btn btn-gray">지급완료</button>' : '<button type="button" class="btn btn-danger">금액오류</button>';
	}

}

?>

<style>
.table th.border_r { border-right:1px solid #999; }
.table td.border_r { border-right:1px solid #999; }
</style>

<div class="row" style="width:99.9%;">
	<div class="col-lg-12">
		<form name='form1' method="post" action="./product_calculate_proc.php" class="form-horizontal">
		<input type="hidden" name="action" value="calculate_state_update">
		<input type="hidden" name="idx"    value="<?=$PRDT['idx']?>">
		<input type="hidden" name="state"  value="<?=$state_code?>">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<h3>상품 정보 &nbsp;
					<button type="button" onClick="location.href='./product/product_form.php?idx=<?=$prd_idx?>';" class="btn btn-default">상품상세정보</button>
					<? if($PRDT['state']=="" || $PRDT['state']=='1') { ?><button type="button" id="simulation_btn" onClick="location.href='invest_repay_simulation.php?idx=<?=$idx?>'" class="btn btn-default">투자시뮬레이션</button><? } ?>
					<button type="button" onClick="window.open('/adm/product_calculate_pop.php?idx=<?php ECHO $idx;?>','product_calculate_pop','width=500,height=550');" class="btn btn-default">대출정보</button>
				</h3>
				<table class="table table-striped table-bordered table-hover">
					<colgroup>
						<col width="%">
						<col width="20%">
						<col width="20%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr style="background-color:#F9F9EF">
							<th class="text-center">대출상품명</th>
							<th class="text-center">총대출금액</th>
							<th class="text-center">대출이자</th>
							<th class="text-center">기간</th>
							<th class="text-center">지급회차</th>
							<th class="text-center">대출자 성명</th>
							<th class="text-center">대출자 연락처</th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td><?=$PRDT['title']?></td>
							<td><?=price_cutting($PRDT['recruit_amount'])?>원 (￦<?=number_format($PRDT['recruit_amount'])?>)</td>
							<td><span style="font-size:10px">(연)</span><?=sprintf('%.2f', $PRDT['loan_interest_rate'])?>%</td>
							<td><?=$loan_date_range?></td>
							<td><?=$INI['max_paied_turn']?> / <?=number_format($INI['repay_count'])?></td>
							<td><?=$loaner?></td>
							<td><?=$LOANER['mb_hp']?></td>
						</tr>
					</tbody>
				</table>

				<h3>실행.상환 설정 <? if($ib_trust) { ?><span style="margin-left:2px;padding:2px 6px;font-size:12px;border-radius:10px;color:#fff;background-color:blue">예치금신탁</span><? } ?></h3>
				<table class="table table-striped table-bordered table-hover">
					<colgroup>
						<col style="width:12.5%">
						<col style="width:12.5%">
						<col style="width:12.5%">
						<col style="width:12.5%">
						<col style="width:12.5%">
						<col style="width:12.5%">
						<col style="width:12.5%">
						<col style="width:12.5%">
					</colgroup>
					<thead>
						<tr style="background-color:#F9F9EF">
							<th class="text-center">대출계약취소</th>
							<th class="text-center">투자자등록(신한)</th>
							<th class="text-center">대출실행</th>
							<th class="text-center">대출금지급처리</th>
							<th class="text-center">원리금 상환.지급 완료</th>
							<th class="text-center">중도상환</th>
							<th class="text-center">부실</th>
							<th class="text-center">투자금반환</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding:4px;text-align:center;" alt="대출계약취소">
<?
	// 대출계약취소 버튼 설정
	if($PRDT['state']=='6') {
		$button1 = '<button type="button" class="btn btn-gray">대출계약취소</button>';
	}
	else if($PRDT['state']=='7') {
		$button1 = '<button type="button" class="btn btn-gray">대출계약취소(기표후)</button>';
	}
	else if($PRDT['state']=='') {
		//if($PRDT['invest_end_date']) {
			$button1 = '<button type="button" id="loan_cancel_btn" class="btn btn-danger">대출계약취소</button>';
		//}
	}
	else {
		$button1 = '<button type="button" onClick="alert(\'대출실행처리 완료되었거나, 완료처리 이력이 있는 대출건은 취소가 불가 합니다.\');" class="btn btn-gray">대출계약취소</button>';
	}
	echo $button1;
?>
							</td>
							<td style="padding:4px;text-align:center;" alt="투자자등록(신한)">
								<?=$ib_investor_regist_button?>
							</td>
							<td style="padding:4px;text-align:center;" alt="대출실행">
								<? if( $PRDT['state'] >= 1) { ?>
								<button type="button" class="btn btn-gray">실행완료</button>
								<? } else if($PRDT['state']=='' && $PRDT['invest_end_date']) { ?>
								<ul style="list-style:none;margin:0;padding:0;display:inline-block">
									<li style="float:left;"><input type="text" name="date" value="<?=G5_TIME_YMD?>" placeholder="일자선택" class="form-control datepicker" style="text-align:center;width:100px" required readonly></li>
									<li style="float:left;margin-left:4px"><button type="button" id="loan_start_btn" class="btn btn-success">대출실행</button></li>
								</ul>
								<? } ?>
							</td>
							<td style="padding:4px;text-align:center;" alt="대출지급처리"><?=$dc_ip_result_button?></td>
							<td style="padding:4px;text-align:center;" alt="대출정상종료">
<?
	// 대출정상종료 버튼 설정
	if($PRDT['state']=='1') {
		if(G5_TIME_YMD < $PRDT['loan_end_date_orig']) {
			$button5 = '<button type="button" onClick="alert(\'대출실행시 설정된 대출 만료일에만 정상종료 가능합니다.\');" class="btn btn-default">대출정상종료</button>';
		}
		else if(G5_TIME_YMD >= $PRDT['loan_end_date']) {
			$button5 = '<button type="button" id="principal_repay_btn" class="btn btn-success">대출정상종료</button>';
		}
	}
	else if($PRDT['state']=='2') {
		$button5 = '<button type="button" class="btn btn-gray">대출정상종료</button>';
	}
	echo $button5;
?>
							</td>
							<td style="padding:4px;text-align:center;" alt="중도상환">
								<? if($PRDT['state']=='5') { ?>
								<button type="button" class="btn btn-gray">중도상환</button>
								<? } else if($PRDT['state']=='1') { ?>
								<ul style="list-style:none;margin:0;padding:0;display:inline-block;">
									<li style="float:left;width:60%;margin:0 0 4px;"><input type="text" id="loan_end_date" name="loan_end_date" value="<?=$PRDT['loan_end_date']?>" placeholder="일자선택" class="form-control datepicker" style="text-align:center;" required readonly></li>
									<li style="float:right;width:39%;"><button type="button" id="early_repay_date_reg_btn" class="btn btn-warning" style="margin:0;width:100%;">일자변경</button></li>
									<li style="width:100%;"><button type="button" id="early_repay_btn" class="btn btn-danger" style="margin:0;width:100%;">중도상환</button></li>
								</ul>
								<? } ?>
							</td>
							<td style="padding:4px;text-align:center;" alt="부실">
<?
	// 부실 처리 버튼 설정
	if($PRDT['state']=='1') {
		$button7 = '<button type="button" id="bad_loan_btn" class="btn btn-danger">부실</button>';
	}
	else if($PRDT['state']=='4') {
		$button7 = '<button type="button" class="btn btn-gray">부실</button>';
	}
	echo $button7;
?>
							</td>
							<td style="padding:4px;text-align:center;" alt="투자금반환">
<?
	// 투자금반환 버튼 설정
	if($PRDT['state']=='6') {
		$button8 = '<button type="button" class="btn btn-gray">투자금반환</button>';
	}
	else if($PRDT['state']=='') {
		if($PRDT['end_datetime'] < $date && $PRDT['invest_end_date']=='') $button8 = '<button type="button" id="refund_btn" class="btn btn-danger">투자금반환</button>';
	}
	echo $button8;
?>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
		</form>

		<script>
		// 투자자 등록
		$('#ib_investor_reg_btn').click(function() {
			if(confirm('「신한은행 제3자 예치금 관리시스템」으로 투자자 등록 전문을 발송 하시겠습니까?')) {
				$('#ajax_return_txt_zone').css('display','block');
				$.ajax({
					url : "product_calculate_proc.php",
					type: "POST",
					data:{ action:'ib_investor_regist', idx:<?=$prd_idx?> },
					success:function(data){
						$('#ajax_return_txt').val(data);
					},
					beforeSend: function() { loading('on'); },
					complete: function() { loading('off'); },
					error: function () {
						alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
					}
				});
			}
		});

		// 대출취소
		$('#loan_cancel_btn').click(function() {
			if(confirm('대출계약취소 처리 하시겠습니까?<? if($ib_trust){ ?>\n\n본 상품에 대한 모든 투자금이 반환 처리 되며,\n실행 후에는「신한은행 제3자 예치금 관리시스템」과의 재연계가 절대불가 하므로 신중하게 결정하십시요.<? } ?>')) {
				document.form1.state.value='6';
				document.form1.submit();
			}
		});

		// 대출실행
		$('#loan_start_btn').click(function() {
			if(document.form1.date.value=='') { alert('대출실행일자를 입력하십시요.'); document.form1.date.focus(); }
			else {
				if(confirm('대출을 실행 하시겠습니까?<? if($ib_trust){ ?>\n\n「신한은행 제3자 예치금 관리시스템」과의 연계가 진행되므로 신중하게 결정하십시요.<? } ?>')) {
					document.form1.submit();
				}
			}
		});

		// 대출정상종료
		$('#principal_repay_btn').click(function() {
			if(confirm('대출정상종료 처리 하시겠습니까?<? if($ib_trust){ ?>\n\n실행 후에는「신한은행 제3자 예치금 관리시스템」과의 재연계가 절대불가 하므로 신중하게 결정하십시요.<? } ?>')) {
				document.form1.submit();
			}
		});

		// 중도상환 처리를 위한 대출종료일자 변경처리
		$('#early_repay_date_reg_btn').click(function() {
			if($('#loan_end_date').val()=='') { alert('대출종료일(중도상환일)을 입력하십시요.');$('#loan_end_date').focus(); }
			else {
				if(confirm('대출종료일자를 변경하시겠습니까?\n\n본 대출건에 대한 정산내역이 변경되므로 신중하게 결정하십시요.')) {
					$.ajax({
						url : "product_calculate_proc.php",
						type: "POST",
						dataType: "json",
						data:{
							idx: <?=$PRDT['idx']?>,
							action: 'repay_date_change',
							loan_end_date: $('#loan_end_date').val()
						},
						success:function(data){
							$('#ajax_return_txt').val(data.result);
							if(data.result=='SUCCESS') {
								window.location.reload();
							}
							else {
								alert(data.message);
							}
						},
						error: function () {
							alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
						}
					});
				}
			}
		});


		// 중도상환
		$('#early_repay_btn').click(function() {
			if($('#loan_end_date').val()=='') { alert('중도상환 일자를 입력하십시요.');$('#loan_end_date').focus(); }
			else {
				if(confirm('중도상환 처리 하시겠습니까?\n\n - 중도상환 처리는 은 모든 이자 및 원금 지급을 완료한 후 최종적으로 요청하여야 합니다. 원리금 지급 여부를 반드시 확인하십시요.<? if($ib_trust){ ?>\n\n - 실행 후에는「신한은행 제3자 예치금 관리시스템」과의 재연계가 절대불가 하므로 신중하게 결정하십시요.<? } ?>')) {
					document.form1.state.value='5';
					document.form1.submit();
				}
			}
		});

		// 부실
		$('#bad_loan_btn').click(function() {
			if(confirm('부실 처리 하시겠습니까?<? if($ib_trust){ ?>\n\n실행 후에는「신한은행 제3자 예치금 관리시스템」과의 재연계가 절대불가 하므로 신중하게 결정하십시요.<? } ?>')) {
				document.form1.state.value='4';
				document.form1.submit();
			}
		});

		// 예치금반환
		$('#refund_btn').click(function() {
			if(confirm('투자금반환 처리 하시겠습니까?<? if($ib_trust){ ?>\n\n본 상품에 대한 모든 투자금이 반환 처리 되며,\n실행 후에는「신한은행 제3자 예치금 관리시스템」과의 재연계가 절대불가 하므로 신중하게 결정하십시요.<? } ?>')) {
				document.form1.submit();
			}
		});
		</script>

		<div class="col-lg-6">
			* 소수점이하 절사 처리된 데이터 입니다.
			<div class="panel panel-primary">
				<div class="panel-heading">대출 정보</div>
				<div class="panel-body">
					<div class="dataTable_wrapper">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">대출금액</th>
									<th class="text-center">전체이자</th>
									<th class="text-center">납입이자(누적)</th>
									<th class="text-center">납입이자(당월)</th>
									<th class="text-center">연이자율</th>
									<th class="text-center">대출기간</th>
									<th class="text-center">이자계산일수</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td align="center"><?=number_format($PRDT['invest_principal'])?>원</td>
									<td align="center"><?=number_format($LOAN['invest_interest'])?></td>
									<td align="center"><?=number_format($LOAN['plus_loan_interest'])?></td>
									<td align="center"><?=number_format($LOAN['month_loan_interest'])?></td>
									<td align="center"><?=$PRDT['loan_interest_rate']?>%</td>
									<td align="center"><?=$loan_date_range?></td>
									<td align="center"><?=$INI['total_day_count']?>일</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			&nbsp;
			<div class="panel panel-primary">
				<div class="panel-heading">투자 정보</div>
				<div class="panel-body">
					<div class="dataTable_wrapper">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">연수익률</th>
									<th class="text-center">예상이자</th>
									<th class="text-center">플랫폼 이용료율</th>
									<th class="text-center">플랫폼 이용료</th>
									<th class="text-center">원천징수</th>
									<th class="text-center">지급이자</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td align="center"><?=$PRDT['invest_return']?>%</td>
									<td align="center"><?=number_format($REPAY_SUM['invest_interest'])?>원</td>
									<td align="center"><?=$PRDT['invest_usefee']?>%</td>
									<td align="center"><?=number_format($REPAY_SUM['invest_usefee'])?>원</td>
									<td align="center"><?=number_format($REPAY_SUM['TAX']['sum'])?>원</td>
									<td align="center"><?=number_format($REPAY_SUM['interest'])?>원</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

<?
if($ib_trust) {

	$USE_PRDT = sql_fetch("SELECT COUNT(idx) AS cnt FROM cf_product WHERE display='Y' AND repay_acct_no='".$PRDT['repay_acct_no']."'");					// 상환용 가상계좌 사용상품수 추출
	$KSNET    = sql_fetch("SELECT VR_ACCT_NO, REF_NO FROM KSNET_VR_ACCOUNT WHERE USE_FLAG='Y' AND VR_ACCT_NO='".$PRDT['repay_acct_no']."'");		// KSNET 가상계좌 등록정보 추출

	$sql = "
		SELECT
			BANK_ID, ACCT_NB, TR_AMT, REMITTER_NM, MEDIA_GBN, ERP_TRANS_DT
		FROM
			IB_FB_P2P_IP
		WHERE 1
			AND TR_AMT_GBN='20'
			AND CUST_ID='".$LOANER['mb_no']."'";
	$sql.= ($USE_PRDT['cnt'] > 1) ? " AND repay_prd_idx='".$prd_idx."' " : "";		// 그룹상품일 경우 자기 상품번호가 등록된 입금내역을 가져오도록... 자기 상품번호는 사전에 수동 입력해줘야 함
	$sql.= " ORDER BY ERP_TRANS_DT DESC";
	$res = sql_query($sql);
	$rows = sql_num_rows($res);

?>
		<div class="col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					대출자 상환용 가상계좌 <?=($PRDT['repay_acct_no'])? '('.$PRDT['repay_acct_no'].')' : '';?> 입금 내역 &nbsp;
					<? if($USE_PRDT['cnt'] > 1 && $PRDT['state']=='1') { ?>
					<button type="button" id="setRepayTarget" class="btn btn-sm btn-default" <?=($prd_idx==$KSNET['REF_NO'])?'disabled':''?>>본상품의 상환계좌로 지정</button>
					<script>
					$('#setRepayTarget').click(function() {
						if(confirm('본 상품번호를 참조번호로 설정하시겠습니까?\n\n주) 설정이후 <?=$PRDT['repay_acct_no']?> 계좌로 입금되는 내역은\n[품번.<?=$prd_idx?>] 상품의 상환건으로 등록됩니다.')) {
							$.ajax({
								url : 'register_process.php',
								type: 'POST',
								dataType: 'json',
								data:{
									action:'setRepayTarget',
									product_idx:'<?=$PRDT['idx']?>',
									vacct:'<?=$KSNET['VR_ACCT_NO']?>'
								},
								success:function(data) {
									//$('#ajax_return_txt_zone').css('display','block');
									//$('#ajax_return_txt').val(data.result);
									if(data.result=='SUCCESS') { alert('해당 계좌의 참조번호가 설정 되었습니다.'); }
								},
								beforeSend: function() { loading('on'); },
								complete: function() { loading('off'); },
								error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
							});
						}
					});
					</script>
					<? } ?>
				</div>
				<div class="panel-body">
					<div class="dataTable_wrapper">

						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">NO</th>
									<th class="text-center">입금일시</th>
									<th class="text-center">계좌정보</th>
									<th class="text-center">입금자명</th>
									<th class="text-center">입금액</th>
									<!--<th class="text-center">시스템반영</th>-->
								</tr>
							</thead>
							<tbody>
<?
	if($rows) {
		for($i=0,$j=$rows; $i<$rows; $i++,$j--) {
			$TRANS = sql_fetch_array($res);
			$erp_trans_dt = substr($TRANS['ERP_TRANS_DT'], 0, 4).".".substr($TRANS['ERP_TRANS_DT'], 4, 2).".".substr($TRANS['ERP_TRANS_DT'], 6, 2)." ".substr($TRANS['ERP_TRANS_DT'], -6, 2).":".substr($TRANS['ERP_TRANS_DT'], -4, 2).":".substr($TRANS['ERP_TRANS_DT'], -2);
			echo '
									<tr class="odd">
										<td align="center">'.$j.'</td>
										<td align="center">'.$erp_trans_dt.'</td>
										<td align="center">'.$BANK[$TRANS['BANK_ID']].' '.$TRANS['ACCT_NB'].'</td>
										<td align="center">'.$TRANS['REMITTER_NM'].'</td>
										<td align="right" style="color:red">'.number_format($TRANS['TR_AMT']).'원</td>
									</tr>' . PHP_EOL;
			$tr_amt_sum += $TRANS['TR_AMT'];

		}
		echo '
									<tr style="background-color:#EDF4FC;color:brown">
										<td align="center">합계</td>
										<td colspan="4" align="right">'.number_format($tr_amt_sum).'원</td>
										<!--<td align="center"></td>-->
									</tr>' . PHP_EOL;
	}
	else {
		echo '							<tr class="odd"><td colspan="10" align="center">입금 내역이 없습니다.</td></tr>' . PHP_EOL;
	}

?>
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
<?
}
?>

		<div class="col-lg-12">
			<div class="panel panel-warning">
				<div class="panel-heading">관리자 코멘트</div>
				<div class="panel-body">
					<div id="memo_list_area" style="margin-bottom:10px;width:100%"><!--목록영역--></div>
					<div class="dataTable_wrapper" style="margin:0 auto;">
						<input type="hidden" name="product_idx" value="<?=$PRDT['idx']?>">
						<textarea name="memo" id="memo" style="width:91%;height:80px"></textarea>
						<span id="memo_input_btn" class="btn btn-primary" style="width:8.5%;height:80px;padding-top:30px;">입력</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
	loadMemo = function(){
		$.ajax({
			url : "ajax_invest_memo.php",
			type: "POST",
			data:{ product_idx:<?=$PRDT['idx']?> },
			success:function(data){
				$('#memo_list_area').html(data);
			},
			error: function () {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			}
		});
	}

	$(document).ready(function(){ loadMemo(); });

	delMemo = function(idx){
		if(confirm('삭제 하시겠습니까?')) {
			$.ajax({
				url : "ajax_invest_memo.php",
				type: "POST",
				data:{ mode:'delete', idx:idx },
				success:function(data){
					loadMemo();
				},
				error: function () {
					alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
				}
			});
		}
	}

	$('#memo_input_btn').on('click', function(){
		var memo_val = $.trim( $('#memo').val() );
		if(memo_val=='') {
			alert('장난해?');
		}
		else {
			$.ajax({
				url : "ajax_invest_memo.php",
				type: "POST",
				data:{ mode:'new', product_idx:<?=$PRDT['idx']?>, memo:memo_val },
				async:false,
				success:function(data){
					$('#memo').val('');
					$('#memo_list_area').html(data);
				},
				error: function () {
					alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
				}
			});
		}
	});
	</script>


	<style>
	#title_row_wrap { position:fixed; display:none; z-index:5; top:0; margin-left:0; }
	</style>

	<div class="col-lg-12">
		<div class="panel-body">
			<div id="title_row_wrap" class="dataTable_wrapper" style="position:relative;padding-right:32px">
<!--
				<table class="table table-striped table-bordered table-hover" style="margin-bottom:0; font-size:12px; opacity:0.9">
					<colgroup>
						<col style="width:3.5%"><col style="width:4%"><col style="width:7%"><col style="width:7%"><col style=""><col style="width:5%"><col style="width:5%"><col style="width:7%">
						<col style="width:6%"><col style="width:6%">
						<col style="width:6%"><col style="width:5%"><col style="width:6%">
						<col style="width:6%"><col style="width:4%"><col style="width:6%">
						<col style="width:7%">
					</colgroup>
					<thead style="background-color:#F8F8EF;">
						<tr align="center">
							<th rowspan="2" class="border_r">NO</th>
							<th colspan="7" class="border_r">투자자</th>
							<th colspan="2" class="border_r">예상이자</th>
							<th colspan="3" class="border_r">누적이자</th>
							<th colspan="4" class="border_r">당월정산</th>
							<th rowspan="2">지급<br>여부</th>
						</tr>
						<tr align="center">
							<th>구분</th>
							<th>ID</th>
							<th>성명.상호명<? if($_SESSION['ss_accounting_admin']) { ?><br>주민.사업자번호<? } ?></th>
							<th>수취방식</th>
							<th>지급은행</th>
							<th class="border_r">계좌번호</th>
							<th class="border_r">투자금</th>

							<th>전체</th>
							<th class="border_r">당월</th>

							<th>플랫폼이용료</th>
							<th>원천징수</th>
							<th class="border_r">지급이자</th>

							<th>플랫폼이용료</th>
							<th>원천징수</th>
							<th>지급이자</th>
							<th class="border_r">원금</th>
						</tr>
					</thead>
				</table>
//-->
			</div>
		</div>
	</div>

<?
$c_tax_num = 0;					// 세금계산서 발급대상자 카운트
$p_tax_num = 0;					// 현금영수증 발급대상자 카운트
$c_tax_succ_num = 0;		// 세금계산서 발급완료 카운트
$p_tax_succ_num = 0;		// 현금영수증 발급완료 카운트

if( in_array($PRDT['state'], array('1','2','4','5','7','8','9')) ) {

	$repay_count = count($REPAY);
	for($i=0,$turn=1; $i<$repay_count; $i++,$turn++) {

		// 전체지급 요청 버튼 설정
		$repay_request_button = '';
		if( in_array($PRDT['state'], array('1','5','7')) ) {
			if($REPAY[$i]['SUCCESS']['loan_interest_state']=='Y') {
				if($REPAY[$i]['SUCCESS']['invest_give_state']=='') {
					if($ib_trust) {
						if($REPAY[$i]['SUCCESS']['ib_request_ready']=='Y') {
							$repay_request_button = '<button type="button" class="btn btn-primary" onClick="requestPopup()">지급요청등록</button>';
						}
						else {
							$repay_request_button = '<button type="button" class="btn btn-warning" onClick="loanInterestGiveIBRequestReady(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');">지급요청대기</button>';
						}
					}
					else {
						$repay_request_button = '<button type="button" class="btn btn-primary" onClick="loanInterestGive(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');">전체지급</button>';
					}
				}
				if($REPAY[$i]['SUCCESS']['invest_give_state']=='W') {		// 기관처리결과대기중일 경우 상태요약 출력
					$repay_request_button = '처리결과<br>대기중';
				}
				if($REPAY[$i]['SUCCESS']['invest_give_state']=='S') {		// 기관처리완료시 지급액션버튼 출력
					$repay_request_button = '<button type="button" class="btn btn-danger" onClick="loanInterestGive(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');">전체지급</button>';
				}
				if($REPAY[$i]['SUCCESS']['invest_give_state']=='Y') {		// 지급처리완료
					//$repay_request_button = '<button type="button"  class="btn btn-gray" style="color:gray">전체지급완료</button>';
				}
			}
		}

		if($REPAY[$i]['repay_schedule_date']) {
			$LT['BTN']['id']  = 'list_button'.$turn;
			$LT['ZONE']['id'] = 'list_area'.$turn;
			if(substr($REPAY[$i]['repay_schedule_date'], 0, 7)==date('Y-m') and $flag<>"1") {
				if($REPAY[$i]['SUCCESS']['invest_give_state']=='') {
					$LT['BTN']['title'] = '접기 <span class="glyphicon glyphicon-minus"></span>';
					$LT['BTN']['class'] = 'btn btn-xs btn-default';
					$LT['ZONE']['display'] = 'block';
				}
				else {
					$LT['BTN']['title'] = '내역보기 <span class="glyphicon glyphicon-list"></span>';
					$LT['BTN']['class'] = 'btn btn-xs btn-primary';
					$LT['ZONE']['display'] = 'none';
				}
				$flag="1";
			}
			else {
				$LT['BTN']['title'] = '내역보기 <span class="glyphicon glyphicon-list"></span>';
				$LT['BTN']['class'] = 'btn btn-xs btn-primary';
				$LT['ZONE']['display'] = 'none';
			}

			$list_toggle_button = '<a id="'.$LT['BTN']['id'].'" onClick="listToggle(\''.$turn.'\')" class="'.$LT['BTN']['class'].'" style="width:90px;">'.$LT['BTN']['title'].'</a>';
		}

		$OVERDUE = NULL;
		if($REPAY[$i]['SUCCESS']['idx']=='') {
			if( G5_TIME_YMD > $REPAY[$i]['repay_schedule_date'] ) {
				$OVERDUE['day_comment'] = '원리금 수납만료일로 부터 '.number_format( ceil((strtotime(G5_TIME_YMD) - strtotime($REPAY[$i]['target_edate']))/86400) ).'일 경과';
				$OVERDUE['txt_field']   = '<input type="text" id="overdue_start_date" placeholder="연체등록일" style="width:100px;text-align:center;" class="datepicker" readonly>';
				$OVERDUE['proc_button'] = '<a id="overdue_proc_button" class="btn btn-xs btn-danger" data-idx="'.$idx.'" data-turn="'.$turn.'" style="width:90px;">연체등록</a>';
			}
		}
		else {
			if($REPAY[$i]['SUCCESS']['overdue_start_date']>'0000-00-00') {
				$OVERDUE['flag_color'] = ($REPAY[$i]['SUCCESS']['overdue_end_date']) ? 'blue' : 'red';
				$OVERDUE['day_comment'] = '>> 연체일수 : ' . number_format($REPAY[$i]['OVERDUE']['day_count']).'일';
			}
		}

?>
	<div class="col-lg-12">
		<div class="panel-body" style="padding-bottom: 0;" <?=($i==0)?"id='list_start'" : "";?>>
			<div style="width:100%;margin:4px 0 4px 0; padding:4px 20px 4px 20px; border:1px solid #ddd; border-radius:3px; background-color:#ffebcc;">
				<ul class="list-inline" style="margin:0">
					<li style="min-width:120px"><strong>이자지급 <?=$turn?>회차</strong></li>
					<li style="width:90px;"><?=$list_toggle_button?></li>
					<li style="margin-left:20px;">지급예정일 : <?=$REPAY[$i]['repay_schedule_date']?></li>
					<li>|</li>
					<li>정산대상기간 : <?=preg_replace('/-/', '.', $REPAY[$i]['target_sdate'])?> ~ <?=preg_replace('/-/', '.', $REPAY[$i]['target_edate'])?></li>
					<li>|</li>
					<li style="min-width:140px">이자계산일수 : <?=$REPAY[$i]['day_count']?>일</li>
					<li>|</li>
					<li>이자소득세율 <?=$REPAY[$i]['interest_tax_ratio']*100?>%, 지방세율 <?=($REPAY[$i]['interest_tax_ratio']/10)*100?>%</li>
					<li style="width:250px;margin-left:20px;color:<?=$OVERDUE['flag_color']?>"><?=$OVERDUE['day_comment']?></li>
					<li><?=$OVERDUE['txt_field']?></li>
					<li><?=$OVERDUE['proc_button']?></li>
				</ul>
			</div>
			<div id="<?=$LT['ZONE']['id'] ?>" class="dataTable_wrapper" style="display:<?=$LT['ZONE']['display']?>">
				<table class="table table-striped table-bordered table-hover" style="margin-bottom:0; font-size:12px">
					<colgroup>
						<col style="width:4%">
						<col style="%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:7%">
						<col style="width:6%">
					</colgroup>
					<thead style="background-color:#F8F8EF;">
						<tr align="center">
							<th rowspan="2" class="border_r">NO</th>
							<th rowspan="2" class="border_r">투자자정보</th>
							<th rowspan="2" class="border_r">투자금</th>
							<th colspan="3" class="border_r">예상이자</th>
							<th colspan="4" class="border_r">당월정산</th>
							<th colspan="3" class="border_r">누적이자</th>
							<th rowspan="2" class="border_r">지급여부</th>
							<th rowspan="2">세금계산서</th>
						</tr>
						<tr align="center">
							<th>전체</th>
							<th>일별</th>
							<th class="border_r">당월</th>

							<th>플랫폼<br>이용료</th>
							<th>원천징수</th>
							<th>지급이자</th>
							<th class="border_r">원금</th>

							<th>플랫폼<br>이용료</th>
							<th>원천징수</th>
							<th class="border_r">지급이자</th>
						</tr>
					</thead>
					<tbody>

<?
		$list_count = count($REPAY[$i]['LIST']);
		for($j=0,$num=$list_count; $j<$list_count; $j++,$num--)
		{

			$member_id   = $REPAY[$i]['LIST'][$j]['mb_id'];
			$member_no   = $REPAY[$i]['LIST'][$j]['mb_no'];
			$member_type = "";
			$member_type.= ($REPAY[$i]['LIST'][$j]['member_type']=='2') ? "법인" : "개인";
			$member_type.= ($REPAY[$i]['LIST'][$j]['is_creditor']=='Y') ? "-대부" : "";

			if($REPAY[$i]['LIST'][$j]['receive_method']) {
				$receive_method = ($REPAY[$i]['LIST'][$j]['receive_method']=='1') ? '환급계좌' : '<font color="#FF2222">예치금</font>';
			}
			else {
				$receive_method = "미지정";
			}

			$bgcolor = ($REPAY[$i]['LIST'][$j]['member_type']=='2') ? '#FFF2CC' : '#FFFFFF';
			$bgcolor = ($REPAY[$i]['LIST'][$j]['is_creditor']=='Y') ? '#FCE4D6' : $bgcolor;

			$invest_type = ($REPAY[$i]['LIST'][$j]['is_advance_invest']=='Y') ? '사전투자' : '일반투자';

			//if($REPAY[$i]['LIST'][$j]['give_idx']==34322) print_rr($REPAY[$i]['LIST'][$j]);

			$repay_result = "";
			if(in_array($PRDT['state'], array('1','2','5','7'))) {

				if($REPAY[$i]['SUCCESS']['ib_request_ready']=='Y') {
					$repay_result = "지급요청대기중";
				}

				if($REPAY[$i]['LIST'][$j]['paied']=='Y') {
					$repay_result = "<span style='color:#AAA'>지급완료<br>".substr($REPAY[$i]['LIST'][$j]['banking_date'], 0, 16)."</span>\n";
					// 실수령-이체금액 체크
					if($REPAY[$i]['LIST'][$j]['interest'] != $REPAY[$i]['LIST'][$j]['paied_amount']) {
						$repair_query = "";
						$repair_query.= "UPDATE cf_product_give SET";
						$repair_query.= " interest='".$REPAY[$i]['LIST'][$j]['interest']."',";
						$repair_query.= " interest_tax='".$REPAY[$i]['LIST'][$j]['TAX']['interest_tax']."',";
						$repair_query.= " local_tax='".$REPAY[$i]['LIST'][$j]['TAX']['local_tax']."',";
						$repair_query.= " fee='".$REPAY[$i]['LIST'][$j]['invest_usefee']."'";
						$repair_query.= " WHERE idx='".$REPAY[$i]['LIST'][$j]['give_idx']."';";

						$repay_result.= "<span style='color:red'>".number_format($REPAY[$i]['LIST'][$j]['paied_amount'])."</span>\n";
						$repay_result.= "<div style='text-align:left'>" . $repair_query . "</div>\n";
					}
				}
				else {
					if($REPAY[$i]['SUCCESS']['invest_give_state']=='W') {
						$repay_result = "처리결과<br>대기중";
					}
					else if($REPAY[$i]['SUCCESS']['invest_give_state']=='S') {  // 기관회수처리 완료 -> 투자자의 잔고에 원리금이 상계처리됨을 뜻함.
						switch($REPAY[$i]['LIST'][$j]['ib_withdraw']) {
							case '00000000' : $repay_result = '기관측 회수금<br>배분완료<br>'.$REPAY[$i]['LIST'][$j]['ib_withdraw_datetime']; break;
							case 'C'        : $repay_result = '<span style="color:red">회수처리실패</span>'; break;
							default         : break;
						}
					}
				}

			}

			if($REPAY[$i]['LIST'][$j]['member_type']=='2') {
				$TAX_INVOICE[$i]['C'] = $TAX_INVOICE[$i]['C'] + 1;
				if($REPAY[$i]['LIST'][$j]['mgtKey']) { $TAX_INVOICE[$i]['C_SUCC'] = $TAX_INVOICE[$i]['C_SUCC'] + 1; }
			}
			else {
				if($REPAY[$i]['LIST'][$j]['is_owner_operator']=='1') {
					$TAX_INVOICE[$i]['C'] = $TAX_INVOICE[$i]['C'] + 1;
					if($REPAY[$i]['LIST'][$j]['mgtKey']) { $TAX_INVOICE[$i]['C_SUCC'] = $TAX_INVOICE[$i]['C_SUCC'] + 1; }
				}
				else {
					$TAX_INVOICE[$i]['P'] = $TAX_INVOICE[$i]['P'] + 1;
					if($REPAY[$i]['LIST'][$j]['mgtKey']) { $TAX_INVOICE[$i]['P_SUCC'] = $TAX_INVOICE[$i]['P_SUCC'] + 1; }
				}
			}

			if($REPAY[$i]['LIST'][$j]['mgtKey']) {
				if(preg_match('/P_/i', $REPAY[$i]['LIST'][$j]['mgtKey']))       $taxinvoicetype = '현금영수증';
				else if(preg_match('/C_/i', $REPAY[$i]['LIST'][$j]['mgtKey']))  $taxinvoicetype = '세금계산서';
				else $taxinvoicetype = '직접확인';

				$taxinvoice_link = '<a href="/LINKHUB/hellofunding/Taxinvoice/GetPopUpURL.php?mgtKey='.$REPAY[$i]['LIST'][$j]['mgtKey'].'" target="_blank">'.$taxinvoicetype.'</a>';
			}
			else {
				$taxinvoice_link = '';
			}

			if($REPAY[$i]['LIST'][$j]['insidebank_after_trans_target']=='1') $bgcolor = '#53B5DC';


			// 원리금수취권번호
			if($INVEST[$j]['prin_rcv_no']) {
				$prin_rcv_no = $INVEST[$j]['prin_rcv_no'];
			}
			else {
				$prin_rcv_no = 'M' . $REPAY[$i]['LIST'][$j]['mb_no'] .'P'.$PRDT['idx'].'I'.$REPAY[$i]['LIST'][$j]['invest_idx'];
			}

?>
						<tr style="background:<?=$bgcolor?>;">
							<td align="center" class="border_r"><?=$num?></td>
							<td align="center" class="border_r" style="padding:2px"><table style="width:100%;">
									<colgroup>
										<col style="width:30%">
										<col style="width:70%">
									</colgroup>
									<tr align="center">
										<td>수취권번호</td>
										<td><?=$prin_rcv_no?></td>
									</tr>
									<tr align="center">
										<td>회원구분</td>
										<td><?=$member_type?></td>
									</tr>
									<tr align="center">
										<td>아이디</td>
										<td>
											<a href="/adm/member/member_view.php?&mb_id=<?=$member_id?>"><?=$member_id?></a><? if(!$_REQUEST['mb_id']){ ?> &nbsp;
										  <a href="<?=$_SERVER['PHP_SELF']?>?idx=<?=$prd_idx?>&mb_id=<?=$member_id?>" class="btn btn-info" style="font-size:11px; line-height:11px; width:80px; padding:3px 4px;">본회원만 보기</a><br><? } ?>
										</td>
									</tr>
									<tr align="center">
										<td>성명.상호</td>
										<td><?=$REPAY[$i]['LIST'][$j]['mb_name']?></td>
									</tr>

<? if($_SESSION['ss_accounting_admin'] && $REPAY[$i]['LIST'][$j]['jumin']) { ?>
									<tr align="center">
										<td>주민.사업자번호</td>
										<td><?=$REPAY[$i]['LIST'][$j]['jumin']?></td>
									</tr>
<? } ?>

									<tr align="center">
										<td>수취방식</td>
										<td><?=$receive_method?></td>
									</tr>
									<tr align="center">
										<td>지급계좌</td>
										<td><?=$REPAY[$i]['LIST'][$j]['bank']?> <span title="<?=$REPAY[$i]['LIST'][$j]['account_num']?>"><?=$_SESSION['ss_accounting_admin']?preg_replace("/-/", "", $REPAY[$i]['LIST'][$j]['account_num']):substr($REPAY[$i]['LIST'][$j]['account_num'],0,strlen($REPAY[$i]['LIST'][$j]['account_num'])-4)."****"?></span></td>
									</tr>
									<tr align="center">
										<td>누적투자</td>
										<td>
											(<?=number_format($MTOTAL_INVEST_SUM[$REPAY[$i]['LIST'][$j]['mb_no']]['count'])?>건) <?=number_format($MTOTAL_INVEST_SUM[$REPAY[$i]['LIST'][$j]['mb_no']]['amount'])?>원 &nbsp;
											<a href="/adm/member/member_view.php?&mb_id=<?=$member_id?>#ft" class="btn btn-default" style="font-size:11px; line-height:11px; padding:3px 4px;">내역보기</a>
										</td>
									</tr>
								</table>
							</td>

							<td align="right" class="border_r"><?=number_format($REPAY[$i]['LIST'][$j]['amount'])?></td>

							<td align="right"><span style='color:#aaa'><?=number_format($REPAY_SUM[$member_id]['invest_interest'])?></span></td>
							<td align="right"><span style='color:#aaa'><?=number_format($REPAY[$i]['LIST'][$j]['day_invest_interest'], 4)?></span></td>
							<td align="right" class="border_r"><span style='color:#3366FF'><?=number_format($REPAY[$i]['LIST'][$j]['invest_interest'])?></span></td>

							<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['TAX']['sum'])?></td>
							<td align="right"><span style='color:#2222FF'><?=number_format($REPAY[$i]['LIST'][$j]['interest'])?></span></td>
							<td align="right" class="border_r"><span style='color:#2222FF'><?=number_format($REPAY[$i]['LIST'][$j]['repay_principal'])?></span></td>

							<td align="right"><span style='color:#aaa'><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['invest_usefee'])?></span></td>
							<td align="right"><span style='color:#aaa'><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['TAX']['sum'])?></span></td>
							<td align="right" class="border_r"><span style='color:#aaa'><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['interest'])?></span></td>

							<td align="center" class="border_r"><?=$repay_result?></td>

							<td align="center"><?=$taxinvoice_link?></td>
						</tr>

<?
		}

		// 합계출력
		if(!$mb_id) {
?>
						<tr align="center" style="background:#EDF4FC;color:#2222FF;">
							<td colspan="2" class="border_r"><?=$turn?>회차 합계</td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['SUM']['amount'])?></td>

							<td align="right"><?=number_format($REPAY_SUM['invest_interest'])?></td>
							<td align="right">-</td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['SUM']['invest_interest'])?></td>

							<td align="right"><?=number_format($REPAY[$i]['SUM']['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['SUM']['TAX']['sum'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['SUM']['interest'])?></td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['SUM']['repay_principal'])?></td>

							<td align="right"><?=number_format($REPAY[$i]['NUJUK_SUM']['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['NUJUK_SUM']['TAX']['sum'])?></td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['NUJUK_SUM']['interest'])?></td>

							<td class="border_r"><?=$repay_request_button?></td>
							<td></td>
						</tr>
<?
		}
?>
					</tbody>
				</table>

				<div class="panel-body" style="text-align:right;">
<?
		// ※ state: 진행현황(1:이자상환중|2:상환완료(투자종료)|3:투자금모집실패|4:부실|5:중도일시상환|6:대출계약취소)
		if(in_array($PRDT['state'], array('1','2','4','5'))) {
			echo '<a href="./product_calculate_excel.php?idx='.$PRDT['idx'].'&turn='.$turn.'&mb_id='.$mb_id.'" target="_blank" class="btn btn-success" style="width:160px;">엑셀저장</a>' . PHP_EOL;
		}

		if(in_array($PRDT['state'], array('1','5'))) {

			// [대출이자 수급완료 처리버튼]
			if($REPAY[$i]['SUCCESS']['loan_interest_state']=='Y') {
				echo '<button type="button" class="btn btn-gray" onClick="alert(\'이미 처리 되었습니다.\');" style="width:160px;">대출이자 수급완료</button>' . PHP_EOL;
			}
			else {
				echo '<button type="button" class="btn btn-danger" onClick="loanInterestSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">대출이자 수급완료</button>' . PHP_EOL;
			}

			// [대출원금 수급완료 처리버튼]
			if($REPAY[$i]['SUCCESS']['loan_principal_state']=='Y') {
				echo '<button type="button" class="btn btn-gray" onClick="alert(\'이미 처리 되었습니다.\');" style="width:160px;">대출원금 수급완료</button>' . PHP_EOL;
			}
			else {
				// 상환방식에 따른 구분 (1:만기일시상환|2:원리금균등상환|3:원금균등상환)
				if($PRDT['repay_type']=='1') {
					if($turn==$repay_count) {
						echo '<button type="button" class="btn btn-danger" onClick="loanPrincipalSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">대출원금 수급완료</button>' . PHP_EOL;
					}
					else {
						echo '<button type="button" class="btn btn-gray" onClick="alert(\'만기일시상환 방식의 대출건 입니다.\');" style="width:160px;">대출원금 수급완료</button>' . PHP_EOL;
					}
				}
				else {
					echo '<button type="button" class="btn btn-danger" onclick="loanPrincipalSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">대출원금 수급완료</button>' . PHP_EOL;
				}
			}

			// [투자수익금 지급완료 처리버튼]
			if($REPAY[$i]['SUCCESS']['invest_give_state']=='Y') {
				echo '<button type="button" class="btn btn-gray" onClick="alert(\'이미 처리 되었습니다.\');" style="width:160px;">투자수익금 지급완료</button>' . PHP_EOL;
			}
			else {
				echo '<button type="button" class="btn btn-danger" onClick="investGiveSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">투자수익금 지급완료</button>' . PHP_EOL;
			}


			// [투자원금 지급완료 처리버튼]
			if($REPAY[$i]['SUCCESS']['invest_principal_give']=='Y') {
				echo '<button type="button" class="btn btn-gray" onClick="alert(\'이미 처리 되었습니다.\');" style="width:160px;">투자원금 지급완료</button>' . PHP_EOL;
			}
			else {
				// 상환방식에 따른 구분 (1:만기일시상환|2:원리금균등상환|3:원금균등상환)
				if($PRDT['repay_type']=='1') {
					if($turn==$repay_count) {
						echo '<button type="button" class="btn btn-danger" onClick="investPrincipalGiveSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">투자원금 지급완료</button>' . PHP_EOL;
					}
					else {
						echo '<button type="button" class="btn btn-gray" onClick="alert(\'만기일시상환 방식의 대출건 입니다.\');" style="width:160px;">투자원금 지급완료</button>' . PHP_EOL;
					}
				}
				else {
					echo '<button type="button" class="btn btn-danger" onClick="investPrincipalGiveSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">투자원금 지급완료</button>' . PHP_EOL;
				}
			}

		}

		if($PRDT['invest_usefee'] > 0 && $REPAY[$i]['SUCCESS']['invest_give_state']=='Y') {		// 세금계산서발행
			if($TAX_INVOICE[$i]['C_SUCC'] < $TAX_INVOICE[$i]['C']) echo '<button type="button" id="tax_invoice_request" onClick="taxInvoiceRequest(\'c\',\''.$PRDT['idx'].'\', \''.$turn.'\', \'\');" class="btn btn-primary">세금계산서발행</button>' . PHP_EOL;
			if($TAX_INVOICE[$i]['P_SUCC'] < $TAX_INVOICE[$i]['P']) echo '<button type="button" id="tax_invoice_request" onClick="taxInvoiceRequest(\'p\',\''.$PRDT['idx'].'\', \''.$turn.'\', \'\');" class="btn btn-primary">현금영수증발행</button>' . PHP_EOL;
		}

?>
				</div><!-- /.panel-body -->

<? if($REPAY[$i]['SUCCESS']['overdue_start_date'] > '0000-00-00') { ?>

				<div style="width:100%;margin:0 0 4px; padding:4px 20px 4px 20px; border:1px solid#brown; border-radius:3px; background-color:#FF2222;">
					<ul class="list-inline" style="margin:0;color:#fff">
						<li style="min-width:120px"><strong>연체 정산 내역</strong></li>
						<li style="min-width:280px">연체귀속기간 : <?=preg_replace('/-/', '.', $REPAY[$i]['OVERDUE']['start_date'])?> ~ <?=($REPAY[$i]['OVERDUE']['end_date']>'0000-00-00') ? preg_replace('/-/', '.', $REPAY[$i]['OVERDUE']['end_date']) : G5_TIME_YMD?></li>
						<li>|</li>
						<li style="min-width:180px">연체귀속일수 : <?=$REPAY[$i]['OVERDUE']['day_count']?>일</li>
					</ul>
				</div>
				<table class="table table-striped table-bordered table-hover" style="margin-bottom:0; font-size:12px;">
					<colgroup>
						<col style="width:4%">
						<col style="%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:6%">
						<col style="width:7%">
						<col style="width:6%">
					</colgroup>
					<thead style="background-color:#F8F8EF;">
						<tr align="center">
							<th class="border_r">NO</th>
							<th class="border_r">투자자정보</th>
							<th class="border_r">투자금</th>
							<th class="border_r"><span style="color:#FF2222">연체이자</span></th>
							<th>플랫폼<br>이용료</th>
							<th>원천징수</th>
							<th><span style="color:#FF2222">지급이자</span></th>
							<th class="border_r"><span style="color:#FF2222">원금</span></th>
							<th class="border_r">지급여부</th>
							<th>세금계산서</th>
						</tr>
					</thead>
					<tbody>
<?
		for($j=0,$num=$list_count; $j<$list_count; $j++,$num--) {

			// 전체지급/요청 버튼 설정
			$repay_request_button = '';
			if( in_array($PRDT['state'], array('1','5','7')) ) {
				if($REPAY[$i]['OVERDUE_SUCCESS']['overdue_receive']=='Y') {
					if($REPAY[$i]['OVERDUE_SUCCESS']['overdue_give']=='') {
						if($ib_trust) {
							if($REPAY[$i]['OVERDUE_SUCCESS']['overdue_ib_request_ready']=='Y') {
								$repay_request_button = '<button type="button" class="btn btn-primary" onClick="requestPopup()">지급요청등록</button>';
							}
							else {
								$repay_request_button = '<button type="button" class="btn btn-warning" onClick="overdueGiveIBRequestReady(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');">지급요청대기</button>';
							}
						}
						else {
							$repay_request_button = '<button type="button" class="btn btn-primary" onClick="overdueGive(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');">전체지급</button>';
						}
					}
					if($REPAY[$i]['OVERDUE_SUCCESS']['invest_give_state']=='W') {		// 기관처리결과대기중일 경우 상태요약 출력
						$repay_request_button = '처리결과<br>대기중';
					}
					if($REPAY[$i]['OVERDUE_SUCCESS']['invest_give_state']=='S') {		// 기관처리완료시 지급액션버튼 출력
						$repay_request_button = '<button type="button" class="btn btn-danger" onClick="overdueGive(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');">전체지급</button>';
					}
					if($REPAY[$i]['OVERDUE_SUCCESS']['invest_give_state']=='Y') {		// 지급처리완료
						//$repay_request_button = '<button type="button"  class="btn btn-gray" style="color:gray">전체지급완료</button>';
					}
				}
			}

			$member_id   = $REPAY[$i]['OVERDUE_LIST'][$j]['mb_id'];
			$member_no   = $REPAY[$i]['OVERDUE_LIST'][$j]['mb_no'];
			$member_type = "";
			$member_type.= ($REPAY[$i]['OVERDUE_LIST'][$j]['member_type']=='2') ? "법인" : "개인";
			$member_type.= ($REPAY[$i]['OVERDUE_LIST'][$j]['is_creditor']=='Y') ? "-대부" : "";

			if($REPAY[$i]['OVERDUE_LIST'][$j]['receive_method']) {
				$receive_method = ($REPAY[$i]['OVERDUE_LIST'][$j]['receive_method']=='1') ? '환급계좌' : '<font color="#FF2222">예치금</font>';
			}
			else {
				$receive_method = "미지정";
			}

			$bgcolor = ($REPAY[$i]['OVERDUE_LIST'][$j]['member_type']=='2') ? '#FFF2CC' : '#FFFFFF';
			$bgcolor = ($REPAY[$i]['OVERDUE_LIST'][$j]['is_creditor']=='Y') ? '#FCE4D6' : $bgcolor;

			$invest_type = ($REPAY[$i]['OVERDUE_LIST'][$j]['is_advance_invest']=='Y') ? '사전투자' : '일반투자';

			$ovd_repay_result = "";

			if($REPAY[$i]['OVERDUE_LIST'][$j]['paied']=='Y') {
				$ovd_repay_result = "<span style='color:#aaa'>지급완료<br>".substr($REPAY[$i]['OVERDUE_LIST'][$j]['banking_date'], 0, 16)."</span>\n";				// 실수령-이체금액 체크
				if($REPAY[$i]['OVERDUE_LIST'][$j]['interest'] != $REPAY[$i]['OVERDUE_LIST'][$j]['paied_amount']) {
					$ovd_repay_result.= "<span style='color:red'>".number_format($REPAY[$i]['OVERDUE_LIST'][$j]['paied_amount'])."</span>\n";
					$ovd_repay_result.= "<br>UPDATE cf_product_give SET interest='".$REPAY[$i]['OVERDUE_LIST'][$j]['interest']."' WHERE invest_idx='".$REPAY[$i]['OVERDUE_LIST'][$j]['invest_idx']."' AND product_idx='".$PRDT['idx']."' AND date='".$REPAY[$i]['repay_date']." AND is_overdue='Y';\n";
				}
			}

			if($REPAY[$i]['OVERDUE_LIST'][$j]['member_type']=='2') {
				$TAX_INVOICE[$i]['C'] = $TAX_INVOICE[$i]['C'] + 1;
				if($REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey']) { $TAX_INVOICE[$i]['C_SUCC'] = $TAX_INVOICE[$i]['C_SUCC'] + 1; }
			}
			else {
				if($REPAY[$i]['OVERDUE_LIST'][$j]['is_owner_operator']=='1') {
					$TAX_INVOICE[$i]['C'] = $TAX_INVOICE[$i]['C'] + 1;
					if($REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey']) { $TAX_INVOICE[$i]['C_SUCC'] = $TAX_INVOICE[$i]['C_SUCC'] + 1; }
				}
				else {
					$TAX_INVOICE[$i]['P'] = $TAX_INVOICE[$i]['P'] + 1;
					if($REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey']) { $TAX_INVOICE[$i]['P_SUCC'] = $TAX_INVOICE[$i]['P_SUCC'] + 1; }
				}
			}

			if($REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey']) {
				if(preg_match('/P_/i', $REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey']))       $taxinvoicetype = '현금영수증';
				else if(preg_match('/C_/i', $REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey']))  $taxinvoicetype = '세금계산서';
				else $taxinvoicetype = '직접확인';

				$taxinvoice_link = '<a href="/LINKHUB/hellofunding/Taxinvoice/GetPopUpURL.php?mgtKey='.$REPAY[$i]['OVERDUE_LIST'][$j]['mgtKey'].'" target="_blank">'.$taxinvoicetype.'</a>';
			}
			else {
				$taxinvoice_link = '';
			}

			$prin_rcv_no = 'M' . $REPAY[$i]['OVERDUE_LIST'][$j]['mb_no'] .'P'.$PRDT['idx'].'I'.$REPAY[$i]['OVERDUE_LIST'][$j]['invest_idx'];

?>
						<tr style="background:<?=$bgcolor?>;">
							<td align="center" class="border_r" alt="NO">
								<?=$num?>
							</td>
							<td align="center" class="border_r" style="padding:2px"><table style="width:100%;">
									<colgroup>
										<col style="width:30%">
										<col style="width:70%">
									</colgroup>
									<tr align="center">
										<td>수취권번호</td>
										<td><?=$prin_rcv_no?></td>
									</tr>
									<tr align="center">
										<td>회원구분</td>
										<td><?=$member_type?></td>
									</tr>
									<tr align="center">
										<td>아이디</td>
										<td>
											<a href="/adm/member/member_view.php?&mb_id=<?=$member_id?>"><?=$member_id?></a><br>
											<? if(!$_REQUEST['mb_id']){ ?><a href="<?=$_SERVER['PHP_SELF']?>?idx=<?=$prd_idx?>&mb_id=<?=$member_id?>" class="btn btn-info" style="font-size:11px; line-height:11px; width:80px; padding:3px 4px;">본회원만 보기</a><br><? } ?>
										</td>
									</tr>
									<tr align="center">
										<td>성명.상호</td>
										<td><?=$REPAY[$i]['OVERDUE_LIST'][$j]['mb_name']?></td>
									</tr>

<? if($_SESSION['ss_accounting_admin'] && $REPAY[$i]['OVERDUE_LIST'][$j]['jumin']) { ?>
									<tr align="center">
										<td>주민.사업자번호</td>
										<td><?=$REPAY[$i]['OVERDUE_LIST'][$j]['jumin']?></td>
									</tr>
<? } ?>

									<tr align="center">
										<td>수취방식</td>
										<td><?=$receive_method?></td>
									</tr>
									<tr align="center">
										<td>지급계좌</td>
										<td><?=$REPAY[$i]['OVERDUE_LIST'][$j]['bank']?> <?=preg_replace("/-/", "", $REPAY[$i]['OVERDUE_LIST'][$j]['account_num'])?></td>
									</tr>
									<tr align="center">
										<td>누적투자</td>
										<td>
											(<?=number_format($MTOTAL_INVEST_SUM[$REPAY[$i]['OVERDUE_LIST'][$j]['mb_no']]['count'])?>건) <?=number_format($MTOTAL_INVEST_SUM[$REPAY[$i]['OVERDUE_LIST'][$j]['mb_no']]['amount'])?>원 &nbsp;
											<a href="/adm/member/member_view.php?&mb_id=<?=$member_id?>#ft" class="btn btn-default" style="font-size:11px; line-height:11px; padding:3px 4px;">내역보기</a>
										</td>
									</tr>
								</table>
							</td>
							<td align="right" alt="투자금" class="border_r"><?=number_format($REPAY[$i]['OVERDUE_LIST'][$j]['amount'])?></td>
							<td align="right" alt="연체이자" class="border_r"><span style='color:#FF2222'><?=number_format($REPAY[$i]['OVERDUE_LIST'][$j]['invest_interest'])?></span></td>
							<td align="right" alt="플랫폼 이용료"><?=number_format($REPAY[$i]['OVERDUE_LIST'][$j]['invest_usefee'])?></td>
							<td align="right" alt="원천징수"><?=number_format($REPAY[$i]['OVERDUE_LIST'][$j]['TAX']['sum'])?></td>
							<td align="right" alt="지급이자"><span style='color:#FF2222'><?=number_format($REPAY[$i]['OVERDUE_LIST'][$j]['interest'])?></span></td>
							<td align="right" alt="원금" class="border_r"><span style='color:#FF2222'><?=number_format($REPAY[$i]['OVERDUE_LIST'][$j]['repay_principal'])?></span></td>
							<td align="center" alt="지급여부" class="border_r"><?=$ovd_repay_result?></td>
							<td align="center"><?=$taxinvoice_link?></td>
						</tr>

<?
		}
?>
						<tr align="center" style="background:#FFDDDD;color:brown;">
							<td colspan="2" class="border_r">합계</td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['OVERDUE_SUM']['amount'])?></td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['OVERDUE_SUM']['invest_interest'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['OVERDUE_SUM']['invest_usefee'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['OVERDUE_SUM']['TAX']['sum'])?></td>
							<td align="right"><?=number_format($REPAY[$i]['OVERDUE_SUM']['interest'])?></td>
							<td align="right" class="border_r"><?=number_format($REPAY[$i]['OVERDUE_SUM']['repay_principal'])?></td>
							<td class="border_r"><?=$repay_request_button?></td>
							<td></td>
						</tr>
					</tbody>
				</table>

				<div class="panel-body" style="text-align:right;">
					<a href="./product_calculate_excel_overdue.php?idx=<?=$PRDT['idx']?>&turn=<?=$turn?>&mb_id=<?=$mb_id?>" target="_blank" class="btn btn-success" style="width:160px;">엑셀저장</a>
<?
		// [연체이자 수급완료 처리버튼]
		if($REPAY[$i]['OVERDUE_SUCCESS']['overdue_receive']=='Y') {
			echo '<button type="button" class="btn btn-gray" onClick="alert(\'이미 처리 되었습니다.\');" style="width:160px;">연체이자 수급완료</button>' . PHP_EOL;
		}
		else {
			echo '<button type="button" class="btn btn-danger" onClick="overdueRcvSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">연체이자 수급완료</button>' . PHP_EOL;
		}

		// [연체이자 지급완료 처리버튼]
		if($REPAY[$i]['OVERDUE_SUCCESS']['overdue_give']=='Y') {
			echo '<button type="button" class="btn btn-gray" onClick="alert(\'이미 처리 되었습니다.\');" style="width:160px;">연체이자 지급완료</button>' . PHP_EOL;
		}
		else {
			echo '<button type="button" class="btn btn-danger" onClick="overdueGiveSuccess(\''.$PRDT['idx'].'\', \''.$REPAY[$i]['repay_date'].'\', \''.$turn.'\');" style="width:160px;">연체이자 지급완료</button>' . PHP_EOL;
		}

		if($PRDT['invest_usefee'] > 0 && $REPAY[$i]['OVERDUE_SUCCESS']['overdue_give']=='Y') {		// 세금계산서발행
			if($TAX_INVOICE[$i]['C_SUCC'] < $TAX_INVOICE[$i]['C']) echo '<button type="button" id="tax_invoice_request" onClick="taxInvoiceRequest(\'c\',\''.$PRDT['idx'].'\', \''.$turn.'\', \'overdue\');" class="btn btn-primary">세금계산서발행</button>' . PHP_EOL;
			if($TAX_INVOICE[$i]['P_SUCC'] < $TAX_INVOICE[$i]['P']) echo '<button type="button" id="tax_invoice_request" onClick="taxInvoiceRequest(\'p\',\''.$PRDT['idx'].'\', \''.$turn.'\', \'overdue\');" class="btn btn-primary">현금영수증발행</button>' . PHP_EOL;
		}

?>
				</div>

<?
	}
?>
			</div>
		</div>

	</div><!-- /.col-lg-12 -->
<?
	}
?>
	<script>
	$(window).scroll(function(){
		if($(window).scrollTop() > $('#list_start').offset().top){
			$("#title_row_wrap").css('position','fixed');
			$("#title_row_wrap").css('display','block');
			$("#title_row_wrap").css('z-index','5');
			$("#title_row_wrap").css('width',$(window).width() - 30);
		}
		else {
			$("#title_row_wrap").css('display','none');
		}
	});
	</script>
<?
}


###############################################################################
## 대출실행이 되지 않은 상품 투자내역
###############################################################################
if( $PRDT['state']=='' || in_array($PRDT['state'], array('3','6')) ) {
?>
	<div class="col-lg-12">
		<div class="panel-body" style="padding-bottom: 0;">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="margin-bottom:0; font-size:12px">
					<thead>
						<tr style="background-color:#F8F8EF">
							<th class="text-center" colspan="14">
								투자자 &nbsp;&nbsp;
								<a href="./repayment/investor_amount_check.php?prd_idx=<?=$prd_idx?>" target="_blank" style="color:#FF2222"><< 투자금, 예치금 비교 >></a>
							</th>
						</tr>
						<tr style="background-color:#F8F8EF">
							<td class="text-center">NO.</td>
							<td class="text-center">투자<br/>번호</td>
							<td class="text-center">회원<br/>번호</td>
							<td class="text-center">ID</td>
							<td class="text-center">성명.상호명<? if($_SESSION['ss_accounting_admin']) { ?><br>(주민.사업자번호)<? } ?></td>
							<td class="text-center">투자금</td>
							<td class="text-center">투자구분</td>
							<td class="text-center">최종투자일시</td>
							<td class="text-center">수취방식</td>
							<td class="text-center">지급은행</td>
							<td class="text-center">계좌번호</td>
							<td class="text-center">누적투자수</td>
							<td class="text-center">누적투자금액</td>
							<td class="text-center">투자취소</td>
						</tr>
					</thead>
					<tbody>
<?
	$plus_day += $last_day;

	$invest_count = count($INVEST);
	for($j=0,$num=$invest_count; $j<$invest_count; $j++,$num--)
	{

			$sqlx = "
				SELECT
					insert_date, LEFT(insert_time, 5) AS insert_time
				FROM
					cf_product_invest_detail
				WHERE
					member_idx='".$INVEST[$j]['mb_no']."'
					AND product_idx='".$PRDT['idx']."'
					AND cancel_date='0000-00-00 00:00:00'
				ORDER BY
					idx DESC
				LIMIT 1";
			//echo $sqlx."<br>\n";
			$TMP = sql_fetch($sqlx);

			//수취계좌 출력
			if($INVEST[$j]['receive_method']=='1') {
				$receive_method = "환급계좌";
				$bank           = $BANK[$INVEST[$j]['bank_code']];
				$account_num    = $INVEST[$j]['account_num'];
			}
			else if($INVEST[$j]['receive_method']=='2') {
				$receive_method = '<font color="#FF2222">예치금</font>';
				$bank           = $BANK[$INVEST[$j]['va_bank_code2']];
				$account_num    = $INVEST[$j]['virtual_account2'];
			}
			else {
				$receive_method = "미지정";
				$bank           = "";
				$account_num    = "";
			}

			$INVEST[$j]['mb_name'] = ($INVEST[$j]['member_type']=='2') ? $INVEST[$j]['mb_co_name'] : $INVEST[$j]['mb_name'];
			$INVEST[$j]['jumin']   = ($INVEST[$j]['member_type']=='2') ? $INVEST[$j]['mb_co_reg_num'] : @getJumin($INVEST[$j]['member_idx']);

			$bgcolor = ($INVEST[$j]['member_type']=='2') ? '#ffffcc' : '#FFFFFF';
		//$bgcolor = ($INVEST[$j]['is_creditor']=='Y') ? '#FCE4D6' : $bgcolor;

			$invest_type = ($INVEST[$j]['is_advance_invest']=='Y') ? '사전투자' : '일반투자';

			if($INVEST[$j]['insidebank_after_trans_target']=='1') $bgcolor = '#53B5DC';		//신한 예치금 이전 대상자 플래그

			$cancel_button = "";
			if($PRDT['state']=='') {
				if($INVEST[$j]['invest_state']=='Y') {
					$cancel_button = "<button type='button' class='btn btn-sm btn-danger' onClick=\"investCancel('".$INVEST[$j]['idx']."');\">투자취소</button>";
				}
			}

?>
						<tr style="background-color:<?=$bgcolor?>">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$INVEST[$j]['idx']?></td>
							<td align="center"><?=$INVEST[$j]['mb_no']?></td>
							<td align="center">
								<a href="<?=$_SERVER['PHP_SELF']?>?idx=<?=$prd_idx?>&mb_id=<?=$INVEST[$j]['mb_id']?>"><?=$INVEST[$j]['mb_id']?></a><br>
								<a href="/adm/member/member_view.php?&mb_id=<?=$INVEST[$j]['mb_id']?>#ft" class="btn btn-info" style="font-size:11px; line-height:11px; padding:3px 4px;">전체투자내역</a>
							</td>
							<td align="center">
								<a href="javascript:;" onClick="balance_check(<?=$INVEST[$j]['mb_no']?>)" style="color:blue"><?=$INVEST[$j]['mb_name']?></a>
								<? if($_SESSION['ss_accounting_admin']) { echo '<br>('.$INVEST[$j]['jumin'].')'; } ?>
							</td>
							<td align="right"><span style="cursor:pointer" onClick="balance_check(<?=$INVEST[$j]['mb_no']?>);"><?=number_format($INVEST[$j]['amount'])?></span></td>
							<td align="center"><?=$invest_type?></td>
							<td align="center"><?=$TMP['insert_date']." ".$TMP['insert_time']?></td>
							<td align="center"><?=$receive_method?></td>
							<td align="center"><?=$bank?></td>
							<td align="center"><?=$account_num?></td>
							<td align="center"><?=number_format($MTOTAL_INVEST_SUM[$INVEST[$j]['member_idx']]['count'])?> 건</td>
							<td align="right"><?=number_format($MTOTAL_INVEST_SUM[$INVEST[$j]['member_idx']]['amount'])?></td>
							<td align="center"><?=$cancel_button?></td>
						</tr>
<?
	}
?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script>
	investCancel = function(idx) {
		if( confirm('투자번호: ' + idx
			        + '\n\n해당 투자내역 취소 및 투자된 예치금이 반환되며,\n'
		          + '모집이 완료된 상품인 경우, 모집중인 상태로 전환됩니다.\n\n'
		          + '진행하시겠습니까?') ) {

			$.ajax({
				url: '/adm/repayment/invest_cancel.ajax.php',
				type: 'post',
				dataType: 'json',
				data:{
					invest_idx: idx
				},
				success: function(data) {
					if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.'); window.location.reload(); }
					else if(data.result=='ERROR') {
						if(data.message=='LOGIN_PLEASE') {
							window.location.replace('/');
						}
						else {
							alert(data.message);
						}
					}
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});

		}
	}
	</script>
<?
}
?>
</div><!-- /.row -->

<!-- 인사이드뱅크 데이터 전송요청 창 //-->
<div id="repay_request_div" style="position:fixed; z-index:1; top:8%; left:0px; width:100%; height:100%; display:none;">
</div>
<!-- 인사이드뱅크 데이터 전송요청 창 //-->

<script>
listToggle = function(no) {
	$field = $('#list_area' + no);
	$button = $('#list_button' + no);

	$button.removeClass();

	if($field.css('display')=='block') {
		$button.html('내역보기 <span class="glyphicon glyphicon-list"></span>');
		$button.addClass('btn btn-xs btn-primary');
	}
	else {
		$button.html('접기 <span class="glyphicon glyphicon-minus"></span>');
		$button.addClass('btn btn-xs btn-default');
	}
	$field.toggle();
	//$field.slideToggle('slow');
}

$('#overdue_proc_button').click(function() {
	$this = $(this);
	start_date = $('#overdue_start_date').val();
	if(start_date=='') {
		alert('연체등록일자를 설정하십시요.');$('#overdue_start_date').focus();
	}
	else {
		if(confirm("연체 등록 하시겠습니까?")) {
			$.ajax({
				url: './product_calculate_proc.php',
				type: 'post',
				dataType: 'json',
				data:{
					action: 'overdue_start',
					idx: $this.data('idx'),
					turn: $this.data('turn'),
					start_date: $('#overdue_start_date').val(),
				},
				success: function(data) {
					$('#ajax_return_txt').val(data.result);
					if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.'); window.location.reload(); }
					else { alert(data.message); }
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
});
</script>

<script>
requestPopup = function() {
	if( $('#repay_request_div').css('display')=='none' ) {
		$('#repay_request_div').fadeIn();
		$.ajax({
			url:'./ajax_ib_repay_request.php',
			type:'post',
			success: function(result) {
				$('#repay_request_div').html(result);
				$.ajax({
					url:'./ajax_ib_send_wait_list.php',
					success: function(result) {
						$('#ib_wait_list').html(result);
					}
				});
			},
			error: function() { alert('통신 에러입니다.'); }
		});
	}
	else {
		$('#repay_request_div').fadeOut();
	}
}


loanInterestSuccess = function(idx, date, turn) {
	if(confirm("'대출이자 수급완료' 처리 하시겠습니까?")) {
		$.ajax({
			url:'./product_calculate_proc.php',
			type: 'post',
			dataType: 'json',
			data:{
				action: 'loan_interest_success',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				//$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

overdueRcvSuccess = function(idx, date, turn) {
	if(confirm("'연체이자 수급완료' 처리 하시겠습니까?")) {
		$.ajax({
			url:'./product_calculate_proc.php',
			type: 'post',
			dataType: 'json',
			data:{
				action: 'overdue_rcv_success',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				//$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

loanInterestGiveIBRequestReady = function(idx, date, turn) {
	if(confirm("신한은행 제3자 예치시스템으로 원리금 지급요청대기 처리 하시겠습니까?")) {
		$.ajax({
			url : "product_calculate_proc.php",
			type: 'post',
			dataType: 'json',
			data:{
				action: 'loan_interest_give_ib_request_ready',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
			//$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

overdueGiveIBRequestReady = function(idx, date, turn) {
	if(confirm("신한은행 제3자 예치시스템으로 연체이자 지급요청대기 처리 하시겠습니까?")) {
		$.ajax({
			url : "product_calculate_proc.php",
			type: 'post',
			dataType: 'json',
			data:{
				action: 'overdue_give_ib_request_ready',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				//$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}


loanInterestGive = function(idx, date, turn) {
	if(confirm("전체지급 처리 하시겠습니까?")) {
		$.ajax({
			url : "product_calculate_proc.php",
			type: 'post',
			dataType: 'json',
			data:{
				action: 'loan_interest_give',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

overdueGive = function(idx, date, turn) {
	if(confirm("연체이자 전체지급 처리 하시겠습니까?")) {
		$.ajax({
			url : "product_calculate_proc.php",
			type: 'post',
			dataType: 'json',
			data:{
				action: 'overdue_give',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

overdueGiveSuccess = function(idx, date, turn) {
	if(confirm("연체이자 지급완료 처리 하시겠습니까?")) {
		$.ajax({
			url : "product_calculate_proc.php",
			type: 'post',
			dataType: 'json',
			data:{
				action: 'overdue_give_success',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

investGiveSuccess = function(idx, date, turn) {
	if(confirm("'투자수익금 지급완료' 처리 하시겠습니까?")) {
		$.ajax({
			url: 'product_calculate_proc.php',
			type: 'post',
			dataType: 'json',
			data:{
				action: 'invest_give_success',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

loanPrincipalSuccess = function(idx, date, turn) {
	if(confirm("'대출원금 수급완료' 처리 하시겠습니까?")) {
		$.ajax({
			url: 'product_calculate_proc.php',
			type: 'post',
			dataType: 'json',
			data:{
				action: 'loan_principal_success',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

investPrincipalGiveSuccess = function(idx, date, turn) {
	if(confirm("'투자원금 지급완료' 처리 하시겠습니까?")) {
		$.ajax({
			url: './product_calculate_proc.php',
			type: 'post',
			dataType: 'json',
			data:{
				action: 'invest_principal_give_success',
				idx: idx,
				date: date,
				turn: turn
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') { alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

taxInvoiceRequest = function(doc_type, idx, turn, overdue) {
	if(doc_type=='c') {
		_doc_type = '세금계산서';
		url = 'tax_invoice_request_c.php';
	}
	else {
		_doc_type = '현금영수증';
		url = 'tax_invoice_request_p.php';
	}

	if(overdue=='overdue') {
		msg = '연체금상환(' + turn + '회차)건의 플랫폼이용료에 관한 ' + _doc_type + ' 발행을 실시합니다. 처리 하시겠습니까?';
	}
	else {
		msg = '원리금상환' + turn + '회차의 플랫폼이용료에 관한 ' + _doc_type + ' 발행을 실시합니다. 처리 하시겠습니까?';
	}

	if(confirm(msg)) {
		$.ajax({
			url: url,
			type: 'post',
			dataType:'json',
			data:{
				idx: idx,
				turn: turn,
				overdue: overdue,
				doc_type: doc_type
			},
			success: function(data) {
				$('#ajax_return_txt').val(data.result);
				if(data.result=='SUCCESS') {
					alert('정상 처리 완료되었습니다.\n페이지를 다시 호출 합니다.'); window.location.reload();
				}
				else { alert(data.message); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}
</script>

<?
unset($INI);
unset($REPAY);
unset($REPAY_SUM);


include_once ('./admin.tail.php');

sql_close();

?>