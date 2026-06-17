<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/m/inc/config.php";
INCLUDE HelloRoot."/inc/inc_header.php";
//메인

SWITCH($RD)
{
	CASE "1" : // 투자하기
		$strFile =  "index_deposit.html";
		$gstrTopMenuActive2 = " class='active'";
	BREAK;
}

$KD = "2";
INCLUDE HelloRoot."/inc/inc_html.php";

IF($strFile)
{
	INCLUDE HelloHtmlLink."/investment/".$strFile;
}
?>

<?php
INCLUDE HelloRoot."/inc/inc_footer.php";
?>