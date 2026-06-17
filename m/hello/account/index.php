<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/m/inc/config.php";
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_header.php";
//메인

$strSubRoot = $_SERVER["DOCUMENT_ROOT"].HelloHtmlLink;

$KD = "99";

SWITCH($RD)
{
	CASE "1" : // 나의현황
		$strFile =  "account_info.html";
		$gstrTopMenuActive1 = " class='active'";
	BREAK;
	CASE "2" : // 환급계좌 변경

	break;
	CASE "3" : // 계좌변경 완료
	break;
	CASE "4" : // 예금조회
	break;
	CASE "5" : // 예금출금
	break;
	CASE "6" : // 예금출금 완료

	BREAK;
}

INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_html.php";

IF($strFile)
{
	INCLUDE $strSubRoot."/content/account/".$strFile;
}
?>

<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_footer.php";
?>