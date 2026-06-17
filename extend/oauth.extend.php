<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 네이버로그인 API 정보
define('G5_NAVER_OAUTH_CLIENT_ID',  'CR6YcKM_M1pGPXuPhuAn');
define('G5_NAVER_OAUTH_SECRET_KEY', 'uPdeAMSuzp');

// 카카오로그인 API 정보
define('G5_KAKAO_OAUTH_REST_API_KEY', '46b87f69bfdcb38e39a5965ced46dc8c');

// 페이스북로그인 API 정보
define('G5_FACEBOOK_CLIENT_ID',  '881768821957389');
define('G5_FACEBOOK_SECRET_KEY', '3d1297e66a3a0ebb8c430b42d0d700c8');

// 구글+ 로그인 API 정보
define('G5_GOOGLE_CLIENT_ID',  '827218894903-0kbgc1uoqijlcfdu1k65gp8hggv4e1ot.apps.googleusercontent.com');
define('G5_GOOGLE_SECRET_KEY', 'P4FAwQ_WqLdbZOPZGCbssDL2');

// OAUTH Callback URL
define('G5_OAUTH_CALLBACK_URL', G5_PLUGIN_URL.'/oauth/callback.php');

if($oauth_mb_no = get_session('ss_oauth_member_no')) {
    $member = get_session('ss_oauth_member_'.$oauth_mb_no.'_info');
    $is_member = true;
    $is_guest  = false;
}
?>