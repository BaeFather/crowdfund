<?php
$sub_menu = "930040";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '연령별 통계 - 투자 현황';

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

$ymf = $y20t + 1 ;
$ymt = $srch_y ;
//echo "$ymf ~ $ymt 미성년자<br/>";
?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">

	<form method="post" name="f_srch">
		<select name="d_type" style="margin-right:20px;height:25px;width:180px;" onchange="javascript:self.location.href='members_years_prd.php';">
			<option selected>연령별 투자 데이터</option>
			<option>연령별 상품군 투자 데이터</option>
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
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=16>연령별 투자 현황</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>20대 이하</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>20대</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>30대</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>40대</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>50대</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>60대 이상</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>기타</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>합계</th>
		</tr>
		<tr>

			<!-- 전체 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 전체 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 부동산 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 주택담보 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 동산 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 확정매출채권 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 확정매출채권 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>

			<!-- 확정매출채권 -->
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>
		</tr>
<?
for ($i = $to_m ; $i >= $start_m ; $i--) {

	$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

	$LIST["Y60"] = get_years_d("$srch_y-$ii", $y60f, $y60t);
	$LIST["Y50"] = get_years_d("$srch_y-$ii", $y50f, $y50t);
	$LIST["Y40"] = get_years_d("$srch_y-$ii", $y40f, $y40t);
	$LIST["Y30"] = get_years_d("$srch_y-$ii", $y30f, $y30t);
	$LIST["Y20"] = get_years_d("$srch_y-$ii", $y20f, $y20t);
	$LIST["YM"]  = get_years_d("$srch_y-$ii", $ymf,  $ymt);
	$LIST["ETC"] = get_years_etc("$srch_y-$ii");
	//print_rr($LIST["Y60"]);

	$month_total_amount = $LIST["YM"]["sum_amount"] + $LIST["Y20"]["sum_amount"] + $LIST["Y30"]["sum_amount"] + $LIST["Y40"]["sum_amount"] + $LIST["Y50"]["sum_amount"] + $LIST["Y60"]["sum_amount"] + $LIST["ETC"]["sum_amount"];
	$month_total_cnt = $LIST["YM"]["sum_cnt"] + $LIST["Y20"]["sum_cnt"] + $LIST["Y30"]["sum_cnt"] + $LIST["Y40"]["sum_cnt"] + $LIST["Y50"]["sum_cnt"] + $LIST["Y60"]["sum_cnt"] + $LIST["ETC"]["sum_cnt"];
	?>
		<tr>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$srch_y."-".$ii?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<?=$LIST["YM"]["sum_amount"]?number_format($LIST["YM"]["sum_amount"]):"";?>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["YM"]["sum_amount"]?number_format(floor($LIST["YM"]["sum_amount"]/$LIST["YM"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y20"]["sum_amount"]?number_format($LIST["Y20"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["Y20"]["sum_amount"]?number_format(floor($LIST["Y20"]["sum_amount"]/$LIST["Y20"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y30"]["sum_amount"]?number_format($LIST["Y30"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["Y30"]["sum_amount"]?number_format(floor($LIST["Y30"]["sum_amount"]/$LIST["Y30"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y40"]["sum_amount"]?number_format($LIST["Y40"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["Y40"]["sum_amount"]?number_format(floor($LIST["Y40"]["sum_amount"]/$LIST["Y40"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y50"]["sum_amount"]?number_format($LIST["Y50"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["Y50"]["sum_amount"]?number_format(floor($LIST["Y50"]["sum_amount"]/$LIST["Y50"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["Y60"]["sum_amount"]?number_format($LIST["Y60"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["Y60"]["sum_amount"]?number_format(floor($LIST["Y60"]["sum_amount"]/$LIST["Y20"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["ETC"]["sum_amount"]?number_format($LIST["ETC"]["sum_amount"]):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["ETC"]["sum_amount"]?number_format(floor($LIST["ETC"]["sum_amount"]/$LIST["ETC"]["sum_cnt"])):"";?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_detail_nu('<?=$srch_y?>','<?=$ii?>','ALL','<?=$y60f?>','<?=$ymt?>');" style="cursor:pointer;">
				<?=$month_total_amount?number_format($month_total_amount):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$month_total_amount?number_format(floor($month_total_amount/$month_total_cnt)):"";?></td>
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

function view_detail_nu(sy,sm,year_gubun,yearf,yeart) {
	//alert(sy+" "+sm+" "+year_gubun+" "+fr+" "+to);
	window.open("./member_years_detail_popup.php?sy="+sy+"&sm="+sm+"&year_gubun="+year_gubun+"&fr="+yearf+"&to="+yeart, "_blank", "left=50,top=30,width=1100,height=600,scrollbars=yes");
}
</script>
<?
function get_years_d($ym, $from , $to) {
	$y_sql = "SELECT COUNT(A.idx) sum_cnt, SUM(A.amount) sum_amount
					FROM cf_product_invest A
					LEFT JOIN g5_member B ON (A.member_idx = B.mb_no)
					LEFT JOIN cf_product C ON (C.idx = A.product_idx)
					WHERE A.invest_state = 'Y'
					  AND SUBSTRING(C.loan_start_date,1,7)='$ym'
					  AND substring(B.mb_birth,1,4) >= '$from' AND substring(B.mb_birth,1,4) <= '$to'
					  AND C.state IN(1,2,5)";
	$y_res = sql_query($y_sql);
	$y_cnt = $y_res->num_rows;
	$y_row = sql_fetch_array($y_res);

	return $y_row;

}

function get_years_etc($ym) {
	$y_sql = "SELECT COUNT(A.idx) sum_cnt, SUM(A.amount) sum_amount
					FROM cf_product_invest A
					LEFT JOIN g5_member B ON (A.member_idx = B.mb_no)
					LEFT JOIN cf_product C ON (C.idx = A.product_idx)
					WHERE A.invest_state = 'Y'
					  AND SUBSTRING(C.loan_start_date,1,7)='$ym'
					  AND B.member_type='1' AND B.mb_birth=''
					  AND C.state IN(1,2,5)";
	$y_res = sql_query($y_sql);
	$y_cnt = $y_res->num_rows;
	$y_row = sql_fetch_array($y_res);

	return $y_row;

}
?>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>