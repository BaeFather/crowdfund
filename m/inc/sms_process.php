<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/common.php";?>
<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/function.php";?>
<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/function_add.php"; ?>
<?
	//sms처리페이지

	$SE		=	$_POST["SE"];
	$kind	=	$_POST["kind"];
	$content	=	$_POST["smscontent"];
	$strval		=	$_POST["strval"];

	sql_conn();

	IF($kind == "save")
	{
		IF(!$content) { alert("값을 입력해야 합니다"); exit; } 
		$Query = "INSERT INTO sms_msg (content) values ('".add_str($content)."')";
	}

	IF($kind == "update")
	{
		IF(!$SE) { alert("접근이 올바르지 않습니다. 다시 시도하여 주십시오"); exit; } 

		$Query = "UPDATE sms_msg SET content = '".add_str($content)."' WHERE seq='".$SE."' ";
	}
	IF($kind == "del")
	{
		IF(!$SE) { alert("접근이 올바르지 않습니다. 다시 시도하여 주십시오"); exit; } 

		$Query = "DELETE FROM sms_msg WHERE seq='".$SE."' ";
	}
	sql_query($Query, $connect);
	alert_confirm_parent("정상처리되었습니다",$strval);
	exit;
?>