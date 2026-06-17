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
</style>
	<script type="text/javascript" src="helloloan.js?ver=<?php ECHO RAND(1000000,9999999);?>"/></script>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<form name="regfm" id="regfm" autocomplete="off" />
	<input type="hidden" name="kind" id="kind" value="<?php ECHO $strKind;?>" />
	<input type="hidden" name="SE" value="<?php ECHO $idx;?>" />
	<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
	<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
	<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
	<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
	<input type="hidden" name="SC" value="<?php ECHO $SC;?>" />
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
			<td class="tdL"><?php ECHO fn_general_select($recyn,$strSelectBox3,fn_hellloan_search_kind(),"상태 ▼","recyn","class='form-control input-sm' id='recyn'","");?></td>
			<th class="tdtop">협력사명</th>
			<td><?php ECHO INPUT_FORM("txt1","cname","input02","","",$cname);?></td>
		</tr>

		<tr>
			<th>담보물 주소</th>
			<td colspan="3" class="tdtop">
				<?//php ECHO INPUT_FORM($strInputText1,"laddr","input04","","required itemname='담보물 주소'",$laddr);?>
				<? if ($RD=="3") { ?>
					<input type="TEXT" id="laddr" name="laddr" value="<?=$laddr?>" required="" class="form-control input-sm" style="display:inline-block; width:600px;">
					<button type="button" onclick="go_hyphen_addr_srch('regfm');" style="margin-right:15px;" class="btn btn-sm btn-default">검색</button>
				<? } else { ?>
					<?=$laddr?>
				<? } ?>
				<br/>
				<input type="hidden" id="d_code"   name="d_code"   value="<?=$d_code?>" />
				<input type="hidden" id="kb_mg_id"   name="kb_mg_id"   value="<?=$kb_mg_id?>" />
				<input type="hidden" id="kb_ju_seri" name="kb_ju_seri" value="<?=$kb_ju_seri?>" />
				<input type="hidden" id="kb_mg_id2"   name="kb_mg_id2"   value="<?=$kb_mg_id2?>" />
				<input type="hidden" id="kb_ju_seri2" name="kb_ju_seri2" value="<?=$kb_ju_seri2?>" />

				<input type="hidden" name="cert_num" id="cert_num" value="<?=$laddr_num?>"/>
			</td>
		</tr>

		<tr>
			<th>KB시세 URL</th>
			<td colspan="3" class="tdtop">
			<?php IF($RD == "3") { ?>
				<?php ECHO INPUT_FORM($strInputText1,"kbquote","form-control input-sm","","",$kbquote);?>
			<?php } ELSEIF($RD == "2") { ?>
				<a href="<?php ECHO $kbquote;?>" target="_blank"><?php ECHO $kbquote;?></a>
			<?php } ?>
			</td>
		</tr>

		<tr>
			<th>원차주명(한글)</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"pname","input02","","required itemname='원차주명'",$pname);?></td>
			<th>원차주명(영문)</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="pname_E_first" name="pname_E_first" value="<?=$pname_E_first?>" required="" class="input02">
				<input type="TEXT" id="pname_E_last" name="pname_E_last" value="<?=$pname_E_last?>" required="" class="input02">
			<? } else { ?>
				<?=$pname_E_first?> <?=$pname_E_last?>
			<? } ?>
			</td>
		</tr>

		<?
		$jumin    = masterDecrypt($regist_number , false);		// AES256 복호화
		$JM       = getBirthGender($jumin);
		$genderNo = $JM[2];
		if ($genderNo=="1") {
			$display_sex = "남자";
		} else if ($genderNo=="2") {
			$display_sex = "여자";
		}

		if ($member["mb_id"]=="admin_sundol4" or $member["mb_id"]=="admin_foolish34" or $member["mb_id"]=="admin_hokudo" or $member["mb_id"]=="admin_eksql71" or $member["mb_id"]=="admin_romrom") {
			$vw_ju = "yes";
			$ju = substr($jumin,0,6)."-".substr($jumin,-7);
		} else {
			$vw_ju = "no";
			$ju = substr($jumin,0,6)."-".substr($jumin,6,1)."******";
		}
		?>
		<tr>
			<th>차주 주민번호</th>
			<td>
			<?php IF($RD == "3") { ?>
				<?
				if ($vw_ju=="yes") { ?>
					<input type="TEXT" id="jumin" name="jumin" value="<?=$jumin?>" required="" class="input02">
				<? } else {
					//ECHO $ju;
					?>
					<input type="hidden" id="jumin" name="jumin" value="<?=$jumin?>" required="" class="input02">
					<?=substr($ju,0,-6)?>******
					<?
				}
				?>
			<?php } ELSEIF($RD == "2") { ?>
				<?//php ECHO substr($ju,0,-6)."******";?>
				<?
				$bld_jumin = substr($ju,0,-6);
				?>
				<? if ($vw_ju=="yes") { ?>
				<span id="p_jumin" onMouseOver="swapText('p_jumin','<?=$ju?>');" onMouseOut="swapText('p_jumin','<?=$bld_jumin?>******');" style="cursor:pointer" ><?=$bld_jumin."****"?></span>
				<? } else { ?>
				<span id="p_jumin" style="cursor:pointer" ><?=$bld_jumin."****"?></span>
				<? } ?>
			<?php } ?>
			</td>
			<th>차주 연락처</th>
			<td>
			<?
			$bld_pphone1 =  substr($pphone1,0,-4);

			if ($RD=="3") {
				if ($vw_ju=="yes") { ?>
				<input type="TEXT" id="pphone1" name="pphone1" value="<?=$pphone1?>" required="" class="input02">
				<? } else { ?>
				<input type="hidden" id="pphone1" name="pphone1" value="<?=$pphone1?>" required="" class="input02">
				<?=$bld_pphone1."****"?>
				<? }
			} else { ?>
				<?//=$pphone1?>
				<?
				if ($vw_ju=="yes") { ?>
				<span id="p_phone1" onMouseOver="swapText('p_phone1','<?=$pphone1?>');" onMouseOut="swapText('p_phone1','<?=$bld_pphone1?>****');" style="cursor:pointer" ><?=$bld_pphone1."****"?></span>
				<? } else { ?>
				<span id="p_phone1" style="cursor:pointer" ><?=$bld_pphone1."****"?></span>
				<? }
			} ?>
			</td>
		</tr>

		<tr>
			<th>담보 제공자</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="dambo_pname" name="dambo_pname" value="<?=$dambo_pname?>" required="" class="form-control input-sm" style="width:150px;">
			<? } else { ?>
				<?=$dambo_pname?>
			<? } ?>
			</td>
			<th>담보제공자 연락처</th>
			<td>
			<?
			$bld_pphone2 =  substr($dambo_pphone,0,-4);
			if ($dambo_pphone) $rep_char = "****";
			else $rep_char = "";

			if ($RD=="3") {
				if ($vw_ju=="yes") { ?>
				<input type="TEXT" id="dambo_pphone" name="dambo_pphone" value="<?=$dambo_pphone?>" required="" class="input02">
				<? } else { ?>
				<input type="hidden" id="dambo_pphone" name="dambo_pphone" value="<?=$dambo_pphone?>" required="" class="input02">
				<?=$bld_pphone2."****"?>
				<? }
			} else {
				if ($vw_ju=="yes") { ?>
				<span id="p_phone2" onMouseOver="swapText('p_phone2','<?=$dambo_pphone?>');" onMouseOut="swapText('p_phone2','<?=$bld_pphone2?><?=$rep_char?>');" style="cursor:pointer" ><?=$bld_pphone2.$rep_char?></span>
				<? } else { ?>
				<span id="p_phone2" style="cursor:pointer" ><?=$bld_pphone2.$rep_char?></span>
				<? }
			} ?>
			</td>
		</tr>

		<tr>
			<th>소득(원)</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="pcp_income" name="pcp_income" value="<?=$pcp_income?>" required="" class="input02">
			<? } else { ?>
				<?=$pcp_income?>
			<? } ?>
			</td>
			<th>신용점수</th>
			<td>
				<?php //ECHO fn_general_select($crating,$strSelectBox,fn_cratring(),"등급선택 ▼","crating","class='select01' required itemname='등급선택'","");?>
				<?php ECHO INPUT_FORM($strInputText1,"crating","input02","","required itemname='신용등급'",$crating);?>
			</td>
		</tr>

		<tr>
			<th>대출기간</th>
			<td class="tdL">
			<?php IF($RD == "3") { ?>
			<?php ECHO fn_general_select($mkind,$strSelectBox,fn_mkind(),"","mkind","class='select02 sf'","OnChange=\"check_fn_mkind(this.value);\"");?><?php ECHO INPUT_FORM($strInputText1,"mdate","input05 disnone sf",""," itematt='int^1' placeholder='일'",$mdate);?>
			<?php } ELSEIF($RD == "2") { ?>
			<?php ECHO fn_mdate_pro($mkind,$mdate); ?>
			<?php } ?>
			</td>
			<!--
			<th>대출구분</th>
			<td class="tdL"><?php ECHO fn_general_select($loankind,$strRadioText,fn_loankind(),"","loankind","class='radio03'","");?>
			<?php IF(($RD == "2" && $loankind == "2") || $RD == "3") { ?>
			[<?php ECHO INPUT_FORM($strInputText1,"loanother","input05","","",$loanother);?>] 질권대출
			<?php } ?></td>
			-->
			<th>거래목적</th>
			<td class="tdL">
			<? if ($RD=="3") { ?>
					<input type="radio" name="loan_for" id="rr1" style="margin:0;" value="가계" <?=$loan_for=="가계"?"checked":""?> > <label for="rr1" style="margin-right:15px;">가계</label>
					<input type="radio" name="loan_for" id="rr2" style="margin:0;" value="대환" <?=$loan_for=="대환"?"checked":""?> > <label for="rr2" style="margin-right:15px;">대환</label>
					<input type="radio" name="loan_for" id="rr3" style="margin:0;" value="매매" <?=$loan_for=="매매"?"checked":""?> > <label for="rr3">매매</label>
			<? } else { ?>
				<?=$loan_for?>
			<? } ?>
			</td>
		</tr>

		<tr>
			<th>직업</th>
			<td>
			<? if ($RD=="3") { ?>
				<select name="pcp_job_group" id="pcp_job_group" class="form-control input-sm" style="display:inline;width:150px;">
					<option>직업선택</option>
					<option value="회사원" <?=$pcp_job_group=="회사원"?"selected":""?> >회사원</option>
					<option value="전문직" <?=$pcp_job_group=="전문직"?"selected":""?> >전문직</option>
					<option value="공무원" <?=$pcp_job_group=="공무원"?"selected":""?> >공무원</option>
					<option value="농축산업종사자" <?=$pcp_job_group=="농축산업종사자"?"selected":""?> >농축산업종사자</option>
					<option value="자유직/프리랜서" <?=$pcp_job_group=="자유직/프리랜서"?"selected":""?> >자유직/프리랜서</option>
					<option value="전업주부" <?=$pcp_job_group=="전업주부"?"selected":""?> >전업주부</option>
					<option value="학생/군인" <?=$pcp_job_group=="학생/군인"?"selected":""?> >학생/군인</option>
					<option value="무직" <?=$pcp_job_group=="무직"?"selected":""?> >무직</option>
					<option value="개인사업자/자영업자" <?=$pcp_job_group=="개인사업자/자영업자"?"selected":""?> >개인사업자/자영업자</option>
					<option value="카지노사업" <?=$pcp_job_group=="카지노사업"?"selected":""?> >카지노사업</option>
					<option value="대부업" <?=$pcp_job_group=="대부업"?"selected":""?> >대부업</option>
					<option value="환전업" <?=$pcp_job_group=="환전업"?"selected":""?> >환전업</option>
					<option value="고가귀금속판매업" <?=$pcp_job_group=="고가귀금속판매업"?"selected":""?> >고가귀금속판매업</option>
					<option value="가상통화산업 관련 종사자" <?=$pcp_job_group=="가상통화산업 관련 종사자"?"selected":""?> >가상통화산업 관련 종사자</option>
				</select>
			<? } else { ?>
				<?=$pcp_job_group?>
			<? } ?>
			</td>
			<th>직장명</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="pcp_company" name="pcp_company" value="<?=$pcp_company?>" required="" class="input02">
			<? } else { ?>
				<?=$pcp_company?>
			<? } ?>
			</td>
		</tr>

		<tr>
			<th>직장주소</th>
			<td colspan=3>
			<? if ($RD=="3") { ?>
				<input type="text" class="form-control input-sm" name="pcp_comp_addr_post" id="pcp_comp_addr_post" value="<?=$pcp_comp_addr_post?>" style="width:100px; margin-bottom:5px; display:inline; "/>
				<button type="button" id="save_button" class="btn btn-default" onclick="sample4_execDaumPostcode()" onclick11="go_addr_srch('c');" style="height:30px; margin-bottom:5px;">검색</button>
				<input type="text" name="pcp_comp_addr" id="pcp_comp_addr" class="form-control input-sm" value="<?=$pcp_comp_addr?>" style="width:500px; margin-bottom:5px;"/>
				<input type="text" name="pcp_comp_addr2" id="pcp_comp_addr2" class="form-control input-sm" value="<?=$pcp_comp_addr2?>" style="width:400px;"/>
			<? } else { ?>
				<?=$pcp_comp_addr_post?> <?=$pcp_comp_addr?> <?=$pcp_comp_addr2?>
			<? } ?>
			</td>
		</tr>

		<?
		if ($kb_mm_sil_date) $kb_mm_sil_date_prn = substr($kb_mm_sil_date,0,4).".".substr($kb_mm_sil_date,4,2).".".substr($kb_mm_sil_date,6,2);
		else $kb_mm_sil_date_prn="";
		?>
		<tr>
			<td colspan=4>
			<table>
				<tr>
					<th class="" colspan="7">KB 시세 <span id="kijun"><?=$kb_date?" (".$kb_date." 기준)":""?></span>
					<? if ($RD=="3") { ?>
					<!--button type="button" class="btn btn-default" onclick="srch_sise();"  style="margin-left:15px; height:25px; padding-top:2px;">가져오기</button-->
					<!--button type="button" class="btn btn-default" onclick="srch_sise2();" style="margin-left:15px; height:25px; padding-top:2px;">가져오기</button-->
					<input type="hidden" name="buildingCd" id="buildingCd" value="<?=$laddr_num?>" />
					<input type="hidden" name="kb_date" id="kb_date" value="<?=$kb_date?>"/>
					<select class="form-control input-sm" name="jmt" id="jmt" onchange="set_sise();" style="width:130px; display:inline; margin-left:15px; height:25px; ">
					</select>
					<button type="button" class="btn btn-default" onclick="hyphen_sise_code();" style="margin-left:15px; height:25px; padding-top:2px;">평형 조회</button>
					<? } ?>
					</th>
				</tr>
				<tr>
					<th>전용면적</th>
					<th>일반가</th>
					<th>하한가</th>
					<th style="text-align:center;">최근매매가 <span id="kb_sil_date_disp"><?=$kb_mm_sil_date?" ".$kb_mm_sil_date_prn."":""?></span><input type="hidden" name="kb_mm_sil_date" id="kb_mm_sil_date" value="<?=$kb_mm_sil_date?>"/></td>
					<th>세대수</th>
					<th>대지권등기</th>
					<!--th>전세가 (원)</th-->
					<th>소액임차보증금</th>
				</tr>
				<tr>
					<td class="tdC">
						<?php ECHO INPUT_FORM($strInputText1,"kbarea","form-control input-sm","","required itemname='전용면적' placeholder='㎡' style='display:inline;width:70px;text-align:right;'",$kbarea);?>
						㎡
					</td>
					<td class="tdC"><?php ECHO INPUT_FORM($strInputText1,"kbprice","form-control input-sm","","required itemname='일반가' style='display:inline; width:120px;text-align:center;' OnKeyUp=\"fn_number_coma('kbprice',this.value, $(this).index());\"",f_number($kbprice));?></td>
					<td class="tdC"><?php ECHO INPUT_FORM($strInputText1,"kbllimit","form-control input-sm","","required itemname='하한가' style='display:inline; width:120px;text-align:center;' OnKeyUp=\"fn_number_coma('kbllimit',this.value, $(this).index());\"",f_number($kbllimit));?></td>
					<td style="text-align:center;">
						<? if ($RD=="3") { ?>
						<input type="text" class="form-control input-sm" name="kb_mm_sil" id="kb_mm_sil" style="width:120px; display:inline; text-align:center; " onkeyup="fn_number_comaX('kb_low',this.value, $(this).index());" value="<?=number_format($kb_mm_sil)?>"/>
						<? } else { ?>
						<?=number_format($kb_mm_sil)?>
						<? } ?>
					</td>
					<td class="tdC"><?php ECHO INPUT_FORM($strInputText1,"hholds","form-control input-sm","","required itemname='세대수' style='width:50px;text-align:center;'",$hholds);?></td>
					<td class="tdC">
					<? if ($RD=="3") { ?>
						<select class="form-control input-sm" name="land_yn" id="land_yn" style="width:70px; display:inline; ">
							<option value="">선택</option>
							<option value="Y" <?=$land_yn=="Y"?"selected":""?>>Y</option>
							<option value="N" <?=$land_yn=="N"?"selected":""?>>N</option>
						</select>
					<? } else { ?>
						<?=$land_yn?>
					<? } ?>
					</td>
					<!--td class="tdC"><?php ECHO INPUT_FORM($strInputText1,"kbcharter","input02","","required itemname='전세가' OnKeyUp=\"fn_number_coma('kbcharter',this.value, $(this).index());\"",f_number($kbcharter));?></td-->
					<td class="tdC">
						<? if ($RD=="3") { ?>
							<input type="TEXT" id="house_deposit" name="house_deposit" value="<?=number_format($house_deposit)?>" required="" class="form-control input-sm" style='width:120px;text-align:center;' OnKeyUp="fn_number_coma('house_deposit',this.value, $(this).index());">
						<? } else { ?>
							<?=number_format($house_deposit)?>
						<? } ?>
					</td>
				</tr>
			</table>
			</td>
		</tr>

		<tr>
			<th>대출신청금액</th>
			<td>
				<?php //ECHO INPUT_FORM($strInputText1,"ddmoney","input02","","required itemname='희망대출금액' OnKeyUp=\"fn_number_coma('ddmoney',this.value, $(this).index());\"",f_number($ddmoney));?>
			<? if ($RD=="3") { ?>
				<input type="text" name="ddmoney" id="ddmoney" class="form-control input-sm" value="<?=number_format($ddmoney)?>" style="width:200px; text-align:right;" onkeyup="fn_number_comaX('ddmoney',this.value, $(this).index());">
			<? } else { ?>
				<?=number_format($ddmoney)?>
			<? } ?>
			</td>
			<th>채권최고액</th>
			<td>
				<?php //ECHO INPUT_FORM($strInputText1,"bsmoney","input02","","required itemname='채권설정금액' OnKeyUp=\"fn_number_coma('bsmoney',this.value, $(this).index());\"",f_number($bsmoney));?>
			<? if ($RD=="3") { ?>
				<input type="text" name="bsmoney" id="bsmoney" class="form-control input-sm" value="<?=number_format($bsmoney)?>" style="width:200px; text-align:right;" onkeyup="fn_number_comaX('bsmoney',this.value, $(this).index());">
			<? } else { ?>
				<?=number_format($bsmoney)?>
			<? } ?>
			</td>
		</tr>

		<tr>
			<td colspan=4>
				<table>
					<tr>
						<th colspan=9 style="background-color:#80CCE1;">
							선순위
							<? if ($RD=="3") { ?><button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="get_issue_mod();">조회</button>
							<? } ?>
							<button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="down_pdf();">다운로드</button>
						</th>
					</tr>
					<tr>
						<th><? if ($RD=="3") { ?><a onclick="add_row('P');" style="cursor:pointer;">+</a><? } ?></th>
						<th>구분</th>
						<th>금융업체</th>
						<th>채권최고액</th>
						<th>기대출금액</th>
						<th>설정율</th>
						<th>채무자</th>
						<th>등기목적</th>
						<th>-</th>
					</tr>
					<tbody id="pre_loan">
				<?
				$sql = "SELECT * FROM hloan_content_loan WHERE hcseq = '$idx' AND loan_gubun='PRE' ORDER BY sort_no ASC";
				$res = sql_query($sql);
				$hap1 = 0; $hap2 = 0;
				for ($i=0 ; $i<$res->num_rows ; $i++) {
					$row = sql_fetch_array($res);
					$hap1 += $row["limit_amount"];
					$hap2 += $row["loan_amount"];
					?>
					<? if ($RD=="3") { ?>
					<tr>
						<td style="text-align:center;"><a onclick='go_bott($(this).parent().parent().index());' style='cursor:pointer;'>▼</a></td>
						<td style="text-align:center;">
							<select name="P_reg_gubun[]" class="form-control input-sm" style="display:inline-block; width:auto;">
								<option value="갑구" <?=$row["reg_gubun"]=="갑구"?"selected":""?>>갑구</option>
								<option value="을구" <?=$row["reg_gubun"]=="을구"?"selected":""?>>을구</option>
							</select>
						</td>
						<td style="text-align:center;">
							<input type=text name='P_creditor[]' class='form-control input-sm' style='display:inline; text-align:left; width:100%;' value='<?=$row["creditor"]?>' >
						</td>
						<td style="text-align:right; padding-right:10px;">
							<input type=text name='P_limit_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:115px;' value='<?=number_format($row["limit_amount"])?>' onchange="yul_mod('p_yul',$(this).parent().parent().index());" onkeyup="fn_number_comaX2(this);"> 원
						</td>
						<td style="text-align:right; padding-right:10px;">
							<input type=text name='P_loan_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:115px;' value='<?=number_format($row["loan_amount"])?>' onkeyup="fn_number_comaX2(this);"> 원
						</td>
						<td style="text-align:center;">
							<select name='P_loan_percent[]' class='form-control input-sm' onchange=yul_mod("p_yul",$(this).parent().parent().index()) style='display:inline;width:auto;'>
							<? for ($p=110 ; $p<=150 ; $p=$p+5) { ?>
								<option value='<?=$p?>' <?=$row["loan_percent"]==$p?"selected":""?> ><?=$p?></option>
							<? } ?>
							</select> %
						</td>
						<td style="text-align:center;">
							<input type=text name='P_debtor[]' class='form-control input-sm' style='display:inline; text-align:center; width:70px;' value='<?=$row["debtor"]?>'  />
						</td>
						<td style="text-align:center;">
							<input type=text name='P_reg_obj[]' class='form-control input-sm' style='display:inline; text-align:center; width:100px;' value='<?=$row["reg_obj"]?>' >
						</td>
						<td><a onclick='go_del("pre_loan", $(this).parent().parent().index());' style='cursor:pointer;'>-</ㅁ></td>
					</tr>
					<? } else { ?>
					<tr>
						<td style="text-align:center;"></td>
						<td style="text-align:center;"><?=$row["reg_gubun"]?></td>
						<td style="text-align:center;"><?=$row["creditor"]?></td>
						<td style="text-align:right; padding-right:10px;"><?=number_format($row["limit_amount"])?> 원</td>
						<td style="text-align:right; padding-right:10px;"><?=number_format($row["loan_amount"])?> 원</td>
						<td style="text-align:center;"><?=$row["loan_percent"]?> %</td>
						<td style="text-align:center;"><?=$row["debtor"]?></td>
						<td style="text-align:center;"><?=$row["reg_obj"]?></td>
						<td></td>
					</tr>
					<? } ?>
					<?
				}
				?>
					</tbody>
					<tr>
						<td style="text-align:center;" colspan=3>
							<? if ($RD=="3") {?><button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="get_sum();">합 계</button>
							<? } else echo "합 계"?>
						</td>
						<td style="text-align:right; padding-right:10px;"><span id="pre_high_amt"><?=number_format($hap1)?></span> 원</td>
						<td style="text-align:right; padding-right:10px;"><span id="pre_gi_amt"><?=number_format($hap2)?></span> 원</td>
						<td colspan=3></td>
					</tr>
					<tr>
						<th colspan=9  style="background-color:#80CCE1;">대환 정보</th>
					</tr>
					<tr>
						<th><? if ($RD=="3") { ?><a onclick="add_row('R');" style="cursor:pointer;">+</a><? } ?></th>
						<th>구분</th>
						<th>금융업체</th>
						<th>채권최고액</th>
						<th>기대출금액</th>
						<th>설정율</th>
						<th>채무자</th>
						<th>등기목적</th>
						<th>-</th>
					</tr>
					<tbody id="rep_loan">
				<?
				$sql = "SELECT * FROM hloan_content_loan WHERE hcseq = '$idx' AND loan_gubun='REP' ORDER BY sort_no ASC";
				$res = sql_query($sql);
				$hap3 = 0 ; $hap4 = 0;
				for ($i=0 ; $i<$res->num_rows ; $i++) {
					$row = sql_fetch_array($res);
					$hap3 += $row["limit_amount"];
					$hap4 += $row["loan_amount"];
					?>
					<? if ($RD=="3") { ?>
					<tr>
						<td style="text-align:center;"><a onclick='go_top($(this).parent().parent().index());' style='cursor:pointer;'>▲</a></td>
						<td style="text-align:center;">
							<select name="R_reg_gubun[]" class="form-control input-sm" style="display:inline-block; width:auto;">
								<option value="갑구" <?=$row["reg_gubun"]=="갑구"?"selected":""?>>갑구</option>
								<option value="을구" <?=$row["reg_gubun"]=="을구"?"selected":""?>>을구</option>
							</select>
						</td>
						<td style="text-align:center;">
							<input type=text name='R_creditor[]' class='form-control input-sm' style='display:inline; text-align:left; width:100%;' value='<?=$row["creditor"]?>' >
						</td>
						<td style="text-align:right; padding-right:10px;">
							<input type=text name='R_limit_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:115px;' value='<?=number_format($row["limit_amount"])?>' onchange="yul_mod('r_yul',$(this).parent().parent().index());" onkeyup="fn_number_comaX2(this);"> 원
						</td>
						<td style="text-align:right; padding-right:10px;">
							<input type=text name='R_loan_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:115px;' value='<?=number_format($row["loan_amount"])?>' onkeyup="fn_number_comaX2(this);"> 원
						</td>
						<td style="text-align:center;">
							<select name='R_loan_percent[]' class='form-control input-sm' style='display:inline;width:auto;' onchange=yul_mod("r_yul",$(this).parent().parent().index())>
							<? for ($p=110 ; $p<=150 ; $p=$p+5) { ?>
								<option value='<?=$p?>' <?=$row["loan_percent"]==$p?"selected":""?> ><?=$p?></option>
							<? } ?>
							</select> %
						</td>
						<td style="text-align:center;">
							<input type=text name='R_debtor[]' class='form-control input-sm' style='display:inline; text-align:center; width:70px;' value='<?=$row["debtor"]?>'  />
						</td>
						<td style="text-align:center;">
							<input type=text name='R_reg_obj[]' class='form-control input-sm' style='display:inline; text-align:center; width:100px;' value='<?=$row["reg_obj"]?>' >
						</td>
						<td><a onclick='go_del("rep_loan", $(this).parent().parent().index());' style='cursor:pointer;'>-</ㅁ></td>
					</tr>
					<? } else { ?>
					<tr>
						<td style="text-align:center;"></td>
						<td style="text-align:center;"><?=$row["reg_gubun"]?></td>
						<td style="text-align:center;"><?=$row["creditor"]?></td>
						<td style="text-align:right; padding-right:10px;"><?=number_format($row["limit_amount"])?> 원</td>
						<td style="text-align:right; padding-right:10px;"><?=number_format($row["loan_amount"])?> 원</td>
						<td style="text-align:center;"><?=$row["loan_percent"]?> %</td>
						<td style="text-align:center;"><?=$row["debtor"]?></td>
						<td style="text-align:center;"><?=$row["reg_obj"]?></td>
						<td></td>
					</tr>
					<? } ?>
					<?
				}
				?>
					</tbody>
					<tr>
						<td style="text-align:center;" colspan=3>
							<? if ($RD=="3") {?><button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="get_sum();">합 계</button>
							<? } else echo "합 계"?>
						</td>
						<td style="text-align:right; padding-right:10px;"><span id="rep_high_amt"><?=number_format($hap3)?></span> 원</td>
						<td style="text-align:right; padding-right:10px;"><span id="rep_gi_amt"><?=number_format($hap4)?></span> 원</td>
						<td colspan=3></td>
					</tr>
				</table>
			</td>
		</tr>


