<?
$conf_sql = "SELECT * FROM cf_auto_invest_config WHERE display='Y'";

$conf_res = sql_query($conf_sql);
$conf_cnt = sql_num_rows($conf_res);

for ($i=0 ; $i<$conf_cnt ; $i++) {
	$conf_row = sql_fetch_array($conf_res);
	$get_conf[$i] = $conf_row;
}
$max_auto_amt = get_max_auto_amt();

if ($member["mb_id"]) {
	$user_sql = "select count(a.idx) chk_user_cnt from cf_auto_invest_config_user a left join cf_auto_invest_config b on (a.ai_grp_idx=b.idx) where a.member_idx='$member[mb_no]' ";
	$user_res = sql_query($user_sql);
	$user_row = sql_fetch_array($user_res);
	$user_cnt = $user_row['chk_user_cnt'];
}
?>

<link rel="stylesheet" type="text/css" href="/auto_invest/css/landing_m.css?ver=20190529_1">

<!-- The Modal -->
    <div id="myModal" class="modal">
	<form name="f_auto_inv" id="f_auto_inv">
      <!-- Modal content -->
      <div class="modal-content">
            <p class="popup"></p>
             <div style="position:relative;z-index:1000;cursor:pointer;padding:2px 0;text-align:right;" onClick="close_pop();">
               <img src="/auto_invest/img/close_btn.png" height="18" alt="닫기"/>
            </div>
			<div style="width:100%;text-align:center;">
				<p style="margin-bottom: 12px;"><img src="/auto_invest/img/tit01.jpg" height="21" alt=""/></p>
				<ul class="tabs">
					<li class="tab01 on" relaa="auto_tab1"><a onclick="move_tab('1');" style="cursor1:pointer;">자동투자안내</a></li>
					<li class="line">|</li>
					<li class="tab02 off" relaa="auto_tab2"><a onclick="move_tab('2');" style="cursor1:pointer;">자동투자 설정</a></li>
					<li class="line">|</li>
					<li class="tab04 off" relaa="auto_tab4"><a onclick="move_tab('3');" style="cursor1:pointer;">자동투자 요약</a></li>
				</ul>

				<!--자동투자안내-->
				<div  class="auto01" id="auto_tab1" style="display:block;">
					<p style="padding-top:20px;"><img src="/auto_invest/img/m/tab_cont01.jpg" alt=""/></p>
					<p style="padding:20px 0 30px 0;"><img src="/auto_invest/img/m/btn001.jpg" alt="다음" height="38" onclick="move_tab('2');" style="cursor:pointer;"/></p>
				</div>
				<!--자동투자 설정-->
				<div  class="auto02" id="auto_tab2" style="display:none;">
					<p style="padding-top:10px;font-size:14px;text-align:left;">* 상품선택 후 자동투자 금액을 설정해주세요. (중복선택 가능)</p>
					<ul class="select_cont01">
						<li>
							<input type="checkbox" id="chk_all" name="chk_all" value="Y" onclick="chk_all_btn();"><label for="chk_all"></label>&nbsp;
							<label for="chk_all"><span>모든상품</span> <span style="font-weight:normal">(투자단위 : 만원)</span></label>
							<p style="float: right; margin-right: 0px;text-align:center;">
							<button type="button" name="auto_help_btn" class="btn_blue_s" id="auto_help_btn">도움말</button>
							</p>
						</li>
						<li style="border-bottom:2px solid #d8d8d8;">
							TIP : 투자금액을 1만원부터 설정하면 복리투자 효과!
						</li>
