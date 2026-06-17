<?
include_once('_common.php');

$sql = "
	SELECT *
	FROM IB_request_log
	WHERE LEFT(regdate,10)='2020-08-05' AND request_code='3200' AND rcode='00000000' AND mb_id LIKE 'admin_%'
	ORDER BY idx";

$res = sql_query($sql);
$cnt = $res->num_rows;
$num = $cnt;
?>
<table border=1>
<?
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	parse_str($row["request_arr"], $arr);

	$tmp1 = explode("(",$arr["GUAR_MEMO"]);
	$tmp2 = explode(")",$tmp1[1]);
	$prd_idx  = $tmp2[0];

	$sql2 = "SELECT *  FROM cf_product_give WHERE LEFT(banking_date,10)='2020-08-05' AND receive_method='1' AND member_idx='$arr[CUST_ID]' AND product_idx='$prd_idx'";
	$res2 = sql_query($sql2);
	$cnt2 = $res2->num_rows;
	$row2 = sql_fetch_array($res2);
	$hap = $row2["interest"] + $row2["principal"];
	if ($arr["TRAN_AMT"]<>$hap AND $cnt2==1) $err_total += $arr["TRAN_AMT"]-$hap;
	?>
	<tr>
		<td><?=$num--?></td>
		<!--td><?=print_r($arr);?></td-->
		<td>request_log idx <?=$row["idx"]?></td>
		<td><?=$arr["CUST_ID"]?></td>
		<td><?=$prd_idx?></td>
		<td><?=$arr["TRAN_AMT"]?></td>
		<td style="background-color:yellow;"><?=$hap?></td>
		<td style="background-color:yellow;">cf_product_give idx <?=$row2["idx"]?></td>
		<td style="background-color:yellow;">일치 <?=$cnt2?></td>
		<td><?=$arr["TRAN_AMT"]==$hap?"-":"ERROR<br/>$sql2"?></td>
	</tr>
	<?
}

?>

</table>
오류금액 : <?=$err_total?>