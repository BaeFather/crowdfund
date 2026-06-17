<?
$sub_menu = '800320';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu800'][4][1];
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

/* 검색 필드 조합 START */

// GET 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}


$sql_search = " 1=1 ";
$sql_search.= " AND receive_agreement='Y'";
if($start_date && $end_date) $sql_search.= " AND LEFT(last_agreement_date, 10) BETWEEN '$start_date' AND '$end_date'";

if(!$sh_term || $sh_term == '') { $sh_term = 'all'; }
if($sh_term && $sh_term != 'all' && $sh_term != '') {
	switch($sh_term) {
		case '30'  : $target_date = date("Y-m-d",strtotime("-30 day", time()));    break;
		case '60'  : $target_date = date("Y-m-d",strtotime("-60 day", time()));    break;
		case '90'  : $target_date = date("Y-m-d",strtotime("-90 day", time()));    break;
		case '180' : $target_date = date("Y-m-d",strtotime("-6 month", time()));   break;
		case '365' : $target_date = date("Y-m-d",strtotime("-12 month", time()));  break;
	}
	$sql_search.= " AND LEFT(last_agreement_date, 10) >= '$target_date'";
}

$sql = "
	SELECT
		*
	 FROM
		sms_request_phone
	WHERE
		$sql_search
	GROUP BY
		phone_no
	ORDER BY
		last_agreement_date DESC";
$res = sql_query($sql);
$total_count = sql_num_rows($res);

for($i=0; $i<$total_count; $i++) {
	$LIST[] =	sql_fetch_array($res);
}
sql_free_result($res);


$ad_member = get_member('admin');

?>

<style>
.sms_info {	margin:10px auto;	padding:15px; }
.sms_info div {	margin-bottom:25px; }
.sms_area {	width:230px;	display:inline-block;	margin:10px auto;	padding:15px; }
.sms_msg textarea {	border:1px solid #EEEEEE;	width:200px;	font-size:12px;	height:250px; }
.sms_title { text-align:center;	font-weight:bold;	color:#FFFFFF;	padding:10px;	background-color:#3C5B9B;	border-radius:3px 3px 0 0; }
.sms_use { padding-top:10px;	text-align:center; }
.sms_error { color:red; }
</style>

<div class="tbl_frm01 tbl_wrap tbl_head02">

	<form name="fmember" id="fmember" action="/adm/sms_all_send2.php" method="post" enctype="multipart/form-data">

		<input type="hidden" name="sh_term" id="sh_term" value="">

		<!-- 검색영역 START -->
		<div style="margin-bottom:5px;">
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='all')?'active':''?>" onclick="term_search('all');" style="margin-right:2px;">전체</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='30')?'active':''?>" onclick="term_search('30');" style="margin-right:2px;">30일간</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='60')?'active':''?>" onclick="term_search('60');" style="margin-right:2px;">60일간</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='90')?'active':''?>" onclick="term_search('90');" style="margin-right:2px;">90일간</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='180')?'active':''?>" onclick="term_search('180');" style="margin-right:2px;">6개월</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='365')?'active':''?>" onclick="term_search('365');" style="margin-right:2px;">12개월</button>
		</div>
		<div>
			<? if($sh_term && $sh_term == 'all') { ?>
			<span style="margin-right:15px;">
				등록일
				<input type="text" class="frm_input datepicker"  name="start_date" size="10" value="<?=$start_date;?>"> ~
				<input type="text" class="frm_input datepicker" name="end_date" size="10" value="<?=$end_date;?>">
			</span>
			<? } ?>

      <!--
			<span style="margin-right:15px;">
				<select name="key_search">
					<option value="">필드선택</option>
					<option value="phone_no" <? if($key_search == 'phone_no'){echo 'selected';} ?>>휴대폰</option>
				</select>
			</span>
			//-->
			<span style="margin-right:15px;">
				<!--<input type="text" class="frm_input" name="keyword" size="30" value="<?=$keyword;?>" disabled>-->
				<input type="submit" class="btn btn-sm btn-warning" value="검색">
			</span>
		</div>
		<!-- 검색영역 E N D -->
	</form>

	<form name="fsms" id="fsms" action="/adm/sms_all_send2_proc.php" method="post" enctype="multipart/form-data">
		<input type="hidden" id="mode" name="mode">
		<!-- SMS 영역 START -->
		<table>
			<caption><?=$g5['title']; ?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th scope="row" style="text-align:center;"><label for="regist_number">SMS 문자전송</label></th>
					<td>
						<div class="sms_area pull-left">
							<div class="sms_title">SMS 문자전송</div>
							<div class="sms_msg"><textarea rows="20" name="sms_msg" id="sms_msg" placeholder="메세지 내용을 입력해주세요" onKeyUp="bytePrint();"></textarea></div>
							<div style="margin-top:4px; text-align:right;"><input type="text" id="sms_msg_length" class="frm_input" value="0" style="width:60px;text-align:right"> byte</div>
							<span class="sms_error" id="msg_err"></span>
						</div>
						<div class="sms_info">
							<div>발신번호 : <input type="text" class="frm_input" name="from_hp" id="from_hp" size="20" value="1588-6760"> <span class="sms_error" id="from_hp_err"></span></div>

							<div>발송시간 :
								<select name="send_time" id="send_time" onchange="check_sms_time(this.value);">
									<option value="d">즉시발송</option>
									<option value="r" selected>예약발송</option>
								</select>
								<span id="send_t_area" style="display:none;">
									<input type="text" class="frm_input datepicker" name="send_ymd" id="send_ymd" size="10" value="" placeholder="날짜선택">
									<select id="send_h" name="send_h">
									<?
									for($i=0; $i<=23; $i++) {
										if(strlen($i) == 1) {
											$j = '0'.$i;
										}else {
											$j = $i;
										}
										echo '<option value='.$j.'>'.$j.'시</option>';
									}
									?>
									</select>
									<select id="send_i" name="send_i">
									<?
									for($i=0; $i<=59; $i++) {
										if(strlen($i) == 1) {
											$j = '0'.$i;
										}else {
											$j = $i;
										}
										echo '<option value='.$j.'>'.$j.'분</option>';
									}
									?>
									</select>
									<span class="sms_error" id="send_time_err"></span>
								</span>
							</div>
							<div><button type="button" id="btn_submit" onClick="sms_send(document.fsms);"  class="btn btn-lg btn-primary" style="width:190px">SMS 발송</button></div>
							<div id="sms_result" class="sms_error"></div>
						</div>

