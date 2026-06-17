<?
###############################################################################
## 회원정보 수정
## action : /mypage/ajax_user_modify.php
###############################################################################
## 2017-04-27 : 개인회원 상품별 금액 제한 관련 내용 추가
## 2018-03-13 : 원리금 수취방식의 예치금충전방식 재활성화
## 2019-01-21 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
## 2021-09-31 : 원리금 수취방식중 환급계좌 상환 선택불가(삭제) 처리
###############################################################################


include_once('./_common.php');

// 신한은행 점검시간 진입금지 --------------------------------------------------------------
if( date('Y-m-d H:i:s') >= $CONF['BANK_STOP_SDATE'] && date('Y-m-d H:i:s') < $CONF['BANK_STOP_EDATE'] ) {
	$msg = "금융기관 점검시간 입니다.";
	msg_replace($msg, "/");
}

if(!$_COOKIE['pwdauth']) {

	$mb_password = trim($_POST['mb_password']);
	if($mb_password=='') {
		msg_go("", "/bbs/member_confirm.php?url=/mypage/mypage.php");
	}
	$mb_password = sql_escape_string($mb_password);


	$pwd_valid = false;

	if(check_password2($mb_password, $member['mb_password'])) {
		$pwd_valid = true;
	}
	else {
		if(check_password($mb_password, $member['mb_password'])) {

			$pwd_valid = true;

			//신규 암호화 방식으로 변경 및 기존 비번 mb_5으로 이전
			//mb_5 값이 있다는건 SHA256 방식의 비밀번호로 업데이트 되었음을 뜻함.
			if(trim($member['mb_5'])=='') {
				$pwd_change_sql = "
					UPDATE
						g5_member
					SET
						mb_password='".get_encrypt_string2($mb_password)."',
						mb_5='".$member['mb_password']."'
					WHERE
						mb_no='".$member['mb_no']."'";
				sql_query($pwd_change_sql);
			}

		}
	}

	if($pwd_valid) {
		setcookie("pwdauth", "Y", time()+300, "/", G5_COOKIE_DOMAIN, true, true);  //5분짜리 쿠키 발행
	}
	else {
		msg_go("비밀번호가 틀립니다.", "/bbs/member_confirm.php?url=/mypage/mypage.php");
	}

}


$g5['title'] = '회원정보';

$g5['top_bn'] = "/images/mypage/sub_info.jpg";
$g5['top_bn_alt'] = "회원정보 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');



if($is_member){
	if($member["mb_hp"]){
		$mb_hp  = preg_replace("/(-| )/", "", $member["mb_hp"]);
		$mb_hp1 = substr($mb_hp, 0, 3);
		$mb_hp2 = substr($mb_hp, 3, -4);
		$mb_hp3 = substr($mb_hp, -4);
	}

	$req_row = sql_fetch("SELECT count(idx) AS cnt FROM investor_type_change_request WHERE mb_no='".$member['mb_no']."' AND allow='wait'");
	$req_count = $req_row['cnt'];
}


$member['regist_number'] = getJumin($member['mb_no']);


if($member['regist_number']) {
	$private_mode = "modify";
	$befor_private_str = $member["bank_private_name"]."||".$member['regist_number']."||".$member["bank_code"]."||".$member["account_num"];
}
else{
	$private_mode = "insert";
	$befor_private_str = "";
}



/**************************************************************************************************************
NICE평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED

서비스명 :  체크플러스 - 안심본인인증 서비스
페이지명 :  체크플러스 - 메인 호출 페이지
보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다.
**************************************************************************************************************/


$sitecode   = "AB917";						// NICE로부터 부여받은 사이트 코드
$sitepasswd = "8vJBrEtmUvdb";			// NICE로부터 부여받은 사이트 패스워드

$authtype  = "M";		// 없으면 기본 선택화면, X: 공인인증서, M: 핸드폰, C: 카드
$popgubun  = "N";		//Y : 취소버튼 있음 / N : 취소버튼 없음
$customize = "";		//없으면 기본 웹페이지 / Mobile : 모바일페이지

$reqseq = "REQ_0123456789";     // 요청 번호, 이는 성공/실패후에 같은 값으로 되돌려주게 되므로

// 업체에서 적절하게 변경하여 쓰거나, 아래와 같이 생성한다.
	//if (extension_loaded($module)) {// 동적으로 모듈 로드 했을경우
		$reqseq = get_cprequest_no($sitecode);
	//} else {
	//	$reqseq = "Module get_request_no is not compiled into PHP";
	//}

// CheckPlus(본인인증) 처리 후, 결과 데이타를 리턴 받기위해 다음예제와 같이 http부터 입력합니다.
$returnurl = G5_URL."/mypage/join_sign_success.php";	// 성공시 이동될 URL
$errorurl  = G5_URL."/mypage/join_sign_fail.php";			// 실패시 이동될 URL

