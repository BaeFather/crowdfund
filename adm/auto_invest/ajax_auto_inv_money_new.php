<?php
include_once("_common.php");

$ai_grp_idx		=	$_POST["ai_grp_idx"];
$strVal = get_auto_real_money_new($ai_grp_idx);

$objval = ARRAY("retcode"=>"OK","retalert"=>"","retval"=>$strVal);
echo json_encode($objval);
?>