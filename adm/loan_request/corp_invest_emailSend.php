<?
include_once('./_common.php');

$idx = $_REQUEST["idx"];

$to = 'pjm@trinitytax.co.kr';

$sql = "
	SELECT
		A.*,
		B.mb_id,
		(SELECT mb_name FROM g5_member WHERE mb_id=A.check_admin_id) AS admin_name
	FROM
		cf_care_service_request A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE (1)
		AND A.idx='".$idx."' AND is_drop=''";
$DATA = sql_fetch($sql);

$print_content = ($DATA['content']) ? nl2br(htmlSpecialChars($DATA['content'])) : '';

$display_is_est = '';  // 출력을 위한 변수 선언, 초기화
if($DATA['is_est'] == 'N') {$display_is_est = "설립예정";} else {$display_is_est = "설립완료";}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {margin: 0; padding: 0;}
		.email_chk_txt {font-size: 20px; font-weight: 700; color: #2f2f2f; text-align: center;}
		.email_send_box .title {font-size: 15px; padding: 15px 0;}
        .email_send_box {padding: 10px 20px; border: 1px dotted #b7b7b7; border-radius: 10px; margin: 0 20px;}
        .email_send_box .email_send_list {padding: 0;}
        .email_send_box .email_send_list li {list-style: none; line-height: 1.7;}
        input[name='email_submit'] {display: block; padding: 12px 28px; border: 0; background-color: #525252; color: #fff; border-radius: 5px; margin: 30px auto; cursor: pointer;}
		.email_send_box .email_send_list li:last-child {white-space: pre-line;}
    </style>
</head>

<body>
<p class="email_chk_txt">이메일 내용 확인<br/>=> <?=$to?></p>
<form method="post" name="f">
	<input type="hidden" name="idx" value="<?=$idx?>"/>  
	<input type="hidden" name="mode" value=""/>
    
	<div class="email_send_box">
        <p>안녕하세요 헬로핀테크입니다.<br />법인투자 문의 전달해드립니다.</p>
        <ul class="email_send_list">
            <li><b>성함 :</b> <?=$DATA['name']?></li>
            <li><b>연락처 :</b> <?=$DATA['phone']?></li>
            <li><b>법인 설립여부 :</b> <?=$display_is_est?></li>
            <li><b>문의내용 :</b> <br/><?=$print_content?></li>
            <li><b>담당자 코멘트 :</b> <br/><?=$DATA['admin_content']?></li>
        </ul>
        <p>문의 사항은 언제든 연락 부탁드립니다.<br />감사합니다.</p>
    </div>
	<input type="button" value="발송" name="email_submit" onclick="goSend();"/>
</form>
</body>
</html>
<script type="text/javascript">
	function goSend() {
		var yn = confirm("이메일을 발송하시겠습니까?");
		if(yn == true){
			var f = document.f;
			f.mode.value = 'send';
			f.submit();
		} 
	}
</script>

<?
if($_REQUEST["mode"]=='send') {
	$charset = 'UTF-8';
	
	$subject = "=?".$charset."?B?".base64_encode('[헬로핀테크] 법인투자 문의 내용 전달')."?=";
	$contents = '안녕하세요 헬로핀테크입니다.<br />법인투자 문의 전달해드립니다.</p><br /><br />';
	$contents .= '<p style="line-height: 1;"><b>성함 :</b> '.$DATA['name'].'</p><br />';
	$contents .= '<p style="line-height: 1;"><b>연락처 :</b> '.$DATA['phone'].'</p><br />';
	$contents .= '<p style="line-height: 1;"><b>법인 설립여부 :</b> '.$display_is_est.'</p><br />';
	$contents .= '<p style="line-height: 1.5;"><b>문의내용 :</b> <br/>'.$print_content.'</p><br />';
	$contents .= '<p style="line-height: 1.5; white-space: pre-line;"><b>담당자 코멘트 :</b> <br/>'.$DATA['admin_content'].'</p>';
	$contents .= '<br /><br />문의 사항은 언제든 연락 부탁드립니다.<br />감사합니다.';
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=utf-8; Content-Transfer-Encoding: 8bit; format=flowed\r\n';
	$headers[] = 'From: '.iconv('utf-8', 'euc-kr', $DATA['name']).'<'.$DATA['email'].'>';

    $result = mail($to, $subject, $contents, implode("\r\n", $headers));
	if($result) {
		echo "<script>alert('이메일 발송 완료되었습니다.'); self.close();</script>";
		
	} else {
		echo "<script>alert('이메일 발송 실패하였습니다.');</script>";
	}
}
?>

