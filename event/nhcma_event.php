<?
include_once('./_common.php');

//while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }
//while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = preg_replace("/(\'|\"|\#|\=|\(|\)|\+|\%|\*)/iu", "$1;", $v); }
while( list($k, $v) = each($_REQUEST) ) {
	if(!is_array($k) ) {
		${$k} = addslashes(clean_xss_tags(trim($v)));
		${$k} = preg_replace("/(\'|\"|\#|\=|\(|\)|\+|\%|\*)/iu", "$1;", ${$k});
	}
}


$g5['title'] = "NH투자증권 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}


if($member['mb_no']) {
	$jn_sql = "SELECT * FROM cf_event_nhCMA WHERE mb_no='".$member['mb_no']."' ORDER BY idx DESC LIMIT 1";
	$jn_row = sql_fetch($jn_sql);
}

if(G5_IS_MOBILE) {
	include_once("nhcma_event_m.php");
	return;
}

?>

<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {width:1150px;text-align:center;}
</style>

<div id="content">
	<div class="content">

		<div id="event">
			<div class="aa"><img src="/evnt/nhCMA/web/new_img01.png"></div>
			<div class="aa"><img src="/evnt/nhCMA/web/new_img02.png"></div>
			<div class="aa"><img src="/evnt/nhCMA/web/new_img03.png"></div>
			<div class="aa"><img src="/evnt/nhCMA/web/new_img04.png"></div>
			<div style="background-color:#5f88c9;height:362px;text-align:center;">
				<img src="/evnt/nhCMA/web/img05_1_web_.png" style="margin:0 auto;margin-top:20px;" />
				<div style="padding-top:13px;border:0px solid blue;width:600px;margin:0 auto;">
					<input type="text" id="cma_acc_num" name="cma_acc_num" value="<?=$jn_row['cma_num']?>" onKeyUp="onlyDigit(this);" style="width:590px;height:50px;border:5px solid red;text-align:center;font-size:large; box-shadow: 2px 3px 0px 0px #333;" placeholder="개설한 CMA 계좌번호를 입력해 주세요.">
					<div style="margin-top:15px;">
						<textarea readonly style="width:590px;height:80px;resize:none;">[개인정보수집 이용동의]

① 개인정보를 제공받는 자 : NH투자증권
② 개인정보를 제공받는 자의 개인정보 이용 목적 : 당사를 통한 CMA계좌 개설 식별 여부 확인
③ 제공하는 개인정보의 항목 : 비대면 CMA 계좌번호,이름, 핸드폰번호
④ 개인정보를 제공받는 자의 개인정보 보유 및 이용 기간 : 계좌개설확인 후 즉시폐기
⑤ 예치금은 헬로펀딩 가상계좌로 지급되며, 가상계좌 미개설시 예치금 지급이 되지 않습니다.
⑥ 동의를 거부할 수 있으며, 동의 거부시 헬로펀딩 예치금이 지급되지 않습니다.
						</textarea>
					</div>
					<div style="width:210px;margin:5px auto;"><input type="checkbox" id="agree_yn" name="agree_yn"> <label for="agree_yn">[필수] 개인정보수집 이용동의</label></div>
					<div style="margin:15px auto 0;width:259px;">
						<? if($jn_row['idx']) { ?>
						<img src="/evnt/nhCMA/web/img_modify_btn_web_.png" onClick="go_mod();" style="cursor:pointer">
						<? } else { ?>
						<img src="/evnt/nhCMA/web/img_ok_btn_web_.png" onClick="go_apply();" style="cursor:pointer">
						<? } ?>
					</div>
				</div>
			</div>
			<div class="aa"><img src="/evnt/nhCMA/web/new_img05.png"></div>
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
<?	if( substr($jn_row["insert_datetime"],0,7) == date('Y-m') ) { ?>
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

//function go_nh() {
//	window.open("https://m.mynamuh.com/nffAct/brg/hellofund","nhcma", "width=400,height=500, scrollbars=yes,resizable=yes");
//}
</script>

<?
if($co['co_include_tail']) {
	@include_once($co['co_include_tail']);
}
else {
	include_once('./_tail.php');
}
?>