<?
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
?>
<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/common.php"; ?>
<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/function.php"; ?>
<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/function_add.php"; ?>
<?
		$t_msg		=	$_POST["t_msg"];
		$s_phone	=	$_POST["s_phone"];
		$r_phone	=	$_POST["r_phone"];	// 보내는이

		$Ssdate			=	$_POST["Ssdate"];
		$Sedate			=	$_POST["Sedate"];
		$Srecyn			=	$_POST["Srecyn"];

		$Ssrdate		=	$_POST["Ssrdate"];
		$Ssedate		=	$_POST["Ssedate"];
		$STXT			=	$_POST["STXT"];
		$page			=	$_POST["page"];

		$retUrl = "/adm/index.php?KD=".$KD."&mmssearchTXT=".$mmssearchTXT."&Ssdate=".$Ssdate."&Sedate=".$Sedate;
		$retUrl .= "&Srecyn=".$Srecyn."&Ssrdate=".$Ssrdate."&Ssedate=".$Ssedate."&STXT=".$STXT."&page=".$page;

		//IF(!$t_msg || !$s_phone || !$r_phone) { ECHO alert("접근이 올바르지 않습니다.다시 시도하여 주십시오"); exit; } 

		sql_conn();
		$s_phone	=	STR_REPLACE(chr(13),",",$s_phone); 

		//$r_phone = "01023334749";
		//$s_phone = "01033342080";
		//$t_msg = "테스트";

		$retval = fn_smssend($s_phone,$r_phone,add_str($t_msg),$connect);
		sql_close($connect);

		IF($retval == "OK")
		{
			alert_confirm_parent__("정상적으로 발송 되었습니다.",$retUrl);

		} ELSE {

			alert_confirm_parent__("처리중 장애가 발생하였습니다. 다시 시도하여 주십시오",$retUrl);

		}
?>