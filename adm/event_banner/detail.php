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
			<td><?php ECHO fn_general_select($recyn,$strSelectBox,$gstrHelloBanner->FnRecyn(),"상태 ▼","recyn","class='input02'","");?></td>
			<th class="tdtop">광고구분</th>
			<td><?php ECHO fn_general_select($cfcode,$strSelectBox,$gstrHelloBanner->RsSection(),"광고구분 ▼","cfcode","class='input02'","");?></td>
		</tr>
		<tr>
			<th class="tdtop">시작일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"sdate","input02 datepicker","","autocomplete='off'",$sdate);?></td>
			<th class="tdtop">종료일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"edate","input02 datepicker","","autocomplete='off'",$edate);?></td>
		</tr>
		<tr>
			<th class="tdtop">제목</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strInputText1,"title","input04","","",$title);?></td>
		</tr>
		<tr>
			<th class="tdtop">PC이미지</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strFileText,"i_file[]","input02","/data/event","",$repimg);?></td>
		</tr>
		<tr>
			<th class="tdtop">모바일 이미지</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strFileText2,"s_file[]","input02","/data/event","",$mrepimg);?></td>
		</tr>
		<tr>
			<th class="tdtop">링크경로</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"targeturl","input02","","",unique_un_replace($targeturl));?></td>
			<th class="tdtop">타겟</th>
			<td class="tdL"><?php ECHO fn_general_select($targetlink,$strSelectBox,$gstrHelloBanner->FnTarget(),"타겟▼","targetlink","class='input02'","");?></td>
		</tr>
		<tr>
			<th class="tdtop">등록일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"reg_date","input02","","",$reg_date);?></td>
			<th class="tdtop">정렬순서</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"sort_id","input02","","",$sort_id);?></td>
		</tr>
		<tr>
			<th class="tdtop">PC클릭수</th>
			<td class="tdL"><?php ECHO INPUT_FORM("txt","icount","input02","","",$icount);?></td>
			<th class="tdtop">모바일클릭수</th>
			<td class="tdL"><?php ECHO INPUT_FORM("txt","mcount","input02","","",$mcount);?></td>
		</tr>

	</table>


	<?php IF($RD == "2") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>&RD=3&SE=<?php ECHO $idx;?>';" class="btn btn-default">수정하기</button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	<?php } ?>
	<?php IF($RD == "3") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="check_w_form('regfm',event);return false;" class="btn btn-default"><?php ECHO $strBtnTxt;?></button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	<?php } ?>
	</form>

	</div>
	<!-- 코멘트 //-->

	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>