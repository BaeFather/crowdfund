<?
include_once("_common.php");

while( list($k,$v) = each($_REQUEST) ) { ${$k} = trim($v); }

if($hcseq || $judamType) {		// ... 주담대 채권관리 또는 온라인 주담대 신청에서 넘어온 데이터
	$from_hp = $CONF['judam_sms_number'];			// 주담대 상담번호
}
else {
	$from_hp = $CONF['admin_sms_number'];			// 고객센터 대표번호
}

$to_hp = preg_replace('/-/', '', $to_hp);

?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
<title>헬로펀딩</title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?ver=20180826">
<link rel="stylesheet" type="text/css" href="/adm/css/admin.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="/adm/css/bootstrap.min.css">
<!--[if lte IE 8]>
<script src="/js/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/common.js?v=20200619"></script>
<script type="text/javascript" src="/adm/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script src="/adm/admin.js"></script>
<script>
$(function(){
	$(".datepicker").datepicker({
		dateFormat      : 'yy-mm-dd',
		changeYear      : true,
		changeMonth     : true,
		monthNamesShort : ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin     : ['일' ,'월', '화', '수', '목', '금', '토']
	});
});
</script>

<style>
/*
.sms_wrap {position: fixed; left: 1500px; z-index: 10; top: 30%; border: 1px solid #eee;}
.sms_wrap .sms_area {width: 100%; padding: 7% 5%;}
.sms_wrap .sms_title {background-color: #3C5B9B; color: #fff; font-weight: 700; padding: 10px; border-radius: 3px 3px 0 0; text-align: center;}
.sms_wrap textarea[name='sms_msg'] {width: 100%; height: 185px;}
.sms_wrap .sms_info {clear: both; margin: 0 13px;}
.sms_wrap .sms_info > div {margin-bottom: 20px;}
*/
body {margin:0; padding:0;}

#wrap {margin:0; padding:0;}
#wrap .sms_area {width: 100%; display:inline-block;	margin:0 auto; padding:15px;}
#wrap .sms_title {background-color: #3C5B9B; color: #fff; font-weight: 700; padding: 10px; border-radius: 3px 3px 0 0; text-align: center;}
#wrap .sms_msg textarea {width:100%; font-size:13px; padding:8px; height:185px; resize:none;}
#wrap .sms_info {margin:20px 0 0; padding:0;}
#wrap .sms_info div {margin-bottom:10px;}

.hideFocus {selector-dummy:expression(this.hideFocus=true); outline:0; -moz-outline-style:none;}
</style>

</head>

<body>

<div id="wrap">

	<form name="fsms" id="fsms">
		<input type="hidden" id="mode" name="mode">
		<? if($hcseq) { ?><input type="hidden" id="hcseq" name="hcseq" value="<?=$hcseq?>"><? } ?>
		<? if($judamType) { ?><input type="hidden" id="type" name="type" value="<?=$judamType?>"><? } ?>
		<div class="sms_area">
			<div class="sms_title">SMS 문자전송</div>
			<div class="sms_msg"><textarea name="sms_msg" id="sms_msg" class="hideFocus" placeholder="메세지 내용을 입력해주세요"  onKeyup="bytePrint();" ></textarea></div>
			<div style="margin-top:4px; text-align:right;"><input type="text" id="sms_msg_length" class="frm_input hideFocus" value="0" style="width:50px;text-align:right;border:0;background-color:#f3f3f3" readonly> byte</div>

			<div class="sms_info">
				<div><input type="text" name="from_hp" id="from_hp" value="<?=$from_hp?>" onKeyup="onlyDigit(this);" placeholder="발신자번호" class="form-control input-sm" readonly></div>
				<div><input type="text" name="to_hp" id="to_hp" value="<?=$to_hp?>" onKeyup="onlyDigit(this);" placeholder="수신자번호" class="form-control input-sm"></div>
				<div style="padding:8px 8px;">
					<label><input type="radio" name="reserve_send" value="0" onClick="reserveSend(this.value);" checked> 즉시발송</label> &nbsp;
					<label><input type="radio" name="reserve_send" value="1" onClick="reserveSend(this.value);"> 예약발송</label>
					<ul id="send_t_area" class="col col-md-* list-inline" style="margin:8px 0;padding:0">
						<li style="margin-bottom:4px;padding:0"><input type="text" class="form-control input-sm datepicker" name="send_ymd" id="send_ymd" value="<?=date('Y-m-d')?>" placeholder="날짜선택" disabled></li>
						<li style="padding:0">
							<select id="send_h" name="send_h" class="form-control input-sm" disabled>
