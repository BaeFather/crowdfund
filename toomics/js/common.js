$(document).ready(function(){

	$('.navi > li').mouseenter(function(){
		$(this).find('.subMenu').slideDown('fast');
		console.log('AAA');
	}).mouseleave(function(){
		$('.subMenu').slideUp('fast');
	});

});

// 전역 변수
var errmsg = "";
var errfld = null;
//var root_url = "https://hellofunding.co.kr";
var root_url = window.location.protocol + "//" + window.location.host;

// 필드 검사
function check_field(fld, msg)
{
    if ((fld.value = trim(fld.value)) == "")
        error_field(fld, msg);
    else
        clear_field(fld);
    return;
}

// 필드 오류 표시
function error_field(fld, msg)
{
    if (msg != "")
        errmsg += msg + "\n";
    if (!errfld) errfld = fld;
    fld.style.background = "#BDDEF7";
}

// 필드를 깨끗하게
function clear_field(fld)
{
    fld.style.background = "#FFFFFF";
}

function trim(s)
{
    var t = "";
    var from_pos = to_pos = 0;

    for (i=0; i<s.length; i++)
    {
        if (s.charAt(i) == ' ')
            continue;
        else
        {
            from_pos = i;
            break;
        }
    }

    for (i=s.length; i>=0; i--)
    {
        if (s.charAt(i-1) == ' ')
            continue;
        else
        {
            to_pos = i;
            break;
        }
    }

    t = s.substring(from_pos, to_pos);
    //				alert(from_pos + ',' + to_pos + ',' + t+'.');
    return t;
}

// 자바스크립트로 PHP의 number_format 흉내를 냄
// 숫자에 , 를 출력
function number_format(val)
{
    if(val){
		if (isNaN(val)) return val;
        val = toNumber(val).toString();
        var len = val.length;
        var str = "";
        if(len > 3){
            var in_c = len % 3;
            if(!in_c) in_c = 3;

            for(n = 0; n < len; n++){
                if(n == in_c){
                    str += ",";
                    in_c = 3 + in_c;
                }
                str += val.charAt(n);
            }
            return str;
        }else{
            return val;
        }
    }
}

function NumberFormat(fn){
  var str = fn.value;
  var Re = /[^0-9]/g;
  var ReN = /(-?[0-9]+)([0-9]{3})/;
  str = str.replace(Re,'');
  while (ReN.test(str)) {
    str = str.replace(ReN, "$1,$2");
    }
  fn.value = str;
}

function toNumber(str){
    str = str.toString();
    var len = str.length;
    var num = "";

    for(i=0; i<len; i++){
        int = parseInt(str.charAt(i));
        if(int >= 0 && int <= 9) num += str.charAt(i);
    }
    return parseInt(num);
}

function onlyDigit(el) {
  el.value = el.value.replace(/\D/g,'');
}


// 새 창
function popup_window(url, winname, opt)
{
    window.open(url, winname, opt);
}


// 폼메일 창
function popup_formmail(url)
{
    opt = 'scrollbars=yes,width=417,height=385,top=10,left=20';
    popup_window(url, "wformmail", opt);
}

// , 를 없앤다.
function no_comma(data)
{
    var tmp = '';
    var comma = ',';
    var i;

    for (i=0; i<data.length; i++)
    {
        if (data.charAt(i) != comma)
            tmp += data.charAt(i);
    }
    return tmp;
}

// 삭제 검사 확인
function del(href)
{
    if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        var iev = -1;
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
            if (re.exec(ua) != null)
                iev = parseFloat(RegExp.$1);
        }

        // IE6 이하에서 한글깨짐 방지
        if (iev != -1 && iev < 7) {
            document.location.href = encodeURI(href);
        } else {
            document.location.href = href;
        }
    }
}

// 쿠키 생성
function set_cookie(name, value, expirehours, domain)
{
	var today = new Date();
	if(expirehours) {
		today.setTime(today.getTime()+(1000*60*60 * expirehours));
	}
	else {
		today.toGMTString();
	}
	var _secure = true;

	cookie_string  = name + '=' + escape( value ) + '; expires=' + today.toGMTString() + '; path=/; domain=' + domain;
  cookie_string += (_secure) ? '; Secure' : '';

	document.cookie = cookie_string;
}

// 쿠키 지움
function delete_cookie(name)
{
	var today = new Date();

	today.setTime(today.getTime() - 1);
	document.cookie = name + "=; expires=" + today.toGMTString() + '; path=/';
}


// 쿠키 얻음
function get_cookie(name)
{
    var find_sw = false;
    var start, end;
    var i = 0;

    for (i=0; i<= document.cookie.length; i++)
    {
        start = i;
        end = start + name.length;

        if(document.cookie.substring(start, end) == name)
        {
            find_sw = true
            break
        }
    }

    if (find_sw == true)
    {
        start = end + 1;
        end = document.cookie.indexOf(";", start);

        if(end < start)
            end = document.cookie.length;

        return unescape(document.cookie.substring(start, end));
    }
    return "";
}