<script>
$(document).ready(function() {
	check_sms_time('r');
});
</script>

					</td>
				</tr>
				<tr>
					<th scope="row" style="text-align:center;"><label for="regist_number">SMS 발송회원</label></th>
					<td>

						<span id="btn_delete" class="btn btn-danger" style="margin-bottom:8px; float:right">선택삭제</span>

						<div style="width:100%;height:300px;padding:0; border:1px solid #ccc; overflow-y:scroll;">

							<table class="table_hover" style="width:98%;margin:1%;">
								<caption><?=$g5['title']?> 목록</caption>
								<thead>
								<tr>
									<th scope="col" style="text-align:center;">
										<label><input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)"></label>
									</th>
									<th scope="col" style="text-align:center;">번호</th>
									<th scope="col" style="text-align:center;">핸드폰</th>
									<th scope="col" style="text-align:center;">성명</th>
									<th scope="col" style="text-align:center;">아이디</th>
									<th scope="col" style="text-align:center;">SMS허용</th>
									<th scope="col" style="text-align:center;">등록일</th>
								</tr>
								</thead>
								<tbody>
<?
if($total_count) {
	$num = $total_count;
	for($i=0,$j=$num; $i<$total_count; $i++,$j--) {

		$mb_sms_txt = ($LIST[$i]['receive_agreement']=='Y') ? "수신동의" : "";

		echo "
								<tr align=\"center\" style=\"font-size:12px;\"><!--onClick=\"on_checkbox('$i')\"-->
									<td headers=\"mb_list_chk\" class=\"td_chk\"><input type=\"checkbox\" id=\"checkbox_{$i}\" name=\"chk[]\" value=\"".$LIST[$i]['phone_no']."\"></td>
									<td>".number_format($j)."</td>
									<td>".$LIST[$i]['phone_no']."</td>
									<td>비회원</td>
									<td>비회원</td>
									<td>".$mb_sms_txt."</td>
									<td>".str_replace('-','.',substr($LIST[$i]['last_agreement_date'],0,16))."</td>
								</tr>\n";

	}
}
else {
?>
								<tr>
									<td colspan="15" align="center" height="200px";>검색된 데이터가 없습니다.</td>
								</tr>
<?
}
?>
							</table>

						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- SMS 영역 E N D -->
	</form>

