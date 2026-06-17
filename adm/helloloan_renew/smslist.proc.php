<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
include_once('../../lib/sms.lib.php');

$gstrFileBoardUrl = "/data/helloloan";
?>
<?php
	$page	=	clean_xss_tags($_POST["page"]);
	$S 		=	clean_xss_tags($_POST["S"]);	// section 1 일반 2 예약
  $kind = clean_xss_tags($_POST["kind"]);
	$SE 	= clean_xss_tags($_POST["SE"]);

	IF($kind == "save")
	{
		$reg_date	=	clean_xss_tags($_POST["reg_date"]);
		$reg_h	=	clean_xss_tags($_POST["reg_h"]);
		$reg_i	=	clean_xss_tags($_POST["reg_i"]);

		IF($S == "2") // 예약발송
		{
			IF(!$reg_date)
			{
				sql_close($connect_db);
				$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("예약일을 지정하여 주십시오.")),"retval"=>"");
				ECHO json_encode($objval);
				EXIT;
			}

			$gstrNdate	 	= $reg_date." ".SPRINTF("%02d",$reg_h).":".SPRINTF("%02d",$reg_i).":00";
		} ELSE {
			$intTime 			= TIME();
			$gstrNdate		= DATE("Y-m-d H:i:s",$intTime+600);
		}

		$inFileCnt = 1;
		$strSFileName = fn_file_upload_new("s_file",$gstrFileBoardUrl,ARRAY("","","",""),$inFileCnt);

		IF(!$strSFileName)
		{
			sql_close($connect_db);
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("첨부파일이 없습니다. 다시 시도하여 주십시오")),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}

		$mb_name = fn_g5_member($_SESSION["ss_mb_id"]);

		$strColumn	= ARRAY(
							"mb_id","mb_name","reg_date","ifile","section"

						);

		$strValues = ARRAY(
						$_SESSION["ss_mb_id"], $mb_name[1], $gstrNdate, $strSFileName, $S
					);


		$strTable	=	"hloan_content_smssend";
		$SeqName	=	"idx";
		$kind		=	"save";

		$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);

		IF($strSFileName)
		{
			fn_helloloan_sms_send($gstrFileBoardUrl."/".$strSFileName, $gstrNdate);
		}

	} ELSEIF($kind == "del") {

		IF(!$SE)
		{
			sql_close($connect_db);
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}

		$strColumn = ARRAY("ifile","reg_date");
		$strQuery = "";
		$strWhere = "WHERE idx='".add_str($SE)."'";
		$strOrder = "idx desc";
		$strTable = "hloan_content_smssend";
		$strlimit1 = "0";
		$strlimit2 = "1";
		$SeqName	=	"idx";

		$qstr = "?S=".$S;

		IF($S == "1")
		{
			$strThTxt = "발송일자";
			$strBtnTxt = "문자발송";
			$intCols = "4";
		} ELSEIF($S == "2") {
			$strThTxt = "예약일자";
			$strBtnTxt = "예약문자등록";
			$intCols = "5";
		}

		$rowView = fr_board_view($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$strlimit1,$strlimit2,"2000");

		$strIfile = $rowView[0]["ifile"];
		$strRegDate = $rowView[0]["reg_date"];


		IF(!$strIfile)
		{
			sql_close($connect_db);
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}

		fn_helloloan_sms_del($gstrFileBoardUrl."/".$strIfile, $strRegDate);

		fn_general_query_update($kind,"","",$strTable,$SeqName,replace_integer($SE),"",$connect_db);
	}

	FUNCTION fn_helloloan_sms_send($obj, $gstrNdate)
	{
		$row = 1;
		if (($handle = fopen($_SERVER["DOCUMENT_ROOT"].$obj, "r")) !== FALSE) {

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				IF($row > 1)
				{
					IF(SUBSTR($data[0],0,1) <> "0")
					{
						$data[0] = "0".$data[0];
					}
					$rowPhone[$row-2] = $data[0];
					$rowMsg[$row-2] = ICONV("EUC-KR","UTF-8",$data[1]);
				}
				$row++;
			}
			fclose($handle);
		}

		FOR($i=0;$i<COUNT($rowPhone);$i++)
		{
			$retval = unit_sms_send("15885210", $rowPhone[$i], $rowMsg[$i], $gstrNdate);
		}
	}

	FUNCTION fn_helloloan_sms_del($obj, $gstrNdate)
	{
		global $link3;
		$row = 1;
		if (($handle = fopen($_SERVER["DOCUMENT_ROOT"].$obj, "r")) !== FALSE) {

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				IF($row > 1)
				{
					IF(SUBSTR($data[0],0,1) <> "0")
					{
						$data[0] = "0".$data[0];
					}
					$rowPhone[$row-2] = $data[0];
					$rowMsg[$row-2] = ICONV("EUC-KR","UTF-8",$data[1]);
				}
				$row++;
			}
			fclose($handle);
		}

		FOR($i=0;$i<COUNT($rowPhone);$i++)
		{
			UNSET($idx);
			$Query = "SELECT idx FROM cf_Msg_Tran WHERE Phone_No='".add_str($rowPhone[$i])."' AND Message='".add_str($rowMsg[$i])."' AND Send_time='".add_str($gstrNdate)."'";
			$Result = sql_query($Query, G5_DISPLAY_SQL_ERROR, $link3);

			IF($Row=sql_fetch_array($Result))
			{
				$idx = $Row["idx"];
			}
			IF($idx)
			{
				$Query = "DELETE FROM cf_Msg_Tran WHERE idx='".add_str($idx)."'";
				sql_query($Query, G5_DISPLAY_SQL_ERROR, $link3);
			}
		}

		file_del($_SERVER["DOCUMENT_ROOT"].$obj);
	}


	sql_close($connect_db);

	$strlink = "&S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&Sdate=".$Sdate."&Edate=".$Edate."&STXT=".$STXT."&page=".$page;	// 추가 리턴변수

	$strRet = fn_general_process_link($kind, "2", $strlink);

	$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("파일이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/helloloan_renew/smslist.php?page=".$page."&S=".$S);
	ECHO json_encode($objval);
	EXIT;
?>
