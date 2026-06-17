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
	check_form_proc("kind=si", event);
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

		<form name="wform" id="wform">
		<input type="hidden" name="kind" value="auth" />
		<input type="hidden" name="price" value="" />
		<input type="hidden" name="rprice" value="" />
		<input type="hidden" name="pid" value="<?php ECHO $strP;?>" />


		<div class="loan4">
			<div class="loan_form">
			<div class="title"><p class="step">지역정보</p></div>
			<ul>
				<li class="step1">
					<select id="si" name="si" class="citys" OnChange="check_form_send('gu',this.value);" required itemname="광역시/도를 선택하여 주세요">
						<option value="">광역시/도 선택</option>
					</select>
				</li>
				<li class="step1">
					<select id="gu" name="gu" class="gus" OnChange="check_form_send('dong',this.value);" required itemname="시/구 를 선택하여 주세요">
						<option value="">시/구 선택</option>
					</select>
				</li>
				<li class="step3">
					<select id="dong" name="dong" class="dongs" OnChange="check_form_send('apt_name',this.value);" required itemname="동을 선택하여 주세요">
						<option value="">동 선택</option>
					</select>
				</li>
			</ul>



			<div class="title"><p class="step">아파트정보</p></div>

			<ul class="radiobt1">
				<li><label class="apt_radio"><input type="radio" name="rdo_apt" value="1" onclick="Rradio_OnOff('Radio_On');" checked><span>선택</span></label></li>
				<li class="pd"><label class="apt_radio"><input type="radio" name="rdo_apt" value="2" onclick="Rradio_OnOff('Radio_Off');"><span>직접입력</span></label></li>
			</ul>

			<ul id="Radio_On" style="display:;">
				<li class="step2 clear">
					<select id="apt_name" name="apt_name" class="b_names" OnChange="check_form_send('apt_area',this.value);">
						<option value="">아파트 선택</option>
					</select>
				</li>
				<li class="step3">
					<select id="apt_area" name="apt_area" class="areas">
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
			<a href="javascript:void(0);" id="btn1" OnClick="check_w_form2('wform',event);">대출한도 조회하기</a>

			<script type="text/javascript">
				function check_w_form2(fmname, event)
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

					  var checkform = check_form(fmname);

						if(checkform == false)
						{
							  return false;
						}

						var rdo_apt = $("input[name='rdo_apt']:checked").val();
						var apt_name = $("select[name='apt_name']").val();
						var apt_area = $("select[name='apt_area']").val();
						var dong_num = $("input[name='dong_num']").val();
						var ho_num = $("input[name='ho_num']").val();
						var floor_num = $("input[name='floor_num']").val();
						var apt_name2 = $("input[name='apt_name2']").val();

						if(rdo_apt == "1")
					  {
							 if(!apt_name) { alert('아파트를 선택해주세요'); return false; }
							 if(!apt_area) { alert('전용면적을 선택해주세요'); return false; }
							 if(!dong_num) { alert('동을 입력해주세요'); return false; }
							 if(!ho_num) { alert('호수를 입력해주세요'); return false; }
							 if(!floor_num) { alert('층수를 입력해주세요'); return false; }
						} else if (rdo_apt == "2") {
							 if(!apt_name2) { alert('아파트명 및 상세주소를 입력해주세요'); return false; }
					  }

						var frm = $('#'+fmname);
						var str = frm.serialize();

						$.ajax({
							type : 'POST',
							url : 'atploan_process_test.php',
							data : str,
							dataType: 'json',
							success : function(data){

								if(data.retcode == "OK"){
									$("input[name='price']").val(data.retprice);
									$("input[name='rprice']").val(data.retrprice);
									$("#wform").attr("method","POST");
									$("#wform").attr("action",data.retval);
									$("#wform").submit();

								} else if(data.retcode == "X") {
									var stralert = decodeURIComponent(data.retalert);
										alert(stralert.replace("+"," "));

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
	
			</script>
		
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
		<div class="m_call">
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