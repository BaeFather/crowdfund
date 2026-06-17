<?php
include_once('./_common.php');

$g5['title'] = '회원가입';
$g5['top_bn'] = "/images/member/sub_join.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');
?>
<!-- 본문내용 START -->
<div id="content">
	<div class="location"><b class="blue">회원가입</b></div>

	<div class="content">

		<h2><span class="blue">헬로펀딩</span>에 오신것을 환영합니다.</h2>
		<div class="subTitle">고객님께 해당하는 가입 유형을 선택해 주세요.</div>

		<!-- 헬로펀딩 가입하기 -->
		<div class="hello">
			<img src="../images/member/icon_join.gif" alt="헬로펀딩 계정으로 가입하기" />
			<div class="">
				<div class="title"><span class="blue">헬로펀딩 계정</span>으로 가입하기</div>
				회원이 되시면 헬로펀딩의 다양한 정보를 확인하실 수 있습니다.<br>회원가입으로 헬로펀딩의 혜택을 누려보세요.<br><br>
				<a href="./join_info.php" class="btn_blue">회원가입</a>
			</div>
		</div>

		<div class="joinType">
			<!-- 구글계정 가입하기 -->
			<div class="">
				<img src="../images/member/icon_google.gif" alt="구글계정 가입하기" />
				<div class="">
					<div class="title"><span class="blue">구글계정</span> 가입하기</div>
					구글계정으로 회원가입을 합니다.<br><br>
					<a href="#" class="btn_blue">회원가입</a>
				</div>
			</div>

			<!-- 페이스북 가입하기 -->
			<div class="">
				<img src="../images/member/icon_facebook.gif" alt="페이스북계정 가입하기" />
				<div class="">
					<div class="title"><span class="blue">페이스북계정</span> 가입하기</div>
					페이스북계정으로 회원가입을 합니다.<br><br>
					<a href="#" class="btn_blue">회원가입</a>
				</div>
			</div>

			<!-- 트위터계정 가입하기 -->
			<div class="">
				<img src="../images/member/icon_twitter.gif" alt="트위터계정으로 가입하기" />
				<div class="">
					<div class="title"><span class="blue">트위터계정</span> 가입하기</div>
					트위터계정으로 회원가입을 합니다.<br><br>
					<a href="#" class="btn_blue">회원가입</a>
				</div>
			</div>
		</div>

	</div>
</div>


<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>