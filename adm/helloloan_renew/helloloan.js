// 아이디 체크
var loanProcessUrl = "/adm/helloloan_renew/process.php";
var voitPorcessUrl = "/adm/helloloan_renew/request.proc.voit.php";
var hwriteselectUrl = "/adm/helloloan_renew/hwrite_select.php";
var hwriteselectReUrl = "/adm/helloloan_renew/hwritere_select.php";
var limitselectUrl = "/adm/helloloan_renew/limit_select.php";
var smslistsendUrl = "/adm/helloloan_renew/smslist.proc.php";
var stratpajax = "/adm/helloloan_renew/apt_ajax.php";

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
			//$("input[name='"+target+"']").val("");
			return false;
		}
		var retval = numberWithCommas(obj);
		$("input[name='"+target+"']").eq(idx).val(retval);
}

function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function fn_calc_ltv()
{
	var tmoney = 0;
		var ddmoney = $("input[name='ddmoney']").val().replace(/,/gi,""); // 희망대출금액
		var examount = 0;

		if(!ddmoney) { ddmoney =0; }

		var examountTarget = $("input[name='examount[]']");
		var kbprice = $("input[name='kbprice']").val(); // 일반가
		var kbllimit = $("input[name='kbllimit']").val(); // 하한가

		if(!kbprice) { kbprice =0; }
		if(!kbllimit) { kbllimit =0; }

		for(var i=0;i<examountTarget.length;i++)
		{
			  if(examountTarget.eq(i).val().replace(/,/gi,""))
				{
					examount = examount + parseInt(examountTarget.eq(i).val().replace(/,/gi,""));
				}
		}
		tmoney = parseInt(ddmoney) + parseInt(examount);

		var ltvkind = $("input[name='ltvkind']:checked").val();
		if(!ltvkind)
		{
			alert("일반가나 하한가를 선택하셔야 합니다");
			return false;
		}
		if(ltvkind == "1")
		{
			tmoney = parseInt(tmoney) / parseInt(kbprice.replace(/,/gi,""));
		} else if(ltvkind == "2") {
			tmoney = parseInt(tmoney) / parseInt(kbllimit.replace(/,/gi,""));
		}
		if(tmoney) { tmoney = tmoney * 100; }

		if(tmoney > 83)
		{
			$("input[name='ltvmoney']").css("color","#ff0000");
		} else {
			$("input[name='ltvmoney']").css("color","#000000");
		}

		var intddmoney = 0;
		var intfees = $("input[name='fees']").val();

		if(!intfees)
		{
			alert('플랫폼 수수료율을 입력하여 주십시오');
			return false;
		}
		var intfeespercent = 0;

		if(intfees)
		{
			intfeespercent = intfees / 100;
		}

		if(ddmoney)
		{
			//intddmoney = parseInt(ddmoney.replace(/,/gi,""))*0.0055;
			//intddmoney = Math.ceil(ddmoney*0.0055);
			intddmoney = Math.floor(ddmoney*intfeespercent);
		}

		var loankind = $("input[name='loankind']:checked").val(); // 선순위1, 후순위2

		/* 헬로펀딩 기준금리 계산*/
		var strLimit = 0;

		if(loankind == "1")	// 선순위
		{
			if(tmoney < 70)
			{
				strLimit = 7.4;
			} else if(tmoney >= 70 && tmoney < 80) {
				strLimit = 8.4;
			} else if(tmoney >=80 && tmoney < 84) {
				strLimit = 8.9;
			}
		} else if(loankind == "2") {	// 후순위
			if(tmoney < 70)
			{
				strLimit = 8.4;
			} else if(tmoney >= 70 && tmoney < 80) {
				strLimit = 9.4;
			} else if(tmoney >=80 && tmoney < 84) {
				strLimit = 9.9;
			}
		}

		if(tmoney > 83)
		{
			strLimit = 0;
			intddmoney = 0;
		}

		/*
		if(tmoney < 75)
		{
			strLimit = 9;
		} else if(tmoney >= 75 && tmoney < 80) {
			strLimit = 10;
		} else if(tmoney >=80) {
			strLimit = 11;
		}

		var hholds = parseInt($("input[name='hholds']").val().replace(/,/gi,""));	//세대수
		var kbprice = parseInt($("input[name='kbprice']").val().replace(/,/gi,"")); //일반가
		var laddr = $("input[name='laddr']").val().split(" "); //주소
		var skindcheck = $(":radio[name='skind']").is(":checked");
		var kbarea = $("input[name='kbarea']").val(); //면적

		if(!hholds) { hholds = 0; }
		if(!kbprice) { kbprice = 0;}
		if(!laddr) { laddr[0] = ""; }
		if(!kbarea) { kbarea = 0; }
		if(skindcheck == false)
		{
			alert("담보구분을 선택하셔야 합니다");
			return false;
		}
		var skind = $(":radio[name='skind']:checked").val(); //담보구분

		if(hholds <= 100) // 세대수  (100세대 이하는 기존적용금리 0.5%
		{
			strLimit += 0.5;
		}
		if(kbprice < 200000000)  // kb시세 기준 2억원 미만 적용금리 0.5%  (일반가기준)
		{
			strLimit += 0.5;
		}

		if(!(laddr[0].substr(0,2) == "서울" || laddr[0].substr(0,2) =="경기" || laddr[0].substr(0,2) == "인천"))  //서울.경기, 인천 외지역 적용금리  0.5
		{
			strLimit += 0.5;
		}
		if(skind == "2" || skind == "3") // 오피스텔.빌라 기존 적용금리 1%  아파트는 제외
		{
			strLimit += 1;
		}
		if(kbarea > 120) // 면적 120m 초과시   0.5%
		{
			strLimit += 0.5;
		}
	  */
		//alert(tmoney + " : "+hholds+" : "+kbprice+" : "+laddr[0]+" : "+skind+" : "+kbarea+ " : "+skindcheck+" === " + strLimit);

		/*헬로펀딩 기준금리 계산 종료 */

		$("input[name='hellobase']").val(strLimit);
		$("input[name='hellofee']").val(numberWithCommas(intddmoney));
		$("input[name='ltvmoney']").val(tmoney.toFixed(2));
}

