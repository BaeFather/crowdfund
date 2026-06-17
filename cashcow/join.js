
function selectGender(sel) {
	document.frmJoin.gender.value = sel;
	if(sel=='m') {
		$('#gender_btn1').css('border','1px solid #000000');
		$('#gender_btn2').css('border','1px solid #CDD0E0');
	}
	else if(sel=='w') {
		$('#gender_btn1').css('border','1px solid #CDD0E0');
		$('#gender_btn2').css('border','1px solid #000000');
	}
}

function provisionBlind() {
	if($('#hp_comp').val() > 3) {
		$('#lease_share_provision').slideDown('slow');
	}
	else {
		$('#lease_share_provision').slideUp('slow');
	}
}

function NiceSockStep1() {

	var f  = document.frmJoin;
	var f2 = document.frmAuthRes;

	_mb_name  = $.trim(f.mb_name.value);
	_mb_birth = $.trim(f.mb_birth.value);
	_mb_hp1   = $.trim(f.mb_hp1.value);
	_mb_hp2   = $.trim(f.mb_hp2.value);
	_mb_hp3   = $.trim(f.mb_hp3.value);

	if(_mb_name=='') { alert('성명을 입력 해주세요'); f.mb_name.focus(); return; }
	if(_mb_birth=='' || _mb_birth.length!=8) { alert('생년월일 8자리를 입력 해주세요. 예)19880413'); f.mb_birth.focus(); return; }
	if(f.gender.value=='') { alert('성별을 선택해 주세요.'); return; }
	if(f.hp_comp.value=='') { alert('통신사를 선택해 주세요'); f.hp_comp.focus(); return; }
	if(_mb_hp1=='') { alert('휴대폰번호를 입력 해주세요'); f.mb_hp1.focus(); return; }
	if(_mb_hp2=='') { alert('휴대폰번호를 입력 해주세요'); f.mb_hp2.focus(); return; }
	if(_mb_hp3=='') { alert('휴대폰번호를 입력 해주세요'); f.mb_hp3.focus(); return; }

/*
	if($("input:checkbox[id='prov1']").is(":checked")==false) { alert('`개인정보이용`에 동의하여 주십시요.');     $('#prov1').focus(); return; }
	if($("input:checkbox[id='prov2']").is(":checked")==false) { alert('`고유식별정보처리`에 동의하여 주십시요.'); $('#prov2').focus(); return; }
	if($("input:checkbox[id='prov3']").is(":checked")==false) { alert('`서비스이용약관`에 동의하여 주십시요.');   $('#prov3').focus(); return; }
	if($("input:checkbox[id='prov4']").is(":checked")==false) { alert('`통신사이용약관`에 동의하여 주십시요.');   $('#prov4').focus(); return; }
	if($('#hp_comp').val() > 3) {
		if($("input:checkbox[id='prov5']").is(":checked")==false) { alert('`개인정보 제3자 제공`에 동의하여 주십시요.'); $('#prov5').focus(); return; }
	}
*/

	$('#auth_request').attr('disabled', true);

	_mb_hp = _mb_hp1 + _mb_hp2 + _mb_hp3;

	f2.auth_mb_name.value   = _mb_name;
	f2.auth_foreigner.value = (f.foreigner.value) ? f.foreigner.value : '';
	f2.auth_mb_birth.value  = _mb_birth;

	var gender = '';
	if(f.mb_birth.value.substring(0,2)=='19') {
		if(f.gender.value == 'm') {
			_gender = (f.foreigner.value=='') ? '1' : '5';
		}
		else if(f.gender.value == 'w') {
			_gender = (f.foreigner.value=='') ? '2' : '6';
		}
	}
	else if(f.mb_birth.value.substring(0,2)=='20') {
		if(f.gender.value == 'm') {
			_gender = (f.foreigner.value=='') ? '3' : '7';
		}
		else if(f.gender.value == 'w') {
			_gender = (f.foreigner.value=='') ? '4' : '8';
		}
	}

	f2.auth_gender.value  = _gender;
	f2.auth_hp_comp.value = f.hp_comp.value;
	f2.auth_mb_hp.value   = _mb_hp;


	var send_data = new Object();
	send_data.Mbirth  = _mb_birth + _gender;
	send_data.Msex    = _gender;
	send_data.Mname   = _mb_name;
	send_data.Mhp     = _mb_hp;
	send_data.Mhpcomp = f.hp_comp.value;

	f.is_sign.value    = "";
	f.mb_dupinfo.value = "";

	$.ajax({
			url  : "/member/ajax.auth1.php",
			type : "post",
			data : send_data,
			success : function(data) {

				//console.log(data);

				res = JSON.parse(data);

				if(res.getReturnCode=="0000") {

					f.mb_dupinfo.value = res.res_seq;
					f.mb_reqnum.value = res.req_seq;

					if (res.already=="Y") {
						f.is_sign.value = "Y";
						alert("본인 인증 되었습니다.");
						$("#auth_request").html('인증완료');
						$("#auth_request").attr('disabled',true);
						$("#auth_request").css('background','#999');

						$("#auth_num").css('display','none');
						$("#auth_submit").css('display','none');

						/* 이통사 약관 구역 히든
						$("#auth_num").css('display','none');
						$("#auth_submit").css('display','none');
						$("#provision_zone").css('display','none');
						*/
						return;
					}
					else {
						if (res.checked=="Y") {
							f.is_sign.value = "Y";
							alert("본인 인증 되었습니다.");
							$("#auth_request").html('인증완료');
							$("#auth_request").attr('disabled',true);
							$("#auth_request").css('background','#999');

							/* 이통사 약관 구역 히든
							$("#auth_num").css('display','none');
							$("#auth_submit").css('display','none');
							$("#provision_zone").css('display','none');
							*/
							return;
						}
						else {
							alert("인증번호를 입력해 주세요."); f.auth_num.focus();
						}
					}
				}
				else if(res.getReturnCode=="DUPLICATE") {
					alert('이미 등록된 휴대폰 번호 입니다.\n중복 가입은 되지 않습니다.');
					return;
				}
				else {

					$('#auth_request').attr('disabled', false);

					msg = "휴대폰 본인 인증 실패\n\n";
					msg+= "[ ERROR CODE: " + res.getReturnCode + " ]";
					if(res.getReturnCode=="0001")      msg+= "\n휴대폰 명의자 정보 불일치\n - 통신사선택오류\n - 생년월일/성명/휴대폰번호 불일치\n - 휴대폰일시정지\n - 선불폰가입자\n - SMS발송실패\n - 인증문자불일치 등의 사유";
					else if(res.getReturnCode=="0003") msg+= "\n기타인증오류";
					else if(res.getReturnCode=="0010") msg+= "\n인증번호 불일치(소켓)";
					else if(res.getReturnCode=="0012") msg+= "\n요청정보오류(입력값오류)";
					else if(res.getReturnCode=="0013") msg+= "\n암호화 시스템 오류";
					else if(res.getReturnCode=="0014") msg+= "\n암호화 처리 오류";
					else if(res.getReturnCode=="0015") msg+= "\n암호화 데이터 오류";
					else if(res.getReturnCode=="0016") msg+= "\n복호화 처리 오류";
					else if(res.getReturnCode=="0017") msg+= "\n복호화 데이터 오류";
					else if(res.getReturnCode=="0018") msg+= "\n통신오류";
					else if(res.getReturnCode=="0019") msg+= "\n데이터베이스 오류";
					else if(res.getReturnCode=="0020") msg+= "\n유효하지않은 CP코드";
					else if(res.getReturnCode=="0021") msg+= "\n중단된 CP코드";
					else if(res.getReturnCode=="0022") msg+= "\n휴대전화본인확인 사용불가 CP코드";
					else if(res.getReturnCode=="0023") msg+= "\n미등록 CP코드";
					else if(res.getReturnCode=="0031") msg+= "\n유효한 인증이력 없음";
					else if(res.getReturnCode=="0035") msg+= "\n기인증완료건(소켓)";
					else if(res.getReturnCode=="0040") msg+= "\n본인확인차단고객(통신사)";
					else if(res.getReturnCode=="0041") msg+= "\n인증문자발송차단고객(통신사)";
					else if(res.getReturnCode=="0050") msg+= "\nNICE 명의보호서비스 이용고객 차단";
					else if(res.getReturnCode=="0052") msg+= "\n부정사용차단";
					else if(res.getReturnCode=="0070") msg+= "\n간편인증앱 미설치";
					else if(res.getReturnCode=="0071") msg+= "\n앱인증 미완료";
					else if(res.getReturnCode=="0072") msg+= "\n간편인증 처리중 오류";
					else if(res.getReturnCode=="0073") msg+= "\n간편인증앱 미설치(LG U+ Only)";
					else if(res.getReturnCode=="0074") msg+= "\n간편인증앱 재설치필요";
					else if(res.getReturnCode=="0075") msg+= "\n간편인증사용불가-스마트폰아님";
					else if(res.getReturnCode=="0076") msg+= "\n간편인증앱 미설치";
					else if(res.getReturnCode=="0078") msg+= "\n14세 미만 인증 오류";
					else if(res.getReturnCode=="0079") msg+= "\n간편인증 시스템 오류";
					else if(res.getReturnCode=="9097") msg+= "\n인증번호 3회 불일치";
					else                               msg+= "\n기타오류";

					alert(msg);
				}

			},
			error : function(jqXHR, textStatus, errorThrown) {
				//console.log(jqXHR);
				//console.log(textStatus);
				//console.log(errorThrown);
			}

	});
}


