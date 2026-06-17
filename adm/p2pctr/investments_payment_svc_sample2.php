<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx) die("상품번호 또는 회차 오류");
?>
<?
$apiNo = "4.4.7";
$apiTitle = "원리금지급 기록";
?>
<?
$url  = $p2p_host . "investments/payment";
$method = "POST";

$sql = "SELECT goods_id  FROM cf_product where idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

$inv_sql = "SELECT * FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y' ORDER BY member_idx"; // LIMIT 100, 100";
$inv_res = sql_query($inv_sql);
$inv_cnt = $inv_res->num_rows;

$data = array(); 
$data1 = array();
$data["pni_payment_common_info"] = array();
$data["pni_payment_list"] = array();

$data["pni_payment_common_info"]["goods_id"] = $row["goods_id"];
//$data["pni_payment_common_info"]["securities_n_count"] = (int)$grow["turn"];
//$data["pni_payment_common_info"]["pay_date"] = check_int($grow["DATE"]);
$data["pni_payment_common_info"]["securities_n_count"] = 8;
$data["pni_payment_common_info"]["pay_date"] = check_int("20210915");

for ($i=0 ; $i<$inv_cnt ; $i++) {

	$inv_row = sql_fetch_array($inv_res);


	$g_sql = "SELECT SUM(interest) sum_int, SUM(principal) sum_prin, min(turn) min_turn FROM cf_product_give 
			   WHERE product_idx='$product_idx' AND member_idx='".$inv_row["member_idx"]."' AND p2pCtr_date=''
			     AND turn<='".$data["pni_payment_common_info"]["securities_n_count"]."'";
	$g_sql .= " AND !(turn=6 and is_overdue='Y') AND !(turn=7 and is_overdue='Y') ";
	$g_res = sql_query($g_sql);
	$g_row = sql_fetch_array($g_res);

	$rdate = "20210915";

	$data1["pni_payment_list"][$i]["member_idx"] = $inv_row["member_idx"];
	$data1["pni_payment_list"][$i]["min_turn"] = $g_row["min_turn"];
	//$data1["give_idx"][$j] = $rowd["idx"];
	//$data1["contract_id"][$j] = $rowd["contract_id"];

	$data["pni_payment_list"][$i]["investment_contract_id"] = $inv_row["contract_id"];
	$data["pni_payment_list"][$i]["pay_p_amount"] = (int)$g_row["sum_prin"];
	$data["pni_payment_list"][$i]["pay_interest"] = (int)$g_row["sum_int"];
	$data["pni_payment_list"][$i]["actual_pay_amount"] = (int)($g_row["sum_prin"]+$g_row["sum_int"]);


}

unset($resj);
if ($mode=="send" and count($data["pni_payment_list"])) {

		//$res = curl_p2pctr2($apiNo, $apiTitle , $url , $method , $data,  $product_idx);
		
		$resj = json_decode($res["body"] , true);

		if ($resj["rsp_code"] == "A0000") {
			/*
			for ($k=0 ; $k<count($data1["give_idx"]) ; $k++) {
				$up_sql = "UPDATE cf_product_give SET p2pCtr_contract_id='".$data["pni_payment_list"][$k]["investment_contract_id"]."', p2pCtr_date='".$rdate."' WHERE product_idx='$product_idx' AND member_idx='".$data1["pni_payment_list"][$i]["member_idx"]."'";
				//sql_query($up_sql);
			}
			*/

			echo "기록 성공<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";		
		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
		}

}

?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
<br/>
회차 <?=$data["pni_payment_common_info"]["securities_n_count"]?> / 지급일 <?=$data["pni_payment_common_info"]["pay_date"]?> / 상품ID <?=$data["pni_payment_common_info"]["goods_id"]?>
<br/><br/>
<table class="table table-bordered table-condensed" style="width:700px;">
	<tr>
		<th></th>
		<th>투자ID</th>
		<th>회원번호</th>
		<th>회차정보</th>
		<th>원금</th>
		<th>이자</th>
		<th>지급총액</th>
	</tr>

<?
for ($i=0 ; $i<count($data["pni_payment_list"]) ; $i++) {

	?>
	<tr>
		<td style="text-align:center;"><?=$i+1?></td>
		<td style="text-align:center;"><?=$data["pni_payment_list"][$i]["investment_contract_id"]?></td>
		<td style="text-align:center;"><?=$data1["pni_payment_list"][$i]["member_idx"]?></td>
		<td style="text-align:center;"><?=$data1["pni_payment_list"][$i]["min_turn"]?> ~</td>
		<td style="text-align:right;"><?=number_format($data["pni_payment_list"][$i]["pay_p_amount"])?></td>
		<td style="text-align:right;"><?=number_format($data["pni_payment_list"][$i]["pay_interest"])?></td>
		<td style="text-align:right;"><?=number_format($data["pni_payment_list"][$i]["actual_pay_amount"])?></td>
	</tr>
	<?
}

?>
</table>

<table >
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
return;
	var f = document.ff;
	f.mode.value="send";
	f.submit();
}
</script>