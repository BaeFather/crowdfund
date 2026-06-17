<style>
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }
input.radioarea {float:left;margin-top:7px;margin-left:10px;border:1px solid #ff0000;}
label {float:left;display:block;padding:0px 5px;}
</style>

	<!-- 검색영역 START -->
	<div style="overflow:hidden; line-height:28px;margin-bottom:8px;">
		<form id="frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline">
			<li>
				<?php ECHO fn_general_select($S3,"",$gstrHelloBanner->FnRecyn(),":노출구분:","S3","class='form-control input-sm' style='width:150px'","");?>
			</li>
			<li>
				<?php ECHO fn_general_select($S4,"",$gstrHelloBanner->RsSection(),":광고구분:","S4","class='form-control input-sm' style='width:150px'","");?>
			</li>
			<li>
				<input type="text" name="S1" value="<?php ECHO $S1;?>"  placeholder="시작일" class="form-control input-sm datepicker" style="width:100px;" autocomplete="off"/>
			</li>
			<li>
				<input type="text" name="S2" value="<?php ECHO $S2;?>"  placeholder="종료일" class="form-control input-sm datepicker" style="width:100px;" autocomplete="off" />
			</li>
			<li>
				<input type="text" name="STXT" value="<?php ECHO $STXT;?>"  placeholder="이름 연락처 검색어 입력" class="form-control input-sm" style="width:250px;" />
			</li>
			<li>
				<button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button>
				&nbsp;
				<button type="button" class="btn btn-sm btn-default" onClick="window.location='<?php ECHO $_SERVER["PHP_SELF"]?>';">초기화</button>
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
			<th scope="col" style="text-align:center;width:60px">NO.</th>
			<th scope="col" style="text-align:center;">등록일</th>
			<th scope="col" style="text-align:center;">상태</th>
			<th scope="col" style="text-align:center;">광고구분</th>
			<th scope="col" style="text-align:center;">시작일</th>
			<th scope="col" style="text-align:center;">종료일</th>
			<th scope="col" style="text-align:center;">PC 이미지</th>
			<th scope="col" style="text-align:center;">모바일 이미지</th>
			<th scope="col" style="text-align:center;">비고</th>
		</tr>
		</thead>
		<tbody>
<?php
	IF($rowList[1] > 0)
	{
		$bunho=($rowList[1])-(($page-1) * $num_per_page); //리스트의 넘버수
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?RD=2&SE=".$idx."&page=".$page."&STXT=".$STXT."&S1=".$S1."&S2=".$S2;
?>
		<tr>
			<td align="center"><?=$bunho?></td>
			<td align="center"><?=$reg_date?></td>
			<td align="center"><?=fn_general_txt($recyn,$gstrHelloBanner->FnRecyn())?></td>
			<td align="center"><?=fn_general_txt($cfcode,$gstrHelloBanner->RsSection())?></td>
			<td align="center"><?=$sdate?></td>
			<td align="center"><?=$edate?></td>
			<td align="center"><?=fn_rep_img_list("/data/event",$repimg,"listrepimg");?></td>
			<td align="center"><?=fn_rep_img_list("/data/event",$mrepimg,"listrepimg");?></td>
			<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='<?php ECHO $RowLink;?>'" class="btn btn-sm <?=($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
				&nbsp;&nbsp;
			</td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="8" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>

	<div style="float:right">
		<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&page=<?=$page?>&RD=3'" class="btn btn-sm btn-default">등록하기</button>
	</div>
	<!-- 리스트 E N D -->

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>