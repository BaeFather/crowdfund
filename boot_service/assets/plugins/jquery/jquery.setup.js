/*
 * Post Setup
 */
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="_csrf"]').attr('content') }
});

$(function () {
    $('.date-picker').datepicker({
        format: "yyyy-mm-dd",
        language: "ko",
        autoclose: true
    })

    // when btnSetDate is clicked
    $('.btnSetDate').on('click', function () {
        var noDay = $(this).data('day');

        if(noDay ==0) {
            $("#startDate").val('');
            $("#endDate").val('');

            return;
        }

        var today = new Date();
        var startDate = today;

        $("#endDate").datepicker('setDate', today);

        startDate.setDate(startDate.getDate() - (noDay - 1));
        $('#startDate').datepicker('setDate', startDate);
    });
});

//datatable excel download
function serverSideButtonAction(e, dt, node, config) {
    var me = this;
    var button = config.text.toLowerCase();
    if (typeof $.fn.dataTable.ext.buttons[button] === "function") {
        button = $.fn.dataTable.ext.buttons[button]();
    }
    var len = dt.page.len();
    var start = dt.page();
    dt.page(0);

    // Assim que ela acabar de desenhar todas as linhas eu executo a função do botão.
    // ssb de serversidebutton
    dt.context[0].aoDrawCallback.push({
        "sName": "ssb",
        "fn": function () {
            $.fn.dataTable.ext.buttons[button].action.call(me, e, dt, node, config);
            dt.context[0].aoDrawCallback = dt.context[0].aoDrawCallback.filter(function (e) { return e.sName !== "ssb" });
        }
    });
    dt.page.len(999999999).draw();
    setTimeout(function () {
        dt.page(start);
        dt.page.len(len).draw();
    }, 30000);
}

$.fn.serializeSearchForm = function (data) {
	// get current Form
	var form = $(this);
	// get all data
	var formData = form.serializeArray();

	// get dateRange
	if($("#startDate").val() != '' && $("#endDate") != '') {
        var dateRange = {
            columnKey: $("#columnKey").val(),
            startDate: $("#startDate").val() + ' 00:00:00',
            endDate: $("#endDate").val() + ' 23:59:59',
        };
        data['dateRange'] = dateRange;
	}

	if($("#keywordField").val() != '' && $("#keyword") != '') {
	    var keywordSearch = {
	        keywordField: $("#keywordField").val(),
	        keyword: $("#keyword").val(),
	    };
	    data['keywordSearch'] = keywordSearch;
	}

	// Add serialize array to return object
	formData.forEach(function (o) {
		data[o.name] = o.value;
	});


	// change all select el from 0 to ''
	form.find('select').each(function (i, o) {
		if ($(this).val() == "0")
			data[$(this).attr('name')] = "";
	});
	return JSON.stringify(data);
};

function getCodeName(pgCodeList, value) {
    var codeName;
    pgCodeList.forEach(element => {
        if(value == element.value) {
            codeName = element.label;
        }
    });
    return codeName;
}

jQuery.fn.serializeObject = function() {
    var obj = null;
    try {
        if (this[0].tagName && this[0].tagName.toUpperCase() == "FORM") {
            var arr = this.serializeArray();
            if (arr) {
                obj = {};
                jQuery.each(arr, function() {
                    obj[this.name] = this.value;
                });
            }//if ( arr ) {
        }
    } catch (e) {
        alert(e.message);
    } finally {
    }

    return obj;
};
