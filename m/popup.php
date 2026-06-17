<? INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/inc_header.php"; ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>대운건설</title>
	<meta charset="utf-8">
	<meta http-equiv="content-type" content="text/html">
	<script type="text/javascript" src="/js/common.js"></script>
	<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" />

</head>
<body>
<?
	$seq	=	$_GET["seq"];
	IF(!$seq) 
	{
		alert_close("접근이 잘못되었습니다");
		ECHO"</body></html>";
		exit;
	}

	IF($seq)
	{
		$Query = "SELECT * FROM ex_main_popup WHERE seq='".$seq."'";

		$Result = sql_query($Query,$connect);

		IF($Row=sql_fetch_array($Result))
		{
			$seq			=	$Row["seq"];
			$repimg			=	$Row["repimg"];
			$linkurl		=	$Row["linkurl"];
			$linktype		=	$Row["linktype"];
			$popname		=	$Row["popname"];
			$content		=	$Row["content"];
			$popw			=	$Row["popw"];
			$poph			=	$Row["poph"];
			$popx			=	$Row["popx"];
			$popy			=	$Row["popy"];
			$popexpdate		=	$Row["popexpdate"];

			sql_free_result($Result);
		}
	}
	sql_close($connect);
?>
<style>
	html{overflow:hidden}
	.pop_area01 {width:<?=$popw?>px;height:<?=$poph?>px;padding:0px;margin:0px;text-align:left;}
	.pop_area02 {width:100%;background-color:#000000;color:#FFFFFF;padding:7px 0 7px 0;overflow:auto;}
	.pop_area02 .pop_area02_sub1 {float:right;padding:0px;margin:0px;}
	.pop_area02 .pop_area02_sub2 {float:right;padding:2px 0 0 0;margin:0px;}
	.pop_area02 .pop_area02_sub3 {float:right;padding:2px 10px 0 0;margin:0px;}
	.fwhite {color:#FFFFFF;}
</style>

<div class="pop_area01">
	<? IF($repimg) { ?>
		<? IF($linktype == "opener") { ?>
		<a href="javascript:check_url('<?=$linkurl?>','<?=$linktype?>');"><img src="/iFile/popup/<?=$repimg?>"></a>
		<? } ELSE { ?>
		<a href="<?=$linkurl?>" target="<?=$linktype?>" OnClick="check_url('','<?=$linktype?>');"><img src="/iFile/popup/<?=$repimg?>"></a>
		<? } ?>
	<? } ?>
	<? IF($content) { ?>
		<?=$content?>
	<? } ?>
</div>
<div class="pop_area02">
	<div class="pop_area02_sub3"><a href="javascript:popup_close('<?=$popname?>');"><span class="fwhite">[닫기]</span></a></div>
	<div class="pop_area02_sub1"><input type="checkbox" name="expirehours<?=$popname?>" id="expirehours<?=$popname?>" value="<?=$popexpdate?>"></div>
	<div class="pop_area02_sub2"><?=$popexpdate?> 일동안 이 창을 다시 열지 않음.</div>
</div>
<script>
	function check_url(obj1,obj2)
	{
		if(obj2=="opener")
		{
			opener.window.location=obj1;
			self.close();
		} else {
			self.close();
		}
	}
</script>
</body>
</html>