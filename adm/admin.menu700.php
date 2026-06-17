<?php

$menu['menu700'] = array (
	array('700000', '정산', '#', 'sales'),
	array('700100', '대출ㆍ상환 현황', ''.G5_ADMIN_URL.'/repayment/invest_status_list.php', 'sales'),
//array('700100', '대출ㆍ상환 현황(구)', ''.G5_ADMIN_URL.'/product_sales.php', 'sales'),
	array('700200', '대출ㆍ상환 현황 (이벤트)', ''.G5_ADMIN_URL.'/event_product_sales.php', 'sales'),
	array('700300', '원리금 지급통계', ''.G5_ADMIN_URL.'/etc/profit_give_stats.php', 'sales'),
	array('700310', '원리금 지급내역', ''.G5_ADMIN_URL.'/repayment/repay_log.php', 'sales'),
	array('700400', '원리금 지급스케쥴', ''.G5_ADMIN_URL.'/repayment/repay_schedule.php', 'sales'),
	array('700500', '원리금 배분요청 로그', ''.G5_ADMIN_URL.'/repayment/repay_exec_log.php', 'sales'),
	array('700600', '세금계산서 일괄 발행', ''.G5_ADMIN_URL.'/repayment/taxinvoice.php', 'sales'),
	array('700700', '투자현황', ''.G5_ADMIN_URL.'/repayment/invest_list.php', 'sales'),
	array('700800', '대출자플랫폼수수료현황', ''.G5_ADMIN_URL.'/repayment/loaner_usefee_repay.php', 'sales'),
	array('700900', '대출ㆍ상환 일대사', ''.G5_ADMIN_URL.'/repayment/nujuk_loan_repay_status.php', 'sales'),
	array('700910', '기타비용 배분', ''.G5_ADMIN_URL.'/repayment/etc_cost_divide.php', 'sales'),
//array('700100', '매출통계', ''.G5_ADMIN_URL.'/sales_list.php', 'sales'),
//array('700900', '회원별 정산정리', ''.G5_ADMIN_URL.'/etc/money_stats.php', 'sales'),
);

?>