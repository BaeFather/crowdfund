<?
###############################################################################
## [신규/기존회원] 신분증 있는 경우, 본인확인 신분증 업로드 정보 확인
## 안씀 !!!!!!!!!
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

			<form name="frmIdenId" id="frmIdenId" method="post" enctype="multipart/form-data"  class="frm-identify">
				<h3>신분증 정보 확인</h3>
				<p>아래 등록하신 신분증 정보를 확인해주세요.</p>
				<div class="info-img"></div>
				<label for="infoName">이름</label>
				<input type="text" name="infoName" id="infoName" class=""/>
				<label for="registNum">주민번호</label>
				<input type="text" name="registNum" id="registNum" class=""/>
				<label for="licenseNum">면허번호</label>
				<input type="text" name="licenseNum" id="licenseNum" class=""/>
				<label for="issueDate">발급일자</label>
				<input type="text" name="issueDate" id="issueDate" class="" style="margin-bottom: 50px;"/>
				<ul class="btn-box m-layout">
					<li><button type="button" class="btn-pic-mod" onClick="location.href='/member_new/kyc_step1.php';">사진 수정</button></li>
					<li><button type="button" class="btn-confirm" onClick="location.href='/member_new/identify_account.php';" style="margin: 0;">확인</button></li>
				</ul>
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
