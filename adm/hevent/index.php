<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '30000';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu300'][5][1];

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

$strEventClass	=	new Event_Board();
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
				$strSelectBox2		= "";
				$strSelectBox3		= "label";
				$strPassword		= "txt";
				$strInputTextarea	= "txt2";
				$strFileText		= "fileImgatt";
				$strFileText2		= "fileImgatt";
			} ELSEIF($RD == "3") {
				$strInputText1		= "text";
				$strRadioText		= "radio";
				$strSelectBox		= "";
				$strSelectBox2		= "txt";
				$strSelectBox3		= "";
				$strPassword		= "password";
				$strInputFile		= "file";
				$strInputTextarea	= "textarea";
				$strFileText		= "fileImg";
				$strFileText2		= "sfileImg";

				INCLUDE $_SERVER["DOCUMENT_ROOT"]."/plugin/editor/smarteditor2/editor.lib.php";

				$editor_js = '';
				$editor_js .= get_editor_js('wr_content', true);
				$editor_js .= chk_editor_js('wr_content', true);

				$editor_js2 = '';
				$editor_js2 .= get_editor_js('wr_content2', true);
				$editor_js2 .= chk_editor_js('wr_content2', true);

			}

			IF($SE)
			{
				$strKind			=	"update";
				$strBtnTxt			=	"수정하기";

				$strColumn	=	ARRAY(
								"idx", "title","content","reg_date","update_date",
								"sdate","edate","ifile","linkurl","target",
								"sort_id","recyn","mainyn","contentm","mainmyn"
						);


				$row = $strEventClass->FnView($SE, $strColumn);

				IF(!$row["idx"])
				{
					alert("접근이 올바르지 않습니다",-1);
					exit;
				}

				$strIFile =		EXPLODE("^",$row["ifile"]);

			} ELSE {
				$strKind			=	"save";
				$strBtnTxt			=	"등록하기";
				$row["reg_date"]	=	DATE("Y-m-d H:i:s");
			}

			$strListUrl = "?SC=".$SC."&SCC=".$SCC."&STXT=".$STXT."&page=".$page;

			include_once("detail.php");
			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$strColumn	=	ARRAY(
								"idx", "title","content","reg_date","update_date",
								"sdate","edate","ifile","linkurl","target",
								"sort_id","recyn","mainyn","mainmyn"
						);

			$strSearch = ARRAY("STXT"=>$STXT, "SC"=>$SC, "SCC"=>$SCC);

			$num_per_page = 15;

			IF(!$page) { $page = 1; }

			$rowList = $strEventClass->FnList($strSearch, $page, $strColumn);

			$total_page		=	$rowList[0];
			$total_count	=	$rowList[1];

			$qstr = "?SC=".$SC."&SCC=".$SCC."&STXT=".$STXT."&page=".$page;

			include_once("list.php");
		BREAK;
	}
?>

</div>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>