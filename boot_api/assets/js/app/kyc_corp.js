$('#KYCNextButton0').on('click', function() {
    var timestamp = Math.floor(+ new Date() / 1000);
    $('#kycStep0').css('display','none');
    $('#masterForm #fStime').val(timestamp);
    $('#kycStep1').css('display','block');
    $(location).attr('href','#');
});

$('#kycStep1 #customerTpCd').on('change', function() {
    idInvalRemove('customerTpCd');
    if( $('#kycStep1 #customerTpCd option:selected').val()=='01' ) {
        $('#estbmPuposDiv').slideDown(); $('#estbmPupos').attr('disabled',false);
        $('#corpRegNoDiv,#customerIndustryCodeDiv').slideUp();
        $('#corpRegNo1,#corpRegNo2,#customerIndustryCode,#industryCd').attr('disabled',true);
    }
    else {
        $('#estbmPuposDiv').slideUp(); $('#estbmPupos').attr('disabled',true);
        $('#corpRegNoDiv,#customerIndustryCodeDiv').slideDown();
        $('#corpRegNo1,#corpRegNo2,#customerIndustryCode,#industryCd').attr('disabled',false);
    }
});

$('#kycStep1 #customerIndustryCode').on('change', function() {
    idInvalRemove('customerIndustryCd1');
});
$('#kycStep1 #industryCd').on('change', function() {
    idInvalRemove('customerIndustryCd1');
});

$('#kycStep1 #corpPhone1').on('change', function() {
    idInvalRemove('corpPhone');
});

$('#kycStep1 #tranFundSourceDiv').on('change', function() {
    idInvalRemove('tranFundSourceDiv');
    if( $('#kycStep1 #tranFundSourceDiv option:selected').val() ) {
        $('#kycStep1 #tranFundSourceNm').val( $('#kycStep1 #tranFundSourceDiv option:selected').text() );
    }
    else {
        $('#kycStep1 #tranFundSourceNm').val('');
    }
    if( $('#kycStep1 #tranFundSourceDiv option:selected').val()=='B99' ) {
        $('#kycStep1 #tranFundSourceOther').val('기타소득');
    }
    else {
        $('#kycStep1 #tranFundSourceOther').val('');
    }
});

// 은행명 입력
function bankNameSet() {
    if($('#bankCode option:selected').val()) {
        $('#masterForm #bankName').val($('#bankCode option:selected').text());
    }
    else {
        $('#masterForm #bankName').val('');
    }
}
$(document).ready(function() {
    // 은행명 등록
    bankNameSet();

    // 법인유형 선택
    if(defineCustomerTpCd!='') {
        $('#kycStep1 #customerTpCd').val(defineCustomerTpCd);
    }
});
$('#kycStep1 #bankCode').on('change', function() {
    idInvalRemove('bankCode');
    bankNameSet();
});

$('#kycStep2 #ceoCountryCd').on('change', function() {
    idInvalRemove('ceoCountryCd');
});

$('#kycStep2 #mbHp').on('change', function() {
    idInvalRemove('mbHp');
});

