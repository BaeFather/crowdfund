<?

exit;		// 2022-06-27 폐기처리 (api서버로 이전처리 : 배부장)


################################################################################
# 실시간 포인트 출금가능금액 체크
# http://hellofunding.co.kr/deposit/deposit.php 에서 ajax로 호출
################################################################################

//set_time_limit(0);

// 자사 도메인이 아닌곳에서 호출된 경우 exit;
$allow_domain  = "hellofunding.co.kr";

if(isset($_SERVER['HTTP_REFERER'])) {
	if( !preg_match("/$allow_domain/i", $_SERVER['HTTP_REFERER']) ) { header('HTTP/1.1 404 Not Found'); exit; }
}
else {
	if(@$_REQUEST['mode']!='debug') { header('HTTP/1.1 404 Not Found'); exit; }
}


include_once("_common.php");

if(!$_SESSION['ss_mb_id']) { header('HTTP/1.1 404 Not Found'); exit; }

// 신한은행 점검시간 ---------------------------------
if( date('Y-m-d H:i:s') >= $CONF['BANK_STOP_SDATE'] && date('Y-m-d H:i:s') < $CONF['BANK_STOP_EDATE'] ) {
	echo '0';
	@sql_close();
	exit;
}


$WITHDRAWAL_POSIBLE_AMOUNT = array();
$WITHDRAWAL_POSIBLE_AMOUNT[] = $member['withdrawal_posible_amount'];

if( $member['member_group']=='F' && ($member['mb_level']>='1' && $member['mb_level']<='5') ) {

	if($member['virtual_account2']) {

		// 신한은행 출금가능금액 체크
		$exec_path = "/usr/local/php/bin/php -q " . G5_PATH . "/deposit/ajax_withdrawal_amount_check.cli.php " . $member['mb_no'] . " &";
		$WITHDRAWAL_POSIBLE_AMOUNT[] = shell_exec($exec_path);

	}

}

echo min($WITHDRAWAL_POSIBLE_AMOUNT);		// 출금가능금액 확정

@sql_close();
exit;



/*
//////////////////////////////////
// 출금가능금액 조회 (신한은행)
//////////////////////////////////
if($member['bank_code'] && $member['account_num'] && $member['va_bank_code2'] && $member['virtual_account2']) {
	// 고객 투자정보조회(4100)
	$ARR['REQ_NUM'] = "041";
	$ARR['CUST_ID'] = $member['mb_no'];
	$IB_RESULT = insidebank_request('256', $ARR);
	if($IB_RESULT['RCODE']=='00000000') {
		$WITHDRAWAL_POSIBLE_AMOUNT['bank'] = $IB_RESULT['WITH_AMT'];
	}
}
echo min($WITHDRAWAL_POSIBLE_AMOUNT);		// 출금가능금액 확정

@sql_close();
exit;
*/

?>