<?
if ((COUNT($examountArr) and $examountArr[0]>0) OR (COUNT($maxbondArr) and $maxbondArr[0]>0)) {
	?>
		<tr>
			<th><?php IF($RD=="3") { ?><div class="circleArea" OnClick="fn_additem_examount('plus');">+</div><?php } ?> 기대출금액</th>
			<td>
			<?php
			IF($idx) {

				FOR($i=0;$i<COUNT($examountArr);$i++) {

					IF($RD == "2") ECHO ($i+1).") ";

					ECHO INPUT_FORM($strInputText1,"examount[]","form-control input-sm",""," OnKeyUp=\"fn_number_coma('examount[]',this.value, $(this).index());\" style='display:block; width:120px; margin:3px 0; text-align:right;' ",f_number($examountArr[$i]));

					IF($RD == "2") ECHO "<BR>";
				}

			} ELSE {

				ECHO INPUT_FORM($strInputText1,"examount[]","input02",""," OnKeyUp=\"fn_number_coma('examount[]',this.value, $(this).index());\"","");

			}
			?>
			<div id="examountarea"></div>
			</td>
			<th>선순위 채권최고액</th>
			<td>
			<?php
			IF($idx) {

				FOR($i=0;$i<COUNT($maxbondArr);$i++) {

					IF($RD == "2") { ECHO ($i+1).") ";  }

					ECHO INPUT_FORM($strInputText1,"maxbond[]","form-control input-sm",""," OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());\" style='display:block; width:120px; margin:3px 0; text-align:right;'",f_number($maxbondArr[$i]));

					IF($RD == "2") { ECHO "<BR>";  }
				}

			} ELSE {

				ECHO INPUT_FORM($strInputText1,"maxbond[]","input02",""," OnKeyUp=\"fn_number_coma('maxbond[]',this.value, $(this).index());\"","");

			}
			?>
			<div id="maxbondarea"></div>
			</td>
		</tr>
	<?
	}