$('#KYCNextButton1').on('click', function() {

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if(check_timestamp > 1800) {
        MsgBox.Alert('error', '입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
        window.location.reload();
    }

    if($.trim($('#kycStep1 #customerNm').val())=='') {
        idInvalTextView('customerNm', 'red', '[법인명를 입력해주세요!]');
        $('#kycStep1 #customerNm').focus();
        return;
    }
    else {
        $("#kycStep1 #customerNm").removeClass('is-invalid');
        $('#customerNm-invalid').empty();
    }

    if($.trim($('#kycStep1 #permitNo1').val())=='' || $.trim($('#kycStep1 #permitNo1').val()).length < 3) {
        idInvalTextView('permitNo', 'red', '[사업자등록번호를 입력해주세요!]');
        $('#kycStep1 #permitNo1').focus();
        return;
    }
    else if($.trim($('#kycStep1 #permitNo2').val())=='' || $.trim($('#kycStep1 #permitNo2').val()).length < 2) {
        idInvalTextView('permitNo', 'red', '[사업자등록번호를 입력해주세요!]');
        $('#kycStep1 #permitNo2').focus();
        return;
    }
    else if($.trim($('#kycStep1 #permitNo3').val())=='' || $.trim($('#kycStep1 #permitNo3').val()).length < 5) {
        idInvalTextView('permitNo', 'red', '[사업자등록번호를 입력해주세요!]');
        $('#kycStep1 #permitNo3').focus();
        return;
    }
    else {
        var permitNo = $.trim($('#kycStep1 #permitNo1').val()) + $.trim($('#kycStep1 #permitNo2').val()) + $.trim($('#kycStep1 #permitNo3').val());
        $('#permitNo').val(permitNo);
        $("#permitNo1,#permitNo2,#permitNo3").removeClass('is-invalid');
        $('#permitNo-invalid').empty();
    }

    if($('#kycStep1 #customerTpCd').val()=='') {
        idInvalTextView('customerTpCd', 'red', '[법인유형을 선택해주세요!]');
        $('#kycStep1 #customerTpCd').focus();
        return;
    }
    else {
        $("#kycStep1 #customerTpCd").removeClass('is-invalid');
        $('#customerTpCd-invalid').empty();
    }

    if( $('#kycStep1 select[name="customerTpCd"] option:selected').val()=='01' ) {
        if($.trim($('#kycStep1 #estbmPupos').val())=='') {
            idInvalTextView('estbmPupos', 'red', '[설립목적을 입력해주세요!]');
            $('#kycStep1 #estbmPupos').focus();
            return;
        }
        else {
            $("#estbmPupos").removeClass('is-invalid');
            $("#kycStep1 #estbmPupos").removeClass('is-invalid');
            $('#estbmPupos-invalid').empty();
        }
    }

    if($('#kycStep1 #corpRegNo1').is(':disabled')==false) {
        if($.trim($('#kycStep1 #corpRegNo1').val())=='' || $.trim($('#kycStep1 #corpRegNo1').val()).length < 6) {
            idInvalTextView('corpRegNo', 'red', '[법인등록번호 앞6자리를 입력해주세요!]');
            $('#kycStep1 #corpRegNo1').focus();
            return;
        }
        else if($.trim($('#kycStep1 #corpRegNo2').val())=='' || $.trim($('#kycStep1 #corpRegNo2').val()).length < 7) {
            idInvalTextView('corpRegNo', 'red', '[법인등록번호 뒤7자리를 입력해주세요!]');
            $('#kycStep1 #corpRegNo2').focus();
            return;
        }
        else {
            var corpRegNo = $.trim($('#kycStep1 #corpRegNo1').val()) + $.trim($('#kycStep1 #corpRegNo2').val());
            $('#corpRegNo').val(corpRegNo);
            $("#corpRegNo").removeClass('is-invalid');
            $('#corpRegNo-invalid').empty();
        }
    }

    if($('#kycStep1 #createDd').val()=='') {
        idInvalTextView('createDd', 'red', '[법인설립일을 입력해주세요!]');
        $('#kycStep1 #createDd').focus();
        return;
    }
    else {
        $("#createDd").removeClass('is-invalid');
        $('#createDd-invalid').empty();
    }

    if($('#kycStep1 #customerIndustryCode').is(':disabled')==false) {
        if($('#kycStep1 #customerIndustryCode').val()=='') {
            $('#industryCd').empty();
            idInvalTextView('customerIndustryCd1', 'red', '[업태를 선택해주세요!]');
            $('#kycStep1 #customerIndustryCode').focus();
            return;
        }
        else if($('#kycStep1 #industryCd').val()=='') {
            idInvalTextView('customerIndustryCd1', 'red', '[종목을 선택해주세요!]');
            $('#kycStep1 #industryCd').focus();
            return;
        }
        else {
            $("#customerIndustryCode,#industryCd").removeClass('is-invalid');
            $('#customerIndustryCd1-invalid').empty();
        }
    }

    if($('#kycStep1 #zipNum').val()=='') {
        idInvalTextView('mbAddrs', 'red', '[주소 및 우편번호는 필수 정보입니다.]');
        $('#kycStep1 #zipNum_button').focus();
        return;
    }
    else if($('#kycStep1 #mbAddr1').val()=='') {
        idInvalTextView('mbAddrs', 'red', '[법인 주소를 입력해주세요.]');
        $('#kycStep1 #zipNum_button').focus();
        return;
    }
    else if($.trim($('#kycStep1 #mbAddr2').val())=='') {
        idInvalTextView('mbAddrs', 'red', '[법인 상세주소를 입력해주세요!]');
        $('#kycStep1 #mbAddr2').focus();
        return;
    }
    else {
        $("#zipNum,#mbAddr1,#mbAddr2").removeClass('is-invalid');
        $('#mbAddrs-invalid').empty();
    }

    if($.trim($('#kycStep1 #corpPhone1').val())=='') {
        idInvalTextView('corpPhone', 'red', '[법인연락처(대표번호)를 선택해주세요!]');
        $('#kycStep1 #corpPhone1').focus();
        return;
    }
    else if($.trim($('#kycStep1 #corpPhone2').val())=='' || $.trim($('#kycStep1 #corpPhone2').val()).length < 3) {
        idInvalTextView('corpPhone', 'red', '[법인연락처(대표번호)를 3자 이상 입력해주세요!]');
        $('#kycStep1 #corpPhone2').focus();
        return;
    }
    else if($.trim($('#kycStep1 #corpPhone3').val())=='' || $.trim($('#kycStep1 #corpPhone3').val()).length < 3) {
        idInvalTextView('corpPhone', 'red', '[법인연락처(대표번호)를 3자 이상 입력해주세요!]');
        $('#kycStep1 #corpPhone3').focus();
        return;
    }
    else {
        var corpPhone = $.trim($('#kycStep1 #corpPhone1').val()) + $.trim($('#kycStep1 #corpPhone2').val()) + $.trim($('#kycStep1 #corpPhone3').val());
        $('#kycStep1 #corpPhone').val(corpPhone);
        $('#corpPhone-invalid').empty();
    }

    if($.trim($('#kycStep1 #mbEmail').val())=='') {
        idInvalTextView('mbEmail', 'red', '[이메일을 입력해주세요!]');
        $('#kycStep1 #mbEmail').focus();
        return;
    }
    else if(!re.test($('#kycStep1 #mbEmail').val())) {
        idInvalTextView('mbEmail', 'red', '[올바른 이메일 주소를 입력해주세요!]');
        $('#kycStep1 #mbEmail').focus();
        return;
    }
    else {
        $('#mbEmail-invalid').empty();
        $("#kycStep1 #mbEmail").removeClass('is-invalid');
    }

    if($('#kycStep1 #tranFundSourceDiv').val()=='') {
        idInvalTextView('tranFundSourceDiv', 'red', '[거래자금 출처를 선택해주세요!]');
        $('#kycStep1 #tranFundSourceDiv').focus();
        return;
    }

    if($('#kycStep1 #bankCode').val()=='') {
        idInvalTextView('bankCode', 'red', '[환급계좌 은행을 입력해주세요!]');
        $('#kycStep1 #bankCode').focus();
        return;
    }
    else {
        $('#bankCode-invalid').empty();
        $("#kycStep1 #bankCode").removeClass('is-invalid');
    }

    if($.trim($('#kycStep1 #accountNum').val())=='') {
        idInvalTextView('accountNum', 'red', '[계좌번호를 입력해주세요!]');
        $('#kycStep1 #accountNum').focus();
        return;
    }
    else {
        $('#accountNum-invalid').empty();
        $("#kycStep1 #accountNum").removeClass('is-invalid');
    }

    $('#kycStep1').css('display','none');
    $('#masterForm #fStime').val(timestamp);
    $('#kycStep2').css('display','block');
    $(location).attr('href','#');
});


var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

$('#KYCNextButton2').on('click', function() {
    if($.trim($('#kycStep2 #ceoNm').val())=='') {
        idInvalTextView('ceoNm', 'red', '[법인연락처(대표번호)를 입력해주세요!]');
        $('#kycStep2 #ceoNm').focus();
        return;
    }
    else {
        $("#kycStep2 #ceoNm").removeClass('is-invalid');
        $('#ceoNm-invalid').empty();
    }

    if($.trim($('#kycStep2 #ceoEngLastNm').val())=='') {
        idInvalTextView('ceoEngNm', 'red', '[대표자 영문(성)을 입력해주세요!]');
        $('#kycStep2 #ceoEngLastNm').focus();
        return;
    }
    else if($.trim($('#kycStep2 #ceoEngFirstNm').val())=='') {
        idInvalTextView('ceoEngNm', 'red', '[대표자 영문(이름)을 입력해주세요!]');
        $('#kycStep2 #ceoEngFirstNm').focus();
        return;
    }
    else {
        var ceoEngNm = $.trim($('#kycStep2 #ceoEngLastNm').val()) + ' ' + $.trim($('#kycStep2 #ceoEngFirstNm').val());
        $('#kycStep2 #ceoEngNm').val(ceoEngNm);
        $("#ceoEngLastNm,#ceoEngFirstNm").removeClass('is-invalid');
        $('#ceoEngNm-invalid').empty();
    }

    if($('#kycStep2 #ceoCountryCd').val()=='') {
        idInvalTextView('ceoCountryCd', 'red', '[대표자 국적을 입력해주세요!]');
        $('#kycStep2 #ceoCountryCd').focus();
        return;
    }
    else {
        $("#ceoCountryCd").removeClass('is-invalid');
        $('#ceoCountryCd-invalid').empty();
    }

    if($('#kycStep2 #ceoPostNo').val()=='') {
        idInvalTextView('ceoAddr', 'red', '[대표자 주소 우편번호를 입력해주세요!]');
        $('#kycStep2 #ceoPostNo_button').focus();
        return;
    }
    else if($('#kycStep2 #ceoAddr').val()=='') {
        idInvalTextView('ceoAddr', 'red', '[대표자 주소를 입력해주세요!]');
        $('#kycStep2 #ceoPostNo_button').focus();
        return;
    }
    else if($.trim($('#kycStep2 #ceoDtlAddr').val())=='') {
        idInvalTextView('ceoAddr', 'red', '[대표자 상세주소를 입력해주세요!]');
        $('#kycStep2 #ceoDtlAddr').focus();
        return;
    }
    else {
        $("#ceoPostNo,#ceoAddr,#ceoDtlAddr").removeClass('is-invalid');
        $('#ceoAddr-invalid').empty();
    }

    if($.trim($('#kycStep2 #mbHp1').val())=='') {
        idInvalTextView('mbHp', 'red', '[대표자 연락처(휴대폰)를 선택해주세요!]');
        $('#kycStep2 #mbHp1').focus();
        return;
    }
    else if($.trim($('#kycStep2 #mbHp2').val())=='' || $.trim($('#kycStep2 #mbHp2').val()).length < 3) {
        idInvalTextView('mbHp', 'red', '[대표자 연락처(휴대폰)를 3자 이상 입력해주세요!]');
        $('#kycStep2 #mbHp2').focus();
        return;
    }
    else if($.trim($('#kycStep2 #mbHp3').val())=='' || $.trim($('#kycStep2 #mbHp3').val()).length < 3) {
        idInvalTextView('mbHp', 'red', '[대표자 연락처(휴대폰)를 3자 이상 입력해주세요!]');
        $('#kycStep2 #mbHp3').focus();
        return;
    }
    else {
        mbHp = $('#kycStep2 #mbHp1').val() + $.trim($('#kycStep2 #mbHp2').val()) + $.trim($('#kycStep2 #mbHp3').val());
        $('#kycStep2 #mbHp').val(mbHp);
        $('#mbHp-invalid').empty();
    }

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if(check_timestamp > 1800) {
        MsgBox.Alert('error', '입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
        window.location.reload();
    }

    // 담당자연락처
    var mbHp = $.trim($('#kycStep2 #mbHp1').val()) + $.trim($('#kycStep2 #mbHp2').val()) + $.trim($('#kycStep2 #mbHp3').val());
    $('#masterForm #mbHp').val(mbHp);

    // 담당자명(=대표자명)
    $('#masterForm #mbName').val($('#kycStep2 #ceoNm').val());

    $('#kycStep2').css('display','none');
    $('#masterForm #fStime').val(timestamp);
    $('#kycStep3').css('display','block');
    $(location).attr('href','#');
});



$('#KYCNextButton3').on('click', function() {

    timestamp = Math.floor(+ new Date() / 1000);
    form_timestamp = $('#fStime').val();
    check_timestamp = timestamp - form_timestamp;

    if(check_timestamp > 1800) {
        MsgBox.Alert('error', '입력시작 후 30분이상 경과 되었습니다.\n다시 시도해주시기 바랍니다.');
        window.location.reload();
    }

    if( $('#masterForm #identifyZipFile').val() == '' ) {
        MsgBox.Alert('error', '제출서류 파일을 등록 해주세요.');
        $('#masterForm #identifyZipFile').focus();
        return;
    }
    else {
        var f = $('#masterForm')[0];
		var fileVal = f.identifyZipFile.value;
        var ext = fileVal.split('.').pop().toLowerCase(); //확장자분리

		if($.inArray(ext, ['jpg','jpeg','png','pdf','zip']) == -1) {
            MsgBox.Alert('error', 'jpg/png/pdf/zip 파일만 등록하실 수 있습니다.');
            return;
        }

    	var maxSize = 10 * 1024 * 1024;	// 10MB
        if( f.identifyZipFile.files[0].size > maxSize ) {
            MsgBox.Alert('error', '10MB를 초과한 파일은 등록하실 수 없습니다.');
            $('#identifyZipFile').val('');
            return;
        }
    }

    MsgBox.Confirm('법인정보등록을 하시겠습니까?', function() {

        // 폼전송 정의
        var finalFormData = new FormData();

        var formData = $('#masterForm').serializeArray();
        var data = new Object();

        formData.forEach(function (o) {
            if(o.value != null && o.value != ''){
                data[o.name] = o.value;
            }
        });

        finalFormData.append('formData', new Blob([ JSON.stringify(data) ], {type : "application/json"}));

//        var fileList = $('.file_control');
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
        //console.log(finalFormData);

        $.ajax({
            url: insertUrl,
            method: "POST",
            data: finalFormData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            enctype : 'multipart/form-data',
            success: function(response){
                if ( response && response.status == 'SUCCESS' ){
                    //console.log(response)
                    $(location).attr('href', '#');
                    $('#kycStep3').css('display','none');
                    $('#kycStepEnd').css('display','block');
                    $('#masterForm').reset();
                    MsgBox.Alert("suc", response.message, function() { location.reload(); });
                }
                else {
                    if(response.data != null){
                        var errorStr = '';
                        for(var i = 0; i <  response.data.length; i++){
                            errorStr += response.data[i].defaultMessage + '<br>'
                        }
                        MsgBox.Alert("error", errorStr);
                    }else{
                        MsgBox.Alert("error", response.message);
                    }
                }
            },
            error: function(response){
                MsgBox.Alert("error", response.responseJSON.message);
            }
        });
    });

});

$('#KYCPrevButton1').on('click',function(){
    $('#kycStep1').css('display','none');
    $('#kycStep0').css('display','block');
    $(location).attr('href','#');
});
$('#KYCPrevButton2').on('click',function(){
    $('#kycStep2').css('display','none');
    $('#kycStep1').css('display','block');
    $(location).attr('href','#');
});
$('#KYCPrevButton3').on('click',function(){
    $('#kycStep3').css('display','none');
    $('#kycStep2').css('display','block');
    $(location).attr('href','#');
});

function idInvalTextView(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);

//    if (color == 'red'){
//        $('#' + id).focus();
//    }
}

var win_zip = function(frm_name, frm_zip, frm_addr1, frm_addr2, frm_addr3, frm_jibeon) {

	if(typeof daum === 'undefined') {
		alert("다음 우편번호 postcode.v2.js 파일이 로드되지 않았습니다.");
		return false;
	}

	var zip_case = 2;   //0이면 레이어, 1이면 페이지에 끼워 넣기, 2이면 새창

	var complete_fn = function(data) {
		// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

		var fullAddr  = '';		// 최종 주소 변수
		var extraAddr = '';		// 조합형 주소 변수

		if (data.userSelectedType === 'R') {		// 사용자가 도로명 주소를 선택했을 경우
			fullAddr = data.roadAddress;
		}
		else {		// 사용자가 지번 주소를 선택했을 경우(J)
			fullAddr = data.jibunAddress;
		}

		// 사용자가 선택한 주소가 도로명 타입일때 조합한다.
		if(data.userSelectedType === 'R') {		//법정동명이 있을 경우 추가한다.
			if(data.bname !== '') {
				extraAddr += data.bname;
			}
			if(data.buildingName !== '') {		// 건물명이 있을 경우 추가한다.
				extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
			}
			extraAddr = (extraAddr !== '' ? ' ('+ extraAddr +')' : '');		// 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
		}

		// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
		var of = document[frm_name];

		of[frm_zip].value	   = data.zonecode;
		of[frm_addr1].value  = data.roadAddress + extraAddr;
		of[frm_jibeon].value = data.jibunAddress;

		of[frm_addr2].focus();

	};

	switch(zip_case) {
		case 1 :	//iframe을 이용하여 페이지에 끼워 넣기
			var daum_pape_id = 'daum_juso_page'+frm_zip,
			element_wrap = document.getElementById(daum_pape_id),
			currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

			if(element_wrap == null) {
				element_wrap = document.createElement("div");
				element_wrap.setAttribute("id", daum_pape_id);
				element_wrap.style.cssText = 'display:none;border:1px solid;left:0;width:100%;height:300px;margin:5px 0;position:relative;-webkit-overflow-scrolling:touch;';
				element_wrap.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-21px;z-index:1" class="close_daum_juso" alt="접기 버튼">';
				jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_addr1+'"]').before(element_wrap);
				jQuery("#"+daum_pape_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e) {
					e.preventDefault();
					jQuery(this).parent().hide();
				});
			}

			daum.postcode.load(function() {
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
						element_wrap.style.display = 'none';			// iframe을 넣은 element를 안보이게 한다.
						document.body.scrollTop = currentScroll;	// 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
					},
					// 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분.
					onresize : function(size) {
						element_wrap.style.height = size.height + "px";		// iframe을 넣은 element의 높이값을 조정한다.
					},
					width : '100%',
					height : '100%'
				}).embed(element_wrap);
			});

			element_wrap.style.display = 'block';
		break;

		case 2 :	//새창으로 띄우기
			daum.postcode.load(function() {
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
					}
				}).open();
			});
		break;

		default :   //iframe을 이용하여 레이어 띄우기
			var rayer_id = 'daum_juso_rayer'+frm_zip,
			    element_layer = document.getElementById(rayer_id);

			if(element_layer == null) {
				element_layer = document.createElement("div");
				element_layer.setAttribute("id", rayer_id);
				element_layer.style.cssText = 'display:none;border:5px solid;position:fixed;width:300px;height:460px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;z-index:10000';
				element_layer.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" class="close_daum_juso" alt="닫기 버튼">';
				document.body.appendChild(element_layer);
				jQuery("#"+rayer_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e) {
					e.preventDefault();
					jQuery(this).parent().hide();
				});
			}

			daum.postcode.load(function() {
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
						element_layer.style.display = 'none';
					},
					width : '100%',
					height : '100%'
				}).embed(element_layer);
			});

			element_layer.style.display = 'block';
	}
}


$(document).ready(function() {
    if($('#ceoCountryCd option:selected').val()=='') {
        $("#ceoCountryCd").val('KR').prop("selected", true);
    }
});


function idInvalRemove(id) {
    $('#' + id + '-invalid').empty();
}


$('.test-select').on('change',function(){
    var idx = $(this).index($(this).children('option:selected'));
    //console.log(idx);
    if(idx != 0){
        $(this).siblings('.invalid-feedback').empty();
        $(this).removeClass('is-invalid');
    }
});

$('#customerIndustryCode').on('change', function() {
    if($('#customerIndustryCode option:selected').val() == ''){
        $('#industryCd').attr('disabled', 'true');
        return false;
    }
    else {
        loadIndustryCodeList();
        $('#industryCd').removeAttr('disabled');
    }
});

$(document).ready(function() {
    if(defineCustomerIndustryCd) {
        $('#customerIndustryCode').val(defineCustomerIndustryCd).prop("selected",true);
        loadIndustryCodeList();
    }
});


function fileDownload(name){
    $("#objectName").val(name);
    $("form[name='file']").submit();
}
