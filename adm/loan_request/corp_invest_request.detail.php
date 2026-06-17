<?

include_once('./_common.php');

$DATA = sql_fetch("
	SELECT
		A.*,
		B.mb_id,
		(SELECT mb_name FROM g5_member WHERE mb_id=A.check_admin_id) AS admin_name
	FROM
		cf_care_service_request A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE (1)
		AND A.idx='".$idx."' AND is_drop=''");

if(!$DATA['idx']) msg_go('데이터가 없습니다.');

if($DATA['check_admin_id']=='') {
	sql_query("
		UPDATE
			cf_care_service_request
		SET
			check_admin_id = '".$member['mb_id']."',
			check_date = NOW()
		WHERE
			idx='".$idx."'");
}

//print_rr($DATA, 'font-size:12px;line-height:13px');

$print_content = ($DATA['content']) ? nl2br(htmlSpecialChars($DATA['content'])) : '';

?>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th scope="col">문의자명</th>
			<td><?=$DATA['name']?>(<?=$DATA['mb_id']?>)</td>
			<th scope="col">연락처</th>
			<td><?=$DATA['phone']?></td>
		</tr>
		<tr>
			<th scope="col">이메일</th>
			<td><?=$DATA['email']?></td>
			<th scope="col">법인설립여부</th>
			<td><?if($DATA['is_est'] == 'N') {echo "설립예정";} else {echo "설립완료";}?></td>
		</tr>
		<tr>
			<th scope="col">내용</th>
			<td colspan="3" style="height:200px;" valign="top"><?=$print_content?></td>
		</tr>
		<tr>
			<th scope="col">확인관리자</th>
			<td><?=$DATA['admin_name']?>(<?=$DATA['check_admin_id']?>)</td>
			<th scope="col">확인일시</th>
			<td><?=substr($DATA['check_date'],0,16)?></td>
		</tr>
		<tr>
			<th scope="col">관리자메모</th>
			<td colspan="3" style="height:200px;" valign="top"><textarea id="admin_content" style="width:100%;height:100%;" required><?=$DATA['admin_content']?></textarea></td>
		</tr>
		<tr>
			<th scope="col">최종수정</th>
			<td colspan="3"><?=substr($DATA['last_editdate'],0,16)?></td>
		</tr>
	</table>


	<div style="max-width:1000px;text-align:center;">
		<button type="button" id="frmEmailSubmit" class="btn btn-sm btn-default" style="width:100px" onclick="emailSend();">이메일 발송</button>
		<button type="button" id="frmSubmit" class="btn btn-sm btn-primary" style="width:80px">등 록</button>
		<button type="button" onClick="dropData('<?=$DATA['idx']?>')" class="btn btn-sm btn-danger" style="width:80px">삭제</button>
		<button type="button" id="list_button" onClick="location.href='<?=$_SERVER['PHP_SELF']?><?=($_SERVER['QUERY_STRING'])? '?'.preg_replace("/&idx=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']):'';?>';" class="btn btn-sm btn-default" style="width:80px">목록보기</button>
	</div>

	<script>
	function emailSend() {
		var win = window.open("corp_invest_emailSend.php?idx="+<?=$idx?>, "PopupEmail", "width=600, height=700");
	}

	$('#frmSubmit').click(function() {
		if( $('#admin_content').val()=='' ) {
			alert('내용을 입력하십시요.');  $('#admin_content').focus();
		}
		else {
			$.ajax({
				url :"corp_invest_request.proc.ajax.php",
				type:"POST",
				dataType:"JSON",
				data:{
					mode:'update',
					idx:'<?=$DATA['idx']?>',
					admin_content:$('#admin_content').val()
				},
				success:function(data) {
					if(data.result=='SUCCESS') {
						alert('등록되었습니다.'); window.location.reload();
					}
					else {
						if(data.message) {
							alert(data.message);
						}
						else {
							console.log();
						}
					}
				},
				error:function(e) { console.log(e); }
			});
		}
	});
	</script>



	<!-- 코멘트 //-->
	<div style="margin-top:30px; max-width:1000px;">
		<h3>COMMENT</h3>
		<ul class="list-inline" style="margin-bottom:20px;">
			<li style="width:85%;height:80px"><textarea id="comment" style="width:100%;height:100%;" required></textarea></li>
			<li style="width:14.6%"><button type="button" id="frmCmtSubmit" class="btn btn-primary" style="width:100%;height:80px;">등 록</button></li>
		</ul>

<?
$cres  = sql_query("SELECT idx, writer, comment, regdate FROM cf_care_service_request_comment WHERE req_idx='".$idx."' ORDER BY idx DESC");
$crows = $cres->num_rows;
if($crows) {
	for($c=0,$cno=1; $c<$crows; $c++,$cno++) {
		$CROW = sql_fetch_array($cres);
		if($CROW['writer']=='system') {
			$cwriter = $CROW['writer'];
		}
		else {
			$MEM  = sql_fetch("SELECT mb_name FROM g5_member WHERE mb_id='".$CROW['writer']."' AND mb_level IN(9, 10)");
			$cwriter = ($MEM['mb_name']) ?  $MEM['mb_name'] : $CROW['writer'];
		}

		$delete_tag = "";
		if($CROW['writer']==$member['mb_id'] || $member['mb_level']=='10') {
			$delete_tag = "<span onClick='dropComment(".$CROW['idx'].")' style='cursor:pointer;color:red'>×</span>";
		}

		$comm = nl2br(htmlSpecialChars($CROW['comment']));

?>
		<table style="font-size:12px">
			<colgroup>
				<col width="200">
				<col width="">
				<col width="30">
			</colgroup>
			<tr style='background:#FAFAFA'>
				<td align="left"><?=$cwriter?></td>
				<td align="right"><span style="color:#aaa"><?=$CROW['regdate']?></span></td>
				<td align="center"><?=$delete_tag?></td>
			</tr>
			<tr>
				<td colspan="3" style="padding:8px 20px"><?=$comm?></td>
			</tr>
		</table>
		<div style="height:10px;"></div>
<?
	}
}
?>
	</div>
	<!-- 코멘트 //-->

	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>

	<script>
	$('#frmCmtSubmit').click(function() {
		if( $('#comment').val()=='' ) {
			alert('내용을 입력하십시요.');  $('#comment').focus();
		}
		else {
			$.ajax({
				url : "corp_invest_request.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data:{
					mode: 'cnew',
					idx: '<?=$DATA['idx']?>',
					comment: $('#comment').val()
				},
				success:function(data) {
					if(data.result=='SUCCESS') { window.location.reload(); }
					else { alert(data.message); }
				},
				error: function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	});

	dropComment = function(commidx) {
		if(confirm('코멘트를 삭제 하시겠습니까?')) {
			$.ajax({
				url : "corp_invest_request.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data:{
					mode: 'cdelete',
					commidx: commidx
				},
				success:function(data) {
					if(data.result=='SUCCESS') { alert('삭제 되었습니다.'); window.location.reload(); }
					else { alert(data.message); }
				},
				error:function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
	</script>