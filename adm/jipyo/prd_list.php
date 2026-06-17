<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') {
	alert('최고관리자만 접근 가능합니다.');
}

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$ii = str_pad($sm, 2 , '0' , STR_PAD_LEFT);

if (!$srch_ymd_f) $srch_ymd_f=date("Y-m")."-01";
if (!$srch_ymd_t) $srch_ymd_t=date("Y-m")."-".date('t', strtotime($srch_ymd_f));
?>
<?
if ($gubun=="A") {
	$wsql_prd = "";
	$type_name = "전체";
} else if ($gubun=="B") {
	$wsql_prd = " AND (A.category='2' AND A.mortgage_guarantees='')";
	$type_name = "부동산";
} else if ($gubun=="J") {
	$wsql_prd = " AND (A.category='2' AND A.mortgage_guarantees='1')";
	$type_name = "주택담보";
} else if ($gubun=="D") {
	$wsql_prd = " AND (A.category='1')";
	$type_name = "동산";
} else if ($gubun=="M") {
	$wsql_prd = " AND (A.category='3')";
	$type_name = "확정매출채권";

} else if ($gubun=="M1") {
	$wsql_prd = " AND (A.category='3' AND A.category2='1')";
	$type_name = "소상공인";

} else if ($gubun=="M2") {
	$wsql_prd = " AND (A.category='3' AND A.category2='2')";
	$type_name = "면세점";

} else {
	$wsql_prd = "";
}

$sql = "SELECT A.*
		  FROM cf_product A
		 WHERE (A.loan_start_date>='$srch_ymd_f' AND A.loan_start_date<='$srch_ymd_t') AND A.display='Y' AND A.isTest=''  $wsql_prd
	  ORDER BY loan_start_date";

$res = sql_query($sql);
$cnt = $res->num_rows;
$dnum = $cnt;
?>
<div class="tbl_head02 tbl_wrap">

	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<h3><?=$sy?>-<?=$sm?> <?=$type_name?> 리스트</h3>
	</div>

	<div style="margin-bottom:8px;">
	<form method="get" name="f_srch">
		<input type="text" name="srch_ymd_f" value="<?=$srch_ymd_f?>" style="width:85px;text-align:center;">
		~
		<input type="text" name="srch_ymd_t" value="<?=$srch_ymd_t?>" style="width:85px;text-align:center;">

		<select name="gubun" style="height:24px;">
			<option value="A" <?=$gubun=="A"?"selected":""?> >전체</option>
			<option value="B" <?=$gubun=="B"?"selected":""?> >부동산</option>
			<option value="J" <?=$gubun=="J"?"selected":""?> >주택담보</option>
			<option value="D" <?=$gubun=="D"?"selected":""?> >동산</option>
			<option value="M" <?=$gubun=="M"?"selected":""?> >확정매출채권</option>
			<option value="M1" <?=$gubun=="M1"?"selected":""?> >-소상공인</option>
			<option value="M2" <?=$gubun=="M2"?"selected":""?> >-면세점</option>
		</select>

		<input type="button" value="검 색" onclick="go_srch();" style="margin-left:15px;height:30px;width:70px;">
	</form>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">No</th>
			<th scope="col" style="text-align:center;border:1px solid green;">상품명</th>
			<th scope="col" style="text-align:center;border:1px solid green;">대출시작일</th>
			<th scope="col" style="text-align:center;border:1px solid green;">대출종료일</th>
			<th scope="col" style="text-align:center;border:1px solid green;">모집금액</th>
		</tr>
	<?
	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);
		$total_cnt += 1;
		$total_amt += $row['recruit_amount'];

		?>
		<tr>
			<td style="text-align:center; border:1px solid green;"><?=$i+1?></td>
			<td style="text-align:left; border:1px solid green;"><?=$row["title"]?></td>
			<td style="text-align:center; border:1px solid green;"><?=$row["loan_start_date"]?></td>
			<td style="text-align:center; border:1px solid green;"><font color="<?=($row[state]=='2' or $row[state]=='5')?'#A6A6A6':'black';?>"><?=$row["loan_end_date"]?></font></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($row['recruit_amount'])?></td>
		</tr>
		<?
	}
	?>
		<tr>
			<td style="text-align:center; border:1px solid green;" colspan=4>합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($total_amt)?></td>
		</tr>
	</table>
</div>
<?
?>
<script>
function go_srch() {
	var f = document.f_srch;
	f.submit();
}
</script>