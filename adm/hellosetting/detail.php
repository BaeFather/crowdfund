<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
.selectarea {width:180px;padding:5px 0;}
label {float:left;display:block;padding:5px 5px;}
.fred {color:#ff0000;}
.fl {float:left;}
.cb {clear:both;}
.ul_guide {width:100%;list-style:none;}
.ul_guide > .li1 {width:20%;float:left;}
.input1 {width:100px;text-align:right;padding:0 7px;}
.input2 {width:100px;text-align:center;padding:0 7px;}
.bbtn {width:50px;text-align:center;color:#FFF;font-weight:bold;background-color:#0000FF;border-color:#0000FF}

.stable {width:100%;border:0px;border-collapse:collapse;}
.stable tr td.td01 {width:90%; border:0px;}
.stable tr td.td02 {width:10%;vertical-align:top;border:0px;}
#ltvarea {clear:both;width:100%;}
.w100 {width:100%;cursor:pointer;}
</style>

	<script type="text/javascript" src="hellosetting.js?ver=<?php ECHO RAND(100000000,999999999);?>" /></script>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off">
	<input type="hidden" name="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="SE" value="<?php ECHO $SE;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
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
			<th scope="col">제목</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"title","","","required itemname='업체명' style='width:80%'",$title[0]);?></td>
		</tr>
		<tr>
			<th scope="col">조견업체</th>
			<td colspan="3"><?php ECHO fn_general_select($hmseq[0],"",$ClassHelloSetting->fn_hloan_member(),":조견업체:","hmseq","class='form-control input-sm' style='width:150px'","");?></td>
		</tr>
		<tr>
			<th scope="col">취급지역</th>
			<td colspan="3">
				<div class="fl">
					<?php ECHO fn_general_select($addr_si[0],"",$ClassHelloSetting->fn_addr_si(),":취급지역:","addr_si","class='form-control input-sm' style='width:150px'","");?>
				</div>
				<div class="fl">
					<?php ECHO fn_general_select($addr_yn[0],"radio",$ClassHelloSetting->fn_addr_yn(),":구분:","addr_yn","class='radioarea' OnClick=\"check_addr_yn(this.value,'');\"","");?>
				</div>
				<div class="cb"></div>
				<div id="addr_gu_area">
				</div>
			</td>
		</tr>
		<tr>
			<th scope="col">LTV 및 금리</th>
			<td colspan="3">

				<table class="stable">
				<tr>
					<td class="td01">
					<?php
						FOR($i=0;$i<COUNT($ltvs);$i++)
						{
					?>
						<div style='width:100%;padding:7px 0;'>
						<input type="hidden" name="SE2" value="<?php ECHO $hcssseq;?>" />
						LTV <?php ECHO INPUT_FORM($strInputText1,"ltvs[]","input1","","",$ltvs[$i]);?>% 이상
						<?php ECHO INPUT_FORM($strInputText1,"ltvl[]","input1","","",$ltvl[$i]);?>% 이하
						&nbsp;&nbsp;
						선순위 금리 <?php ECHO INPUT_FORM($strInputText1,"ms[]","input1","","",$ms[$i]);?>%
						후순위 금리 <?php ECHO INPUT_FORM($strInputText1,"ml[]","input1","","",$ml[$i]);?>%
						</div>
					<?php
						}
					?>
						<div id="ltvarea"></div>
					</td>
					<td class="td02">
						<input type="button" value="+" class="bbtn" OnClick="ltvplus();">
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th scope="col">적용일자</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"rec_date","input2 datepicker","","",$rec_date[0]);?></td>
		</tr>
		<tr>
			<th scope="col">적용</th>
			<td colspan="3"><?php ECHO fn_general_select($recyn[0],"radio",$ClassHelloSetting->fn_setting_recyn(),":적용:","recyn","class='radioarea'","");?></td>
		</tr>
		<tr>
			<th scope="col">기간</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"period","input2","","",$period[0]);?> 개월</td>
		</tr>
	</table>

	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="check_w_form('regfm',event);return false;" class="btn btn-default"><?php ECHO $strBtnTxt;?></button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>';" class="btn btn-default">목록보기</button>
	</div>
	</form>

	<script type="text/javascript">
	<!--
		var addr_yn = "<?php ECHO $addr_yn[0];?>";
		var addr_gu = "<?php ECHO $addr_gu[0];?>";
		var seq		= "<?php ECHO $SE;?>";
		itemcnt = 0;

		if(seq)
		{
			check_addr_yn(addr_yn,addr_gu);
		}
	//-->
	</script>
<?php
	FOR($i=0;$i<COUNT($strHistoryList);$i++)
	{
		ECHO "<div class='w100' OnClick=\"open_window_center('dpop.php?SE=".$strHistoryList[$i][0]."','dpop','800','600','no');\">".$strHistoryList[$i][2]." 수정 ".$strHistoryList[$i][1]."(".$ClassHelloSetting->fn_g5_member($strHistoryList[$i][1]).") - 로그보기</div>";
	}
?>
