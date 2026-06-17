<?php
include_once('./_common.php');

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>

<div class="wrapper">

	<div id="container" style="">
	
		<h3 style="min-width:480;">부동산등기 주소검색(간편)</h3>
	
		<div class="tbl_frm01 tbl_wrap">
			
			<table class="table table-bordered" style="max-width:100%; margin: 20px auto;">
			

				<form name="f_dong" method="post" action="" onsubmit="return go_hyphen_addr_srch()">
			
				<tr>
					<th>검색 주소</th>
					<td>
						<input type="text" name="srch_addr" id="srch_addr" class="form-control input-sm" style="width:400px; display:inline;">

						<button type="button" onclick="go_hyphen_addr_srch();" class="btn btn-sm btn-default" style="margin-left:7px;">검색</button>
						
					</td>
				</tr>

				</form>
				
				<tr>
					<th>주소 선택</th>
					<td>
						<select name="tg_addr" id="tg_addr"  class="form-control input-sm" style="display:inline;width:550px;" >
							<option value=""></option>
							<!--option value="13452010008341">경기도 용인시 수지구 동천동 932 한빛마을래미안이스트팰리스3단지 제1301동 제2층 제201호</option-->
						</select>
					</td>
				</tr>
				
			</table>


			<div style="width:100%;text-align:center;">
				<button type="button" onclick="set_hyphen();" class="btn btn-sm btn-default" style="width:100px;">선택완료</button>
			</div>
			

		</div>

	</div>

</div>

<script>
function set_hyphen() {

	var fm = "<?=$f?$f:'fm'?>";

<?
if ($f=="regfm") {
	?>
	opener.regfm.cert_num.value = $("#tg_addr").val();
	opener.regfm.laddr.value = $("#tg_addr option:checked").text();

	opener.get_issue2();
	self.close();
	<?
} else {
	?>
	opener.fm.cert_num.value = $("#tg_addr").val();
	opener.fm.laddr.value = $("#tg_addr option:checked").text();

	opener.get_issue2();
	self.close();
	<?
}
?>
}

function go_hyphen_addr_srch() {

	var addr = $("#srch_addr").val();

	if (!addr || addr.length<10) {
		alert("검색어가 없거나 입력값이 너무 작습니다. "+addr.length);
		return false;
	}

	$("#tg_addr option").remove();
	$("#tg_addr").append("<option value='' style='text-align-last: center; text-align: center; -ms-text-align-last: center; -moz-text-align-last: center;'>.............. 검 색 중 ..............</option>");

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : '/hyphen/hyphen_srch_simple_addr.php',
		data : {'addr':addr},
		success : function(data) {
			console.log(data);

			if (data["out"]["errYn"]=="Y") {
				$("#tg_addr option").remove();
				alert(data["out"]["errMsg"]);
				return;
			}

			$("#tg_addr option").remove();
			$("#tg_addr").append("<option value=''>주소 선택</option>");

			
			for (var i=0 ; i<data["out"]["outC0000"]["list"].length ; i++) {
					$("#tg_addr").append("<option value='"+data["out"]["outC0000"]["list"][i]["부동산고유번호"]+"'>"+data["out"]["outC0000"]["list"][i]["부동산소재지번"]+"</option>");
			}

		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

	return false; // submit 방지
}
</script>