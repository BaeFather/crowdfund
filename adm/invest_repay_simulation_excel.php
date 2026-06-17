<?

set_time_limit(0);

$sub_menu = '700200';
include_once('./_common.php');





auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출


$prd_idx            = trim($_REQUEST['idx']);											// 상품번호기준
$mb_id              = trim($_REQUEST['mb_id']);										// 특정 투자자만 조회 할 경우
$invest_period      = trim($_REQUEST['invest_period']);						// (시뮬레이션용) 투자개월수
$loan_start_date    = trim($_REQUEST['loan_start_date']);					// (시뮬레이션용) 투자시작일
$loan_end_date      = trim($_REQUEST['loan_end_date']);						// (시뮬레이션용) 투자만기일
$invest_usefee      = trim($_REQUEST['invest_usefee']);						// (시뮬레이션용) 플랫폼이용료율
$invest_usefee_type = trim($_REQUEST['invest_usefee_type']);			// (시뮬레이션용) 플랫폼이용료 징수방식
//$turn               = trim($_REQUEST['turn']);


$INV_ARR   = repayCalculation($prd_idx, $mb_id, $invest_period, $loan_start_date, $loan_end_date, $invest_usefee, $invest_usefee_type);

$INI       = $INV_ARR['INI'];
$PRDT      = $INV_ARR['PRDT'];
$LOANER    = $INV_ARR['LOANER'];
$INVEST    = $INV_ARR['INVEST'];
$MTOTAL_INVEST_SUM = $INV_ARR['MTOTAL_INVEST_SUM'];
$REPAY     = $INV_ARR['REPAY'];
$REPAY_SUM = $INV_ARR['REPAY_SUM'];


$ib_trust = ($PRDT['ib_trust']=='Y' && $PRDT['ib_product_regist']=='Y') ? true : false;


$date  = date('Y-m-d H:i:s');
$state = '';
if($PRDT['state']) {
	if($PRDT['state']=='1') { $state = '이자상환중'; $state_code = '2'; }
	if($PRDT['state']=='2') { $state = '상품마감'; }
	if($PRDT['state']=='4') { $state = '부실'; }
	if($PRDT['state']=='5') { $state = '중도상환'; $state_code = '2'; }
	if($PRDT['state']=='6') { $state = '대출계약취소(기표전)'; }
	if($PRDT['state']=='7') { $state = '대출계약취소(기표후)'; }
}
else {
	if ($PRDT['open_datetime'] > $date) { $state = '투자대기중'; }
	if ($PRDT['start_datetime'] < $date && $PRDT['end_datetime'] > $date && $PRDT['invest_end_date'] == '') { $state = '투자모집중'; }
	if ($PRDT['end_datetime'] < $date && $PRDT['invest_end_date'] == '') { $state = '투자금 모집실패'; $state_code = '3'; }
	if ($PRDT['invest_end_date'] != '' && $PRDT['state'] == '') { $state = '대기중'; $state_code = '1'; }
}


$now_date  = date('Y-m-d');
$file_name = $now_date."_투자상환시뮬레이션(".$PRDT['title']." ".$turn."회차).xls";
$file_name = iconv("utf-8", "euc-kr", $file_name);

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-description: PHP4 Generated Data" );


$i = $turn-1;
$list_count = count($REPAY[$i]['LIST']);

