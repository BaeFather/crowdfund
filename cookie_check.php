<?

if (!preg_match("/(220\.117\.134|183\.98\.101)/", $_SERVER["REMOTE_ADDR"])) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include_once("_common.php");


$ck_pid = get_cookie("ck_pid");
echo "ck_pid : " . $ck_pid . "<br/>\n";

if( in_array($ck_pid, array_keys($CONF['PARTNER'])) ) {
	echo "11111111111111";
}


print_rr($_COOKIE);

$COOKEY = array_keys($_COOKIE);

echo "<pre style='font-size:12px'>";
for($i=0; $i<count($_COOKIE); $i++) {
	echo $COOKEY[$i] . " : " . base64_decode($_COOKIE[$COOKEY[$i]]) . "\n";
}
echo "</pre>";

?>