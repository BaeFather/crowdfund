<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
label {float:left;display:block;padding:0px 5px;}
.fred {color:#ff0000;}
a.ab, a.ab:link, a.ab:visited, a.ab:active, a.ab:link {color:#333;text-decoration:none}
</style>

	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<form id="frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline" style="width:1000px;padding-left:0;margin-bottom:5px;">
			<li>
				<?php ECHO fn_general_select($S2,"",$ClassHelloSetting->fn_hloan_member(),":조견업체:","S2","class='form-control input-sm' style='width:150px'","");?>
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
			<th scope="col" style="text-align:center;width:60px">NO.</th>
			<th scope="col" style="text-align:center;">조견업체</th>
			<th scope="col" style="text-align:center;">취급지역</th>
			<th scope="col" style="text-align:center;">제목</th>
			<th scope="col" style="text-align:center;">적용일자</th>
			<th scope="col" style="text-align:center;">기간(개월)</th>
			<th scope="col" style="text-align:center;">적용여부</th>
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

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&SE=".$hcsseq."&page=".$page."&S2=".$S2."&STXT=".$STXT;
?>
		<tr>
			<td align="center"><?=$bunho?></td>
			<td align="center"><a href="<?php ECHO $RowLink;?>" class="ab"><?=fn_general_txt($hmseq, $ClassHelloSetting->fn_hloan_member())?></a></td>
			<td align="center"><a href="<?php ECHO $RowLink;?>" class="ab"><?=$addr_si?></a></td>
			<td align="center"><a href="<?php ECHO $RowLink;?>" class="ab"><?=$title?></a></td>
			<td align="center"><a href="<?php ECHO $RowLink;?>" class="ab"><?=$rec_date?></a></td>
			<td align="center"><a href="<?php ECHO $RowLink;?>" class="ab"><?=$period?></a></td>
			<td align="center"><a href="<?php ECHO $RowLink;?>" class="ab"><?=fn_general_txt($recyn, $ClassHelloSetting->fn_setting_recyn())?></a></td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="7" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>