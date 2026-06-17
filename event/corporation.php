<?php

if( !preg_match('/220\.117\.134/', $_SERVER['REMOTE_ADDR']) and !preg_match('/211\.211\.50\.106/', $_SERVER['REMOTE_ADDR'])) {
	header("Location: /");
}

include_once('./_common.php');


$g5['title'] = "헬로법인설립안내센터";
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');


?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><b class="blue"><?=$g5['title']?></b></div>

<? if(G5_IS_MOBILE) { ?>

	<div style="width:96%; padding:10px 2% 10px 2%;" >
		<div style="width:100%; margin:0 auto;">
		  <p><img src="../images/investment/corporation_m.jpg" width="100%"></p>

			<p style="background:url('../images/investment/bottom_bg03.jpg') no-repeat; background-size:100%;min-height:250px;text-align:center;background-color:#d6deeb;">
				<form id="counsel_request" name="counsel_request" style="width:100%;">

				<ul id="new1" style="display:none;font-size:14px;margin:-205px 0 0 0;text-align:center;">
					<p style="padding-bottom:10px;"><img src="../images/investment/tit001.jpg" height="22"></p>
					<li style="display:inline-block;">
						<input type="text" id="schedule_req_date" name="schedule_req_date" placeholder="날짜선택" readonly style="width:100px;height:30px;font-size:14px;text-align:center;border-radius:3px;border:1px solid #4b71be;">
					</li>
					<li style="display:inline-block;padding:0 0 0 5px;"><img id="schedule_req_date_img" src="/images/investment/cal_btn01.png" height="24" style="cursor:pointer"></li>
					<li style="display:inline-block;padding-left:20px;">
						<select id="schedule_req_time" name="schedule_req_time" style="height:32px;font-size:14px;border-radius:3px;border:1px solid #4b71be;">
							<option value="10">오전 10시</option>
							<option value="11">오전 11시</option>
							<option value="12">오후 12시</option>
							<option value="13">오후 1시</option>
							<option value="14">오후 2시</option>
							<option value="15">오후 3시</option>
							<option value="16">오후 4시</option>
							<option value="17">오후 5시</option>
							<option value="18">오후 6시</option>
						</select>
					</li>

					<div style="margin-top:15px;clear:both;font-size:14px;text-algin:center;">
					  <input type="checkbox" id="use_agree" checked> 개인정보 이용에 동의합니다.
					  <span id="btn3" style="cursor:pointer;border-radius:2px;font-size:14px;padding:3px 5px;background-color:#838383;color:#fff;">자세히 보기</span>
					</div>

					<div style="cursor:pointer; margin:15px auto 0;display:block;text-align:center;"><img id="submit_button" src="../images/investment/btn_img003.jpg" height="40"></div>
					<p style="padding-top:10px;padding-bottom:16px;"><img src="../images/investment/tit002.jpg" height="25"></p>
				</ul>

				<div id="agree" style="position:absolute;margin:-223px 0 0 0;background-color:#fff;line-height:20px;width:86%;padding:15px 15px;border:3px solid #1248b5;display:none;">
					<strong style="font-size:18px;display:inline-block;width:72%;">개인정보 이용에 대한 동의</strong>
					<strong id="close" style="display:inline-block;width:26%;cursor:pointer;text-align:right;"><b style="font-size:16px;padding:3px 8px;background-color:#1248b5;color:#fff;">X</b></strong><br/><br/>

					   1. 개인정보 이용목적<br/>
						  회원가입시 등록된 휴대폰을 통한 헬로법인설립안내<br/>센터상담<br/><br/>

					   2. 이용하는 개인정보 항목<br/>
						  성명, 휴대폰번호<br/><br/>

					   3. 정보이용기간<br/>
						  헬로법인설립안내센터 관련 상담 종료시까지
				</div>
				<script type="text/javascript">
				  $('#btn3').click(function(){
					$('#agree').css('display','block');
				  });
				</script>
				<script type="text/javascript">
				  $('#close').click(function(){
					$('#agree').css('display','none');
				  });
				</script>

				<div id="btn1" style="margin-top:-200px;padding-bottom:20px;cursor:pointer;display:block;text-align:center;">
					<p style="padding-bottom:10px;"><img src="../images/investment/tit001.jpg" height="22"></p>
					<p><img src="../images/investment/btn_img003.jpg" height="50"></p>
					<p style="padding-top:10px;"><img src="../images/investment/tit002.jpg" height="25"></p>
				</div>
				<script type="text/javascript">
				  $('#btn1').click(function(){
					$('#btn1').css('display','none');
					$('#new1').css('display','block');
				 });
				</script>
				</form>
			</p>
			<p><a href="tel:02-3453-6476"><img src="../images/investment/bottom_bg02.jpg" width="100%"></a></p>
		</div>
	</div>

<? } else { ?>

	<div style="width:80%; padding:10px 10% 10px 10%;">
		<div style="width:773px; margin:0 auto;">
			<p><img src="../images/investment/corporation.jpg" width="100%"></p>
			<p style="background:url('../images/investment/bottom_bg01.jpg') no-repeat; width:100%; height:292px;">
				<form id="counsel_request" name="counsel_request">
				<ul id="new2" style="position:relative;top:-255px;left:100px;font-size:16px;display:none;">
					<li style="float:left;">
						<input type="text" id="schedule_req_date" name="schedule_req_date" placeholder="날짜선택" readonly style="width:100px;height:30px;right:5px;font-size:14px;text-align:center;border-radius:3px;border:1px solid #4b71be;">
					</li>
					<li style="float:left;padding:3px 0 0 5px;"><span id="schedule_req_date_img" style="cursor:pointer;"><img src="/images/investment/cal_btn02.png" ></span></li>
					<li style="float:left;padding:0 0 40px 20px;">
						<select id="schedule_req_time" name="schedule_req_time" style="height:32px;font-size:16px;border-radius:3px;border:1px solid #4b71be;">
							<option value="10">오전 10시</option>
							<option value="11">오전 11시</option>
							<option value="12">오후 12시</option>
							<option value="13">오후 1시</option>
							<option value="14">오후 2시</option>
							<option value="15">오후 3시</option>
							<option value="16">오후 4시</option>
							<option value="17">오후 5시</option>
							<option value="18">오후 6시</option>
						</select>
					</li>

					<div style="position:absolute;left:-15px;margin-top:45px;clear:both;font-size:14px;">
					  <input type="checkbox" id="use_agree" checked> 개인정보 이용에 동의합니다.
					  <span id="btn3" style="cursor:pointer;border-radius:2px;font-size:14px;padding:3px 5px;background-color:#838383;color:#fff;">자세히 보기</span>
					</div>

					<div style="cursor:pointer;margin:30px 0 0 18px;width:206px;height:40px;display:block;"><img id="submit_button" src="/images/investment/btn_img001.jpg"></div>
				</ul>
				<div id="btn2" style="position:relative;top:-255px;left:65px;cursor:pointer;margin:41px 0 0 18px;width:206px;height:40px;display:block;"><img src="/images/investment/btn_img002.jpg"></div>


				<div id="agree" style="position:absolute;margin:-223px 0 0 275px;background-color:#fff;line-height:20px;padding:20px 20px;border:3px solid #1248b5;display:none;">
					<strong style="font-size:18px;">개인정보 이용에 대한 동의</strong>
					<strong id="close" style="margin-left:150px;cursor:pointer;font-size:16px;padding:3px 8px;background-color:#1248b5;color:#fff;">X</strong><br/><br/>

					   1. 개인정보 이용목적<br/>
						  회원가입시 등록된 휴대폰을 통한 헬로법인설립안내센터 상담<br/><br/>

					   2. 이용하는 개인정보 항목<br/>
						  성명, 휴대폰번호<br/><br/>

					   3. 정보이용기간<br/>
						  헬로법인설립안내센터 관련 상담 종료시까지
				</div>
				<script type="text/javascript">
				  $('#btn3').click(function(){
						$('#agree').css('display','block');
				  });
				</script>
				<script type="text/javascript">
				  $('#close').click(function(){
						$('#agree').css('display','none');
				  });
				</script>

				</form>
				<script type="text/javascript">
				  $('#btn2').click(function(){
						$('#btn2').css('display','none');
						$('#new2').css('display','block');
				 });
				</script>
			</p>
		</div>
	</div>

<? } ?>

