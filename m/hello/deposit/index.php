<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/m/inc/config.php";
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_header.php";
//메인

$strSubRoot = $_SERVER["DOCUMENT_ROOT"].HelloHtmlLink;

SWITCH($RD)
{
	CASE "1" : // 투자하기
		$strFile =  "index_mystatus.html";
		$gstrTopMenuActive1 = " class='active'";
	BREAK;
}

$KD = "1";
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_html.php";

IF($strFile)
{
	INCLUDE $strSubRoot."/deposit/".$strFile;
}
?>

<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_footer.php";
?>