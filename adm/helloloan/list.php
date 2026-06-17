<style>
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }
input.radioarea {float:left;margin-top:7px;margin-left:10px;border:1px solid #ff0000;}
label {float:left;display:block;padding:0px 5px;}
</style>

	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;width:100%;">
		<form id="frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<?php ECHO fn_general_select($S1,"",fn_Search_S1($gstrMseq),":협력사:","S1","class='form-control input-sm' style='width:150px'","");?>
			</li>
			<!--
			<li>
				<?php ECHO fn_general_select($S3,"",fn_product_manager($connect),":물건담당자:","S3","class='form-control input-sm' style='width:150px'","");?>
			</li>
			//-->
			<li>
				<?php ECHO fn_general_select($S4,"",fn_product_hello(),":헬로펀딩상품:","S4","class='form-control input-sm' style='width:150px'","");?>
			</li>
			<li>
				<?php ECHO fn_general_select($S2,"",fn_Search_S2(),":통합검색:","S2","class='form-control input-sm' style='width:150px'","");?>
			</li>
			<li>
				<input type="text" name="STXT" value="<?php ECHO $STXT;?>"  placeholder="검색어 입력" class="form-control input-sm" style="width:250px;" />
			</li>
			<li>
				<button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button>
				&nbsp;<button type="button" class="btn btn-sm btn-search" Onclick="window.location='/adm/helloloan/';">초기화</button>
			</li>
		</ul>
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px;">
		<li style="width:100%;">
			<?php ECHO fn_general_select($SC,"radio",fn_hellloan_search_kind(),"","SC","class='radioarea'","");?>
		</li>
		<li style="width:100%;float:right;text-align:right;">
			<!--button type="button" onclick="go_new();" style="margin-right:5px;" class="btn btn-sm btn-warning">등록하기</button-->
			<button type="button" onclick="go_new3();" style="margin-right:5px;" class="btn btn-sm btn-success">신규등록</button>
			<button type="button" style="margin-right:5px;" onClick="location.href='listexcel.php?S1=<?=$S1?>&S2=<?=$S2?>&S3=<?=$S3?>&S4=<?=$S4?>&SC=<?=$SC?>&STXT=<?=$STXT?>'" class="btn btn-sm btn-success">엑셀다운로드</button>

			<? if ($member["mb_id"]=="admin_romrom" OR $member["mb_id"]=="admin_sundol4") { ?>
			<button type="button" onclick="go_log();" style="margin-right:5px;" class="btn btn-sm btn-search">HYPHEN log</button>
			<? } ?>
		</li>
		</ul>
		</form>
	</div>
	<!-- 검색영역 E N D -->


	<!-- 리스트 START -->

	<div style="float:right; display:inline-block; font-size:12px;line-height:20px;width:100%;">
		<span style="float:left">▣ 등록 : <?=number_format($total_count);?>건</span>
		<span style="float:right"><?=$page?> / <?=$total_page?> Page<span>
	</div>
	<table class="table table-striped table-bordered table-hover" style="padding-top:0; font-size:12px;">
		<caption style="padding:0"><?=$g5['title']?> 목록</caption>
		<thead>
		<tr>
			<th rowspan="2" style="text-align:center;width:60px">NO.</th>
			<th rowspan="2" style="text-align:center;width:60px">신청번호</th>
			<th rowspan="2" style="text-align:center;">등록일자</th>
			<th rowspan="2" style="text-align:center;">협력사</th>
			<th colspan="3" style="text-align:center;">담보물 주소</th>
			<th colspan="7" style="text-align:center;">대출정보</th>
			<th rowspan="2" style="text-align:center;">상태</th>

			<!--th colspan="4" style="text-align:center;">심사</th-->
			<!--th colspan="2" style="text-align:center;">헬로펀딩</th-->
			<th colspan="3" style="text-align:center;">심사 구분</th>


			<th rowspan="2" style="text-align:center;">비고</th>
		</tr>
		<tr>
			<th style="text-align:center;">주소1</th>
			<th style="text-align:center;">주소2</th>
			<th style="text-align:center;">상세주소</th>
			<th style="text-align:center;">원차주명</th>
			<th style="text-align:center;">생년월일</th>
			<th style="text-align:center;">대출금</th>
			<th style="text-align:center;">대출기간</th>
			<th style="text-align:center;">금리</th>
			<th style="text-align:center;">기표희망일</th>
			<th style="text-align:center;">LTV</th>
			<!--th style="text-align:center;">가결</th>
			<th style="text-align:center;">부결</th>
			<th style="text-align:center;">감액</th>
			<th style="text-align:center;color:#0000ff;">팀장</th-->
			<!--th style="text-align:center;">호번</th>
			<th style="text-align:center;">상품</th-->
			<th style="text-align:center;">심사</th>
			<th style="text-align:center;">승인</th>
			<th style="text-align:center;">준법심사</th>
		</tr>
		</thead>
		<tbody>
<?php
	IF($rowList[1] > 0)
	{
		$intDdmoneySum = 0;
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$intDdmoneySum += $ddmoney;
		}
?>
		<tr>
			<th colspan="8">합 계</th>
			<th style="text-align:left;letter-spacing:0px;font-size:13px;padding-left:15px;"><?php ECHO f_number($intDdmoneySum);?></th>
			<th colspan="10"></th>
		</tr>
<?php
		$bunho=($rowList[1])-(($page-1) * $num_per_page); //리스트의 넘버수
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&SE=".$hcseq."&page=".$page."&S1=".$S1."&S2=".$S2."&STXT=".$STXT."&SC=".$SC;

			$strAddr = fn_check_addr($laddr);


			$SIM4_sql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$hcseq' AND gubun='심사' ORDER BY reg_date DESC LIMIT 1";
			$SIM4_row = sql_fetch($SIM4_sql);
			if ($SIM4_row["votyn"]=="3") $SIM4 = "O";
			else if ($SIM4_row["votyn"]=="1") $SIM4 = "X";
			else $SIM4 = "";

			$SIM3_sql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$hcseq' AND gubun='승인' ORDER BY reg_date DESC LIMIT 1";
			$SIM3_row = sql_fetch($SIM3_sql);
			if ($SIM3_row["votyn"]=="3") $SIM3 = "O";
			else if ($SIM3_row["votyn"]=="1") $SIM3 = "X";
			else $SIM3 = "";

			$SIM2_sql = "SELECT * FROM hloan_admin_member_vote WHERE hcseq='$hcseq' AND gubun='심의' ORDER BY reg_date DESC LIMIT 1";
			$SIM2_row = sql_fetch($SIM2_sql);
			if ($SIM2_row["votyn"]=="3") $SIM2 = "O";
			else if ($SIM2_row["votyn"]=="1") $SIM2 = "X";
			else $SIM2 = "";

			$reg_num = "";
			$birth_sex = "";
			if ($regist_number) {
				$reg_num = masterDecrypt($regist_number, false);
				$birth_sex = substr($reg_num,0,6)."-".substr($reg_num,6,1);
			}
?>
		<tr>
			<td align="center"><?=$bunho?></td>
			<td align="center"><?=$hcseq?></td>
			<td align="center" nowrap><?=SUBSTR($reg_date,2,14)?></td>
			<td align="center"><?=$cname?></td>
			<td align="center"><?=$strAddr[0]?></td>
			<td align="center"><?=$strAddr[1]?></td>
			<td align="left"><?=$strAddr[2]?></td>

			<td align="center"><?=$pname?><?//=$pphone1?></td>
			<td align="center"><?=$birth_sex?></td>
			<td align="center"><?=f_number($ddmoney)?></td>
			<td align="center"><?=fn_mdate_pro($mkind,$mdate)?></td>
			<td align="center"><?=$hellobase?></td>
			<td align="center"><?=$vdate?></td>
			<td align="center"><?=fn_check_ltv($ltvmoney)?></td>

			<td align="center" style="width:110px;"><?=fn_general_txt($recyn,fn_hellloan_search_kind())?></td>
			<? /*
			<td align="center"><?php ECHO $CNT1;?></td>
			<td align="center"><?php ECHO $CNT2;?></td>
			<td align="center"><?php ECHO $CNT3;?></td>
			<td align="center"><?php ECHO fn_general_txt($votyn,hloan_voteyn(2));?></td>
			*/ ?>
			<? /*
			<td align="center"><?=$honumber?></td>
			<td align="center"><?=fn_general_txt($productyn,fn_product_hello())?></td>
			*/ ?>
			<td align="center"><?php ECHO $SIM4;?></td>
			<td align="center"><?php ECHO $SIM3;?></td>
			<td align="center"><?php ECHO $SIM2;?></td>

			<td align="center" nowrap>
				<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&idx=<?=$hcseq?>&page=<?=$page?>&RD=2'" class="btn btn-sm <?=($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
				<!--button type="button" style="margin-top:2px;" onClick="location.href='excel.php?idx=<?=$hcseq?>'" class="btn btn-sm btn-success">다운로드</button-->
				<button type="button" onclick="go_del('<?=$hcseq?>', event);" style="margin-right:15px;" class="btn btn-sm btn-warning">삭제</button>
			</td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="20" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>


	<form name="dregfm" id="dregfm">
		<input type="hidden" name="kind" value="del" />
		<input type="hidden" name="SE" value="" />
		<input type="hidden" name="S1" value="" />
		<input type="hidden" name="S2" value="" />
		<input type="hidden" name="S3" value="" />
		<input type="hidden" name="S4" value="" />
		<input type="hidden" name="SC" value="" />
		<input type="hidden" name="STXT" value="" />
		<input type="hidden" name="page" value="" />
	</form>

<script>
function go_del(hcseq , event) {
	
	var yn = confirm("삭제하시겠습니까?");
	if (!yn) return;

	var f = document.dregfm;
	f.SE.value = hcseq;

	check_del_form_list('dregfm',event);
	//alert("작업중");
}

function check_del_form_list(fmname, event)
{
		if(event.stopPropagation)
		{
			event.preventDefault();
			event.stopPropagation();
		} else {
			event.cancelBubble = true;
		}


		if(confirm('삭제한 데이터는 복구되지 않습니다.\n정말 삭제하시겠습니까?'))
		{
			var frm = $('#'+fmname);
			var str = frm.serialize();
			var loanProcessUrl = "/adm/helloloan/process.php";

			$.ajax({
				type : 'POST',
				url : loanProcessUrl,
				data : str,
				dataType: 'json',
				success : function(data){
console.log(data);
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
}
</script>


<script>
function go_new2() {
	self.location.href="hloan_detail2_test.php";
}
function go_new3() {
	self.location.href="hloan_detail2.php";
}
function go_log() {
	window.open("hyphen_log.php", "hyphen_log", "left=10,top=10,width=1600,height=900");
}
</script>