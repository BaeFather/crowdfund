<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');
//include_once($_SERVER["DOCUMENT_ROOT"].'/lib/repay_calculation_new.php');
foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }

if (!$product_idx or !$turn) die("상품번호 또는 회차 오류");
?>
<?
$apiNo = "4.2.7";
$apiTitle = "대출상환 기록";
?>
<?
$url  = $p2p_host . "loans/repayment";
//if ($product_idx=="3107") $url  = $p2p_host . "data/loans/repayment";  // 이관용
$method = "POST";

$sql = "SELECT loan_contract_id  FROM cf_product where idx='$product_idx'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

// 2022-01-12 연체중 원금 일부상환 임의처리
if ($product_idx=="8068" or $product_idx=="8081") { $turn=1 ; $turn_sno=1; }

$give_sql = "SELECT DATE,turn,SUM(interest) sum_int , SUM(principal) sum_prin, SUM(interest_tax) int_tax, SUM(local_tax) loc_tax, SUM(fee) sum_fee 
			   FROM cf_product_give 
			  WHERE product_idx='$product_idx' 
			    AND turn='$turn' AND turn_sno='$turn_sno' and idx<>'933095'";
$give_res = sql_query($give_sql);
$give_cnt = sql_num_rows($give_res);


$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

unset($headers);
$headers[] = "Content-Type: application/json; charset=UTF-8";
ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);

$data = array();

$data["loan_contract_id"] = $row["loan_contract_id"];
$data["loan_repayment_list"] = array();

for ($i=0 ; $i<$give_cnt ; $i++) {

	$grow = sql_fetch_array($give_res);

	$data["loan_repayment_list"][$i]["repayment_n_count"] = (int)$grow["turn"];
	if ($product_idx=="5933") $data["loan_repayment_list"][$i]["repayment_n_count"]=(int)$grow["turn"]+1;
	if ($product_idx=="4945") $data["loan_repayment_list"][$i]["repayment_n_count"]=(int)$grow["turn"]+1;
	//if ($product_idx=="5863") $data["loan_repayment_list"][$i]["repayment_n_count"]=9;
	if ($product_idx=="6150") $data["loan_repayment_list"][$i]["repayment_n_count"]=(int)$grow["turn"]+1;
	//$data["loan_repayment_list"][$i]["repayment_type"] = "RP00";   // RP00 (일반상환), RP10 (조기상환), RP90 (연체상환)

	$data["loan_repayment_list"][$i]["repayment_type"] = $repayment_type;
	$data["loan_repayment_list"][$i]["repayment_date"] = check_int($grow["DATE"]);
	$data["loan_repayment_list"][$i]["repayment_p_amount"] = (int)$grow["sum_prin"];
	$data["loan_repayment_list"][$i]["repayment_interest"] = (int)($grow["sum_int"]+$grow["int_tax"]+$grow["loc_tax"]+$grow["sum_fee"]);
	$data["loan_repayment_list"][$i]["repayment_total_amount"] = (int)($data["loan_repayment_list"][$i]["repayment_p_amount"] + $data["loan_repayment_list"][$i]["repayment_interest"]);

	if ($product_idx=="8068") {
		$data["loan_repayment_list"][$i]["repayment_n_count"]=(int)$grow["turn"]+1;
		$data["loan_repayment_list"][$i]["repayment_date"]="20220311";
		$data["loan_repayment_list"][$i]["repayment_p_amount"] = 10062664;
		$data["loan_repayment_list"][$i]["repayment_interest"] = 198473;
		$data["loan_repayment_list"][$i]["repayment_total_amount"] = 10261137;
	}
	if ($product_idx=="8081") {
		$data["loan_repayment_list"][$i]["repayment_n_count"]=(int)$grow["turn"]+1;
		$data["loan_repayment_list"][$i]["repayment_date"]="20220311";
		$data["loan_repayment_list"][$i]["repayment_p_amount"] = 7819407;
		$data["loan_repayment_list"][$i]["repayment_interest"] =  141627;
		$data["loan_repayment_list"][$i]["repayment_total_amount"] = 7961034;
	}
	if ($product_idx=="8109") {
		$data["loan_repayment_list"][$i]["repayment_date"]="20220311";
	}
 	if ($product_idx=="5933") {
		$data["loan_repayment_list"][$i]["repayment_p_amount"]=70000174;
		$data["loan_repayment_list"][$i]["repayment_total_amount"] = 70156275;
	}

}

