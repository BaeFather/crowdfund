<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '910500';
auth_check($auth[$sub_menu], "w");

$g5['title'] = "주택담보대출 심사";

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<div class="tbl_frm01 tbl_wrap">

	<form name="fm" method="post" action="" autocomplete="off" onsubmit="return go_addr_srch()">
	<div style="text-align:center;">
		주소 <input type="text" class="form-control input-sm" name="sch_addr" id="sch_addr" value="<?=$sch_addr?>" style="width:300px;display:inline; margin: 0 20px;"/>
		<button type="button" class="btn btn-sm btn-warning" onClick="go_addr_srch();">검색</button>
		<!--input type=button class="btn btn-sm btn-warning" onclick="go_addr_srch();" value="search"/-->

		<br/><br/>


		<select name="tg_addr" id="tg_addr"  class="form-control input-sm" style="display:inline;width:600px;" >
			<option value=""></option>
		</select>
		<button type="button" class="btn btn-sm btn-warning" onClick="go_issue_req();">발급</button>

		<pre id="issue_res2" style="text-align:left; margin-top:20px;"></pre>

		<pre id="issue_res" style="text-align:left; margin-top:20px;"></pre>

	</div>
	</form>

</div>

<script>
function go_issue_req() {

	var unqno = $("#tg_addr").val();
	//var unqno = "28492013001009";

	var yn = confirm(unqno+" 등기부등본을 발급하시겠습니까?");
	if (!yn) return;

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : '/hyphen/hyphen_issue.php',
		data : {'uniqNo':unqno},
		success : function(data) {

			console.log(data);

			$("#issue_res").text( JSON.stringify(data,null,2) ); 

			$("#issue_res2").text();

			$("#issue_res2").text("지번_및_번호 : "+data["out"]["outList"]["지번_및_번호"]+"\n\n"); 

			$("#issue_res2").text( $("#issue_res2").text()+"소유지분현황_갑구"); 
			for (var i=0 ; i< data["out"]["outList"]["소유지분현황_갑구"].length ; i++) {
				console.log(data["out"]["outList"]["소유지분현황_갑구"][i]);
				$("#issue_res2").text( $("#issue_res2").text()+"\n\t" + data["out"]["outList"]["소유지분현황_갑구"][i]["등기명의인"]+ " , 주민번호 "+ data["out"]["outList"]["소유지분현황_갑구"][i]["주민_등록번호"] + " , 최종지분: "+ data["out"]["outList"]["소유지분현황_갑구"][i]["최종지분"]+" , 순위번호: "+data["out"]["outList"]["소유지분현황_갑구"][i]["순위번호"]); 
			}


			$("#issue_res2").text( $("#issue_res2").text()+"\n\n\n소유지분을_제외한_소유권에_관한_사항_갑구"); 
			for (var i=0 ; i< data["out"]["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"].length ; i++) {
				$("#issue_res2").text( $("#issue_res2").text()+"\n\t" + data["out"]["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"][i]["순위번호"] + "\t"+ data["out"]["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"][i]["등기목적"] + "\t"+data["out"]["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"][i]["접수정보"] + "\t"+ data["out"]["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"][i]["주요등기사항"]+ "\t"+ data["out"]["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"][i]["대상소유자"]); 
			}

		
			$("#issue_res2").text( $("#issue_res2").text()+"\n\n\n근저당권 및 전세권등 을구"); 
			for (var i=0 ; i< data["out"]["outList"]["근_저당권_및_전세권_등_을구"].length ; i++) {
				$("#issue_res2").text( $("#issue_res2").text()+"\n\t"+ data["out"]["outList"]["근_저당권_및_전세권_등_을구"][i]["순위번호"] +"\t"+ data["out"]["outList"]["근_저당권_및_전세권_등_을구"][i]["등기목적"] +  "\t"+ data["out"]["outList"]["근_저당권_및_전세권_등_을구"][i]["접수정보"] + "\t"+ data["out"]["outList"]["근_저당권_및_전세권_등_을구"][i]["주요등기사항"]); 
			}
		
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});
}

function go_addr_srch() {

	var addr = $("#sch_addr").val();

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
}
</script>