function NiceSockStep2() {
	var f = document.frmJoin;

	_auth_num = $.trim(f.auth_num.value);

	if (_auth_num=='') {
		alert("먼저 인증요청을 해주세요.");
		return false;
	}

	var send_data = new Object();
	send_data.Authno      = _auth_num;
	send_data.ResponseSeq = f.mb_dupinfo.value;
	send_data.RequestSeq  = f.mb_reqnum.value;

	$.ajax({
			url  : "/member/ajax.auth2.php",
			type : "post",
			data : send_data,
			success : function(data) {

				//console.log(data);

				res = JSON.parse(data);

				if (res.getReturnCode=="0000") {
					f.is_sign.value = "Y";
					alert("본인 인증 되었습니다.");
					$("#auth_request").html('인증완료');
					$("#auth_request").attr('disabled',true);
					$("#auth_num").css('display','none');
					$("#auth_submit").css('display','none');
					return;
				}
				else {

					msg = "휴대폰 본인 인증 실패\n\n";
					msg+= "[ ERROR CODE: " + res.getReturnCode + " ]";
					if(res.getReturnCode=="0001")       msg+= "\n인증번호 불일치";
					else if(res.getReturnCode=="0031")	msg+= "\n응답 고유번호 확인 불가";
					else if(res.getReturnCode=="0032")	msg+= "\n주민번호 불일치";
					else if(res.getReturnCode=="0033")	msg+= "\n요청 고유번호 불일치";
					else if(res.getReturnCode=="0033")	msg+= "\n기 인증 완료 건";

					alert("본인 인증에 실패하였습니다.");
					return;
				}
			},
			error : function(jqXHR, textStatus, errorThrown) {
				//console.log(jqXHR);
				//console.log(textStatus);
				//console.log(errorThrown);
				alert(textStatus);
			}
	});
}


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

	if(len < id_min_length || len > id_max_length){ result = false; }
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
	if(idpw_type=='hard') {
		if(!re3.test(str)) { result = false; }
	}

	var len = str.length;

	if(len < pw_min_length || len > pw_max_length) { result = false; }
	if(str.indexOf(' ') > -1) { result = false; }
	return result;
}


