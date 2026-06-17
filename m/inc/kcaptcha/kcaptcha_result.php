<?
header("Content-Type: text/html; charset=utf-8");
// 캡챠 세션값과 비교하여 맞는지? 틀린지? 결과값을 출력합니다.
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/common.php";
INCLUDE $_SERVER["DOCUMENT_ROOT"]."/inc/function.php";

$count = (int)get_session2("captcha_count");
if ($count >= 5) { // 설정값 이상이면 자동등록방지 입력 문자가 맞아도 오류 처리
    echo false;
} else {
    set_session("captcha_count", $count + 1);
    echo (get_session2("captcha_keystring") == $_POST['captcha_key']) ? true : false;
}
?>