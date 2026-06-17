var join_url = "";
var member_type = 1;
var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
$(document).ready(function() {
	$("#USERNM1, #JUMINNO1, #USERNM2, #JUMINNO2, #strAccountNo").keyup(function(evt){
		$("input[name='private_yn']").val("N");
	});
	$("#strBankCode").change(function(evt){
		$("input[name='private_yn']").val("N");
	});
});


function ajax_call(gbn,prm1){
	$(document).ready(function() {
    if(gbn == 1)      var url = join_url+'/mypage/get_user_info.php';
		else if(gbn == 2) var url = join_url+'/mypage/insert_user_info.php';

		$.post(url,{'prm1':prm1},
			function(result) {
				if(gbn == 1) {
					if(result != 'x'){
						var ary_rst = result.split('*:'); // 0:아이디,  1:멤버타입,  2:mb_no
						member_type = 1*ary_rst[1];

						if(member_type == 1){ // 개인회원
							document.getElementById('m_type1').style.display = '';
						}else{ // 기업회원
							document.getElementById('m_type2').style.display = '';
						}

						document.getElementById('mb_id').innerHTML = ary_rst[0]; // 회원 아이디
						document.frm.mb_no.value = ary_rst[2];
					}
					else {
						location.href = join_url;
					}
				}
				else{
					alert(result);
					if(result == 'o'){
						alert('저장되었습니다');
						location.href = join_url;
					}
					else{
						alert('저장에 실패하였습니다\n\n다시 저장해 주세요');
					}
				}
			}
		);
	});
}

function get_info(){
	join_url = document.frm.url.value;
	ajax_call(1,'');
}

function input_check(msg){
	if(msg.replace(/(^\s*)|(\s*$)/g,"") == ''){ return false; }else{ return true; }
}


//본 예제에서는 도로명 주소 표기 방식에 대한 법령에 따라, 내려오는 데이터를 조합하여 올바른 주소를 구성하는 방법을 설명합니다.
function search_address(zip_num,address_road,address_dong) {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

			// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
			// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
			var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
			var extraRoadAddr = ''; // 도로명 조합형 주소 변수

			// 법정동명이 있을 경우 추가한다. (법정리는 제외)
			// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
			if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
				extraRoadAddr += data.bname;
			}
			// 건물명이 있고, 공동주택일 경우 추가한다.
			if(data.buildingName !== '' && data.apartment === 'Y'){
			   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
			}
			// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
			if(extraRoadAddr !== ''){
				extraRoadAddr = ' (' + extraRoadAddr + ')';
			}
			// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
			if(fullRoadAddr !== ''){
				fullRoadAddr += extraRoadAddr;
			}

			// 우편번호와 주소 정보를 해당 필드에 넣는다.
			document.getElementById(zip_num).value = data.zonecode; //5자리 새우편번호 사용
			document.getElementById(address_road).value = fullRoadAddr;
			document.getElementById(address_dong).value = data.jibunAddress;

			// 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
			if(data.autoRoadAddress) {
				//예상되는 도로명 주소에 조합형 주소를 추가한다.
				var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
				document.getElementById('guide').innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';

			} else if(data.autoJibunAddress) {

				var expJibunAddr = data.autoJibunAddress;
				document.getElementById(address_dong).value = expJibunAddr;
				document.getElementById('guide').innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';

			} else {
				document.getElementById('guide').innerHTML = '';
			}
		}
	}).open();
}


function focus_out(id){
	document.getElementById(id).focus();
	alert('[주소찾기] 버튼을 클릭해 주세요');
}


var str_account = ''; // 인증에 성공한 계좌 정보 저장 문자열
var bank_proc   = 0;

