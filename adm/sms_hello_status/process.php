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
							ARRAY("SE","",""),ARRAY("page","","Y"),ARRAY("STXT","",""),
							ARRAY("passwd","","Y"),ARRAY("cname","","Y"),ARRAY("cphone","","Y"),ARRAY("reg_date","","Y"),ARRAY("recyn","","Y")
					);
	} ELSEIF($kind == "update") {
		$strPost = ARRAY(
							ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("STXT","",""),
							ARRAY("passwd","","Y"),ARRAY("cname","","Y"),ARRAY("cphone","","Y"),ARRAY("reg_date","","Y"),ARRAY("recyn","","Y")
					);
	} ELSEIF($kind == "smssend") {
		$strPost = ARRAY(
							ARRAY("SE","","Y"),ARRAY("page","","Y"),ARRAY("STXT","",""),
							ARRAY("RD","","Y")
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

	IF(!$reg_date) { $reg_date = DATE("Y-m-d H:i:s"); }

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
							"passwd","cname","cphone","reg_date","recyn"
						);

		$strValues = ARRAY(
						$passwd, $cname, $cphone, $reg_date,$recyn
					);

		$strTable		=	"cf_product_admin_user";
		$SeqName	=	"midx";

		$INSERT_ID = fn_general_query_update($kind,$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);
		sql_close($connect_db);

		$strlink = "&STXT=".$STXT."&page=".$page;	// 추가 리턴변수

		SWITCH($kind)
		{
			CASE "save" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
			CASE "update" : $strRet = fn_general_process_link($kind, "2", $strlink); BREAK;
			CASE "del" : $strRet = fn_general_process_link($kind, "1", $strlink); BREAK;
		}
		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("글이 정상 ".$strRet[0]." 되었습니다")),"retval"=>"/adm/sms_hello_status/?".$strRet[1]);
		ECHO json_encode($objval);
		EXIT;
	} ELSEIF($kind == "smssend") {

		IF(!$SE)
		{
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}

		include_once($_SERVER["DOCUMENT_ROOT"]."/lib/sms.lib.php");

		$intTime  = TIME();

		$strTable = "cf_product_admin_report_send";
		$SeqName   = "ridx";
		$strColumn = ARRAY("send_time","reg_time","end_time");
		$strValues = ARRAY($intTime,0,0);

		fn_general_query_update("update",$strColumn,$strValues,$strTable,$SeqName,replace_integer($SE),"",$connect_db);

		UNSET($strColumn);

		$strWhere	=	" WHERE ridx='".add_str($SE)."'";
		$strOrder	=	$SeqName;
		$intLimit1	=	0;
		$intLimit2	=	1;
		$intStrlen	=	100;
		$strTable	=  "(SELECT t1.ridx, t1.midx, t1.send_time, t2.title,t2.product,t3.cphone
						FROM cf_product_admin_report_send t1
						LEFT JOIN cf_product_admin_report t2
						ON t1.pidx = t2.pidx
						LEFT JOIN cf_product_admin_user t3
						ON t1.midx=t3.midx) t1";

		$strColumn  =	ARRAY("ridx","midx","send_time","title","product","cphone");
		$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);
		IF($rowView[0][$SeqName])
		{
			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = $rowView[0][$strColumn[$i]];
			}
		}

		/*
		CREATE TABLE `test_table` (
			`seq` INT(11) NOT NULL AUTO_INCREMENT,
			`send_time` INT(11) NOT NULL,
			`title` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cphone` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			PRIMARY KEY (`seq`) USING BTREE
		)
		COMMENT='투자요약 전송 기록'
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		ROW_FORMAT=DYNAMIC
		AUTO_INCREMENT=25;
		*/


		//$Query = "INSERT INTO test_table (send_time, title, cphone) values ('".$send_time."','".$title."','".$cphone."');";
		//sql_query($Query);

		$sms_msg = $title."\n\n";
		$sms_msg .= $product."\n\n";
		$sms_msg .= "https://www.hellofunding.co.kr/hello_report/?RT=".$send_time.$midx;
		/*sms발송*/
		unit_sms_send($_admin_sms_number, $cphone, $sms_msg);

		sql_close($connect_db);

		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("정상 발송 되었습니다")),"retval"=>"/adm/sms_hello_status/");
		ECHO json_encode($objval);
		EXIT;
	}
?>