<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = '800300';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu800'][3][1];
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');



// GET 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}

if($mode=='search') {

	if(!$member_group) $member_group = 'F';
	//if(!$sh_term) $sh_term = '30';


	$sql_search = " 1=1 ";
	$sql_search.= " AND B.mb_level IN(1,2,3,4,5) ";
	if($member_group) $sql_search.= " AND B.member_group='$member_group' ";
	if($member_type)  $sql_search.= " AND B.member_type='$member_type' ";
	if($mb_10) $sql_search.= " AND B.mb_10='$mb_10' ";
	$sql_search.= " AND B.is_rest='N' ";
	//$sql_search.= " AND LEFT(B.mb_hp,3) IN('010','011','016','017','018','019') ";
	$sql_search.= ($allsend) ? "" : " AND B.mb_sms='1' ";

	if($sh_term && $sh_term != 'all') {
		switch($sh_term) {
			case '30'  : $target_date = date("Y-m-d", strtotime("-30 day"));    break;
			case '60'  : $target_date = date("Y-m-d", strtotime("-60 day"));    break;
			case '90'  : $target_date = date("Y-m-d", strtotime("-90 day"));    break;
			case '180' : $target_date = date("Y-m-d", strtotime("-6 month"));   break;
			case '365' : $target_date = date("Y-m-d", strtotime("-365 day"));  break;
		}
		$sql_search.= " AND LEFT(B.mb_datetime,10)>='$target_date'";
	}
	if($start_date)  $sql_search.= " AND LEFT(B.mb_datetime,10)>='$start_date' ";
	if($end_date)    $sql_search.= " AND LEFT(B.mb_datetime,10)<='$end_date' ";
	if($start_point) $sql_search.= " AND B.mb_point>='$start_point'";
	if($end_point)   $sql_search.= " AND B.mb_point<='$end_point' ";
	if($key_search && $keyword) {
		if($key_search=='B.mb_no') {
			if( preg_match("/\,/", $keyword) ) {
				$sql_search.= " AND $key_search IN(".preg_replace("/( )/", "", $keyword).") ";
			}
			else {
				$sql_search.= " AND $key_search='$keyword' ";
			}
		}
		else if($key_search=='B.mb_hp') {
			$sql_search.= " AND $key_search = '".masterEncrypt($keyword, false)."' ";
		}
		else {
			$sql_search.= " AND $key_search LIKE '%$keyword%' ";
		}
	}

	if($syndi_id) {
		if ($syndi_id=="chosun")           $sql_search.= " AND (B.chosun_userid<>'' and B.chosun_userid is not NULL) ";
		else if ($syndi_id=="finnq")       $sql_search.= " AND (B.finnq_userid<>'' and B.finnq_userid is not NULL) ";
		else if ($syndi_id=="TvTalk")      $sql_search.= " AND (B.tvtalk_userid<>'' and B.tvtalk_userid is not NULL) ";
		else if ($syndi_id=="hktvwowstar") $sql_search.= " AND (B.wowstar_userid<>'' and B.wowstar_userid is not NULL) ";
		else if ($syndi_id=="oligo")       $sql_search.= " AND (B.oligo_userid<>'' and B.oligo_userid is not NULL) ";
	}
	if($exc_chosun=="Y") $sql_search.= " AND (B.chosun_userid='' or B.chosun_userid is NULL) ";

	//투자한 회원을 선택했을 경우
	if($product_idx != '') {
		$PRDT = sql_fetch("SELECT state FROM cf_product WHERE idx='$product_idx'");

		$sql = "SELECT member_idx FROM cf_product_invest WHERE product_idx='$product_idx'";
		$sql.= ($PRDT['state']==6) ? " AND invest_state='R' " : " AND invest_state='Y' ";
		$sql.= " GROUP BY member_idx ORDER BY member_idx";

		$rs = sql_query($sql);
		$rows = sql_num_rows($rs);
		if($rows) {
			$sql_search .= " AND mb_no IN(";
			for ($i=0,$j=1; $i<$rows; $i++,$j++) {
				$r = sql_fetch_array($rs);
				$sql_search.= "'".$r['member_idx']."'";
				$sql_search.= ($j < $rows) ? "," : "";
			}
			$sql_search .= ") ";
		}
	}

	//echo $sql_search;
	if($sh_term=="imsi") {

		$sql = "
			SELECT
				A.*,
				B.member_type, B.member_investor_type, B.is_creditor, B.is_owner_operator, B.mb_id, B.mb_name, B.mb_hp, B.mb_email, LEFT(B.mb_datetime, 10) AS mb_datetime, B.mb_sms, B.mb_co_name
			FROM
				investor_type_change_request A
			LEFT JOIN
				g5_member B  ON A.mb_no=B.mb_no
			WHERE 1=1
				AND B.mb_level='1'
				AND A.order_type='2'
				AND A.allow='Y'
				AND A.rights_end_date < '2018-12-31'
			ORDER BY
				A.idx DESC";

	}
	else {

		$sql = "
			SELECT
				B.mb_no, B.mb_id, B.member_type, B.mb_point, B.mb_name, B.mb_co_name, B.mb_email, B.mb_hp, B.mb_mailling, B.mb_sms, B.mb_datetime
			FROM
				g5_member B
			WHERE
				$sql_search
			GROUP BY
				B.mb_hp
			ORDER BY
				B.mb_datetime DESC";

	}
	//print_rr($sql);
	$res = sql_query($sql);
	$total_count = sql_num_rows($res);

	for($i=0; $i<$total_count; $i++) {
		$R = sql_fetch_array($res);
		$R['mb_hp'] = masterDecrypt($R['mb_hp']);

		$LIST[] =	$R;
	}

	sql_free_result($res);

}

