<?php
include_once('./_common.php');

$EVENT = sql_fetch("SELECT * FROM invitation_event WHERE idx='2' AND cancel='N'");
if(!$EVENT['idx']) {
	echo "<script>alert('진행중인 이벤트가 아닙니다.');location.replace('/');</script>";
	exit;
}

$g5['title'] = $EVENT['title'];
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
			<img src="../images/invest_request/top_img01_m.jpg" width="100%">
		</div>
		<div style="width:100%; margin:0 auto; background:url('/images/invest_request/center_bg01_m.jpg') repeat-y center top; background-size:100%;">
			<div style="text-align:center;padding:30px 0 20px 0;"><img src="../images/invest_request/img01_m.jpg" width="80%"></div>
			<ul style="width:85%;margin:0 auto;border-top:2px solid #0e2974;">
				<li style="padding:10px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:16px;color:#000;">업체명</span>
					<span style="padding-left:25px;"><input type="text" id="nm_co_name" required style="ime-mode:active;width:63%;height:40px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span>
				</li>
				<li style="padding:10px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:16px;color:#000;">담당자명</span>
					<span style="padding-left:10px;"><input type="text" id="nm_name" required style="ime-mode:active;width:63%;height:40px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span>
				</li>
				<li style="padding:10px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:16px;color:#000;">연락처</span>
					<span style="padding-left:23px;">
						<input type="text" id="nm_phone1" onKeyUp='onlyDigit(this);' maxlength="3" required style="ime-mode:disabled;width:21%;height:40px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
						<input type="text" id="nm_phone2" onKeyUp='onlyDigit(this);' maxlength="4" required style="ime-mode:disabled;width:21%;height:40px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
						<input type="text" id="nm_phone3" onKeyUp='onlyDigit(this);' maxlength="4" required style="ime-mode:disabled;width:21%;height:40px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
					</span>
				</li>
			</ul>
			<div style="text-align:center;padding:20px 0;"><img id="request_button" src="../images/invest_request/btn01_m.jpg" width="50%" style="cursor:pointer;"></div>
		</div>
		<div style="width:100%; margin:0 auto;">
			<img src="../images/invest_request/bottom_bg01_m.jpg" width="100%">
		</div>

	</div>
<? } else { ?>
  <div style="width:80%; padding:40px 10% 40px 10%;">

		<div style="width:773px; margin:0 auto;">
			<img src="../images/invest_request/top_img01.jpg" >
		</div>
		<div style="width:773px; margin:0 auto; background:url('/images/invest_request/center_bg01.jpg') repeat-y center top;">
			<div style="text-align:center;padding:40px 0 20px 0;"><img src="../images/invest_request/img01.jpg"></div>
			<ul style="width:600px;margin:0 auto;border-top:2px solid #0e2974;">
				<li style="padding:15px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:18px;color:#000;">업체명</span>
					<span style="padding-left:50px;"><input type="text" id="nm_co_name" required style="ime-mode:active;width:253px;height:45px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span>
				</li>
				<li style="padding:15px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:18px;color:#000;">담당자명</span>
					<span style="padding-left:33px;"><input type="text" id="nm_name" required style="ime-mode:active;width:253px;height:45px;border-radius:5px;border:1px solid #50b0c9;padding-left:10px;color:#000;font-size:16px;"></span>
				</li>
				<li style="padding:15px 0;border-bottom:1px solid #50b0c9;">
					<span style="font-size:18px;color:#000;">연락처</span>
					<span style="padding-left:48px;">
						<input type="text" id="nm_phone1" onKeyUp='onlyDigit(this);' maxlength="3" required style="ime-mode:disabled;width:123px;height:45px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;">
						<input type="text" id="nm_phone2" onKeyUp='onlyDigit(this);' maxlength="4" required style="ime-mode:disabled;width:123px;height:45px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;margin-left:10px;">
						<input type="text" id="nm_phone3" onKeyUp='onlyDigit(this);' maxlength="4" required style="ime-mode:disabled;width:123px;height:45px;border-radius:5px;border:1px solid #50b0c9;text-align:center;color:#000;font-size:16px;margin-left:10px;">
					</span>
				</li>
			</ul>
			<div style="text-align:center;padding:20px 0;"><img id="request_button" src="../images/invest_request/btn01.jpg" style="cursor:pointer;"></div>
		</div>
		<div style="width:773px; margin:0 auto;">
			<img src="../images/invest_request/bottom_bg01.jpg">
		</div>

	</div>
<? } ?>

</div>

<script>
$('#request_button').on('click', function(){
	if($('#nm_co_name').val()=='') { alert('업체명을 입력하여 주십시요!'); $('#nm_co_name').focus(); }
	else if($('#nm_name').val()=='') { alert('담당자명을 입력하여 주십시요!'); $('#nm_name').focus(); }
	else if($('#nm_phone1').val()=='') { alert('연락처(국번)를 입력하여 주십시요!'); $('#nm_phone1').focus(); }
	else if($('#nm_phone2').val()=='') { alert('연락처를 입력하여 주십시요!'); $('#nm_phone2').focus(); }
	else if($('#nm_phone3').val()=='') { alert('연락처를 입력하여 주십시요!'); $('#nm_phone3').focus(); }
	else {

		var nm_phone = $('#nm_phone1').val() + '-' + $('#nm_phone2').val() + '-' + $('#nm_phone3').val();

		$.ajax({
			url : "./ajax_invitation.php",
			type: "post",
			data: {
				event_idx:'2',
				nm_co_name: $('#nm_co_name').val(),
				nm_name: $('#nm_name').val(),
				nm_phone: nm_phone
			},
			success: function(data){
				if(data=="wait") {
					alert("준비중인 이벤트 입니다.");
				}
				else if(data=="finished") {
					alert("이미 종료된 이벤트 입니다.");
				}
				else if(data=="1"){
					alert("참가 신청 되었습니다. 감사합니다.");
				}
				else if(data=="2") {
					alert("이미 신청 처리 되었습니다..");
				}
				else {
					alert("시스템 에러입니다. 관리자에게 문의해주세요.");
				}
			},
			error: function () {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			}
		});
	}
});
</script>


<!-- 본문내용 E N D -->
<?php

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>