var last_id = null;
function menu(id)
{
    if (id != last_id)
    {
        if (last_id != null)
            document.getElementById(last_id).style.display = "none";
        document.getElementById(id).style.display = "block";
        last_id = id;
    }
    else
    {
        document.getElementById(id).style.display = "none";
        last_id = null;
    }
}

function textarea_decrease(id, row)
{
    if (document.getElementById(id).rows - row > 0)
        document.getElementById(id).rows -= row;
}

function textarea_original(id, row)
{
    document.getElementById(id).rows = row;
}

function textarea_increase(id, row)
{
    document.getElementById(id).rows += row;
}

// 글숫자 검사
function check_byte(content, target)
{
    var i = 0;
    var cnt = 0;
    var ch = '';
    var cont = document.getElementById(content).value;

    for (i=0; i<cont.length; i++) {
        ch = cont.charAt(i);
        if (escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }
    // 숫자를 출력
    document.getElementById(target).innerHTML = cnt;

    return cnt;
}

// 브라우저에서 오브젝트의 왼쪽 좌표
function get_left_pos(obj)
{
    var parentObj = null;
    var clientObj = obj;
    //var left = obj.offsetLeft + document.body.clientLeft;
    var left = obj.offsetLeft;

    while((parentObj=clientObj.offsetParent) != null)
    {
        left = left + parentObj.offsetLeft;
        clientObj = parentObj;
    }

    return left;
}

// 브라우저에서 오브젝트의 상단 좌표
function get_top_pos(obj)
{
    var parentObj = null;
    var clientObj = obj;
    //var top = obj.offsetTop + document.body.clientTop;
    var top = obj.offsetTop;

    while((parentObj=clientObj.offsetParent) != null)
    {
        top = top + parentObj.offsetTop;
        clientObj = parentObj;
    }

    return top;
}

function flash_movie(src, ids, width, height, wmode)
{
    var wh = "";
    if (parseInt(width) && parseInt(height))
        wh = " width='"+width+"' height='"+height+"' ";
    return "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' "+wh+" id="+ids+"><param name=wmode value="+wmode+"><param name=movie value="+src+"><param name=quality value=high><embed src="+src+" quality=high wmode="+wmode+" type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?p1_prod_version=shockwaveflash' "+wh+"></embed></object>";
}

function obj_movie(src, ids, width, height, autostart)
{
    var wh = "";
    if (parseInt(width) && parseInt(height))
        wh = " width='"+width+"' height='"+height+"' ";
    if (!autostart) autostart = false;
    return "<embed src='"+src+"' "+wh+" autostart='"+autostart+"'></embed>";
}

function doc_write(cont)
{
    document.write(cont);
}

var win_password_lost = function(href) {
    window.open(href, "win_password_lost", "left=50, top=50, width=617, height=330, scrollbars=1");
}

$(document).ready(function(){
    $("#login_password_lost, #ol_password_lost").click(function(){
        win_password_lost(this.href);
        return false;
    });
});

/**
 * 포인트 창
 **/
var win_point = function(href) {
    var new_win = window.open(href, 'win_point', 'left=100,top=100,width=600, height=600, scrollbars=1');
    new_win.focus();
}

/**
 * 쪽지 창
 **/
var win_memo = function(href) {
    var new_win = window.open(href, 'win_memo', 'left=100,top=100,width=620,height=500,scrollbars=1');
    new_win.focus();
}

/**
 * 메일 창
 **/
var win_email = function(href) {
    var new_win = window.open(href, 'win_email', 'left=100,top=100,width=600,height=580,scrollbars=0');
    new_win.focus();
}

/**
 * 자기소개 창
 **/
var win_profile = function(href) {
    var new_win = window.open(href, 'win_profile', 'left=100,top=100,width=620,height=510,scrollbars=1');
    new_win.focus();
}

/**
 * 스크랩 창
 **/
var win_scrap = function(href) {
    var new_win = window.open(href, 'win_scrap', 'left=100,top=100,width=600,height=600,scrollbars=1');
    new_win.focus();
}

/**
 * 홈페이지 창
 **/
var win_homepage = function(href) {
    var new_win = window.open(href, 'win_homepage', '');
    new_win.focus();
}

/**
 * 우편번호 창
 **/
var win_zip = function(frm_name, frm_zip, frm_addr1, frm_addr2, frm_addr3, frm_jibeon) {

	if(typeof daum === 'undefined') {
		alert("다음 우편번호 postcode.v2.js 파일이 로드되지 않았습니다.");
		return false;
	}

	var zip_case = 1;   //0이면 레이어, 1이면 페이지에 끼워 넣기, 2이면 새창

	var complete_fn = function(data){
		// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

		var fullAddr  = '';		// 최종 주소 변수
		var extraAddr = '';		// 조합형 주소 변수

		if (data.userSelectedType === 'R'){		// 사용자가 도로명 주소를 선택했을 경우
			fullAddr = data.roadAddress;
		}
		else {		// 사용자가 지번 주소를 선택했을 경우(J)
			fullAddr = data.jibunAddress;
		}

		// 사용자가 선택한 주소가 도로명 타입일때 조합한다.
		if(data.userSelectedType === 'R') {		//법정동명이 있을 경우 추가한다.
			if(data.bname !== ''){
				extraAddr += data.bname;
			}
			if(data.buildingName !== '') {		// 건물명이 있을 경우 추가한다.
				extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
			}
			extraAddr = (extraAddr !== '' ? ' ('+ extraAddr +')' : '');		// 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
		}

		// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
		var of = document[frm_name];

		of[frm_zip].value	   = data.zonecode;
		of[frm_addr1].value  = data.roadAddress + extraAddr;
		of[frm_jibeon].value = data.jibunAddress;

		of[frm_addr2].focus();

	};

	switch(zip_case) {
		case 1 :	//iframe을 이용하여 페이지에 끼워 넣기
			var daum_pape_id = 'daum_juso_page'+frm_zip,
			element_wrap = document.getElementById(daum_pape_id),
			currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

			if(element_wrap == null) {
				element_wrap = document.createElement("div");
				element_wrap.setAttribute("id", daum_pape_id);
				element_wrap.style.cssText = 'display:none;border:1px solid;left:0;width:100%;height:300px;margin:5px 0;position:relative;-webkit-overflow-scrolling:touch;';
				element_wrap.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-21px;z-index:1" class="close_daum_juso" alt="접기 버튼">';
				jQuery('form[name="'+frm_name+'"]').find('input[name="'+frm_addr1+'"]').before(element_wrap);
				jQuery("#"+daum_pape_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e){
					e.preventDefault();
					jQuery(this).parent().hide();
				});
			}

			daum.postcode.load(function(){
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
						element_wrap.style.display = 'none';			// iframe을 넣은 element를 안보이게 한다.
						document.body.scrollTop = currentScroll;	// 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
					},
					// 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분.
					onresize : function(size) {
						element_wrap.style.height = size.height + "px";		// iframe을 넣은 element의 높이값을 조정한다.
					},
					width : '100%',
					height : '100%'
				}).embed(element_wrap);
			});

			element_wrap.style.display = 'block';
		break;

		case 2 :	//새창으로 띄우기
			daum.postcode.load(function(){
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
					}
				}).open();
			});
		break;

		default :   //iframe을 이용하여 레이어 띄우기
			var rayer_id = 'daum_juso_rayer'+frm_zip,
			    element_layer = document.getElementById(rayer_id);

			if(element_layer == null) {
				element_layer = document.createElement("div");
				element_layer.setAttribute("id", rayer_id);
				element_layer.style.cssText = 'display:none;border:5px solid;position:fixed;width:300px;height:460px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;z-index:10000';
				element_layer.innerHTML = '<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" class="close_daum_juso" alt="닫기 버튼">';
				document.body.appendChild(element_layer);
				jQuery("#"+rayer_id).off("click", ".close_daum_juso").on("click", ".close_daum_juso", function(e){
					e.preventDefault();
					jQuery(this).parent().hide();
				});
			}

			daum.postcode.load(function(){
				new daum.Postcode({
					oncomplete: function(data) {
						complete_fn(data);
						element_layer.style.display = 'none';
					},
					width : '100%',
					height : '100%'
				}).embed(element_layer);
			});

			element_layer.style.display = 'block';
	}
}

