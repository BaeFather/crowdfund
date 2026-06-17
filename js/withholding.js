
var withholdingProcessUrl = "/deposit/withholding.php";

function check_form(sval)
{
	var arr = document.getElementsByName(sval)[0].elements;

	for(var i=0;i<arr.length;i++)
	{
		attAttArr = "";

		if(arr[i].getAttribute("itemname") != undefined)
		{
			if(arr[i].type == "text" || arr[i].type == "textarea" || arr[i].type == "password" || arr[i].type == "select-one")
			{
				if(!arr[i].value) {
						alert(arr[i].getAttribute("itemname")+' 필수 항목 입니다.');
						arr[i].focus();
						return false;
				}
			}

			if(arr[i].getAttribute("itematt") != undefined)
			{
				var attAttArr = arr[i].getAttribute("itematt").split("^");
				if(attAttArr[0] == "int")
				{
					if((parseInt(attAttArr[1]) > parseInt(arr[i].value.length)) || !OnlyNum(arr[i].value))
					{
						alert(arr[i].getAttribute("itemname")+' 는 숫자만 입력이 가능하며 '+attAttArr[1]+' 자 이상이어야 합니다');
						arr[i].value="";
						arr[i].focus();
						return false;
					}
				}
			}

			if(arr[i].getAttribute("itemlan") != undefined)
			{
				var attAttArr = arr[i].getAttribute("itemlan").split("^");
				if(attAttArr[0] == "ko")
				{
					if((parseInt(attAttArr[1]) > parseInt(arr[i].value.length)) || !korCodeCheck(arr[i].value))
					{
						alert(arr[i].getAttribute("itemname")+' 는 한글만 입력이 가능하며 '+attAttArr[1]+' 자 이상이어야 합니다');
						arr[i].value="";
						arr[i].focus();
						return false;
					}
					if (isEmpty(arr[i].value))
					{
						alert(arr[i].getAttribute("itemname")+' 는 공백없이 입력이 가능하며 '+attAttArr[1]+' 자 이상이어야 합니다');
						arr[i].value="";
						arr[i].focus();
						return false;
					}
				}
			}

			if(arr[i].type == "radio" || arr[i].type == "checkbox")
			{
				var radiotrue = false;

				var radioname = arr[i].getAttribute("name");

				var radionamelen = document.getElementsByName(radioname).length;

				for(var j=0;j<radionamelen;j++)
				{
					if(document.getElementsByName(radioname)[j].checked == true)
					{
						 radiotrue = true;
						 break;
					}
				}

				if(radiotrue == false)
				{
					alert(arr[i].getAttribute("itemname")+' 필수 항목 입니다.');
					arr[i].focus();
					return false;
				}
			}
		}
		else
		{
			if(arr[i].getAttribute("itematt") != undefined)
			{
				var attAttArr = arr[i].getAttribute("itematt").split("^");
				if(attAttArr[0] == "int" && parseInt(arr[i].value.length) > 0)
				{
					if((parseInt(attAttArr[1]) > parseInt(arr[i].value.length)) || !OnlyNum(arr[i].value))
					{
						alert(attAttArr[2]+' 는 숫자만 입력이 가능하며 '+attAttArr[1]+' 자 이상이어야 합니다');
						arr[i].value="";
						arr[i].focus();
						return false;
					}
				}
			}
		}
	}
	return true;
}

// 한글체크
function korCodeCheck($str){
	var str = $str;
	var korCodeCheck = true;
	for(i=0; i<str.length; i++){
		if(!((str.charCodeAt(i) > 0x3130 && str.charCodeAt(i) < 0x318F) || (str.charCodeAt(i) >= 0xAC00 && str.charCodeAt(i) <= 0xD7A3)))
		{
			korCodeCheck = false; //한글이 아닐경우
			break;
		}
	}
	return korCodeCheck
}

// 공백체크
function isEmpty( data ) {
	 for ( var i = 0 ; i < data.length ; i++ )    {
		if ( data.substring( i, i+1 ) == " " )
		 return true;
	 }
	 return false;
}

// 숫자만 입력 기입
function OnlyNum(word)
{
	reOnlyNum = new RegExp("[0-9]", "g");
	var returnValue = true;
	for(i=0;i<word.length;i++)  {
		 if(!(word.substr(i,1).match(reOnlyNum))) {
			returnValue=false;
		}
	}
	return returnValue;
}

function check_w_form(fmname, event)
{
	if(!event)
	{
		event =window.event;
	}
	if(event.stopPropagation)
	{
		event.preventDefault();
		event.stopPropagation();
	} else {
		event.cancelBubble = true;
	}

	var checkform = check_form(fmname);

	if(checkform == false)
	{
			return false;
	}

	var mb_virtualkind = $("input[name='mb_virtualkind']").val();
	var s_date = $("input[name='s_date']").val();
	var e_date = $("input[name='e_date']").val();

	if(!mb_virtualkind || mb_virtualkind == "" || mb_virtualkind == 0)
	{
		alert("환급계좌 및 가상계좌를 먼저 발급 받으시기 바랍니다");
		return false;
	}

	if(s_date > e_date)
	{
		alert('시작일은 종료일보다 클수 없습니다.\n시작년월과 종료년월을 확인해주세요.');
		return false;
	}

	var frm = $('#'+fmname);
	var str = frm.serialize();

	$.ajax({
		type : 'POST',
		url : withholdingProcessUrl,
		data : str,
		dataType: 'json',
		success : function(data){

			if(data.retcode == "OK"){
				var stralert = decodeURIComponent(data.retalert);
					alert(stralert.replace("+"," "));
					window.location = data.retval;

			} else if(data.retcode == "X") {
				var stralert = decodeURIComponent(data.retalert);
					alert(stralert.replace("+"," "));

			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
			console.log(errorThrown);
			return false;
		}
	});
}

$('.dateym').datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		dateFormat: 'yy-mm',
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토'],
		closeText: "적용",
		currentText: "오늘",
		onClose: function(dateText, inst) {
				var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).datepicker('setDate', new Date(year, month, 1));
		},
		beforeShow : function(input, inst) {
				var datestr;
				if ((datestr = $(this).val()).length > 0) {
						year = datestr.substring(datestr.length-4, datestr.length);
						month = jQuery.inArray(datestr.substring(0, datestr.length-5), $(this).datepicker('option', 'monthNamesShort'));
						$(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
						$(this).datepicker('setDate', new Date(year, month, 1));
				}
		}
});