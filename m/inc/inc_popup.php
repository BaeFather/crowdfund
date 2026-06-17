<?
	$Query2 = "SELECT seq,popkind,popname,popw,poph,popx,popy,repimg,linkurl,linktype,content,popexpdate FROM ex_main_popup WHERE (sdate <='".substr($gstrNdate,0,10)."' AND ldate >='".substr($gstrNdate,0,10)."') AND recyn='Y'";
	if($gstrMobileCheck == true)
	{
		$Query2 .= " AND myn='Y'";
	}
	$Result2 = sql_query($Query2,$connect);

	$i = 1;
	WHILE($Row2=sql_fetch_array($Result2))
	{
		$seq			=	$Row2["seq"];
		$popkind		=	$Row2["popkind"];
		$popname		=	$Row2["popname"];
		$popw			=	$Row2["popw"];
		$poph			=	$Row2["poph"]+32;
		$popx			=	$Row2["popx"];
		$popy			=	$Row2["popy"];

		$repimg			=	$Row2["repimg"];
		$linkurl		=	$Row2["linkurl"];
		$linktype		=	$Row2["linktype"];
		$content		=	$Row2["content"];
		$popexpdate		=	$Row2["popexpdate"];

			IF($popkind == "P")	// 일반팝업
			{
?>
		<script type="text/javascript">
			var NoticePop<?=$i?> = get_cookie("NoticePop_<?=$popname?>");

			if(!NoticePop<?=$i?> || (NoticePop<?=$i?> != "done"))	
			{
				open_window_custom('popup.php?seq=<?=$seq?>','<?=$popname?>','<?=$popw?>','<?=$poph?>','<?=$popx?>','<?=$popy?>','no');
			}
		</script>
<?
			}
			IF($popkind == "L")	// 레이어 팝업
			{
				$poptoppadding = "7";
				$popx = $popx."px";
				$popw = $popw."px";
				$popw2 = $popw."px";
				$poph = $poph-32;

				if($gstrMobileCheck == true)
				{
					$poptoppadding = "0";
					$popx = "3%";;
					$popy = $popy+120;
					$popw = "94%";
					$popw2 = "100%";
					$poph = ($poph / 2)+6;
					$poptoppadding = "20";
				}
?>
			<style>
				#<?=$popname?> {position:absolute;top:<?=$popy?>px;left:<?=$popx?>;width:<?=$popw?>;overflow:hidden;border:1px solid #333333;background:#FFFFFF;}
				#<?=$popname?> .pop_area01 {width:<?=$popw2?>;padding:0px;margin:0px;text-align:left;overflow:hidden;}
				#<?=$popname?> .pop_area02 {width:100%;background-color:#000000;color:#FFFFFF;padding:<?=$poptoppadding?>px 0 7px 0;overflow:hidden;}
				#<?=$popname?> .pop_area02 .pop_area02_sub1 {float:right;padding:2px 10px;margin:0px;}
				#<?=$popname?> .pop_area02 .pop_area02_sub2 {float:right;padding:2px 0 0 0;margin:0px;font-size:13px;}
				#<?=$popname?> .pop_area02 .pop_area02_sub3 {float:right;padding:2px 10px 0 0;margin:0px;font-size:13px;}
				.fwhite {color:#ffffff;}
			</style>

			<div id="<?=$popname?>" style="display:none;z-index:10000">
				<div class="pop_area01">
				<? IF($repimg) { ?>
					<? IF($linkurl) { ?>
					<a href="<?=$linkurl?>" target="<?=$linktype?>" OnClick="check_url('','<?=$linktype?>');">
					<? } ?>
					<img src="/iFile/popup/<?=$repimg?>" width='100%'>
					<? IF($linkurl) { ?>
					</a>
					<? } ?>
				<? } ?>
				<? IF($content) { ?>
					<?=$content?>
				<? } ?>
				</div>
				<div class="pop_area02">
					<div class="pop_area02_sub3"><a href="javascript:popup_layer_close('<?=$popname?>');"><span class="fwhite">[닫기]</span></a></div>
					<div class="pop_area02_sub1"><input type="checkbox" name="expirehours<?=$popname?>" id="expirehours<?=$popname?>" value="<?=$popexpdate?>"></div>
					<div class="pop_area02_sub2"><?=$popexpdate?> 일동안 이 창을 다시 열지 않음.</div>
				</div>
			</div>

			<script type="text/javascript">
				var NoticePop<?=$i?> = get_cookie("NoticePop_<?=$popname?>");

				if(!NoticePop<?=$i?> || (NoticePop<?=$i?> != "done"))	
				{
					document.getElementById("<?=$popname?>").style.display = "block";
				}
			</script>
<?
			}
			$i++;
	}

?>