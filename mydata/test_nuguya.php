<?
$host = "secure.nuguya.com";
$port=443;

$socket  = fsockopen("ssl://".$host, $port, $errno, $errstr, 10); // 소켓 타임아웃 10초

if(!$socket) {
	echo "ERROR <br/>";
} else {
	echo "소켓연결성공 ".$socket." - ".$errno." ".$errstr;
}

fclose($socket);


?>