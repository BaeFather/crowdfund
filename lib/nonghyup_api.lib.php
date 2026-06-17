<?
// 농협 소켓통신

function nh_fin_access($data){

	$post_param = "data=".json_encode($data);

	$URL = 호출 url
	$url_info = parse_url($URL);

	$fp = fsockopen ($url_info["host"], 포트번호, $errno, $errstr, 90);
	if (!is_resource($fp))
	{
		echo "not connect host : errno=$errno,errstr=$errstr";
		exit;
	}

	fputs($fp,"POST $URL HTTP/1.0\n");
	fputs($fp,"Content-type: application/x-www-form-urlencoded\n");
	fputs($fp,"Content-length: " . strlen($post_param) . "\n");
	fputs($fp,"\n");
	fputs($fp,"$post_param\n");
	fputs($fp,"\n");

	while(!feof($fp)) {
		$res .= fgets ($fp, 1024);
	}

	fclose ($fp);

	return $res;
}

?>