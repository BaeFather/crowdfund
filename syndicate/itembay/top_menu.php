<!-- 상단 메뉴 //-->
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
<header>
	<div class="header_menu">
		<nav>
			<div class="top_nav">
				<h1><a href="<?php ECHO G5_URL;?>"><img src="https://www.hellofunding.co.kr/theme/2018/img/main/logo.png" alt="HELLO FUNDING"></a></h1>
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

<?php
			IF($member["mb_no"])
			{
				$badge_image_url = "/images/main/badge" . $member['member_type'];
				$badge_image_url.= ($member['member_type']=='1') ? $member['member_investor_type'] : "";
				$badge_image_url.= ".png";
?>
				<div id="member">
					<!--투자자회원정보 시작-->
					<div class="logout">
						<div id="name_zone">
							<img src="<?=$badge_image_url?>">
							<? if($special_print_name) { ?><a><?=$special_print_name;?></a><? }else{ ?><?=preg_replace('/주식회사/', '(주)', $print_mb_name);?>님<? } ?>
						</div>
						<div id="invest_zone2" class="invest_zone2">
							<img src="/images/main/top_bg01.png" alt="top_bg">
							<div class="header">
								<div class="deposit" onClick="location.href='/deposit/deposit.php?tab=2';" style="cursor:pointer;">
									<span class="outcome"><strong>예치금</strong><strong><?=number_format($member['mb_point']);?>원</strong></span>
									<!--img src="/theme/2018/img/kakaopay_btn.png" width="48%" alt="" class="kakao_btn"-->
								</div>
								<div class="deposit" onClick1="location.href='/deposit/deposit.php?tab=3';" style="cursor:pointer;">
									<span class="outcome">
									<strong>신한은행</strong>
									<? if($BANK[$member['va_bank_code2']]) { ?>
									<strong style="float:left;"><?=$member['virtual_account2'];?></strong>
									<? }else{ ?>
									<strong style="float:left;">없음</strong>
									<? } ?>
									</span>
									<? if($BANK[$member['va_bank_code2']]) { ?>
									<a class="copy_numb" onclick="ctrl_copy('<?=$member['virtual_account2'];?>')" style="cursor:pointer;">계좌번호 복사</a>
									<script>
									function ctrl_copy(cptxt) {
										var tempInput = document.createElement("input");
									    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
									    tempInput.value = cptxt;
										document.body.appendChild(tempInput);

										var tg=document.getElementById("vt_acc111");
										if (isOS()) {
											var range, selection;
											range = document.createRange();
											range.selectNodeContents(tempInput);
											selection = window.getSelection();
											selection.removeAllRanges();
											selection.addRange(range);
											tempInput.setSelectionRange(0, 999999);
										} else {
											tempInput.select();
										}
										document.execCommand("copy");
										alert("복사되었습니다.");
									}
									function isOS() {
										return navigator.userAgent.match(/ipad|iphone/i);
									}
									</script>
									<? } ?>
								</div>
							</div>
							<div class="body">
								<div class="line"></div>
								<ul>
									<li>
										<div class="invest_amount">
											<strong>나의 투자한도</strong>
											<?
											if ($member['member_investor_type']==0) $imsi_investor_type = 3;
											else $imsi_investor_type = $member['member_investor_type'];

											if ($INDI_INVESTOR[$imsi_investor_type]['site_limit']=='999999999999') $imsi_site_limit = "제한 없음";
											else $imsi_site_limit = price_cutting($INDI_INVESTOR[$imsi_investor_type]['site_limit'])."원";
											?>
											<strong><?=$imsi_site_limit?></strong>
										</div>
									</li>
									<li>
										<div class="invest_amount">
											<strong>현재 투자금액 </strong>
											<strong><?=price_cutting($member['ing_invest_amount'])?>원</strong>
										</div>
									</li>
									<li>
										<div class="invest_amount">
											<strong>투자 가능금액</strong>
											<strong><?=$invest_possible_amount?></strong>
										</div>
									</li>
									<?
									if ($member['member_investor_type'] == "1") {
									?>
									<div class="triangle"></div>
									<li class="invest_amount2">
										<div class="invest_amount">
											<strong>부동산.주택담보</strong>
											<strong><?=$invest_possible_amount_prpt;?></strong>
										</div>
										<div style="clear:both;"></div>
										<div class="invest_amount">
											<strong>동산.헬로페이</strong>
											<strong><?=price_cutting($member['invest_possible_amount_ds'])?>원</strong>
										</div>
									</li>
									<?
									}
									?>
								</ul>
								<ul>
									<li><a href="/deposit/deposit.php"><span class="invest_btn1">투자현황</span></a></li>
								</ul>
								<div class="line"></div>
								<div class="footer">
									<p>원리금 수취방식</p>
									<div>
									<?
									if ($member['receive_method']=="1") {
										?>
										<span class="refund_l_off"><strong><a onclick="change_receive('2');" style="color:#818181 !important;">예치금 상환</a></strong></span>
										<span class="refund_r_on"><strong>환급계좌 상환</strong></span>
										<?
									} else if ($member['receive_method']=="2") {
										?>
										<span class="refund_l_on"><strong>예치금 상환</strong></span>
										<span class="refund_r_off"><strong><a onclick="change_receive('1');" style="color:#818181 !important;">환급계좌 상환</a></strong></span>
										<?
									}
									?>
									</div>
								</div>
								<div class="line"></div>

								<div class="mem_infos">
									<a href="/member/member_confirm.php?url=/mypage/mypage.php" class="mem_info_btn">회원정보</a>
									<a href="/member/logout.php" class="log_out_btn">로그아웃</a>

								</div>
							</div>
							<div class="closer"><img id="invest_close" src="<?=HF_IMG_URL?>/close_btn01.png" alt="닫기"></div>
						</div>
					</div>
				</div>
<script>
function change_receive(n_receive_method) {

<? if(!$bank_acct_registered || !$virtual_acct_registered) { ?>
	vaOpen();
	return;
<? } ?>

	if (n_receive_method != '1' &&  n_receive_method !='2') return false;

	var yn = confirm("원리금 수취방식을 변경하시겠습니까?");

	if (yn) {
		$.ajax({
			url : "/root_mypage/ajax_receive_proc.php",
			type: "POST",
			data : {new_receive_method : n_receive_method},
			success: function(res, textStatus, jqXHR){

				if (res=="ok") {
					alert("원리금 수취방식이 변경되었습니다.");
					window.location.reload();
				} else {
					alert("error");
				}
			},
			error: function (jqXHR, textStatus, errorThrown)	{
				console.log(jqXHR + " " + textStatus);
			}
		});
	}
}
</script>


<?php
			} ELSE {
?>

				<div id="member">
					<div class="login"><a href="#none" OnClick="fn_login_check();" class="login_btn">로그인</a></div>
					<div class="join"><a href="/member/join_info.php" class="join_btn">회원가입</a></div>
				</div>
<?php
			}
?>
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