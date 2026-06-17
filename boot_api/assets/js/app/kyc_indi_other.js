$('#prevBtn2').on('click',function(){
    $('#kycStep2').css('display','none');
    $('#kycStep1').css('display','block');
    $(location).attr('href','#');
});
$('#prevBtn3').on('click',function(){
    $('#kycStep3').css('display','none');
    $('#kycStep2').css('display','block');
    $(location).attr('href','#');
});
function idInvalTextView(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);
}

// btn7에서 사용할 validation 체크
function validation(){
    var jsonData = new Object;
    var engFirstNm =$("#engFirstNm").val();			                            // 영문명(성)
    var engLastNm =$("#engLastNm").val();			                            // 영문명(이름)
    var addr = $("#addr").val();			                                    // 자택 주소
    var postcode = $('#postcode').val();		                                // 자택 우편번호
    var job  = $('#job option:selected').val();  		                        // 직업
    var purpose  = $('#purpose option:selected').val();  			            // 거래목적
    var tranFundSourceDiv  = $('#tranFundSourceDiv option:selected').val();  			// 거래자금출처
    var countryCode =  $('#countryCode option:selected').val();                 // 국적
    var memberIdx = $("#memberIdx").val();                                      //mbNo
    var detailAddrress = $("#detailAddrress").val();                            //mbNo
    var tranFundSourceNm = $('#tranFundSourceNm').val();                        //거래자금출처이름
    var accountNewPurposeNm = $('#accountNewPurposeNm').val();                  //거래목적이름

    jsonData['engFirstNm'] = engFirstNm;
    jsonData['engLastNm'] = engLastNm;
    jsonData['addr'] = addr;
    jsonData['postcode'] = postcode;
    jsonData['job'] = job;
    jsonData['purpose'] = purpose;
    jsonData['tranFundSourceDiv'] = tranFundSourceDiv;
    jsonData['countryCode'] = countryCode;
    jsonData['memberIdx'] = memberIdx;
    jsonData['detailAddrress'] = detailAddrress;
    jsonData['tranFundSourceNm'] = tranFundSourceNm;
    jsonData['accountNewPurposeNm'] = accountNewPurposeNm;


    //본인 정보 입력 Validation Check
    if($.trim($('#kycStep6 #engLastNm').val())=='') {
        idInvalTextView('engNm', 'red', '[영문(성)을 입력해주세요!]');
        return false;
    }
    else if($.trim($('#kycStep6 #engFirstNm').val())=='') {
        idInvalTextView('engNm', 'red', '[영문(이름)을 입력해주세요!]');
        return false;
    }
    else {
        var engNm = $.trim($('#kycStep6 #engFirstNm').val()) + $.trim($('#kycStep6 #engLastNm').val());
        $('#kycStep6 #engNm').val(engNm);
        $("#engLastNm,#engFirstNm").removeClass('is-invalid');
        $('#engNm-invalid').empty();
    }
    if($.trim($('#kycStep6 #addr').val())=='') {
        idInvalTextView('addr', 'red', '[주소 및 우편번호는 필수 정보입니다.]');
        return false;
    }else {
        $("#kycStep6 #addrs").removeClass('is-invalid');
        $('#addr-invalid').empty();
    }
    if($.trim($('#kycStep6 #job').val())=='') {
        idInvalTextView('job', 'red', '[직업을 선택해 주세요.]');
        return false;
    }
    else {
        $("#kycStep6 #job").removeClass('is-invalid');
        $('#job-invalid').empty();
    }
    if($.trim($('#kycStep6 #purpose').val())=='') {
        idInvalTextView('purpose', 'red', '[거래목적을 선택해 주세요.]');
        return false;
    }
    else {
        $("#kycStep6 #purpose").removeClass('is-invalid');
        $('#purpose-invalid').empty();
    }
    if($.trim($('#kycStep6 #tranFundSourceDiv').val())=='') {
        idInvalTextView('tranFundSourceDiv', 'red', '[거래자금출처를 선택해 주세요.]');
        return false;
    }
    else {
        $("#kycStep6 #tranFundSourceDiv").removeClass('is-invalid');
        $('#tranFundSourceDiv-invalid').empty();
    }
    if($.trim($('#kycStep6 #countryCode').val())=='') {
        idInvalTextView('countryCode', 'red', '[국적을 선택해 주세요.]');
        return false;
    }
    else {
        $("#kycStep6 #countryCode").removeClass('is-invalid');
        $('#countryCode-invalid').empty();
    }
}


