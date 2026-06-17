<?php
include_once('./_common.php');
include_once('./_head.php');
include_once('../lib/function_prc.php');
include_once(G5_PATH . '/pid_check.inc.php');		// pid 유입체크 및 쿠키생성이 필요한 페이지에만 include

// index파일

?>
<style>
.content2 {width:100%;text-align:center;}

</style>
<div id="content">
	<!--div class="location"><span></span><b class="blue">이용안내 > 이벤트</b></div-->

	<div>
		<h2 class="top_title">헬로펀딩 이벤트</h2>
		<p class="top_text">현재 진행중인 이벤트를 확인할 수 있습니다.<br class="br"></p>
	</div>

	<div class="content2">
<?
$strEventClass	=	new Event_Board();

$strQueryKind = false;
SWITCH($RD)
{
	CASE "2" :
		$strInputText1		= "txt1";
		$strInputText2		= "txt2";		// nl2br
		$strInputTextarea	= "txt2";
		$strInputTextarea2	= "txt2";
		$strRadioText		= "txt";
		$strSelectBox		= "txt";
		$strSelectBox2		= "txt";
		$strCheckboxText	= "txt";
		$strInputFile		= "filetxt";
		$strInputSFile		= "sfile";
		$strFileText		= "fileImgatt";

		IF(!$SE)
		{
			alert_back("접근이 올바르지 않습니다","-1");
			sql_close($connect);
			exit;
		}

		$strColumn	=	ARRAY(
						"idx", "title","content","reg_date",
						"sdate","edate","ifile","contentm"
				);


		$row = $strEventClass->FnView($SE, $strColumn);

		IF(!$row["idx"])
		{
			alert("접근이 올바르지 않습니다",-1);
			exit;
		}

		IF(G5_IS_MOBILE)
		{
			$content = $row["contentm"];
		} ELSE {
			$content = $row["content"];
		}


		$intDate = $strEventClass->dateDifference($sdate,$edate)+1;

		IF($intDate >= 60)
		{
			$strDateTxt = "<span style='font-size:30px;'>∞</span>";
		} ELSE {
			$strDateTxt = "D-".$intDate;
		}

		IF($strEventClass->dateDifference(DATE("Y-m-d"),$edate) > 0)
		{
			$strListTxt = "진행중 이벤트";
		} ELSE {
			$strListTxt = "종료된 이벤트";
			$strDateTxt = "End";
		}

		$strBtnTxt		= "상세보기";

		$qstr = "/hevent/?page=".$page;
		$strUrl = "write.php";
	BREAK;
	CASE "1" :
	DEFAULT :
			$strColumn	=	ARRAY(
								"idx", "title","sdate","edate","ifile","linkurl","target"
						);

			$strSearch = ARRAY("STXT"=>$STXT, "SC"=>"Y");
			$page = 1;
			$rowList = $strEventClass->FnListFront($strSearch, $page, 15, $strColumn);

			$strSearch2 = ARRAY("STXT"=>$STXT, "SC"=>"N");
			$rowList2 = $strEventClass->FnListFront($strSearch2, $page, 6, $strColumn);

			$total_page		=	$rowList2[0];
			$total_count	=	$rowList2[1];


			$qstr = "/hevent/?RD=2&page=".$page;

		$strUrl = "list.php";
	BREAK;

}
IF($strUrl)
{
include_once("./".$strUrl);
}
?>
		</div>
	</div>
<?php
include_once('./_tail.php');
?>