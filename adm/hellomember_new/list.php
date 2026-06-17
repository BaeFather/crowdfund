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
				<?php ECHO fn_general_select($S2,"",fn_Search_hmember_new(),":통합검색:","S2","class='form-control input-sm' style='width:150px'","");?>
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
		<button type="button" id="list_button" onClick="location.href='<?php ECHO $_SERVER["PHP_SELF"]?>?RD=3&ST=<?php ECHO $ST;?>';" class="btn btn-default">등록하기</button>
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
			<th scope="col" style="text-align:center;width:60px">NO.</th>
			<th scope="col" style="text-align:center;">중개법인</th>
			<th scope="col" style="text-align:center;">소속 영업팀</th>
			<th scope="col" style="text-align:center;">담당자</th>
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
			unset($hmseq);
			unset($recyn);
			UNSET($intTcnt);
			UNSET($intTsum);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&SE=".$hmseq."&page=".$page."&S1=".$S1."&S2=".$S2."&STXT=".$STXT."&SC=".$SC."&ST=".$ST;

			FOR($j=0;$j<COUNT($strHelloKind);$j++)
			{
				$intTcnt += $intCompanyCnt[0][$hmseq][$strHelloKind[$j][0]];
				$intTsum += $intCompanyCnt[1][$hmseq][$strHelloKind[$j][0]];
			}
			IF(!$mname) { $hphone = "";  $hname= "";}
?>
		<tr>
			<td align="center"><?=$bunho?></td>
			<td align="center"><?=$cname?></td>
			<td align="center"><?=$hname?></td>
			<td align="center"><?=$mname?><br /><?php ECHO $hphone;?></td>
			<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&idx=<?=$hmseq?>&page=<?=$page?>&RD=2&ST=<?php ECHO $ST?>'" class="btn btn-sm <?=($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
			</td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="5" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>