// 본인확인 1/6 - 본인확인서류제출
function checkValid (btnId){

    var jsonData  = new Object();
    var regExp = /^[0-9]*$/;                                                    //은행 계좌번호 숫자만들어가게 하기위함 .
    var newAccountNum = $("#newAccountNum").val();                              //계좌번호
    var privateNo1 = $("#privateNo1").val();                                    //앞자리주번
    var privateNo2 = $("#privateNo2").val();                                    //뒷자리주번
    var authNo = $("#authNo").val();                                            //1원인증
    var foreigner = $("#foreigner").val();                                            //1원인증
    var koreanN = $("#koreanN").val();                                            //외국인
    var idOwnerN = $("#idOwnerN").val();                                            //미성년자
    var idOwnerY = $("#idOwnerY").val();                                            //가입자본인
    var koreanY = $("#koreanY").val();                                            //내국인

    jsonData['privateNo1'] = privateNo1;
    jsonData['privateNo2'] = privateNo2;
    jsonData['foreigner'] = foreigner;
    jsonData['koreanN'] = koreanN;
    jsonData['idOwnerN'] = idOwnerN;
    jsonData['koreanY'] = koreanY;
    jsonData['idOwnerY'] = idOwnerY;

    if(btnId == 'nextBtn1'){

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if( ($('#indiEtcIdentfyDoc1').val()=='') && ($('#indiEtcIdentfyDoc2').val()=='') ) {
        MsgBox.Alert('error', '제출서류 파일을 등록 해주세요.');
        $('#indiEtcIdentfyDoc1').focus();
        return false;
    }
    else {
        if( $('#indiEtcIdentfyDoc1').val() && $('#indiEtcIdentfyDoc1')[0].files[0].size > 5000000) {
            MsgBox.Alert('error', '5MB를 초과한 파일은 등록하실 수 없습니다.');
            $('#indiEtcIdentfyDoc1').val('');
            return false;
        }
        if( $('#indiEtcIdentfyDoc2').val() && $('#indiEtcIdentfyDoc2')[0].files[0].size > 5000000) {
            MsgBox.Alert('error', '5MB를 초과한 파일은 등록하실 수 없습니다.');
            $('#indiEtcIdentfyDoc2').val('');
            return false;
        }
    }
     $('#masterForm #fStime').val(timestamp);
     $(location).attr('href','#');
}

$('#nextBtn1').on('click', function() {

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    $('#kycStep1').css('display','none');
    $('#masterForm #fStime').val(timestamp);
    $('#kycStep2').css('display','block');
    $(location).attr('href','#');

});

// 본인확인 2/6 - 인적정보 입력
if(btnId == 'nextBtn2'){

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if(check_timestamp > 1800) {
        MsgBox.Alert('error', '입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
        window.location.reload();
    }

    if($.trim($('#kycStep2 #userName').val())=='') {
        idInvalTextView('userName', 'red', '[성명을 입력해주세요!]');
        $('#kycStep2 #userName').focus();
        return;
    }
    else {
        $("#kycStep2 #userName").removeClass('is-invalid');
        $('#userName-invalid').empty();
    }

    $('#privateNo').val('');
    if($.trim($('#kycStep2 #privateNo1').val())=='' || $.trim($('#kycStep2 #privateNo1').val()).length < 6) {
        idInvalTextView('privateNo', 'red', '[주민등록번호(앞 6자리)를 입력해주세요!]');
        $('#kycStep2 #privateNo1').focus();
        return;
    }
    else if($.trim($('#kycStep2 #privateNo2').val())=='' || $.trim($('#kycStep2 #privateNo2').val()).length < 7) {
        idInvalTextView('privateNo', 'red', '[주민등록번호(뒤 7자리)를 입력해주세요!]');
        $('#kycStep2 #privateNo2').focus();
        return;
    }
    else {
        var privateNo = $.trim($('#kycStep2 #privateNo1').val()) + $.trim($('#kycStep2 #privateNo2').val());

        if( checkJumin(privateNo) ) {
            $('#privateNo').val(privateNo);
            $("#privateNo1,#privateNo2").removeClass('is-invalid');
            $('#privateNo-invalid').empty();
        }
        else {
            idInvalTextView('privateNo', 'red', '[정상적인 등록번호가 아닙니다.]');
            $('#kycStep2 #privateNo2').focus();
            return;
        }

    }

}

// 본인확인 - 출금계좌등록
if(btnId == 'nextBtn3'){

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if($.trim($('#kycStep3 #newBankCode').val())=='') {
        idInvalTextView('newBankCode', 'red', '[은행을 선택해 주세요.]');
        $('#kycStep3 #newBankCode').focus();
        return false;
    }
    else{
        $("#kycStep3 #newBankCodes").removeClass('is-invalid');
        $('#newBankCode-invalid').empty();
    }
    if($.trim($('#kycStep3 #newAccountNum').val())==''){
         idInvalTextView('newAccountNum' , 'red' , '[계좌번호를 입력해주세요.]');
         $('#kycStep3 #newAccountNum').focus();
         return false;
    }else{
         if(!regExp.test(newAccountNum)){
           idInvalTextView('newAccountNum' , 'red' , '[숫자만 입력해주세요.]');
           $('#newAccountNum #newBankCode').focus();
           return false;
         }else {
            $("#kycStep3 #newAccountNums").removeClass('is-invalid');
            $('#kycStep3-invalid').empty();
         }
    }

    // 계좌 변경 validation
    var jsonData = new Object();
    var newBankCode = $('#newBankCode option:selected').val();
    var newAccountNum = $("#newAccountNum").val();

    jsonData['newBankCode'] = newBankCode;
    jsonData['newAccountNum'] = newAccountNum;

    $.ajax({
        url: accountUser,
        type: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(jsonData),
        beforeSend: function(request) {
            $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
        },
        complete: function(response) {
            $('#loadingSpinnerLayer').remove();
            $('#loadingSpinner').remove();
        },
        success: function(result) {
            if ( result && result.status == 'SUCCESS' ) {
                $.ajax({
                    url: mpAccountChange,
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify(jsonData),
                    beforeSend: function(){
                        $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
                    },
                    success: function(result) {
                        if ( result && result.status == 'SUCCESS' ){
                         $("#accountOwnerName").val(result.data.accountOwnerName);
                            $("#trdNo").val(result.data.data.trdNo);
                            $("#mchtTrdNo").val(result.data.data.mchtTrdNo);

                            $("#bankCode").val($('#newBankCode').val());
                            $("#accountNumber").val($('#newAccountNum').val());

                            $('#kycStep4').css('display','block');
                            $('#kycStep3').css('display','none');

                        }
                        else {
                          idInvalTextView('newAccountNum', 'red', "본인명의 계좌를 등록해 주세요!");
                          $('#kycStep4 #newAccountNum').focus();
                          return false;
                      }
                          $("#kycStep4 #newAccountNum").removeClass('is-invalid');
                          $('#authNo-invalid').empty();
                          return false;
                    },
                    error: function(jqXHR,textStatus){
                        ajaxError(jqXHR, textStatus);
                    },
                    complete: function(){
                        $('#loadingSpinnerLayer').remove();
                        $('#loadingSpinner').remove();
                    },
                });
            }
            else {
                idInvalTextView('newAccountNum', 'red', "본인명의 계좌를 등록해 주세요!");
                $('#kycStep4 #newAccountNum').focus();
                return false;
            }
        },
        error: function(jqXHR, textStatus) {
            if(jqXHR.responseJSON.messageType == 'ALERT'){
                MsgBox.Alert('error', jqXHR.responseJSON.message);
                return;
            }
            else {
                ajaxError(jqXHR, textStatus);
            }
            return false;
        }
    });

}

// 본인확인 - 본인계좌인증
if(btnId =='nextBtn4'){

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if($.trim($('#kycStep4 #authNo').val())=='') {
        idInvalTextView('authNo', 'red', '[고객님 계좌의 입금내역에 표시된 4자리의 숫자를 입력 해주세요.]');
        $('#kycStep4 #authNo').focus();
        return false;
    }else {
        if(authNo.length < 4){
            idInvalTextView('authNo', 'red', '[4자리의 인증번호 숫자를 입력 해주세요.]');
            $('#kycStep4 #authNo').focus();
            return false;
        }
        else{
            $("#kycStep4 #authNos").removeClass('is-invalid');
            $('#authNo-invalid').empty();
        }
    }
    var jsonData = new Object();
    var authNo = $("#authNo").val();
    var trdNo = $("#trdNo").val();
    var mchtTrdNo = $("#mchtTrdNo").val();

    jsonData['authNo'] = authNo;
    jsonData['trdNo'] = trdNo;
    jsonData['mchtTrdNo'] = mchtTrdNo;

    $.ajax({
        url: mpAccountSave,
        type: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(jsonData),
        beforeSend: function(){
            $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
        },
        success: function(result) {
            if ( result && result.status == 'SUCCESS' ){
                 $('#kycStep5').css('display','block');
                 $('#kycStep4').css('display','none');
            }
            else {
                idInvalTextView('authNo', 'red', '[인증 번호가 불일치합니다.]');
                $('#kycStep5 #authNo').focus();
                return false;
            }
                $("#kycStep5 #authNos").removeClass('is-invalid');
                $('#authNo-invalid').empty();
                return false;
        },
        error: function(jqXHR,textStatus){
            ajaxError(jqXHR, textStatus);
        },
        complete: function(){
            $('#loadingSpinnerLayer').remove();
            $('#loadingSpinner').remove();
        },
    });
}

$("#nextBtn4").on("click", function() {
    var bank_name = $('input[name=bankName]').val();
    var account_num = $('input[name=accountNumber]').val();

    $('#confirmBankName').text(bank_name);
    $('#confirmBankAccount').text(account_num);

});

if(btnId == 'nextBtn6'){
    var engFirstNm =$("#engFirstNm").val();			                            // 영문명(성)
    var engLastNm =$("#engLastNm").val();			                            // 영문명(이름)
    var addr = $("#addr").val();			                                    // 자택 주소
    var postcode = $('#postcode').val();		                                // 자택 우편번호
    var job  = $('#job option:selected').val();  		                        // 직업
    var purpose  = $('#purpose option:selected').val();  			            // 거래목적
    var tranFundSourceDiv  = $('#tranFundSourceDiv option:selected').val();  			// 거래자금출처
    var countryCode =  $('#countryCode option:selected').val();                 // 국적
    var memberIdx = $("#memberIdx").val();                                      //mbNo
    var detailAddrress = $("#detailAddrress").val();                            //mbNo
    var tranFundSourceNm = $('#tranFundSourceNm').val();                        //거래자금출처이름
    var accountNewPurposeNm = $('#accountNewPurposeNm').val();                  //거래목적이름

    jsonData['engFirstNm'] = engFirstNm;
    jsonData['engLastNm'] = engLastNm;
    jsonData['accountNewPurposeNm'] = accountNewPurposeNm;

    var finalFormData = new FormData();
    var formData = $('#masterForm').serializeArray();

    formData.forEach(function (o) {
        if(o.value != null && o.value != ''){
            jsonData[o.name] = o.value;
        }
    });

    finalFormData.append('formData', new Blob([ JSON.stringify(jsonData) ], {type : "application/json"}));

    var fileList = $('.file_control');
    var fileColumnList = [];
    for(var i = 0; i < fileList.length; i ++){
        if(fileList[i].files.length > 0){
            for (var j = 0; j < fileList[i].files.length; j++) {
                finalFormData.append('file',fileList[i].files[j]);
                fileColumnList.push($('.file_control').eq(i).data('columnName'));
            }
        }
    }
    finalFormData.append('fileColumnName', new Blob([ JSON.stringify(fileColumnList) ], {type : "application/json"}));


    //본인 정보 입력 Validation Check
    if($.trim($('#kycStep6 #engLastNm').val())=='') {
        idInvalTextView('engNm', 'red', '[영문(성)을 입력해주세요!]');
        $('#kycStep6 #engLastNm').focus();
        return false;
    }
    else if($.trim($('#kycStep6 #engFirstNm').val())=='') {
        idInvalTextView('engNm', 'red', '[영문(이름)을 입력해주세요!]');
        $('#kycStep6 #engFirstNm').focus();
        return false;
    }
    else {
        var engNm = $.trim($('#kycStep6 #engFirstNm').val()) + $.trim($('#kycStep6 #engLastNm').val());
        $('#kycStep6 #engNm').val(engNm);
        $("#engLastNm,#engFirstNm").removeClass('is-invalid');
        $('#engNm-invalid').empty();
    }
    if($.trim($('#kycStep6 #addr').val())=='') {
        idInvalTextView('addr', 'red', '[주소 및 우편번호는 필수 정보입니다.]');
        $('#kycStep6 #addr').focus();
        return false;
    }else {
        $("#kycStep6 #addrs").removeClass('is-invalid');
        $('#addr-invalid').empty();
    }
    if($.trim($('#kycStep6 #job').val())=='') {
        idInvalTextView('job', 'red', '[직업을 선택해 주세요.]');
        $('#kycStep6 #job').focus();
        return false;
    }
    else {
        $("#kycStep6 #jobs").removeClass('is-invalid');
        $('#job-invalid').empty();
    }
    if($.trim($('#kycStep6 #purpose').val())=='') {
        idInvalTextView('purpose', 'red', '[거래목적을 선택해 주세요.]');
        $('#kycStep6 #purpose').focus();
        return false;
    }
    else {
        $("#kycStep6 #purposed").removeClass('is-invalid');
        $('#purpose-invalid').empty();
    }
    if($.trim($('#kycStep6 #tranFundSourceDiv').val())=='') {
        idInvalTextView('tranFundSourceDiv', 'red', '[거래자금출처를 선택해 주세요.]');
        $('#kycStep6 #tranFundSourceDiv').focus();
        return false;
    }
    else {
        $("#kycStep6 #tranFundSourceDived").removeClass('is-invalid');
        $('#tranFundSourceDiv-invalid').empty();
    }
    if($.trim($('#kycStep6 #countryCode').val())=='') {
        idInvalTextView('countryCode', 'red', '[국적을 선택해 주세요.]');
        $('#kycStep6 #countryCode').focus();
        return false;
    }
    else {
        $("#kycStep6 #countryCodes").removeClass('is-invalid');
        $('#countryCode-invalid').empty();
    }

    MsgBox.Confirm("본인확인정보를 등록 하시겠습니까?" , function(){
        $.ajax({
            url: insertUrl,
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: finalFormData,
            contentType: false,
            processData: false,
            enctype : 'multipart/form-data',
            success: function(result) {

                if ( result && result.status == 'SUCCESS' ){

                    accountUpdates();
                    $('#kycStep7').css('display','block');
                    $('#kycStep6').css('display','none');

                }
                else {
                   // console.log(result.message);
                }
            },
            error: function(jqXHR,textStatus){
                ajaxError(jqXHR, textStatus);
            }
        });
    });
}
    return true;
}


// 제출가능서류 선택
function formHighlight() {
    $('.z1, .z11, .z12, .z2, .z21').css('background-color','');
    $('#idOwnerY, #idOwnerN').attr('disabled',false);

    $('#masterForm #agentId').val('');

    if ($('#masterForm #koreanY').is(':checked')) {
        $('.z1').css('background-color','#fcf4f5');

        if( $('#masterForm #idOwnerY').is(':checked') ) {
            $('.z11').css('background-color','#fcf4f5');
            $('#masterForm #agentId').attr('disabled',true);
            $('#masterForm #agentCheckButton').attr('disabled',true);
            $('#agent_zone').slideUp();
        }
        else {
            $('.z12').css('background-color','#fcf4f5');
            $('#masterForm #agentId').attr('disabled',false);
            $('#masterForm #agentCheckButton').attr('disabled',false);
            $('#agent_zone').slideDown();
        }

        $('#masterForm #foreigner').val('');
        $('.privateNo-Label').text('주민등록번호');

        $('#masterForm #countryCd').val("KR").prop("selected", true);
        $('.countryCdDiv').css("display", "none");
    }
    else {

        $('.z2, .z21').css('background-color','#fcf4f5');
        $('#masterForm #idOwnerY, #idOwnerN').attr('disabled',true);
        $('#masterForm #agentId').attr('disabled',true);
        $('#masterForm #agentCheckButton').attr('disabled',true);
        $('#masterForm #agent_zone').slideUp();

        $('#masterForm #foreigner').val('1');
        $('.privateNo-Label').text('외국인등록번호');

        $('#masterForm #countryCd option:eq(0)').prop("selected", true);
        $('.countryCdDiv').css("display", "block");
    }
}

$(document).ready(function() {
    formHighlight();
});

// 개인사업자 선택시 위험군 선택 노출
$('#jobDivCd,#jobDivCd2').on('change',function() {
    if($('#jobDivCd').val()=='02') {
        if($('#jobDivCd2').val()=='92') {
            //개인 대부업 등록을 희망하시는 분은 정보 입력을 완료하신 후, 고객센터로 연락주세요.
            $('.job_div_next2').slideDown();
        }
        else {
            $('.job_div_next2').slideUp();
        }
    }
});

// 다음 주소찾기 api
function execDaumPostcode(postcode, address, detailAddrress, mbAddrJibeon , invalid) {
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
            document.getElementById(mbAddrJibeon).value = data.jibunAddress;
            $('#addr-invalid').empty();
            // 커서를 상세주소 필드로 이동한다.
            document.getElementById(detailAddrress).focus();
        }
    }).open();
}

