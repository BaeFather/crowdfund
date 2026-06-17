<?php
$sub_menu = "930040";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '연령별 통계 - 상품별 투자 현황';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
if (!$srch_y) $srch_y = date("Y");

$start_m = 1;
$to_m = 12;
if ( $srch_y==date("Y") ) $to_m=date("m");

//$y60f = $srch_y - 69 ;
$y60f = "1900" ;
$y60t = $srch_y - 60 ;
//echo "$y60f ~ $y60t 60대<br/>";

$y50f = $srch_y - 59 ;
$y50t = $srch_y - 50 ;
//echo "$y50f ~ $y50t 50대<br/>";

$y40f = $srch_y - 49 ;
$y40t = $srch_y - 40 ;
//echo "$y40f ~ $y40t 40대<br/>";

$y30f = $srch_y - 39 ;
$y30t = $srch_y - 30 ;
//echo "$y30f ~ $y30t 30대<br/>";

$y20f = $srch_y - 29 ;
$y20t = $srch_y - 20 ;
//echo "$y20f ~ $y20t 20대<br/>";

$ymf = $y20t - 1 ;
$ymt = $srch_y ;
//echo "$ymf ~ $ymt 미성년자<br/>";
?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">

	<form method="post" name="f_srch">
		<select name="d_type" style="margin-right:20px;height:25px;width:180px;" onchange="javascript:self.location.href='members_years.php';">
			<option>연령별 투자 데이터</option>
			<option selected>연령별 상품군 투자 데이터</option>
		</select>
		<select name="srch_y" onchange="go_srch();" style="height:25px;width:75px;">
			<?
			for ($i=date("Y") ; $i>="2016"; $i--) {
				?>
				<option value="<?=$i?>" <?=$srch_y==$i?"selected":""?> ><?=$i?></option>
				<?
			}
			?>
		</select>
	</form>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=3>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=24>연령별 상품군 투자 현황 (평균금액)</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=6>부동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=6>주택담보</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=6>동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=6>헬로페이</th>

		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">20대 이하</th>
			<th scope="col" style="text-align:center;border:1px solid green;">20대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">30대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">40대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">50대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">60대 이상</th>

			<th scope="col" style="text-align:center;border:1px solid green;">20대 이하</th>
			<th scope="col" style="text-align:center;border:1px solid green;">20대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">30대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">40대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">50대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">60대 이상</th>

			<th scope="col" style="text-align:center;border:1px solid green;">20대 이하</th>
			<th scope="col" style="text-align:center;border:1px solid green;">20대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">30대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">40대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">50대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">60대 이상</th>

			<th scope="col" style="text-align:center;border:1px solid green;">20대 이하</th>
			<th scope="col" style="text-align:center;border:1px solid green;">20대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">30대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">40대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">50대</th>
			<th scope="col" style="text-align:center;border:1px solid green;">60대 이상</th>

		</tr>
<?
for ($i = $to_m ; $i >= $start_m ; $i--) {

	$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

	$LIST["Y60"] = get_years_data("$srch_y-$ii", $y60f, $y60t);
	$LIST["Y50"] = get_years_data("$srch_y-$ii", $y50f, $y50t);
	$LIST["Y40"] = get_years_data("$srch_y-$ii", $y40f, $y40t);
	$LIST["Y30"] = get_years_data("$srch_y-$ii", $y30f, $y30t);
	$LIST["Y20"] = get_years_data("$srch_y-$ii", $y20f, $y20t);
	$LIST["YM"] = get_years_data("$srch_y-$ii", $ymf, $ymt);
	//if ($i==11) print_rr($LIST["Y60"]);
	?>
		<tr>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$srch_y."-".$ii?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["YM"]["P"]["sum_amount"]?number_format($LIST["YM"]["P"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y20"]["P"]["sum_amount"]?number_format($LIST["Y20"]["P"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y30"]["P"]["sum_amount"]?number_format($LIST["Y30"]["P"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y40"]["P"]["sum_amount"]?number_format($LIST["Y40"]["P"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y50"]["P"]["sum_amount"]?number_format($LIST["Y50"]["P"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y60"]["P"]["sum_amount"]?number_format($LIST["Y60"]["P"]["sum_amount"]):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["YM"]["2"]["sum_amount"]?number_format($LIST["YM"]["2"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y20"]["2"]["sum_amount"]?number_format($LIST["Y20"]["2"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y30"]["2"]["sum_amount"]?number_format($LIST["Y30"]["2"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y40"]["2"]["sum_amount"]?number_format($LIST["Y40"]["2"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y50"]["2"]["sum_amount"]?number_format($LIST["Y50"]["2"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y60"]["2"]["sum_amount"]?number_format($LIST["Y60"]["2"]["sum_amount"]):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["YM"]["1"]["sum_amount"]?number_format($LIST["YM"]["1"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y20"]["1"]["sum_amount"]?number_format($LIST["Y20"]["1"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y30"]["1"]["sum_amount"]?number_format($LIST["Y30"]["1"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y40"]["1"]["sum_amount"]?number_format($LIST["Y40"]["1"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y50"]["1"]["sum_amount"]?number_format($LIST["Y50"]["1"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y60"]["1"]["sum_amount"]?number_format($LIST["Y60"]["1"]["sum_amount"]):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["YM"]["3"]["sum_amount"]?number_format($LIST["YM"]["3"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y20"]["3"]["sum_amount"]?number_format($LIST["Y20"]["3"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y30"]["3"]["sum_amount"]?number_format($LIST["Y30"]["3"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y40"]["3"]["sum_amount"]?number_format($LIST["Y40"]["3"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y50"]["3"]["sum_amount"]?number_format($LIST["Y50"]["3"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y60"]["3"]["sum_amount"]?number_format($LIST["Y60"]["3"]["sum_amount"]):"";?></td>


		</tr>
	<?
}
?>
	</table>

</div>

<script>
function go_srch() {
	var f = document.f_srch;
	f.submit();
}
</script>
<?
function get_years_data($ym, $from , $to) {
	$y_sql = "SELECT C.category, C.mortgage_guarantees , COUNT(A.idx) sum_cnt, SUM(A.amount)/10000 sum_amount  FROM cf_product_invest_detail A
					LEFT JOIN g5_member B ON (A.member_idx = B.mb_no)
					LEFT JOIN cf_product C ON (A.product_idx = C.idx)
					WHERE A.invest_state = 'Y'
						AND SUBSTRING(A.insert_date,1,7)='$ym'
						AND substring(B.mb_birth,1,4) >= '$from' AND substring(B.mb_birth,1,4) <= '$to'
					GROUP BY C.category , C.mortgage_guarantees
					";
	$y_res = sql_query($y_sql);
	$y_cnt = $y_res->num_rows;
	for ($i=0 ; $i<$y_cnt ; $i++) {
		$row_nr = sql_fetch_array($y_res);
		if ($row_nr['category']=="1") {
			$retval[1] = $row_nr;
		} else if ($row_nr['category']=="2") {
			if ($row_nr['mortgage_guarantees'] == '1') {  // 주택담보
				$retval[2] = $row_nr;
			} else {  //PF
				$retval['P'] = $row_nr;
			}
		} else if ($row_nr['category']=="3") {
			$retval[3] = $row_nr;
		}
	}

	return $retval;
}
?>