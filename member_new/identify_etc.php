<?
###############################################################################
## [신규/기존회원] 신분증 없는 경우, 기타 본인 확인 서류 제출  
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
					<span class="active">1</span>/4
				</div>
			</div>
			<hr />
			<form name="frmIdenEtc" id="frmIdenEtc" method="post" enctype="multipart/form-data"  class="frm-identify">
				<h3>기타 본인 확인 서류 제출</h3>
				<p>주민등록증, 운전면허증 외 본인확인 서류 제출 시<br />관리자 승인 후 서비스 이용이 가능합니다.<br />영업일 기준 1~3일 정도 소요됩니다.</p>
				<table class="info-table">
					<tr>
						<th colspan="3">제출 가능 서류 (택 1)</th>
					</tr>
					<tr>
						<td rowspan="2" style="text-align: center; width: 100px;">내국인</td>
						<td colspan="2">여권 + 주민등록등(초)본</td>
					</tr>
					<tr>
						<td colspan="2">주민등록등(초)본 + 법정대리인 신분증</td>
					</tr>
					<tr>
						<td style="text-align: center; width: 100px;">외국인</td>
						<td colspan="2">여권 + 국내거소증 OR 국제운전면허증</td>
					</tr>
				</table>
				<div class="filebox m-layout"> 
					<input type="file" id="file"> 
					<input class="upload-name name1" value="선택된 파일 없음" readonly>
					<label for="file">파일선택</label> 
				</div>
				<div class="filebox m-layout"> 
					<input type="file" id="file2"> 
					<input class="upload-name name2" value="선택된 파일 없음" readonly>
					<label for="file2">파일선택</label> 
				</div>
				<p class="cf" style="color: red;">PDF, JPG, PNG 파일만 등록 가능</p>
				<ul class="btn-prev-next m-layout">
					<li><button type="button" class="btn-prev" onClick="location.href='/member_new/identify_uploads.php';">이전</button></li>
					<li><button type="button" class="btn-next" onClick="location.href='/member_new/identify_account.php';">다음</button></li>
				</ul>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
// input file 값 변경
$(document).ready(function(){ 
	var fileTarget = $('#file'); 
	var fileTarget2 = $('#file2'); 

	fileTarget.on('change', function(){ 
		var cur = $(this).val().split('/').pop().split('\\').pop();
		$(".name1").val(cur);
	}); 

	fileTarget2.on('change', function(){ 
		var cur = $(this).val().split('/').pop().split('\\').pop();
		$(".name2").val(cur);
	}); 
}); 
</script>

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
