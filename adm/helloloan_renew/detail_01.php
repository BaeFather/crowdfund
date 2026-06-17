	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off">
	<input type="hidden" name="kind" id="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="section" id="section" value="1" />
	<input type="hidden" name="SE" id="SE" value="<?php ECHO $idx;?>" />
	<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
	<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
	<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
	<input type="hidden" name="Sdate" value="<?php ECHO $Sdate;?>" />
	<input type="hidden" name="Edate" value="<?php ECHO $Edate;?>" />
	<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
	<input type="hidden" name="page" value="<?php ECHO $page;?>" />

	<input type="hidden" name="addr_si" id="addr_si" value="<?php ECHO $laddrArr[0];?>" />
	<input type="hidden" name="bcode" id="bcode" value="<?php ECHO $bcode;?>" />
	<input type="hidden" name="mm" id="mm" value="<?php ECHO $kbmoney/10000;?>" /><!-- kb평균 시세//-->
	<input type="hidden" name="Interest" id="Interest" value="<?php ECHO $Interest;?>" /><!-- 금리 //-->
	<input type="hidden" name="feesmoney" id="feesmoney" value="<?php ECHO $feesmoney;?>" /><!--플랫폼 수수료//-->
	<input type="hidden" name="ltv" id="ltv" value="<?php ECHO $ltv;?>" /><!-- ltv //-->
	<input type="hidden" name="aptcrdate" id="aptcrdate" value="<?php ECHO $aptcrdate;?>" />
	<input type="hidden" name="atptot" id="atptot" value="<?php ECHO $atptot;?>" />

	<input type="hidden" name="arecyn" id="arecyn" value="<?php ECHO $arecyn;?>">
	<input type="hidden" name="seqor" id="seqor" value="<?php ECHO $seq;?>">


	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th class="tdtop">진행사항</th>
			<td class="tdL"><?php ECHO fn_general_select($recyn,$strSelectBox,fn_hellloan_search_kind_renew(),"상태 ▼","recyn","class='input02' id='recyn'","");?></td>
			<th class="tdtop">담당자명</th>
			<td><?php ECHO INPUT_FORM($strInputText2,"hname","input02","","",$cname);?>
				/ <?php ECHO INPUT_FORM($strInputText2,"hphone","input02","","",$hphone);?>
			</td>
		</tr>
		<tr>
			<th>담보물 주소</th>
			<td colspan="3" class="tdtop">
				<div class="pdb5">
				<?php ECHO fn_general_select("",$strSelectBox2,"","광역시/도 선택","si","class='select03' id='si' OnChange=\"check_form_send('gu',this.value)\" required itemname='광역시/도를 선택하여 주세요'","");?>

				<?php ECHO fn_general_select("",$strSelectBox2,"","시/구 선택","gu","class='select03' id='gu' OnChange=\"check_form_send('dong',this.value)\" ","");?>

				<?php ECHO fn_general_select("",$strSelectBox2,"","동 선택","dong","class='select03' id='dong' OnChange=\"check_form_send('apt_name',this.value)\" required itemname='동을 선택하여 주세요'","");?>

				<?php ECHO INPUT_FORM($strInputText1,"jibun","input06","","placeholder='지번' ",$jibun);?>

				</div>
				<div class="pdb5">
				<?php ECHO fn_general_select("",$strSelectBox2,"","::아파트선택::","apt_name","class='select03' id='apt_name' OnChange=\"check_form_send('apt_area',this.value)\"","");?>

				<?php ECHO fn_general_select("",$strSelectBox2,"","::평형선택::","apt_area","class='select03' id='apt_area' OnChange=\"fn_apt_mm()\"","");?>

				<?php ECHO INPUT_FORM($strInputText1,"dong2","input06","id='dong2'","placeholder='동' required itemname='동'",$dong);?>

				<?php ECHO INPUT_FORM($strInputText1,"floor","input06","id='floor'","placeholder='층' required itemname='층' OnChange=\"fn_apt_mm()\"",$floor);?>

				<?php ECHO INPUT_FORM($strInputText1,"ho","input06","id='ho'","placeholder='호' required itemname='호'",$ho);?>
				</div>
			</td>
		</tr>
		<tr>
			<th>희망대출금액 (원)</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"ddmoney","input02","","required itemname='희망대출금액' OnKeyUp=\"fn_number_coma('ddmoney',this.value, $(this).index());check_form_check();\"",f_number($ddmoney));?></td>

			<th>차주명</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"lenmember","input02","","required itemname='차주명'",$lenmember);?></td>
		</tr>
		<tr>
			<th>선순위 채권최고액 (원)</th>
			<td colspan="3">
			1. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input02_","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[0]);?>
			2. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input02_","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[1]);?>
			3. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input02_","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[2]);?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			4. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input02__","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[3]);?>
			5. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input02__","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[4]);?>
			6. <?php ECHO INPUT_FORM($strInputText1,"maxbond[]","input02__","","OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());check_form_check();\"",$maxbond[5]);?>


			</td>
		</tr>
		<tr>
			<th>물건순위</th>
			<td><?php ECHO fn_general_select($loankind,$strRadioText,fn_loankind(),"","loankind","class='radioarea'  OnClick=\"check_form_check();\"","");?></td>
			<th>경매여부</th>
			<td><?php ECHO fn_general_select($auctionyn,$strRadioText,fn_auction(),"","auctionyn","class='radioarea'  OnClick=\"check_form_check();\"","");?></td>
		</tr>
		<tr>
			<th>플랫폼 수수료율(%)</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"fees","input06","","required itemname='플랫폼 수수료율' placeholder='' OnKeyUp=\"check_form_check();\"",$fees);?> %</td>
			<th>등기부등본첨부</th>
			<td><?php IF($ifile) { ?><a href="http://admin.hellofunding.kr/inc/download.php?F=/data/board&val=<?php ECHO $ifile;?>" target="_blank"><?php ECHO $ifile;?></a><?php } ?>
			</td>
		</tr>
		<tr>
			<th>비고</th>
			<td colspan="3">
				<?php ECHO INPUT_FORM($strInputText1,"content","input02","","",$content);?>
			</td>
		</tr>
		<tr>
			<th>조견업체 선택</th>
			<td colspan="3">
				<?php ECHO fn_general_select($hmseq2,$strRadioText,fn_hmseq(),"::조견업체선택::","hmseq2","class='radioarea' OnClick=\"check_form_check()\"","");?>
			</td>
		</tr>
	</table>

	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="75%">
		</colgroup>
		<tr>
			<th>중개법인 1차 심사</th>
			<td>
				<?php ECHO fn_general_txt($arecyn, fn_loan_arecyn());?>
			</td>

		</tr>

		<tr>
			<th>헬로펀딩 1차 심사</th>
			<td>
				<?php ECHO fn_general_select($mb_no,$strSelectBox2,hloan_admin_member($connect_for),"::담당자 선택::","mb_no","class='select02'","");?>

				<?php ECHO fn_general_select($arecyn,$strSelectBox2,hloan_voteyn_renew(),"::심사현황::","arecyn","class='select02'","");?>

				<?php ECHO INPUT_FORM($strInputText1,"recyn_other","input08 tL","","",$recyn_other);?>
			</td>

		</tr>
	</table>

	<div class="write_detail_area" id="write_detail_area"></div>


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
		<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
		<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	</form>


	<script type="text/javascript" src="helloloan.js?ver=<?php ECHO RAND(1000000,9999999);?>"/></script>

	<script>
		var aptcode = "<?php ECHO $aptnameArr[0];?>";
		var areacode = "<?php ECHO $aptareaArr[0];?>";

		var arecyn = "<?php ECHO $arecyn;?>";

		var si =	"<?php ECHO $si;?>";
		var gu =	"<?php ECHO $gu;?>";
		var dong =	"<?php ECHO $dg;?>";
		var apt_name = "<?php ECHO $aptname;?>";
		var apt_area =	"<?php ECHO $aptarea;?>";
		var strlink = "";

		check_form_proc("kind=si", event);

		//var aptcode = "<?php ECHO $aptnameArr[0];?>";
		//var areacode = "<?php ECHO $aptareaArr[0];?>";

		function check_send()
		{
			check_send_form('regfm');
		}
		function check_addr_send()
		{
			check_form_send("gu", si);
			check_form_send("dong", gu);

			setTimeout(check_other_send,1000);
		}
		function check_other_send()
		{
			check_form_send("apt_name", dong);
			check_form_send("apt_area", apt_name);

			setTimeout(check_send,2500);
		}

		setTimeout(check_addr_send,1000);
	</script>