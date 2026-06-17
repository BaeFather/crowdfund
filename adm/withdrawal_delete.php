<?
$sub_menu = "500200";
include_once("./_common.php");

check_demo();

auth_check($auth[$sub_menu], "d");


if(!$_GET['idx'] || $_GET['idx'] == '') {
	alert("잘못된 접근입니다..","./withdrawal_list.php");
}else {

	$sql = " DELETE FROM g5_withdrawal WHERE idx = {$_GET['idx']}";

	sql_query($sql);

	alert("삭제되었습니다.","./withdrawal_list.php");
}
?>
