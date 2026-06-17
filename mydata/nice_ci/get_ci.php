<?
include_once($_SERVER["DOCUMENT_ROOT"]."/mydata/mydata.common.lib.php");
?>
<!--
<pre>
Array
(
    [dataHeader] => Array
        (
            [CNTY_CD] => kr
            [GW_RSLT_CD] => 1200
            [GW_RSLT_MSG] => 오류 없음
        )

    [dataBody] => Array
        (
            [rsp_cd] => P000
            [enc_data] => iw6FcBoZEM6m8H9bGpon74+XivaDzywoS62fT1R48UeQwwLL8907cKyAQPl60M3noxTBrKxYDtV+TPkKutN4iHA6knkp345oJVhtOsTSix/gkTZoTJdKGhsty7YttS4UbcCH1k2z3x8Oy/wFdwTp5xtm6fe67vR9swlS7fNkNmMHIKqKcwpZiekjR/7XKyFaMdgnBETiHMFzS8OJ3r5nb7X47gcVUX37cKOx76aL738=
            [integrity_value] => CM6+E+ayd8zV5ecuaVlbIcvAspDf7+WrUz+r47maXpE=
            [result_cd] => 0000
        )

)
</pre>
-->
<?

$hmac_key = "ASDFSDFEWRSDSESFSDFSERSDFSDFSDFC";
$cipher = "aes-256-cbc";
$sym_key = "ASDFGHJKLQWERTYUASDFGHJKLQWERTYU";
$iv = "QWERTYUJHGFDSAZX";
/*
$enc_data = "iw6FcBoZEM6m8H9bGpon74+XivaDzywoS62fT1R48UeQwwLL8907cKyAQPl60M3noxTBrKxYDtV+TPkKutN4iHA6knkp345oJVhtOsTSix/gkTZoTJdKGhsty7YttS4UbcCH1k2z3x8Oy/wFdwTp5xtm6fe67vR9swlS7fNkNmMHIKqKcwpZiekjR/7XKyFaMdgnBETiHMFzS8OJ3r5nb7X47gcVUX37cKOx76aL738=";
$res_hmac = base64_encode(hash_hmac('sha256', $enc_data, $hmac_key, true));
echo "<br/><br/>$res_hmac<br/>";

$aa1 = openssl_decrypt($enc_data ,$cipher , $sym_key, $options=0, $iv);
$aa2 = base64_decode($aa1);
echo "enc ".$aa1."<br/>";
echo "64 ".$aa2."<br/>";
echo "전승 N8uCqH0jmjhJMDeJ/+7/sO8BnrbKHHcD5wU7DYd1TFm4ruMK+10KryT27eLeh7ClPrZI9dNMhTTQZhOh7qCRvQ=="."<br/>";
die();
*/
$url = $nice_host."/digital/niceid/cert/v1.0/ipin/addinfo/ci";

/*
[Authorization 생성가이드]

"Basic " + Base64Encoding(${client_id}+":"+${client_secret})

* client_id: APP등록 시 생성 값
* clent_secret: APP등록 시 생성 값
*/
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
//$sdata["jumin_id"] = "8108122030313";  // 일반투자회원
$sdata["jumin_id"] = "7307231226922";  // 차주회원
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

$data["dataBody"]["symkey_version"] = "202110235E890045-D35D-IPE4-8603-1EDFDD068F59";
$data["dataBody"]["enc_data"] = $sdata2;
$data["dataBody"]["integrity_value"] =  base64_encode(hash_hmac('sha256', $data["dataBody"]["enc_data"], $hmac_key, true));

$nice_res = nice_curl($url, $headers, $data);

echo "<pre>"; print_r($nice_res); echo "</pre>";
echo "<pre>"; print_r($data); echo "</pre>";
echo "<br/><br/>==================== RESULT ===========================<br/><br/>";
if ($nice_res["dataHeader"]["GW_RSLT_CD"]=="1200") {

	if ($nice_res["dataBody"]["rsp_cd"]=="P000") {

		if ($nice_res["dataBody"]["result_cd"]=="0000") {

			$res_hmac = base64_encode(hash_hmac('sha256', $nice_res["dataBody"]["enc_data"], $hmac_key, true));

			if ($res_hmac == $nice_res["dataBody"]["integrity_value"]) {
				$res_dec = openssl_decrypt($nice_res["dataBody"]["enc_data"] ,$cipher , $sym_key, $options=0, $iv);
				$res_arr = json_decode($res_dec, true);
				$ci = $res_arr["ci1"];

				echo "CI = ".$ci;

			}

		}

	}
}
?>