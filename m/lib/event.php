<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/m/inc/config.php";
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_header.php";
//메인

$strSubRoot = $_SERVER["DOCUMENT_ROOT"].HelloHtmlLink;

$KD = "99";

SWITCH($RD)
{
	CASE "1" : // 계좌정보
		$strFile =  "index_mystatus.html";
		$gstrTopMenuActive1 = " class='active'";
		BREAK;
	CASE "2" : // 환급계좌 변경
	CASE "2" : // 계좌변경 완료
	CASE "2" : // 예금조회
	CASE "2" : // 예금출금
	CASE "2" : // 예금출금 완료

		BREAK;

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