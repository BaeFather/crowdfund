<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '910500';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][5][1];

$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';

// 받은 데이터를 변수화
foreach($_REQUEST as $k=>$v) { ${$_REQUEST[$k]} = $v; }

$qstr = $_SERVER['QUERY_STRING'];
if($idx) {
	$qstr = preg_replace("/&idx=([0-9]){1,10}/", "", $qstr);
}
if($page) {
	$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $qstr);
}

$countUp = false;
if($idx && $mode!='download') {
	if($_COOKIE['loan_request_view']) {
		$VIEW_IDX = explode(",", $_COOKIE['loan_request_view']);
		if(!in_array($idx, $VIEW_IDX)) {
			$addIdx = $_COOKIE['loan_request_view'] . "," . $idx;
			setcookie("loan_request_view", $addIdx, strtotime(date('Y-m-d')." 23:59:59"), "/");
			$countUp = true;
		}
	}
	else {
		setcookie("loan_request_view", $idx, strtotime(date('Y-m-d')." 23:59:59"), "/");
		$countUp = true;
	}
}

include_once (G5_ADMIN_PATH.'/admin.head.php');

$RD		=&	$_GET["RD"];
IF(!$RD) { $RD =&	$_POST["RD"]; }
IF(!$RD) { $RD = 1; }

IF(!$page) { $page = 1; }
?>
<?php

	$strColumn = ARRAY("year","month","hcseq");

	FOR($i=0;$i<COUNT($strColumn);$i++)
	{
		${$strColumn[$i]} = $_GET[$strColumn[$i]];
	}
?>
<div class="sub_content_title">일정캘린더</div>

