<?
###############################################################################
## [신규/기존회원] 1원 계좌 인증 하기
###############################################################################

include_once('./_common.php');

include_once('../lib/function_prc.php');
include_once('../lib/etc.lib.php');

while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }

if ($co['co_include_head'])
	@include_once($co['co_include_head']);
else
	include_once('./_head.php');

add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);

?>

<!-- 본문내용 START -->

<div id="content" class="identify-wrap">
	<div class="content">

		<div class="confirm_form">
			<div class="top-title">
				<h2>본인확인</h2>
				<div class="page-num">
					<span class="active">2</span>/4
				</div>
			</div>
			<hr />
			<form name="frmAccChk" id="frmAccChk" method="post" class="frm-identify">
				<h3>본인 계좌 인증</h3>
				<p>인증하신 계좌로 1원을 보내드렸습니다.<br />계좌의 입금내역에 표시된 4자리를 입력해주세요.</p>
				<div class="acc-chk-box">
					<p>입금자명</p>
					<p>헬로 <span class="star">* * * *</span><span class="right">1원</span></p>
				</div>
				<label for="accChkNum">헬로 뒤 4자리 숫자</label>
				<input type="text" name="accChkNum" id="accChkNum" placeholder="4자리 숫자 입력"/>
				<p class="cf2">* 입금내역이 없다면 등록하신 계좌 정보를 다시 확인해주세요.</p>
				<div class="m-layout">
					<input type="text" name="accChkInput" id="accChkInput" value="국민은행 123456566767" readonly class="input-disable acc-change"/>
					<button class="change-btn" onClick="location.href='/member_new/identify_account.php';">계좌변경하기</button>
				</div>
				<button type="button" class="btn-next" onClick="location.href='/member_new/identify_acc_confirm.php';" style="margin-top: 150px">다음</button>
			</form>
		</div>

	</div>
</div>

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