</div>

<script>
$(function() {
	$("#schedule_req_date").datepicker({
		dateFormat: "yy-mm-dd",
		monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
		monthNamesShort : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
		dayNames : ['일', '월', '화', '수', '목', '금', '토'],
		dayNamesShort : ['일', '월', '화', '수', '목', '금', '토'],
		dayNamesMin : ['일', '월', '화', '수', '목', '금', '토']
	});
});

$('#schedule_req_date_img').click(function() {
	$("#schedule_req_date").focus();
});

$('#submit_button').click(function() {
<? if($is_member) { ?>
	f = document.counsel_request;

	var use_agree = ($("input:checkbox[id='use_agree']").is(':checked')) ? 'Y' : 'N';

	if(f.schedule_req_date.value=='') {
		alert('상담 신청 일자를 선택 하여 주십시요.');
		$("#schedule_req_date").focus();
	}
	else if( use_agree != 'Y' ) {
		alert('개인정보 이용에 동의 하신 후 신청 하여 하십시요.');
		$("#use_agree").focus();
	}
	else {
		if(confirm('상담 신청을 등록 하시겠습니까?')) {
			ajax_data = $("#counsel_request").serialize();
			$.ajax({
				url : "./ajax_corporation_proc.php",
				type: "POST",
				data : ajax_data,
				success: function(data, textStatus, jqXHR){
					if(data == '1') {
						alert('등록 되었습니다');
					}
					else if(data == 'X01') {
						alert('로그인 하십시요.'); $(location).attr('href', '/bbs/login.php?url=<?=urlencode($_SERVER['PHP_SELF'])?>');
					}
					else if(data == 'X02') {
						alert('본 이벤트는 개인회원 전용 이벤트입니다.');
					}
					else if(data == 'X03') {
						alert('이미 등록된 요청이 있습니다.');
					}
					else {
						alert('시스템 에러입니다. 고객센터로 문의 하십시요.');
					}
				},
				error: function (jqXHR, textStatus, errorThrown)	{
					alert('네트워크 에러입니다. 잠시 후 다시 시도하십시요.');
				}
			});
		}
	}
<? } else { ?>
	alert('로그인 이후 신청 가능합니다.');
	$(location).attr('href', '/bbs/login.php?url=<?=urlencode($_SERVER['PHP_SELF'])?>');
<? } ?>
});
</script>

<!-- 본문내용 E N D -->
<?php

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>