</div>

<script>
function bytePrint() {
	strlength = byteCheck($('#sms_msg'));
	$('#sms_msg_length').val(strlength);
	if(Number(strlength) <= 86) {
		$('#btn_submit').removeClass('btn-danger').addClass('btn-primary');
		$('#btn_submit').text('SMS 발송');
	}
	else {
		$('#btn_submit').removeClass('btn-primary').addClass('btn-danger');
		$('#btn_submit').text('LMS 발송');
	}
}

function byteCheck(el) {
	var codeByte = 0;
	for(i=0; i<el.val().length; i++) {
		var oneChar = escape( el.val().charAt(i) );
		if( oneChar.length==1 ) {
			codeByte++;
		}
		else if( oneChar.indexOf("%u") != -1 ) {
			codeByte+=2;
		}
		else if( oneChar.indexOf("%") != -1 ) {
			codeByte++;
		}
	}
	if(codeByte > 0) codeByte+=2;
	return codeByte;
}
</script>

<script>
// 예약발송 시, 예약시간 폼 노출
function check_sms_time(val) {
	if(val == 'r') {
		$('#send_t_area').show();
	}else {
		$('#send_t_area').hide();
	}
}

// 문자발송
function sms_send(f) {
	var msg = $('#sms_msg').val();
	var from_hp = $('#from_hp').val();
	var to_hp = $('#to_hp').val();
	var send_time = $('#send_time').val();
	var send_ymd = $('#send_ymd').val();
	var send_h = $('#send_h').val();
	var send_i = $('#send_i').val();

	var chk1;
	var chk2;
	var chk3;
	var chk4;

	if(msg == '') {
		$('#msg_err').html('메세지 내용을 입력해주세요.');
		chk1 = 'N';
	}else {
		$('#msg_err').html('');
		chk1 = 'Y';
	}

	if(from_hp == '') {
		$('#from_hp_err').html('발신번호를 입력해주세요.');
		chk2 = 'N';
	}else {
		$('#from_hp_err').html('');
		chk2 = 'Y';
	}

	if(to_hp == '') {
		$('#to_hp_err').html('수신번호를 입력해주세요.');
		chk3 = 'N';
	}else {
		$('#to_hp_err').html('');
		chk3 = 'Y';
	}

	if(send_time == 'r' && send_ymd == '') {
		$('#send_time_err').html('예약발송 시간을 설정해주세요.');
		chk4 = 'N';
	}else {
		$('#send_time_err').html('');
		chk4 = 'Y';
	}

	if(chk1=='Y' && chk2=='Y' && chk3=='Y' && chk4=='Y') {
		$('#mode').val('send');
		if(confirm('대량발송의 경우, 처리시간이 길어질수 있습니다.\n\n완료 메세지가 나올때까지 기다려주세요.\n\n선택된 회원들에게 SMS 발송을 하시겠습니까?')) {
			f.submit();
			return;
		}
	}
}

// 가입기간 검색
function term_search(day_val) {
	$('#sh_term').val(day_val);
	$('#fmember').submit();
}

// tr 클릭시 체크박스 온
function on_checkbox(idx) {
	var checked_yn = $('#checkbox_'+idx).is(':checked');
	if(checked_yn == false) {
		$('#checkbox_'+idx).attr('checked', true);
	}else {
		$('#checkbox_'+idx).attr('checked', false);
	}
}
</script>

<script>
$('#btn_delete').click(function() {
	var fsms = document.fsms;
	fsms.mode.value = 'number_delete';
	if(confirm('선택된 수신자 정보를 삭제하시겠습니까?')) {
		fsms.submit();
	}
});
</script>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>