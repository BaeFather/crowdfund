<?
###############################################################################
## [신규/기존회원] 법인용: 사업자정보 및 증빙파일 등록
##
###############################################################################

include_once('./_common.php');

include_once(G5_LIB_PATH . '/function_prc.php');
include_once(G5_LIB_PATH . '/etc.lib.php');

include_once(G5_PATH . '/data/aml_inc/aml_array.inc.php');
include_once(G5_PATH . '/data/aml_inc/kofiu_code.inc.php');

while( list($k, $v) = each($_REQUEST) ) { if( !is_array($k) ) ${$k} = trim($v); }

if($member['member_type']<>'2' || $member['mb_level']<>'1') {
	msg_replace("", "/");
}
if( $member['kyc_allow_yn']=='Y' && $member['kyc_next_dd'] > date('Y-m-d')) {
	msg_replace("", "/");
}

if( in_array($member['kyc_allow_yn'], array('W','I')) ) {
	msg_go("본인확인 서류 검토중");
}
else if( $member['kyc_allow_yn']=='N' ) {
	// 계속 진행
}


if ($co['co_include_head'])
	@include_once($co['co_include_head']);
else
	include_once('./_head.php');

$tmpFileDir = G5_PATH . "/data/kyc_tmp";			// 신분증이미지파일 임시저장소

add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);

?>

