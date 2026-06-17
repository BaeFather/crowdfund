<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include_once('./_common.php');
include_once('../admin.loan.function.php');

	$strPost = ARRAY(
							ARRAY("ddmoney","","Y"),ARRAY("maxbond","1",""),ARRAY("fees","","Y"),ARRAY("loankind","","Y"),ARRAY("mm","","Y"), ARRAY("auctionyn","","Y"),ARRAY("hmseq2","","Y"),ARRAY("si","","Y"),ARRAY("gu","",""),ARRAY("seqor","",""),ARRAY("arecyn","","")
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

	$maxbondArr	=	EXPLODE(",",$maxbond);

	FOR($i=0;$i<COUNT($maxbondArr);$i++)
	{
		$maxbondVal	+=	replace_integer($maxbondArr[$i]);
	}

	$fnLimitSelect = new Limit_Select();

	$seqkind = false;

	IF($seqor)	// 기존 조견표 등록시 기존 조견표 호출
	{
		IF($arecyn && !IN_ARRAY($arecyn,ARRAY("3","4")))	//진행사항이 승인중이라면
		{
			$seqkind = true;
		}
	}
	if($seqkind == false)
	{
		$fnLimitSelect->intSeq = $fnLimitSelect->fn_setting_list($hmseq2, $si, $gu);
	} else {
		$fnLimitSelect->intSeq = $seqor;
	}

	$retobj = $fnLimitSelect->fn_limit_select($ddmoney, $maxbondVal, $fees, $loankind,$mm,$auctionyn, $hmseq2, $si, $gu);

	IF($retobj[2][0] == null)
	{
		$retobj[1][0] = 0;
	}

	IF($retobj[5] == 0)
	{
		$retval = "<input type='hidden' name='seq' value='".$fnLimitSelect->intSeq."'><table class='content_w1_table'>
			<tr>
				<th>취급 불가지역으로 신청이 불가합니다.</th>
			</tr>
			</table>";
	} ELSE {

		$intMaxLimit = 1;

		IF((STR_REPLACE(",","",$mm) * 10000) > $fnLimitSelect->fn_maxlimit())	// 한도금액 초과
		{
			$intMaxLimit = 0;
		}

	$retval = "
			<input type='hidden' name='seq' value='".$fnLimitSelect->intSeq."'>
			<table class='content_w1_table'>
			<tr>
				<th>가능 한도금액 (원) </th>
				<td colspan='".COUNT($retobj[4])."' class='col3 fb ".$strTClass."'>".f_number(roanlmoney($retobj[6]))." (KB시세 : ".f_number(STR_REPLACE(",","",$mm) * 10000).")</td>
			</tr>
			<tr>
				<th>LTV 기준</th>
				<td class='fb fg1'>현재LTV ".$retobj[3][0]." 기준</td>";

				FOR($i=1;$i<COUNT($retobj[4]);$i++)
				{
				//$retval .= "<td>".$retobj[4][$i][3]."%이상~<br />".$retobj[4][$i][4]."%이하</td>";
				$retval .= "<td>".$retobj[4][$i][4]."%이하</td>";
				}

			$retval .= "</tr>
			<tr>
				<th>가능대출금 (원)</th>
				<td class='fb fg1 f15'>";

			IF($retobj[2][0] == null)
			{
				$retval .= "0";
			} ELSE {
				$retval .= f_number(roanlmoney($retobj[0][0]));
			}
			$retval .= "</td>";

				FOR($i=1;$i<COUNT($retobj[4]);$i++)
				{
					IF($retobj[0][$i] < 0)
					{
						$retobj[0][$i] = 0;
					}
					$retval .= "<td>".f_number(roanlmoney($retobj[0][$i]))."</td>";
				}

			$retval .= "</tr>
			<tr>
				<th>헬로금리 (%)</th>
				<td class='fb fg1'>";

			IF($retobj[2][0] == null)
			{
				$retval .= "<span class='fred'>LTV 초과 취급불가</span>";
			} ELSE {
				$retval .= $retobj[2][0];
			}
			$retval .= "</td>";
				FOR($i=1;$i<COUNT($retobj[4]);$i++)
				{
				$retval .= "<td>".$retobj[2][$i]."</td>";
				}

			$retval .= "</tr>
			<tr>
				<th>대출기간</th>
				<td colspan='".COUNT($retobj[4])."'>".$retobj[4][1][5]." 개월</td>
			</tr>
			<tr>
				<th>플랫폼 수수료 (원)</th>
				<td class='fb fg1'>";
			IF($retobj[2][0] == null)
			{
				$retval .= "0";
			} ELSE {
				$retval .= f_number($retobj[1][0]);
			}
			$retval .= "</td>";
				FOR($i=1;$i<COUNT($retobj[4]);$i++)
				{
					IF($retobj[1][$i] < 0)
					{
						$retobj[1][$i] = 0;
					}
					$retval .= "<td>".f_number($retobj[1][$i])."</td>";
				}

			$retval .= "</tr>
			</table>

			<div class='warning_area'>
				* 가능한도금액=(헬로취급지역 LTV 80% 이하 – 선순위 원금)
			</div>
			";
	}

	sql_close($connect);
	$objval = ARRAY("retcode"=>"OK","retalert"=>"","retval"=>$retval, "retadd"=> ARRAY($retobj[2][0],STR_REPLACE("%","",$retobj[3][0]),$retobj[1][0],$intMaxLimit));
	ECHO json_encode($objval);
	EXIT;
?>

