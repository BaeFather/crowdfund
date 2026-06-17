<?php

header("location:https://www.hellofunding.co.kr"); exit;

/*
if(!preg_match('/220\.117\.134/', $_SERVER['REMOTE_ADDR'])) {
	header("HTTP/1.0 404 Not Found");
	die();
}
*/


/*******************************************************************************
** 공통 변수, 상수, 코드
*******************************************************************************/
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );

// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
@header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');


//==========================================================================================================================
// extract($_GET); 명령으로 인해 page.php?_POST[var1]=data1&_POST[var2]=data2 와 같은 코드가 _POST 변수로 사용되는 것을 막음
// 081029 : letsgolee 님께서 도움 주셨습니다.
//--------------------------------------------------------------------------------------------------------------------------
$ext_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
				  'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
				  'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$ext_cnt = count($ext_arr);
for ($i=0; $i<$ext_cnt; $i++) {
	// POST, GET 으로 선언된 전역변수가 있다면 unset() 시킴
	if (isset($_GET[$ext_arr[$i]]))  unset($_GET[$ext_arr[$i]]);
	if (isset($_POST[$ext_arr[$i]])) unset($_POST[$ext_arr[$i]]);
}
//==========================================================================================================================


function root_path()
{
	$chroot = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], dirname(__FILE__)));
	$result['path'] = str_replace('\\', '/', $chroot.dirname(__FILE__));
	$tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
	$document_root = str_replace($tilde_remove, '', $_SERVER['SCRIPT_FILENAME']);
	$pattern = '/' . preg_quote($document_root, '/') . '/i';
	$root = preg_replace($pattern, '', $result['path']);
	$port = ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':'.$_SERVER['SERVER_PORT'];
	$http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
	$user = str_replace(preg_replace($pattern, '', $_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']);
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	if(isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host))
		$host = preg_replace('/:[0-9]+$/', '', $host);
	$host = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/", '', $host);
	$result['url'] = $http.$host.$port.$user.$root;
	return $result;
}

$hf_path = root_path();
$DOCUMENT_RT = $hf_path['path'];
//$ORI_ROOT = str_replace('/chosun2','',$DOCUMENT_ROOT);
$ORI_ROOT = $_SERVER['DOCUMENT_ROOT'];
unset($tmp_path);

// SQL Injection 대응 문자열 필터링
function sql_escape_string($str)
{
	if(defined('G5_ESCAPE_PATTERN') && defined('G5_ESCAPE_REPLACE')) {
		$pattern = G5_ESCAPE_PATTERN;
		$replace = G5_ESCAPE_REPLACE;

		if($pattern)
			$str = preg_replace($pattern, $replace, $str);
	}

	$str = call_user_func('addslashes', $str);

	return $str;
}

// PHP 4.1.0 부터 지원됨
// php.ini 의 register_globals=off 일 경우
@extract($_GET);
@extract($_POST);
@extract($_SERVER);


$config = array();
$member = array();
$board  = array();
$group  = array();
$hf     = array();


include_once($DOCUMENT_RT.'/lib/dbconfig.php');
include_once($DOCUMENT_RT.'/hf_config.php');
include_once($DOCUMENT_RT.'/lib/lib_common.php');
include_once($DOCUMENT_RT.'/lib/investment.lib.php');
include_once($DOCUMENT_RT.'/lib/sms_dbconfig.php');		// 메세지 발송 외부 DB설정
include_once($DOCUMENT_RT.'/lib/crypt.lib.php');		// 신규 암호화


define('HF_THEME_PATH',		$DOCUMENT_RT.'/theme');
define('HF_THEME_URL',		$DOCUMENT_RT.'/theme');
define('G5_THEME_MOBILE_PATH', $theme_path.'/'.G5_MOBILE_DIR);
define('G5_THEME_LIB_PATH',	$theme_path.'/'.G5_LIB_DIR);
define('HF_THEME_CSS_URL',	 HF_THEME_URL.'/css');
define('G5_THEME_IMG_URL',	 G5_THEME_URL.'/'.G5_IMG_DIR);
define('HF_THEME_JS_URL',	 HF_THEME_URL.'/js');

$connect_db = sql_connect(HF_MYSQL_HOST, HF_MYSQL_USER, HF_MYSQL_PASSWORD) or die('MySQL Connect Error!!!');