/**
 * 새로운 비밀번호 분실 창 : 101123
 **/
win_password_lost = function(href)
{
    var new_win = window.open(href, 'win_password_lost', 'width=617, height=330, scrollbars=1');
    new_win.focus();
}

/**
 * 설문조사 결과
 **/
var win_poll = function(href) {
    var new_win = window.open(href, 'win_poll', 'width=616, height=500, scrollbars=1');
    new_win.focus();
}

/**
 * 스크린리더 미사용자를 위한 스크립트 - 지운아빠 2013-04-22
 * alt 값만 갖는 그래픽 링크에 마우스오버 시 title 값 부여, 마우스아웃 시 title 값 제거
 **/
$(function() {
    $('a img').mouseover(function() {
        $a_img_title = $(this).attr('alt');
        $(this).attr('title', $a_img_title);
    }).mouseout(function() {
        $(this).attr('title', '');
    });
});

/**
 * 텍스트 리사이즈
**/
function font_resize(id, rmv_class, add_class)
{
    var $el = $("#"+id);

    $el.removeClass(rmv_class).addClass(add_class);

    set_cookie("ck_font_resize_rmv_class", rmv_class, 1, g5_cookie_domain);
    set_cookie("ck_font_resize_add_class", add_class, 1, g5_cookie_domain);
}

