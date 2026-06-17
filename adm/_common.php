<?

define('G5_IS_ADMIN', true);

include_once ('../common.php');
include_once(G5_ADMIN_PATH.'/admin.lib.php');
include_once(G5_ADMIN_PATH.'/admin.menu.config.php');
include_once(G5_ADMIN_PATH.'/inc_sub_admin_check.php');  //부관리자 체크

if($member['mb_id']=='samo') {
	echo "<script>location.href='/'</script>";
	exit;
}
