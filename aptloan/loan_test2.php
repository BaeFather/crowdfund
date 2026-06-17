<?
include_once('./_common.php');

IF(!$_COOKIE["pid"])
{
	$strP = clean_xss_tags($_GET["p"]);
	IF($strP)
	{
		setcookie("pid",TRIM($strP),0,"/","");
	}
} ELSE {
	$strP = clean_xss_tags($_COOKIE["pid"]);
}

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

?>
<link href="css/loan.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>


<script type="text/javascript">
	var si =	"";
	var gu =	"";
	var dong =	"";
	var danji =	"";
	var strlink = "";
</script>
<script type="text/javascript" src="./aptloan.js"></script>

<script>
	//check_form_proc("kind=si", event);
</script>
<script type="text/javascript">

function Rradio_OnOff(id)
{
   if(id == "Radio_On")
   {
      document.all["Radio_On"].style.display = '';         // 보이게
      document.all["Radio_Off"].style.display = 'none';  // 안보이게
   }
   else
   {
      document.all["Radio_On"].style.display = 'none';  // 안보이게
      document.all["Radio_Off"].style.display = '';         // 보이게
   }
}
</script>

<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->

<div id="content">


	<div id="loan">
		<div class="loan1">
			<h2>대출한도조회 및 대출 신청</h2>
			<p>한도조회는 신용점수에 영향을 주지 않습니다.</p>
		</div>
		<div class="loan3">
			<ul>
				<li class="ball_01 regular">1</li>
				<li class="ball_02 regular">2</li>
			</ul>
		</div>

		<form name="wform" id="wform" method="post">

		<input type="hidden" name="si" id="si" />
		<input type="hidden" name="gu" id="gu" />
		<input type="hidden" name="dong" id="dong" />
		<input type="hidden" name="apt_name" id="apt_name" />

		<input type="hidden" name="kind" value="auth" />
		<input type="hidden" name="price" value="" />
		<input type="hidden" name="rprice" value="" />
		<input type="hidden" name="pid" value="<?php ECHO $strP;?>" />
		<input type="hidden" name="rdo_apt" value="1" />

		<input type="hidden" name="buildingcd" id="buildingcd">


		<div class="loan4">

			<div class="loan_form">
			<div class="title"><p class="step">지역정보</p></div>

			<div style="display:block; width:100%;">
				<input type="hidden" name="zipcode" value="" id="zipcode" class="form-control input-sm" readonly size="5" maxlength="6">
				<input type="text" name="address" value="" id="address" class="form-control input-sm" style="width:500px; margin-bottom:2px;">
				<input type="hidden" name="address_detail" value="" id="address_detail" class="form-control input-sm" style="width:800px;">
				<input type="hidden" name="address2" value="" id="address2" class="form-control input-sm">
				<input type="hidden" name="address3" value="" id="address3" class="form-control input-sm">


					<a onClick="win_zip_loan('wform', 'zipcode', 'address', 'address_detail', 'address2', 'address' , 'si' , 'gu' ,'dong' , 'apt_name');" class="btn_blue">주소 검색</a>

			</div>



			<div class="title"><p class="step">아파트정보</p></div>

			<ul class="radiobt1">
				<li><label class="apt_radio"><input type="radio" name="rdo_apt22" value="1" onclick="Rradio_OnOff('Radio_On');" checked><span>선택</span></label></li>
				<li class="pd"><label class="apt_radio"><input type="radio" name="rdo_apt22" value="2" onclick="Rradio_OnOff('Radio_Off');"><span>직접입력</span></label></li>
			</ul>

			<ul id="Radio_On" style="display:;">
				<li class="step2 clear">
					<select id="apt_name22" name="apt_name22" class="b_names" OnChange="check_form_send('apt_area',this.value);">
						<option value="">아파트 선택</option>
					</select>
				</li>
				<li class="step3">
					<select id="apt_area" name="apt_area" class="areas" onchange="set_price();">
						<option value="">전용면적 선택 (㎡)</option>
					</select>
				</li>
				<li class="step2"><input class="dong_num" type="text" name="dong_num" value="" placeholder="동을 입력해주세요"></li>
				<li class="step2"><input class="floor_num" type="text" name="floor_num" value="" placeholder="층수을 입력해주세요"></li>
				<li class="step3"><input class="ho_num" type="text" name="ho_num" value="" placeholder="호수를 입력해주세요"></li>
			</ul>

			<ul id="Radio_Off" style="display: none;">
				<li class="step2 clear">
					<input class="detail_add" type="text" name="apt_name2" value="" placeholder="아파트명 및 상세주소를 입력해주세요  ex) 홍길동아파트 105동 11호">
				</li>
			</ul>

		<div class="btn1">
			<a href="javascript:void(0);" id="btn1" OnClick="check_w_form_re('wform',event);">대출한도 조회하기</a>
		</div>
		</form>

		<div class="call">
		<ul>
			<li>
				아파트 담보대출상담이 필요하시면 언제든지 연락주세요!<br>
				<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
			</li>
			<li>
				<img src="img/call.png">
			</li>
			<li>
			    1588-5210
			</li>

		</ul>
			
		</div>
		<div class="loan_warning">
			연계대출 이자율 연19.9%이내(연체금리 연 20%이내), 연계대출 시 법무비 등 부가비용이 발생할 수 있으며 신용점수가 하락될 수 있습니다.&nbsp;
			대출유형에 따라 중도상환수수료 등 조기상환 조건이 적용될 수 있습니다. &nbsp;플랫폼 이용 시 플랫폼이용수수료가 발생할 수 있습니다.&nbsp;
			과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.
		</div>		
			
		<div class="m_call">
			
		<div class="m_loan_warning">
			연계대출 이자율 연19.9%이내(연체금리 연 20%이내), 연계대출 시 법무비 등 부가비용이 발생할 수 있으며 신용점수가 하락될 수 있습니다.&nbsp;
			대출유형에 따라 중도상환수수료 등 조기상환 조건이 적용될 수 있습니다. &nbsp;플랫폼 이용 시 플랫폼이용수수료가 발생할 수 있습니다.&nbsp;
			과도한 빚은 당신에게 큰 불행을 안겨줄 수 있습니다.
		</div>		
		<ul>
			<li>
				상담이 필요하시면 언제든지 연락주세요!<br>
				<span>운영시간 (월 ~목 : 10시 - 19시 , 금 : 10시 - 17시)</span>
			</li>
			<li>
			    <img src="img/call.png"><span>1588-5210</span>
			</li>

		</ul>
			
			
		</div>


	</div>
	</div>

