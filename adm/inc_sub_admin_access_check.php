<?
///////////////////////////////////////////////////////////////////////////////
// 관리자 페이지 ACCESS LOG 남기기
///////////////////////////////////////////////////////////////////////////////

if(!$_SESSION['ss_is_admin']) {

	header('HTTP/1.0 404 Not Found'); exit;

}
else {

	//print_rr($_SERVER, 'font-size:12px');

	$access_page_title = sql_real_escape_string($g5['title']);
	if($_SERVER['QUERY_STRING']) {
		$query_string = "'".sql_real_escape_string($_SERVER['QUERY_STRING'])."'";
	}
	else {
		$query_string = "NULL";
	}

	$sql = "
		INSERT INTO
			g5_admin_access_log
		SET
			dt       = NOW(),
			login_no = '".$_SESSION['ss_admin_login_idx']."',
			mb_no    = '".$member['mb_no']."',
			mb_name  = '".$member['mb_name']."',
			path     = '".$_SERVER['PHP_SELF']."',
			param    = $query_string,
			title    = '".$access_page_title."'";
	//print_rr($sql, 'font-size:12px');
	if( $_SESSION['ss_admin_login_idx'] ) {
		sql_query($sql);
	}
	else {
		return;
	}

}


?>