function check_bank_new(){
	var frm = document.frm;
	member_type_val = frm.member_type.value;
	if(member_type_val==1){
		var usernm = frm.USERNM1.value;
		var jumin  = frm.JUMINNO1.value;
		var msg1 = '주민등록번호를 입력해 주세요';
		var msg2 = '주민등록번호는 숫자만 입력 할 수 있습니다';
		var msg3 = '성명을 입력해 주세요';
		var msg4 = '주민등록번호 자리수(13자리)가 맞지 않습니다.';
	}
	else{
		var usernm = frm.USERNM2.value;
		var jumin  = frm.JUMINNO2.value;
		var msg1 = '법인등록번호를 입력해 주세요';
		var msg2 = '법인등록번호는 숫자만 입력 할 수 있습니다';
		var msg3 = '상호명을 입력해 주세요';
		var msg4 = '법인등록번호 자리수(13자리)가 맞지 않습니다.';
	}

	if(!input_check(usernm)){ alert(msg3); return false; }
	if(!input_check(jumin)){ alert(msg1); return false; }
	if(jumin.length != 13){ alert(msg4); return false; }

	if (jumin.substr(2,2) < '01' || jumin.substr(2,2) > '12' || jumin.substr(4,2) < '01' || jumin.substr(4,2) > '31' || jumin.charAt(6) > 8) {
		 alert('올바른 주민등록번호가 아닙니다!(1)');
		 return false;
	}

	//-- 주민번호체크 시작 ----------------
	var sum  = 0;
	var sum2 = 0;
	var at   = 0;
	sum = (jumin.charAt(0)*2) + (jumin.charAt(1)*3) + (jumin.charAt(2)*4) +	(jumin.charAt(3)*5) + (jumin.charAt(4)*6) + (jumin.charAt(5)*7) +
				(jumin.charAt(6)*8) + (jumin.charAt(7)*9) + (jumin.charAt(8)*2) + (jumin.charAt(9)*3) + (jumin.charAt(10)*4) + (jumin.charAt(11)*5);

	sum2 = sum % 11;

	if(jumin.charAt(6) > 4 && jumin.charAt(6) < 9) {

		// 외국인
		att  = (11 - sum2) % 10;
		att2 = ((11 - sum2) % 10 + 2) % 10;

		if( (jumin.charAt(12) != att) && (jumin.charAt(12) != att2) ) {
			alert('올바른 주민등록번호가 아닙니다!');
			return false;
		}

	}
	else {

		// 내국인
		if(sum2 > 0) {
			at = (sum2==1) ? 11 : sum2;
		}
		else {
			at = 10;
		}
		att = 11 - at;

		if( jumin.charAt(12) != att ) {
			alert('올바른 주민등록번호가 아닙니다!(2-1)');
			return false;
		}

	}
	//-- 주민번호체크 끝 ----------------

	if(frm.strBankCode.value == ""){  alert("은행을 선택하세요..");  return false; }

	if(!input_check(frm.strAccountNo.value)){  alert("계좌번호를 입력하세요..");  frm.strAccountNo.focus();  return false; }

	if(bank_proc == 0){
		bank_proc = 1;
		$(document).ready(function() {

			var url = join_url+'/mypage/check_count_proc.php';

			$.post(url,{
					'service':frm.service.value,
					'svcGbn':frm.svcGbn.value,
					'svc_cls':frm.svc_cls.value,
					'USERNM':usernm,
					'JUMINNO':jumin,
					'strBankCode':frm.strBankCode.value,
					'strAccountNo':frm.strAccountNo.value,
					'bank_private_name_sub':frm.bank_private_name_sub.value
				},
				function(result) {

					$('#ajax_return_txt').val(result);

					bank_proc = 0;
					var ary_rst = result.split('*:'); // 0:결과코드, 1:메시지, 2:주문번호

					if(ary_rst[0] == '0000'){
						frm.private_yn.value="Y";
						alert('인증되었습니다.\n\n아래의 확인 버튼을 클릭하여 계속 진행하여 주십시요.');
						str_account = usernm + ',' + jumin + ',' + frm.strBankCode.value + ',' + frm.strAccountNo.value;
					}
					else{
						alert('인증에 실패 하였습니다\n\n정확한 계좌번호로 다시 인증해 주세요\n\n결과코드:'+ary_rst[0]+'\n\n에러내용:'+ary_rst[1]+'\n\n주문번호:'+ary_rst[2]);
					}
				}
			);
		});
	}
	else{
		alert('인증처리 중입니다');
	}
}


