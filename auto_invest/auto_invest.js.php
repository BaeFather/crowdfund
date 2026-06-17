<script>
function chk_def() {
	for (var i=0 ; i<<?php ECHO count($get_conf)?> ; i++) {
		$('input:checkbox[name="chk_item['+i+']"]').prop("checked",true);
		$("#auto_money\\["+i+"\\]").val("1");
		$("#auto_money2\\["+i+"\\]").val("100");

		$("#auto_moneyOr\\["+i+"\\]").val("1");
		$("#auto_moneyOr2\\["+i+"\\]").val("100");
	}
	chk_total_checkbox();
}

function get_auto_inv_sess() {
	<?php
	if (!$_SESSION['ss_auto_inv']) {
		echo "return;";
	} else {
		for ($i=0 ; $i< count($_SESSION['ss_auto_inv']) ; $i++) {
			?>
			if ('<?php ECHO $_SESSION["ss_auto_inv"][$i]["inv_yn"]?>'=='Y') {
				$('input:checkbox[name="chk_item['+<?php ECHO $i?>+']"]').prop("checked",true);
				$("#auto_money\\[<?=$i?>\\]").val("<?php ECHO $_SESSION['ss_auto_inv'][$i]['auto_money']?>");
			} else {
				$('input:checkbox[name="chk_item['+<?php ECHO $i?>+']"]').prop("checked",false);
			}
			<?php
		}
	}
	?>
	chk_total_checkbox();
}

var this_idx = "";


function chk_tab3() {

	var selected = chk_total_checkbox();
	if (selected) {
		//move_tab('3');
	} else {
		alert("1개이상 선택해야 합니다.");
		return;
	}

	var intNumkind = false;
	var t_numOr = 0;
	var t_numOr2 = 0;
	var t_num = 0;
	var t_num2 = 0;
	for (var i=0 ; i<<?php ECHO count($get_conf)?> ; i++) {
		if ($('input:checkbox[name="chk_item['+i+']"]').is(":checked")) {
			t_numOr = $('input[name="auto_moneyOr['+i+']"]').val();
			t_numOr2 = $('input[name="auto_moneyOr2['+i+']"]').val();

			t_num = $('input[name="auto_money['+i+']"]').val();
			t_num2 = $('input[name="auto_money2['+i+']"]').val();

			if(parseInt(t_num) > parseInt(t_num2))
			{
				alert("시작금액은 종료금액보다 클수 없습니다.");
				if(t_numOr && t_numOr2)	//기존 값이 있다면
				{
					if(parseInt(t_numOr2) > parseInt(t_num2)) {
						$('input[name="auto_money2['+i+']"]').val(t_numOr2);
					} else {
						$('input[name="auto_money['+i+']"]').val(t_numOr);
					}
				} else {
					$('input[name="auto_money['+i+']"]').val("1");
					$('input[name="auto_money2['+i+']"]').val("100");
				}
				intNumkind = true;
				break;
			}

		}
	}

	if(intNumkind == true)
	{
		return;
	}

	sess_set();

	var chk_yn = false;
<?php
	for ($i=0 ; $i<count($get_conf) ; $i++) {
?>
	var chk_yn = chk_auto_money('<?php ECHO $i?>');
	if (!chk_yn) {
		return false;
	}
<?php
	}
?>
	move_tab('4');
}

function move_tab(idx,nosess) {

	if (this_idx==idx) return;
	for (var i=1 ; i<=4 ; i++) {
		if($(".tab0"+i).hasClass('on'))  $(".tab0"+i).removeClass('on');
		$("#auto_tab"+i).hide();
	}

	if (idx==4) {

		if (nosess!="Y") {
			sess_set();
		} else {
		}

		var limit_check = check_auto_money();

		if (!limit_check) {
			this_idx = 4;
			move_tab(2);
			return;
		}
		disp_all();

	}

	$(".tab0"+idx).addClass('on');
	$("#auto_tab"+idx).show();
	this_idx = idx;

	$(".modal-content").scrollTop(0);
}

