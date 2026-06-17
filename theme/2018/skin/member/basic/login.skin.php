<?

if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?ver=20181127">', 0);
add_javascript('<script src="'.G5_PLUGIN_URL.'/oauth/jquery.oauth.login.js"></script>', 10);

$chk_fail = sql_fetch("SELECT fail_count FROM login_fail WHERE ip='".$_SERVER['REMOTE_ADDR']."'");

$captcha_use = false;

if($chk_fail['fail_count']>=5) {
	$captcha_use = true;
	add_javascript("<script src='//www.google.com/recaptcha/api.js'></script>", 11);
}

$ck_save_id = $_COOKIE['ck_save_id'];
IF($_POST["login_url"]) { $login_url = $_POST["login_url"]; }
IF($_GET["login_url"]) { $login_url = $_GET["login_url"]; }

?>
<div id="content">
	<div class="content">

		<form name="flogin" action="<?=$login_action_url;?>" onsubmit="return flogin_submit(this);" method="post">
			<input type="hidden" name="url" value="<?=$login_url;?>">
			<? if($_REQUEST['mode']){ ?><input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>"><? } ?>

			<fieldset id="login_form">
				<legend>안녕하세요. 헬로펀딩입니다.</legend>
				<span class="title">안녕하세요.</span>
				<span class="sub_title">헬로펀딩입니다.</span>
				<div class="clearfix"></div>
				<br>

				<label for="login_id">아이디</label>
				<input type="text" name="mb_id" id="login_id" value="<?=$ck_save_id?>" required="required" autocomplete="off" class="textbox mb-id required" placeholder="아이디를 입력해주세요." tabindex="1">
				<div style="width:100%;text-align:right"><input type="checkbox" name="id_save" id="login_id_save" <?=($ck_save_id)?'checked':'';?> tabindex="3"> <label for="login_id_save" style="color:#555;font-size:14px">아이디 저장</label></div><br>

				<label for="login_pw">비밀번호</label>
				<input type="password" name="mb_password" id="login_pw" required="required" autocomplete="off" class="textbox mb-pw required" placeholder="비밀번호를 입력해주세요." tabindex="2"><br><br>

				<? if($captcha_use) { ?>
				<div id="grecaptcha" class="g-recaptcha" data-sitekey="6LeVVmcUAAAAAC-XMBa4lubXOw8iQytwTHCXd1BD"></div>
				<script type="text/javascript">
				$("#captcha_key").css('display','inline');
				</script>
				<? } ?>

				<div class="btn_group">
					<button type="submit" id="login_submit" class="btn_big_blue" tabindex="4">로그인</button>
					<button type="button" class="btn_big_link pull-left"  onclick="location.href='<?=G5_URL?>/member/find_id.php'">아이디 찾기</button></a>
					<button type="button" class="btn_big_link pull-right" onclick="location.href='<?=G5_URL?>/member/find_pw.php'">비밀번호 찾기</button></a>
				</div>

				<div class="register_description">
					<label>아직 헬로펀딩에 가입하지 않으셨나요?</label>
					<span>
						<a href="<?=G5_URL?>/member/join_info.php?tab=p">
						<!--a onclick="alert('온라인투자연계금융업 시스템 변경으로\n신규 회원가입이 일시 중단됩니다.\n빠른 시일 내에 회원가입 서비스를 제공하겠습니다.');" style="cursor:pointer;"-->
						회원가입</a></span>
				</div>
			</fieldset>
		</form>

	</div>
</div>

<script type="text/javascript">
function flogin_submit(f) {
	if(typeof(grecaptcha) != 'undefined') {
		if(grecaptcha.getResponse() == "") {
			alert("로봇이 아닙니다.를 체크해 주세요.");
			return false;
		}
	}
	return true;
}

$('#login_id_save').click(function(){
	if(this.checked) {
		//
	}
	else {
		delete_cookie('ck_save_id');
	}
});
</script>