<div class="general_body">
<?
	//---- 오늘 날짜
	$thisyear  = date('Y');
	$thismonth = date('n');
	$today     = date('j');

	if (!$year) {
	  $year = $thisyear;
	}

	if (!$month) {
	  $month = $thismonth;
	}

	$strTodayTrue = FALSE;

	IF($year == $thisyear && $month == $thismonth)
	{
		$strTodayTrue = TRUE;
	}

	$txtDate = sprintf("%04d",$year)."-".sprintf("%02d",$month)."-".sprintf("%02d",$today);

	$maxdate = date(t, mktime(0, 0, 0, $month, 1, $year));   // the final date of $month

	$prevmonth = $month - 1;
	$nextmonth = $month + 1;
	$prevyear = $year;
	$nextyear = $year;
	if ($month == 1) {
	  $prevmonth = 12;
	  $prevyear = $year - 1;
	} elseif ($month == 12) {
	  $nextmonth = 1;
	  $nextyear = $year + 1;
	}

	$wheresdate = $year."-".sprintf("%02d",$month)."-01";
	$whereedate = $year."-".sprintf("%02d",$month)."-".date("t",date_ntime($year,$month,"1"));

	$Query = "SELECT sspeq,ptitle,o_name,dyn,LEFT(send_date,10) as send_date,cmseq,sprseq FROM ".query_shop_all_list()." WHERE left(reg_date,10) >='".$wheresdate."' AND  left(reg_date,10) <='".$whereedate."' ".$Where." ORDER BY left(reg_date,10) ASC";

    $Result = sql_query($Query,$connect);

	$i=0;
	WHILE($RowM=sql_fetch_array($Result))
	{
		$Rowsspeq		=	$RowM["sspeq"];
		$Rowptitle		=	$RowM["ptitle"];
		$Rowoname		=	$RowM["o_name"];
		$Rowdyn			=	$RowM["dyn"];
		$Rowsdate		=	$RowM["send_date"];
		$Rowcmseq		=	$RowM["cmseq"];
		$Rowsprseq		=	$RowM["sprseq"];
		$Rowedate		=	$Rowsdate;

		$m = 0;

		FOR($j=$Rowsdate;$j<=$Rowedate;$j++)
		{
			$calendar[SUBSTR($j,-2)]["seq"][] = $Rowsspeq;
			$calendar[SUBSTR($j,-2)]["title"][] = $Rowptitle;
			$calendar[SUBSTR($j,-2)]["iname"][] = $Rowoname;
			$calendar[SUBSTR($j,-2)]["recyn"][] = $Rowdyn;
			$calendar[SUBSTR($j,-2)]["cmseq"][] = $Rowcmseq;
			$calendar[SUBSTR($j,-2)]["sprseq"][] = $Rowsprseq;

			$j = substr(add_date($Rowsdate,0,$m),0,10);
			$m++;
		}
		$RowsdateOr = $Rowsdate;
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
?>
<div class="general_totalcnt">총 대상건수 : <?=NUMBER_FORMAT($i);?> 건</div>

<style>
	.tbschedule {}
	.tbschedule th {font-size:14px;padding:5px 0 5px 0;}
	.tbschedule th {font-size:12px;padding:5px 0 5px 0;}
</style>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:20px auto;">
  <tr>
  	<td style="padding:0px 5px 20px 5px;text-align:center;font-size:26px;font-weight:bold;">
		<a href="<?=$PHP_SELF?>?KD=<?=$KD?>&year=<?=$prevyear?>&month=<?=$prevmonth?>"><</a>
		<?=$year?>
		년
		<?=$month?>
		월
		<a href="<?=$PHP_SELF?>?KD=<?=$KD?>&year=<?=$nextyear?>&month=<?=$nextmonth?>">></a>
	</td>
  </tr>
  <tr>
    <td colspan="2" style="padding:0 5px 0 5px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="d1d3d4" class="tbschedule">
      <tr bgcolor="#f2e8df" align="center">
        <th width="15%" height="30" style="text-align:center;color:#FFFFFF;background-color:#FF0000;">일</th>
        <th width="14%" style="text-align:center">월</th>
        <th width="14%" style="text-align:center">화</th>
        <th width="14%" style="text-align:center">수</th>
        <th width="14%" style="text-align:center">목</th>
        <th width="14%" style="text-align:center">금</th>
        <th width="15%" style="text-align:center;color:#FFFFFF;background-color:#0000FF;">토</th>
      </tr>
<?
$date   = 1;
$offset = 0;
echo "<tr bgcolor='#FFFFFF' align='right' valign='top'>";

while ($date <= $maxdate) {

	unset($strTDClass);

	$date_html = sprintf("%02d",$date);
	$dateFull_html = $year."-".sprintf("%02d",$month)."-".$date_html;

	IF($today == $date_html) { $strTDClass = " pd5_2"; } ELSE { $strTDClass = " pd5"; }

    if ($date == '1') {
		$offset = date('w', mktime(0, 0, 0, $month, $date, $year));  // 0: sunday, 1: monday, ..., 6: saturday
		SkipOffset($cellh, $cellw, $offset);
		$offset++;

        $html = " <td class='".$strTDClass."'>";
		IF($offset == 1)
		{
			$html .= "<span style='color:#ff0000;'>";
		}
		IF($offset == 7)
		{
			$html .= "<span style='color:#0000ff;font-size:14px;'>";
		}
		$html .=  $date_html;
		IF($offset == 1 || $offset == 7)
		{
			$html .= "</span>";
		}
		IF(COUNT($calendar[$date_html]["seq"]) > 0)
		{
			FOR($k=0;$k<=COUNT($calendar[$date_html]["seq"]);$k++)
			{
				UNSET($forkd);
				IF($calendar[$date_html]["cmseq"][$k] > 0)
				{
					$forkd = "12";
				} ELSE {
					$forkd = "11";
				}
				$html .= "<div class='calendar_td'><a href=\"".$PHP_SELF."?KD=".$forkd."&RD=2&SE=".$calendar[$date_html]["seq"][$k]."&SE2=".$calendar[$date_html]["sprseq"][$k]."&sdate=".substr($txtDate,0,7)."-".sprintf("%02d",$date_html)."\">";

				if($calendar[$date_html]["title"][$k])
				{
					$html .= "<span style='font-size:12px;'>";
					IF($calendar[$date_html]["cmseq"][$k] > 0)
					{
						$html .= "<span class='fred'>[협]</span>";
					}
					$html .= "[".$calendar[$date_html]["title"][$k]."] ".$calendar[$date_html]["iname"][$k]."</span>";
				}
				$html .= "</a></div>";
			}
		}
		$html .= "</td>";
		echo $html;

	} else {
		$offset++;

        $html = " <td class='".$strTDClass."'>";

		IF($offset == 1)
		{
			$html .= "<span style='color:#ff0000;font-size:14px;'>";
		}
		IF($offset == 7)
		{
			$html .= "<span style='color:#0000ff;font-size:14px;'>";
		}
		$html .=  $date_html;
		IF($offset == 1 || $offset == 7)
		{
			$html .= "</span>";
		}

		IF(COUNT($calendar[$date_html]["seq"]) > 0)
		{
			FOR($k=0;$k<=COUNT($calendar[$date_html]["seq"]);$k++)
			{
				UNSET($forkd);
				IF($calendar[$date_html]["cmseq"][$k] > 0)
				{
					$forkd = "12";
				} ELSE {
					$forkd = "11";
				}
				$html .= "<div class='calendar_td'><a href=\"".$PHP_SELF."?KD=".$forkd."&RD=2&SE=".$calendar[$date_html]["seq"][$k]."&SE2=".$calendar[$date_html]["sprseq"][$k]."&sdate=".substr($txtDate,0,7)."-".sprintf("%02d",$date_html)."\">";

				if($calendar[$date_html]["title"][$k])
				{
					$html .= "<span style='font-size:12px;'>";
					IF($calendar[$date_html]["cmseq"][$k] > 0)
					{
						$html .= "<span class='fred'>[협]</span>";
					}
					$html .= "[".$calendar[$date_html]["title"][$k]."] ".$calendar[$date_html]["iname"][$k]."</span>";
				}
				$html .= "</a></div>";
			}
		}

		$html .= "</td>";
		echo $html;
	}

	$date++;

	if ($offset == 7) {
		echo "</TR> \n";
		if ($date <= $maxdate) {
			echo "<tr bgcolor='#FFFFFF' valign='top'> \n";
		}
		$offset = 0;
	}
} // end of while

if ($offset != 0) {
  SkipOffset($cellh, $cellw, (7-$offset));
  echo "</TR> \n";
}

sql_close($connect);
?>
    </table>
	</td>
</tr>
</table>