function get_auto_inv_conf() {

	$.ajax({
		url  : "/deposit/ajax_auto_invest_conf_get.php",
		type : "POST",
		dataType : "json",
		success : function(data) {

			if (data.err_msg) {
				var yn = confirm("로그인후 이용가능합니다.\n로그인 페이지로 이동하시겠습니까?");
				if (yn)	self.location.href="/bbs/login.php?url=/auto_invest/";
				return false;
			}

			if (!data.auto_conf.length) return false;

			var max_amount = 0;

			for (var j =0 ; j<<?php ECHO count($get_conf)?>; j++) {
				$('input:checkbox[name="chk_item['+j+']"]').prop("checked",false);
				$("#auto_money\\["+j+"\\]").attr("disabled",true);
				$("#auto_money2\\["+j+"\\]").attr("disabled",true);
			}

			<?php
			for ($j=0 ; $j<count($get_conf) ; $j++) {
				?>
			for (var i=0 ; i<data.auto_conf.length ; i++) {
				if ("<?php ECHO $get_conf[$j][idx]?>"==data.auto_conf[i].ai_grp_idx) {
					$('input:checkbox[name="chk_item['+<?php ECHO $j?>+']"]').prop("checked",true);
					$("#auto_money\\[<?php ECHO $j?>\\]").val(data.auto_conf[i].setup_amount/10000);
					$("#auto_money2\\[<?php ECHO $j?>\\]").val(data.auto_conf[i].setup_amount2/10000);

					$("#auto_moneyOr\\[<?php ECHO $j?>\\]").val(data.auto_conf[i].setup_amount/10000);
					$("#auto_moneyOr2\\[<?php ECHO $j?>\\]").val(data.auto_conf[i].setup_amount2/10000);
				}
			}
				<?php
			}
			?>

			for (var i=0 ; i<data.auto_conf.length ; i++) {

				if (max_amount<data.auto_conf[i].setup_amount) max_amount = data.auto_conf[i].setup_amount;

				if (data.auto_conf[i].ai_grp_idx=="8") {
					//$('input:checkbox[name="chk_item['+j+']"]').prop("checked",true);
				}

			}

			chk_total_checkbox();
			return true;
		},
		error : function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR+"\n"+textStatus+"\n"+errorThrown);
		}
	});
}

function open_auto_config_pop(flag) {
<?php
if ($member["mb_no"]) {
	?>
	var set_dt = get_auto_inv_conf();

	if (<?php ECHO $user_cnt?> >0) {
<?php
		if ($_SESSION['ss_auto_amt']) {
			?>
			alert("설정된 자동투자 내역이 있습니다.\n\n설정 내용을 확인해 주세요.");
			self.location.href="<?php ECHO G5_URL?>/deposit/deposit.php?tab=5";
			return;
<?php
		}
?>
	} else  {
<?php
		if ($_SESSION['ss_auto_amt']) {
?>
			get_auto_inv_sess();
<?php
		} else {
?>
			chk_def();	// 시작
<?php
		}
?>
	}
<?php
} else {
?>
<?php
	if ($_SESSION['ss_auto_amt']) {
		?>
		get_auto_inv_sess();
		<?php
	} else {
		?>
		chk_def();
<?php
	}
}

?>

open_pop();

}

function chk_total_checkbox() {
	var yy = 0;
	var nn = 0;
	for (var j =0 ; j<<?php ECHO count($get_conf)?>; j++) {
		if ($('input:checkbox[name="chk_item['+j+']"]').is(":checked")) {
			yy = yy+1;
			$("#auto_money\\["+j+"\\]").attr("disabled",false);
			$("#auto_money2\\["+j+"\\]").attr("disabled",false);
		} else {
			nn = nn+1;
			$("#auto_money\\["+j+"\\]").attr("disabled",true);
			$("#auto_money2\\["+j+"\\]").attr("disabled",true);
		}
	}
	if (yy==<?php ECHO count($get_conf)?>) $('input:checkbox[name="chk_all"]').prop("checked",true);
	else $('input:checkbox[name="chk_all"]').prop("checked",false);
	return yy;
}

