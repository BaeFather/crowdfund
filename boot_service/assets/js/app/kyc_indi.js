function idInvalTextView(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);
}

$('#prevBtn2').on('click',function(){
    MsgBox.Confirm("이전으로 들어가시겠습니까?" , function(){
        $('#kycStep2').css('display','none');
        $('#kycStep1').css('display','block');
        location.reload();
    });
});

$('#prevBtn3').on('click',function(){
    MsgBox.Confirm("이전으로 들어가시겠습니까?" , function(){
        $('#kycStep3').css('display','none');
        $('#kycStep2').css('display','block');
    });
});

$('#prevBtn5').on('click',function(){
    MsgBox.Confirm("이전으로 들어가시겠습니까?" , function(){
        $('#kycStep5').css('display','none');
        $('#kycStep4').css('display','block');
    });
});

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
    if($.trim($('#kycStep7 #engLastNm').val())=='') {
        idInvalTextView('engNm', 'red', '[영문(성)을 입력해주세요!]');
        return false;
    }
    else if($.trim($('#kycStep7 #engFirstNm').val())=='') {
        idInvalTextView('engNm', 'red', '[영문(이름)을 입력해주세요!]');
        return false;
    }
    else {
        var engNm = $.trim($('#kycStep7 #engFirstNm').val()) + $.trim($('#kycStep7 #engLastNm').val());
        $('#kycStep7 #engNm').val(engNm);
        $("#engLastNm,#engFirstNm").removeClass('is-invalid');
        $('#engNm-invalid').empty();
    }
    if($.trim($('#kycStep7 #addr').val())=='') {
        idInvalTextView('addr', 'red', '[주소 및 우편번호는 필수 정보입니다.]');
        return false;
    }else {
        $('#addr-invalid').empty();
    }
    if($.trim($('#kycStep7 #job').val())=='') {
        idInvalTextView('job', 'red', '[직업을 선택해 주세요.]');
        return false;
    }
    else {
        $('#job-invalid').empty();
    }
    if($.trim($('#kycStep7 #purpose').val())=='') {
        idInvalTextView('purpose', 'red', '[거래목적을 선택해 주세요.]');
        return false;
    }
    else {
        $("#kycStep7 #purpose").removeClass('is-invalid');
        $('#purpose-invalid').empty();
    }
    if($.trim($('#kycStep7 #tranFundSourceDiv').val())=='') {
        idInvalTextView('tranFundSourceDiv', 'red', '[거래자금출처를 선택해 주세요.]');
        return false;
    }
    else {
        $('#tranFundSourceDiv-invalid').empty();
    }
    if($.trim($('#kycStep7 #countryCode').val())=='') {
        idInvalTextView('countryCode', 'red', '[국적을 선택해 주세요.]');
        return false;
    }
    else {
        $('#countryCode-invalid').empty();
    }
}

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

