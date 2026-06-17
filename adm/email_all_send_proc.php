<?

set_time_limit(0);

include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

auth_check($auth[$sub_menu], 'w');


// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}

if(count($chk) == 0) {

	debug_flush("
		<script>
		alert('발송할 회원이 선택되지 않았습니다.');
		top.location.reload();
		</script>
	");

	exit;

}
else {


	$sql = "
		INSERT INTO
			g5_mailling_list
		SET
			email_title    = '".$email_title."',
			email_contents = '".$email_contents."',
			regdate        = NOW()";
	sql_query($sql);
	$mailing_idx = sql_insert_id();

	$mail_subject = $email_title;
	$from_mail = G5_SMTP_ID;
	//$from_mail = $CONF['customer_mail'];

	$j=1;
	$CNT = array('SUCC'=>0, 'FAIL'=>0);

	foreach($chk as $k => $v) {

		$TO_EMAIL = explode('^', $v);
		$to_mail = trim($TO_EMAIL[0]);
		$to_name = trim($TO_EMAIL[1]);
		$to_name_mail = $to_name."<".$to_mail.">";
		$send_result = "";

		if($to_mail) {

			if(mailer("헬로펀딩", $from_mail, $to_mail, $to_name, $mail_subject, $email_contents, 1)) {
				$send_result = "SUCCESS";
				$CNT['SUCC']++;
			}
			else {
				$send_result = "FAIL";
				$CNT['FAIL']++;
			}

			$sql = "
				INSERT INTO
					g5_mailling_detail
				SET
					list_idx   = '".$mailing_idx."',
					fromMail   = '".$from_mail."',
					toMail     = '".$to_name_mail."',
					sendResult = '".$send_result."',
					rdate      = NOW()";
			sql_query($sql);
			sql_query("UPDATE g5_mailling_list SET succ_count='".$CNT['SUCC']."', fail_count='".$CNT['FAIL']."' WHERE idx='".$mailing_idx."'");

			debug_flush("<span style='font-size:11px'>" . $j . ": " . $to_name . "(" . $to_mail . ") >>>> ".$send_result."</span><br>\n");

			//if(($j%300)==0) usleep(500000);

			$j++;

		}

	}

	debug_flush("
		<script>
		alert('메일 발송이 완료 되었습니다.\\n\\n'
		    + ' 정상발송: ".number_format($CNT['SUCC'])."건\\n'
				+ ' 발송실패: ".number_format($CNT['FAIL'])."건');
		top.location.reload();
		</script>
	");


}

exit;

?>