function check_bank(){

	var frm = document.frm;

	if(member_type == 1){
		var usernm = frm.USERNM1.value;
		var jumin  = frm.JUMINNO1.value;
		var msg1 = '주민등록번호를 입력해 주세요';
		var msg2 = '주민등록번호는 숫자만 입력 할 수 있습니다';
		var msg3 = '성명을 입력해 주세요';
		var msg4 = '주민등록번호 자리수(13자리)가 맞지 않습니다.';
	}else{
		var usernm = frm.USERNM2.value;
		var jumin  = frm.JUMINNO2.value;
		var msg1 = '법인등록번호를 입력해 주세요';
		var msg2 = '법인등록번호는 숫자만 입력 할 수 있습니다';
		var msg3 = '상호명을 입력해 주세요';
		var msg4 = '법인등록번호 자리수(13자리)가 맞지 않습니다.';
	}

	if(!input_check(usernm)) { alert(msg3); return false; }
	if(!input_check(jumin))  { alert(msg1); return false; }
	if(jumin.length != 13)   { alert(msg4); return false; }

	for(i=0;i<jumin.length;i++) {
		c = jumin.charAt(i);
		if((c < '0' || c > '9')){
			alert(msg2);
			return false;
		}
	}

	if(frm.strBankCode.value == "") {  alert("은행을 선택하세요..");  return false; }
	if(!input_check(frm.strAccountNo.value)) {  alert("계좌번호를 입력하세요..");  frm.strAccountNo.focus();  return false; }

	if(bank_proc == 0) {

		bank_proc = 1;

		$(document).ready(function() {

			var url = join_url+'/mypage/check_count_proc.php';

			$.post(url,{
					'service':frm.service.value,
			    'svcGbn':frm.svcGbn.value,
					'svc_cls':frm.svc_cls.value,
					'USERNM':usernm,
					'JUMINNO':jumin,
					'strBankCode':frm.strBankCode.value,
					'strAccountNo':frm.strAccountNo.value,
				},
				function(result) {
					bank_proc = 0;
					var ary_rst = result.split('*:'); // 0:결과코드, 1:메시지, 2:주문번호

					if(ary_rst[0] == '0000'){
						alert('인증되었습니다.\n\n아래의 확인 버튼을 클릭하여 계속 진행하여 주십시요.');
						str_account = usernm + ',' + jumin + ',' + frm.strBankCode.value + ',' + frm.strAccountNo.value;
					}
					else{
						alert('인증에 실패 하였습니다\n\n정확한 계좌번호로 다시 인증해 주세요\n\n결과코드:'+ary_rst[0]+'\n\n에러내용:'+ary_rst[1]+'\n\n주문번호:'+ary_rst[2]);
					}
				}
			);
		});

	}
	else{
		alert('인증처리 중입니다');
	}

}

