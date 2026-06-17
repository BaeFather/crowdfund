<?php
include_once("./_common.php");

foreach($_REQUEST as $k=>$v) {
	${$k} = trim($v);
}

if(($adminid==$_CONF['SYNDI_ID'] || $adminid==$_CONF['SYNDI_ID_AS']) && $adminpw==$_CONF['SYNDI_PW']) {
	setcookie("syndi_admin_login","1",0,"/",HF_COOKIE_DOMAIN);
	echo "SUCCESS";
}
else {
	setcookie("syndi_admin_login","",-1,"/",HF_COOKIE_DOMAIN);
	echo "FAIL";
}
?>