$rdate = preg_replace('/[^0-9]/','', $grow["DATE"]);

if ($mode=="send") {

	//$res = curl_p2pctr($url , $method , $data , $headers);
	$res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, "");

	//$intEtime = time();
	//$thrSec = $intStime - $intEtime;
	//fn_log($apiNo, $apiTitle, $mb_no, $url, $res["req_body"] , $res["body"], $res["http_code"], $thrSec, $strApiTrxNo);

	$resj = json_decode($res["body"] , true);
	if ($resj["rsp_code"] == "A0000") {
		echo "기록 성공<br/><br/>";
		echo "<pre>"; print_r($res); echo "</pre><br/><br/>";		

		$upsucc_sql = "UPDATE cf_product_success 
						  SET loan_contract_id='".$row["loan_contract_id"]."' , p2pCtr_date='".$rdate."'  
						WHERE product_idx='$product_idx' AND turn='$turn' AND turn_sno='$turn_sno'";
		//echo "<br/>$upsucc_sql<br/>";
		sql_query($upsucc_sql);

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
	<h3><?=$apiNo?> <?=$apiTitle?><?=strpos($url,'data')!==false?" (이관용)":""?></h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>대출계약 번호</th>
		<td style="text-align:center;vertical-align:middle;"><?=$data["loan_contract_id"]?></td>
	</tr>
	<tr>
		<th>상환</th>
		<td style="text-align:center;vertical-align:middle;">
			<table>
				<tr>
					<th>회차</th>
					<th>유형</th>
					<th>상환일</th>
					<th>원금</th>
					<th>이자</th>
					<th>상환금</th>
				</tr>
<? for ($i=0 ; $i<count($data["loan_repayment_list"]) ; $i++) { ?>
				<tr>
					<td style="text-align:center;"><?=$data["loan_repayment_list"][$i]["repayment_n_count"]?></td>
					<td style="text-align:center;"><?=$data["loan_repayment_list"][$i]["repayment_type"]?></td>
					<td style="text-align:center;"><?=$data["loan_repayment_list"][$i]["repayment_date"]?></td>
					<td style="text-align:right;"><?=number_format($data["loan_repayment_list"][$i]["repayment_p_amount"])?></td>
					<td style="text-align:right;"><?=number_format($data["loan_repayment_list"][$i]["repayment_interest"])?></td>
					<td style="text-align:right;"><?=number_format($data["loan_repayment_list"][$i]["repayment_total_amount"])?></td>
				</tr>
<? } ?>
			</tbale>
		</td>
	</tr>
</table>

<table style="width:100%;border:0;">
	<tr>
		<td style="text-align:center; border:0;">
			<form method="post" name="ff">
			<input type=hidden name="mode" value=""/>
			<input type=hidden name="product_idx" value="<?=$product_idx?>"/>
			<input type=hidden name="turn" value="<?=$turn?>"/>
			<select name="repayment_type">
				<option value=""></option>
				<option value="RP00" <?=$repayment_type=="RP00"?"selected":""?> >일반상환</option>
				<option value="RP10" <?=$repayment_type=="RP10"?"selected":""?> >조기상환</option>
				<option value="RP90" <?=$repayment_type=="RP90"?"selected":""?> >연체상환</option>
			</select>
			<input type="button" class="btn btn-sm btn-warning" onclick="go_send();" value="전송"/>
			</form>
		</td>
	</tr>
</table>

<script>
function go_send() {

	var f = document.ff;
	if (!f.repayment_type.value) {
		alert("대출상환 유형을 선택해주세요.");
		return;
	}

	var yn = confirm("이대로 전송하시겠습니까?");
	if (!yn) return;

	f.mode.value="send";
	f.submit();
}
</script>