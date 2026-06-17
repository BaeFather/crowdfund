<?php
include_once('./_common.php');

include_once(G5_ADMIN_PATH . '/admin.head.nomenu.php');
?>
<?
$ym = $_REQUEST['ym'];
$cate = $_REQUEST['category'];
if ($cate) {
	if ($cate=="2" or $cate=="21") {
		if ($cate=="21") $wh_cate = " and category='2' and mortgage_guarantees='1'";
		else $wh_cate = " and category='$cate' and mortgage_guarantees<>'1'";
	} else {
		$wh_cate = " and category='$cate' ";
	}
}


$sql = "select idx, category, mortgage_guarantees, title, loan_start_date, loan_usefee, invest_usefee from cf_product where substring(loan_start_date,1,7)='$ym' 
			$wh_cate
		order by loan_start_date, start_num";
//echo "$sql";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

//echo "$row[f_sum] | $row[f_cnt]";
?>
<div id="wrapper2" styleaa="display:block;clear;both;margin:20px 20px;min-width:200px;">

	<div id="container" >

		<h1 style="min-width:0px;border-bottom:0px;"><?=$ym?></h1>

			<table style="width:95%;align:center;margin:0 auto;">
			<?
			for ($i=0 ; $i<$cnt ; $i++) {
				$row = sql_fetch_array($res);

				$row['catenm'] = get_catenm($row["category"], $row["mortgage_guarantees"]);
				
				$sum_invest_usefee += $row['loan_usefee'];
				?>
				<tr>
					<td style="text-align:center;width:30px;"><?=$i+1?></td>
					<td><?=$row['title']?></td>
					<td style="text-align:center;"><?=$row['catenm']?></td>
					<td style="text-align:right;"><?=$row['loan_usefee']?></td>
					<td style="text-align:center;"><?=$row['loan_start_date']?></td>
				</tr>
				<?
			}
			?>
				<tr>
					<td><?=$cnt?></td>
					<td colspan=2 style="text-align:center;">합 계</td>
					<td style="text-align:right;"><?=$sum_invest_usefee?></td>
				</tr>
			</table>

	</div>

</div>

<?
function get_catenm($cat, $cat2) {
	if ($cat=="1") {
		$catenm = "동산";
	} else if ($cat=="2") {
		if ($cat2=="1") $catenm = "주택담보";
		else $catenm = "부동산";
	} else if ($cat=="3") {
		$catenm = "확정매출채권";
	}
	return $catenm;
}
?>