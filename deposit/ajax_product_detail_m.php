<style>
.tblXX { width:100%; border:1px solid #ccc; }
.tblXX th { padding:2px 4px 2px 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc; background-color:#F7F7F7 }
.tblXX td { padding:2px 4px 2px 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
</style>

<div style='width:98%;position:fixed;margin-top:0;'>
	<img src="../images/btn_close.gif" alt="close" class="close">
	<div class="title">투자내역 상세보기</div>
</div>
<div class="con" style="padding-top:30px;">
	<h3 style="text-align:left;padding-top:10px;"><b>[투자내역 상세보기 안내]</b></h3>
	<div style="margin:16px 14px 20px 14px; font-size:9pt;text-align:left;">
		<ol>
			<li style="list-style-type:decimal;">
				투자수익으로 인해 발생된 세금을 국세청에 원천징수 할 때에는 원단위를 절사합니다. 이 때 절사된 금액을 '실 지급액'에 합산하여 투자자분에게 지급하므로 실 지급액은 계산된 금액보다 클 수 있습니다.
			</li>
			<li style="list-style-type:decimal;margin-top:4px;">
				투자 원금은 대출자의 원금 상환 후 영업일 5일 이내에 월이자와 함께 지급됩니다.
			</li>
			<li style="list-style-type:decimal;margin-top:4px;">
				이자 선지급 상품의 경우 각 회차별 지급예정일과 지급상태 표기일이 다를 수 있습니다.
			</li>
			<li style="list-style-type:decimal;margin-top:4px;">
				만기일시상환을 기준으로 표기된 회차별 이자는 조기상환 등의 이유로 변동될 수 있습니다.
			</li>
			<li style="list-style-type:decimal;margin-top:4px;">
				매월 투자원금의 0.1% 를 플랫폼 이용료로 수취합니다. (단, 면제상품은 플랫폼이용료를 수취하지 않습니다.)
				<table style="margin:6px 0;font-size:1.0em">
					<tr>
						<td style="line-height:14px;">※ 플랫폼 이용료 산정식</td>
					</tr>
					<tr>
						<td style="padding:4px;line-height:14px;background:#EFEFEF">
							투자금액의 연 1.2%(<strong>월 0.1%</strong>) 의 금액을 365일로 나눈 금액(˚일별플랫폼이용료)에 상환회차월별 일수를 곱한 금액을 산정합니다.
							단, 윤년에 귀속된 상환회차의 경우 366일로 나눈 금액을 산정함.
						</td>
					</tr>
					<tr>
						<td style="padding-top:6px;line-height:14px;">※ 원천징수액 산정식</td>
					</tr>
					<tr>
						<? if($member['member_type']=='2') { ?>
						<td valign="top" style="padding:4px;line-height:14px;background:#EFEFEF">투자수익에 소득세(25%)와 주민세(2.5%가)가 추가되어 27.5%가 세금으로 산정됩니다.</td>
						<? } else if($member['member_type']=='1') { ?>
						<td valign="top" style="padding:4px;line-height:14px;background:#EFEFEF">투자수익에 소득세(14%)와 주민세(1.4%가)가 추가되어 15.4%가 세금으로 산정됩니다.</td>
						<? } else { ?>
						<td style="line-height: 1.5;padding:4px;background:#EFEFEF">
						투자수익에 소득세(14%)와 주민세(1.4%가)가 추가되어 15.4%가 세금으로 산정됩니다.<br />
						투자수익에 소득세(25%)와 주민세(2.5%가)가 추가되어 27.5%가 세금으로 산정됩니다.
						</td>
						<? } ?>
					</tr>
				</table>
			</li>
		</ol>
	</div>
	<div class="type03_2 mb30">
		<table class="table_procuct_detail">
			<colgroup>
				<col width="33.34%">
				<col width="33.33%">
				<col width="33.33%">
			</colgroup>
			<tr>
				<th colspan="3" style="background:#EEE">상품 및 투자 정보</th>
			</tr>
			<tr>
				<th>상품명</th>
				<td colspan="2"><?=$PSTATE['PRDT']["title"]?></td>
			<tr>
				<th>대출기간</th>
				<td colspan="2"><?if( in_array($PSTATE['PRDT']['state'], array('1','2','5')) ) { echo preg_replace("/-/", ".", $PSTATE['PRDT']['loan_start_date']).' ~ '.preg_replace("/-/", ".", $PSTATE['PRDT']['loan_end_date']); } ?></td>
			</tr>
			<tr>
				<th>투자금</th>
				<th><?=($PSTATE['PRDT']['invest_days'] > 0) ? '일수' : '개월수';?></th>
				<th>지급회차</th>
			</tr>
			<tr>
				<td><?=number_format($PSTATE['INVEST'][0]['amount'])?>원</td>
				<td><?=($PSTATE['PRDT']['invest_days'] > 0) ? $PSTATE['PRDT']['invest_days'].'일' : $PSTATE['PRDT']['invest_period'].'개월';?></td>
				<td><?=number_format($PAID['max_turn'])?> / <?=number_format($PSTATE['PRDT']['total_repay_turn'])?></td>
			</tr>

			<tr>
				<th>연수익률</th>
				<th>플랫폼<br>이용료율</th>
				<th>세전이자</th>
			</tr>
			<tr>
				<td><?=$PSTATE['PRDT']['invest_return']?>%</td>
				<td><?=$print_invest_usefee?></td>
				<td><?=number_format($TOTAL['invest_interest']);?>원</td>
			</tr>
			<tr>
				<th>플랫폼<br>이용료</th>
				<th>원천징수</th>
				<th>지급이자</th>
			</tr>
			<tr>
				<td><?=number_format($TOTAL['invest_usefee']);?>원</td>
				<td><?=number_format($TOTAL['tax']);?>원</td>
				<td><?=number_format($TOTAL['interest']);?>원</td>
			</tr>
		</table>
	</div>
<?
if( count($LIST) ) {

	for($i=0,$j=1; $i<count($LIST); $i++,$j++) {

		switch($LIST[$i]['gubun']) {
			case 'overdue_repay' : $bgcolor = '#FF3333'; $fcolor = '#FF0000'; break;
			case 'partial_repay' : $bgcolor = '#3333FF'; $fcolor = '#3333FF'; break;
			default              : $bgcolor = '#222'; $fcolor = ''; break;
		}

?>
	<div style="width:100%;padding:2px;margin-bottom:2px; border-radius:4px; background:<?=$bgcolor?>;color:#FFF;text-align:center; font-size:12px"><b><?=$LIST[$i]['repay_title']?> <?=$LIST[$i]['repay_schedule_date']?" (예정 ".$LIST[$i]['repay_schedule_date'].")":""?></b></div>
	<div class="mb10">
		<table class="tblXX">
			<colgroup>
				<col width="40%">
				<col width="60%">
			</colgroup>
			<tbody>
				<tr>
					<th>지급계좌</th>
					<td style="text-align:center;background-color:#FAFAFA"><?=$LIST[$i]['repay_bank_info']?></td>
				</tr>
				<tr>
					<th>이자</th>
					<td style="text-align:right"><?=number_format($LIST[$i]['invest_interest'])?>원</td>
				</tr>
				<tr>
					<th>원금</th>
					<td style="text-align:right"><?=number_format($LIST[$i]['repay_principal'])?>원</td>
				</tr>
				<tr>
					<th>플랫폼이용료</th>
					<td style="text-align:right"><?=number_format($LIST[$i]['invest_usefee'])?>원</td>
				</tr>
				<tr>
					<th>원천징수</th>
					<td style="text-align:right"><?=number_format($LIST[$i]['tax'])?>원</td>
				</tr>
				<tr>
					<th>실지급액</th>
					<td style="text-align:right;color:#3366FF"><?=number_format($LIST[$i]['last_repay_amount'])?>원</td>
				</tr>
				<tr>
					<th>지급상태</th>
					<td style="text-align:right;"><?=$LIST[$i]['repay_status']?></td>
				</tr>
			</tbody>
		</table>
	</div>
<?
	}

}

?>
	<div class="mb20">
		<div style="padding-top:4px;text-align:left;color:#FF3333;font-size:12px">
			<? if($PSTATE['PRDT']['idx']=='95') { ?>
			* 본 상품은 선이자 지급 상품입니다. 원금은 2016년 12월 12일에 상환됩니다.
			<? } else { ?>
			* 원금은 마지막 회차에 이자와 합산하여 지급됩니다.<br>
			* 상환지연 또는 연체시 투자금대비 (연)<?=$PSTATE['PRDT']['overdue_rate']?>% 의 일별이자 × 상환지연일수로 정산하여 지급됩니다.
			<? } ?>
		</div>
	</div>

	<span id="no" class="btn_big_link">닫기</span>

	<div style="height:10px;"></div>

</div>

<?

@sql_close();
exit;

?>