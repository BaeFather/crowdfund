<?
###############################################################################
## [신규회원] 법인 회원 가입  ->  step 1 ~ 마지막 완료 단계까지
###############################################################################

add_javascript('<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>', 0);
?>

<form name="frmJoin" id="frmJoin" method="post" enctype="multipart/form-data">
	<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="url"         name="url"         value="<?=G5_URL?>" />
	<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="ordertime"   name="ordertime"   value="<?=time()?>">
	<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="member_type" name="member_type" value="2">
	<input type="hidden" id="pid"		  name="pid"		 value="<?php ECHO $pid;?>" />
	
	<fieldset  class="join-wrap">
		
		<!-- 안내사항 start -->
		<section> 
			<table class="info-table">
				<colgroup>
					<col width="120px">
					<col width="330px">
				</colgroup>
				<tr>
					<th colspan="3">법인회원 증빙서류(필수)</th>
				</tr>
				<tr style="height: 80px;">
					<td style="text-align: center;">일반법인</td>
					<td colspan="2">사업자 등록증, 법인등기부등본, 법인 통장사본,<br />대표자 신분증, <u style="color: #1e7fe8;">실소유자 정보 양식</u> 및 증빙서류,<br />주주명부, 법인인감증명서 등</td>
				</tr>
				<tr style="height: 40px;">
					<td style="text-align: center;">비영리법인</td>
					<td colspan="2">일반법인 증빙서류 + 정관 제출</td>
				</tr>
			</table>
			<!--a href="../../member/file/실제_소유자_정보.docx" class="info-down-file"><img src="../../theme/2018/img/member/down.png" alt="양식 다운로드" />실제 소유자 정보 양식 다운받기</a-->
			<div class="info-down-btn">
				<a href="" class="info-down-file"><img src="../../theme/2018/img/member/down.png" alt="실소유자 양식 다운로드" />실소유자 양식 다운</a>
				<a href="" class="info-down-file"><img src="../../theme/2018/img/member/down.png" alt="주주명부 양식 다운로드" />주주명부 양식 다운</a>
			</div>
			<ul class="notice">
				<li>- 3개월 이내 발급 서류를 제출해주세요.</li>
				<li>- 대부업 법인은 대부업등록증 추가해주세요.</li>
				<li>- 법인회원 심사 승인을 위하여 증빙서류 제출 시 심사 가능합니다.</li>
			</ul>
			<button type="button" class="next-btn c-join-info-btn">회원가입 양식 작성하기</button>
			<br />
		</section>  
		<!-- 안내사항 end -->
		
		<!-- step1 start -->
		<section class="c-join-step1" style="display: none;"> 
			<div class="page-num">
				<span class="active">1</span>/5
			</div>

			<div class="field-wrap">
				<label for="mb_id">아이디</label>
				<span class="help-block">(첫글자는 반드시 영문으로 작성)</span>
				<div class="member_id">
					<div class="input-wrap">
						<input type="text" name="mb_id" id="mb_id" onkeyup="stringCheck('mb_id');" required="required" autocomplete="off" placeholder="아이디를 입력해주세요" maxlength="15">
					</div>
					<button type="button" id="confirm_id" class="btn_default">중복체크</button>
				</div>
				<span id="mb_id_error" class="error-block"></span>
				<!--<span class="help-block">영문 또는 영문/숫자 조합, 6~15자리 등록 가능합니다.</span>-->
			</div>

			<div class="field-wrap">
				<label for="mb_password">비밀번호</label>
				<input type="password" name="mb_password" id="mb_password" onkeyup="stringCheck('mb_password','password');" required="required" autocomplete="off" placeholder="비밀번호를 입력해주세요" onblur="passwd_check();" maxlength="15">
				<!--<span class="help-block"><?=$PW_LIMIT[$idpw_type]['describe']?></span>-->
				<span id="mb_password_error" class="error-block"></span>
			</div>

			<div class="field-wrap">
				<label for="cfm_password">비밀번호 확인</label>
				<input type="password" id="cfm_password" onkeyup="stringCheck('cfm_password','password');" autocomplete="off" placeholder="비밀번호를 한 번 더 입력해주세요" onKeyUp="if(this.value.length >= $('#mb_password').val().length){ passwd_check(); }" onblur="passwd_check();" maxlength="15">
				<span id="cfm_password_error" class="error-block"></span>
			</div>

			<div class="field-wrap">
				<label for="mb_co_name">법인명</label>
				<input type="text" id="mb_co_name" name="mb_co_name" onkeyup="stringCheck('mb_co_name','name');" autocomplete="off" placeholder="법인명을 입력해주세요">
				<span id="mb_co_name_error" class="error-block"></span>
			</div>
			
			<div class="field-wrap">
				<label for="mb_email">이메일</label>
				<input type="text" id="mb_email" name="mb_email" onkeyup="stringCheck('mb_email','email');" autocomplete="off" placeholder="이메일을 입력해주세요">
				<span id="mb_email_error" class="error-block"></span>
			</div>

			<button type="button" class="next-btn">다음</button>
			<div class="register_description">
				<label>이미 헬로펀딩 회원이신가요?</label>
				<span class="pull-right"><a href="<?=G5_BBS_URL?>/login.php">로그인하기</a></span>
			</div>
		</section> 
		<!-- step1 end -->

		<!-- step2 start -->
		<section class="c-join-step2" style="display: none;"> 
			<div class="page-num">
				<span class="active">2</span>/5
			</div>

			<div class="field-wrap">
				<label for="mb_co_reg_num1">사업자등록번호</label>
				<input type="checkbox" name="is_creditor" id="is_creditor" value="Y"><label for="is_creditor" class="is_creditor"><span>대부업법인</span></label>
				<div class="mb_co_reg_num_wrap">
					<input type="text" class="mb_co_reg_num" id="mb_co_reg_num1" name="mb_co_reg_num1" autocomplete="off" maxlength="3" onkeyup="onlyDigit(this);fn_mb_co_reg_check();if($('#mb_co_reg_num1').val().length=='3'){$('#mb_co_reg_num2').focus();}">
					<input type="text" class="mb_co_reg_num" id="mb_co_reg_num2" name="mb_co_reg_num2" autocomplete="off" maxlength="2" onkeyup="onlyDigit(this);fn_mb_co_reg_check();if($('#mb_co_reg_num2').val().length=='2'){$('#mb_co_reg_num3').focus();}">
					<input type="text" class="mb_co_reg_num" id="mb_co_reg_num3" name="mb_co_reg_num3" autocomplete="off" maxlength="5" onkeyup="onlyDigit(this);fn_mb_co_reg_check();">
				</div>
				<span class="error-block"></span>
			</div>
			
			<div class="field-wrap"> 
				<label for="mb_co_enroll_num">법인등록번호</label>
				<input type="checkbox" name="is_non_profit" id="is_non_profit" value="Y" onclick="nonProfit();"><label for="is_non_profit" class="is_non_profit"><span>비영리법인</span></label>
				<div class="mb_co_enroll_num_wrap">
					<input type="text" id="mb_co_enroll_num1" name="mb_co_enroll_num1" autocomplete="off" class="mb_co_enroll_num">
					<input type="text" id="mb_co_enroll_num2" name="mb_co_enroll_num2" autocomplete="off" class="mb_co_enroll_num">
				</div>
				<span class="error-block"></span>
			</div>

			<div class="field-wrap">
				<div class="kind-c">
					<label for="">업종</label>
					<input type="text" id="kind_c" name="kind_c">
					<span class="error-block"></span>
				</div>
				<!-- 비영리법인 체크 시 노출 -->
				<div class="est-c" style="display: none;">
					<label for="">설립목적</label>
					<input type="text" id="est_c" name="est_c">
					<span class="error-block"></span>
				</div>
			</div>

			<div class="field-wrap">
				<label for="">사업장주소</label>
				<div class="add-zip-wrap">
					<input type="text" name="postcode_c" id="postcode_c" class="zip-num" placeholder="우편번호">
					<input type="button" onclick="execDaumPostcode(1)" class="add-search" value="주소검색">
				</div>
				<input type="text" name="addr_c" id="addr_c" placeholder="주소">
				<input type="text" name="detailAddr_c" id="detailAddr_c" placeholder="상세주소">
				<span class="error-block"></span>
			</div>
			
			<div class="field-wrap">
				<label for="bankName">환급 계좌 은행</label>
				<select name="strBankCode" id="strBankCode" class="sel-code">
					<option value="">은행을 선택하세요</option>
					<?
					$BANK_KEYS = array_keys($BANK);
					for($i=0; $i<count($BANK); $i++) {
						$selected = ($BANK_KEYS[$i]==sprintf("%03d", $member["bank_code"])) ? 'selected' : '';
						echo "<option value='".$BANK_KEYS[$i]."' $selected>".$BANK[$BANK_KEYS[$i]]."</option>\n";
					}
					?>
				</select>
				<span class="error-block"></span>
			</div>

			<div class="field-wrap">
				<label for="">환급 계좌번호</label>
				<input type="text" name="refund_acc" id="refund_acc" placeholder="'-'를 제외한 계좌번호 입력">
				<span class="error-block"></span>
			</div>
			
			<ul class="btn-wrap">
				<li><button type="button" class="prev-btn">이전</button></li>
				<li><button type="button" class="next-btn">다음</button></li>
			</ul>
		</section> 
		<!-- step2 end -->
		
		<!-- step3 start -->
		<section class="c-join-step3" style="display: none;"> 
			<div class="page-num">
				<span class="active">3</span>/5
			</div>

			<div class="field-wrap">
				<label for="cmb_name">대표자 성명</label>
				<input type="text" id="cmb_name" name="cmb_name" placeholder="대표자 실명 입력">
				<span class="error-block"></span>
			</div>

			<div class="field-wrap">
				<label for="mb_eng_name">대표자 영문명</label>
				<span class="help-block">(영문 입력 필수)</span>
				<div class="eng_name_wrap">
					<input type="text" name="eng_name1" id="eng_name1" class="eng_name" placeholder="이름">
					<input type="text" name="eng_name2" id="eng_name2" class="eng_name" placeholder="성">
				</div>
				<span class="error-block"></span>
			</div>
			
			<div class="field-wrap">
				<label for="mb_hp_c">대표자 연락처</label><br>
				<input type="text" class="ph_num_c" id="mb_hp_c" name="mb_hp_c" autocomplete="off" placeholder="'-'를 제외한 연락처 입력">
				<span class="error-block"></span>
			</div>  

			<div class="field-wrap">
				<label for="">대표자 주소</label>
				<div class="add-zip-wrap">
					<input type="text" name="postcode_cmb" id="postcode_cmb" class="zip-num" placeholder="우편번호">
					<input type="button" onclick="execDaumPostcode(2)" class="add-search" value="주소검색">
				</div>
				<input type="text" name="addr_cmb" id="addr_cmb" placeholder="주소">
				<input type="text" name="detailAddr_cmb" id="detailAddr_cmb" placeholder="상세주소">
				<span class="error-block"></span>
			</div>
			
			<div class="field-wrap">
				<label for="birth_c">대표자 생년월일</label><br />
				<div class="c-birth-wrap">
					<select name="birth_year" id="birth_year" class="birth-c" onchange="appendYear();">
						<option value="">년도 선택</option>
					</select>
					<select name="birth_month" class="birth-c" id="birth_month">
						<option value="">월 선택</option>
					</select>
					<select name="birth_day" class="birth-c" id="birth_day">
						<option value="">일 선택</option>
					</select>
				</div>
				<span class="error-block"></span>
			</div>

			<div class="field-wrap">
				<label for="mb_name">담당자 성명</label>
				<input type="checkbox" name="is_info_same" id="is_info_same" value="Y" onclick="infoSame();"><label for="is_info_same" class="is_info_same"><span>대표정보동일</span></label>
				<input type="text" id="mb_name" name="mb_name" onkeyup="stringCheck('mb_name','00email');">
				<span class="error-block"></span>   
			</div>                        

			<div class="field-wrap">
				<label for="mb_hp">담당자 연락처</label>
				<input type="text" class="ph_num_c2" id="mb_hp" name="mb_hp" autocomplete="off" placeholder="'-'를 제외한 연락처 입력">
				<span class="error-block"></span>
			</div>

			<ul class="btn-wrap">
				<li><button type="button" class="prev-btn">이전</button></li>
				<li><button type="button" class="next-btn">다음</button></li>
			</ul>
		</section> 
		<!-- step3 end -->

		<!-- step4 start -->
		<section class="c-join-step4" style="display: none;"> 
			<div class="page-num">
				<span class="active">4</span>/5
			</div>
			
			<div class="field-wrap">
				<label for="">증빙서류 첨부</label>
				<div class="filebox_c">
					<input type="file" id="file1">
					<input class="upload-name" placeholder="선택된 파일 없음" readonly>
					<label for="file1">파일선택</label>
				</div>
				<div class="filebox_c">
					<input type="file" id="file2">
					<input class="upload-name" placeholder="선택된 파일 없음" readonly>
					<label for="file2">파일선택</label>
				</div>
				<div class="filebox_c">
					<input type="file" id="file3">
					<input class="upload-name" placeholder="선택된 파일 없음" readonly>
					<label for="file3">파일선택</label>
				</div>
			</div>

			<ul class="notice">
				<li style="color: red;">* PDF, JPG, PNG 파일형식만 등록 가능합니다.</li>
				<li>* 입력한 정보와 제출한 증빙서류 검토 후 가입이 완료됩니다.(영업일 1~3일)</li>
				<li>* 대부업법인은 대부업등록증 첨부 필수입니다.</li>
				<li>* 모든 서류는 최근 3개월 이내의 것이여야 합니다.</li>
			</ul>

	<!--
	<?
	$recommend_event = sql_fetch("SELECT idx, sdate, edate FROM recommend_event_config WHERE is_real='1'
	AND left(sdate,7)='".DATE("Y-m")."' ORDER BY idx DESC LIMIT 1");

	if(date('Y-m-d') >= $recommend_event['sdate'] && date('Y-m-d') <= $recommend_event['edate']) {
	?>
			<!-- 추천인 ID 입력 
			<div id="nameDiv" style="margin:23px 0;">
				<?php IF($pid) { ?>
				<label for="rec_mb_id">추천인 코드</label>
				<input type="text" id="rec_mb_id" name="rec_mb_id" onKeyUp="stringCheck('rec_mb_id','email');" autocomplete="off" maxlength="30" onKeyUp="$('#rec_flag').val('');" <?php IF($pid) { ECHO " value='".fn_general_select($pid,"txt",$strMemberClass->fn_partner(),"","","","")."' readonly"; } ?> />
				<span id="rec_mb_id_check_txt" class="help-block">* 추천 가능한 추천인코드 입니다.</span>
				<?php } ELSE { ?>
				<label for="rec_mb_id">추천인 ID</label>
				<input type="text" id="rec_mb_id" name="rec_mb_id" onKeyUp="stringCheck('rec_mb_id','email');" autocomplete="off" maxlength="30" onKeyUp="$('#rec_flag').val('');recommend_id_check();" <?php IF($pid) { ECHO " value='".fn_general_select($pid,"txt",$strMemberClass->fn_partner(),"","","","")."' readonly"; } ?> />
				<span id="rec_mb_id_check_txt" class="help-block">* 추천인 ID가 있는 경우 입력해 주세요.</span>
				<?php } ?>

				<input type="hidden" id="rec_flag" />
			</div>

			<div id="nameDivTxt1" style="display:none;">
			가입이력이 있어 추천인을 이용할 수 없습니다.
			</div>

			<div id="nameDivTxt2" style="display:none;">
			탈퇴이력이 있어 추천인을 이용할 수 없습니다.
			</div>
			
			<script>
			// 추천인 입력 처리
			function recommend_id_check(){
				$('#rec_flag').val('');  //추천인확인 플래그 초기화
				var rec_mb_id = $('#rec_mb_id').val();
				if(rec_mb_id) {
					if(rec_mb_id.length >= 4) {
						$.ajax({
							url : "/member/ajax_rec_id_check.php",
							type: "POST",
							dataType: "JSON",
							data: {rec_mb_id : rec_mb_id},
							success: function(data) {
								if(data.result=='') {
									$('#rec_mb_id_check_txt').html("<span style='color:red'>* 시스템 오류 입니다. 관리자에게 문의하십시요.</span>");
								}
								else if(data.result=="1") {
									$('#rec_mb_id_check_txt').html("<span style='color:blue'>" + data.message + "</span>"); $('#rec_flag').val('Y');
								}
								else {
									$('#rec_mb_id_check_txt').html("<span style='color:red'>" + data.message + "</span>"); $('#rec_mb_id').focus();
								}
							},
							error: function () { $('#rec_mb_id_check_txt').html("<span style='color:red'>* 네트워크 오류 입니다. 잠시 후 다시 시도하여 주십시요.</span>"); }
						});
					}
				}
				else { $('#rec_mb_id_check_txt').html("* 추천인 ID가 있는 경우 입력해 주세요."); }
			}
			</script>
	<?
	}
	?>-->
			
			<div class="agree-box">
				<ul class="agree-all">
					<li>
						<input type="checkbox" id="allChkAgree" name="allChkAgree" checked="checked" /><label for="allChkAgree"><span>서비스 정책 전체동의</span></label>
					</li>
				</ul>
				<ul class="agree">
					<li>
						<input type="checkbox" id="agree_provision" name="agree_provision" class="agree-list" checked="checked" /><label for="agree_provision"><span>[필수] 서비스 이용약관 동의</span></label>
						<a href="<?=G5_BBS_URL?>/content.php?co_id=provision">&gt;</a>
					</li>
					<li>
						<input type="checkbox" id="agree_provision2" name="agree_provision2" class="agree-list" checked="checked" /><label for="agree_provision2"><span>[필수] 온라인연계투자약관에 동의합니다.</span></label>
						<a href="<?=G5_BBS_URL?>/content.php?co_id=provision2" ">&gt;</a>
					</li>
					<li>
						<input type="checkbox" id="agree_usecredit" name="agree_usecredit" class="agree-list" checked="checked" /><label for="agree_usecredit"><span>[필수] 개인(신용)정보 수집 및 이용에 동의합니다.</span></label>
						<a href="<?=G5_BBS_URL?>/content.php?co_id=usecredit">&gt;</a>
					</li>
					<li>
						<input type="checkbox" id="agree_3rdparty" name="agree_3rdparty" class="agree-list" checked="checked" /><label for="agree_3rdparty"><span>[필수] 개인정보 제3자 제공에 동의합니다.</span></label>
						<a href="<?=G5_BBS_URL?>/content.php?co_id=3rdparty_agreement">&gt;</a>
					</li>
					<li>
						<input type="checkbox" id="agree_identify" name="agree_identify" class="agree-list" checked="checked" /><label for="agree_identify"><span>[필수] 고유식별정보 처리에 동의합니다.</span></label>
						<a href="<?=G5_BBS_URL?>/content.php?co_id=identify">&gt;</a>
					</li>
					<li>
						<input type="checkbox" id="agree_marketing" name="agree_marketing" class="agree-list" checked="checked" /><label for="agree_marketing"><span>[선택] 마케팅 정보 수집 및 활용동의</span></label>
						<a href="<?=G5_BBS_URL?>/content.php?co_id=marketing_agreement">&gt;</a>
					</li>
			<? if(G5_TIME_YMD >= $CONF['online_invest_policy_sdate']) { ?>
					<li>
						<input type="checkbox" id="invested_mailling" name="invested_mailling" class="agree-list" checked="checked" /><label for="invested_mailling"><span>[선택] 투자설명서 발급 동의</span></label>
					</li>
			<? } ?>

				</ul>
			</div>
			
			<ul class="btn-wrap">
				<li><button type="button" class="prev-btn">이전</button></li>
				<li><button type="button" class="join-btn" id="submit_button">회원가입하기</button></li>
			</ul>

		</section> <!-- step4 end -->
	</fieldset>
</form>

<!-- 법인회원 가입완료(신규) -->
<div class="cmb-new-ok" style="display: none;">
	<div class="page-num">
		<span class="active">5</span>/5
	</div>
	<div class="info-txt">
		<h3>법인 승인 및 가상 계좌 발급 대기</h3>
		<p class="txt">
			제출하신 서류를 관리자가 검토 후<br />
			법인회원 승인 및 가상계좌가 발급되오니 조금만 기다려주세요.<br />
			<br />
			영업일 기준 1~3일 정도 소요됩니다.
		</p>
		<img src="../../theme/2018/img/member/step02_5_1.jpg" alt="step" class="img-process"/>
		<button type="button" class="btn-main" onclick="location.href='<?=G5_URL?>'">메인으로 가기</button>
	</div>
</div>


<script type="text/javascript">

// (임시) 서류 제출 완료 화면 띄우기
$('#submit_button').on('click', function() {
	$('.cmb-new-ok').show();
	$(this).parent().parent().parent('section').hide();
});


// 비영리법인 선택 시
function nonProfit() {
	var nonProfitChk = $("#is_non_profit");

	if (nonProfitChk.prop("checked")) {
		$('.kind-c').css('display','none');
		$('.est-c').css('display','block');
	} else {
		$('.kind-c').css('display','block');
		$('.est-c').css('display','none');
	}
}


// input file 값 변경
$('.filebox_c > input[type=file]').on('click', function() {

	var fileIdVal = $(this).attr('id');
	var fileTarget = $('.filebox_c > input#'+fileIdVal); 

	fileTarget.on('change', function(){  // 값이 변경되면
		var cur = $(this).val().split('/').pop().split('\\').pop(); 
		fileTarget.next('.upload-name').val(cur);
	}); 
}); 

</script>

<script type="text/javascript">

// 대표정보동일 chk box 클릭시 담당자 & 대표자 정보 동일하게
function infoSame() {
	var infoChk = $("#is_info_same");

	if (infoChk.prop("checked")) {
		var cmbName = $("#cmb_name").val();
		var mbHpC = $("#mb_hp_c").val();

		$("#mb_name").attr("value", cmbName);
		$("#mb_hp").attr("value", mbHpC);
	}
}

// 동적으로 생년월일 select option
$(document).ready(function () {
	setBirthDate();
});

function setBirthDate() {
	var date = new Date();
	var year = date.getFullYear();
	var month;
	var day;

	for (var y = (year - 50); y <= year; y++) {
		$("#birth_year").append("<option value='" + y + "'>" + y + " 년" + "</option>");
	}

	for (var i = 1; i <= 12; i++) {
		$("#birth_month").append("<option value='" + i + "'>" + i + " 월" + "</option>");
	}

	for (var i = 1; i <= 31; i++) {
		$("#birth_day").append("<option value='" + i + "'>" + i + " 일" + "</option>");
	}

}

// 다음 주소찾기 api
function execDaumPostcode(type) {
	
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

			// 각 주소의 노출 규칙에 따라 주소를 조합한다.
			// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
			var addr = ''; // 주소 변수
			var extraAddr = ''; // 참고항목 변수

			//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
			if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
				addr = data.roadAddress;
			} else { // 사용자가 지번 주소를 선택했을 경우(J)
				addr = data.jibunAddress;
			}

			// 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
			if(data.userSelectedType === 'R') {
				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
				// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraAddr += data.bname;
				}
				// 건물명이 있고, 공동주택일 경우 추가한다.
				if(data.buildingName !== '' && data.apartment === 'Y'){
					extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraAddr !== ''){
					addr += ' (' + extraAddr + ')';
				}
			} 
			
			if(type == '1') {
				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById('postcode_c').value = data.zonecode;
				document.getElementById('addr_c').value = addr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById('detailAddr_c').focus();
			} else if(type == '2') {
				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById('postcode_cmb').value = data.zonecode;
				document.getElementById('addr_cmb').value = addr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById('detailAddr_cmb').focus();
			}
		}
	}).open();
}

