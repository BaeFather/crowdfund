<?php
include_once('./_common.php');

$RT		=	$_POST["RT"];
$passwd	=	$_POST["passwd"];

IF(!$RT || !$passwd || STRLEN($RT) < 10)
{
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("정상적인 접근이 아닙니다.")),"retval"=>"");
	ECHO json_encode($objval);
	sql_close($connect_db);
	EXIT;
}

$intRT1 = SUBSTR($RT,0,10);
$intRT2 = SUBSTR($RT,10,10);

$Query = "SELECT t2.ridx,t3.passwd,t2.reg_time,t2.end_time FROM
          cf_product_admin_report t1 LEFT JOIN cf_product_admin_report_send t2
		  ON t1.pidx=t2.pidx
		  LEFT JOIN cf_product_admin_user t3
		  ON t2.midx=t3.midx
		  WHERE t2.send_time='".addslashes($intRT1)."' AND t2.midx='".addslashes($intRT2)."'";

$Result = sql_query($Query);

IF($Row=sql_fetch_array($Result))
{
	$intRidx	=	$Row["ridx"];
	$strPasswd	=	$Row["passwd"];
	$reg_time	=	$Row["reg_time"];
	$end_time	=	$Row["end_time"];
	sql_free_result($Result);
}

IF(!$strPasswd || ($strPasswd <> $passwd))
{
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("정상적인 접근이 아닙니다..")),"retval"=>"");
	ECHO json_encode($objval);
	sql_close($connect_db);
	EXIT;
}

IF($end_time > 0)
{
	IF($end_time < time())
	{
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("페이지가 종료 되었습니다.")),"retval"=>"");
		ECHO json_encode($objval);
		sql_close($connect_db);
		EXIT;
	} ELSE {
		set_cookie("REPORT_ID",$RT,1800);
		sql_close($connect_db);
	}
} ELSE {

	IF($intRidx)
	{
		set_cookie("REPORT_ID",$RT,1800);

		$Query = "UPDATE cf_product_admin_report_send SET
						 reg_time='".time()."',
						 end_time='".(time()+2592000)."',
						 ipaddr='".$_SERVER["REMOTE_ADDR"]."'
						 WHERE ridx='".addslashes($intRidx)."'";

		sql_query($Query);
		sql_close($connect_db);
	} ELSE {
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("페이지가 종료 되었습니다.")),"retval"=>"");
		ECHO json_encode($objval);
		sql_close($connect_db);
		EXIT;
	}
}
$objval = ARRAY("retcode"=>"OK","retalert"=>"","retval"=>"/hello_report/?RT=".$RT);
ECHO json_encode($objval);
EXIT;
?>