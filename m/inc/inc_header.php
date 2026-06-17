<?php INCLUDE HelloRoot."/inc/common.php"; ?>
<?php INCLUDE HelloRoot."/inc/function.php"; ?>
<?php INCLUDE HelloRoot."/inc/function_add.php"; ?>
<?php
	if(!$_SERVER["HTTPS"])
	{
		$Us = "https://".STR_REPLACE("www.","",$gstrHttpHost); //이동경로
		$Ds = (strpos($Us,'?') !== false) ? '&' : '?';
		$Qs = ($_SERVER['QUERY_STRING'] ? $Ds.$_SERVER['QUERY_STRING'] : '');
		header('Location: '.$Us.$Qs);
		exit;
	}
	IF(!$gstrSessionReffer)
	{
		IF(preg_match("#".$gstrMainUrl."#",$gsterREFFER))
		{
		} ELSE {
			IF($gsterREFFER)
			{
				$_SESSION["sreffer"] = $gsterREFFER;
				$gstrSessionReffer = $gsterREFFER;
			}
		}
	}

	// 레퍼 저장  $intRefferSeq 레퍼 고유값 리턴
	sql_conn();

	$gstrTopMenuActive1 = "";
	$gstrTopMenuActive2 = "";
	$gstrTopMenuActive3 = "";
?>