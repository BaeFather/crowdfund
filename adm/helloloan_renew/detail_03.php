	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off" encType="multipart/form-data">
	<input type="hidden" name="kind" id="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="section" id="section" value="3" />
	<input type="hidden" name="SE" id="SE" value="<?php ECHO $idx;?>" />
	<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
	<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
	<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
	<input type="hidden" name="Sdate" value="<?php ECHO $Sdate;?>" />
	<input type="hidden" name="Edate" value="<?php ECHO $Edate;?>" />
	<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
	<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
		</colgroup>
		<tr>
			<th colspan="7">
				대출자 정보
			</th>
		</tr>
		<tr>
			<th class="tdtop">진행사항</th>
			<td class="tdL"><?php ECHO fn_general_select($recyn,$strSelectBox2,fn_hellloan_search_kind_renew(),"상태 ▼","recyn","class='select03' id='recyn'","");?></td>
			<th>자서일정</th>
			<td class="tdL" colspan="3"><?php ECHO INPUT_FORM($strInputText2,"auth_date","input02","","",$auth_date);?> 일</td>
		</tr>
		<tr>
			<th>대출자명</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"lenmember","input02","","",$lenmember);?></td>
			<th>연락처</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"lenphone","input02","","",$lenphone);?></td>
			<th>특이사항</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"lenother","input02","","",$lenother);?></td>
		</tr>
		<tr>
			<th>대출자 주민번호</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"loan_jumin","input02","","",$loan_jumin);?></td>
			<th>실거주지</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"loan_addr","input02","","",$loan_addr);?></td>
		</tr>
		<tr>
			<th>담보제공자명</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"promember","input02","","",$promember);?></td>
			<th>연락처</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"prophone","input02","","",$prophone);?></td>
			<th>특이사항</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"proother","input02","","",$proother);?></td>
		</tr>
		<tr>
			<th>자금목적</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"purpose","input02","","",$purpose);?></td>
			<th>SMS 발송 여부</th>
			<td colspan="3"><?php ECHO fn_general_select($smsyn,$strRadioText,fn_smsyn(),"상태 ▼","smsyn","class='radioarea' id='recyn'","");?></td>
		</tr>
	</table>

	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
			<col width="16.6%">
		</colgroup>
		<tr>
			<th colspan="7">
				아파트 정보
			</th>
		</tr>
		<tr>
			<th>면적</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"aptarea","input02","","",$aptareaArr[1]);?></td>
			<th>준공일</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"aptcrdate","input02","","",$aptcrdate);?></td>
			<th>세대수</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"atptot","input02","","",f_number($atptot));?></td>
		</tr>
		<tr>
			<th>설정내용</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"other","input02","","",$other);?></td>
			<th>물건순위</th>
			<td><?php ECHO fn_general_txt($loankind,fn_loankind());?></td>
			<th>시세기준</th>
			<td>KB</td>
		</tr>
		<tr>
			<th>LTV</th>
			<td colspan="5" class="tdC"><?php ECHO $okltv;?> %</td>
		</tr>
		<tr>
			<th>담보여유금액</th>
			<td colspan="5" class="tdC"><?php ECHO f_number($strLastLtv);?> 원</td>
		</tr>
		<tr>
			<th>입지조건</th>
			<td colspan="5">
				<?php ECHO INPUT_FORM($strInputTextarea,"conditions","text01","","",$conditions);?>
			</td>
		</tr>
	</table>

	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="16.6%">
			<col width="83.4%">
		</colgroup>
		<tr>
			<th colspan="2">
				첨부서류
			</th>
		</tr>
		<tr>
			<th>내용</th>
			<td>
			<?php
			FOR($i=0;$i<10;$i++)
			{
				INPUT_FORM("sfileForm","s_file".$i,"",$gstrFileBoardUrl,"",$sfileArr[$i]);
			}
			?>
			</td>
		</tr>
	</table>


	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="check_w_file_form('regfm',event);return false;" class="btn btn-default"><?php ECHO $strBtnTxt;?></button>
		&nbsp;&nbsp;
		<?php IF($member["mb_no"]=="2" || $member["mb_no"]=="5") { ?>
		<button type="button" id="list_button" onClick="check_del_form('dregfm',event);" class="btn btn-default">삭제하기</button>
		&nbsp;&nbsp;
		<?php } ?>
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	</form>

	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>

	<form name="dregfm" id="dregfm">
		<input type="hidden" name="kind" value="del" />
		<input type="hidden" name="SE" value="<?php ECHO $idx;?>" />
		<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
		<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
		<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
		<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
		<input type="hidden" name="Sdate" value="<?php ECHO $Sdate;?>" />
		<input type="hidden" name="Ldate" value="<?php ECHO $Ldate;?>" />
		<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
		<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	</form>


	<script type="text/javascript" src="helloloan.js?ver=<?php ECHO RAND(1000000,9999999);?>"/></script>

	<script>
		var aptcode = "<?php ECHO $aptnameArr[0];?>";
		var areacode = "<?php ECHO $aptareaArr[0];?>";
	</script>