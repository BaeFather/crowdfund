<?php
$header = array();
$header[] = 'Connection: close';
$params = array('http' => array(
   'ignore_errors' => TRUE,
  'protocol_version' => '1.0',
   'method' => 'GET'
    ));

if ($header !== null) {
  $head = "";
  foreach($header as $h) {
    $head = $head . $h . "\r\n";
  }
  $params['http']['header'] = substr($head,0,-2);
}


$ctx = stream_context_create($params);

$response = (file_get_contents('https://ca-test.linkhub.co.kr/', false, $ctx));

if ($http_response_header[0] != "HTTP/1.1 200 OK") {
  var_dump($response);
}
var_dump($response);
?>