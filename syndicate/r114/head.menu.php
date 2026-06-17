<?
//if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//2017-04-24 : 개인회원 상품별 금액 제한 관련 내용 추가
//include_once("_common.php");

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
<div id="header_menu" class="header_menu">
	<div id="member">
<?
		if ($is_member)
		{

		$badge_image_url = "/images/main/badge" . $member['member_type'];
		$badge_image_url.= ($member['member_type']=='1') ? $member['member_investor_type'] : "";
		$badge_image_url.= ".png";

?>

		<div class="logout">
			<div id="name_zone">
				<img src="<?=$badge_image_url?>" alt="<?=$special_print_name;?>"/>
				<? if($special_print_name) { ?>
				<a><?=$special_print_name;?></a>
			</div>
			<? } else { ?>
				<strong><?=preg_replace('/주식회사/', '(주)', $print_mb_name);?>님</strong>
			<? } ?>
		</div>
		<div class="header">
			<div class="deposit">
				<span class="pull-left"><strong>예치금.</strong></span>
				<span class="pull-right"><strong><?=number_format($member['mb_point']);?>원</strong></span>
			</div>
		</div>
		<div class="body">
			<p><label><strong>나의 투자정보</strong></label></p>
			<ul>
				<li>
					<div class="invest_amount">
						<img src="/images/main/icon02.jpg" alt="투자잔액"/><br/><br/>
						<strong>투자잔액</strong><br/>
						<strong><?=price_cutting($member['ing_invest_amount']);?>원</strong>
					</div>
				</li>
				<li>
					<div class="invest_amount_detail">
						<p>총 투자가능액</p>
						<p><?=$invest_possible_amount;?></p>
						<hr/>
						<p>부동산 상품 <span class="d_flag_btn" id="d_flag_btn">?</span></p>
						<p>투자가능액</p>
						<p><?=$invest_possible_amount_prpt;?></p>
						<div id="d_flag" class="d_flag_description">
							[P2P대출 가이드라인에 의한 개인투자자의 투자한도액]<br>
							1. 총 투자한도액 : 2,000만원<br>
							단, 부동산 상품(PF, 부동산 담보 등)은 1,000만원까지 투자 가능<br>
							<div id="d_flag_close" class="d_flag_close">x</div>
						</div>
					</div>
				</li>
			</ul>
			<ul>
				<li>
					<a href="/deposit/deposit.php"><span class="invest_btn1">투자내역</span></a>
				</li>
				<li>
					<a href="/deposit/deposit.php?tab=4"><span class="invest_btn2">투자스케쥴러</span></a>
				</li>
			</ul>
			<a href="/deposit/deposit.php?tab=5"><span class="invest_btn3">자동투자설정</span></a>
		</div>
		<div class="clearfix"></div>
		<div class="footer">
			<p>⊙ 원리금 수취방식</p>
			<div>
				<span class="pull-left"><strong><?=($member['receive_method'] == '2') ? '예치금 상환' : '환급계좌 상환';?></strong></span>
				<span class="pull-right">
					<button type="button" onClick="location.href='/member/member_confirm.php?url=/mypage/mypage.php#bank_edit';">설정변경</button>
				</span>
			</div>

			<p>⊙ 예치금 가상계좌</p>

			<div>
				<? if($BANK[$member['va_bank_code2']]) { ?>
					<strong style="float:left;"><?=$BANK[$member['va_bank_code2']];?> <?=$member['virtual_account2'];?></strong>
					<span style="float:right;"><a onclick="go_kkp();" style="cursor:pointer;"><img src="/images/mypage/kakaopay_btn.png" height="25" alt="카카오페이"></a></span>
				<? }else{ ?>
					<strong style="float:left;">없음</strong>
				<? } ?>
			</div>
			<button type="button" class="btn_default per100" onClick="location.href='/deposit/deposit.php?tab=2'">예치금 출금</button>
			<ul>
				<li>
					<a href="/member/member_confirm.php?url=/mypage/mypage.php"><span class="btn_green">회원정보</span></a>
				</li>
				<li>
					<a href="/member/logout.php"><span class="btn_blue">로그아웃</span></a>
				</li>
			</ul>
		</div>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
	<? } ?>
</div>

<script type="text/javascript">
// 부동산 상품 설명레이어
$('#d_flag_btn, #d_flag_close').on('click', function() { $('#d_flag').fadeToggle('slow'); });

// 사용자 레이어
$('#name_zone, #invest_close').on('click', function(e) {
	$('#invest_zone').stop().fadeToggle('slow');
});
</script>

<script type="text/javascript">

function go_kkp() {
	var request = "";
	$.ajax({
		type: "POST",
		url: "/kakao_remit/kakao_remit.php",
		dataType : "json",
		beforeSend: function() {
			//$.indicator("spinner").show();
		},
		success: function(res) {
			console.log(res);
			if (res.status=="200") {
				<? if ($CONF['flatform']=="app") { ?>
					//alert(res.next_send_url);
					self.location.href = res.next_send_url;
				<? } else { ?>
					self.location.href = res.next_send_url;
				<? } ?>
			} else {
				alert(res.error_message);
			}
		},
		error: function(e) {
			console.log(e);
			alert("통신 오류");
		},
		complete: function() {
			//$.indicator("spinner").hide();
		}
	});
}

</script>