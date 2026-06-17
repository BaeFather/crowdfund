<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?ver=20201223">', 0);

?>

<div id="content">
	<div class="content">

		<div id="member_confirm">
			<form name="fmemberconfirm" action="<?=$url?>" onSubmit="return fmemberconfirm_submit(this);" method="post">
				<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
				<input type="hidden" name="w" value="u">
				<legend>안녕하세요. 헬로펀딩입니다.</legend>
				<div class="title"><strong>헬로펀딩 계정 확인</strong></div>
				<div style="width:96%;margin:20px auto; 0; font-size:14px; color:green">
					<? if( preg_match("/member_leave\.php/", "", $_REQUEST['url']) ) { ?>
					비밀번호를 입력하시면 회원탈퇴가 완료됩니다.
					<? }else{ ?>
					회원님의 정보를 안전하게 보호하기 위하여, 비밀번호를 한번 더 확인합니다.
					<? } ?>
				</div>
				<div class="clearfix"></div>
				<label for="mb_password">비밀번호</label>
				<input type="password" name="mb_password" id="mb_password" required="required" class="mb_password required" placeholder="비밀번호를 입력해주세요."><br>
				<div class="btn_group">
					<button type="submit" name="submit" id="btn_submit" class="btn_big_blue">확인</button>
					<button type="button" name="main" class="btn_big_green" onclick="location.href='<?=G5_URL?>';">메인으로 돌아가기</button>
				</div>
			</form>
		</div>

		<script>
		function fmemberconfirm_submit(f) {
			document.getElementById("btn_submit").disabled = true;
			return true;
		}
		</script>

	</div>
</div>