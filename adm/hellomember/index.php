<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '910400';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][4][1];

//$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';
$g5['title'] = "업체관리";

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

			$strKind			=	"save";
			$strBtnTxt			=	"등록하기";

			IF($RD == "2")
			{
				$strInputText1		= "txt1";
				$strRadioText		= "txt";
				$strSelectBox		= "txt";
				$strPassword		= "txt";
			} ELSEIF($RD == "3") {
				$strInputText1		= "text";
				$strRadioText		= "radio";
				$strSelectBox		= "";
				$strPassword		= "password";
			}

			$intSeqName	=	"hmseq";
			$strColumn	=	ARRAY(
									$intSeqName, "phmseq","hid","cname","hname","hphone","reg_date","recyn","level"
							);

			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = "";
			}

			IF($idx)
			{
				$strTable	=	"hloan_member";
				$strWhere	=	" WHERE hmseq='".add_str($idx)."'";
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
				} ELSE {
					alert_back("접근이 올바르지 않습니다","-1");
					EXIT;
				}

				$strKind			=	"update";
				$strBtnTxt			=	"수정하기";
			}

			$strListUrl = "?S2=".$S2."&STXT=".$STXT."&page=".$page;

			include_once("detail.php");
			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$num_per_page = 20;
			$intSeqName = "hmseq";
			$strColumn	= ARRAY($intSeqName,"phmseq","hid","cname","hname","hphone","reg_date","recyn","level","cname2");
			$strTable	= "
			(
			SELECT st1.hmseq,st1.phmseq, hid, st1.cname, hname, hphone, reg_date, recyn, level, IFNULL(st2.cname,'') as cname2 FROM
			(SELECT hmseq,phmseq, hid, cname, hname, hphone, reg_date, recyn, level FROM hloan_member)  st1 LEFT JOIN (SELECT hmseq, cname FROM hloan_member WHERE level='2') st2 ON st1.phmseq=st2.hmseq
			) t1
			";
			$frQuery	= "";

			IF($STXT) {
				IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
				IF($S2)
				{
					$strWhere .= "(".$S2." LIKE '%".add_str($STXT)."%')";
				} ELSE {
					$strWhere .= "(cname LIKE '%".add_str($STXT)."%' OR hname LIKE '%".add_str($STXT)."')";
				}
			}
			$strOrder	=	$intSeqName." DESC";
			$strlimit2	=	$num_per_page;

			$strHelloKind = fn_hellloan_kind();

			$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

			$total_count  = $rowList[1];
			$total_page	=	$rowList[0];

			$intCompanyCnt	=	fn_hloan_member_cnt($connect_for);

			$qstr = "?S2=".$S2."&STXT=".$STXT;

			include_once("list.php");

		BREAK;
	}
?>

</div>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>