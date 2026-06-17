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
							ARRAY("SE","",""),ARRAY("page","",""),ARRAY("STXT","",""),ARRAY("SC","",""),ARRAY("section","",""),
							ARRAY("sdate","","Y"),ARRAY("edate","","Y"),ARRAY("linkurl","",""),ARRAY("target","",""),
							ARRAY("recyn","","Y"),ARRAY("reg_date","","Y"),ARRAY("sort_id","",""),ARRAY("title","","Y"),ARRAY("wr_content","",""),ARRAY("mainyn","",""),ARRAY("wr_content2","",""),ARRAY("mainmyn","","")
					);
	} ELSEIF($kind == "update") {
		$strPost = ARRAY(
							ARRAY("SE","","Y"),ARRAY("page","",""),ARRAY("STXT","",""),ARRAY("SC","",""),
							ARRAY("sdate","","Y"),ARRAY("edate","","Y"),ARRAY("linkurl","",""),ARRAY("target","",""),
							ARRAY("recyn","","Y"),ARRAY("reg_date","","Y"),ARRAY("sort_id","",""),ARRAY("title","","Y"),ARRAY("wr_content","",""),ARRAY("mainyn","",""),ARRAY("wr_content2","",""),ARRAY("mainmyn","","")
					);
	} ELSEIF($kind == "del") {
		$strPost = ARRAY(
							ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("STXT","","")
					);
	} ELSE {
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	}

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][1] > 0)
		{
			$strPostTarget = "";
			FOR($j=0;$j<COUNT($_POST[$strPost[$i][0]]);$j++)
			{
				$strPostVal = "";
				IF($j > 0)
				{
					$strPostTarget .=  ":";
					//${$strPost[$i][0]} .=  ",";
				}
				$strPostVal		 =& $_POST[$strPost[$i][0]][$j];
				$strPostTarget	.= replace_integer($strPostVal);
				//${$strPost[$i][0]} .= $_POST[$strPost[$i][0]][$j];
			}
			${$strPost[$i][0]} = $strPostTarget;

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
	$gstrNdate = DATE("Y-m-d H:i:s");
//	print_r($_FILES);
//	exit;
//	echo "<BR>";

	$strIFileName = fn_file_upload("i_file","/data/fevent",ARRAY("","","",""),"multi");

	IF(!$reg_date) { $reg_date = $gstrNdate; }
	IF(!$sort_id) { $sort_id = 0; }

	IF($kind == "save" || $kind == "update")
	{
		IF($kind =="update")
		{
			IF(!$SE)
			{
				$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
				ECHO json_encode($objval);
				EXIT;
			}
		}

		$strColumn	= ARRAY(
							"title","content","reg_date",
							"sdate","edate","ifile","linkurl","target",
							"sort_id","recyn","mainyn","contentm","mainmyn"
						);

		$strValues = ARRAY(
							urldecode($title), urldecode($wr_content),$reg_date,
							$sdate, $edate, $strIFileName[0], urldecode($linkurl),$target,
							$sort_id, $recyn,$mainyn, urldecode($wr_content2),$mainmyn
						);

		IF($kind == "save")
		{
			$strColumn[] = "section";
			$strValues[] = $section;
		}

		IF($kind == "update")
		{
			$strColumn[] = "update_date";
			$strValues[] = DATE("Y-m-d H:i:s");
		}

		$strTable		=	"hello_board";
		$SeqName	=	"idx";


		$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);
		sql_close($connect_db);

		$strlink = "&STXT=".$STXT."&page=".$page."&SC=".$SC;	// 추가 리턴변수

		SWITCH($kind)
		{
			CASE "save" : $strRet = fn_general_process_link2($kind, "2", $strlink); BREAK;
			CASE "update" : $strRet = fn_general_process_link2($kind, "2", $strlink); BREAK;
			CASE "del" : $strRet = fn_general_process_link2($kind, "1", $strlink); BREAK;
		}
		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/hevent/?".$strRet[1]);
		ECHO json_encode($objval);
		EXIT;

	} ELSEIF($kind == "del") {

		$strTable		=	"hello_board";
		$SeqName	=	"idx";

		$INSERT_ID = fn_general_query_update($kind,"","",$strTable,$SeqName,replace_integer($SE),"",$connect_db);

		$strlink = "&STXT=".$STXT."&page=".$page."&SC=".$SC;	// 추가 리턴변수

		SWITCH($kind)
		{
			CASE "save" : $strRet = fn_general_process_link2($kind, "2", $strlink); BREAK;
			CASE "update" : $strRet = fn_general_process_link2($kind, "2", $strlink); BREAK;
			CASE "del" : $strRet = fn_general_process_link2($kind, "1", $strlink); BREAK;
		}
		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/hevent/?".$strRet[1]);
		ECHO json_encode($objval);
		EXIT;
	}
?>