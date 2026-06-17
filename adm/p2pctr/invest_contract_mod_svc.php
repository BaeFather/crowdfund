<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx) die("상품번호 또는 회차 오류");
?>
<?
$apiNo = "4.4.5";
$apiTitle = "투자계약 갱신";
?>
<?
$sql = "SELECT A.idx, A.member_idx, A.contract_id, A.amount, A.contract_status,
			   B.mb_name
		  FROM cf_product_invest A 
		  LEFT JOIN g5_member B ON(A.member_idx=B.mb_no)
		 WHERE A.product_idx='$product_idx' 
		   AND A.invest_state='Y' 
		 ORDER BY A.idx DESC";
$res = sql_query($sql);
$cnt = $res->num_rows;
$num = $cnt;
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$product_idx?> <?=$prd_row["title"]?></h3>
	<h3><?=$apiNo?> <?=$apiTitle?></h3>


<table class="table table-bordered table-condensed">
	<tr>
		<th>No</th>
		<th>번호</th>
		<th>이름</th>
		<th>금액</th>
		<th>투자 ID</th>
		<th>상태</th>
		<th>전송결과</th>
	</tr>
<?
for ($i=0 ; $i<$cnt; $i++) {
	
	$row = sql_fetch_array($res);
	$loan_contract_id = $row["contract_id"];
	$amount_total += $row["amount"];

	$url  = $p2p_host . "investments/contract/".$loan_contract_id;
	$method = "PUT";

	$data = array();
	$data["status"] = "S301";

	$resj = array();

	if ($mode=="send" AND $loan_contract_id AND $row["contract_status"]<>"S301") {

		$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $row["member_idx"]);
		$resj = json_decode($curl_res["body"] , true);

		if ($resj["rsp_code"] == "A0000") {

			$sts_up_sql = "UPDATE cf_product_invest SET contract_status='S301' WHERE idx='".$row["idx"]."'";
			sql_query($sts_up_sql);

			p2pctr_end_check($product_idx);

		} else {
			$fail_cnt++;
		}

	}

	if ($row["contract_status"]=="S301") $status_txt ="상환완료";
	else $status_txt = "";

	?>
	<tr>
		<td style="text-align:center; "><?=$num--?></td>
		<td style="text-align:center; "><?=$row["member_idx"]?></td>
		<td style="text-align:center; "><?=$row["mb_name"]?></td>
		<td style="text-align:right; "><?=number_format($row["amount"])?></td>
		<td style="text-align:center; "><?=$row["contract_id"]?></td>
		<td style="text-align:center; "><?=$status_txt?></td>
		<td style="text-align:left; ">
			<?=$resj["rsp_code"]?><?=$resj["rsp_code"]<>"A0000"?"<br/>".$resj["rsp_message"]:""?>
		</td>
	</tr>
	<?
}
?>
	<tr>
		<th style="text-align:center; " colspan=3>합계</td>
		<th style="text-align:right; "><?=number_format($amount_total)?></th>
		<th style="text-align:center; " colspan=3><?=$fail_cnt?$fail_cnt." 건 실패":""?></td>
</table>

<table style="width:100%;border:0;">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_send();" value="전송"/>
			</form>
		</td>
	</tr>
</table>

<script>
function go_send() {
	var yn = confirm("이대로 전송하시겠습니까?");
	if (!yn) return;

	var f = document.ff;
	f.mode.value="send";
	f.submit();
}
</script>
<?
function p2pctr_end_check($product_idx) {
	$all_sql = "SELECT COUNT(idx) all_cnt FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y'";
	$all_row = sql_fetch($all_sql);
	$all_cnt = $all_row["all_cnt"];

	$end_sql = "SELECT COUNT(idx) end_cnt FROM cf_product_invest 
				 WHERE product_idx='$product_idx' AND invest_state='Y'
			       AND contract_status IN ('S301','S302', 'S303', 'S304', 'S311', 'S312')";
	$end_row = sql_fetch($end_sql);
	$end_cnt = $end_row["end_cnt"];

	if ($end_cnt==$all_cnt) {

		$main_sql = "SELECT * FROM p2pctr_product WHERE product_idx='$product_idx'";
		$main_res = sql_query($main_sql);
		$main_cnt = $main_res->num_rows;

		if ($main_cnt) {
			$main_row = sql_fetch_array($main_res);
			$upd_sql = "UPDATE p2pctr_product SET p2pctr_end='Y' WHERE product_idx='$product_idx'";
			sql_query($upd_sql);
		} else {
			$main_ins_sql = "INSERT INTO p2pctr_product 
									 SET product_idx='$product_idx', 
										 p2pctr_end='Y'";
			sql_query($main_ins_sql);
		}


	}
}
?>