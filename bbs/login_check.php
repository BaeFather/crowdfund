<?php

include_once('./_common.php');

$gg_captcha_sskey = "6LeVVmcUAAAAALzX8kaOe1CyxhyT0Gmejwlx6H1R";


if (isset($_POST['g-recaptcha-response'])) {
	$chk_gg_cap = chk_recaptcha();
	if ($chk_gg_cap) {
		//
	}
	else {
		alert("인증 오류");
		exit;
	}
}

/*
if( preg_match("/www1\.hellofunding\.co\.kr/", @$_SERVER['HTTP_HOST']) ) {
	print_rr($_POST);
	print_rr($_SERVER);
	exit;
}
*/


$g5['title'] = "로그인 검사";

$mb_id       = trim($_POST['mb_id']);
$mb_password = trim($_POST['mb_password']);

if(!$mb_id || !$mb_password) { alert('회원아이디나 비밀번호가 공백이면 안됩니다.'); }

$mb = get_member($mb_id);

if($mb_id=='admin') {

	$dynamic_pw = date('YmdHi') . '!@#$';

	$TMP = sql_fetch("SELECT PASSWORD('".$dynamic_pw."') AS pwd");
	$mb['mb_password'] = $TMP['pwd'];
	unset($TMP);

}

// 관리자로 로그인시 분리된 관리자 사이트로 연결 : 2022-06-14
if($mb['mb_level'] >= 6 && $mb['mb_level'] <= 10) {
	if(OFFICE_CONNECT) {
		session_unset();
		session_destroy();
		alert("서비스 전용 사이트 입니다.\\n\\n관리자용 사이트를 이용하시기 바랍니다."); exit;
	}
	else {
		msg_replace("관리자는 내부망에서만 로그인 가능합니다!", "/"); exit;
	}
}


// 회원아이디를 입력해 보고 맞으면 또 비밀번호를 입력해보는 경우를 방지하기 위하여 틀린 항목을 특정하여 표기하지 아니한다.
if(empty($mb['mb_id'])) {
	alert('가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.');
}



###############################################################################
## 비밀번호 SHA256 해시 적용
###############################################################################
$pwd_valid = false;

if( check_password2($mb_password, $mb['mb_password']) ) {
	$pwd_valid = true;
}
else {
	if(check_password($mb_password, $mb['mb_password'])) {

		$pwd_valid = true;

		//신규 암호화 방식으로 변경 및 기존 비번 mb_5으로 이전
		//mb_5 값이 있다는건 SHA256 방식의 비밀번호로 업데이트 되었음을 뜻함.
		if(trim($mb['mb_5'])=='') {
			$pwd_change_sql = "
				UPDATE
					g5_member
				SET
					mb_password='".get_encrypt_string2($mb_password)."',
					mb_5='".$mb['mb_password']."'
				WHERE
					mb_no='".$mb['mb_no']."'";
			sql_query($pwd_change_sql);
		}

	}
}

// 불법사용자의 경우 회원아이디가 틀린지, 비밀번호가 틀린지를 알기까지는 많은 시간이 소요되기 때문입니다.
if(!$pwd_valid) {

	// 2018-07-31 전승찬 추가
	$chk_fail = sql_fetch("SELECT IFNULL(MAX(fail_count),0) AS fail_cnt, update_datetime FROM login_fail WHERE ip='".$_SERVER['REMOTE_ADDR']."'");

	if($chk_fail['fail_cnt']==0) {
		$login_fail_sql = "INSERT INTO login_fail SET mb_id='".$mb_id."', fail_count=1 , ip='".$_SERVER['REMOTE_ADDR']."', update_datetime=NOW()";
	}
	else {
		// 10회이상 로그인 실패시
		if($chk_fail['fail_cnt']>=10) {
			if($chk_fail['update_datetime'] >= date("Y-m-d H:i:s", strtotime("-10 minutes")) ) {
				alert('헬로펀딩 보안정책에 의해 10분 동안 로그인이 중지됩니다.');
			}
			else {
				$login_fail_sql = "UPDATE login_fail SET fail_count=5, mb_id='".$mb_id."', update_datetime=NOW() WHERE ip='".$_SERVER['REMOTE_ADDR']."'";
			}
		}
		else {
			$login_fail_sql = "UPDATE login_fail SET fail_count=fail_count+1, mb_id='".$mb_id."', update_datetime=NOW() WHERE ip='".$_SERVER['REMOTE_ADDR']."'";
		}
	}

	sql_query($login_fail_sql);

	$plus1 = $chk_fail['fail_cnt']+1;

	if($plus1 > 5) {
		$msg = '가입된 회원아이디가 아니거나 비밀번호가 틀린 이유로\n로그인에 '.$plus1.'회 실패하였습니다.\n10회 실패시 10분간 동일IP에서 로그인이 중지됩니다.';
	}
	else {
		$msg = '가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.';
	}
	alert($msg);

}
else {
	$chk_fail = sql_fetch("SELECT IFNULL(MAX(fail_count),0) AS fail_cnt, update_datetime FROM login_fail WHERE ip='".$_SERVER['REMOTE_ADDR']."'");

	if($chk_fail['fail_cnt']>=10) {
		if($chk_fail['update_datetime'] >= date("Y-m-d H:i:s", strtotime("-10 minutes")) ) {
			alert('헬로펀딩 보안정책에 의해 10분 동안 로그인이 중지됩니다.');
		}
	}
}
###############################################################################


