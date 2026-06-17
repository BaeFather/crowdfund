<?php
include_once('./_common.php');

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<div class="wrapper">

	<div id="container" style="">
	
		<h2 style="min-width:480;">아파트 검색</h1>
	
		<div class="tbl_frm01 tbl_wrap">
<?
$sql = "SELECT mg_id, d_name FROM hello_apt_kb GROUP BY d_code ORDER BY d_name";
$res = sql_query($sql);
?>
			
			
			<table class="table table-bordered" style="max-width:100%; margin: 10px auto;">
			
				<form method="post" name="f_dong">
			
				<tr>
					<th>동 이름</th>
					<td>
						<input type="text" list="d_list" name="d_name" id="d_name" class="form-control input-sm" style="width:300px; display:inline;" value="<?=$d_name?>">
						<datalist id="d_list">
						<?
						for ($i=0 ; $i<$res->num_rows; $i++) {
							$row = sql_fetch_array($res);
							?>
							<option data-value="<?=$row[mg_id]?>" value="<?=$row[d_name]?>" ></option>
							<?
						}
						?>
						</datalist>

						<button type="button" onclick="go_apt_srch();" class="btn btn-sm btn-default" style="margin-left:7px;">검색</button>
						
					</td>
				</tr>

				</form>

<?
if ($d_name) {
	$sql = "SELECT mg_id, dj_name, SUBSTRING_INDEX(d_name , ' ', -1) dong_name FROM hello_apt_kb WHERE d_name='$d_name' GROUP BY mg_id ORDER BY dj_name";
} else {
	$sql = "SELECT mg_id, dj_name, SUBSTRING_INDEX(d_name , ' ', -1) dong_name FROM hello_apt_kb WHERE idx<1 GROUP BY mg_id ORDER BY dj_name";
}
$res = sql_query($sql);
?>
				
				<tr>
					<th>아파트명</th>
					<td>
						<input type="text" list="kb_list" name="dj_name" id="dj_name" class="form-control input-sm" style="width:400px;">
						<datalist id="kb_list">
						<?
						for ($i=0 ; $i<$res->num_rows; $i++) {
							$row = sql_fetch_array($res);
							?>
							<option data-value="<?=$row[mg_id]?>" value="<?=$row[dj_name]?> (<?=$row[dong_name]?>)"></option>
							<?
						}
						?>
						</datalist>

						<input type="hidden" name="mg_id" id="mg_id" />
						<input type="hidden" name="jm"    id="jm" />
						<input type="hidden" name="mg_id2" id="mg_id2" /> <!-- 단지기본일련번호 -->
						<input type="hidden" name="jm2"    id="jm2" /> <!-- 면적일련번호 -->
					</td>
				</tr>
				
				
				<tr>
					<th>타입</th>
					<td>
						<select name="apt_type" id="apt_type"  class="form-control input-sm" style="display:inline;width:100px;" onchange="get_juso(this.value)">
							<option value="">평형 선택</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<th>동호수 선택</th>
					<td>
						<input type="text" name="dong" id="dong" class="form-control input-sm" style="width:50px; display:inline;"> 동
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="text" name="ho"   id="ho"   class="form-control input-sm" style="width:50px; display:inline;"> 호
					</td>
				</tr>
				
				<tr>
					<th>주소</th>
					<td>
						<input type="text" name="juso" id="juso" class="form-control input-sm" style="width:400px;">
						<input type="hidden" name="juso2" id="juso2">
					</td>
				
			</table>


			<div style="width:100%;text-align:center;">
				<button type="button" onclick="set_kb();" class="btn btn-sm btn-default" style="width:100px;">선택완료</button>
			</div>
			

		</div>

	</div>

</div>

<script>
function go_apt_srch() {

	var f = document.f_dong;
	f.submit();

	/*
	var d_name = $("#d_name").val();

	$.ajax({
		type : 'post',
		//dataType : 'json',
		url : 'ajax_kb_apt.php',
		data : {'d_name': d_name },
		success : function(data) {
			console.log(data);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});
	*/
	
}

function set_kb() {

	var js = $("#juso").val()+" "+$("#dong").val()+"동 "+$("#ho").val()+"호";  // 주소
	var mg_id = $("#mg_id").val();  // kb 시세의 mg_id
	var ju_seri = $("#apt_type").val();  // kb 시세의 ju_seri
	var jm = $("#jm").val();  // kb 시세의 mg_id
	var d_name = $("#juso2").val();  

	opener.fm.laddr.value = js;
	opener.fm.mg_id.value = mg_id;
	opener.fm.ju_seri.value = ju_seri;
	//opener.fm.kb_jm.value = number_format(jm);
	opener.fm.kb_jm.value = jm;
	opener.fm.laddr2.value = d_name;
	opener.set_house_deposit();
	self.close();
}

function get_juso(ju_seri) {

	var mg_id = $("#mg_id").val();

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_kb_juso.php',
		data : {'mg_id':mg_id , 'ju_seri':ju_seri},
		success : function(data) {
			console.log(data);
			$("#juso").val(data.juso);
			$("#jm").val(data.jm);
			$("#juso2").val(data.d_name);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}
function get_type(mg_id) {

	$.ajax({
		type: 'post',
		dataType: 'json',
		url: 'ajax_kb_type.php',
		data: {'mg_id':mg_id},
		success:function(data) {
			console.log(data);

			if (data==null) data=0;

			$("#apt_type option").remove();
			$("#apt_type").append("<option value=''>평형 선택</option>");

			for (var i=0 ; i<data["tp"].length ; i++) {
				$("#apt_type").append("<option value='"+data["tp"][i]["ju_seri"]+"'>"+data["tp"][i]["jm"]+"</option>");
			}

		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
			console.log(errorThrown);
			return false;
		}
	});

}

function getDataListSelectedOption(txt_input, data_list_options) {
	var shownVal = document.getElementById(txt_input).value;
	var value2send = document.querySelector("#" + data_list_options + " option[value='" + shownVal + "']").dataset.value;
    return value2send;
}

$('#dj_name').on('change', function(){
	//var showVal = $("#dj_name").val();
	//var realVal = $("#dj_name option[value='"+showVal+"']").data();
	//alert(realVal);
var mg_id = getDataListSelectedOption("dj_name","kb_list");
$("#mg_id").val(mg_id) ;
get_type(mg_id);
	//$('#dj_name').val();
	/*
    if (
        !(e instanceof InputEvent) ||
        e.inputType === 'insertReplacementText')
    ) {
        // determine if the value is in the datalist. If so, someone selected a value in the list!
    }
	*/
});
</script>


<script>
var getTextLength = function(str) { var len = 0; for (var i = 0; i < str.length; i++) { if (escape(str.charAt(i)).length == 6) { len++; } len++; } return len; }


$('#addr').keyup(function(){

	var strlen2 = getTextLength( $('#addr').val());

	if (strlen2>=4) {
		search_apt();
	}

});

function search_apt() {
	alert($('#addr').val());
}
</script>