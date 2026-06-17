<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {width:100%;text-align:center;}
</style>

<div id="content">
	<div class="content">

		<div id="event">
			<div class="aa"><img src="/evnt/nhCMA/mobile/new_img01_mobile.png" style="width:95%;"></div><div class="aa" style="margin-top:6px;"><img src="/evnt/nhCMA/mobile/img_ok_btn1_m.png" onClick="go_nh();" style="cursor:pointer;width:50%;"></div>
			<div class="aa" style="border:0px solid red;"><img src="/evnt/nhCMA/mobile/new_img02_mobile.png" style="width:95%;"></div>
			<div class="aa" style="border:0px solid red;"><img src="/evnt/nhCMA/mobile/new_img03_mobile.png" style="width:95%;"></div>
			<div class="aa" style="border:0px solid red;"><img src="/evnt/nhCMA/mobile/new_img04_mobile.png" style="width:95%;"></div>
			<div style="background-color:#5f88c9;width:95%;margin:0 auto;text-align:center;" >
				<img src="/evnt/nhCMA/mobile/img05_1_m__.png" style="margin:10px auto;width:65%;" />
				<div style="margin-left:8%;border:0px solid blue;width:80%;">
					<input type="number" id="cma_acc_num" name="cma_acc_num" value="<?=$jn_row['cma_num']?>" onKeyUp="onlyDigit(this);" style="height:40px;width:100%;border:4px solid red;text-align:center;font-size:15px;font-family:spoqahansans;box-shadow: 0px 3px 0px 0px #333;" placeholder="개설한 CMA 계좌번호를 입력해 주세요.">
					<div style="margin-top:10px;margin-left:0px;padding-bottom:10px;">
						<textarea readonly style="width:100%;height:100px;resize:none;line-height:20px; font-family:spoqahansans;">[개인정보수집 이용동의]

① 개인정보를 제공받는 자 : NH투자증권
② 개인정보를 제공받는 자의 개인정보 이용 목적 : 당사를 통한 CMA계좌 개설 식별 여부 확인
③ 제공하는 개인정보의 항목 : 비대면 CMA 계좌번호,이름, 핸드폰번호
④ 개인정보를 제공받는 자의 개인정보 보유 및 이용 기간 : 계좌개설확인 후 즉시폐기
⑤ 예치금은 헬로펀딩 가상계좌로 지급되며, 가상계좌 미개설시 예치금 지급이 되지 않습니다.
⑥ 동의를 거부할 수 있으며, 동의 거부시 헬로펀딩 예치금이 지급되지 않습니다.
						</textarea>
					</div>
					<div style="width:100%;pading-bottom:20px;text-align:center;"><input type="checkbox" id="agree_yn" name="agree_yn"><label for="agree_yn" style="font-size:1.2em;"> [필수] 개인정보수집 이용동의</label></div>
					<div style="text-align:center;width:100%;padding:10px 0 15px;">
						<? if($jn_row['idx']) { ?>
						<img src="/evnt/nhCMA/mobile/img_modify_btn_m_.png" onClick="go_mod();" style="cursor:pointer;width:50%;">
						<? } else { ?>
						<img src="/evnt/nhCMA/mobile/img_ok_btn_m_.png" onClick="go_apply();" style="cursor:pointer;width:50%;">
						<? } ?>
					</div>
				</div>
			</div>
			<div class="aa" style="border:0px solid red;"><img src="/evnt/nhCMA/mobile/new_img05_mobile.png" style="width:95%;"></div>
			<div class="aa" style="border:0px solid red;">
				<div style="background-color:#ffc300;width:95%;margin:0 auto;">
					<a onclick="go_nh();" style="cursor:pointer;"><img src="/evnt/nhCMA/mobile/img_ok_btn2_m.png" style="width:50%;"></a>
				</div>
			</div>
			<div class="aa" style="border:0px solid red;"><img src="/evnt/nhCMA/mobile/new_img06_mobile.png" style="width:95%;"></div>
		</div>

	</div>
</div>

<script type="text/javascript">
go_mod = function() {
<? if(!$member['mb_no']) { ?>
	alert("로그인후 이용해 주세요.");
	location.href="/bbs/login.php?url=<?=urlencode('/event/nhcma_event.php');?>";
	return;
<? } else { ?>
<?	if( substr($jn_row["insert_datetime"],0,7) == date("Y-m") ) { ?>
	var cma_acc_num_val = $('#cma_acc_num').val();
	if(cma_acc_num_val) cma_acc_num_val = $.trim(cma_acc_num_val);

	if(cma_acc_num_val=='') {
		alert("CMA 계좌번호를 입력해주세요."); $('#cma_acc_num').focus();
		return;
	}

	var regex = /[^0-9]/;
	if(regex.test(cma_acc_num_val)) {
		alert("숫자만 입력가능합니다."); $('#cma_acc_num').focus();
		return;
	}

	if(cma_acc_num_val.length != 11) {
		alert("계좌번호를 확인해 주세요."); $('#cma_acc_num').focus();
		return;
	}

	if(!$("#agree_yn").is(":checked")) {
		alert("정보제공에 동의해 주세요.");
		return;
	}

	$.ajax({
		url: '/evnt/nhCMA/ajax_go_nhcma2_modi.php',
		type: 'GET',
		data: {"cma_num":cma_acc_num_val},
		dataType: 'JSON',
		success: function(data) {
			console.log(data);
			if (data.res == "ok") {
				alert("이벤트 응모가 성공적으로 이뤄졌습니다.");
				location.href="/investment/invest_list.php";
			}
			else if (data.res == "old") {
				alert("변경가능 기간이 아닙니다.");
				return;
			}
		},
		error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
	});
<?	} else { ?>
	alert("변경 가능한 기간이 아닙니다.");
	return;
<?	} ?>
<? } ?>
}

go_apply = function() {
<? if(!$member['mb_no']) { ?>
	alert("로그인후 이용해 주세요.");
	location.href="/bbs/login.php?url=<?=urlencode('/event/nhcma_event.php');?>";
	return;
<? } else { ?>
	var cma_acc_num_val = $('#cma_acc_num').val();
	if(cma_acc_num_val) cma_acc_num_val = $.trim(cma_acc_num_val);

	if(cma_acc_num_val=='') {
		alert("CMA 계좌번호를 입력해주세요."); $('#cma_acc_num').focus();
		return;
	}

	var regex = /[^0-9]/;
	if(regex.test(cma_acc_num_val)) {
		alert("숫자만 입력가능합니다."); $('#cma_acc_num').focus();
		return;
	}

	if(cma_acc_num_val.length != 11) {
		alert("계좌번호를 확인해 주세요."); $('#cma_acc_num').focus();
		return;
	}

	if(!$("#agree_yn").is(":checked")) {
		alert("정보제공에 동의해 주세요.");
		return;
	}

	$.ajax({
		url: '/evnt/nhCMA/ajax_go_nhcma2.php',
		type: 'GET',
		data: {"cma_num" : cma_acc_num_val},
		dataType: 'JSON',
		success: function(data) {
			console.log(data);
			if (data.res == "ok") {
				alert("이벤트 응모가 성공적으로 이뤄졌습니다.");
				location.href="/investment/invest_list.php";
			}
			else if (data.res == "dup") {
				alert("이미 참여하신 이벤트 입니다.");
			}
		},
		error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
	});
<? } ?>
}

function go_nh() {
	window.open("https://m.mynamuh.com/nffAct/brg/hellofund","nhcma", "width=400,height=500, scrollbars=yes,resizable=yes");
}
</script>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>
