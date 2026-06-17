<?
include_once('./_common.php');
include_once('../lib/function_prc.php');
include_once('./review.class.php');

$g5['title'] = "5월 친구초대 첫투자 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$SE = $_POST["SE"];
$RD = $_POST["RD"];

IF(!$SE) { $SE = clean_xss_tags($_GET["SE"]); }
IF(!$RD) { $RD = clean_xss_tags($_GET["RD"]); }



$page		=	$_POST["page"];
$section	=	$_POST["section"];

$viewy		=	$_POST["viewy"]; //높이값

$pkd	=	$_POST["pkd"];	// 리스트 백버튼시 더보기 구현

IF(!$SE || !$RD)
{
	include_once('./list.php');
} ELSEIF($SE && $RD == "2") {
	include_once('./view.php');
}


if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>
