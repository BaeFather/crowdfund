<?
include_once("../syndication_config.php");

if($USER_UNIQUE_KEY) {

	if( empty($_SESSION['ss_mb_id']) || empty($_SESSION['ss_mb_key']) ) {
		$mb = sql_fetch("SELECT * FROM g5_member WHERE mb_level='1' AND syndi_id='".$_CONF['SYNDI_ID']."' AND syndi_userid='".$USER_UNIQUE_KEY."'");
		if($mb) {
			$_SESSION['ss_mb_id']  = $mb['mb_id'];
			$_SESSION['ss_mb_key'] = md5($mb['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

			// 로그인 횟수 증가
			sql_query("UPDATE g5_member SET login_cnt=login_cnt+1 WHERE mb_id='".$mb['mb_id']."'");

		}
	}

}

?>