function member_modify(){

	var frm           = document.frm;
	var member_type   = frm.member_type.value;
	var mb_no         = frm.mb_no.value;			// 유저 인덱스
	var mb_name       = frm.mb_name.value;			// 성명 또는 담당자명
	var pass          = frm.mb_password.value;		// 비밀번호
	var cfm_password  = frm.cfm_password.value;		// 비밀번호확인
	var mb_hp1        = frm.mb_hp1.value;
	var mb_hp2        = frm.mb_hp2.value;
	var mb_hp3        = frm.mb_hp3.value;
	var email         = frm.mb_email.value;			// 이메일

	if(member_type=='1') { // 개인회원
		if(!input_check(mb_name)){ alert("성명을 입력해주세요."); return false; }
		if(input_check(pass)){ // 비밀번호를 입력하였을 경우
			if(pass != frm.cfm_password.value){ alert('입력하신 비밀번호와 비밀번호 확인이 다릅니다\n\n비밀번호를 정확하게 입력해 주세요');  return false; }
		}
	}
	else {	// 기업회원
		var mb_co_name    = frm.mb_co_name.value;		// 상호명
		var mb_co_reg_num = frm.mb_co_reg_num.value;	// 사업자번호
		if(input_check(pass)){ // 비밀번호를 입력하였을 경우
			if(pass != frm.cfm_password.value){ alert('입력하신 비밀번호와 비밀번호 확인이 다릅니다\n\n비밀번호를 정확하게 입력해 주세요');  return false; }
		}
		if(!input_check(mb_co_name))    { alert('상호명을 입력해 주세요'); frm.mb_co_name.focus(); return false; }
		if(!input_check(mb_co_reg_num)) { alert('사업자등록번호를 입력해 주세요'); frm.mb_co_reg_num.focus(); return false; }
		if(!input_check(mb_name))       { alert("담당자명을 입력해주세요."); return false; }
	}
	if(!input_check(mb_hp1)) { alert('휴대폰번호를 입력해 주세요'); return false; }
	if(!input_check(mb_hp2)) { alert('휴대폰번호를 입력해 주세요'); return false; }
	if(!input_check(mb_hp3)) { alert('휴대폰번호를 입력해 주세요'); return false; }
	if(!input_check(email))  { alert('이메일을 입력해 주세요'); frm.mb_email.focus(); return false; }
	if(!re.test(email))      { alert("올바른 이메일 주소를 입력하세요"); frm.mb_email.focus(); return false; }


	if( confirm('회원 정보를 저장 하시겠습니까?') ){

	//var ajax_data = $('#frm').serialize();
		var ajax_data = new FormData($('#frm')[0]);
		$($('#attachFile')[0].files).each(function(index, file){
			ajax_data.append('attach_file[]', file);
		});

		$.ajax({
			url : "/root_mypage/ajax_user_modify.php",
			type: "POST",
			processData: false,
			contentType: false,
			data : ajax_data,
			success: function(data, textStatus, jqXHR){
				$('#ajax_return_txt').val(data);

				if(data == 'o'){
					alert('저장되었습니다'); 
					//window.location.reload();
					self.location.href="/";
				}
				else if(data == 'dhp'){
					alert('변경된 핸드폰번호가 이미 등록 된 번호 입니다.');
				}
				else{
					alert('저장에 실패하였습니다\n\n다시 저장해 주세요');
				}
				return;
			},
			error: function (jqXHR, textStatus, errorThrown)	{

			}
		});

	}
}

