<?
$sub_menu = "500100";
include_once('./_common.php');


auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_GET)) { ${$key} = trim($value); }


$sdate = ($sdate) ? $sdate : date('Y-m') . '-01';
$edate = ($edate) ? $edate : date('Y-m') . '-' . date('t', strtotime($sdate));
if($sdate > $edate) {
	$sdate_ = min(array($sdate, $edate));
	$edate_ = max(array($sdate, $edate));
	$sdate = $_sdate;
	$edate = $_edate;
}



$where= " AND TARGET_DATE BETWEEN '$sdate' AND '$edate'";

$sql = "
	SELECT
		TARGET_DATE,
		BAL_DEP_CNT, BAL_DEP_AMT,
		BAL_RET_CNT, BAL_RET_AMT,
		REPAY_CNT, REPAY_AMT,
		LOAN_CNT, LOAN_AMT,
		PRIN_CNT, PRIN_AMT,
		BAL_ALLAMT,
		BAL_TRUAMT,
		BAL_TRUAMT2
	FROM
		IB_deal_daylog
	WHERE 1
		$where
	ORDER BY
		TARGET_DATE ASC";

$res  = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {

	$LIST[$i] = sql_fetch_array($res);

	$etc_divid_sql = "
		SELECT
			IFNULL(COUNT(idx),0) AS etc_divid_cnt,
			IFNULL(SUM(fee),0) AS etc_divid_amt
		FROM
			cf_etc_cost
		WHERE 1
			AND LEFT(rdatetime,10) = '".$LIST[$i]['TARGET_DATE']."'
			AND is_drop = ''";
	$ETC_R = sql_fetch($etc_divid_sql);

	$LIST[$i]['etc_divid_cnt'] = $ETC_R['etc_divid_cnt'];
	$LIST[$i]['etc_divid_amt'] = $ETC_R['etc_divid_amt'];


	// 계산상 예치금 잔액과 은행잔액간 비교	(예치금총잔액 = 전일예치금총잔액 + 예치금입금액 + 원리금배분완료액 - 예치금출금 - 대출실행)
	$BEFORE_DATA = sql_fetch("SELECT BAL_ALLAMT, BAL_TRUAMT FROM IB_deal_daylog WHERE target_date = '".date("Y-m-d", strtotime($LIST[$i]['TARGET_DATE'] ." -1 day"))."'");
	$calc_balance_amt = $BEFORE_DATA['BAL_ALLAMT'] + $LIST[$i]['BAL_DEP_AMT'] + $LIST[$i]['PRIN_AMT'] - $LIST[$i]['BAL_RET_AMT'] - $LIST[$i]['LOAN_AMT'];
	$LIST[$i]['balance_different'] = ($LIST[$i]['BAL_ALLAMT'] != $calc_balance_amt) ? 1 : 0;		// 정산차액발생플래그

/*
	echo $LIST[$i]['TARGET_DATE'] . " : ";
	echo "은행잔액 ". number_format($LIST[$i]['BAL_ALLAMT']) ." / \n";
	echo "정산잔액 {$BEFORE_DATA['BAL_ALLAMT']} + {$LIST[$i]['BAL_DEP_AMT']} + {$LIST[$i]['PRIN_AMT']} - {$LIST[$i]['BAL_RET_AMT']} - {$LIST[$i]['LOAN_AMT']} = ". number_format($calc_balance_amt) ." / \n";
	echo "차액 " . number_format($LIST[$i]['BAL_ALLAMT']-$calc_balance_amt) . "<br/>\n";
*/

	// 예치금입금건수, 금액
	$TOTAL['BAL_DEP_CNT'] += $LIST[$i]['BAL_DEP_CNT'];
	$TOTAL['BAL_DEP_AMT'] += $LIST[$i]['BAL_DEP_AMT'];

	// 예치금출금건수, 금액
	$TOTAL['BAL_RET_CNT'] += $LIST[$i]['BAL_RET_CNT'];
	$TOTAL['BAL_RET_AMT'] += $LIST[$i]['BAL_RET_AMT'];

	// 상환금입금건수, 금액
	$TOTAL['REPAY_CNT']   += $LIST[$i]['REPAY_CNT'];
	$TOTAL['REPAY_AMT']   += $LIST[$i]['REPAY_AMT'];

	// 대출실행건수, 금액
	$TOTAL['LOAN_CNT']    += $LIST[$i]['LOAN_CNT'];
	$TOTAL['LOAN_AMT']    += $LIST[$i]['LOAN_AMT'];

	// 원리금배분건수, 금액
	$TOTAL['PRIN_CNT']    += $LIST[$i]['PRIN_CNT'];
	$TOTAL['PRIN_AMT']    += $LIST[$i]['PRIN_AMT'];

	// 기타비용배분건수, 금액
	$TOTAL['etc_divid_cnt']    += $LIST[$i]['etc_divid_cnt'];
	$TOTAL['etc_divid_amt']    += $LIST[$i]['etc_divid_amt'];


	$y_val[$i]       = $LIST[$i]['TARGET_DATE'];
	$BAL_ALLAMT[$i]  = $LIST[$i]['BAL_ALLAMT'] * 1;
	$BAL_DEP_AMT[$i] = $LIST[$i]['BAL_DEP_AMT'] * 1;
	$BAL_RET_AMT[$i] = $LIST[$i]['BAL_RET_AMT'] * 1;
	$REPAY_AMT[$i]   = $LIST[$i]['REPAY_AMT'] * 1;
	$LOAN_AMT[$i]    = $LIST[$i]['LOAN_AMT'] * 1;

}

