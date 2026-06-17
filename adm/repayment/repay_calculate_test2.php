<?
###############################################################################
## /adm/product_calculate_test.php 를 본파일로 대체
###############################################################################

include_once('./_common.php');


auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sub_menu = '700100';
$g5['title'] = $menu['menu700'][1][1] . " > 정산상세";


$TBL['product'] = 'cf_product';
$TBL['invest']  = 'cf_product_invest';
$TBL['invest_detail'] = 'cf_product_invest_detail';
$TBL['success'] = 'cf_product_success';
$TBL['give']    = 'cf_product_give_test';
$TBL['member']  = 'g5_member';
$TBL['bill']    = getBillTable($_REQUEST['idx']);

$is_control_user = ( in_array($_SESSION['ss_mb_id'], $CONF['OPERATOR']) ) ? true : false;
$prd_idx         = trim($_REQUEST['idx']);			// 상품번호를기준
$mb_no           = trim($_REQUEST['mb_no']);		// 특정 투자자만 조회 할 경우


$PRDT = sql_fetch("SELECT * FROM {$TBL['product']} WHERE idx = '".$prd_idx."'");
if(!$PRDT['idx']) {
	msg_go('해당 상품은 존재하지 않습니다.');
}
else {

	$ib_trust = ($PRDT['ib_trust']=='Y' && $PRDT['ib_product_regist']=='Y') ? true : false;

	if($PRDT['loan_start_date'] == '0000-00-00')      $PRDT['loan_start_date']      = '';
	if($PRDT['loan_end_date'] == '0000-00-00')        $PRDT['loan_start_date']      = '';
	if($PRDT['recruit_period_start'] == '0000-00-00') $PRDT['recruit_period_start'] = '';
	if($PRDT['recruit_period_end'] == '0000-00-00')   $PRDT['recruit_period_end']   = '';

	if($PRDT['recruit_period_start'] && $PRDT['recruit_period_end']) {
		$PRINT['recruit_period'] = preg_replace("/-/", ".", $PRDT['recruit_period_start']) . " ~ " . preg_replace("/-/", ".", $PRDT['recruit_period_end']);
	}

	// 특별처리상품 플래그 (초기상품중 종료일이 5일 이전일때 이전회차와 최종상환회차를 동일회차로 처리한 상품 구분)
	$exceptionProduct = ($PRDT['idx'] < 162  && $PRDT['ib_trust']=='N' && substr($PRDT['loan_end_date'],-2) <= '05') ? 1 : 0;
	$shortTermProduct = ($PRDT['invest_days']>0) ? 1 : 0;

	if( in_array($PRDT['state'], array('','1','2','5','8','4','9')) ) {
		if( $PRDT['invest_end_date'] && ($PRDT['loan_start_date'] && $PRDT['loan_end_date']) ) {
			$INI['total_invest_days'] = repayDayCount($PRDT['loan_start_date'], $PRDT['loan_end_date']);		// 상환대상일수
			$INI['total_repay_turn']  = repayTurnCount($PRDT['loan_start_date'], $PRDT['loan_end_date'], $exceptionProduct, $shortTermProduct);		// 상환차수
		}
	}

	$state = '';
	if($PRDT['state']) {
		if($PRDT['state']=='1') { $state = '이자상환중'; $state_code = '2'; }
		if($PRDT['state']=='2') { $state = '상품마감'; }
		if($PRDT['state']=='4') { $state = '부실'; }
		if($PRDT['state']=='5') { $state = '중도상환'; $state_code = '2'; }
		if($PRDT['state']=='6') { $state = '대출취소(기표전)'; }
		if($PRDT['state']=='7') { $state = '대출취소(기표후)'; }
		if($PRDT['state']=='8') { $state = '연체'; }
		if($PRDT['state']=='9') { $state = '부도(상환불가)'; }
	}
	else {
		if($PRDT['open_datetime'] > G5_TIME_YMDHIS) { $state = '투자대기중'; }
		if(($PRDT['start_datetime'] < G5_TIME_YMDHIS && $PRDT['end_datetime'] > G5_TIME_YMDHIS) && $PRDT['invest_end_date'] == '') { $state = '투자모집중'; }
		if($PRDT['end_datetime'] < G5_TIME_YMDHIS && $PRDT['invest_end_date'] == '') { $state = '투자금 모집실패'; $state_code = '3'; }
		if($PRDT['invest_end_date'] != '' && $PRDT['state'] == '') { $state = '대기중'; $state_code = '1'; }
	}

	if($PRDT['loan_mb_no']) {
		$LOANER = sql_fetch("SELECT mb_no, mb_id, member_type, mb_name, mb_co_name, mb_hp, va_bank_code2, virtual_account2, va_private_name2  FROM {$TBL['member']} WHERE mb_no='".$PRDT['loan_mb_no']."' AND member_group='L'");
		$LOANER['mb_hp'] = masterDecrypt($LOANER['mb_hp'], false);
	}

	$ROW = sql_fetch("
		SELECT
			COUNT(idx) AS cnt,
			IFNULL(SUM(amount),0) AS amt
		FROM
			{$TBL['invest']}
		WHERE 1
			AND product_idx='".$PRDT['idx']."' AND invest_state='Y'");

	$INVEST['count']   = $ROW['cnt'];
	$INVEST['amount']  = $ROW['amt'];

	$INVEST['advance_invest_amount'] = sql_fetch("SELECT IFNULL(SUM(amount),0) AS sum_amount FROM {$TBL['invest_detail']} WHERE product_idx='".$PRDT['idx']."' AND invest_state='Y' AND is_advance_invest='Y'")['sum_amount'];
	$INVEST['auto_invest_amount']    = sql_fetch("SELECT IFNULL(SUM(amount),0) AS sum_amount FROM {$TBL['invest_detail']} WHERE product_idx='".$PRDT['idx']."' AND invest_state='Y' AND is_auto_invest='1'")['sum_amount'];
	$INVEST['normal_invest_amount'] = $INVEST['amount'] - $INVEST['auto_invest_amount'];
	$INVEST['end_date'] = sql_fetch("SELECT insert_datetime FROM {$TBL['invest']} WHERE product_idx='".$PRDT['idx']."' AND invest_state='Y' ORDER BY idx DESC LIMIT 1")['insert_datetime'];

	$PAID['last_paid_turn'] = sql_fetch("SELECT IFNULL(MAX(turn),0) AS max_turn FROM {$TBL['give']} WHERE product_idx='".$prd_idx."' AND turn_sno='0' AND is_overdue='N'")['max_turn'];
	$PRINT['repay_turn'] = ($INI['total_repay_turn']) ? $PAID['last_paid_turn'] . ' / ' . $INI['total_repay_turn'] : '';

	if($LOANER['member_type']=='2') {
		$PRINT['loaner_nm'] = $LOANER['mb_co_name'];
	}
	else if($LOANER['member_type']=='1') {
		$PRINT['loaner_nm'] = $LOANER['mb_co_name'];
	}

	$PRINT['interest_rate'] = "(연) " . floatRtrim($PRDT['invest_return']) . "%";
	if($PRDT['overdue_rate'] > 0) $PRINT['interest_rate'].= ' (연체시 ' . floatRtrim($PRDT['overdue_rate']) . '%)';

	$PRINT['invest_period'] = ($PRDT['state'] == '') ? $PRDT['invest_period'].'개월' : preg_replace('/-/', '.', $PRDT['loan_start_date']).' ~ '.preg_replace('/-/', '.', $PRDT['loan_end_date']);		// 대출기간
	$PRINT['invest_month']  = ($PRDT['invest_days'] > 0) ? $PRDT['invest_days'].'일' : $PRDT['invest_period'].'개월';
	if($LOANER['va_bank_code2'] && $LOANER['virtual_account2'] && $LOANER['va_private_name2']) {
		$PRINT['loaner_acct_info'] = $BANK[$LOANER['va_bank_code2']] . ' &nbsp; ' . $LOANER['virtual_account2'] . ' &nbsp; ' . $LOANER['va_private_name2'];
	}
	$PRINT['category'] = "";
	if($PRDT['category']=='1') {
		$PRINT['category'].= "동산";
	}
	else if($PRDT['category']=='2') {
		$PRINT['category'].= "부동산";
		$PRINT['category'].= ($PRDT['mortgage_guarantees']=='1') ? " > 주택담보" : " > PF";
	}
	else if($PRDT['category']=='3') {
		if($PRDT['category2']=='1') $PRINT['category'].= "소상공인 확정매출채권";
		if($PRDT['category2']=='2') $PRINT['category'].= "면세점 확정매출채권 ";
	}

	if($PRDT['loan_usefee'] > 0) $PRINT['loan_usefee'] = floatRtrim($PRDT['loan_usefee']) . '%';
	if($PRDT['invest_usefee'] > 0) $PRINT['invest_usefee'] = "(연) " . floatRtrim($PRDT['invest_usefee']) . '%';

	$PRINT['withhold_tax_rate'] = '(연) ' . floatRtrim($PRDT['withhold_tax_rate']) . '%';

	$PRINT['investSummary'] = number_format($INVEST['amount']) . "원";
	if($INVEST['amount'] > 0) {
		$PRINT['investSummary'].= " (";
		$PRINT['investSummary'].= "모집율: " . @($INVEST['amount'] / $PRDT['recruit_amount']) * 100 . '%';
		$PRINT['investSummary'].= ($INVEST['advance_invest_amount']) ? ' / 사전투자: ' . number_format($INVEST['advance_invest_amount']) . '원' : '';
		$PRINT['investSummary'].= ($INVEST['auto_invest_amount']) ? ' / 자동투자: ' . number_format($INVEST['auto_invest_amount']) . '원' : '';
		$PRINT['investSummary'].= " / 일반투자: " . number_format($INVEST['normal_invest_amount']) . '원';
		$PRINT['investSummary'].= ")";
	}

	if($PRDT['invest_end_date']) {
		$PRINT['invest_end_dt'] = substr(preg_replace("/-/", ".", $INVEST['end_date']), 0, 16);
		$PRINT['recruit_interval'] = getDateInterval($PRDT['open_datetime'], $INVEST['end_date']);
	}

}


// 일별 정산내역 조회
$sql = "SELECT COUNT(idx) AS cnt FROM {$TBL['bill']} WHERE product_idx='".$PRDT['idx']."'";
$bill_count = sql_fetch($sql)['cnt'];

// 정산내역산정(재산정) 버튼
$proc_title = ($bill_count) ? '정산내역 재산정' : '정산내역 산정';
$MAKEBILLBTN = array(
	'title' => $proc_title,
	'id'    => '',
	'class' => ''
);

if( in_array($PRDT['state'], array('1','8')) || ($PRDT['state']=='' && $PRDT['invest_end_date']) ) {
	$MAKEBILLBUTTON['id']    = 'make_bill_button';
	$MAKEBILLBUTTON['class'] = 'btn-warning';
}
if($member['mb_id']=='admin_sori9th') {
	$MAKEBILLBTN['id']    = 'make_bill_button';
	$MAKEBILLBTN['class'] = 'btn-warning';
}


// 상환용가상계좌폐쇄 버튼 설정
$loaner_vacct_drop_button = '';
$vacct_drop_msg = '';
if( $ib_trust && $PRDT['repay_acct_no'] ) {
	$ING_PRDT = sql_fetch("SELECT COUNT(idx) AS cnt FROM {$TBL['product']} WHERE state IN('1','8','9') AND repay_acct_no='".$PRDT['repay_acct_no']."'");
	if($ING_PRDT['cnt']==0) {

		$DATA1 = sql_fetch("SELECT COUNT(*) AS cnt FROM KSNET_VR_ACCOUNT WHERE VR_ACCT_NO = '".$PRDT['repay_acct_no']."' AND USE_FLAG = 'Y'");
		$DATA2 = sql_fetch("SELECT COUNT(FB_SEQ) AS cnt FROM IB_vact_hellocrowd WHERE acct_no = '".$PRDT['repay_acct_no']."' AND acct_st = '1'");
		if($DATA1['cnt'] || $DATA2['cnt']) {
			$vacct_drop_msg.= "처리 후 당 계좌로의 입금이 되지 않습니다.\\n상환용 가상계좌를 해제 하시겠습니까?";
			$loaner_vacct_drop_button = '<button type="button" onClick="if(confirm(\''.$vacct_drop_msg.'\')){ loanerVacctDrop(\''.$PRDT['idx'].'\'); }" class="btn btn-sm btn-danger">상환계좌해제</button>';
		}
		else {
			$loaner_vacct_drop_button = '<button type="button" class="btn btn-sm btn-gray">상환계좌해제</button>';
		}
	}
}

$page_reload_msg = "페이지를 다시 호출 합니다.";


include_once(G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
.prdt_table { margin:0; }
.prdt_table th { background:#EFEFEF;color:#777;font-weight:normal; }
.table th.border_r { border-right:1px solid #999; }
.table td.border_r { border-right:1px solid #999; }
.min1200 { min-width:1200px; }
.font12 { font-size:12px; }
.topbtn { min-width:100px; }
input::placeholder { text-align:center; }
td.subtitle { background:#F8F8EF; font-size:16px; font-weight:bold; }

.tab_container ul.tabX { height:32px; background:url('/images/tab_bg.gif') repeat-x left bottom; }
.tab_container ul.tabX li { float:left; margin-right:1px; padding:0 2px; line-height:30px; text-align:center; font-size:12px; color:#AAA; background-color:#F7F7F7; border:1px solid #E5E5E5; border-bottom:0; cursor:pointer; }
.tab_container ul.tabX li.on { border:1px solid #ccc; background-color:#fff; border-bottom-color:#fff; color:#000; font-weight:bold; }
.tab_container ul.tabX li:last-child { margin:0; display:inline-block; }
.tab_container .tab_content { margin:0; padding:20px; border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; }

.tblx { font-size:12px; }
.tblx td { padding:4px 6px 3px; }

.mb20 { margin-bottom:20px; }
.pd8 { padding:8px; }

.switch { cursor:pointer; }
</style>

<div class="min1200" style="width:100%;">
	<div class="panel-body">
		<div class="dataTable_wrapper">

<? if($mode=='debug') { ?>
			<ul style="width:100%;list-style:none;display:inline-block; padding:0; border:1px dotted">
				<li style="width:50%;float:left;"><div style='font-size:12px;max-height:150px;margin:4px;overflow-y:scroll'>$PRDT : <?=print_rr($PRDT, 'font-size:12px;line-height:14px;');?></li>
				<li style="width:50%;float:left;"><div style='font-size:12px;max-height:150px;margin:4px;overflow-y:scroll'>$LOANER : <?=print_rr($LOANER, 'font-size:12px;line-height:14px;');?></li>
				<li style="width:50%;float:left;"><div style='font-size:12px;max-height:150px;margin:4px;overflow-y:scroll'>$INI : <?=print_rr($INI, 'font-size:12px;line-height:14px;');?></li>
				<li style="width:50%;float:left;"><div style='font-size:12px;max-height:150px;margin:4px;overflow-y:scroll'>$INVEST : <?=print_rr($INVEST, 'font-size:12px;line-height:14px;');?></li>
				<li style="width:50%;float:left;"><div style='font-size:12px;max-height:150px;margin:4px;overflow-y:scroll'>$PRINT : <?=print_rr($PRINT, 'font-size:12px;line-height:14px;');?></li>
			</ul>
<? } ?>

<!-- 버튼영역 START //-->
			<div style="margin-bottom:8px; float:right;">
				<button type="button" class="btn btn-sm btn-default topbtn" onClick="location.href='/adm/repayment/invest_status_list_test.php?=<?=preg_replace("/&idx=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);?>'">상품목록</button>
				<button type="button" class="btn btn-sm btn-default topbtn" onClick="window.open('/adm/product/product_form.php?idx=<?=$prd_idx?>','product_pop','');">상품설정</button>
				<button type="button" class="btn btn-sm btn-default topbtn" onClick="window.open('/adm/product_calculate_pop.php?idx=<?=$prd_idx?>','loaninfo_pop','width=600,height=550');">대출정보</button>
				<button type='button' class='btn btn-sm btn-default topbtn' onClick="window.open('/adm/invest_repay_simulation.php?idx=<?=$prd_idx?>','simulation_pop','');">투자시뮬레이션</button>
				<button type="button" class="btn btn-sm btn-success topbtn" onClick="window.open('/adm/repayment/repay_calculate.php?idx=<?=$prd_idx?>','calculate_pop','');">(구)정산내역1</button>
				<button type="button" class="btn btn-sm btn-success topbtn" onClick="window.open('/adm/product_calculate.php?idx=<?=$prd_idx?>','calculate_pop','');">(구)정산내역2</button>
				<button type="button" class="btn btn-sm <?=$MAKEBILLBTN['class']?> topbtn" id="<?=$MAKEBILLBTN['id']?>"><?=$MAKEBILLBTN['title']?></button>
				<?=$loaner_vacct_drop_button?>
			</div>
			<script>
			$('#make_bill_button').click(function() {
				if( confirm('정산내역을 <?=($bill_count)?'재생성':'생성'?> 하시겠습니까?\n이미 등록된 정산내역은 삭제 됩니다.') ) {
					$.ajax({
						url:'/adm/repayment/make_bill.php',
						type:'post',
						dataType:'json',
						data:{ prd_idx:'<?=$prd_idx?>' },
						success: function(data) {
							if(data.result=='SUCCESS') { alert(data.message); window.location.replace('<?=$_SERVER['REQUEST_URI']?>'); }
							else if(data.result=='PRDT_NULL') { alert(data.message); }
							else if(data.result=='CHECK_SDATE') { alert(data.message); }
							else if(data.result=='CHECK_EDATE') { alert(data.message); }
							else if(data.result=='CHECK_DATE_BALANCE') { alert(data.message); }
							else if(data.result=='CREATE_TABLE_ERROR') { alert(data.message); }
							else { alert('등록시 이상이 발생. 관리자에게 문의 바랍니다.'); }
						},
						beforeSend: function() { loading('on'); },
						complete: function() { loading('off'); },
						error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
					});
				}
			});
			</script>
<!-- 버튼영역 END //-->

<!-- 상품개요 START //-->
			<table class="table table-bordered mb20" style="border:2px solid #000">
				<tr>
					<td class="subtitle"><span id="productSummary_switch" class="switch">▼ 상품 개요</span></td>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="productSummary" class="pd8">
							<table class="table table-bordered prdt_table">
								<colgroup>
									<col style="width:10%">
									<col style="width:11.66%">
									<col style="width:11.66%">
									<col style="width:10%">
									<col style="width:11.66%">
									<col style="width:11.66%">
									<col style="width:10%">
									<col style="width:11.66%">
									<col style="width:11.7%">
								</colgroup>
								<tr align="center">
									<th>상품명</th>
									<td colspan="5"><b style="color:#3366FF"><?=$PRDT['title']?></b></td>
									<th>대출자명</th>
									<td colspan="2"><?=$PRINT['loaner_nm']?></td>
								</tr>
								<tr align="center">
									<th>상품번호</th>
									<td colspan="2"><b><?=$PRDT['idx']?></b></td>
									<th>카테고리</th>
									<td colspan="2"><?=$PRINT['category']?></td>
									<th>대출금액</th>
									<td colspan="2"><b style="color:#FF2222"><?=($PRDT['recruit_amount'] >= 10000) ? price_cutting($PRDT['recruit_amount']) : number_format($PRDT['recruit_amount'])?>원 (￦<?=number_format($PRDT['recruit_amount'])?>)</b></td>
								</tr>
								<tr align="center">
									<th>설정기간</th>
									<td colspan="2"><?=$PRINT['invest_month']?></td>
									<th>대출이자율</th>
									<td colspan="2"><?=preg_replace("/\(연\)/", "<font style='font-size:11px'>(연)</font>", $PRINT['interest_rate'])?></td>
									<th>대출금상환계좌</th>
									<td colspan="2"><span class="font12"><?=$PRINT['loaner_acct_info']?></span></td>
								</tr>
								<tr align="center">
									<th>대출자이용료율</th>
									<td colspan="2"><?=$PRINT['loan_usefee']?></td>
									<th>투자자이용료율</th>
									<td colspan="2"><?=preg_replace("/\(연\)/", "<font style='font-size:11px'>(연)</font>", $PRINT['invest_usefee'])?></td>
									<th>원천세율</th>
									<td colspan="2"><?=preg_replace("/\(연\)/", "<font style='font-size:11px'>(연)</font>", $PRINT['withhold_tax_rate'])?></td>
								</tr>
							</table>
						</div>

					</td>
				</tr>
			</table>
<!-- 상품개요 END //-->

<!-- 투자요약 정보 START //-->
			<table class="table table-bordered mb20" style="border:2px solid #000">
				<tr>
					<td class="subtitle"><span id="investSummary_switch" class="switch">▼ 투자요약 정보</span></td>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="investSummary" class="pd8">
							<table class="table table-bordered prdt_table">
								<colgroup>
									<col style="width:10%">
									<col style="width:11.66%">
									<col style="width:11.66%">
									<col style="width:10%">
									<col style="width:11.66%">
									<col style="width:11.66%">
									<col style="width:10%">
									<col style="width:11.66%">
									<col style="width:11.7%">
								</colgroup>
								<tr align="center">
									<th>모집설정기간</th>
									<td colspan="2"><?=$PRINT['recruit_period']?></td>
									<th>모집마감일시</th>
									<td colspan="2"><?=$PRINT['invest_end_dt']?></td>
									<th>소요시간</th>
									<td colspan="2"><?=$PRINT['recruit_interval']?></td>
								</tr>
								<tr align="center">
									<th>투자기간</th>
									<td colspan="2"><?=$PRINT['invest_period']?></td>
									<th>이자계산일수</th>
									<td colspan="2"><?=($INI['total_invest_days'])?$INI['total_invest_days'].'일':'';?></td>
									<th>정산회차</th>
									<td colspan="2"><?=$PRINT['repay_turn']?></td>
								</tr>
								<tr align="center">
									<th>투자자수</th>
									<td colspan="2"><?=number_format($INVEST['count'])."명";?></td>
									<th>모집현황</th>
									<td colspan="5"><?=$PRINT['investSummary']?></td>
								</tr>
							</table>
						</div>

					</td>
				</tr>
			</table>
<!-- 투자요약 정보 END //-->

<?
if($ib_trust) {

	$USE_PRDT = sql_fetch("SELECT COUNT(idx) AS cnt FROM {$TBL['product']} WHERE display='Y' AND repay_acct_no='".$PRDT['repay_acct_no']."'");					// 상환용 가상계좌 사용상품수 추출
	$KSNET    = sql_fetch("SELECT VR_ACCT_NO, REF_NO FROM KSNET_VR_ACCOUNT WHERE USE_FLAG='Y' AND VR_ACCT_NO='".$PRDT['repay_acct_no']."'");		// KSNET 가상계좌 등록정보 추출

?>
<!-- 대출자 상환금 출납 내역 START //-->
			<table class="table table-bordered mb20" style="border:2px solid #000">
				<tr>
					<td class="subtitle"><span id="loanerMoneyLog_switch" class="switch">▼ 대출자 상환금 출납 내역</span>
						<? if($USE_PRDT['cnt'] > 1 && $PRDT['state']=='1') { ?>
						::: 상환계좌: <?=$PRDT['repay_acct_no']?>
						&nbsp; <button type="button" id="setRepayTarget" class="btn btn-sm btn-warning" <?=($prd_idx==$KSNET['REF_NO'])?'disabled':''?>>본 상품의 상환계좌로 지정</button>
						<script>
						$('#setRepayTarget').click(function() {
							if(confirm('본 상품번호를 참조번호로 설정하시겠습니까?\n\n주) 설정이후 <?=$PRDT['repay_acct_no']?> 계좌로 입금되는 내역은\n[품번.<?=$prd_idx?>] 상품의 상환건으로 등록됩니다.')) {
								$.ajax({
									url : 'repay_proc.php',
									type: 'POST',
									dataType: 'json',
									data: {
										action:'set_repay_target',
										idx:'<?=$PRDT['idx']?>',
										vacct:'<?=$KSNET['VR_ACCT_NO']?>'
									},
									success:function(data) {
										if(data.result=='SUCCESS') {
											alert('해당 계좌의 참조번호가 설정 되었습니다.\n\n<?=$page_reload_msg?>'); window.location.reload();
										}
										else {
											alert(data.message);
										}
									},
									beforeSend: function() { loading('on'); },
									complete: function() { loading('off'); },
									error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
								});
							}
						});
						</script>
						<? } ?>
					</td>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="loanerMoneyLog" class="pd8"></div>

						<div id="loadingLoanerMoneyLog" style="position:absolute; z-index:1000; width:100%; height:186px; display:none;">
							<div align="center" style="margin:60px auto;">
								<img src="/images/loading/ani_load.gif" width="24"><br/>
								<span style="display:inline-block;background:#888;color:#FFF;margin-top:8px; padding:0 10px; border-radius:12px;">loading</span>
							</div>
						</div>

					</td>
				</tr>
			</table>
			<script>
			loadLoanerMoneyLog = function() {
				$.ajax({
					url : "./ajax_loaner_money_log.php",
					type: "POST",
					data:{ idx:'<?=$PRDT['idx']?>', vacct:'<?=$KSNET['VR_ACCT_NO']?>', 'print_form':'1' },
					success:function(data) {
						$('#loanerMoneyLog').html(data);
					},
					beforeSend:function() { $('#loadingLoanerMoneyLog').css('display','block'); },
					complete:function() { $('#loadingLoanerMoneyLog').css('display','none'); },
					error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}

			$(document).ready(function(){ loadLoanerMoneyLog(); });
			</script>
<!-- 대출금 상환금 출납 내역 END //-->
<?
}
?>

<!-- 원금일부상환 등록내역 START //-->
			<table class="table table-bordered mb20" style="border:2px solid #000">
				<tr>
					<td class="subtitle"><span id="partialRepayLog_switch" class="switch">▼ 원금일부상환 등록내역</span>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="partialRepayLog" class="pd8">
							<table class="tblx table-bordered prdt_table">
								<tr align="center" style="background:#EEE">
									<td>NO</td>
									<td>상환일</td>
									<td>상환금액</td>
									<td>귀속회차</td>
									<td>등록관리자</td>
									<td>등록일시</td>
								</tr>
<?
$sql  = "SELECT * FROM cf_partial_redemption WHERE product_idx='".$prd_idx."' ORDER BY idx ASC";
$res  = sql_query($sql);
$rows = sql_num_rows($res);
if($rows) {
	for($i=0,$no=1; $i<$rows; $i++,$no++) {
		$R = sql_fetch_array($res);
?>
								<tr align="center">
									<td><?=$no?></td>
									<td><?=$R['account_day']?></td>
									<td align="right"><?=number_format($R['amount']);?></td>
									<td><?=$R['turn']?>회차</td>
									<td><?=$R['writer_id']?></td>
									<td><?=substr($R['rdate'],0,16)?></td>
								</tr>
<?
	}
}
else {
	echo "<tr align='center'><td colspan='6'>내역이 없습니다.</td></tr>\n";
}
?>
							</table>
						</div>

					</td>
				</tr>
			</table>
<!-- 원금일부상환 등록내역 END //-->

<!-- 관리자 메모 START //-->
			<table class="table table-bordered prdt_table mb20" style="border:2px solid #000">
				<tr>
					<td class="subtitle" style="padding:8px"><span id="adminMemo_switch" class="switch">▼ 관리자 메모</td>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="adminMemo" class="pd8">
							<div id="memo_list_area" style="margin-bottom:10px;width:100%"><!--목록영역--></div>
							<div class="dataTable_wrapper" style="margin:0 auto;">
								<input type="hidden" name="product_idx" value="<?=$PRDT['idx']?>">
								<textarea name="memo" id="memo" style="width:91%;height:60px"></textarea>
								<button type="button" id="memo_input_btn" class="btn btn-primary" style="width:8.5%;height:60px;">입력</button>
							</div>
						</div>

						<div id="loadingAdminMemo" style="position:absolute; z-index:1000; width:100%; height:186px; display:none;">
							<div align="center" style="margin:60px auto;">
								<img src="/images/loading/ani_load.gif" width="24"><br/>
								<span style="display:inline-block;background:#888;color:#FFF;margin-top:8px; padding:0 10px; border-radius:12px;">loading</span>
							</div>
						</div>

					</td>
				</tr>
			</table>

			<script>
			loadMemo = function(){
				$.ajax({
					url : "../ajax_invest_memo.php",
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
						url : "../ajax_invest_memo.php",
						type: "POST",
						data:{ mode:'delete', idx:idx },
						success:function(data){
							loadMemo();
						},
						beforeSend:function() { $('#loadingAdminMemo').css('display','block'); },
						complete:function() { $('#loadingAdminMemo').css('display','none'); },
						error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
					});
				}
			}

			$('#memo_input_btn').on('click', function(){
				var memo_val = $.trim( $('#memo').val() );
				if(memo_val=='') {
					alert('내용은?');
					$('#memo').focus();
					return;
				}
				else {
					$.ajax({
						url : "/adm/ajax_invest_memo.php",
						type: "POST",
						data:{ mode:'new', product_idx:<?=$PRDT['idx']?>, memo:memo_val },
						async:false,
						success:function(data){
							$('#memo').val('');
							$('#memo_list_area').html(data);
						},
						beforeSend:function() { $('#loadingAdminMemo').css('display','block'); },
						complete:function() { $('#loadingAdminMemo').css('display','none'); },
						error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
					});
				}
			});
			</script>
<!-- 관리자 메모 END //-->

<?
if( in_array($PRDT['state'], array('1','2','4','5','7','8','9')) ) {
?>
<!-- 투자자 정산요약 정보 START //-->
			<table class="table table-bordered prdt_table mb20" style="border:2px solid #000">
				<tr>
					<td class="subtitle"><span id="repaySummary_switch" class="switch">▼ 투자자 정산예정 및 지급합계</span></td>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="repaySummary" class="pd8"></div>

					</td>
				</tr>
			</table>
			<script>
			getRepaySummary = function(prd_idx, mb_no) {
				$.ajax({
					url : 'ajax.repay_summary.php',
					type: 'POST',
					data: {prd_idx:'<?=$prd_idx?>', mb_no:'<?=$mb_no?>'},
					success:function(data) {
						if(data) $('#repaySummary').html(data);
					},
					beforeSend:function() { $('#repaySummary').css('display','block'); },
					error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
			$(document).ready(function(){ getRepaySummary(); });
			</script>


			<div class="tab_container">
				<ul class="tabX" style="width:100%;list-style:none;padding-left:10px;margin:0;">
<? for($i=0,$j=1; $i<$INI['total_repay_turn']; $i++,$j++) { ?>
					<li class="<?=($i==0)?'on':'';?>" data-prd_idx="<?=$prd_idx?>" data-turn="<?=$j?>" date-mb_no="<?=$mb_no?>" style="min-width:2%;max-width:8%;width:<?=(98/$INI['total_repay_turn']).'%';?>"><?=$j?>회차</li>
<? } ?>
				</ul>
<? for($i=0,$j=1; $i<$INI['total_repay_turn']; $i++,$j++) { ?>
				<div id="area<?=$i?>" class="tab_content"></div>
<? } ?>
			</div>

			<script>
			$(document).ready(function() {
				$(this).addClass('on').siblings().removeClass('on');
				$('.tab_content').hide();
				//$('.tab_content:eq(0)').show();

				$('.tabX li').click(function() {

					var prd_idx = $(this).attr('data-prd_idx');
					var turn = $(this).attr('data-turn');
					var mb_no = $(this).attr('date-mb_no');

					//if($('.tab_content:eq('+cur+')').html()=='') {

						$.ajax({
							url : 'ajax.repay_log.php',
							type: 'POST',
							data: {
								prd_idx:prd_idx,
								turn:turn,
								mb_no:mb_no
							},
							success: function(data) {
								if(data) $('.tab_content:eq('+cur+')').html(data);
							},
							beforeSend: function() { loading('on'); },
							complete: function() { loading('off'); },
							error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
						});

					//}

					$(this).addClass('on').siblings().removeClass('on');
					var cur = $(this).index();
					$('.tab_content').hide();
					$('.tab_content:eq('+cur+')').show();
				});

			});
			</script>
<!-- 투자자 정산요약 정보 END //-->

<?
}
else {
?>
<!-- 기표전 투자자 현황 START //-->
			<table class="table table-bordered prdt_table mb20">
				<tr>
					<td class="subtitle"><span id="investList_switch" class="switch">▼ 투자자 현황</span></td>
				</tr>
				<tr>
					<td style="padding:0">

						<div id="investList" style="padding:8px"></div>

						<div id="loadingInvestList" style="position:absolute; z-index:1000; width:100%; height:186px; display:none;">
							<div align="center" style="margin:60px auto;">
								<img src="/images/loading/ani_load.gif" width="24"><br/>
								<span style="display:inline-block;background:#888;color:#FFF;margin-top:8px; padding:0 10px; border-radius:12px;">loading</span>
							</div>
						</div>

					</td>
				</tr>
			</table>
			<script>
			loadInvestList = function() {
				$.ajax({
					url : 'ajax.invest_list.php',
					type: 'POST',
					data: {prd_idx:'<?=$prd_idx?>'},
					success:function(data) {
						if(data) $('#investList').html(data);
					},
					beforeSend:function() { $('#loadingInvestList').css('display','block'); },
					complete:function() { $('#loadingInvestList').css('display','none'); },
					error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
			$(document).ready(function(){ loadInvestList(); });
			</script>
<!-- 기표전 투자자 현황 END //-->
<?
}
?>
		</div>
	</div>
</div>

<style>
#divBillDetail { display:none; position:fixed; z-index:1000000; width:100%;height:100%; left:0; top:0; min-width:1000px; min-height:500px; }
</style>
<div id="divBillDetail"></div>
<script>
openBillDetail = function(prd_idx, turn, member_idx, is_overdue, mode) {
	$.blockUI({
		message: $('#divBillDetail'),css:{ 'border':'0', 'position':'fixed' },
	});
	$('#divBillDetail').draggable();

	$.ajax({
		url: 'ajax.bill_detail.php',
		type: 'post',
		data:{
			prd_idx: prd_idx,
			turn: turn,
			member_idx: member_idx,
			is_overdue: is_overdue,
			mode: mode
		},
		success: function(data) {
			$('#divBillDetail').empty();
			$('#divBillDetail').html(data);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
}
</script>

<? if($loaner_vacct_drop_button) { ?>
<script>
loanerVacctDrop = function(idx) {
	$.ajax({
		url: 'ajax.loaner_vacct.proc.php',
		type: 'post',
		dataType: 'json',
		data: {mode:'drop', idx:idx,},
		success: function(data) {
			if(data.result=='SUCCESS') {
				alert('해제 완료 되었습니다.');
				window.location.reload();
			}
			else {
				alert(data.message);
			}
		},
		error: function(e) { console.log(e); }
	});
}
</script>
<? } ?>

<script>
$('#productSummary_switch').on('click', function() { $('#productSummary').slideToggle(); });
$('#investSummary_switch').on('click', function() { $('#investSummary').slideToggle(); });
$('#loanerMoneyLog_switch').on('click', function() { $('#loanerMoneyLog').slideToggle(); });
$('#partialRepayLog_switch').on('click', function() { $('#partialRepayLog').slideToggle(); });
$('#adminMemo_switch').on('click', function() { $('#adminMemo').slideToggle(); });
$('#repaySummary_switch').on('click', function() { $('#repaySummary').slideToggle(); });
$('#investList_switch').on('click', function() { $('#investList').slideToggle(); });
</script>

<script>
// 팝업 닫기
popupClose = function() {
	$.unblockUI();
	return false;
}
</script>

<?

include_once (G5_ADMIN_PATH.'/admin.tail.php');

?>