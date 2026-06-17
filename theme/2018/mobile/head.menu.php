<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//2017-04-24 : 개인회원 상품별 금액 제한 관련 내용 추가
include_once("_common.php");

if($is_member)
{

	$bank_acct_registered    = ($member['bank_code'] && $member['account_num'] && $member['bank_private_name']) ? true : false;
	$virtual_acct_registered = ($member['va_bank_code2'] && $member['virtual_account2']) ? true : false;

	if($member['mb_id']=='fintech01') $special_print_name = "NH투자증권<br/>(피델리스 Fin Tech<br/>전문투자형 사모투자신탁<br/>제1호 신탁업자 지위)";
	if($member['mb_id']=='fintech02') $special_print_name = "NH투자증권<br/>(피델리스 Fin Tech<br/>전문투자형 사모투자신탁<br/>제2호 신탁업자 지위)";
	if($member['mb_id']=='fintech03') $special_print_name = "NH투자증권<br/>(피델리스 대신 P2P<br/>전문투자형 사모투자신탁<br/>제1호 신탁업자 지위)";
	if($member['mb_id']=='fintech04') $special_print_name = "피델리스 P2P<br/>전문투자형사모투자신탁 제1호";
	if($member['mb_id']=='fintech05') $special_print_name = "피델리스 핀테크인컴<br/>전문투자형 사모투자신탁 제1호";

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
<div id="header_menu" class="header_menu">
	<div id="member2">
<?
		if($is_member) {

			if($member['mb_level']=='1') {
				$badge_image_url = "/theme/2018/img/mobile/badge" . $member['member_type'];
				$badge_image_url.= ($member['member_type']=='1') ? $member['member_investor_type'] : "";
				$badge_image_url.= ".png";
			}

?>

		<div class="logout">
			<div id="name_zone">
				<? if($badge_image_url) { ?><img src="<?=$badge_image_url?>" alt="<?=$special_print_name?>"/><? } ?>
				<? if($special_print_name) { ?><a><?=$special_print_name?></a>
			</div>
			<? } else { ?><br/>
				<strong><?=preg_replace('/주식회사/', '(주)', $print_mb_name);?>님</strong>
			<? } ?>
		</div>
		<div class="header">
			<div class="deposit">
				<span class="outcome" onClick="location.href='/deposit/deposit.php?tab=2';"><strong>예치금</strong><strong><?=number_format($member['mb_point']);?>원</strong></span>
				<a onclick="go_kkp();" style="cursor:pointer;"><img src="/theme/2018/img/kakaopay_btn.png" width="40%"  alt="카카오페이" class="kakao_btn" /></a>
			</div>
			<div class="deposit">
				<span class="outcome" onClick="location.href='/deposit/deposit.php?tab=3';">
				<strong>신한은행</strong>
				<? if($BANK[$member['va_bank_code2']]) { ?>
				<strong style="float:left;"> <?=$member['virtual_account2'];?></strong>
				<? }else{ ?>
				<strong style="float:left;">없음</strong>
				<? } ?>
				</span>
				<? if($BANK[$member['va_bank_code2']]) { ?>
				<a class="copy_numb" onclick="copyVacct('<?=$member['virtual_account2'];?>')" style="cursor:pointer;">계좌번호 복사</a>
				<script>
				function copyVacct(cptxt) {
					var tempInput = document.createElement("input");
					tempInput.style = "position: absolute; left: -1000px; top: -1000px; visibility:hidden;";
					tempInput.value = cptxt;
					document.body.appendChild(tempInput);

					var tg=document.getElementById("vt_acc111");
					if(isOS()) {
						var range, selection;
			      range = document.createRange();
			      range.selectNodeContents(tempInput);
						selection = window.getSelection();
			      selection.removeAllRanges();
			      selection.addRange(range);
			      tempInput.setSelectionRange(0, 999999);
					}
					else {
						tempInput.select();
					}
					document.execCommand("copy");
					document.body.removeChild(tempInput);
					alert("복사되었습니다.");
				}
				function isOS() {
						return navigator.userAgent.match(/ipad|iphone/i);
				}
				</script>
				<? } ?>
			</div>

		</div>
		<!--div class="auto_invest"><a href="/deposit/deposit.php?tab=5"><span class="invest_btn3">자동투자설정 <img src="/theme/2018/img/mobile/right_arrow.png" alt="" /></span></a></div-->
		<div class="body">
			<p></p>
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
						<strong>현재 투자금액</strong>
						<strong><?=price_cutting($member['ing_invest_amount'])?>원</strong>
					</div>
				</li>
				<li>
					<div class="invest_amount">
						<strong>투자 가능한도</strong>
						<strong><?=$invest_possible_amount?></strong>
					</div>
				</li>
				<?
				if ($member['member_investor_type'] == "1") {
					?>
				<div class="triangle"></div>
				<li class="invest_amount2">

					<div class="invest_amount">
						<strong>부동산, 주택담보</strong>
						<strong><?=$invest_possible_amount_prpt;?></strong>
					</div>

					<div class="invest_amount">
						<strong>동산, SCF</strong>
						<strong><?=price_cutting($member['invest_possible_amount_ds'])?>원</strong>
					</div>
				</li>
					<?
				}
				?>
			</ul>
			<ul>
				<li>
					<a href="/deposit/deposit.php"><span class="invest_btn1">투자현황</span></a>
				</li>
			</ul>

		</div>
		<div class="clearfix"></div>

<? if(false) { ?>
<!--
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
//-->
<!--<script>
function change_receive(n_receive_method) {

<? if(!$bank_acct_registered || !$virtual_acct_registered) { ?>
	vaOpen();
	return;
<? } ?>

	if (n_receive_method != '1' &&  n_receive_method !='2') return false;

	var yn = confirm("원리금 수취방식을 변경하시겠습니까?");

	if (yn) {
		$.ajax({
			url : "/mypage/ajax_receive_proc.php",
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
</script>-->
<? } ?>

		<div class="clearfix"></div>

		<div class="mem_infos">

<?
// 회원정보 버튼 설정
$member_info_button = "<a href=\"/bbs/member_confirm.php?url=/mypage/mypage.php\"><span class=\"mem_info_btn\">회원정보</span></a>";
//if( $office_connect ) {
//	if( $member['kyc_allow_yn'] != 'Y' ) {
//		$member_info_button = "<a href=\"javascript:;\" onClick=\"KYCPopup();\"><span class=\"mem_info_btn\">회원정보</span></a>";
//	}
//}
echo $member_info_button . "\n";
?>
			<a href="<?=G5_BBS_URL?>/logout.php"><span class="log_out_btn">로그아웃</span></a>

			<? if( $member['mb_level']>=9 || in_array($member['mb_id'], array('cym5359','rkdtjdgur0','fndgud','iolololoi','hoyeol0524','icvvvb','kubaman','youngsin1969','knson100','jyj26621')) ) { ?>
			<a href="http://doc.hellofunding.kr" target="_blank"><span class="log_out_btn" style="background:#EFEFEF">물건관리</span></a>
			<? } ?>
		</div>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
<?
	}		// end if($is_member)
?>
</div>

<script type="text/javascript">
	/*function domReady (){
		var myScroll = new IScroll('#header_menu', {
			el: document.getElementById('member'),
			momentum: false,
			hScrollbar: false,
			vScrollbar: false
		});
	}

	if (document.addEventListener) {
		document.addEventListener("DOMContentLoaded", function () {
			document.removeEventListener("DOMContentLoaded", arguments.callee, false);
			domReady();
		}, false);
	} // Internet Explorer
	else if (document.attachEvent) {
		document.attachEvent("onreadystatechange", function () {
			if (document.readyState === "complete") {
				document.detachEvent("onreadystatechange", arguments.callee);
				domReady();
			}
		});
	}*/

	// 부동산 상품 설명레이어
	$('#d_flag_btn, #d_flag_close').on('click', function() { $('#d_flag').fadeToggle('slow'); });

	// 사용자 레이어
	$('#name_zone, #invest_close').on('click', function(e) {
		$('#invest_zone').stop().fadeToggle('slow');
	});
</script>

<script type="text/javascript">
function go_kkp() {

	//alert("서비스 점검중입니다.");
	//return false;

<? if(!$BANK[$member['va_bank_code2']]) { ?>
	alert("가상계좌를 발급한 후에 이용가능합니다.");

	vaOpen();

	return false;
<? } ?>

	var request = "";
	$.ajax({
		type: "POST",
		url: "/kakao_remit/kakao_remit_dozn.php",
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