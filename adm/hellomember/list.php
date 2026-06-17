<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
label {float:left;display:block;padding:0px 5px;}
.fred {color:#ff0000;}
</style>

	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<form id="frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline" style="width:1000px;padding-left:0;margin-bottom:5px;">
			<li>
				<?php ECHO fn_general_select($S2,"",fn_Search_hmember(),":통합검색:","S2","class='form-control input-sm' style='width:150px'","");?>
			</li>
			<li>
				<input type="text" name="STXT" value="<?php ECHO $STXT;?>"  placeholder="검색어 입력" class="form-control input-sm" style="width:250px;" />
			</li>
			<li>
				<button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button>
			</li>
		</ul>
		</form>
	</div>
	<!-- 검색영역 E N D -->


	<!-- 리스트 START -->

	<div style="width:100%;">
	<span style="float:right">
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"]?>?RD=3';" class="btn btn-default">등록하기</button>
	</span>
	</div>
	<div style="float:right; display:inline-block; font-size:12px;line-height:20px;width:100%;">
		<span style="float:left">▣ 등록 : <?=number_format($total_count);?>건</span>
		<span style="float:right"><?=$page?> / <?=$total_page?> Page<span>
	</div>
	<table class="table table-striped table-bordered table-hover" style="padding-top:0; font-size:12px;">
		<caption style="padding:0"><?=$g5['title']?> 목록</caption>
		<thead>
		<tr>
			<th scope="col" rowspan="2" style="text-align:center;width:60px">NO.</th>
			<th scope="col" rowspan="2" style="text-align:center;">구분</th>
			<th scope="col" rowspan="2" style="text-align:center;">협력사 명</th>
			<?php FOR($i=0;$i<COUNT($strHelloKind);$i++) { ?>
			<th scope="col" colspan="2" style="text-align:center;"><?php ECHO $strHelloKind[$i][1];?></th>
			<?php } ?>
			<th scope="col" rowspan="2" style="text-align:center;">비고</th>
		</tr>
		<tr>
			<?php FOR($i=0;$i<COUNT($strHelloKind);$i++) { ?>
			<th>건수</th>
			<th>금액</th>
			<?php } ?>
		</th>
		</thead>
		<tbody>
<?php

	IF($rowList[1] > 0)
	{
		$bunho=($rowList[1])-(($page-1) * $num_per_page); //리스트의 넘버수
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			unset($RowLink);
			unset($hmseq);
			unset($recyn);
			UNSET($intTcnt);
			UNSET($intTsum);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&SE=".$hcseq."&page=".$page."&S1=".$S1."&S2=".$S2."&STXT=".$STXT."&SC=".$SC;

			FOR($j=0;$j<COUNT($strHelloKind);$j++)
			{
				$intTcnt += $intCompanyCnt[0][$hmseq][$strHelloKind[$j][0]];
				$intTsum += $intCompanyCnt[1][$hmseq][$strHelloKind[$j][0]];
			}
?>
		<tr>
			<td align="center"><?=$bunho?></td>
			<td align="center"><?=fn_general_txt($level, fn_hmember_level())?></td>
			<td align="center"><?=$cname?></td>
			<?php FOR($j=0;$j<COUNT($strHelloKind);$j++) { ?>
			<?php IF($strHelloKind[$j][0] == "T") { ?>
			<td align="center"><?php ECHO NUMBER_FORMAT($intTcnt);?></td>
			<td align="center"><?php ECHO NUMBER_FORMAT($intTsum);?></td>
			<?php } ELSE { ?>
			<td align="center"><?php ECHO NUMBER_FORMAT($intCompanyCnt[0][$hmseq][$strHelloKind[$j][0]]);?></td>
			<td align="center"><?php ECHO NUMBER_FORMAT($intCompanyCnt[1][$hmseq][$strHelloKind[$j][0]]);?></td>
			<?php } ?>
			<?php } ?>
			<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&idx=<?=$hmseq?>&page=<?=$page?>&RD=2'" class="btn btn-sm <?=($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
			</td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="14" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>