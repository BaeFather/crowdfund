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
	<script type="text/javascript" src="smshello.js"/></script>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm">
	<input type="hidden" name="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="SE" value="<?php ECHO $midx;?>" />
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
			<th class="tdtop">등록일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"reg_date","input02","","",$reg_date);?></td>
			<th class="tdtop">상태</th>
			<td><?php ECHO fn_general_select($recyn,$strSelectBox,fn_recyn_report(),"상태 ▼","recyn","class='input02'","");?></td>
		</tr>
		<tr>
			<th class="tdtop">성함</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"cname","input02","","",$cname);?></td>
			<th class="tdtop">연락처</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"cphone","input02","","",$cphone);?></td>
		</tr>
		<tr>
			<th>인증번호</th>
			<td colspan="3" class="tdtop">
				<?php ECHO INPUT_FORM($strInputText1,"passwd","input04","","required itemname='인증번호'",$passwd);?>
			</td>
		</tr>
		<tr>
	</table>


	<?php IF($RD == "2") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>&RD=3&idx=<?php ECHO $idx;?>';" class="btn btn-default">수정하기</button>
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