<?
for($i=0; $i<=23; $i++) {
	$hour = sprintf('%02d', $i);
	$selected = (date('H', strtotime('+70 min'))==$hour) ? 'selected' : '';
	echo "<option value='{$hour}' $selected>{$hour}시</option>\n";
}
?>
							</select>
						</li>
						<li>
							<select id="send_i" name="send_i" class="form-control input-sm" disabled>
<?
for($i=0; $i<=59; $i++) {
	$min = sprintf('%02d', $i);
	echo "<option value='{$min}' $selected>{$min}분</option>\n";
}
?>
							</select>
						</li>
					</ul>
				</div>
				<div style="margin:0;padding:0">
					<button type="button" id="btn_submit" onClick="smsSend();" class="btn btn-lg btn-primary" style="width:100%">SMS 발송</button>
				</div>
			</div>
		</div>
	</form>

</div>

<!-- 로딩 -->
<div id="loading" style="position:fixed; z-index:1001; top:0px; left:0px; width:100%; height:100%; display:none;">
	<table width="100%" height="100%">
	  <tr>
		  <td height="100%" align="center">
				<img src="/images/loading/ani_load.gif" width="24"><br/>
				<span style="display:inline-block;background:#888;color:#FFF;margin-top:8px; padding:0 10px; border-radius:12px;">loading</span>
			</td>
		</tr>
	</table>
</div>

<script>
loading = function(arg) {
	if(arg=='on') {
		$('#loading').css('display','block');
	}
	else {
		$('#loading').css('display','none');
	}
}

bytePrint = function() {
	strlength = byteCheck($('#sms_msg'));
	$('#sms_msg_length').val(strlength);
	if(Number(strlength) <= 86) {
		$('#btn_submit').removeClass('btn-danger').addClass('btn-primary');
		$('#btn_submit').text('SMS 발송');
	}
	else {
		$('#btn_submit').removeClass('btn-primary').addClass('btn-danger');
		$('#btn_submit').text('LMS 발송');
	}
}

byteCheck = function(el) {
	var codeByte = 0;
	for(i=0; i<el.val().length; i++) {
		var oneChar = escape( el.val().charAt(i) );
		if( oneChar.length==1 ) {
			codeByte++;
		}
		else if( oneChar.indexOf("%u") != -1 ) {
			codeByte+=2;
		}
		else if( oneChar.indexOf("%") != -1 ) {
			codeByte++;
		}
	}
	if(codeByte > 0) codeByte+=2;
	return codeByte;
}


// 예약발송 시, 예약일시 폼 활성화
reserveSend = function(val) {
	if(val == '1') {
		$('#send_ymd').attr('disabled', false);
		$('#send_h').attr('disabled', false);
		$('#send_i').attr('disabled', false);
	}
	else {
		$('#send_ymd').attr('disabled', true);
		$('#send_h').attr('disabled', true);
		$('#send_i').attr('disabled', true);
	}
}

// 문자발송
smsSend = function() {

	$('#mode').val('');

	if($('#sms_msg').val() == '') { alert('메세지를 입력하십시요.'); $('#sms_msg').focus(); return; }
	if($('#from_hp').val() == '') { alert('발신번호를 입력 하십시요.'); $('#from_hp').focus(); return; }
	if($('#to_hp').val() == '') { alert('수신번호를 입력 하십시요.'); $('#to_hp').focus(); return; }

	to_hp_len = $('#to_hp').val().length;
	if(Number(to_hp_len) < 10 || Number(to_hp_len) > 11) {
		alert('수신번호를 정확히 입력 하십시요.');
		$('#to_hp').val('');
		$('#to_hp').focus();
		return;
	}

	if($("input:radio[name=reserve_send]:checked").val()=='1') {
		if($('#send_ymd').val() == '') {
			alert('예약발송일을 설정 하십시요.');
			$('#send_ymd').focus();
			return;
		}
	}

	if(confirm('발송요청을 등록 하시겠습니까?')) {
		$('#mode').val('send');
		$('#btn_submit').attr('disabled',true);

		$.ajax({
			type: 'post',
			dataType: 'json',
			url: 'ajax.sms.proc.php',
			data: $('#fsms').serialize(),
			success:function(data) {
				if(data.result == 'SUCCESS') {
					$('#sms_msg').val('');
					alert('등록되었습니다.');
				}
				else {
					alert(data.msg);
				}
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function(e) { console.log(e); }
		});

		$('#mode').val('');
		$('#btn_submit').attr('disabled',false);

		return;
	}
	else {
		$('#btn_submit').attr('disabled',false);
	}

}
</script>

</body>
</html>