?>
		<tr>
			<th rowspan=2>시세 기준 값</th>
			<td rowspan=2 style="vertical-align:middle;" class="tdL">


				<?php ECHO fn_general_select($ltvkind,$strRadioText,fn_ltvkind(),"","ltvkind","","");?>

				<?php IF($RD == "3") { // 등록,수정?>

				<!--input type="button" name="calcbtn" value="LTV 계산" class="btnCalc" OnClick="fn_calc_ltv();" /-->
				<!--button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="fn_calc_ltv();">계산하기</4-->
				<button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="cal_ltv();">계산하기</button>
				<button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="cal_ltv2();">계산</button>
				<?php } ?>
			</td>
			<th>원금기준 LTV</th>
			<td>
				<?php IF($RD == "2") { ECHO fn_check_ltv($ltvmoney); } ELSE { ?>
				<?php ECHO INPUT_FORM($strInputText1,"ltvmoney","input06".$strLevClass,"","required itemname='LTV'",$ltvmoney);?>
				<?php } ?>
				%
			</td>
		</tr>

		<tr>
			<th>채권최고액기준 LTV</th>
			<td>
				<? if ($RD=="3") { ?>
					<input type="TEXT" id="ltvmoney2" name="ltvmoney2" value="<?=$ltvmoney2?>" required="" class="input06">
				<? } else { ?>
					<?=$ltvmoney2?>
				<? } ?>
				%
			</td>
		</tr>

		<tr>
			<th>가산금리</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="radio" name="add_hellobase" id="rr31" value="0"    <?=$add_hellobase=="0"?"checked":""?> >
					<label for="rr31" style="display:inline-block; float:none; margin:0;">없음</label>
				<input type="radio" name="add_hellobase" id="rr32" value="0.5"  <?=$add_hellobase=="0.5"?"checked":""?> >
					<label for="rr32" style="display:inline-block; float:none; margin:0;">0.5%</label>
				<input type="radio" name="add_hellobase" id="rr33" value="1"    <?=$add_hellobase=="1"?"checked":""?> >
					<label for="rr33" style="display:inline-block; float:none; margin:0;">1%</label>
				<button type="button" class="btn btn-sm btn-default" style="margin-left:15px; "  onclick="fn_calc_eja();">계산하기</button>
			<? } else { ?>
				<?=$add_hellobase?>
			<? } ?>
			</td>
			<th>금리</th>
			<td>
				<?//php ECHO INPUT_FORM($strInputText1,"hellobase","input02","","",$hellobase);?>
				<? if ($RD=="3") { ?>
					<input type="TEXT" id="hellobase" name="hellobase" value="<?=$hellobase?>" required="" class="form-control input-sm" style="display:inline; width:100px; text-align:right;">
				<? } else { ?>
					<?=$hellobase?>
				<? } ?>
				%
			</td>
		</tr>

		<tr>
			<!--th>기표희망일</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"vdate","input06 datepicker","","",$vdate);?></td-->
			<th>매각가율</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="sale_per" name="sale_per" value="<?=$sale_per?>" required="" class="form-control input-sm" style="display:inline; width:100px; text-align:right;"> %
			<? } else { ?>
				<?=$sale_per?> %
			<? } ?>
			</td>
			<th>대출만기일</th>
			<td colspan="1"><?php ECHO INPUT_FORM($strInputText1,"hloan_end_date","form-control input-sm datepicker","","style='display:inline; width:100px; text-align:center;'",$hloan_end_date);?></td>
		</tr>

		<? /*
		<tr>
			<th class="tdtop">담당자</th>
			<td><?php ECHO INPUT_FORM("txt1","hname","input02","","",$hname);?></td>
			<!--th class="tdtop">담당자 연락처</th>
			<td><?php ECHO INPUT_FORM("txt1","hphone","input02","","",$hphone);?></td-->
			<th class="tdtop"></th>
			<td></td>
		</tr>
		*/ ?>


		<!--tr>
			<th>담보구분</th>
			<td colspan="3"  class="tdL"><?php ECHO fn_general_select($skind,$strRadioText,fn_skind(),"","skind","class='radio03'","");?></td>
		</tr-->

		<tr>
			<th>헬로 수수료율</th>
			<td colspan="1" class="tdL">
				<?//php ECHO INPUT_FORM($strInputText1,"fees","input06","","required itemname='플랫폼 수수료율' placeholder=''",$fees);?>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="fees" name="fees" value="<?=$fees?>" required="" class="input06" style="text-align:right;">
			<? } else { ?>
				<?=$fees?>
			<? } ?>
				%
			</td>
			<th rowspan=2>플랫폼이용료</th>
			<td rowspan=2 style="vertical-align:middle;">
				<?//php ECHO INPUT_FORM($strInputText1,"hellofee","input02","","",f_number($hellofee));?>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="hellofee" name="hellofee" value="<?=f_number($hellofee)?>" required="" class="input06" style="text-align:right;">
				<button type="button" class="btn btn-sm btn-default" style="margin-left:3px; margin-bottom:5px;" onclick="cal_hellofee();">계산하기</button>
			<? } else { ?>
				<?=f_number($hellofee)?>
			<? } ?>
			</td>
		</tr>

		<tr>
			<th>중개 수수료율</th>
			<td>
			<? if ($RD=="3") { ?>
				<input type="TEXT" id="hm_fees" name="hm_fees" value="<?=$hm_fees?>" required="" class="input06" style="text-align:right;">
			<? } else { ?>
				<?=$hm_fees?>
			<? } ?>
				%
			</td>
		</tr>

		<!--
		<tr>
			<th>원금기준 LTV(%)</th>
			<td colspan="1" class="tdL">
				<?php IF($RD == "2") { ECHO fn_check_ltv($ltvmoney); } ELSE { ?>
				<?php ECHO INPUT_FORM($strInputText1,"ltvmoney","input06".$strLevClass,"","required itemname='LTV'",$ltvmoney);?>
				<?php } ?>
				<?php IF($RD == "2") { ECHO " &nbsp;&nbsp; / 기준 : ";  }?><?php ECHO fn_general_select($ltvkind,$strRadioText,fn_ltvkind(),"","ltvkind","","");?>

				<?php IF($RD == "3") { // 등록,수정?>

				<input type="button" name="calcbtn" value="LTV 계산" class="btnCalc" OnClick="fn_calc_ltv();" />
				<?php } ?>
			</td>
			<th>채권최고액기준 LTV(%)</th>
			<td colspan="3" class="tdL">
				<? if ($RD=="3") { ?>
					<input type="TEXT" id="ltvmoney2" name="ltvmoney2" value="<?=$ltvmoney2?>" required="" class="input02">
				<? } else { ?>
					<?=$ltvmoney2?>
				<? } ?>
			</td>
		</tr>
		-->

		<? /*
		<tr>
			<!--
			<th>준공일</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"comday","input02","","required itemname='준공일'",$comday);?></td>
			-->
			<th></th>
			<td></td>
			<th>세대수</th>
			<td><?php ECHO INPUT_FORM($strInputText1,"hholds","input02","","required itemname='세대수'",$hholds);?></td>
		</tr>
		*/ ?>

		<tr>
			<th>특이사항</th>
			<td colspan="3" class="tdL">
				<?php ECHO INPUT_FORM($strInputTextarea,"content","text01","","",$content);?>
			</td>
		</tr>