// 차단된 아이디인가?
if ($mb['mb_intercept_date'] && $mb['mb_intercept_date'] <= date("Ymd", G5_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $mb['mb_intercept_date']);
    alert('회원님의 아이디는 접근이 금지되어 있습니다.\n처리일 : '.$date);
}

// 탈퇴한 아이디인가?
if ($mb['mb_leave_date'] && $mb['mb_leave_date'] <= date("Ymd", G5_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $mb['mb_leave_date']);
    alert('탈퇴한 아이디이므로 접근하실 수 없습니다.\n탈퇴일 : '.$date);
}

// 대출회원 로그인 금지
if ($mb['member_group']=='L') {
	alert('대출회원 전용페이지는 제작준비중 입니다.');
}

// 승인요청중인 회원인지 체크
if ($mb['mb_level']=='0') {
	if($mb['member_type']=='2') {

		$message = $mb['mb_co_name'] . "\\n\\n\\n현재 법인회원 신청 승인 중입니다.\\n\\n첨부문서 확인 후 연락드리겠습니다.\\n\\n감사합니다.";

		if(preg_match("/\/bbs\/login\.php/", $_SERVER['HTTP_REFERER'] )) {
			alert($message);
		}
		else {
			msg_replace($message, "/");		// 홈으로 이동
		}

	}
	else {
		if($mb['junior_doc1'] || $mb['junior_doc2'] || $mb['junior_doc3']) {
			alert('현재 가입심사중입니다.\\n\\n심사에 소요되는 시간은 영업일 기준 24시간 이내이며, 웹사이트 로그인시 확인하실 수 있습니다.\\n(승인심사 이전에 플랫폼 이용 및 투자는 참여하실 수 없습니다.)');
		}
		else {
			alert('현재 가입심사중입니다.\\n고객센터로 문의하여 주시기 바랍니다.');
		}
	}
}

// 승인거절자인지 체크
if ($mb['mb_level']=='100') {
	if($mb['member_type']=='2') {
		alert('법인정보 확인이 불가하여 가입 승인을 받지 못하였습니다.');
	}
	else {
		alert('본인 확인이 불가하여 가입 승인을 받지 못하였습니다.');
	}
}


// 메일인증여부 체크
if ($config['cf_use_email_certify'] && !preg_match("/[1-9]/", $mb['mb_email_certify'])) {
    $ckey = md5($mb['mb_ip'].$mb['mb_datetime']);
    confirm("{$mb['mb_email']} 메일로 메일인증을 받으셔야 로그인 가능합니다. 다른 메일주소로 변경하여 인증하시려면 취소를 클릭하시기 바랍니다.", G5_URL, G5_BBS_URL.'/register_email.php?mb_id='.$mb_id.'&ckey='.$ckey);
}

// 휴면계정 체크
if($_POST['mode']=='restclear') {
	sql_query("UPDATE {$g5['member_table']} SET is_rest='N' WHERE mb_no='".$mb['mb_no']."'");
	sql_fetch("INSERT INTO g5_member_rest_log (mb_no, gubun, rdate) VALUES ('".$mb['mb_no']."', 'unrest', NOW())");	// 휴면해제처리 로그 등록
}
else {
	if ($mb['member_group']=='F' && $mb['is_rest']=='Y') {
		echo "
			<script>
			if( confirm('안녕하세요 헬로펀딩입니다.\\n\\n회원님은 1년 이상 헬로펀딩 서비스를 이용하지 않아 휴면계정으로 전환되었습니다.\\n\\n휴면계정 해지를 원하시는 경우 아래 확인 버튼을 통해 다시 한번 로그인 해주세요.') ) {
				location.replace('/bbs/login.php?mode=restclear');
			}
			else {
				location.replace('/');
			}
			</script>";
		exit;
	}
}

