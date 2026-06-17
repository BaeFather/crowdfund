// 아이디 체크
var processUrl = "atploan_process.php";

dropComment = function(commidx) {
	if(confirm('게시글을 삭제 하시겠습니까?')) {
		$.ajax({
			url : "request.proc.ajax.php",
			type: "POST",
			dataType: "JSON",
			data:{
				mode: 'delete',
				commidx: commidx
			},
			success:function(data) {
				if(data.result=='SUCCESS') { alert('삭제 되었습니다.'); window.location.reload(); }
				else { alert(data.message); }
			},
			error:function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
}

function fn_number_coma(target, obj, idx)
{
		obj = obj.replace(/,/gi,"");

		if(!OnlyNum(obj))
		{
			alert("숫자만 입력이 가능합니다");
			$("input[name='"+target+"']").val("");
			return false;
		}
		var retval = numberWithCommas(obj);
		$("input[name='"+target+"']").eq(idx).val(retval);
}

function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function fn_check_number(target, obj)
{
		obj = obj.replace(/,/gi,"");

		if(!OnlyNum(obj))
		{
			alert("숫자만 입력이 가능합니다");
			$("input[name='"+target+"']").val("");
			return false;
		}
}

function check_search(fmname)
{
	var form = check_form(fmname);

	if(form == false)
	{
		return false;
	}
	$("#"+fmname).attr("method","get");
	$("#"+fmname).submit();

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

		var rdo_apt = $("input[name='rdo_apt']:checked").val();
		var apt_name = $("select[name='apt_name']").val();
		var apt_area = $("select[name='apt_area']").val();
		var dong_num = $("input[name='dong_num']").val();
		var ho_num = $("input[name='ho_num']").val();
		var floor_num = $("input[name='floor_num']").val();
		var apt_name2 = $("input[name='apt_name2']").val();

		if(rdo_apt == "1")
	  {
			 if(!apt_name) { alert('아파트를 선택해주세요'); return false; }
			 if(!apt_area) { alert('전용면적을 선택해주세요'); return false; }
			 if(!dong_num) { alert('동을 입력해주세요'); return false; }
			 if(!ho_num) { alert('호수를 입력해주세요'); return false; }
			 if(!floor_num) { alert('층수를 입력해주세요'); return false; }
		} else if (rdo_apt == "2") {
			 if(!apt_name2) { alert('아파트명 및 상세주소를 입력해주세요'); return false; }
	  }

		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : processUrl,
			data : str,
			dataType: 'json',
			success : function(data){
				//console.log(data);
				if(data.retcode == "OK"){
					$("input[name='price']").val(data.retprice);
					$("input[name='rprice']").val(data.retrprice);
					$("#wform").attr("method","POST");
					$("#wform").attr("action",data.retval);
					$("#wform").submit();

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

function check_request_end()
{
	$.ajax({
		type : 'POST',
		url : "loan_end.php",
		success : function(data){
		}
	});
}


function check_request_form(fmname, event)
{
	grecaptcha.ready(function() {
		grecaptcha.execute('6LdBq8UaAAAAAGHv6kxJ2lQ5gPcv5-e7E7scf9FR', {action: 'submit'}).then(function(token) {
			
			$('#'+fmname).prepend('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
			
			
			var frm = $('#'+fmname);
			var str = frm.serialize();
			
			
			if(!event)
			{
			   event = window.event;
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

			var rdo_apt = $("input[name='rdo_apt']").val();

			if(rdo_apt == "1")
			{
				var rprice = $("input[name='rprice']").val() / 10000;
				var ramount = $("input[name='ramount']").val().replace(/,/gi,"");

				if(parseInt(rprice) < parseInt(ramount))
				{
					alert("대출신청금액은 대출가능금액을 초과할 수 없습니다.");
					return false;
				}
			}
	

			$.ajax({
				type : 'POST',
				url : 'atploan_process.php',  //processUrl
				data : str,
				dataType: 'json',
				success : function(data){
				
					console.log(data);
					
					if(data.retcode == "OK"){
						 check_request_end();
						 //var stralert = decodeURIComponent(data.retalert);
						 //alert(stralert.replace("+"," "));
						 window.location = data.retval;

					} else if(data.retcode == "X") {
						var stralert = decodeURIComponent(data.retalert);
							alert(stralert.replace("+"," "));

					}
					
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					alert("처리중 오류가 발생하였습니다. 다시 시도하여 주십시오.");
					console.log("XMLHttpRequest : "+XMLHttpRequest+", textStatus : "+textStatus);
					console.log(errorThrown);
					return false;
				}
			});	
	
		});
	});	
	

}


function fn_ramount(obj)
{
		fn_number_coma('ramount',obj, 0);

		var rdo_apt = $("input[name='rdo_apt']").val();

		if(rdo_apt == "1")
	  {
			var rprice = $("input[name='rprice']").val() / 10000;
			var ramount = $("input[name='ramount']").val().replace(/,/gi,"");

			if(parseInt(rprice) < parseInt(ramount))
			{
				alert("대출신청금액은 대출가능금액을 초과할수 없습니다.");
				var rmount_or = $("input[name='rmount_or']").val();
				if(rmount_or)
				{
						$("input[name='ramount']").val(rmount_or);
				}

			} else {
				$("input[name='rmount_or']").val(ramount);
			}
		}
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

function NumberFormatHan(fn, fntxt){
  var str = fn.value;
  var Re = /[^0-9]/g;
  var ReN = /(-?[0-9]+)([0-9]{3})/;
  str = str.replace(Re,'');
  while (ReN.test(str)) {
    str = str.replace(ReN, "$1,$2");
    }
  fn.value = str;

	var str = "obj="+fn.value;

	$.ajax({
		type : 'POST',
		url : 'loan_money_check.php',
		data : str,
		dataType: 'json',
		success : function(data){
			if(data.retcode == "OK"){
				var strhan = decodeURIComponent(data.retval);
				$("#"+fntxt).html(strhan);
			} else if(data.retcode == "X") {
				var stralert = decodeURIComponent(data.retalert);
					alert(stralert.replace("+"," "));

			}
		}
	});
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

function check_ad_form(fmname,event)
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

	var form = check_form(fmname);

	if(form == false)
	{
		return false;
	}

	var frm = $('#'+fmname);
	var str = frm.serialize();

	$.ajax({
		type : 'POST',
		url : processUrl,
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

function check_ad_del_form(fmname,event)
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

	var intcheck = 0;
	$("input[name='seq[]']:checked").each(function() {
		intcheck++;
	});

	if(intcheck == 0)
	{
		alert("삭제할 대상을 하나 이상 선택하셔야 합니다");
		return false;
	}

	if(confirm('삭제한 데이터는 복구되지 않습니다.\n정말 삭제 하시겠습니까?'))
  {
		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : processUrl,
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
}

function check_mkind(obj)
{
	var targetDiv = "";
	switch(obj)
	{
		case "1" : targetDiv = "#table_1"; break;
		case "2" : targetDiv = "#table_2"; break;
		case "3" : targetDiv = "#table_3"; break;
	}
	if(targetDiv)
	{
		if($(targetDiv).css("display") == "none")
		{
			$(targetDiv).css("display","block");
		} else {
			$(targetDiv).css("display","none");
		}
	}
}

function check_ad_checkbox(oratt, targetatt,indexof)
{
	var targetlength = $("input[name='"+oratt+"']").length;
	var strval = "";

	var j = 0;
	for(var i=0;i<targetlength;i++)
	{
		if($("input[name='"+oratt+"']")[i].checked == true)
		{
			if(j > 0)
			{
				strval += ",";
			}
			strval += $("input[name='"+oratt+"']")[i].value;
		}
		j++;
	}
	$("input[name='"+targetatt+"']")[indexof].value = strval;
}

function check_system_kind(kind, obj,indexof)
{
  var targetname = "";
	switch(kind)
	{
		case "1" : targetname = "jsystem[]"; break;
		case "2" : targetname = "isystem[]"; break;
		case "3" : targetname = "jsystem2[]"; break;
		case "4" : targetname = "isystem2[]"; break;
	}

	if(obj == "2")	// 포인트
	{
		$("select[name='"+targetname+"'").eq(indexof).css("display","block");
	} else {
		$("select[name='"+targetname+"'").eq(indexof).css("display","none");
	}
}

function open_window_center(strurl,strname,intwidth,intheight,strscroll)
{
	if(strscroll == "")
	{
		strscroll = "no";
	}
	var intLeft = Math.ceil((window.screen.width - parseInt(intwidth)) / 2 );
	var intTop = Math.ceil((window.screen.height - parseInt(intheight)) / 2 ) - 100;

	window.open(strurl,strname,'resizable=no,width='+intwidth+',height='+intheight+',scrollbars='+strscroll+',top='+intTop+',left='+intLeft);
}

function check_popstatus_update(kind, seq, seq2, target, event)
{
	if(event.stopPropagation)
	{
		event.preventDefault();
		event.stopPropagation();
	} else {
		event.cancelBubble = true;
	}

	var strtxt = "";
	var str = "";
	var etitle = $("input[name='etitle']").eq(target).val();
	var ntitle = $("input[name='ntitle']").eq(target).val();
	var recyn  = $("select[name='recyn']").eq(target).val();

	str = "&kind="+kind+"&idx="+seq+"&SE="+seq2+"&etitle="+etitle+"&ntitle="+ntitle+"&recyn="+recyn;

	if(kind == "update") { strtxt = "수정"; } else if(kind == "del") { strtxt = "삭제"; }

	if(confirm('정말 '+strtxt+' 하시겠습니까?'))
	{
		$.ajax({
			type : 'POST',
			url : copyUrl,
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
}

function check_form_send(kind, obj)
{
	if(kind == "gu")
	{
		strlink = "&si="+obj;
	} else if(kind == "dong") {
		si = $("#si").val();
		strlink = "&si="+si+"&gu="+obj;
	} else if(kind == "apt_name") {
		var objarr = obj.split(",");
		strlink = "&dcode="+objarr[0];

	} else if(kind == "apt_area") {
	  var objarr = obj.split(",");
		strlink = "&mg_id="+objarr[0];
	}

	check_form_proc("kind="+kind+strlink, event);
}

function check_form_proc(strval, event)
{
	if(event != undefined)
	{
		if(event.stopPropagation)
		{
			event.preventDefault();
			event.stopPropagation();
		} else {
			event.cancelBubble = true;
		}
	}

	$.ajax({
		type : 'POST',
		url : "apt.ajax.php",
		data : strval,
		dataType: 'json',
		success : function(data){

			if(data.retcode == "OK"){

				var optOr = document.getElementById(data.retkind);

				for(var i=optOr.options.length-1; i>0; i--)
				{
					optOr.options[i] = null;
				}

				if(data.retval != null)
				{
					Rradio_OnOff('Radio_On');
					$("input[name='rdo_apt']")[0].checked = true;

					for(var i=0;i<parseInt(data.retval.length);i++)
					{
						var opt = document.createElement('option');

						if(data.retkind == "dong" || data.retkind == "apt_name")
						{
								opt.text = data.retval[i][1];
								var dataval = new Array(data.retval[i][0], data.retval[i][1]);
								opt.value = dataval;

						} else {
							if(data.retkind == "apt_area")
							{
								opt.text = data.retval[i][1]+" ㎡";
								var dataval = new Array(data.retval[i][0], data.retval[i][1]);
								opt.value = dataval;
							} else {
								opt.text = data.retval[i];
								opt.value = data.retval[i];
							}

						}
						document.getElementById(data.retkind).add(opt);
					}

				} else {
					if(data.retkind == "apt_name")
					{
					  alert('아파트 정보를 찾지 못했습니다.\n아파트 명 및 상세 주소를 자세히 입력해주세요.');
				 	  Rradio_OnOff('Radio_Off');
						$("input[name='rdo_apt']")[1].checked = true;
					  return false;
					} else {
					  alert('정보를 찾지 못했습니다. 다시 선택 해주세요.');
					  return false;
					}
			  }

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

