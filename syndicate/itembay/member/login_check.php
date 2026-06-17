<?php
include_once('./_common.php');

$gg_captcha_sskey = "6LeVVmcUAAAAALzX8kaOe1CyxhyT0Gmejwlx6H1R";


if (isset($_POST['g-recaptcha-response'])) {
	$chk_gg_cap = chk_recaptcha();
	if ($chk_gg_cap) {
	} else {
		alert("인증 오류");
		exit;
	}
}

$g5['title'] = "로그인 검사";

$mb_id       = trim($_POST['mb_id']);
$mb_password = trim($_POST['mb_password']);


if (!$mb_id || !$mb_password)
    alert('회원아이디나 비밀번호가 공백이면 안됩니다.');

$mb = get_member($mb_id);

if(!$mb['mb_id']) {
	alert('가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.');
}

###############################################################################
## 비밀번호 SHA256 해시 적용
###############################################################################
$pwd_valid = false;

if(check_password2($mb_password, $mb['mb_password'])) {
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

if (!$pwd_valid) {

	// 2018-07-31 전승찬 추가
	$chk_fail = sql_fetch("select max(fail_count) fail_cnt, update_datetime from login_fail where ip='".$_SERVER['REMOTE_ADDR']."'");
	if ($chk_fail['fail_cnt']) {
		if ($chk_fail['fail_cnt']>=10) {

			if ($chk_fail['update_datetime'] >= date("Y-m-d H:i:s", strtotime("-10 minutes")) ) {
				alert('헬로펀딩 보안정책에 의해 10분 동안 로그인이 중지됩니다.');
			} else {
				$login_fail_sql = "update login_fail set fail_count=5, mb_id='".$mb_id."', update_datetime=now() where ip='".$_SERVER['REMOTE_ADDR']."'";
			}

		} else {
			$login_fail_sql = "update login_fail set fail_count=fail_count+1, mb_id='".$mb_id."', update_datetime=now() where ip='".$_SERVER['REMOTE_ADDR']."'";
		}
	} else {
		$login_fail_sql = "insert into login_fail set mb_id='".$mb_id."', fail_count=1 , ip='".$_SERVER['REMOTE_ADDR']."', update_datetime=now()";
	}
	sql_query($login_fail_sql);

	$plus1 = $chk_fail['fail_cnt']+1;

	if ($plus1>5) {
		alert('가입된 회원아이디가 아니거나 비밀번호가 틀린 이유로\n로그인에 '.$plus1.'회 실패하였습니다.\n10회 실패시 10분간 동일IP에서 로그인이 중지됩니다.');
	} else {
		alert('가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\\n비밀번호는 대소문자를 구분합니다.');
	}

} else {

	$chk_fail = sql_fetch("select max(fail_count) fail_cnt, update_datetime from login_fail where ip='".$_SERVER['REMOTE_ADDR']."'");
	if ($chk_fail['fail_cnt']) {
		if ($chk_fail['fail_cnt']>=10) {

			if ($chk_fail['update_datetime'] >= date("Y-m-d H:i:s", strtotime("-10 minutes")) ) {
				alert('헬로펀딩 보안정책에 의해 10분 동안 로그인이 중지됩니다.');
			}
		}
	}
}

// 차단된 아이디인가?
if ($mb['mb_intercept_date'] && $mb['mb_intercept_date'] <= date("Ymd", HF_SERVER_TIME)) {
    $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $mb['mb_intercept_date']);
    alert('회원님의 아이디는 접근이 금지되어 있습니다.\n처리일 : '.$date);
}

// 탈퇴한 아이디인가?
if ($mb['mb_leave_date'] && $mb['mb_leave_date'] <= date("Ymd", HF_SERVER_TIME)) {
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
		alert("업체명: ".$mb['mb_co_name']."\\n\\n\\n현재 법인회원 신청 승인 중입니다.\\n\\n첨부문서 확인 후 연락드리겠습니다.\\n\\n감사합니다.");
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
				location.replace('/member/login.php?mode=restclear');
			}
			else {
				location.replace('/');
			}
			</script>";
		exit;
	}
}

@include_once($member_skin_path.'/login_check.skin.php');




// 접속IP 확인, 사내망일경우 로그인 허용, 외부망일경우 관리자 전용 로그인 페이지로 이동 2017-11-17
/*
if(in_array($mb['mb_level'], array(9, 10)) && !preg_match('/220\.117\.134|1\.232\.187\.196/i', $_SERVER['REMOTE_ADDR'])) {
    set_session('ss_is_admin', false);
}else{
    set_session('ss_is_admin', true);
}
*/

// 슈퍼마스터 개인정보 열람권한 부여
/*
if($mb['mb_level']=='10') {
	set_session('ss_accounting_admin', true);
}
*/

// 회원아이디 세션 생성
set_session('ss_mb_id', $mb['mb_id']);

// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
set_session('ss_mb_key', md5($mb['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

// 신한가상계좌발급안내문을 공지하기 위한 최종로그인시간 세션 발췌 (임시)2017-10-29
set_session('last_login', substr($mb['mb_today_login'], 0, 10));

/*if($mb['mb_id']=='gsh0301') {
	// 경영지원팀 회계담당자 플래그
	set_session('ss_accounting_admin', true);
}*/
// 포인트 체크
if($config['cf_use_point']) {
    $sum_point = get_point_sum($mb['mb_id']);

    $sql= "UPDATE {$hf['member_table']} SET mb_point='$sum_point' WHERE mb_id='".$mb['mb_id']."'";
    sql_query($sql);
}

// 3.26
// 아이디 쿠키에 한달간 저장
if ($auto_login) {
    // 3.27
    // 자동로그인 ---------------------------
    // 쿠키 한달간 저장
    $key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $mb['mb_password']);
    set_cookie('ck_mb_id', $mb['mb_id'], 86400 * 31);
    set_cookie('ck_auto', $key, 86400 * 31);
    // 자동로그인 end ---------------------------
} else {
    set_cookie('ck_mb_id', '', 0);
    set_cookie('ck_auto', '', 0);
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
    //$link = HF_URL;
	//$link = BSC_URL;
	$link = "/";
}

$IP_AREA = IP_AREA($_SERVER['REMOTE_ADDR']);

// 로그인 기록 등록 (2018-10-17부터 기록 시작함) ------------------
if($mb['mb_level']=='1') {
	$SLOG['ip']     = $_SERVER['REMOTE_ADDR'];
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
			rdate   = '".HF_TIME_YMD."',
			rtime   = '".HF_TIME_HIS."'";
	sql_query($log_sql);

	unset($SLOG);
}
// 로그인 기록 등록 (2018-10-17부터 기록 시작함) ------------------

// 로그인 횟수 증가
sql_query("UPDATE {$hf['member_table']} SET login_cnt=login_cnt+1 WHERE mb_id='".$mb['mb_id']."'");
sql_query("delete from login_fail where ip='".$_SERVER['REMOTE_ADDR']."'");

if($_POST['mode']=='restclear') {
	echo "
		<script>
			alert('휴면계정 해지가 완료되었습니다.\\n\\n감사합니다.');
			location.replace('/');
		</script>";
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