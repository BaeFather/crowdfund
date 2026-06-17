<?
include_once('./_common.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<table border=1>
<?
$sql = "SELECT A.idx, A.loan_register_id, A.title FROM cf_product A WHERE A.loan_register_id<>'' ORDER BY A.idx";
$res = sql_query($sql);
$cnt = $res->num_rows;

$no = $cnt;
for ( $i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	$p2p_sql = "SELECT * FROM p2pctr_product WHERE product_idx='$row[idx]'";
	$p2p_row = sql_fetch($p2p_sql);
	if (is_array($p2p_row)) {
		//$aa="update";
		$aa="";
		$psql="";
	} else {
		$aa="INSERT";
		/*
		$psql = "INSERT INTO p2pctr_product
						 SET product_idx=
							 loan_register_id=
							 loan_register_status=
							 loan_register_datetime=
							 goods_id=
							 goods_status=
							 goods_update_datetime=
							 loan_contract_id=
							 loan_status=
							 loan_contract_datetime=
							 p2pctr_end=";
		*/
		$psql = "INSERT INTO p2pctr_product
						 SET product_idx='$row[idx]',
							 loan_register_id='$row[loan_register_id]'
				";
		//sql_query($psql);
	}
	?>
	<tr>
		<td><?=$no--?></td>
		<td><?=$row["idx"]?></td>
		<td><?=$row["title"]?></td>
		<td><?=$p2p_row["idx"]?></td>
		<td><?=$psql?></td>
	</tr>
	<?
}
?>
</table>