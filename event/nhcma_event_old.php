<?
include_once('./_common.php');

$g5['title'] = "NH투자증권 이벤트";

if($co['co_include_head']) {
	@include_once($co['co_include_head']);
}
else {
	include_once('./_head.php');
}

$event_start_date = "2019-10-14";

if( date('Y-m-d') < $event_start_date ) {
	$join_link = "javascript:alert('본 이벤트는「".date('Y년 m월 d일', strtotime($event_start_date)) . "」부터 시작합니다.\\n많은 참여 부탁드립니다.');";
	$join_link2 = $join_link;
}
else {
	$join_link = ($member['mb_no']) ? "javascript:alert('죄송합니다. 신규가입자 이벤트 입니다.');" : "/member/join_info.php?tab=p";
	$join_link2 = "/investment/invest_list.php";
}


if(G5_IS_MOBILE) {
	include_once("nhcma_event_m_old.php");
	return;
}

?>

<style>
#event {width:100%; margin:0; padding:0 }
#event .aa {width:1150px;text-align:center;}
</style>

<!-- 본문내용 START -->
<div id="content">

	<div class="location"><span></span><b class="blue"><?=$g5['title']?></b></div>
	<div class="content">

		<div id="event">
			<div class="aa"><img src="/evnt/nhCMA/NH_PC.jpg" usemap="#001" style="width:100%;"/></div>
		</div>

	</div>
</div>
<!-- 본문내용 E N D -->

<form>
<input typ="text" id="cma_acc_num" name="cma_acc_num"/><br/>
정보 제공 동의 <input type="checkbox" id="agree_yn" name="agree_yn"/><br/><br/>
<input type="button" onclick="go_apply();" value="이벤트 응모"/> 
</form>

<map name="001">
	<area shape="rect" coords="270,590,509,644"   onclick="go_nh();" onmouseover="document.body.style.cursor='pointer';" onmouseout="document.body.style.cursor='default';" />
	<area shape="rect" coords="217,4156,550,4205" onclick="go_nh();" onmouseover="document.body.style.cursor='pointer';" onmouseout="document.body.style.cursor='default';" />
</map>

 

<script>
function go_apply() {	
<?
if (!$member['mb_no']) {
	?>
	alert("로그인후 이용해 주세요.");
	return;
	<?
}
?>

	if (!$("#cma_acc_num").val()) {
		alert("cma 계좌번호를 입력해주세요.");
		return;
	}
	//var regex = /[0-9]+$/;
	var regex = /[^0-9]/;
	if (regex.test($("#cma_acc_num").val())) {
		alert("숫자만 입력가능합니다.");
		return;
	}

	if (!$("#agree_yn").is(":checked")) {
		alert("정보제공에 동의해 주세요.");
		return;
	}

	$.ajax({
		url: '/evnt/nhCMA/ajax_go_nhcma2.php',
		type: 'GET',
		data: {"cma_num":$("#cma_acc_num").val()},
		dataType: 'JSON',
		success: function(data) {
			console.log(data);
			if (data.res == "ok") {
				alert("이벤트 응모가 성공적으로 이뤄졌습니다.");
			} else if (data.res == "dup") {
				alert("이미 참여하신 이벤트 입니다.");
			}
		},
		error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
	});
}
function go_nh() {
<?
if ($member["mb_no"]) {
	?>
	window.open("https://m.mynamuh.com/nffAct/brg/hellofund","nhcma", "width=400,height=500, scrollbars=yes,resizable=yes");
	/*
	$.ajax({
		url: '/evnt/nhCMA/ajax_go_nhcma.php',
		type: 'GET',
		//data: {page:page, search_state:search_state},
		dataType: 'JSON',
		success: function(data) {
			console.log(data);
			if (data.res == "fail") {
				alert("로그인후 이용해 주세요.");
			} else {
				//self.location.href="https://m.mynamuh.com/nffAct/brg/hellofund";
				window.open("https://m.mynamuh.com/nffAct/brg/hellofund","nhcma", "width=400,height=500, scrollbars=yes,resizable=yes");
			}
		},
		error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
	});
	*/
	<?
} else {
	?>
	alert("로그인후 이용해 주세요.");
	<?
}
?>
}
</script>

<?
if($co['co_include_tail']){
	@include_once($co['co_include_tail']);
} else {
	include_once('./_tail.php');
}
?>