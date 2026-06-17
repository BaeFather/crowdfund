<?
###############################################################################
## [신규/기존회원] 1원 인증 계좌 선택
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
			<form name="frmAccInfo" id="frmAccInfo" method="post" class="frm-identify">
				<h3>출금 계좌 등록</h3>
				<!--<h3>본인 계좌 선택</h3>-->
				<p>본인확인이 가능한 본인명의 계좌정보를 입력해주세요.<br />인증된 계좌는 예치금 환급계좌로 사용됩니다.</p>
				<label for="bankName" style="margin-top: 0;">은행</label>
				<select name="strBankCode" id="strBankCode" class="sel-code">
					<option value="">은행을 선택하세요</option>
					<?
					$BANK_KEYS = array_keys($BANK);
					for($i=0; $i<count($BANK); $i++) {
						$selected = ($BANK_KEYS[$i]==sprintf("%03d", $member["bank_code"])) ? 'selected' : '';
						echo "<option value='".$BANK_KEYS[$i]."' $selected>".$BANK[$BANK_KEYS[$i]]."</option>\n";
					}
					?>
				</select>
				<label for="accountNum">계좌번호</label>
				<input type="text" name="accountNum" id="accountNum" placeholder="'-'를 제외한 계좌번호 입력"/>
				<button type="button" class="btn-account" onClick="location.href='/member_new/identify_acc_chk.php';">내 계좌로 1원 보내기</button>
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
