<?php // 내용 ?>

<style>
	
		
	#content .top_title {font-size:30px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 40px 0 10px; background-color: #fff;}
	#content .top_title .sky {color:#33a5ed;}
	#content .top_text {font-size:17px; color:#777; padding-bottom: 36px; font-family:'SpoqaHanSans','sanserif'}	
	
	.ev_guide1 {width:100%;overflow:hidden;background-color:#fff;}
	.evtitle {font-size:23px;font-family:'spoqahansans'; font-weight:300; letter-spacing: -1px; width:1150px; margin:30px auto 0; text-align:left; padding-left: 25px;}
	.ev_guide {width:1150px;margin:0 auto;}
	.ev_ul {list-style:none;margin:5px 0 50px 15px;overflow:hidden;display:table}
	.ev_ul .evli {float:left;width:350px;height:420px;padding-right:35px;overflow:hidden;margin:35px 0px 0px 0px; margin:10px 0 0 0;}
	.ev_ul .evli2 {height:350px;margin:10px 0px 0px 0px;}
	.ev_ul .last {padding:0px;}
	.ev_ul .evli .ev_area {list-style:none;overflow:hidden;width:100%;}
	.ev_ul .evli .ev_area .evli_pic {width:350px;height:300px;cursor:pointer;text-align:center;}
	.ev_ul .evli .ev_area .evli_txt {width:350px;height:120px;padding-top:20px;cursor:pointer;}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea {width:100%;overflow:hidden;}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea .evlis_txt1{float:left;width:55px;height:55px;border-radius:32px;background:#efeeec;color:#222;font-size:16px;line-height:55px;font-family:'spoqahansans';}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea .evlis_txt2{float:right;width:286px;}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea .evlis_txt2 .evlis_txt2_1{width:98%;text-align:left;font-size:17px;color:#222; overflow:hidden;font-family:'spoqahansans';float:right; letter-spacing:-0.5px; padding-top: 3px;}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea .evlis_txt2 .evlis_txt2_2{width:98%;text-align:left;font-size:15px;color:#777;padding-top:1px;font-family:'spoqahansans';float:right;}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea .evlis_txt3 {width:100%;text-align:left;font-size:16px;color:#333333;padding-top:4px;height:32px;overflow:hidden;font-family:'spoqahansans';}
	.ev_ul .evli .ev_area .evli_txt .ev_sarea .evlis_txt4 {width:100%;text-align:left;font-size:18px;color:#00a0e9;padding-top:4px;font-family:'spoqahansans';}

	.ev_ul .evli .ev_area .evli_txt2 {width:360px;height:60px;padding-top:5px;cursor:pointer; }
	.ev_ul .evli .ev_area .evli_txt2 .ev_sarea {width:100%;overflow:hidden;}
	.ev_ul .evli .ev_area .evli_txt2 .ev_sarea .evlis_txt1 {width:100%;text-align:left;font-size:16px;color:#555;padding-top:4px;height:45px;overflow:hidden;font-family:'spoqahansans';}
	.ev_ul .evli .ev_area .evli_txt2 .ev_sarea .evlis_txt2 {width:100%;text-align:left;font-size:16px;color:#00a0e9;padding-top:4px;font-family:'spoqahansans';}

	.ev_guide2 {width:100%;overflow:hidden;background-color:#f3f3f3; padding-top: 30px;}
	.listrepimg {width:350px;height:300px;}
	.listrepimg2 {width:350px;height:300px;opacity:0.4;}

	.bgcl {overflow:hidden;background-color:#000;}

	.btn-other {width:100px;background:#222;font-size:16px;line-height:32px;height:32px;border-radius:16px;color:#FFF;margin:50px auto;}

	@media all and (max-width: 900px){
		#content {text-align: center; width:100%;}
		#content .top_title {font-size:24px; color:#333; letter-spacing:-1px; font-weight: 400; padding: 20px 0 10px; background-color: #fff;}
		#content .top_title .sky {color:#33a5ed;}
		#content .top_text {font-size:14px; color:#777; font-family:'SpoqaHanSans','sanserif'}
		#content .top_text .del {display: none;}
		
		.evtitle {font-size:20px;font-family:'spoqahansans'; font-weight:300; letter-spacing: -1px; width:1150px; margin:20px auto 0; text-align:left; padding-left: 20px;}
		.ev_guide {width:100%;margin:0 auto;}
		.ev_ul  {width:100%; margin:5px auto 50px;}
		.ev_ul .evli {float:none;width:90%;height:auto;padding:0px;margin:5px auto 20px;}
		.ev_ul .evli .ev_area .evli_pic {width:100%;height:auto;}
		.listrepimg {width:100%;height:auto;}
		.listrepimg2 {width:100%;height:auto;opacity:0.7;}
	}

</style>

	<div class="ev_guide1">
		<!--div class="evtitle">진행중 이벤트</div-->
		<div class="ev_guide">
		<ul class="ev_ul">
<?php

	IF($rowList[1] > 0)
	{
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			UNSET($RowLink);
			UNSET($RowTarget);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}

			IF($linkurl)
			{
				$RowLink = $linkurl."?SE=".$idx;
				$RowTarget = $target;
			} ELSE {
				$RowLink = $qstr."&SE=".$idx;
				$RowTarget = "_self";
			}

			IF($i > 0 && (($i+1) % 3 == 0))
			{
				$strClass ="evli last";
			} ELSE {
				$strClass ="evli";
			}

			IF(G5_IS_MOBILE == true)
			{
				$strImg = $strEventClass->FnRepimg($ifile,2,"/data/fevent");
			} ELSE {
				$strImg = $strEventClass->FnRepimg($ifile,0,"/data/fevent");
			}
			$intDate = $strEventClass->dateDifference(DATE("Y-m-d"),$edate)+1;

			IF($intDate >= 60)
			{
				$strDateTxt = "<span style='font-size:30px;'>∞</span>";
			} ELSE {
				$strDateTxt = "D-".$intDate;
			}
?>
			<li class="<?php ECHO $strClass;?>">
				<a href="<?php ECHO $RowLink;?>" target="<?php ECHO $RowTarget;?>">
				<ul class="ev_area">
					<li class="evli_pic">
						<img src="<?php ECHO $strImg;?>" class="listrepimg" />
					</li>

					<?php IF(G5_IS_MOBILE == false) { ?>
					<li class="evli_txt">
						<ul class="ev_sarea">
							<!--li class="evlis_txt1"><?php ECHO $strDateTxt;?></li-->
							<li class="evlis_txt1">진행중</li>
							<li class="evlis_txt2">
								<div class="evlis_txt2_1"><?php ECHO $title?></div>
								<div class="evlis_txt2_2"><?php ECHO $sdate?> ~ <?php if($edate == '9999-12-31') echo '종료시까지'; else echo $edate ?></div>
							</li>
						</ul>
					</li>
					<?php } ?>
				</ul>
				</a>
			</li>
<?php
		}
	}
?>
		</ul>
		</div>
	</div>
<?php IF(G5_IS_MOBILE == false) { ?>
<?php
	IF($rowList2[1] > 0)
	{
?>
	<div class="ev_guide2">
		<div class="evtitle">종료된 이벤트 / 당첨자 발표</div>
		<div class="ev_guide">
		<ul class="ev_ul" id="ev_ul_list">
<?php
		FOR($i=0;$i<COUNT($rowList2[2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList2[2][$i][$j];
			}
			IF($linkurl)
			{
				$RowLink = $linkurl;
				$RowTarget = $target;
			} ELSE {
				$RowLink = $qstr."&SE=".$idx;
				$RowTarget = "_self";
			}

			IF($i > 0 && (($i+1) % 3 == 0))
			{
				$strClass ="evli evli2 last";
			} ELSE {
				$strClass ="evli evli2";
			}

			IF(G5_IS_MOBILE == true)
			{
				$strImg = $strEventClass->FnRepimg($ifile,2,"/data/fevent");
			} ELSE {
				$strImg = $strEventClass->FnRepimg($ifile,0,"/data/fevent");
			}
?>

			<li class="<?php ECHO $strClass;?>">
				<a href="<?php ECHO $RowLink;?>" target="<?php ECHO $RowTarget;?>">
				<ul class="ev_area">
					<li class="evli_pic bgcl">
						<img src="<?php ECHO $strImg;?>" class="listrepimg2" />
					</li>
					<li class="evli_txt2">
						<ul class="ev_sarea">
							<li class="evlis_txt1"><?php ECHO $title?></li>
						</ul>
					</li>
				</ul>
				</a>
			</li>

<?php
		}
?>
		</ul>
		</div>
		<button type="button" class="btn-other" onClick="check_hevent_list(event);">+ 더보기</button>

	</div>
	<script>
		var heventProcessUrl = "/hevent/cquery.php";
		var page = 1;
		function check_hevent_list(event)
		{
			if(event.stopPropagation)
			{
				event.preventDefault();
				event.stopPropagation();
			} else {
				event.cancelBubble = true;
			}

			var str="&page="+page+"&section=1";

			$.ajax({
				type : 'POST',
				url : heventProcessUrl,
				data : str,
				dataType: 'json',
				success : function(data){

					if(data.retcode == "OK"){
						var targetlayer = $("#ev_ul_list");
						var strcontent = "";

						if(data.rettotal.length > 0)
						{
							targetlayer.append(decodeURIComponent(data.retval));
							page = data.page;
						} else {
							alert("더이상 더보기 리스트가 없습니다.");
						}


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
	</script>
<?php
	}
?>
<?php } ?>