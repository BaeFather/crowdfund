<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
?>
<?
$apiNo = "4.2.4";
$apiTitle = "대출계약 기록";

$url  = $p2p_host . "loans/contract";
//if ($product_idx=="3107") $url  = $p2p_host . "data/loans/contract";  // 이관용
$method = "POST";
?>
<?
$prd_sql = "SELECT recruit_amount, start_date, invest_period, invest_days, loan_start_date, loan_end_date,
				   loan_mb_no,
				   loan_register_id, goods_id, loan_contract_id
			  FROM cf_product 
			 WHERE idx='$product_idx'";

$prd_res = sql_query($prd_sql);
$prd_row = sql_fetch_array($prd_res);

$loan_contract_id = $prd_row["loan_contract_id"];
if (!$loan_contract_id AND $mode=="send") $loan_contract_id = get_new_id("loan_contract_id");

$loan_term_days = get_term_days($prd_row["start_date"], $prd_row["invest_period"], $prd_row["invest_days"]);

$data = array();

$data["loan_register_id"] = $prd_row["loan_register_id"];
$data["loan_contract_info"] = array();
$data["borrower_info"] = array();
$data["loan_schd_repayment_list"] = array();

$data["loan_contract_info"]["loan_contract_id"] = $loan_contract_id;
$data["loan_contract_info"]["goods_id_list"] = array();
$data["loan_contract_info"]["goods_id_list"][0]["goods_id"] = $prd_row["goods_id"];
$data["loan_contract_info"]["loan_contract_amount"] = intval($prd_row["recruit_amount"]);   // 대출 신청금액
$data["loan_contract_info"]["status"] = "S100"; // 계약 - 상환중
$data["loan_contract_info"]["loan_term_days"] = (int)$loan_term_days;
$data["loan_contract_info"]["contract_start_date"] = preg_replace('/[^0-9]/','', $prd_row["loan_start_date"]);
$data["loan_contract_info"]["contract_end_date"]   = preg_replace('/[^0-9]/','', $prd_row["loan_end_date"]);
$data["loan_contract_info"]["contract_dtm"] = preg_replace('/[^0-9]/','', $prd_row["loan_start_date"])."120000";

$brw_info = get_brw_info($prd_row["loan_mb_no"]);
$data["borrower_info"]["identity_no"] = $brw_info["brw_idno"];

$repay_info = repayCalculationNew($product_idx, '' , 'Y');
for ($i=0 ; $i<count($repay_info["REPAY"]) ; $i++) {
	$data["loan_schd_repayment_list"][$i]["repayment_n_count"] = (int)$repay_info["REPAY"][$i]["repay_num"];
	$data["loan_schd_repayment_list"][$i]["schd_date"]         = check_int($repay_info["REPAY"][$i]["repay_date"]);

	if (is_array($repay_info["REPAY"][$i]["PARTIAL"])) $data["loan_schd_repayment_list"][$i]["schd_p_amount"] = (int)$repay_info["REPAY"][$i]["PARTIAL"][0]["SUM"]["partial_principal"];
	else $data["loan_schd_repayment_list"][$i]["schd_p_amount"] = (int)$repay_info["REPAY"][$i]["SUM"]["repay_principal"];

	if ($product_idx==5933) {
		if (is_array($repay_info["REPAY"][$i]["PARTIAL"])) $data["loan_schd_repayment_list"][$i]["schd_p_amount"] = (int)$repay_info["REPAY"][$i]["PARTIAL"][1]["SUM"]["partial_principal"];
		else $data["loan_schd_repayment_list"][$i]["schd_p_amount"] = (int)$repay_info["REPAY"][$i]["SUM"]["repay_principal"];
	}
	

	$data["loan_schd_repayment_list"][$i]["schd_interest"]     = (int)$repay_info["REPAY"][$i]["SUM"]["invest_interest"];
	$data["loan_schd_repayment_list"][$i]["schd_fee_etc"]      = (int)$repay_info["REPAY"][$i]["SUM"]["invest_usefee"];
}

