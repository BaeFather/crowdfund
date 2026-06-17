<?

//2017-04-24 : 개인회원 상품별 금액 제한 관련 내용 추가

include_once("_common.php");

if($is_member) {

	if($member['mb_id']=='fintech01') $special_print_name = "<strong style='color:#153fa1;'>NH투자증권</strong><br><span style='font-size:12px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech02') $special_print_name = "<strong style='color:#153fa1;'>NH투자증권</strong><br><span style='font-size:12px'>(피델리스 Fin Tech 전문투자형 사모투자신탁 제2호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech03') $special_print_name = "<strong style='color:#153fa1;'>NH투자증권</strong><br><span style='font-size:12px'>(피델리스 대신 P2P 전문투자형 사모투자신탁 제1호 신탁업자 지위)</span>";
	if($member['mb_id']=='fintech04') $special_print_name = "<br><strong style='color:#153fa1;'>피델리스 P2P 전문투자형<br>사모투자신탁 제1호</strong>";
	if($member['mb_id']=='fintech05') $special_print_name = "<br><strong style='color:#153fa1;'>피델리스 핀테크인컴 전문투자형<br>사모투자신탁 제1호</strong>";

	if($member['member_type']=='1') {
		$print_mb_name = "<a>".$member["mb_name"]."</a>";
		$invest_possible_amount = (in_array($member['member_investor_type'], array('1','2'))) ? price_cutting($member['invest_possible_amount'])."원" : "제한 없음";
		$invest_possible_amount_prpt = (in_array($member['member_investor_type'], array('1'))) ? price_cutting($member['invest_possible_amount_prpt'])."원" : "제한 없음";
	}
	else {
		$print_mb_name = "<a>".$member["mb_co_name"]."</a>";
		$invest_possible_amount = "제한 없음";
		$invest_possible_amount_prpt = "제한 없음";
	}

	if($member['bank_code'] && $member['account_num'] && $member['va_bank_code2'] && $member['virtual_account2']) $bank_ok = true;

}

?>
	<nav>
		<ul id="main-menu">
<?
$gnb_menus = array();

$sql = "SELECT * FROM {$g5['menu_table']}
        WHERE me_use = '1'
        AND length(me_code) = '2'
        ORDER BY me_order, me_id ";
$result = sql_query($sql, false);
$gnb_zindex = 999; // gnb_1dli z-index 값 설정용

for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
		<li onClick="window.<?=$row['me_target']?>.location.href='<?=$row['me_link']?>'" style="cursor:pointer">
			<?=$row['me_name']?>
<?
	$submenus = '';

	$sql_c = "
		SELECT
			COUNT(me_id) AS 'sub_cnt' FROM {$g5['menu_table']}
		WHERE
			me_use = '1'
			AND length(me_code) = '4'
			AND substring(me_code, 1, 2) = '{$row['me_code']}'
		ORDER BY
			me_order, me_id";
	$row_c = sql_fetch($sql_c);
	$sub_m_cnt = $row_c['sub_cnt'];


	// 서브메뉴가 있다면
	if($sub_m_cnt > 0) {

		echo '<div class="smenu">' . PHP_EOL;

		$sql2 = "
			SELECT
				*
			FROM
				{$g5['menu_table']}
			WHERE
				me_use = '1'
				AND length(me_code) = '4'
				AND substring(me_code, 1, 2) = '{$row['me_code']}'
			ORDER BY
				me_order, me_id ";
		$result2 = sql_query($sql2);

		for ($k=0; $row2=sql_fetch_array($result2); $k++) {
			echo '<p><a href="'.$row2['me_link'].'" target="_'.$row2['me_target'].'">'.$row2['me_name'].'</a></p>' . PHP_EOL;
		}

		echo '</div>' . PHP_EOL;

	}
?>
			</li>
<?
}
?>
		</ul>
		<div id="member">
