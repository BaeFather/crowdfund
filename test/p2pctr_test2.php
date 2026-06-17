<?
include_once('./_common.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

$member_idx =""; // "49565"; //"6412"; //"50174"; //"23650"; //"3517"; //"44702"; //"2745"; //"6312"; //"8695"; //"48195"; //"22381"; //"12302"; 
//"4176"; //"50342"; //"6372";// "2670"; //"48288"; //"49011"; //"35946"; 
//"2984"; //"7121"; //"4736"; //"43924"; //"42292"; //"16156"; //"48267"; //"9762"; //"40892";
//"45605"; //"42973"; //"3691"; //"3690"; //"3689"; //"6265"; //"22898"; //"6444"; //"1597"; //"21949"; //"50372"; //"13229"; //"22243"; 
//"6458"; //"34552"; //"1948"; //"27500"; //"4614"; //"21140"; // //"14846"; //"25485"; //"6412";
// 한도초과 48158 21213 20383 18128 46418 23018 14106 44166 "23585" "41792" "49343";
$product_idx = "6926";

echo "$member_idx<br/><br/>";

//$rrr = p2pctr_invest_register22($member_idx, $product_idx);
//$ccc = p2pctr_invest_register_canc22($member_idx, $product_idx);
?>
<?
function p2pctr_invest_register22($member_idx, $product_idx) {

	if (!$member_idx OR !$product_idx) return false;

	global $p2p_host;

	$psql = "SELECT goods_id FROM cf_product WHERE idx='$product_idx'";

	$pres = sql_query($psql);
	$prow = sql_fetch_array($pres);
	$goods_id = $prow["goods_id"];

	if (!$goods_id) return;
	$goods_type = get_goods_type($product_idx);

	$sqli = "SELECT * FROM cf_product_invest WHERE invest_state='Y' AND product_idx='$product_idx' AND member_idx='$member_idx'";
	$resi = sql_query($sqli);
	$cnti = sql_num_rows($resi);

	if (!$cnti) return;

	$rowi = sql_fetch_array($resi);

	if ($rowi["investment_register_id"]) {  // 기 등록한 자료가 있다면 취소한다.
		$canc_res = p2pctr_invest_register_canc($member_idx, $product_idx);
	}

	$apiNo = "4.4.2";
	$apiTitle = "투자신청 기록";
	$url  = $p2p_host . "investments/register";
	$method = "POST";

	$data["investment_register_info"] = array();
	$data["investor_info"] = array();
	$data["goods_info"] = array();
$mode="send";
	$investment_register_id = $rowi["investment_register_id"];
	if (!$investment_register_id AND $mode=="send") $investment_register_id = get_new_id("investment_register_id");

	$data["investment_register_info"]["investment_register_id"] = $investment_register_id;
	$data["investment_register_info"]["bank_inquiry_id"] = $investment_register_id;
	$data["investment_register_info"]["investment_amount"] = (int)$rowi["amount"];
	$data["investment_register_info"]["investment_register_dtm"] = preg_replace('/[^0-9]/','', $rowi["insert_date"]).preg_replace('/[^0-9]/','', $rowi["insert_time"]);
	$data["investment_register_info"]["status"] = "T100"; // T100 투자신청중
	$data["investment_register_info"]["investments_document_info"]["document_confirm_date"] = preg_replace('/[^0-9]/','', $rowi["insert_date"]);
	$data["investment_register_info"]["investments_document_info"]["document_type"] = "DP99"; // DP01  (전자문서 형식의 파일)  DP99  (전자문서 이외의 파일)

	$inv_info = get_inv_info($member_idx);
	$data["investor_info"]["identity_no"] = $inv_info["inv_idno"];
	$data["investor_info"]["name"] = $inv_info["inv_name"];
	$data["investor_info"]["type"] = $inv_info["inv_type"];

	if (substr($data["investor_info"]["type"] , 0 , 2) == "I3") $data["investor_info"]["business_register_no"] = $inv_info["business_register_no"];
	$data["goods_info"]["goods_id"] = $goods_id;
	$data["goods_info"]["goods_type"] = $goods_type;


	if ($mode=="send" AND !$rowi["investment_register_id"]) {

		$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data , $product_idx, $member_idx);
		$resj = json_decode($curl_res["body"] , true);

		if ($resj["rsp_code"] == "A0000") {
			$up_sql = "UPDATE cf_product_invest SET investment_register_id = '$investment_register_id' WHERE idx = '$rowi[idx]' AND investment_register_id='' ";
			sql_query($up_sql);
			echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
			return true;
		} else {
			echo "기록 실패<br/><br/>";
			echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";
			return fail;
		}
		
	}

}

function p2pctr_invest_register_canc22($member_idx, $product_idx) {

	if (!$member_idx OR !$product_idx) return false;

	global $p2p_host;

	$sqli = "SELECT * FROM cf_product_invest WHERE invest_state='Y' AND product_idx='$product_idx' AND member_idx='$member_idx'";
	$resi = sql_query($sqli);
	$cnti = sql_num_rows($resi);

	if (!$cnti) return;

	$rowi = sql_fetch_array($resi);

	$apiNo = "4.4.3";
	$apiTitle = "투자신청 갱신";
	$url  = $p2p_host . "investments/register/".$rowi["investment_register_id"];
	$method = "PUT";


	$cdata = array();
	$cdata["status"] = "T150";

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $cdata, $product_idx, $member_idx);
	$resj = json_decode($curl_res["body"] , true);
	echo "<pre>"; print_r($resj); echo "</pre><br/><br/>";

	if ($resj["rsp_code"] == "A0000") {

		$up_sql = "UPDATE cf_product_invest SET investment_register_id='' WHERE idx = '".$rowi["idx"]."'";
		//sql_query($up_sql);
		echo "$up_sql<br/><br/>";
		return true;
	} else {
		return false;
	}

	return false;
}
?>