<?
die();
$toName = "전승찬"; //받는이 이름
$toMail = "jsc6176@hellofunding.co.kr"; //받는이 메일

$mail_content = "메일 test";   //메일 내용
$subject = "메일 TEST";  //메일 제목

$fromName = "sendmail";  //보내는이 이름
$fromMail = "sendmail@akeasd.co.kr";  //보내는이 메일


$headers = "Return-Path: <".$fromMail.">\n";
$headers .= "From: ".$fromName." <".$fromMail.">\n";
$headers .= "X-Sender: <".$fromMail.">\n";
$headers .= "X-Mailer: PHP\n";
$headers .= "Reply-To: ".$fromName." <".$fromMail.">\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html;charset=utf-8\n";

/*

$headers .= "To: ".  $tomail_name  ." <".  $mail_addr  .">\r\n";
$headers .= "Reply-To: ".$se_name." <".$se_email.">\r\n";
$headers .= "X-Priority: 3\r\n";
$headers .= "X-MSMail-Priority: High\r\n";
$headers .= "X-Mailer: Just My Server"; 

*/

 

$rs = @mail("$toName <$toMail>",$subject,$mail_content,$headers);

 
var_dump($rs);
 

if($rs) echo "전송성공";
else echo "전송실패";
?>