function checkJumin(ssn) {

	var sum   = 0;
	var month = ssn.substr(2,2);
	var day   = ssn.substr(4,2);

	if(ssn.length != 13) {
		return false;
	}

	//월의 경우 13월을 넘지 않아야 한다.
	if(month < 13 && month != 0 && day != 0) {

		//2월의 경우
		if(month == 2) {
			//29일을 넘지 않아야 한다.
			if(day > 29) return false;
		}
		else if(month == 4 || month == 6 || month == 9 || month == 11) {
			// 4,6,9,11월의 경우 30일을 넘지 않아야 한다.
			if(day > 30) return false;
		}
		else {
			// 그외 월이 31일을 넘지 않아야 한다.
			if(day > 31) return false;
		}

	}
	else {
		return false;
	}

	for(var i = 0; i < 12; i++) {
		sum += Number(ssn.substr(i, 1)) * ((i % 8) + 2);
	}

	if(ssn.substr(6,1) == 1 || ssn.substr(6,1) == 2 || ssn.substr(6,1) == 3 || ssn.substr(6,1) == 4 || ssn.substr(6,1) == 9 || ssn.substr(6,1) == 0) {

		//내국인 주민번호 검증(1900(남/여) 2000(남/여))
		if(((11 - (sum % 11)) % 10) == Number(ssn.substr(12,1))) {
			return true;
		}

		return false;

	}
	else if(ssn.substr(6,1) == 5 || ssn.substr(6,1) == 6 || ssn.substr(6,1) == 7 || ssn.substr(6,1) == 8) {

		//외국인 등록번호 검증(1900(남/여) 2000(남/여))
		if(Number(ssn.substr(8,1)) % 2 != 0) {
			return false;
		}

		if((((11 - (sum % 11)) % 10 + 2) % 10) == Number(ssn.substr(12, 1))) {
			return true;
		}

		return false;
	}

	return true;

}

