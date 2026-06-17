<?
$name    = ($DATA['mb_name']) ? $DATA['mb_name'] : $DATA['nm_name'];
$co_name = ($DATA['mb_co_name']) ? $DATA['mb_co_name'] : $DATA['nm_co_name'];
$phone   = ($DATA['mb_hp']) ? $DATA['mb_hp'] : $DATA['nm_phone'];
?>

<style>
.title_td { background-color:#FAFAFA; font-weight:bold; text-align:center; }
</style>

<div class="row" style="padding-bottom:20px;">
	<form id="frmMemoEdit">
		<input type="hidden" name="idx" value="<?=$idx?>">
		<table align="center" class="table table-striped table-bordered table-hover" style="width:98%">
			<colgroup>
				<col width="15%">
				<col width="35%">
				<col width="15%">
				<col width="35%">
			</colgroup>
			<thead>
				<tr class="odd">
					<td class="title_td">이벤트명</td>
					<td><?=$DATA['title']?></td>
					<td class="title_td">기간</td>
					<td><?=preg_replace("/-/", ".", $DATA['sdate'])?> ~ <?=preg_replace("/-/", ".", $DATA['edate'])?></td>
				</tr>
				<tr>
					<td class="title_td">성명</td>
					<td><?=$name?></td>
					<td class="title_td">업체명</td>
					<td><?=$co_name?></td>
				</tr>
				<tr>
					<td class="title_td">연락처</td>
					<td><?=$phone?></td>
					<td class="title_td">상담신청시간</td>
					<td><?=substr(preg_replace("/-/", ".", $DATA['schedule_req_date']), 0, 16)?></td>
				</tr>
				<tr>
					<td class="title_td">접속정보</td>
					<td><?=$DATA['device']?>, <?=$DATA['ip']?></td>
					<td class="title_td">등록일시</td>
					<td><?=preg_replace("/-/", ".", $DATA['rdate'])?></td>
				</tr>
				<tr>
					<td class="title_td">관리자 확인</td>
					<td>
						<input type="radio" name="view_flag" value="Y" <?=($DATA['view_flag']=='Y')?'checked':''?>> 확인 &nbsp;
						<input type="radio" name="view_flag" value="N" <?=($DATA['view_flag']=='' || $DATA['view_flag']=='N')?'checked':''?>> 미확인
					</td>
					<td class="title_td"></td>
					<td></td>
				</tr>
				<tr>
					<td class="title_td">관리자 메모</td>
					<td colspan="3">
						<textarea id="admin_memo" name="admin_memo" style="width:100%;height:150px"><?=$DATA['admin_memo']?></textarea>
					</td>
				</tr>
			</thead>
		</table>
	</form>
	<p align="center"><a id="submit_button" class="btn btn-warning btn-lg">등록확인</a><p>
</div>

<script type="text/javascript">
$('#submit_button').click(function(){
	form_data = $("#frmMemoEdit").serialize();
	$.ajax({
		url : "invitaion_event_proc.php",
		type: "POST",
		data : form_data,
		success: function(data, textStatus, jqXHR){
			//$('#admin_memo').val(data);
			if(data=='OK') {
				alert('저장 되었습니다.'); location.reload();
			}
			else if(data=='NONE') {
				alert('이미 삭제된 데이터 입니다.'); history.go(-1);
			}
			else {
				alert('시스템 에러입니다. 관리자에게 문의하십시요.');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)	{
			alert('네트워크 에러입니다. 잠시 후 다시 시도하십시요.');
		}
	});
});
</script>