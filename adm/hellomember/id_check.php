<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
?>
<?php
	$strPost = ARRAY(
					ARRAY("idor","",""),ARRAY("id","","Y")
			);

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][1] > 0)
		{
			${$strPost[$i][0]} = $_POST[$strPost[$i][0]][0];
		} ELSE {
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
	}

	$Query	  = "SELECT count(*) as CNT FROM hloan_member";
	$strWhere = " WHERE hid='".add_str($id)."'";

	IF($idor)
	{
		$strWhere .= " AND hid<>'".add_str($idor)."'";
	}

	$Query .= $strWhere;

	$row	=	sql_fetch($Query,$connect);
	$intCnt	=	$row["CNT"];

	sql_close($connect);

	IF($intCnt > 0)
	{
		$strRetCode = "X";
		$strTxt = "이미 등록된 아이디";
	} ELSE {
		$strRetCode = "OK";
		$strTxt = "사용가능한 아이디";
	}
	$objval = ARRAY("retcode"=>$strRetCode,"retalert"=>STR_REPLACE("+"," ",urlencode($strTxt)),"retval"=>"");
	ECHO json_encode($objval);
	EXIT;
?>