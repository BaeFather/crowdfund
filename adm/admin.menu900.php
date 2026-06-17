<?

$menu9005_sub_title = ($member['mb_id']=="seintax") ? "세인세무법인" : "초청회 신청현황";


$menu["menu900"] = array (
	array('900000', '이벤트', '#', 'menu900'),
	array('900101', '친구초대 이벤트',        G5_ADMIN_URL.'/event/recommend_event.php', 'recommend'),
	array('900102', '쿠폰지급 이벤트',				  	G5_ADMIN_URL.'/event/partner_event.php', 'partner'),
	array('900100', '(구)추천인 보상',        G5_ADMIN_URL.'/event/reward_gift_send.php', 'event_reward1-1'),
	array('900200', '(구)피추천인 보상',      G5_ADMIN_URL.'/event/reward_point_send.php', 'event_reward1-2'),
	array('900300', '후기이벤트 내역',        G5_ADMIN_URL.'/event/epilogue_survey.php', 'epilogue_survey'),
	array('900400', '영화티켓 지급내역',      G5_ADMIN_URL.'/event/movie_ticket_list.php', 'movie_ticket_list'),
	array('900500', $menu9005_sub_title,      G5_ADMIN_URL.'/event/invitaion_event.php', 'invitation'),
	array('900600', '천억돌파 기념 이벤트',   G5_ADMIN_URL.'/event/100b.php', '1000b'),
	array('900700', '천억돌파 기념 이벤트Ⅱ', G5_ADMIN_URL.'/event/100b2.php', '1000b2'),
	array('900800', 'NH 투자증권 이벤트',     G5_ADMIN_URL.'/event/nh_cma_event.php', 'nhcma'),
	array('900900', '배너관리',               G5_ADMIN_URL.'/event_banner/', 'nhcma'),
	array('900901', '법인투자케어서비스',     G5_ADMIN_URL.'/loan_request/corp_invest_request.php', 'old_loan_request'),
);

?>