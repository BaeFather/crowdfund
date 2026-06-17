<?
include_once('_common.php');


//if ($_SERVER["REQUEST_METHOD"]!="GET") { echo "ERROR-DATA"; exit; }
//if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }

//while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

//print_rr($_REQUEST, "font-size:11px");

$type = '2';
$sy = '2018';
$sdate = "2018-01-01";
$edate = "2019-12-31";

if(!$sy) $sy = date(Y);

$TOTAL = array(
           'invest_count'     => 0,
					 'invest_amount'    => 0,
					 'target_principal' => 0,
					 'payed_principal'  => 0,
					 'target_interest'  => 0,
					 'payed_interest'   => 0
				 );

// 상품 및 투자정보
$sql = "
	SELECT
		A.idx AS invest_idx, A.amount, A.is_return, A.return_date,
		B.idx AS product_idx
	FROM
		`cf_product_invest` A,
		`cf_product` B
	WHERE 1=1
		AND B.idx=A.product_idx
		AND B.state IN('1','2','5','7')
		AND A.invest_state='Y'
		AND A.member_idx='".$member['mb_no']."'
	ORDER BY
		A.insert_date";

$res  = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$row = sql_fetch_array($res);

	$PSTATE = investStatement($row['product_idx'], $row['amount'], '', '', $row['invest_idx']);

	$repay_count[$i] = count($PSTATE['REPAY']);

	for($x=0,$y=1; $x<$repay_count[$i]; $x++,$y++) {

		$date = $banking_date = "";

		// (★★중요★★) 일자기준: 지급기록 있으면 지급일, 지급기록 없으면 예정일
		$banking_date = ($type==1) ? substr($PSTATE['REPAY'][$x]['banking_date'], 0, 7) : substr($PSTATE['REPAY'][$x]['banking_date'], 0, 10);
		if($banking_date) {
			$date = $banking_date;
			if( in_array($PSTATE['PRDT']['state'], array('2','5')) ) {
				$PAYED_PRINCIPAL[$date] += $PSTATE['REPAY'][$x]['principal'];   // 지급완료 원금
			}
		}
		else {
			$date = ($type==1) ? substr($PSTATE['REPAY'][$x]['repay_schedule_date'], 0, 7) : $PSTATE['REPAY'][$x]['repay_schedule_date'];
		}

		$SIMUL[$date]['target_principal'] += $PSTATE['REPAY'][$x]['principal'];		// 지급예정 원금
		$SIMUL[$date]['target_interest']  += $PSTATE['REPAY'][$x]['interest'];    // 지급예정 이자


		echo "<div style='font-size:12px'>" .
		     " 상품번호 : " . $row['product_idx'] . " " . $y . "회차" .
		     " | 지급예정일 : " . $date .
		     " | 원금 : " . $PSTATE['REPAY'][$x]['principal'] .
		     " | 이자 : " . $PSTATE['REPAY'][$x]['interest'] .
		     " | 지급완료일 : " . $banking_date .
		     "</div>\n";


	}
	unset($PSTATE);
}

//if ($member['mb_id']=="romrom") print_rr($SIMUL);

//투자번호, 상품번호 배열화
$sql  = "SELECT idx, product_idx FROM `cf_product_invest` WHERE member_idx='".$member['mb_no']."' AND invest_state='Y' ORDER BY insert_date";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$row = sql_fetch_array($res);
	$sql_invest_arr.= ($j<$rows) ? "'".$row['idx']."'," : "'".$row['idx']."'";
	$sql_product_arr.= ($j<$rows) ? "'".$row['product_idx']."'," : "'".$row['product_idx']."'";
}


$date_length = ($type=='2') ? '10' : '7';

//투자내역
$sql = "
	SELECT
		LEFT(insert_date, $date_length) AS date,
		COUNT(idx) AS invest_count, SUM(amount) AS invest_amount
	FROM
		`cf_product_invest`
	WHERE 1
		AND member_idx='".$member['mb_no']."'
		AND invest_state='Y'
	GROUP BY
		LEFT(insert_date, $date_length)
	ORDER BY
		insert_date, idx";
//if($_SERVER['REMOTE_ADDR']=='220.117.134.164')	print_rr($sql, "font-size:11px");
$res = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$r = sql_fetch_array($res);
	if($r['date']) {
		$IVSTLOG[$r['date']]['invest_count']  = $r['invest_count'];
		$IVSTLOG[$r['date']]['invest_amount'] = $r['invest_amount'];
	}
	unset($r);
}


