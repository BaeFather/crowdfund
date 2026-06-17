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
				<input type="text" name="STXT" value="<?php ECHO $STXT;?>"  placeholder="이름 연락처 검색어 입력" class="form-control input-sm" style="width:250px;" />
			</li>
			<li>
				<button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button>
				&nbsp;
				<button type="button" class="btn btn-sm btn-default" onClick="window.location='<?php ECHO $_SERVER["PHP_SELF"]?>';">초기화</button>
			</li>
			<li style="padding-left:100px">
				<input type="text" name="p_id" id="p_id" value=""  placeholder="상품번호" class="form-control input-sm" style="width:100px;" />
			</li>
			<li>
				<button type="button" class="btn btn-sm btn" onClick="fn_create_report(event);">리포트생성하기</button>
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
			<th scope="col" style="text-align:center;">구분</th>
			<th scope="col" style="text-align:center;">이름</th>
			<th scope="col" style="text-align:center;">연락처</th>
			<th scope="col" style="text-align:center;">인증번호</th>
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
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&SE=".$midx."&page=".$page."&STXT=".$STXT;
?>
		<tr>
			<td align="center"><?=$bunho?></td>
			<td align="center"><?=$reg_date?></td>
			<td align="center"><?=fn_general_txt($recyn,fn_recyn_report())?></td>
			<td align="center"><?=$cname?></td>
			<td align="center"><?=$cphone?></td>
			<td align="center"><?=$passwd?></td>
			<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&idx=<?=$midx?>&page=<?=$page?>&RD=2'" class="btn btn-sm <?=($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
				&nbsp;&nbsp;
				<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&idx=<?=$midx?>&page=<?=$page?>&RD=4'" class="btn btn-sm btn-warning">발송내역보기</button>
			</td>
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

	<div style="float:right">
		<button type="button" style="margin-top:2px;" onClick="location.href='<?=$qstr?>&page=<?=$page?>&RD=3'" class="btn btn-sm btn-default">등록하기</button>
	</div>
	<!-- 리스트 E N D -->

<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>

<script>
function fn_create_report(event)
{
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

	var p_id = $("#p_id").val();

	if(confirm('정말 리포트를 등록 하시겠습니까?\n리포트가 등록되면 자동 문자가 발송 됩니다'))
	{
		if(!p_id)
		{
			alert("상품번호를 입력해야 합니다.");
			return false;
		} else {
			str = "&p_id="+p_id;
			$.ajax({
				type : 'POST',
				url : "./smscreate.php",
				data : str,
				dataType: 'json',
				success : function(data)
				{
					if(data.retcode == "OK")
					{
						var stralert = decodeURIComponent(data.retalert);
						alert(stralert.replace("+"," "));
						window.location = data.retval;
					} else if(data.retcode == "X") {
						var stralert = decodeURIComponent(data.retalert);
						alert(stralert.replace("+"," "));
					}
					return false;
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
					return false;
				}
			});
		}
	}
}
</script>