function daum_execDaumPostcode()
{
	new daum.Postcode({
   onsearch: function(data) {

	 },
		oncomplete: function(data)
		{
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

			// 각 주소의 노출 규칙에 따라 주소를 조합한다.
			// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
			var addr = ''; // 주소 변수
			var extraAddr = ''; // 참고항목 변수

			//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다. 지번 디폴트
		  addr  = data.autoJibunAddress;
			if(addr == "" || data == undefined)
			{
				addr = data.jibunAddress;
			}

			// 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
			if(data.userSelectedType === 'R'){
				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
				// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraAddr += data.bname;
				}
				// 건물명이 있고, 공동주택일 경우 추가한다.
				if(data.buildingName !== '' && data.apartment === 'Y'){
					extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraAddr !== ''){
					extraAddr = ' (' + extraAddr + ')';
				}

			} else {

			}

			var addrArr = addr.split(" ");
			var targetaddr = new Array("서울");

		  var strkind = false;

			for(var i=0;i<targetaddr.length;i++)
			{
			  if(addrArr[0] == targetaddr[i])
				{
					strkind = true;
					break;
				}
   		}

			if(strkind == true)
			{
				$("#addr_warning").html("");
			} else {
				$("#addr_warning").html("*가능지역이 아닙니다.(서울 지역만 가능)");
		  }
			$("#addr_si").val(addrArr[0]);
			$("#bcode").val(data.bcode);

			// 우편번호와 주소 정보를 해당 필드에 넣는다.
			document.getElementById("laddr").value = addr;
			// 커서를 상세주소 필드로 이동한다.
			document.getElementById("laddr").focus();

		  if(data.bcode)
			{
				var str= "kind=1&d_code="+data.bcode;

				$.ajax({
					type : 'POST',
					url : limitselectUrl,
					data : str,
					dataType: 'json',
					success : function(data)
					{
						if(data.retcode == "OK")
						{
							var strval = data.retval;

							if(strval != null || strval)
							{
								var optOr = document.getElementById("aptname");

								for(var i=optOr.options.length-1; i>0; i--)
								{
									optOr.options[i] = null;
								}

								for(var i=0;i<strval.length;i++)
								{
									var opt = document.createElement('option');
									opt.text = strval[i][1];
									opt.value = strval[i][0];
									document.getElementById("aptname").add(opt);
								}
							}

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
			}
		}
	}).open();
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

		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : loanProcessUrl,
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

function check_w_file_form(fmname, event)
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
	  var fileinputname = "";
		for(var j=0;j<10;j++)
		{
			fileinputname = 's_file'+j;

			formData.append(fileinputname, $("input[name='"+fileinputname+"']")[0].files[0]);
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
				formData.append(strdarr[0],decodeURIComponent(strdarr[1]));
		}

		$.ajax({
			type : 'POST',
			url : loanProcessUrl,
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

function check_del_form(fmname, event)
{
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

		if(confirm('삭제한 데이터는 복구되지 않습니다.\n정말 삭제하시겠습니까?'))
		{
			var frm = $('#'+fmname);
			var str = frm.serialize();

			$.ajax({
				type : 'POST',
				url : loanProcessUrl,
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

function fn_additem_examount(kind)
{
	var t1content = "";
	var t2content = "";
	var t1layer = $("#examountarea");
	var t2layer = $("#maxbondarea");

	var intlength = $("input[name='examount[]']").length;

	if(kind == "plus")
	{
		t1content = "<input type='text' name='examount[]' value='' class='input02' OnKeyUp=\"fn_number_coma('examount[]',this.value, "+intlength+");\" /> ";
		t2content = "<input type='text' name='maxbond[]' value='' class='input02' OnKeyUp=\"fn_number_coma('maxbond[]',this.value, "+intlength+");\" /> ";

		t1layer.append(t1content);
		t2layer.append(t2content);
		}
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

function check_admin_member_vote(midx,mlevel,obj,tindex,seq,event)
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

		var str = "&midx="+midx+"&mlevel="+mlevel+"&obj="+obj+"&SE="+seq+"&tindex="+tindex;

		$.ajax({
			type : 'POST',
			url : voitPorcessUrl,
			data : str,
			dataType: 'json',
			success : function(data){

				if(data.retcode == "OK"){
					$("#recyn").html(data.retyn);

					if(mlevel == "2" && obj)
					{
						for(var i=0;i<$("select[name='votyn[]']").length;i++)
						{
							if(i != tindex)
							{
								$("select[name='votyn[]']").eq(i).attr("disabled",true);
							}
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

function check_form_check()
{
		if($("#SE").val())
		{
				check_send_form('regfm');
		}
}

function check_send_form_re(fmname)
{
		var form = check_form(fmname);

		if(form == false)
		{
			return false;
		}

		var frm = $('#'+fmname);
		var str = frm.serialize();

		var dataother = "";

			$.ajax({
			type : 'POST',
			url : hwriteselectReUrl,
			data : str,
			dataType: 'json',
			success : function(data){

				if(data.retcode == "OK"){
					dataother = data.retadd;

					$("#Interest").val(dataother[0]); //금리
					$("#ltv").val(dataother[1]); //ltv
					$("#feesmoney").val(dataother[2]); //플랫폼수수료
					$("#mm").val(dataother[3]); //kb시세
					$("#cr_date").val(dataother[4]); //준공년월
					$("#tot_house").val(dataother[5]); //세대수

					$("#Interest_area").html(dataother[0]); //금리
					$("#ltv_area").html(dataother[1]); //ltv
					$("#feesmoney_area").html(number_format(dataother[2])); //플랫폼수수료


				  var ddmoney = $("input[name='ddmoney']").val();
					$("input[name='okmoney']").val(ddmoney);

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

function check_send_form(fmname)
{
		var form = check_form(fmname);

		if(form == false)
		{
			return false;
		}

		var frm = $('#'+fmname);
		var str = frm.serialize();

		var dataother = "";

			$.ajax({
			type : 'POST',
			url : hwriteselectUrl,
			data : str,
			dataType: 'json',
			success : function(data){

				if(data.retcode == "OK"){
					dataother = data.retadd;

					$("#Interest").val(dataother[0]); //금리
					$("#ltv").val(dataother[1]); //ltv
					$("#feesmoney").val(dataother[2]); //플랫폼수수료


					$("#write_detail_area").html(data.retval);


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

function check_aptname()
{
		var bcode = $("#bcode").val();
		if(bcode)
		{
			var str= "kind=1&d_code="+bcode;

			$.ajax({
				type : 'POST',
				url : limitselectUrl,
				data : str,
				dataType: 'json',
				success : function(data)
				{
					if(data.retcode == "OK")
					{
						var strval = data.retval;

						if(strval != null || strval)
						{
							var optOr = document.getElementById("aptname");

							for(var i=optOr.options.length-1; i>0; i--)
							{
								optOr.options[i] = null;
							}

							for(var i=0;i<strval.length;i++)
							{
								var opt = document.createElement('option');
								opt.text = strval[i][1];
								opt.value = strval[i][0];
								document.getElementById("aptname").add(opt);
							}

							if(aptcode)
							{
								$("#aptname").val(aptcode);
							}
						}

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
		}
}

function fn_apt_pyoung_view(obj)
{
	if(obj)
	{
		var str= "kind=2&mg_id="+obj;
		$.ajax({
			type : 'POST',
			url : limitselectUrl,
			data : str,
			dataType: 'json',
			success : function(data)
			{
				if(data.retcode == "OK")
				{
					var strval = data.retval;

					if(strval != null || strval)
					{
						var optOr = document.getElementById("aptarea");

						for(var i=optOr.options.length-1; i>0; i--)
						{
							optOr.options[i] = null;
						}

						for(var i=0;i<strval.length;i++)
						{
							var opt = document.createElement('option');
							opt.text = strval[i][0]+"㎡ ("+strval[i][1]+"평)";
							opt.value = strval[i][2];
							document.getElementById("aptarea").add(opt);
						}

						if(areacode)
						{
							$("#aptarea").val(areacode);
						}
					}

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
	}
}

function fn_apt_mm(obj)
{
	if(obj.value)
	{
		var aptname= $("#aptname").val();
		var str= "kind=3&mg_id="+aptname+"&ju_seri="+obj.value;

		$.ajax({
			type : 'POST',
			url : limitselectUrl,
			data : str,
			dataType: 'json',
			success : function(data)
			{
				if(data.retcode == "OK")
				{
					var strval = data.retval;

					if(strval != null || strval)
					{
						$("#mm").val(strval[0][0]);
						$("#aptareatext").val(obj.options[obj.selectedIndex].text);
					}

					if($("#SE").val())
					{
						  check_send_form('regfm');
					}

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
	}
}

function check_file_form(fmname, kind,  event)
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

		var strtxt = "";
		if(kind == "2")
		{
			strtxt = "등록된 파일로 문자를 발송하시겠습니까?\n문자는 등록하신 일자에 예약등록 됩니다.";
		} else {
			strtxt = "등록된 파일로 문자를 발송하시겠습니까?\n문자는 10분후 일괄 발송 됩니다.";
		}

		if(confirm(strtxt))
		{
			var checkform = check_form(fmname);

			if(checkform == false)
			{
					return false;
			}

			var formData = new FormData();
			var fileinputname = 's_file0';
			formData.append(fileinputname, $("input[name='"+fileinputname+"']")[0].files[0]);

			var frm = $('#'+fmname);
			var str = frm.serialize();

			var strarr = str.split("&");
			var strdarr = new Array();
			for(var i=0;i<strarr.length;i++)
			{
					strdarr = strarr[i].split("=");
					if(parseInt(strdarr[0].indexOf("%5B%5D")) > 1)
					{
						strdarr[0] = strdarr[0].replace("%5B%5D","")+"[]";
					}
					formData.append(strdarr[0],decodeURIComponent(strdarr[1]));
			}

			$.ajax({
				type : 'POST',
				url : smslistsendUrl,
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
}

function check_form_del(fmname, obj)
{
	if(confirm('삭제한 예약문자는 복구되지 않습니다. 정말 삭제하시겠습니까?'))
	{
		$("#SE").val(obj);
		var frm = $('#'+fmname);
		var str = frm.serialize();

		$.ajax({
			type : 'POST',
			url : smslistsendUrl,
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
		if(!si)
	  {
			si = $("#si").val();
		}
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

function check_form_gu_proc(strval)
{
	var opt = "";

	$.ajax({
		type : 'POST',
		url : stratpajax,
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
					for(var i=0;i<parseInt(data.retval.length);i++)
					{
						opt = document.createElement('option');
						opt.text = data.retval[i][1];
						var dataval = new Array(data.retval[i][0], data.retval[i][1]);
						opt.value = dataval;

						document.getElementById(data.retkind).add(opt);
					}
					$("#dong").val(dong).prop("selected",true);
				}

			}
		}
	});
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
		url : stratpajax,
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

					if(data.retkind == "si")
					{
						$("#si").val(si).prop("selected",true);
					}
					if(data.retkind == "gu")
					{
						$("#gu").val(gu).prop("selected",true);
					}
					if(data.retkind == "dong")
					{
						$("#dong").val(dong).prop("selected",true);
					}
					if(data.retkind == "apt_name")
					{
						$("#apt_name").val(apt_name).prop("selected",true);
					}
					if(data.retkind == "apt_area")
					{
						$("#apt_area").val(apt_area).prop("selected",true);
						$("#aptcrdate").val(data.retval[0][2]);
						$("#atptot").val(data.retval[0][3]);
					}

					si = $("#si").val();
					if(si == "세종특별자치시")
					{
							$("#gu").css("display","none");
					}

				} else {
					si = $("#si").val();
					if(si == "세종특별자치시")
					{
							$("#gu").css("display","none");
					}

					if(data.retkind == "apt_name")
					{
					  alert('해당 지역은 대출가능한 아파트가 없습니다.');
					  return false;
					} else {
					  if(data.retkind == "gu")
						{
							if(si == "세종특별자치시")
							{
								strlink = "&si="+si+"&gu=";
								check_form_gu_proc("kind=dong"+strlink);

							} else {
								alert('정확한 시/구를 선택해주세요.');
								return false;
							}

						} else {
							alert('정확한 시/구를 선택해주세요.');
						  return false;
						}
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
