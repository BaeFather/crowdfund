$.fn.serializeFormMultiPartDataToUrl = function (url) {

    var finalFormData = new FormData();

    var formData = $(this).serializeArray();
    var data = new Object();

    formData.forEach(function (o) {
        if(o.value != null && o.value != ''){
            data[o.name] = o.value;
        }
    });

    finalFormData.append('formData', new Blob([ JSON.stringify(data) ], {type : "application/json"}));

    var fileList = $('.custom-file-input');
    var fileColumnList = [];
    for(var i = 0; i < fileList.length; i ++){
        if(fileList[i].files.length > 0){

            for (var j = 0; j < fileList[i].files.length; j++) {
                finalFormData.append('file',fileList[i].files[j]);
                fileColumnList.push($('.custom-file-input').eq(i).data('columnName'));
            }
        }
    }
    finalFormData.append('fileColumnName', new Blob([ JSON.stringify(fileColumnList) ], {type : "application/json"}));

    console.log(finalFormData);

    $.ajax({
        url: url,
        method: "POST",
        data: finalFormData,
        dataType: "JSON",
        contentType: false,
        processData: false,
        enctype : 'multipart/form-data',
        success: function(response){
            if ( response && response.status == 'SUCCESS' ){
                MsgBox.Alert("suc", response.message, function() { location.reload(); });
            }else {
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
};

$.fn.serializeFormMultiPartDataToUrlInvestType = function (url) {

    var finalFormData = new FormData();

    var formData = $(this).serializeArray();
    var data = new Object();

    formData.forEach(function (o) {
        if(o.value != null && o.value != ''){
            data[o.name] = o.value;
        }
    });

    finalFormData.append('formData', new Blob([ JSON.stringify(data) ], {type : "application/json"}));

    var fileList = $('.custom-file-input');
    var fileColumnList = [];
    for(var i = 0; i < fileList.length; i ++){
        if(fileList[i].files.length > 0){

            for (var j = 0; j < fileList[i].files.length; j++) {
                finalFormData.append('file',fileList[i].files[j]);
                fileColumnList.push($('.custom-file-input').eq(i).data('columnName'));
            }
        }
    }
    finalFormData.append('fileColumnName', new Blob([ JSON.stringify(fileColumnList) ], {type : "application/json"}));

    console.log(finalFormData);

    $.ajax({
        url: url,
        method: "POST",
        data: finalFormData,
        dataType: "JSON",
        contentType: false,
        processData: false,
        enctype : 'multipart/form-data',
        success: function(response){

            if ( response && response.status == 'SUCCESS' ){
                MsgBox.Alert("suc", response.message, function() { location.reload(); });
                $("#invest-type-change").modal("hide");
            } else if(response.status == 'FAILED') {
                console.log(response.message);
                invalidText('formFileMultiple2', 'red', '['+ response.message +']');
            } else {
                if(response.data != null){
                    var errorStr = '';
                    for(var i = 0; i <  response.data.length; i++){
                        errorStr += response.data[i].defaultMessage
                    }
                    MsgBox.Alert("error", errorStr);
                }else{
                    invalidText('file', 'red', '['+ response.message +']');
                }
            }
        },
        error: function(response){
            MsgBox.Alert("error", response.responseJSON.message);
        }
    });
};