$ad_member = get_member('admin');

?>

<style>
.sms_info { margin:10px auto; padding:15px; }
.sms_info div { margin-bottom:25px; }
.sms_area { width:230px; display:inline-block; margin:10px auto; padding:15px; }
.sms_msg textarea { border:1px solid #EEEEEE; width:200px; font-size:12px; height:250px; }
.sms_title { text-align:center; font-weight:bold; color:#FFFFFF; padding:10px; background-color:#3C5B9B; border-radius:3px 3px 0 0;}
.sms_use { padding-top:10px; text-align:center; }
.sms_error { color:red; }
</style>

<div class="tbl_frm01 tbl_wrap tbl_head02">

	<form name="fmember" id="fmember" action="/adm/sms_all_send.php" method="get" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="sh_term" id="sh_term" value="<?=$sh_term?>">

		<!-- 검색영역 START -->
		<div style="margin-bottom:10px;">
			<?
			$day_before = date('Y-m-d', strtotime( $this_date . ' -7 day'));
			?>
			<select name="product_idx" id="product_idx" alt="투자상품리스트" onChange="document.fmember.submit();" class="form-control" style="width:500px;font-size:12px">
        <option value="">::투자상품선택::</option>
<?
	//$res = sql_query("SELECT idx, title, start_date, state FROM cf_product WHERE display='Y' ORDER BY idx DESC, title DESC");
	$res = sql_query("
		SELECT
			idx, title, start_date, state
		FROM
			cf_product
		WHERE 1
			AND display='Y' AND isTest=''
			AND state!=''
			-- AND loan_end_date>='$day_before'
		ORDER BY
			start_num desc");
	while($PRDT = sql_fetch_array($res)) {
		$selected = ($PRDT['idx']==$product_idx) ? "selected" : "";
		switch($PRDT['state']) {
			case '1' : $print_state = '이자상환중'; break;
			case '2' : $print_state = '상환완료'; break;
			case '3' : $print_state = '투자금모집실패'; break;
			case '4' : $print_state = '부실'; break;
			case '5' : $print_state = '중도상환'; break;
			case '6' : $print_state = '대출취소(기표전)'; break;
			case '7' : $print_state = '대출취소(기표후)'; break;
			case '8' : $print_state = '연체'; break;
			case '9' : $print_state = '부도(상환불가))'; break;
		}
		echo "<option value='".$PRDT['idx']."' $selected>".stripSlashes($PRDT['title'])." :::: ".$print_state."</option>";
	}
?>
			</select>
		</div>
		<div style="margin-bottom:5px;">
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='all')?'active':''?>" onclick="term_search('all');" style="margin-right:2px;">전체가입자</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='30')?'active':''?>" onclick="term_search('30');" style="margin-right:2px;">30일이내 가입자</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='60')?'active':''?>" onclick="term_search('60');" style="margin-right:2px;">60일이내 가입자</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='90')?'active':''?>" onclick="term_search('90');" style="margin-right:2px;">90일이내 가입자</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='180')?'active':''?>" onclick="term_search('180');" style="margin-right:2px;">6개월이내 가입자</button>
			<button type="button" class="btn-default btn btn-sm <?=($sh_term=='365')?'active':''?>" onclick="term_search('365');" style="margin-right:2px;">12개월이내 가입자</button>

			<!--<button type="button" class="btn-default btn btn-sm <?=($sh_term=='imsi')?'active':''?>" onclick="imsi_search();" style="margin-right:2px;">임시</button>-->

		</div>

		<ul class="col-sm-10 list-inline" style="padding-left:0;margin-bottom:5px">
			<li>
				<select name="member_group" class="form-control">
					<option value="">::회원그룹::</option>
					<option value="F" <?=($member_group=='F')?'selected':'';?>>투자회원</option>
					<option value="L" <?=($member_group=='L')?'selected':'';?>>대출회원</option>
				</select>
			</li>
			<li>
				<select name="member_type" class="form-control">
					<option value="">::회원구분::</option>
					<option value="1" <?=($member_type == '1')?'selected':'';?>>개인회원</option>
					<option value="2" <?=($member_type == '2')?'selected':'';?>>법인회원</option>
					<option value="3" <?=($member_type == '3')?'selected':'';?>>SNS회원</option>
				</select>
			</li>
				<?
				//if ($member['mb_id']== "admin_romrom") {
				if (2>1) {
					?>
			<li style="margin-left:20px;margin-right:20px;">
				<select class="form-control" name="syndi_id">
					<option>파트너사</option>
					<?
					foreach ($CONF['SYNDICATOR'] as $syndi_key => $syndi_text) {
						?>
					<option value="<?=$syndi_key?>" <?=$syndi_id==$syndi_key?"selected":""?> ><?=$syndi_text["name"]?></option>
						<?
					}
					?>
				</select>
			</li>
			<!-- <li style="margin-right:20px;">
				<label class="checkbox-inline"><input type="checkbox" name="exc_chosun" value="Y" <?=($exc_chosun)?'checked':'';?>> 땅집고 제외</label>
			</li> -->
				<? } ?>
			<li><label class="checkbox-inline"><input type="checkbox" name="mb_10" value="1" <?=($mb_10)?'checked':'';?>> 직원</label></li>
			<li></li>
			<li><label class="checkbox-inline"><input type="checkbox" name="allsend" value="Y" <?=($allsend)?'checked':'';?>> 비수신 설정자 포함</label></li>
			<li></li>
			<li>가입일:</li>
			<li><input type="text" name="start_date" value="<?=$start_date?>" class="form-control datepicker" style="width:100px;" <?=($sh_term && $sh_term=='all')?'':'disabled'?>></li>
			<li>~</li>
			<li><input type="text" name="end_date" value="<?=$end_date?>" class="form-control datepicker" style="width:100px" <?=($sh_term && $sh_term=='all')?'':'disabled'?>></li>
			<li></li>
			<li>예치금:</li>
			<li><input type="text" name="start_point" value="<?=$start_point?>" class="form-control" style="width:100px"></li>
			<li>~</li>
			<li><input type="text" name="end_point" value="<?=$end_point?>" class="form-control" style="width:100px"></li>
		</ul>

		<ul class="col-sm-10 list-inline" style="padding-left:0;margin-bottom:5px">
			<li>
				<select name="key_search"  class="form-control" style="width:120px">
					<option value="">::선택::</option>
					<option value="B.mb_no" <?=($key_search=='B.mb_no')?'selected':'';?>>회원번호</option>
					<option value="B.mb_id" <?=($key_search=='B.mb_id')?'selected':'';?>>아이디</option>
					<option value="B.mb_email" <?=($key_search=='B.mb_email')?'selected':'';?>>이메일</option>
					<option value="B.mb_name" <?=($key_search=='B.mb_name')?'selected':'';?>>이름</option>
					<option value="B.mb_hp" <?=($key_search=='B.mb_hp')?'selected':'';?>>휴대폰</option>
				</select>
			</li>
			<li><input type="text" name="keyword" value="<?=$keyword;?>" class="form-control" style="width:200px"></li>
			<li><input type="submit" class="btn btn-warning" value="검색"></li>
		</ul>

		<!-- 검색영역 E N D -->
	</form>

	<form name="fsms" id="fsms" enctype="multipart/form-data">
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
						<div class="sms_area" style="float:left;">
							<div class="sms_title">SMS 문자전송</div>
							<div class="sms_msg"><textarea rows="20" name="sms_msg" id="sms_msg" placeholder="메세지 내용을 입력해주세요" onKeyUp="bytePrint();"></textarea></div>
							<div style="margin-top:4px; text-align:right;"><input type="text" id="sms_msg_length" class="frm_input" value="0" style="width:60px;text-align:right"> byte</div>
							<span id="msg_err" class="sms_error"></span>
						</div>

						<div class="sms_info" style="float:left; width:600px;padding-top:30px;">
							<ul class="col-sm-10 list-inline" style="padding-left:0;">
								<li style="float:left;padding-top:8px">발신번호</li>
								<li style="float:left"><input type="text" name="from_hp" id="from_hp" value="<?=$CONF['admin_sms_number']?>" class="form-control" style="width:120px"></li>
								<li style="float:left;padding-top:8px""><span id="from_hp_err" class="sms_error"></span></li>
							</ul>

							<ul class="col-sm-10 list-inline" style="padding-left:0;">
								<li style="float:left;padding-top:8px">발송모드</li>
								<li style="float:left">
									<select name="send_time" id="send_time" class="form-control" style="width:120px" onChange="check_sms_time(this.value);">
										<option value="d">즉시발송</option>
										<option value="r" selected>예약발송</option>
									</select>
								</li>
								<li style="float:left;padding-top:8px""><span id="send_time_err" class="sms_error"></span></li>
							</ul>

							<ul id="send_t_area" class="col-sm-10 list-inline" style="padding-left:0;margin-left:60px;display:none;">
								<li style="float:left"><input type="text" class="form-control datepicker" name="send_ymd" id="send_ymd" value="" placeholder="일자선택" style="width:120px"></li>
								<li style="float:left">
									<select id="send_h" name="send_h" class="form-control" style="width:80px">
