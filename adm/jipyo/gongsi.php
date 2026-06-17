<?php
$sub_menu = "930010";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '공시지표통계 - 누적금액 데이터';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$srch_y) $srch_y = date("Y");

$start_m = 1;
$to_m = 12;
if ( $srch_y==date("Y") ) $to_m=date("m");

?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">

	<form method="post" name="f_srch">
		<select name="d_type" style="margin-right:20px;height:25px;width:180px;" onchange="javascript:self.location.href='gongsi_prd_type.php';">
			<option selected>누적금액 데이터</option>
			<option>상품 유형별 데이터</option>
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
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=3>활동성</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=4>안정성</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">누적 투자금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">누적 상환금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">재투자율</th>
			<th scope="col" style="text-align:center;border:1px solid green;">대출잔액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">상환율</th>
			<th scope="col" style="text-align:center;border:1px solid green;">연체율</th>
			<th scope="col" style="text-align:center;border:1px solid green;">연체</th>
		</tr>
		<?
		for ($i = $to_m ; $i >= $start_m ; $i--) {

			$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

			$rec = get_nujuk_rec("$srch_y-$ii");
			$rep = get_nujuk_rep("$srch_y-$ii");
			$rerep = get_rerec("$srch_y-$ii");
		//$rem = get_rem("$srch_y-$ii");
			$rem = $rec['recruit_amount_total'] - $rep['recruit_amount_total'];

			$sang_per = @floor($rep['recruit_amount_total']/$rec['recruit_amount_total'] * 100 * 10000) / 10000;

			$yun = get_yun("$srch_y-$ii");
			$yun_per = @floor($yun['yun_count']/$rec['recruit_count_total'] * 100 * 10000) / 10000;
			$yun_month = get_yun_month("$srch_y-$ii");
			?>
		<tr>
			<td style="text-align:center;border:1px solid green;"><?=$i?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$rec['recruit_amount_total']?number_format($rec['recruit_amount_total']):''?></td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$rep['recruit_amount_total']?number_format($rep['recruit_amount_total']):''?></td>
			<td style="text-align:center;border:1px solid green;">
				<a title="<?=$rerep['re']?> / <?=$rerep['tot']?>">
				<?=$rerep['per']?number_format($rerep['per'],2)." %":''?></a>
			</td>
			<td style="text-align:center;border:1px solid green;font-weight:bold;"><?=$rem?number_format($rem):''?></td>
			<td style="text-align:center;border:1px solid green;"><?=number_format($sang_per,2)?> %</td>
			<td style="text-align:center;border:1px solid green;"><?=number_format($yun_per,2)?> %</td>
			<td style="text-align:center;border:1px solid green;"><?=number_format($yun_month['yun_count'])?> </td>
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

// 누적 투자금액 및 건수
function get_nujuk_rec($ym) {

	$sql_nr = "
		SELECT
			COUNT(idx) AS recruit_count_total,
			SUM(recruit_amount) AS recruit_amount_total
		FROM
			cf_product A
		WHERE 1
			AND A.display = 'Y' AND A.isTest = '' AND recruit_amount > 10000
			AND A.state NOT IN('3','6','7')
			AND A.loan_start_date > '0000-00-00' AND A.loan_start_date <= '$ym-31'
		ORDER BY
			idx";
	$res_nr = sql_query($sql_nr);
	$row_nr = sql_fetch_array($res_nr);

	$retval['recruit_count_total']  = $row_nr['recruit_count_total'];
	$retval['recruit_amount_total'] = floor($row_nr['recruit_amount_total']/1000);

	return $retval;

}

// 누적 상환금액 및 건수
function get_nujuk_rep($ym) {

	if($ym == date("Y-m")) {
		$sql_nr = "
			SELECT
				COUNT(idx) AS recruit_count_total
			FROM
				cf_product
			WHERE 1
				AND display = 'Y' AND isTest = '' AND recruit_amount > 10000
				AND state IN('2','5')
				AND (loan_end_date>'0000-00-00' AND loan_end_date <= '".date("Y-m-d")."')";

		$sql_nr2 = "
			SELECT
				IFNULL(SUM(principal),0) AS sum_paid_principal
			FROM
				cf_product_give
			WHERE 1
				AND banking_date <= '".date("Y-m-d H:i:s")."'";

	}
	else {

		$sql_nr = "
			SELECT
				COUNT(idx) AS recruit_count_total
			FROM
				cf_product
			WHERE 1
				AND display = 'Y' AND isTest = '' AND recruit_amount > 10000
				AND state IN('2','5')
				AND (loan_end_date > '0000-00-00' AND loan_end_date <= '$ym-31')";

		$sql_nr2 = "
			SELECT
				IFNULL(SUM(principal),0) AS sum_paid_principal
			FROM
				cf_product_give
			WHERE 1
				AND banking_date <= '$ym-31 29:59:59'";

	}
	$row_nr  = sql_fetch($sql_nr);
	$row_nr2 = sql_fetch($sql_nr2);

	$retval['recruit_count_total']  = $row_nr['recruit_count_total'];
	$retval['recruit_amount_total'] = floor($row_nr2['sum_paid_principal']/1000);

	return $retval;
}

function get_rerec($ym) {
	$sql_total_inv_mem = "SELECT COUNT(DISTINCT member_idx) AS cnt FROM cf_product_invest_detail WHERE invest_state='Y' and insert_date<='$ym-31'";
	$res_total_inv_mem = sql_query($sql_total_inv_mem);
	$row_total_inv_mem = sql_fetch_array($res_total_inv_mem);
  $total_inv_mem = $row_total_inv_mem['cnt'];
	/*
	$sql_total_dup_mem = "
		SELECT
			A.member_idx, count(A.member_idx) cnt,
			(SELECT COUNT(*) FROM cf_product_invest_detail WHERE invest_state='Y' AND insert_date<'$ym-01' AND member_idx=A.member_idx) before_invest
		FROM
			cf_product_invest_detail A
		WHERE
			A.invest_state = 'Y'
			AND (A.insert_date >= '$ym-01' AND A.insert_date <= '$ym-31')
		GROUP BY
			A.member_idx
		HAVING
			cnt>0 AND before_invest>0";
	*/
	$sql_total_dup_mem = "
		SELECT
			A.member_idx,
			COUNT(A.member_idx) cnt
		FROM
			cf_product_invest_detail A
		WHERE 1
			AND A.invest_state = 'Y'
			AND (A.insert_date <= '$ym-31')
		GROUP BY
			A.member_idx
		HAVING
			cnt > 1 ";
	$res_total_dup_mem = sql_query($sql_total_dup_mem);
	$total_dup_mem = $res_total_dup_mem->num_rows;

	$re_inv = @floor( (($total_dup_mem / $total_inv_mem) * 100) *100 ) / 100;

	$retval['tot'] = $total_inv_mem;
	$retval['re']  = $total_dup_mem;
	$retval['per'] = $re_inv;
	//echo "<br/>$ym $re_inv $total_dup_mem / $total_inv_mem";
	return $retval;
}

function get_rem($ym) {
	if ($ym==date("Y-m")) {
		$sql = "SELECT SUM(recruit_amount) AS rem_money, loan_end_date FROM cf_product WHERE loan_start_date <= '".date("Y-m-d")."' AND loan_end_date > '".date("Y-m-d")."' AND state NOT IN('3','6','7')";
	}
	else {
		$sql = "SELECT SUM(recruit_amount) AS rem_money, loan_end_date FROM cf_product WHERE loan_start_date < '$ym-01' AND loan_end_date > '$ym-31'";
		$sql = "SELECT SUM(recruit_amount) AS rem_money, loan_end_date FROM cf_product WHERE loan_start_date <= '$ym-31' AND loan_end_date > '$ym-31' AND state NOT IN('3','6','7')";
	}
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row['rem_money'] = floor($row['rem_money']/1000);

	return $row['rem_money'];
}

function get_yun($ym) {

	$sql = "SELECT SUM(recruit_amount) AS yun_money, COUNT(idx) cnt FROM cf_product WHERE loan_end_date_orig <> '0000-00-00' AND loan_end_date_orig < '$ym-01' and loan_end_date > '$ym-31'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row['yun_money'] = floor($row['yun_money']/1000);

	$retval['yun_money'] = $row['yun_money']?$row['yun_money']:0 ;
	$retval['yun_count'] = $row['yun_count']?$row['yun_cnt']:0 ;

	return $retval;

}

function get_yun_month($ym) {

	$sql = "SELECT SUM(recruit_amount) AS yun_money, COUNT(idx) cnt FROM cf_product WHERE loan_end_date_orig <> '0000-00-00' AND loan_end_date_orig <= '$ym-01' and loan_end_date > '$ym-31'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$row['yun_money'] = floor($row['yun_money']/1000);

	$retval['yun_money'] = $row['yun_money']?$row['yun_money']:0 ;
	$retval['yun_count'] = $row['yun_count']?$row['yun_cnt']:0 ;

	return $retval;

}



include_once (G5_ADMIN_PATH.'/admin.tail.php');

?>