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
						ARRAY("midx","","Y"),ARRAY("mlevel","","Y"),ARRAY("obj","",""),ARRAY("SE","","Y"),ARRAY("tindex","","")
					);

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][2] == "Y")
		{
			IF($_POST[$strPost[$i][0]]<>"")
			{
				${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
			} ELSE {
				$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오.($i)")),"retindex"=>"","votyn"=>"","retyn"=>"");
				ECHO json_encode($objval);
				EXIT;
			}
		} ELSE {
			${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
		}

	}

	$intSeqName	=	"idx";
	$strTable	=	"hloan_admin_member_vote";
	$strWhere	=	" WHERE midx='".add_str($midx)."' AND hcseq='".add_str($SE)."'";
	$strOrder	=	$intSeqName;
	$strColumn	=	ARRAY($intSeqName);
	$intLimit1	=	0;
	$intLimit2	=	1;
	$intStrlen	=	100;

	$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

	IF($rowView[0][$intSeqName])
	{
		$kind = "update";
	} ELSE {
		$kind = "save";
	}
	$kind = "save";

	if ($gubun=="심의번호") {
		$etc1 = $obj;
	} else {
		$etc1 = "";
	}

	UNSET($strColumn);
	$strColumn	= ARRAY(
						"midx","hcseq","votyn" , "gubun" , "etc1"
					);

	$strValues = ARRAY(
					$midx, $SE, $obj, $gubun, $etc1

				);

	IF($kind == "save")
	{
		$strColumn[] = "reg_date";
		$strValues[] = DATE("Y-m-d H:i:s");
	}

	$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$intSeqName,replace_integer($rowView[0][$intSeqName]),"",$connect_db);

	IF($mlevel == "2")
	{
		IF($obj == 9 || $obj == 8)
		{
			$rettxt = "승인";
			$retyn  = "Y";
		} ELSE {
			$rettxt = "반려";
			$retyn  = "B";
		}
	} ELSE {
		$strTable	=	"hloan_admin_member_vote t1 LEFT JOIN hloan_admin_member t2 ON t1.midx=t2.midx";
		$strWhere	=	" WHERE t1.hcseq='".add_str($SE)."' AND t2.mb_level='1'";
		$strOrder	=	"t1.midx";
		$strColumn	=	"";
		$strQuery   =   " IFNULL(SUM(t1.votyn),0) as TSUM";
		$intLimit1	=	0;
		$intLimit2	=	1;
		$intStrlen	=	100;

		$rowView = fr_board_view(ARRAY("TSUM"),$strTable,$strQuery,$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

		IF($rowView[0]["TSUM"])
		{
			IF($rowView[0]["TSUM"] == 9)
			{
				$rettxt = "승인";
				$retyn  = "Y";
			} ELSE {
				$rettxt = "심사중";
				$retyn  = "M";
			}
		}
	}

	IF(!$retyn)
	{
		$rettxt = "대기";
		$retyn  = "N";
	}

	//fn_general_query_update("update",ARRAY("recyn"),ARRAY($retyn),"hloan_content","hcseq",replace_integer($SE),"",$connect_db);

	sql_close($connect_db);

	$objval = ARRAY("retcode"=>"OK","retalert"=>"","retindex"=>$tindex,"votyn"=>$obj,"retyn"=>$rettxt);
	ECHO json_encode($objval);
	EXIT;
?>