</div>
</div>


<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>

<script>
var sise_data = new Array();

function check_w_form_re(fmname, event) {

	var f = document.wform;
	f.action = "loan_form.php";
	f.submit();
}

function set_price() {

	console.log(sise_data);

	var idx = "";

	for (var i=0 ; i<sise_data["outH0001"]["list"].length; i++) {
		if ($("#apt_area").val() == i+","+sise_data["outH0001"]["list"][i]["areaSerialNumber"]) {
			idx = i;
		}
	}

	if (idx >= 0) {
console.log(idx);
		var pcnt = get_pcnt($("#address").val());
		var prc  = sise_data["outH0001"]["list"][idx]["nomAvrDealPrc"] * 10000;
		var rprc = (prc * pcnt);

		$("input[name='price']").val(prc);
		$("input[name='rprice']").val(rprc);
	}

}

function get_pcnt(addr) {
	if (!addr) eturn;
	var si = addr.substring(0,2);
	var perc = 0;

	if (si=="서울") perc = 0.83;
	else if(si=="경기") perc = 0.80;
	else if(si=="인천") perc = 0.80;
	else perc = 0.75
	
	return perc;
}

function hyphen_sise(bldcd) {

	var bdCd = bldcd;

	if (!bdCd) {
		alert("단지코드가 없습니다.");
		return;
	}

	$.ajax({
		type : 'post',
		dataType : 'json',
		//url : '/hyphen/hyphen_sise.php',
		url : '/hyphen/hyphen_sise_test.php',
		data : {'buildingCd': bdCd},
		success : function(data) {

			console.log(data);
			sise_data = data;

			$("#apt_area").empty();  // 평형선택 초기화
			$("#apt_area").append("<option value=''>전용면적 선택 (㎡)</option>");

			for (var i=0 ; i<data["outH0001"]["list"].length; i++) {
				$("#apt_area").append("<option value='"+i+","+data["outH0001"]["list"][i]["areaSerialNumber"]+"'>"+data["outH0001"]["list"][i]["exclusiveSpace"]+" "+data["outH0001"]["list"][i]["supplySpaceType"]+"</option>");
			}

			alert("end");

		},
		//beforeSend: function() { loading('on'); },
		//complete: function() { loading('off'); },
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}
async function hyphen_sise_code(addr) {

//	var addr = $("address").val();
	if (!addr) {
		alert("주소를 입력해주세요.");
		return;
	}





	$.ajax({
		type : 'post',
		dataType : 'json',
		url : '/hyphen/hyphen_sise_code_test.php',
		data : {'addr': addr},
		success : function(data) {

			console.log(data);
			var bldcd = data["out"]["outB0002"]["list"][0]["buildingCd"];
			$("#buildingcd").val(data["out"]["outB0002"]["list"][0]["buildingCd"]);

			hyphen_sise(bldcd);
/*
			if (data["out"]["outB0002"]["list"].length) {
				$("#buildingCd").val(data["out"]["outB0002"]["list"]["0"]["buildingCd"]);
				hyphen_sise();
			} else {
				alert("주소지를 찾을수 없습니다.\n("+srch_addr+")");
			}
*/
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}

	});

}
</script>

<script>
/**
 * 우편번호 창
 **/
var win_zip_loan = function(frm_name, frm_zip, frm_addr1, frm_addr2, frm_addr3, frm_jibeon, si, gu, dong, apt_name) {

	if(typeof daum === 'undefined') {
		alert("다음 우편번호 postcode.v2.js 파일이 로드되지 않았습니다.");
		return false;
	}

	var zip_case = 1;   //0이면 레이어, 1이면 페이지에 끼워 넣기, 2이면 새창

	var complete_fn = function(data) {
		// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

		var fullAddr  = '';		// 최종 주소 변수
		var extraAddr = '';		// 조합형 주소 변수

		if (data.userSelectedType === 'R') {		// 사용자가 도로명 주소를 선택했을 경우
			fullAddr = data.roadAddress;
		}
		else {		// 사용자가 지번 주소를 선택했을 경우(J)
			fullAddr = data.jibunAddress;
		}

		// 사용자가 선택한 주소가 도로명 타입일때 조합한다.
		if(data.userSelectedType === 'R') {		//법정동명이 있을 경우 추가한다.
			if(data.bname !== '') {
				extraAddr += data.bname;
			}
			if(data.buildingName !== '') {		// 건물명이 있을 경우 추가한다.
				extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
			}
			extraAddr = (extraAddr !== '' ? ' ('+ extraAddr +')' : '');		// 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
		}

		// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
		var of = document[frm_name];

		//of[frm_zip].value	   = data.zonecode;
		//of[frm_addr1].value  = data.roadAddress + extraAddr;
		//of[frm_jibeon].value = data.jibunAddress;

		//of[frm_addr2].focus();

		of[si].value = data.sido;
		of[gu].value = data.sigungu;
		of[dong].value = data.bcode + "," + data.bname;
		of[apt_name].value = data.buildingCode + "," + data.buildingName;

		of[frm_addr1].value  = fullAddr;
		of[frm_addr1].focus();

		hyphen_sise_code(fullAddr);

	};

	switch(zip_case) {
		case 1 :	//iframe을 이용하여 페이지에 끼워 넣기
			var daum_pape_id = 'daum_juso_page'+frm_zip,
			element_wrap = document.getElementById(daum_pape_id),
			currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

			if(element_wrap == null) {
				element_wrap = document.createElement("div");
				element_wrap.setAttribute("id", daum_pape_id);
				element_wrap.style.cssText = 'display:none;border:1px solid;left:0;width:100%;height:300px;margin:5px 0;position:relative;-webkit-overflow-scrolling:touch;';
				element_wrap.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-21px;z-index:1" class="close_daum_juso" alt="접기 버튼">';
				jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_addr1+'"]').before(element_wrap);
				jQuery("#"+daum_pape_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e) {
					e.preventDefault();
					jQuery(this).parent().hide();
				});
			}

			daum.postcode.load(function() {
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
						element_wrap.style.display = 'none';			// iframe을 넣은 element를 안보이게 한다.
						document.body.scrollTop = currentScroll;	// 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.												
					},
					// 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분.
					onresize : function(size) {
						element_wrap.style.height = size.height + "px";		// iframe을 넣은 element의 높이값을 조정한다.
					},
					width : '100%',
					height : '100%'
				}).embed(element_wrap);
			});

			element_wrap.style.display = 'block';
			
		break;

		case 2 :	//새창으로 띄우기
			daum.postcode.load(function() {
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
					}
				}).open();
			});
		break;

		default :   //iframe을 이용하여 레이어 띄우기
			var rayer_id = 'daum_juso_rayer'+frm_zip,
			    element_layer = document.getElementById(rayer_id);

			if(element_layer == null) {
				element_layer = document.createElement("div");
				element_layer.setAttribute("id", rayer_id);
				element_layer.style.cssText = 'display:none;border:5px solid;position:fixed;width:300px;height:460px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;z-index:10000';
				element_layer.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" class="close_daum_juso" alt="닫기 버튼">';
				document.body.appendChild(element_layer);
				jQuery("#"+rayer_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e) {
					e.preventDefault();
					jQuery(this).parent().hide();
				});
			}

			daum.postcode.load(function() {
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
						element_layer.style.display = 'none';
					},
					width : '100%',
					height : '100%'
				}).embed(element_layer);
			});

			element_layer.style.display = 'block';

	}
}
</script>