<?
if ($RD=="2") { ?>
		<tr>
			<th>원인서류</th>
			<td colspan="3">
				<?php FOR($i=0;$i<5;$i++) { ?>
					<?
					//if ($ifileArr[$i] and file_exists("afile/".$ifileArr[$i])) {
					if ($ifileArr[$i]) { ?>
					<a href="/data/afile/<?php ECHO $ifileArr[$i];?>" target="_blank">[다운로드] <?php ECHO $ifileArr_ori[$i];?></a><br />
					<? } ?>
				<?php } ?>
			</td>
		</tr>
	<?
}
?>

<!--
		<tr>
			<th>심사</th>
			<td colspan="3">
<?php
				FOR($i=0;$i<COUNT($strVoteMember);$i++)
				{
					ECHO $strVoteMember[$i];
				}
?>
			</td>
		</tr>
//-->
		<tr>
			<th>헬로펀딩 상품</th>
			<td class="tdL"><?php ECHO fn_general_select($productyn,$strSelectBox,fn_product_hello(),":헬로펀딩 상품:","productyn","class='input02'","");
				?></td>
			<th>품번</th>
			<td class="tdL">

			<? if ($RD=="3") { ?>
				<?php ECHO INPUT_FORM($strInputText1,"product_idx","form-control input-sm","","style='display:inline; width:100px; text-align:center;'",$product_idx);?>
			<? } else {
				if ($product_idx) { ?>
				<a href="/investment/investment.php?prd_idx=<?=$product_idx?>" target=_blank>
				<? } ?>
				<?=$product_idx?></a>
			<? } ?>
			</td>

			<!--
			<th>물건담당자</th>
			<td class="tdL"><?php ECHO fn_general_select($mb_no,$strSelectBox,fn_product_manager($connect_db),":물건담당자:","mb_no","class='input02'","");
				?></td>
			//-->

		</tr>
	</table>

<?
$sql_mv = "SELECT * FROM hloan_admin_member WHERE mb_no='".$member['mb_no']."'";
$res_mv = sql_query($sql_mv);
$cnt_mv = $res_mv->num_rows;
if ($cnt_mv) {
	$row_mv = sql_fetch_array($res_mv);
	$midx = $row_mv["midx"];
}

