//아이디 체크
function auth_id(f_name) {

	var f = document.forms[f_name];
	var mb_id = $.trim(f.c_mb_id.value);

	// 공백 체크
	if(mb_id=='') { alert('ID를 입력 하십시요.'); f.c_mb_id.focus(); }

	else {

		if (!id_string_check(mb_id)) { alert("올바른 ID 형식이 아닙니다."); }
		else {

			$.ajax({
				url : "/member/confirm_id.php",
				type: "POST",
				data : {'prm1':mb_id},
				success: function(data) {
					if(data=='o')	   
					{ 
						alert('사용 가능한 아이디 입니다.'); 
						if      (f_name=="frmJoin1") auth_mb_id1 = mb_id; 
						else if (f_name=="frmJoin2") auth_mb_id2 = mb_id; 
					} 
					else if(data=='x') { alert('사용 하실 수 없는 아이디 입니다.'); }
					else			   { alert('시스템 오류 입니다. 고객센터로 문의 하십시요.'); }
				},
				error: function () {
					alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시요.');
				}
			})

		}
	}

}


function go_submit(f_name) {

	var f = document.forms[f_name];

	var mb_id = f.c_mb_id.value;
	var mb_password     = f.c_mb_password.value;
	var email           = f.c_mb_email.value;
	var mb_name         = f.c_mb_name.value;
	var mb_birthday     = f.c_mb_birthday.value;
	var mb_sex          = f.c_mb_sex.value;
	var hp_comp         = f.c_hp_comp.value;
	var mb_hp1          = f.c_mb_hp1.value;
	var mb_hp2          = f.c_mb_hp2.value;
	var mb_hp3          = f.c_mb_hp3.value;
	var mb_hp           = f.c_mb_hp1.value + f.c_mb_hp2.value + f.c_mb_hp3.value;
	var is_sign         = f.c_is_sign.value;
	var agree_provision = f.c_agree_provision.checked ? 1 : '';
	var agree_privacy   = f.c_agree_privacy.checked ? 1 : '';
	var agree_marketing = f.c_agree_marketing.checked ? 1 : '';
	var syndi_id        = f.syndi_id.value;
	var tvtalk_id       = f.tvtalk_id.value;

	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if($.trim(mb_id)=='')            { alert('ID를 입력 해주세요.'); f.c_mb_id.focus(); return; }

	else if (f_name=="frmJoin1" && auth_mb_id1=="") {alert('ID 중복체크를 실행 해주세요.'); return; }
	else if (f_name=="frmJoin1" && mb_id!=auth_mb_id1) {alert('ID 가 변경되었습니다..\nID 체크를 다시 해주세요.'); return; }
	else if (f_name=="frmJoin2" && auth_mb_id2=="") {alert('ID 중복체크를 실행 해주세요.'); return; }
	else if (f_name=="frmJoin2" && mb_id!=auth_mb_id2) {alert('ID 가 변경되었습니다..\nID 체크를 다시 해주세요'); return; }

	else if($.trim(mb_password)=='') { alert('비밀번호를 입력 해주세요'); f.c_mb_password.focus();return; }
	else if(pass_string_check(mb_password)==false) { alert('비밀번호는 영문,숫자 조합으로 4~16자 이내로 해야 합니다.');  f.c_mb_password.focus(); return; }
	else if($.trim(email)=='')       { alert("이메일 주소를 입력하세요"); f.c_mb_email.focus();return; }
	else if(!re.test(email))         { alert("올바른 이메일 주소를 입력하세요"); f.c_mb_email.focus(); return; }
	else if($.trim(mb_name)=='')     { alert('이름을 입력 해주세요'); f.c_mb_name.focus(); return; }
	else if($.trim(mb_birthday)=='' || mb_birthday.length!=8) { alert('생년월일 8자리를 입력 해주세요. 예)19880413'); f.c_mb_birthday.focus(); return; }
	else if($.trim(mb_sex)=='')      { alert('성별을 선택해 주세요'); return;}
	else if($.trim(hp_comp)=='')     { alert('통신사를 선택해 주세요'); f.c_hp_comp.focus(); return; }
	else if($.trim(mb_hp1)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp1.focus(); return; }
	else if($.trim(mb_hp2)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp2.focus(); return; }
	else if($.trim(mb_hp3)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp3.focus(); return; }

	else if(is_sign != 'Y')          { alert('서비스 이용을 위하여 본인인증을 받으셔야 합니다.'); return; }
	else if(mb_birthday != f.c_auth_birthday.value)	    { alert('인증받은 생년월일이 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); return; }
	else if(hp_comp != f.c_auth_telecom.value)	    { alert('인증받은 통신사가 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); return; }
	else if(mb_hp != f.c_auth_mb_hp.value)	    { alert('인증받은 휴대폰번호가 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); return; }
	else if(mb_name != f.c_auth_mb_name.value)  { alert('인증받은 성명이 변경 되었습니다\n\n본인 확인을 다시 실행하셔야 합니다.'); return; } 

	else if(!agree_provision)      { alert("서비스 이용약관에 대한 동의가 필요합니다."); f.c_agree_provision.focus(); return; }
	else if(!agree_privacy)        { alert("개인정보처리방침에 대한 동의가 필요합니다."); f.c_agree_privacy.focus(); return; }

	else {

		if (confirm('회원으로 가입하시겠습니까?')) {
			$.ajax({
				url  : "simple_join_proc.php",
				type : "POST",
				data : $('[name="'+f_name+'"]').serialize(),
				//dataType : "json",
				success : function(data) {
					//console.log(data);
					//if (data.sql_res) {};
					if (data == 'OK'){ 
						document.getElementById('tvtalk_hidden').src = "http://m.tvtalk.tv/partner_proc.php?tvtalkid="+tvtalk_id+"&helloid="+mb_id;
						alert("회원가입이 완료되었습니다."); 
						//self.location.href="http://m.tvtalk.tv/partner_proc.php?tvtalkid="+tvtalk_id+"&helloid="+mb_id;
						self.location.href = "https://www.hellofunding.co.kr";
						/*
						setTimeout( function() {
							self.location.href = "https://www.hellofunding.co.kr";
						}, 2000);
						*/
					} 
					else if(data == 'DUP_ID')             { alert('등록 하실 수 없는 아이디 입니다.'); }
					else if(data == 'DUP_HP')             { alert('이미 등록된 휴대폰 번호 정보가 있습니다.'); }
					else                                  { alert('회원 가입중 오류가 발생하였습니다.'); }

				},
				error : function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR);
					console.log(textStatus);
					console.log(errorThrown);
				}
			});
		}

	}

}


function fnPopup(f_name) {

	var f = document.forms[f_name];

	if ($.trim(f.c_mb_name.value)=='')     { alert('이름을 입력 해주세요'); f.c_mb_name.focus();return; }
	if ($.trim(f.c_mb_birthday.value)=='') { alert('생년월일 8자리를 입력 해주세요. 예)19880413'); f.c_mb_birthday.focus();return; }
	if ($.trim(f.c_mb_sex.value)=='')      { alert('성별을 선택해 주세요.'); return; }
	if ($.trim(f.c_hp_comp.value)=='')     { alert('통신사를 선택해 주세요'); f.c_hp_comp.focus();return; }
	if ($.trim(f.c_mb_hp1.value)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp1.focus(); return;}
	if ($.trim(f.c_mb_hp2.value)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp2.focus(); return;}
	if ($.trim(f.c_mb_hp3.value)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp3.focus(); return;}

	f.c_auth_mb_hp.value = f.c_mb_hp1.value + f.c_mb_hp2.value + f.c_mb_hp3.value;
	f.c_auth_mb_name.value = f.c_mb_name.value;
	var m_sex="";
	if (f.c_mb_sex.value == "M") m_sex = "1";
	else if (f.c_mb_sex.value == "F") m_sex = "2";


	var send_data = new Object();
	send_data.Mbirth = f.c_mb_birthday.value + m_sex ;
	send_data.Mname = f.c_mb_name.value;
	send_data.Mhp = f.c_auth_mb_hp.value;
	send_data.Mhpcomp = f.c_hp_comp.value;

	//console.log(send_data);

	$.ajax({
			url  : "auth_proc.php",
			type : "POST",
			data : send_data,
			
			success : function(data) {
				//console.log(data);
				
				res = JSON.parse(data);
				//console.log(res.getReturnCode);

				if (res.getReturnCode=="0000")
				{
					f.c_is_sign.value = "Y";
					f.c_mb_dupinfo.value = "11111";
					alert("인증되었습니다.");
				}

			},
			error : function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
	});

}

function NiceSock(f_name) {

	var f = document.forms[f_name];

	if ($.trim(f.c_mb_name.value)=='')     { alert('이름을 입력 해주세요'); f.c_mb_name.focus();return; }
	if ($.trim(f.c_mb_birthday.value)=='' || f.c_mb_birthday.value.length!=8) { alert('생년월일 8자리를 입력 해주세요. 예)19880413'); f.c_mb_birthday.focus();return; }
	if ($.trim(f.c_mb_sex.value)=='')      { alert('성별을 선택해 주세요.'); return; }
	if ($.trim(f.c_hp_comp.value)=='')     { alert('통신사를 선택해 주세요'); f.c_hp_comp.focus();return; }
	if ($.trim(f.c_mb_hp1.value)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp1.focus(); return;}
	if ($.trim(f.c_mb_hp2.value)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp2.focus(); return;}
	if ($.trim(f.c_mb_hp3.value)=='')      { alert('휴대폰번호를 입력 해주세요'); f.c_mb_hp3.focus(); return;}

	f.c_auth_mb_hp.value = f.c_mb_hp1.value + f.c_mb_hp2.value + f.c_mb_hp3.value;
	f.c_auth_mb_name.value = f.c_mb_name.value;
	f.c_auth_birthday.value = f.c_mb_birthday.value;
	f.c_auth_telecom.value = f.c_hp_comp.value;
	var m_sex="";

	if (f.c_mb_birthday.value.substring(0,2)=="19") {
		if (f.c_mb_sex.value == "M") m_sex = "1";
		else if (f.c_mb_sex.value == "F") m_sex = "2";
	} else if (f.c_mb_birthday.value.substring(0,2)=="20") {
		if (f.c_mb_sex.value == "M") m_sex = "3";
		else if (f.c_mb_sex.value == "F") m_sex = "4";
	}
	//alert(m_sex);

	var send_data = new Object();
	send_data.Mbirth = f.c_mb_birthday.value + m_sex ;
	send_data.Msex  = m_sex;
	send_data.Mname = f.c_mb_name.value;
	send_data.Mhp = f.c_auth_mb_hp.value;
	send_data.Mhpcomp = f.c_hp_comp.value;

	//console.log(send_data);

	f.c_is_sign.value = "";
	f.c_mb_dupinfo.value = "";


	$.ajax({
			url  : "auth_proc.php",
			type : "POST",
			data : send_data,
			success : function(data) {
				console.log(data);
				
				res = JSON.parse(data);

				if (res.getReturnCode=="0000")
				{
					f.c_mb_dupinfo.value = res.res_seq;
					f.c_mb_reqnum.value = res.req_seq;

					
					if (res.already=="Y") {
						f.c_is_sign.value = "Y";
						if (f.name=="frmJoin1") {
							document.getElementById("span_confirm11").style.display = "none";
							document.getElementById("span_confirm12").style.display = "none";
						} else if (f.name=="frmJoin2") {
							document.getElementById("span_confirm21").style.display = "none";
							document.getElementById("span_confirm22").style.display = "none";
						}
						alert("인증되었습니다.");
						return;
					}
					

					if (res.checked=="Y") {
						f.c_is_sign.value = "Y";

						alert("인증되었습니다.");
					} else {
						alert("인증번호를 입력해 주세요.");
						f.c_authnum.focus();
					}
				} else {
					alert("본인인증에 실패하였습니다.");
				}

			},
			error : function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
	});

}

function NiceSock2(f_name) {
	var f = document.forms[f_name];


	if (!f.c_authnum.value) {
		alert("먼저 인증요청을 해주세요.");
		return false;
	}

	var send_data = new Object();
	send_data.Authno = f.c_authnum.value;
	send_data.ResponseSeq = f.c_mb_dupinfo.value;
	send_data.RequestSeq  = f.c_mb_reqnum.value;

	$.ajax({
			url  : "auth_proc2.php",
			type : "POST",
			data : send_data,
			success : function(data) {
				//console.log(data);
				res = JSON.parse(data);

				if (res.getReturnCode=="0000")
				{
					f.c_is_sign.value = "Y";
					alert("인증되었습니다.");
				} else {
					alert("본인인증에 실패하였습니다.");
				}

			},
			error : function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
	});
}

function nicePopup(f_name) {

	var f = document.forms[f_name];

	popupChk = window.open('', 'popupChk', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
	document.form_chk.action = "https://nice.checkplus.co.kr/CheckPlusSafeModel/checkplus.cb";
	document.form_chk.target = "popupChk";
	document.form_chk.param_r1.value = f_name;
	document.form_chk.param_r2.value = f.c_hp_comp.value;
	document.form_chk.submit();
}

function select_sex1(sel) {

	if (sel=="M")
	{
		document.frmJoin1.c_mb_sex.value = sel;
		$('#sex_btn1_1').css('background-color','#252525');
		$('#sex_btn1_2').css('background-color','white');
	} 
	else if (sel=="F")
	{
		document.frmJoin1.c_mb_sex.value = sel;
		$('#sex_btn1_1').css('background-color','white');
		$('#sex_btn1_2').css('background-color','#252525');
	}

}

function select_sex2(sel) {

	if (sel=="M")
	{
		document.frmJoin2.c_mb_sex.value = sel;
		$('#sex_btn2_1').css('background-color','#252525');
		$('#sex_btn2_2').css('background-color','white');
	} 
	else if (sel=="F")
	{
		document.frmJoin2.c_mb_sex.value = sel;
		$('#sex_btn2_1').css('background-color','white');
		$('#sex_btn2_2').css('background-color','#252525');
	}

}





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

	if(len < 6 || len > 15){ result = false; }

	return result;
};

// 암호 체크
passwd_check = function() {
	var str1 = $('#mb_password').val();
	if(str1.length > 1) {
		if(pass_string_check(str1)==true) {
			
		}
		else if(pass_string_check(str1)==false) {
			alert("영문/숫자 조합 4 ~ 15자리 형식으로 입력하십시요.");
		}
		else {

		}
	}
	else {
		alert("비밀번호를 입력하십시요.");
	}
};

// 암호 형식 체크 function
pass_string_check = function(str){
	var result = true;
	var re1 = /[a-zA-Z]/i;		// 영문
	var re2 = /[0-9]/i;				// 숫자
	var re3 = /[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?]/i; // 특수문자

	if(!re1.test(str)) { result = false; }
	if(!re2.test(str)) { result = false; }
	

	var len = str.length;

	if(len < 4 || len > 15) { result = false; }
	if(str.indexOf(' ') > -1) { result = false; }

	return result;
}

function onlyDigit(el) {
  el.value = el.value.replace(/\D/g,'');
}


function onlyDigit2(el, maxlen) {
	if (!maxlen) maxlen=0;
  el.value = el.value.replace(/\D/g,'');
  if (maxlen) {
	  if (el.value.length>=maxlen) {
		  $(el).next().trigger("focus");
	  }
  }
}
