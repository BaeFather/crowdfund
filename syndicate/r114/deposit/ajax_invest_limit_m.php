<style>
.tblX { width:100%; border:1px solid #ccc }
.tblX th, .tblX td { padding:4px 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
.tblX th.title { font-family:'NG'; font-size:12px; }
.tblX th.border_r { border-right:1px solid #999; }
.tblX td.border_r { border-right:1px solid #999; }
.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }
span.left  { float:left; }
span.right { float:right; }
</style>

<h3>나의 투자한도</h3>
<div class="type03 mb30">
	<table class="tblX">
		<colgroup>
			<col style='width:28%'>
			<col style='width:36%'>
			<col style='width:36%'>
		</colgroup>
		<tr>
			<th style="text-align:center">투자자 유형</th>
			<td style="text-align:center" colspan="2"><?=($member['member_type']=='2') ? '법인투자자' : $INDI_INVESTOR[$member['member_investor_type']]['title'];?></td>
		</tr>
		<tr>
			<th rowspan="2" style="text-align:center">누적투자금액</th>
			<th style="text-align:center">전체</th>
			<th style="text-align:center;font-size:12px">대출 가이드라인<br>적용 이후</th>
		</tr>
		<tr>
			<td style="text-align:center"><?=number_format($member['total_invest_amount'])?>원</td>
			<td style="text-align:center"><?=number_format($member['total_invest_amount_new'])?>원</td>
		</tr>
		<tr>
			<th rowspan="2" style="text-align:center">투자잔액</th>
			<th style="text-align:center">전체</th>
			<th style="text-align:center;font-size:12px">대출 가이드라인<br>적용 이후</th>
		</tr>
		<tr>
			<td style="text-align:center"><?=number_format($member['ing_invest_amount'])?>원</td>
			<td style="text-align:center"><?=number_format($member['ing_invest_amount_new'])?>원</td>
		</tr>
		<tr>
			<th rowspan="2" style="text-align:center">투자가능금액</th>
			<th style="text-align:center">전체</th>
			<th style="text-align:center">부동산</th>
		</tr>
		<tr>
			<td style="text-align:center"><?=$invest_possible_amount_str?></td>
			<td style="text-align:center"><?=($INDI_INVESTOR[$member['member_investor_type']]['prpt_limit'])?number_format($member['invest_possible_amount_prpt']).'원' : '구분없음'?></td>
		</tr>
		<tr>
			<th style="text-align:center">투자가능일</th>
			<td style="text-align:center" colspan="2"><?=$invest_possible_date?></td>
		</tr>
	</table>
	<div style="margin:8px 0 0 8px;font-size:11px;color:#2222FF;line-height:16px">
		* <?=date('Y년 m월 d일', strtotime($CONF['loan_guideline_date0']))?> - 금융위원회 시행 P2P 대출 가이드라인 적용<br>
		* <?=date('Y년 m월 d일', strtotime($CONF['loan_guideline_date1']))?> - 금융위원회 시행 P2P 대출 가이드라인(2차) 갱신
	</div>
</div>

<p>&nbsp;</p>

<?
if($member['member_type']=='1' && in_array($member['member_investor_type'], array('1','2'))) {
?>
<h3>투자 가능 스케쥴</h3>
<div>
	<table class="tblX" style="border-top:2px solid #284893;">
		<!--
		<tr style="background-color:#EFEFEF">
			<th rowspan="3" align="center"><span style="color:brown">적용기준일<br><?=date("Y-m-d", strtotime('+1 day', strtotime($CONF['old_type_end_date'])));?></span></th>
			<th class="title" style="height:20px;">투자금액</th>
			<th align="center"><span style="color:brown">-</span></th>
		</tr>
		<tr style="background-color:#EFEFEF">
			<th class="title" style="height:20px;">상환(예정)금액</th>
			<td align="center"><span style="color:brown">-</span></td>
		</tr>
		<tr style="background-color:#EFEFEF">
			<th class="title" style="height:20px;color:#FF2222">투자가능금액</th>
			<td align="right"><span style="color:brown"><?=number_format($INDI_INVESTOR[$member['member_investor_type']]['site_limit'])?>원</span></td>
		</tr>
		//-->
<?
	for($i=0,$j=1; $i<$day_count; $i++,$j++) {

		if($RS['LIST'][$i]['date']==date('Y-m-d')) {
			$print_date = "<b>오늘</b>";
			$td_bgcolor = "#E7E2F1";
		}
		else {
			$print_date = $RS['LIST'][$i]['date'];
			$td_bgcolor = '#EFEFEF';
		}

		if($RS['LIST'][$i]['date'] > $CONF['old_type_end_date']) {

			if($member['member_type']=='1' && $member['member_investor_type']=='1') {

?>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" rowspan="8" align="center"><?=$print_date?></td>
			<td bgcolor="<?=$td_bgcolor?>" rowspan="3" align="center">투자금액</td>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">부동산</td>
			<td align="right"><?=$RS['LIST'][$i]['start_amount_A']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">동산/기타</td>
			<td align="right"><?=$RS['LIST'][$i]['start_amount_B']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">전체</td>
			<td align="right"><?=$RS['LIST'][$i]['start_amount']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" rowspan="3" align="center">상환(예정)</td>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">부동산</td>
			<td align="right"><?=$RS['LIST'][$i]['exit_amount_A']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">동산/기타</td>
			<td align="right"><?=$RS['LIST'][$i]['exit_amount_B']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">전체</td>
			<td align="right"><?=$RS['LIST'][$i]['exit_amount']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" rowspan="2" align="center">투자가능액</td>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">부동산</td>
			<td align="right"><?=$RS['LIST'][$i]['possible_amount_A']?></td>
		</tr>
		<tr>
			<td bgcolor="<?=$td_bgcolor?>" class="title" align="center">전체</td>
			<td align="right"><?=$RS['LIST'][$i]['possible_amount']?></td>
		</tr>
<?
			}
			else {
?>
		<tr style="background-color:<?=$tr_bgcolor?>">
			<td width="30%" rowspan="3" align="center"><?=$print_date?></td>
			<td width="30%" class="title" align="center" style="height:20px;">투자금액</td>
			<td width="40%" align="right"><?=$RS['LIST'][$i]['start_amount']?></td>
		</tr>
		<tr style="background-color:<?=$tr_bgcolor?>">
			<td class="title" align="center" style="height:20px;">상환(예정)금액</td>
			<td align="right"><?=$RS['LIST'][$i]['exit_amount']?></td>
		</tr>
		<tr style="background-color:<?=$tr_bgcolor?>">
			<td class="title" align="center" style="height:20px;color:#FF2222">투자가능금액</td>
			<td align="right"><?=$RS['LIST'][$i]['possible_amount']?></td>
		</tr>
<?
			}
		}
	}
?>
	</table>

<?
}
?>

</div>