?>

				<table border="1">
					<tr>
						<td colspan="<?=($_SESSION['ss_accounting_admin'])?'15':'14'?>" style="background-color:#FFEBCC">
							이자지급 회차 : <?=$turn?>차 <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
							정산일 : <?=$REPAY[$i]['repay_date']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
							대상기간 : <?=preg_replace('/-/', '.', $REPAY[$i]['target_sdate'])?> ~ <?=preg_replace('/-/', '.', $REPAY[$i]['target_edate'])?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
							이자계산일수 : <?=$REPAY[$i]['day_count']?>일
						</td>
					</tr>
					<tr>
						<th style="background-color:#F8F8EF" colspan="<?=($_SESSION['ss_accounting_admin'])?'5':'4'?>">투자자</th>
						<th style="background-color:#F8F8EF" colspan="2">예상이자(세전)</th>
						<th style="background-color:#F8F8EF" colspan="3">누적</th>
						<th style="background-color:#F8F8EF" colspan="5">당월</th>
					</tr>
					<tr>
						<th style="width:3%;background-color:#F8F8EF">NO</th>
						<th style="width:6%;background-color:#F8F8EF">구분</th>
						<th style="width:7%;background-color:#F8F8EF">ID</th>
						<th style="background-color:#F8F8EF">이름</th>
						<? if($_SESSION['ss_accounting_admin']) { ?><th style="width:7%;background-color:#F8F8EF">주민번호</th><? } ?>
						<th style="width:7%;background-color:#F8F8EF">투자금</th>

						<th style="width:7%;background-color:#F8F8EF">전체</th>
						<th style="width:7%;background-color:#F8F8EF">당월</th>

						<th style="width:7%;background-color:#F8F8EF">플랫폼이용료</th>
						<th style="width:7%;background-color:#F8F8EF">원천징수</th>
						<th style="width:7%;background-color:#F8F8EF">실수령이자</th>

						<th style="width:7%;background-color:#F8F8EF">플랫폼이용료</th>
						<th style="width:7%;background-color:#F8F8EF">원천징수</th>
						<th style="width:7%;background-color:#F8F8EF">실수령이자</th>
						<th style="width:7%;background-color:#F8F8EF">상환원금</th>
					</tr>

<?
for($j=0,$num=$list_count; $j<$list_count; $j++,$num--) {

	$member_id   = $REPAY[$i]['LIST'][$j]['mb_id'];
	$member_name = $REPAY[$i]['LIST'][$j]['mb_name'];

	$member_type = "";
	$member_type.= ($REPAY[$i]['LIST'][$j]['member_type']=='2') ? "기업" : "개인";
	$member_type.= ($REPAY[$i]['LIST'][$j]['is_creditor']=='Y') ? "-대부" : "";

	if($REPAY[$i]['LIST'][$j]['receive_method']) {
		$receive_method = ($REPAY[$i]['LIST'][$j]['receive_method']=='1') ? '환급계좌' : '<font color="#FF2222">예치금</font>';
	}
	else {
		$receive_method = "미지정";
	}

	$principal = ($turn < $INI['repay_count']) ? 0 : $REPAY[$i]['LIST'][$j]['amount'];
	$principal_sum = $principal_sum + $principal;
?>
					<tr>
						<td align="center"><?=$num?></td>
						<td align="center"><?=$member_type?></td>
						<td align="center"><?=$member_id?></td>
						<td align="center"><?=$member_name?></td>
						<? if($_SESSION['ss_accounting_admin']) { ?><td align="center" style="mso-number-format:'@';"><?=$REPAY[$i]['LIST'][$j]['jumin']?></td><? } ?>
						<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['amount'])?></td>

						<td align="right"><?=number_format($REPAY_SUM[$member_id]['invest_interest'])?></td>
						<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['invest_interest'])?></td>

						<td align="right"><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['invest_usefee'])?></td>
						<td align="right"><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['TAX']['sum'])?></td>
						<td align="right"><?=number_format($REPAY[$i]['MEMBER_NUJUK'][$member_id]['interest'])?></td>

						<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['invest_usefee'])?></td>
						<td align="right"><?=number_format($REPAY[$i]['LIST'][$j]['TAX']['sum'])?></td>
						<td align="right"><span style='color:#3366FF'><?=number_format($REPAY[$i]['LIST'][$j]['interest'])?></span></td>
						<td align="right"><span style='color:#3366FF'><?=number_format($principal)?></span></td>
					</tr>
<?
}
?>
					<tr style="color:blue;">
						<td align="center" colspan="<?=($_SESSION['ss_accounting_admin'])?'5':'4';?>" style="background-color:#EDF4FC;">합계</td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['SUM']['amount'])?></td>

						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY_SUM['invest_interest'])?></td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['SUM']['invest_interest'])?></td>

						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['NUJUK_SUM']['invest_usefee'])?></td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['NUJUK_SUM']['TAX']['sum'])?></td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['NUJUK_SUM']['interest'])?></td>

						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['SUM']['invest_usefee'])?></td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['SUM']['TAX']['sum'])?></td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($REPAY[$i]['SUM']['interest'])?></td>
						<td align="right" style="background-color:#EDF4FC;"><?=number_format($principal_sum)?></td>
					</tr>
				</table>

<?
unset($INI);
unset($REPAY);
unset($REPAY_SUM);
?>