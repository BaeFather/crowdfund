<?
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

// 리퍼러 체크
referer_check();

//$mb_id = $_POST['prm1'];
$mb_id = mysqli_real_escape_string($g5['connect_db'], trim($_POST['prm1']));

if( preg_match("/(test|admin)/", $mb_id) ) {

	echo 'x';

}
else {

	$query = "SELECT mb_no FROM g5_member WHERE mb_id = '".$mb_id."' AND mb_leave_date=''";

	$row = sql_fetch($query);

	echo ($row['mb_no']) ? 'x' : 'o';

}

exit;
?>