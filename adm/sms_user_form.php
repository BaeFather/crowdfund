<?php
$sub_menu = '800100';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "SMS 사용자발송";
$g5['title'] = $html_title.' 설정';

// SMS 정보
if(isset($_GET['init']) && $_GET['init'] == 1) {
	$sql = "SELECT * FROM `g5_sms_userinfo_default` ORDER BY send_type_no ASC";
	$g5['title'] .= ' (최초설정값이 로드됨)';
}else {
	$sql = "SELECT * FROM `g5_sms_userinfo` ORDER BY send_type_no ASC";
}
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
			{USER_NAME} → 홍길동
		</li>
	</ul>
</div>

<form name="frmsmsform" id="frmsmsform" action="./sms_user_update.php" method="post" enctype="MULTIPART/FORM-DATA" >
<?php
for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
	<input type="hidden" name="idx[]" value="<?php echo $row['idx'];?>" />
	<div class="sms_area">
		<div class="sms_title">
			<?php echo $row['send_type_no'];?>. <?php echo $row['send_type_desc']; ?>
		</div>
		<div class="sms_msg">
			<textarea rows="20" name="msg[]" placeholder="메세지 내용을 입력해주세요"><?php echo $row['msg'];?></textarea>
		</div>
		<div class="sms_use">
			<label><input type="checkbox" align="absmiddle" name="use_yn_<?php echo $row['idx'];?>" value="1" <?php if($row['use_yn'] == '1'){ echo 'checked'; } ?>/>
			체크시 사용함</label>
		</div>
	</div>

<?php
}
?>


<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
	<input type="button" value="원래대로" class="btn_submit" onclick="document.getElementById('frmsmsform').reset();" style="background-color:#383A3F;">
	<input type="button" value="최초설정 불러오기" class="btn_submit" onclick="document.location.href='./sms_user_form.php?init=1';" style="background-color:#383A3F;">
</div>

</form>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