<?
for ($i=0 ; $i<count($get_conf) ; $i++) {
?>
						<input type="hidden" name="auto_moneyOr[<?php ECHO $i;?>]" id="auto_moneyOr[<?php ECHO $i;?>]" value="" />
						<input type="hidden" name="auto_moneyOr2[<?php ECHO $i;?>]" id="auto_moneyOr2[<?php ECHO $i;?>]" value="" />


						<li>
							<input type="hidden" name="grp_idx[<?=$i?>]" value="<?=$get_conf[$i]['idx']?>">
							<input type="hidden" name="chk_item[<?=$i?>]" value="N">

							<div class="row" style="height: 40px;">
							<div style="display: inline-block; max-width: 190px; vertical-align: -webkit-baseline-middle;">
							<input type="checkbox" id="chk_item[<?=$i?>]" name="chk_item[<?=$i?>]" value="Y" onchange="chk_total_checkbox();">
							<label for="chk_item[<?=$i?>]"></label>&nbsp;
							<label for="chk_item[<?=$i?>]" style="letter-spacing:-1px;"><?=$get_conf[$i]["grp_title"]?></span></label>
							<span><a id=det_<?=$get_conf[$i]['idx']?> style="cursor:pointer;"><img src="/auto_invest/img/detail_btn02.png" height="14px" width="14px" style="margin-top: 4px;" alt="자세히 보기"></a></span>
							</div>
							<div style="display: inline-block; float: right;letter-spacing:-1px;">

							<input type="text" id="auto_money[<?=$i?>]" name="auto_money[<?=$i?>]" onblur="chk_num(<?=$i?>);" class="field" value="<?=$def_val[$get_conf[$i][idx]]?>" required style="text-align:right;width:60px;padding-right:4px; height: 34px; margin-top:4px; margin-bottom: 6px; font-size: 14px;" maxlength="6" />
							~
							<input type="text" id="auto_money2[<?=$i?>]" name="auto_money2[<?=$i?>]" OnBlur="chk_num2(<?=$i?>);" class="field" value="0" required style="text-align:right;width:60px;padding-right:4px; height: 34px; margin-top:4px; margin-bottom: 6px; font-size: 14px;" maxlength="6" />
							</div>
						</div>
						</li>
<?
}
?>
					</ul>
					<p style="padding-top:10px;font-size:14px;clear:both;" id="msg_money"></p>
					<p style="padding:20px 0 30px 0;">
						<img src="/auto_invest/img/m/btn002.jpg" alt="이전" height="38" onclick="move_tab('1');" style="cursor:pointer;"/>&nbsp;&nbsp;
						<img src="/auto_invest/img/m/btn001.jpg" alt="다음" height="38" onclick="chk_tab3();" style="cursor:pointer;"/>
					</p>
				</div>

				<!--자동투자요약-->
				<div  class="auto04" id="auto_tab4" style="display:none;">
					<ul>
						<li>
							<p style="padding-bottom:5px;">선택한 상품 및 설정 금액</p>
							<div class="div1 row">
								<div id="disp_selected" style="width: 100%;"></div>
							</div>
						</li>
					</ul>
					<p style="padding-top:10px;font-size:14px;clear:both;">※위와 같이 자동투자를 설정합니다.</p>
					<p style="padding:5px 0 20px 0;">
						<img src="/auto_invest/img/m/btn003.jpg" alt="처음으로" height="38" onclick="move_tab('1');" style="cursor:pointer;"/>&nbsp;&nbsp;
						<img src="/auto_invest/img/m/btn001.jpg" alt="다음" height="38" onclick="go_auto_conf_save();" style="cursor:pointer;"/>
					</p>
					<div style="background-color:#f3f3f4;font-size:12px;text-align:left !important;padding:15px 22px;">
						<div style="font-size:14px;font-weight:600;">자동투자 유의사항</div>
						1. 투자상품의 구조와 투자위험을 충분히 숙지한 후 자동투자를 신청하시기 바랍니다.<br/>
						2. 자동투자 신청 순번에 따라 자동으로 투자가 진행되며, 자동투자 신청자수가 많은 경우 본인의 순번이
						   돌아오기까지 다소 시간이 걸릴 수 있습니다.<br/>
						3. 예치금 잔액 부족 및 투자한도를 초과한 경우 투자가 진행되지 않을 수 있습니다.<br/>
						4. 자동투자설정 금액은 언제든 변경 가능합니다. 설정금액 변경 시 기존 순번은 그대로 유지되며, 변경된 조건은 다음 투자부터 적용됩니다.<br/>
					</div>
				</div>
			</div>
      </div>
    </div>

