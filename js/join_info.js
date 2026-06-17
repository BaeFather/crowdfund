var join_url = '';
var user_id  = ''; // 유저가 입력한 아이디
var member_type = 1; // 1:개인회원, 2:기업(법인)회원
var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
function ajax_call(gbn,prm1){

	$(document).ready(function() {

    if(gbn == 1) {
			var url = join_url+'/member/confirm_id.php';
		}
		else if(gbn == 2) {
			var url = join_url+'/member/join_info_proc.php';
		}

		$.post(url,{'prm1':prm1},

			function(result) {
				$('#ajax_return_txt').val(result);
				if(gbn == 1) {

					if(result == 'x') {
						alert('이미 등록되어 있는 아이디 입니다\n\n다른 아이디를 입력해 주세요');
						document.getElementById('mb_id').value = '';
					}
					else {
						alert('사용 가능한 아이디입니다');
						user_id = prm1;
					}

				}
				else {

					var arr = result.split(":::");
          if(arr.length>=4) {

						msg = "당신의 설레는 내일, 헬로펀딩에 오신것을 환영합니다.";
						if(arr[3]==2) {
							msg + "\n\n"
							    + "기업 회원가입을 축하드립니다.\n"
							    + "사업자등록증과 통장사본을 등록해주세요\n\n"
							    + "◆ 등록방법 ◆\n\n"
							    + "'회원정보 > 환급계좌등록 및 원천징수정보'에서 업로드 하시거나,\n"
							    + "hellofunding@gmail.com 으로 보내주시기 바랍니다.";
						}

						if(document.getElementById('rec_flag')) {
							if(document.getElementById('rec_flag').value=='Y') {
								msg + "\n\n"
								    + "가상계좌를 발급받으시면 추천인 이벤트 참여가 완료됩니다.\n(가상계좌를 발급 받으셔야 영화티켓이 지급됩니다.)\n\n'확인'을 클릭하시면 가상계좌 발급페이지로 이동합니다.";
								action_url = '/bbs/login_check.php?url=/deposit/deposit.php?tab=2';
							}
							else {
								action_url = '/bbs/login_check.php';
							}
						}
						else {
							action_url = '/bbs/login_check.php';
						}

						//location.href = './complete.php';

						// LiveLog TrackingCheck Script Start
						var tmp = new Date();
						LLOrderName = arr[4]; //상담자명(가입자명) 변수
						LLNumber = tmp.getTime() + (Math.floor(Math.random() * 100) + 1);
						LLDBid = LLOrderName + "|dinfo|" + LLNumber;
						LLscriptPlugIn.load('//livelog.co.kr/js/plugShow.php', "sg_check.payment('"+LLDBid+"','"+LLNumber+"','Y')");
						eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('0(5);6 0(a){1 b=2 3();1 c=b.4()+a;7(8){b=2 3();9(b.4()>c)d}}',14,14,'sleep|var|new|Date|getTime|2000|function|while|true|if||||return'.split('|'),0,{}));
						// LiveLog TrackingCheck Script End


						alert(msg);
            var f = document.flogin;
            f.method = 'post';
						f.action = action_url;
            f.mb_id.value = arr[1];
            f.mb_password.value = arr[2];
						f.submit();

					}
					else {
						if(result == 'x') {
							alert("시스템 에러 입니다. 관리자에 문의해 주세요");
							return;
						}
						else if(result == 'di') {
							alert("이미 등록 된 아이디 입니다.");
							return;
						}
						else if(result == 'dhp') {
							alert("이미 등록 된 핸드폰 번호 입니다.");
							return;
						}
						else if(result == 'o') {
							alert('가입되었습니다');
							location.href = './complete.php';
						}
						else {
							alert("시스템 에러 입니다. 관리자에 문의해 주세요");
							return;
						}
					}

				}

			}

		);

	});

}

// 멤버 타입 변경
function change_member_type(k){
	member_type = k;
	$('#member_type').val(k);
}


function input_check(msg){
	if(msg.replace(/(^\s*)|(\s*$)/g,"") == '') {
		return false;
	}
	else{
		return true;
	}
}


function confirm_id(){

	if(member_type == 1){
		var id = document.getElementById('mb_id1').value;
	}
	else {
		var id = document.getElementById('mb_id2').value;
	}

	if(!input_check(id)){ alert('아이디를 입력해 주세요');  return false; }

	if(!id_check(id)){ alert('아이디는 띄어쓰기 없이 영문 또는 영문과 숫자의 조합으로\n\n8자리 이상 15자리 이하로 입력하셔야 하며,\n\n첫글자는 반드시 영문으로 입력하셔야 합니다');  return false; }

	join_url = document.frm.url.value;

	ajax_call(1,id);

}