if ($mode=="send" AND !$rowi["loan_contract_id"]) {

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, "");
	$resj = json_decode($curl_res["body"] , true);


	if ($resj["rsp_code"] == "A0000" or $resj["rsp_code"] == "A0001") {
		$up_sql = "UPDATE cf_product SET loan_contract_id = '$loan_contract_id' WHERE idx = '$product_idx' AND loan_contract_id='' ";
		sql_query($up_sql);

		$main_sql = "SELECT * FROM p2pctr_product WHERE product_idx='$product_idx'";
		$main_res = sql_query($main_sql);
		$main_cnt = $main_res->num_rows;

		if ($main_cnt) {
			$main_row = sql_fetch_array($main_res);
			$main_up_sql = "UPDATE p2pctr_product 
							   SET loan_contract_id='$loan_contract_id',
								   loan_status='S100',
								   loan_contract_datetime=NOW()
							 WHERE idx='".$main_row[idx]."'";
			sql_query($main_up_sql);
		} else {
			$main_ins_sql = "INSERT INTO p2pctr_product 
									 SET product_idx='$product_idx', 
										 loan_contract_id='$loan_contract_id',
										 loan_status='S100',
										 loan_contract_datetime=NOW()";
			sql_query($main_ins_sql);
		}

		echo "기록 성공<br/><br/>";
		echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
	} else {
		echo "기록 실패<br/><br/>";
		echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
	}


}
?>
<?
//echo "<pre>"; print_r($data); echo "</pre>";
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="tbl_head02 tbl_wrap" style="margin-top:10px;">
	<h3><?=$apiNo?> <?=$apiTitle?><?=strpos($url,'data')!==false?" (이관용)":""?></h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>대출신청 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_register_id"]?>
		</td>
	</tr>
	<tr>
		<th>상품 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_contract_info"]["goods_id_list"][0]["goods_id"]?>
		</td>
	</tr>
	<tr>
		<th>대출계약 ID</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_contract_info"]["loan_contract_id"]?>
		</td>
	</tr>
	<tr>
		<th>대출계약금액</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=number_format($data["loan_contract_info"]["loan_contract_amount"])?>
		</td>
	</tr>
	<tr>
		<th>대출계약기간</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_contract_info"]["contract_start_date"]?> ~ <?=$data["loan_contract_info"]["contract_end_date"]?> ( <?=$data["loan_contract_info"]["loan_term_days"]?>일)
		</td>
	</tr>
	<tr>
		<th>대출계약체렬일시</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=$data["loan_contract_info"]["contract_dtm"]?>
		</td>
	</tr>
	<tr>
		<th>차입자 식별번호</th>
		<td style="text-align:center;vertical-align:middle;">
			<?=substr($data["borrower_info"]["identity_no"],0,6)?>-<?=str_repeat("*",strlen(substr($data["borrower_info"]["identity_no"],6)))?>
		</td>
	</tr>
	<tr>
		<th>상환예정</th>
		<td style="text-align:center;vertical-align:middle;">
			<table class="table table-bordered table-condensed">
				<tr>
					<th>회차</th>
					<th>일자</th>
					<th>원금</th>
					<th>이자</th>
					<th>수수료</th>
				</tr>
			<? for ($i=0 ; $i<count($data["loan_schd_repayment_list"]) ; $i++) { ?>
				<tr>
					<td style="text-align:center;vertical-align:middle;">
						<?=$data["loan_schd_repayment_list"][$i]["repayment_n_count"]?>
					</td>
					<td style="text-align:center;vertical-align:middle;">
						<?=$data["loan_schd_repayment_list"][$i]["schd_date"]?>
					</td>
					<td style="text-align:right;vertical-align:middle;">
						<?=number_format($data["loan_schd_repayment_list"][$i]["schd_p_amount"])?>
					</td>
					<td style="text-align:right;vertical-align:middle;">
						<?=number_format($data["loan_schd_repayment_list"][$i]["schd_interest"])?>
					</td>
					<td style="text-align:right;vertical-align:middle;">
						<?=number_format($data["loan_schd_repayment_list"][$i]["schd_fee_etc"])?>
					</td>
				</tr>
			<? } ?>
			</table>
		</td>
	</tr>
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