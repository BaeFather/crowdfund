<?
if($_SERVER["REQUEST_METHOD"]!="POST") exit;

include_once("_common.php");

if($is_member) {
	setcookie("pwdauth", "Y", time()+30, "/", G5_COOKIE_DOMAIN, true, true);
	echo 1;
}
else {
	exit;
}
?>