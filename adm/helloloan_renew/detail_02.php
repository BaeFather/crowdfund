	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off">
	<input type="hidden" name="kind" id="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="section" id="section" value="2" />
	<input type="hidden" name="SE" id="SE" value="<?php ECHO $idx;?>" />
	<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
	<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
	<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
	<input type="hidden" name="Sdate" value="<?php ECHO $Sdate;?>" />
	<input type="hidden" name="Edate" value="<?php ECHO $Edate;?>" />
	<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
	<input type="hidden" name="page" value="<?php ECHO $page;?>" />

	<input type="hidden" name="si" id="si" value="<?php ECHO $si;?>" />
	<input type="hidden" name="gu" id="gu" value="<?php ECHO $gu;?>" />
	<input type="hidden" name="dong" id="dong" value="<?php ECHO $dg;?>" />
	<input type="hidden" name="floor" id="floor" value="<?php ECHO $floor;?>" />
	<input type="hidden" name="bcode" id="bcode" value="<?php ECHO $bcode;?>" />
	<input type="hidden" name="aptname" id="aptnametext" value="<?php ECHO $aptname;?>" />
	<input type="hidden" name="aptarea" id="aptareatext" value="<?php ECHO $aptarea;?>" />
	<input type="hidden" name="mm" id="mm" value="<?php ECHO $kbmoney/10000;?>" /><!-- kb평균 시세//-->
	<input type="hidden" name="Interest" id="Interest" value="<?php ECHO $Interest;?>" /><!-- 금리 //-->
	<input type="hidden" name="feesmoney" id="feesmoney" value="<?php ECHO $feesmoney;?>" /><!--플랫폼 수수료//-->
	<input type="hidden" name="ltv" id="ltv" value="<?php ECHO $ltv;?>" /><!-- ltv //-->
	<input type="hidden" name="arecyn" id="arecyn" value="<?php ECHO $arecyn;?>">

	<input type="hidden" name="aptcrdate" id="cr_date" value="<?php ECHO $aptcrdate;?>">
	<input type="hidden" name="atptot" id="tot_house" value="<?php ECHO $atptot;?>">
	<input type="hidden" name="hmseq2" id="tot_house" value="<?php ECHO $hmseq2;?>">
	<input type="hidden" name="apt_areatxt" id="tot_house" value="<?php ECHO $aptareaArr[1];?>">



	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="30%">
			<col width="30%">
			<col width="40%">
		</colgroup>
		<tr>
			<th>대출자명</th>
			<th>연락처</th>
			<th>특이사항</th>
		</tr>
		<tr>
			<td><?php ECHO INPUT_FORM($strInputText1,"lenmember","input02","","",$lenmember);?></td>
			<td><?php ECHO INPUT_FORM($strInputText1,"lenphone","input02","","",$lenphone);?></td>
			<td><?php ECHO INPUT_FORM($strInputText1,"lenother","input02","","",$lenother);?></td>
		</tr>
		<tr>
			<th>담보제공자명</th>
			<th>연락처</th>
			<th>특이사항</th>
		</tr>
		<tr>
			<td><?php ECHO INPUT_FORM($strInputText1,"promember","input02","","",$promember);?></td>
			<td><?php ECHO INPUT_FORM($strInputText1,"prophone","input02","","",$prophone);?></td>
			<td><?php ECHO INPUT_FORM($strInputText1,"proother","input02","","",$proother);?></td>
		</tr>
		<tr>
			<th>등록일</th>
			<td colspan="2"><?php ECHO $reg_date;?></td>
		</tr>
	</table>

	<table  class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="20%">
			<col width="20%">
			<col width="20%">
			<col width="20%">
			<col width="20%">
		</colgroup>
		<tr>
			<th colspan="5">금리계산기</th>
		</tr>
		<tr>
			<th>주소</th>
			<td colspan="4">
				<?php ECHO $si.$gu." ".$dongVal[1]." ".$aptnameArr[1]." ".$jibun." ".$dong."동 ".$floor."층 ".$ho."호 (".$aptareaArr[1]."㎡)"; ?>
			</td>
		</tr>
		<tr>
			<th>희망대출금액 (원)</th>
			<td colspan="4"><?php ECHO INPUT_FORM($strInputText1,"ddmoney","input09","","required itemname='희망대출금액' OnKeyUp=\"fn_number_coma('ddmoney',this.value, $(this).index());\"",f_number($ddmoney));?></td>
		</tr>
		<tr>
			<th>선순위 채권최고액 (원)</th>
			<td colspan="4">
			1. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input09","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[0]);?>
			2. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input09","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[1]);?>
			3. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input09","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[2]);?>
			4. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input09_","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[3]);?>
			5. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input09_","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[4]);?>
			6. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input09_","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[5]);?>


			</td>
		</tr>
		<tr>
			<th>물건순위</th>
			<td><?php ECHO fn_general_select($loankind,$strRadioText,fn_loankind(),"","loankind","class='radioarea' ","");?></td>
			<th>경매여부</th>
			<td colspan="2"><?php ECHO fn_general_select($auctionyn,$strRadioText,fn_auction(),"경매여부 ▼","auctionyn","class='radioarea'","");?></td>
		</tr>
		<tr>
			<th>플랫폼 수수료율 (%)</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"fees","input06","","required itemname='플랫폼 수수료율' placeholder='' ",$fees);?> %</td>
			<th>조견업체</th>
			<td><?php ECHO fn_general_select($hmseq2,"txt",fn_hmseq(),"::조견업체선택::","hmseq2","class='select01'","");?></td>
			<td><input type="button" name="loanbtn" value="대출승인금액 산출" class="btn_calc" OnClick="check_send_form_re('regfm');"></td>
		</tr>
		<tr>
			<th rowspan="3">계산 내용</th>
			<th>구분</th>
			<th>시세</th>
			<th>LTV</th>
			<th>금리</th>
		</tr>
		<tr>
			<th>KB(기준)</th>
			<td class="tC"><?php ECHO f_number($kbmoney);?></td>
			<td class="tC"><span id="ltv_area"><?php ECHO $ltv;?></span></td>
			<td class="tC"><span id="Interest_area"><?php ECHO $Interest;?></span></td>
		</tr>
		<tr>
			<th>플랫폼 수수료 (원)</th>
			<td colspan="3"><span id="feesmoney_area"><?php ECHO f_number($feesmoney);?></td>
		</tr>
	</table>

	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="20%">
			<col width="30%">
			<col width="20%">
			<col width="30%">
		</colgroup>
		<tr>
			<th>대출승인금액</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"okmoney","input02 tR","","readonly",f_number($ddmoney));?></td>
			<th>자서일정</th>
			<th><?php ECHO INPUT_FORM($strInputText1,"auth_date","input02 datepicker tC","","",$auth_date);?></th>
		</tr>
		<tr>
			<th>헬로펀딩 승인 심사</th>
			<td colspan="3">
				<?php ECHO fn_general_select($arecyn,$strRadioText,fn_hellloan_search_kind_2_renew(),"","arecyn","class='radioarea'","");?>
				<?php ECHO INPUT_FORM($strInputText1,"recyn_other2","input08","","",$recyn_other2);?>
			</td>
		</tr>
	</table>


	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="check_w_form('regfm',event);return false;" class="btn btn-default"><?php ECHO $strBtnTxt;?></button>
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
		<input type="hidden" name="Edate" value="<?php ECHO $Edate;?>" />
		<input type="hidden" name="SC" value="<?php ECHO $SC;?>" />
		<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
		<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	</form>


	<script type="text/javascript" src="helloloan.js?ver=<?php ECHO RAND(1000000,9999999);?>"/></script>

	<script>
		var aptcode = "<?php ECHO $aptnameArr[0];?>";
		var areacode = "<?php ECHO $aptareaArr[0];?>";

//		check_send_form('regfm');
	</script>