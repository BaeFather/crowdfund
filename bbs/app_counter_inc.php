<?

if(!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {

	$cookie_token = get_cookie('token');

	if( $cookie_token && $member['mb_no'] ) {
		sql_query("UPDATE cf_app_info SET mb_no='".$member['mb_no']."' WHERE token='".$cookie_token."' AND mb_no=''");
	}

	if( empty($cookie_token) && $_GET[md5('token')] ) {
		set_cookie('token', base64_decode($_GET[md5('token')]), "*0");
	}

	if($_GET[md5('token')]) {
		$APPINFO['token'] = base64_decode($_GET[md5('token')]);
		$APPINFO['ver']   = base64_decode($_GET[md5('ver')]);

		if( $APPROW = sql_fetch("SELECT ver, mb_no, rdate FROM cf_app_info WHERE token='".$APPINFO['token']."' ORDER BY rdate DESC LIMIT 1") ) {

			if($APPROW['ver'] > $APPINFO['ver']) {
				// 업데이트로 연결
			}
			else if($APPROW['ver'] < $APPINFO['ver']) {
				$appsql = "
					UPDATE
						cf_app_info
					SET
						ver='".$APPINFO['ver']."',
						udate=NOW()
					WHERE
						token='".$APPINFO['token']."'";
				sql_query($appsql);
			}
			else {
				//
			}

		}
		else {

			$appsql = "
				INSERT INTO
					cf_app_info
				SET
					token = '".$APPINFO['token']."',
					ver = '".$APPINFO['ver']."',
					mb_no = '".$member['mb_no']."',
					rdate = NOW()";
			sql_query($appsql);

		}

	}
}

?>