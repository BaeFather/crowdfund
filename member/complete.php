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

<?php if(G5_IS_MOBILE){ ?>
	<img src="<?=G5_THEME_URL?>/img2/member/sub_join.jpg" alt="회원가입완료 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." />
<?php } ?>



	<div class="location"><span>회원가입</span><b class="blue">회원가입완료</b></div>

	<div class="content">

		<!-- 가입완료 -->
		<div class="welcome">
			<img src="../images/member/icon_complete.gif" alt="welcome" />
			<div class="title">회원가입을 환영합니다.</div>
			헬로펀딩 회원가입이 완료되었습니다.<br>헬로펀딩 투자 참여를 위한 예치금 계좌를 발급 받으세요.
			<div class="btnArea">
				<a href="/" class="btn_big_link">메인으로</a>
			</div>
		</div>
		<!-- 가입완료 -->
	</div>
</div>


<!-- 본문내용 E N D -->
<?php
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>