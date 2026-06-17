<?
include_once('./_common.php');

check_url_host($url);

// 이미 로그인 중이라면
/*
if($is_member) {
	if($url)
		goto_url($url);
	else
		goto_url(HF_URL);
}
*/
$login_url = login_url($url);
$login_action_url = G5_HTTPS_BBS_URL."/login_check.php";

IF(!$login_url) { $login_url = $_SERVER["REQUEST_URI"]; }

// 로그인 스킨이 없는 경우 관리자 페이지 접속이 안되는 것을 막기 위하여 기본 스킨으로 대체
$login_file = $member_skin_path.'/login.skin.php';
if(!file_exists($login_file)) $member_skin_path   = G5_SKIN_PATH.'/member/basic';


add_javascript('<script src="/plugin/oauth/jquery.oauth.login.js"></script>', 10);

$chk_fail = sql_fetch("select fail_count from login_fail where ip='".$_SERVER['REMOTE_ADDR']."'");
//if($chk_fail[fail_count]>=5) $captcha_use="Y";
if($captcha_use=="Y") include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
?>

<? if(G5_IS_MOBILE) { ?>
<link rel="stylesheet" href="/member/member_style.css">
<? } ?>

<? if($captcha_use=="Y") { ?><script src='https://www.google.com/recaptcha/api.js'></script><? } ?>

<div id="login_guide">
	<div class="content">
		<img src="/images/btn_close.gif" alt="close" class="close">
		<div class="title">※ 로그인</div>

		<form name="flogin2" action="/member/login_check.php" onsubmit="return flogin_submit(this);" method="post">
			<input type="hidden" name="url" value="<?=$login_url?>" />
			<? if($_REQUEST['mode']){ ?><input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>" /><? } ?>

			<fieldset id="login_form">
				<legend>안녕하세요. 헬로펀딩입니다.</legend>
				<span class="title">안녕하세요.</span>
				<span class="sub_title">헬로펀딩입니다.</span>
				<div class="clearfix"></div>
				<br/>
				<label for="login_id">아이디</label>
				<input type="text" name="mb_id" id="login_id" required="required" class="mb-id required" placeholder="아이디를 입력해주세요." /><br/>
				<label for="login_pw">비밀번호</label>
				<input type="password" name="mb_password" id="login_pw" required="required" class="mb-pw required" placeholder="비밀번호를 입력해주세요." /><br/>

				<? if($captcha_use) { ?>
				<div id="grecaptcha" class="g-recaptcha" data-sitekey="6LeVVmcUAAAAAC-XMBa4lubXOw8iQytwTHCXd1BD"></div>
				<? } ?>

				<div class="btn_group">
					<button type="submit" name="login_submit" class="btn_big_blue">로그인</button>
					<a href="/member/find_id.php"><button type="button" class="btn_big_link pull-left">아이디 찾기</button></a>
					<a href="/member/find_pw.php"><button type="button" class="btn_big_link pull-right">비밀번호 찾기</button></a>
				</div>
				<div class="register_description">
					<label>아직 헬로펀딩에 가입하지 않으셨나요?</label>
					<span class="pull-right"><a href="<?=BSC_URL?>/member/join_info.php">회원가입</a></span>
				</div>
			</fieldset>
		</form>

	</div>
</div>

<script type="text/javascript">

// 모바일 여부
var attkind = "<?php ECHO G5_IS_MOBILE?>";

function flogin_submit(f) {
	if(typeof(grecaptcha) != 'undefined') {
		if(grecaptcha.getResponse() == "") {
			alert("로봇이 아닙니다.를 체크해 주세요.");
			return false;
		}
	}
	return true;
}


function fn_login_check()
{
	if(!attkind || attkind =="" || attkind ==0)
	{
		$.blockUI({
			message: $('#login_guide'),
			css: { top:'100px',left:'20%',width:'60%',border:0, cursor:'default' }
		});
	} else {
		$.blockUI({
			message: $('#login_guide'),
			css: { top:'2%',left:'2%',right:'2%',width:'96%',border:0, cursor:'default' }
		});

	}
}

</script>