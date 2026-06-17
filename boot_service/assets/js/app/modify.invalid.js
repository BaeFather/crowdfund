
var pw_min_length = 6;
var pw_max_length = 15;


// 유효성 검사 에러 텍스트 표시
function invalidText(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);
}

// 암호 체크
passwd_check = function() {
	var result = true;
	var str1 = $.trim($('#newPw').val());  // 새 비밀번호
	var str2 = $.trim($('#newPwCheck').val());  // 새 비밀번호 확인
	var str3 = $.trim($('#currentPw').val());  // 현재 비밀번호


    if(str3.length > 0) {        $('#currentPw-invalid').empty();
    } else {
        invalidText('currentPw', 'red', '[현재 비밀번호를 입력해 주세요.]');
        result = false;
    }

    if(str1.length > 0) {
        $('#newPw-invalid').empty();
    }
    else {
        invalidText('newPw', 'red', '[새 비밀번호를 입력해 주세요.]');
        result = false;
    }

    if(pass_string_check(str1) == false) {
        invalidText('newPw', 'red', '[영문/숫자/특수문자 조합, 8-15자리 등록 가능합니다.]');
        result = false;
    } else {
        $('#newPw-invalid').empty();
    }

    if(pass_string_check(str2) == false) {
        invalidText('newPwCheck', 'red', '[영문/숫자/특수문자 조합, 8-15자리 등록 가능합니다.]');
        result = false;
    } else {
        $('#newPwCheck-invalid').empty();
    }

    if(str2.length > 0) {
        if((str1 == str2) == false) {
            invalidText('newPwCheck', 'red', '[비밀번호가 일치하지 않습니다.]');
            result = false;
        }
        else {
            $('#newPwCheck-invalid').empty();
        }
    }
    else {
        invalidText('newPwCheck', 'red', '[비밀번호를 한번 더 입력해 주세요.]');
        result = false;
    }

	return result;
}

// 암호 형식 체크 function
pass_string_check = function(str){
	var result = true;
	var re1 = /[a-zA-Z]/i;		// 영문
	var re2 = /[0-9]/i;			// 숫자
	var re3 = /[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?]/i; // 특수문자

	if(!re1.test(str)) { result = false; }
	if(!re2.test(str)) { result = false; }
	if(!re3.test(str)) { result = false; }

	var len = str.length;

	if(len < pw_min_length || len > pw_max_length) { result = false; }
	if(str.indexOf(' ') > -1) { result = false; }
	return result;
}


// 회원탈퇴 시 비밀번호 형식 체크
nowPasswd_check = function() {
	var result = true;
	var str3 = $.trim($('#nowPassword').val());  // 현재 비밀번호

    if(str3.length > 0) {
        $('#nowPassword-invalid').empty();
    } else {
        invalidText('nowPassword', 'red', '[현재 비밀번호를 입력해 주세요.]');
        result = false;
    }

    return result;
}


// 출금 계좌 변경 체크
accountSend_check = function() {
	var result = true;
	var regExp = /^[0-9]*$/;
	var bank_name = $('#newBankCode').val();
	var bank_account = $('#newAccountNum').val();

	if(!bank_name) {
	    invalidText('newBankCode', 'red', '[출금하실 은행을 선택해 주세요.]');
        result = false;
	} else {
	    $('#newBankCode-invalid').empty();
	}

    if(!bank_account || bank_account == '') {
        invalidText('newAccountNum', 'red', '[계좌번호를 입력해주세요.]');
        result = false;
    } else {
        $('#newAccountNum-invalid').empty();
    }

    return result;
}

// 계좌 입금내역 값 체크
accountSubmit_check = function() {

    var result = true;
	var auth_no = $('#authNo').val();

    if(!auth_no || auth_no == '') {
        invalidText('authNo', 'red', '[입금내역에 표시된 4자리 숫자를 입력해주세요.]');
        result = false;
    } else {
        $('#authNo-invalid').empty();
    }

    return result;
}


// 이메일 체크
var re = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

email_check = function() {
	var result = true;
	var str = $('#email').val();

	if(str.length > 1) {
		if(re.test(str) == false) {
			invalidText('email', 'red', '[올바른 이메일 주소를 입력해 주세요.]');
			result = false;
		}
		else {
			$("#email").addClass('is-invalid');
            $('#email-invalid').empty();
		}
	}
	else {
		invalidText('email', 'red', '[이메일 주소를 입력해 주세요.]');
		result = false;
	}

	return result;
}


// 법인 휴대폰 체크
phoneCorpSubmit_check = function() {
    var result = true;
    var regExp = /^(01[016789]{1}|)([0-9]{3,4})([0-9]{4})$/;
    var phoneString = $('#corpPhone').val();

    if(!phoneString) {
        invalidText('corpPhone', 'red', '[휴대폰 번호를 입력해주세요.]');
        $("#corpPhone-invalid").css("display", "block");
        result = false;
    } else {
        $('#corpPhone-invalid').empty();
    }

    if(!regExp.test(phoneString)) {
        invalidText('corpPhone', 'red', '[전화번호 형식에 맞지 않습니다.]');
        $("#corpPhone-invalid").css("display", "block");
        result = false;
    } else {
        $('#corpPhone-invalid').empty();
    }


    return result;

}

// 투자자 유형 체크
investType_check = function() {
    var result = true;
    var formFileMultiple1 = $('#formFileMultiple1').val();
    var formFileMultiple2 = $('#formFileMultiple2').val();
    var file_value = $('input[type=file]').val();

    var imgFile = $('input[name=fname]').val();
    var fileFormat = /(.*?)\.(jpg|jpeg|png|zip|pdf|JPG|JPEG|PNG|ZIP|PDF)$/

    // 항목 유효성 검사
    if(!imgFile.match(fileFormat)) {
        invalidText('formFileMultiple2', 'red', '[해당 파일만 등록 가능합니다. ※ jpg, jpeg, png, zip, pdf]');
        $("#formFileMultiple2-invalid").css("display", "block");
        result = false;
    } else {
        $('#formFileMultiple2-invalid').empty();
    }

    if(!file_value || file_value == '') {
        invalidText('formFileMultiple2', 'red', '[증빙서류를 첨부해 주세요.]');
        $("#formFileMultiple2-invalid").css("display", "block");
        result = false;
    } else {
        $('#formFileMultiple2-invalid').empty();
    }

    return result;

}