function chk_auto_money(idx) {

	if ($("#auto_money\\["+idx+"\\]").is(':disabled')) {
		return true;
	}

	var regex = /[0-9]|\./;
	var tg_num = $("#auto_money\\["+idx+"\\]").val();
	var tg_num2 = $("#auto_money2\\["+idx+"\\]").val();

	if (!tg_num || !tg_num2) {
		alert("자동 투자 금액을 입력해 주세요.");
		return false;
	}

	if( !regex.test(tg_num) || !regex.test(tg_num2)) {
		alert("숫자만 입력가능합니다.");
		return false;
	}
	if (tg_num < 1 || tg_num2 < 1) {
		alert("1만원 이상만 설정 가능합니다.");
		return false;
	}

	if (tg_num*10000 > <?php ECHO $max_auto_amt?> || tg_num2*10000 > <?php ECHO $max_auto_amt?>) {
	<?php if ($member['mb_id']) { ?>
		$("#msg_money").html("회원님의 자동투자 가능금액은 <?php ECHO number_format($max_auto_amt/10000)?>만원이며,<br/>투자자유형 변경을 통해 투자한도를 늘릴 수 있습니다.<br/>[<a href='/mypage/mypage.php'><font style='color:blue;'>투자자 유형 변경하기</font></a>]");
	<?php } else { ?>
		//$("#msg_money").html("소득적격, 전문투자자, 법인 회원만 500만원 초과 입력 가능합니다.)<br/><a href='<?php ECHO G5_BBS_URL?>/login.php?url=<?php ECHO urlencode($_SERVER[PHP_SELF])?>'><font style='color:blue;text-decoration:underline;'>로그인</font></a> 후 이용해 주세요.<br/><br/>");
		$("#msg_money").html("개인회원은 500만원까지 설정가능합니다.<br/>(소득적격 투자자는 2,000만원 내, 전문/법인 투자자는 제한없이 설정 가능합니다.)<br/><a href='<?php ECHO G5_BBS_URL?>/login.php?url=<?php ECHO urlencode($_SERVER[PHP_SELF])?>'><font style='color:blue;text-decoration:underline;'>로그인</font></a> 후 이용해 주세요.<br/>");
	<?php } ?>
		return false;
	}
	return true;

}
function chk_all_btn() {
	var chkbx;
	if ($('#chk_all').is(":checked")) {
		chkbx = true;
	} else {
		chkbx = false;
	}

	for (var i=0 ; i<<?php ECHO count($get_conf)?> ; i++) {
		$('input:checkbox[name="chk_item['+i+']"]').prop("checked",chkbx);

		if (chkbx) {
			$("#auto_money\\["+i+"\\]").attr("disabled",false);
			$("#auto_money2\\["+i+"\\]").attr("disabled",false);
		} else {
			//$("#auto_money\\["+i+"\\]").val("");
			$("#auto_money\\["+i+"\\]").attr("disabled",true);
			$("#auto_money2\\["+i+"\\]").attr("disabled",true);
		}
	}
}

function check_auto_money() {
	var intMaxAutoAmt = "<?php ECHO $max_auto_amt?>";
	for (var i=0 ; i<<?php ECHO count($get_conf)?> ; i++) {
		if ($('input:checkbox[name="chk_item['+i+']"]').is(":checked")) {
			var tg_num = $("#auto_money\\["+i+"\\]").val() * 10000;
			var tg_num2 = $("#auto_money2\\["+i+"\\]").val() * 10000;

			if (tg_num > parseInt(intMaxAutoAmt) || tg_num2 > parseInt(intMaxAutoAmt)) {
				return false;
			}
		}
	}
	return true;
}

