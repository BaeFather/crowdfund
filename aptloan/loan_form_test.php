<?
include_once('./_common.php');

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$strPost = ARRAY("price","rprice","si","gu","dong","rdo_apt","apt_name","apt_area","dong_num","ho_num","floor_num","apt_name2","pid");

FOR($i=0;$i<COUNT($strPost);$i++)
{
	${$strPost[$i]}  = clean_xss_tags($_POST[$strPost[$i]]);
}

$dongArr = EXPLODE(",",$dong);
$apt_nameArr = EXPLODE(",",$apt_name);
$apt_areaArr = EXPLODE(",",$apt_area);
?>
<link href="css/loan.css" rel="stylesheet">
<link href="S-Core-Dream-light/s-core-dream.css" rel="stylesheet">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="./aptloan.js?ver=2"></script>

<!-- 사이트 키 값 넣은 스크립트 추가 -->
<script src='https://www.google.com/recaptcha/api.js?render=6LdBq8UaAAAAAGHv6kxJ2lQ5gPcv5-e7E7scf9FR'></script> 

<!-- 본문내용 START -->

<!--------------------웹----------------------------------------------------------->



<div id="content">

	<div id="loan">
		<div class="loan1">
			<h2>대출한도조회 및 대출 신청</h2>
			<p>한도조회는 신용등급에 영향을 주지 않습니다.</p>
		</div>
		<div class="loan3">
			<ul>
				<li class="ball_03 regular">1</li>
				<li class="ball_04 regular">2</li>
			</ul>
		</div>

		<div class="result_loan">
			<div class="loan_form">
			<div class="loan_value">
		<?php IF($rdo_apt == "1") { ?>
		<?php IF(!$rprice || $rprice <= 0) { ?>

			<h3>대출 예상 한도 금액</h3>
			<p>대출 가능 한도가 없습니다</p>
		<?php } ELSE { ?>
			<h3>대출 예상 한도 금액</h3>
			<p><?php ECHO price_cutting($rprice);?>원</p>
		<?php } ?>
		<?php } ELSE { ?>
			<h3>대출 예상 한도 금액</h3>
			<p>대출신청시 안내 가능합니다.</p>
		<?php } ?>
			</div>

		<?php IF($rdo_apt == "1") { ?>
		<?php IF(!$rprice || $rprice <= 0) { ?>
			<table>
				<tr>
					<th class="first"><span class="del">아파트</span> 정보</th>
					<td class="first"><?php ECHO $si." ".$gu." ".$dongArr[1]." ".$apt_nameArr[1];?><br>
						<?php ECHO $dong_num;?>동 <?php ECHO $ho_num;?>호 (<?php ECHO $floor_num;?>층)</td>
				</tr>
				<tr>
					<th><span class="del">아파트</span> 면적</th>
					<td><?php ECHO $apt_areaArr[1];?>m²</td>
				</tr>
				<tr>
					<th>담보시세</th>
					<td><?php ECHO price_cutting($price);?>원 (KB시세 기준)</td>
				</tr>
				<tr>
					<th>가능금액</th>
					<td>0 원</td>
				</tr>
			</table>
		<?php } ELSE { ?>
			<table>
				<tr>
					<th class="first"><span class="del">아파트</span> 정보</th>
					<td class="first"><?php ECHO $si." ".$gu." ".$dongArr[1]." ".$apt_nameArr[1];?><br>
						<?php ECHO $dong_num;?>동 <?php ECHO $ho_num;?>호 (<?php ECHO $floor_num;?>층)</td>
				</tr>
				<tr>
					<th><span class="del">아파트</span> 면적</th>
					<td><?php ECHO $apt_areaArr[1];?>m²</td>
				</tr>
				<tr>
					<th>담보시세</th>
					<td><?php ECHO price_cutting($price);?>원 (KB시세 기준)</td>
				</tr>
				<tr>
					<th>가능금액</th>
					<td><?php ECHO price_cutting($rprice);?>원</td>
				</tr>
				<tr>
					<th>연이자율</th>
					<td>6%~</td>
				</tr>
				<tr>
					<th class="last">대출기간</th>
					<td class="last">최소 1개월부터 ~ </td>
				</tr>
			</table>
			<span class="result_info">선순위 대출 및 기타 개인의 신용에 따라 대출 한도는 조정될 수 있습니다.</span>
		<?php } ?>
		<?php } ELSEIF($rdo_apt == "2") { ?>
			<table>
				<tr>
					<th class="first"><span class="del">아파트</span> 정보</th>
					<td class="first"><?php ECHO $si." ".$gu." ".$dongArr[1]." ".$apt_name2;?></td>
				</tr>
				<tr>
					<th>담보시세</th>
					<td>KB시세 확인중</td>
				</tr>
			</table>
		<?php } ?>



		<?php
			$strRequest = true;
			IF($rdo_apt == "1")
			{
				IF(!$rprice || $rprice <= 0)
				{
					$strRequest = false;
				}
			}
		?>
		<?php IF($strRequest == false) { ?>
		<div class="fail_loan4">
			<p class="text">
				회원님이 선택하신 아파트의 경우 대출가능 한도가 없는 것으로 확인됩니다.<br>
				<span>선순위 대출이 한도를 초과</span>하였거나, <span>대출승인 지역이 아닌 경우</span> 한도가 조회 되지 않습니다.<br>
				다시 조회를 원하시거나 내용 수정이 필요하신 경우 아래 버튼을 클릭해주세요.
			</p>
		</div>

		<div class="btn3">
			<a href="/aptloan/loan2.php" id="btn3">다시조회하기</a>
		</div>

		<?php } ELSE { ?>

		<form name="regfm" id="regfm" action="atploan_process_test.php">

		<input type="hidden"	name="kind"			value="save" />
		<input type="hidden"	name="si"			value="<?php ECHO $si;?>" />
		<input type="hidden"	name="gu"			value="<?php ECHO $gu;?>" />
		<input type="hidden"	name="dong"			value="<?php ECHO $dong;?>" />
		<input type="hidden"	name="apt_name"		value="<?php ECHO $apt_name;?>" />
		<input type="hidden"	name="apt_name2"	value="<?php ECHO $apt_name2;?>" />
		<input type="hidden"	name="apt_area"		value="<?php ECHO $apt_area;?>" />
		<input type="hidden"	name="price"		value="<?php ECHO $price;?>" />
		<input type="hidden"	name="dong_num"		value="<?php ECHO $dong_num;?>" />
		<input type="hidden"	name="ho_num"		value="<?php ECHO $ho_num;?>" />
		<input type="hidden"	name="floor_num"	value="<?php ECHO $floor_num;?>" />
		<input type="hidden"	name="rprice"		value="<?php ECHO $rprice;?>" />
		<input type="hidden"	name="rmount_or"	value="" />
		<input type="hidden"	name="rdo_apt"		value="<?php ECHO $rdo_apt;?>" />
		<input type="hidden"	name="pid"			value="<?php ECHO $pid;?>" />

		<div class="re_loan4">
			<?php IF($rdo_apt == "1") { ?>
			<p class="title">대출신청 <span class="info">*대출가능 금액은 <?php ECHO price_cutting($rprice);?>원입니다.</span></p>
			<?php } ?>
			<ul>
				<li class="td sum"><input class="loansum2" type="text" name="ramount" value="" placeholder="대출신청 금액을 입력해주세요" required itemname='대출신청 금액' onKeyUp="onlyDigit(this);NumberFormatHan(this,'wamt_txt');"><div class="won">만원</div>
				<!--<li class="td sum"><input class="loansum2" type="text" name="ramount" value="" placeholder="대출신청 금액을 입력해주세요" required itemname='대출신청 금액' OnKeyUp="fn_ramount(this.value);NumberFormatHan(this,'wamt_txt');"><div class="won">만원</div>-->
				<div style="text-align:right;padding-right:40px;" id="wamt_txt"></div>
				</li>
			</ul>
			<ul>
				<li><input class="name" type="text" name="rname" value="" required itemname='이름'  placeholder="이름을 입력해주세요"></li>
				<li><input class="tel" type="text" name="rphone" value="" placeholder="연락처를 '-' 없이 입력해주세요"  required itemname='연락처'  onKeyUp="onlyDigit(this);"></li>
				<!--<li><input class="tel" type="text" name="rphone" value="" placeholder="연락처를 '-' 없이 입력해주세요"  required itemname='연락처'  OnKeyUp="fn_check_number('rphone',this.value);"></li>-->
			</ul>
			<ul>
				<li class="re_label_check"><label><input type="checkbox" name="check01" id="check01" value="Y" required itemname='개인정보 수집 및 이용'><span>개인정보 수집 및 이용에 동의합니다.</span></label></li>
			</ul>
				</li>
			</ul>
		</div>

		<div class="btn2">
			<a href="javascript:void(0);" id="btn2" OnClick="check_request_form('regfm',event);">대출신청하기</a>
<?
if ($_SERVER["REMOTE_ADDR"]=="220.117.134.247" or $_SERVER["REMOTE_ADDR"]=="220.117.134.166") {
		?>
			<br/>
			<a href="javascript:void(0);" id="btn2" OnClick="check_request_form2('regfm',event);">대출신청하기2</a>
			
			<script>
			function check_request_form2(fmname, event) {



				grecaptcha.ready(function() {
					grecaptcha.execute('6LdBq8UaAAAAAGHv6kxJ2lQ5gPcv5-e7E7scf9FR', {action: 'submit'}).then(function(token) {
						
						$('#'+fmname).prepend('<input type="text" name="g-recaptcha-response" value="' + token + '">');
						
						//alert(token);
						
						var frm = $('#'+fmname);
						var str = frm.serialize();
						
						
						if(!event)
						{
						   event = window.event;
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

						var rdo_apt = $("input[name='rdo_apt']").val();

						if(rdo_apt == "1")
						{
							var rprice = $("input[name='rprice']").val() / 10000;
							var ramount = $("input[name='ramount']").val().replace(/,/gi,"");

							if(parseInt(rprice) < parseInt(ramount))
							{
								alert("대출신청금액은 대출가능금액을 초과할 수 없습니다.");
								return false;
							}
						}

						

						$.ajax({
							type : 'POST',
							url : 'atploan_process_test.php',
							data : str,
							dataType: 'json',
							success : function(data){
							
								console.log(data);
								
								if(data.retcode == "OK"){
									 check_request_end();
									 //var stralert = decodeURIComponent(data.retalert);
									 //alert(stralert.replace("+"," "));
									 window.location = data.retval;

								} else if(data.retcode == "X") {
									var stralert = decodeURIComponent(data.retalert);
										alert(stralert.replace("+"," "));

								}
								
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
								alert("처리중 오류가 발생하였습니다. 다시 시도하여 주십시오.");
								console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
								console.log(errorThrown);
								return false;
							}
						});	
				
					});
				});	
				

			}
			</script>
		<?
}
?>
		</div>
		</form>
		<?php } ?>

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
