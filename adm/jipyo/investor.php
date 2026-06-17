<?php
$sub_menu = "930040";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '투자자 통계 - 회원 투자 현황';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
if (!$srch_y) $srch_y = date("Y");
$month_before = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -1 month' ) );
$month_before = substr($month_before,0,7);

$start_m = 1;
$to_m = 12;
if ( $srch_y==date("Y") ) $to_m=date("m");
?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">

	<form method="post" name="f_srch">
		<select name="d_type" style="margin-right:20px;height:25px;width:250px;" onchange="go_inv();">
			<option value="1" selected>회원 투자 현황</option>
			<option value="2">개인회원 상품당 투자금액 현황</option>
			<!--option value="3">개인회원 월별 평균투자금액 현황</option-->
			<option value="4">법인회원 상품당 투자금액 현황</option>
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
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=22>헬로펀딩 회원 투자 현황 (단위:천원)</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=3>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=7>누적 투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=7>월별 투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=7>신규 투자금액</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>전체</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>법인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>비율</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>전체</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>법인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>비율</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>전체</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>법인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>비율</th>
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
		</tr>
<?
for ($i = $to_m ; $i >= $start_m ; $i--) {

	$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

	$LIST["nu"] = get_nujuk("$srch_y-$ii");
	$per_p = round(($LIST["nu"]["P"]["amount"] / $LIST["nu"]["A"]["amount"]) * 100 * 10) / 10;
	$per_c = round(($LIST["nu"]["C"]["amount"] / $LIST["nu"]["A"]["amount"]) * 100 * 10) / 10;
	$LIST["nu"]["bi"] = number_format($per_p,1) . " <b>:</b> ". number_format($per_c,1);

	$LIST["nu2"] = get_nujuk_invest_type("$srch_y-$ii");

	$LIST["ym"] = get_month("$srch_y-$ii");
	$perm_p = round(($LIST["ym"]["P"]["amount"] / $LIST["ym"]["A"]["amount"]) * 100 * 10) / 10;
	$perm_c = round(($LIST["ym"]["C"]["amount"] / $LIST["ym"]["A"]["amount"]) * 100 * 10) / 10;
	$LIST["ym"]["bi"] = number_format($perm_p,1) . " <b>:</b> ". number_format($perm_c,1);

	unset($LIST["nw"]);
	if ( "$srch_y-$ii" == date("Y-m") or "$srch_y-$ii" == $month_before or 2>1) $LIST["nw"] = new_inv("$srch_y-$ii");
	if ($LIST["nw"]["A"]["amount"]) $pern_p = round(($LIST["nw"]["P"]["amount"] / $LIST["nw"]["A"]["amount"]) * 100 * 10) / 10;
	if ($LIST["nw"]["A"]["amount"]) $pern_c = round(($LIST["nw"]["C"]["amount"] / $LIST["nw"]["A"]["amount"]) * 100 * 10) / 10;
	$LIST["nw"]["bi"] = number_format($pern_p,1) . " <b>:</b> ". number_format($pern_c,1);

	?>
		<tr>
			<td style="text-align:center; border:1px solid green;"><?=$srch_y."-".$ii?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<div><?=$LIST["nu"]["A"]["cnt"]?number_format($LIST["nu"]["A"]["cnt"]):""?></div>
				<div style="background-color:#E5F1FB; margin-top:5px;">
				일반 <?=$LIST["nu2"]["P1"]["cnt"]?number_format($LIST["nu2"]["P1"]["cnt"]):""?><br/>
				소득 <?=$LIST["nu2"]["P2"]["cnt"]?number_format($LIST["nu2"]["P2"]["cnt"]):""?><br/>
				전문 <?=$LIST["nu2"]["P3"]["cnt"]?number_format($LIST["nu2"]["P3"]["cnt"]):""?><br/>
				법인 <?=$LIST["nu"]["C"]["cnt"]?number_format($LIST["nu"]["C"]["cnt"]):""?>
				</div>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<div><?=$LIST["nu"]["A"]["amount"]?number_format($LIST["nu"]["A"]["amount"]):""?></div>
				<div style="background-color:#E5F1FB; margin-top:5px;font-weight:normal;">
				일반 <?=$LIST["nu2"]["P1"]["amount"]?number_format($LIST["nu2"]["P1"]["amount"]):""?><br/>
				소득 <?=$LIST["nu2"]["P2"]["amount"]?number_format($LIST["nu2"]["P2"]["amount"]):""?><br/>
				전문 <?=$LIST["nu2"]["P3"]["amount"]?number_format($LIST["nu2"]["P3"]["amount"]):""?><br/>
				법인 <?=$LIST["nu"]["C"]["amount"]?number_format($LIST["nu"]["C"]["amount"]):""?>
				</div>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<?=$LIST["nu"]["P"]["cnt"]?number_format($LIST["nu"]["P"]["cnt"]):""?>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<?=$LIST["nu"]["P"]["amount"]?number_format($LIST["nu"]["P"]["amount"]):""?>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["nu"]["C"]["cnt"]?number_format($LIST["nu"]["C"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["nu"]["C"]["amount"]?number_format($LIST["nu"]["C"]["amount"]):""?></td>
			<td style="text-align:center; border:1px solid green;"><?=$LIST["nu"]["bi"]?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["ym"]["A"]["cnt"]?number_format($LIST["ym"]["A"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["ym"]["A"]["amount"]?number_format($LIST["ym"]["A"]["amount"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["ym"]["P"]["cnt"]?number_format($LIST["ym"]["P"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["ym"]["P"]["amount"]?number_format($LIST["ym"]["P"]["amount"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["ym"]["C"]["cnt"]?number_format($LIST["ym"]["C"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["ym"]["C"]["amount"]?number_format($LIST["ym"]["C"]["amount"]):""?></td>
			<td style="text-align:center; border:1px solid green;"><?=$LIST["ym"]["bi"]?></td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["nw"]["A"]["cnt"]?number_format($LIST["nw"]["A"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["nw"]["A"]["amount"]?number_format($LIST["nw"]["A"]["amount"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["nw"]["P"]["cnt"]?number_format($LIST["nw"]["P"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["nw"]["P"]["amount"]?number_format($LIST["nw"]["P"]["amount"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$LIST["nw"]["C"]["cnt"]?number_format($LIST["nw"]["C"]["cnt"]):""?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$LIST["nw"]["C"]["amount"]?number_format($LIST["nw"]["C"]["amount"]):""?></td>
			<td style="text-align:center; border:1px solid green;"><?=$LIST["nw"]["bi"]?></td>
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
function get_nujuk($ym) {

	$sql = "SELECT B.member_type, COUNT(A.idx) cnt , SUM(A.amount) amount , B.member_investor_type
			  FROM cf_product_invest A
	     LEFT JOIN g5_member B ON(A.member_idx=B.mb_no)
		 LEFT JOIN cf_product C ON(C.idx = A.product_idx)
			 WHERE invest_state='Y'
			   AND C.loan_start_date <= '$ym-31'
			   AND C.state in (1,2,5)
	      GROUP BY B.member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		if ($i>10) die("safe die loop");

		$row = sql_fetch_array($res);
		$row["amount"] = floor($row["amount"]/1000);

		if ($row['member_type']=="2") {					// 법인회원

			$retval["C"]["cnt"] += $row["cnt"];
			$retval["C"]["amount"] += $row["amount"];

			$retval["A"]["cnt"] += $row["cnt"];
			$retval["A"]["amount"] += $row["amount"];

		} else if ($row['member_type']=="1") {         // 개인회원

			$retval["P"]["cnt"] += $row["cnt"];
			$retval["P"]["amount"] += $row["amount"];

			$retval["A"]["cnt"] += $row["cnt"];
			$retval["A"]["amount"] += $row["amount"];

		}
	}

	return $retval;
}

function get_nujuk_invest_type($ym) {

	$sql = "SELECT B.member_type, COUNT(A.idx) cnt , SUM(A.amount) amount , B.member_investor_type
			  FROM cf_product_invest A
	     LEFT JOIN g5_member B ON(A.member_idx=B.mb_no)
		 LEFT JOIN cf_product C ON(C.idx = A.product_idx)
			 WHERE invest_state='Y'
			   AND C.loan_start_date <= '$ym-31'
			   AND C.state in (1,2,5)
	      GROUP BY B.member_type, B.member_investor_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		if ($i>10) die("safe die loop");

		$row = sql_fetch_array($res);
		$row["amount"] = floor($row["amount"]/1000);

		if ($row['member_type']=="2") {					// 법인회원

			$retval["C"]["cnt"] += $row["cnt"];
			$retval["C"]["amount"] += $row["amount"];

			$retval["A"]["cnt"] += $row["cnt"];
			$retval["A"]["amount"] += $row["amount"];

		} else if ($row['member_type']=="1") {         // 개인회원

			$retval["P"]["cnt"] += $row["cnt"];
			$retval["P"]["amount"] += $row["amount"];

			$retval["A"]["cnt"] += $row["cnt"];
			$retval["A"]["amount"] += $row["amount"];

			if ($row["member_investor_type"] == "1") {  // 개인 - 일반투자자
				$retval["P1"]["cnt"] += $row["cnt"];
				$retval["P1"]["amount"] += $row["amount"];
			} else if ($row["member_investor_type"] == "2") {  // 개인 - 소득적격
				$retval["P2"]["cnt"] += $row["cnt"];
				$retval["P2"]["amount"] += $row["amount"];
			} else if ($row["member_investor_type"] == "3") {  // 개인 - 전문투자자
				$retval["P3"]["cnt"] += $row["cnt"];
				$retval["P3"]["amount"] += $row["amount"];
			}


		}
	}

	return $retval;
}

function get_month($ym) {
	$sql = "SELECT B.member_type, COUNT(A.idx) cnt , SUM(A.amount) amount
			  FROM cf_product_invest A
	     LEFT JOIN g5_member B ON(A.member_idx=B.mb_no)
		 LEFT JOIN cf_product C ON(C.idx = A.product_idx)
			 WHERE invest_state='Y'
			   AND (C.loan_start_date>='$ym-01' AND C.loan_start_date <= '$ym-31')
	      GROUP BY B.member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		if ($i>10) die("safe die loop");

		$row = sql_fetch_array($res);
		$row["amount"] = floor($row["amount"]/1000);

		if ($row['member_type']=="2") {
			$retval["C"]["cnt"] += $row["cnt"];
			$retval["C"]["amount"] += $row["amount"];

			$retval["A"]["cnt"] += $row["cnt"];
			$retval["A"]["amount"] += $row["amount"];

		} else if ($row['member_type']=="1") {
			$retval["P"]["cnt"] += $row["cnt"];
			$retval["P"]["amount"] += $row["amount"];

			$retval["A"]["cnt"] += $row["cnt"];
			$retval["A"]["amount"] += $row["amount"];
		} else {
			echo $row['member_type']." MEMBER TYPE ERROR <br/>";
		}
	}

	return $retval;
}

function new_inv($ym) {
	$sql = "SELECT count(A.member_idx) cnt, sum(A.amount) amount
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
		 LEFT JOIN cf_product C ON (C.idx = A.product_idx)
			 WHERE A.first_inv='Y'
			   AND B.member_type='1'
			   AND SUBSTRING(C.loan_start_date,1,7)='$ym'";
	//if ($ym=="2019-12") echo "$sql";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row["amount"] = floor($row["amount"]/1000);

	$retval["P"]["cnt"] = $row["cnt"];
	$retval["P"]["amount"] = $row["amount"];

	$sql = "SELECT count(A.member_idx) cnt, sum(A.amount) amount
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
		 LEFT JOIN cf_product C ON (C.idx = A.product_idx)
			 WHERE A.first_inv='Y'
			   AND B.member_type='2'
			   AND SUBSTRING(C.loan_start_date,1,7)='$ym'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row["amount"] = floor($row["amount"]/1000);

	$retval["C"]["cnt"] = $row["cnt"];
	$retval["C"]["amount"] = $row["amount"];

	$retval["A"]["cnt"] = $retval["P"]["cnt"] + $retval["C"]["cnt"];
	$retval["A"]["amount"] = $retval["P"]["amount"] + $retval["C"]["amount"];

	return $retval;
}

function new_inv_old($ym) {
	$sql = "SELECT count(A.member_idx) cnt, sum(A.amount) amount
			  FROM (
					SELECT member_idx, MIN(concat(C.loan_start_date,' ',C.idx)) first_inv, amount, B.member_type
					  FROM cf_product_invest
			   	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest.member_idx)
				 LEFT JOIN cf_product C ON (C.idx = cf_product_invest.product_idx)
					 WHERE invest_state='Y'
					   AND B.member_type='1'
					   AND C.state IN(1,2,5)
					 GROUP BY member_idx
				    HAVING substring(first_inv,1,7)='$ym'
				  ORDER BY amount) A";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row["amount"] = floor($row["amount"]/1000);

	$retval["P"]["cnt"] = $row["cnt"];
	$retval["P"]["amount"] = $row["amount"];

	/*
	$sql = "SELECT count(A.member_idx) cnt, sum(A.amount) amount
			  FROM (SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, amount, B.member_type
					FROM cf_product_invest
			   	    LEFT JOIN g5_member B on (B.mb_no = cf_product_invest.member_idx)
					WHERE B.member_type='2'
					GROUP BY member_idx
					HAVING substring(first_inv,1,7)='$ym') A";
	*/
	$sql = "SELECT count(A.member_idx) cnt, sum(A.amount) amount
			  FROM (
					SELECT member_idx, MIN(concat(C.loan_start_date,' ',C.idx)) first_inv, amount, B.member_type
					  FROM cf_product_invest
			   	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest.member_idx)
				 LEFT JOIN cf_product C ON (C.idx = cf_product_invest.product_idx)
					 WHERE invest_state='Y'
					   AND B.member_type='2'
					   AND C.state IN(1,2,5)
					 GROUP BY member_idx
					 HAVING substring(first_inv,1,7)='$ym'
	  			   ORDER BY amount) A";
	//echo "<br/><br/>$sql";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row["amount"] = floor($row["amount"]/1000);

	$retval["C"]["cnt"] = $row["cnt"];
	$retval["C"]["amount"] = $row["amount"];

	$retval["A"]["cnt"] = $retval["P"]["cnt"] + $retval["C"]["cnt"];
	$retval["A"]["amount"] = $retval["P"]["amount"] + $retval["C"]["amount"];

	return $retval;
}
?>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>