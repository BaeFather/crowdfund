<?php
// 임의 정산유저 권한 부여 (주민등록번호 열람 가능)

include_once('./_common.php');

if($_SESSION['ss_mb_id']=="admin") {

	if($_SESSION['ss_accounting_admin']) {
		unset($_SESSION['ss_accounting_admin']);
		echo "<script>alert('회계관리자권한 삭제');location.replace('/adm/');</script>";
	}
	else {
		set_session('ss_accounting_admin', true);
		echo "<script>alert('회계관리자권한 부여');location.replace('/adm/');</script>";
	}

	//print_r($_SESSION);

}
else {
	header("HTTP/1.1 404 Not Found");
}

exit;

?>
