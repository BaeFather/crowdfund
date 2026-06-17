<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
.selectarea {width:180px;padding:5px 0;}
label {float:left;display:block;padding:5px 5px;}
.fred {color:#ff0000;}
.circleArea {position:absolute;margin-left:0px;background-color:#0000ff;border-radius:30px;color:#FFF;font-weight:bold;width:20px;height:20px;border:0px;cursor:pointer;}
.input01 {width:100%;border-radius:3px;line-height:24px;font-size:14px;text-align:left;border:1px solid #333;}
.input02 {width:95%;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:0 auto;}
.input02::placeholder {text-align:center;}
.input04 {width:98%;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:0 auto;}

</style>
	<script type="text/javascript" src="eventbanner.js?ver=<?php ECHO RAND(10000000000,99999999999);?>"/></script>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" enctype="multipart/form-data">
	<input type="hidden" name="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="SE" value="<?php ECHO $SE;?>" />
	<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
	<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
	<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
	<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
	<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th class="tdtop">상태</th>
			<td><?php ECHO fn_general_select($recyn,$strSelectBox,$gstrHelloEventRequest->FnRecyn(),"상태 ▼","recyn","class='input02'","");?></td>
		</tr>
		<tr>
			<th class="tdtop">아이디</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"mb_id","input04","","",$mb_id);?></td>
		</tr>
		<tr>
			<th class="tdtop">이름</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"mb_name","input04","","",$mb_name);?></td>
		</tr>
		<tr>
			<th class="tdtop">연락처</th>
			<td class="tdL">
				<span id="hp<?=$i?>" onMouseOver="swapText('hp<?=$i?>','<?=$mb_phone?>');" onMouseOut="swapText('hp<?=$i?>','<?=$mb_phoneval?>');" onClick="copy_trackback('<?php ECHO $mb_phone;?>');" style="cursor:pointer" <?=$mb_phone?>><?=$mb_phoneval?></span>
			</td>
		</tr>
		<tr>
			<th class="tdtop">등록일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"reg_date","input02","","",$reg_date);?></td>
		</tr>
		<tr>
			<th class="tdtop">관리자코멘트</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strTextarea,"admin_comment","input02","","",$admin_comment);?></td>
		</tr>
		<tr>
			<th class="tdtop">등록일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"reg_date","input02","","",$reg_date);?></td>
		</tr>
	</table>


	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="check_w_form('regfm',event);return false;" class="btn btn-default"><?php ECHO $strBtnTxt;?></button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	</form>

	</div>
	<!-- 코멘트 //-->

	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>