$list_count = count($LIST);

$to_month = date("Y-m");
$to_month_sdate = $to_month . "-01";
$to_month_edate = $to_month . "-" . date('t', strtotime($to_month_sdate));

$next_month = date('Y-m', strtotime($sdate . ' first day of +1 month'));
$next_month_sdate = $next_month . "-01";
$next_month_edate = $next_month . "-" . date('t', strtotime($next_month_sdate));

$prev_month = date('Y-m', strtotime($sdate . ' first day of -1 month'));
$prev_month_sdate = $prev_month . "-01";
$prev_month_edate = $prev_month . "-" . date('t', strtotime($next_month_sdate));

$prev_12month = date('Y-m', strtotime($sdate . ' first day of -1 year'));
$prev_12month_sdate = $prev_12month . "-01";
$prev_12month_edate = $prev_12month . "-" . date('t', strtotime($next_month_sdate));


$g5['title'] = '신한 일별거래내역';
include_once('./admin.head.php');

?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>
var point_repay_cnt = 0;
var point_repay_amt = 0;
var cash_repay_amt  = 0;

var LIST_DIVIDE_AMT      = new Array();
var LIST_POINT_REPAY_AMT = new Array();
var LIST_CASH_REPAY_AMT  = new Array();

function checkData(i,tdate) {
	$.ajax({
		url : "ajax_check_day.php",
		type: "post",
		dataType: "json",
		data : {"ymd" : tdate},
		success: function(result) {

			res = result;

			LIST_DIVIDE_AMT[i]      = Number(res.divide_amt);					// 원리금배분
			LIST_POINT_REPAY_AMT[i] = Number(res.point_repay_amt);		// 원리금→예치금전환
			LIST_CASH_REPAY_AMT[i]  = Number(res.cash_repay_amt);			// 원리금→계좌이체

			// 예치금 입금
			if( ($('#deposit_cnt_'+i).text()!=number_format(res.deposit_cnt)+'건') || ($('#deposit_amt_'+i).text()!=number_format(res.deposit_amt)+'원') ) {
				$('#deposit_cnt_'+i).css("color","#FF2222");
				$('#deposit_amt_'+i).css("color","#FF2222");
			}

			// 예치금 출금
			if( ($('#withdrawal_cnt_'+i).text()!=number_format(res.withdrawal_cnt)+'건') || ($('#withdrawal_amt_'+i).text()!=number_format(res.withdrawal_amt)+'원') ) {
				$('#withdrawal_cnt_'+i).css("color","#FF2222");
				$('#withdrawal_amt_'+i).css("color","#FF2222");
			}

			// 상환금 입금
			if( ($('#loaner_deposit_cnt_'+i).text()!=number_format(res.loaner_deposit_cnt)+'건') || ($('#loaner_deposit_amt_'+i).text()!=number_format(res.loaner_deposit_amt)+'원') ) {
				$('#loaner_deposit_cnt_'+i).css("color","#FF2222");
				$('#loaner_deposit_amt_'+i).css("color","#FF2222");
			}

			// 대출실행
			if( ($('#loan_start_cnt_'+i).text()!=number_format(res.loan_start_cnt)+'건') || ($('#loan_start_amt_'+i).text()!=number_format(res.loan_start_amt)+'원') ) {
				$('#loan_start_cnt_'+i).css("color","#FF2222");
				$('#loan_start_amt_'+i).css("color","#FF2222");
			}


			// 원리금 배분
			if( ($('#divide_cnt_'+i).text()!=number_format(res.divide_cnt)+'건') || ($('#divide_amt_'+i).text()!=number_format(res.divide_amt)+'원') ) {
				$('#divide_cnt_'+i).css("color","#FF2222");
				$('#divide_amt_'+i).css("color","#FF2222");
			}


			// 원리금→예치금전환
			$('#point_repay_cnt_'+i).text(number_format(res.point_repay_cnt) + '건');
			$('#point_repay_amt_'+i).text(number_format(res.point_repay_amt) + '원');

			point_repay_cnt = point_repay_cnt + Number(res.point_repay_cnt);
			point_repay_amt = point_repay_amt + Number(res.point_repay_amt);

			point_repay_cnt_val = (point_repay_cnt > 0) ? number_format(point_repay_cnt) + '건' : '0건';
			point_repay_amt_val = (point_repay_amt > 0) ? number_format(point_repay_amt) + '원' : '0원';

			$('#point_repay_cnt').text(point_repay_cnt_val);
			$('#point_repay_amt').text(point_repay_amt_val);


			//console.log(res);
		},
		error: function () {
			//alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
		}
	});
}
</script>

