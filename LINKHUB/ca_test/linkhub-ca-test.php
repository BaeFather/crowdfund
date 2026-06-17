<?php

$http = curl_init('https://ca-test.linkhub.co.kr/');

curl_setopt($http, CURLOPT_RETURNTRANSFER, TRUE);

$responseJson = curl_exec($http);

$http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);

if ($responseJson != true){
  var_dump(curl_error($http));
}

curl_close($http);


if($http_status != 200) {
  var_dump($responseJson);
}

var_dump($responseJson);


?>