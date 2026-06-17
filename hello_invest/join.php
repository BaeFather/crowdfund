<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="format-detection" content="telephone=no, address=no, email=no" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>
<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-jp.css' rel='stylesheet' type='text/css'>
<link href="css/GFA.css" rel="stylesheet" >

<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript">
var idpw_type     = "<?=$idpw_type?>";
var id_min_length = <?=$ID_LIMIT[$idpw_type]['min_length']?>;
var id_max_length = 40;
var pw_min_length = <?=$PW_LIMIT[$idpw_type]['min_length']?>;
var pw_max_length = <?=$PW_LIMIT[$idpw_type]['max_length']?>;
var pw_describe   = "<?=$PW_LIMIT[$idpw_type]['describe']?>";
var toomics_curl  = "<?=$callback_url?>";
</script>
<script type="text/javascript" src="join.js?v=n"></script>

</head>


<div id="join">
	<div class="logo"><img src=img/logo.png alt="헬로펀딩"></div>
		<h2><span>행복금융 파트너, 헬로펀딩</span><br>정직과 신뢰로 행복한 금융을 만듭니다.</h2>
				<div id="quick_join" class="quick_join">
					<form name="frmJoin" id="frmJoin">
						<input type="hidden" id="ordertime"    name="ordertime"    value="<?=time()?>" />
						<input type="hidden" id="member_type"  name="member_type"  value="1" />
						<input type="hidden" id="is_sign"      name="is_sign"      value="N" />
						<input type="hidden" id="mb_dupinfo"   name="mb_dupinfo"   value="" />
						<input type="hidden" id="mb_ci"        name="mb_ci"        value="" />
						<input type="hidden" id="mb_reqnum"    name="mb_reqnum"    value="" />
						<input type="hidden" id="gender"       name="gender"       value="" />
						<input type="hidden" id="app_id"       name="app_id"       value="<?=$app_id?>" />
						<input type="hidden" id="response_idx" name="response_idx" value="<?=$response_idx?>" />

						<div class="join_form">
						<div class="label_form">
							<p class="label">아이디</p>
							<input type="text" name="mb_id" id="mb_id" class="protect-browser id" placeholder="아이디를 입력해주세요.">
							<span class="cheak" onClick="id_check();"><button type="button" onClick="id_check();" class="cheak_btn">중복체크</button></span>
							<input type="hidden" name="mb_email" id="mb_email">
						</div>


						<div class="label_form">
							<span class="label">비밀번호</span>
							<input type="password" name="mb_password" id="mb_password" class="protect-browser pass" placeholder="비밀번호를 입력해주세요." onBlur="passwd_check();">
							<div id="mb_password_error" style="display:none;margin-left:10px;padding:0;width:100%;font-size:11px;"></div>
						</div>


						<div class="label_form">
							<span class="label">비밀번호확인</span>
							<input type="password" name="cfm_password" id="cfm_password" class="protect-browser" placeholder="비밀번호를 한번 더 입력해주세요." onBlur="passwd_check();">
							<div id="cfm_password_error" style="display:none;margin-left:10px;padding:0;width:100%;font-size:11px;"></div>
						</div>


						<div class="label_form">
							<span class="label">성명</span>
							<input type="text" name="mb_name" id="mb_name" placeholder="홍길동" class="protect-browser name">
							<select id="foreigner" name="foreigner" class="foreigner">
								<option value="">내국인</option>
								<option value="1">외국인</option>
							</select>
						</div>



						<div class="label_form">
							<span class="label">생년월일</span>
							<input type="birth" name="mb_birth" id="mb_birth" class="protect-browser birth" onKeyUp="onlyDigit(this);" placeholder="ex.19800101">
							<span class="sex_btn1" id="gender_btn1" onClick="selectGender('m');">남</span>
							<span class="sex_btn2" id="gender_btn2" onClick="selectGender('w');">여</span>


						</div>
						<div class="label_form">
							<span class="label">휴대폰 번호</span>
							<select id="hp_comp" name="hp_comp" onChange="provisionBlind();">
								<option value="">통신사</option>
								<option value="1">SKT</option>
								<option value="2">KT</option>
								<option value="3">LGU+</option>
								<option value="5">알뜰폰(SKT)</option>
								<option value="6">알뜰폰(KT)</option>
								<option value="7">알뜰폰(LGU+)</option>
							</select>
							<input style="margin-right:1%;" type="phonenumb" id="mb_hp1" class="protect-browser phone" name="mb_hp1" maxlength="3" onKeyUp="onlyDigit(this); if(this.value.length==3){document.frmJoin.mb_hp2.focus();}">
							<input style="margin-right:1%;" type="phonenumb" id="mb_hp2" class="protect-browser phone" name="mb_hp2" maxlength="4" onKeyUp="onlyDigit(this); if(this.value.length==4){document.frmJoin.mb_hp3.focus();}">
							<input type="phonenumb" id="mb_hp3" class="protect-browser phone" name="mb_hp3" maxlength="4" onKeyUp="onlyDigit(this);">
							<button type="button" class="certify_btn" id="auth_request" onClick="NiceSockStep1('<?=$mode?>');">인증번호 받기</button>
							<input type="text" id="auth_num" class="protect-browser num" name="auth_num" placeholder="인증번호 6자리">
							<button type="button" class="repeat_btn" id="auth_submit" onClick="NiceSockStep2();">확 인</button>
							<div id="member_sign" class="error-block"></div>
						</div>
					</div>

					<div class="agree">
						<p>서비스 정책동의</p>
						<p>
							<span class="agree_list"><label><input type="checkbox" id="agree_provision" name="agree_provision" checked /><span> [필수] <a href="/company/provision.php" target="_self" style="text-decoration: underline;">서비스이용약관</a>에 동의합니다.</span></label></span><br>
							<span class="agree_list"><label><input type="checkbox" id="agree_privacy" name="agree_privacy" checked /> <span>[필수] <a href="/company/privacy.php" target="_self" style="text-decoration: underline;">개인정보처리방침</a>에 동의합니다.</span></label></span><br>
							<span class="agree_list"><label><input type="checkbox" id="agree_marketing" name="agree_marketing" checked /> <span>[선택] <a href="/company/marketing_agreement.php" target="_self" style="text-decoration: underline;">마케팅 정보 수집 및 활용</a>에 동의합니다.</span></label></span>

						</p>

					</div>
					</form>

					<form name="frmAuthRes">
						<input type="hidden" name="auth_mb_name" />
						<input type="hidden" name="auth_foreigner" />
						<input type="hidden" name="auth_mb_birth" />
						<input type="hidden" name="auth_gender" />
						<input type="hidden" name="auth_hp_comp" />
						<input type="hidden" name="auth_mb_hp" />
					</form>

				</div>
			</div>

<button type="button" class="njoin_btn" onClick="go_submit();">회원가입하기</button>