//아이디 체크
var auth_mb_id = '';

id_check = function() {
	var f = document.frmJoin;
	var mb_id = trim(f.mb_id.value);

	//$('#mb_id_error').empty();

	if(mb_id=='') { alert('ID를 입력 하십시요.'); f.mb_id.value = ''; f.mb_id.focus(); }
	else {
		if (!id_string_check(mb_id)) { alert('올바른 ID 형식이 아닙니다.'); }
		else {
			$.ajax({
				url : "/member/confirm_id.php",
				type: "post",
				data : {'prm1':mb_id},
				success: function(data) {
					$('#ajax_return_txt').val(data);
					if(data=='o')	     { alert('사용 가능한 아이디 입니다.'); auth_mb_id = mb_id; }
					else if(data=='x') { alert('사용 하실 수 없는 아이디 입니다.'); }
					else			         { alert('시스템 오류 입니다. 고객센터로 문의 하십시요.'); }
				},
				error: function () {
					alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시요.');
				}
			})
		}
	}
}


// 암호 체크
passwd_check = function() {
	var str1 = $('#mb_password').val();
	var str2 = $('#cfm_password').val();
	if(str1.length > 1) {

		$('#mb_password_error').css('display', 'none');

		if(pass_string_check(str1)==true) {
			$('#mb_password_error').css('display', 'block');
			$('#mb_password_error').html('<span style="color:green">형식에 적합한 비밀번호 입니다.</span>');
		}
		else if(pass_string_check(str1)==false) {
			$('#mb_password_error').css('display', 'block');
			$('#mb_password_error').html('<span style="color:red">'+pw_describe+'</span>');
		}
		else {
			//$('#mb_password_error').empty();
		}
	}
	else {
		//alert('비밀번호를 입력하십시요.');
	}
	if(str2.length > 1) {
			$('#cfm_password_error').css('display', 'none');
		//$('#cfm_password_error').html('');
		if(str1!='' && str2!='') {
			if(str1==str2) {
				$('#cfm_password_error').html('<span style="color:green">비밀번호가 일치합니다.</span>');
				$('#cfm_password_error').css('display', 'block');
			}
			else {
				$('#cfm_password_error').html('<span style="color:red">비밀번호가 일치하지 않습니다.</span>');
				$('#cfm_password_error').css('display', 'block');
			}
		}
	}
};


