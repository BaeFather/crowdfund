<?

$menu['menu800'] = array (
	array('800000', 'SMS/메일', '#', 'sms'),
	array('800100', 'SMS 사용자발송 설정', ''.G5_ADMIN_URL.'/sms_user_form.php', 'sms_user_form'),
	array('800150', '카카오 사용자발송 설정', ''.G5_ADMIN_URL.'/kakao_user_form.php', 'kakao_user_form'),
	array('800200', 'SMS 관리자발송 설정', ''.G5_ADMIN_URL.'/sms_admin_form.php', 'sms_admin_form'),
	//array('800300', '회원 SMS발송 설정', ''.G5_ADMIN_URL.'/sms_all_send.php', 'sms_all_send'),
	//array('800320', '비회원 SMS발송 설정', ''.G5_ADMIN_URL.'/sms_all_send2.php', 'sms_all_send2'),
	array('800400', '회원 메일발송 설정', ''.G5_ADMIN_URL.'/email_all_send.php', 'email_all_send'),
	//array('800500', 'SMS발송현황(KP모바일)', ''.G5_ADMIN_URL.'/sms_schedule_list.php', 'sms_schedule_list'),
	//array('800510', 'SMS발송현황(보내고)', ''.G5_ADMIN_URL.'/sms_schedule_list.20180327.php', 'sms_schedule_list'),
	array('800610', '투자자통계 수신인관리', ''.G5_ADMIN_URL.'/sms_hello_status/', 'sms_hello_status'),
	array('800520', '회원 SMS발송(smtnt)', ''.G5_ADMIN_URL.'/smtnt_sms_all_send.php', 'sms_schedule_list'),
	array('800530', '비회원 SMS발송(smtnt)', ''.G5_ADMIN_URL.'/smtnt_sms_all_send2.php', 'sms_schedule_list'),
	array('800520', 'SMS발송현황(SMTNT)', ''.G5_ADMIN_URL.'/smtnt_sms_schedule_list.php', 'sms_schedule_list')
);

?>
