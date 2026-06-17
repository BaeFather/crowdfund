<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="content">
	<!--<div class="location"><b class="blue"><?=$g5['title'] ?></b></div>-->

	<div class="content">
		<div class="login" style="margin-top:10%;">
			<img src="/images/member/icon_find.gif" alt="회원 비밀번호 확인" />
			<div class="title"><span class="blue">헬로펀딩 계정</span> 확인</div><br />
			<p>
				<? if($url == 'member_leave.php') { ?>
				비밀번호를 입력하시면 회원탈퇴가 완료됩니다.
				<? }else{ ?>
				회원님의 정보를 안전하게 보호하기 위해<br/> 비밀번호를 한번 더 확인합니다.
				<? } ?>
			</p>

			<form name="fmemberconfirm" action="<?=$url?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
				<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
				<input type="hidden" name="w" value="u">
			<div class="inputArea">
				<div class="id" style="display:none"><?=$member['mb_id']?></div>
				<div class="pw"><input type="password" name="mb_password" id="mb_confirm_pw" required placeholder="비밀번호(필수)" maxLength="30" /></div>
			</div>
			<div class="btn_confirm">
				<input type="submit" value="확인" class="btn_big_blue">
				<a href="<?=G5_URL?>" class="btn_big_gray" style="width:99.5%; margin-top:4px;">메인으로 돌아가기</a>
			</div>
			</form>

			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>
</div>

<script>
function fmemberconfirm_submit(f) {
	document.getElementById("btn_submit").disabled = true;
	return true;
}
</script>