<?
if ($is_member) {
	//로그인후
	if ($is_admin) {
?>
			<div class="admin_logout" style="cursor:pointer">
				<a href="<?=G5_ADMIN_URL?>" target="_self"><?=$member["mb_name"]?></a>
				<div class="smenu">
					<p><a href="<?=G5_ADMIN_URL?>" target="_self">관리자툴</a></p>
					<p><a href="<?=G5_BBS_URL?>/logout.php" target="_self" style="color:red">로그아웃</a></p>
				</div>
			</div>
<?
	}
	else {

		if($member['mb_level'] > 8) {
?>
			<div class="logout" style="cursor:pointer">
				<a href="#" target="_self"><?=$member["mb_name"]?> 님</a>
				<div class="smenu">
					<p><a href="<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php" target="_self">회원정보</a></p>
					<p><a href="<?=G5_URL?>/deposit/deposit.php" target="_self">투자내역</a></p>
					<p><a href="<?=G5_BBS_URL?>/logout.php" target="_self">로그아웃</a></p>
				</div>
			</div>
<?
		}
		else {

			$badge_image_url = "/images/main/badge" . $member['member_type'];
			$badge_image_url.= ($member['member_type']=='1') ? $member['member_investor_type'] : "";
			$badge_image_url.= ".png";

?>

<!--투자자회원정보 시작-->
			<div class="logout">
				<p id="badge_zone" style="cursor:pointer;float:left;padding-top:0px;"><img src="<?=$badge_image_url?>"></p>
<? if($special_print_name) { ?>
				<p id="name_zone" style="cursor:pointer;position:absolute;display:inline-block; top:8px; padding:auto; margin:auto 0 auto 20px; width:210px;"><a style='font-size:14px;line-height:18px;'><?=$special_print_name?></a></p>
<? } else { ?>
				<p id="name_zone" style="cursor:pointer;float:left;padding:7px 0 0 5px;"><a><?=preg_replace('/주식회사/', '(주)', $print_mb_name)?>님</a></p>
<? } ?>
				<div class="smenu1" id="smenu1">

					<p><img src="/images/main/top_bg01.png" alt=""></p>
					<p><ul style="clear:both; width:100%; background:url('/images/main/center_bg01.png') repeat-y;">
							<li style="height:10px;"></li>
							<li style="border-bottom:1px solid #abbbde;width:98%;margin:0 auto;">
								<p class="mi1">예치금. <span style="float:right;text-align:right"><?=number_format($member['mb_point'])?>원</span></p>
							</li>
							<li style="border-bottom:1px solid #abbbde;width:98%;margin:0 auto;padding-bottom:10px;">
								<p class="myinv_tit">나의 투자정보</p>
								<p style="width:98%;">
									<p class="invest_info1">
										<img src="/images/main/icon02.jpg" alt=""><br><br>
										투자잔액<br>
										<?=price_cutting($member['ing_invest_amount'])?>원
									</p>
									<p class="invest_info2">
										<!--<img src="/images/main/icon03.jpg" alt=""><br><br>-->
										<span class="invest_total">
											총 투자가능액<br>
											<?=$invest_possible_amount?>
										</span>
										<span class="pf_invest">
<? if($member['member_type']=='1' && $member['member_investor_type']=='1') { ?>
											부동산 상품 <span class="flag_btn" id="btn3">?</span><br>
											투자가능액<br>
											<?=$invest_possible_amount_prpt?>
<? } else { ?>
											<br><br><br>
<? } ?>
										</span>
										<div id="conts3" style="position:absolute;width:82%; margin:140px 8px 0;padding:15px 15px;border-radius:10px;background-color:#000;color:#fff;font-size:13px;text-align:left;line-height:22px;z-index:150;opacity:0.9;display:none;">
											[P2P대출 가이드라인에 의한 개인투자자의 투자한도액]<br>
											1. 총 투자한도액 : 2,000만원<br>
											단, 부동산 상품(PF, 부동산 담보 등)은 1,000만원까지 투자 가능<br>
											<div id="close3" style="position:absolute;right:0;top:105px;margin:5px 5px 0 0;font-size:11px;font-family:'verdana';cursor:pointer;width:18px;height:18px;border:1px solid #fff;text-align:center;line-height:18px;color:#fff;">x</div>
										</div>
										<script type="text/javascript">
										$('#btn3, #close3').on('click', function() { $('#conts3').fadeToggle('slow'); });
										</script>
									</p>
								</p>
								<p style="width:98%;clear:both;padding:3px 0;">
									<p style="float:left;"><a href="/deposit/deposit.php"><span class="invest_btn1">투자내역</span></a></p>
									<p style="float:left;"><a href="/deposit/deposit.php?tab=4"><span class="invest_btn2">투자스케쥴러</span></a></p>
									<p style="clear:both;"><a href="/deposit/deposit.php?tab=5"><span class="invest_btn3">자동투자설정</span></a></p>
								</p>
							</li>

<? if($bank_ok) { ?>
							<li style="margin-top:8px;">
								<p style="margin:0 auto;width:227px; font:12px NG; color:#777;">⊙ 원리금 수취방식</p>
								<p class="rcv_info">
									<span style="float:left;"><?=($member['receive_method']=='2')?'예치금 상환':'환급계좌 상환'?></span>
									<span style="float:right;"><button type="button" class="invest_btn4" onClick="location.href='<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php#bank_edit';">설정변경</button></span>
								</p>
							</li>
							<li style="margin-top:8px;">
								<p style="margin:0 auto;width:227px; font:12px NG; color:#777;">⊙ 예치금 가상계좌</p>
								<p class="count_info">
									<?=$BANK[$member['va_bank_code2']]?> <?=$member['virtual_account2']?>
									<!--<?=$member['va_private_name2']?>-->
								</p>
							</li>

							<li class="outcome_btn"><button type="button" class="btn_default" onClick="location.href='/deposit/deposit.php?tab=2'" style="width:100%;">예치금 출금</button></li>
<? } ?>

							<li style="margin:0 auto 8px; width:226px;">
								<a href="<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php"><span class="btn_green" style="min-width:82px;">회원정보</span></a>
								<a href="<?=G5_BBS_URL?>/logout.php"><span class="btn_blue" style="min-width:80px;">로그아웃</span></a>
							</li>
							<li style="height:4px;"></li>
						</ul>
					</p>
					<p style="background:url('/images/main/bottom_bg01.png') no-repeat; height:26px;line-height:24px;text-align:right;"><img id="smenu1_close" src="/images/main/close_btn01.png" style="padding-right:10px; cursor:pointer;"></p>

				</div>
			</div>
			<script type="text/javascript">
			$('#badge_zone, #name_zone, #smenu1_close').on('click', function() {
				$('.smenu1').fadeToggle('slow');
			});
			</script>
<!--투자자회원정보 끝-->


<?
		}
	}
}
else {
?>
			<div class="login"><a href="<?=G5_BBS_URL?>/login.php">로그인</a></div>
			<div class="join"><a href="<?=G5_BBS_URL?>/register_choice.php">회원가입</a></div>
<?
}
?>
		</div>
	</nav>