<style>
.borderR { border-right:1px solid #999; }
.tb { font-size:12px; }
</style>

<div class="tbl_head02 tbl_wrap" style="min-width:1500px;">

	<!-- 이슈영역 //-->
	<!--
	<div style="padding:8px; margin-bottom:16px;border:1px solid #222; border-radius:3px; background:#FDFECB;">
		<b style="color:red">CHECK ISSUE</b><br>
		<span style="display:inline-block;padding-left:10px;font-size:12px">
			<b>[ (품번.833) [제790호] 성남 여수동 오피스텔 준공자금 의 선지급 후 중도상환발생!! ]</b><br/>
			1) 2019-07-19 : 5회차 이자(31일치) 이자  63,698,630원 선지급<br/>
			2) 2019-08-19 : 중도상환요청으로 5,000,000,000원 수동상환(예치금으로 지급후 회원계정으로 10억씩 5회 출금처리) 하였음.<br/>
			3) 2019-08-19 : 1)항 선지급분중 18일치 이자  36,986,301원을 제외한 차액 26,712,329원을 헬로크라우드대부계좌로 반환받았음.
		</span>
	</div>
	-->
	<!-- 이슈영역 //-->

	<!-- 검색영역 //-->
	<form id="frmSearch" method="get" class="form-horizontal" style="margin:0;">
		<div class="form-group">
			<ul class="col-sm-10 list-inline" style="width:100%;">
				<li><button type='button' class="btn btn-sm btn-default" style="width:60px;height:24px;padding:0;line-height:24px;" onClick="setDateRange('toMonth');">당월</button></li>
				<li style="padding-left:0px;"><button type='button' class="btn btn-sm btn-default" style="width:60px;height:24px;padding:0;line-height:24px;" onClick="setDateRange('nextMonth');">차월</button></li>
				<li style="padding-left:0px;"><button type='button' class="btn btn-sm btn-default" style="width:60px;height:24px;padding:0;line-height:24px;" onClick="setDateRange('prevMonth');">전월</button></li>
				<li style="padding-left:0px;"><button type='button' class="btn btn-sm btn-default" style="width:60px;height:24px;padding:0;line-height:24px;" onClick="setDateRange('prev12Month');">전년</button></li>
			</ul>
			<ul class="col-sm-10 list-inline" style="width:100%;">
				<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" placeholder="대상일자(시작)" readonly></li>
				<li>~</li>
				<li style="padding-left:0px;"><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" placeholder="대상일자(종료)" readonly></li>
				<li><button type="submit" class="btn btn-sm btn-primary">검색</button></li>
				<li style="float:right;font-size:12px;color:brown">
					※ '신한은행 인사이드뱅크'를 통하여 시간단위로 당일 거래 내역 리포트를 수집합니다. &nbsp;
					<button type="button" onClick="startCrowling();" class="btn btn-sm btn-warning">실시간정보갱신</button>
				</li>
			</ul>
		</div>
	</form>
	<!-- 검색영역 //-->

	<!-- 그래프 영역 //-->
	<div id="hi_container" style="border:1px dotted #aaa; margin:0 auto; width:100%; height:300px; min-height:400px; margin-bottom:8px;"></div>
	<!-- 그래프 영역 //-->

	<span>※ 예치금총잔액 = 전일예치금총잔액 + 예치금입금액 + 원리금배분액 - 예치금출금 - 대출실행</span><br/>
	<span>※ 상환용모계좌잔액 = 전일상환용모계좌잔액 + 상환금입금액 - 원리금배분액 - 기타비용배분액 - 원리금배분내역중수수료세금합계</span>
	<table class="tb table-bordered table-hover" style="margin:0;">
		<colgroup>
			<col style="width:%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:4.5%">
			<col style="width:6.3%">

			<col style="width:6.3%">
			<col style="width:6.3%">
			<col style="width:6.3%">
		</colgroup>
		<tr align="center" style="background:#F8F8EF">
			<td rowspan="2" style="border-right:1px solid #999;">대상일자</td>
			<td colspan="2" style="border-right:1px solid #999;">예치금입금</td>
			<td colspan="2" style="border-right:1px solid #999;">예치금출금</td>
			<td colspan="2" style="border-right:1px solid #999;">상환금입금</td>
			<td colspan="2" style="border-right:1px solid #999;">대출실행</td>
			<td colspan="2" style="border-right:1px solid #999;">원리금배분</td>
			<td colspan="2" style="border-right:1px solid #999;">기타비용배분<br/>(수수료)</td>
			<td colspan="2" style="border-right:1px solid #999;">원리금→예치금전환</td>
			<td rowspan="2" style="border-right:1px solid #999;">예치금<br>총잔액</td>
			<td rowspan="2" style="border-right:1px solid #999;">예치금<br>신탁계좌잔액</td>
			<td rowspan="2">상환용<br>모계좌잔액</td>
		</tr>
		<tr align="center" style="background:#F8F8EF">
			<!-- 예치금입금 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

			<!-- 예치금출금 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

			<!-- 상환금입금 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

			<!-- 대출실행 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

			<!-- 원리금배분 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

			<!-- 기타비용배분 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

			<!-- 원리금→예치금전환 -->
			<td>건수</td>
			<td style="border-right:1px solid #999;">금액</td>

		</tr>

