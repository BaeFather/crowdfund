var reviewUrl = "list.ajax.php";
var reviewWriteUrl = "process.php";
var page = 1;
var section = "1";

function check_review_load(section)
{
	var str = "section="+section+"&page="+page+"&pkd=1";
	$.ajax({
		type : 'POST',
		url : reviewUrl,
		data : str,
		success : function(data){
			$("#interview_list").html(data);
			page++;
			$("#page").val(page);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
			console.log(errorThrown);
			return false;
		}
	});
}

function check_review_list(event)
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

	var str = "section="+section+"&page="+page+"&pkd=";
	$.ajax({
		type : 'POST',
		url : reviewUrl,
		data : str,
		success : function(data){
			if(data == "end") { alert("등록된 글이 없습니다."); return false; }
		  var strdata = $("#interview_list").html();
			strdata = strdata + data;
			$("#interview_list").html(strdata);
			page++;
			$("#page").val(page);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
			console.log(errorThrown);
			return false;
		}
	});
}

$(document).on("click",".btnServiceOpen",function(e) {
	 var i = $(this).index();
	 var seq= document.getElementsByName("SE[]")[i];

	var str= "SE="+seq.value;

	$.ajax({
		type : 'POST',
		url : "view.ajax.php",
		data : str,
		dataType: 'json',
		success : function(data)
		{
			if(data.retcode == "OK")
			{
				if(data.retval)
				{
					$("#box").html(data.retval);
				}

				$.blockUI({
					message: $('#myModal'),
					css: { width:'0px',height:'0px',border:'0px' }
				});

			} else if(data.retcode == "X") {
				var stralert = decodeURIComponent(data.retalert);
				alert(stralert.replace("+"," "));
			}
			return false;
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
			return false;
		}
	});

});

$(document).on("click",".btnServiceClose",function(e) {
	$.unblockUI();
	return false;
});

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

function check_review_w_form(fmname, event)
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

		var frm = $('#'+fmname);
		var str = frm.serialize();

		var formData = new FormData();
		var intLength = $("input[name='thumbnail']").length;

		for(var j=0;j<intLength;j++)
		{
			formData.append('thumbnail', $("input[name='thumbnail']")[j].files[0]);
		}

		var strarr = str.split("&");
		var strdarr = new Array();

		for(var i=0;i<strarr.length;i++)
		{
				strdarr = strarr[i].split("=");
				if(parseInt(strdarr[0].indexOf("%5B%5D")) > 1)
				{
					strdarr[0] = strdarr[0].replace("%5B%5D","")+"[]";
				}
				formData.append(strdarr[0],strdarr[1]);
		}

		$.ajax({
			type : 'POST',
			url : reviewWriteUrl,
			data : formData,
			contentType: false,
			processData: false,
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

function check_review_w2_form(fmname, event)
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

		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : reviewWriteUrl,
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