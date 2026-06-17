<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_javascript('<script src="'.G5_PLUGIN_URL.'/oauth/jquery.oauth.login.js"></script>', 10);
?>

<!-- 비주얼 -->
<!--<img src="<?=G5_THEME_URL?>/img2/member/sub_login.jpg" alt="회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." />-->

<div id="content">

	<div class="location"><b class="blue">로그인</b></div>
	<div class="content">
		<!-- 헬로펀딩 로그인하기 -->
		<div class="login">
			<img src="../images/member/icon_find.gif" alt="헬로펀딩 계정으로 가입하기" />
			<div class="title"><span class="blue">헬로펀딩 계정</span>으로 로그인하기</div>
			헬로펀딩으로 로그인을 하세요<br>다양한 정보를 보실 수 있습니다.

			<form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="return flogin_submit(this);" method="post">
				<input type="hidden" name="url" value="<?php echo $login_url ?>">
			<div class="inputArea">
				<div class="id"><input type="text" name="mb_id" id="login_id" required placeholder="아이디" /></div>
				<div class="pw"><input type="password" name="mb_password" id="login_pw" required placeholder="비밀번호" /></div>
			</div>

			<div class="btnArea">
				<input type="submit" value="로그인" class="btn_big_blue">
			</div>
			</form>

			<div class="linkArea" style="padding-top:10px;">
				<a href="register_choice.php">회원가입</a>
				<a href="../member/find_id.php">아이디 찾기</a>
				<a href="../member/find_pw.php">비밀번호 찾기</a>
			</div>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
		<!-- 헬로펀딩 로그인하기 -->
	</div>

</div>

<script>
function flogin_submit(f) {
	return true;
}
</script>
