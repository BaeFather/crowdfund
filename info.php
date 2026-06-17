<?
if ( !preg_match("/(183\.98\.101\.|172\.17\.3\.|172\.19\.3\.)/", $_SERVER['REMOTE_ADDR']) ) {
	header("HTTP/1.0 404 Not Found");
	exit;
}


echo "SERVER 1";
echo "<pre style='font-size:12px'>"; print_r($_SERVER); echo "</pre>\n";
//phpinfo();

?>