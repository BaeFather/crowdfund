
function selectGender(sel) {
	document.frmJoin.gender.value = sel;
	if(sel=='m') {
		$('#gender_btn1').css('border','1px solid #02a0e9').css('color','#02a0e9');
		$('#gender_btn2').css('border','1px solid #ddd').css('color','#444');
	}
	else if(sel=='w') {
		$('#gender_btn1').css('border','1px solid #ddd').css('color','#444');
		$('#gender_btn2').css('border','1px solid #02a0e9').css('color','#02a0e9');
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

	$('#auth_error1').empty();
	$('#auth_error2').empty();
	$('#hp_comp_error').empty();
	$('#mb_hp_error').empty();

	var f  = document.frmJoin;
	var f2 = document.frmAuthRes;

	_mb_name  = $.trim(f.mb_name.value);
	_mb_birth = $.trim(f.mb_birth.value);
	_mb_hp1   = $.trim(f.mb_hp1.value);
	_mb_hp2   = $.trim(f.mb_hp2.value);
	_mb_hp3   = $.trim(f.mb_hp3.value);

	if(_mb_name=='') { $('#auth_error1').html('성명을 입력해 주세요.'); f.mb_name.focus(); return; }
	if(_mb_birth=='' || _mb_birth.length!=8) { $('#auth_error2').html('생년월일 8자리를 입력해 주세요.'); f.mb_birth.focus(); return; }
	if($('#full_age').val() < 19) { $('#auth_error2').html('가입연령제한 : 만19세 미만의 미성년자는 가입이 되지 않습니다.'); f.mb_birth.focus(); return; }
	if(f.gender.value=='') { $('#auth_error2').html('성별을 선택해 주세요.'); $(' #gender_btn1').focus(); return; }
	if(f.hp_comp.value=='') { $('#hp_comp_error').html('통신사를 선택해 주세요.'); f.hp_comp.focus(); return; }
	if(_mb_hp1=='') { $('#mb_hp_error').html('휴대폰번호를 입력해 주세요.'); f.mb_hp1.focus(); return; }
	if(_mb_hp2=='') { $('#mb_hp_error').html('휴대폰번호를 입력해 주세요.'); f.mb_hp2.focus(); return; }
	if(_mb_hp3=='') { $('#mb_hp_error').html('휴대폰번호를 입력해 주세요.'); f.mb_hp3.focus(); return; }

	if($("input:checkbox[id='prov1']").is(":checked")==false) { alert('`개인정보이용`에 동의하여 주십시요.');     $('#prov1').focus(); return; }
	if($("input:checkbox[id='prov2']").is(":checked")==false) { alert('`고유식별정보처리`에 동의하여 주십시요.'); $('#prov2').focus(); return; }
	if($("input:checkbox[id='prov3']").is(":checked")==false) { alert('`서비스이용약관`에 동의하여 주십시요.');   $('#prov3').focus(); return; }
	if($("input:checkbox[id='prov4']").is(":checked")==false) { alert('`통신사이용약관`에 동의하여 주십시요.');   $('#prov4').focus(); return; }
	if($('#hp_comp').val() > 3) {
		if($("input:checkbox[id='prov5']").is(":checked")==false) { alert('`개인정보 제3자 제공`에 동의하여 주십시요.'); $('#prov5').focus(); return; }
	}

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

	//console.log(send_data);


	f.is_sign.value    = "";
	f.mb_dupinfo.value = "";

	$.ajax({
			url  : "/member/ajax.auth1.php",
			type : "post",
			data : send_data,
			success : function(data) {

				//console.log(data);

				res = JSON.parse(data);

				if(res.rescode=="0000") {

					f.mb_dupinfo.value = res.resseq;
					f.mb_reqnum.value = res.reqseq;

					if (res.already=="Y") {
						f.is_sign.value = "Y";
						alert("본인 인증 되었습니다.");
						$("#auth_request").html('인증완료');
						$("#auth_request").attr('disabled',true);
						$("#auth_num").css('display','none');
						$("#auth_submit").css('display','none');
						$("#provision_zone").css('display','none');
						$("#mb_ci").val(res.ci);

						if(parseInt(res.mkd) > 0)	//탈퇴이력체크
						{
							$("#nameDiv").css("display","none");
							$("#nameDivTxt").css("display","block");
						}
						return;
					}
					else {
						if (res.checked=="Y") {
							f.is_sign.value = "Y";
							alert("본인 인증 되었습니다.");
							$("#auth_request").html('인증완료');
							$("#auth_request").attr('disabled',true);
							$("#auth_num").css('display','none');
							$("#auth_submit").css('display','none');
							$("#provision_zone").css('display','none');

							if(parseInt(res.mkd) > 0)	//탈퇴이력체크
							{
								$("#nameDiv").css("display","none");
								$("#nameDivTxt").css("display","block");
							}
							return;
						}
						else {
							alert("인증번호를 입력해 주세요."); f.auth_num.focus();
						}
					}
				}
				else if(res.rescode=="DUPLICATE") {
					alert('등록된 휴대폰 번호 정보가 있습니다.\n\n계정 정보는 로그인 페이지의 `아이디 찾기`, `비밀번호 찾기`등을 이용하여 확인 하실 수 있습니다.');
					location.href='/bbs/login.php';
					return;
				}
				else {

					$('#auth_request').attr('disabled', false);

					msg = "휴대폰 본인 인증 실패\n\n";
					msg+= "[ ERROR CODE: " + res.rescode + " ]";
					if(res.rescode=="0001")      msg+= "\n휴대폰 명의자 정보 불일치\n - 통신사선택오류\n - 생년월일/성명/휴대폰번호 불일치\n - 휴대폰일시정지\n - 선불폰가입자\n - SMS발송실패\n - 인증문자불일치 등의 사유";
					else if(res.rescode=="0003") msg+= "\n기타인증오류";
					else if(res.rescode=="0010") msg+= "\n인증번호 불일치(소켓)";
					else if(res.rescode=="0012") msg+= "\n요청정보오류(입력값오류)";
					else if(res.rescode=="0013") msg+= "\n암호화 시스템 오류";
					else if(res.rescode=="0014") msg+= "\n암호화 처리 오류";
					else if(res.rescode=="0015") msg+= "\n암호화 데이터 오류";
					else if(res.rescode=="0016") msg+= "\n복호화 처리 오류";
					else if(res.rescode=="0017") msg+= "\n복호화 데이터 오류";
					else if(res.rescode=="0018") msg+= "\n통신오류";
					else if(res.rescode=="0019") msg+= "\n데이터베이스 오류";
					else if(res.rescode=="0020") msg+= "\n유효하지않은 CP코드";
					else if(res.rescode=="0021") msg+= "\n중단된 CP코드";
					else if(res.rescode=="0022") msg+= "\n휴대전화본인확인 사용불가 CP코드";
					else if(res.rescode=="0023") msg+= "\n미등록 CP코드";
					else if(res.rescode=="0031") msg+= "\n유효한 인증이력 없음";
					else if(res.rescode=="0035") msg+= "\n기인증완료건(소켓)";
					else if(res.rescode=="0040") msg+= "\n본인확인차단고객(통신사)";
					else if(res.rescode=="0041") msg+= "\n인증문자발송차단고객(통신사)";
					else if(res.rescode=="0050") msg+= "\nNICE 명의보호서비스 이용고객 차단";
					else if(res.rescode=="0052") msg+= "\n부정사용차단";
					else if(res.rescode=="0070") msg+= "\n간편인증앱 미설치";
					else if(res.rescode=="0071") msg+= "\n앱인증 미완료";
					else if(res.rescode=="0072") msg+= "\n간편인증 처리중 오류";
					else if(res.rescode=="0073") msg+= "\n간편인증앱 미설치(LG U+ Only)";
					else if(res.rescode=="0074") msg+= "\n간편인증앱 재설치필요";
					else if(res.rescode=="0075") msg+= "\n간편인증사용불가-스마트폰아님";
					else if(res.rescode=="0076") msg+= "\n간편인증앱 미설치";
					else if(res.rescode=="0078") msg+= "\n14세 미만 인증 오류";
					else if(res.rescode=="0079") msg+= "\n간편인증 시스템 오류";
					else if(res.rescode=="9097") msg+= "\n인증번호 3회 불일치";
					else                         msg+= "\n기타오류";

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

	var auth_num = $("#auth_num").val();

	if (auth_num=='') {
		alert("먼저 인증요청을해 주세요.");
		return false;
	}

	var mb_hp1   = $("#mb_hp1").val();
	var mb_hp2   = $("#mb_hp2").val();
	var mb_hp3   = $("#mb_hp3").val();

	var mb_hp = mb_hp1 + mb_hp2 + mb_hp3;

	var send_data = new Object();
	send_data.Authno      = auth_num;
	send_data.ResponseSeq = $("#mb_dupinfo").val();
	send_data.RequestSeq  = $("#mb_reqnum").val();
	send_data.Mhp  				= mb_hp;
	send_data.Mname				= $("#mb_name").val();

	$.ajax({
			url  : "/member/ajax.auth2.php",
			type : "post",
			data : send_data,
			success : function(data) {

				//console.log(data);

				res = JSON.parse(data);

				if (res.rescode=="0000") {
					$("#is_sign").val("Y");
					$("#auth_request").html('인증완료');
					$("#auth_request").attr('disabled',true);
					$("#auth_num").css('display','none');
					$("#auth_submit").css('display','none');
					$("#mb_ci").val(res.ci);
					alert("본인 인증 되었습니다.");

					if(parseInt(res.mkd) > 0)	//탈퇴이력체크
					{
						$("#nameDiv").css("display","none");
						$("#nameDivTxt").css("display","block");
					}
					return;
				}
				else {

					msg = "휴대폰 본인 인증 실패\n\n";
					msg+= "[ ERROR CODE: " + res.rescode + " ]";
					if(res.rescode=="0001")       msg+= "\n인증번호 불일치";
					else if(res.rescode=="0031")	msg+= "\n응답 고유번호 확인 불가";
					else if(res.rescode=="0032")	msg+= "\n주민번호 불일치";
					else if(res.rescode=="0033")	msg+= "\n요청 고유번호 불일치";
					else if(res.rescode=="0033")	msg+= "\n기 인증 완료 건";

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

function add_file_zone(obj_cval) {
	var cval = obj_cval.value;
	if(cval=='2') {
		$('#describe01').css('color','#3366FF');
		$('#describe02').css('color','#CCCCCC');
	}
	else if(cval=='3') {
		$('#describe01').css('color','#CCCCCC');
		$('#describe02').css('color','#3366FF');
	}

	(cval=='2' || cval=='3')  ? $('#file_zone').show() : $('#file_zone').hide();
}

var msg = "아래 요건 중 한가지를 만족하는 경우 해당 <br><br> 1) 이자, 배당소득이 2천만원 초과 <br> 2) 사업, 근로소득이 1억원 초과";
$('#question_1').webuiPopover({ title: "소득적격 투자자 조건", content: msg, closeable: true, width: 330, height: 100, arrow: false, offsetTop: 5, offsetLeft: 70, trigger: "click", placement: 'bottom', backdrop: false});

var msg = "아래 요건을 모두 만족하는 경우 해당<br><br> 1) 금융투자업회사에서 계좌 개설 1년 이상 경과 <br> 2) 금융투자상품 잔고 5억원 이상 <br> 3) 소득액 1억원 또는 재산가액 10억원 이상";
$('#question_2').webuiPopover({ title: "전문 투자자 조건", content: msg, closeable: true, width: 330, height: 100, arrow: false, offsetTop: 5, offsetLeft: 50, trigger: "click", placement: 'bottom', backdrop: false});

$('input:radio[name=member_investor_type]').click(function() {
	var cval = $('input:radio[name=member_investor_type]:checked').val();
	if(cval=='2') {
		$('#describe01').css('color','#3366FF');
		$('#describe02').css('color','#CCCCCC');
	}
	else if(cval=='3') {
		$('#describe01').css('color','#CCCCCC');
		$('#describe02').css('color','#3366FF');
	}
});

$('input:radio[name=member_investor_type], #extend_btn').click(function() {
	var cval = $('input:radio[name=member_investor_type]:checked').val();
	(cval=='2' || cval=='3')  ? $('#file_zone').show() : $('#file_zone').hide();
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
	temp += "<ul>";
	temp += "  <li><input type='file' class='attachFile' name='attach_file[]'> <input type='text' name='memo[]' class='file_text' placeholder='첨부서류 간략설명'></li>";
	temp += "</ul>";
	new_td.innerHTML = temp;
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

	$('#mb_id_error').empty();

	if(mb_id) {
		if( id_string_check(mb_id) ) {

			$.ajax({
				url : "/member/confirm_id.php",
				type: "post",
				data : {'prm1':mb_id},
				success: function(data) {
					$('#ajax_return_txt').val(data);
					if(data=='o')	     { $('#mb_id_error').html('<span style="color:green">사용 가능한 아이디 입니다.</span>'); auth_mb_id = mb_id; }
					else if(data=='x') { $('#mb_id_error').html('<span style="color:red">사용 하실 수 없는 아이디 입니다.</span>'); }
					else			         { alert('시스템 오류 입니다. 고객센터로 문의 하십시요.'); }
				},
				error: function () {
					alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시요.');
				}
			});

		}
		else {
			$('#mb_id_error').html('영문 첫글자로, 영문 또는 영문.숫자 조합, 6~15자리 입니다.');
		}
	}
	else {
		$('#mb_id_error').html('ID를 입력해 주세요.');
	}
}


// 암호 체크
passwd_check = function() {
	var str1 = $.trim($('#mb_password').val());
	var str2 = $.trim($('#cfm_password').val());

	$('#mb_password_error').empty();
	$('#cfm_password_error').empty();

	if(str1) {
		if(str1.length > 0) {
			if(pass_string_check(str1)==true) {
				$('#mb_password_error').html('<span style="color:green">형식에 적합한 비밀번호 입니다.</span>');
			}
			else if(pass_string_check(str1)==false) {
				$('#mb_password_error').html(pw_describe);
			}
		}
		else {
			$('#mb_password_error').html('비밀번호를 입력해 주세요.');
		}
	}

	if(str1 && str2) {
		if(str2.length > 0) {
			if(str1!='' && str2!='') {
				if(str1==str2) {
					$('#cfm_password_error').html('<span style="color:green">비밀번호가 일치합니다.</span>');
				}
				else {
					$('#cfm_password_error').html('비밀번호가 일치하지 않습니다.');
				}
			}
		}
		else {
			$('#cfm_password_error').html('비밀번호를 한번 더 입력해 주세요.');
		}
	}
}


// 이메일 체크
var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

email_check = function() {
	var str = $('#mb_email').val();
	if(str.length > 1) {
		if(!re.test(str)) {
			$('#mb_email_error').html('올바른 이메일 주소를 입력해 주세요.');
		}
		else {
			$('#mb_email_error').empty();
		}
	}
	else {
		$('#mb_email_error').html('이메일 주소를 입력해 주세요.');
	}
}


// 회원 가입
go_submit = function() {
	var f = document.frmJoin;

	var mb_dupinfo      = $.trim(f.mb_dupinfo.value);
	var mb_ci           = $.trim(f.mb_ci.value);
	var mb_id           = $.trim(f.mb_id.value);
	var mb_password     = $.trim(f.mb_password.value);
	var cfm_password    = $.trim($('#cfm_password').val());
	var email           = $.trim(f.mb_email.value);
	var mb_name         = $.trim(f.mb_name.value);
	var mb_birth        = $.trim(f.mb_birth.value);
	var gender          = $.trim(f.gender.value);
	var hp_comp         = $.trim(f.hp_comp.value);
	var mb_hp1          = $.trim(f.mb_hp1.value);
	var mb_hp2          = $.trim(f.mb_hp2.value);
	var mb_hp3          = $.trim(f.mb_hp3.value);
	var mb_hp           = mb_hp1 + mb_hp2 + mb_hp3;

	var agree_provision  = $("input:checkbox[id='agree_provision']").is(':checked') ? 1 : '';			// 서비스 이용약관
	var agree_provision2 = $("input:checkbox[id='agree_provision2']").is(':checked') ? 1 : '';		// 온라인연계투자약관
	var agree_usecredit  = $("input:checkbox[id='agree_usecredit']").is(':checked') ? 1 : '';			// 개인(신용)정보 수집 및 이용
	var agree_3rdparty   = $("input:checkbox[id='agree_3rdparty']").is(':checked') ? 1 : '';			// 개인정보 제3자 제공
	var agree_identify   = $("input:checkbox[id='agree_identify']").is(':checked') ? 1 : '';			// 고유식별정보 처리

	var is_sign         = f.is_sign.value;
	var pid							=	f.pid.value;


	var f2 = document.frmAuthRes;
	var _auth_mb_name   = f2.auth_mb_name.value;
	var _auth_mb_hp     = f2.auth_mb_hp.value;

	if(mb_id == '') { $('#mb_id_error').html('ID를 입력해 주세요.'); f.mb_id.focus(); }
	else if(auth_mb_id == '') { $('#mb_id_error').html('ID 체크를 실행해 주세요.</span>'); f.mb_id.focus(); }
	else if(mb_id != auth_mb_id) { $('#mb_id_error').html('ID 체크를 다시 실행해 주세요.</span>'); $('#confirm_id').focus(); }
	else if(mb_password == '') { $('#mb_password_error').html('비밀번호를 입력해 주세요.'); f.mb_password.focus(); }
	else if(pass_string_check(mb_password) == false) { $('#mb_password_error').html(pw_describe);  f.mb_password.focus(); }
	else if(cfm_password == '') { $('#cfm_password_error').html('비밀번호확인을 입력해 주세요.');  $('#cfm_password').focus(); }
	else if(mb_password != cfm_password) { $('#cfm_password_error').html('비밀번호가 일치하지 않습니다.'); $('#cfm_password').focus(); }
	else if(email == '') { $('#mb_email_error').html('이메일 주소를 입력해 주세요.'); f.mb_email.focus(); }
	else if(!re.test(email)) { $('#mb_email_error').html('올바른 이메일 주소를 입력해 주세요.'); f.mb_email.focus(); }

	else if(is_sign != 'Y') { alert('서비스 이용을 위하여 본인인증을 받으셔야 합니다.'); location.href='#authFocus'; }

	else if(mb_name == '') { $('#auth_error1').html('성명을 입력해 주세요.'); f.mb_name.focus(); }
	else if(mb_hp1 == '') { $('#mb_hp_error').html('휴대폰번호를 입력해 주세요.'); f.mb_hp1.focus(); }
	else if(mb_hp2 == '') { $('#mb_hp_error').html('휴대폰번호를 입력해 주세요.'); f.mb_hp2.focus(); }
	else if(mb_hp3 == '') { $('#mb_hp_error').html('휴대폰번호를 입력해 주세요.'); f.mb_hp3.focus(); }

	else if(mb_name != _auth_mb_name) { alert('인증받은 성명이 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); authActivative(); f.mb_name.focus(); }
	else if(mb_hp != _auth_mb_hp) { alert('인증받은 휴대폰번호가 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); authActivative(); f.mb_hp1.focus(); }

	else if(!agree_provision) { alert("서비스 이용약관에 대한 동의가 필요합니다."); $('#agree_provision').focus(); }
	else if(!agree_provision2) { alert("온라인연계투자약관에 대한 동의가 필요합니다."); $('#agree_provision2').focus(); }
	else if(!agree_usecredit) { alert("개인(신용)정보 수집 및 이용에 대한 동의가 필요합니다."); $('#agree_usecredit').focus(); }
	else if(!agree_3rdparty) { alert("개인정보 제3자 제공에 대한 동의가 필요합니다."); $('#agree_3rdparty').focus(); }
	else if(!agree_identify) { alert("고유식별정보 처리에 대한 동의가 필요합니다."); $('#agree_identify').focus(); }

	else {
		if( confirm('개인회원으로 가입하시겠습니까?') ) {

			var ajax_data = new FormData($('#frmJoin')[0]);

			//$($('.attachFile')[0].files).each(function(index, file) { ajax_data.append('attach_file[]', file); });

			$.ajax({
				url : "/member/join_info_proc_p.php",
				type: "POST",
				processData: false,
				contentType: false,
				data : ajax_data,
				success: function(data) {

					//console.log(data);

					if(data == 'OK') {
						// 정상 가입 처리 ------------------------------------------------------------------

						if(pid == "naverpay") {
							alert("네이버페이 이벤트에 정상 응모 되셨습니다.\n\n당신의 설레는 내일, 헬로펀딩에 오신것을 환영합니다.");
					  }
						else {
							alert('정상 회원 가입 되셨습니다.\n\n당신의 설레는 내일, 헬로펀딩에 오신것을 환영합니다.');
					  }

						f2 = document.flogin;
						f2.method = 'post';
						f2.action = '/bbs/login_check.php';
						f2.mb_id.value = trim(mb_id);
						f2.mb_password.value = trim(mb_password);
						f2.submit();
						// 정상 가입 처리 ------------------------------------------------------------------
					}
					else if(data=='TIME_OVER')          { alert('처리유효시간(10분) 을 초과 하였습니다.'); location.reload(); }
					else if(data=='DUP_ID')             { alert('등록 하실 수 없는 아이디 입니다.'); }
					else if(data=='DUP_ID_CHECK_ERROR') { alert('아이디 중복체크를 실행 하십시요.'); }
					else if(data=='DUP_CI')             { alert('등록된 회원정보가 있습니다.\n\n계정 정보는 로그인 페이지의 `아이디 찾기`, `비밀번호 찾기`등을 이용하여 확인 하실 수 있습니다.'); }
					else if(data=='DUP_HP')             { alert('등록된 휴대폰 번호 정보가 있습니다.\n\n계정 정보는 로그인 페이지의 `아이디 찾기`, `비밀번호 찾기`등을 이용하여 확인 하실 수 있습니다.'); }
					else if(data=='DUP_EMAIL')          { alert('등록된 이메일주소 정보가 있습니다.\n\n계정 정보는 로그인 페이지의 `아이디 찾기`, `비밀번호 찾기`등을 이용하여 확인 하실 수 있습니다.'); }
					else if(data=='RECOMMEND_DUP_IP')   { alert('동일한 추천인 아이디를 등록한 IP 기록이 존재하여 추천인을 등록 하실 수 없습니다.'); $('rec_mb_id').val(''); $('#rec_flag').val(''); }
					else if(data=='RECOMMEND_RE_JOIN_USER') { alert('이벤트 기간중 가입된 후 탈퇴한 이력이 있어 가입이 불가 합니다. 추천인 이벤트가 종료된 이후 가입 가능합니다.'); }
					else if(data=='RECOMMEND_REJECT')		{ alert('동일한 이벤트에 응모한 기록이 존재하여 추천인을 등록하실 수 없습니다. 입력하신 추천인ID는 자동 삭제됩니다.'); $('rec_mb_id').val(''); $('#rec_flag').val(''); }
					else if(data=='REJECTED_HOST')		  { alert('내부정책에 의하여 가입이 거부 되었습니다. 고객센터로 문의 하십시요.'); }
					else if(data=='LIMIT_AGE_UNDER')		{ alert('마케팅 정책에 의하여 만19세 미만은 가입이 가입이 되지 않습니다.'); }
					else                                { alert('시스템 오류 입니다. 고객센터로 문의 하십시요.\n\n오류코드: ' + data); }

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
	$("#provision_zone").css('display','block');
}
