function idInvalTextView(id, color, msg){
    $('#' + id).addClass('is-invalid');
    $('#' + id + '-invalid').css("color", color);
    $('#' + id + '-invalid').text(msg);
}
    var jsonData = new Object();
    var name =$('#name').val();
    var phone =$('#phone').val();
    var email =$('#email').val();
    var isEst = false;
    var content =$('#content').val();
    var isAgree = $('#is_agree').is(':checked') ? 1 : '';

    jsonData['name'] = name;
    jsonData['phone'] = phone;
    jsonData['email'] = email;
    jsonData['isEst'] = isEst;
    jsonData['content'] = content;
    jsonData['isAgree'] = isAgree;

    // console.log(jsonData);
    // console.log(isAgree);

    /*$("input[name=isEst]").on("click", function() {
        if($(this).val()) {
            isEst = true;
        }
    });*/

    function validation(){
        if($.trim($('#request1 #name').val())=='') {
            idInvalTextView('name', 'red', '[성명을 입력해 주세요.]');
            return false;
        }
        else {
             $("#request1 #names").removeClass('is-invalid');
             $('#name-invalid').empty();
        }

        if($.trim($('#request1 #phone').val())=='') {
            idInvalTextView('phone', 'red', '[연락처를 입력해 주세요.]');
            return false;
        }
        else if(phone_check($("#phone").val()) == false){
            idInvalTextView('phone', 'red', '[잘못된 형식의 번호입니다.]');
            return false;
        }
        else {
            $("#request1 #phones").removeClass('is-invalid');
            $('#phone-invalid').empty();
        }

        if($.trim($('#request1 #email').val())=='') {
            idInvalTextView('email', 'red', '[이메일을 입력해 주세요.]');
            return false;
        }else if(email_check($("#email").val()) == false){
            idInvalTextView('email', 'red', '[잘못된 형식의 이메일 주소입니다.]');
            return false;
        }
        else {
            $("#request1 #emails").removeClass('is-invalid');
            $('#email-invalid').empty();
        }

        if($.trim($('#request1 #content').val())=='') {
            idInvalTextView('content', 'red', '[문의내용을 입력해 주세요.]');
            return false;
        }
        else {
            $("#request1 #contents").removeClass('is-invalid');
            $('#content-invalid').empty();
        }
    }


    //본인 정보 입력 Validation Check
    function formSubmit(){

        var jsonData = new Object();
        var name =$('#name').val();
        var phone =$('#phone').val();
        var email =$('#email').val();
        var isEst = $('input[type=radio][name=isEst]:checked').val();
        var content =$('#content').val();
        var isAgree = $('#is_agree').is(':checked') ? 1 : '';

        jsonData['name'] = name;
        jsonData['phone'] = phone;
        jsonData['email'] = email;
        jsonData['isEst'] = isEst;
        jsonData['content'] = content;
        jsonData['isAgree'] = isAgree;

        // console.log(jsonData);
        // console.log(name);
        // console.log(phone);
        // console.log(email);
        // console.log(isEst);
        // console.log(isAgree);


        if(isEmpty(name)) {
            idInvalTextView('name', 'red', '[성명을 입력해 주세요.]');
            return false;
        }
        else {
             $("#request1 #names").removeClass('is-invalid');
             $('#name-invalid').empty();
        }
        if($.trim($('#request1 #phone').val())=='') {
            idInvalTextView('phone', 'red', '[연락처를 입력해 주세요.]');
            return false;
        }

        else if(phone_check($("#phone").val()) == false){
            idInvalTextView('phone', 'red', '[잘못된 형식의 번호입니다.]');
            return false;
        }

        else {
            $("#request1 #phones").removeClass('is-invalid');
            $('#phone-invalid').empty();
        }

        if($.trim($('#request1 #email').val())=='') {
            idInvalTextView('email', 'red', '[이메일을 입력해 주세요.]');
            return false;
        }

        else if(isEmpty(email_check(email))){
            idInvalTextView('email', 'red', '[잘못된 형식의 이메일 주소입니다.]');
            return false;
        }

        else {
            $("#request1 #emails").removeClass('is-invalid');
            $('#email-invalid').empty();
        }


        if($.trim($('#request1 #content').val())=='') {
            idInvalTextView('content', 'red', '[문의내용을 입력해 주세요.]');
            return false;
        }
        else {
            $("#request1 #contents").removeClass('is-invalid');
            $('#content-invalid').empty();
        }

        if(isEmpty(isAgree)) {
            MsgBox.Alert('error','개인 정보 수집 및 이용에 동의해 주세요.');
            return false;
        }


        MsgBox.Confirm("무료상담 신청을 등록하시겠습니까?" , function(){

            $.ajax({
                url: corpInvestGuideInsert,
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                traditional: true,
                data: JSON.stringify(jsonData),
                success: function(result) {
                // console.log(result);
                    if ( result && result.status == 'SUCCESS' ){
                        MsgBox.Alert(result.message, result.message , function() {location.href = corpInvestGuide;});
                    }
                    else {
                        MsgBox.Alert("error", result.message);
                    }
                },
                error: function(status,error,request){
                   alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                   // console.log(status,error,request);
                }
            });
        });
    }


    // 이메일 형식 체크
    function email_check(email) {
        var regex=/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return (email != '' && email != 'undefined' && regex.test(email));
    }

    //휴대폰 전화 체크
    function phone_check(phone) {
        var regex=/^[0-9]{2,3}[0-9]{3,4}[0-9]{4}$/;
        return (phone != '' && phone != 'undefined' && regex.test(phone));
    }


