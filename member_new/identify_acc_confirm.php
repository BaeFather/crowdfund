<?
###############################################################################
## [신규/기존회원] 1원 계좌 인증 확인
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
			<form name="frmAccSubmit" id="frmAccSubmit" method="post" class="frm-identify">
				<h3>본인 계좌 확인</h3>
				<p>인증하신 계좌로 환급계좌가 등록되었습니다.<br />예치금 출금 시 해당 계좌로 출금됩니다.</p>
				<div class="acc-confirm-box">
					<p>환급계좌</p>
					<p>국민은행 1234567890</p>
				</div>
				<button type="button" class="btn-confirm-next" onclick="location.href='/member_new/identify_info_form.php'">다음</button>
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
