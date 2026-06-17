<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
.selectarea {width:180px;padding:5px 0;}
label {float:left;display:block;padding:5px 5px;}
.fred {color:#ff0000;}
.tdC {text-align:center;}
.circleArea {position:absolute;margin-left:0px;background-color:#0000ff;border-radius:30px;color:#FFF;font-weight:bold;width:20px;height:20px;border:0px;cursor:pointer;}
.input01 {width:100%;border-radius:3px;line-height:24px;font-size:14px;text-align:left;border:1px solid #333;}
.input02 {width:95%;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:0 auto;}
.input02::placeholder {text-align:center;}
.input02_ {width:250px;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:0 auto;}
.input02__ {width:250px;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:5px 0px auto;}
.input04 {width:98%;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:0 auto;}
.input05 {width:60px;line-height:24px;font-size:15px;text-align:center;border:1px solid #CCC;}
.input06 {width:100px;line-height:24px;font-size:15px;text-align:center;border:1px solid #CCC;margin:0 auto;}
.input07 {width:200px;line-height:24px;font-size:15px;text-align:center;border:1px solid #CCC;margin:0 auto;}
.input08 {width:400px;line-height:24px;font-size:15px;text-align:center;border:1px solid #CCC;margin:0 auto;}

.input09{width:240px;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:0 auto;}
.input09_{width:240px;line-height:24px;font-size:15px;text-align:left;border:1px solid #CCC;margin:5px 0px auto;}
.tR {text-align:right;}
.tC {text-align:center;}
.tL {text-align:left;}
.select01 {width:95%;line-height:24px;font-size:15px;padding:3px 0;}
.select03 {width:150px;line-height:24px;font-size:15px;padding:3px 0;}
.tdC {text-align:center;}
.select02 {width:100px;line-height:24px;font-size:15px;padding:3px 0;margin-right:10px;}
.text01 {width:100%;height:70px;font-size:15px;}

.smenu_guide {width:1000px;overflow:hidden;margin:20px 0px;list-style:none;padding:0px;}
.smenu_guide > li.sli {float:left; width:20%;border-top:1px solid #CCC; border-bottom:1px solid #333; border-left:1px solid #333;text-align:center;padding:15px 0;font-size:15px;cursor:pointer;}
.smenu_guide > li.active {background-color:#0000ff;color:#FFF;}
.smenu_guide > li:last-child {border-right:1px solid #333;}
.write_detail_area {width:1000px;margin:30px 0px;}
.content_w1_table tr td {text-align:center;}
.fb {font-weight:bold;}
.fred {color:#FF0000;}
.btn_calc {padding:0px 20px;border:1px solid #CCC;background-color:#333;color:#fff;line-height:30px;height:30px;border-radius:7px;}

.gtable {width:1600px;overflow:hidden;margin:0px;list-style:none;padding:0px;}
.gtable td.gtd1{width:1000px;vertical-align:top;}
.gtable td.gtd2{width:300px;vertical-align:top;text-align:center;}
.gtable td.gtd3{width:250px;vertical-align:top;text-align:center;}

.gtable .comment_btn {width:100%;height:40px;margin:10px auto;}
</style>
	<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<ul class="smenu_guide">
		<li class="sli<?php IF(!$SD || $SD=="1") { ECHO " active"; } ?>" OnClick="window.location='<?php ECHO $strListUrl;?>&RD=2&SD=1&idx=<?php ECHO $idx;?>'">기본정보</li>
		<li class="sli<?php IF($SD=="2") { ECHO " active"; } ?>""  OnClick="window.location='<?php ECHO $strListUrl;?>&RD=2&SD=2&idx=<?php ECHO $idx;?>'">원차주 정보</li>
		<li class="sli<?php IF($SD=="3") { ECHO " active"; } ?>""  OnClick="window.location='<?php ECHO $strListUrl;?>&RD=2&SD=3&idx=<?php ECHO $idx;?>'">상품 상세정보</li>
	</ul>

	<table class="gtable">
	<tr>
		<td class="gtd1">
	<?php INCLUDE_ONCE($strSubUrl); ?>
		</td>
		<td class="gtd2">
	<?php IF($RD == "2") { ?>
	<!-- 코멘트 //-->
	<div style="margin:20px auto; max-width:280px;">
		<h4>코멘트 & 로그</h4>
		<ul class="list-inline" style="margin-bottom:20px;">
			<li style="width:100%;height:80px"><textarea id="comment" style="width:100%;height:100%;" required></textarea></li>
			<li style="width:100%"><button type="button" id="frmCmtSubmit" class="btn btn-primary comment_btn">등 록</button></li>
		</ul>
		<script>
		$('#frmCmtSubmit').click(function() {
			if( $('#comment').val()=='' ) {
				alert('내용을 입력하십시요.');  $('#comment').focus();
			}
			else {
				$.ajax({
					url : "request.proc.ajax.php",
					type: "POST",
					dataType: "JSON",
					data:{
						mode: 'new',
						idx: '<?=$idx?>',
						comment: $('#comment').val()
					},
					success:function(data) {
						if(data.result=='SUCCESS') { window.location.reload(); }
						else { console.log(result); }
					},
					error: function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
		});
		</script>


<?php
$cres  = sql_query("SELECT idx, writer, mb_id , comment, regdate FROM hloan_comment_renew WHERE req_idx='".add_str($idx)."' ORDER BY idx DESC");
$crows = $cres->num_rows;
if($crows) {
	for($c=0,$cno=1; $c<$crows; $c++,$cno++) {
		$CROW = sql_fetch_array($cres);
		$delete_tag = "";
		if(($CROW['mb_id']==$member['mb_id']) || $member['mb_level']=='10') {
			$delete_tag = "<span onClick='dropComment(".$CROW['idx'].")' style='cursor:pointer;color:red'>×</span>";
		}

		$comm = nl2br(htmlSpecialChars($CROW['comment']));

?>
		<table style="font-size:12px">
			<colgroup>
				<col width="280">
				<col width="20">
			</colgroup>
			<tr style='background:#FAFAFA'>
				<td align="left">
				<span style="color:#aaa"><?=$CROW['regdate']?></span><br />
				<?=$CROW['writer']?> (<?php ECHO $CROW["mb_id"]?>)</td>
				<td align="center"><?=$delete_tag?></td>
			</tr>
			<tr>
				<td colspan="3" style="padding:8px 7px;text-align:left;"><?=$comm?></td>
			</tr>
		</table>
		<div style="height:10px;"></div>
<?php
		}
?>
<?php
	}
}
?>



		</td>
		<td class="gtd3">
		<style>
		.sms_info { margin:10px auto; padding:15px; }
		.sms_info div { margin-bottom:25px; }
		.sms_area { width:230px; display:inline-block; margin:10px auto; padding:15px; }
		.sms_msg textarea { border:1px solid #EEEEEE; width:200px; height:150px;font-size:12px; }
		.sms_title { text-align:center; font-weight:bold; color:#FFFFFF; padding:10px; background-color:#3C5B9B; border-radius:3px 3px 0 0; }
		.sms_use { padding-top:10px; text-align:center; }
		.sms_error { color:red; }

		.roundbox { width:100%;list-style:none;padding:9px;clear:both; display:inline-block; border:1px dotted #555; border-radius:5px;background-color:#FDFECB; }
		</style>
				<div style="display:inline-block;width:235px;border:1px solid #aaa;background:#fafafa">
					<div class="sms_area pull-left">
						<div class="sms_title">SMS 문자전송</div>
						<div class="sms_msg"><textarea rows="20" name="sms_msg" id="sms_msg" placeholder="메세지 내용을 입력해주세요"></textarea></div>
						<div class="sms_error" id="msg_err"></div>
					</div>
					<div class="sms_info">
						<div>발신번호 : <input type="text" class="frm_input" id="from_hp" value="15885210" readonly style="width:120px"><span class="sms_error" id="from_hp_err"></span></div>
						<div>수신번호 : <input type="text" class="frm_input" id="to_hp" value="<?=$lenphone?>" style="width:120px"><span class="sms_error" id="to_hp_err"></span></div>
						<div>발송시간 : <select id="send_time" onChange="check_sms_time(this.value);" class="frm_input" style="width:120px">
								<option value="d">즉시발송</option>
								<option value="r">예약발송</option>
							</select>
							<span id="send_t_area" style="display:none;">
								<input type="text" class="frm_input datepicker" id="send_ymd" size="10" value="" placeholder="날짜선택">
								<select id="send_h" class="frm_input">
								<?
								for($i=0; $i<=23; $i++) {
									if(strlen($i) == 1) {
										$j = '0'.$i;
									} else {
										$j = $i;
									}
									echo '<option value='.$j.'>'.$j.'시</option>';
								}
								?>
								</select>
								<select id="send_i" class="frm_input">
								<?
								for($i=0; $i<=59; $i++) {
									if(strlen($i) == 1) {
										$j = '0'.$i;
									}else {
										$j = $i;
									}
									echo '<option value='.$j.'>'.$j.'분</option>';
								}
								?>
								</select>
								<span class="sms_error" id="send_time_err"></span>
							</span>
						</div>
						<div>
							<button type="button" class="btn-info btn btn-lg" onClick="sms_send();" style="width:100%;">SMS 전송</button>
							<span class="sms_error" id="sms_result"></span>
						</div>
					</div>
				</div>
		</td>
	</table>
	<!-- 코멘트 //-->

<script>
	// 문자발송
function sms_send() {
	var msg       = $('#sms_msg').val();
	var from_hp   = $('#from_hp').val();
	var to_hp     = $('#to_hp').val();
	var send_time = $('#send_time').val();
	var send_ymd  = $('#send_ymd').val();
	var send_h    = $('#send_h').val();
	var send_i    = $('#send_i').val();

	var chk1;
	var chk2;
	var chk3;
	var chk4;

	if(msg == '') {
		$('#msg_err').html('메세지 내용을 입력해주세요.');
		chk1 = 'N';
	}
	else {
		$('#msg_err').html('');
		chk1 = 'Y';
	}

	if(from_hp == '') {
		$('#from_hp_err').html('발신번호를 입력해주세요.');
		chk2 = 'N';
	}
	else {
		$('#from_hp_err').html('');
		chk2 = 'Y';
	}

	if(to_hp == '') {
		$('#to_hp_err').html('수신번호를 입력해주세요.');
		chk3 = 'N';
	}
	else {
		$('#to_hp_err').html('');
		chk3 = 'Y';
	}

	if(send_time == 'r' && send_ymd == '') {
		$('#send_time_err').html('예약발송 시간을 설정해주세요.');
		chk4 = 'N';
	}
	else {
		$('#send_time_err').html('');
		chk4 = 'Y';
	}

	if(chk1 == 'Y' && chk2 == 'Y' && chk3 == 'Y' && chk4 == 'Y') {
		$.ajax({
			url:'/adm/member/member_sms_proc.php',
			type:'POST',
			data:{
				'from_hp':from_hp,
				'to_hp':to_hp,
				'send_msg':msg,
				'send_time':send_time,
				'send_ymd':send_ymd,
				'send_h':send_h,
				'send_i':send_i
			},
			dataType: 'text',
			success: function(data) {
				if(trim(data) == '1') {
					$('#sms_result').html('SMS 전송이 성공하였습니다.');
				}else {
					$('#sms_result').html('DB오류로 인해 발송이 실패하였습니다.');
				}
			}
		});
	}
}


// 폼 리셋
function fmember_reset() {
	$("form")[0].reset();
}
</script>