if ($RD<>"3") {
	?>
	<table class="table table-bordered" style="max-width:1000px; margin-top:20px; ">
		<tbody>
			<tr>
				<th colspan=4 style="width:400px; border-right: 1px solid black;">상품심사</th>
				<th colspan=4 style="width:600px; ">준법감시인 심사</th>
			</tr>
			<tr>
				<th style="text-align:center;">심사</th>
				<td>
				<?
				$vsql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$idx' AND gubun='심사' ORDER BY idx DESC LIMIT 1";
				$vres = sql_query($vsql);
				$vcnt = $vres->num_rows;
				if ($vcnt) {
					$vrow = sql_fetch_array($vres);
					$vyn1 = $vrow["votyn"];
				} else $vyn1 = "";
				?>
					<select name="votyn1" id="votyn1" class="form-control input-sm" style="display:inline; width:80px;" onchange="vote_1('<?=$midx?>','1',this.value,'1','<?=$idx?>',event, '심사');">
						<option value=""></option>
						<option value="2" <?=$vyn1=="2"?"selected":""?> >대기</option>
						<option value="3" <?=$vyn1=="3"?"selected":""?> >승인</option>
						<option value="1" <?=$vyn1=="1"?"selected":""?> >반려</option>
					</select>
				</td>
				<th>승인</th>
				<td style="border-right: 1px solid black;">
				<?
				$vsql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$idx' AND gubun='승인' ORDER BY idx DESC LIMIT 1";
				$vres = sql_query($vsql);
				$vcnt = $vres->num_rows;
				if ($vcnt) {
					$vrow = sql_fetch_array($vres);
					$vyn2 = $vrow["votyn"];
				} else $vyn2 = "";
				?>
					<select name="votyn2" id="votyn2" class="form-control input-sm" style="display:inline; width:80px;" onchange="vote_1('<?=$midx?>','1',this.value,'1','<?=$idx?>',event, '승인');">
						<option value=""></option>
						<option value="2" <?=$vyn2=="2"?"selected":""?> >대기</option>
						<option value="3" <?=$vyn2=="3"?"selected":""?> >승인</option>
						<option value="1" <?=$vyn2=="1"?"selected":""?> >반려</option>
					</select>
				</td>
				<th>준법감시인 심사</th>
				<td>
				<?
				$vsql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$idx' AND gubun='심의' ORDER BY idx DESC LIMIT 1";
				$vres = sql_query($vsql);
				$vcnt = $vres->num_rows;
				if ($vcnt) {
					$vrow = sql_fetch_array($vres);
					$vyn3 = $vrow["votyn"];
					$sim_num = $vrow["etc1"];
				} else $vyn3 = "";

				$vsql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$idx' AND gubun='심의번호' ORDER BY idx DESC LIMIT 1";
				$vres = sql_query($vsql);
				$vcnt = $vres->num_rows;
				if ($vcnt) {
					$vrow = sql_fetch_array($vres);
					$sim_num = $vrow["etc1"];
				} else $vyn3 = "";
				?>
					<select name="votyn3" id="votyn3" class="form-control input-sm" style="display:inline; width:80px;" onchange="vote_1('<?=$midx?>','1',this.value,'1','<?=$idx?>',event, '심의');">
						<option value=""></option>
						<option value="2" <?=$vyn3=="2"?"selected":""?> >대기</option>
						<option value="3" <?=$vyn3=="3"?"selected":""?> >승인</option>
						<option value="1" <?=$vyn3=="1"?"selected":""?> >반려</option>
					</select>
				</td>
				<th>준법감시인 심사번호</th>
				<td>
					<input type="TEXT" id="sim_num" name="sim_num" value="<?=$sim_num?>" required="" class="form-control input-sm" style="width:150px; display:inline;">
					<button type="button" class="btn btn-default" onclick="vote_1('<?=$midx?>','1',document.regfm.sim_num.value,'1','<?=$idx?>',event, '심의번호');">입력</button>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="margin-top:-15px;">* 헬로펀딩</div>
	<?
}
?>

<?
$vsql = "SELECT t1.midx, t1.mb_no, t1.mb_id, t1.mb_name, t1.mb_level,
					 IFNULL(t2.idx,0) as idx, IFNULL(t2.votyn,'')as votyn, t2.idx, t2.hcseq
				FROM hloan_admin_member t1
		   LEFT JOIN
					(SELECT idx,midx, votyn, hcseq FROM hloan_admin_member_vote WHERE hcseq='$idx') t2 ON t1.midx=t2.midx
		    ORDER BY t1.sort_id ASC";
$vsql = "SELECT A.* , B.*
		   FROM hloan_admin_member_vote A
	  LEFT JOIN hloan_admin_member B ON (A.midx=B.midx)
		  WHERE hcseq='$idx'";
$vres = sql_query($vsql);
$vcnt = $vres->num_rows;
?>
<table class="table table-bordered" style="max-width:1000px; margin-top:20px;">
	<tr>
		<th colspan="4">결재로그</th>
	</tr>
	<tr>
		<th>구분</th>
		<th>내용</th>
		<th>일자</th>
		<th>담당자</th>
	</tr>
