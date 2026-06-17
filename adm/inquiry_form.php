<?php
/*
CREATE TABLE `cf_inquiry` (
	`receive_email` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`sms_user_content` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`sms_admin_content` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`sms_user_use` CHAR(1) NOT NULL COLLATE 'utf8_general_ci',
	`sms_admin_use` CHAR(1) NOT NULL COLLATE 'utf8_general_ci',
	`privacy_content` TEXT NOT NULL COLLATE 'utf8_general_ci'
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DYNAMIC;
*/

$sub_menu = "600300";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql = "select * from cf_inquiry";
$row = sql_fetch($sql);

$g5['title'] = '문의하기 관련설정';
include_once('./admin.head.php');
?>

<link href="css/bootstrap.min.css" rel="stylesheet">

<div class="row">
	<form id="finquiry_form" method="post" action="register_process.php" enctype="multipart/form-data" onsubmit="return finquiry_submit(this);" class="form-horizontal">
	<input type="hidden" name="w" value="">
	<input type="hidden" name="token" value="">
	<input type="hidden" name="action" value="inquiry_update">
	<div class="col-lg-12">
		<div class="form-group">
			<label class="col-sm-1 control-label">문의하기 받는메일 설정</label>
			<div class="col-sm-10">
				<input type="text" name="receive_email" value="<?php echo $row['receive_email']; ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">문의하기 SMS 관련설정</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li class="text-center">
					<textarea name="sms_user_content" rows="5" class="form-control"><?php echo $row['sms_user_content']; ?></textarea>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="sms_user_use" value="Y" <?php echo ($row['sms_user_use'] == 'Y') ? 'checked' : ''; ?>>
							체크시 사용함
						</label>
					</div>
					[사용자]
				</li>
				<li class="text-center">
					<textarea name="sms_admin_content" rows="5" class="form-control"><?php echo $row['sms_admin_content']; ?></textarea>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="sms_admin_use" value="Y" <?php echo ($row['sms_admin_use'] == 'Y') ? 'checked' : ''; ?>>
							체크시 사용함
						</label>
					</div>
					[관리자]
				</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">개인정보 보호정책 설정</label>
			<div class="col-sm-10">
				<?php echo editor_html('privacy_content', get_text($row['privacy_content'], 0)); ?>
			</div>
		</div>
	</div>
	<!-- /.col-lg-12 -->
	<p class="text-center">
		<button type="submit" class="btn btn-success">설정을 저장합니다.</button>
	</p>
	</form>
</div>
<!-- /.row -->

<script>
function finquiry_submit(f)
{
    <?php echo get_editor_js("privacy_content"); ?>

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
