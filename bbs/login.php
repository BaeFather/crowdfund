<?php
include_once('./_common.php');

while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }
while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = preg_replace("/(\'|\"|\#|\=|\(|\)|\+|\%|\*)/iu", "$1;", $v); }

$g5['title'] = '로그인';

$g5['top_bn'] = "/images/member/sub_login.jpg";
$g5['top_bn_alt'] = "로그인 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

include_once('./_head.php');

$url = $_GET['url'];

// url 체크
check_url_host($url);

// 이미 로그인 중이라면
if ($is_member) {
    if ($url)
        goto_url($url);
    else
        goto_url(G5_URL);
}

$login_url        = login_url($url);
$login_action_url = G5_HTTPS_BBS_URL."/login_check.php";

// 로그인 스킨이 없는 경우 관리자 페이지 접속이 안되는 것을 막기 위하여 기본 스킨으로 대체
$login_file = $member_skin_path.'/login.skin.php';
if (!file_exists($login_file))
    $member_skin_path   = G5_SKIN_PATH.'/member/basic';


include_once($member_skin_path.'/login.skin.php');

include_once('./_tail.php');

