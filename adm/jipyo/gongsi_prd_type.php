<?php
$sub_menu = "930010";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '공시지표통계 - 상품 유형별 데이터';

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
		<select name="d_type" style="margin-right:20px;height:25px;width:180px;" onchange="javascript:self.location.href='gongsi.php';">
			<option>누적금액 데이터</option>
			<option selected>상품 유형별 데이터</option>
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
		<span style="margin-left:20px;">단위 : 천원</span>
	</form>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=3>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=24>헬로펀딩 성장성</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>전체</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>부동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>주택담보</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>소상공인</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>면세점</th>
		</tr>
		<tr>
			<!-- 전체 -->
			<th scope="col" style="text-align:center;border:1px solid green;">누적건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규금액</th>

			<!-- 부동산 -->
			<th scope="col" style="text-align:center;border:1px solid green;">누적건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규금액</th>

			<!-- 주택담보 -->
			<th scope="col" style="text-align:center;border:1px solid green;">누적건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규금액</th>

			<!-- 동산 -->
			<th scope="col" style="text-align:center;border:1px solid green;">누적건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규금액</th>

			<!-- 소상공인 -->
			<th scope="col" style="text-align:center;border:1px solid green;">누적건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규금액</th>

			<!-- 면세점 -->
			<th scope="col" style="text-align:center;border:1px solid green;">누적건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규건수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">신규금액</th>
		</tr>
		<tr style="background-color:#E7EAED;">
			<td style="text-align:center; border:1px solid green;"></td>

			<td style="text-align:center; border:1px solid green;" colspan=2>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="AC"></span></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="AA"></span></td>

			<td style="text-align:center; border:1px solid green;" colspan=2>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="BC"></span></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="BA"></span></td>

			<td style="text-align:center; border:1px solid green;" colspan=2>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="JC"></span></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="JA"></span></td>

			<td style="text-align:center; border:1px solid green;" colspan=2>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="DC"></span></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="DA"></span></td>

			<td style="text-align:center; border:1px solid green;" colspan=2>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="MC1"></span></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="MA1"></span></td>

			<td style="text-align:center; border:1px solid green;" colspan=2>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="MC2"></span></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><span id="MA2"></span></td>
		</tr>
		<?
		for ($i = $to_m ; $i >= $start_m ; $i--) {
			if ($i<0) die("safe die");
			$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

			// 누적데이타
			$rec = get_nujuk_rec_cat("$srch_y-$ii");
			$rep = get_nujuk_rep_cat("$srch_y-$ii");

			$total_rec_cnt = $rec['1']['recruit_count_total'] +$rec['2']['recruit_count_total'] +$rec['P']['recruit_count_total'] +$rec['3']['recruit_count_total'] + $rec['4']['recruit_count_total'];
			$total_rec_amt = $rec['1']['recruit_amount_total']+$rec['2']['recruit_amount_total']+$rec['P']['recruit_amount_total']+$rec['3']['recruit_amount_total'] +$rec['4']['recruit_amount_total'];

			$total_rep_cnt = $rep['1']['recruit_count_total'] +$rep['2']['recruit_count_total'] +$rep['P']['recruit_count_total'] +$rep['3']['recruit_count_total']+$rep['4']['recruit_count_total'];
			$total_rep_amt = $rep['1']['recruit_amount_total']+$rep['2']['recruit_amount_total']+$rep['P']['recruit_amount_total']+$rep['3']['recruit_amount_total']+$rep['4']['recruit_amount_total'];

			$total_ac += $total_rep_cnt;
			$total_aa += $total_rep_amt;
			$total_bc += $rep['P']['recruit_count_total'];
			$total_ba += $rep['P']['recruit_amount_total'];
			$total_jc += $rep['2']['recruit_count_total'];
			$total_ja += $rep['2']['recruit_amount_total'];
			$total_dc += $rep['1']['recruit_count_total'];
			$total_da += $rep['1']['recruit_amount_total'];
			$total_mc1 += $rep['3']['recruit_count_total'];
			$total_ma1 += $rep['3']['recruit_amount_total'];
			$total_mc2 += $rep['4']['recruit_count_total'];
			$total_ma2 += $rep['4']['recruit_amount_total'];
			?>
		<tr>
			<!-- 전체 -->
			<td style="text-align:right; border:1px solid green;"><?=$i?> 월</td>

			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<?=$total_rec_cnt?number_format($total_rec_cnt):"";?>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$total_rec_amt?number_format($total_rec_amt):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','A');" style="cursor:pointer;color:black;">
				<?=$total_rep_cnt?number_format($total_rep_cnt):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','A');" style="cursor:pointer;color:black;">
				<?=$total_rep_amt?number_format($total_rep_amt):"";?></a>
			</td>

			<!-- 부동산 -->
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$rec['P']['recruit_count_total']?number_format($rec['P']['recruit_count_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$rec['P']['recruit_amount_total']?number_format($rec['P']['recruit_amount_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','B');" style="cursor:pointer;color:black;">
				<?=$rep['P']['recruit_count_total']?number_format($rep['P']['recruit_count_total']):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','B');" style="cursor:pointer;color:black;">
				<?=$rep['P']['recruit_amount_total']?number_format($rep['P']['recruit_amount_total']):"";?></a>
			</td>

			<!-- 주택담보 -->
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$rec['2']['recruit_count_total']?number_format($rec['2']['recruit_count_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$rec['2']['recruit_amount_total']?number_format($rec['2']['recruit_amount_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','J');" style="cursor:pointer;color:black;">
				<?=$rep['2']['recruit_count_total']?number_format($rep['2']['recruit_count_total']):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','J');" style="cursor:pointer;color:black;">
				<?=$rep['2']['recruit_amount_total']?number_format($rep['2']['recruit_amount_total']):"";?></a>
			</td>

			<!-- 동산 -->
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$rec['1']['recruit_count_total']?number_format($rec['1']['recruit_count_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$rec['1']['recruit_amount_total']?number_format($rec['1']['recruit_amount_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','D');" style="cursor:pointer;color:black;">
				<?=$rep['1']['recruit_count_total']?number_format($rep['1']['recruit_count_total']):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','D');" style="cursor:pointer;color:black;">
				<?=$rep['1']['recruit_amount_total']?number_format($rep['1']['recruit_amount_total']):"";?></a>
			</td>

			<!-- 소상공인 -->
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$rec['3']['recruit_count_total']?number_format($rec['3']['recruit_count_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$rec['3']['recruit_amount_total']?number_format($rec['3']['recruit_amount_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','M');" style="cursor:pointer;color:black;">
				<?=$rep['3']['recruit_count_total']?number_format($rep['3']['recruit_count_total']):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','M');" style="cursor:pointer;color:black;">
				<?=$rep['3']['recruit_amount_total']?number_format($rep['3']['recruit_amount_total']):"";?></a>
			</td>

			<!-- 면세점 -->
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=$rec['4']['recruit_count_total']?number_format($rec['4']['recruit_count_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;"><?=$rec['4']['recruit_amount_total']?number_format($rec['4']['recruit_amount_total']):"";?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','M');" style="cursor:pointer;color:black;">
				<?=$rep['4']['recruit_count_total']?number_format($rep['4']['recruit_count_total']):"";?></a>
			</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;font-weight:bold;">
				<a onclick="view_prd('<?=$srch_y?>','<?=$ii?>','M');" style="cursor:pointer;color:black;">
				<?=$rep['4']['recruit_amount_total']?number_format($rep['4']['recruit_amount_total']):"";?></a>
			</td>
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
function view_prd(sy,sm,gbn) {
	var srch_ymd_f = sy+"-"+sm+"-01";
	var srch_ymd_t = sy+"-"+sm+"-31";
	window.open("prd_list.php?srch_ymd_f="+srch_ymd_f+"&srch_ymd_t="+srch_ymd_t+"&gubun="+gbn, "_blank", "left=50,top=30,width=1100,height=900,scrollbars=yes");
}
$("#AC").text("<?=number_format($total_ac)?>");
$("#AA").text("<?=number_format($total_aa)?>");

$("#BC").text("<?=number_format($total_bc)?>");
$("#BA").text("<?=number_format($total_ba)?>");

$("#JC").text("<?=number_format($total_jc)?>");
$("#JA").text("<?=number_format($total_ja)?>");

$("#DC").text("<?=number_format($total_dc)?>");
$("#DA").text("<?=number_format($total_da)?>");

$("#MC1").text("<?=number_format($total_mc1)?>");
$("#MA1").text("<?=number_format($total_ma1)?>");

$("#MC2").text("<?=number_format($total_mc2)?>");
$("#MA2").text("<?=number_format($total_ma2)?>");
</script>

<?
function get_nujuk_rec_cat($ym) {
	// 누적 투자금액 및 건수
	$sql_nr = "
		SELECT category,category2, mortgage_guarantees, sum(recruit_amount) recruit_amount_total, count(idx) recruit_count_total
		FROM cf_product A
		WHERE 1
			AND A.display='Y' AND A.isTest=''
			AND state NOT IN('3','6','7')
			AND loan_start_date>'0000-00-00'
			AND loan_start_date<='$ym-31'
		GROUP BY category, mortgage_guarantees, category2
		ORDER BY category, mortgage_guarantees";
		//echo "$sql_nr<br/>";
	$res_nr = sql_query($sql_nr);
	$cnt_nr = $res_nr->num_rows;

	for ($i=0 ; $i<$cnt_nr ; $i++) {

		$row_nr = sql_fetch_array($res_nr);
		$row_nr["recruit_amount_total"] = floor($row_nr["recruit_amount_total"]/1000);

		if ($row_nr['category']=="1") {
			$retval[1] = $row_nr;
		} else if ($row_nr['category']=="2") {
			if ($row_nr['mortgage_guarantees'] == '1') {  // 주택담보
				$retval[2] = $row_nr;
			} else {  //PF
				$retval['P'] = $row_nr;
			}
		} else if ($row_nr['category']=="3") {
			if ($row_nr['category2']=="1") {
				$retval[3] = $row_nr;
			} else if ($row_nr['category2']=="2") {
				$retval[4] = $row_nr;
			}
		}

	}

	return $retval;
}

function get_nujuk_rep_cat($ym) {
	// 누적 상환금액 및 건수
	$sql_nr = "
		SELECT category, category2, mortgage_guarantees, sum(recruit_amount) recruit_amount_total, count(idx) recruit_count_total
		FROM cf_product A
		WHERE 1
			AND A.display='Y' AND A.isTest=''
			AND state NOT IN('3','6','7')
			AND loan_start_date>='$ym-01'
			AND loan_start_date<='$ym-31'
		GROUP BY category, mortgage_guarantees, category2
		ORDER BY category, mortgage_guarantees";
	$res_nr = sql_query($sql_nr);
	$cnt_nr = $res_nr->num_rows;

	for ($i=0 ; $i<$cnt_nr ; $i++) {

		$row_nr = sql_fetch_array($res_nr);
		$row_nr["recruit_amount_total"] = floor($row_nr["recruit_amount_total"]/1000);

		if ($row_nr['category']=="1") {
			$retval[1] = $row_nr;
		} else if ($row_nr['category']=="2") {
			if ($row_nr['mortgage_guarantees'] == '1') {  // 주택담보
				$retval[2] = $row_nr;
			} else {  //PF
				$retval['P'] = $row_nr;
			}
		} else if ($row_nr['category']=="3") {
			if ($row_nr['category2']=="1") {
				$retval[3] = $row_nr;
			} else if ($row_nr['category2']=="2") {
				$retval[4] = $row_nr;
			}
		}

	}

	return $retval;
}
?>