// reqseq값은 성공페이지로 갈 경우 검증을 위하여 세션에 담아둔다.

$_SESSION["REQ_SEQ"] = $reqseq;

// 입력될 plain 데이타를 만든다.
$plaindata =  "7:REQ_SEQ" . strlen($reqseq) . ":" . $reqseq .
						  "8:SITECODE" . strlen($sitecode) . ":" . $sitecode .
						  "9:AUTH_TYPE" . strlen($authtype) . ":". $authtype .
						  "7:RTN_URL" . strlen($returnurl) . ":" . $returnurl .
						  "7:ERR_URL" . strlen($errorurl) . ":" . $errorurl .
						  "11:POPUP_GUBUN" . strlen($popgubun) . ":" . $popgubun .
						  "9:CUSTOMIZE" . strlen($customize) . ":" . $customize ;

	//if (extension_loaded($module)) {// 동적으로 모듈 로드 했을경우
		$enc_data = get_encode_data($sitecode, $sitepasswd, $plaindata);
	//} else {
	//	$enc_data = "Module get_request_data is not compiled into PHP";
	//}

if( $enc_data == -1 ) {
	$returnMsg = "암/복호화 시스템 오류입니다.";
	$enc_data = "";
}
else if( $enc_data== -2 ) {
	$returnMsg = "암호화 처리 오류입니다.";
	$enc_data = "";
}
else if( $enc_data== -3 ) {
	$returnMsg = "암호화 데이터 오류 입니다.";
	$enc_data = "";
}
else if( $enc_data== -9 ) {
	$returnMsg = "입력값 오류 입니다.";
	$enc_data = "";
}

$receive_type1 = $receive_type2 = false;
if($member['bank_code'] && $member['account_num'] && $member['bank_private_name']) {
	$receive_type1 = true;
}
if($member['va_bank_code2'] && $member['virtual_account2'] && $member['va_private_name2']) {
	$receive_type2 = true;
}

// 가상계좌 등록내역 (세틀뱅크)
$VACT = sql_fetch("SELECT bank_cd, acct_no, cmf_nm, acct_st FROM vacs_vact WHERE acct_no='".$member['virtual_account']."' ORDER BY acct_no DESC LIMIT 1");

// 가상계좌 등록내역 (신한)
$KSNET_VACT = sql_fetch("SELECT BANK_CODE, VR_ACCT_NO, CORP_NAME, USE_FLAG FROM KSNET_VR_ACCOUNT WHERE VR_ACCT_NO='".$member['virtual_account2']."' ORDER BY VR_ACCT_NO DESC LIMIT 1");

if($member['bank_code'] && $member['bank_private_name'] && $member['account_num']) {
	$real_account_regist = true;
	if($KSNET_VACT['USE_FLAG']=='Y') {
		$ib_vact_status  = '정상';
	}
	else if($KSNET_VACT['USE_FLAG']=='N') {
		$ib_vact_status = '거래불가';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'거래불가코드가 등록되었습니다.\\n고객센터로 문의하십시요.\');" class="btn_blue">신한은행 가상계좌 발급받기</button>';
	}
	else {
		$ib_vact_status = '미발급';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="location.href=\'/deposit/deposit.php?tab=3\';" class="btn_blue">신한은행 가상계좌 발급받기</button>';
	}
}
else {
	$real_account_regist = false;
	$ib_vact_status = '미발급';
	$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'원리금을 상환 받으실 환급계좌를 먼저 등록 하셔야 합니다.\');" class="btn_blue_dis">신한은행 가상계좌 발급받기</button>';
}

//print_rr($member, "text-align:left");

?>

