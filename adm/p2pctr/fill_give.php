<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

$product_idx="5168";

if (!$product_idx) die("상품번호 또는 회차 오류");
?>

<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">

	<table class="table table-bordered table-condensed" stylee="width:1200px;">

<?
$sql = "SELECT A.*, B.contract_id FROM cf_product_give A
		  LEFT JOIN cf_product_invest B ON(B.idx=A.invest_idx)
		 WHERE A.product_idx='$product_idx' 
		 ORDER BY A.member_idx, A.turn, A.turn_sno";
/*
$sql = "SELECT A.*, B.contract_id FROM cf_product_give A
		  LEFT JOIN cf_product_invest B ON(B.idx=A.invest_idx)
		 WHERE A.product_idx='$product_idx' 
		   AND (A.p2pCtr_contract_id='' OR A.p2pCtr_date='')
		 ORDER BY A.member_idx, A.turn, A.turn_sno";
*/
$res = sql_query($sql);
$cnt = $res->num_rows;

$no = $cnt;

for ($i=0 ; $i<$cnt ; $i++) {

	$row = sql_fetch_array($res);

	$up_sql1 = "";
	if (!$row["p2pCtr_contract_id"]) {
		$up_sql1 = "UPDATE cf_product_give SET p2pCtr_contract_id='".$row["contract_id"]."' WHERE product_idx='$product_idx' AND idx='".$row["idx"]."' AND p2pCtr_contract_id=''";
		//sql_query($up_sql1);
	}

	$up_sql2 = "";
	if (!$row["p2pCtr_date"]) {
		$up_sql2 = "UPDATE cf_product_give SET p2pCtr_date='20211005' WHERE product_idx='$product_idx' AND idx='".$row["idx"]."' AND p2pCtr_date=''";
		//sql_query($up_sql2);
	}

	?>

		<tr>
			<td style="text-align:center;"><?=$no--?></td>
			<td style="text-align:center;"><?=$row["idx"]?></td>
			<td style="text-align:center;"><?=$row["member_idx"]?></td>
			<td style="text-align:center;"><?=$row["contract_id"]?></td>
			<td style="text-align:center;"><?=$row["turn"]?></td>
			<td style="text-align:center;"><?=$row["turn_sno"]?></td>
			<td style="text-align:center;"><?=$row["is_overdue"]?></td>
			<td style="text-align:center;"><?=$row["p2pCtr_contract_id"]?></td>
			<td style="text-align:center;"><?=$row["p2pCtr_date"]?></td>
			<td><?=$up_sql1?><br/><?=$up_sql2?></td>
		</tr>

	<?
}
?>
</table>