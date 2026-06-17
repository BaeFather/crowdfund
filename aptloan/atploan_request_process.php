<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//처리
?>
<?php
include_once('./_common.php');
include_once('../lib/etc.lib.php');
?>
<?php
	$kind =& $_POST["kind"];

	IF($kind == "save") {
		$strPost = ARRAY(
							ARRAY("si","","Y"),ARRAY("gu","","Y"),ARRAY("dong","","Y"),ARRAY("price","","Y"),
							ARRAY("apt_name","",""),ARRAY("apt_area","",""),ARRAY("dong_num","",""),ARRAY("ho_num","",""),
							ARRAY("floor_num","",""),ARRAY("apt_name2","",""),ARRAY("rdo_live","","Y"),ARRAY("rprice","","Y"),
							ARRAY("loansum","","Y"),ARRAY("ramount","","Y"),ARRAY("rphone","","Y"),ARRAY("rname","","Y"),ARRAY("check01","","Y")
					);
	} ELSE {
		$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("접근이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	}

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][1] > 0)
		{
			//$strPostTarget = "";
			FOR($j=0;$j<COUNT($_POST[$strPost[$i][0]]);$j++)
			{
				IF($j == 0) { ${$strPost[$i][0]} = ""; }
				IF($j > 0)
				{
					${$strPost[$i][0]} .=  ",";
				}
				${$strPost[$i][0]} .= $_POST[$strPost[$i][0]][$j];
			}
		} ELSE {
			IF($strPost[$i][2] == "Y")
			{
				IF($_POST[$strPost[$i][0]]<>"")
				{
					${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
				} ELSE {
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오 : ".$strPost[$i][0])),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
			}
		}
	}

	echo print_r($_POST);
	exit;

	$gstrNdate = DATE("Y-m-d H:i:s");

	IF($kind == "save")
	{


		$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("헬로펀딩 아파트 담보대출을 신청해주셔서 감사합니다.\\n\\n.1영업일 이내에 담당자 확인 후 연락드리겠습니다")),"retval"=>"/aptloan");
		ECHO json_encode($objval);
		sql_close($connect_db);
		exit;
	}
?>