<!-- 본문내용 START -->
<script type="text/javascript" src="<?=G5_JS_URL?>/mypage.js?ver=20200821"></script>
<style>
.address_field{width:120px; height:30px; border-color:#CFCFCF; border-width:1px; border-style:solid; background-color:#FBFBFB}
</style>
<? if(G5_IS_MOBILE) { ?>
<style>
.content .type01 input.text { width:98%; }
.content .type01 textarea.textArea { width:98%; }
</style>
<? } ?>

<div id="content">

<? if(G5_IS_MOBILE){ ?>
	<!--<img src="<?=G5_THEME_URL?>/img2/mypage/sub_info.jpg" alt="회원정보 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.">-->
<? } ?>

	<!--div class="location"><span></span><b class="blue">회원정보 수정 및 환급계좌 등록</b></div-->

	<div class="content">

		<form name="frm" id="frm" method="post" ENCTYPE="multipart/form-data">
			<input type="hidden" name="service"      value="1">				<!-- 계좌 소유주 확인 서비스 구분 -->
			<input type="hidden" name="svcGbn"       value="5">				<!-- 업무구분 -->
			<input type="hidden" name="svc_cls"      value="">
			<input type="hidden" name="mb_no"        value="<?=$member["mb_no"]?>">
			<input type="hidden" name="url"          value="<?=G5_URL?>">
			<input type="hidden" name="member_type"  value="<?=$member["member_type"]?>">
			<input type="hidden" name="before_member_private" value="<?=$befor_private_str?>">
			<input type="hidden" name="private_yn"   value="<?=($member["member_type"]=='2')?'Y':''?>">
			<input type="hidden" name="private_mode" value="<?=$private_mode?>">
			<input type="hidden" name="bank_name"    value="<?=$member["bank_name"]?>">
			<input type="hidden" name="old_mb_hp"    value="<?=$mb_hp?>">
			<input type="hidden" name="mb_dupinfo"   value="<?=$member["mb_dupinfo"]?>" id="mb_dupinfo">
			<input type="hidden" name="mb_ci"        value="<?=$member["mb_ci"]?>" id="mb_ci">
			<input type="hidden" name="mobile"       value="x">
			<input type="hidden" name="investor_update" value="">

			<!-- 개인회원 -->
			<div class="typeBox">
				<h3>회원정보 수정</h3>
				<div class="type01 mb40">
					<table>
						<tbody>
							<tr>
								<th>ID</th>
								<td id="mb_id"></td>
							</tr>
							<tr>
								<th>비밀번호</th>
								<td>
									<input type="password" class="text" id="mb_password" name="mb_password" placeholder="<?=$PW_LIMIT[$idpw_type]['describe']?>" onKeyUp="passwd_check();" onBlur="passwd_check();"> (비밀번호를 변경하고자 하시는 분만 입력해 주세요)
									<span id="mb_password_error" class="error-block"></span>
								</td>
							</tr>
							<tr>
								<th>비밀번호확인</th>
								<td>
									<input type="password" class="text" id="cfm_password" name="cfm_password" onKeyUp="passwd_check();" onBlur="passwd_check();"> (비밀번호를 입력하신 분만 입력해 주세요)
									<span id="cfm_password_error" class="error-block"></span>
								</td>
							</tr>
<? if($member["member_type"]=="1") { ?>
							<tr>
								<th>성명</th>
								<td>
									<span id="member_name_text" style="display:none"></span>
									<input type="text" class="text" name="mb_name" id="mb_name" value="<?=$member["mb_name"]?>" readonly>
								</td>
							</tr>
							<tr>
								<th>본인인증</th>
								<td>
									<input type="button" class="btn_green" value="본인인증하기" onClick="fnPopup();"> <span id="member_sign" style="color:tomato;">(휴대폰번호 교체시 재인증 하십시오.)</span>
								</td>
							</tr>
<? } else { ?>
							<tr>
								<th>상호명</th>
								<td><input type="text" class="text" name="mb_co_name" id="mb_co_name" value="<?=$member["mb_co_name"]?>" readonly></td>
							</tr>
							<tr>
								<th>사업자번호</th>
								<td><input type="text" class="text" name="mb_co_reg_num" id="mb_co_reg_num" value="<?=$member["mb_co_reg_num"]?>" readonly></td>
							</tr>
							<tr>
								<th>담당자명</th>
								<td><input type="text" class="text" name="mb_name" id="mb_name" value="<?=$member["mb_name"]?>"></td>
							</tr>
<? } ?>
							<tr>
								<th>휴대폰번호</th>
								<td>
									<input type="text" class="text" id="mb_hp1" name="mb_hp1" size="3" style="min-width:54px" value="<?=$mb_hp1?>" maxlength="4" <? if($member["member_type"]=="1") { ?>readonly<? }else{ ?>onKeyup="onlyDigit(this);"<? } ?>>&nbsp;&nbsp;
									<input type="text" class="text" id="mb_hp2" name="mb_hp2" size="3" style="min-width:54px" value="<?=$mb_hp2?>" maxlength="4" <? if($member["member_type"]=="1") { ?>readonly<? }else{ ?>onKeyup="onlyDigit(this);"<? } ?>>&nbsp;&nbsp;
									<input type="text" class="text" id="mb_hp3" name="mb_hp3" size="3" style="min-width:54px" value="<?=$mb_hp3?>" maxlength="4" <? if($member["member_type"]=="1") { ?>readonly<? }else{ ?>onKeyup="onlyDigit(this);"<? } ?>>
									<span id="mb_hp_text" style="display:none"></span>
								</td>
							</tr>
							<tr>
								<th>이메일</th>
								<td><input type="text" class="text" name="mb_email" id="mb_email" value="<?=$member["mb_email"]?>"></td>
							</tr>

<?
if($member['member_type']=='1') {

	$MITYPE = array(
							'1' => array('title'=>'일반 투자자',     'invest_limit'=>'최대 1,000만원'),
							'2' => array('title'=>'소득적격 투자자', 'invest_limit'=>'최대 4,000만원'),
							'3' => array('title'=>'전문 투자자',     'invest_limit'=>'무제한')
						);

?>
							<tr>
								<th>투자자 유형</th>
								<td style="line-height:30px;">
									<p style="font-weight:bold;color:#153FA1"><?=$MITYPE[$member['member_investor_type']]['title']?></p>
									<? if(in_array($member['member_investor_type'], array('2','3'))) { ?>
									<p style="font-weight:bold;color:#153FA1">
										<!-- 유효기간 : <?=preg_replace("/-/", ".", $member['special_investor']['rights_sdate'])?> ~ <?=preg_replace("/-/", ".", $member['special_investor']['rights_edate'])?> //-->
										<? if( $member['special_investor']['valid_days'] <= 30 ) { ?><a id="extend_btn" class="btn_blue" style="margin-left:30px;">기간연장</a><? } ?>
									</p>
									<? } ?>
								</td>
							</tr>

							<tr>
								<th>투자자 유형 변경</th>
								<td style="line-height:30px;">
									<ul style="list-style:none">
										<li <?if(!G5_IS_MOBILE){?>style="float:left;width:180px"<?}?>><input type="radio" name="member_investor_type" value="1" <?=($member['member_investor_type']=='1')?'checked':''?> <?=($member['member_investor_type']>1)?'disabled':''?>>&nbsp;<strong>일반 투자자</strong></li>
										<!--<li style="padding-left:16px"><font style="font-size:12px;color:#FF3333">투자한도 최대 <?=price_cutting($INDI_INVESTOR['1']['site_limit'])?>원<?=(G5_IS_MOBILE)?'<br>':''?>
											(단, 부동산 상품(PF, 부동산 담보 등)은 <?=price_cutting($INDI_INVESTOR['1']['prpt_limit'])?>원까지 투자 가능)</font></li>-->
										<li style="padding-left:16px"><font style="font-size:12px;color:#FF3333">투자한도 업권 내 3천만원</font></li>
									</ul>
									<ul style='list-style:none'>
										<li <?if(!G5_IS_MOBILE){?>style="float:left;width:180px"<?}?>><input type="radio" name="member_investor_type" value="2" <?=($member['member_investor_type']=='2')?'checked':''?> <?=($member['member_investor_type']>2)?'disabled':''?>>&nbsp;<strong>소득적격 투자자</strong></li>
										<!--<li style="padding-left:16px;"><font style="font-size:12px;color:#FF3333">투자한도 최대 <?=price_cutting($INDI_INVESTOR['2']['site_limit'])?>원</font></li>-->
										<li style="padding-left:16px;"><font style="font-size:12px;color:#FF3333">투자한도 업권 내 1억원</font></li>
									</ul>
									<ul style='list-style:none'>
										<li <?if(!G5_IS_MOBILE){?>style="float:left;width:180px"<?}?>><input type="radio" name="member_investor_type" value="3" <?=($member['member_investor_type']=='3')?'checked':''?> <?=($member['member_investor_type']>3)?'disabled':''?>>&nbsp;<strong>전문 투자자</strong></li>
										<li style="padding-left:16px;"><font style="font-size:12px;color:#FF3333">투자한도 무제한</font></li>
									</ul>

									<? if($req_count) { ?>
									<div style="color:blue">현재 회원님이 등록하신 <b><?=$req_count?></b>건의 투자자 유형 변경 요청이 승인심사중 입니다.</div>
									<? } ?>

									<div id="file_zone" style="display:none;">
										<div style="margin:16px 0 8px;">
											<ul>
												<li>
													<b>[첨부서류]</b> &nbsp;
													<input type='button' value='+' onClick='asset_add()' style="width:50px;">
													<input type='button' value='-' onClick='asset_del()' style="width:50px;">
												</li>
											</ul>
											<ul>
												<li id="describe01">▶ 소득적격 투자자 : 종합소득 과세표준 확정신고서, 종합소득세신고서 접수증 첨부</li>
												<li id="describe02">▶ 전문 투자자 : 전문투자자 확인증 첨부</li>
											</ul>
											<ul>
												<li style="color:brown">※ 투자자 유형 변경에 필요한 각각의 서류가 첨부되지 않은 경우 변경 신청은 등록 되지 않습니다.</li>
											</ul>
										</div>
										<table id="aset" totalvalue="1" width="100%" style="border:0;">
											<tr>
												<td>
													<ul style='list-style:none'>
														<li style='float:left;padding-right:4px;'><input type='file' name='attach_file[]' id='attachFile' style='height:35px;<?=(G5_IS_MOBILE)?'width:98%':''?>'></li>
														<li style='float:left'><input type='text' name='memo[]' class='text' placeholder='첨부서류 간략설명' style='width:100%'></li>
													</ul>
												</td>
											</tr>
											<tr>
												<td>
													<ul style='list-style:none'>
														<li style='float:left;padding-right:4px;'><input type='file' name='attach_file[]' id='attachFile' style='height:35px;<?=(G5_IS_MOBILE)?'width:98%':''?>'></li>
														<li style='float:left'><input type='text' name='memo[]' class='text' placeholder='첨부서류 간략설명' style='width:100%'></li>
													</ul>
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>

<? if(G5_TIME_YMD >= $CONF['online_invest_policy_sdate']) { ?>
							<tr>
								<th>투자설명서 발급</th>
								<td style="line-height:30px;">
									<p>
										<label style="cursor:pointer;"><input type="checkbox" id="invested_mailling" name="invested_mailling" <?=($member['invested_mailling']=='1')?'checked':''?>> 투자설명서 발급에 동의합니다.
										<!--<font style="font-size:12px;">(정상투자 실행시 관련 내용을 전자우편으로 고지함)</font>--></label>
									</p>
								</td>
							</th>
<? } ?>

							<script type="text/javascript">
							changeHighlight = function() {
								var cval = $('input:radio[name=member_investor_type]:checked').val();
								if(cval=='2') {
									$('#describe01').css('color','#3366FF');
									$('#describe02').css('color','#CCCCCC');
								}
								else if(cval=='3') {
									$('#describe01').css('color','#CCCCCC');
									$('#describe02').css('color','#3366FF');
								}
							}

							$('input:radio[name=member_investor_type], #extend_btn').click(function() {
								$('input[name=investor_update]').val('1');
								var cval = $('input:radio[name=member_investor_type]:checked').val();
								if(cval=='2' || cval=='3') {
									$('#file_zone').show();
									changeHighlight();
								}
								else {
									$('#file_zone').hide();
								}
							});

							var aset = document.getElementById('aset');
							function asset_del(){
								if(aset.totalvalue==1) {
									alert("최소한 1개는 존재해야합니다.");
								}
								else {
									aset.deleteRow(aset.childNodes[0].childNodes.length-1);
									aset.totalvalue = parseInt(aset.totalvalue) - 1;
								}
							}

							function asset_add(){
								aset.totalvalue = parseInt(aset.totalvalue) + 1;
								var value  = aset.totalvalue;
								var new_tr = aset.insertRow();
								var new_td = new_tr.insertCell();
								var temp  = "";
										temp += "<ul style='list-style:none'>";
										temp += "  <li style='float:left;padding-right:4px;'><input type='file' name='attach_file[]' id='attachFile' style='height:35px;<?=(G5_IS_MOBILE)?'width:98%':''?>'></li>";
										temp += "  <li style='float:left'><input type='text' name='memo[]' class='text' placeholder='첨부서류 간략설명' style='width:100%'></li>";
										temp += "</ul>";
										new_td.innerHTML = temp;
							}
							</script>
<?
}
else {
?>
		<input type='file' name='attach_file[]' id='attachFile' style='display:none'>
		<input type='text' name='memo[]' style='display:none'>
<?
}
?>

						</tbody>
					</table>

					<div style="height:30px;"></div>

					<h3>서비스 정책동의</h3>
					<div class="type02">
						<table>
							<tbody>
								<tr>
									<td>
										<label style="cursor:pointer;color:#ff2222"><input type="checkbox" id="agree_provision" checked disabled> [필수] <span style="cursor:pointer" onClick="location.href='<?=G5_BBS_URL?>/content.php?co_id=provision';"><u>서비스 이용약관</u></span>에 동의합니다.</label><br>
										<label style="cursor:pointer;color:#ff2222"><input type="checkbox" id="agree_privacy"   checked disabled> [필수] <span style="cursor:pointer" onClick="location.href='<?=G5_BBS_URL?>/content.php?co_id=privacy';"><u>개인정보처리방침</u></span>에 동의합니다.</label><br>
										<label style="cursor:pointer;color:#3366ff"><input type="checkbox" id="options_agree" name="options_agree" value="1" <?=($member['options_agree']=='1')?'checked':''?>> [선택] <span style="cursor:pointer" onClick="location.href='<?=G5_BBS_URL?>/content.php?co_id=marketing_agreement';"><u>마케팅 정보 수집 및 활용</u></span>에 동의합니다.</label><br>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

<?

	$modify_button = '<a href="javascript:;" class="btn_big_blue" id="check" onClick="member_modify()">확인</a>';

	// 본인확인이 되지 않은 경우
	if( date('Y-m-d') >= '2022-01-01' && $member['kyc_next_dd'] <= date('Y-m-d') ) {
		$modify_button = '<a href="javascript:;" class="btn_big_blue" id="check" onClick="KYCPopup();">확인</a>';
	}

?>
				<div class="btnArea">
					<?=$modify_button?>
					<a href="/mypage/break.php" class="btn_big_gray" id="check">회원탈퇴</a>
				</div>

				<div style="height:50px;"></div>

				<a id="bank_edit"></a>

				<h3>환급계좌 등록 및 원천징수정보</h3>
				<div class="type01 mb40">
					<!-- 개인회원 정보 -->
					<table id="m_type1" style="<?=($member["member_type"]==1)?"":"display:none"?>">
						<tbody>
							<tr>
								<th>성명</th>
								<td>
									<input type="text"class="text"  name="USERNM1" id="USERNM1" value="<?=($member["bank_private_name"])?$member["bank_private_name"]:$member['mb_name'];?>" style="color:#afafaf;" readonly> &nbsp;
									(부기명) <input type="text"class="text"  name="bank_private_name_sub" id="bank_private_name_sub" value="<?=$member["bank_private_name_sub"]?>"> <!-- 2017-07-10 적용 -->
								</td>
							</tr>
<? if(!$receive_type1) { ?>
							<tr>
								<th>주민등록번호</th>
								<td><input type="text" class="text" name="JUMINNO1" id="JUMINNO1" value="<?=$member['regist_number']?>" onKeyup="onlyDigit(this)"> (숫자만 입력하십시오)</td>
							</tr>
<? } else { ?>
							<input type="hidden" name="JUMINNO1" id="JUMINNO1" value="<?=$member['regist_number']?>">
<? } ?>
						</tbody>
					</table>
					<!------------------->
					<!-- 기업회원 정보 -->
					<table id="m_type2" class="text" style="<?=($member["member_type"]==2)?"":"display:none"?>">
						<tbody>
<?
				if($receive_type1) {
					$bank_info = $member['bank_name'] . " " . $member['account_num'] . " " . $member['bank_private_name'];
					$bank_info.= ($member['bank_private_name_sub']) ? " " . $member['bank_private_name_sub'] : "";  //부기명 추가
?>
							<tr>
								<th>환급계좌</th>
								<td><?=$bank_info?></td>
							</tr>
<?
				}
?>

							<tr>
								<th rowspan="2">원리금<br>수취방식</th>
								<td>
									<ul>

										<li id="doc2" style="padding:0 10px 10px 10px;float:left">
											<label><input type="radio" name="receive_method" value="2" <?=($member["receive_method"]=='2' || ($member["receive_method"]!='1' || $receive_type2)) ? 'checked':'';?> onClick="checkHighlight();"> <strong>예치금 상환</strong>
											<div style="padding:6px 0 0 16px;font-size:0.96em">원리금을 회원님의 예치금으로 지급하여 드리는 방식입니다.<br>
											상환된 원리금으로 지속적인 투자운용을 하실 경우 편리합니다.</div></label>
										</li>
<? if(date('Y-m-d') <= '2021-09-30') { ?>
										<li id="doc1" style="padding:0 0 10px 10px;float:left">
											<label><input type="radio" name="receive_method" value="1" <?=($member["receive_method"]=='1') ? 'checked':'';?> onClick="checkHighlight();"> <strong>환급계좌 상환</strong>
											<div style="padding:6px 10px 0 16px;font-size:0.96em">원리금을 회원님 본인명의의 실 계좌로 지급하여 드리는 방식입니다.<br>
											별도의 출금신청이 필요 없어 편리합니다.</div></label>
										</li>
<? } ?>
									<label>
								</td>
							</tr>
							<tr>
								<td style="line-height:20px;padding:15px 0;">
									<span style="color:#2e55be;font-weight:bold;">※ 원리금 지급 일시</span>
									<div style="padding:6px 0 0 16px">원금은 대출자가 대출금을 상환할 시 5영업일 이내에 해당 월 이자와 원금이 함께 지급되며 수익금은 매월 5일(공휴일인 경우 익일)에 지급됩니다.</div>
								</td>
							</tr>

							<tr>
								<th>사업자등록증</th>
								<td>
									<input type="file" name="business_license" style="width:284px;height:30px;"> <input type="button" class="btn_green" value="취소" id="btn_clear_file">
<?
						if($member["business_license"] != ""){
?>
									<a href="<?=G5_URL?>/mypage/license_download.php" style="color:#FF6633" alt="<?=$member["business_license"]?>">[파일보기]</a>	&nbsp;&nbsp;
									<!--<input type="checkbox" id="del_business_license" name="del_business_license" value="Y"> 삭제-->
									<input type="hidden" id="org_business_license" name="org_business_license" value="<?=$member["business_license"]?>">
<?
						}
?>
								</td>
							</tr>
							<tr>
								<th>통장사본</th>
								<td>
									<input type="file" name="bankbook" style="width:284px;height:30px;"> <input type="button" class="btn_green" value="취소" id="btn_clear_file2">
<?
						if($member["bankbook"] != ""){
?>
									<a href="<?=G5_URL?>/mypage/bankbook_download.php" style="color:#FF6633" alt="<?=$member["bankbook"]?>">[파일보기]</a>	&nbsp;&nbsp;
									<!--<input type="checkbox" id="del_bankbook" name="del_bankbook" value="Y"> 삭제-->
									<input type="hidden" id="org_bankbook" name="org_bankbook" value="<?=$member["bankbook"]?>">
<?
						}
?>
								</td>
							</tr>
						</tbody>
					</table>

<? if($member['member_type']!='2') { ?>
					<table>
						<tbody>
<?	if(false) { ?>
							<!--
							<tr>
								<th>주소</th>
								<td>
									<input type="text" id="zip_num" name="zip_num" value="<?=$member["zip_num"]?>" class="address_field" placeholder=" 우편번호" onFocus="focus_out('zip_btn')">
									<input type="button" id='zip_btn' class="btn_green" onClick="execDaumPostcode()" value="주소찾기"><br>
									<input type="text" id="address_road" name="address_road" class="text" placeholder="도로명주소" onFocus="focus_out('zip_btn')" value="<?=$member["mb_addr1"]?>"  ><br>
									<input type="text" id="address_dong" name="address_dong" class="text" placeholder="지번주소" onFocus="focus_out('zip_btn')" value="<?=$member["mb_addr_jibeon"]?>" >
									<span id="guide" style="color:#999"></span>
								</td>
							</tr>
							<tr>
								<th>상세주소</th>
								<td><input type="text" class="text" name="mb_addr2"   value="<?=$member["mb_addr2"]?>"></td>
							</tr>
							//-->
<?	} ?>
							<tr>
								<th rowspan="2">계좌번호</th>
								<td>
									<select name="strBankCode" id="strBankCode">
										<option value="">은행을 선택하세요</option>
										<?
										$BANK_KEYS = array_keys($BANK);
										for($i=0; $i<count($BANK); $i++) {
											$selected = ($BANK_KEYS[$i]==sprintf("%03d", $member["bank_code"])) ? 'selected' : '';
											echo "<option value='".$BANK_KEYS[$i]."' $selected>".$BANK[$BANK_KEYS[$i]]."</option>\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td><input type="text" class="text small" name="strAccountNo" id="strAccountNo" value="<?=$member["account_num"]?>" onKeyup="onlyDigit(this);">
									<input type="button" class="btn_green" value="계좌인증" onClick="check_bank_new()"> (숫자만 입력하십시오)
								</td>
							</tr>

							<tr>
								<th>가상계좌</th>
								<td style="height:50px;">
									<li style='padding-left:10px;font-weight:bold;color:#2222FF;vetical-align:middle;line-height:25px;list-style:none;'><?=$BANK[$KSNET_VACT['BANK_CODE']]?> <?=$KSNET_VACT['VR_ACCT_NO']?> <br/><?=$KSNET_VACT['CORP_NAME']?> <?=$vact_reg_button?></li>
									<li style='padding-left:10px;line-height:25px;list-style:none;'><span style='display:inline-block;font-weight:700;color:red;'>※ 예치금 입금 시 반드시 본인 명의의 계좌를 통해 본인명</span>으로 이체하여 주시기 바랍니다. <br/>(타인 명의 입금 시 예치금 전환 및 출금이 제한될 수 있습니다.)</li>
								</td>
							</tr>

							<tr>
								<th rowspan="2">원리금<br>수취방식</th>
								<td>
									<ul>
										<li id="doc2" style="padding:0 10px 10px 10px;float:left">
											<label><input type="radio" name="receive_method" value="2" <?=($member["receive_method"]!='1' || $receive_type2) ? 'checked':'';?> onClick="checkHighlight();"> <strong>예치금 상환</strong>
											<div style="padding:6px 0 0 16px;font-size:0.96em">원리금을 회원님의 예치금으로 지급하여 드립니다.
											<!--<br>상환된 원리금으로 지속적인 투자운용을 하실 경우 편리합니다.--></div></label>
									  </li>
<? if(date('Y-m-d') <= '2021-09-30') { ?>
										<li id="doc1" style="padding:0 0 10px 10px;float:left">
											<label><input type="radio" name="receive_method" value="1" <?=($member["receive_method"]=='1') ? 'checked':'';?> onClick="checkHighlight();"> <strong>환급계좌 상환</strong>
											<div style="padding:6px 10px 0 16px;font-size:0.96em">원리금을 회원님 본인명의의 실 계좌로 지급하여 드리는 방식입니다.<br>
											별도의 출금신청이 필요 없어 편리합니다.</div></label>
										</li>
<? } ?>
									<label>
								</td>
							</tr>
							<tr>
								<td style="line-height:20px;padding:15px 0;">
									<span style="color:#2e55be;font-weight:bold;">※ 원리금 지급 일시</span>
									<div style="padding:6px 0 0 16px">원금은 대출자가 대출금을 상환할 시 5영업일 이내에 해당 월 이자와 원금이 함께 지급되며 수익금은 매월 5일(공휴일인 경우 익일)에 지급됩니다.</div>
								</td>
							</tr>
						</tbody>
					</table>
<? } ?>

				</div>
			</div>
			<!-- 개인회원 End -->

<?
	if($member['member_type']=='2') {
		$modify_button2 = '<a href="javascript:;" class="btn_big_blue" id="check" onClick="private_process_for_business();">확인</a>';
	}
	else {
		$modify_button2 = '<a href="javascript:;" class="btn_big_blue" id="check" onClick="private_process();">확인</a>';
	}

	// 본인확인이 되지 않은 경우
	if( date('Y-m-d') >= '2022-01-01' && $member['kyc_next_dd'] <= date('Y-m-d') ) {
		$modify_button2 = '<a href="javascript:;" class="btn_big_blue" id="check" onClick="KYCPopup();">확인</a>';
	}
?>
			<div class="btnArea">
				<?=$modify_button2;?>
			</div>

		</form>

	</div>
</div>

<!-- 본문내용 E N D -->

<!-- 본인인증 서비스 팝업을 호출하기 위해서는 다음과 같은 form이 필요합니다. -->
<form name="form_chk" method="post">
	<input type="hidden" name="m" value="checkplusSerivce">						<!-- 필수 데이타로, 누락하시면 안됩니다. -->
	<input type="hidden" name="EncodeData" value="<?=$enc_data?>">		<!-- 위에서 업체정보를 암호화 한 데이타입니다. -->

	<!-- 업체에서 응답받기 원하는 데이타를 설정하기 위해 사용할 수 있으며, 인증결과 응답시 해당 값을 그대로 송신합니다.
	해당 파라미터는 추가하실 수 없습니다. -->
	<input type="hidden" name="param_r1" value="">
	<input type="hidden" name="param_r2" value="">
	<input type="hidden" name="param_r3" value="">
</form>

<?
if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>

<script>
checkHighlight = function() {
	$('#doc1,#doc2').css('color','');
	$checkedval = $('input:radio[name="receive_method"]:checked').val();
	if($checkedval=='1') $('#doc1').css('color','#FF2222');
	else if($checkedval=='2') $('#doc2').css('color','#FF2222');
}
$(document).ready(function() { checkHighlight(); });
</script>

<script>
window.name ="Parent_window";
function fnPopup() {
	window.open('', 'popupChk', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
	document.form_chk.action = "https://nice.checkplus.co.kr/CheckPlusSafeModel/checkplus.cb";
	document.form_chk.target = "popupChk";
	document.form_chk.submit();
}

$(document).ready(function(){
<? if($member["member_type"]=="1") { ?>
	$('#mb_hp1 , #mb_hp2 , #mb_hp3, #mb_name').click(function(){
		alert("본인인증 후 변경 가능합니다.");
		return;
	});
<? } ?>
	$('#btn_clear_file').click(function(){
		$("input[name='business_license']").val("");
	});
	$('#btn_clear_file2').click(function(){
		$("input[name='bankbook']").val("");
	});


	//탭 기능
	$('.typeBox:eq(0)').show();
	$('.tab_type01 li').click(function(){
		var cur = $(this).index();
		$(this).addClass('on').siblings().removeClass('on');
		$('.typeBox').hide();
		$('.typeBox:eq('+cur+')').show();
	});
	get_info();
});


// 암호 형식 체크 function
pass_string_check = function(str){
	var result = true;
	var re1 = /[a-zA-Z]/i;		// 영문
	var re2 = /[0-9]/i;				// 숫자
	var re3 = /[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?]/i; // 특수문자

	if(!re1.test(str)) { result = false; }
	if(!re2.test(str)) { result = false; }
	<? if($idpw_type=='hard') { ?>if(!re3.test(str)) { result = false; }<? } ?>

	var len = str.length;

	if(len < <?=$PW_LIMIT[$idpw_type]['min_length']?> || len > <?=$PW_LIMIT[$idpw_type]['max_length']?>) { result = false; }
	if(str.indexOf(' ') > -1) { result = false; }
	return result;
}

// 암호 체크
passwd_check = function() {
	var str1 = $('#mb_password').val();
	var str2 = $('#cfm_password').val();
	if(str1.length > 1) {
		if(pass_string_check(str1)==true) {
			$('#mb_password_error').html('<br><span style="color:green">형식에 적합한 비밀번호 입니다.</span>');
		}
		else {
			$('#mb_password_error').html('<br><span style="color:red"><?=$PW_LIMIT[$idpw_type]['describe']?></span>');
		}
	}
	else {
		$('#mb_password_error').empty();
	}

	$('#cfm_password_error').empty();
	if(str2.length > 1) {
		if(str1==str2) {
			$('#cfm_password_error').html('<br><span style="color:green">비밀번호가 일치합니다.</span>');
		}
		else {
			$('#cfm_password_error').html('<br><span style="color:red">비밀번호가 일치하지 않습니다.</span>');
		}
	}
}
</script>