function id_check(str){ // 숫자와 알파벳만 입력 허용

	var safe_char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // 입력을 허용하는 글자들
	var len       = str.length;
	var result    = true;
	var char      = '';
	for(i=0;i<len;i++){

		char = str.charAt(i);

		if(i == 0) {
			var re2 = /[0-9]/i; // 숫자
			if(re2.test(char)){
				result = false;
				break;
			}
		}
		if(safe_char.indexOf(char) == -1) {
			result = false;
			break;
		}

	}

	if(len < 8 || len > 15){ result = false; }


	return result;

}


function pass_check(str){

	var result = true;

	var re1 = /[a-zA-Z]/i; // 영문
	var re2 = /[0-9]/i; // 숫자
	var re3 = /[@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?]/i; // 특수문자

	if(!re1.test(str)) { result = false; }
	if(!re2.test(str)) { result = false; }
	if(!re3.test(str)) { result = false; }

	var len = str.length;
	if(len < 8 || len > 15) { result = false; }
	if(str.indexOf(' ') > -1) { result = false; }

	return result;

}



function go_sumbit() {

	var f = document.frm;
	var mb_dupinfo = f.mb_dupinfo.value;

	if(member_type == 1) {					// 개인회원

		var mb_id      = f.mb_id1.value;
		var mb_pass    = f.mb_password1.value;
		var cfm_pass   = document.getElementById('cfm_password1').value;
		var mb_name    = f.mb_name1.value;
		var mb_hp1     = f.mb_hp1_1.value;
		var mb_hp2     = f.mb_hp1_2.value;
		var mb_hp3     = f.mb_hp1_3.value;
		var email      = f.mb_email1.value;
		var agree      = document.getElementById('articles_agree1').checked;
		var mailling   = (document.getElementById('mb_mailling1').checked) ? 1 : 0;
		var sms        = (document.getElementById('mb_sms1').checked) ? 1 : 0;

		var rec_mb_id  = f.rec_mb_id.value;
		var rec_flag   = document.getElementById('rec_flag').value;

		if(!input_check(mb_id)) { alert('아이디를 입력해 주세요'); f.mb_id1.focus(); return false; }
		if(user_id == '') { alert('아이디 중복확인을 해 주세요'); f.mb_id1.focus(); return false; }
		if(mb_id != user_id) { alert('입력하신 아이디가 변경 되었습니다\n\n아이디 중복확인을 다시 해주세요'); return false; }
		if(!input_check(mb_pass)) { alert('비밀번호를 입력해 주세요'); f.mb_password1.focus(); return false; }
		if(!pass_check(mb_pass)) { alert('비밀번호는 공백 없이 영문,숫자,특수문자를\n\n혼합하여 8자리 이상 15자리 이하로\n\n입력하셔야 합니다'); f.mb_password1.focus(); return false; }
		if(!input_check(cfm_pass)) { alert('비밀번호확인을 입력해 주세요'); f.cfm_password1.focus(); return false; }
		if(mb_pass != cfm_pass) { alert('"비밀번호"와 "비밀번호확인"이 다릅니다\n\n비밀번호를 정확하게 입력해 주세요'); f.cfm_password1.focus(); return false; }

		if(f.is_sign.value != 'Y'){ alert('서비스 이용을 위하여 본인인증을 받으셔야 합니다.'); return false; }

		if(!input_check(mb_name)) { alert('성명을 입력해 주세요'); return false; }
		if(!input_check(mb_hp1)) { alert('휴대폰번호를 입력해 주세요'); return false; }
		if(!input_check(mb_hp2)) { alert('휴대폰번호를 입력해 주세요'); return false; }
		if(!input_check(mb_hp3)) { alert('휴대폰번호를 입력해 주세요'); return false; }
		if(!input_check(email)) { alert('이메일을 입력해 주세요'); f.mb_email1.focus(); return false; }
		if(!re.test(email)) { alert("올바른 이메일 주소를 입력하세요"); f.mb_email1.focus(); return false; }

		var cfm_msg  = '개인회원으로 가입하시겠습니까?';

	}
	else if(member_type == 2) {										// 기업회원

		var mb_id         = f.mb_id2.value;
		var mb_pass       = f.mb_password2.value;
		var cfm_pass      = document.getElementById('cfm_password2').value;
		var mb_co_name    = f.mb_co_name.value;
		var mb_co_reg_num = f.mb_co_reg_num.value;
		var mb_name       = f.mb_name2.value;
		var mb_hp1        = f.mb_hp2_1.value;
		var mb_hp2        = f.mb_hp2_2.value;
		var mb_hp3        = f.mb_hp2_3.value;
		var email         = f.mb_email2.value;
		var agree         = document.getElementById('articles_agree2').checked;
		var mailling      = (document.getElementById('mb_mailling2').checked) ? 1 : 0;
		var sms           = (document.getElementById('mb_sms2').checked) ? 1 : 0;

		if(!input_check(mb_id)) { alert('아이디를 입력해 주세요');  f.mb_id2.focus(); return false; }
		if(user_id == '') { alert('아이디 중복확인을 해 주세요'); f.mb_id2.focus(); return false; }
		if(mb_id != user_id) { alert('입력하신 아이디가 변경 되었습니다\n\n아이디 중복확인을 다시 해주세요'); return false; }
		if(!input_check(mb_pass)) { alert('비밀번호를 입력해 주세요'); f.mb_password2.focus(); return false; }
		if(!pass_check(mb_pass)) { alert('비밀번호는 공백 없이 영문,숫자,특수문자를\n\n혼합하여 8자리 이상 15자리 이하로\n\n입력하셔야 합니다'); f.mb_password2.focus(); return false; }
		if(!input_check(cfm_pass)) { alert('비밀번호확인을 입력해 주세요'); f.cfm_password1.focus(); return false; }
		if(mb_pass != cfm_pass) { alert('"비밀번호"와 "비밀번호확인"이 다릅니다\n\n비밀번호를 정확하게 입력해 주세요'); return false; }
		if(!input_check(mb_co_name)) { alert('상호명을 입력해 주세요'); f.mb_co_name.focus(); return false; }
		if(!input_check(mb_co_reg_num)) { alert('사업자등록번호를 입력해 주세요'); f.mb_co_reg_num.focus(); return false; }

		var pattern = /(^[0-9]{3}-[0-9]{2}-[0-9]{5}$)/;
		if (!pattern.test(mb_co_reg_num)) {
			alert("사업자등록번호 10자리를 '000-00-00000' 형식으로 입력하십시오.");
			f.mb_co_reg_num.focus();
			return false;
		}
		else {
			var sum = 0;
			var at = 0;
			var att = 0;
			saupjano_arr = mb_co_reg_num.split("-");
			var saupjano = saupjano_arr[0] + saupjano_arr[1] + saupjano_arr[2];
			sum = (saupjano.charAt(0)*1)+
			      (saupjano.charAt(1)*3)+
			      (saupjano.charAt(2)*7)+
			      (saupjano.charAt(3)*1)+
			      (saupjano.charAt(4)*3)+
			      (saupjano.charAt(5)*7)+
			      (saupjano.charAt(6)*1)+
			      (saupjano.charAt(7)*3)+
			      (saupjano.charAt(8)*5);
			sum += parseInt((saupjano.charAt(8)*5)/10);
			at = sum % 10;
			if (at != 0) att = 10 - at;

			if (saupjano.charAt(9) != att) {
				alert("올바른 사업자등록번호가 아닙니다."); return false;
			}
		}
		if(!input_check(mb_name)) { alert('담당자명을 입력해 주세요'); f.mb_name2.focus(); return false; }
		if(!input_check(mb_hp1)) { alert('휴대폰번호를 입력해 주세요'); f.mb_hp2_1.focus(); return false; }
		if(!input_check(mb_hp2)) { alert('휴대폰번호를 입력해 주세요'); f.mb_hp2_2.focus(); return false; }
		if(!input_check(mb_hp3)) { alert('휴대폰번호를 입력해 주세요'); f.mb_hp2_3.focus(); return false; }

		if(!input_check(email)) { alert('이메일을 입력해 주세요'); f.mb_email2.focus(); return false; }
		if(!re.test(email)) { alert("올바른 이메일 주소를 입력하세요"); f.mb_email2.focus(); return false; }

		var cfm_msg  = '기업회원으로 가입하시겠습니까?';

	}

	if(!agree) {
		alert('서비스이용약관 및 개인정보보호정책동의 에 체크를 해주셔야 합니다');
		document.getElementById('articles_agree2').focus();
		return false;
	}

	if(confirm(cfm_msg)) {
		f.method = 'post';
		f.enctype = 'multipart/form-data';
		f.action = '/member/join_info_proc.php';
		f.target = 'axFrame';
		f.submit();
	}

}


/*
var bank_proc = 0;
function check_bank(k){
	if(bank_proc == 0){
		bank_proc = 1;
		$(document).ready(function() {
			var url = join_url+'/member/check_count_proc.php';
			var frm = document.frm;
			if(k == 1){
				var prm1 = frm.strBankCode1.value;
				var prm2 = frm.strAccountNo1.value;
			}else{
				var prm1 = frm.strBankCode2.value;
				var prm2 = frm.strAccountNo2.value;
			}

			$.post(url,{'strBankCode':prm1, 'strAccountNo':prm2, 'service':frm.service.value, 'svcGbn':frm.svcGbn.value, 'svc_cls':frm.svc_cls.value },
				function(result) {
					bank_proc = 0;
					var ary_rst = result.split('*:'); // 0:결과코드, 1:메시지, 2:주문번호
					if(ary_rst[0] == '0000'){
						alert('인증되었습니다');
					}else{
						alert('인증에 실패 하였습니다\n\n정확한 계좌번호로 다시 인증해 주세요\n\n결과코드:'+ary_rst[0]+'\n\n에러내용:'+ary_rst[1]+'\n\n주문번호:'+ary_rst[2]);
					}
				}
			);
		});
	}else{
		alert('인증처리 중입니다');
    }
}

*/