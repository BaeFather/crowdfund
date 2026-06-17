<?php
include_once('./_common.php');

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

$use_sc_svr = "N";  // 내부 스크래핑 서버 사용 여부 Y는 사용
?>
<?
if ($d_name) {

	$sql = "SELECT d_code, mg_id, dj_name, SUBSTRING_INDEX(d_name , ' ', -1) dong_name, d_code FROM hello_apt_kb WHERE d_name='$d_name' GROUP BY mg_id ORDER BY dj_name limit 1";
	$row = sql_fetch($sql);
	$dcode = $row["d_code"];

	if ($dcode) get_dj($dcode);

}


function get_dj($dcode) {

	global $use_sc_svr;

	$chk_sql = "SELECT COUNT(*) cnt FROM hello_apt_kb WHERE d_code='$dcode' AND (mg_id2='' OR mg_id2 is NULL) ";
	$chk_row = sql_fetch($chk_sql);

	if ($chk_row["cnt"]>0) {

		if ($use_sc_svr=="Y") {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
			curl_setopt($ch, CURLOPT_URL, "http://scrap2.hellofunding.kr/scrap2/get_dj.php?d_code=".$dcode);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$response = curl_exec($ch);
			curl_close($ch);
		
		} else {

			$param3 = array('법정동코드' => $d_code,'유형' => 1);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
			curl_setopt($ch, CURLOPT_URL, "https://api.kbland.kr/land-price/price/fastPriceComplexName?".http_build_query($param3));
			$response = curl_exec($ch);
			curl_close($ch);

		}

		$d = json_decode($response, true);

		for ($i=0 ; $i<count($d["dataBody"]["data"]); $i++) {

			$mg_id  = $d["dataBody"]["data"][$i]["시세물건식별자"];
			$mg_id2 = $d["dataBody"]["data"][$i]["단지기본일련번호"];
			$addr2  = $d["dataBody"]["data"][$i]["주소"];

			$chk_sql2 = "SELECT COUNT(*) cnt FROM hello_apt_kb WHERE d_code='$dcode' AND mg_id='".$mg_id."' AND (mg_id2='' OR mg_id2 is NULL)";
			$chk_row2 = sql_fetch($chk_sql2);

			if ($chk_row2["cnt"]>0) {
				$up_sql = "UPDATE hello_apt_kb SET mg_id2='$mg_id2', addr2='".$addr2."' WHERE d_code='$dcode' AND mg_id='".$mg_id."' AND (mg_id2='' OR mg_id2 is NULL)";
				sql_query($up_sql);
			}			

		}
	} 

	//echo "<pre>"; print_r($d); echo "</pre>";

}
?>
<div class="wrapper">

	<div id="container" style="">
	
		<h2 style="min-width:480;">아파트 검색</h1>
	
		<div class="tbl_frm01 tbl_wrap">
<?
$sql = "SELECT d_code, mg_id, d_name FROM hello_apt_kb GROUP BY d_code ORDER BY d_name";
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
							<option data-value="<?=$row[d_code]?>" value="<?=$row[d_name]?>" ></option>
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
	$sql = "SELECT mg_id, mg_id2, dj_name, SUBSTRING_INDEX(d_name , ' ', -1) dong_name, d_code FROM hello_apt_kb WHERE d_name='$d_name' GROUP BY mg_id ORDER BY dj_name";
} else {
	$sql = "SELECT mg_id, mg_id2, dj_name, SUBSTRING_INDEX(d_name , ' ', -1) dong_name, d_code FROM hello_apt_kb WHERE idx<1 GROUP BY mg_id ORDER BY dj_name";
}
$res = sql_query($sql);
?>
				
				<tr>
					<th>아파트명</th>
					<td>
						<input type="text" list="kb_list" name="dj_name" id="dj_name" class="form-control input-sm" style="width:380px;">
						<datalist id="kb_list">
						<?
						for ($i=0 ; $i<$res->num_rows; $i++) {
							$row = sql_fetch_array($res);
							?>
							<option data-value="<?=$row[mg_id]?>:<?=$row[mg_id2]?>" value="<?=$row[dj_name]?> (<?=$row[dong_name]?>)"></option>
							<?
						}
						?>
						</datalist>

						<input type="hidden" name="mg_id" id="mg_id" />
						<input type="hidden" name="jm"    id="jm" />
						<input type="hidden" name="mg_id2" id="mg_id2" /> <!-- 단지기본일련번호 -->
						<input type="hidden" name="ju_seri2"    id="ju_seri2" /> <!-- 면적일련번호 -->
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
						<input type="text" name="juso" id="juso" class="form-control input-sm" style="width:380px;">
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

	var imsi = $("#apt_type").val().split(':');

	var js = $("#juso").val()+" "+$("#dong").val()+"동 "+$("#ho").val()+"호";  // 주소
	var mg_id  = $("#mg_id").val();  // kb 시세의 mg_id
	var mg_id2 = $("#mg_id2").val();  // kb 시세의 mg_id
	var ju_seri  = imsi[0];  // kb 시세의 ju_seri
	var ju_seri2 = $("#ju_seri2").val();  // kb 시세의 ju_seri
	var jm = $("#jm").val();  // kb 시세의 mg_id
	var d_name = $("#juso2").val();  

	opener.fm.laddr.value = js;
	opener.fm.mg_id.value  = mg_id;
	opener.fm.mg_id2.value = mg_id2;
	opener.fm.ju_seri.value  = ju_seri;
	opener.fm.ju_seri2.value = ju_seri2;
	//opener.fm.kb_jm.value = number_format(jm);
	opener.fm.kb_jm.value = jm;
	opener.fm.laddr2.value = d_name;
	opener.fm.d_code.value = "<?=$row[d_code]?>";
	opener.set_house_deposit();
	self.close();
}

function get_juso(ju_seri) {
	var imsi = ju_seri.split(':');
	ju_seri = imsi[0];
	var ju_seri2 = imsi[1];


	var mg_id = $("#mg_id").val();

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_kb_juso.php',
		data : {'mg_id':mg_id , 'ju_seri':ju_seri},
		success : function(data) {
			console.log(data);
			$("#juso").val(data.juso+" "+data.dj_name);
			$("#jm").val(data.jm);
			$("#juso2").val(data.d_name+" "+data.dj_name);
			$("#ju_seri2").val(ju_seri2);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}
function get_type(mg_id,mg_id2) {

	$.ajax({
		type: 'post',
		dataType: 'json',
		url: 'ajax_kb_type2.php',
		data: {'mg_id':mg_id , 'mg_id2':mg_id2},
		success:function(data) {
			console.log(data);

			if (data==null) data=0;

			$("#apt_type option").remove();
			$("#apt_type").append("<option value=''>평형 선택</option>");

			for (var i=0 ; i<data["tp"].length ; i++) {
				if (data["tp"][i]["top_gubun"]) {
					$("#apt_type").append("<option value='"+data["tp"][i]["ju_seri"]+":"+data["tp"][i]["ju_seri2"]+"'>"+data["tp"][i]["jm"]+"("+data["tp"][i]["top_gubun"]+")"+"</option>");
				} else {
					$("#apt_type").append("<option value='"+data["tp"][i]["ju_seri"]+":"+data["tp"][i]["ju_seri2"]+"'>"+data["tp"][i]["jm"]+"</option>");
				}
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

	var allid = getDataListSelectedOption("dj_name","kb_list");
	var imsi = allid.split(':');
	var mg_id = imsi[0];
	var mg_id2= imsi[1];

	$("#mg_id").val(mg_id) ;
	$("#mg_id2").val(mg_id2) ;
	get_type(mg_id, mg_id2);

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