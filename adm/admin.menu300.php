<?

$menu['menu300'] = array(
	array('300000', '게시판', '#', 'bbs_board'),
	array('300100', '(구)게시판관리', '' . G5_ADMIN_URL . '/board_list.php', 'bbs_board'),
	array('300200', '(구)게시판그룹관리', '' . G5_ADMIN_URL . '/boardgroup_list.php', 'bbs_group'),
	array('300300', '헬로펀딩 소식 설정', G5_ADMIN_URL . '/funding_news_list.php', 'funding_news_list', 1),
	array('300400', '공지사항관리', '/bbs/board.php?bo_table=notice', 'bbs_board'),
	array('300430', '이벤트관리', G5_ADMIN_URL . '/hevent/', 'hevent', 1),
	array('300500', '페이지 내용관리', G5_ADMIN_URL . '/contentlist.php', 'scf_contents', 1),
	array('300600', 'FAQ관리', G5_ADMIN_URL . '/faqmasterlist.php', 'scf_faq', 1),
	array('300700', '투자후기관리', G5_ADMIN_URL . '/epilogue_list.php', 'scf_epilogue', 1),
	array('300740', '추천평관리', G5_ADMIN_URL . '/recommend_list.php', 'scf_epilogue', 1),
	array('300750', '인터뷰 신청관리', G5_ADMIN_URL . '/event_request/', 'scf_epilogue', 1),
	array('300710', '펀딩디자이너 스토리', G5_ADMIN_URL . '/funding_designer_list.php', 'scf_funding_designer', 1),
	array('300720', '헬로비디오 관리', G5_ADMIN_URL . '/video_list.php', 'scf_video_list', 1),
	array('300730', '헬로라이브TV', G5_ADMIN_URL . '/tv_list.php', 'scf_tv_list', 1),
	array('300770', '사업정보고시 관리', G5_ADMIN_URL . '/business_info/', 'business_info', 1),
	array('300780', '팝업 관리', G5_ADMIN_URL . '/site_popup_list.php', 1)
);

?>
