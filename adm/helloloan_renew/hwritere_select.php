<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include_once('./_common.php');
include_once('../admin.loan.function.php');

	$strPost = ARRAY(
							ARRAY("ddmoney","","Y"),ARRAY("maxbond","1","Y"),ARRAY("fees","","Y"),ARRAY("loankind","","Y"),ARRAY("mm","","Y"), ARRAY("auctionyn","","Y")
							, ARRAY("bcode","",""), ARRAY("aptname","","Y"), ARRAY("aptarea","","Y"),ARRAY("hmseq2","","Y"),ARRAY("floor","","Y"),ARRAY("si","","Y"),ARRAY("gu","",""),ARRAY("apt_areatxt","","")
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
					${$strPost[$i][0]} .=  ",";
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
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오 : ".$strPost[$i][0])),"retval"=>"");
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
	$aptnameArr	=	EXPLODE(",",$aptname);
	$aptareaArr =   EXPLODE(",",$aptarea);

	$rowList = $fnLimitSelect->fn_kb_limit("3", " ", $aptnameArr[0], $aptareaArr[0]);

	IF($rowList[1][1] > 0)
	{
		FOR($i=0;$i<COUNT($rowList[1][2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($rowList[0]);$j++)
			{
				${$rowList[0][$j]} = $rowList[1][2][$i][$j];
			}
			IF(strpos($apt_areatxt,"탑층") == true)
			{
				$intMM	=	$mm;
			} ELSE {
				IF($floor)
				{
					IF($floor > 3)
					{
						$intMM	=	$mm;
					} ELSE {
						$intMM	=	$mm_b;
					}
				} ELSE {
					$intMM	=	$mm;
				}
			}

		}
	}

	$maxbondArr	=	EXPLODE(",",$maxbond);

	FOR($i=0;$i<COUNT($maxbondArr);$i++)
	{
		$maxbondVal	+=	replace_integer($maxbondArr[$i]);
	}

	// 리턴값 mm : kb시세 , 준공년월 cr_date, 세대수 tot_house
	$retobj = $fnLimitSelect->fn_limit_select($ddmoney,$maxbondVal, $fees, $loankind,$intMM,$auctionyn,$hmseq2, $si, $gu);

	IF($retobj[2][0] == null)
	{
		$retobj[1][0] = 0;
	}

	$strClass1 = "";
	$strClass2 = "";
	$strClass3 = "";
	$strTClass = "";
	IF($retobj[0][1] < 0) { $strClass1 = " class='fred'"; }
	IF($retobj[0][2] < 0) { $strClass2 = " class='fred'"; }
	IF($retobj[0][3] < 0) { $strClass3 = " class='fred'"; $strTClass = "fred"; }

	$retval = "<table class='content_w1_table'>
			<tr>
				<th>총 한도금액 (원) </th>
				<td colspan='4' class='col3 fb ".$strTClass."'>".f_number($retobj[6])." (KB시세 : ".f_number(STR_REPLACE(",","",$intMM) * 10000).")</td>
			</tr>
			<tr>
				<th>LTV 기준</th>
				<td class='fb'>현재LTV ".$retobj[3][0]." 기준</td>
				<td>70% 미만</td>
				<td>70%초과~80%미만</td>
				<td>80%초과~83%이하</td>
			</tr>
			<tr>
				<th>예상대출금 (원)</th>
				<td class='fb'>".f_number($retobj[0][0])."</td>
				<td".$strClass1.">".f_number($retobj[0][1])."</td>
				<td".$strClass2.">".f_number($retobj[0][2])."</td>
				<td".$strClass3.">".f_number($retobj[0][3])."</td>
			</tr>
			<tr>
				<th>플랫폼 수수료 (원)</th>
				<td class='fb'>".f_number($retobj[1][0])."</td>
				<td>".f_number($retobj[1][1])."</td>
				<td>".f_number($retobj[1][2])."</td>
				<td>".f_number($retobj[1][3])."</td>
			</tr>
			<tr>
				<th>헬로금리 (%)</th>
				<td class='fb'>".$retobj[2][0]."</td>
				<td>".$retobj[2][1]."</td>
				<td>".$retobj[2][2]."</td>
				<td>".$retobj[2][3]."</td>
			</tr>
			<tr>
				<th>대출기간</th>
				<td colspan='4'>12 개월</td>
			</tr>
			</table>
			";


	sql_close($connect);
	$objval = ARRAY("retcode"=>"OK","retalert"=>"","retval"=>$retval, "retadd"=> ARRAY($retobj[2][0],STR_REPLACE("%","",$retobj[3][0]),$retobj[1][0],$mm,$cr_date,$tot_house));
	ECHO json_encode($objval);
	EXIT;
?>