function go_auto_conf_save() {

	var limit_check = check_auto_money();

	if (!limit_check) {
		alert("자동투자 설정금액은 투자자 유형별 최대 투자금액을\n초과할 수 없습니다.\n\n회원님의 최대 투자가능 금액은 <?php ECHO number_format($max_auto_amt)?>만원 입니다.");
		return ;
	}

	<?php
	if(!$member["mb_id"]) {
		?>
		alert("로그인 후 이용 가능합니다.");
		self.location.href = "<?php ECHO G5_BBS_URL?>"+"/login.php?url=" + "<?php ECHO urlencode($_SERVER[PHP_SELF])?>";
		return;
		<?php
	}
	?>

	invest_warning_agree_open();

}

function go_auto_conf_save2() {

	send_data = $("#f_auto_inv").serialize();
	$.ajax({
		url      : "/auto_invest/ajax_auto_invest_conf_set_save.php",
		type     : "POST",
		dataType : "json",
		data     : send_data,
		success : function(data) {
			console.log(data);
			if (data.err_msg) { alert(data.err_msg); return; }
			if (data.cnt_modi) {
				alert("설정이 저장되었습니다.");
				//call_reset();
				self.location.reload();
			} else alert("변경된 내용이 없습니다.");

		},
		error : function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
			//alert(jqXHR+"\n"+textStatus+"\n"+errorThrown);
			alert(textStatus+"\n"+errorThrown);
		}
	});
}

function sess_set() {

<?php if ($member['mb_no']) echo "return;"; ?>

	send_data = $("#f_auto_inv").serialize();

	$.ajax({
		url      : "/auto_invest/ajax_sess_set.php",
		type     : "POST",
		dataType : "json",
		data     : send_data,
		success : function(data) {
		},
		error : function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
			//alert(jqXHR+"\n"+textStatus+"\n"+errorThrown);
			alert(textStatus+"\n"+errorThrown);
		}
	});
}

function disp_all() {

<?php if (G5_IS_MOBILE) { ?>


	var html_txt = "<div style='margin:auto 0px;'>";
	var selected_num = 1;
<?php
for ($i=0 ; $i<count($get_conf) ; $i++) {
	if (strpos($get_conf[$i]['grp_title'],'br')) $margin14 = "margin-top:-14px;";
	else $margin14 = "";
?>
	if ($('input[name="chk_item[<?php ECHO $i?>]"]').is(":checked")) {
		html_txt += "<p>"+selected_num+". <?php ECHO $get_conf[$i][grp_title]?> ";
		html_txt += "<span style='float:right; color: #1277f4;<?php ECHO $margin14?>'>";
		//html_txt += "<img src='/auto_invest/img/won_icon.png' style='width: 16px; height: 16px; margin-top: 4px; margin-right: 4px'>";
		html_txt += number_format($('input[name="auto_money[<?php ECHO $i?>]"]').val())+ " 만원";
		html_txt += "~";
		html_txt += number_format($('input[name="auto_money2[<?php ECHO $i?>]"]').val())+ " 만원</span>";
		html_txt += "</p>";
		selected_num += 1;
	}
<?php
}
?>
	$("#disp_selected").html(html_txt);

<?php } else { ?>


	var html_txt = "<div style='margin:auto 0px;'>";
	var selected_num = 1;
<?php
	for ($i=0 ; $i<count($get_conf) ; $i++) {
		if (strpos($get_conf[$i]['grp_title'],'br')) $margin18 = "margin-top:-18px;";
		else $margin18 = "";
?>
		if ($('input[name="chk_item[<?php ECHO $i?>]"]').is(":checked")) {
			html_txt += "<p>"+selected_num+". <?php ECHO $get_conf[$i][grp_title]?> ";
			html_txt += "<span style='float:right; color: #1277f4;<?php ECHO $margin18?>'>";
			//html_txt += "<img src='/auto_invest/img/won_icon.png' style='width: 20px; height: 20px; margin-top: 5px; margin-right: 6px'>";
			html_txt += number_format($('input[name="auto_money[<?php ECHO $i?>]"]').val())+ " 만원";
			html_txt += "~";
			html_txt += number_format($('input[name="auto_money2[<?php ECHO $i?>]"]').val())+ " 만원</span>";
			html_txt += "</p>";
			selected_num += 1;
		}
<?php
	}
?>
	$("#disp_selected").html(html_txt+"</div>");

<?php } ?>

}