function private_process(){
	var frm = document.frm;
	var private_mode = frm.private_mode.value;
	var member_type_val = frm.member_type.value;
	var private_yn = frm.private_yn.value;
	var zip_num = frm.zip_num.value;
	var receive_method = frm.receive_method.value;

	if(private_yn=="Y"){
		if(zip_num == '우편번호' || !input_check(zip_num)){ alert('주소를 입력해 주세요');  return false; }
		var mb_addr1 = frm.address_road.value;
		var mb_addr2 = frm.mb_addr2.value;
		var mb_addr_jibeon = frm.address_dong.value;
		var bank_code = frm.strBankCode.value; // 은행코드

		if(member_type == 1) {
			var bank_private_name = frm.USERNM1.value;
			var jumin  = frm.JUMINNO1.value;  // 주민등록번호
		}
		else {
			var bank_private_name = frm.USERNM1.value;
			var jumin  = frm.JUMINNO2.value;  // 법인번호
		}

		var str_account2 = bank_private_name + ',' + jumin + ',' + frm.strBankCode.value + ',' + frm.strAccountNo.value;

		if(str_account != str_account2){ alert('인증받은 계좌정보와 현재 입력되어 있는 계좌정보가 다릅니다\n\n계좌인증을 다시 해 주세요');  return false; }

		/*
		if(receive_method != '1') {
			alert('원리금 수취방식이 변경되어, 원리금의 예치금으로의 수취를 더 이상 이용 하실 수 없습니다.\n\n수취방식을 환급계좌로 변경하여 주십시요.');
			frm.receive_method.option[0].checked;
			return false;
		}
		*/

		switch(bank_code){
			case '004' : bank_name = '국민은행';  break;
			case '081' : bank_name = 'KEB하나은행';  break;
			case '088' : bank_name = '신한은행';  break;
			case '071' : bank_name = '우체국';  break;
			case '011' : bank_name = '농협은행';  break;
			case '020' : bank_name = '우리은행';  break;
			case '089' : bank_name = '케이뱅크';  break;
			case '090' : bank_name = '카카오뱅크';  break;
			case '007' : bank_name = '수협중앙회';  break;
			case '023' : bank_name = 'SC은행';  break;
			case '002' : bank_name = '산업은행';  break;
			case '003' : bank_name = '기업은행';  break;
			case '027' : bank_name = '한국씨티은행';  break;
			case '031' : bank_name = '대구은행';  break;
			case '032' : bank_name = '부산은행';  break;
			case '034' : bank_name = '광주은행';  break;
			case '035' : bank_name = '제주은행';  break;
			case '037' : bank_name = '전북은행';  break;
			case '039' : bank_name = '경남은행';  break;
			case '045' : bank_name = '새마을금고중앙회';  break;
			case '048' : bank_name = '신협중앙회';  break;
			case '050' : bank_name = '상호저축은행';  break;
			case '054' : bank_name = 'HSBC은행';  break;
			case '055' : bank_name = '도이치은행';  break;
			case '001' : bank_name = '한국은행';  break;
			case '008' : bank_name = '수출입은행';  break;
			case '012' : bank_name = '지역농․축협';  break;
			case '052' : bank_name = '모건스탠리은행';  break;
			case '056' : bank_name = '알비에스피엘씨은행';  break;
			case '057' : bank_name = '제이피모간체이스은행';  break;
			case '058' : bank_name = '미즈호은행';  break;
			case '059' : bank_name = '미쓰비시도쿄UFJ은행';  break;
			case '060' : bank_name = 'BOA은행';  break;
			case '061' : bank_name = '비엔피파리바은행';  break;
			case '062' : bank_name = '중국공상은행';  break;
			case '063' : bank_name = '중국은행';  break;
			case '064' : bank_name = '산림조합중앙회';  break;
			case '065' : bank_name = '대화은행';  break;
			case '066' : bank_name = '교통은행';  break;
			case '076' : bank_name = '신용보증기금';  break;
			case '077' : bank_name = '기술보증기금';  break;
			case '093' : bank_name = '한국주택금융공사';  break;
			case '094' : bank_name = '서울보증보험';  break;
			case '095' : bank_name = '경찰청';  break;
			case '096' : bank_name = '한국전자금융(주)';  break;
			case '099' : bank_name = '금융결제원';  break;
			case '209' : bank_name = '유안타증권';  break;
			case '218' : bank_name = '현대증권';  break;
			case '221' : bank_name = '골든브릿지투자증권';  break;
			case '222' : bank_name = '한양증권';  break;
			case '223' : bank_name = '리딩투자증권';  break;
			case '224' : bank_name = 'BNK투자증권';  break;
			case '225' : bank_name = 'IBK투자증권';  break;
			case '226' : bank_name = 'KB투자증권';  break;
			case '227' : bank_name = 'KTB투자증권';  break;
			case '230' : bank_name = '미래에셋증권';  break;
			case '238' : bank_name = '대우증권';  break;
			case '240' : bank_name = '삼성증권';  break;
			case '243' : bank_name = '한국투자증권';  break;
			case '247' : bank_name = 'NH투자증권';  break;
			case '261' : bank_name = '교보증권';  break;
			case '262' : bank_name = '하이투자증권';  break;
			case '263' : bank_name = 'HMC투자증권';  break;
			case '264' : bank_name = '키움증권';  break;
			case '265' : bank_name = '이베스트투자증권';  break;
			case '266' : bank_name = 'SK증권';  break;
			case '267' : bank_name = '대신증권';  break;
			case '269' : bank_name = '한화투자증권';  break;
			case '270' : bank_name = '하나대투증권';  break;
			case '278' : bank_name = '신한금융투자';  break;
			case '279' : bank_name = '동부증권';  break;
			case '280' : bank_name = '유진투자증권';  break;
			case '287' : bank_name = '메리츠종합금융증권';  break;
			case '290' : bank_name = '부국증권';  break;
			case '291' : bank_name = '신영증권';  break;
			case '292' : bank_name = '엘아이지투자증권';  break;
			case '293' : bank_name = '한국증권금융';  break;
			case '294' : bank_name = '펀드온라인코리아';  break;
			case '295' : bank_name = '우리종합금융';  break;
			case '296' : bank_name = '삼성선물';  break;
			case '297' : bank_name = '외환선물';  break;
			case '298' : bank_name = '현대선물';  break;
			case '041' : bank_name = '우리카드';  break;
			case '044' : bank_name = '외환카드';  break;
			case '361' : bank_name = 'BC카드';  break;
			case '367' : bank_name = '현대카드';  break;
			case '368' : bank_name = '롯데카드';  break;
			case '366' : bank_name = '신한카드';  break;
			case '369' : bank_name = '수협카드';  break;
			case '370' : bank_name = '씨티카드';  break;
			case '371' : bank_name = 'NH카드';  break;
			case '374' : bank_name = '하나SK카드';  break;
			case '381' : bank_name = 'KB국민카드';  break;
			case '364' : bank_name = '광주카드';  break;
			case '365' : bank_name = '삼성카드';  break;
			case '372' : bank_name = '전북카드';  break;
			case '373' : bank_name = '제주카드';  break;
			case '431' : bank_name = '미래에셋생명';  break;
			case '452' : bank_name = '삼성생명';  break;
			case '453' : bank_name = '흥국생명';  break;
		}
		frm.bank_name.value = bank_name;

		if(confirm('입력하신 정보를 저장 하시겠습니까?')) {
			frm.method  = 'post';
			frm.action  = "/root_mypage/ajax_bank_proc.php";
			frm.enctype = 'multipart/form-data';
			frm.target  = 'axFrame';
			frm.submit();
		}

	}
	else{
		alert("계좌인증을 해주세요");
	}

}