// 전체동의 체크항목
$("#allChkAgree").click(function(){
	if($("#allChkAgree").prop("checked")) {
		$(".agree-list").prop("checked", true);
	} else {
		$(".agree-list").prop("checked", false);
	}
});

$(".agree-list").click(function() {
	if($(".agree-list:checked").length == $(".agree-list").length) {
		$("#allChkAgree").prop("checked", true);
	} else {
		$("#allChkAgree").prop("checked", false);
	} 
});

// 아이디 형식 체크 function
id_string_check = function(str){ // 숫자와 알파벳만 입력 허용
	var safe_char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // 입력을 허용하는 글자들
	var len	   = str.length;
	var result	= true;
	var char	  = '';
	for(i=0;i<len;i++) {
		char = str.charAt(i);
		if(i == 0) {
			var re2 = /[0-9]/i; // 숫자
			if(re2.test(char)) {
				result = false; break;
			}
		}
		if(safe_char.indexOf(char) == -1) {
			result = false; break;
		}
	}

	if(len < <?=$ID_LIMIT[$idpw_type]['min_length']?> || len > <?=$ID_LIMIT[$idpw_type]['max_length']?>){ result = false; }
	return result;
};

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
};

var auth_mb_id = '';

//아이디 체크
$('#confirm_id').click(function() {
	var f = document.frmJoin;
	var mb_id = trim(f.mb_id.value);

	$('#mb_id_error').empty();

	if(mb_id=='') { alert('아이디를 입력하십시오.'); f.mb_id.value = ''; f.mb_id.focus(); }
	else {
		if (!id_string_check(mb_id)) { $('#mb_id_error').html('<span style="color:red">올바른 아이디 형식이 아닙니다.</span>'); }
		else {
			$.ajax({
				url : "/member/confirm_id.php",
				type: "POST",
				data : {'prm1':mb_id},
				success: function(data) {
					$('#ajax_return_txt').val(data);
					if(data=='o')	  { $('#mb_id_error').html('<span style="color:green">사용 가능한 아이디 입니다.</span>'); auth_mb_id = mb_id; }
					else if(data=='x') { alert('사용 하실 수 없는 아이디 입니다.'); $('#mb_id_error').html('<span style="color:red">사용 하실 수 없는 아이디 입니다.</span>'); }
					else			   { alert('시스템 오류 입니다. 고객센터로 문의 하십시오.'); }
				},
				error: function () {
					alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시오.');
				}
			})
		}
	}
});