function open_pop(flag) {
	$('#myModal').show();
	$('html').css('overflow','hidden');
}

//팝업 Close 기능
function close_pop(flag) {
	 $('#myModal').hide();
	 <?php if ($member['mb_no']) { ?>
	 auto_invest_config();
	 move_tab(2);
	 <?php } ?>
}

function call_reset() {
	close_pop();
	move_tab(2);
}

function chk_num(ind) {
	var t_num = $('input[name="auto_money['+ind+']"]').val();
	var t_num2 = $('input[name="auto_money2['+ind+']"]').val();

	var chk_res = onlyNumb(t_num);
	if (!chk_res) {
		var fix_num = t_num.replace(/[^0-9]/gi,'');
		$('input[name="auto_money['+ind+']"]').val(fix_num);
		alert("숫자만 입력가능합니다.");
	}
//	$('input[name="auto_money['+ind+']"]').val(t_num*1);
}

function chk_num2(ind) {
	var t_num = $('input[name="auto_money['+ind+']"]').val();
	var t_num2 = $('input[name="auto_money2['+ind+']"]').val();

	var chk_res = onlyNumb(t_num2);
	if (!chk_res) {
		var fix_num = t_num2.replace(/[^0-9]/gi,'');
		$('input[name="auto_money2['+ind+']"]').val(fix_num);
		alert("숫자만 입력가능합니다.");
	}

//	$('input[name="auto_money2['+ind+']"]').val(t_num*1);
}

function onlyNumb(x) {

	var anum=/^[0-9]*$/;
	var ch_res = false;

	if (anum.test(x)) {
		ch_res = true;
	} else {
		ch_res = false;
	}
	return ch_res;
}

function invest_warning_agree_open() {
	$.blockUI({
		message: $('#invest_warning_agree'),
		<?php if(G5_IS_MOBILE) { ?>
		css: { top:'1%',left:'1%', width:'98%', border:0, cursor:'default' }
		<?php } else { ?>
		css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
		<?php } ?>
	});
}

function strCheck(arg) {

	if(arg=='동의함') {
		var agree_val = '1';
		var class_val = 'btn_big_blue2';
		$('#invest_warning_agree_btn').focus();
	}
	else {
		var agree_val = '';
		var class_val = 'btn_big_blue2 off';
	}
	$('#_agree').val(agree_val);
	$('#invest_warning_agree_btn').attr('class', class_val);
}

function agree_check() {
	if( $('#_agree').val()!='1' ) {
		alert('빈칸에 "동의함"을 정확히  입력해 주셔야 자동투자 설정이 가능합니다.');
	}
	else {

		$.unblockUI();
		$('#str').val("");
		$('#yn_agree').val($('#_agree').val());
		$('#_agree').val("");
		go_auto_conf_save2();
	}
}

$('#invest_warning_agree_btn').click(function() {
	if( $('#_agree').val()!='1' ) {
		alert('빈칸에 "동의함"을 정확히  입력해 주셔야 자동투자 설정이 가능합니다.');
	}
	else {

		$.unblockUI();
		$('#str').val("");
		$('#yn_agree').val($('#_agree').val());
		$('#_agree').val("");
		go_auto_conf_save2();
	}
});
</script>