// 회원 가입
go_submit = function() {
	var f  = document.frmJoin;
	var mb_dupinfo      = f.mb_dupinfo.value;
	var mb_id           = f.mb_id.value;
	var mb_password     = f.mb_password.value;
	var cfm_password    = document.getElementById('cfm_password').value;
	var email           = f.mb_email.value;
	var mb_name         = f.mb_name.value;
	var mb_birth        = f.mb_birth.value;
	var gender          = f.gender.value;
	var hp_comp         = f.hp_comp.value;
	var mb_hp1          = f.mb_hp1.value;
	var mb_hp2          = f.mb_hp2.value;
	var mb_hp3          = f.mb_hp3.value;
	var mb_hp           = trim(mb_hp1) + trim(mb_hp2) + trim(mb_hp3);
	var agree_provision = $("input:checkbox[id='agree_provision']").is(':checked') ? 1 : '';
	var agree_privacy   = $("input:checkbox[id='agree_privacy']").is(':checked') ? 1 : '';
	var agree_marketing = $("input:checkbox[id='agree_marketing']").is(':checked') ? 1 : '';
	var is_sign         = f.is_sign.value;

	var f2 = document.frmAuthRes;
	var _auth_mb_name   = f2.auth_mb_name.value;
	var _auth_mb_hp     = f2.auth_mb_hp.value;

	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if(trim(mb_id)=='')            { alert('ID를 입력 해주세요.'); f.mb_id.focus(); }
	else if(auth_mb_id=='')        { alert('ID 중복체크를 실행 해주세요.'); f.mb_id.focus(); }
	else if(mb_id!=auth_mb_id)	   { alert('ID 중복체크을 통과한 ID와 최종 입력된 아이디가 일치하지 않습니다.\n\nID 체크를 다시 해주세요'); $('#confirm_id').fucus(); }
	else if(trim(mb_password)=='') { alert('비밀번호를 입력 해주세요'); f.mb_password.focus(); }
	else if(pass_string_check(mb_password)==false) { alert('비밀번호는 ' + pw_describe);  f.mb_password.focus(); }
	else if(trim(cfm_password)==''){ alert('비밀번호확인을 입력 해주세요');  $('#cfm_password').focus(); }
	else if(mb_password != cfm_password) { alert('"비밀번호"와 "비밀번호확인"이 다릅니다\n\n비밀번호를 정확하게 입력 해주세요'); $('#cfm_password').focus(); }
	else if(trim(email)=='')       { alert("이메일 주소를 입력하세요"); f.mb_email.focus(); }
	else if(!re.test(email))       { alert("올바른 이메일 주소를 입력하세요"); f.mb_email.focus(); }
	else if(trim(mb_name)=='')     { alert('성명을 입력 해주세요'); f.mb_name.focus(); }
	else if(is_sign != 'Y')        { alert('서비스 이용을 위하여 본인인증을 받으셔야 합니다.'); $('#auth_request').focus(); }
	else if(trim(mb_hp1)=='')      { alert('휴대폰번호를 입력 해주세요'); $('#auth_request').focus(); }
	else if(trim(mb_hp2)=='')      { alert('휴대폰번호를 입력 해주세요'); $('#auth_request').focus(); }
	else if(trim(mb_hp3)=='')      { alert('휴대폰번호를 입력 해주세요'); $('#auth_request').focus(); }

	else if(mb_name != _auth_mb_name) { alert('인증받은 성명이 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); authActivative(); f.mb_name.focus(); }
	else if(mb_hp != _auth_mb_hp)	    { alert('인증받은 휴대폰번호가 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); authActivative(); f.mb_hp1.focus(); }

	else if(!agree_provision)      { alert("서비스 이용약관에 대한 동의가 필요합니다."); $('#agree_provision').focus(); }
	else if(!agree_privacy)        { alert("개인정보처리방침에 대한 동의가 필요합니다."); $('#agree_privacy').focus(); }
	else if(!agree_marketing)      { alert("마케팅 정보 수집 및 활용에 대한 동의가 필요합니다."); $('#agree_marketing').focus(); }
	else {
		if( confirm('개인회원으로 가입하시겠습니까?') ) {

			var ajax_data = $('#frmJoin').serialize();

			/*
			// 파일첨부기능이 있는 폼일 경우
			var ajax_data = new FormData($('#frmJoin')[0]);
			$($('.attachFile')[0].files).each(function(index, file){
				ajax_data.append('attach_file[]', file);
			});
			*/

			$.ajax({
				url : "/member/join_info_proc_p.php",
				type: "POST",
			//processData: false,		// 파일첨부기능이 있는 폼일 경우
			//contentType: false,		// 파일첨부기능이 있는 폼일 경우
				data : ajax_data,
				success: function(data) {
					console.log(data);

					if(data == 'OK') {
						alert('당신의 설레는 내일, 헬로펀딩 가입에 감사드립니다.');

						$.ajax({
							url : cashcow_curl,
							type: "GET",
							data: {'response_idx' : $('#response_idx').val() },
							success: function(data2) {
								//console.log(data2);
								if(data2=='00') { location.reload(); }
								//else if(data2=='01') { alert('캐시카우 중복 이벤트'); }
								else {
									console.log(data2);
								}
							},
							error: function(e) {
								console.log(e);
							}
						});

					}
					else if(data == 'TIME_OVER')          { alert('처리유효시간(10분) 을 초과 하였습니다.'); location.reload(); }
					else if(data == 'DUP_ID')             { alert('등록 하실 수 없는 아이디 입니다.'); }
					else if(data == 'DUP_ID_CHECK_ERROR') { alert('아이디 중복체크를 실행 하십시요.'); }
					else if(data == 'DUP_HP')             { alert('등록된 휴대폰 번호 정보가 있습니다.\n\n계정 정보는 헬로펀딩 로그인 페이지의 `아이디 찾기`, `비밀번호 찾기`등을 이용하여 확인 하실 수 있습니다.'); }
					else if(data == 'DUP_EMAIL')          { alert('등록된 이메일주소 정보가 있습니다.\n\n계정 정보는 헬로펀딩 로그인 페이지의 `아이디 찾기`, `비밀번호 찾기`등을 이용하여 확인 하실 수 있습니다.'); }
					else                                  { alert('시스템 오류 입니다. 고객센터로 문의 하십시요.'); }

				},
				error: function(jqXHR, textStatus, errorThrown) {
					//console.log(jqXHR);
					//console.log(textStatus);
					//console.log(errorThrown);
					alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시요.');
				}
			});

		}
	}
}

// 본인인증 재활성화
function authActivative() {
	$("#auth_request").html('인증요청');
	$("#auth_request").attr('disabled',false);
	$("#auth_num").css('display','block');
	$("#auth_submit").css('display','block');
	//$("#provision_zone").css('display','block');
}