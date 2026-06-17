<?php
include_once('_common.php');

$strPost = ARRAY("reg_num");

FOR($i=0;$i<COUNT($strPost);$i++)
{
  ${$strPost[$i]} = $_POST[$strPost[$i]];
}

IF(!$reg_num)
{
  $objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오 : ".$strPost[$i][0])),"ret1"=>"","ret2"=>"");
	ECHO json_encode($objval);
	EXIT;
}

$Query = "
        SELECT IFNULL(t1.CNT,0)  as CNT1, IFNULL(t2.CNT,0) as CNT2 FROM
        (SELECT '1' as kind, COUNT(*) as CNT FROM g5_member WHERE member_type='2' AND mb_co_reg_num = '".$reg_num."') t1
        LEFT join
        (SELECT '1' as kind, COUNT(*) as CNT FROM g5_member_drop WHERE member_type='2' AND mb_co_reg_num = '".$reg_num."') t2
        ON t1.kind=t2.kind";

$Row =sql_fetch($Query);

$intCnt1  = $Row["CNT1"];
$intCnt2  = $Row["CNT2"];


$objval = ARRAY("retcode"=>"OK","retalert"=>"","ret1"=>$intCnt1,"ret2"=>$intCnt2);
ECHO json_encode($objval);
sql_close($connectdb);
EXIT;
?>
