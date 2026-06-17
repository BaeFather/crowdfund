<?

include_once("_common.php");

/*
if( in_array($member['mb_level'], array('6','7','8','9','10')) ) {
	header('Location: https://manager.hellofunding.co.kr');
}
*/

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$outer_connect = ( OFFICE_CONNECT ) ? false : true;		// 사무실 아이피로 접속시 관리자 인증 바이패스

// 인증처리 (사내 IP대역이 아닌 경우 문자메세지 인증)
if( $outer_connect ) {

	// 관리자 외부접속허용 스위치
	if(!$CONF['bypass_admin_outer_connect']) {
		header('HTTP/1.0 404 Not Found'); exit;
	}

	if($_SESSION['ss_is_admin'] ) {
		//
	}
	else {

		if( in_array($member['mb_level'], array('6','7','8','9')) ) {

			if( strpos($path, "login.php") === false ) {

				$SUBADMIN = sql_fetch("SELECT allow_location FROM g5_sub_admin WHERE mb_no = '".$member['mb_no']."'");

				if($SUBADMIN['allow_location']=='local') {
					msg_replace('외부접속 허용되지 않습니다.', '/bbs/logout.php');
				}
				else {
					msg_replace('관리자는 추가인증을 해주세요.', G5_ADMIN_URL.'/login.php?url=' . urlencode(G5_ADMIN_URL));
				}

			}
		}
		else {
			header('HTTP/1.0 404 Not Found'); exit;
		}

	}

}
else {
	// 로컬접속시 세션체크
	if(!$_SESSION['ss_is_admin'] ) {
		msg_replace('', '/');
	}
}




// 부관리자 권한 체크
$add_sql = "
	SELECT
		idx, is_inspecter, is_editor, auth_info, privacy_auth, hp_auth, account_view_auth, member_control_auth, product_control_auth, account_auth, allow_location
	FROM
		g5_sub_admin AS A
	LEFT JOIN
		g5_member AS B  ON A.mb_no = B.mb_no
	WHERE 1
		AND B.mb_id = '".$_SESSION['ss_mb_id']."'
		AND B.mb_level IN('9','10')";
$sad = sql_fetch($add_sql);
/*
if($sad['allow_location']=='local') {
	if($outer_connect) {
		if(!$CONF['bypass_admin_outer_connect']) {
			ob_start();
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}
}
*/

$subadmin_auth = $sad['auth_info'];
if($subadmin_auth != '') {
	$subadmin_auth_arr = explode(',', $subadmin_auth);
}

//열람권한없음 : 불허된 사용자의 경우 404출력
if($sub_menu) {
	if(!in_array(substr($sub_menu, 0, 3), $subadmin_auth_arr)) {
		ob_start();
		header("HTTP/1.0 404 Not Found");
		exit;
	}
}

?>