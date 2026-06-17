<h3>나의 투자한도</h3>
<div class="type03 mb30">
	<table class="tblX">
		<colgroup>
			<col style='width:28%'>
			<col style='width:36%'>
			<col style='width:36%'>
		</colgroup>
		<tr>
			<th>투자자 유형</th>
			<td style="text-align:center" colspan="2"><?=($member['member_type']=='2') ? '법인투자자' : $INDI_INVESTOR[$member['member_investor_type']]['title'];?></td>
		</tr>
		<tr>
			<th rowspan="2">누적투자금액</th>
			<th>전체</th>
			<th style="font-size:12px">대출 가이드라인<br>적용 이후</th>
		</tr>
		<tr>
			<td style="text-align:center"><?=number_format($member['total_invest_amount'])?>원</td>
			<td style="text-align:center"><?=number_format($member['total_invest_amount_new'])?>원</td>
		</tr>
		<tr>
			<th rowspan="2">투자잔액</th>
			<th>전체</th>
			<th style="font-size:12px">대출 가이드라인<br>적용 이후</th>
		</tr>
		<tr>
			<td style="text-align:center"><?=number_format($member['ing_invest_amount'])?>원</td>
			<td style="text-align:center"><?=number_format($member['ing_invest_amount_new'])?>원</td>
		</tr>
		<tr>
			<th rowspan="2">투자가능금액</th>
			<th>동산.헬로페이</th>
			<th>부동산.주택담보</th>
		</tr>
		<tr>
			<td style="text-align:center"><?=$invest_possible_amount_str?></td>
			<td style="text-align:center"><?=($INDI_INVESTOR[$member['member_investor_type']]['prpt_limit'])?number_format($member['invest_possible_amount_prpt']).'원' : '구분없음'?></td>
		</tr>
		<tr>
			<th>투자가능일</th>
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
			<td style="text-align:center;background:<?=$td_bgcolor?>;" rowspan="8"><?=$print_date?></td>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" rowspan="3">투자금액</td>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >부동산</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['start_amount_A']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >동산/기타</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['start_amount_B']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >전체</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['start_amount']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" rowspan="3">상환(예정)</td>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >부동산</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['exit_amount_A']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >동산/기타</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['exit_amount_B']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >전체</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['exit_amount']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" rowspan="2">투자가능액</td>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >부동산</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['possible_amount_A']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >전체</td>
			<td style="text-align:right"><?=$RS['LIST'][$i]['possible_amount']?></td>
		</tr>
<?
			}
			else {
?>
		<tr>
			<td style="width:30%;text-align:center;background:<?=$td_bgcolor?>;" rowspan="3" ><?=$print_date?></td>
			<td style="width:30%;height:20px;text-align:center;background:<?=$td_bgcolor?>;" >투자금액</td>
			<td style="width:40%;text-align:right;"><?=$RS['LIST'][$i]['start_amount']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;" >상환(예정)금액</td>
			<td style="text-align:right;"><?=$RS['LIST'][$i]['exit_amount']?></td>
		</tr>
		<tr>
			<td style="text-align:center;background:<?=$td_bgcolor?>;color:#FF2222" >투자가능금액</td>
			<td style="text-align:right;"><?=$RS['LIST'][$i]['possible_amount']?></td>
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
<?
@sql_close();
exit;
?>