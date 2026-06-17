<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_javascript('<script src="'.G5_PLUGIN_URL.'/oauth/jquery.oauth.login.js"></script>', 10);

?>

<div id="content">
	<div class="location"><span></span><b class="blue">로그인</b></div>

	<div class="content">

		<form name="flogin" action="<?=$login_action_url?>" onsubmit="return flogin_submit(this);" method="post">
			<input type="hidden" name="url" value="<?=$login_url?>">
			<? if($_REQUEST['mode']){ ?><input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>"><? } ?>

			<!-- 헬로펀딩 로그인하기 -->
			<div class="login">
				<img src="/images/member/icon_join.gif" alt="헬로펀딩 계정으로 로그인하기" >
				<div class="title"><span class="blue">헬로펀딩 계정</span>으로 로그인하기</div>
				헬로펀딩으로 로그인을 하세요<br>다양한 정보를 보실 수 있습니다.
				<div class="inputArea">
					<div class="id"><input type="text"     name="mb_id"       id="login_id" required class="mb-id required" placeholder="아이디" style="background-color:transparent;"></div>
					<div class="pw"><input type="password" name="mb_password" id="login_pw" required class="mb-pw required" placeholder="비밀번호" style="background-color:transparent;" ></div>
				</div>

				<div style="text-align:center;">
					<input type="submit" value="로그인" class="btn_big_blue" style="width:282px;">
				</div>

				<div class="linkArea" style="margin:10px 0 30px;">
					<a href="<?=G5_BBS_URL?>/register_choice.php">회원가입</a>
					<a href="<?=G5_URL?>/member/find_id.php">아이디 찾기</a>
					<a href="<?=G5_URL?>/member/find_pw.php">비밀번호 찾기</a>
				</div>

<!--
				<div class="loginType">
					 구글계정 로그인하기
					<div class="">
						<img src="/images/member/icon_google.gif" alt="구글계정 로그인" >
						<div class="">
							<div class="title"><span class="blue">구글계정</span> 로그인</div>
							구글계정으로 로그인 합니다.<br>
							<a href="<? echo $social_oauth_url.'google'; ?>" class="btn_link social_oauth" target="_blank">바로가기</a>
						</div>
					</div>

				 페이스북 로그인하기
					<div class="">
						<img src="/images/member/icon_facebook.gif" alt="페이스북계정 로그인" >
						<div class="">
							<div class="title"><span class="blue">페이스북계정</span> 로그인</div>
							페이스북계정으로 로그인 합니다.<br>
							<a href="<? echo $social_oauth_url.'facebook'; ?>" class="btn_link social_oauth" target="_blank">바로가기</a>
						</div>
					</div>

					 트위터계정 로그인하기
					<div class="">
						<img src="/images/member/icon_twitter.gif" alt="트위터계정으로 로그인" >
						<div class="">
							<div class="title"><span class="blue">트위터계정</span> 로그인</div>
							트위터계정으로 로그인 합니다.<br>
							<a href="#" class="btn_link">바로가기</a>
						</div>
					</div>
				</div>

-->


			</div>
			<!-- 헬로펀딩 로그인하기 -->
		</form>

	</div>

</div>

<script>
function flogin_submit(f) {
	return true;
}
</script>