//이자상환내역 (실지급일 기준)
if($sql_invest_arr && $sql_product_arr) {
	$sql = "
		SELECT
			LEFT(banking_date, $date_length) AS banking_date,
			SUM(invest_amount) AS invest_amount,
			SUM(interest) AS interest,
			SUM(principal) AS principal,
			SUM(interest_tax) AS interest_tax,
			SUM(local_tax) AS local_tax,
			SUM(local_tax) AS fee
		FROM
			`cf_product_give`
		WHERE 1
			AND invest_idx IN($sql_invest_arr)
			AND product_idx IN($sql_product_arr)
		GROUP BY
			LEFT(banking_date, $date_length)
		ORDER BY
			banking_date, idx";
	//if($_SERVER['REMOTE_ADDR']=='220.117.134.164')	print_rr($sql, "font-size:11px");  // 전승찬 debug
	$res  = sql_query($sql);
	$rows = sql_num_rows($res);
	for($i=0; $i<$rows; $i++) {
		$r = sql_fetch_array($res);
		if($r['banking_date']) {
			$REPAYLOG[$r['banking_date']]['payed_interest'] = $r['invest_amount'];
		}
		unset($r);
	}
}


if($type==2) {
	$sdate = ($sdate) ? $sdate : date('Y-m').'-01';
	$sdate_lastday = sprintf("%02d", date(t, strtotime($sdate)));
	$edate = ($edate) ? $edate : date("Y-m")."-".$sdate_lastday;
	$sy  = substr($sdate, 0, 4);
	$sym = substr($sdate, 0, 7);

	$day_count = ceil((strtotime($edate) - strtotime($sdate)) / 86400)+1;
}


$loop_count = ($type==2) ? $day_count : 12;

for($i=0,$j=1; $i<$loop_count; $i++,$j++) {

	if($type==2) {
		$date = date("Y-m-d", strtotime($sdate)+86400*$i);
	}
	else {
		$date = $sy . '-' . sprintf('%02d', $j);
	}
	//echo $date."<br>\n";


	$DATA[$date] = array(
									 'invest_count'     => 0,
									 'invest_amount'    => 0,
									 'target_principal' => 0,
									 'payed_principal'  => 0,
									 'target_interest'  => 0,
									 'payed_interest'   => 0
								 );

	$DATA[$date]['invest_count']     = $IVSTLOG[$date]['invest_count'];
	$DATA[$date]['invest_amount']    = $IVSTLOG[$date]['invest_amount'];

	$DATA[$date]['target_principal'] = $SIMUL[$date]['target_principal'] - $PAYED_PRINCIPAL[$date];		// 2017-04-02 수정
	$DATA[$date]['payed_principal']  = $PAYED_PRINCIPAL[$date];

	$DATA[$date]['target_interest']  = $SIMUL[$date]['target_interest'] - $REPAYLOG[$date]['payed_interest'];		// 2017-04-02 수정
	$DATA[$date]['payed_interest']   = $REPAYLOG[$date]['payed_interest'];

	/*
	// 정산일 기준일 이전 데이터의 예정금액 0 처리
	if($type==1) {
		if( $date < date('Y-m')) {
			$DATA[$date]['target_principal'] = 0;
			$DATA[$date]['target_interest']  = 0;
		}
	}
	else if($type==2) {
		if( date('Y-m-d', strtotime('+4 day', strtotime($date))) < date('Y-m-d') ) {
			$DATA[$date]['target_principal'] = 0;
			$DATA[$date]['target_interest']  = 0;
		}
	}
	*/

	$TOTAL['invest_count']     += $DATA[$date]['invest_count'];
	$TOTAL['invest_amount']    += $DATA[$date]['invest_amount'];
	$TOTAL['target_principal'] += $DATA[$date]['target_principal'];
	$TOTAL['payed_principal']  += $DATA[$date]['payed_principal'];
	$TOTAL['target_interest']  += $DATA[$date]['target_interest'];
	$TOTAL['payed_interest']   += $DATA[$date]['payed_interest'];

	if($type==2 && ($DATA[$date]['invest_count']=='' && $DATA[$date]['invest_amount']=='' && $DATA[$date]['target_principal']=='' && $DATA[$date]['payed_principal']=='' && $DATA[$date]['target_interest']=='' && $DATA[$date]['payed_interest']=='')) {
		if($day_count>31) {
			unset($DATA[$date]);
		}
	}

	unset($IVSTLOG[$date]); unset($SIMUL[$date]); unset($REPAYLOG[$date]);

}

