<?
include_once('./_common.php');

if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }
//if ($_SERVER["REQUEST_METHOD"]!="GET") { echo "ERROR-DATA"; exit; }


$invest_idx = trim($_REQUEST["idx"]);
$sql = "
	SELECT
		amount, product_idx, invest_state
	FROM
		cf_product_invest
	WHERE
		idx = '$invest_idx' AND member_idx='{$member['mb_no']}'";
$INVEST = sql_fetch($sql);
if(!$INVEST) { echo "ERROR-DATA"; exit; }

// 상환 스케쥴 가져오기
$PSTATE = investStatement($INVEST['product_idx'], $INVEST['amount'], '', '', $invest_idx);


$print_invest_usefee = ($PSTATE['PRDT']['invest_usefee']>'0.00')? '월 '.sprintf('%.2f', $PSTATE['PRDT']['invest_usefee']/12).'%' : '면제';


if(G5_IS_MOBILE) {
	include_once("ajax_product_detail_m.php");
	return;
}

?>

	<style>
	.titX { max-width:120px; text-align:center; font-size:14px; font-weight:bold; padding:3px; color:#000; margin-bottom:2px; margin-left:8px; }
	.con { overflow:scroll; height:500px; overflow-x:hidden; border-top:1px solid #aaa; }
	@media (min-height:1001px) { .con { height:600px; } }
	@media (max-height:1000px) { .con { height:550px; } }
	@media (max-height:900px)  { .con { height:500px } }
	@media (max-height:850px)  { .con { height:450px } }
	@media (max-height:800px)  { .con { height:400px } }
	@media (max-height:750px)  { .con { height:350px } }
	@media (max-height:700px)  { .con { height:300px } }
	@media (max-height:650px)  { .con { height:250px } }
	@media (max-height:600px)  { .con { height:200px } }
	@media (max-height:550px)  { .con { height:150px } }
	</style>

	<img src="../images/btn_close.gif" alt="close" class="close">
	<div class="title">투자내역 상세보기</div>
	<h3 style="text-align:left;padding-top:10px;padding-left:30px"><b>[투자내역 상세보기 안내]</b></h3>

	<div style="margin:20px 50px; font-size:10pt;text-align:left;">
		<ol>
			<li style="list-style-type:decimal;">
				투자수익으로 인해 발생된 세금을 국세청에 원천징수 할 때에는 원단위를 절사합니다.<br>
				이 때 절사된 금액을 '실 지급액'에 합산하여 투자자분에게 지급하므로 실 지급액은 계산된 금액보다 클 수 있습니다.
			</li>
			<li style="list-style-type:decimal;margin-top:6px;">
			  투자 원금은 대출자의 원금 상환 후 영업일 5일 이내에 월이자와 함께 지급됩니다.
			</li>
			<li style="list-style-type:decimal;margin-top:6px;">
				이자 선지급 상품의 경우 각 회차별 지급예정일과 지급상태 표기일이 다를 수 있습니다.
			</li>
			<li style="list-style-type:decimal;margin-top:6px;">
				만기일시상환을 기준으로 표기된 회차별 이자는 조기상환 등의 이유로 변동될 수 있습니다.
			</li>
			<li style="list-style-type:decimal;margin-top:6px;">
				매월 투자원금의 0.1% 를 플랫폼 이용료로 수취합니다. (단, 면제상품은 플랫폼이용료를 수취하지 않습니다.)<br>
				<table style="width:96%;margin:6px 00">
					<colgroup>
						<col style="width:20%">
						<col>
					</colgroup>
					<tr>
						<td valign="top" style="padding:2px 6px">※ 플랫폼 이용료 산정식 :</td>
						<td valign="top" style="padding:2px 6px">투자금액의 연 1.2%(<strong>월 0.1%</strong>) 의 금액을 365일로 나눈 금액(˚일별플랫폼이용료)에 상환회차월별 일수를 곱한 금액을 산정합니다.</td>
					</tr>
					<tr>
						<td valign="top" style="padding:2px 6px">※ 원천징수액 산정식 :</td>
						<td valign="top" style="padding:2px 6px">투자수익에 소득세(25%)와 주민세(2.5%가)가 추가되어 27.5%가 세금으로 산정됩니다.</td>
					</tr>
				</table>
			</li>
		</ol>
	</div>

	<div class="con">
		<div style="text-align:left;font-family:NGB;font-size:16px">● 상품 정보</div>
		<div class="type03_2 mb20">
			<table>
				<thead>
					<tr>
						<th>상품명</th>
						<th>기간</th>
						<th>총 회차</th>
						<th>지급 회차</th>
						<th>투자금</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?=$PSTATE['PRDT']["title"]?></td>
						<td><?if( in_array($PSTATE['PRDT']['state'], array('1','2','5')) ) { echo preg_replace("/-/", ".", $PSTATE['PRDT']['loan_start_date']).' ~ '.preg_replace("/-/", ".", $PSTATE['PRDT']['loan_end_date']); } ?></td>
						<td><?=number_format($PSTATE['INI']['repay_count'])?></td>
						<td><?=number_format($PSTATE['REPAYSUM']['last_repay_turn'])?></td>
						<td><?=number_format($INVEST['amount'])?>원</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div style="text-align:left;font-family:NGB;font-size:16px">● 투자 정보</div>
		<div class="type03_2 mb20">
			<table>
        <colgroup>
					<col style="width:16%">
					<col style="width:16%">
					<col style="width:16%">
					<col style="width:16%">
					<col style="width:16%">
					<col style="width:17%">
				</colgroup>
				<thead>
					<tr>
						<th>연수익률</th>
						<th>총이자</th>
						<th>플랫폼<br>이용료율(%)</th>
						<th>플랫폼<br>이용료</th>
						<th>원천징수</th>
						<th>지급이자<br>(세후)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
<? if($INVEST['invest_state']=="Y") { ?>
						<td><?=$PSTATE['PRDT']['invest_return']?>%</td>
						<td><?=number_format($PSTATE['REPAYSUM']['invest_interest']);?>원</td>
						<td><?=$print_invest_usefee?></td>
						<td><?=number_format($PSTATE['REPAYSUM']['invest_usefee']);?>원</td>
						<td><?=number_format($PSTATE['REPAYSUM']['withhold']);?>원</td>
						<td><?=number_format($PSTATE['REPAYSUM']['interest']);?>원</td>
<? } else { ?>
						<td align="center" colspan="6">투자 정보가 없습니다.</td>
<? } ?>
					</tr>
				</tbody>
			</table>
		</div>

<?
if($INVEST['invest_state']=="Y") {
	if( in_array($PSTATE['PRDT']['state'], array('1','2','5','7')) ) {
?>
		<div style="text-align:left;font-family:NGB;font-size:16px">● 지급 내역</div>
		<div class="type03">
			<table>
        <colgroup>
					<col style="width:12.5%">
					<col style="width:12.5%">
					<col style="width:12.5%">
					<col style="width:13%">
					<col style="width:12%">
					<col style="width:12%">
					<col style="width:13%">
					<col style="width:12.5%">
				</colgroup>
				<thead>
					<tr>
						<th>회차<br>지급예정일</th>
						<th>지급계좌</th>
						<th>이자</th>
						<th>상환원금</th>
						<th>플랫폼<br>이용료</th>
						<th>원천징수</th>
						<th>실지급액</th>
						<th>지급상태</th>
					</tr>
				</thead>
				<tbody>
<?
		$repay_count = count($PSTATE['REPAY']);
		for ($i=0,$j=1; $i<$repay_count; $i++,$j++) {

			if($PSTATE['REPAY'][$i]['SUCCESS']['loan_interest_state']=='Y') {
				if($PSTATE['REPAY'][$i]['paied']=='Y') {
					$repay_status = "<span style='color:green;line-height:18px'>지급<br>".preg_replace("/-/", ".", substr($PSTATE['REPAY'][$i]['banking_date'], 0, 10))."</span>";
				}
				else {
					$repay_status = '정산처리중';
				}
				$repay_bank_info = ($PSTATE['REPAY'][$i]['receive_method']=='1') ? $PSTATE['REPAY'][$i]['bank_name']." ".$PSTATE['REPAY'][$i]['account_num']  : "예치금 지급";
			}
			else {
				$repay_status    = "";
				$repay_bank_info = "";
			}

			//6호상품 선이자 지급결과 임시 처리 (편집일:2016-12-09)
			if($PSTATE['PRDT']['idx']=='95') {
				$repay_status = "<span style='color:green;line-height:18px'>2016.11.10<br>지급완료</span>";
			}

			echo '
					<tr>
						<td style="font-size:12px;">'.$j.'차<br>
							<div style="margin-top:2px;padding:1px;background-color:#999;color:#fff;border-radius:3px;"><strong>'.preg_replace("/-/", ".", $PSTATE['REPAY'][$i]['repay_schedule_date']).'</strong></div>
						</td>
						<td style="font-size:12px;">'.$repay_bank_info.'</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['invest_interest']).'원</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['principal']).'원</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['invest_usefee']).'원</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['withhold']).'원</td>
						<td style="font-size:12px;text-align:right;color:#3366FF">'.number_format($PSTATE['REPAY'][$i]['send_price']).'원</td>
						<td style="font-size:12px;">'.$repay_status.'</td>
					</tr>' . PHP_EOL;


			if($PSTATE['REPAY'][$i]['OVERDUE']['give_idx']) {

				$print_title = ($PSTATE['REPAY'][$i]['OVERDUE']['day_count'] > 30) ? '연체이자<br>('.$PSTATE['REPAY'][$i]['OVERDUE']['day_count'].'일분)' : '상환지연이자<br>('.$PSTATE['REPAY'][$i]['OVERDUE']['day_count'].'일분)';

				if($PSTATE['REPAY'][$i]['paied']=='Y') {
					$repay_status = "<span style='color:green;line-height:18px'>지급<br>".preg_replace("/-/", ".", substr($PSTATE['REPAY'][$i]['OVERDUE']['banking_date'], 0, 10))."</span>";
				}
				else {
					$repay_status = '정산처리중';
				}
				$repay_bank_info = ($PSTATE['REPAY'][$i]['receive_method']=='1') ? $PSTATE['REPAY'][$i]['OVERDUE']['bank_name']." ".$PSTATE['REPAY'][$i]['OVERDUE']['account_num']  : "예치금 지급";

				echo '
					<tr>
						<td style="font-size:12px;">'.$print_title.'<br>
							<div style="margin-top:2px;padding:1px;background-color:#999;color:#fff;border-radius:3px;"><strong>'.preg_replace("/-/", ".", $PSTATE['REPAY'][$i]['OVERDUE']['repay_schedule_date']).'</strong></div>
						</td>
						<td style="font-size:12px;">'.$repay_bank_info.'</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['OVERDUE']['invest_interest']).'원</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['OVERDUE']['principal']).'원</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['OVERDUE']['invest_usefee']).'원</td>
						<td style="font-size:12px;text-align:right">'.number_format($PSTATE['REPAY'][$i]['OVERDUE']['withhold']).'원</td>
						<td style="font-size:12px;text-align:right;color:#3366FF">'.number_format($PSTATE['REPAY'][$i]['OVERDUE']['send_price']).'원</td>
						<td style="font-size:12px;">'.$repay_status.'</td>
					</tr>' . PHP_EOL;

			}

		}
?>
				</tbody>
			</table>
			<div style="padding-top:8px;text-align:left;line-height:18px;color:#FF3333">
				<? if($PSTATE['PRDT']['idx']=='95') { ?>
				* 본 상품은 선이자 지급 상품입니다. 원금은 2016년 12월 12일에 상환됩니다.
				<? } else { ?>
				* 원금은 마지막 회차에 이자와 합산하여 지급됩니다.<br>
				* 상환지연 또는 연체시 투자금대비 (연)<?=$PSTATE['PRDT']['overdue_rate']?>% 의 일별이자 × 상환지연일수로 정산하여 지급됩니다.
				<? } ?>
			</div>
		</div>
<?
	}
}
?>
		<div style="margin:10px auto 10px">
			<span id="no" class="btn_big_link">닫기</span>
		</div>
	</div>