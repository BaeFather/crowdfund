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
							ARRAY("SE","",""),ARRAY("page","","Y"),ARRAY("S1","",""),ARRAY("S2","",""),ARRAY("STXT","",""),
							ARRAY("product_idx","","Y"),ARRAY("laddr","","Y"),ARRAY("pname","","Y"),ARRAY("crating","","Y"),ARRAY("comday","","Y"),ARRAY("hholds","","Y"),
							ARRAY("ddmoney","","Y"),ARRAY("bsmoney","","Y"),ARRAY("mdate","",""),ARRAY("kbarea","","Y"),ARRAY("kbprice","","Y"),
							ARRAY("kbllimit","","Y"),ARRAY("kbcharter","","Y"),ARRAY("examount","1","Y"),ARRAY("maxbond","1","Y"),ARRAY("ltvmoney","","Y"),
							ARRAY("ltvkind","","Y"),ARRAY("rowner","",""),ARRAY("tenant","",""),ARRAY("content","",""),ARRAY("SC","",""),ARRAY("mb_no","",""),ARRAY("productyn","",""),ARRAY("recyn","",""),ARRAY("mkind","","Y"),ARRAY("loankind","",""),ARRAY("loanother","",""),ARRAY("vdate","",""),ARRAY("hellobase","",""),ARRAY("hellofee","",""),ARRAY("honumber","",""),ARRAY("kbquote","",""),ARRAY("skind","",""),ARRAY("fees","","Y")
					);
	} ELSEIF($kind == "update") {
		$strPost = ARRAY(
							ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("S1","",""),ARRAY("S2","",""),ARRAY("STXT","",""),
							ARRAY("product_idx","","Y"),ARRAY("laddr","","Y"),ARRAY("pname","","Y"),ARRAY("crating","","Y"),ARRAY("comday","","Y"),ARRAY("hholds","","Y"),
							ARRAY("ddmoney","","Y"),ARRAY("bsmoney","","Y"),ARRAY("mdate","",""),ARRAY("kbarea","","Y"),ARRAY("kbprice","","Y"),
							ARRAY("kbllimit","","Y"),ARRAY("kbcharter","","Y"),ARRAY("examount","1","Y"),ARRAY("maxbond","1","Y"),ARRAY("ltvmoney","","Y"),
							ARRAY("ltvkind","","Y"),ARRAY("rowner","",""),ARRAY("tenant","","  "),ARRAY("content","",""),ARRAY("SC","",""),ARRAY("mb_no","",""),ARRAY("productyn","",""),ARRAY("recyn","",""),ARRAY("mkind","","Y"),ARRAY("loankind","",""),ARRAY("loanother","",""),ARRAY("vdate","",""),ARRAY("hellobase","",""),ARRAY("hellofee","",""),ARRAY("honumber","",""),ARRAY("kbquote","",""),ARRAY("skind","",""),ARRAY("fees","","Y"),
							ARRAY("pcp_company","","")
					);
	} ELSEIF($kind = "del") {
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
			/*
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
			*/
		}
	}
	$gstrNdate	=	DATE("Y-m-d H:i:s");

	IF($kind == "save" || $kind == "update")
	{

		$jumin = replace_integer($jumin);
		$encJumin = masterEncrypt($jumin, false);
$examount = str_replace(":0", "" , $examount);
$maxbond = str_replace(":0", "" , $maxbond);

		$strColumn	= ARRAY(
							"product_idx","laddr","pname","crating","comday",
							"hholds","ddmoney","bsmoney","mdate","kbarea",
							"kbprice","kbllimit","kbcharter","examount","maxbond",
							"ltvmoney","ltvkind","rowner","tenant","content",
							"mkind","loankind","loanother","vdate","hellobase", "add_hellobase",
							"hellofee","honumber","kbquote","skind","fees",
							"kb_mg_id", "kb_ju_seri", "pcp_company",
							"pname_E_first", "pname_E_last", "pphone1",
							"dambo_pphone", "dambo_pname", "loan_for",
							"pcp_comp_addr_post", "pcp_comp_addr", "pcp_comp_addr2",
							"land_yn", "sale_per", "regist_number" ,"ltvmoney2",
							"hm_fees", "house_deposit", "pcp_job_group",
							"kb_mm_sil", "kb_mm_sil_date", "kb_date", "hloan_end_date", "laddr_num"
						);

		$strValues = ARRAY(
						$product_idx, $laddr, $pname, $crating, $comday,
						$hholds, replace_integer($ddmoney), replace_integer($bsmoney), $mdate, $kbarea,
						replace_integer($kbprice), replace_integer($kbllimit), replace_integer($kbcharter), $examount, $maxbond,
						$ltvmoney, $ltvkind, $rowner, $tenant, $content,
						$mkind, $loankind, $loanother, $vdate, $hellobase, $add_hellobase,
						replace_integer($hellofee),$honumber,$kbquote,$skind,$fees,
						$kb_mg_id, $kb_ju_seri, $pcp_company,
						$pname_E_first , $pname_E_last, $pphone1,
						$dambo_pphone, $dambo_pname, $loan_for,
						$pcp_comp_addr_post, $pcp_comp_addr, $pcp_comp_addr2,
						$land_yn, $sale_per, $encJumin, $ltvmoney2,
						$hm_fees, replace_integer($house_deposit), $pcp_job_group,
						replace_integer($kb_mm_sil), $kb_mm_sil_date, $kb_date, $hloan_end_date, $cert_num
					);

		IF($kind == "save")
		{
			$strColumn[] = "reg_date";
			$strValues[] = $gstrNdate;

			$strColumn[] = "ipaddr";
			$strValues[] = $gstrRemoteaddr;

			$recyn = "N";
		}
		IF($hmseq)
		{
			$strColumn[] = "hmseq";
			$strValues[] = $hmseq;
		}
		IF($recyn)
		{
			$strColumn[] = "recyn";
			$strValues[] = $recyn;
		}
		IF($mb_no)
		{
			$strColumn[] = "mb_no";
			$strValues[] = $mb_no;
		}
		//IF($productyn)
		//{
		$strColumn[] = "productyn";
		$strValues[] = $productyn;
		//}
	}

	$strTable	=	"hloan_content";
	$SeqName	=	"hcseq";

	$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);

	if ($kind == "update") {  // 선순위 정보 , 대환정보 update 전승찬 추가 2022-04-25

		$del_sql = "DELETE FROM hloan_content_loan WHERE hcseq='$SE'";
		sql_query($del_sql);

		for ($i=0 ; $i<count($P_creditor) ; $i++) {

			$sort_no = $i+1;
			$insub_sql = "INSERT INTO hloan_content_loan
								  SET hcseq = '$SE',
									  loan_gubun = 'PRE',
									  sort_no = '".$sort_no."',
									  reg_gubun = '".$P_reg_gubun[$i]."',
									  creditor = '".$P_creditor[$i]."',
									  limit_amount = '".replace_integer($P_limit_amount[$i])."',
									  loan_amount = '".replace_integer($P_loan_amount[$i])."',
									  loan_percent = '".$P_loan_percent[$i]."',
									  debtor = '".$P_debtor[$i]."',
									  reg_obj = '".$P_reg_obj[$i]."'
						";	
			sql_query($insub_sql);
		}

		for ($i=0 ; $i<count($R_creditor) ; $i++) {

			$sort_no = $i+1;
			$insub_sql = "INSERT INTO hloan_content_loan
								  SET hcseq = '$SE',
									  loan_gubun = 'REP',
									  sort_no = '".$sort_no."',
									  reg_gubun = '".$R_reg_gubun[$i]."',
									  creditor = '".$R_creditor[$i]."',
									  limit_amount = '".replace_integer($R_limit_amount[$i])."',
									  loan_amount = '".replace_integer($R_loan_amount[$i])."',
									  loan_percent = '".$R_loan_percent[$i]."',
									  debtor = '".$R_debtor[$i]."',
									  reg_obj = '".$R_reg_obj[$i]."'
						";	
			sql_query($insub_sql);
		}
	}

	IF($kind == "del")
	{
		/*댓글삭제*/
		//fn_general_query_update("del","","","hloan_comment","req_idx",replace_integer($SE),"",$connect_db);
	}

	sql_close($connect_db);

	$strlink = "&S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&SC=".$SC."&STXT=".$STXT."&page=".$page;	// 추가 리턴변수

	SWITCH($kind)
	{
		CASE "save" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
		CASE "update" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
		CASE "del" : $strRet = fn_general_process_link($kind, "1", $strlink); BREAK;
	}
	$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/helloloan/?".$strRet[1]);
	ECHO json_encode($objval);
	EXIT;
?>