// 은행명 onchange event
function getBankName(name) {
    var bank_name = $('select#newBankCode option:selected').text().replace(':: 은행선택 ::','');
    $('#bankName').val(bank_name);
}

function getPurposed(name) {
    var purpose_name = $('select#purpose option:selected').text().replace(':: 거래목적 선택 ::','');
    $('#accountNewPurposeNm').val(purpose_name);
}

function getTranFundSourceDiv(name) {
    var tranFundSourceDiv_name = $('select#tranFundSourceDiv option:selected').text().replace(':: 선택하세요 ::','');
    $('#tranFundSourceNm').val(tranFundSourceDiv_name);
}

// 계좌 업데이트
function accountUpdates(){

    var jsonData = new Object();
    var formData = $('#accountInfoFrm').serializeArray();

    var mbId = $('input[name=mbId]').val();
    var mbNo = $('input[name=mbNo]').val();

    // form data ajax
    formData.forEach(function (o) {
        jsonData[o.name] = o.value;
    });
    jsonData['mbId'] = mbId;
    jsonData['mbNo'] = mbNo;

    $.ajax({
        url: accountUpdate,
        type: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(jsonData),
        success: function(result) {
            // console.log(result);
        },
        error: function(jqXHR,textStatus){
            ajaxError(jqXHR, textStatus);
            return false;
        }
    });
}