// 암호 체크
passwd_check = function() {
	var str1 = $('#mb_password').val();
	var str2 = $('#cfm_password').val();
	if(str1.length > 1) {
		if(pass_string_check(str1)==true) {
			$('#mb_password_error').html('<span style="color:green">형식에 적합한 비밀번호 입니다.</span>');
		}
		else if(pass_string_check(str1)==false) {
			$('#mb_password_error').html('<span style="color:red"><?=$PW_LIMIT[$idpw_type]['describe']?></span>');
		}
		else {
			$('#mb_password_error').empty();
		}
	}
	else {
		$('#mb_password_error').html('<span style="color:red">비밀번호를 입력하십시오.</span>');
	}
	if(str2.length > 1) {
		$('#cfm_password_error').html('');
		if(str1!='' && str2!='') {
			if(str1==str2) {
				$('#cfm_password_error').html('<span style="color:green">비밀번호가 일치합니다.</span>');
			}
			else {
				$('#cfm_password_error').html('<span style="color:red">비밀번호가 일치하지 않습니다.</span>');
			}
		}
	}
};

// 다음 버튼 클릭 시 
$('.next-btn').on('click', function() {
	if ($(this).parents('section').css('display','block')) {
		$(this).parents('section').next().css('display','block');
		$(this).parents('section').css('display','none');
	}
});