<?
if($list_count > 1) {
?>
		<!-- 전체합산데이터 -->
		<tr align="right" style="background:#EEEEFF;color:brown">
			<td style="text-align:center;border-right:1px solid #999;">합계</td>

			<!-- 예치금입금 -->
			<td><?=number_format($TOTAL['BAL_DEP_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['BAL_DEP_AMT'])?>원</td>

			<!-- 예치금출금 -->
			<td><?=number_format($TOTAL['BAL_RET_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['BAL_RET_AMT'])?>원</td>

			<!-- 상환금입금 -->
			<td><?=number_format($TOTAL['REPAY_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['REPAY_AMT'])?>원</td>

			<!-- 대출실행 -->
			<td><?=number_format($TOTAL['LOAN_CNT'])?>건</td>
			<td style="border-right:1px solid #999;"><?=number_format($TOTAL['LOAN_AMT'])?>원</td>

			<!-- 원리금배분 -->
			<td id="divide_cnt"><?=number_format($TOTAL['PRIN_CNT'])?>건</td>
			<td id="divide_amt" style="border-right:1px solid #999;"><?=number_format($TOTAL['PRIN_AMT'])?>원</td>

			<!-- 기타비용배분 -->
			<td id="etc_divide_cnt"><?=number_format($TOTAL['etc_divid_cnt'])?>건</td>
			<td id="etc_divide_amt" style="border-right:1px solid #999;"><?=number_format($TOTAL['etc_divid_amt'])?>원</td>

			<!-- 원리금→예치금전환 -->
			<td id="point_repay_cnt">0건</td>
			<td id="point_repay_amt" style="border-right:1px solid #999;">0원</td>

			<td align="center" style="border-right:1px solid #999;">-</td>
			<td align="center" style="border-right:1px solid #999;">-</td>
			<td align="center">-</td>
		</tr>
		<!-- 전체합산데이터 -->

<?
}

//$deposit_url    = "/adm/vact_log_shinhan.php";
$investor_deposit_url    = "/adm/deposit_withdrawal/vact_log.php?view=investor";
$loaner_deposit_url    = "/adm/deposit_withdrawal/vact_log.php?view=loaner";
$withdrawal_url = "/adm/withdrawal_list.php";
$balance_url    = "/adm/repayment/repay_log.php";

if($list_count) {

$LIST = array_reverse($LIST);		//날짜역순으로 정렬
//print_rr($LIST, 'font-size:12px');

	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$DETAIL['deposit']       = $investor_deposit_url."&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE']."&TR_AMT_GBN=10";
		$DETAIL['loaner_deposit']= $loaner_deposit_url."&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE']."&TR_AMT_GBN=20";
		$DETAIL['withdrawal']    = $withdrawal_url."?state=2&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE']."&ib_regist=1";
		$DETAIL['divide']        = "/adm/repayment/repay_exec_log.php?sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE'];
		$DETAIL['etc_divide']    = "/adm/repayment/etc_cost_divide.php?dateFld=order_rdate&sdate=".$LIST[$i]['TARGET_DATE']." &edate=".$LIST[$i]['TARGET_DATE'];
		$DETAIL['point_repay']   = $balance_url."?date_field=A.banking_date&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE']."&give_rcv_method=2";
		$DETAIL['cash_repay']    = $balance_url."?date_field=A.banking_date&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE']."&give_rcv_method=1";
		$DETAIL['loan_start']    = "/adm/product/product_list.php?date_field=A.loan_start_date&sdate=".$LIST[$i]['TARGET_DATE']."&edate=".$LIST[$i]['TARGET_DATE'];

		$tr_bgcolor = (date('w', strtotime($LIST[$i]['TARGET_DATE']))=='0') ? '#FFEEEE' : '';

?>
		<tr align="right" style="background:<?=$tr_bgcolor?>">
			<td style="text-align:center;border-right:1px solid #999;"><span onClick="startCrowling('<?=$LIST[$i]['TARGET_DATE']?>');" style="cursor:pointer"><?=$LIST[$i]['TARGET_DATE']?></span></td>

			<!-- 예치금 입금 -->
			<td><a href="<?=$DETAIL['deposit']?>" target="_blank" id="deposit_cnt_<?=$i?>"><?=number_format($LIST[$i]['BAL_DEP_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;" id="deposit_amt_<?=$i?>"><?=number_format($LIST[$i]['BAL_DEP_AMT'])?>원</td>

			<!-- 예치금 출금 -->
			<td><a href="<?=$DETAIL['withdrawal']?>" target="_blank" id="withdrawal_cnt_<?=$i?>"><?=number_format($LIST[$i]['BAL_RET_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;" id="withdrawal_amt_<?=$i?>"><?=number_format($LIST[$i]['BAL_RET_AMT'])?>원</td>

			<!-- 상환금 입금 -->
			<td><a href="<?=$DETAIL['loaner_deposit']?>" target="_blank" id="loaner_deposit_cnt_<?=$i?>"><?=number_format($LIST[$i]['REPAY_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;" id="loaner_deposit_amt_<?=$i?>"><?=number_format($LIST[$i]['REPAY_AMT'])?>원</td>

			<!-- 대출 실행 -->
			<td><a href="<?=$DETAIL['loan_start']?>" id="loan_start_cnt_<?=$i?>" target="_blank"><?=number_format($LIST[$i]['LOAN_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;" id="loan_start_amt_<?=$i?>"><?=number_format($LIST[$i]['LOAN_AMT'])?>원</td>

			<!-- 원리금배분 -->
			<td><a href="<?=$DETAIL['divide']?>" target="_blank" id="divide_cnt_<?=$i?>"><?=number_format($LIST[$i]['PRIN_CNT'])?>건</a></td>
			<td style="border-right:1px solid #999;" id="divide_amt_<?=$i?>"><?=number_format($LIST[$i]['PRIN_AMT'])?>원</td>

			<!-- 원리금→기타비용배분-->
			<td><a href="<?=$DETAIL['etc_divide']?>" target="_blank" id="etc_divide_cnt_<?=$i?>"><?=number_format($LIST[$i]['etc_divid_cnt'])?>건</a></td>
			<td style="border-right:1px solid #999;" id="etc_divide_amt_<?=$i?>"><?=number_format($LIST[$i]['etc_divid_amt'])?>원</td>

			<!-- 원리금→예치금전환 -->
			<td><a href="<?=$DETAIL['point_repay']?>" target="_blank" id="point_repay_cnt_<?=$i?>"></a></td>
			<td style="border-right:1px solid #999;" id="point_repay_amt_<?=$i?>"></td>

			<!-- 예치금 충전 잔액 -->
			<td style="border-right:1px solid #999;"><span style="<?=($LIST[$i]['balance_different'])?'color:#FF2222':''?>"><?=number_format($LIST[$i]['BAL_ALLAMT'])?>원</span></td>

			<!-- 예치금 신탁 계좌 잔액 -->
			<td style="border-right:1px solid #999;"><span style="<?=($LIST[$i]['balance_different'])?'color:#FF2222':''?>"><?=number_format($LIST[$i]['BAL_TRUAMT'])?>원</span></td>

			<!-- 회수금 집금 계좌 잔액 -->
			<td><span><?=number_format($LIST[$i]['BAL_TRUAMT2'])?>원</span></td>
		</tr>

<?
	}
}
else {
?>
		<tr align="right">
			<td align="center" colspan="20">데이터가 없습니다.</td>
		</tr>
<?
}
?>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

</div>
<?
$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
?>

<script>
setDateRange = function(target) {
	if(target=='prevMonth') {
		$('#sdate').val('<?=$prev_month_sdate?>');
		$('#edate').val('<?=$prev_month_edate?>');
	}
	else if(target=='prev12Month') {
		$('#sdate').val('<?=$prev_12month_sdate?>');
		$('#edate').val('<?=$prev_12month_edate?>');
	}
	else if(target=='nextMonth') {
		$('#sdate').val('<?=$next_month_sdate?>');
		$('#edate').val('<?=$next_month_edate?>');
	}
	else {
		$('#sdate').val('<?=$to_month_sdate?>');
		$('#edate').val('<?=$to_month_edate?>');
	}
	$('#frmSearch').submit();
}


startCrowling = function(reqdate) {
	if(reqdate === undefined) {
		var message = '금일 데이터를 갱신 하시겠습니까?\n\n상환용 모계좌 잔액은 당일분만 갱신됩니다.';
		reqdate = '';
	}
	else {
		var message = '대상일 : ' + reqdate + '\n위 데이터를 갱신 하시겠습니까?';
	}

	if(confirm(message)) {
		$.ajax({
			url : "ajax_ib_live_data_crowling.php",
			type : "POST",
			data : { reqdate:reqdate },
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			success:function(data) {
				if(data=='OK') { window.location.reload(); }
			},
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}

}


$(document).ready(function() {
<?
if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {
		echo "	setTimeout( function(){ checkData('{$i}','{$LIST[$i]['TARGET_DATE']}'); }, ".($j*300)." );\n";
	}
}
?>
});



/*
$(document).ajaxStop(function() {
	LIST_DIVIDE_AMT.reverse();
	chart.addSeries({ name : "원리금배분", data : LIST_DIVIDE_AMT });

	LIST_POINT_REPAY_AMT.reverse();
	chart.addSeries({ name : "원리금→예치금", data : LIST_POINT_REPAY_AMT });

	LIST_CASH_REPAY_AMT.reverse();
	chart.addSeries({ name : "원리금→계좌출금", data : LIST_CASH_REPAY_AMT });
});
*/

var y_val       = JSON.parse('<?=addslashes(json_encode($y_val)) ?>');
var BAL_ALLAMT  = JSON.parse('<?=addslashes(json_encode($BAL_ALLAMT)) ?>');			// 예치금총잔액
var BAL_DEP_AMT = JSON.parse('<?=addslashes(json_encode($BAL_DEP_AMT)) ?>');		// 예치금입금액
var BAL_RET_AMT = JSON.parse('<?=addslashes(json_encode($BAL_RET_AMT)) ?>');		// 예치금출금액
var REPAY_AMT   = JSON.parse('<?=addslashes(json_encode($REPAY_AMT)) ?>');			// 상환금입금액
var LOAN_AMT    = JSON.parse('<?=addslashes(json_encode($LOAN_AMT)) ?>');				// 대출실행금액

//y_val.reverse();
//BAL_ALLAMT.reverse();
//BAL_DEP_AMT.reverse();
//BAL_RET_AMT.reverse();
//REPAY_AMT.reverse();
//LOAN_AMT.reverse();

//console.log(y_val);
//console.log(LOAN_AMT);

var chart = Highcharts.chart('hi_container', {
	chart: {
		type: 'line'
	},
	title: {
		text: '예치금 잔액 추이표'		// 제목
	},
	subtitle: {
		text: ''		// 부재
	},
	xAxis: { categories: y_val },
	yAxis: {
		title: {
			text: '금액'
		}
	},
	tooltip: {
		shared: true,
		crosshairs: true
	},
	plotOptions: {
		line: {
			dataLabels: {
				enabled: true,
				allowOverlap : true,
				formatter: function() {
					//console.log(this.x +" "+ this.series.data[this.series.data.length-1].category);
					if(this.x == this.series.data[this.series.data.length-1].category) {
						return this.series.name + ': '+ number_format(this.y);
					}
					else {
						return null;
					}
				}
			},
			enableMouseTracking: true
		}
	},
	series: [
		{ name: '예치금총잔액', data: BAL_ALLAMT },
		//{ name: '예치금입금', data: BAL_DEP_AMT },
		//{ name: '예치금출금', data: BAL_RET_AMT },
		//{ name: '상환금입금', data: REPAY_AMT },
		//{ name: '대출실행', data: LOAN_AMT }
	]
});
</script>

<?php
include_once ('./admin.tail.php');
?>