<?
	for($i=0; $i<=23; $i++) {
		$ii = sprintf('%02d', $i);
		echo '<option value="'.$ii.'">'.$ii.'시</option>' . PHP_EOL;
	}
?>
									</select>
								</li>
								<li style="float:left">
									<select id="send_i" name="send_i" class="form-control" style="width:80px">
<?
	for($i=0; $i<=59; $i++) {
		$ii = sprintf('%02d', $i);
		echo '<option value="'.$ii.'">'.$ii.'분</option>' . PHP_EOL;
	}
?>
									</select>
								</li>
							</ul>
							<ul class="col-sm-10 list-inline" style="clear:both;padding-left:0;">
								<li style="height:40px"><label class="checkbox-inline"><input type="checkbox" name="member_all" value="1" onClick="if(this.checked){alert('전체 회원에게 발송됩니다. 선택에 주의를 요합니다.');}"> 전체가입자 대상 (비수신 설정자 제외, 땅집고 제외)</label></li>
								<li><button type="button" id="btn_submit" class="btn btn-lg btn-primary" style="width:190px">SMS 발송</button></li>
								<li><span id="sms_result" class="sms_error"></span></li>
							<ul>

<script>
$(document).ready(function() {
	check_sms_time('r');
});
</script>

						</div>
					</td>
				</tr>
				<tr>
					<th scope="row" style="text-align:center;"><label for="regist_number">SMS 발송회원</label></th>
					<td>
						<div style="width:100%;height:300px;padding:0; border:1px solid #ccc; overflow-y:scroll;">

							<table class="table_hover" style="width:98%;margin:1%;">
								<caption><?=$g5['title']; ?> 목록</caption>
								<thead>
								<tr>
									<th scope="col" style="text-align:center;"><label><input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)"></label></th>
									<th scope="col" style="text-align:center;">번호</th>
									<th scope="col" style="text-align:center;">아이디</th>
									<th scope="col" style="text-align:center;">연락처</th>
									<th scope="col" style="text-align:center;">예치금</th>
									<th scope="col" style="text-align:center;">성명/담당자명</th>
									<th scope="col" style="text-align:center;">업체명</th>
									<th scope="col" style="text-align:center;">SMS수신허용</th>
									<th scope="col" style="text-align:center;">등록일</th>
								</tr>
								</thead>
								<tbody>
