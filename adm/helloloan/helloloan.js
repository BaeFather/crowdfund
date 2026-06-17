// 아이디 체크
var loanProcessUrl = "/adm/helloloan/process.php";
var voitPorcessUrl = "/adm/helloloan/request.proc.voit.php";

function get_sum() {

	// 선순위 채권최고액의 합을 구한다.
	var total = 0;
    $('input[name^="P_limit_amount"]').each( function() {
        total += parseInt(this.value);
    });
	$("#pre_high_amt").text(number_format_hyphen(total));

	// 선순위 기대출 금액의 합을 구한다.
	var gtotal = 0;
    $('input[name^="P_loan_amount"]').each( function() {
        gtotal += parseInt(this.value);
    });
	$("#pre_gi_amt").text(number_format_hyphen(gtotal));

	// 대환 채권최고액의 합을 구한다.
	var total = 0;
    $('input[name^="R_limit_amount"]').each( function() {
        total += parseInt(this.value);
    });
	$("#rep_high_amt").text(number_format_hyphen(total));

	// 대환 기대출 금액의 합을 구한다.
	var gtotal = 0;
    $('input[name^="R_loan_amount"]').each( function() {
        gtotal += parseInt(this.value);
    });
	$("#rep_gi_amt").text(number_format_hyphen(gtotal));

}

function go_hyphen_addr_srch(form_name) {
	var ww = 800;
	var wh = 300;

	var top  = ($(window).height()/2)-(wh/2);
	var left = ($(window).width()/2)-(ww/2);

	window.open('hyphen_addr_srch.php?f='+form_name,'kb_srch','top='+top+',left='+left+',width='+ww+',height='+wh+',toolbar=0,menubar=0,status=0,scrollbars=yes,resizable=yes');
}