// APP에서 로그인시 세션타임 설정
/*
if($_CONF['flatform']=='app') {
	ini_set("session.gc_maxlifetime", 86400*7);
}
*/

@include_once($member_skin_path.'/login_check.skin.php');


///////////////////////////////////////////////////////////////////////////////
// 관리자 권한 설정
///////////////////////////////////////////////////////////////////////////////
if($mb['mb_level'] >= '6' && $mb['mb_level'] <= '10') {

	if($mb['mb_level']=='9') {

		//부관리자 정보
		$SADMIN = sql_fetch("SELECT privacy_auth, allow_location FROM g5_sub_admin WHERE mb_no='".$mb['mb_no']."'");

		/*
		if($mb['mb_id']=='admin_sori9th') {
			echo "OFFICE_CONNECT : " . OFFICE_CONNECT . "<br>\n";
			echo "\$CONF['bypass_admin_outer_connect'] : " . $CONF['bypass_admin_outer_connect'] . "<br>\n";
			echo "\$SADMIN['allow_location'] : " . $SADMIN['allow_location'] . "<br>\n";
			exit;
		}
		*/

		// 서브관리자(level:9) 처리 :  개인정보열람권한 소유자라면 열람권한 부여
		if( OFFICE_CONNECT || ($CONF['bypass_admin_outer_connect'] && $SADMIN['allow_location']=='all') ) {

			set_session('ss_is_admin', true);

			// 개인정보 열람가능권한 소유자라면 열람권한 부여
			if($SADMIN['privacy_auth']=='Y') {
				set_session('ss_accounting_admin', true);
			}

			// 부관리자 로그인 기록 저장
			$insert_sql = "
				INSERT INTO
					g5_admin_login_log
				SET
					mb_no      = '".$mb['mb_no']."',
					mb_name    = '".$mb['mb_name']."',
					all_datetime = NOW(),
					all_ip     = '".$_SERVER['REMOTE_ADDR']."',
					all_device = '".getDevice()."'";
			sql_query($insert_sql);
			$login_idx = sql_insert_id();
			if($login_idx) {

				set_session('ss_admin_login_idx', $login_idx);

				$save_start_ymd = date('Y-m-d', strtotime("-12 month"));

				//12개월 이전 자료 삭제
				$delete_sql  = "DELETE FROM g5_admin_login_log WHERE mb_no='".$mb['mb_no']."' AND LEFT(all_datetime,10) < '".$save_start_ymd."'";
				sql_query($delete_sql);

				$delete_sql2 = "DELETE FROM g5_admin_access_log WHERE mb_no='".$mb['mb_no']."' AND LEFT(dt,10) < '".$save_start_ymd."'";
				sql_query($delete_sql2);

			}

		}

	}
	else {

		// 슈퍼관리자(level:10) 처리
		//if( OFFICE_CONNECT || $CONF['bypass_admin_outer_connect'] ) {
			set_session('ss_is_admin', true);
			set_session('ss_accounting_admin', true);
		//}
		//else {
			//alert('외부접속불가!!');
		//}

	}

}
///////////////////////////////////////////////////////////////////////////////


// 회원아이디 세션 생성
set_session('ss_mb_no', $mb['mb_no']);
set_session('ss_mb_id', $mb['mb_id']);
set_session('ss_mb_level', $mb['mb_level']);

// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
set_session('ss_mb_key', md5($mb['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

// 신한가상계좌발급안내문을 공지하기 위한 최종로그인시간 세션 발췌 (임시)2017-10-29
set_session('last_login', substr($mb['mb_today_login'], 0, 10));


// 포인트 체크 : 2022-04-20 Desabled
/*
if($config['cf_use_point']) {
	$sum_point = get_point_sum($mb['mb_id']);
	sql_query("UPDATE {$g5['member_table']} SET mb_point='".$sum_point."' WHERE mb_id='".$mb['mb_id']."'");
}
*/

// 3.26
// 아이디 쿠키에 한달간 저장
if($auto_login) {
    // 3.27
    // 자동로그인 ---------------------------
    // 쿠키 한달간 저장
    $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $mb['mb_password']);
    set_cookie('ck_mb_id', $mb['mb_id'], 86400 * 30);
    set_cookie('ck_auto', $key, 86400 * 30);
    // 자동로그인 end ---------------------------
}
else {
    set_cookie('ck_mb_id', '', 0);
    set_cookie('ck_auto', '', 0);
}

// 아이디 저장
if($id_save) {
	setcookie('ck_save_id', $mb['mb_id'], G5_SERVER_TIME+(86400*30), "/", G5_COOKIE_DOMAIN, true, true);		// 쿠키암호화 안함
}

if ($url) {
    // url 체크
    check_url_host($url);

    $link = urldecode($url);
    // 2003-06-14 추가 (다른 변수들을 넘겨주기 위함)
    if (preg_match("/\?/", $link))
        $split= "&amp;";
    else
        $split= "?";

    // $_POST 배열변수에서 아래의 이름을 가지지 않은 것만 넘김
    foreach($_POST as $key=>$value) {
        if ($key != 'mb_id' && $key != 'mb_password' && $key != 'x' && $key != 'y' && $key != 'url') {
            $link .= "$split$key=$value";
            $split = "&amp;";
        }
    }
}
else  {
    $link = G5_URL;
}


//////////////////////////////////////////////////
// 중앙기록관리 누적투자액 API를 이용하여 잔여투자한도 가져오기
//////////////////////////////////////////////////
if( date('H:i') < $CONF['P2PCTR_PAUSE']['STIME'] && date('H:i') > $CONF['P2PCTR_PAUSE']['ETIME'] ) {

	if( $mb['member_group']=='F' && in_array($mb['mb_level'], array('1','2','3','4','5')) && ($mb['va_bank_code2'] && $mb['virtual_account2']) ) {
		$exec_str = "/usr/local/php/bin/php -q ".G5_PATH."/investment/get_p2pctr_limit_amt.exec.php " . $mb['mb_no'];
		$exec_result = exec($exec_str);
	}

}


// 로그인 기록 등록 (2018-10-17부터 기록 시작함) ------------------
if($mb['mb_level']=='1') {
	$SLOG['ip']     = escape_trim($_SERVER['REMOTE_ADDR']);
	$SLOG['device'] = ( in_array(@strtolower(getDevice()), array('mobile','tablet')) ) ? 'm' : 'p';

	$log_sql = "
		INSERT INTO
			cf_login_success
		SET
			mb_no   = '".$mb['mb_no']."',
			mb_id   = '".$mb['mb_id']."',
			ip      = '".$SLOG['ip']."',
			device  = '".$SLOG['device']."',
			country = '".$IP_AREA['country_code']."',
			region  = '".$IP_AREA['region_name']."',
			city    = '".$IP_AREA['city']."',
			rdate   = '".G5_TIME_YMD."',
			rtime   = '".G5_TIME_HIS."'";
	sql_query($log_sql);

	unset($SLOG);
}
// 로그인 기록 등록 (2018-10-17부터 기록 시작함) ------------------

// 로그인 횟수 증가
sql_query("UPDATE ".$g5['member_table']." SET login_cnt=login_cnt+1 WHERE mb_id='".$mb['mb_id']."'");
sql_query("DELETE FROM login_fail WHERE ip='".$_SERVER['REMOTE_ADDR']."'");

// 첫 로그인 회원은 웰컴페이지 경유하여 홈페이지로 이동
$TMP = sql_fetch("SELECT login_cnt FROM ".$g5['member_table']." WHERE mb_id='".$mb['mb_id']."'");

if($TMP['login_cnt']==1) {
	msg_replace("", "/member/welcome.php");
}

if($_POST['mode']=='restclear') {
	msg_replace("휴면계정 해지가 완료되었습니다.\\n\\n감사합니다.", "/");
}
else {
	goto_url($link);
}


function chk_recaptcha() {
	global $gg_captcha_sskey;

	if (!isset($_POST['g-recaptcha-response'])) return false;

	$gg_response = trim($_POST['g-recaptcha-response']);
	if ($gg_response == "") return false;

	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array('secret' => $gg_captcha_sskey, 'response' => $gg_response, 'remoteip' => $_SERVER['REMOTE_ADDR']);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, sizeof($data));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	$obj = json_decode($result);

	if($obj->success == false) {
		return false;
	}

	return true;

}


?>