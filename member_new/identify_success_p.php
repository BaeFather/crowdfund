<?
###############################################################################
## [기존회원] 본인확인 완료 (신분증 있는 경우)
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
				<h3>본인 확인 완료</h3>
				<p>
					온라인투자연계금융업 및 이용자 보호에<br />
					관한 법률 제21조(투자자에 대한 정보확인)에 의한<br />
					본인확인 인증절차가 완료되었습니다.<br />
					<br />
					투자 및 서비스(출금, 상환 등) 이용이 가능합니다.
				</p>
				<button type="button" class="btn-main btn-main-mg4">메인으로 가기</button>
			</div>
		</div>

	</div>
</div>

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
