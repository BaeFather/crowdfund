// 유효성 검사 에러 텍스트 표시
function invalidText(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);
}

// 예치금 출금 step1
withdraw_check = function() {
	var result = true;
	var withdraw_price = $('#withdrawPrice').val().replace(/,/g, "");
	var request_price = $('#withdrawRequestPrice').val().replace(/,/g, "");

    if(request_price) {
        $('#withdrawRequestPrice-invalid').empty();

        if(parseInt(withdraw_price) < parseInt(request_price)) {
            invalidText('withdrawRequestPrice', 'red', '[출금가능금액을 확인해 주세요.]');
            result = false;
        } else {
            $('#withdrawRequestPrice-invalid').empty();
        }

    } else {
        invalidText('withdrawRequestPrice', 'red', '[출금 요청금액을 입력해주세요.]');
        result = false;
    }

    return result;
}

// 예치금 출금 step2
withdraw_success = function() {
	var result = true;
	var request_pw = $('#currentPwd').val();

    if(request_pw) {
        $('#currentPwd-invalid').empty();
    } else {
        invalidText('currentPwd', 'red', '[현재 비밀번호를 입력해주세요.]');
        result = false;
    }

    return result;
}