<?

$menu['menu500'] = array (
	array('500000', '예치금', '#', 'deal'),
	//array('500050', '투자 내역', G5_ADMIN_URL.'/inv_list.php', 'inv'),
	array('500100', '신한 일별거래내역', G5_ADMIN_URL.'/shinhan_deal_daylog.php', 'deal'),
	array('500200', '예치금관리', G5_ADMIN_URL.'/balance_list.php', 'balance'),
	array('500300', '출금관리', G5_ADMIN_URL.'/withdrawal_list.php', 'withdrawal'),
	array('500400', '가상계좌입금내역', G5_ADMIN_URL.'/deposit_withdrawal/vact_log.php', 'withdrawal'),
//array('500400', '가상계좌입금내역', G5_ADMIN_URL.'/vact_log_shinhan.php', 'withdrawal'),
	array('500500', '가상계좌입금내역(구)', G5_ADMIN_URL.'/vact_log.php', 'withdrawal'),
	array('500600', '차명입금관리', G5_ADMIN_URL.'/deposit_withdrawal/others_deposit_list.php', 'deposit'),
	array('500700', '즉시출금관리', G5_ADMIN_URL.'/deposit_withdrawal/auth_withdrawal_list.php', 'deposit'),
	array('500800', '카카오송금', G5_ADMIN_URL.'/money_state/kakao_list.php', 'deposit')
);

?>