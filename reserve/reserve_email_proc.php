<?php
include_once('./_common.php');
include_once('../lib/class_mailsend.php');

$subject  = $_POST['subject'];
$email    = $_POST['email'];
$contents = $_POST['content'];
$contents = preg_replace('/<script/i', '<x-script', $contents);
$contents = preg_replace('/<\/script/i', '</x-script', $contents);
$contents = preg_replace('/\\n/', '<br>', htmlspecialchars($contents));

//----- 수신인(관리자) 메일 주소
$query = " SELECT cf_admin_email FROM g5_config WHERE cf_admin = 'admin' ";
$row   = sql_fetch($query);
$to    = $row['cf_admin_email'];

//----- 헤더(대출신청인 메일주소)
$headers = "From: ".$email."\r\n";

//----- 메일발송
//$rst = mail($to, $subject, $contents, $headers);

$MAIL = array(
          'subject'      => $subject,
          'senderName'   => $senderName,
          'senderMail'   => $email,
          'receiverMail' => $to,
          'contents'     => $contents,
					'multi-part'   => 'false'
				);

$mailSend = new MailSend;
$mailSend->NewHeader($MAIL['subject'], $MAIL['senderName'], $MAIL['senderMail'], $MAIL['multi-part']);
$mailSend->AddBody($MAIL['contents']);
$rst = $mailSend->SendMail($MAIL['receiverMail']);

if($rst) {
	echo('o');
}
else {
	echo('x');
}


/*
include_once('./_common.php');

include_once('../lib/mailer.lib.php');

/* 관리자 SMS 전송 (타입번호 : 8. Q&A 접수완료)
include_once('../lib/sms.lib.php');
$sms_res = select_sms_send('admin', 8, '', '01087166747');


$mail_subject = '[Q&A] '.$_POST['subject'];

$result = mailer($_POST['suject'], trim($_POST['email']), $config['cf_admin_email'], $mail_subject, '<div style="font-size:12pt;">'.nl2br($_POST['contents']).'</div>', 1);

echo $result;
*/


?>