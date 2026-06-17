<?
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
				<h3>가상 계좌 발급 대기</h3>
				<p>제출하신 본인확인 서류를 관리자가 검토 후<br />가상계좌가 발급되오니 조금만 기다려주세요.<br /><br />영업일 기준 1~3일 정도 소요됩니다.</p>
				<img src="../../theme/2018/img/member/step12.jpg" alt="step" class="img-process"/>
				<button type="button" class="btn-main btn-main-mg2">메인으로 가기</button>
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
