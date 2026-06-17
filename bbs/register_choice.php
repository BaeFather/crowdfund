<?php
include_once('./_common.php');

// 로그인중인 경우 회원가입 할 수 없습니다.
if ($is_member) {
    goto_url(G5_URL);
}

// 세션을 지웁니다.
set_session("ss_mb_reg", "");

$g5['title'] = '회원가입';

$g5['top_bn'] = "/images/member/sub_join.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";


include_once('./_head.php');

//echo $member_skin_path;

include_once($member_skin_path.'/register.choice.skin.php');

include_once('./_tail.php');
?>
