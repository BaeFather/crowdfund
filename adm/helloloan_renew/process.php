<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$gstrFileBoardUrl = "/data/helloloan";
?>
<?php
	$kind		=& $_POST["kind"];
	$section	 =& $_POST["section"];

	IF($kind == "update") {

		IF($section == "1")
		{
			$strPost = ARRAY(
								ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("S1","",""),ARRAY("S2","",""),ARRAY("S3","",""),ARRAY("S4","",""),
								ARRAY("Sdate","",""),ARRAY("Edate","",""),ARRAY("STXT","",""),
								ARRAY("si","","Y"),ARRAY("gu","",""),ARRAY("dong","","Y"),ARRAY("jibun","",""),
								ARRAY("apt_name","",""),ARRAY("apt_area","",""),ARRAY("dong2","","Y"),
								ARRAY("floor","","Y"),ARRAY("ho","","Y"),ARRAY("ddmoney","","Y"),ARRAY("maxbond","1","Y"),ARRAY("loankind","","Y"),ARRAY("lenmember","",""),
								ARRAY("auctionyn","","Y"),ARRAY("fees","","Y"),ARRAY("mm","","Y"),ARRAY("Interest","","Y"),ARRAY("ltv","","Y"),ARRAY("recyn_other","",""),ARRAY("mb_no","",""),ARRAY("arecyn","",""),ARRAY("aptcrdate","",""),ARRAY("atptot","",""),ARRAY("hmseq2","","Y")
						);
		}

		IF($section == "2")
		{
			$strPost = ARRAY(
								ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("S1","",""),ARRAY("S2","",""),ARRAY("S3","",""),ARRAY("S4","",""),
								ARRAY("Sdate","",""),ARRAY("Edate","",""),ARRAY("STXT","",""),
								ARRAY("lenmember","",""),
								ARRAY("lenphone","",""),ARRAY("lenother","",""),ARRAY("promember","",""),ARRAY("prophone","",""),ARRAY("proother","",""),
								ARRAY("ddmoney","","Y"),ARRAY("maxbond","1","Y"),ARRAY("loankind","","Y"),ARRAY("auctionyn","","Y"),ARRAY("fees","","Y"),ARRAY("Interest","","Y"),ARRAY("feesmoney","","Y"),ARRAY("mm","","Y"),ARRAY("aptcrdate","",""),ARRAY("atptot","",""),ARRAY("arecyn","","")
						);
		}
		IF($section == "3")
		{
			$strPost = ARRAY(
								ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("S1","",""),ARRAY("S2","",""),ARRAY("S3","",""),ARRAY("S4","",""),
								ARRAY("Sdate","",""),ARRAY("Edate","",""),ARRAY("STXT","",""),
								ARRAY("arecyn","",""),
								ARRAY("loan_jumin","",""),ARRAY("loan_addr","",""),ARRAY("purpose","",""),ARRAY("smsyn","",""),ARRAY("other","",""),ARRAY("conditions","","")
						);
		}
	} ELSEIF($kind == "del") {
			$strPost = ARRAY(
							ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("S1","",""),ARRAY("S2","",""),ARRAY("S3","",""),ARRAY("S4","",""),ARRAY("STXT","","")
						);
	} ELSE {
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	}

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][1] > 0)
		{
			FOR($j=0;$j<COUNT($_POST[$strPost[$i][0]]);$j++)
			{
				IF($j == 0) { ${$strPost[$i][0]} = ""; }
				IF($j > 0)
				{
					${$strPost[$i][0]} .=  ",";
				}
				${$strPost[$i][0]} .= replace_integer(urldecode($_POST[$strPost[$i][0]][$j]));
			}

		} ELSE {
			IF($strPost[$i][2] == "Y")
			{
				IF($_POST[$strPost[$i][0]]<>"")
				{
					${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
				} ELSE {
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오".$strPost[$i][0])),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
			}
		}
	}
	$gstrNdate	=	DATE("Y-m-d H:i:s");

	IF($kind == "update")
	{
		IF($section == "1")
		{
			$mm			=	replace_integer($mm) * 10000;

			$strColumn	= ARRAY(
								"si","gu","dg","aptname","aptarea","dong",
								"floor","ho","ddmoney","maxbond","loankind",
								"auctionyn","fees","bcode","kbmoney","Interest","ltv","mb_no","arecyn",
								"aptcrdate","atptot","lenmember","hmseq2","jibun"

							);

			$strValues = ARRAY(
							$si, $gu, $dong, $apt_name,$apt_area,$dong2,
							$floor, $ho, replace_integer($ddmoney), $maxbond, $loankind,
							$auctionyn, $fees, $bcode, $mm, $Interest,$ltv,$mb_no,$arecyn,
							$aptcrdate,$atptot,$lenmember, $hmseq2, $jibun
						);

			IF($recyn_other)
			{
				$strColumn[] = "recyn_other";
				$strValues[] = $recyn_other;
			}

			IF($arecyn&& $mb_no)
			{
				$strColumn[] = "votdate";
				$strValues[] = $gstrNdate;
			}
		}
		IF($section == "2")
		{
			$mm			=	replace_integer($mm) * 10000;
			$strColumn	= ARRAY(
								"lenmember","lenphone","lenother","promember",
								"prophone","proother","okddmoney","okmaxbond","okloankind",
								"okauctionyn","okfees","okfeesmoney","okkbmoney","okInterest","okltv","arecyn","recyn_other2","auth_date","aptcrdate","atptot"

							);

			$strValues = ARRAY(
							$lenmember, $lenphone, $lenother, $promember,
							$prophone, $proother, replace_integer($ddmoney), $maxbond, $loankind,
							$auctionyn, $fees, $feesmoney, $mm, $Interest,$ltv,$arecyn,$recyn_other2,$auth_date,$aptcrdate,$atptot

						);
			IF($arecyn == "7") //최종승인이라면
			{
				$strColumn[] = "recyn";
				$strValues[] = "7";

				$strColumn[] = "votdate2";
				$strValues[] = $gstrNdate;
			}
		}

		IF($section == "3")
		{
			$inFileCnt = 10;
			$strSFileName = fn_file_upload_new("s_file",$gstrFileBoardUrl,ARRAY("","","",""),$inFileCnt);

			$conditions = STR_REPLACE("+"," ",$conditions);
			$loan_addr = STR_REPLACE("+"," ",$loan_addr);
			$other = STR_REPLACE("+"," ",$other);

			$strColumn	= ARRAY(
								"recyn","loan_jumin","loan_addr","purpose",
								"smsyn","other","conditions","sfile"

							);

			$strValues = ARRAY(
							$recyn, $loan_jumin, $loan_addr, $purpose,
							$smsyn, $other, $conditions, $strSFileName

						);
		}
	}

	$strTable	=	"hloan_content_renew";
	$SeqName	=	"hcseq";

	$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);

	IF($kind == "del")
	{
		/*댓글삭제*/
	//	fn_general_query_update($kind,"","","hloan_comment_renew","req_idx",replace_integer($SE),"",$connect_db);
	}

	sql_close($connect_db);

	$strlink = "&S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&Sdate=".$Sdate."&Edate=".$Edate."&STXT=".$STXT."&page=".$page;	// 추가 리턴변수

	SWITCH($kind)
	{
		CASE "save" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
		CASE "update" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
		CASE "del" : $strRet = fn_general_process_link($kind, "1", $strlink); BREAK;
	}
	$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/helloloan_renew/?".$strRet[1]);
	ECHO json_encode($objval);
	EXIT;
?>