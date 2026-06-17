<?php
include_once('./_common.php');
$RT		=	$_GET["RT"];

IF(!$RT || STRLEN($RT) < 10)
{
	ECHO "접근이 올바르지 않습니다";
	EXIT;
}

IF(!get_cookie("REPORT_ID") || (get_cookie("REPORT_ID") <> $RT))
{
?>
	<html>
	<head>
	<title>헬로펀딩 상품 투자 요약보고</title>
	<meta name='viewport' content='width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=2.0,user-scalable=yes'>
	<meta name='mobile-web-app-capable' content='yes'>
	<meta name='apple-mobile-web-app-capable' content='yes'>
	<link href='//fonts.googleapis.com/css?family=Nanum+Gothic:400,700,800&amp;subset=korean' rel='stylesheet'>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha384-rY/jv8mMhqDabXSo+UCggqKtdmBfd3qC2/KvyTDNQ6PcUJXaxK1tMepoQda4g5vB" crossorigin="anonymous"></script>
	<link href='/css/report.css?ver=<?php ECHO DATE('YmdHis')?>' rel='stylesheet'>
	</head>
	<body>
	<form name="authregform" id="authregform">
	<input type="hidden" name="RT" value="<?php ECHO $RT;?>" />
		<div class="login_guide">
			<div class="login_logo_area"><img src="/theme/2018/img/main/logo.png" /></div>
			<table class="login_table">
			<tr>
				<td><input type="password" name="passwd" placeholder="비밀번호" class="input1" /></td>
			</tr>
			<table>

			<input type="submit" name="login_btn" class="login_btn" value="인증하기" OnClick="check_report_login('authregform',event);" />
		</div>
	</form>
	</body>
	</html>

	<script type="text/javascript">
	<!--
	var reportLinkUrl = "/hello_report/process.php";

	function check_report_login(fmname, event)
	{
		if(!event)
		{
		   event =window.event;
		}
		if(event.stopPropagation)
		{
			event.preventDefault();
			event.stopPropagation();
		} else {
			event.cancelBubble = true;
		}

		var passwd =  $("input[name='passwd']");
		if(!passwd.val())
		{
			alert('비밀번호를 입력하여 주십시오');
			return false;
		}

		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : reportLinkUrl,
			data : str,
			dataType: 'json',
			success : function(data){

				if(data.retcode == "OK"){
					window.location = data.retval;

				} else if(data.retcode == "X") {
					var stralert = decodeURIComponent(data.retalert);
						alert(stralert.replace("+"," "));

				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
				console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
				console.log(errorThrown);
				return false;
			}
		});
	}
	//-->
	</script>

<?php

} ELSE {

	if (!$today) $today = date("Y-m-d");
	$intRT = SUBSTR($RT,0,10);
	//$Query = "SELECT t2.title, t2.content FROM cf_product_admin_report_send t1 LEFT JOIN cf_product_admin_report t2 ON t1.pidx=t2.pidx WHERE t1.send_time='".addslashes($intRT)."'";
	$Query = "SELECT t2.title, t2.content FROM cf_product_admin_report t2 WHERE t2.title  like 'SCF 상품 투자요약%' and substring(t2.reg_time,1,10)='$today' ";
	$Result = sql_query($Query);

	IF($Row=sql_fetch_array($Result))
	{
		$strContent	=	$Row["content"];
		sql_free_result($Result);
	}

	sql_close($connect);

	IF($strContent)
	{
		ECHO $strContent;
		echo "<br/><br/>";
	} ELSE {
		//echo $Query."<BR><BR>";
		ECHO "등록된 항목이 없습니다";
		EXIT;
	}
}
?>