$select_db  = sql_select_db(HF_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');
$hf['connect_db'] = $connect_db;
$g5['connect_db'] = $connect_db;
sql_set_charset('utf8', $connect_db);


//==============================================================================
//==============================================================================
// SESSION 설정
//------------------------------------------------------------------------------
@ini_set("session.use_trans_sid", 0);	 // PHPSESSID를 자동으로 넘기지 않음
@ini_set("url_rewriter.tags","");		 // 링크에 PHPSESSID가 따라다니는것을 무력화함 (해뜰녘님께서 알려주셨습니다.)

session_save_path(HF_SESSION_PATH);

if (isset($SESSION_CACHE_LIMITER))
	@session_cache_limiter($SESSION_CACHE_LIMITER);
else
	@session_cache_limiter("no-cache, must-revalidate");

session_cache_limiter('');

ini_set("session.cache_expire", 180);	 // 세션 캐쉬 보관시간 (분)
ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
ini_set("session.gc_probability", 1);	 // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
ini_set("session.gc_divisor", 100);	   // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.

session_set_cookie_params(0, '/');
ini_set("session.cookie_domain", HF_COOKIE_DOMAIN);

@session_start();
// PHPSESSID가 이미 세팅되어 있고 이 값이 비어있지 않은 경우
// 세션이 이미 사용중이다.
// 이 경우 무조건 session_start() 호출
$session_name = session_name();
if (($session_id = $_COOKIE[$session_name]) != false) {
	  @session_start();
} else {
	// 현재 세션이 열려있지 않은 상태이다.
	// 모듈 어딘가에서 $_SESSION값을 세팅하고 바로 끝내버리는 경우도 있으므로
	// 이를 위해서 lazy_session_start를 shutdown_function으로 등록한다.
	register_shutdown_function('lazy_session_start');
}

//==============================================================================
// 사용기기 설정
// config.php G5_SET_DEVICE 설정에 따라 사용자 화면 제한됨
// pc 설정 시 모바일 기기에서도 PC화면 보여짐
// mobile 설정 시 PC에서도 모바일화면 보여짐
// both 설정 시 접속 기기에 따른 화면 보여짐
//------------------------------------------------------------------------------
$is_mobile = false;
$set_device = true;
if(defined('G5_SET_DEVICE')) {
	switch(G5_SET_DEVICE) {
		case 'pc':
			$is_mobile  = false;
			$set_device = false;
		break;
		case 'mobile':
			$is_mobile  = true;
			$set_device = false;
		break;
		default:
		break;
	}
}

//==============================================================================
// Mobile 모바일 설정
// 쿠키에 저장된 값이 모바일이라면 브라우저 상관없이 모바일로 실행
// 그렇지 않다면 브라우저의 HTTP_USER_AGENT 에 따라 모바일 결정
// G5_MOBILE_AGENT : config.php 에서 선언
//------------------------------------------------------------------------------
$was_mobile = is_mobile();
if(G5_USE_MOBILE && $set_device) {
	if($_REQUEST['device']=='pc')             $force_mobile = false;
	else if($_REQUEST['device']=='mobile')    $force_mobile = true;
	else if(isset($_SESSION['ss_is_mobile'])) $force_mobile = $_SESSION['ss_is_mobile'];
}
else {
	$set_device = false;
}

$is_mobile = isset($force_mobile) ? $force_mobile : $was_mobile;

if($set_device && isset($force_mobile)) {
	if($is_mobile != $was_mobile)
		$_SESSION['ss_is_mobile'] = $force_mobile;
	else
		unset($_SESSION['ss_is_mobile']);
}
define('G5_IS_MOBILE', $is_mobile);

define('G5_DEVICE_BUTTON_DISPLAY', $set_device);
if(G5_IS_MOBILE) {
	//$g5['mobile_path'] = G5_PATH.'/'.$g5['mobile_dir'];
	$g5['mobile_path'] = '/mobile';
}



//==============================================================================
// 공용 변수
//------------------------------------------------------------------------------
// 기본환경설정
// 기본적으로 사용하는 필드만 얻은 후 상황에 따라 필드를 추가로 얻음
$config = sql_fetch(" select * from {$hf['config_table']} ");


// 조선일보 땅집Go 요청에 대한 별도 세션 발행
/*
	if (preg_match("/\/chosun/i", $_SERVER['PHP_SELF'])) {
		if($_GET['SESS_KEY']) {
			die("땅집고");
		} else {
			if(!preg_match("/chosun/i", $_COOKIE['PHPSESSID'])) {
				$_CONF['SESS_ID'] = uniqid('chosun'.date('ymd'));
				session_destroy();
				setcookie("PHPSESSID", "", time()-3600, "/");
				session_commit();
				session_id($_CONF['SESS_ID']);
				session_start();
			}
		}
	}
*/


/*
if( preg_match("/dev2\.wow4989\.co\.kr|wowstar\.co\.kr/i", $_SERVER['HTTP_REFERER']) || preg_match("/\/syndicate\/wowstar/i", $_SERVER['PHP_SELF']) ) {
	if($_GET['SESS_KEY']) {
		if($_GET['SESS_KEY']!=$_COOKIE['PHPSESSID']) {
			$_CONF['SESS_ID'] = $_GET['SESS_KEY'];
			session_destroy();
			setcookie("PHPSESSID", "", time()-3600, "/");
			session_commit();
			session_id($_CONF['SESS_ID']);
			session_start();
		}
	}
	else {
		if(!preg_match("/wowstar/i", $_COOKIE['PHPSESSID'])) {
			$_CONF['SESS_ID'] = uniqid('wowstar'.date('ymd'));
			session_destroy();
			setcookie("PHPSESSID", "", time()-3600, "/");
			session_commit();
			session_id($_CONF['SESS_ID']);
			session_start();
		}
	}
}
*/

// 자동로그인 부분에서 첫로그인에 포인트 부여하던것을 로그인중일때로 변경하면서 코드도 대폭 수정하였습니다.
if ($_SESSION['ss_mb_id']) { // 로그인중이라면
	$member = get_member($_SESSION['ss_mb_id']);

	// 차단된 회원이면 ss_mb_id 초기화
	if($member['mb_intercept_date'] && $member['mb_intercept_date'] <= date("Ymd", G5_SERVER_TIME)) {
		set_session('ss_mb_id', '');
		$member = array();
	} else {
		// 오늘 처음 로그인 이라면
		if (substr($member['mb_today_login'], 0, 10) != G5_TIME_YMD) {
			// 첫 로그인 포인트 지급
			//insert_point($member['mb_id'], $config['cf_login_point'], HF_TIME_YMD.' 첫로그인', '@login', $member['mb_id'], HF_TIME_YMD);

			// 오늘의 로그인이 될 수도 있으며 마지막 로그인일 수도 있음
			// 해당 회원의 접근일시와 IP 를 저장
			$sql = " update {$hf['member_table']} set mb_today_login = '".HF_TIME_YMDHIS."', mb_login_ip = '{$_SERVER['REMOTE_ADDR']}' where mb_id = '{$member['mb_id']}' ";
			sql_query($sql);
		}
	}
} else {
	// 자동로그인 ---------------------------------------
	// 회원아이디가 쿠키에 저장되어 있다면 (3.27)
	if ($tmp_mb_id = get_cookie('ck_mb_id')) {

		$tmp_mb_id = substr(preg_replace("/[^a-zA-Z0-9_]*/", "", $tmp_mb_id), 0, 20);
		// 최고관리자는 자동로그인 금지
		if (strtolower($tmp_mb_id) != strtolower($config['cf_admin'])) {
			$sql = " select mb_password, mb_intercept_date, mb_leave_date, mb_email_certify from {$hf['member_table']} where mb_id = '{$tmp_mb_id}' ";
			$row = sql_fetch($sql);
			if($row['mb_password']){
				$key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $row['mb_password']);
				// 쿠키에 저장된 키와 같다면
				$tmp_key = get_cookie('ck_auto');
				if ($tmp_key === $key && $tmp_key) {
					// 차단, 탈퇴가 아니고 메일인증이 사용이면서 인증을 받았다면
					if ($row['mb_intercept_date'] == '' &&
						$row['mb_leave_date'] == '' &&
						(!$config['cf_use_email_certify'] || preg_match('/[1-9]/', $row['mb_email_certify'])) ) {
						// 세션에 회원아이디를 저장하여 로그인으로 간주
						set_session('ss_mb_id', $tmp_mb_id);

						// 페이지를 재실행
						echo "<script type='text/javascript'> window.location.reload(); </script>";
						exit;
					}
				}
			}
			// $row 배열변수 해제
			unset($row);
		}
	}
	// 자동로그인 end ---------------------------------------
}

// 회원, 비회원 구분
$is_member = $is_guest = false;
$is_admin = '';
if ($member['mb_id']) {
	$is_member = true;
	$is_admin = is_admin($member['mb_id']);
	$member['mb_dir'] = substr($member['mb_id'],0,2);
} else {
	$is_guest = true;
	$member['mb_id'] = '';
	$member['mb_level'] = 1; // 비회원의 경우 회원레벨을 가장 낮게 설정
}

ob_start();

// 자바스크립트에서 go(-1) 함수를 쓰면 폼값이 사라질때 해당 폼의 상단에 사용하면
// 캐쉬의 내용을 가져옴. 완전한지는 검증되지 않음
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: public, must-revalidate, pre-check=0, post-check=0');
if ($is_member) {
	$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
	header('Expires: 0'); // rfc2616 - Section 14.21
	header('Last-Modified: ' . $gmnow);
	header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0'); // HTTP/1.1
	header('Cache-Control: no-store, no-cache', false); // HTTP/1.1
	header('Pragma: no-cache'); // HTTP/1.0
}

$html_process = new html_process();
?>