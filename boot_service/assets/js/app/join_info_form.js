var id_min_length = 6;
var pw_min_length = 6;
var id_max_length = 15;
var pw_max_length = 15;
var auth_mb_id = '';

$("#confirmIdBtn").on('click', function(){
    var mbId = $("#mbId").val();

    if(isEmpty(mbId)) {
        idInvalTextView('mbId', 'red', '[아이디를 입력해주세요!]');

        return false;
    }

    id_check();
});

function idInvalTextView(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);

//    if (color == 'red'){
//        $('#' + id).focus();
//    }
}

function id_check() {
    var mbId = $("#mbId").val();
    $('#mbId-invalid').empty();

    if(mbId) {
        if( idStringCheck(mbId) ) {
            $.ajax({
                url: "/sp/join/findId",
                method: 'POST',
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    "mbId" : mbId
                }),
                success: function(data){
                    console.log(data);
                    if(data.useStatus == "Y"){
                        idInvalTextView('mbId', 'green', '[사용 가능한 아이디 입니다.]');
                        auth_mb_id = $('#mbId').val();
                    }else if(data.useStatus == "N"){
                        idInvalTextView('mbId', 'red', '[이미 사용중인 아이디 입니다.]');
                    }else{
                        idInvalTextView('mbId', 'red', '[사용 하실 수 없는 아이디 입니다.!]');
                    }
                },
                error: function(jqXHR,textStatus){
                    ajaxError(jqXHR, textStatus);
                }
           });
        }else{
            idInvalTextView('mbId', 'red', '[영문 또는 영문.숫자 조합, 6~15자리 입니다.]');
        }
    }else{
        idInvalTextView('mbId', 'red', '[아이디는 필수정보입니다.]');
    }
}

// 아이디 형식 체크 function
idStringCheck = function(str){ // 숫자와 알파벳만 입력 허용
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

// 암호 체크
passwd_check = function() {
	var str1 = $.trim($('#mbPassword').val());
	var str2 = $.trim($('#passwordCheck').val());

	if(str1) {
		if(str1.length > 0) {
			if(pass_string_check(str1)==true) {
			    idInvalTextView('mbPassword', 'green', '[사용 가능한 비밀번호 입니다.]');
			}
			else if(pass_string_check(str1)==false) {
			    idInvalTextView('mbPassword', 'red', '[영문/숫자/특수문자 조합, 8-15자리 등록 가능합니다.]');
			}
		}
		else {
		    idInvalTextView('mbPassword', 'red', '[비밀번호를 입력해 주세요.]');
		}
	}

	if(str1 && str2) {
		if(str2.length > 0) {
			if(str1!='' && str2!='') {
				if(str1==str2) {
				    if(pass_string_check(str2)==false) {
				        idInvalTextView('passwordCheck', 'red', '[영문/숫자/특수문자 조합, 8-15자리 등록 가능합니다.]');
				    }else{
				        idInvalTextView('passwordCheck', 'green', '[비밀번호가 일치합니다.]');
				    }
				}
				else {
				    idInvalTextView('passwordCheck', 'red', '[비밀번호가 일치하지 않습니다.]');
				}
			}
		}
		else {
		    idInvalTextView('passwordCheck', 'red', '[비밀번호를 한번 더 입력해 주세요.]');
		}
	}
}

// 암호 형식 체크 function
pass_string_check = function(str){
	var result = true;
	var re1 = /[a-zA-Z]/i;		// 영문
	var re2 = /[0-9]/i;				// 숫자
	var re3 = /[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?]/i; // 특수문자

	if(!re1.test(str)) { result = false; }
	if(!re2.test(str)) { result = false; }
	if(!re3.test(str)) { result = false; }

	var len = str.length;

	if(len < pw_min_length || len > pw_max_length) { result = false; }
	if(str.indexOf(' ') > -1) { result = false; }
	return result;
}

// 이메일 체크
var re = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
email_check = function() {
//	var re = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	var str = $('#email').val();

	if(str.length > 1) {
		if(re.test(str) == false) {
			idInvalTextView('email', 'red', '[올바른 이메일 주소를 입력해 주세요.]');
		}
		else {
			$("#email").addClass('is-invalid');
            $('#email-invalid').empty();
		}
	}
	else {
		idInvalTextView('email', 'red', '[이메일 주소를 입력해 주세요.]');
	}
}

servicePolicyCheck = function() {
    var agreeProvision = $("#agree_provision").is(':checked') ? 1 : '';			// 서비스 이용약관
    var agreeInvestment = $("#agree_investment").is(':checked') ? 1 : '';		// 온라인연계투자약관
    var agreePrivacy = $("#agree_privacy").is(':checked') ? 1 : '';				// 개인(신용)정보 수집 및 이용
    var agreeProvide = $("#agree_provide").is(':checked') ? 1 : '';				// 개인정보 제3자 제공
    var agreeIdentify = $("#agree_identify").is(':checked') ? 1 : '';			// 고유식별정보 처리

    if(!agreeProvision){
        servicePolicyCheckAlert('error', '서비스 이용약관에 대한 동의가 필요합니다.', 'agree_provision');

        return false;
    }

    if(!agreeInvestment){
        servicePolicyCheckAlert('error', '온라인연계투자약관에 대한 동의가 필요합니다.', 'agree_investment');

        return false;
    }

    if(!agreePrivacy){
        servicePolicyCheckAlert('error', '개인(신용)정보 수집 및 이용에 대한 동의가 필요합니다.', 'agree_privacy');

        return false;
    }

    if(!agreeProvide){
        servicePolicyCheckAlert('error', '개인정보 제3자 제공에 대한 동의가 필요합니다.', 'agree_provide');

        return false;
    }

    if(!agreeIdentify){
        servicePolicyCheckAlert('error', '고유식별정보 처리에 대한 동의가 필요합니다.', 'agree_identify');

        return false;
    }

    return true;
}

servicePolicyCheckAlert = function(alertTatle, alertMsg, fieldId) {
    var card_contents_view = $('#agree');

    MsgBox.Alert(alertTatle, alertMsg);

    if(!card_contents_view.is(':visible')) {
        card_contents_view.slideDown(300);
        $(this).text('▲');
    }

    $('#' + fieldId).focus();
}

// 만나이 반환
function calcAge(birth) {
    var date = new Date();
    var year = date.getFullYear();
    var month = (date.getMonth() + 1);
    var day = date.getDate();
    if (month < 10) month = '0' + month;
    if (day < 10) day = '0' + day;
    var monthDay = month + day;
    birth = birth.replace('-', '').replace('-', '');
    var birthdayy = birth.substr(0, 4);
    var birthdaymd = birth.substr(4, 4);
    var age = monthDay < birthdaymd ? year - birthdayy - 1 : year - birthdayy;
    return age;
}

jQuery.fn.serializeObject = function() {
    var obj = null;
    try {
        // this[0].tagName이 form tag일 경우
        if(this[0].tagName && this[0].tagName.toUpperCase() == "FORM" ) {
            var arr = this.serializeArray();
            if(arr){
                obj = {};
                jQuery.each(arr, function() {
                    // obj의 key값은 arr의 name, obj의 value는 value값
                    obj[this.name] = this.value;
                });
            }
        }
    }catch(e) {
        alert(e.message);
    }finally {}

    return obj;
};

function objToJson(formData){
    var data = formData;
    var obj = {};
    $.each(data, function(idx, ele){
        obj[ele.name] = ele.value;
    });
    return obj;
}