function checkValid (btnId){
    var jsonData  = new Object();
    var regExp = /^[0-9]*$/;                                                    //은행 계좌번호 숫자만들어가게 하기위함 .
    var idType = $("input[type=radio][name=idChoice]:checked").val();           //신분증종류 (1:주민증,2:면허증)
    var newAccountNum = $("#newAccountNum").val();                              //계좌번호
    var authNo = $("#authNo").val();                                            //1원인증
    var userName = $('input[name=userName]').val();
    var mbName = $('input[name=mbName]').val();
    var juminNo = $('input[name=juminNo]').val();   //진위확인 주민등록증 13자리 주민
    var juminNo1 = $('input[name=juminNo1]').val();
    var juminNo2 = $('input[name=juminNo2]').val();
    var driverNo = $('input[name=driverNo]').val();
    var issueDate = $('input[name=issueDate]').val();
    var identity = $('input[name=identity]').val(); //진위확인 주민등록증 13자리 주민
    var birthDate = $('input[name=birthDate]').val(); //생년월일
    var driverNo = $('input[name=driverNo]').val(); //생년월일
    var driverNo = $('accountOwnerName').val(); //계좌소유주명

    var formData = $('#masterForm').serializeArray();
    var user = $('#masterForm #userName').val();


    //신분증 선택
    if(btnId == "nextBtn1"){
        //신분증 확인
        if(idType == 1) {
            var html = '';
            html += '<h5>주민등록증 확인</h5>';
            html += '<p>주민등록증 의 촬영 또는 스캔 이미지를 등록합니다.</p>';

            $('#idTypeCheck').html(html);

        } else {
            var html = '';
            html += '<h5>운전면허증 확인</h5>';
            html += '<p>운전면허증 의 촬영 또는 스캔 이미지를 등록합니다.</p>';

            $('#idTypeCheck').html(html);
        }
    }

    //신분증 확인
    if(btnId == "nextBtn2"){
        if( $('#masterForm #customFile').val() == '') {
            MsgBox.Alert('error',"업로드된 파일이 없습니다. 파일을 업로드 해주세요." , function(){$('#customFile').focus();});
            return false;
        }else{
            var f = $('#masterForm')[0];

            if( f.customFile.files[0].size > 10000000) {
            MsgBox.Alert('error',"10MB를 초과한 파일은 등록하실 수 없습니다" , function(){$('#customFile').focus();});
            return false;
            }
        }
         // 폼전송 정의
        var finalFormData = new FormData();
        var formData = $('#masterForm').serializeArray();
        var data = new Object();
        data['idType'] = idType;
        formData.forEach(function (o) {
            if(o.value != null && o.value != ''){
                data[o.name] = o.value;
            }
        });

        finalFormData.append('formData', new Blob([ JSON.stringify(data) ], {type : "application/json"}));

        var file = $('.custom-file-input');
        var fileColumnList = [];
        for(var i = 0; i < file.length; i ++){
            if(file[i].files.length > 0){

                for (var j = 0; j < file[i].files.length; j++) {
                    finalFormData.append('file',file[i].files[j]);
                    fileColumnList.push($('.custom-file-input').eq(i).data('columnName'));
                }
            }
        }
        finalFormData.append('fileColumnName', new Blob([ JSON.stringify(fileColumnList) ], {type : "application/json"}));
        finalFormData.append('useBFormData', new Blob([ JSON.stringify(data) ], {type : "application/json"}));
        finalFormData.append('fileSrc', new Blob([ JSON.stringify($('.img-card img').attr('src')) ], {type : "application/json"}));

        MsgBox.Confirm("신분증OCR(광학문자인식)을 진행합니다. \n 10~15초 정도의 다소 많은 시간이 소요됩니다. \n처리 완료시까지 대기하여 주시겠습니까?" , function(){

            $.ajax({
                url: usebOcrUrl,
                method: "POST",
                data: finalFormData,
                dataType: "JSON",
                contentType: false,
                processData: false,
                enctype : 'multipart/form-data',
                beforeSend: function(){
                    $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
                },
                success: function(response) {
                    if ( response && response.status == 'SUCCESS' ){
                        $("#userName").val(response.data.userName);
                        $("#juminNo1").val(response.data.juminNo1);
                        $("#juminNo2").val(response.data.juminNo2);
                        $("#driverNo").val(response.data.driverNo);
                        $("#issueDate").val(response.data.issueDate);

                        $('#kycStep3').css('display','block');
                        $('#kycStep2').css('display','none');
                    }else{
                        MsgBox.Alert("error" , response.message);
                        return false;
                    }
                },
                error: function(jqXHR,textStatus){
                    if(jqXHR.responseJSON.messageType == 'ALERT'){
                        MsgBox.Alert('error', jqXHR.responseJSON.message);
                        return;
                    }
                    else {
                        ajaxError(jqXHR, textStatus);
                    }
                },
                complete: function(response){
                    $('#loadingSpinnerLayer').remove();
                    $('#loadingSpinner').remove();

                    var userName = $('input[name=userName]').val();
                    var juminNo1 = $('input[name=juminNo1]').val();
                    var juminNo2 = $('input[name=juminNo2]').val();
                    var driverNo = $('input[name=driverNo]').val();
                    var issueDate = $('input[name=issueDate]').val();
                    var identity =$('input[name=identity]').val();

                    var formData = $('#masterForm').serializeArray();
                    var user = $('#masterForm #userName').val();


                    if(idType == '1'){
                        var html = '';

                        html += '<table>';
                        html += '<tr>';
                        html += '<td>성명</td>';
                        html += '<td>' + userName + '</td>';
                        html += '</tr>';
                        html += '<tr>';
                        html += '<td>주민번호</td>';
                        html += '<td>' + juminNo1 +'-'+ juminNo2 + '</td>';
                        html += '</tr>';
                        html += '<tr>';
                        html += '<td>발급일자</td>';
                        html += '<td>' + issueDate + '</td>';
                        html += '</tr>';
                        html += '</table>';

                        $('#kycStatus').html(html);

                    } else {
                        var html = '';
                        html += '<table>';
                        html += '<tr>';
                        html += '<td>성명</td>';
                        html += '<td>' + userName + '</td>';
                        html += '</tr>';
                        html += '<tr>';
                        html += '<td>주민번호</td>';
                        html += '<td>' + juminNo1 +'-'+ juminNo2 + '</td>';
                        html += '</tr>';
                        html += '<tr>';
                        html += '<td>면허번호</td>';
                        html += '<td>' + driverNo + '</td>';
                        html += '</tr>';
                        html += '</table>';

                        $('#kycStatus').html(html)

                    }
                },
            });
        });
    }
        //신분증 정보확인
        if(btnId == "nextBtn3"){
            var formData = $('#masterForm').serializeArray();
            var licenseNo =$('#licenseNo').val();
            licenseNo = driverNo;
            identity = juminNo1 + juminNo2;
            juminNo = juminNo1 + juminNo2;

            jsonData['identity'] = identity;
            jsonData['juminNo'] = identity;
            jsonData['userName'] = userName;
            jsonData['issueDate'] = issueDate;
            jsonData['driverNo'] = driverNo;

            formData.forEach(function (o) {
                if(o.value != null && o.value != ''){
                    jsonData[o.name] = o.value;
                }
            });

        MsgBox.Confirm("신분증 진위 여부를 판별 합니다.\n3~10초 정도 소요될 수 있습니다." , function(){
            if(idType == '1'){
                    $.ajax({
                        url: usebStatusIdCard,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify(jsonData),
                        beforeSend: function(){
                            $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
                        },
                        complete: function(response){
                            $('#loadingSpinnerLayer').remove();
                            $('#loadingSpinner').remove();
                        },
                        success: function(response) {
                            if (response && response.status == 'SUCCESS' ){
                                $('#kycStep4').css('display','block');
                                $('#kycStep3').css('display','none');
                                 //console.log(response);
                            }
                            else{
                                MsgBox.Alert("error");
                                return false;
                            }

                        },error: function(jqXHR,textStatus){
                            if(jqXHR.responseJSON.messageType == 'ALERT'){
                                MsgBox.Alert('error', jqXHR.responseJSON.message);
                                return;
                            }
                            else {
                                ajaxError(jqXHR, textStatus);
                            }
                        },
                    });
            }
            else{
                $.ajax({
                    url: usebStatusDriverCard,
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify(jsonData),
                    beforeSend: function(){
                        $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
                    },
                    complete: function(response){
                        $('#loadingSpinnerLayer').remove();
                        $('#loadingSpinner').remove();
                    },
                    success: function(response) {
                        if (response && response.status == 'SUCCESS' ){

                            $('#kycStep4').css('display','block');
                            $('#kycStep3').css('display','none');
                             //console.log(response);
                        }
                        else{
                            MsgBox.Alert("신분증 진위여부에 실패했습니다. 다시 시도해주세요.");
                            return false;
                        }

                    },error: function(jqXHR,textStatus){
                        if(jqXHR.responseJSON.messageType == 'ALERT'){
                            MsgBox.Alert('error', jqXHR.responseJSON.message);
                            return;
                        }
                        else {
                            ajaxError(jqXHR, textStatus);
                        }
                    },
                });
            }
        });
    }


    //출금 계좌 등록
    if(btnId =="nextBtn4"){
        if($.trim($('#kycStep4 #newBankCode').val())=='') {
            idInvalTextView('newBankCode', 'red', '[은행을 선택해 주세요.]');
            $('#kycStep4 #newBankCode').focus();
            return false;
        }
        else{
            $("#kycStep4 #newBankCodes").removeClass('is-invalid');
            $('#newBankCode-invalid').empty();
        }
        if($.trim($('#kycStep4 #newAccountNum').val())==''){
             idInvalTextView('newAccountNum' , 'red' , '[계좌번호를 입력해주세요.]');
             $('#kycStep4 #newAccountNum').focus();
             return false;
        }else{
             if(!regExp.test(newAccountNum)){
               idInvalTextView('newAccountNum' , 'red' , '[숫자만 입력해주세요.]');
               $('#newAccountNum #newBankCode').focus();
               return false;
             }
             else {
                $("#kycStep4 #newAccountNums").removeClass('is-invalid');
                $('#kycStep4-invalid').empty();
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
                        complete: function(){
                            $('#loadingSpinnerLayer').remove();
                            $('#loadingSpinner').remove();
                        },
                        success: function(result) {
                            if ( result && result.status == 'SUCCESS' ){
                                $("#accountOwnerName").val(result.data.accountOwnerName);
                                $("#trdNo").val(result.data.data.trdNo);
                                $("#mchtTrdNo").val(result.data.data.mchtTrdNo);

                                $("#bankCode").val($('#newBankCode').val());
                                $("#accountNumber").val($('#newAccountNum').val());

                                $('#kycStep5').css('display','block');
                                $('#kycStep4').css('display','none');
                            }
                            else {
                                idInvalTextView('newAccountNum', 'red', '['+ result.message +']');
                                $('#kycStep4 #newAccountNum').focus();
                                return false;
                            }
                                $("#kycStep4 #newAccountNum").removeClass('is-invalid');
                                $('#authNo-invalid').empty();
                                return false;
                        },
                        error: function(jqXHR,textStatus){
                            if(jqXHR.responseJSON.messageType == 'ALERT'){
                                MsgBox.Alert('error', jqXHR.responseJSON.message);
                                return;
                            }
                            else {
                                ajaxError(jqXHR, textStatus);
                            }
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

    // 본인 계좌 인증
    if(btnId =="nextBtn5"){
        if($.trim($('#kycStep5 #authNo').val())=='') {
            idInvalTextView('authNo', 'red', '[고객님 계좌의 입금내역에 표시된 4자리의 숫자를 입력 해주세요.]');
            $('#kycStep5 #authNo').focus();
            return false;
        }else {
            if(authNo.length < 4){
                idInvalTextView('authNo', 'red', '[4자리의 인증번호 숫자를 입력 해주세요.]');
                $('#kycStep5 #authNo').focus();
                return false;
            }
            else{
                $("#kycStep5 #authNos").removeClass('is-invalid');
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
            async: false,
            success: function(result) {
                if ( result && result.status == 'SUCCESS' ){
                     $('#kycStep6').css('display','block');
                     $('#kycStep5').css('display','none');
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
                if(jqXHR.responseJSON.messageType == 'ALERT'){
                    MsgBox.Alert('error', jqXHR.responseJSON.message);
                    return;
                }
                else {
                    ajaxError(jqXHR, textStatus);
                }
            },
        });
    }

    $("#nextBtn5").on("click", function() {
        var bank_name = $('input[name=bankName]').val();
        var account_num = $('input[name=accountNumber]').val();

        $('#confirmBankName').text(bank_name);
        $('#confirmBankAccount').text(account_num);

    });

    if(btnId == "nextBtn7"){
        var engFirstNm =$("#engFirstNm").val();			                            // 영문명(성)
        var engLastNm =$("#engLastNm").val();			                            // 영문명(이름)
        var addr = $("#addr").val();			                                    // 자택 주소
        var postcode = $('#postcode').val();		                                // 자택 우편번호
        var mbAddrJibeon = $('#mbAddrJibeon').val();		                                // 자택 우편번호
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
        jsonData['mbAddrJibeon'] = mbAddrJibeon;


        //본인 정보 입력 Validation Check
        if($.trim($('#kycStep7 #engLastNm').val())=='') {
            idInvalTextView('engNm', 'red', '[영문(성)을 입력해주세요!]');
            $('#kycStep7 #engLastNm').focus();
            return false;
        }
        else if($.trim($('#kycStep7 #engFirstNm').val())=='') {
            idInvalTextView('engNm', 'red', '[영문(이름)을 입력해주세요!]');
            $('#kycStep7 #engFirstNm').focus();
            return false;
        }
        else {
            var engNm = $.trim($('#kycStep7 #engFirstNm').val()) + $.trim($('#kycStep7 #engLastNm').val());
            $('#kycStep7 #engNm').val(engNm);
            $("#engLastNm,#engFirstNm").removeClass('is-invalid');
            $('#engNm-invalid').empty();
        }
        if($.trim($('#kycStep7 #addr').val())=='') {
            idInvalTextView('addr', 'red', '[주소 및 우편번호는 필수 정보입니다.]');
            $('#kycStep7 #addr').focus();
            return false;
        }else {
            $("#kycStep7 #addrs").removeClass('is-invalid');
            $('#addr-invalid').empty();
        }
        if($.trim($('#kycStep7 #job').val())=='') {
            idInvalTextView('job', 'red', '[직업을 선택해 주세요.]');
            $('#kycStep7 #job').focus();
            return false;
        }
        else {
            $("#kycStep7 #jobs").removeClass('is-invalid');
            $('#job-invalid').empty();
        }
        if($.trim($('#kycStep7 #purpose').val())=='') {
            idInvalTextView('purpose', 'red', '[거래목적을 선택해 주세요.]');
            $('#kycStep7 #purpose').focus();
            return false;
        }
        else {
            $("#kycStep7 #purposed").removeClass('is-invalid');
            $('#purpose-invalid').empty();
        }
        if($.trim($('#kycStep7 #tranFundSourceDiv').val())=='') {
            idInvalTextView('tranFundSourceDiv', 'red', '[거래자금출처를 선택해 주세요.]');
            $('#kycStep7 #tranFundSourceDiv').focus();
            return false;
        }
        else {
            $("#kycStep7 #tranFundSourceDived").removeClass('is-invalid');
            $('#tranFundSourceDiv-invalid').empty();
        }
        if($.trim($('#kycStep7 #countryCode').val())=='') {
            idInvalTextView('countryCode', 'red', '[국적을 선택해 주세요.]');
            $('#kycStep7 #countryCode').focus();
            return false;
        }
        else {
            $("#kycStep7 #countryCodes").removeClass('is-invalid');
            $('#countryCode-invalid').empty();
        }
        MsgBox.Confirm("본인확인정보를 등록 하시겠습니까?" , function(){
            $.ajax({
                url: mpWflUrl,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(jsonData),
                success: function(result) {
                    if ( result && result.status == 'SUCCESS' ){
                        accountUpdates();
                        kakaoUpdate();

                        $('#kycStep8').css('display','block');
                        $('#kycStep7').css('display','none');
                    }else{
                        //console.log(result);
                    }
                },
                error: function(jqXHR,textStatus){
                    if(jqXHR.responseJSON.messageType == 'ALERT'){
                        MsgBox.Alert('error', jqXHR.responseJSON.message);
                        return;
                    }
                    else {
                        ajaxError(jqXHR, textStatus);
                    }
                }
            });
        });
    }
    return true;
}

// onchange event
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


function kakaoUpdate(){
    var jsonData = new Object();
    var memberIdx = $("#memberIdx").val();  // mbNo

    jsonData['memberIdx'] = memberIdx;

    $.ajax({
        url: memberVirtualAccountRegister,
        type: "POST",
        contentType: "application/json",
        dataType: "json",
        async: false,
        data: JSON.stringify(jsonData),
        success: function(result) {
            if ( result && result.status == 'SUCCESS' ){
                // console.log(result);
            }
            else {
                // console.log(result);
            }
        },
        error: function(jqXHR,textStatus){
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



