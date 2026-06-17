<?php
	DEFINE("HelloRoot",$_SERVER["DOCUMENT_ROOT"]."/m");
	DEFINE("HelloFront","/m");

	DEFINE("HelloHelloLink", HelloRoot."/hello");
	DEFINE("HelloHtmlLink", HelloRoot."/content/html");

	DEFINE("HelloContentLink", HelloFront."/content");
	DEFINE("HelloImageLink",HelloFront."/content/img");


	// 주메뉴 링크
	DEFINE("HelloMenu1", HelloFront."/deposit/");
	DEFINE("HelloMenu2", HelloFront."/investment/");
	DEFINE("HelloMenu3", HelloFront."/review/");
?>