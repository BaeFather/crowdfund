<?

$menu['menu200'] = array (
	array('200000', '회원', '#', 'member'),
	array('200100', '전체 회원 정보', G5_ADMIN_URL.'/member/member_list.php', 'mb_list'),
	array("200300", "개인투자자 승인", G5_ADMIN_URL.'/member/investor_type_req.php', 'investor_type_req'),
	array("200400", "접속 통계 (실시간)", G5_ADMIN_URL.'/m3stats.php', 'm3stats'),
	array("200500", "전환 통계", G5_ADMIN_URL.'/visit_status/vstatus.php', 'visit_stats'),
	array("200600", "원천징수영수증 신청현황", G5_ADMIN_URL.'/withholding/', 'withholding'),
	array("200700", "080 수신거부", G5_ADMIN_URL.'/member/block_sms_list.php', 'block_080'),
	array('200800', '관리자 설정', G5_ADMIN_URL.'/member/subadmin_list.php', 'subadmin_list')
);

//array('200200', '포인트관리', G5_ADMIN_URL.'/point_list.php', 'mb_point'),
//array('200300', '회원메일발송', G5_ADMIN_URL.'/mail_list.php', 'mb_mail'),
//array("200410", "접속 키워드 로그", ''.G5_ADMIN_URL.'/m3stats2.php', 'm3stats2'),
//array('200800', '접속자집계', G5_ADMIN_URL.'/visit_list.php', 'mb_visit', 1),
//array('200810', '접속자검색', G5_ADMIN_URL.'/visit_search.php', 'mb_search', 1),
//array('200820', '접속자로그삭제', G5_ADMIN_URL.'/visit_delete.php', 'mb_delete', 1),
//array('200900', '투표관리', G5_ADMIN_URL.'/poll_list.php', 'mb_poll')

?>
