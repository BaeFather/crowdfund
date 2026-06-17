<?
include_once('./_common.php');

if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }

//print_rr($_REQUEST, "font-size:11px");

if($member['member_type']=='1') {
	if($member['member_investor_type']=='3') {
		$invest_possible_amount_str = "제한없음";
		$invest_possible_date = "상시가능";
	}
	else {
		$invest_possible_amount_str = number_format($member['invest_possible_amount'])."원";
		if($member['invest_possible_amount'] > 0) {
			$invest_possible_date = "상시가능";
		}
	}
}
if($member['member_type']=='2') {
	$invest_possible_amount_str = "제한없음";
	$invest_possible_date = "상시가능";
}

if($member['member_type']=='1' && in_array($member['member_investor_type'], array('1','2'))) {

	// 상품 및 투자정보
	$where = " A.member_idx = '".$member['mb_no']."'";
	$where.= " AND A.invest_state = 'Y' ";
	$where.= " AND A.product_idx > '".$CONF['old_type_end_prdt_idx']."' ";
	$where.= " AND B.state IN('','1','2','5') ";

	$sql = "
		SELECT
			A.amount, A.insert_date,
			B.state, B.category, B.invest_period, B.loan_start_date, B.loan_end_date
		FROM
			cf_product_invest A
		LEFT JOIN
			cf_product B
		ON
			A.product_idx=B.idx
		WHERE
			$where
		ORDER BY
			A.insert_date";

	$res  = sql_query($sql);
	$rows = sql_num_rows($res);

	$DLIST = array();

	for($i=0; $i<$rows; $i++) {
		$row = sql_fetch_array($res);
		if($row['loan_start_date']=='0000-00-00') $row['loan_start_date'] = "";

		if($row['loan_start_date']=='') {
			$row['loan_start_date'] = $row['insert_date'];
			$row['loan_end_date']   = date('Y-m-d', strtotime('+'.$row['invest_period'].' month', strtotime($row['loan_start_date']))+86400);
		}

		if(!in_array($row['loan_start_date'], $DLIST)) array_push($DLIST, $row['loan_start_date']);
		if(!in_array($row['loan_end_date'], $DLIST)) array_push($DLIST, $row['loan_end_date']);

		$SAMOUNT[$row['loan_start_date']] += $row['amount'];
		$EAMOUNT[$row['loan_end_date']]   += $row['amount'];

		if($row['category']=='2') {		//부동산 투자금액
			$SAMOUNT_A[$row['loan_start_date']] += $row['amount'];
			$EAMOUNT_A[$row['loan_end_date']]   += $row['amount'];
		}
		else {												//그외 투자금액
			$SAMOUNT_B[$row['loan_start_date']] += $row['amount'];
			$EAMOUNT_B[$row['loan_end_date']]   += $row['amount'];
		}

	}

	//print_rr($INVAMT);

	if( !in_array(date('Y-m-d'), $DLIST) ) array_push($DLIST, date('Y-m-d'));  //금일 데이터가 없으면 강제 배열추가
	sort($DLIST);

	//print_rr($DLIST, "font-size:11px;");


	// 배열 재생산 (출력용)
	$limit_amount   = $INDI_INVESTOR[$member['member_investor_type']]['site_limit'];	// 전체 투자한도
	$limit_amount_A = $INDI_INVESTOR[$member['member_investor_type']]['prpt_limit'];	// 부동산 투자한도

	$day_count = count($DLIST);

	for($i=0,$j=1; $i<$day_count; $i++,$j++) {

		$invest_possible_amount_tmp = $limit_amount;
		$invest_possible_amount_A_tmp = $limit_amount_A;

		if($SAMOUNT[$DLIST[$i]])   $invest_possible_amount_tmp = $invest_possible_amount_tmp - $SAMOUNT[$DLIST[$i]];
		if($SAMOUNT_A[$DLIST[$i]]) $invest_possible_amount_A_tmp = $invest_possible_amount_A_tmp - $SAMOUNT_A[$DLIST[$i]];

		if($EAMOUNT[$DLIST[$i]]) $invest_possible_amount_tmp = $invest_possible_amount_tmp + $EAMOUNT[$DLIST[$i]];
		if($EAMOUNT_A[$DLIST[$i]]) $invest_possible_amount_A_tmp = $invest_possible_amount_A_tmp + $EAMOUNT_A[$DLIST[$i]];

		$ing_amount = $ing_amount + $SAMOUNT[$DLIST[$i]] - $EAMOUNT[$DLIST[$i]];

		$print_start_amount   = number_format($SAMOUNT[$DLIST[$i]])."원";
		$print_start_amount_A = "<span style='color:#ccc'>".number_format($SAMOUNT_A[$DLIST[$i]])."원</span>";
		$print_start_amount_B = "<span style='color:#ccc'>".number_format($SAMOUNT_B[$DLIST[$i]])."원</span>";

		$print_exit_amount   = number_format($EAMOUNT[$DLIST[$i]])."원";
		$print_exit_amount_A = "<span style='color:#ccc'>".number_format($EAMOUNT_A[$DLIST[$i]])."원</span>";
		$print_exit_amount_B = "<span style='color:#ccc'>".number_format($EAMOUNT_B[$DLIST[$i]])."원</span>";

		$print_possible_amount   = "<span style='color:#FF2222'>".number_format($invest_possible_amount_tmp)."원</span>";
		$print_possible_amount_A = "<span style='color:#FF2222;opacity:0.6'>".number_format($invest_possible_amount_A_tmp)."원</span>";

		$print_ing_amount      = ($ing_amount > 0) ? number_format($ing_amount)."원" : "<span style='color:#ccc'>0원</span>";

		// 현재 투자한도금액 이상 투자한 경우 투자가능일 발생 일자 산출
		if($invest_possible_amount_tmp > 0) {
			if($invest_possible_date=='') $invest_possible_date = $DLIST[$i];
		}
		else {
			unset($invest_possible_date);
		}

		$RS['LIST'][$i] = array(
			'date'            => $DLIST[$i],										// 표기 일자
			'start_amount'    => $print_start_amount,						// 투자금액(전체)
			'start_amount_A'  => $print_start_amount_A,					// 투자금액(부동산)
			'start_amount_B'  => $print_start_amount_B,					// 투자금액(그외)
			'exit_amount'     => $print_exit_amount,						// 상환(예정)금액(전체)
			'exit_amount_A'   => $print_exit_amount_A,					// 상환(예정)금액(부동산)
			'exit_amount_B'   => $print_exit_amount_B,					// 상환(예정)금액(그외)
			'possible_amount' => $print_possible_amount,				// 투자가능금액(전체)
			'possible_amount_A' => $print_possible_amount_A,		// 투자가능금액(부동산)
			'ing_amount'      => $print_ing_amount							// 투자잔액(전체)
		);

		$limit_amount = $invest_possible_amount_tmp;
		$limit_amount_A = $invest_possible_amount_A_tmp;
	}

}