<?
for ($i=0 ; $i<$vcnt ; $i++) {
	$vrow = sql_fetch_array($vres);
	if ($vrow["votyn"]=="1") $votyn_txt = "반려";
	else if ($vrow["votyn"]=="2") $votyn_txt = "대기";
	else if ($vrow["votyn"]=="3") $votyn_txt = "승인";
	else $votyn_txt = "";

	if ($vrow["gubun"]=="심의번호") $votyn_txt=$vrow["etc1"];

	if ($vrow["gubun"]=="심의") $vrow["gubun"]="준법감시인 심사";
	?>
	<tr>
		<td  style="text-align:center;"><?=$vrow["gubun"]?></td>
		<td style="text-align:center;"><?=$votyn_txt?></td>
		<td style="text-align:center;"><?=$vrow["reg_date"]?></td>
		<td style="text-align:center;"><?=$vrow["mb_name"]?></td>
	</tr>
	<?
}
?>
</table>

	<?php IF($RD == "2") { ?>
	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"].$strListUrl?>&RD=3&idx=<?php ECHO $idx;?>';" class="btn btn-default">수정하기</button>
		&nbsp;&nbsp;
		<?php IF($member["mb_no"]=="2" || $member["mb_no"]=="5" || $member["mb_no"]=="10966" || $member["mb_no"]=="9" || $member["mb_no"]=="17281" || $member["mb_no"]=="43790") { ?>
		<button type="button" id="list_button" onClick="check_del_form('dregfm',event);" class="btn btn-default">삭제하기</button>
		&nbsp;&nbsp;
		<?php } ?>
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

<br/><br/>
<?
if ($RD=="3") {
	?>
<form name="fm_file" method="post" action="file_save.php" autocomplete="off" enctype="multipart/form-data">

	<input type="hidden" name="idx" value="<?=$idx?>"/>
	<input type="hidden" name="old_file" value="<?=$ifile?>">
	<input type="hidden" name="old_file_ori" value="<?=$ifile_ori?>">

	<table class="table table-bordered" style="max-width:1000px; margin-top:15px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th>원인서류</th>
			<td colspan="3">

				<table border=0>
<?
$edit_file="";

if ($RD=="3") {
	FOR($i=0;$i<5;$i++) { ?>
					<tr>
						<td style="height:46px;">
		<? // if ($ifileArr[$i] and file_exists("afile/".$ifileArr[$i])) { ?>
		<? if ($ifileArr[$i] ) { ?>
			<a href="/data/afile/<?php ECHO $ifileArr[$i];?>" target="_blank">[다운로드] <?php ECHO $ifileArr_ori[$i];?></a>
			<a onclick="del_file('<?=$ifileArr[$i];?>','<?=$ifileArr_ori[$i];?>');" style="cursor:pointer;">[<font color="red">삭제</font>]</a><br />
		<? } else {
			$edit_file="Y"; ?>
			<input type="FILE" name="i_file[]" id="i_file_<?=$i?>" class="" style="margin-bottom:5px;" />
		<? } ?>
						</td>
					</tr>
	<? }

	if ($edit_file=="Y") { ?>
					<tr>
						<td style="text-align:center;">
	<button type="button" onclick="go_file_save()" class="btn btn-default">파일저장</button>
						</td>
					</tr>
	<? }
} else if ($RD=="2") {
	FOR($i=0;$i<count($ifileArr);$i++) { ?>
					<tr>
						<td style="height:46px;">
		<? // if ($ifileArr[$i] and file_exists("afile/".$ifileArr[$i])) { ?>
		<? if ($ifileArr[$i]) { ?>
			<a href="/data/afile/<?php ECHO $ifileArr[$i];?>" target="_blank">[다운로드] <?php ECHO $ifileArr_ori[$i];?></a>
		<? } ?>
						</td>
					</tr>
	<? }

}
?>
				</table>
			</td>
		</tr>
	</table>
	<?
}
?>


</form>

<script>
function down_pdf() {
	var uniqNo = $("#cert_num").val();

	if (!uniqNo) return;

	window.open('hyphen_view_db.php?uniqNo='+uniqNo,'_blank','toolbar=0,menubar=0,status=0,scrollbars=yes,resizable=yes');
}
var honey_sise;
function hyphen_sise() {

	var bdCd = $("#buildingCd").val();

	if (!bdCd) {
		alert("단지코드가 없습니다.");
		return;
	}


	$.ajax({
		type : 'post',
		dataType : 'json',
		url : '/hyphen/hyphen_sise.php',
		data : {'buildingCd': bdCd},
		success : function(data) {

			console.log(data);
			honey_sise = data;

			$("#jmt").empty();  // 평형선택 초기화
			$("#jmt").append("<option value=''>평형 선택</option>");

			for (var i=0 ; i<data["outH0001"]["list"].length; i++) {
				$("#jmt").append("<option value='"+data["outH0001"]["list"][i]["areaSerialNumber"]+"'>"+data["outH0001"]["list"][i]["exclusiveSpace"]+" "+data["outH0001"]["list"][i]["supplySpaceType"]+"</option>");
			}

		},
		//beforeSend: function() { loading('on'); },
		//complete: function() { loading('off'); },
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}

function set_sise() {
	var jm_code = $("#jmt").val();
	var jm ;
	for (var i=0 ; honey_sise["outH0001"]["list"].length; i++) {
		if (honey_sise["outH0001"]["list"][i]["areaSerialNumber"] == jm_code) {
			jm = honey_sise["outH0001"]["list"][i];
			break;
		} else {
			console.log("아니야");
		}
	}

	console.log(jm);
	//$("#kb_jm").val(jm["exclusiveSpace"]);  // 전용면적
	$("#kbarea").val(jm["exclusiveSpace"]);  // 전용면적
	//$("#kb_il").val(number_format(jm["nomAvrDealPrc"]*10000));   // 일반평균가
	$("#kbprice").val(number_format(jm["nomAvrDealPrc"]*10000));   // 일반평균가
	//$("#kb_low").val(number_format(jm["subAvrDealPrc"]*10000));  // 하위편균가
	$("#kbllimit").val(number_format(jm["subAvrDealPrc"]*10000));  // 하위편균가
	//$("#kb_sil").val(number_format(jm["recentDealPrc"]*10000));  // 최근매매가
	$("#kb_mm_sil").val(number_format(jm["recentDealPrc"]*10000));  // 최근매매가
	//$("#kb_tot_house").val(number_format(honey_sise["outH0001"]["totGen"]));  // 세대수
	$("#hholds").val(number_format(honey_sise["outH0001"]["totGen"]));  // 세대수
	//$("#kb_top_floor").val(number_format(honey_sise["outH0001"]["topFloor"]));  // 최고층

}

function hyphen_sise_code() {

	var addr = $("#laddr").val();
	if (!addr) {
		alert("주소를 입력해주세요.");
		return;
	}

	var addr_arr = addr.split(" ");
	var srch_addr = "";

	for (var i=0 ; i<addr_arr.length ; i++) {

		srch_addr += " " + addr_arr[i];

		if (addr_arr[i].substring(addr_arr[i].length-1)=="동") {
			srch_addr += " " + addr_arr[i+1];
			break;
		}
	}


	$("#jmt option").remove();
	$("#jmt").append("<option value='' style='text-align-last: center; text-align: center; -ms-text-align-last: center; -moz-text-align-last: center;'>....검 색 중....</option>");



	//var tmp1 = addr.split("[");
	//var tmp2 = tmp1[1].split("]");
	//srch_addr = tmp2[0];

	//srch_addr = addr;


	$.ajax({
		type : 'post',
		dataType : 'json',
		url : '/hyphen/hyphen_sise_code.php',
		data : {'addr': srch_addr},
		success : function(data) {

			console.log(data);

			if (data["out"]["outB0002"]["list"].length) {
				$("#buildingCd").val(data["out"]["outB0002"]["list"]["0"]["buildingCd"]);
				hyphen_sise();
			} else {
				$("#jmt option").remove();
				alert("주소지를 찾을수 없습니다.\n("+srch_addr+")");
			}

		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			$("#jmt option").remove();
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}

function yul_mod(PR , idx) {

	$('[type=text], textarea').each(function(){ this.defaultValue = this.value; });
	$('[type=checkbox], [type=radio]').each(function(){ this.defaultChecked = this.checked; });
	$('select option').each(function(){ this.defaultSelected = this.selected; });

	var limit_amt = 0;
	var pcnt = 0;

	if (PR=="p_yul") {

		$("input[name='P_limit_amount[]']").each(function(i, v) {
			if (i==idx) limit_amt= this.value.replace(/,/gi,"");
		});

		$("select[name='P_loan_percent[]']").each(function(i, v) {
			if (i==idx) pcnt= this.value.replace(/,/gi,"");
		});

		var gi_amt = limit_amt * 100 / pcnt ;

		$("input[name='P_loan_amount[]']").each(function(i, v) {
			if (i==idx) this.value = gi_amt ;
		});

	} else if (PR=="r_yul") {

		$("input[name='R_limit_amount[]']").each(function(i, v) {
			if (i==idx) limit_amt= this.value.replace(/,/gi,"");
		});

		$("select[name='R_loan_percent[]']").each(function(i, v) {
			if (i==idx) pcnt= this.value.replace(/,/gi,"");
		});

		var gi_amt = limit_amt * 100 / pcnt ;

		$("input[name='R_loan_amount[]']").each(function(i, v) {
			if (i==idx) this.value = gi_amt ;
		});

	}

}

function get_issue2() {
}
function get_issue_mod() {

	//$("#cert_num").val("13452010008341");
	var cnum = $("#cert_num").val();

	if (!cnum) {
		alert("등기부등본 조회용 주소 고유번호가 없습니다.\n주소검색을 해주세요.");
		return;
	}


	get_issue_manual();
}


</script>

<script>
function add_row(gbn) {

	var trow="";

	if (gbn=="P") {

		trow = "<tr>";
		trow += "<td><a onclick='go_bott($(this).parent().parent().index());' style='cursor:pointer;'>▼</a></td>";
		trow += "<td style='text-align:center;'><select name='P_reg_gubun[]' class='form-control input-sm' style='display:inline-block; width:auto;'><option value=''></option><option value='갑구'>갑구</option><option value='을구'>을구</option></select></td>"; // 구분 갑구 을구
		trow += "<td><input type=text name='P_creditor[]' class='form-control input-sm' style='display:inline; text-align:left; width:100%;' value='' ></td>"; // 금융업체
		trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='P_limit_amount[]' onkeyup='fn_number_comaX2(this);' onchange='yul_mod(\"p_yul\",$(this).parent().parent().index());' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='' > 원</td>"; // 채권최고액
		trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='P_loan_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='' > 원</td>"; // 기대출금액
		trow += "<td style='text-align:center;'><select name='P_loan_percent[]' onchange=yul_mod(\"p_yul\",$(this).parent().parent().index()) class='form-control input-sm' style='display:inline;width:50px; padding:5px 3px;'><option value='110'>110</option><option value='115'>115</option><option value='120' selected>120</option><option value='125'>125</option><option value='130'>130</option><option value='135'>135</option><option value='140'>140</option><option value='145'>145</option><option value='150'>150</option></select> %</td>"; // 설정율
		trow += "<td style='text-align:center;'><input type=text name='P_debtor[]' class='form-control input-sm' style='display:inline; text-align:center; width:90px;' value='' ></td>"; // 채무자
		trow += "<td style='text-align:center;'><input type=text name='P_reg_obj[]' class='form-control input-sm' style='display:inline; text-align:center; width:110px;' value='' ></td>"; // 등기목적
		trow += "<td style='text-align:center;'><a onclick='go_del(\"pre_loan\", $(this).parent().parent().index());' style='cursor:pointer;'>-</a></td>";
		trow += "</tr>";

		$("#pre_loan").append(trow);

	} else if (gbn=="R") {

		trow = "<tr>";
		trow += "<td><a onclick='go_top($(this).parent().parent().index());' style='cursor:pointer;'>▲</a></td>";
		trow += "<td style='text-align:center;'><select name='R_reg_gubun[]' class='form-control input-sm' style='display:inline-block; width:auto;'><option value=''></option><option value='갑구'>갑구</option><option value='을구'>을구</option></select></td>"; // 구분 갑구 을구
		trow += "<td><input type=text name='R_creditor[]' class='form-control input-sm' style='display:inline; text-align:left; width:100%;' value='' ></td>"; // 금융업체
		trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='R_limit_amount[]' onkeyup='fn_number_comaX2(this);' onchange='yul_mod(\"r_yul\",$(this).parent().parent().index());' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='' ></td>"; // 채권최고액
		trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='R_loan_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='' ></td>"; // 기대출금액
		trow += "<td style='text-align:center;'><select name='R_loan_percent[]' onchange=yul_mod(\"r_yul\",$(this).parent().parent().index()) class='form-control input-sm' style='display:inline;width:50px; padding:5px 3px;'><option value='110'>110</option><option value='115'>115</option><option value='120' selected>120</option><option value='125'>125</option><option value='130'>130</option><option value='135'>135</option><option value='140'>140</option><option value='145'>145</option><option value='150'>150</option></select></td>"; // 설정율
		trow += "<td style='text-align:center;'><input type=text name='R_debtor[]' class='form-control input-sm' style='display:inline; text-align:center; width:90px;' value='' ></td>"; // 채무자
		trow += "<td style='text-align:center;'><input type=text name='R_reg_obj[]' class='form-control input-sm' style='display:inline; text-align:center; width:110px;' value='' ></td>"; // 등기목적
		trow += "<td style='text-align:center;'><a onclick='go_del(\"rep_loan\", $(this).parent().parent().index());' style='cursor:pointer;'>-</a></td>";
		trow += "</tr>";
		$("#rep_loan").append(trow);

	}
}

function get_sum() {

	$('[type=text], textarea').each(function(){ this.defaultValue = this.value; });
	$('[type=checkbox], [type=radio]').each(function(){ this.defaultChecked = this.checked; });
	$('select option').each(function(){ this.defaultSelected = this.selected; });

	// 선순위 채권최고액의 합을 구한다.
	var total = 0;
    $('input[name^="P_limit_amount"]').each( function() {
        total += isNaN(parseInt(this.value))?0:parseInt(this.value.replace(/,/gi,""));
    });
	$("#pre_high_amt").text(number_format_hyphen(total));

	// 선순위 기대출 금액의 합을 구한다.
	var gtotal = 0;
    $('input[name^="P_loan_amount"]').each( function() {
        gtotal += isNaN(parseInt(this.value))?0:parseInt(this.value.replace(/,/gi,""));
    });
	$("#pre_gi_amt").text(number_format_hyphen(gtotal));

	// 대환 채권최고액의 합을 구한다.
	var total = 0;
    $('input[name^="R_limit_amount"]').each( function() {
        total += isNaN(parseInt(this.value))?0:parseInt(this.value.replace(/,/gi,""));
    });
	$("#rep_high_amt").text(number_format_hyphen(total));

	// 대환 기대출 금액의 합을 구한다.
	var gtotal = 0;
    $('input[name^="R_loan_amount"]').each( function() {
        gtotal += isNaN(parseInt(this.value))?0:parseInt(this.value.replace(/,/gi,""));
    });
	$("#rep_gi_amt").text(number_format_hyphen(gtotal));

}
$( document ).ready(function() {
	//get_sum();
});

function srch_sise() {

	var mg_id = $("#kb_mg_id").val();
	var ju_seri = $("#kb_ju_seri").val();


	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_kb_sise.php',
		data : {'mg_id':mg_id , 'ju_seri':ju_seri},
		success : function(data) {
			console.log(data);
			$("#kbarea").val(data.jm);
			$("#kbprice").val(number_format(data.mm*10000));     // 일반가
			$("#kbllimit").val(number_format(data.mm_b*10000));  // 하한가
			$("#hholds").val(data.tot_house);  // 총세대수
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}

function srch_sise2() {

	var d_code   = $("#d_code").val();
	var mg_id2   = $("#kb_mg_id2").val();
	var ju_seri2 = $("#kb_ju_seri2").val();


	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_kb_sise2.php',
		data : {'d_code':d_code , 'mg_id2':mg_id2 , 'ju_seri2':ju_seri2},
		success : function(data) {
			console.log(data);

			var sdd = data.mm_date;
			sdd = sdd.substring(0,4)+"."+sdd.substring(4,6)+"."+sdd.substring(6,8);
			$("#kbarea").val(data.jm);
			$("#kbprice").val(number_format(data.mm*10000));     // 일반가
			$("#kbllimit").val(number_format(data.mm_b*10000));  // 하한가
			$("#hholds").val(data.sum_tot_house);  // 총세대수
			$("#kijun").text("("+data.kijun+" 기준)");
			$("#kb_mm_sil").val(number_format(data.mm_sil*10000)); // 실거래가
			$("#kb_mm_sil_date").val(data.mm_date); // 실거래가
			//$("#kb_sil_date_disp").text(" ("+data.mm_date+")");
			$("#kb_sil_date_disp").text(sdd);
			$("#kb_date").val(data.kijun.replace(/\./gi,'')); // 실거래가
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}
</script>

<script>
function del_file(fl_name , fl_name2) {
	var yn = confirm(fl_name2+"를 삭제하시겠습니까?");
	if (!yn) return;

	$.ajax({
		type : 'POST',
		url : 'file_delete.php',
		data:{
			idx: '<?=$idx?>',
			file_name : fl_name,
			file_name_ori : fl_name2
		},
		//dataType: 'json',
		success : function(data){

			self.location.reload();

		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
			console.log(errorThrown);
			return false;
		}
	});

}
function go_file_save() {
	var f = document.fm_file;
	f.submit();
}
</script>


	<?php IF($RD == "2") { ?>
	<!-- 코멘트 //-->
	<div style="margin-top:30px; max-width:1000px;">
		<h3>COMMENT & LOG</h3>
		<ul class="list-inline" style="margin-bottom:20px;">
			<li style="width:85%;height:80px"><textarea id="comment" style="width:100%;height:100%;" required></textarea></li>
			<li style="width:14.6%"><button type="button" id="frmCmtSubmit" class="btn btn-primary" style="width:100%;height:80px;">등 록</button></li>
		</ul>
		<script>
		$('#frmCmtSubmit').click(function() {
			if( $('#comment').val()=='' ) {
				alert('내용을 입력하십시요.');  $('#comment').focus();
			}
			else {
				$.ajax({
					url : "request.proc.ajax.php",
					type: "POST",
					dataType: "JSON",
					data:{
						mode: 'new',
						idx: '<?=$idx?>',
						comment: $('#comment').val()
					},
					success:function(data) {
						if(data.result=='SUCCESS') { window.location.reload(); }
						else { console.log(result); }
					},
					error: function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
		});
		</script>
<?php
$cres  = sql_query("SELECT idx, writer, mb_id , comment, regdate FROM hloan_comment WHERE req_idx='".add_str($idx)."' ORDER BY idx DESC");
$crows = $cres->num_rows;
if($crows) {
	for($c=0,$cno=1; $c<$crows; $c++,$cno++) {
		$CROW = sql_fetch_array($cres);
		$delete_tag = "";
		if(($CROW['mb_id']==$member['mb_id']) || $member['mb_level']=='10') {
			$delete_tag = "<span onClick='dropComment(".$CROW['idx'].")' style='cursor:pointer;color:red'>×</span>";
		}

		$comm = nl2br(htmlSpecialChars($CROW['comment']));

?>
		<table style="font-size:12px">
			<colgroup>
				<col width="200">
				<col width="">
				<col width="30">
			</colgroup>
			<tr style='background:#FAFAFA'>
				<td align="left"><?=$CROW['writer']?> (<?php ECHO $CROW["mb_id"]?>)</td>
				<td align="right"><span style="color:#aaa"><?=$CROW['regdate']?></span></td>
				<td align="center"><?=$delete_tag?></td>
			</tr>
			<tr>
				<td colspan="3" style="padding:8px 20px"><?=$comm?></td>
			</tr>
		</table>
		<div style="height:10px;"></div>
<?php
		}
?>
<?php
	}
}
?>
	</div>
	<!-- 코멘트 //-->

	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>

	<form name="dregfm" id="dregfm">
		<input type="hidden" name="kind" value="del" />
		<input type="hidden" name="SE" value="<?php ECHO $idx;?>" />
		<input type="hidden" name="S1" value="<?php ECHO $S1;?>" />
		<input type="hidden" name="S2" value="<?php ECHO $S2;?>" />
		<input type="hidden" name="S3" value="<?php ECHO $S3;?>" />
		<input type="hidden" name="S4" value="<?php ECHO $S4;?>" />
		<input type="hidden" name="SC" value="<?php ECHO $SC;?>" />
		<input type="hidden" name="STXT" value="<?php ECHO $STXT;?>" />
		<input type="hidden" name="page" value="<?php ECHO $page;?>" />
	</form>


<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    //본 예제에서는 도로명 주소 표기 방식에 대한 법령에 따라, 내려오는 데이터를 조합하여 올바른 주소를 구성하는 방법을 설명합니다.
    function sample4_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var roadAddr = data.roadAddress; // 도로명 주소 변수
                var extraRoadAddr = ''; // 참고 항목 변수

                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraRoadAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if(data.buildingName !== '' && data.apartment === 'Y'){
                   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if(extraRoadAddr !== ''){
                    extraRoadAddr = ' (' + extraRoadAddr + ')';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                //document.getElementById('sample4_postcode').value = data.zonecode;
                //document.getElementById("sample4_roadAddress").value = roadAddr;
                //document.getElementById("sample4_jibunAddress").value = data.jibunAddress;
                document.getElementById('pcp_comp_addr_post').value = data.zonecode;
                document.getElementById("pcp_comp_addr").value = roadAddr;
                //document.getElementById("sample4_jibunAddress").value = data.jibunAddress;

            }
        }).open();
    }
</script>

	<script>
		function check_fn_mkind(obj)
		{
			if(obj == "A")
			{
				$("input[name=mdate]").css("display","block");
			} else {
				$("input[name=mdate]").css("display","none");
			}
		}

		var mkind = "<?php ECHO $mkind;?>";
		var mdate = "<?php ECHO $mdate;?>";

		if(mkind == "A")
		{
			$("input[name=mdate]").css("display","block");
		}

	</script>

<script>
var vote1 = $("#votyn1").val();
function vote_1(midx, mlevel, obj, tindex, seq, event, gubun) {

	// midex  : hloan_amdin_member 테이블의 midx
	// mlevel : hloan_amdin_member 테이블의 mlevel
	// obj    : 1 감액 , 2 부결 , 3 가결 , 4 반려 , 8 승인-감액 , 9 승인
	// tindex : 나열순서 - 1 김단비 , 2 이동심 , 3 채영민 , 4 조윤주
	// seq    : 상품 품번
	// event  : javascript event

/*
	if(!event) {
		event = window.event;
	}

	if(event.stopPropagation) {
		event.preventDefault();
		event.stopPropagation();
	} else {
		event.cancelBubble = true;
	}
*/

//	$('#votyn1 option[value="'+obj+'"]').prop('selected', true);


	if (!midx) {
		alert("권한이 없습니다.\n\n관리자에에 문의하여 주세요.");
		$('#votyn1 option[value="'+vote1+'"]').prop('selected', true);
		return;
	}

	if (gubun=="심의번호") {
		var yn = confirm("준법감시인 심사번호를 입력하시겠습니까?");
	} else {
		var yn = confirm("심사단계를 "+ $("#votyn1 option:checked").text() +" 상태로 변경하시겠습니까");
	}

	if (!yn) {
		$('#votyn1 option[value="'+vote1+'"]').prop('selected', true);
		return;
	}

	var str = "&midx="+midx+"&mlevel="+mlevel+"&obj="+obj+"&SE="+seq+"&tindex="+tindex+"&gubun="+gubun;

	$.ajax({
		type : 'POST',
		url : 'request.proc.voit.php',
		data : str,
		dataType: 'json',
		success : function(data){

			console.log(data);

			if(data.retcode == "OK"){
				alert("변경 완료!!");
				self.location.reload();
			} else {
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

function check_admin_member_vote2(midx,mlevel,obj,tindex,seq,event) {


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

		var str = "&midx="+midx+"&mlevel="+mlevel+"&obj="+obj+"&SE="+seq+"&tindex="+tindex;

		$.ajax({
			type : 'POST',
			url : voitPorcessUrl,
			data : str,
			dataType: 'json',
			success : function(data){

				if(data.retcode == "OK"){
					$("#recyn").html(data.retyn);

					if(mlevel == "2" && obj)
					{
						for(var i=0;i<$("select[name='votyn[]']").length;i++)
						{
							if(i != tindex)
							{
								$("select[name='votyn[]']").eq(i).attr("disabled",true);
							}
						}
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

function go_bott(idx) {

	$('[type=text], textarea').each(function(){ this.defaultValue = this.value; });
	$('[type=checkbox], [type=radio]').each(function(){ this.defaultChecked = this.checked; });
	$('select option').each(function(){ this.defaultSelected = this.selected; });

	var cl = $("#pre_loan > tr").eq(idx);
	cl.find('td').eq(0).text("");

	cl.find('td').eq(0).append("<a onclick='go_top($(this).parent().parent().index());' style='cursor:pointer;'>▲</a>");

	cl.find('td').eq(1).html(cl.find('td').eq(1).html().replace("P_","R_"));
	cl.find('td').eq(2).html(cl.find('td').eq(2).html().replace("P_","R_"));
	cl.find('td').eq(3).html(cl.find('td').eq(3).html().replace("P_","R_"));
	cl.find('td').eq(4).html(cl.find('td').eq(4).html().replace("P_","R_"));
	cl.find('td').eq(5).html(cl.find('td').eq(5).html().replace("P_","R_")); cl.find('td').eq(5).html(cl.find('td').eq(5).html().replace("p_","r_"));
	cl.find('td').eq(6).html(cl.find('td').eq(6).html().replace("P_","R_"));
	cl.find('td').eq(7).html(cl.find('td').eq(7).html().replace("P_","R_"));

	$("#rep_loan").append(cl);

}

function go_top(idx) {

	$('[type=text], textarea').each(function(){ this.defaultValue = this.value; });
	$('[type=checkbox], [type=radio]').each(function(){ this.defaultChecked = this.checked; });
	$('select option').each(function(){ this.defaultSelected = this.selected; });

	var cl = $("#rep_loan > tr").eq(idx);
	cl.find('td').eq(0).text("");

	cl.find('td').eq(0).append('<a onclick="go_bott($(this).parent().parent().index());" style="cursor:pointer;">▼</a>');

	cl.find('td').eq(1).html(cl.find('td').eq(1).html().replace("R_","P_"));
	cl.find('td').eq(2).html(cl.find('td').eq(2).html().replace("R_","P_"));
	cl.find('td').eq(3).html(cl.find('td').eq(3).html().replace("R_","P_"));
	cl.find('td').eq(4).html(cl.find('td').eq(4).html().replace("R_","P_"));
	cl.find('td').eq(5).html(cl.find('td').eq(5).html().replace("R_","P_")); cl.find('td').eq(5).html(cl.find('td').eq(5).html().replace("r_","p_"));
	cl.find('td').eq(6).html(cl.find('td').eq(6).html().replace("R_","P_"));
	cl.find('td').eq(7).html(cl.find('td').eq(7).html().replace("R_","P_"));

	$("#pre_loan").append(cl);

}

function go_del(pre_rep, idx) {
	var cl = $("#"+pre_rep+" > tr").eq(idx);
	cl.remove();
}

</script>

<script>
function fn_number_comaX2(obj) {
	console.log(obj);
	var old_val = obj.value;
	var new_val = old_val.replace(/,/gi,"");

	if(!OnlyNumX(new_val))
	{
		alert("숫자만 입력이 가능합니다.");
		return false;
	}

	var new_val = numberWithCommasX(new_val);

	obj.value = new_val;
}
function fn_number_comaX(target, obj, idx)
{
		obj = obj.replace(/,/gi,"");

		if(!OnlyNumX(obj))
		{
			alert("숫자만 입력이 가능합니다");
			//$("input[name='"+target+"']").val("");
			return false;
		}
		var retval = numberWithCommasX(obj);
		//console.log(target+" "+obj+" "+idx+" "+retval);
		$("input[name='"+target+"']").eq(idx).val(retval);
}
// 숫자만 입력 기입
function OnlyNumX(word)
{
	reOnlyNum = new RegExp("[0-9]", "g");
	var returnValue = true;
	for(i=0;i<word.length;i++)  {
		 if(!(word.substr(i,1).match(reOnlyNum))) {
			returnValue=false;
		}
	}
	return returnValue;
}

function numberWithCommasX(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var intv1;

$('#ddmoney').focusout(function() {

	var ddmoney = parseInt($("#ddmoney").val().replace(/,/gi,"")) ; // 대출 신청금액

	if (ddmoney) {
		var chtop = 0;
		chtop = ddmoney * 1.2;
		//chtop = Math.floor(chtop);
	} else if (ddmoney==0) {
		chtop = 0;
	} else {
		chtop = 0;
	}

	$('#bsmoney').val(chtop);



	//intv1 = setInterval(blink_text, 1000);

	var cnt=0;
	var intv1 = setInterval(function() {
								blink_txt("bsmoney");
								cnt ++; if (cnt === 5) clearInterval(intv1);
							}, 1000);

	//setInterval(blinkText, 1000);

});

//var cnt = 0 ;

function blink_txt(tgt) {
	$("#"+tgt).fadeOut(500);
	$("#"+tgt).fadeIn(500);
}

function blink_text() {
    $('#bsmoney').fadeOut(500);
    $('#bsmoney').fadeIn(500);

	cnt = cnt + 1 ;
console.log(cnt);
	if (cnt>4) clearInterval(intv1);
}

function blinkText(){
$('#bsmoney').animate({
opacity : 0
},1000);
$('#bsmoney').animate({
opacity : 1
},1000);
}
</script>

<script>
function cal_hellofee() {
	var ddmoney = parseInt($("#ddmoney").val().replace(/,/gi,"")) ; // 대출 신청금액
	var fees = parseFloat($("#fees").val()) / 100;
	var hm_fees = parseFloat($("#hm_fees").val()) / 100;

	if (isNaN(ddmoney)) ddmoney = 0;
	if (isNaN(fees)) fees = 0;
	if (isNaN(hm_fees)) hm_fees = 0;

	var hellofee = Math.floor(ddmoney * (fees + hm_fees));
	$("#hellofee").val(number_format(hellofee));
	//$("#hellofee").select();

	var cnt=0;
	var intv3 = setInterval(function() {
								blink_txt("hellofee");
								cnt ++; if (cnt === 5) clearInterval(intv3);
							}, 1000);
}

function fn_calc_eja() {

	var hellobase = 0; // 금리
	var loan_for = $(':radio[name="loan_for"]:checked').val();  // 거래 목적
	var add_hellobase = $(':radio[name="add_hellobase"]:checked').val(); // 가산금리
	    add_hellobase = parseFloat(add_hellobase);

	if  (loan_for=="매매") {
		hellobase = 9.5;
	} else {

		var addr = $("#laddr").val();
		var ltv = parseFloat($("#ltvmoney").val());

		if (addr.match(/서울/) || addr.match(/경기/) || addr.match(/인천/)) {

			if (ltv<=60) {
				hellobase = 7.5
			} else if ( ltv>60 && ltv<=70) {
				hellobase = 8;
			} else if (ltv>70 && ltv<=80) {
				hellobase = 8.5;
			} else {
				alert("LTV 80% 초과 취급불가");
				return;
			}

		} else if (addr.match(/대전/) || addr.match(/대구/) || addr.match(/광주/) || addr.match(/부산/) || addr.match(/울산/) ) {

			if (ltv<=60) {
				hellobase = 8.5
			} else if ( ltv>60 && ltv<=70) {
				hellobase = 9;
			} else if (ltv>70 && ltv<=80) {
				hellobase = 9.5;
			} else {
				alert("LTV 80% 초과 취급불가");
				return;
			}

		} else {
			alert("서울,경기,인천\n대전,대구,광주,부산,울산\n외 지역 취급불가");
			return;
		}
	}

	hellobase = hellobase + add_hellobase;

	$("#hellobase").val(hellobase);
/*
	var cnt=0;
	var intv2 = setInterval(function() {
								blink_txt("hellobase");
								console.log(cnt);
								cnt ++; if (cnt === 5) clearInterval(intv2);
							}, 1000);
*/
	var cnt=0;
	var intv2 = setInterval(function() {
								blink_txt("hellobase");
								cnt ++; if (cnt === 5) clearInterval(intv2);
							}, 1000);

}

function cal_ltv2() {
	if (!$("#ddmoney").val()) {
		alert("대출신청금액을 입력해 주세요.");
		return;
	}

	var new_money = parseInt($("#ddmoney").val().replace(/,/gi,""));
	var old_money = 0; // 기대출금액 합계
	var old_money2= 0; // 채권최고액 합계
	var k_money = 0;   // 기준가 (일반가 또는 하한가)
	var house_deposit = parseInt($("#house_deposit").val().replace(/,/gi,""));  // 소액임차보증금

	// 선순위 기대출금액 합계를 구한다.
	$('input[name^="P_loan_amount"]').each( function() {
		old_money = old_money + parseInt(this.value.replace(/,/gi,""));
	});
	/*
	// 대환 기대출금액 합계를 구한다.
	$('input[name^="R_loan_amount"]').each( function() {
		old_money = old_money + parseInt(this.value.replace(/,/gi,""));
	});
	*/


	// 선순위 채권 최고액 합계를 구한다.
	$('input[name^="P_limit_amount"]').each( function() {
		old_money2 = old_money2 + parseInt(this.value.replace(/,/gi,""));
	});
	/*
	// 대환 채권최고액액 합계를 구한다.
	$('input[name^="R_limit_amount"]').each( function() {
		old_money2 = old_money2 + parseInt(this.value.replace(/,/gi,""));
	});
	*/

	var temp = $(':radio[name="ltvkind"]:checked').val();
	if (temp=="1") {  // 일반가
		k_money = parseInt($("#kbprice").val().replace(/,/gi,""));
	} else {  // 하한가
		k_money = parseInt($("#kbllimit").val().replace(/,/gi,""));
	}

	var ltv = (new_money + old_money) / k_money;
	ltv = Math.round(ltv * 10000) / 100;
	$("#ltvmoney").val(ltv);

	var ltv2 = (new_money + old_money2 + house_deposit) / k_money;  // 채권최고액 ltv
	ltv2 = Math.round(ltv2 * 10000) / 100;
	$("#ltvmoney2").val(ltv2);

}

function cal_ltv() {

	if (!$("#ddmoney").val()) {
		alert("대출신청금액을 입력해 주세요.");
		return;
	}
	if (!$('input:radio[name=ltvkind]').is(":checked")) {
		alert("기준가를 선택해 주세요.");
		return;
	}

	var new_money = parseInt($("#ddmoney").val().replace(/,/gi,""));
	var old_money = 0; // 기대출금액 합계
	var old_money2= 0; // 채권최고액 합계
	var k_money = 0;   // 기준가 (일반가 또는 하한가)
	var house_deposit = parseInt($("#house_deposit").val().replace(/,/gi,""));  // 소액임차보증금
	if (!house_deposit) house_deposit=0;

	var temp = $(':radio[name="ltvkind"]:checked').val();
	if (temp=="1") {  // 일반가
		k_money = parseInt($("#kbprice").val().replace(/,/gi,""));
	} else {  // 하한가
		k_money = parseInt($("#kbllimit").val().replace(/,/gi,""));
	}

	if (!k_money) {
		alert("기준가가 없습니다.");
		return;
	}

	// 기대출금액 합계를 구한다.
	$('input[name^="examount"]').each( function() {
		old_money = old_money + parseInt(this.value.replace(/,/gi,""));
	});
	if (isNaN(old_money)) old_money = 0;

	// 채권최고액 합계를 구한다.
	$('input[name^="maxbond"]').each( function() {
		old_money2 = old_money2 + parseInt(this.value.replace(/,/gi,""));
	});
	if (isNaN(old_money2)) old_money2 = 0;

	//var ltv = ( (new_money + old_money) / k_money ).toFixed(4) * 100;  // 기대출금액 ltv
	var ltv = (new_money + old_money) / k_money;
	//ltv = Math.floor(ltv * 10000) / 100;
	ltv = Math.round(ltv * 10000) / 100;
	$("#ltvmoney").val(ltv);

	//var ltv2 = ( (new_money + old_money2 + house_deposit) / k_money ).toFixed(4) * 100;  // 채권최고액 ltv
	var ltv2 = (new_money + old_money2 + house_deposit) / k_money;  // 채권최고액 ltv
	//ltv2 = Math.floor(ltv2 * 10000) / 100;
	ltv2 = Math.round(ltv2 * 10000) / 100;
	$("#ltvmoney2").val(ltv2);


}

</script>