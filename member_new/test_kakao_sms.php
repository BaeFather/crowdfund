<?

exit;

include_once("_common.php");
include_once(G5_PATH . "/lib/insidebank.lib.php");
include_once(G5_PATH . '/lib/sms.lib.php');

if($member['mb_no'] != '817') {
	header("http/1.0 404 not found");
	exit;
}


$mb_id = 'sori9th';
$member = get_member($mb_id);


//print_rr($_SERVER);
//print_rr($member, 'color:#AAA');


			// 카카오 메세지 발송
			$tcode = "hello002";
			$KaKao_Message_Send = new KaKao_Message_Send();
			$KaKao_Message_Send->MEMBER = $member;	// common.lib member 환경변수
			$KaKao_Message_Send->kakao_insert($tcode);



sql_close();
exit;

?>