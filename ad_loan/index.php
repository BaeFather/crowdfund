<?php
////////////////////////
// 대출신청페이지
////////////////////////
include_once('./_common.php');


IF(!$_COOKIE["pid"])
{
	$strP = $_GET["p"];
	IF($strP)
	{
		setcookie("pid",TRIM($strP),0,"/","");
	}
} ELSE {
	$strP = $_COOKIE["pid"];
}


if(G5_IS_MOBILE) {
	include_once('./index_m.php');
	return;
}


include_once('index_pc.php');

?>