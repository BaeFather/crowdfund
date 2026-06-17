<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include_once('./_common.php');
include_once('../admin.loan.function.php');

	$strPost = ARRAY(
							ARRAY("kind","","Y"),ARRAY("d_code","",""),ARRAY("mg_id","",""),ARRAY("ju_seri","","")
					);

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][1] > 0)
		{
			FOR($j=0;$j<COUNT($_POST[$strPost[$i][0]]);$j++)
			{
				IF($j == 0) { ${$strPost[$i][0]} = ""; }
				IF($j > 0)
				{
					${$strPost[$i][0]} .=  ":";
				}
				${$strPost[$i][0]} .= replace_integer(urldecode($_POST[$strPost[$i][0]][$j]));
			}

		} ELSE {
			IF($strPost[$i][2] == "Y")
			{
				IF($_POST[$strPost[$i][0]]<>"")
				{
					${$strPost[$i][0]} = urldecode($_POST[$strPost[$i][0]]);
				} ELSE {
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오 : ".$i)),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = urldecode($_POST[$strPost[$i][0]]);
			}
		}
	}

	$page = 1;
	$num_per_page = 100;

	$fnLimitSelect = new Limit_Select();
	$rowList = $fnLimitSelect->fn_kb_limit($kind, $d_code, $mg_id, $ju_seri);

	// 리턴값 0,  select 컬럼   1, 결과값 리턴

	IF($rowList[1][1] > 0)
	{
		FOR($i=0;$i<COUNT($rowList[1][2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($rowList[0]);$j++)
			{
				${$rowList[0][$j]} = $rowList[1][2][$i][$j];
			}

			IF($kind =="1")
			{
				$retval[]	=	ARRAY($mg_id, $dj_name);
			} ELSEIF($kind == "2") {
				$retval[]	=	ARRAY($jm, $jmp, $ju_seri);
			} ELSEIF($kind == "3") {
				$retval[]	=	ARRAY($mm);
			}
		}
	}

	sql_close($connect);
	$objval = ARRAY("retcode"=>"OK","retalert"=>"","retval"=>$retval);
	ECHO json_encode($objval);
	EXIT;
?>