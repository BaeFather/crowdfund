<?
include_once('./_common.php');
include_once('./quest_config.php');


if($ECONF['balance_point']==0) {
	msg_replace("이벤트가 종료되었습니다.", "/");
}

set_cookie('ck_event_id', $ECONF['event_id'], 60*2);		// 이벤트ID 2시간 쿠키 발행

//if(!G5_IS_MOBILE) { msg_go("본 이벤트는 모바일을 통해서만 진행중입니다.\\n휴대폰 또는 테블릿을 이용하여 주시기 바랍니다."); }

$g5['title'] = $ECONF['title'];

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


if( in_array($is_entered, array('ready','1')) ) {
	msg_replace("","/event/quest_finish.php");
}

?>

<link rel="stylesheet" href="/event/quest_event.css" />

<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

		<div id="questDiv">

			<div class="divBogi">

				<p style="text-align:center;margin-bottom:20px;padding:20px 0; background:#e4f2ff; border:0px solid #aaa; border-radius:3px;">
					<span style="font-size:16px;color:#073190">헬로펀딩은 삼성역 KT&G 건물 몇층에<br/>위치하고 있을까요?</span>
				</p>


				<form id="frmAns" name="frmAns">
					<input type="hidden" id="event_id" name="event_id" value="<?=$ECONF['event_id']?>">
				<ul class="select_board">
					<li style="width:25%; text-align:center;padding-top:5px;">
						<input type="radio" id="box1" name="answer" value="2층"><label for="box1"></label><span style="padding-left:5px;display:inline-block;font-size:16px;">2층</span>
					</li>
					<li style="width:25%; text-align:center;padding-top:5px;">
						<input type="radio" id="box2" name="answer" value="4층"><label for="box2"></label><span style="padding-left:5px;display:inline-block;font-size:16px;">4층</span>
					</li>
					<li style="width:25%; text-align:center;padding-top:5px;">
						<input type="radio" id="box3" name="answer" value="5층"><label for="box3"></label><span style="padding-left:5px;display:inline-block;font-size:16px;">5층</span>
					</li>
					<li style="width:25%; text-align:center;padding-top:5px;">
						<input type="radio" id="box4" name="answer" value="8층"><label for="box4"></label><span style="padding-left:5px;display:inline-block;font-size:16px;">8층</span>
					</li>
				</ul>
				</form>
			</div>

			<p align="center">힌트 : <a href="/" style="color:#3366FF">https://www.hellofunding.co.kr</a> 에서 확인하세요.</p>

			<div style="margin-top:30px;">
				<button type="button" class="answer_button" onClick="sendAnswer();">입력완료</button>
			</div>

		</div>

		<div id="notiDiv">

			<div style="text-align:center;padding:25px 0 35px 0; background:url('../images/event/popup01.png') center top no-repeat;background-size:100% 100%;">
				<p style="font-size:20px;text-align:center;font-weight:400;color:#000;">
					<p style="display:inline-block;font-size:20px;text-align:center;font-weight:400;color:#000;line-height:40px;">축하합니다.</p>
				</p>
				<p style="font-size:26px;font-weight:600;color:#0d65dd;line-height:26px;"><span id="gift_point"></span><span style="color:#000;font-weight:400;">원 당첨</span></p>
				<p style="font-size:14px;color:#000;padding-top:5px;"><span id="msg"></span></p>
			</div>

			<div style="margin-top:10px;">
				<button type="button" class="next_button" onClick="location.href='quest_finish.php'">당첨금 받기</button>
			</div>

		</div>

	</div>
</div>

<!-- 본문내용 E N D -->

<script>
<? if($member['mb_id'] && $member['mb_level']=='1') { ?>
sendAnswer = function() {
	var cval = $('input:radio[name="answer"]:checked').val();

	if(cval==undefined) { alert('보기에서 정답을 선택하여 주십시요.'); }
	else {

		$.ajax({
			url: 'quest.ajax.php',
			dataType: "json",
			type: 'post',
			data: {
				mode: 'check_answer',
				event_id : '<?=$ECONF['event_id']?>',
				answer: cval
			},
			success: function(json) {
				if(json.data.result=='SUCCESS') {
					$('#questDiv').fadeOut('fast');

					$('#gift_point').html(json.data.point);
					$('#msg').html(json.data.msg);

					$('#notiDiv').slideDown('slow');

					$('#next_button').val('당첨금 받기');
					if(json.data.va=='1') {
						//
					}
				}
				else if(json.data.result=='FAIL')            { alert('정답이 아닙니다.'); }
				else if(json.data.result=='LOGIN_PLEASE')    { alert('로그인 후 참여 가능한 이벤트 입니다.'); location.href='/bbs/login.php?event_id=<?=$ECONF['event_id']?>'; }
				else if(json.data.result=='DUPLICATE_ENTRY') { alert('이미 응모하신 이벤트 입니다.'); location.href='quest_finish.php'; }
				else if(json.data.result=='FINISHED_EVENT')  { alert('종료된 이벤트 입니다.\n감사합니다.'); location.href='/'; }
				else { console.log(result); }
			},
			beforeSend: function() { $('#loading').css('display','block'); },
			complete: function() { $('#loading').css('display','none'); },
			error: function(e) { console.log(e); }
		});

	}
}
<? } else { ?>
sendAnswer = function() {
	alert('로그인 후 참여 가능한 이벤트 입니다.'); location.href='/bbs/login.php?event_id=<?=$ECONF['event_id']?>';
}
<? } ?>
</script>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>