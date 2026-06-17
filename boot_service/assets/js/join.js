// (임시) 서류 제출 완료 화면 띄우기
$('#submit_button').on('click', function () {
    $('.cmb-new-ok').show();
    $(this).parent().parent().parent('section').hide();
});

// 서비스 전체동의 folding
$('.agree_arrow_img').on('click', function() {
    var card_contents = $(this).parent().parent().siblings('.agree');

    if(card_contents.is(':visible')) {
        card_contents.slideUp(300);
        $(this).text('▼');
    } else {
        card_contents.slideDown(300);
        $(this).text('▲');
    }
});

// input file 값 변경
$(document).ready(function () {
    var fileTarget = $('#file');
    var fileTarget2 = $('#file2');
    var fileTarget3 = $('#file3');

    fileTarget.on('change', function () {
        var cur = $(this).val().split('/').pop().split('\\').pop();
        $(".name1").val(cur);
    });

    fileTarget2.on('change', function () {
        var cur = $(this).val().split('/').pop().split('\\').pop();
        $(".name2").val(cur);
    });

    fileTarget3.on('change', function () {
        var cur = $(this).val().split('/').pop().split('\\').pop();
        $(".name3").val(cur);
    });

});


// 대표정보동일 chk box 클릭시 담당자 & 대표자 정보 동일하게
function infoSame() {
    var infoChk = $("#is-info-same");

    if (infoChk.is(":checked")) {
        var companyManagerName = $("#ceoName").val();
        var companyManagerHp1 = $("#ceoHp1 option:selected").val();
        var companyManagerHp2 = $("#ceoHp2").val();
        var companyManagerHp3 = $("#ceoHp3").val();

        $("#companyManagerName").val(companyManagerName);
        $("#companyManagerHp1").val(companyManagerHp1).prop("selected", true);
        $("#companyManagerHp2").val(companyManagerHp2);
        $("#companyManagerHp3").val(companyManagerHp3);
        $("#companyManagerName-invalid").empty();
        $("#companyManagerHp-invalid").empty();
    }else{
        $("#companyManagerName").val('');
        $("#companyManagerHp1").val('010').prop("selected", true);
        $("#companyManagerHp2").val('');
        $("#companyManagerHp3").val('');
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
function execDaumPostcode(postcode, address, detailAddrress, invalid) {
    new daum.Postcode({
        oncomplete: function (data) {
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
            if (data.userSelectedType === 'R') {
                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                    extraAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if (data.buildingName !== '' && data.apartment === 'Y') {
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data
                        .buildingName);
                }
                // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if (extraAddr !== '') {
                    addr += ' (' + extraAddr + ')';
                }
            }


            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById(postcode).value = data.zonecode;
            document.getElementById(address).value = addr;
            $('#'+invalid).empty();
            // 커서를 상세주소 필드로 이동한다.
            document.getElementById(detailAddrress).focus();
//            if(x == '1') {
//                // 우편번호와 주소 정보를 해당 필드에 넣는다.
//                document.getElementById('postcode_c').value = data.zonecode;
//                document.getElementById('addr_c').value = addr;
//                // 커서를 상세주소 필드로 이동한다.
//                document.getElementById('detailAddr_c').focus();
//            } else if(x == '2') {
//                // 우편번호와 주소 정보를 해당 필드에 넣는다.
//                document.getElementById('postcode_cmb').value = data.zonecode;
//                document.getElementById('addr_cmb').value = addr;
//                // 커서를 상세주소 필드로 이동한다.
//                document.getElementById('detailAddr_cmb').focus();
//            }
        }
    }).open();
}


// 전체동의 체크항목
$("#allChkAgree").click(function () {
    if ($("#allChkAgree").prop("checked")) {
        $(".agree-list").prop("checked", true);
    } else {
        $(".agree-list").prop("checked", false);
    }

    if($('.agree').css('display') == 'none' && $("#allChkAgree").is(':checked')){
        $('.agree_arrow_img').trigger('click');
    }
});

$(".agree-list").click(function () {
    if ($(".agree-list:checked").length == $(".agree-list").length) {
        $("#allChkAgree").prop("checked", true);
    } else {
        $("#allChkAgree").prop("checked", false);
    }
});

//$(document).ready(function() {
//
//    // 다음 버튼 클릭 시
//    $('.next-btn').on('click', function () {
//        if ($(this).parents('.join-step').css('display', 'block')) {
//            $(this).parents('.join-step').next().css('display', 'block');
//            $(this).parents('.join-step').css('display', 'none');
//        }
//    });
//
//    // 이전 버튼 클릭 시
//    $('.prev-btn').on('click', function () {
//        if ($(this).parents('.join-step').css('display', 'block')) {
//            $(this).parents('.join-step').prev().css('display', 'block');
//            $(this).parents('.join-step').css('display', 'none');
//        }
//    });
//});


// 대부업법인 클릭 시
$('#is_creditor').click(function () {
    if ($("input:checkbox[id='is_creditor']").is(':checked') == true) {
        $('.loan_co_license_zone').css('display', 'block');
    } else {
        $('.loan_co_license_zone').css('display', 'none');
    }
});




/***********************************************************************************************************************************************/




// 아이디 형식 체크 function
id_string_check = function (str) { // 숫자와 알파벳만 입력 허용
    var safe_char =
    '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // 입력을 허용하는 글자들
    var len = str.length;
    var result = true;
    var char = '';
    for (i = 0; i < len; i++) {
        char = str.charAt(i);
        if (i == 0) {
            var re2 = /[0-9]/i; // 숫자
            if (re2.test(char)) {
                result = false;
                break;
            }
        }
        if (safe_char.indexOf(char) == -1) {
            result = false;
            break;
        }
    }

    if (len < 6 || len > 15) {
        result = false;
    }
    return result;
};

// 암호 형식 체크 function
pass_string_check = function (str) {
    var result = true;
    var re1 = /[a-zA-Z]/i; // 영문
    var re2 = /[0-9]/i; // 숫자
    var re3 = /[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?]/i; // 특수문자

    if (!re1.test(str)) {
        result = false;
    }
    if (!re2.test(str)) {
        result = false;
    }
    if (!re3.test(str)) {
        result = false;
    }
    var len = str.length;

    if (len < 8 || len > 15) {
        result = false;
    }
    if (str.indexOf(' ') > -1) {
        result = false;
    }
    return result;
};

var auth_mb_id = '';

