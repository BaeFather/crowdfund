<?php
include_once("_common.php");

$obj	=	$_POST["obj"];

IF(!$obj)
{
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")));
	ECHO json_encode($objval);
}
$retval = price_cutting(STR_REPLACE(",","",$obj)*10000);
IF($retval == 0)
{
	$retval = "";
} ELSE {
	$retval .= "원";
}

$objval = ARRAY("retcode"=>"OK","retval"=>urlencode($retval));
ECHO json_encode($objval);
EXIT;
?>