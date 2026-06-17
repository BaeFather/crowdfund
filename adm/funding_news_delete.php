<?php

$sub_menu = '300300';
include_once('./_common.php');


if(isset($_GET['idx']) && $_GET['idx'] != '') {


	$sql = "DELETE FROM funding_news_list WHERE idx = '{$_GET['idx']}'";

	sql_query($sql);

	alert("삭제되었습니다.","./funding_news_list.php");

}else {
	goto_url("./funding_news_list.php");
}








?>