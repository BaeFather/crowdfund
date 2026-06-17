<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') {
	alert('최고관리자만 접근 가능합니다.');
}

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$ii = str_pad($sm, 2 , '0' , STR_PAD_LEFT);
?>
<?
$birth_sql = " AND substring(B.mb_birth,1,4) >= '$fr' AND substring(B.mb_birth,1,4) <= '$to'";

$sql = "SELECT A.product_idx, C.title, count(A.idx) inv_cnt , sum(A.amount) inv_amount, C.loan_start_date, C.recruit_amount
		  FROM cf_product_invest A
	 LEFT JOIN g5_member B ON (A.member_idx = B.mb_no)
	 LEFT JOIN cf_product C ON (C.idx = A.product_idx)
	 	 WHERE A.invest_state = 'Y'
		   AND SUBSTRING(C.loan_start_date,1,7)='$sy-$sm'
		   $birth_sql
		   AND C.state IN(1,2,5)
	  GROUP BY A.product_idx
	  ORDER BY C.start_num desc";
$res = sql_query($sql);
$cnt = $res->num_rows;
$dnum = $cnt;
?>
<div class="tbl_head02 tbl_wrap">

	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<h3><?=$sy?>-<?=$sm?> 개인 투자내역</h3>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">No</th>
			<th scope="col" style="text-align:center;border:1px solid green;">상품명</th>
			<th scope="col" style="text-align:center;border:1px solid green;">대출시작일</th>
			<th scope="col" style="text-align:center;border:1px solid green;">모집금액</th>
			<th scope="col" style="text-align:center;border:1px solid green;">투자자수</th>
			<th scope="col" style="text-align:center;border:1px solid green;">투자금액</th>
		</tr>
	<?
	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		$tot_rec += $row['recruit_amount'];
		$tot_cnt += $row['inv_cnt'];
		$tot_amt += $row['inv_amount'];
		?>
		<tr>
			<td style="text-align:center; border:1px solid green;"><?=$dnum--?></td>
			<td style="text-align:left; margin-left:12px; border:1px solid green;"><?=$row['title']?></td>
			<td style="text-align:center; border:1px solid green;"><?=$row['loan_start_date']?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($row['recruit_amount'])?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($row['inv_cnt'])?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($row['inv_amount'])?></td>
		</tr>
		<?
	}
	?>
		<tr>
			<td style="text-align:center; border:1px solid green;" colspan="3">합 계</td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($tot_rec)?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($tot_cnt)?></td>
			<td style="text-align:right; margin-right:12px; border:1px solid green;"><?=number_format($tot_amt)?></td>
		</tr>
	</table>
</div>