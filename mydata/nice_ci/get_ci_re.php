<?
include_once($_SERVER["DOCUMENT_ROOT"]."/mydata/mydata.common.lib.php");
?>
<?
$hmac_key = "ASDFSDFEWRSDSESFSDFSERSDFSDFSDFC";
$cipher = "aes-256-cbc";
$sym_key = "ASDFGHJKLQWERTYUASDFGHJKLQWERTYU";
$iv = "QWERTYUJHGFDSAZX";

$url = $nice_host."/digital/niceid/cert/v1.0/ipin/addinfo/ci";

$Au = $client_id.":".$client_secret;
$Auth = "Basic ". base64_encode( $Au );

$headers = array();
ARRAY_PUSH($headers,"Content-Type: application/json;");
ARRAY_PUSH($headers,"Authorization: ".$Auth);
ARRAY_PUSH($headers,"productID: ".$product_id);


$sdata = array();
$sdata["site_code"] = "Rlo5Mg==";
$sdata["info_req_type"] = "1";
//$sdata["jumin_id"] = "7207311221110";
//$sdata["jumin_id"] = "8509091904429";
$sdata["jumin_id"] = $_POST["jm"];
$sdata["req_no"] = date("YmdHis");
$sdata["req_dtim"] = date("YmdHis");
$sdata_json = json_encode($sdata , JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE ); 


//$iv = "";
$tag = "";
$hmac_key = "ASDFSDFEWRSDSESFSDFSERSDFSDFSDFC";
$sdata2 = openssl_encrypt($sdata_json, $cipher, $sym_key, $options=0, $iv);



$data = array();

$data["dataHeader"]["CNTY_CD"] = "kr";
//$data["dataHeader"]["TRAN_ID"] = date("YmdHis")."0000000001";

//$data["dataBody"]["symkey_version"] = "202110235E890045-D35D-IPE4-8603-1EDFDD068F59"; // ~20220423
$data["dataBody"]["symkey_version"] = "202204288E83AC59-6DEA-IPAE-AF40-C9DC752BB49F";
$data["dataBody"]["enc_data"] = $sdata2;
$data["dataBody"]["integrity_value"] =  base64_encode(hash_hmac('sha256', $data["dataBody"]["enc_data"], $hmac_key, true));

$nice_res = nice_curl($url, $headers, $data);

//echo $nice_res["dataBody"]["result_cd"]."\n";
//print_r($nice_res);

if ($nice_res["dataHeader"]["GW_RSLT_CD"]=="1200") {

	if ($nice_res["dataBody"]["rsp_cd"]=="P000") {

		if ($nice_res["dataBody"]["result_cd"]=="0000") {

			$res_hmac = base64_encode(hash_hmac('sha256', $nice_res["dataBody"]["enc_data"], $hmac_key, true));

			if ($res_hmac == $nice_res["dataBody"]["integrity_value"]) {
				$res_dec = openssl_decrypt($nice_res["dataBody"]["enc_data"] ,$cipher , $sym_key, $options=0, $iv);
				$res_arr = json_decode($res_dec, true);
				$ci = $res_arr["ci1"];

				//echo $ci;
				echo json_encode($nice_res, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE);

			}

		}

	}
}

?>