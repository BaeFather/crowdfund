<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx) die("상품번호 또는 회차 오류");
?>
<?
$apiNo = "4.2.8";
$apiTitle = "대출상환 예정정보 갱신";
?>
<?
$prd_sql = "SELECT loan_contract_id
			  FROM cf_product 
			 WHERE idx='$product_idx'";
$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$loan_contract_id = $prd_row["loan_contract_id"];

$url  = $p2p_host . "loans/repayment/".$loan_contract_id;
$method = "PUT";



$data = array(); 
$data["drop_loan_schd_repayment_info"] = array();
$data["replace_loan_schd_repayment_list"] = array();

$data["drop_loan_schd_repayment_info"]["drop_start_repayment_n_count"] = (int)$mturn_s;
$data["drop_loan_schd_repayment_info"]["drop_end_repayment_n_count"]   = (int)$mturn_e;

$cnt = 0;
if ($mturn_s and $mturn_e) {
	$sql = "SELECT turn,
				   SUBSTRING(banking_date,1,10) bdate, 
				   SUM(principal) sum_prin, 
				   SUM(interest) sum_int,
				   SUM(interest_tax) sum_taxi, 
				   SUM(local_tax) sum_taxl,
				   SUM(fee) sum_fee
			  FROM cf_product_give 
			 WHERE product_idx='$product_idx' AND turn>='$mturn_s' 
			 GROUP BY turn";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);
}

//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
//$repay_info = repayCalculationNew($product_idx, '');
//echo "<pre>"; echo json_encode($repay_info["REPAY"],JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE) ; echo "</pre>";

for ($i=0 ; $i<$cnt ; $i++) {

	$row = sql_fetch_array($res);
	
	$data["replace_loan_schd_repayment_list"][$i]["repayment_n_count"] = (int)$row["turn"];
	$data["replace_loan_schd_repayment_list"][$i]["schd_date"] = preg_replace('/[^0-9]/','', $row["bdate"]);
	$data["replace_loan_schd_repayment_list"][$i]["schd_p_amount"] = (int)$row["sum_prin"];
	$data["replace_loan_schd_repayment_list"][$i]["schd_interest"] = (int)$row["sum_int"]+$row["sum_taxi"]+$row["sum_taxl"]+$row["sum_fee"];

}

if ($mturn_s and $mturn_e) {echo "<pre>"; print_r($data); echo "</pre>";}

if ($mode=="send" and $mturn_s and $mturn_e) {

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data);

}

?>


<table style="width:100%;border:0;">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
			수정회차 <input type=text name="mturn_s" value="<?=$mturn_s?>" style="width:30px;"/> 
			~
			<input type=text name="mturn_e" value="<?=$mturn_e?>" style="width:30px;"/>&nbsp;&nbsp;
			<input type="button" class="btn btn-sm btn-warning" onclick="go_srch();" value="검색"/>
			<br/><br/>
			
			<input type="button" class="btn btn-sm btn-warning" onclick="go_send();" value="전송"/>
			</form>
		</td>
	</tr>
</table>

<script>
function go_srch() {
	var f = document.ff;
	f.submit();
}
function go_send() {

	var f = document.ff;
	if (!<?=$cnt?>) {
		alert("회차 검색을 먼저 해주세요.");
		return;
	}

	var yn = confirm("이대로 전송하시겠습니까?");
	if (!yn) return;

	
	f.mode.value="send";
	f.submit();
}
</script>

<?
echo "<pre>"; print_r($curl_res); echo "</pre>";
?>