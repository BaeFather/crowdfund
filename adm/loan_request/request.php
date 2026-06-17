<?

include_once('./_common.php');

$sub_menu = '910100';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][1][1];

$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';


// 받은 데이터를 변수화
foreach($_REQUEST as $k=>$v) { ${$_REQUEST[$k]} = $v; }


$TYPE     = array('1'=>'아파트담보대출신청', '2'=>'취급법인유동화신청');
$RELATION = array('1'=>'본인', '2'=>'가족', '3'=>'중개인');
$PERIOD   = array('6'=>'6개월', '9'=>'9개월', '12'=>'12개월', '12+'=>'12개월 초과');
$PURPOSE  = array('1'=>'기대출상환', '2'=>'기대출상환 및 추가대출', '3'=>'선순위대출', '4'=>'사업자금', '5'=>'전세퇴거자금', '6'=>'기타');
$JSTATE   = array('1'=>'대기중', '2'=>'진행중', '3'=>'부결', '4'=>'승인');

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

if($countUp) {
	sql_query("UPDATE cf_apat_loan_request SET view=view+1 WHERE idx='".$idx."'");
}

//print_rr($_COOKIE);

include_once (G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

<?
if($idx!='') {
	IF($_GET["skin"] == "2")
	{
		$strIncludeUrl = "request.detail2.php";
	} ELSE {
		$strIncludeUrl = "request.detail.php";
	}

	include_once($strIncludeUrl);
	echo "<br /><br />\n";
}

include_once("request.list.php");
?>

</div>

<?

include_once (G5_ADMIN_PATH.'/admin.tail.php');

?>