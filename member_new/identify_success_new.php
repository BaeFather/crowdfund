<?
###############################################################################
## [신규회원] 본인확인 완료 (신분증 있는 경우)
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
					<span class="active">4</span>/4
				</div>
			</div>
			<hr />
			<div class="info-txt">
				<h3>가상 계좌 발급 완료</h3>
				<p>헬로펀딩 가상계좌가 정상적으로 발급되었습니다.<br />가상계좌에 예치금 입금 후 투자해주세요. 감사합니다.</p>
				<div class="acc-confirm-box">
					<p>예치금 가상계좌</p>
					<p><input type="text" value="국민은행 1234567890" id="accCopy" class="acc-copy"/><span class="btn-copy" onclick="accCopy();">복사</span></p>
				</div>
				<button type="button" class="btn-main btn-main-mg3">메인으로 가기</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function accCopy() {
		var copyTxt = document.getElementById("accCopy");
		copyTxt.select();
		document.execCommand("Copy");
		alert('복사되었습니다.');
	}
</script>

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
