<?php

include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '900000';
auth_check($auth[$sub_menu], "w");

while( list($k, $v) = each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }



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

$html_title = $menu['menu900'][11][1];
$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';

include_once (G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

<?php

	$gstrHelloBanner = NEW Hello_Banner();

	SWITCH($RD)
	{
		CASE "3"	:	// write ,update
		CASE "2"	:	// read

			IF($RD == "2")
			{
				$strInputText1		= "txt1";
				$strRadioText		= "txt";
				$strSelectBox		= "txt";
				$strPassword		= "txt";
				$strInputTextarea	= "txt2";
				$strFileText		= "fileImgatt";
				$strFileText2		= "fileImgatt";
			} ELSEIF($RD == "3") {
				$strInputText1		= "text";
				$strRadioText		= "radio";
				$strSelectBox		= "";
				$strPassword		= "password";
				$strInputTextarea	= "textarea";
				$strFileText		= "fileImg";
				$strFileText2		= "sfileImg";
			}
			$intSeqName	=	"idx";
			$strColumn	= ARRAY(
									$intSeqName,"cfcode","sdate","edate","repimg","mrepimg",
									"targeturl","targetlink","recyn","reg_date","sort_id","title"
								);


			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = "";
			}

			IF($SE)
			{
				$strTable	=	"cf_banner";

				$strWhere	=	" WHERE idx='".add_str($SE)."'";
				$strOrder	=	$intSeqName;
				$intLimit1	=	0;
				$intLimit2	=	1;
				$intStrlen	=	100000;

				$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

				IF($rowView[0][$intSeqName])
				{
					FOR($i=0;$i<COUNT($strColumn);$i++)
					{
						${$strColumn[$i]} = $rowView[0][$strColumn[$i]];
					}
				} ELSE {
					alert_back("접근이 올바르지 않습니다","-1");
					EXIT;
				}

				$strKind			=	"update";
				$strBtnTxt			=	"수정하기";
			} ELSE {
				$strKind			=	"save";
				$strBtnTxt			=	"등록하기";
				$reg_date			=	DATE("Y-m-d H:i:s");
				$recyn				=	"Y";
			}

			$strListUrl = "?STXT=".$STXT."&page=".$page."&S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4;

			include_once("detail.php");
			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$num_per_page = 20;
			$intSeqName = "idx";
			$strColumn	= ARRAY(
									$intSeqName,"cfcode","sdate","edate","repimg","mrepimg",
									"targeturl","targetlink","recyn","reg_date","sort_id","title"
						  );
			$strTable	= "cf_banner";
			$frQuery	= "";

			IF($STXT) {
				IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
				$strWhere .= " (cfcode = '".add_str($STXT)."')";
			}
			IF($S1 && $S2)
			{
				IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
				$strWhere .= " (sdate >= '".add_str($S1)."' AND edate<='".add_str($S2)."')";
			}
			IF($S3)
			{
				IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
				$strWhere .= " recyn='".add_str($S3)."'";
			}
			IF($S4)
			{
				IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
				$strWhere .= " cfcode='".add_str($S4)."'";
			}

			$strOrder	=	$intSeqName." DESC";
			$strlimit2	=	$num_per_page;

			IF(!$page) { $page = 1; }

			$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

			$total_page	=	$rowList[0];
			$total_count=   $rowList[1];

			$qstr = "?STXT=".$STXT."&S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4;

			include_once("list.php");
		BREAK;
	}
?>

</div>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>