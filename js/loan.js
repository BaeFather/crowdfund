// JavaScript Document
function ajax_call(gbn,prm1){
	$(document).ready(function() {

		var url = document.frm.url.value;
        url += '/loan/loan_proc.php';

		$.post(url, {'prm1':prm1}, function(result) {
			if(result == 'o'){
				alert('대출 신청이 등록되었습니다.\n확인 후 기재하신 연락처로 연락드리겠습니다.\n감사합니다.');
				location.href = document.frm.url.value;
			}
			else{
				alert('대출 신청에 실패 하였습니다\n\n다시 신청해 주세요.');
			}
		});

	});
}

function input_check(msg){
	if(msg.replace(/(^\s*)|(\s*$)/g,"") == ''){ return false; }else{ return true; }
}

function execDaumPostcode() {
	daum.postcode.load(function(){
		new daum.Postcode({
			oncomplete: function(data) {
				var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
				var extraRoadAddr = ''; // 도로명 조합형 주소 변수

				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraRoadAddr += data.bname;
				}

				if(data.buildingName !== '' && data.apartment === 'Y'){
					extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}

				if(extraRoadAddr !== ''){
					extraRoadAddr = ' (' + extraRoadAddr + ')';
				}

				if(fullRoadAddr !== ''){
					fullRoadAddr += extraRoadAddr;
				}

				document.getElementById('zip_num').value = data.zonecode;
				document.getElementById('address_road').value = fullRoadAddr;
				document.getElementById('address_dong').value = data.jibunAddress;

				// 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
				if(data.autoRoadAddress) {
					//예상되는 도로명 주소에 조합형 주소를 추가한다.
					var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
					document.getElementById('guide').innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
				}
				else if(data.autoJibunAddress) {
					var expJibunAddr = data.autoJibunAddress;
					document.getElementById('guide').innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
				}
				else {
					document.getElementById('guide').innerHTML = '';
				}

			}
		}).open();
	});
}



function focus_out(id){
	document.getElementById(id).focus();
	alert('[주소찾기] 버튼을 클릭해 주세요');
}



function money_check(){

	  var obj = document.getElementById('request_money');
	  var money = obj.value;
	      money = money.replace(/\,/gi, '');  // 콤마 제거

	  var rst  = true;
	  var str  = '0123456789';
	  var char = '';
	  var len  = money.length;
	  for(i=0;i<len;i++){

		  char = money.charAt(i);

		  if(str.indexOf(char) == -1){

               alert('희망대출금에는 숫자만 입력 가능합니다');
			   obj.value = '';
			   rst       = false;
			   break;

		  } // end if

	  } // end for

	  if(rst == true){ obj.value = comma(money); }

}


function comma(str) {
  var reg = /(^[+-]?\d+)(\d{3})/;
  str += '';
  while(reg.test(str))
    str = str.replace(reg, '$1' + ',' + '$2');
  return str;
}

function change_loan_type() {

	var obj = document.getElementById('loan_type');

	if(obj.value=='2' || obj.value=='2A' || obj.value=='2B' || obj.value=='2C') {
		document.getElementById('add1').style.display = '';
		document.getElementById('add2').style.display = '';
	//document.getElementById('ds').style.display = 'none';
	}
	else if(obj.value=='1') {
		document.getElementById('add1').style.display = 'none';
		document.getElementById('add2').style.display = 'none';
	//document.getElementById('ds').style.display = '';
	}
	else {
		document.getElementById('add1').style.display = 'none';
		document.getElementById('add2').style.display = 'none';
	//document.getElementById('ds').style.display = 'none';
	}

}

function go_submit(){

	var name            = document.getElementById('name').value;
	var email           = document.getElementById('email').value;
	var phone           = document.getElementById('phone').value;
	var loan_type       = document.getElementById('loan_type').value;
	var zip_num         = document.getElementById('zip_num').value;
	var address_road    = document.getElementById('address_road').value;
	var address_dong    = document.getElementById('address_dong').value;
	var address_detail  = document.getElementById('address_detail').value;
	var request_money   = document.getElementById('request_money').value;
	var request_period  = document.getElementById('request_period').value;
	var content         = document.getElementById('loan_content').value;
//var dongsan_info    = document.getElementById('dongsan_info').value;

    //----- 입력체크
	if(!input_check(name)) { alert('성명을 입력해 주세요');  return false; }
	if(!input_check(email)) { alert('이메일을 입력해 주세요');  return false; }
	if(!input_check(phone)) { alert('전화번호를 입력해 주세요');  return false; }
	if(loan_type == '2') {
		if(!input_check(zip_num)) {
			alert('담보주소를 입력해 주세요');  return false;
		}
	}
	else {
		/*
		if(!input_check(dongsan_info)) {
			alert('동산내용을 입력해 주세요');  return false;
		}
		*/
	}
	if(!input_check(content)) { alert('내용을 입력해 주세요');  return false; }
	if(!input_check(request_money)) { alert('희망대출금을 입력해 주세요');  return false; }


	if(confirm('입력하신 내용으로 대출 신청을 하시겠습니까?')) {

		var prm = name + '*:' +
		          email + '*:' +
		          phone + '*:' +
		          loan_type + '*:' +
		          zip_num + '*:' +
		          address_road + '*:' +
		          address_dong + '*:' +
		          address_detail + '*:' +
		          request_money + '*:' +
		          request_period + '*:' +
		          content;
		        //dongsan_info + '*:' +

		ajax_call(1, prm);

	}

}

function length_check(id,type){

	var str = document.getElementById(id).value;

	var len = get_byte(str);

	if(type == 1){ // 내용

		document.getElementById('byt1').innerHTML = len;

		if(len > 500){

				alert('내용은 500바이트를 초과해서 입력 하실수 없습니다');
				cut_str(id);
				length_check(id,type);

		}

	}else{ // 동산내용

		document.getElementById('byt2').innerHTML = len;

		if(len > 500){

				alert('동산내용은 500바이트를 초과해서 입력 하실수 없습니다');
				cut_str(id);
				length_check(id,type);

		}

	}

}

function cut_str(id){

	var str = document.getElementById(id).value;
	var len = str.length;
	var new_str = '';
	for(i=0;i<=len-4;i++){
		new_str += str.charAt(i);
	}

	document.getElementById(id).value = new_str;

}

function get_byte(str){

		var l = 0;

		for (var i=0; i<str.length; i++) l += (str.charCodeAt(i) > 128) ? 2 : ((str.charCodeAt(i) == 64) ? 10 : 1);

    return l;

}