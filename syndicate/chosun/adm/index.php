<?
include_once("../syndication_config.php");

$_SESSION['syndi_admin_login'] = false;

include_once('./head.php');
?>

		<link href="/adm/css/bootstrap.min.css" rel="stylesheet">

		<div id="content" style="position:absolute; height:100%; margin-top:0; background-color:#666666; width:100%;">
			<div class="content investment" style="width:98%;margin:150px auto;">

				<div style="width:440px; height:310px; margin:0 auto; background-image:url('images/ams_gate_bg.gif');">
					<div style="height:114px;"></div>

					<form id="frmLogin">
					<ul style="list-style:none;">
						<li style="margin-left:182px;"><input type="text" name="adminid" id="adminid" class="frm_input required" required style="width:140px;height:24px;"></li>
						<li style="margin-left:182px; margin-top:4px;"><input type="password" name="adminpw" id="adminpw" class="frm_input required" required style="width:140px;height:24px;"></li>
						<li style="margin-left:150px; margin-top:20px;"><img id="submit_btn" src="images/login.gif" style="cursor:pointer;" width="144" height="38"></li>
					</ul>
					</form>
				</div>

			</div>
		</div>

<?
include_once('./tail.php');
?>

<script>
$('#submit_btn').click(function() {
	if($('#adminid').val()=='') { alert('ID를 입력하십시요'); $('#adminid').focus(); }
	else if($('#adminpw').val()=='') { alert('비밀번호를 입력하십시요'); $('#adminpw').focus(); }
	else {
		var formData = $('#frmLogin').serialize();
		$.ajax({
			type: "POST",
			url: "ajax_login_proc.php",
			data: formData,
			success:function(data) {
				if(data=='SUCCESS') {
					$(location).attr('href', 'product_status.php');
				}
				else {
					alert('인증에 실패 하였습니다.');
				}
			},
			error: function(e) { alert("error"); }
		});
	}
});
</script>