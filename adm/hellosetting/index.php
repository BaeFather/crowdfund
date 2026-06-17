<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
include_once('./hellosetting.class.php');

$sub_menu = '910800';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][8][1];

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

			$strInputText1		= "text";
			$strRadioText		= "radio";
			$strSelectBox		= "";
			$strPassword		= "password";
			/*
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
			*/

			$intSeqName	=	"hcsseq";
			$strColumn	=	ARRAY(
									$intSeqName, "mb_id","hmseq","title","addr_si","addr_yn","addr_gu","rec_date","reg_date","recyn","hcssseq","ltvs","ltvl","ms","ml","period"
							);

			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = "";
			}

			IF($SE)
			{
				$strTable	=	"
					(
						SELECT
						t1.hcsseq, t1.mb_id, t1.hmseq, t1.title, t1.addr_si, t1.addr_yn, t1.addr_gu, t1.rec_date,t1.reg_date, t1.recyn, t1.period, IFNULL(t2.hcssseq,0) as hcssseq, IFNULL(t2.ltvs,'') ltvs,IFNULL(t2.ltvl,'') ltvl,IFNULL(t2.ms,'') ms,IFNULL(t2.ml,'') ml
						FROM hloan_content_setting t1 LEFT JOIN hloan_content_setting_slave t2
						ON t1.hcsseq = t2.hcsseq WHERE t1.hcsseq='".add_str($SE)."'
					) t1
				";

				$strWhere	=	"";
				$strOrder	=	$intSeqName.",ltvs";
				$intLimit1	=	0;
				$intLimit2	=	100;
				$intStrlen	=	100;

				$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

				IF($rowView[0][$intSeqName])
				{
					FOR($j=0;$j<COUNT($rowView);$j++)
					{
						FOR($i=0;$i<COUNT($strColumn);$i++)
						{
							${$strColumn[$i]}[$j] = $rowView[$j][$strColumn[$i]];
						}
					}
				} ELSE {
					alert_back("접근이 올바르지 않습니다","-1");
					EXIT;
				}

				$strKind			=	"update";
				$strBtnTxt			=	"수정하기";
			}

			$strListUrl = "?S2=".$S2."&STXT=".$STXT."&page=".$page;
			$num_per_page = 1000;

			// 히스토리 리스트
			$strHistoryList = $ClassHelloSetting->fn_setting_history($SE);

			include_once("detail.php");
			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$num_per_page = 20;
			$intSeqName = "hcsseq";
			$strColumn	= ARRAY($intSeqName,"hmseq","title","title","addr_si","rec_date","recyn","period");
			$strTable	= "hloan_content_setting";
			$frQuery	= "";
			$strWhere	= "";

			$strOrder	=	$intSeqName." DESC";
			$strlimit2	=	$num_per_page;

			$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

			$total_count  = $rowList[1];
			$total_page	=	$rowList[0];

			$qstr = "?S2=".$S2."&STXT=".$STXT;

			include_once("list.php");

		BREAK;
	}
?>

</div>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>