<!-- 투자위험고지 팝업 시작 -->
<div id="invest_warning_agree" class="popbluetheme" style="height:auto;">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">※ 투자위험고지</div>
	<div class="con">
		<div class="con1">
			본 투자상품은 원금이 보장되지 않습니다. 모든 투자상품은 현행 법률 상 ‘유사수신 행위의 규제에 관한 법률’에 의거하여 원금과 수익을 보장할 수 없습니다.<br/>
			또한 차입자가 원금의 전부 또는 일부를 상환하지 못할 경우 발생하게 되는 투자금 손실 등 투자위험은 투자자가 부담하게 됩니다.
		</div>
		<div class="con2">
			<div style="color:#000">상기 내용을 확인하였으며 그 내용에</div>
			<div style="padding-top:10px;">
				<input type="text" id="str" name="str" maxlength="3" onKeyup="strCheck(this.value);" class="text1"  >
				<input type="hidden" name="agree_yn" id="_agree">
				<br/><br/>
				※ 빈칸에 '<font style="color:red;">동의함</font>' 을 입력하셔야만 자동투자가 가능합니다.
			</div>
		</div>
		<div class="con3" style="color:#FF2222">
		※ 자동투자 설정시 상환방식이 예치금 상환방식으로 변경됩니다.
		</div>
		<div class="btnArea">
			<button type="button" id="invest_warning_agree_btn" class="btn_big_blue2 off" style="ime-mode:active">확인</button>
		</div>
	</div>
</div>
<input type="hidden" name="yn_agree" id="yn_agree" />
</form>

<?
function get_max_auto_amt() {
	global $member;
	$max_amt = 5000000;
	if ($member['mb_no']) {
		if ($member['member_investor_type']=="2") {
			$max_amt = 20000000;
		} else if ($member['member_investor_type']=="3") {
			$max_amt = 9999999999;
		}

		if ($member['member_type']=="2") $max_amt=9999999999;
	}
	return $max_amt;
}
?>

<script>
//var msg = "헬로펀딩은 가맹점에서 발생한 확정매출액을 담보로 대출하며, 카드사의 확정정산대금을 지급받아 영업일 기준 1~3일만에 상환받는 초단기 상품입니다.";
var msg = "헬로펀딩은 가맹점에서 발생한 매출액(확정매출 포함)을 담보로 대출하며, 카드사의 정산대금을 지급받아 영업일 기준 1~6일만에 상환받는 초단기 상품입니다.";
$('#det_16').webuiPopover({ title: "<center>소상공인 매출채권</center>", content: msg, closeable: true, width: 240, trigger: "click", placement: 'bottom', backdrop: false, animation:'pop'});

var msg = "헬로펀딩의 동산담보 상품은 헬로펀딩의 안전금고에 보관되며, 연체 및 부실 발생시 매입업체를 통해 즉시 매각하여 상환됩니다.";
$('#det_15').webuiPopover({ title: "<center>동산담보</center>", content: msg, closeable: true, width: 240, trigger: "click", placement: 'bottom-right', backdrop: false, animation:'pop'});

var msg = "본 상품은 소상공인 확정매출채권 외에 온라인, 오프라인, 면세점 등에서 발생한 확정매출채권을 담보로 대출하며, 확정된 정산대금으로 상환받는 상품입니다.";
$('#det_17').webuiPopover({ title: "<center>헬로페이 (확정매출채권)</center>", content: msg, closeable: true, width: 240, trigger: "click", placement: 'bottom', backdrop: false, animation:'pop'});

var msg = "헬로펀딩의 부동산 담보 상품은 준공자금, ABL, 브릿지, NPL 상품 등으로 구성되어 있으며, 담보의 근저당권, 우선수익권 등 권리설정을 통해 안전장치를 마련합니다.<br/><br/>*본 상품은 투자에 대한 금리, 기간, 권리설정이 다양하므로 상품출시 후 48시간의 유예기간 내 투자설명을 숙지하시어 자동투자 여부를 결정하시기 바랍니다.";
$('#det_13').webuiPopover({ title: "<center>부동산 담보</center>", content: msg, closeable: true, width: 240, trigger: "click", placement: 'bottom-right', backdrop: false, animation:'pop'});

var msg = "LTV 85% 이하의 서울 및 수도권, 6대 광역시, 세종시 등 주요지역 아파트 및 서울 오피스텔,다세대 담보 상품을 취급합니다.";
$('#det_14').webuiPopover({ title: "<center>주택 담보</center>", content: msg, closeable: true, width: 240, trigger: "click", placement: 'bottom-right', backdrop: false, animation:'pop'});

$('#auto_help_btn').webuiPopover({ title: "<center>자동투자 도움말</center>", content: "<img src='/auto_invest/img/auto_help_image_m.png' alert='자동투자 도움말' style='width:100%' >", closeable: true, width: 300, height: 200, trigger: "click", placement: 'bottom-left', backdrop: false, animation:'pop'});
</script>

<?
include "auto_invest.js.php";
?>