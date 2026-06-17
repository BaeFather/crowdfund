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
	$kind =& $_POST["kind"];

	IF($kind == "save")
	{
		$strPost = ARRAY(
						ARRAY("SE","",""),ARRAY("S2","",""),ARRAY("ST","","Y"),ARRAY("STXT","",""),ARRAY("page","","Y"),
						ARRAY("idor","",""),ARRAY("recyn","","Y"),ARRAY("hid","","Y"),
						ARRAY("hpasswd","","Y"),ARRAY("cname","","Y"),ARRAY("hname","",""),ARRAY("hphone","",""),ARRAY("phmseq","",""),ARRAY("co_number1","",""),ARRAY("co_number2","",""),ARRAY("rname","",""),ARRAY("rphone","","")
				);
	} ELSEIF($kind == "update") {
		$strPost = ARRAY(
						ARRAY("SE","","Y"),ARRAY("S2","",""),ARRAY("ST","","Y"),ARRAY("STXT","",""),ARRAY("page","","Y"),
						ARRAY("idor","","Y"),ARRAY("recyn","","Y"),ARRAY("hid","","Y"),
						ARRAY("hpasswd","",""),ARRAY("cname","","Y"),ARRAY("hname","",""),ARRAY("hphone","",""),ARRAY("phmseq","",""),ARRAY("co_number1","",""),ARRAY("co_number2","",""),ARRAY("rname","",""),ARRAY("rphone","","")
				);
	} ELSE {
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	}

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

	/*아이디 조회*/
	$Query	  = "SELECT count(*) as CNT FROM hloan_member";
	$strWhere = " WHERE hid='".add_str($hid)."'";

	IF($idor)
	{
		$strWhere .= " AND hid<>'".add_str($idor)."'";
	}

	$Query .= $strWhere;

	$row	=	sql_fetch($Query,$connect);
	$intCnt	=	$row["CNT"];

	IF($intCnt > 0)
	{
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("이미 등록된 아이디입니다. 다른 아이디를 입력하여 주십시오.")),"retval"=>"");
		sql_close($connect_db);
		ECHO json_encode($objval);
		EXIT;
	}
	/* 아이디 조회 끝 */

	$gstrNdate	=	DATE("Y-m-d H:i:s");

	IF($kind == "save" || $kind == "update")
	{

		$strIFileName = fn_file_upload("i_file","/data/hellomember",ARRAY("","","",""),"single");

		$strColumn	=	ARRAY("hid","cname","hname","hphone","recyn","level","co_number1","co_number2","rname","rphone","ifile","section");
		$strValues	=	ARRAY($hid, urldecode($cname), urldecode($hname), $hphone, $recyn, "2", $co_number1, $co_number2, urldecode($rname), $rphone, $strIFileName[0],$ST);
		$strTable		=	"hloan_member_renew";

		IF($kind == "save")
		{
			$strColumn[] = "reg_date";
			$strValues[] = $gstrNdate;

			$strColumn[] = "login_cnt";
			$strValues[] = 0;

			$strColumn[] = "login_date";
			$strValues[] = "0000-00-00 00:00:00";
		}

		IF($phmseq)
		{
			$strColumn[] = "phmseq";
			$strValues[] = $phmseq;
		}

		IF($hpasswd)
		{
			$strColumn[] = "hpasswd";
			$strValues[] = sql_password($hpasswd);
		}

		$SeqName	=	"hmseq";
		$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);

		IF($kind == "save" && $level == "2")	// 총괄
		{
			fn_general_query_update("update",ARRAY("phmseq"),ARRAY($INSERT_ID),$strTable,$SeqName,replace_integer($INSERT_ID),"",$connect_db);
		}

		$strlink = "&S2=".$S2."&STXT=".$STXT."&page=".$page."&ST=".$ST;	// 추가 리턴변수

		SWITCH($kind)
		{
			CASE "save" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
			CASE "update" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
			CASE "del" : $strRet = fn_general_process_link($kind, "1", $strlink); BREAK;
		}
		sql_close($connect_db);
		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/hellomember_new/?".$strRet[1]);
		ECHO json_encode($objval);
		EXIT;
	}
?>