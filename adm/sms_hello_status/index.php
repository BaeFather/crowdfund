<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '800610';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu800'][8][1];

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
?>

<style>
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

<?php
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
			} ELSEIF($RD == "3") {
				$strInputText1		= "text";
				$strRadioText		= "radio";
				$strSelectBox		= "";
				$strPassword		= "password";
				$strInputTextarea	= "textarea";
			}

			$intSeqName	=	"midx";
			$strColumn	= ARRAY($intSeqName,"passwd","cname","cphone","reg_date","recyn");

			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = "";
			}

			IF($idx)
			{
				$strTable	=	"cf_product_admin_user";

				$strWhere	=	" WHERE midx='".add_str($idx)."'";
				$strOrder	=	$intSeqName;
				$intLimit1	=	0;
				$intLimit2	=	1;
				$intStrlen	=	100;

				$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

				IF($rowView[0][$intSeqName])
				{
					FOR($i=0;$i<COUNT($strColumn);$i++)
					{
						${$strColumn[$i]} = $rowView[0][$strColumn[$i]];
					}
					$examountArr	=	EXPLODE(":",$examount);
					$maxbondArr		=	EXPLODE(":",$maxbond);
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

			$strListUrl = "?STXT=".$STXT."&page=".$page;

			include_once("detail.php");
			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$num_per_page = 20;
			$intSeqName = "midx";
			$strColumn	= ARRAY($intSeqName,"passwd","cname","cphone","reg_date","recyn");
			$strTable	= "cf_product_admin_user";
			$frQuery	= "";

			IF($STXT) {
				IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
				$strWhere .= " (cname LIKE '%".add_str($STXT)."%' OR cphone LIKE '%".add_str($STXT)."%')";
			}

			$strOrder	=	$intSeqName." DESC";
			$strlimit2	=	$num_per_page;

			IF(!$page) { $page = 1; }

			$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

			$total_page	=	$rowList[0];
			$total_count=   $rowList[1];

			$qstr = "?STXT=".$STXT;

			include_once("list.php");
		BREAK;
		CASE "4" :
			$num_per_page = 100;
			$intSeqName = "ridx";
			$strColumn	= ARRAY($intSeqName,"pidx","send_time","reg_time","end_time","title");
			$strTable	= "
			(
				SELECT t1.ridx,t1.pidx,t1.send_time, t1.reg_time, t1.end_time, t2.title
				FROM (SELECT * FROM cf_product_admin_report_send WHERE midx='".$idx."') t1
				LEFT JOIN cf_product_admin_report t2
				ON t1.pidx = t2.pidx
				ORDER BY send_time DESC
			) t1
			";
			$frQuery	= "";
			$strWhere   = "";

			$strOrder	=	$intSeqName." DESC";
			$strlimit2	=	$num_per_page;

			IF(!$page) { $page = 1; }

			$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

			$total_page	=	$rowList[0];
			$total_count=   $rowList[1];

			$qstr = "?STXT=".$STXT."&RD=4&idx=".$idx;

			include_once("list2.php");
		BREAK;
	}
?>

</div>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>