<?
if($is_member) {

	$bank_acct_registered    = ($member['bank_code'] && $member['account_num'] && $member['bank_private_name']) ? true : false;
	$virtual_acct_registered = ($member['va_bank_code2'] && $member['virtual_account2']) ? true : false;

	if($member['mb_id']=='fintech01') $special_print_name = "<span style='color:#153FA1;'>NH투자증권<br><span style='font-size:12px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech02') $special_print_name = "<span style='color:#153FA1;'>NH투자증권<br><span style='font-size:12px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제2호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech03') $special_print_name = "<span style='color:#153FA1;'>NH투자증권<br><span style='font-size:12px'>(피델리스 대신 P2P 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech04') $special_print_name = "<span style='color:#153FA1;'>피델리스 P2P 전문투자형<br>사모투자신탁 제1호</span>";
	if($member['mb_id']=='fintech05') $special_print_name = "<span style='color:#153FA1;'>피델리스 핀테크인컴 전문투자형<br>사모투자신탁 제1호</span>";

	if($member['member_type']=='1') {
		$print_mb_name = "<a>".$member["mb_name"]."</a>";
		$invest_possible_amount = (in_array($member['member_investor_type'], array('1','2'))) ? price_cutting($member['invest_possible_amount'])."원" : "제한 없음";
		$invest_possible_amount_prpt = (in_array($member['member_investor_type'], array('1','2'))) ? price_cutting($member['invest_possible_amount_prpt'])."원" : "제한 없음";
	} else {
		$print_mb_name = "<a>".$member["mb_co_name"]."</a>";
		$invest_possible_amount = "제한 없음";
		$invest_possible_amount_prpt = "제한 없음";
	}

	if($member['bank_code'] && $member['account_num'] && $member['va_bank_code2'] && $member['virtual_account2']) $bank_ok = true;
}
?>
<!-- 상단 메뉴 //-->
<header>
	<div class="header_menu">
		<nav>
			<div class="top_nav">
				<ul>
					<li class="td1"><img src="/img/menu_back_icon.png" alt="이전보기" class="menuicon" OnClick="history.back();" /></li>
					<li><a href="<?php ECHO G5_URL;?>"><img src="https://www.hellofunding.co.kr/theme/2018/img/main/logo.png" alt="HELLO FUNDING"></a><li>
					<li class="td2">
					<?php IF($member["mb_no"]) { ?>
					<img src="/img/my_page_icon.png" alt="마이페이지 아이콘" class="menuicon"  OnClick="window.location='/mypage/my.php';" />
					<?php } ELSE { ?>
					<a href="#none" OnClick="fn_login_check()">로그인</a>
					<?php } ?>
					</li>
				</ul>
				<ul>
					<li><a href="/investment/invest_list.php" title="투자상품보기">투자상품보기</a></li>
					<li><a href="/etc/epilogue.php" title="투자후기">투자후기</a></li>
					<li><a href="javascript:;" title="이용안내">이용안내</a>
						<div id="s_menu">
							<p><a href="/investment/guide.php" title="투자방법안내">투자방법안내</a></p>
							<p><a href="/etc/faq.php" title="도움말">도움말</a></p>
							<p><a href="/etc/question.php" title="문의하기">문의하기</a></p>
						</div>
					</li>
				</ul>

			</div>
		</nav>
	</div>
	<script>
	// 부동산 상품 설명레이어
	$('#d_flag_btn, #d_flag_close').on('click', function() { $('#d_flag').fadeToggle('slow'); });

	// 사용자 레이어
	$('#name_zone, #invest_close').on('click', function(e) {
		$('#invest_zone2').stop().fadeToggle('slow');
	});
	// 레이어 닫기
	$(document).mouseup(function(e) {
		var my_layer = $("#invest_zone2");
		if(e.target.className =="invest_zone2"){return false;}
		if(my_layer.css("display") == "block") {
			if(!my_layer.is(e.target) && my_layer.has(e.target).length === 0 && e.target.className != "invest_zone2") {
				my_layer.hide();
			}
		}
		//e.preventDefault();
	});
	</script>
</header>