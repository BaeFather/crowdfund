<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.4.4";
$apiTitle = "투자계약 기록";
?>
<?
if ($imsi=="Y") $p2p_host=$p2p_host."data/";

$url  = $p2p_host . "investments/contract";
//if ($product_idx=="3107") $url  = $p2p_host . "data/investments/contract";  // 이관용
$method = "POST";

$sql = "SELECT idx, loan_start_date, loan_end_date, goods_id  FROM cf_product where idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);


$sqli = "SELECT * FROM cf_product_invest WHERE product_idx='$product_idx' AND invest_state='Y' order by member_idx";
$resi = sql_query($sqli);
$cnti = sql_num_rows($resi);
$no = $cnti;
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?><?=strpos($url,'data')!==false?" (이관용)":""?></h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>No</th>
		<th>계약번호</th>
		<th>회원번호</th>
		<th>투자금액</th>
		<th>투자기간</th>
		<th>투자계약일</th>
		<th>상환</th>
		<th>결과</th>
	</tr>
<?


$repay_info = repayCalculationNew($product_idx, '' , 'Y');

for ($i=0 ; $i<$cnti ; $i++) {
	$intStime = time();

	$rowi = sql_fetch_array($resi);


	$contract_id = $rowi["contract_id"];
	if (!$contract_id AND $mode=="send") $contract_id = get_new_id("contract_id");

	$data = array();

	$data["investment_register_id"] = $rowi["investment_register_id"];
	$data["goods_id"] = $row["goods_id"];
	$data["investment_contract_info"] = array();
	$data["investor_info"] = array();
	$data["pni_securities_scheduled_list"] = array();

	$data["investment_contract_info"]["contract_id"] = $contract_id;
	$data["investment_contract_info"]["contract_amount"] = (int)$rowi["amount"];
	$data["investment_contract_info"]["contract_start_date"] = check_int($row["loan_start_date"]);
	$data["investment_contract_info"]["contract_end_date"] = check_int($row["loan_end_date"]);
	$data["investment_contract_info"]["contract_dtm"] = check_int($row["loan_start_date"])."170000";
	$data["investment_contract_info"]["status"] = "S100"; // S100 계약 - 상환중

	$inv_info = get_inv_info($rowi["member_idx"]);
	$data["investor_info"]["identity_no"] = $inv_info["inv_idno"];

	for ($j=0 ; $j<count($repay_info["REPAY"]) ; $j++) {
		$key = array_search($rowi["member_idx"] , array_column($repay_info["REPAY"][$j]["LIST"], 'member_idx'));
		$data["pni_securities_scheduled_list"][$j]["securities_n_count"] = (int)$repay_info["REPAY"][$j]["repay_num"];

		$data["pni_securities_scheduled_list"][$j]["schd_date"] = check_int($repay_info["REPAY"][$j]["repay_date"]);
		//$data["pni_securities_scheduled_list"][$j]["schd_p_amount"] = (int)($repay_info["REPAY"][$j]["LIST"][$key]["partial_principal"]+$repay_info["REPAY"][$j]["LIST"][$key]["repay_principal"]);		

		$par_amt=0;

		if (is_array($repay_info["REPAY"][$j]["PARTIAL"])) {
			$par_amt = $repay_info["REPAY"][$j+1]["LIST"][$key]["partial_principal"];
		}
		$data["pni_securities_scheduled_list"][$j]["schd_p_amount"] = (int)($par_amt+$repay_info["REPAY"][$j]["LIST"][$key]["repay_principal"]);

		$data["pni_securities_scheduled_list"][$j]["schd_interest"] = (int)$repay_info["REPAY"][$j]["LIST"][$key]["invest_interest"];
	}

	if ($mode=="send" AND !$rowi["contract_id"]) {

		//$res = curl_p2pctr($url , $method , $data , $headers);
		$res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $rowi["member_idx"]);

		$resj = json_decode($res["body"] , true);

		if ($resj["rsp_code"] == "A0000" OR substr($resj["rsp_code"],0,4)=="A000") {
			$up_sql = "UPDATE cf_product_invest SET contract_id = '$contract_id' WHERE idx = '$rowi[idx]' AND contract_id='' ";
			sql_query($up_sql);

		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($res); echo "</pre><br/><br/>";
		}

	}

	?>
	<tr>
		<td style="text-align:center;vertical-align:middle;"><?=$no--?></td>
		<td style="text-align:center;vertical-align:middle;">

			<? if ($data["investment_contract_info"]["contract_id"]) { ?>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_cont_srch('<?=$data[investment_contract_info][contract_id]?>');" value='<?=$data["investment_contract_info"]["contract_id"]?>'/>
			<? } ?>
		</td>
		<td style="text-align:center;vertical-align:middle;"><?=$rowi["member_idx"]?></td>
		<td style="text-align:right;vertical-align:middle;"><?=number_format($data["investment_contract_info"]["contract_amount"])?></td>
		<td style="text-align:center;vertical-align:middle;"><?=$data["investment_contract_info"]["contract_start_date"]?> ~ <?=$data["investment_contract_info"]["contract_end_date"]?></td>
		<td style="text-align:center;vertical-align:middle;"><?=$data["investment_contract_info"]["contract_dtm"]?></td>
		<td style="text-align:center;vertical-align:middle;">
			<table>
	<?

	for ($j=0 ; $j<count($data["pni_securities_scheduled_list"]) ; $j++) {
		?>
			<tr>
				<td><?=$data["pni_securities_scheduled_list"][$j]["securities_n_count"]?></td>
				<td><?=$data["pni_securities_scheduled_list"][$j]["schd_date"]?></td>
				<td><?=$data["pni_securities_scheduled_list"][$j]["schd_interest"]?></td>
				<td><?=number_format($data["pni_securities_scheduled_list"][$j]["schd_p_amount"])?>
				</td>
			</tr>
		<?
	}

	?>
			</table>
		</td>
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

function go_cont_srch(cont_id) {
	window.open("/adm/p2pctr/investments_contract_id.php?cont_id="+cont_id,"","width=800, height=800");
}
</script>