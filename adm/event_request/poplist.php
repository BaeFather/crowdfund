<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$SE	=	$_GET["SE"];

IF(!$SE)
{
	ECHO "접근이 올바르지 않습니다.";
	EXIT;
}
$Query = "SELECT title, content FROM cf_product_admin_report WHERE pidx='".addslashes($SE)."'";
$Result = sql_query($Query);

IF($Row=sql_fetch_array($Result))
{
	$strContent	=	$Row["content"];
	sql_free_result($Result);
}

sql_close($connect);

IF($strContent)
{
} ELSE {
	ECHO "등록된 항목이 없습니다";
	EXIT;
}
?>

	<html>
	<head>
	<title>헬로펀딩 상품 투자 요약보고</title>
	<meta name='viewport' content='width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=2.0,user-scalable=yes'>
	<meta name='mobile-web-app-capable' content='yes'>
	<meta name='apple-mobile-web-app-capable' content='yes'>
	<link href='https://fonts.googleapis.com/css?family=Nanum+Gothic:400,700,800&amp;subset=korean' rel='stylesheet'>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha384-rY/jv8mMhqDabXSo+UCggqKtdmBfd3qC2/KvyTDNQ6PcUJXaxK1tMepoQda4g5vB" crossorigin="anonymous"></script>
	<link href='/css/report.css?ver=<?php ECHO DATE('YmdHis')?>' rel='stylesheet'>
	</head>
	<body>
	<?php ECHO $strContent;?>
	</body>
	</html>
<?php
	exit;
?>