if(G5_IS_MOBILE) {
	include_once("ajax_invest_limit_m.php");
	return;
}

//print_rr($member,'font-size:11px;line-height:14px;');

?>

<style>
.tblX { width:100%; border:1px solid #ccc }
.tblX th, .tblX td { padding:0 4px 0 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
.tblX th.title { font-family:'NG'; font-size:12px; font-weight:bold; }
.tblX th.border_r { border-right:1px solid #999; }
.tblX td.border_r { border-right:1px solid #999; }
.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }
span.left  { float:left; }
span.right { float:right; }
</style>

<h3>나의 투자한도</h3>
<div class="type03 mb30">
	<table class="tblX">
		<colgroup>
			<col style='width:33.33%'>
			<col style='width:33.33%'>
			<col style='width:33.33%'>
		</colgroup>
		<tr>
			<th>투자자 유형</th>
			<td colspan="2"><?=($member['member_type']=='2') ? '법인투자자' : $INDI_INVESTOR[$member['member_investor_type']]['title'];?></td>
		</tr>
		<tr>
			<th rowspan="2">누적투자금액</th>
			<th>전체</th>
			<th>P2P 대출 가이드라인 적용 이후</th>
		</tr>
		<tr>
			<td><?=number_format($member['total_invest_amount'])?>원</td>
			<td><?=number_format($member['total_invest_amount_new'])?>원</td>
		</tr>
		<tr>
			<th rowspan="2">투자잔액</th>
			<th>전체</th>
			<th>P2P 대출 가이드라인 적용 이후</th>
		</tr>
		<tr>
			<td><?=number_format($member['ing_invest_amount'])?>원</td>
			<td><?=number_format($member['ing_invest_amount_new'])?>원</td>
		</tr>
		<tr>
			<th rowspan="2">투자가능금액</th>
			<th>전체</th>
			<th>부동산</th>
		</tr>
		<tr>
			<td><?=$invest_possible_amount_str?></td>
			<td><?=($INDI_INVESTOR[$member['member_investor_type']]['prpt_limit'])?number_format($member['invest_possible_amount_prpt']).'원' : '구분없음'?></td>
		</tr>
		<tr>
			<th>투자가능일</th>
			<td colspan="2"><?=$invest_possible_date?></td>
		</tr>
	</table>
	<div style="margin:8px 0 0 8px;font-size:12px;color:#2222FF;line-height:16px">
		* <?=date('Y년 m월 d일', strtotime($CONF['loan_guideline_date0']))?> - 금융위원회 시행 P2P 대출 가이드라인(1차) 적용<br>
		* <?=date('Y년 m월 d일', strtotime($CONF['loan_guideline_date1']))?> - 금융위원회 시행 P2P 대출 가이드라인(2차) 갱신
	</div>
</div>

<p>&nbsp;</p>

<?
if($member['member_type']=='1' && in_array($member['member_investor_type'], array('1','2'))) {
?>
<h3>투자 가능 스케쥴</h3>
<div>
	<table class="tblX">
		<colgroup>
			<col style='width:%'>
			<col style='width:11%'>
			<col style='width:10%'>
			<col style='width:11%'>
			<col style='width:11%'>
			<col style='width:11%'>
			<col style='width:11%'>
			<col style='width:<?=($member['member_investor_type']=='1')?12:20;?>%'>
			<? if($member['member_investor_type']=='1') { ?><col style='width:12%'><? } ?>
		</colgroup>
		<tr style="background-color:#F7F7F7;border-top:2px solid #284893;">
			<th class="title border_r" rowspan="2" style="text-align:center;">일자</th>
			<th class="title border_r" colspan="3" style="text-align:center;">투자금액</th>
			<th class="title border_r" colspan="3" style="text-align:center;">상환(예정)금액</th>
			<th class="title" <?=($member['member_investor_type']=='1')?'colspan="2"':'rowspan="2"';?> style="text-align:center;"><span style="color:#FF2222">투자가능금액</span></th>
		</tr>
		<tr style="background-color:#F7F7F7;">
			<th class="title" style="opacity:0.6;text-align:center;">부동산</th>
			<th class="title" style="opacity:0.6;text-align:center;font-size:0.8em;">동산/기타</th>
			<th class="title border_r" style="text-align:center;">전체</th>
			<th class="title" style="opacity:0.6;text-align:center;">부동산</th>
			<th class="title" style="opacity:0.6;text-align:center;font-size:0.8em;">동산/기타</th>
			<th class="title border_r" style="text-align:center;">전체</th>
			<? if($member['member_investor_type']=='1') { ?>
			<th class="title" style="text-align:center;"><span style="color:#FF2222;opacity:0.6">부동산</span></th>
			<th class="title" style="text-align:center;"><span style="color:#FF2222">전체</span></th>
			<? } ?>
		</tr>
		<!--
		<tr align="right" style="height:25px;background-color:#EFEFEF">
			<td align="center" class="border_r"><span style="color:brown">(적용기준일) <?=date("Y-m-d", strtotime('+1 day', strtotime($CONF['old_type_end_date'])));?></span></td>
			<td align="center"><span style="color:brown"></span></td>
			<td align="center"><span style="color:brown"></span></td>
			<td align="center" class="border_r"><span style="color:brown"></span></td>
			<td align="center"><span style="color:brown"></span></td>
			<td align="center"><span style="color:brown"></span></td>
			<td align="center" class="border_r"><span style="color:brown"></span></td>
			<? if($member['member_investor_type']=='1') { ?><td align="right"><span style="color:brown;opacity:0.6"><?=number_format($INDI_INVESTOR[$member['member_investor_type']]['prpt_limit'])?>원</span></td><? } ?>
			<td align="right"><span style="color:brown"><?=number_format($INDI_INVESTOR[$member['member_investor_type']]['site_limit'])?>원</span></td>
		</tr>
		//-->
<?
	for($i=0,$j=1; $i<$day_count; $i++,$j++) {

		if($RS['LIST'][$i]['date']==date('Y-m-d')) {
			$print_date = "<b>오늘</b>";
			$tr_bgcolor = "#E7E2F1";
		}
		else {
			$print_date = $RS['LIST'][$i]['date'];
			$tr_bgcolor = "";
		}

		if($RS['LIST'][$i]['date'] > $CONF['old_type_end_date']) {
?>
		<tr align="right" style="height:25px;background-color:<?=$tr_bgcolor?>">
			<td align="center" class="border_r" style="font-size:0.8em;"><?=$print_date?></td>
			<td style="font-size:0.8em;"><?=$RS['LIST'][$i]['start_amount_A']?></td>
			<td style="font-size:0.8em;"><?=$RS['LIST'][$i]['start_amount_B']?></td>
			<td class="border_r" style="font-size:0.8em;"><?=$RS['LIST'][$i]['start_amount']?></td>
			<td style="font-size:0.8em;"><?=$RS['LIST'][$i]['exit_amount_A']?></td>
			<td style="font-size:0.8em;"><?=$RS['LIST'][$i]['exit_amount_B']?></td>
			<td class="border_r" style="font-size:0.8em;"><?=$RS['LIST'][$i]['exit_amount']?></td>
			<? if($member['member_investor_type']=='1') { ?><td style="font-size:0.8em;"><?=$RS['LIST'][$i]['possible_amount_A']?></td><? } ?>
			<td style="font-size:0.8em;"><?=$RS['LIST'][$i]['possible_amount']?></td>
		</tr>
<?
		}
	}
?>
	</table>

<?
}
?>

</div>