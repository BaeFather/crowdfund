<style>
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }
input.radioarea {float:left;margin-top:7px;margin-left:10px;border:1px solid #ff0000;}
label {float:left;display:block;padding:0px 5px;}

.stable {width:1200px;border-collapse:collapse;}
.stable th.th1 {padding:0 20px}

.input1 {height:30px;padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px;border:1px solid #CCC;}
.search01 {height:30px;padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px;border:1px solid #CCC;}
.w120 {width:120px; text-align:center;}
.w200 {width:200px; text-align:left;}

.btn1 {height:40px;padding:0px 10px;font-size:16px;line-height:40px;border-radius:3px;background-color:#f0ad4e;border:1px solid #eea236;color:#FFF;text-align:center;}

.btn2 {height:40px;padding:0px 10px;font-size:16px;line-height:40px;border-radius:3px;background-color:#eee;border:1px solid #ccc;color:#333;text-align:center;}
</style>
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;width:100%;">
		<form id="frmSearch" method="get" class="form-horizontal" autocomplete="off">
		<table class="stable">
		<tr>
			<th class="th1">통합검색</th>
			<td>
				<?php ECHO fn_general_select($S2,"",fn_Search_S2_new(),":통합검색:","S2","class='input1 w120'","");?>

				<input type="text" name="STXT" value="<?php ECHO $STXT;?>"  placeholder="검색어 입력" class="input1 w200" />
			</td>
			<th class="th1">등록일시</th>
			<td>
				<input type="text" name="Sdate" class="input1 w120 datepicker" value="<?php ECHO $Sdate;?>"> ~ <input type="text" name="Edate" class="input1 w120 datepicker" value="<?php ECHO $Edate;?>">
			</td>
			<td rowspan="2" style="text-align:center;line-height:50px;">
				<button type="submit" class="btn1 w120" onClick="form_change();">검색</button> &nbsp;&nbsp;<button type="button" class="btn2 w120" Onclick="window.location='/adm/helloloan_renew/';">초기화</button>
			</td>
		</tr>
		<tr>
			<th class="th1">상세</th>
			<td colspan="3">
				<?php ECHO fn_general_select($S1,"",fn_loankind(),"::물건순위::","S1","class='search01'","");?>

				<?php ECHO fn_general_select($S5,"",fn_loan_arecyn(),"::심사 진행상황::","S5","class='search01'","");?>

				<?php ECHO fn_general_select($S3,"",fn_hellloan_search_kind_renew(),"::헬로펀딩 진행상황::","S3","class='search01'","");?>

				<?php ECHO fn_general_select($S4,"",fn_hellloan_member($connect_for,"2"),"::중개법인::","S4","class='search01'",""); ?>
			</td>

		</tr>
		</table>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px;margin-top:20px;">
		<li style="width:100%;float:right;">

			<button type="button" style="float:right;margin:0 7px;" onClick="location.href='listexcel.php?S1=<?=$S1?>&S2=<?=$S2?>&S3=<?=$S3?>&S4=<?=$S4?>&SC=<?=$SC?>&STXT=<?=$STXT?>'" class="btn btn-sm btn-success">엑셀다운로드</button>

			<button type="button" style="float:right;background:blue;font-size:13px;padding:3px 10px;border:0px;line-height:25px;color:#FFF;border-radius:4px;margin:0 7px;" onClick="popup_window('smslist.php?S=1','smslist','scrollbars=yes,width=900,height=700,top=10,left=20');">대량 문자발송</button>

			<button type="button" style="float:right;background:orange;font-size:13px;padding:3px 10px;border:0px;line-height:25px;color:#FFF;border-radius:4px;margin:0 7px;" onClick="popup_window('smslist.php?S=2','smslist','scrollbars=yes,width=900,height=700,top=10,left=20');">대량 문자예약</button>

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
			<th colspan="7" style="text-align:center;">기본정보</th>
			<th rowspan="2" style="text-align:center;">1차 심사</th>
			<th rowspan="2" style="text-align:center;">헬로 심사</th>
			<th rowspan="2" style="text-align:center;">심사자</th>
			<th colspan="3" style="text-align:center;">원차주</th>
			<th colspan="4" style="text-align:center;">대출정보</th>
			<!--<th rowspan="2" style="text-align:center;">기타</th>//-->
		</tr>
		<tr>
			<th style="text-align:center;">접수번호</th>
			<th style="text-align:center;">상품호번</th>
			<th style="text-align:center;">중개법인</th>
			<th style="text-align:center;">주소1</th>
			<th style="text-align:center;">주소2</th>
			<th style="text-align:center;">상세주소</th>
			<th style="text-align:center;">물건순위</th>
			<th style="text-align:center;">이름</th>
			<th style="text-align:center;">등록일</th>
			<th style="text-align:center;">연락처</th>
			<th style="text-align:center;">대출금액</th>
			<th style="text-align:center;">금리</th>
			<th style="text-align:center;">대출기간</th>
			<th style="text-align:center;">수수료율</th>
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

		$bunho=($rowList[1])-(($page-1) * $num_per_page); //리스트의 넘버수
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&idx=".$hcseq."&page=".$page.$qstr;
			$dongArr	=	EXPLODE(",",$dg);

			IF(SUBSTR($len_date,0,10) == "0000-00-00") { $len_date = ""; } ELSE { $len_date = SUBSTR($len_date,0,10); }
?>
		<tr OnClick="window.location='<?php ECHO $RowLink;?>'" style="cursor:pointer;">
			<td align="center"><?=$bunho?></td>
			<td align="center"><?=$hnum?></td>
			<td align="center"></td>
			<td align="center"><?=$cname?></td>
			<td align="center"><?php ECHO $si?></td>
			<td align="center"><?php ECHO $gu?></td>
			<td align="left"><?=$dongArr[1]?> <?php ECHO fn_loan_name_replace($aptname,",")?> <?php ECHO $jibun;?> <?php ECHO $dong?>동 <?php ECHO $floor?>층 <?php ECHO $ho?>호 (<?php ECHO fn_loan_name_replace($aptarea,",")?> ㎡)</td>

			<td align="center"><?=fn_general_txt($loankind,fn_loankind())?></td>
			<td align="center"><?=fn_general_txt($arecyn,fn_loan_arecyn())?></td>
			<td align="center"><?=fn_general_txt($recyn,fn_hellloan_search_kind_renew())?></td>
			<td align="center"><?=fn_general_txt($mb_no,hloan_admin_member($connect_for))?></td>
			<td align="center"><?=$lenmember?></td>
			<td align="center"><?=$len_date?></td>
			<td align="center"><?=$lenphone?></td>

			<td align="center"><?=f_number($okddmoney)?></td>
			<td align="center"><?=$okInterest?></td>
			<td align="center"><?=$loan_sdate?>~<?=$loan_edate?></td>
			<td align="center"><?=$okfees?></td>

			<!--<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='excel.php?idx=<?=$hcseq?>'" class="btn btn-sm btn-success">다운로드</button>
			</td>//-->
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
