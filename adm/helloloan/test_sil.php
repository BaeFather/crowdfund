<?
error_reporting(0);
include_once('./_common.php');

$mg_id2 = "2690";
$ju_seri2 = "2557";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
curl_setopt($ch, CURLOPT_URL, "http://scrap2.hellofunding.kr/scrap2/kb_sil.php?d_code=$d_code&mg_id2=$mg_id2&ju_seri2=$ju_seri2");
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
curl_close($ch);

$d = json_decode($response, true);

$sil_price = $d["dataBody"]["data"]["시세"][0]["매매거래금액"];
$sil_day   = $d["dataBody"]["data"]["시세"][0]["매매계약종료년월일"];

$up_sql = "UPDATE hello_apt_kb SET mm_sil='$sil_price' , mm_date='$sil_day' WHERE mg_id2='$mg_id2' AND ju_seri2='$ju_seri2'";

echo $response."<br/><br/>";
echo $sil_price." - ".$sil_day."<br/><br/>";
echo $up_sql . "<br/><br/>";
echo "<pre>"; print_r($d); echo "</pre>";

?>