// 이전 버튼 클릭 시 
$('.prev-btn').on('click', function() {
	if ($(this).parents('section').css('display','block')) {
		$(this).parents('section').prev().css('display','block');
		$(this).parents('section').css('display','none');
	}
});

// 대부업법인 클릭 시
$('#is_creditor').click(function() {
	if($("input:checkbox[id='is_creditor']").is(':checked')==true) { 
		$('.loan_co_license_zone').css('display','block');
	}
	else {
		$('.loan_co_license_zone').css('display','none');
	}
});

function fn_mb_co_reg_check()
{
	var reg_num1 = $("#mb_co_reg_num1").val();
	var reg_num2 = $("#mb_co_reg_num2").val();
	var reg_num3 = $("#mb_co_reg_num3").val();

	var reg_num = reg_num1+''+reg_num2+''+reg_num3;

	if(reg_num1.length == 3 && reg_num2.length==2 && reg_num3.length==5)
	{
		// 중복 및 탈퇴 체크
		$.ajax({
			type : 'POST',
			url : "./join_info_form_c_check.php",
			data : "reg_num="+reg_num,
			dataType: 'json',
			success : function(data){

				if(data.retcode == "OK")
				{
					if(parseInt(data.ret1) > 0 || parseInt(data.ret2) > 0)
				  {
						if(parseInt(data.ret1) > 0)	//가입이력체크
						{
							$("#nameDiv").css("display","none");
							$("#nameDivTxt1").css("display","block");
							$("#nameDivTxt2").css("display","none");
						}
						if(parseInt(data.ret2) > 0)	//탈퇴이력체크
						{
							$("#nameDiv").css("display","none");
							$("#nameDivTxt1").css("display","none");
							$("#nameDivTxt2").css("display","block");
						}
					} else {
						$("#nameDiv").css("display","block");
						$("#nameDivTxt1").css("display","none");
						$("#nameDivTxt2").css("display","none");
					}

				} else if(data.retcode == "X") {
					var stralert = decodeURIComponent(data.retalert);
						alert(stralert.replace("+"," "));
				}
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오.");
				console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
				console.log(errorThrown);
				return false;
			}
		});

	}
}

