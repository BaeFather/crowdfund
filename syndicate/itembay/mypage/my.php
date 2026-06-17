<?
###############################################################################
## 마이페이지
###############################################################################

include_once('./_common.php');

include_once(HF_PATH.'/hf_head.php');

IF(!$member["mb_no"]) {
	alert("접근이 올바르지 않습니다");
	exit;
}
?>
			<div id="content">
				<div id="member_mypage">
					<!--투자자회원정보 시작-->
					<div class="logout">
						<div id="invest_zone2" class="invest_zone2">
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
								<div class="footer1">
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
						</div>
					</div>
				</div>
			</div>
<script>
function change_receive(n_receive_method) {

<? if(!$bank_acct_registered || !$virtual_acct_registered) { ?>
	vaOpen();
	//return;
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


			</div>

<?
include_once(HF_PATH.'/_tail.php');
?>
