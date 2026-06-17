var sise_data;


function openCloseToc() {
    if(document.getElementById('toc-content').style.display === 'block') {
      document.getElementById('toc-content').style.display = 'none';
      document.getElementById('toc-toggle').textContent = '상품설명 더보기';
    } else {
      document.getElementById('toc-content').style.display = 'block';
      document.getElementById('toc-toggle').textContent = '상품설명 접기';
    }
}


function searchPostCode() {
    new daum.Postcode({
        oncomplete: function(data) {

            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var roadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 참고 항목 변수

            // 법정동명이 있을 경우 추가한다. (법정리는 제외)
            // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
            if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if(data.buildingName !== '' && data.apartment === 'Y'){
               extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }

            // 검색 결과 필드에 저장
            $('#si').val(data.sido);
            $('#gu').val(data.sigungu);
            $('#dong').val(data.bcode + "," + data.bname);

            var buildingName = ' ';
            if(data.buildingName !== '' && data.apartment === 'Y'){
                buildingName = data.buildingName;
            }

            $('#aptName').val(data.buildingCode + "," + buildingName);

            var fullAddr  = '';		// 최종 주소 변수
            var extraAddr = '';		// 조합형 주소 변수

            if (data.userSelectedType === 'R') {		// 사용자가 도로명 주소를 선택했을 경우
                fullAddr = data.roadAddress;
            }
            else {		// 사용자가 지번 주소를 선택했을 경우(J)
                fullAddr = data.jibunAddress;
            }
            $('#address').val(data.address);

            idInvalTextView('address', 'green', '');
            hyphenRealPrice(fullAddr);

        }
    }).open();
}

function hyphenRealPrice(fullAddr){
    var data  = new Object();

    $.ajax({
        url: hyphen_sise_url,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        traditional: true,
        data: JSON.stringify({
            "addr" : fullAddr
        }),
        beforeSend: function(){
            $("#aptArea").empty();  // 평형선택 초기화
            //$('#address').after('<div id="loadingSpinner" class="loading_wrap justify-content-center"><div class="loading_box"><div class="spinner-border" role="status"></div><p>Loading...</p></div></div>');
            $('body').append('<div id="loadingSpinnerLayer" class="loading-layer"></div><div id="loadingSpinner" class="loading-bar"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><p>L O A D I N G</p></div>');
        },
        success: function(response) {

            if(response.status == "SUCCESS"){
                sise_data = response.data.data;

                var bldcd = sise_data["outH0001"]["buildingCd"];
                $("#buildingcd").val(bldcd);

                $("#aptArea").empty();  // 평형선택 초기화
                $("#aptArea").append("<option value=''>전용면적 선택 (㎡)</option>");

                for (var i=0 ; i<sise_data["outH0001"]["list"].length; i++) {
                    $("#aptArea").append("<option value='" + i + ',' + sise_data["outH0001"]["list"][i]["areaSerialNumber"] + ',' + sise_data["outH0001"]["list"][i]["exclusiveSpace"] + "'>" + sise_data["outH0001"]["list"][i]["exclusiveSpace"] + '㎡ ' + sise_data["outH0001"]["list"][i]["supplySpaceType"] + "</option>");
                }
            }else if(response.status == "SEARCH_FAIL"){ // 검색결과 없을 때
                MsgBox.Alert('error', response.message, function(){
                    resetForm();
                    $('#tab2').trigger('click');
                });
            }else{
                MsgBox.Alert('error', response.message,function(){
                    resetForm();
                });
            }
        },
        error:function(response,status,error){
            if(response.responseJSON.messageType == 'ALERT'){
                MsgBox.Alert('error', response.responseJSON.message);
                return;
            }
            if(response.responseJSON){
                MsgBox.Alert('error', "해당 건물은 시세 조회가 불가합니다. 직접 입력창으로 이동합니다.",function(){
                    resetForm();
                    $('#tab2').trigger('click');
                });
            }else{
                MsgBox.Alert('error', response.message,function(){
                    resetForm();
                });
            }
        },
        complete: function(response){
            $('#loadingSpinnerLayer').remove();
            $('#loadingSpinner').remove();
        },
    });
}

