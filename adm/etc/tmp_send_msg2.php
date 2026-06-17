<?

// 특정 조건의 번호로 문자 날리기

set_time_limit(0);

include_once("_common.php");
include_once("../../lib/sms.lib.php");

if(!$is_admin) exit;

$SEND_MSG['A'] = "[헬로펀딩] 신한은행 가상계좌 자동변경 안내\n\n" .
                 "{@name}님 가상계좌 : {@bank_name} {@vacct} {@vacct_name}\n\n" .
                 "회원님의 투자금과 예치금의 안전한 관리를 위한 신한은행 제3자 예치금관리 시스템 적용이 완료되어 기존 가상계좌에서 신한은행 가상계좌로 자동 변경되었습니다\n" .
                 "(기존 발급된 가상계좌는 이용하실 수 없습니다.)\n\n" .
                 "헬로펀딩의 예치금, 투자금, 상환금은 신한은행이 직접 관리하며 예치금은 실시간 출금이 가능하므로 더욱 안전하고 편리하게 헬로펀딩을 이용하실 수 있습니다.\n\n" .
                 "감사합니다.\n\n" .
                 "[헬로펀딩]\n" .
                 "홈페이지 바로가기: www.hellofunding.co.kr\n" .
                 "카톡바로상담 : goo.gl/ptTE1m\n" .
                 "고객센터: 1588-6760\n" .
                 "무료수신거부: 080-900-0982";

$SEND_MSG['B'] = "[헬로펀딩] 환급계좌 등록 및 가상계좌 발급 안내\n\n" .
                 "10월 15일 18시 이전에 예치금을 보유하였으나 환급계좌가 없으셨던 분들은 신한은행 가상계좌 발급 후 신한은행으로 기존 예치금이 이관된 이후 출금이 가능합니다.\n\n" .
                 "(신한은행 가상계좌 발급 후 신한은행으로 예치금 이관에 소요되는 시간은  영업일 48시간 이내입니다.)\n\n" .
                 "감사합니다.\n\n" .
                 "[헬로펀딩]\n" .
                 "홈페이지 바로가기: www.hellofunding.co.kr\n" .
                 "카톡바로상담 : goo.gl/ptTE1m\n" .
                 "고객센터: 1588-6760\n" .
                 "무료수신거부: 080-900-0982";

$send_date = date("Y-m-d H:i:s", time()+300);		// 5분뒤 발송


$sql  = "
	SELECT
		mb_no, member_type, mb_id, mb_name, mb_co_name, mb_hp, va_bank_code2, virtual_account2, va_private_name2
	FROM
		g5_member
	WHERE 1
		AND mb_id!=''
		AND mb_level='1'
		AND member_group='F'
	ORDER BY
		member_type DESC, mb_no";
$res  = sql_query($sql);
$rows = $res->num_rows;
for($i=0,$j=1; $i<$rows; $i++,$j++) {
	$LIST = sql_fetch_array($res);

	$LIST['mb_hp'] = preg_replace("/ /", "", $LIST['mb_hp']);

	$print_name = ( $LIST['member_type']=='2' ) ? $LIST['mb_co_name'] : $LIST['mb_name'];

	if( trim($LIST['virtual_account2']) ) {
		$send_msg = $SEND_MSG['A'];
		$send_msg = preg_replace("/\{@name\}/", $print_name, $send_msg);
		$send_msg = preg_replace("/\{@bank_name\}/", $BANK[$LIST['va_bank_code2']], $send_msg);
		$send_msg = preg_replace("/\{@vacct\}/", $LIST['virtual_account2'], $send_msg);
		$send_msg = preg_replace("/\{@vacct_name\}/", $LIST['va_private_name2'], $send_msg);
	}
	else {
		$send_msg = $SEND_MSG['B'];
	}

	if( $_REQUEST['action']==date('YmdHi') ) {
		if(unit_sms_send($_admin_sms_number, $LIST['mb_hp'], $send_msg, $send_date)) {
			debug_flush($LIST['mb_hp'] . "<br>\n");
		}
	}
	else {
		debug_flush("<pre style='font-size:12px'><b>[".$j."] ID: ".$LIST['mb_id']." (".$print_name.") / 연락처: ".$LIST['mb_hp']." / 신한가상계좌: ".$LIST['virtual_account2']."</b>\n\n\n".$send_msg . "</pre><br><hr style='border:1px dotted #000'><br>\n");
	}

	if($j%300==0) {
		sleep(2);
	}

}


debug_flush("<font color='red'>종료</font>");


exit;

?>