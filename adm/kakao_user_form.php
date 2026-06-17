<?php
$sub_menu = '800150';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "카카오 친구톡 사용자발송";
$g5['title'] = $html_title.' 설정';

// 정보
$sql = "SELECT * FROM `g5_kakao_userinfo` ORDER BY binary(tcode) ASC";
$result = sql_query($sql);

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<style>

	.sms_area {
		width:230px;
		display:inline-block;
		margin:10px auto;
		padding:15px;
	}

	.sms_msg textarea {
		border:1px solid #EEEEEE;
		width:200px;
		font-size:12px;
	}

	.sms_title {
		text-align:center;
		font-weight:bold;
		color:#FFFFFF;
		padding:10px;
		background-color:#3C5B9B;
		border-radius:3px 3px 0 0;
	}

	.sms_use {
		padding-top:10px;
		text-align:center;
	}


</style>

<div style="color:red; font-weight:bold; line-height:18px;">
	<ul>
		<li>
			<span style="text-decoration:underline;">{}로 감싸진 문구</span>는 DB에서 자동으로 불러와지는 값으로 <span style="text-decoration:underline;">절대 수정하시면 안됩니다.</span>
		</li>
		<li>
			예를 들어, {USER_NAME} 은 DB에서 불러온 회원의 이름으로 <span style="text-decoration:underline;">'자동치환'</span> 되어 발송됩니다.
		</li>
		<li>
			{USER_NAME} → 홍길동<br />
      {ACCOUNT_NAME} → 예금주<br />
      {ACCOUNT_NUMBER} → 계좌번호 <br />
      {DEPOSIT_MONEY} → 입금액<br />
      {WITHDRAW_ACCOUNT} → 출금계좌<br />
      {WIDTHDRW_MONEY} → 출금액<br />
      {USER_DEPOSIT} → 예치금 잔액<br />
      {PRODUCT_NAME} → 상품명<br />
      {INVEST_MONEY} → 투자금액<br />
      {USER_CONFIG} → 자동투자 설정한 내용  - #{선택 카테고리 1} : #{최소 설정금액}원 ~ #{최대 설정금액}원 <br />
      {PRODUCT_NUMBER} → 상품호번<br />
		</li>
	</ul>
</div>

<form name="frmsmsform" id="frmsmsform" action="./kakao_user_update.php" method="post" enctype="MULTIPART/FORM-DATA" >
<?php
for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
	<input type="hidden" name="idx[]" value="<?php echo $row['idx'];?>" />
	<div class="sms_area">
		<div class="sms_title">
			<?php echo $row['tcode'];?>. <?php echo $row['subject']; ?>
		</div>
		<div class="sms_msg" style="margin-bottom:7px;">
			<textarea rows="15" name="content[]" placeholder="메세지 내용을 입력해주세요"><?php echo $row['content'];?></textarea>
		</div>
		<div class="sms_msg">
			<textarea rows="3" name="turl[]" placeholder="리턴 URL (있을시만)"><?php echo $row['turl'];?></textarea>
		</div>
		<div class="sms_use">
			<label><input type="checkbox" align="absmiddle" name="recyn[]" value="<?php echo $row['idx'];?>^Y" <?php if($row['recyn'] == 'Y'){ echo 'checked'; } ?>/>
			체크시 사용함</label>
		</div>
	</div>

<?php
}
?>


<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
	<input type="button" value="원래대로" class="btn_submit" onclick="document.getElementById('frmsmsform').reset();" style="background-color:#383A3F;">
</div>

</form>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
