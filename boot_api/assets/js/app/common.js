/*
 * 값이 빈값인지 체크합니다. !value 하면 생기는 논리적 오류를 제거하기 위해 명시적으로 value == 사용 // [], {} 도 빈값으로 처리
 */
var isEmpty = function(value) {
    if (value == "" || value == null || value == undefined || (value != null && typeof value == "object" && !Object.keys(value).length)) {
        return true
    } else {
        return false
    }
};


/*
 * marking error message at invalid field
 */
var markingErrorField = function (response) {
    const errorFields = response.data;
    $('.invalid-feedback').text('');
    $('.form-control').removeClass('is-invalid');

    if(errorFields.length > 0) {
        var $field, $invalid, error;
        for(var i=0, length = errorFields.length; i<length;i++){
            error = errorFields[i];
            $field = $('#'+error['field']);
            $invalid = $('#'+error['field']+'-invalid');
            if($field && $field.length > 0){
                $field.addClass('is-invalid');
                $invalid.text($invalid.text() + '['+error.defaultMessage + ']');
            }
        }
    }
    else {
        MsgBox.Alert("error", response.message);
    }
};

/*
 * check digit
 */
function onlyDigit(e) {
  e.value = e.value.replace(/\D/g,'');
}

function onlyAlphabet(e){
    e.value = e.value.replace(/[^a-z]/gi,'');
}

function onlyAlphabetUpper(e)  {
	e.value = e.value.replace(/[^A-Za-z ]/ig, '').toUpperCase();
}

function onlyAlphabetLower(e)  {
	e.value = e.value.replace(/[^A-Za-z ]/ig, '').toLowerCase();
}

function onlyAlphabetNumber(e)  {
	e.value = e.value.replace(/[^0-9A-Za-z\.\(\)\- ]/ig, '');
}

/*
 * check number comma
 */
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/*
 * check string
 */
function stringCheck(fid, usetype) {
    spcStr = /[\{\}\[\]\/?.,;:|\)*`!^\-+┼<>@\#$%&\'\"\\\(\=]/gi;

    if(usetype) {
        if(usetype=='name')          spcStr = /[\{\}\[\]\/?,;:|*`!^\+┼<>\#$%&\'\"\\\=]/gi;
        else if(usetype=='email')    spcStr = /[\{\}\[\]\/?,;:|\)*`!^\+┼<>\#$%&\'\"\\\(\=]/gi;
        else if(usetype=='password') spcStr = /[\{\}\[\]\/?.,;:|`\+┼<>\\'\"\\\=]/gi;
    }

    haystack = new Array("--", "union ", "select ", "insert ", "from ", "where ", "update ", "drop ", " if", "if ", "join ", "decalre ", "and ", "or ", "column_name", "table_name    ", "openrowset", "substr", "substring", "xp_", "sysobjects", "syscolumns");
    objVal = $('#'+fid).val();

    if(objVal != '') {
        if( spcStr.test(objVal) ) {
            $('#'+fid).val( objVal.replace(spcStr, ""));
            return;
        }

        for(i=0; i<haystack.length; i++) {
            if( objVal.match(new RegExp(haystack[i], "i")) ) {
                $('#'+fid).val( objVal.replace(RegExp(haystack[i], "i"), "") );
                return;
            }
        }
    }
}

/*
 * check input
 */
function checkInput(msg){
	if(msg.replace(/(^\s*)|(\s*$)/g,"") == '') {
	    return false;
	}
	else {
	    return true;
	}
}

/*
 * ajax error action
 */
function ajaxError(jqXHR,textStatus){
    console.log('jqXHR.status :' + jqXHR.status);
    console.log('jqXHR.responseText :' + jqXHR.responseText);
    console.log('textStatus : ' + textStatus);

    if (jqXHR.status === 0) {
        MsgBox.Alert("error", 'Not connect.\n Verify Network.');
    } else if (jqXHR.status == 404) {
        MsgBox.Alert("error", 'Requested page not found. [404]');
    } else if (jqXHR.status == 500) {
        MsgBox.Alert("error", 'Internal Server Error [500].');
    } else if (textStatus === 'parsererror') {
        MsgBox.Alert("error", 'Requested JSON parse failed.');
    } else if (textStatus=== 'timeout') {
        MsgBox.Alert("error", 'Time out error.');
    } else if (textStatus === 'abort') {
        MsgBox.Alert("error", 'Ajax request aborted.');
    } else if (textStatus === 'error') {
        const obj = JSON.parse(jqXHR.responseText);
        if(obj.status === 'BIND_ERROR') {
            markingErrorField(obj);
        }
        else {
            MsgBox.Alert("error", obj.message + '[' + obj.code + ']');
        }
    } else {
        MsgBox.Alert("error", 'Uncaught Error.\n' + jqXHR.responseText);
    }
}


//쿠키값 Set
function setCookie(cookieName, value, exdays){
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var cookieValue = escape(value) + ((exdays==null) ? "" : "; expires=" +
    exdate.toGMTString());
    document.cookie = cookieName + "=" + cookieValue;
}

//쿠키값 Delete
function deleteCookie(cookieName){
    var expireDate = new Date();
    expireDate.setDate(expireDate.getDate() - 1);
    document.cookie = cookieName + "= " + "; expires=" + expireDate.toGMTString();
}

//쿠키값 가져오기
function getCookie(cookie_name) {
    var x, y;
    var val = document.cookie.split(';');

    for (var i = 0; i < val.length; i++) {
        x = val[i].substr(0, val[i].indexOf('='));
        y = val[i].substr(val[i].indexOf('=') + 1);
        x = x.replace(/^\s+|\s+$/g, ''); // 앞과 뒤의 공백 제거하기

        if (x == cookie_name) {
          return unescape(y); // unescape로 디코딩 후 값 리턴
        }
    }
}

/*
 * 숫자만 입력
 */
function formatNumber(numberString) {
    var selection = window.getSelection().toString();
    if(selection !== '') {
        return;
    }

    if( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
        return;
    }
    var input = numberString.value;
    var input = input.replace(/[\D\s\._\-]+/g, "");
    input = input ? parseInt( input, 10 ) : 0;
    numberString.value = (input === 0 ) ? "" : input.toLocaleString('ko-KR', {maximumSignificantDigits : 21});
}

function replaceNumberString(str) {
    if(str == '') {
        return;
    }
    var regex = /[^0-9]/g;
    return str.replace(regex, "");
}

/*
 * KYC 체크
 */
function checkKYC(kycState) {
    //KYC 체크
    if(kycState == 'N') {
       $("#kyc-confirm").modal("show");
        return false;
    }
    else if(kycState == 'I') {
        $("#kyc-ing").modal("show");
        return false;
    }
    else if(kycState == 'Y') {
        return true;
    }

    return false;
}

/*
 * alert text at input-box
 */
function alertText(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);
}