<?
if (!defined('_GNUBOARD_')) exit;
?>

			<noscript>
				<p>
					귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
					<strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
				</p>
			</noscript>

		</div>

<? if($_COOKIE['debug_mode']) { ?>
	<div style="height:150px;"></div>
<? } ?>

</div>

<!-- 로딩 -->
<div id="loading" style="position:fixed; z-index:1001; top:0px; left:0px; width:100%; height:100%; display:none;">
	<table width="100%" height="100%">
	  <tr>
		  <td height="100%" align="center">
				<img src="/images/loading/ani_load.gif" width="24"><br/>
				<span style="display:inline-block;background:#888;color:#FFF;margin-top:8px; padding:0 10px; border-radius:12px;">loading</span>
			</td>
		</tr>
	</table>
</div>
<script>
loading = function(arg) {
	if(arg=='on') {
		$('#loading').css('display','block');
	}
	else {
		$('#loading').css('display','none');
	}
}
</script>

<!--ajax 실행 확인 영역-->
<div id="ajax_return_txt_zone" style="display:<?=($_COOKIE['debug_mode'])?'block':'none';?>;width:500px; right:1px; bottom:0; position:fixed;z-index:10000">
	<textarea id="ajax_return_txt" style="width:100%;height:250px;font-size:11px;color:red;border:1px solid #EEE;background-color:#ffffcc" readonly></textarea>
</div>

<?
include_once(G5_ADMIN_PATH . "/loan_request/inc_loan_request_alim.php");
?>

<iframe name="axFrame" id="axFrame" style="width:97%;margin-left:30px;border:1px solid #000; display:none"></iframe>

<footer id="ft">
	<p>
		Copyright &copy; <?=$_SERVER['HTTP_HOST']?>. All rights reserved.<br>
		<a href="#">상단으로</a>
	</p>
	<? if( in_array($member['mb_id'], array('admin_sori9th','admin_romrom')) ) {  ?>
		<div style='width:99.8%;float:left; text-align:center;'>RUN TIME : <? echo get_microtime()-$begin_time; ?><br></div>
	<? }  ?>
</footer>

<!-- <p>실행시간 : <? echo get_microtime() - $begin_time; ?> -->

<script src="<?=G5_ADMIN_URL?>/admin.js?ver=<?=time()?>"></script>
<script>
$(function(){
	$(".datepicker").datepicker({
		dateFormat      : 'yy-mm-dd',
		changeYear      : true,
		changeMonth     : true,
		monthNamesShort : ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin     : ['일' ,'월', '화', '수', '목', '금', '토']
	});
});

$(function(){
	var hide_menu = false;
	var mouse_event = false;
	var oldX = oldY = 0;

	$(document).mousemove(function(e) {
		if(oldX == 0) {
			oldX = e.pageX;
			oldY = e.pageY;
		}

		if(oldX != e.pageX || oldY != e.pageY) {
			mouse_event = true;
		}
	});

	// 주메뉴
	var $gnb = $(".gnb_1dli > a");
	$gnb.mouseover(function() {
		if(mouse_event) {
			$(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
			$(this).parent().addClass("gnb_1dli_over gnb_1dli_on");
			menu_rearrange($(this).parent());
			hide_menu = false;
		}
	});

	$gnb.mouseout(function() {
		hide_menu = true;
	});

	$(".gnb_2dli").mouseover(function() {
		hide_menu = false;
	});

	$(".gnb_2dli").mouseout(function() {
		hide_menu = true;
	});

	$gnb.focusin(function() {
		$(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
		$(this).parent().addClass("gnb_1dli_over gnb_1dli_on");
		menu_rearrange($(this).parent());
		hide_menu = false;
	});

	$gnb.focusout(function() {
		hide_menu = true;
	});

	$(".gnb_2da").focusin(function() {
		$(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
		var $gnb_li = $(this).closest(".gnb_1dli").addClass("gnb_1dli_over gnb_1dli_on");
		menu_rearrange($(this).closest(".gnb_1dli"));
		hide_menu = false;
	});

	$(".gnb_2da").focusout(function() {
		hide_menu = true;
	});

	$('#gnb_1dul>li').bind('mouseleave',function(){
		submenu_hide();
	});

	$(document).bind('click focusin',function(){
		if(hide_menu) {
			submenu_hide();
		}
	});

	// 폰트 리사이즈 쿠키있으면 실행
	var font_resize_act = get_cookie("ck_font_resize_act");
	if(font_resize_act != "") {
		font_resize("container", font_resize_act);
	}
});

function submenu_hide() {
	$(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
}

function menu_rearrange(el)
{
	var width = $("#gnb_1dul").width();
	var left = w1 = w2 = 0;
	var idx = $(".gnb_1dli").index(el);

	for(i=0; i<=idx; i++) {
		w1 = $(".gnb_1dli:eq("+i+")").outerWidth();
		w2 = $(".gnb_2dli > a:eq("+i+")").outerWidth(true);

		if((left + w2) > width) {
			el.removeClass("gnb_1dli_over").addClass("gnb_1dli_over2");
		}

		left += w1;
	}
}

function balance_check(idx) {
	$.ajax({
		url : "/adm/member/ajax_member_balance_check.php",
		type: "POST",
		data:{
			mb_no:idx
		},
		success:function(data){
			alert(data);
		},
		error: function () {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
		}
	});
}

function swapText(id, str) {
	if(str.trim()!='') {
		obj = '#'+id;
		$(obj).empty(str);
		$(obj).append(str);
	}
}

setInterval(function() { $(".blinkEle").toggle(); }, 500);
</script>

<?

include_once(G5_ADMIN_PATH.'/inc_auto_logout.php');

if($member['mb_level'] == '9') {
	include_once(G5_ADMIN_PATH."/inc_sub_admin_access_check.php");
}


include_once(G5_PATH.'/tail.sub.php');

if(OFFICE_CONNECT) {
	//ob_end_flush();
	//ob_end_clean();
}

?>