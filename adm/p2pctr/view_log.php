<?
include_once('./_common.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
?>
<?
$prd_sql = "SELECT title FROM cf_product WHERE idx='$product_idx'";
$prd_row = sql_fetch($prd_sql);

$sql = "SELECT A.* , B.mb_name, B.mb_co_name
		  FROM p2pctr_request_log A 
		  LEFT JOIN g5_member B ON(A.member_idx=B.mb_no)
		 WHERE A.product_idx='$product_idx' 
		   AND A.rcv_http_code='200' 
		   AND A.apiNo<>'4.3.4'
		 ORDER BY A.idx";
$res = sql_query($sql);
$cnt = $res->num_rows;
$num = 1;
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$product_idx?> - <?=$prd_row["title"]?></h3>

<table class="table table-bordered table-condensed">
	<tr>
		<th style="width:120px;">No</th>
		<th style="text-align:center;">날짜</th>
		<th style="text-align:center;">작 업</th>
		<th style="text-align:center;">회원</th>
		<th style="text-align:center;">이름</th>
		<th style="text-align:center;">금액</th>
	</tr>
<?
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	$data = json_decode($row["reqJson"],true);

	$amount = 0;

	if ($row["apiNo"]=="4.2.2") {  // 대출 신청
		$amount = $data["loan_register_info"]["loan_register_amount"];
	} else if ($row["apiNo"]=="4.3.1") {  // 상품모집 
		$amount = $data["goods_info"]["offering_amount_limit"];
	} else if ($row["apiNo"]=="4.4.2") {  // 투자 신청
		$amount = $data["investment_register_info"]["investment_amount"];
	} else if ($row["apiNo"]=="4.4.4") {  // 투자 계약 
		$amount = $data["investment_contract_info"]["contract_amount"];
	}
	$mb_name = $row["mb_co_name"]?$row["mb_co_name"]:$row["mb_name"];
	?>
	<tr>
		<td style="text-align:center;"><?=$num++?></td>
		<td style="text-align:center;"><?=$row["rdate"]?> <?=$row["rtime"]?></td>
		<td style="text-align:center;"><?=$row["apiTitle"]?></td>
		<td style="text-align:center;"><?=$row["member_idx"]?$row["member_idx"]:""?></td>
		<td style="text-align:center;"><?=$mb_name?></td>
		<td style="text-align:right;"><?=number_format($amount)?></td>
	</tr>
	<?
}
?>
</table>

</div>
