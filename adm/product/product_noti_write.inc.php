<?

$DATA = sql_fetch("SELECT * FROM cf_sms_noti WHERE idx='".$idx."'");

?>

<ul id="writeZone" style="list-style:none;display:inline-block;width:100%;padding:0;">
	<li style="width:59%;padding:8px;float:left; border:1px solid #AAA;border-radius:3px;">
		<form id="frmWrite">
			<input type="hidden" id="mode" name="mode" value="<?=($DATA['idx'])?'edit':'new';?>">
			<input type="hidden" id="idx" name="idx" value="<?=$idx?>">
		<table class="table table-bordered table-condensed">
			<colgroup>
				<col width="100px">
				<col width="%">
			<colgroup>
			<tr>
				<td align="center" bgcolor="#F8F8EF">구분</td>
				<td><select id="gubun" name="gubun" class="form-control" style="width:200px;">
						<option value="1" <?=($DATA['gubun']=='1')?'selected':''?>>상품안내</option>
						<option value="2" <?=($DATA['gubun']=='2')?'selected':''?>>일반공지</option>
						<option value="3" <?=($DATA['gubun']=='3')?'selected':''?>>긴급공지</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="center" bgcolor="#F8F8EF">제목</td>
				<td><input type="text" id="subject" name="subject" value="<?=$DATA['subject']?>" class="form-control" style="width:100%;"></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#F8F8EF">내용</td>
				<td>
					<textarea id="cont0" name="cont[]" class="form-control" style="margin-bottom:4px;height:100px;"><?=$DATA['cont0']?></textarea>
<?
for($k=1; $k<5; $k++) {
	echo "					<textarea id=\"cont{$k}\" name=\"cont[]\" class=\"form-control\" style=\"margin-bottom:4px;height:100px;\">".$DATA['cont'.$k]."</textarea>\n";
}
?>
				</td>
			</tr>
		</table>
		</form>
		<p style="text-align:center;">
			<button type="button" id="btnSubmit" onClick="formSubmit();" class="btn btn-danger" style="width:150px"><?=($DATA['idx'])?'수정':'등록';?></button>
			<button type="button" id="preview" class="btn btn-default" style="width:150px">미리보기</button>
		</p>
	</li>
	<li id="previewZone" style="width:50%;height:100%; max-width:500px; min-height:500px; margin-left:20px; padding0;float:left; font-size:1.2em; border:1px dotted #000;">
		<!--<textarea id="previewZone2" width="100%" height="100%"></textarea>-->
	</li>
</ul>
<br/><br/>

<script>
function formSubmit() {
	if( confirm("<?=($DATA['idx'])?'수정':'등록';?> 하시겠습니까?") ) {

		var ajax_data = $('#frmWrite').serialize();

		$.ajax({
			url : "product_noti.proc.php",
			type: "post",
			data : ajax_data,
			success: function(data) {
				if(data == 'INSERT_OK')           { location.href='?page=1'; }
				else if(data == 'EDIT_OK')        { alert('게시글이 수정 되었습니다.'); location.reload(); }
				else if(data == 'INSERT_FAIL')    { alert('게시글 등록 실패'); }
				else if(data == 'EDIT_FAIL')      { alert('게시글 수정 실패'); }
				else if(data == 'EDIT_NO_CHANGE') { alert('기존내용과 다르지 않습니다.'); }
				else if(data == 'LOSTED_CONTENT') { alert('없는 게시글 입니다.'); location.replace('?page=<?=$page?>'); }
				else                              { alert('시스템 오류 입니다.'); }
			}
		});

	}
}

$('#preview').click(function() {

	var ajax_data = $('#frmWrite').serialize();

	$.ajax({
		url : "product_noti_preview.php",
		type: "post",
		data : ajax_data,
		success: function(data) {
			$('#previewZone').html(data);
		}
	});

});
</script>
