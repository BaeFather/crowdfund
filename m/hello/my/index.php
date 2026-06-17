<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/m/inc/config.php";
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_header.php";
//메인

IF($gstrLoginKind == true)
{

} ELSE {
	gourl("",HelloHelloLink."/investment/");
	EXIT;
}
?>

<?php
INCLUDE $_SERVER["DOCUMENT_ROOT"].HelloRoot."/inc/inc_footer.php";
?>