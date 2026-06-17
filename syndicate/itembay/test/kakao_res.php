<?php

$sample_receive = array(
  "status" => 200,
  "tid" => "T2476823102374074532",
  "next_send_url" => "https://money-web.kakao.com/web/remittance?e=325a874af21b972ff77",
  "created_at" => "20180101000000",
  "expired_at" => "20180101000015"
);

$kkp_res_test = json_encode($sample_receive , JSON_UNESCAPED_UNICODE);

//$kkp_res = file_get_contents("php://input"); //read the HTTP body.


$kkp_res = json_decode($kkp_res_test,true);

echo "<pre>";print_r( $kkp_res);echo "</pre>";


if ($kkp_res['status']=="200") {
	echo "정상<br/>";
	
	$tmp = explode("?",$kkp_res['next_send_url']);
	$url_pas = $tmp[1];
	
	echo "token : $url_pas<br/>";
	echo "카카오페이 송금번호 : $kkp_res[tid]<br/>";
	
} else if ($kkp_res['status']=="401") {
	echo "잘못된 API key로 요청";
} else if ($kkp_res['status']=="400") {
	echo "잘못된 파라미터";
} else if ($kkp_res['status']=="500") {
	echo "서버에러";
}
?>