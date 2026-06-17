<?

include_once("../syndication_config.php");

foreach($_REQUEST as $k=>$v) {
	${$k} = trim($v);
}

if(($adminid==$_CONF['SYNDI_ID'] || $adminid==$_CONF['SYNDI_ID_AS']) && $adminpw==$_CONF['SYNDI_PW']) {
	$_SESSION['syndi_admin_login'] = true;
	echo "SUCCESS";
}
else {
	$_SESSION['syndi_admin_login'] = false;
	echo "FAIL";
}

?>