<?
if($total_count) {
	$num = $total_count;
	for($i=0,$j=$num; $i<$total_count; $i++,$j--) {

		$mb_sms_txt = ($LIST[$i]['mb_sms']=='1') ? "허용" : "";

		echo "
								<tr align=\"center\" style=\"font-size:12px;\"><!--onClick=\"on_checkbox('$i')\"-->
									<td headers=\"mb_list_chk\" class=\"td_chk\"><input type=\"checkbox\" id=\"checkbox_{$i}\" name=\"chk[]\" value=\"".$LIST[$i]['mb_hp']."\"></td>
									<td>".number_format($j)."</td>
									<td>".$LIST[$i]['mb_id']."</td>
									<td>".$LIST[$i]['mb_hp']."</td>
									<td align='right'>".number_format($LIST[$i]['mb_point'])."원</td>
									<td>".$LIST[$i]['mb_name']."</td>
									<td>".$LIST[$i]['mb_co_name']."</td>
									<td>".$mb_sms_txt."</td>
									<td>".str_replace('-','.',substr($LIST[$i]['mb_datetime'],0,16))."</td>
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
								</tbody>
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
$("#btn_submit").on('click', function() {

	var msg       = $('#sms_msg').val();
	var from_hp   = $('#from_hp').val();
	var to_hp     = $('#to_hp').val();
	var send_time = $('#send_time').val();
	var send_ymd  = $('#send_ymd').val();
	var send_h    = $('#send_h').val();
	var send_i    = $('#send_i').val();

	var chk1;
	var chk2;
	var chk3;
	var chk4;

	if(msg=='')     { $('#msg_err').html('메세지 내용을 입력해주세요.'); return; }
	if(from_hp=='') { $('#from_hp_err').html('발신번호를 입력해주세요.'); return; }
	if(to_hp=='')   { $('#to_hp_err').html('수신번호를 입력해주세요.'); return; }
	if(send_time=='r' && send_ymd=='') { $('#send_time_err').html('예약발송 시간을 설정해주세요.'); return; }

	var fdata = $('#fsms').serialize();
	if( confirm('대량발송의 경우, 처리시간이 길어질수 있습니다.\n\n완료 메세지가 나올때까지 기다려주세요.\n\nSMS 발송을 진행 하시겠습니까?') ) {
		$.ajax({
			url:'sms_all_send_proc.php',
			type:'post',
			data:fdata,
			dataType:'JSON',
			success: function(data) {
			//$('#ajax_return_txt_zone').css('display','block');
			//$('#ajax_return_txt').val(data);

				if(data.result=='SUCCESS') {
					alert('발송실행 : ' + data.msg);
				}
				else {
					alert('처리실패 : ' + data.msg);
				}
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}

});

// 가입기간 검색
function term_search(day_val) {
	$('#sh_term').val(day_val);
	$('#fmember').submit();
}

function imsi_search() {
	$('#sh_term').val("imsi");
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

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>