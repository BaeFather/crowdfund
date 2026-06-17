<?php // 내용 ?>

<style>
	.ev_guide_view {width:1150px;margin:0 auto;overflow:hidden;}
	.ev_guide_view .ev_view_title_area {width:100%;overflow:hidden;margin-top:40px;}
	.ev_guide_view .ev_view_title_area .ev_area {width:100%;list-style:none;overflow:hidden;border-bottom:2px solid #00a0e9;padding-bottom:7px;}
	.ev_guide_view .ev_view_title_area .ev_area .evli_dday{float:left;width:64px;height:64px;border-radius:32px;background:#00a0e9;color:#FFF;font-size:22px;line-height:64px;font-family:'spoqahansans';}
	.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area{float:left;width:1086px;overflow:hidden;}
	.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area .ev_area2{list-style:none;width:100%;overflow:hidden;}
	.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area .ev_area2 .evli_txt1 {float:left;color:#00a0e9;font-size:25px;width:40%;text-align:left;font-weight:bold;padding-left:15px;font-family:'spoqahansans';line-height:64px;  font-weight: 300;}
	.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area .ev_area2 .evli_txt2 {float:right;color:#777777;font-size:18px;width:40%;text-align:right;font-family:'spoqahansans';line-height:64px; font-weight: 300;}
	.ev_guide_view .ev_view_title_area .ev_view_title {width:100%;color:#222222;font-size:25px;text-align:left;font-family:'spoqahansans';line-height:64px;overflow:hidden; font-weight: 300;}
	.ev_guide_view .ev_content_area {width:100%;overflow:hidden;padding:0 0 14px 0;overflow:hidden;}
	.ev_guide_view .ev_btn_area {width:100%;overflow:hidden;padding-bottom:50px;overflow:hidden;text-align:center;}
	.ev_guide_view .ev_btn_area .listbtn {width:200px;background-color:#00a0e9;font-size:20px;text-align:center;color:#FFF;padding:7px 0;border-radius:25px;border:0px;cursor:pointer;}

	@media all and (max-width: 900px){
		.ev_guide_view {width:90%;margin:0 auto;}
		.ev_guide_view .ev_view_title_area .ev_area .evli_dday{font-size:18px;}
		.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area {float:left;width:80%;}

		.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area .ev_area2 .evli_txt1 {float:none;width:100%;font-size:18px;line-height:30px;text-align:left;}
		.ev_guide_view .ev_view_title_area .ev_area .evli_txt_area .ev_area2 .evli_txt2 {float:none;width:100%;font-size:18px;line-height:30px;text-align:left;padding-left:15px;}
		.ev_guide_view .ev_view_title_area .ev_view_title {font-size:18px;text-align:center;line-height:30px;padding:20px 0;}
		.ev_content_area > p > img {width:100%;}

	}

</style>

	<div class="ev_guide_view">
		<div class="ev_view_title_area">
			<ul class="ev_area">
				<li class="evli_dday"><?php ECHO $strDateTxt;?></li>
				<li class="evli_txt_area">
					<ul class="ev_area2">
						<li class="evli_txt1"><?php ECHO $strListTxt;?></li>
						<li class="evli_txt2"><?php ECHO $strEventClass->StrDateReplace($row["sdate"],"-",".");?> ~ <?php ECHO $strEventClass->StrDateReplace($row["edate"],"-",".");;?></li>
					</ul>
				</li>
			</ul>
			<div class="ev_view_title"><?php ECHO $row["title"]; ?></div>
		</div>
		<div class="ev_content_area">
			<?php ECHO $content;?>
		</div>

		<div class="ev_btn_area">
			<input type="button" name="listbtn" class="listbtn" value="목록으로 돌아가기" OnClick="window.location='<?php ECHO $qstr;?>'" />
		</div>
	</div>