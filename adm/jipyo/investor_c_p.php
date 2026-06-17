<?php
$sub_menu = "930040";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '투자자 통계 - 법인회원 상품당 투자금액 현황';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
if (!$srch_y) $srch_y = date("Y");

$start_m = 1;
$to_m = 12;
if ( $srch_y==date("Y") ) $to_m=date("m");
?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">

	<form method="post" name="f_srch">
		<select name="d_type" style="margin-right:20px;height:25px;width:250px;" onchange="go_inv();">
			<option value="1">회원 투자 현황</option>
			<option value="2">개인회원 상품당 투자금액 현황</option>
			<!--option value="3">개인회원 월별 평균투자금액 현황</option-->
			<option value="4" selected>법인회원 상품당 투자금액 현황</option>
			<!--option value="5">법인회원 월별 평균투자금액 현황</option-->
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
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>1만원~100만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>101만원~300만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>301만원~500만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>501만원~1000만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>1001만원~2000만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>2001만원~3000만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>3001만원~5000만원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>5001만원~1억원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>1억원 초과</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>합계</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">금액</th>
		</tr>
<?
for ($i = $to_m ; $i >= $start_m ; $i--) {

	$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

	$LIST[$i] = get_sum_case("$srch_y-$ii");

	?>
		<tr>
			<td style="text-align:center; border:1px solid green;"><?=$LIST[$i][1]["ym"]?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][1]["sub_cnt"]?number_format($LIST[$i][1]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][1]["sub_sum"]?number_format($LIST[$i][1]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][2]["sub_cnt"]?number_format($LIST[$i][2]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][2]["sub_sum"]?number_format($LIST[$i][2]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][3]["sub_cnt"]?number_format($LIST[$i][3]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][3]["sub_sum"]?number_format($LIST[$i][3]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][4]["sub_cnt"]?number_format($LIST[$i][4]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][4]["sub_sum"]?number_format($LIST[$i][4]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][5]["sub_cnt"]?number_format($LIST[$i][5]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][5]["sub_sum"]?number_format($LIST[$i][5]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][6]["sub_cnt"]?number_format($LIST[$i][6]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][6]["sub_sum"]?number_format($LIST[$i][6]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][7]["sub_cnt"]?number_format($LIST[$i][7]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][7]["sub_sum"]?number_format($LIST[$i][7]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][8]["sub_cnt"]?number_format($LIST[$i][8]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][8]["sub_sum"]?number_format($LIST[$i][8]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i][9]["sub_cnt"]?number_format($LIST[$i][9]["sub_cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i][9]["sub_sum"]?number_format($LIST[$i][9]["sub_sum"]):""?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST[$i]["total"]["cnt"]?number_format($LIST[$i]["total"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST[$i]["total"]["amt"]?number_format($LIST[$i]["total"]["amt"]):""?></td>

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
function go_inv() {
	var f = document.f_srch;
	var go_page = "";
	if (f.d_type.value=="1") {
		go_page = "investor.php";
	} else if (f.d_type.value=="2") {
		go_page = "investor_p_p.php";
	} else if (f.d_type.value=="3") {
		go_page = "investor_p_m.php";
	} else if (f.d_type.value=="4") {
		go_page = "investor_c_p.php";
	} else if (f.d_type.value=="5") {
		go_page = "investor_c_m.php";
	}
	self.location.href = go_page;
}
</script>

<?
function get_sum_case($ym) {

	$sql = "SELECT substring(B.loan_start_date,1,7) ym, COUNT(A.idx) sub_cnt, SUM(A.amount) sub_sum, A.amount,
				CASE
					WHEN A.amount<10000 THEN 11
					WHEN A.amount BETWEEN 10000 AND 1000000 THEN 1
					WHEN A.amount BETWEEN 1000001 AND 3000000 THEN 2
					WHEN A.amount BETWEEN 3000001 AND 5000000 THEN 3
					WHEN A.amount BETWEEN 5000001 AND 10000000 THEN 4
					WHEN A.amount BETWEEN 10000001 AND 20000000 THEN 5
					WHEN A.amount BETWEEN 20000001 AND 30000000 THEN 6
					WHEN A.amount BETWEEN 30000001 AND 50000000 THEN 7
					WHEN A.amount BETWEEN 50000001 AND 100000000 THEN 8
					WHEN A.amount > 100000000 THEN 9
					ELSE 99
				END AS cc
			FROM cf_product_invest A
			LEFT JOIN cf_product B ON(B.idx=A.product_idx)
			LEFT JOIN g5_member C ON(C.mb_no=A.member_idx)
			WHERE (B.loan_start_date>='$ym-01' AND B.loan_start_date<='$ym-31')
			  AND C.member_type='2'
			  AND B.state IN(1,2,5)
			  AND A.invest_state = 'Y'
			GROUP BY cc ORDER BY cc";
	//if ($ym=="2019-12") echo "$sql<br/><br/>";

	$res = sql_query($sql);
	$cnt = $res->num_rows;


	for ($i=0 ; $i<$cnt ; $i++) {
		if ($i>20) die("safe die loop");
		$row = sql_fetch_array($res);
		$row["sub_sum"] = floor($row["sub_sum"]/1000);

		if ($row["cc"]==1) {
			$retval[1] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==2) {
			$retval[2] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==3) {
			$retval[3] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==4) {
			$retval[4] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==5) {
			$retval[5] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==6) {
			$retval[6] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==7) {
			$retval[7] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==8) {
			$retval[8] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		} else if ($row["cc"]==9) {
			$retval[9] = $row;
			$retval["total"]["cnt"] += $row["sub_cnt"];
			$retval["total"]["amt"] += $row["sub_sum"];
		}

	}

	//echo "<pre>";print_r($retval);echo "</pre>";

	return $retval;
}
?>