<?
include_once('./_common.php');
auth_check($auth[$sub_menu], "w");

$g5['title'] = '중앙기록관리 - 이관';

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$sql = "SELECT idx, recruit_amount, loan_register_id, goods_id, loan_contract_id FROM cf_product WHERE idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

//$invr_sql = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest_detail WHERE product_idx='$product_idx' AND investment_register_id<>'' AND invest_state<>'N' ";
$invr_sql = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest WHERE product_idx='$product_idx' AND investment_register_id<>'' AND invest_state='Y' ";
$invr_res = sql_query($invr_sql);
$invr_row = sql_fetch_array($invr_res);

//$invr_sql2 = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest_detail WHERE product_idx='$product_idx' AND contract_id<>'' AND invest_state<>'N' ";
$invr_sql2 = "SELECT COUNT(idx) invr_cnt, SUM(amount) invr_amount FROM cf_product_invest WHERE product_idx='$product_idx' AND contract_id<>'' AND invest_state='Y' ";
$invr_res2 = sql_query($invr_sql2);
$invr_row2 = sql_fetch_array($invr_res2);
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
<table class="table table-bordered table-condensed">
	<tr>
		<th style="width:120px;">대출신청</th>
		<td style="text-align:center;">
		<?
		if ($row["loan_register_id"]) {
			?>	
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_regist();" value="<?=$row[loan_register_id]?>"/>
			<?
		} else {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_regist();" value="기록"/>
			<?
		}
		?>
		</td>
	</tr>
	<tr>
		<th>상품모집</th>
		<td style="text-align:center;">
		<?
		if ($row["goods_id"]) {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_goods_regist();" value="<?=$row["goods_id"]?>"/>	
			<?
		} else {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_goods_regist();" value="기록"/>
			<?
		}
		?>
		</td>
	</tr>
	<tr>
		<th>상품설명서</th>
		<td style="text-align:center;">
		</td>
	</tr>
	<tr>
		<th>투자신청</th>
		<td style="text-align:center;">
			<? $txt1 = substr($invr_row["invr_amount"],0,-4)." / ". substr($row["recruit_amount"],0,-4); ?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_invest_regist();" value="<?=$txt1?>"/>
		</td>
	</tr>
	<tr>
		<th>상품모집 갱신</th>
		<td style="text-align:center;">
			<!--input type="button" class="btn btn-sm btn-default" onclick="go_goods_mod();" value="모집완료"/-->
		</td>
	</tr>
	<tr>
		<th>대출계약</th>
		<td style="text-align:center;">
		<?
		if ($row["loan_contract_id"]) {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_contract();" value="<?=$row["loan_contract_id"]?>"/>	
			<?
		} else {
			?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_contract();" value="기록"/>
			<?
		}
		?>
		</td>
	</tr>
	<tr>
		<th>투자계약</th>
		<td style="text-align:center;">
			<? $txt2 = substr($invr_row2["invr_amount"],0,-4)." / ". substr($row["recruit_amount"],0,-4); ?>
			<input type="button" class="btn btn-sm btn-default" onclick="go_invest_contract();" value="<?=$txt2?>" />
			<?= $invr_row2["invr_cnt"];?> / <?=$invr_row["invr_cnt"];?>
		</td>
	</tr>
	<tr>
		<th>대출상환 기록</th>
		<td style="text-align:center;">
			<input type="button" class="btn btn-sm btn-default" onclick="go_loan_repayment();" value="기록" />
		</td>
	</tr>
	<tr>
		<th>원리금지급</th>
		<td style="text-align:center;">
			<input type="button" class="btn btn-sm btn-default" onclick="go_invest_pay();" value="기록" /><br/>
			<?
			$wsql = "SELECT product_idx, turn, COUNT(turn) tcnt FROM cf_product_give WHERE product_idx='$product_idx' AND p2pCtr_contract_id<>'' GROUP BY product_idx,turn ORDER BY turn ";
			$wres = sql_query($wsql);
			$wcnt = sql_num_rows($wres);
			for ($k=0 ; $k<$wcnt ; $k++) {
				$wrow = sql_fetch_array($wres);
				?>
				<?=$wrow["turn"]?>회차 <?=$wrow["tcnt"]?>명<br/>
				<?
			}
			?>
		</td>
	</tr>
</table>

</div>

<script>
// 대출신청 기록
function go_loan_regist() {  
	window.open("/adm/p2pctr/loan_register.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 상품모집 기록
function go_goods_regist() {
	window.open("/adm/p2pctr/goods_register.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 투자신청 기록
function go_invest_regist() {
	window.open("/adm/p2pctr/invest_register.php?product_idx=<?=$product_idx?>","","width=1300, height=800");
}

// 상품모집 갱신
function go_goods_mod() {
	window.open("/adm/p2pctr/goods_mod.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 대출계약 기록
function go_loan_contract() {
	window.open("/adm/p2pctr/loan_contract.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 투자계약 기록
function go_invest_contract() {
	window.open("/adm/p2pctr/invest_contract.php?product_idx=<?=$product_idx?>","","width=1300, height=800");
}

// 대출상환 기록
function go_loan_repayment() {
	window.open("/adm/p2pctr/loan_repayment.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}

// 원리금지급 기록
function go_invest_pay() {
	window.open("/adm/p2pctr/investments_payment.php?product_idx=<?=$product_idx?>","","width=800, height=800");
}
</script>

<?

?>