$tmp = sql_fetch("SELECT LEFT(loan_end_date,4) AS max_year FROM cf_product ORDER BY loan_end_date DESC LIMIT 1");
$maxYear = $tmp['max_year'];

if(G5_IS_MOBILE) {
	include_once("ajax_repay_stats_m.php");
	return;
}

?>

<style>
.tblX { width:100%; border:1px solid #ccc }
.tblX th, .tblX td { padding:0 4px 0 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }
span.left  { float:left; }
span.right { float:right; }
</style>

<h3>수익금 현황</h3>
<div class="mb30">

	<div style="width:100%; display:inline-block;">
		<span class="left">
			<span class="<?=($type==1)?'btn_blue_s':'btn_gray_s';?>" onClick="loadPage('1', '<?=$sy?>', '');" style="width:80px">연간.월별</span>
			<span class="<?=($type==2)?'btn_blue_s':'btn_gray_s';?>" onClick="loadPage2('2', '', '');" style="width:80px">일별.기간별</span>
		</span>
		<span class="right">
<? if($type=='1') { ?>
			<select id="sy" style="height:22px;color:navy">
				<option value="">:: 대상년도 ::</option>
<?
	for($i=2016; $i<=$maxYear; $i++) {
		$selected = ($i==$sy) ? 'selected' : '';
		echo "<option value='".$i."' $selected>".$i."년</option>\n";
	}
?>
			</select>
<? } else { ?>
			<input type="text" class="inp datepicker" id="sdate" value="<?=$sdate?>" placeholder="검색시작일" style="width:100px;height:18px" readonly> ~
			<input type="text" class="inp datepicker" id="edate" value="<?=$edate?>" placeholder="검색종료일" style="width:100px;height:18px" readonly>
<? } ?>
			<span id="load_btn" class="btn_red">확인</span>
		</span>
	</div>

	<table class="tblX">
		<colgroup>
			<col style="width:12%">
			<col style="width:13%">
			<col style="width:13%">
			<col style="width:13%">
			<col style="width:13%">
			<col style="width:13%">
			<col style="width:13%">
			<col style="width:9%">
		</colgroup>
		<tr align="center" style="background-color:#F7F7F7;border-top:2px solid #284893;">
			<th rowspan="2" style="height:40px"><b>구분</b></th>
			<th colspan="2" style="height:20px"><b>투자발생</b></th>
			<th colspan="2" style="height:20px"><b>원금상환</b></th>
			<th colspan="2" style="height:20px"><b>이자상환</b></th>
			<th rowspan="2" style="height:40px"><b>-</b></th>
		</tr>
		<tr align="center" style="height:25px;background-color:#F7F7F7;">
			<th><b>건수</b></th>
			<th><b>금액</b></th>
			<th><b>예정</b></th>
			<th><b>지급</b></th>
			<th><b>예정</b></th>
			<th><b>지급</b></th>
		</tr>
		<tr align="right" style="height:25px;color:red;background-color:#999;">
			<td align="center" style="color:#fff;">합계</td>
			<td style="color:#fff;"><?=number_format($TOTAL['invest_count'])?> 건</td>
			<td style="color:#fff;"><?=number_format($TOTAL['invest_amount'])?> 원</td>
			<td style="color:#fff;"><?=number_format($TOTAL['target_principal'])?> 원</td>
			<td style="color:#fff;"><?=number_format($TOTAL['payed_principal'])?> 원</td>
			<td style="color:#fff;"><?=number_format($TOTAL['target_interest'])?> 원</td>
			<td style="color:#fff;"><?=number_format($TOTAL['payed_interest'])?> 원</td>
			<td align="center" style="color:#fff;"></td>
		</tr>
<?
if(count($DATA)) {
	$DATA_ARR = array_keys($DATA);
	for($i=0,$j=1; $i<count($DATA); $i++,$j++) {

		$target_principal = $target_interest = 0;

		$date = $DATA_ARR[$i];

		if($type==1) {
			$from_date = $date.'-01';
			$to_date   = $date."-".sprintf("%02d", date(t, strtotime($from_date)));
			$link      = "loadPage2('2','$from_date','$to_date');";
		}
		else if($type==2) {
			$from_date = substr($date, 0, 4);
			$to_date   = '';
			$link      = "loadPage('1','$from_date','$to_date');";
		}

		if($DATA[$date]['target_principal'] > 0) {
			$target_principal = $DATA[$date]['target_principal'];
			$fcolor1 = '#000';
		}
		else {
			$target_principal = 0;
			$fcolor1 = '#ccc';
		}

		if($DATA[$date]['target_interest'] > 0) {
			$target_interest = $DATA[$date]['target_interest'];
			$fcolor2 = '#000';
		}
		else {
			$target_interest = 0;
			$fcolor2 = '#ccc';
		}

?>
		<tr align="right" style="height:25px;" onMouseOver="this.bgColor='#F7F7F7'" onMouseOut="this.bgColor=''">
			<td align="center"><a href="#" onClick="<?=$link?>"><?=$DATA_ARR[$i]?></td>
			<td style="color:<?=($DATA[$date]['invest_count'])?'#000':'#ccc'?>"><?=number_format($DATA[$date]['invest_count'])?> 건</td>
			<td style="color:<?=($DATA[$date]['invest_amount'])?'#000':'#ccc'?>"><?=number_format($DATA[$date]['invest_amount'])?> 원</td>
			<td style="color:<?=$fcolor1?>"><?=number_format($target_principal)?> 원</td>
			<td style="color:<?=($DATA[$date]['payed_principal'])?'#000':'#ccc'?>"><?=number_format($DATA[$date]['payed_principal'])?> 원</td>
			<td style="color:<?=$fcolor2?>"><?=number_format($target_interest)?> 원</td>
			<td style="color:<?=($DATA[$date]['payed_interest'])?'#000':'#ccc'?>"><?=number_format($DATA[$date]['payed_interest'])?> 원</td>
			<td align="center"><a href="#" onClick="<?=$link?>"><span class="btn_gray_s2"><?=($type==1)?'일별보기':'월별보기'?></span></a></td>
		</tr>
<?
	}
}
else {
?>
		<tr align="right" style="height:25px;" onMouseOver="this.bgColor='#F7F7F7'" onMouseOut="this.bgColor=''">
			<td align="center" colspan="10">데이터가 없습니다.</td>
		</tr>
<?
}
?>
	</table>

	<div style="margin-top:10px;color:brown;font-size:13px;text-align:right">※ 이자상환액은 플랫폼 이용료 및 세금(비영업대금에 대한 이자소득세, 주민세 등)을 제외한 금액입니다.</div>

</div>

<script type="text/javascript">
$(function(){
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토']
	});
});

