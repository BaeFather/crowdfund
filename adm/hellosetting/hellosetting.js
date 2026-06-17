var strHellidUrl = "/adm/hellosetting/process.php";

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

		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : strHellidUrl,
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

function check_addr_yn(obj,objval)
{
	  var addr_si = $("select[name='addr_si']").val();
		if(!addr_si) { alert('취급지역을 우선선택하여 주세요'); return false;}

		var str = "kind=addr_si&addr_yn="+obj+"&addr_si="+addr_si;


		$.ajax({
			type : 'POST',
			url : strHellidUrl,
			data : str,
			dataType: 'json',
			success : function(data){
				//alert(data.retval);
				var str = "<ul class='ul_guide'>";
			  for(var i=0;i<data.retval.length;i++)
				{
					str += "<li class='li1'><input type='checkbox' name='addr_gu[]' value='"+data.retval[i]+"'";
					if(objval)
					{
						var objvalArr = objval.split(",");
						for(var j=0;j<objvalArr.length;j++)
						{
							if(objvalArr[j] ==data.retval[i])
							{
								str += " checked";
								break;
							}
						}
				  } else {
						if(obj == "A")
						{
							str += " checked";
						}
					}
					str += ">"+data.retval[i]+"</li>";
				}
				str += "</ul>";
				$("#addr_gu_area").html(str);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
				return false;
			}
		});
}

function check_addr_yn_pop(obj,objval)
{
	  var addr_si = $("select[name='addr_si']").val();
		if(!addr_si) { alert('취급지역을 우선선택하여 주세요'); return false;}

		var str = "kind=addr_si&addr_yn="+obj+"&addr_si="+addr_si;


		$.ajax({
			type : 'POST',
			url : strHellidUrl,
			data : str,
			dataType: 'json',
			success : function(data){
				//alert(data.retval);
				var str = "<ul class='ul_guide'>";
			  for(var i=0;i<data.retval.length;i++)
				{
					str += "<li class='li1'><input type='checkbox' name='addr_gu[]' value='"+data.retval[i]+"'";
					if(objval)
					{
						var objvalArr = objval.split(",");
						for(var j=0;j<objvalArr.length;j++)
						{
							if(objvalArr[j] ==data.retval[i])
							{
								str += " checked";
								break;
							}
						}
				  } else {
						if(obj == "A")
						{
							str += " checked";
						}
					}
					str += " disabled>"+data.retval[i]+"</li>";
				}
				str += "</ul>";
				$("#addr_gu_area").html(str);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
				return false;
			}
		});
}

function ltvplus()
{
	var obj = "<input type='hidden' name='SE2' value='' /><div style='width:100%;padding:7px 0;'>LTV <INPUT TYPE='TEXT' name='ltvs[]'  VALUE='' Class='input1'>% 이상 ";
	obj += "<INPUT TYPE='TEXT' name='ltvl[]'  VALUE='' Class='input1'>% 이하&nbsp;&nbsp;";
	obj += "&nbsp;&nbsp;선순의 금리 <INPUT TYPE='TEXT' name='ms[]'  VALUE='' Class='input1'>%";
	obj += " 후순위 금리 <INPUT TYPE='TEXT' name='ml[]'  VALUE='' Class='input1'>%</div>";

	var targetlayer = document.getElementById("ltvarea");
	var divname = "div"+itemcnt;
	var newDiv = document.createElement('div');
  newDiv.setAttribute("id", divname);

	targetlayer.appendChild(newDiv);
	document.getElementById(divname).innerHTML += obj;

	itemcnt++;
}