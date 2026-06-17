<?
include_once($_SERVER["DOCUMENT_ROOT"]."/mydata/mydata.common.lib.php");
?>
<pre>
<!--
Array
(
    [dataHeader] => Array
        (
            [CNTY_CD] => kr
            [GW_RSLT_CD] => 1200
            [GW_RSLT_MSG] => 오류 없음
            [TRAN_ID] => 202110221019440000000001
        )

    [dataBody] => Array
        (
            [rsp_cd] => P000
            [site_code] => Rlo5Mg==
            [result_cd] => 0000
            [key_version] => 202110225148939f-fb5a-IP74-8622-1ef2290f4933
            [public_key] => MIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKCAYEA47mFeENBqm1m7njDEsw+8eI+9SWBPMjB8Yg9PP4CO4oyZXomwGZJxPAaZou+oJZLbRdhoKijUN9u0aeyxCUyXeAUGc3OpuKHV9Zx2W9t1Vr/yhdkO1uZ1JRzVOcywk7A953WyeygaoAV/OJaIVMNC+nAqMARLtO8p7VMJIfRdgh+QD/hquaNFadBbIdG5BzqgubgvQajuIgI4yl5UEb4L29e+3oOiB4ghbB2Y6j7U05Q32OWoDc3iMqytPUuJssPzWyO3STVJWI6+SY1baWEdGSyhlxr6npdSmWbKMbAsFnc3KMMvd2qHdgJoLhpV0+dxOggi79L1k0iXYWroNju27kbltEg7NIEQ4aChjkWqBkx94AKseXaTEWKW5NWL+6ElKaTYGjQXWhNXaMVIxEJDtQFecNL7UY00IhX60RYZgnOiWQ/R0Uiftd9PB1ACP5P/tBad9SJOhLDTRsnqbErWhhXN/XDOQ2U4x8odXqzEx06tsaY4m9mfdNUrzbm5X25AgMBAAE=
            [valid_dtim] => 20221022101944
        )

)
-->
Array
(
    [dataHeader] => Array
        (
            [CNTY_CD] => kr
            [GW_RSLT_CD] => 1200
            [GW_RSLT_MSG] => 오류 없음
            [TRAN_ID] => 202211021739400000000001
        )

    [dataBody] => Array
        (
            [rsp_cd] => P000
            [site_code] => Rlo5Mg==
            [result_cd] => 0000
            [key_version] => 202211027a1c9b48-fde3-IP0c-bbd7-aa6f8cfb4ce3
            [public_key] => MIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKCAYEAtb68uYobb7ld5VV3F3IBuL7F/NdxwBHf1FHbWNTbgGi57aRhMBiqNpS84S5h5r6oVqzTFUyrNNRgAPepnHSpDlunyz6FH29blXEWy40R9xqZPrCD+zu/sMVpW9fjjCPj6EVyXzPPYjaxNPYEqJzMa9I2vv9u3UIFocmJ9yBnxqFrtpC5tXIefpX34X0MgLo0vxRs2cPSIUOcgU8dsziHPdvu0DumlMzVwV/xny5eefdxPjyz0f6vCbwvlFM0DA5TQQGBUAVFUzfYpzeMEnep4DNN8rfSNgjh/NCxdAo7SbpnJNfyUOzHnZ7I8OyeRiqLjJnJnUF/Q3cMFdTlI9nvHLcmMYEG4W6LKvutzMh2s0V4R3M4tjIl5iHnYPwxGlLX1ZzgSbJX87YATqaENTVy5+u/OpOAowDByWh2kQiAUq97i95gtNo1tsWvXMXrPzfqaa34e3kGDcHrXBkFm2XODOY1/kh6Hv+gjj935V92lFKK2brwMzIQG2o8JVH3KOM/AgMBAAE=
            [valid_dtim] => 20231102173940
        )

)
</pre>
<?
die();
$url = $nice_host."/digital/niceid/api/v1.0/common/crypto/publickey";

/*
"bearer " + Base64Encoding(${access_token}+":"+${current_timestamp}+":"+ ${client_id})

* access_token: 토큰 발급 API를 통해 발급 받은 토큰 값(유효기간 존재)
* current_timestamp: 현재시간 Timestamp (예: new Date().getTime()/1000 )
* client_id: APP등록 시 생성 값
*/
$tt = time();
$Au = $nice_access_token.":".$tt.":".$client_id ;
$Auth = "bearer ". base64_encode( $Au );


$headers = array();
ARRAY_PUSH($headers,"Content-Type: application/json;");
ARRAY_PUSH($headers,"Authorization: ".$Auth);
ARRAY_PUSH($headers,"client_id: ".$client_id);
ARRAY_PUSH($headers,"productID: "."2101466024");

echo $url."<br/>";
echo $Au."<br/>";
echo "<pre>"; print_r($headers); echo "</pre>";

$data = array();
//$data["dataHeader"]["CNTY_CD"] = "ko";
$data["dataHeader"]["CNTY_CD"] = "kr";
$data["dataHeader"]["TRAN_ID"] = date("YmdHis")."0000000001";
$data["dataBody"]["req_dtim"] = date("YmdHis");

$nice_res = nice_curl($url, $headers, $data);

echo "<pre>"; print_r($nice_res); echo "</pre>";
?>
