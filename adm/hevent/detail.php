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
.input05 {width:60px;line-height:24px;font-size:15px;text-align:center;border:1px solid #CCC;}
.input06 {width:100px;line-height:24px;font-size:15px;text-align:center;border:1px solid #CCC;margin:0 auto;}
.select01 {width:95%;line-height:24px;font-size:15px;padding:3px 0;}
.tdC {text-align:center;}
.select02 {width:100px;line-height:24px;font-size:15px;padding:3px 0;margin-right:10px;}
.srepimg {max-width:200px;min-width:50%;}
</style>
	<script type="text/javascript" src="hevent.js?ver=<?php ECHO RAND(1000000,9999999);?>"/></script>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off"/>
	<input type="hidden" name="kind" id="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="SE" value="<?php ECHO $SE;?>" />
	<input type="hidden" name="SC" value="<?php ECHO $SC;?>" />
	<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
	<input type="hidden" name="section" value="1" />
	<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	<?php IF($RD == "2") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>&RD=3&SE=<?php ECHO $SE;?>';" class="btn btn-default">수정하기</button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="check_del_form('dregfm',event);" class="btn btn-default">삭제하기</button>
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

	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th class="tdtop">노출상태</th>
			<td class="tdL" colspan="3"><?php ECHO fn_general_select($row["recyn"],$strSelectBox3,$strEventClass->StrKind(),"노출상태 ▼","recyn","class='input02'","");?></td>
		</tr>
		<tr>
			<th class="tdtop">PC 메인노출</th>
			<td class="tdL"><?php ECHO fn_general_select($row["mainyn"],$strSelectBox3,$strEventClass->StrKind(),"PC 메인노출 ▼","mainyn","class='input02'","");?></td>
			<th class="tdtop">모바일 메인노출</th>
			<td class="tdL"><?php ECHO fn_general_select($row["mainmyn"],$strSelectBox3,$strEventClass->StrKind(),"모바일 메인노출 ▼","mainmyn","class='input02'","");?></td>
		</tr>
		<tr>
			<th class="tdtop">시작일</th>
			<td class="tdL"><?php ECHO INPUT_FORM($strInputText1,"sdate","input02 datepicker","","autocomplete='off'",$row["sdate"]);?></td>
			<th class="tdtop">종료일</th>
			<td class="tdL">
				<?
				ECHO INPUT_FORM($strInputText1,"edate","input02 datepicker","","autocomplete='off' style='width:66%;float:left;margin-right:5px'",$row["edate"]);
				if($RD == "3") {
				?>
				<label for="endDate"><input type="checkbox" value="9999-12-31" name="edate" id="endDate" <? if($row["edate"]=='9999-12-31') {echo 'checked';} ?>/> 종료시까지</label>
				<? } ?>
			</td>
		</tr>
		<tr>
			<th class="tdtop">대표이미지<br />(350*160)</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strFileText,"i_file[]","input02","/data/fevent","",$strIFile[0]);?></td>
		</tr>
		<tr>
			<th class="tdtop">PC 메인이미지<br />(350*300)</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strFileText,"i_file[]","input02","/data/fevent","",$strIFile[1]);?></td>
		</tr>
		<tr>
			<th class="tdtop">모바일 메인이미지<br />(760*347)</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strFileText,"i_file[]","input02","/data/fevent","",$strIFile[2]);?></td>
		</tr>
		<tr>
			<th>제목</th>
			<td class="tdtop" colspan="3">
				<?php ECHO INPUT_FORM($strInputText1,"title","input04","","required itemname='제목'",$row["title"]);?>
			</td>
		</tr>
		<tr>
			<th>PC 내용</th>
			<td colspan="3" class="tdL">
			<?php IF($RD == "3") { ?>
				<?php ECHO editor_html('wr_content', $row["content"], true);?>
			<?php } ELSE { ?>
				<?php ECHO $row["content"];?>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<th>모바일 내용</th>
			<td colspan="3" class="tdL">
			<?php IF($RD == "3") { ?>
				<?php ECHO editor_html('wr_content2', $row["contentm"], true);?>
			<?php } ELSE { ?>
				<?php ECHO $row["contentm"];?>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<th>링크URL</th>
			<td class="tdtop" colspan="3">
				<?php ECHO INPUT_FORM($strInputText1,"linkurl","input04","","",$row["linkurl"]);?>
			</td>
		</tr>
		<tr>
			<th class="tdtop">타겟</th>
			<td class="tdL">
				<?php ECHO fn_general_select($row["target"],$strSelectBox3,$strEventClass->FnTarget(),"타겟 ▼","target","class='input02'","");?>
			</td>
			<th class="tdtop">정렬순서</th>
			<td class="tdL">
				<?php ECHO INPUT_FORM($strInputText1,"sort_id","input04","","",$row["sort_id"]);?>
			</td>
		</tr>
		<tr>
			<th>등록일</th>
			<td class="tdtop" colspan="3">
				<?php ECHO INPUT_FORM($strInputText1,"reg_date","input04 datepicker","","required itemname='등록일'",$row["reg_date"]);?>
			</td>
		</tr>
	</table>


	<?php IF($RD == "2") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>&RD=3&SE=<?php ECHO $SE;?>';" class="btn btn-default">수정하기</button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="check_del_form('dregfm',event);" class="btn btn-default">삭제하기</button>
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
	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>

	<form name="dregfm" id="dregfm">
		<input type="hidden" name="kind" value="del" />
		<input type="hidden" name="SE" value="<?php ECHO $SE;?>" />
		<input type="hidden" name="SC" value="<?php ECHO $SC;?>" />
		<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
		<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	</form>

	<script>
		function check_w_form(fmname, event)
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

				 var checkform = check_form(fmname);

				if(checkform == false)
				{
					  return false;
				}

				<? echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

				<? echo $editor_js2; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

				var frm = $('#'+fmname);
				var str = frm.serialize();

				var formData = new FormData();

				for(var i=0;i<$("input[name='i_file[]").length;i++)
				{
					formData.append('i_file[]', $("input[name='i_file[]']")[i].files[0]);
				}

				var strarr = str.split("&");
				var strdarr = new Array();
				for(var i=0;i<strarr.length;i++)
				{
					  strdarr = strarr[i].split("=");
						if(parseInt(strdarr[0].indexOf("%5B%5D")) > 1)
						{
							strdarr[0] = strdarr[0].replace("%5B%5D","")+"[]";
						}
						formData.append(strdarr[0],strdarr[1]);
			  }

				$.ajax({
					type : 'POST',
					url : heventProcessUrl,
					data : formData,
					dataType: 'json',
					contentType: false,
					processData: false,
					success : function(data){

						if(data.retcode == "OK"){
							var stralert = decodeURIComponent(data.retalert);
								alert(stralert.replace("+"," "));
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
	</script>