<style>
select {border:1px solid #c6c6c6;height:30px;border-radius:3px;padding:0 10px;}
#step0 {display:block;}
#step1 {display:none;}

.frm-identify { padding: 30px 25px 30 px; }

.textarea { display: inline-block; box-sizing: border-box; border:1px solid #ddd; margin:10px 0 0; width:100%; height:100px; border-radius:3px; text-indent:10px; }
</style>

<!-- 본문내용 START -->

<div id="content" class="identify-wrap">
	<div class="content">

	<form id="masterForm" name="masterForm" onSubmit="return false">
		<input type="hidden" id="f_order_id" name="f_order_id" value="<?=strtoupper(uniqid());?>">
		<input type="hidden" id="f_stime"    name="f_stime"    value="<?=time();?>">
		<input type="hidden" id="f_judge"    name="f_judge"    value="1">

<!--- ▼ STEP1 ▼ -------------------------------------------------------------->
	<div id="kycStep1" class="confirm_form" style="display:block">
		<div class="top-title">
			<h2>법인회원확인</h2>
			<div class="page-num">
				<span class="active">1</span>/4
			</div>
		</div>
		<hr>
		<div class="frm-identify">
			<h3>사업자 정보 입력</h3>

			<div class="f-box">
				<label for="CUSTOMER_NM">법인명</label>
				<div class="input-box m-layout">
					<input type="text" name="CUSTOMER_NM" id="CUSTOMER_NM" value="<?=$member['mb_co_name']?>" readonly>
				</div>
			</div>

			<div class="f-box">
				<label for="CUSTOMER_NM">법인명(영문)</label>
				<div class="input-box m-layout">
					<input type="text" name="CUSTOMER_ENG_NM" id="CUSTOMER_ENG_NM" value="<?=$member['mb_co_name_eng']?>">
				</div>
			</div>

			<div class="f-box">
				<label for="PERMIT_NO1">사업자등록번호</label>
				<div class="input-box m-layout">
					<input type="text" name="PERMIT_NO1" id="PERMIT_NO1" maxlength="3" class="input2" onKeyup="onlyDigit(this);" value="<?=substr(preg_replace("/(-| )/","", $member['mb_co_reg_num']),0,3);?>">
					<input type="text" name="PERMIT_NO2" id="PERMIT_NO2" maxlength="2" class="input2" onKeyup="onlyDigit(this);" value="<?=substr(preg_replace("/(-| )/","", $member['mb_co_reg_num']),3,2);?>">
					<input type="text" name="PERMIT_NO3" id="PERMIT_NO3" maxlength="5" class="input2" onKeyup="onlyDigit(this);" value="<?=substr(preg_replace("/(-| )/","", $member['mb_co_reg_num']),5);?>">
				</div>
			</div>

			<div class="f-box">
				<label for="CUSTOMER_TP_CD">법인유형</label>
				<div class="input-box m-layout">
					<select id="CUSTOMER_TP_CD" name="CUSTOMER_TP_CD" class="sel-code">
						<option value="">:: 법인유형선택 ::</option>
						<option value="08">일반</option>
						<option value="01">비영리단체</option>
						<option value="07">상장회사</option>
						<option value="04">금융기관</option>
						<option value="05">국가.지방자치단체</option>
						<option value="06">UN산하 국제자선기구</option>
					</select>
				</div>
			</div>

			<div class="f-box" id="ESTBM_PUPOS_div" style="display:none">
				<label for="ESTBM_PUPOS">설립목적 (비영리법인전용)</label>
				<div class="input-box m-layout">
					<textarea name="ESTBM_PUPOS" id="ESTBM_PUPOS" class="textarea" disabled style="resize:none"></textarea>
				</div>
			</div>

			<script>
			$('#CUSTOMER_TP_CD').on('change', function() {
				if( $('select[name="CUSTOMER_TP_CD"] option:selected').val()=='01' ) {
					$('#ESTBM_PUPOS_div').slideDown(); $('#ESTBM_PUPOS').attr('disabled',false);
					$('#CORP_REG_NO_div, #c_cd_div').slideUp(); $('#CORP_REG_NO1, #CORP_REG_NO2, #C_CD, #P_CD').attr('disabled',true);
				}
				else {
					$('#ESTBM_PUPOS_div').slideUp(); $('#ESTBM_PUPOS').attr('disabled',true);
					$('#CORP_REG_NO_div, #c_cd_div').slideDown(); $('#CORP_REG_NO1, #CORP_REG_NO2, #C_CD, #P_CD').attr('disabled',false);
				}
			});
			</script>

			<div class="f-box" id="CORP_REG_NO_div">
				<label for="CORP_REG_NO1">법인등록번호</label>
				<div class="input-box m-layout">
					<input type="text" name="CORP_REG_NO1" id="CORP_REG_NO1" class="input1" maxlength="6" onKeyup="onlyDigit(this);" value="<?=substr(preg_replace("/(-| )/","", $member['corp_num']),0,6);?>">
					<input type="text" name="CORP_REG_NO2" id="CORP_REG_NO2" class="input1" maxlength="7" onKeyup="onlyDigit(this);" value="<?=substr(preg_replace("/(-| )/","", $member['corp_num']),6);?>">
				</div>
			</div>

			<div class="f-box" id="CREATE_DD_div">
				<label for="CREATE_DD1">법인설립일</label>
				<div class="input-box m-layout">
					<select name="CREATE_DD_Y" id="CREATE_DD_Y" class="input2 sel-code">
						<option value="">설립연도</option>
						<? for($i=date('Y'); $i>=1900; $i--) { echo "<option value='{$i}'>{$i}년</option>"; } ?>
					</select>
					<select name="CREATE_DD_m" id="CREATE_DD_m" class="input2 sel-code">
						<option value="">월</option>
						<? for($i=1; $i<=12; $i++) { echo "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."월</option>"; } ?>
					</select>
					<select name="CREATE_DD_d" id="CREATE_DD_d" class="input2 sel-code">
						<option value="">일</option>
						<? for($i=1; $i<=31; $i++) { echo "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."일</option>"; } ?>
					</select>
				</div>
			</div>

			<div class="f-box" id="c_cd_div">
				<label for="C_CD">업종선택</label>
				<div class="input-box m-layout">
					<select name="C_CD" id="C_CD" class="sel-code">
						<option value="">:: 업태 ::</option>
<?
	$res = sql_query("SELECT C_CD, C_NM FROM aml_kofiu_industry_code GROUP BY C_CD ORDER BY C_CD");
	while( $row = sql_fetch_array($res) ) {
		echo "<option value='".$row['C_CD']."'>".$row['C_NM']."</option>";
	}
?>
					</select>
					<select name="P_CD" id="P_CD" class="sel-code">
						<option value="">:: 종목 ::</option>
					</select>
				</div>
			</div>

			<script>
			function get_industry_code() {

				var $target = $("select[id='P_CD']");

				var c_cd = $('#C_CD').val();

				$.ajax({
					url : '/ajax_industry_code.php',
					type : 'post',
					data : {'C_CD':c_cd},
					dataType : 'json',
					success: function(data) {
						$target.empty();
						$target.append("<option value=''>:: 종목 ::</option>");

						if(data.length > 0) {
							$(data).each(function(i){
								$target.append("<option value='" + data[i]['code'] + "'>" + data[i]['name'] + "</option>");
							});
						}
						else {
						}
					},
					error: function() {
						alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
						$('#loading').css('display','none');
						return;
					}
				});
			}

			$('#C_CD').on('change', function(){
				get_industry_code();
			});
			</script>

			<div class="f-box">
				<label for="zip_num">법인주소</label>
				<ul style="width:100%; margin:0;padding:0;">
					<li>
						<div class="input-box m-layout">
							<input type="text" name="zip_num" id="zip_num" class="zip-num input-disable" placeholder="우편번호" readonly required="required"  onClick="win_zip('masterForm', 'zip_num', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">
							<input type="button" id="zip_num_button" class="add-search" value="주소검색" onClick="win_zip('masterForm', 'zip_num', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">
						</div>
					</li>
					<li><input type="text" name="mb_addr1" id="mb_addr1" class="input-disable" placeholder="도로명주소" readonly required="required" onClick="win_zip('masterForm', 'zip_num', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');"></li>
					<li>
						<input type="text" name="mb_addr2" id="mb_addr2" placeholder="상세주소" required="required">
					</li>
					<li style="display:none">
						<input type="text" name="mb_addr_jibeon" id="mb_addr_jibeon">
						<input type="text" name="mb_addr3" id="mb_addr3">
					</li>
				</ul>
			</div>

			<div class="f-box">
				<label for="zip_num">법인연락처(대표번호)</label>
				<input type="text" id="corp_phone" name="corp_phone" value="<?=$member['corp_phone']?>" onKeyup="onlyDigit(this);" maxlength="11">
			</div>

			<div class="f-box">
				<label for="TRAN_FUND_SOURCE_DIV">거래자금 출처</label>
				<select id="TRAN_FUND_SOURCE_DIV" name="TRAN_FUND_SOURCE_DIV" class="sel-code">
					<option value="">:: 거래자금출처 ::</option>
					<option value="B01">사업소득</option>
					<option value="B02">부동산임대소득</option>
					<option value="B03">부동산양도소득</option>
					<option value="B04">금융소득(이자 및 배당)</option>
					<option value="B99">기타</option>
				</select>
				<input type="hidden" id="TRAN_FUND_SOURCE_NM" name="TRAN_FUND_SOURCE_NM">
				<input type="hidden" id="TRAN_FUND_SOURCE_OTHER" name="TRAN_FUND_SOURCE_OTHER">
			</div>

			<div style="margin-top:50px;">
				<button type="button" id="KYCNextButton1" class="btn-confirm">확인</button>
			</div>
		</div>
	</div><!-- end kycStep1 -->

	<script>
	$('#kycStep1 select[name="TRAN_FUND_SOURCE_DIV"]').on('change', function() {
		if( $('#kycStep1 select[name="TRAN_FUND_SOURCE_DIV"] option:selected').val() ) {
			$('#kycStep1 #TRAN_FUND_SOURCE_NM').val( $('#kycStep1 select[name="TRAN_FUND_SOURCE_DIV"] option:selected').text() );
		}
		else {
			$('#kycStep1 #TRAN_FUND_SOURCE_NM').val('');
		}
		if( $('#kycStep1 select[name="TRAN_FUND_SOURCE_DIV"] option:selected').val()=='B99' ) {
			$('#kycStep1 #TRAN_FUND_SOURCE_OTHER').val('기타소득');
		}
		else {
			$('#kycStep1 #TRAN_FUND_SOURCE_OTHER').val('');
		}
	});

	$('#KYCNextButton1').on('click', function() {
		if($.trim($('#kycStep1 #CUSTOMER_NM').val())=='') { alert('법인명을 입력해 주세요.'); $('#kycStep1 #CUSTOMER_NM').focus(); return; }
		if($.trim($('#kycStep1 #CUSTOMER_ENG_NM').val())=='') { alert('법인명(영문)을 입력해 주세요.'); $('#kycStep1 #CUSTOMER_ENG_NM').focus(); return; }
		if($.trim($('#kycStep1 #PERMIT_NO1').val())=='') { alert('사업자등록번호를 입력해 주세요.'); $('#kycStep1 #PERMIT_NO1').focus(); return; }
		if($.trim($('#kycStep1 #PERMIT_NO2').val())=='') { alert('사업자등록번호를 입력해 주세요.'); $('#kycStep1 #PERMIT_NO2').focus(); return; }
		if($.trim($('#kycStep1 #PERMIT_NO3').val())=='') { alert('사업자등록번호를 입력해 주세요.'); $('#kycStep1 #PERMIT_NO3').focus(); return; }
		if($('#kycStep1 #CUSTOMER_TP_CD').val()=='') { alert("법인유형을 선택해 주세요.\n일반 법인일 경우 '일반'를 선택하시면 됩니다."); $('#kycStep1 #CUSTOMER_TP_CD').focus(); return; }

		if( $('#kycStep1 select[name="CUSTOMER_TP_CD"] option:selected').val()=='01' ) {
			if($.trim($('#kycStep1 #ESTBM_PUPOS').val())=='') { alert('설립목적을 입력해 주세요.'); $('#kycStep1 #ESTBM_PUPOS').focus(); return; }
		}

		if($('#kycStep1 #CORP_REG_NO1').is(':disabled')==false) {
			if($.trim($('#kycStep1 #CORP_REG_NO1').val())=='' || $.trim($('#kycStep1 #CORP_REG_NO1').val()).length < 6) { alert('법인등록번호를 입력해 주세요.'); $('#kycStep1 #CORP_REG_NO1').focus(); return; }
			if($.trim($('#kycStep1 #CORP_REG_NO2').val())=='' || $.trim($('#kycStep1 #CORP_REG_NO2').val()).length < 7) { alert('법인등록번호를 입력해 주세요.'); $('#kycStep1 #CORP_REG_NO2').focus(); return; }
		}

		if($('#kycStep1 #CREATE_DD_Y').val()=='') { alert('법인설립일을 입력해 주세요.'); $('#kycStep1 #CREATE_DD_Y').focus(); return; }
		if($('#kycStep1 #CREATE_DD_m').val()=='') { alert('법인설립일을 입력해 주세요.'); $('#kycStep1 #CREATE_DD_m').focus(); return; }
		if($('#kycStep1 #CREATE_DD_d').val()=='') { alert('법인설립일을 입력해 주세요.'); $('#kycStep1 #CREATE_DD_d').focus(); return; }

		if($('#kycStep1 #C_CD').is(':disabled')==false) {
			if($('#kycStep1 #C_CD').val()=='') { alert('업태를 선택해 주세요.'); $('#kycStep1 #C_CD').focus(); return; }
			if($('#kycStep1 #P_CD').val()=='') { alert('종목을 선택해 주세요.'); $('#kycStep1 #P_CD').focus(); return; }
		}

		if($('#kycStep1 #zip_num').val()=='') { alert('법인주소 우편번호를 입력해 주세요.'); $('#kycStep1 #zip_num_button').focus(); return; }
		if($('#kycStep1 #mb_addr1').val()=='') { alert('법인주소를 입력해 주세요.'); $('#kycStep1 #mb_addr1').focus(); return; }
		if($.trim($('#kycStep1 #mb_addr2').val())=='') { alert('법인상세주소를 입력해 주세요.'); $('#kycStep1 #mb_addr2').focus(); return; }
		if($.trim($('#kycStep1 #corp_phone').val())=='') { alert('법인연락처(대표번호)를 입력해 주세요.'); $('#kycStep1 #corp_phone').focus(); return; }

		if($('#kycStep1 #TRAN_FUND_SOURCE_DIV').val()=='') { alert('거래자금 출처를 선택해 주세요.'); $('#kycStep1 #TRAN_FUND_SOURCE_DIV').focus(); return; }

		timestamp = Math.floor(+ new Date() / 1000);
		form_timestamp = $('#f_stime').val();
		check_timestamp = timestamp - form_timestamp;

		if(check_timestamp > 1800) {
			alert('입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
			window.location.reload();
		}

		$(location).attr('href','#');
		$('#kycStep1').fadeOut();
		setTimeout(function() {
			$('#masterForm #f_stime').val(timestamp);
			$('#kycStep2').fadeIn();
		}, 300);
	});
	</script>
<!--- ▲ STEP1 ▲ -------------------------------------------------------------->

<!--- ▼ STEP2 ▼ -------------------------------------------------------------->
	<div id="kycStep2" class="confirm_form" style="display:<?=($member['mb_id']=='sori9th2')?'none':'none';?>">
		<div class="top-title">
			<h2>법인회원확인</h2>
			<div class="page-num">
				<span class="active">2</span>/4
			</div>
		</div>
		<hr>
		<div class="frm-identify">
			<h3>대표자 및 담당자 정보</h3>

			<div class="f-box">
				<label for="">대표자 성명</label>
				<div class="input-box m-layout">
					<input type="text" name="CEO_NM" id="CEO_NM" value="<?=$member['mb_co_owner']?>">
				</div>
			</div>

			<div class="f-box">
				<label for="">대표자 영문명</label>
				<div class="input-box m-layout">
					<input type="text" name="CEO_ENG_LAST_NM" id="CEO_ENG_LAST_NM" value="<?=$member['eng_last_nm']?>" class="input1" placeholder="성" style="ime-mod:disabled" onKeyup="onlyAlphabetUpper(this);">
					<input type="text" name="CEO_ENG_FIRST_NM" id="CEO_ENG_FIRST_NM" value="<?=$member['eng_first_nm']?>" class="input1" placeholder="이름" style="ime-mod:disabled" onKeyup="onlyAlphabetUpper(this);">
				</div>
			</div>

			<div class="f-box">
				<label for="">대표자 국적</label>
				<div class="input-box m-layout">
					<select name="CEO_COUNTRY_CD" id="CEO_COUNTRY_CD" class="sel-code">
						<option value="">:: 국적선택 ::</option>
<?
	for($i=0; $i<$KOFIU_COUNTRY_COUNT; $i++) {
		$selected = ($KOFIU_COUNTRY_CODE[$KCCD_ARRKEY[$i]]['CD']=='KR') ? 'selected' : '';
		echo "<option value='".$KOFIU_COUNTRY_CODE[$KCCD_ARRKEY[$i]]['CD']."' $selected>".$KOFIU_COUNTRY_CODE[$KCCD_ARRKEY[$i]]['NM']."</option>";
	}
?>
					</select>
				</div>
			</div>

			<div class="f-box">
				<label for="CEO_POST_NO">대표자 주소</label>
				<ul style="width:100%; margin:0;padding:0;">
					<li>
						<div class="input-box m-layout">
							<input type="text" name="CEO_POST_NO" id="CEO_POST_NO" class="zip-num input-disable" placeholder="우편번호" readonly required="required">
							<input type="button" id="CEO_POST_NO_button" class="add-search" value="주소검색" onClick="win_zip('masterForm', 'CEO_POST_NO', 'CEO_ADDR', 'CEO_DTL_ADDR', 'CEO_ADDR3', 'CEO_ADDR_jibeon');">
						</div>
					</li>
					<li><input type="text" name="CEO_ADDR" id="CEO_ADDR" class="input-disable" placeholder="도로명주소" readonly required="required"></li>
					<li><input type="text" name="CEO_DTL_ADDR" id="CEO_DTL_ADDR" autocomplete="off" placeholder="상세주소" class="input1" required="required"></li>
					<li style="display:none">
						<input type="text" name="CEO_ADDR_jibeon" id="CEO_ADDR_jibeon">
						<input type="text" name="CEO_ADDR3"       id="CEO_ADDR3">
					</li>
				</ul>
			</div>

			<div class="f-box">
				<label for="mb_name">담당자 성명</label>
				<div class="input-box m-layout">
					<ul style="width:100%; display:inline-block">
						<li style="float:left;width:50%;"><input type="text" name="mb_name" id="mb_name" value="<?=$member['mb_name']?>"></li>
						<li style="float:left;width:48%;margin-left:2%;">
							<select name="corp_officer_div" id="corp_officer_div" class="sel-code">
								<option value="">:: 법인과의 관계 ::</option>
								<option value="1" <?if($member['corp_officer_div']=='1') echo "selected";?>>대표자</option>
								<option value="2" <?if($member['corp_officer_div']=='2') echo "selected";?>>소속직원</option>
							</select>
						</li>
					</ul>
				</div>
			</div>

			<div class="f-box">
				<label for="mb_hp">담당자 휴대폰</label>
				<div class="input-box m-layout">
					<select name="mb_hp1" id="mb_hp1" class="input2 sel-code">
						<option value="010">010</option>
						<option value="011">011</option>
						<option value="016">016</option>
						<option value="017">017</option>
						<option value="018">018</option>
						<option value="019">019</option>
					</select>&nbsp;
					<input type="text" name="mb_hp2" id="mb_hp2" class="input2" onKeyUp="onlyDigit(this);" maxlength="4">
					<input type="text" name="mb_hp3" id="mb_hp3" class="input2" onKeyUp="onlyDigit(this);" maxlength="4">
				</div>
			</div>

			<div class="f-box">
				<label for="">담당자 이메일</label>
				<div class="input-box m-layout">
					<input type="text" name="mb_email" id="mb_email" value="<?=$member['mb_email'];?>" onKeyUp="stringCheck('mb_email','email');" maxlength="64">
				</div>
			</div>

			<div style="margin-top:20px;">
				<ul>
					<li style="float:left;width:49.5%"><button type="button" id="KYCBackButton2" class="btn-next" style="background:#777;">이전</button></li>
					<li style="float:right;width:49.5%;margin-left:1%"><button type="button" id="KYCNextButton2" class="btn-confirm">다음</button></li><br/>
				</ul>
			</div>
		</div>
	</div>

	<script>
	$('#KYCBackButton2').on('click', function(){
		$(location).attr('href', '#');
		$('#kycStep2').fadeOut();
		setTimeout(function() { $('#kycStep1').fadeIn(); }, 300);
	});

	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	$('#KYCNextButton2').on('click', function() {
		if($.trim($('#kycStep2 #CEO_NM').val())=='') { alert('대표자명을 입력해 주세요.'); $('#kycStep2 #CEO_NM').focus(); return; }
		if($.trim($('#kycStep2 #CEO_ENG_LAST_NM').val())=='') { alert('대표자 영문(성)을 입력해 주세요.'); $('#kycStep2 #CEO_ENG_LAST_NM').focus(); return; }
		if($.trim($('#kycStep2 #CEO_ENG_FIRST_NM').val())=='') { alert('대표자 영문(이름)을  입력해 주세요.'); $('#kycStep2 #CEO_ENG_FIRST_NM').focus(); return; }
		if($('#kycStep2 #CEO_COUNTRY_CD').val()=='') { alert('대표자 국적을 선택해 주세요.'); $('#kycStep2 #CEO_COUNTRY_CD').focus(); return; }

		if($('#kycStep2 #CEO_POST_NO').val()=='') { alert('대표자 주소 우편번호를 입력해 주세요.'); $('#kycStep2 #CEO_POST_NO_button').focus(); return; }
		if($('#kycStep2 #CEO_ADDR1').val()=='') { alert('대표자 주소를 입력해 주세요.'); $('#kycStep2 #CEO_ADDR1').focus(); return; }
		if($.trim($('#kycStep2 #CEO_DTL_ADDR').val())=='') { alert('대표자 상세주소를 입력해 주세요.'); $('#kycStep2 #CEO_DTL_ADDR').focus(); return; }

		if($.trim($('#kycStep2 #mb_name').val())=='') { alert('담당자명을 입력해 주세요.'); $('#kycStep2 #mb_name').focus(); return; }
		if($('#kycStep2 #corp_officer_div').val()=='') { alert('법인과의 관계를 선택해 주세요.'); $('#kycStep2 #corp_officer_div').focus(); return; }

		if($.trim($('#kycStep2 #mb_hp1').val())=='') { alert('담당자 연락처를 입력해 주세요.'); $('#kycStep2 #mb_hp1').focus(); return; }
		if($.trim($('#kycStep2 #mb_hp2').val())=='' || $.trim($('#kycStep2 #mb_hp2').val()).length < 3) { alert('담당자 연락처를 3자리수 이상 입력해 주세요.'); $('#kycStep2 #mb_hp2').focus(); return; }
		if($.trim($('#kycStep2 #mb_hp3').val())=='' || $.trim($('#kycStep2 #mb_hp3').val()).length < 3) { alert('담당자 연락처를 3자리수 이상 입력해 주세요.'); $('#kycStep2 #mb_hp3').focus(); return; }

		if($.trim($('#kycStep2 #mb_email').val())=='') { alert('담당자 이메일을 입력해 주세요.'); $('#kycStep2 #mb_email').focus(); return; }
		if(!re.test($('#kycStep2 #mb_email').val())) { alert("올바른 이메일 주소를 입력하세요"); $('#kycStep2 #mb_email').focus(); return; }

		timestamp = Math.floor(+ new Date() / 1000);
		form_timestamp = $('#f_stime').val();
		check_timestamp = timestamp - form_timestamp;

		if(check_timestamp > 1800) {
			alert('입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
			window.location.reload();
		}

		$(location).attr('href', '#');
		$('#kycStep2').fadeOut();
		setTimeout(function() {
			$('#masterForm #f_stime').val(timestamp);
			$('#kycStep3').fadeIn();
		}, 300);
	});
	</script>
<!--- ▲ STEP2 ▲ -------------------------------------------------------------->

<!--- ▼ STEP3 ▼ -------------------------------------------------------------->
	<div id="kycStep3" class="confirm_form" style="display:<?=($member['mb_id']=='sori9th2')?'none':'none';?>">
		<div class="top-title">
			<h2>법인회원확인</h2>
			<div class="page-num">
				<span class="active">3</span>/4
			</div>
		</div>
		<hr>
		<div class="frm-identify">
			<h3>실제 소유자 정보</h3>

			<div class="f-box">
				<label for="">실제 소유자 정보 양식 다운로드</label>
				<div class="input-box m-layout">
					<ul style="display:inline-block;width:100%">
						<li style="float:left;width:49.5%"><button type="button" onClick="downloadFile('pdf');" class="btn-next" style="margin-top:20px;background:#FFF;border:1px solid #FF2222;color:#FF2222">PDF 문서로 내려받기</button></li>
						<li style="float:right;width:49.5%"><button type="button" onClick="downloadFile('docx');" class="btn-next" style="margin-top:20px;background:#FFF;border:1px solid #3366FF;color:#3366FF">MS-WORD 문서로 내려받기</button></li>
					</ul>
				</div>
			</div>

			<div class="f-box">
				<label for="">필수 증빙서류</label>
				<div class="input-box m-layout">
					<ul style="display:inline-block;margin:10px 20px;color:brown">
						<li>- 사업자 등록증</li>
						<li>- 법인등기부등본</li>
						<li>- 법인인감증명서</li>
						<li>- 법인 통장사본</li>
						<li>- 대표자 신분증</li>
						<li>- 실제 소유자 정보 양식 및 증빙 서류 등</li>
						<li>- 주주명부</li>
						<li>- 대부업 법인의 경우 대부업 등록증</li>
						<li>- 비영리 법인의 경우 정관 제출</li>
					</ul>
					<div style="color:#3366FF">* 입력한 정보와 제출한 증빙서류 검토 후 가입이 완료됩니다.(영업일 1~3일)</div>
				</div>
			</div>

			<div class="f-box">
				<label id="zip_file_attach" style="color:#FF2222"><input type="radio" id="attach_type" name="attach_type" value="zip" checked> 증빙서류 첨부하기 (묶음 또는 압축파일 등록)</label>
				<div class="input-box m-layout" id="fileDiv1">
					<div class="filebox m-layout">
						<input class="upload-name file-mask" value="선택된 파일 없음" readonly>
						<input type="file" id="identify_zip_file" name="identify_zip_file" accept=".jpg, .jpeg, .png, .pdf, .zip">
						<label for="identify_zip_file">파일선택</label>
					</div>
				</div>
			</div>

			<div style="margin-top:50px;">
				<ul>
					<li style="float:left;width:49.5%"><button type="button" id="KYCBackButton3" class="btn-next" style="background:#777;">이전</button></li>
					<li style="float:right;width:49.5%;margin-left:1%"><button type="button" id="KYCNextButton3" class="btn-confirm">다음</button></li>
				</ul>
			</div>
		</div>
	</div>

	<script>
	$('#KYCBackButton3').on('click', function(){
		$(location).attr('href', '#');
		$('#kycStep3').fadeOut();
		setTimeout(function() { $('#kycStep2').fadeIn(); }, 300);
	});

	function downloadFile(file_type) {
		if(file_type=='pdf') {
			printtype = 'PDF';
			url = 'docs/헬로펀딩_실제소유자_확인사항.pdf';
		}
		else {
			printtype = 'MS-WORD';
			url = 'docs/헬로펀딩_실제소유자_확인사항.docx';
		}

		if(confirm(printtype + ' 문서 파일을 다운로드 받으시겠습니까?')) {
			window.open(url);
		}
	}

	$('#KYCNextButton3').on('click', function() {

		timestamp = Math.floor(+ new Date() / 1000);
		form_timestamp = $('#f_stime').val();
		check_timestamp = timestamp - form_timestamp;

		if(check_timestamp > 1800) {
			alert('입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
			window.location.reload();
		}

		if( $('#masterForm #identify_zip_file').val() == '' ) {
			alert('제출서류 파일을 등록 해주세요.');
			$('#masterForm #identify_zip_file').focus();
			return;
		}
		else {
			var f = $('#masterForm')[0];
			if( f.identify_zip_file.files[0].size > 50000000 ) {
				alert('50MB를 초과한 파일은 등록하실 수 없습니다.');
				$('#identify_zip_file').val('');
				$('.file-mask').val('선택된 파일 없음');
				return;
			}
		}

		$(location).attr('href', '#');
		$('#kycStep3').fadeOut();
		setTimeout(function() {
			$('#masterForm #f_stime').val(timestamp);
			$('#kycStep4').fadeIn();
		}, 300);

	});
	</script>

	<script>
	$(document).ready(function(){
		var fileTarget = $('#identify_zip_file');
		fileTarget.on('change', function() {						// 값이 변경되면
			if(window.FileReader){
				var filename = $(this)[0].files[0].name;		// modern browser
			}
			else {
				var filename = $(this).val().split('/').pop().split('\\').pop(); // old IE 파일명만 추출
			}
			$(this).siblings('.file-mask').val(filename);			// 추출한 파일명 삽입
		});
	});
	</script>
<!--- ▲ STEP3 ▲ -------------------------------------------------------------->

<!--- ▼ STEP4 ▼ -------------------------------------------------------------->
	<div id="kycStep4" class="confirm_form" style="display:<?=($member['mb_id']=='sori9th2')?'none':'none';?>">
		<div class="top-title">
			<h2>법인회원확인</h2>
			<div class="page-num">
				<span class="active">4</span>/4
			</div>
		</div>
		<hr>
		<div class="frm-identify">
			<h3>출금계좌정보</h3>

			<div class="f-box">
				<div class="input-box m-layout">
					<label for="bankCode" style="margin-top:10px; font-size:14px;">은행</label>
					<select name="bankCode" id="bankCode" class="sel-code">
						<option value="">은행을 선택하세요</option>
<?
	$sql = "SELECT bank_code, bank FROM bank_info WHERE display='1' ORDER BY favorite DESC, bank_code ASC";
	$res = sql_query($sql);
	$bank_count = $res->num_rows;
	for($i=0; $i<$bank_count; $i++) {
		$R = sql_fetch_array($res);
		$selected = ($R['bank_code']==sprintf("%03d", $member["bank_code"])) ? 'selected' : '';
		echo "<option value='".$R['bank_code']."' $selected>".$R['bank']."</option>\n";
	}
?>
					</select>

					<label for="acntNo" style="margin-top:10px; font-size:14px;">계좌번호</label>
					<input type="text" name="acntNo" id="acntNo" value="<?=$member['account_num']?>" onKeyup="onlyDigit(this);" placeholder="'-'를 제외한 계좌번호 입력">
					<label for="acntNo" style="margin-top:10px; font-size:14px;">예금주</label>
					<input type="text" name="acntName" id="acntName" value="<?=$member['bank_private_name']?>">
					<label for="acntNo" style="margin-top:10px; font-size:14px;">부기명</label>
					<input type="text" name="acntNameSub" id="acntNameSub" value="<?=$member['bank_private_name_sub']?>">
				</div>
			</div>

			<div class="f-box">
				<label for="agree_provision">서비스정책동의</label>
				<div class="agree">
					<label style="font-size:13px"><input type="checkbox" id="agree_provision"  name="agree_provision"  value='1' checked="checked"> [필수] <a href="/bbs/content.php?co_id=provision" target="_blank"><u>서비스 이용약관</u></a>에 동의합니다.</label><br/>
					<label style="font-size:13px"><input type="checkbox" id="agree_provision2" name="agree_provision2" value='1' checked="checked"> [필수] <a href="/bbs/content.php?co_id=provision2" target="_blank"><u>온라인연계투자약관</u></a>에 동의합니다.</label><br/>
					<label style="font-size:13px"><input type="checkbox" id="agree_usecredit"  name="agree_usecredit"  value='1' checked="checked"> [필수] <a href="/bbs/content.php?co_id=usecredit" target="_blank"><u>개인(신용)정보 수집 및 이용</u></a>에 동의합니다.</label><br/>
					<label style="font-size:13px"><input type="checkbox" id="agree_3rdparty"   name="agree_3rdparty"   value='1' checked="checked"> [필수] <a href="/bbs/content.php?co_id=3rdparty_agreement" target="_blank"><u>개인정보 제3자 제공</u></a>에 동의합니다</label><br/>
					<label style="font-size:13px"><input type="checkbox" id="agree_identify"   name="agree_identify"   value="1" checked="checked"> [필수] <a href="/bbs/content.php?co_id=identify" target="_blank"><u>고유식별정보 처리</u></a>에 동의합니다.</label><br/>
					<label style="font-size:13px"><input type="checkbox" id="agree_marketing"  name="agree_marketing"  value='1' checked="checked"> [선택] <a href="/bbs/content.php?co_id=marketing_agreement" target="_blank"><u>마케팅 정보 수집 및 활용</u></a>에 동의합니다.</label>
				</div>
			</div>

			<div style="margin-top:50px;">
				<ul>
					<li style="float:left;width:49.5%"><button type="button" id="KYCBackButton4" class="btn-next" style="background:#777;">이전</button></li>
					<li style="float:right;width:49.5%;margin-left:1%"><button type="button" id="KYCNextButton4" class="btn-confirm">등록완료</button></li>
				</ul>
			</div>
		</div>
	</div>

	<script>
	$('#KYCBackButton4').on('click', function(){
		$(location).attr('href', '#');
		$('#kycStep4').fadeOut();
		setTimeout(function() { $('#kycStep3').fadeIn(); }, 300);
	});

	$('#KYCNextButton4').on('click', function() {
		if($('#kycStep4 #bankCode').val()=='')                     { alert('환급계좌 은행을 선택해 주세요.'); $('#kycStep4 #bankCode').focus(); return; }
		if($.trim($('#kycStep4 #acntNo').val())=='')               { alert('환급계좌번호를 선택해 주세요.'); $('#kycStep4 #acntNo').focus(); return; }
		if($.trim($('#kycStep4 #acntName').val())=='')             { alert('예금주를 입력해 주세요.'); $('#kycStep4 #acntName').focus(); return; }

		if($('#kycStep4 #agree_provision').is(':checked')==false)  { alert('이용약관 동의가 필요합니다.'); $('#kycStep4 #agree_provision').focus(); return; }
		if($('#kycStep4 #agree_provision2').is(':checked')==false) { alert('온라인연계투자약관 동의가 필요합니다.'); $('#kycStep4 #agree_provision2').focus(); return; }
		if($('#kycStep4 #agree_usecredit').is(':checked')==false)  { alert('개인(신용)정보 수집 및 이용 동의가 필요합니다.'); $('#kycStep4 #agree_usecredit').focus(); return; }
		if($('#kycStep4 #agree_3rdparty').is(':checked')==false)   { alert('개인정보 제3자 제공 동의가 필요합니다.'); $('#kycStep4 #agree_3rdparty').focus(); return; }
		if($('#kycStep4 #agree_identify').is(':checked')==false)   { alert('고유식별정보 처리 동의가 필요합니다.'); $('#kycStep4 #agree_identify').focus(); return; }

		if( confirm('법인정보등록을 하시겠습니까?') ) {
			formSubmit();
		}
	});

	function formSubmit() {
		var form = $('#masterForm')[0];
		var fData = new FormData(form);

		$.ajax({
			url : 'kyc_corp.proc.php',
			type : 'post',
			data : fData,
			dataType : 'json',
			contentType : false,		 //contentType : false 선언 시 content-type 헤더가 multipart/form-data로 전송
			processData : false,     //processData : false 선언 시 formData를 string으로 변환하지 않음
			success: function(data) {
				if(data.result == 'success') {
					$(location).attr('href','#');
					$('#kycStep4').fadeOut();
					setTimeout(function() { $('#kycStep5').fadeIn(); }, 300);
				}
				else {
					if(data.result == 'time_over') {
						alert('입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다..');
						window.location.reload();
					}
					else {
						alert(data.message);
					}
					return;
				}
			},
			beforeSend: function() { $('#loading').css('display','block'); },
			complete: function() { $('#loading').css('display','none'); },
			error: function() {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
				$('#loading').css('display','none');
				return;
			}
		});
	}
	</script>
<!--- ▲ STEP4 ▲ -------------------------------------------------------------->
</form>

<!--- ▼ STEP5 ▼ -------------------------------------------------------------->
	<div id="kycStep5" class="confirm_form" style="display:none">
		<div class="top-title">
			<h2>법인회원확인</h2>
			<div class="page-num">
				<span class="active">4</span>/4
			</div>
		</div>
		<hr>

		<div class="frm-identify">
			<h3>본인확인 서류 제출 완료</h3>
			<p style="margin-top:50px; text-align:center">
				제출하신 서류 관리자 검토 후 법인 확인이 완료되며,<br/>
				서비스 이용이 가능합니다.<br/><br/>
				영업일 기준 1~3일 정도 소요됩니다.
			</p>
			<div style="margin-top:100px;">
				<button type="button" id="KYCNextButton5" class="btn-confirm">확인</button>
			</div>
		</div>
	</div>
	<script>
	$('#KYCNextButton5').on('click', function() {
		location.replace('/');
	});
	</script>
<!--- ▲ STEP5 ▲ -------------------------------------------------------------->

</div>

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
