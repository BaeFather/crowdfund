<?php
include_once('./_common.php');

$g5['title'] = '로그인';
$g5['top_bn'] = "/images/member/sub_login.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$url = $_GET['url'];

// url 체크
check_url_host($url);

// 이미 로그인 중이라면
if ($is_member) {
    if ($url)
        goto_url($url);
    else
        goto_url(G5_URL);
}

$login_url        = login_url($url);
$login_action_url = G5_HTTPS_BBS_URL."/login_check.php";
?>
<!-- 본문내용 START -->
<div id="content">
	<div class="location"><b class="blue">로그인</b></div>

	<div class="content">

		<!-- 헬로펀딩 로그인하기 -->
		<div class="login">
			<img src="../images/member/icon_join.gif" alt="헬로펀딩 계정으로 가입하기" />
			<div class="title"><span class="blue">헬로펀딩 계정</span>으로 가입하기</div>
			헬로펀딩으로 로그인을 하세요<br>다양한 정보를 보실 수 있습니다.
			<form name="flogin" action="<?php echo $login_action_url ?>" method="post">
			<input type="hidden" name="url" value="<?php echo $login_url ?>">
			<div class="inputArea">
				<div class="id"><input type="text" class="mb-id required" name="mb_id" id="login_id" maxLength="20" title="아이디" /></div>
				<div class="pw"><input type="password" class="mb-pw required" name="mb_password" id="login_pw" maxLength="20" title="비밀번호" /></div>
			</div>
			<div><input type="submit" value="로그인" class="btn_green" /></div>
			</form>
			<div class="linkArea"><a href="/member/join.php">회원가입</a> <a href="#">아이디 찾기</a> <a href="#">비밀번호 찾기</a></div>

			<div class="loginType">
				<!-- 구글계정 로그인하기 -->
				<div class="">
					<img src="../images/member/icon_google.gif" alt="구글계정 로그인" />
					<div class="">
						<div class="title"><span class="blue">구글계정</span> 로그인</div>
						구글계정으로 로그인 합니다.<br>
						<a href="#" class="btn_link">바로가기</a>
					</div>
				</div>

				<!-- 페이스북 로그인하기 -->
				<div class="">
					<img src="../images/member/icon_facebook.gif" alt="페이스북계정 로그인" />
					<div class="">
						<div class="title"><span class="blue">페이스북계정</span> 로그인</div>
						페이스북계정으로 로그인 합니다.<br>
						<a href="#" class="btn_link">바로가기</a>
					</div>
				</div>

				<!-- 트위터계정 로그인하기 -->
				<div class="">
					<img src="../images/member/icon_twitter.gif" alt="트위터계정으로 로그인" />
					<div class="">
						<div class="title"><span class="blue">트위터계정</span> 로그인</div>
						트위터계정으로 로그인 합니다.<br>
						<a href="#" class="btn_link">바로가기</a>
					</div>
				</div>
			</div>
		</div>
		<!-- 헬로펀딩 로그인하기 -->

	</div>
</div>


<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>