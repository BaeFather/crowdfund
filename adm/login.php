<?

include_once('../common.php');

// 이미 로그인 중이라면
if(!$is_member) {
	goto_url(G5_ADMIN_PATH);
}

$login_action_url = G5_ADMIN_URL; // 관리자 로그인 처리

include_once(G5_PATH."/head.sub.php");


add_javascript('<script src="'.G5_PLUGIN_URL.'/oauth/jquery.oauth.login.js"></script>', 10);

?>

<div id="content" style="background: none !important;">
	<div class="content">

		<form name="flogin" id="flogin" action="<?=$login_action_url?>" method="post">
			<input type="hidden" name="is_admin" value=""/>

			<!-- 헬로펀딩 로그인하기 -->
			<div class="login">
				<a href="/"><img src="/theme/blueman1/img/logo.png" alt="헬로펀딩 관리자 계정으로 로그인하기" /></a>
				<div class="title"><span class="blue">헬로펀딩 관리자</span> 로그인하기</div>
				<div class="inputArea">
					<div class="id"><input type="text" name="mb_name" id="login_pw" required class="required" placeholder="이름을 입력해주세요." style="background-color:transparent;" /></div>
					<div class="phone" style="display: none;"><input type="text" name="input_auth_no" id="input_auth_no" placeholder="인증번호" style="background-color:transparent;width: 180px;" /></div>
					<span id="timer"></span>
				</div>

				<div style="text-align:center;">
					<input type="submit" value="확인" class="btn_big_blue" style="width:282px;"/>
				</div>

				<div class="linkArea" style="margin:10px 0 30px;"></div>
			</div>
		</form>

	</div>
</div>

<script type="text/javascript">
var tid;
var set_time = 180; // 3분

// 타이머 표시
function msg_time(){
	m = Math.floor(set_time / 60) + "분 " + (set_time % 60) + "초";	// 남은 시간 계산
	var msg = "<span>" + m + "</span>";
	document.getElementById("timer").innerHTML = msg;	// div 영역에 출력
	set_time--;							// 1초씩 감소
	if(set_time < 0) {			// 시간이 종료 되었으면..
		clearInterval(tid);		// 타이머 해제
		$('div.phone').hide();
		document.getElementById("timer").innerHTML = "";
	}
}




// 타이머 시작
function TimerStart(){ tid=setInterval('msg_time()',1000) };

$(document).ready(function() {
	$("form#flogin").submit(function(e) {
		var fm = $(this).serialize();

		// 관리자 인지 체크
		var url = "/adm/login_proc.ajax.php";

		$.post(url, fm,
			function(data){
				if(data.error){
					alert(data.message);
					return false;
				}
				if(data.check){
					alert(data.message);
					$('input:hidden[name="is_admin"]').val(1);
					$('.btn_big_blue').text("인증확인");
					$("div.phone").show();
					msg_time();
					TimerStart();
					return false;
				}
				if(data.limited){
					alert(data.message);
					$("div.phone").hide();
					clearInterval(tid);		// 타이머 해제
					return false;
				}
				if(data.not_auth_no){
					alert(data.message);
					$('#input_auth_no').focus();
					return false;
				}
				if (data.success) {
					document.location.href = "/adm/";
				}
			},
			"json");
		e.preventDefault();
	});
});
</script>