$('#submit_button').click(function() {
	var f = document.frmJoin;
	var mb_id			       = trim(f.mb_id.value);
	var mb_password	     = trim(f.mb_password.value);
	var cfm_password	   = document.getElementById('cfm_password').value;
	var mb_co_name	     = trim(f.mb_co_name.value);
	var mb_co_reg_num1   = trim(f.mb_co_reg_num1.value);
	var mb_co_reg_num2   = trim(f.mb_co_reg_num2.value);
	var mb_co_reg_num3   = trim(f.mb_co_reg_num3.value);
	var is_creditor	     = f.is_creditor.value
	var mb_name          = trim(f.mb_name.value);
	var mb_hp1           = trim(f.mb_hp1.value);
	var mb_hp2           = trim(f.mb_hp2.value);
	var mb_hp3           = trim(f.mb_hp3.value);
	var mb_hp            = mb_hp1 + mb_hp2 + mb_hp3;
	var email            = trim(f.mb_email.value);
	var business_license = f.business_license.value;
	var bankbook         = f.bankbook.value;
	var loan_co_license  = f.loan_co_license.value;
	var agree_provision  = $("input:checkbox[id='agree_provision']").is(':checked') ? 1 : '';
	var agree_privacy    = $("input:checkbox[id='agree_privacy']").is(':checked') ? 1 : '';

	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if(trim(mb_id)=='')            { alert('아이디를 입력해주세요.'); f.mb_id.focus(); }
	else if(auth_mb_id == '')      { alert('아이디 중복체크를 실행해주세요.'); f.mb_id.focus(); }
	else if(mb_id!=auth_mb_id)     { alert('아이디 중복체크를 통과한 아이디와 최종 입력된 아이디가 일치하지 않습니다.\n\nID 체크를 다시 해주세요.'); $('#confirm_id').fucus(); }
	else if(trim(mb_password)=='') { alert('비밀번호를 입력해주세요.'); f.mb_password.focus(); }
	else if(pass_string_check(mb_password)==false) { alert('비밀번호는 공백 없이 영문, 숫자, 특수문자를\n\n혼합하여 8자리 이상 15자리 이하로\n\n입력 해주세요.');  f.mb_password.focus(); }
	else if(trim(cfm_password)==''){ alert('비밀번호를 다시 한 번 입력해주세요.');  $('#cfm_password').focus(); }
	else if(mb_password != cfm_password) { alert('비밀번호가 일치 하지 않습니다.'); $('#cfm_password').focus(); }
	else if(mb_co_name=='')        { alert('상호명을 입력해주세요.'); f.mb_co_name.focus(); }
	else if(mb_co_reg_num1=='' || mb_co_reg_num1.length < 3) { alert('사업자번호를 입력해주세요.'); f.mb_co_reg_num1.focus(); }
	else if(mb_co_reg_num2=='' || mb_co_reg_num2.length < 2) { alert('사업자번호를 입력해주세요.'); f.mb_co_reg_num2.focus(); }
	else if(mb_co_reg_num3=='' || mb_co_reg_num3.length < 5) { alert('사업자번호를 입력해주세요.'); f.mb_co_reg_num3.focus(); }
	else if(trim(mb_name)=='')     { alert('담당자명을 입력해주세요.'); f.mb_name.focus(); }
	else if(trim(mb_hp1)=='')      { alert('휴대폰번호를 입력해주세요.'); f.mb_hp1.focus(); }
	else if(trim(mb_hp2)=='')      { alert('휴대폰번호를 입력해주세요.'); f.mb_hp2.focus(); }
	else if(trim(mb_hp3)=='')      { alert('휴대폰번호를 입력해주세요.'); f.mb_hp3.focus(); }
	else if(trim(email)=='')       { alert("이메일 주소를 입력해주세요."); f.mb_email.focus(); }
	else if(!re.test(email))       { alert("유효하지 않은 이메일 양식입니다."); f.mb_email.focus(); }
	else if(business_license=='')  { alert("사업자 등록증 사본을 첨부해주세요."); f.business_license.focus(); }
	else if(bankbook=='')          { alert("통장사본을 첨부해주세요."); f.bankbook.focus(); }
	else if( $("input:checkbox[id='is_creditor']").is(':checked')==true && loan_co_license=='') { alert('대부업 등록증 사본을 첨부해주세요.'); f.loan_co_license.focus(); }
	else if(!agree_provision)      { alert("서비스 이용약관에 대한 동의가 필요합니다."); $('#agree_provision').focus(); }
	else if(!agree_privacy)        { alert("개인정보처리방침에 대한 동의가 필요합니다."); $('#agree_privacy').focus(); }
	else {
		if( confirm('법인회원으로 가입하시겠습니까?') ) {

		//var ajax_data = $('#frmJoin').serialize();
			var ajax_data = new FormData($('#frmJoin')[0]);
			$($('#business_license').files).each(function(index, file) {
				ajax_data.append('business_license', file);
			});
			$($('#bankbook').files).each(function(index, file) {
				ajax_data.append('bankbook', file);
			});
			$($('#loan_co_license').files).each(function(index, file) {
				ajax_data.append('loan_co_license', file);
			});

			$.ajax({
				url : "join_info_proc_c.php",
				type: "POST",
				processData: false,
				contentType: false,
				data : ajax_data,
				success: function(data) {
					//console.log(data);

					if(data == 'OK') {
						// 정상 가입 처리 (심사대기 상태로..) ------------------------------------------------------------------
						location.replace('/member/welcome.php?is_company=1');
						// -----------------------------------------------------------------------------------------------------
					}
					else if(data=='TIME_OVER')          { alert('처리유효시간(10분) 을 초과 하였습니다.'); location.reload(); }
					else if(data=='DUP_ID')             { alert('등록 하실 수 없는 아이디 입니다.'); }
					else if(data=='DUP_HP')             { alert('이미 등록된 휴대폰 번호 정보가 있습니다.'); }
					else if(data=='DUP_EMAIL')          { alert('이미 등록된 이메일주소 정보가 있습니다.'); }

					else if(data=='RECOMMEND_DUP_IP')      { alert('동일한 추천인 아이디를 등록한 IP 기록이 존재하여 추천인을 등록 하실 수 없습니다.'); $('rec_mb_id').val(''); $('#rec_flag').val(''); }
					else if(data=='RECOMMEND_RE_JOIN_USER'){ alert('이벤트 기간중 가입된 후 탈퇴한 이력이 있어 가입이 불가 합니다. 추천인 이벤트가 종료된 이후 가입 가능합니다.'); }

					else if(data=='FILE_SAVE_ERROR(1)') { alert('사업자등록증:\n\n첨부파일 저장 오류 입니다. 고객센터로 문의 하십시오.'); }
					else if(data=='FILE_SAVE_ERROR(2)') { alert('통장사본:\n\n첨부파일 저장 오류 입니다. 고객센터로 문의 하십시오.'); }
					else if(data=='FILE_SAVE_ERROR(3)') { alert('대부업 등록증:\n\n첨부파일 저장 오류 입니다. 고객센터로 문의 하십시오.'); }
					else if(data=='DISALLOW_FILE(1)')   { alert('사업자등록증:\n\n이미지 및 PDF 문서 파일만 등록 가능합니다.'); }
					else if(data=='DISALLOW_FILE(2)')   { alert('통장사본:\n\n이미지 파일 및 PDF 문서 파일만 등록 가능합니다.'); }
					else if(data=='DISALLOW_FILE(3)')   { alert('대부업 등록증:\n\n이미지 파일 및 PDF 문서 파일만 등록 가능합니다.'); }

					else                                  { alert('시스템 오류 입니다. 고객센터로 문의 하십시오.'); }

				},
				error: function(jqXHR, textStatus, errorThrown) {
					//console.log(jqXHR);
					//console.log(textStatus);
					//console.log(errorThrown);
					alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시오.');
				}
			});

		}
	}
});
</script>
