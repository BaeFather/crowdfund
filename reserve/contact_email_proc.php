<?

include_once('./_common.php');
include_once(G5_LIB_PATH . '/mailer.lib.php');
include_once(G5_LIB_PATH . '/sms.lib.php');

/* 관리자 SMS 전송 (타입번호 : 7. 제휴상담 접수완료) */

$ad_member   = get_member('admin');
$replace_arr = array();
$sms_res     = select_sms_send('admin', 7, $replace_arr, $ad_member['mb_hp']);

$mail_subject = '[제휴문의] '.$_POST['subject'];

$result = mailer($_POST['suject'], trim($_POST['email']), $config['cf_admin_email'], $mail_subject, '<div style="font-size:12pt;">'.nl2br($_POST['contents']).'</div>', 1);

echo $result;


?>