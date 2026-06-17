<?

exit;		// 2022-06-27 폐기처리

###############################################################################
## 신한은행측 출금가능금액 조회
## php -q /home/crowdfund/public_html/deposit/ajax_withdrawal_amount_check.cli.php 817
###############################################################################


set_time_limit(30);

$base_path = "/home/crowdfund/public_html";

include_once($base_path . "/common.cli.php");
include_once($base_path . "/lib/insidebank.lib.php");

$mb_no = @$_SERVER['argv']['1'];

if(!$mb_no) exit;

if( date('Y-m-d H:i:s') >= $CONF['BANK_STOP_SDATE'] && date('Y-m-d H:i:s') < $CONF['BANK_STOP_EDATE'] ) {

	echo '0';

}
else {

	// 고객 투자정보조회(4100)
	$ARR['REQ_NUM'] = '041';
	$ARR['CUST_ID'] = $mb_no;
	$IB_RESULT = insidebank_request('256', $ARR);
	if($IB_RESULT['RCODE']=='00000000') {
		$WITHDRAWAL_POSIBLE_AMOUNT['bank'] = $IB_RESULT['WITH_AMT'];
	}

	echo $WITHDRAWAL_POSIBLE_AMOUNT['bank'];

}

sql_close();
exit;

?>