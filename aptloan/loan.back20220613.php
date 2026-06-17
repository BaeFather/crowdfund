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
			<a href="javascript:void(0);" id="btn1" OnClick="check_w_form('wform',event);">대출한도 조회하기</a>
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