<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include_once('./_common.php');
include_once('../admin.loan.function.php');
//서브
$strApt	=	new strAptPrice();

$kind	=	$_POST["kind"];

IF($kind)
{
	SWITCH($kind)
	{
		CASE "si" :
			$strVal =	$strApt->addr_si();
		BREAK;
		CASE "gu" :
			$strSi		=	$_POST["si"];
			$strVal =	$strApt->addr_gu($strSi);
		BREAK;
		CASE "dong" :
			$strSi		=	$_POST["si"];
			$strGu		=	$_POST["gu"];
			$strVal =	$strApt->addr_dong($strSi, $strGu);
		BREAK;
		CASE "apt_name" :
			$strDcode	=	$_POST["dcode"];
			$strVal =	$strApt->Apt_name($strDcode);

		BREAK;
		CASE "apt_area" :
			$strMgid	=	$_POST["mg_id"];
			$strVal =	$strApt->Apt_area($strMgid);
		BREAK;
		DEFAULT :
			$strVal =	$strApt->addr_si();
		BREAK;
	}
	$strRetCode = "OK";
	$strRetAlt = "";
} ELSE {
	$strRetCode = "X";
	$strVal = "";
	$strRetAlt = STR_REPLACE("+"," ",urlencode("일치하는 데이터가 없습니다."));
}

$objval = ARRAY("retcode"=>$strRetCode,"retalert"=>$strRetAlt,"retval"=>"","retkind"=>$kind,"retval"=>$strVal);
ECHO json_encode($objval);
sql_close($connect);
?>