function get_issue_manual() {

	var cert_num = $("#cert_num").val();

	$.ajax({
		type : 'post',
		dataType : 'json',
		//url : '/hyphen/hyphen_issue_test.php',
		url : '/hyphen/hyphen_issue.php',
		data : {'uniqNo':cert_num},
		success : function(data) {

			console.log(data);

			// out 이 오면 에러
			//if ($.isArray(data["out"])) {
			if (data["out"]) {
				alert(data["out"]["errMsg"]);
				return false;
			} 

			if (data["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"]) {

				arr1 = data["outList"]["소유지분을_제외한_소유권에_관한_사항_갑구"];

				$("#rep_loan").empty();
		
				for (var i=0 ; i<arr1.length ; i++) {

					var trow = "";
					var gap = "갑구";
					var pre_rep = "";
					var bank = "";
					var band_ori = "";
					var amt = 0;
					var amt_ori = 0;
					var gi_amt = Math.floor(amt/1.2);
					var tg = arr1[i]["대상소유자"];
					var loan_obj = arr1[i]["등기목적"]; // 등기목적
					var add_row = "";

					var tmp2 = arr1[i]["주요등기사항"].split("(br)");
					for (m=0 ; m<tmp2.length ; m++) {
						var tmp2_1 = tmp2[m].split(/\s+/);
						if (tmp2_1[0].match("청구금액")) {
							amt = tmp2_1[1].replace(/[^0-9]/gi, '');
						} else {
							if (tmp2_1[1]) bank=$.trim($.trim(tmp2[m]).substring(4));// bank = tmp2_1[1];
						}
					}


					tg = arr1[i]["대상소유자"];
					loan_obj = arr1[i]["등기목적"]; // 등기목적

					if (loan_obj=="질권" || loan_obj=="근질권" || loan_obj=="전세권설정" || loan_obj=="전세권이전") {
						continue;
					}

					if (arr1[i]["주요등기사항밑줄"]=="Y") {
						add_row = "N";
						if (bank) bank_ori = bank;
						if (amt)  amt_ori  = amt;
					} else {
						add_row = "Y";
						if (bank_ori && !bank) bank = bank_ori;
						if (amt_ori  && !amt)  amt  = amt_ori;
					}					


					if (amt) gi_amt = Math.floor(amt/1.2);  // 기본 설정율은 120%

					pre_rep = "대환";

					updn_arrow = "<a onclick='go_top($(this).parent().parent().index());' style='cursor:pointer;'>▲</a>";
					tag_name = "R";

					if (add_row=="Y") {

						trow = "<tr>";
						trow += "<td><a onclick='go_top($(this).parent().parent().index());' style='cursor:pointer;'>▲</a></td>";
						trow += "<td style='text-align:center;'>"+gap+"<input type='hidden' name='R_reg_gubun[]' value='"+gap+"'></td>"; // 구분 갑구 을구
						trow += "<td><input type=text name='R_creditor[]' class='form-control input-sm' style='display:inline; text-align:left; width:100%;' value='"+bank+"' ></td>"; // 금융업체
						trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='R_limit_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='"+amt+"' > 원</td>"; // 채권최고액
						trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='R_loan_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='"+gi_amt+"' > 원</td>"; // 기대출금액
						trow += "<td style='text-align:center;'><select name='R_loan_percent[]' class='form-control input-sm' style='display:inline;width:50px; padding:5px 3px;'><option value='110'>110<option><option value='120' selected>120<option><option value='130'>130<option><option value='140'>140<option><option value='150'>150<option></select> %</td>"; // 설정율
						trow += "<td style='text-align:center;'><input type=text name='R_debtor[]' class='form-control input-sm' style='display:inline; text-align:center; width:90px;' value='"+tg+"' ></td>"; // 채무자
						trow += "<td style='text-align:center;'><input type=text name='R_reg_obj[]' class='form-control input-sm' style='display:inline; text-align:center; width:110px;' value='"+loan_obj+"' ></td>"; // 등기목적
						trow += "<td style='text-align:center;'><a onclick='go_del(\"rep_loan\", $(this).parent().parent().index());' style='cursor:pointer;'>-</a></td>";
						trow += "</tr>";

						//$("#tg_addr").append("<option value='"+data["out"]["outC0000"]["list"][i]["부동산고유번호"]+"'>"+data["out"]["outC0000"]["list"][i]["부동산소재지번"]+"</option>");
						//$("#pre_loan").append('<tr><td>11</td><td>22</td><td>33</td><td>44</td><td>55</td><td>66</td><td>77</td><td>88</td></tr>');
						$("#rep_loan").append(trow);

					}

				}

			}

			// 소유지분을_제외한_소유권에_관한_사항_갑구 는 무조건 대환정보로 입력			
			if (data["outList"]["근_저당권_및_전세권_등_을구"]) {

				arr2 = data["outList"]["근_저당권_및_전세권_등_을구"];

				$("#pre_loan").empty();

				var amt_ori = 0;
				var bank_ori ="";

				for (var i=0 ; i<arr2.length ; i++) {

					var amt = 0;   
					var bank = "";
					var gap = "을구";
					var pre_rep = "";
					var updn_arrow = ""; // 위로 아래로 화살표
					var gi_amt = 0;  // 기대출금액
					var tg = ""; // 대상채무자
					var loan_obj = ""; // 등기목적

					var chae_money = "";
					var chae_man = "";
					var tmp2 = arr2[i]["주요등기사항"].split("(br)");

					for (m=0 ; m<tmp2.length ; m++) {
						var tmp2_1 = tmp2[m].split(/\s+/);
						if (tmp2_1[0].match("채권최고액")) {
							amt = tmp2_1[1].replace(/[^0-9]/gi, '');
						} else {
							if (tmp2_1[1]) bank = tmp2_1[1];
						}
					}


					tg = arr2[i]["대상소유자"];
					loan_obj = arr2[i]["등기목적"]; // 등기목적

					if (loan_obj=="질권" || loan_obj=="근질권" || loan_obj=="전세권설정") {
						continue;
					}

					if (arr2[i]["주요등기사항밑줄"]=="Y") {
						add_row = "N";
						if (bank) bank_ori = bank;
						if (amt)  amt_ori  = amt;
					} else {
						add_row = "Y";
						if (bank_ori && !bank) bank = bank_ori;
						if (amt_ori  && !amt)  amt  = amt_ori;
					}


					if (amt) gi_amt = Math.floor(amt/1.2);  // 기본 설정율은 120%

					if (bank.match("은행") || bank.match("보험") || bank.match("금고") || bank.match("신협") || bank.match("수협") || bank.match("농협") || bank.match("뱅크") || bank.match("캐피탈")) {						
						pre_rep = "선순위";
					} else {
						pre_rep = "대환";
					}


					var minus = "";
					if (pre_rep=="선순위") {

						updn_arrow = "<a onclick='go_bott($(this).parent().parent().index());' style='cursor:pointer;'>▼</a>";
						minus = "<td style='text-align:center;'><a onclick='go_del(\"pre_loan\", $(this).parent().parent().index());' style='cursor:pointer;'>-</a></td>";
						tag_name = "P";

					} else if (pre_rep=="대환") {

						updn_arrow = "<a onclick='go_top($(this).parent().parent().index());' style='cursor:pointer;'>▲</a>";
						minus = "<td style='text-align:center;'><a onclick='go_del(\"rep_loan\", $(this).parent().parent().index());' style='cursor:pointer;'>-</a></td>";
						tag_name = "R";

					}


					if (add_row=="Y") {

						trow = "<tr>";
						trow += "<td>"+updn_arrow+"</td>";
						trow += "<td style='text-align:center;'>"+gap+"<input type='hidden' name='R_reg_gubun[]' value='"+gap+"'></td>"; // 구분 갑구 을구
						trow += "<td><input type=text name='"+tag_name+"_creditor[]' class='form-control input-sm' style='display:inline; text-align:left; width:100%;' value='"+bank+"' ></td>"; // 금융업체
						trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='"+tag_name+"_limit_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='"+amt+"' > 원</td>"; // 채권최고액
						trow += "<td style='text-align:right; padding-right:10px;'><input type=text name='"+tag_name+"_loan_amount[]' class='form-control input-sm' style='display:inline; text-align:right; width:100px;' value='"+gi_amt+"' > 원</td>"; // 기대출금액
						trow += "<td style='text-align:center;'><select name='"+tag_name+"_loan_percent[]' class='form-control input-sm' style='display:inline;width:50px; padding:5px 3px;'><option value='110'>110<option><option value='120' selected>120<option><option value='130'>130<option><option value='140'>140<option><option value='150'>150<option></select> %</td>"; // 설정율
						trow += "<td style='text-align:center;'><input type=text name='"+tag_name+"_debtor[]' class='form-control input-sm' style='display:inline; text-align:center; width:90px;' value='"+tg+"' ></td>"; // 채무자
						trow += "<td style='text-align:center;'><input type=text name='"+tag_name+"_reg_obj[]' class='form-control input-sm' style='display:inline; text-align:center; width:110px;' value='"+loan_obj+"' ></td>"; // 등기목적
						trow += minus;
						trow += "</tr>";

						if (pre_rep=="선순위") $("#pre_loan").append(trow);
						else $("#rep_loan").append(trow);

						bank_ori = "";
						amt_ori  = 0;

					}

				}

			}

			if (data["pdfHexString"]) $("#pdf").val(data["pdfHexString"]);

			set_house_deposit(); // 소액임차보증금 세팅

			$("#ddmoney").focus();

			alert("등본 업로드 완료");

		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("등기부등본발급중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});


}

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

	var yn = confirm("수정하시겠습니까?");
	if (!yn) return;

	  var checkform = check_form(fmname);

		if(checkform == false)
		{
			  return false;
		}

		var frm = $('#'+fmname);
		var str = frm.serialize();

		//var frm2 = $('#'+fmname)[0];
		//var str2 = new FormData(frm2);
		//str2.append('i_file_0', $('#i_file_0')[0].files[0]);



		$.ajax({
			type : 'POST',
			url : loanProcessUrl,
			data : str,
//processData : false,
//contentType : false,
			dataType: 'json',
			success : function(data){
//console.log(data);
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
		//t1content = "<input type='text' name='examount[]' value='' class='input02' OnKeyUp=\"fn_number_coma('examount[]',this.value, "+intlength+");\" /> ";
		//t2content = "<input type='text' name='maxbond[]' value='' class='input02' OnKeyUp=\"fn_number_coma('maxbond[]',this.value, "+intlength+");\" /> ";
		t1content = "<input type='text' name='examount[]' value='' class='form-control input-sm' OnKeyUp=\"fn_number_coma('examount[]',this.value, "+intlength+");\" style='display:block; width:120px; margin:3px 0; text-align:right;' /> ";
		t2content = "<input type='text' name='maxbond[]'  value='' class='form-control input-sm' OnKeyUp=\"fn_number_coma('maxbond[]',this.value, "+intlength+");\" style='display:block; width:120px; margin:3px 0; text-align:right;' /> ";

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

// 자바스크립트로 PHP의 number_format 흉내를 냄
// 숫자에 , 를 출력
function number_format_hyphen(val) {
	if(val) {

		if(isNaN(val)) return val;

		val = toNumber(val).toString();

		var len = val.length;
		var str = "";

		if(len > 3) {
			var in_c = len % 3;
			if(!in_c) in_c = 3;

			for(n = 0; n < len; n++) {
				if(n == in_c) {
					str += ",";
					in_c = 3 + in_c;
				}
				str += val.charAt(n);
			}
			return str;

		}
		else {
			return val;
		}

	} else {
		if (val==0) return 0;
	}
}