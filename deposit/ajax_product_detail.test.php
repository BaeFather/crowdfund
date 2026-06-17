<?

include_once('_common.php');
include_once(G5_LIB_PATH . '/repay_calculation_new.php');

if(!$member['mb_id']){ echo "ERROR-LOGIN"; exit; }


$invest_idx = trim($_REQUEST["idx"]);

$sql = "
	SELECT
		amount, product_idx, invest_state
	FROM
		cf_product_invest
	WHERE 1
		AND idx = '".$invest_idx."'
		AND member_idx = '".$member['mb_no']."'
		AND invest_state = 'Y'";
$INVEST = sql_fetch($sql);

if(!$INVEST) { echo "ERROR-DATA"; exit; }

// 실 상환된 마지막 정상상환회차 번호
$PAID = sql_fetch("
	SELECT
		IFNULL(MAX(turn), 0) AS max_turn
	FROM
		cf_product_give
	WHERE 1
		AND product_idx='".$INVEST['product_idx']."'
		AND member_idx='".$member['mb_no']."'
		AND turn_sno='0'
		AND is_overdue='N'");

// 상환 스케쥴 가져오기
$PSTATE = repayCalculationNew($INVEST['product_idx'], $member['mb_id']);

$repay_count = count($PSTATE['REPAY']);

if($repay_count) {

	$print_invest_usefee = ($PSTATE['PRDT']['invest_usefee']>'0.00')? floatRtrim($PSTATE['PRDT']['invest_usefee']/12).'% / 월' : '면제';


	$TOTAL['principal']       = $PSTATE['TOTAL_REPAY_SUM']['repay_principal'];
	$TOTAL['invest_interest'] = $PSTATE['TOTAL_REPAY_SUM']['invest_interest'];
	$TOTAL['tax']             = $PSTATE['TOTAL_REPAY_SUM']['TAX']['sum'];
	$TOTAL['invest_usefee']   = $PSTATE['TOTAL_REPAY_SUM']['invest_usefee'];
	$TOTAL['interest']        = $PSTATE['TOTAL_REPAY_SUM']['interest'];

	/////////////////////////////////////////////////////////////
	// 차수별 원리금 상환 내역 배열화 (연체,부분상환 포함)
	/////////////////////////////////////////////////////////////
	$LIST = array();

	for($i=0,$j=1; $i<$repay_count; $i++,$j++) {

		$REPAY = $PSTATE['REPAY'][$i];

		////////////////////////////////////////////////
		// 정규 회차 상환내역
		////////////////////////////////////////////////
		$RLIST['gubun']               = 'regular';
		$RLIST['turn']                = $REPAY['turn'];
		$RLIST['repay_title']         = $REPAY['turn'] . "회차 원리금";
		$RLIST['repay_schedule_date'] = preg_replace("/-/", ".",$REPAY['repay_schedule_date']);

		if($REPAY['LIST'][0]['paied'] == 'Y') {
			$RLIST['repay_bank_info'] = ($REPAY['LIST'][0]['receive_method'] == '1') ? $REPAY['LIST'][0]['bank']." ".substr($REPAY['LIST'][0]['account_num'], 0, -4) . "****" : "예치금 지급";
		}
		else {
			$RLIST['repay_bank_info'] = "";
		}
		$RLIST['repay_principal']     = $REPAY['LIST'][0]['repay_principal'];
		$RLIST['invest_interest']     = $REPAY['LIST'][0]['invest_interest'];
		$RLIST['tax']                 = $REPAY['LIST'][0]['TAX']['sum'];
		$RLIST['invest_usefee']       = $REPAY['LIST'][0]['invest_usefee'];
		$RLIST['last_repay_amount']   = $REPAY['LIST'][0]['repay_principal'] + $REPAY['LIST'][0]['interest'];

		// 2020-08-05 최종회차에 이자만 상환을 해야할 경우를 위한 임시 방편
		//echo $RLIST['turn'].":".$repay_count."\n";
		if( $INVEST['product_idx']=='3023' && $RLIST['turn']==$repay_count ) {
			if($REPAY['SUCCESS']['invest_give_state']=='Y' && $REPAY['SUCCESS']['invest_principal_give']=='Y') {
				//
			}
			else {
				if($REPAY['SUCCESS']['invest_give_state']=='Y') {
					$RLIST['repay_title'] = $REPAY['turn'] . "회차 이자";
					$RLIST['last_repay_amount'] = $REPAY['LIST'][0]['interest'];
				}
			}
		}

		$RLIST['paid']                = $REPAY['LIST'][0]['paied'];

		$repay_status = "";
		if($REPAY['SUCCESS']['invest_give_state'] == 'Y') {
			$repay_status.= "<span style='color:green;line-height:18px'>지급";
			$repay_status.= (G5_IS_MOBILE) ? "&nbsp;" : "<br/>";
			$repay_status.= preg_replace("/-/", ".", substr($REPAY['LIST'][0]['banking_date'], 0, 10))."</span>";
		} else {
			if($REPAY['SUCCESS']['ib_request_ready']) $repay_status = '정산처리중';
		}
		$RLIST['repay_status'] = $repay_status;

		if($PSTATE['PRDT']['idx']=='95') $RLIST['repay_status'] = "지급<br/>2016.11.10";			// 6호상품 선이자 지급결과 임시 처리 (편집일:2016-12-09)

		array_push($LIST, $RLIST);
		unset($RLIST);


		////////////////////////////////////////////////
		// 연체내역
		////////////////////////////////////////////////
		if( $PSTATE['REPAY'][$i]['OVERDUE']['start_date'] ) {

			$OVERDUE = $REPAY['OVERDUE'];

			$OLIST['gubun']               = 'overdue_repay';
			$OLIST['turn']                = $REPAY['turn'];
			$OLIST['repay_title']         = $REPAY['turn'] . '회차 연체이자 (' . $OVERDUE['day_count'] . '일)';
			$OLIST['repay_schedule_date'] = '';

			if($OVERDUE['LIST'][0]['paied'] == 'Y') {
				$OLIST['repay_bank_info'] = ($OVERDUE['LIST'][0]['receive_method']=='1') ? $OVERDUE['LIST'][0]['bank']." ".substr($OVERDUE['LIST'][0]['account_num'], 0, -4) . "****" : "예치금 지급";
			} else {
				$OLIST['repay_bank_info'] = '';
			}
			$OLIST['repay_principal']     = $OVERDUE['LIST'][0]['repay_principal'];
			$OLIST['invest_interest']     = $OVERDUE['LIST'][0]['invest_interest'];
			$OLIST['tax']                 = $OVERDUE['LIST'][0]['TAX']['sum'];
			$OLIST['invest_usefee']       = $OVERDUE['LIST'][0]['invest_usefee'];
			$OLIST['last_repay_amount']   = $OVERDUE['LIST'][0]['interest'] + $OVERDUE['LIST'][0]['repay_principal'];
			$OLIST['paid']                = $OVERDUE['LIST'][0]['paied'];

			$ovd_repay_status = "";
			if($OVERDUE['SUCCESS']['overdue_give'] == 'Y') {
				$ovd_repay_status.= "<span style='color:green;line-height:18px'>지급";
				$ovd_repay_status.= (G5_IS_MOBILE) ? "&nbsp;" : "<br/>";
				$ovd_repay_status.= preg_replace("/-/", ".", substr($OVERDUE['LIST'][0]['banking_date'], 0, 10))."</span>";
			} else {
				if($OVERDUE['SUCCESS']['overdue_ib_request_ready']) $ovd_repay_status = '정산처리중';
			}
			$OLIST['repay_status'] = $ovd_repay_status;

			array_push($LIST, $OLIST);
			unset($OLIST);

		}


		////////////////////////////////////////////////
		// 일부원금상환내역
		////////////////////////////////////////////////
		if( count($PSTATE['REPAY'][$i]['PARTIAL']) ) {
			for($k=0; $k<count($PSTATE['REPAY'][$i]['PARTIAL']); $k++) {

				$PARTIAL = $PSTATE['REPAY'][$i]['PARTIAL'][$k];

				$PTLIST['gubun']               = 'partial_repay';
				$PTLIST['turn']                = $REPAY['turn']."-".$PARTIAL['turn_sno'];
				$PTLIST['repay_title']         = $REPAY['turn'] . '회차 원금상환';
				if($PARTIAL['turn_sno']>1) $PTLIST['repay_title'] = '('.$PARTIAL['turn_sno'].')';
				$PTLIST['repay_schedule_date'] = '';

				if($PARTIAL['LIST'][0]['paied'] == 'Y') {
					$PTLIST['repay_bank_info']     = ($PARTIAL['LIST'][0]['receive_method']=='1') ? $PARTIAL['LIST'][0]['bank']." ".substr($PARTIAL['LIST'][0]['account_num'], 0, -4) . "****" : "예치금 지급";
				} else {
					$PTLIST['repay_bank_info']     = '';
				}
				$PTLIST['repay_principal']     = $PARTIAL['LIST'][0]['repay_principal'];
				$PTLIST['invest_interest']     = $PARTIAL['LIST'][0]['invest_interest'];
				$PTLIST['tax']                 = $PARTIAL['LIST'][0]['TAX']['sum'];
				$PTLIST['invest_usefee']       = $PARTIAL['LIST'][0]['invest_usefee'];
				$PTLIST['last_repay_amount']   = $PARTIAL['LIST'][0]['interest'] + $PARTIAL['LIST'][0]['repay_principal'];
				$PTLIST['paid']                = $PARTIAL['LIST'][0]['paied'];

				$ptl_repay_status = "";
				if($PARTIAL['SUCCESS']['invest_principal_give'] == 'Y') {
					$ptl_repay_status.= "<span style='color:green;line-height:18px'>지급";
					$ptl_repay_status.= (G5_IS_MOBILE) ? "&nbsp;" : "<br/>";
					$ptl_repay_status.= preg_replace("/-/", ".", substr($PARTIAL['LIST'][0]['banking_date'], 0, 10))."</span>";
				}
				else {
					if($PARTIAL['SUCCESS']['ib_request_ready']) $ptl_repay_status = '정산처리중';
				}
				$PTLIST['repay_status'] = $ptl_repay_status;

				array_push($LIST, $PTLIST);
				unset($PTLIST);

			}		// end for($k=0; $k<count($PSTATE['REPAY'][$i]['PARTIAL']); $k++)
		}

	}		// end for($i=0,$j=1; $i<$repay_count; $i++,$j++)

}

//print_rr($LIST, 'text-align:left; font-size:12px;width:100%;height:150px; overflow-x:scroll');


if(G5_IS_MOBILE) {
	include_once("ajax_product_detail_m.php");
	return;
}

?>
<style>
.con { overflow:scroll; height:400px; margin:0 auto; overflow-x:hidden; }
@media (min-height:1001px) { .con { height:500px; } }
@media (max-height:1000px) { .con { height:450px; } }
@media (max-height:900px)  { .con { height:400px } }
@media (max-height:850px)  { .con { height:350px } }
@media (max-height:800px)  { .con { height:300px } }
@media (max-height:750px)  { .con { height:250px } }
@media (max-height:700px)  { .con { height:200px } }
@media (max-height:650px)  { .con { height:150px } }
</style>

<div class="title">투자내역 상세보기</div>
<img src="../images/btn_close.gif" alt="close" class="close">
<h3 style="text-align:left;padding-top:10px;padding-left:30px"><b>[투자내역 상세보기 안내]</b></h3>

<div style="margin:20px 50px; font-size:10pt;text-align:left;">
	<ol>
		<li style="list-style-type:decimal;">
			투자수익으로 인해 발생된 세금을 국세청에 원천징수 할 때에는 원단위를 절사합니다.<br>
			이 때 절사된 금액을 '실 지급액'에 합산하여 투자자분에게 지급하므로 실 지급액은 계산된 금액보다 클 수 있습니다.
		</li>
		<li style="list-style-type:decimal;margin-top:6px;">
			투자 원금은 대출자의 원금 상환 후 영업일 5일 이내에 원금 상환일까지 발생한 미지급 이자와 함께 지급됩니다.
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
					<td valign="top" style="padding:2px 6px">
						투자금액의 연 1.2%(<strong>월 0.1%</strong>) 의 금액을 365일로 나눈 금액(˚일별플랫폼이용료)에 상환회차월별 일수를 곱한 금액을 산정합니다.
						단, 윤년에 귀속된 상환회차의 경우 366일로 나눈 금액을 산정함.
					</td>
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
					<th>연수익률</th>
					<th>플랫폼<br>이용료율(%)</th>
					<th>지급회차</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?=$PSTATE['PRDT']["title"]?></td>
					<td><?if( in_array($PSTATE['PRDT']['state'], array('1','2','5')) ) { echo preg_replace("/-/", ".", $PSTATE['PRDT']['loan_start_date']).' ~ '.preg_replace("/-/", ".", $PSTATE['PRDT']['loan_end_date']); } ?></td>
					<td><?=floatRtrim($PSTATE['PRDT']['invest_return'])?>%</td>
					<td><?=$print_invest_usefee?></td>
					<td><?=number_format($PAID['max_turn'])?> / <?=number_format($PSTATE['PRDT']['total_repay_turn'])?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div style="text-align:left;font-family:NGB;font-size:16px">● 투자 정보</div>
	<div class="type03_2 mb20">
		<table>
			<colgroup>
				<col style="width:20%">
				<col style="width:20%">
				<col style="width:20%">
				<col style="width:20%">
				<col style="width:20%">
				<col style="width:20%">
			</colgroup>
			<thead>
				<tr>
					<th>투자금</th>
					<th>세전이자</th>
					<th>플랫폼이용료</th>
					<th>원천징수</th>
					<th>지급이자(세후)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?=number_format($PSTATE['INVEST'][0]['amount'])?>원</td>
					<td><?=number_format($TOTAL['invest_interest']);?>원</td>
					<td><?=number_format($TOTAL['invest_usefee']);?>원</td>
					<td><?=number_format($TOTAL['tax']);?>원</td>
					<td><?=number_format($TOTAL['interest']);?>원</td>
				</tr>
			</tbody>
		</table>
	</div>

<?
if( count($LIST) ) {
?>
	<div style="text-align:left;font-family:NGB;font-size:16px">● 상환 스케쥴</div>
	<div class="type03">
		<table style="width:100%">
			<colgroup>
				<col style="width:%">
				<col style="width:10%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:10%">
			</colgroup>
			<thead>
				<tr>
					<th>회차</th>
					<th>지급예정일</th>
					<th>지급계좌</th>
					<th>원금</th>
					<th>이자</th>
					<th>플랫폼<br>이용료</th>
					<th>원천징수</th>
					<th>실지급액</th>
					<th>지급상태</th>
				</tr>
			</thead>
			<tbody>
<?
	for($i=0,$j=1; $i<count($LIST); $i++,$j++) {

		switch($LIST[$i]['gubun']) {
			case 'overdue_repay' : $bgcolor = '#FFF5EE'; $fcolor = '#FF0000'; break;
			case 'partial_repay' : $bgcolor = '#FFF5EE'; $fcolor = '#3333FF'; break;
			default              : $bgcolor = ''; $fcolor = ''; break;
		}

?>
				<tr style="background:<?=$bgcolor?>;">
					<td style="font-size:12px;color:<?=$fcolor?>;"><?=$LIST[$i]['repay_title']?></td>
					<td style="font-size:12px;"><?=$LIST[$i]['repay_schedule_date']?></td>
					<td style="font-size:12px;"><?=$LIST[$i]['repay_bank_info']?></td>
					<td style="font-size:12px;text-align:right"><?=number_format($LIST[$i]['repay_principal'])?>원</td>
					<td style="font-size:12px;text-align:right"><?=number_format($LIST[$i]['invest_interest'])?>원</td>
					<td style="font-size:12px;text-align:right"><?=number_format($LIST[$i]['invest_usefee'])?>원</td>
					<td style="font-size:12px;text-align:right"><?=number_format($LIST[$i]['tax'])?>원</td>
					<td style="font-size:12px;text-align:right;color:#3366FF"><?=number_format($LIST[$i]['last_repay_amount'])?>원</td>
					<td style="font-size:12px;"><?=$LIST[$i]['repay_status']?></td>
				</tr>

<?
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

</div>
<div style="margin:10px auto 20px">
	<span id="no" class="btn_big_link">닫기</span>
</div>