$('#sy').on('change', function() {
	val = $('#sy').val();
	loadPage('<?=$type?>', val, '');
});

$('#load_btn').on('click', function() {
<?if($type==1) { ?>
	val1 = $('#sy').val();
	val2 = '';
	loadPage('<?=$type?>', val1, val2);
<? } else if($type==2) { ?>
	val1 = $('#sdate').val();
	val2 = $('#edate').val();
	loadPage2('<?=$type?>', val1, val2);
<? } ?>
});

loadPage = function(arg1, arg2) {
	val1 = (arg1) ? arg1 : '<?=$type?>';
	val2 = (arg2) ? arg2 : '<?=$sy?>';
	$.ajax({
		url: '/deposit/ajax_repay_stats.php',
		type: 'GET',
		data: {type:val1, sy:val2},
		success: function(data) {
			$('#ajax_return_txt').val(data);
			$('#interest_status_area').empty();
			$('#interest_status_area').html(data);
		}
	});
}

loadPage2 = function(arg1, arg2, arg3) {
	val1 = (arg1) ? arg1 : '<?=$type?>';
	val2 = (arg2) ? arg2 : '<?=$sdate?>';
	val3 = (arg3) ? arg3 : '<?=$edate?>';
	$.ajax({
		url: '/deposit/ajax_repay_stats.php',
		type: 'GET',
		data: {type:val1, sdate:val2, edate:val3},
		success: function(data) {
			$('#ajax_return_txt').val(data);
			$('#interest_status_area').empty();
			$('#interest_status_area').html(data);
		}
	});
}
</script>