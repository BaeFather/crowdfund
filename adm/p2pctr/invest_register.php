<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr.lib.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.4.2";
$apiTitle = "투자신청 기록";
?>
<?
$url  = $p2p_host . "investments/register";
$method = "POST";

$sql = "SELECT idx, goods_id, bank_inquiry_id FROM cf_product where idx='$product_idx'";
$row = sql_fetch($sql);

//$sqli = "SELECT * FROM cf_product_invest_detail WHERE product_idx='$product_idx' AND invest_state<>'N' ORDER BY member_idx";
$sqli = "SELECT * FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y' ORDER BY member_idx";
$resi = sql_query($sqli);
$cnti = $resi->num_rows;
$no = $cnti;
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
<table class="table table-bordered table-condensed">
	<tr>
		<th>No</th>
		<th>회원번호</th>
		<th>이름</th>
		<th>투자자타입</th>
		<th>인증번호</th>
		<th>금액</th>
		<th>투자신청일시</th>
		<th>투자신청ID</th>
		<th>결과</th>
	</tr>
<?
for ($i=0 ; $i<$cnti ; $i++) {

	$intStime = time();

	$rowi = sql_fetch_array($resi);

	$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
	$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

	unset($headers);
	$headers[] = "Content-Type: application/json; charset=UTF-8";
	ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
	ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
	ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

	$strData = array();

	$strData["investment_register_info"] = array();
	$strData["investor_info"] = array();
	$strData["goods_info"] = array();

	$investment_register_id = "";
	$investment_register_id = $rowi["investment_register_id"];
	if (!$investment_register_id AND $mode=="send") $investment_register_id = get_new_id("investment_register_id");

	$strData["investment_register_info"]["investment_register_id"] = $investment_register_id;
	$strData["investment_register_info"]["bank_inquiry_id"] = $rowi['prin_rcv_no'];			// <======= 2022-03-18 수정 배부장
	$strData["investment_register_info"]["investment_amount"] = (int)$rowi["amount"];
	$strData["investment_register_info"]["investment_register_dtm"] = check_int($rowi["insert_date"]).check_int($rowi["insert_time"]);
	$strData["investment_register_info"]["status"] = "T100"; // T100 투자신청중
	$strData["investment_register_info"]["investments_document_info"]["document_confirm_date"] = check_int($rowi["insert_date"]);
	$strData["investment_register_info"]["investments_document_info"]["document_type"] = "DP99"; // DP01  (전자문서 형식의 파일)  DP99  (전자문서 이외의 파일)
	$inv_info = get_inv_info($rowi["member_idx"]);
	$strData["investor_info"]["identity_no"] = $inv_info["inv_idno"];
	$strData["investor_info"]["name"] = $inv_info["inv_name"];
	$strData["investor_info"]["type"] = $inv_info["inv_type"];
	if (substr($strData["investor_info"]["type"] , 0 , 2) == "I3") $strData["investor_info"]["business_register_no"] = $inv_info["business_register_no"];
	$strData["goods_info"]["goods_id"] = $row["goods_id"];
	$strData["goods_info"]["goods_type"] = get_goods_type($product_idx);

	$typeText="";
	if ($strData["investor_info"]["type"]=="I110") $typeText = "일반개인투자자";
	else if ($strData["investor_info"]["type"]=="I120") $typeText = "소득적격투자자";
	else if ($strData["investor_info"]["type"]=="I130") $typeText = "개인전문투자자";
	else if ($strData["investor_info"]["type"]=="I310") $typeText = "법인투자자";
	else if ($strData["investor_info"]["type"]=="I320") $typeText = "여신금융기관";
	else if ($strData["investor_info"]["type"]=="I330") $typeText = "P2P온투업자";

	//echo "<pre>"; print_r($strData); echo "</pre>";

	if ($mode=="send" AND !$rowi["investment_register_id"]) {

		$res = curl_p2pctr($url , $method , $strData , $headers);


		$intEtime = time();
		$thrSec = $intStime - $intEtime;
		fn_log($apiNo, $apiTitle, $mb_no, $url, $res["req_body"] , $res["body"], $res["http_code"], $thrSec);

		$resj = json_decode($res["body"] , true);
		if ($resj["rsp_code"] == "A0000") {
			//$up_sql = "UPDATE cf_product_invest_detail SET investment_register_id = '$investment_register_id' WHERE idx = '$rowi[idx]' AND investment_register_id='' ";
			$up_sql = "UPDATE cf_product_invest SET investment_register_id = '$investment_register_id' WHERE idx = '$rowi[idx]' AND investment_register_id='' ";
			sql_query($up_sql);
		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
		}

	}

	?>
	<tr>
		<td style="text-align:center;"><?=$no--?></td>
		<td style="text-align:center;"><?=$rowi["member_idx"]?></td>
		<td style="text-align:center;"><?=$strData["investor_info"]["name"]?></td>
		<td style="text-align:center;"><?=$typeText?></td>
		<td style="text-align:center;"><?=substr($strData["investor_info"]["identity_no"],0,6)?>-<?=str_repeat("*",strlen(substr($strData["investor_info"]["identity_no"],6)))?></td>
		<td style="text-align:right;"><?=number_format($strData["investment_register_info"]["investment_amount"])?></td>
		<td style="text-align:center;"><?=$strData["investment_register_info"]["investment_register_dtm"]?></td>
		<td style="text-align:center;"><?=$strData["investment_register_info"]["investment_register_id"]?></td>
		<td style="text-align:center;"><?=$resj["rsp_code"]?><?=$resj["rsp_code"]<>"A0000"?"<br/>$resj[rsp_message]":""?></td>
	</tr>
	<?
}
?>

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