function check_w_form_re() {

    if ($('#tab1').is(":checked")) {

        if (!$('#writeForm #address').val()) {
            //MsgBox.Alert("error","주소를 입력해주세요.");
            idInvalTextView('address', 'red', '[주소를 입력해 주세요.]');
            return;
        } else {
            idInvalTextView('address', 'green', '');
        }

        if (!$('#writeForm #aptArea').val()) {
            //MsgBox.Alert("error","전용면적을 선택해주세요.");
            idInvalTextView('aptArea', 'red', '[전용면적을 선택해주세요.]');
            return;
        } else {
            idInvalTextView('aptArea', 'green', '');
        }

        if (!$('#writeForm #floorNum').val()) {
            //MsgBox.Alert("error","층수를 입력해주세요.");
            idInvalTextView('floorNum', 'red', '[층수를 입력해주세요.]');
            return;
        } else {
            idInvalTextView('floorNum', 'green', '');
        }

        if (!$('#writeForm #dongNum').val()) {
            idInvalTextView('dongNum', 'red', '[동을 입력해주세요.]');
            return;
        } else {
            idInvalTextView('dongNum', 'green', '');
        }

        if (!$('#writeForm #hoNum').val()) {
            //MsgBox.Alert("error","호수를 입력해주세요.");
            idInvalTextView('hoNum', 'red', '[호수를 입력해주세요.]');
            return;
        } else {
            idInvalTextView('hoNum', 'green', '');
        }

    } else if ($('#tab2').is(":checked")) {
        if (!$('#detailWriteForm #aptName2').val()) {
            //MsgBox.Alert("error","아파트 및 상세주소를 입력해주세요.");
            idInvalTextView('aptName2', 'red', '[아파트 및 상세주소를 입력해주세요.]');
            return;
        } else {
            idInvalTextView('aptName2', 'green', '');
        }
    } else {
        MsgBox.Alert("error","선택 또는 직접입력중 하나를 선택하고 주소를 입력해 주세요.");
        return;
    }

    if($('#tab1').is(":checked")){
        // 조회된 시세로 대출 한도 설정
        var idx = 0;
        var floorNum = $('#floorNum').val();

        for (var i=0 ; i<sise_data["outH0001"]["list"].length; i++) {
            if ($("#aptArea").val() == i + ',' + sise_data["outH0001"]["list"][i]["areaSerialNumber"] + ',' + sise_data["outH0001"]["list"][i]["exclusiveSpace"]) {
                idx = i;
            }
        }

        if (idx >= 0) {
            var pcnt = get_pcnt($("#address").val());
            var prc = 0;

            // 1~3층은 하위 평균 매매가, 4층 이상은 일반 평균 매매가
            if(floorNum <= 3){
                prc  = sise_data["outH0001"]["list"][idx]["subAvrDealPrc"] * 10000;
            }else{
                prc  = sise_data["outH0001"]["list"][idx]["nomAvrDealPrc"] * 10000;
            }

            var rprc = parseInt(prc * pcnt);

            $("#price").val(Number(prc));
            $("#rprice").val(Number(rprc));
        }


        if(prc <= 0){
            MsgBox.Alert('error', "해당 조건의 시세 정보가 없습니다. 직접 입력창으로 이동합니다.",function(){
                resetForm();
                $('#tab2').trigger('click');
            });
        }else{
            $('#writeForm').submit();
        }

    }else if($('#tab2').is(":checked")){
        $('#detailWriteForm').submit();
    }
}

function idInvalTextView(id, color, msg){
    if (!msg) {
        $('#' + id).removeClass('is-invalid');
        $('#' + id + '-invalid').text(msg);
    } else {
        $('#' + id).addClass('is-invalid');
        //$('#' + id).css("background-image", 'none');
        $('#' + id + '-invalid').css("color", color);
        $('#' + id + '-invalid').text(msg);
    }
}

function get_pcnt(addr) {
    if (!addr) return;
    var si = addr.substring(0,2);
    var perc = 0;

    /*
    if (si=="서울") perc = 0.83;
    else if(si=="경기") perc = 0.80;
    else if(si=="인천") perc = 0.80;
    else perc = 0.75
    */

    perc = 0.70

    return perc;
}

function resetForm(){
    $("#aptArea").empty();  // 평형선택 초기화
    $("#aptArea").append("<option value=''>전용면적 선택 (㎡)</option>");
    $("#address").val("");
    $("#floorNum").val("");
    $("#address").val("");
    $("#dongNum").val("");
    $("#hoNum").val("");
}

