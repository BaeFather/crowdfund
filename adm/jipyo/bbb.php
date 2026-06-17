<?php
$sub_menu = "930010";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = '수익율 통계';

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>

<?
?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<select>
			<option>수익율 통계</option>
		</select>
		<select>
			<?
			for ($i=date("Y") ; $i>="2016"; $i--) {
				?>
				<option value="<?=$i?>"><?=$i?></option>
				<?
			}
			?>
		</select>
	</div>
	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=5>헬로펀딩 수익율(대출자, 투자자 수수료 수익)</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=5>투자자 수익율</th>
		</tr>
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>
			<th scope="col" style="text-align:center;border:1px solid green;">부동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;">주택담보</th>
			<th scope="col" style="text-align:center;border:1px solid green;">동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;">확정매출채권</th>
			
			<th scope="col" style="text-align:center;border:1px solid green;">평균</th>
			<th scope="col" style="text-align:center;border:1px solid green;">부동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;">주택담보</th>
			<th scope="col" style="text-align:center;border:1px solid green;">동산</th>
			<th scope="col" style="text-align:center;border:1px solid green;">확정매출채권</th>
		</tr>
<?
$srch_y = $_REQUEST['srch_y'];
if (!$srch_y) $srch_y = date("Y");

for ($i=12 ; $i>=1 ; $i--) {
	
	$srch_m = str_pad($i , 2 , "0", STR_PAD_LEFT);
	$srch_ym = $srch_y . "-". $srch_m;
	
	if ($srch_ym>date("Y-m")) continue;
	
	$LIST[$i]["ym"] = $srch_ym;
	
	$sql = "select substring(loan_start_date,1,7) ym,
				   category, mortgage_guarantees,
				   sum(loan_usefee) sum_loanfee,
				   sum(invest_usefee) sum_usefee, 
				   count(*) sum_usecnt 
			from cf_product 
			where substring(loan_start_date,1,7)='$srch_ym' 
			group by category, mortgage_guarantees
			order by loan_start_date desc";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);
	
	for ($j=0 ; $j<$cnt ; $j++) {
	
		$row = sql_fetch_array($res);
	
		if ($row['sum_loanfee']) $loan_fee_avg = floor(($row['sum_loanfee'] / $row['sum_usecnt'])*100)/100;
		else $loan_fee_avg = "0.00";
	
		if ($row['category']=="1") {               // 동산
			$LIST[$i]['dong'] = $loan_fee_avg;
		} else if ($row['category']=="2") {     // 부동산
			if ($row['mortgage_guarantees']=="1") { //주택담보
				$LIST[$i]['ju'] = $loan_fee_avg;
			} else {
				$LIST[$i]['bu'] = $loan_fee_avg;
			}
		} else if ($row['category']=="3") {     // 매출채권
			$LIST[$i]['mae'] = $loan_fee_avg;
		}
		
		$tot_loan_fee += $row['sum_loanfee'];
		$tot_loan_cnt += $row['sum_usecnt'];
		
	}
	
	if ($tot_loan_cnt) $LIST[$i]['all'] = floor(($tot_loan_fee / $tot_loan_cnt)*100)/100;
	
	?>
		<tr>
			<td style="text-align:center;border:1px solid green;"><?=$srch_ym?></td>
			<td style="text-align:center;border:1px solid green;"><a onclick="bbb_detail('<?=$LIST[$i]["ym"]?>',0);" style="cursor:pointer;"><?=number_format($LIST[$i]['all'], 2, '.', ',')?></a></td>
			<td style="text-align:center;border:1px solid green;"><a onclick="bbb_detail('<?=$LIST[$i]["ym"]?>',2);" style="cursor:pointer;"><?=number_format($LIST[$i]['bu'], 2, '.', ',')?></a></td>
			<td style="text-align:center;border:1px solid green;"><a onclick="bbb_detail('<?=$LIST[$i]["ym"]?>',21);" style="cursor:pointer;"><?=number_format($LIST[$i]['ju'], 2, '.', ',')?></a></td>
			<td style="text-align:center;border:1px solid green;"><a onclick="bbb_detail('<?=$LIST[$i]["ym"]?>',1);" style="cursor:pointer;"><?=number_format($LIST[$i]['dong'], 2, '.', ',')?></a></td>
			<td style="text-align:center;border:1px solid green;"><a onclick="bbb_detail('<?=$LIST[$i]["ym"]?>',3);" style="cursor:pointer;"><?=number_format($LIST[$i]['mae'], 2, '.', ',')?></a></td>
		</tr>
	<?
}
?>
	</table>
</div>
<script>
function bbb_detail(ym, cate) {
	window.open("/adm/jipyo/bbb_detail.php?ym="+ym+"&category="+cate, "_blank", "width=800,height=700,resizable=yes,scrollbars=yes");
}
</script>
<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>