function private_process_for_business(){
	var frm = document.frm;
	var private_mode = frm.private_mode.value;
	var member_type_val = frm.member_type.value;
	var private_yn = frm.private_yn.value;
	var receive_method = frm.receive_method.value;

	/*
	if(receive_method != '1') {
		alert('신한은행 예치금 신탁관리 시스템이 적용되어, 원리금의 예치금으로의 수취를 더 이상 이용 하실 수 없습니다.\n\n수취방식을 환급계좌로 변경하여 주십시요.');
		frm.receive_method.option[0].checked;
		return false;
	}
	*/

	if(confirm('입력하신 정보를 저장 하시겠습니까?')){
		frm.method  = 'post';
		frm.action  = "/mypage/ajax_bank_proc.php";
		frm.enctype = 'multipart/form-data';
		frm.target  = 'axFrame';
		frm.submit();
	}

}


function go_submit(){

	var frm = document.frm;

	var mb_no = frm.mb_no.value; // 유저 인덱스

	var usernm = frm.mb_name.value;   // 성명

	var email = frm.mb_email.value; // 이메일
	if(!input_check(email)){ alert('이메일을 입력해 주세요');  return false; }

	if(str_account == ''){ alert('[계좌인증]을 먼저 해주세요');  return false; }

	if(member_type == 1){

		var bank_private_name = frm.USERNM1.value;
		var jumin  = frm.JUMINNO1.value;  // 주민등록번호

	}else{

		var bank_private_name = frm.USERNM1.value;
		var jumin  = frm.JUMINNO2.value;  // 법인번호

	}


	var str_account2 = bank_private_name + ',' + jumin + ',' + frm.strBankCode.value + ',' + frm.strAccountNo.value;

	if(str_account != str_account2){ alert('인증받은 계좌정보와 현재 입력되어 있는 계좌정보가 다릅니다\n\n계좌인증을 다시 해 주세요');  return false; }

	var pass = frm.mb_password.value; // 비밀번호

	if(input_check(pass)){ // 비밀번호를 입력하였을 경우
		if(pass != frm.cfm_password.value){ alert('입력하신 비밀번호와 비밀번호 확인이 다릅니다\n\n비밀번호를 정확하게 입력해 주세요');  return false; }
	}

	var zip_num = document.getElementById('zip_num').value;
	if(zip_num == '우편번호' || !input_check(zip_num)){ alert('주소를 입력해 주세요');  return false; }

	var mb_addr1 = document.getElementById('address_road').value;
	var mb_addr2 = frm.mb_addr2.value;
	var mb_addr_jibeon = document.getElementById('address_dong').value;

	var bank_code = frm.strBankCode.value; // 은행코드
	switch(bank_code){
		case '001' : bank_name = '한국은행';  break;
		case '002' : bank_name = '산업은행';  break;
		case '003' : bank_name = '기업은행';  break;
		case '004' : bank_name = '국민은행';  break;
		case '007' : bank_name = '수협중앙회';  break;
		case '008' : bank_name = '수출입은행';  break;
		case '011' : bank_name = '농협은행';  break;
		case '012' : bank_name = '지역농․축협';  break;
		case '020' : bank_name = '우리은행';  break;
		case '023' : bank_name = 'SC은행';  break;
		case '027' : bank_name = '한국씨티은행';  break;
		case '031' : bank_name = '대구은행';  break;
		case '032' : bank_name = '부산은행';  break;
		case '034' : bank_name = '광주은행';  break;
		case '035' : bank_name = '제주은행';  break;
		case '037' : bank_name = '전북은행';  break;
		case '039' : bank_name = '경남은행';  break;
		case '041' : bank_name = '우리카드';  break;
		case '044' : bank_name = '외환카드';  break;
		case '045' : bank_name = '새마을금고중앙회';  break;
		case '048' : bank_name = '신협중앙회';  break;
		case '050' : bank_name = '상호저축은행';  break;
		case '052' : bank_name = '모건스탠리은행';  break;
		case '054' : bank_name = 'HSBC은행';  break;
		case '055' : bank_name = '도이치은행';  break;
		case '056' : bank_name = '알비에스피엘씨은행';  break;
		case '057' : bank_name = '제이피모간체이스은행';  break;
		case '058' : bank_name = '미즈호은행';  break;
		case '059' : bank_name = '미쓰비시도쿄UFJ은행';  break;
		case '060' : bank_name = 'BOA은행';  break;
		case '061' : bank_name = '비엔피파리바은행';  break;
		case '062' : bank_name = '중국공상은행';  break;
		case '063' : bank_name = '중국은행';  break;
		case '064' : bank_name = '산림조합중앙회';  break;
		case '065' : bank_name = '대화은행';  break;
		case '066' : bank_name = '교통은행';  break;
		case '071' : bank_name = '우체국';  break;
		case '076' : bank_name = '신용보증기금';  break;
		case '077' : bank_name = '기술보증기금';  break;
		case '081' : bank_name = 'KEB하나은행';  break;
		case '088' : bank_name = '신한은행';  break;
		case '089' : bank_name = '케이뱅크';  break;
		case '090' : bank_name = '카카오뱅크';  break;
		case '093' : bank_name = '한국주택금융공사';  break;
		case '094' : bank_name = '서울보증보험';  break;
		case '095' : bank_name = '경찰청';  break;
		case '096' : bank_name = '한국전자금융(주)';  break;
		case '099' : bank_name = '금융결제원';  break;
		case '209' : bank_name = '유안타증권';  break;
		case '218' : bank_name = '현대증권';  break;
		case '221' : bank_name = '골든브릿지투자증권';  break;
		case '222' : bank_name = '한양증권';  break;
		case '223' : bank_name = '리딩투자증권';  break;
		case '224' : bank_name = 'BNK투자증권';  break;
		case '225' : bank_name = 'IBK투자증권';  break;
		case '226' : bank_name = 'KB투자증권';  break;
		case '227' : bank_name = 'KTB투자증권';  break;
		case '230' : bank_name = '미래에셋증권';  break;
		case '238' : bank_name = '대우증권';  break;
		case '240' : bank_name = '삼성증권';  break;
		case '243' : bank_name = '한국투자증권';  break;
		case '247' : bank_name = 'NH투자증권';  break;
		case '261' : bank_name = '교보증권';  break;
		case '262' : bank_name = '하이투자증권';  break;
		case '263' : bank_name = 'HMC투자증권';  break;
		case '264' : bank_name = '키움증권';  break;
		case '265' : bank_name = '이베스트투자증권';  break;
		case '266' : bank_name = 'SK증권';  break;
		case '267' : bank_name = '대신증권';  break;
		case '269' : bank_name = '한화투자증권';  break;
		case '270' : bank_name = '하나대투증권';  break;
		case '278' : bank_name = '신한금융투자';  break;
		case '279' : bank_name = '동부증권';  break;
		case '280' : bank_name = '유진투자증권';  break;
		case '287' : bank_name = '메리츠종합금융증권';  break;
		case '290' : bank_name = '부국증권';  break;
		case '291' : bank_name = '신영증권';  break;
		case '292' : bank_name = '엘아이지투자증권';  break;
		case '293' : bank_name = '한국증권금융';  break;
		case '294' : bank_name = '펀드온라인코리아';  break;
		case '295' : bank_name = '우리종합금융';  break;
		case '296' : bank_name = '삼성선물';  break;
		case '297' : bank_name = '외환선물';  break;
		case '298' : bank_name = '현대선물';  break;
		case '361' : bank_name = 'BC카드';  break;
		case '364' : bank_name = '광주카드';  break;
		case '365' : bank_name = '삼성카드';  break;
		case '366' : bank_name = '신한카드';  break;
		case '367' : bank_name = '현대카드';  break;
		case '368' : bank_name = '롯데카드';  break;
		case '369' : bank_name = '수협카드';  break;
		case '370' : bank_name = '씨티카드';  break;
		case '371' : bank_name = 'NH카드';  break;
		case '372' : bank_name = '전북카드';  break;
		case '373' : bank_name = '제주카드';  break;
		case '374' : bank_name = '하나SK카드';  break;
		case '381' : bank_name = 'KB국민카드';  break;
		case '431' : bank_name = '미래에셋생명';  break;
		case '452' : bank_name = '삼성생명';  break;
		case '453' : bank_name = '흥국생명';  break;
	}

	var account_num = frm.strAccountNo.value; // 계좌번호

	if(document.getElementById('mb_mailling').checked){ var mailling = 1; }else{ var mailling = 0; } // 메일수신여부 (1:수신, 0:비수신)
	if(document.getElementById('mb_sms').checked){      var sms = 1;      }else{ var sms = 0; }      // 문자수신여부 (1:수신, 0:비수신)

	if(confirm('입력하신 정보를 저장 하시겠습니까?')){

		var prm = mb_no+'*:'+email+'*:'+pass+'*:'+usernm+'*:'+jumin+'*:'+zip_num+'*:'+mb_addr1+'*:'+mb_addr2+'*:'+mb_addr_jibeon+'*:'+bank_name+'*:'+account_num+'*:'+mailling+'*:'+sms+'*:'+bank_code+'*:'+bank_private_name;

		ajax_call(2,prm);

	}

}
