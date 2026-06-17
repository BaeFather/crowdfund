<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
include_once('./hellosetting.class.php');
?>
<?php
	$kind =& $_POST["kind"];

	IF($kind == "save")
	{
		$strPost = ARRAY(
						ARRAY("S2","",""),ARRAY("STXT","",""),ARRAY("page","","Y"),
						ARRAY("title","",""),ARRAY("hmseq","","Y"),ARRAY("addr_si","","Y"),ARRAY("addr_yn","",""),
						ARRAY("addr_gu","",""),ARRAY("rec_date","","Y"),ARRAY("recyn","",""),ARRAY("ltvs","",""),ARRAY("ltvl","",""),ARRAY("ms","",""),ARRAY("ml","",""),ARRAY("period","","")
				);
	} ELSEIF($kind == "update") {
		$strPost = ARRAY(
						ARRAY("SE","","Y"),ARRAY("SE2","1","Y"),ARRAY("S2","",""),ARRAY("STXT","",""),ARRAY("page","","Y"),
						ARRAY("title","",""),ARRAY("hmseq","","Y"),ARRAY("addr_si","","Y"),ARRAY("addr_yn","","Y"),
						ARRAY("addr_gu","",""),ARRAY("rec_date","","Y"),ARRAY("recyn","",""),ARRAY("ltvs","",""),ARRAY("ltvl","",""),ARRAY("ms","",""),ARRAY("ml","",""),ARRAY("period","","")
				);
	} ELSEIF($kind == "addr_si") {

		$strPost = ARRAY(ARRAY("addr_yn","","Y"),ARRAY("addr_si","",""));

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
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오".$i)),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
			}
		}
	}

	$gstrNdate	=	DATE("Y-m-d H:i:s");

	IF($kind == "save" || $kind == "update")
	{
		IF(COUNT($addr_gu) > 0)
		{
			FOR($i=0;$i<COUNT($addr_gu);$i++)
			{
				IF($i > 0)
				{
					$addr_guVal .= ",";
				}
				$addr_guVal .= $addr_gu[$i];
			}
		}

		$strColumn	=	ARRAY("mb_id","hmseq","title","addr_si","addr_yn","addr_gu","rec_date","recyn","period");
		$strValues	=	ARRAY($_SESSION["ss_mb_id"], $hmseq, $title, $addr_si, $addr_yn, $addr_guVal, $rec_date, $recyn,$period);
		$strTable		=	"hloan_content_setting";

		IF($kind == "save")
		{
			$strColumn[] = "reg_date";
			$strValues[] = $gstrNdate;

		}
		$SeqName	=	"hcsseq";

		$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);

		$strTable2		=	"hloan_content_setting_slave";
		$SeqName2		=	"hcssseq";

		IF($kind == "update" || $kind == "save")
		{
			/* history 기록*/
			$Q1 = "INSERT INTO hloan_content_setting_history (hcsseq, mb_id, hmseq, title, addr_si, addr_yn, addr_gu, rec_date, reg_date, update_date, recyn,period)
			SELECT hcsseq, mb_id, hmseq, title, addr_si, addr_yn, addr_gu, rec_date, reg_date, '".$gstrNdate."', recyn,period FROM hloan_content_setting WHERE hcsseq='".$INSERT_ID."'";
			sql_query($Q1);

			$Query = "SELECT seq FROM hloan_content_setting_history WHERE hcsseq='".$INSERT_ID."' ORDER BY seq DESC LIMIT 0, 1";
			$Row = sql_fetch($Query);

			$intSeq = $Row["seq"];

			IF($kind == "update")
			{
				/* slave 삭제*/
				fn_general_query_update("del","","",$strTable2,$SeqName,replace_integer($INSERT_ID),"",$connect_db);
			}

			$strltvskind = false;
			FOR($i=0;$i<COUNT($ltvs);$i++)
			{
				/* slave 저장 */
				$strColumn2	=	ARRAY("hcsseq","ltvs","ltvl","ms","ml");
				$strValues2	=	ARRAY($INSERT_ID, $ltvs[$i], $ltvl[$i], $ms[$i], $ml[$i]);

				IF($ltvl[$i] && $ms[$i] && $ml[$i])
				{
				fn_general_query_update("save",$strColumn2,$strValues2,$strTable2,$SeqName2,replace_integer($SE2),"",$connect_db);
				}
				$strltvskind = true;
			}

			IF($strltvskind == true)
			{
				$Q2 = "INSERT INTO hloan_content_setting_slave_history (hcsseq,seq,ltvs,ltvl,ms,ml)
					   SELECT hcsseq,'".$intSeq."',ltvs,ltvl,ms,ml FROM hloan_content_setting_slave WHERE hcsseq='".$INSERT_ID."'";
				sql_query($Q2);
			}
		}

		$strlink = "&S2=".$S2."&STXT=".$STXT."&page=".$page;	// 추가 리턴변수

		SWITCH($kind)
		{
			CASE "save" : $strRet = fn_general_process_link2($kind, "2", $strlink); BREAK;
			CASE "update" : $strRet = fn_general_process_link2($kind, "2", $strlink); BREAK;
			CASE "del" : $strRet = fn_general_process_link2($kind, "1", $strlink); BREAK;
		}
		sql_close($connect_db);
		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/hellosetting/?".$strRet[1]);
		ECHO json_encode($objval);
		EXIT;

	} ELSEIF($kind == "addr_si") {
		$page = 1;
		$num_per_page = 10000;
		$objval = $ClassHelloSetting->fn_addr_gu($addr_si);

		sql_close($connect_db);
		ECHO json_encode(ARRAY("retval"=>$objval));
		EXIT;
	}
?>