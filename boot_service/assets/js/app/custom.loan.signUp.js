// reCAPTCHA v3
/*
grecaptcha.ready(function() {
    grecaptcha.execute('6LdBq8UaAAAAAGHv6kxJ2lQ5gPcv5-e7E7scf9FR', {action: 'submit'}).then(function(token) {
         $('#token').val(token);
    });
});
*/


// 사용자의 IP
async function getIpClient() {
    try {
        const response = await axios.get('https://api.ipify.org?format=json');
        $('#ip').val(response.data.ip);
    } catch (error) {
        console.error(error);
    }
}

// 외부영역 클릭 시 팝업 닫기
$(document).mouseup(function (e){

    var LayerPopup = $(".popover");
    LayerPopup.removeClass("show");

});

$(document).ready(function(){

    $('#wloanDate').datepicker({
        format: "yyyy-mm-dd",
        language: "ko",
        dateFormat: "yyyy-mm",
        autoclose: true
    });

    //$('#wloanDate').datepicker('setDate', 'today');
    $('#wloanDate').datepicker('setStartDate', new Date());

    // 사용자의 IP
    getIpClient();

    // 사용자의 디바이스
    var device = '';

    //모바일(스마트폰+태블릿)일 때 실행 될 스크립트
    if(navigator.userAgent.match(/Android|Mobile|iP(hone|od|ad)|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune/)){
        device = 'MOBILE';
    }else{
        device = 'PC';
    }
    $('#device').val(device);
});

$('.form-amount').keyup(function(){

    var check = /^[0-9]+$/;
    $(this).val($(this).val().replace(/[^0-9]/g,""));

    if($(this).val() > $(this).data("max")){
        $(this).val($(this).data("max"));
    }

    //var amount = Number($(this).val() * 10000);
    var amount = Number($(this).val().replaceAll(',','') * 10000);
    var amountStr = "";
    var hundredMillion = parseInt(amount / Number(100000000));

    if(hundredMillion > 0){ // 억
        if(hundredMillion > 999){
            amountStr += hundredMillion.toLocaleString('ko-KR') + "억";
        }else{
            amountStr += hundredMillion + "억";
        }
        var tenThousand = (amount - hundredMillion * 100000000) / 10000;
        if(tenThousand > 999){
            amountStr += " " + tenThousand.toLocaleString('ko-KR') + "만";
        }else if(tenThousand > 0){
            amountStr += " " + tenThousand + "만";
        }
    }else{
        if(amount / 10000 > 999){
            amountStr += (amount / 10000).toLocaleString('ko-KR') + "만";
        }else if(amount / 10000 > 0){
            amountStr += (amount / 10000) + "만";
        }
    }
    amountStr += "원";

    var thisVal = Number($(this).val().replaceAll(',',''));
    $(this).val(thisVal.toLocaleString('ko-KR'));
    $(this).parent().next().html('<span>' + amountStr + '</span>');
});

function signUpLoan(){
    if(!$('#checkbox-marketing').is(':checked')){
        MsgBox.Alert('error', '개인정보 수집 및 이용에 동의해주세요.', function() { $('#checkbox-marketing').focus(); });
        return false;
    }

    var jsonData = new Object();
    var formData = $('#signUpForm').serializeArray();

    var valid = '';
    var validStr = '';
    var rdoApt = $('#rdoApt').val();
    //var hpRegex = new RegExp("01[016789][0-9]{3,4}[0-9]{4}");
    var hpRegex = new RegExp(/[0-9]/g);
    var nameRegex = new RegExp("[가-힣]{2,5}");

    for(var i = 0; i < formData.length; i++){
        var o = formData[i];

        switch(o.name){
            case 'wloanDate':
                if( o.value == '' || o.value == null){
                    valid = o.name; validStr = '희망대출일을 입력해 주세요.';
                }
            break;

            case 'hpIneb':
                if( o.value == '' || o.value == null){
                    valid = o.name; validStr = '신청자 연락처를 입력해 주세요.';
                }else if(!hpRegex.test(o.value)){
                    valid = o.name; validStr = '신청자 연락처는 숫자만 입력해 주세요.';
                } else idInvalTextView('hpIneb', 'green', '');
            break;

            case 'name':
                if( o.value == '' || o.value == null){
                    valid = o.name; validStr = '신청자 이름을 입력해 주세요.';
                }else if(!nameRegex.test(o.value)){
                    valid = o.name; validStr = '신청자 이름을 정확히 입력해 주세요.';
                } else idInvalTextView('name', 'green', '');
            break;

            case 'wamt':
                if(Number(o.value) <= 0){
                    valid = o.name; validStr = '대출신청금액을 입력해 주세요.';
                }else if(Number(o.value) < 5000) {
                    valid = o.name; validStr = '최소 대출신청 금액은 5,000만원 이상입니다.';
                }else if(rdoApt == 1 && Number(o.value) * 10000 > rprice){
                    valid = o.name; validStr = '대출신청금액은 대출가능금액을 초과할 수 없습니다.';
                }
            break;
        }

        var workIndex = $('#work option').index($("#work option:selected"));
        var tenantIndex = $('#tenant option').index($("#tenant option:selected"));

        if(tenantIndex == 0){
            valid = 'tenant'; validStr = '세입자 유무를 선택해 주세요.';
        } else idInvalTextView('tenant', 'green', '');

        if(workIndex == 0){
            valid = 'work'; validStr = '직업을 선택해 주세요.';
        } else idInvalTextView('work', 'green', '');


        if(valid != ''){
            //MsgBox.Alert('error', validStr, function() { $('#' + valid).focus(); });
            idInvalTextView(valid, 'red', validStr);
            return false;
        };

        if(o.name == 'wamt' || o.name == 'alreadyDept'){
            jsonData[o.name] = o.value.replaceAll(',','') * 10000;
        }else{
            jsonData[o.name] = o.value;
        }
    }

    $.ajax({
        url: signUpUrls,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        traditional: true,
        data: JSON.stringify(jsonData),
        success: function(response) {
            if(response.status == 'SUCCESS'){
                location.href = endUrls;
            }else{
                console.log(response)
                MsgBox.Alert('error', response.message, function(){location.href=getBackUrls;});
            }
        },error: function(jqXHR,textStatus){
            ajaxError(jqXHR, textStatus);
        }
    });
};

function idInvalTextView(id, color, msg){
    if (!msg) {
        $('#' + id).removeClass('is-invalid');
        $('#' + id + '-invalid').text(msg);
    } else {
        $('#' + id).addClass('is-invalid');
        //$('#' + id).css("background-image", 'none');
        $('#' + id + '-invalid').css("color", color);
        $('#' + id + '-invalid').text(msg);
        //if (id=="wloanDate") $('#' + id + '-invalid').focus(); // 달력이 자동으로 떠서 포커스를 메시지에 줌
        if (id=="wloanDate") $('#dayDatePicker').focus(); // 달력이 자동으로 떠서 포커스를 메시지에 줌
        else $('#' + id).focus();
    }
}