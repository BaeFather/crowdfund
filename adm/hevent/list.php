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
				<input type="text" name="STXT" value="<?php ECHO $STXT;?>"  placeholder="검색어 입력" class="form-control input-sm" style="width:250px;" />
			</li>
			<li>
				<button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button>
				&nbsp;<button type="button" class="btn btn-sm btn-search" Onclick="window.location='/adm/hevent/';">초기화</button>
			</li>
		</ul>
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px;">
		<li style="width:100%;">
			<div style="float:left;width:50%;text-align:left;">
			<label>PC 노출 :</label> <?php ECHO fn_general_select($SC,"radio",$strEventClass->StrKind(),"","SC","class='radioarea'","");?>

			 <label>|</label> <label>모바일 노출 :</label> <?php ECHO fn_general_select($SCC,"radio",$strEventClass->StrKind(),"","SCC","class='radioarea'","");?>
			</div>
			<div style="float:left;width:50%;text-align:right;">

			</div>
		</li>
		</ul>
		</form>
	</div>
	<!-- 검색영역 E N D -->

	<div style="float:right; display:inline-block; font-size:12px;line-height:20px;width:100%;padding:0px 0px 15px 0px;;">
		<button type="submit" class="btn btn-primary" onClick="window.location='./?RD=3';">이벤트글쓰기</button>
	</div>

	<!-- 리스트 START -->

	<div style="float:right; display:inline-block; font-size:12px;line-height:20px;width:100%;">
		<span style="float:left">▣ 등록 : <?php ECHO number_format($total_count);?>건</span>
		<span style="float:right"><?php ECHO $page?> / <?php ECHO $total_page?> Page<span>
	</div>
	<table class="table table-striped table-bordered table-hover" style="padding-top:0; font-size:12px;">
		<caption style="padding:0"><?php ECHO $g5['title']?> 목록</caption>
		<thead>
		<tr>
			<th style="text-align:center;width:60px">NO.</th>
			<th style="text-align:center;width:100px">메인(PC)</th>
			<th style="text-align:center;width:100px">메인(모바일)</th>
			<th style="text-align:center;width:100px">현재상태</th>
			<th style="text-align:center;width:200px">대표이미지</th>
			<th style="text-align:center;">제목</th>
			<th style="text-align:center;width:100px">시작일</th>
			<th style="text-align:center;width:100px">종료일</th>
			<th style="text-align:center;width:100px">정렬순서</th>
			<th style="text-align:center;width:150px">등록일자</th>
			<th style="text-align:center;width:100px">기타</th>
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
			$RowLink = $qstr."&RD=2&SE=".$idx;

			$strImg = $strEventClass->FnRepimg($ifile,0,"/data/fevent");
?>
		<tr>
			<td align="center"><?php ECHO $bunho?></td>
			<td align="center"><?php ECHO fn_general_txt($mainyn,$strEventClass->StrKind());?></td>
			<td align="center"><?php ECHO fn_general_txt($mainmyn,$strEventClass->StrKind());?></td>
			<td align="center"><?php ECHO $strEventClass->FnNowState($edate)?></td>
			<td align="center"><img src="<?php ECHO $strImg;?>" class="listrepimg" /></td>
			<td align="left"><?php ECHO $title?></td>
			<td align="center"><?php ECHO $sdate?></td>
			<td align="center"><?php ECHO $edate?></td>

			<td align="center"><?php ECHO $sort_id?></td>
			<td align="center"><?php ECHO $reg_date?></td>
			<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='<?php ECHO $RowLink?>'" class="btn btn-sm <?php ECHO ($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
			</td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="11" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->

	<div style="float:right; display:inline-block; font-size:12px;line-height:20px;width:100%;padding:0px 0px 15px 0px;;">
		<button type="submit" class="btn btn-primary" onClick="window.location='./?RD=3';">이벤트글쓰기</button>
	</div>

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>