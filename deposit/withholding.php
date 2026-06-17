<?php
include_once("_common.php");
/* 처리 페이지*/

$strPost = ARRAY(
					ARRAY("member_type","","Y"),ARRAY("mb_name","","Y"),ARRAY("mb_jumin","","Y"),ARRAY("mb_email","","Y"),
					ARRAY("s_date","","Y"),ARRAY("e_date","","Y"),ARRAY("rkind","","Y"),ARRAY("content","",""),ARRAY("mb_no","","Y")
			);

FOR($i=0;$i<COUNT($strPost);$i++)
{
	IF($strPost[$i][2] == "Y")
	{
		IF($_POST[$strPost[$i][0]]<>"")
		{
			${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
		} ELSE {
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}
	} ELSE {
		${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
	}
}

$gstrNdate	=	DATE("Y-m-d H:i:s");

IF($s_date > $e_date)
{
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("시작일은 종료일보다 클수 없습니다.")),"retval"=>"");
	ECHO json_encode($objval);
	EXIT;
}

IF($member_type=="1")	//개인
{
	$link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);
	$Query = "SELECT regist_number FROM member_private WHERE mb_no='".$mb_no."'";
	$Row = sql_fetch($Query, "", $link2);

	$mb_jumin = $Row["regist_number"];
	sql_close($link2);
}

$Query = "INSERT INTO cf_withholding_request
		  (member_type, mb_no, mb_name, mb_jumin, mb_email, s_date, e_date, rkind, content, reg_date,recyn)
		  VALUES
		  ('".$member_type."','".$mb_no."','".$mb_name."','".$mb_jumin."','".$mb_email."','".$s_date."','".$e_date."','".$rkind."','".$content."','".$gstrNdate."','N')";

sql_query($Query);

sql_close();

$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("원천징수영수증 신청이 정상 등록 되었습니다.")),"retval"=>"/deposit/deposit.php");
ECHO json_encode($objval);
EXIT;
?>