/**
 * 댓글 수정 토큰
**/
function set_comment_token(f)
{
    if(typeof f.token === "undefined")
        $(f).prepend('<input type="hidden" name="token" value="">');

    $.ajax({
        url: g5_bbs_url+"/ajax.comment_token.php",
        type: "GET",
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            f.token.value = data.token;
        }
    });
}

/**
 * URL 형식 체크
 */
function isURL(strUrl) {
    var expUrl = /^http[s]?\:\/\//i;
    return expUrl.test(strUrl);
}

$(function(){
    $(".win_point").click(function() {
        win_point(this.href);
        return false;
    });

    $(".win_memo").click(function() {
        win_memo(this.href);
        return false;
    });

    $(".win_email").click(function() {
        win_email(this.href);
        return false;
    });

    $(".win_scrap").click(function() {
        win_scrap(this.href);
        return false;
    });

    $(".win_profile").click(function() {
        win_profile(this.href);
        return false;
    });

    $(".win_homepage").click(function() {
        win_homepage(this.href);
        return false;
    });

    $(".win_password_lost").click(function() {
        win_password_lost(this.href);
        return false;
    });

    /*
    $(".win_poll").click(function() {
        win_poll(this.href);
        return false;
    });
    */

    // 사이드뷰
    var sv_hide = false;
    $(".sv_member, .sv_guest").click(function() {
        $(".sv").removeClass("sv_on");
        $(this).closest(".sv_wrap").find(".sv").addClass("sv_on");
    });

    $(".sv, .sv_wrap").hover(
        function() {
            sv_hide = false;
        },
        function() {
            sv_hide = true;
        }
    );

    $(".sv_member, .sv_guest").focusin(function() {
        sv_hide = false;
        $(".sv").removeClass("sv_on");
        $(this).closest(".sv_wrap").find(".sv").addClass("sv_on");
    });

    $(".sv a").focusin(function() {
        sv_hide = false;
    });

    $(".sv a").focusout(function() {
        sv_hide = true;
    });

    // 셀렉트 ul
    var sel_hide = false;
    $('.sel_btn').click(function() {
        $('.sel_ul').removeClass('sel_on');
        $(this).siblings('.sel_ul').addClass('sel_on');
    });

    $(".sel_wrap").hover(
        function() {
            sel_hide = false;
        },
        function() {
            sel_hide = true;
        }
    );

    $('.sel_a').focusin(function() {
        sel_hide = false;
    });

    $('.sel_a').focusout(function() {
        sel_hide = true;
    });

    $(document).click(function() {
        if(sv_hide) { // 사이드뷰 해제
            $(".sv").removeClass("sv_on");
        }
        if (sel_hide) { // 셀렉트 ul 해제
            $('.sel_ul').removeClass('sel_on');
        }
    });

    $(document).focusin(function() {
        if(sv_hide) { // 사이드뷰 해제
            $(".sv").removeClass("sv_on");
        }
        if (sel_hide) { // 셀렉트 ul 해제
            $('.sel_ul').removeClass('sel_on');
        }
    });

    $(document).on( "keyup change", "textarea#wr_content[maxlength]", function(){
        var str = $(this).val();
        var mx = parseInt($(this).attr("maxlength"));
        if (str.length > mx) {
            $(this).val(str.substr(0, mx));
            return false;
        }
    });
});


// F5, ctrl+F5, ctrl+r 방지
/*
$(document).keydown(function (e) {
    var allowPageList   = new Array('/a.php', '/investment/invest_list.php');
    var bBlockF5Key     = true;
    for (number in allowPageList) {
        var regExp = new RegExp('^' + allowPageList[number] + '.*', 'i');
        if (regExp.test(document.location.pathname)) {
            bBlockF5Key = false;
            break;
        }
    }

    if (bBlockF5Key) {
        if (e.which === 116) {
            if (typeof event == "object") {
                event.keyCode = 0;
            }
            return false;
        } else if (e.which === 82 && e.ctrlKey) {
            return false;
        }
    }
});
*/

function copy_trackback(trb) {
    var IE=(document.all)?true:false;
    if (IE) {
        if(confirm("이 글의 트랙백 주소를 클립보드에 복사하시겠습니까?"))
            window.clipboardData.setData("Text", trb);
    } else {
        temp = prompt("이 글의 트랙백 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", trb);
    }
}


function copyToClipboard(val) {
	var t = document.createElement("textarea");
	document.body.appendChild(t);
	t.value = val;
	t.select();
	document.execCommand('copy');
	document.body.removeChild(t);
	alert('복사되었습니다.')
}
