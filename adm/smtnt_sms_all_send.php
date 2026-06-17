<?
$sub_menu = '800520';
include_once('./_common.php');


auth_check($auth[$sub_menu], "w");

$html_title = "회원 SMS발송 설정 (SMTNT)";
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }

if(!$sh_term) $sh_term = '30';			//$sh_term = 'imsi';
if(!$member_group) $member_group = 'F';
if(!$sms) $sms = 'allower';

if($mode=='search') {

	$sql_search = " 1=1 ";
	$sql_search.= " AND B.mb_level IN(1,2,3,4,5) ";
	if($member_group) $sql_search.= " AND B.member_group='$member_group' ";
	if($member_type)  $sql_search.= " AND B.member_type='$member_type' ";
	if($mb_10) $sql_search.= " AND B.mb_10='$mb_10' ";

	if($is_rest) {
		$sql_search.= ($is_rest=='both') ? "" : " AND B.is_rest='".$is_rest."' ";
	}
	else {
		$sql_search.= " AND B.is_rest='N' ";
	}

	if($sms=='allower') {
		$sql_search.= " AND B.mb_sms='1'";		// 수신설정자
	}
	else {
		if($sms=='all') $sql_search.= "";		// 전체대상발송
		else if($sms=='refuser') $sql_search.= " AND B.mb_sms='0'";		// 비수신설정자
	}

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
		if($key_search=='B.mb_hp') {
			$sql_search.= " AND $key_search = '".masterEncrypt($keyword, false)."' ";
		}
		else {
			$sql_search.= " AND $key_search LIKE '%$keyword%' ";
		}
	}

	if($syndi_id) {
		$ARR_KEY = array_keys($CONF['SYNDICATOR']);
		for($i=0; $i<count($ARR_KEY); $i++) {
			if($syndi_id==$ARR_KEY[$i]) {
				if($syndi_id=='hktvwowstar') {
					$sql_search.= " AND (B.wowstar_userid!='') ";
				}
				else {
					$sql_search.= " AND (B.{$ARR_KEY[$i]}_userid!='') ";
				}
			}
		}
	}

	//투자한 회원을 선택했을 경우
	if($product_idx != '') {
		$PRDT = sql_fetch("SELECT state FROM cf_product WHERE idx='$product_idx'");

		$sql = "SELECT member_idx FROM cf_product_invest WHERE product_idx='$product_idx'";
		$sql.= ($PRDT['state']==6) ? " AND invest_state='R' " : " AND invest_state='Y' ";
		$sql.= " GROUP BY member_idx ORDER BY member_idx";

		$rs = sql_query($sql);
		$rows = $rs->num_rows;
		if($rows) {
			$sql_search.= " AND mb_no IN(";
			for ($i=0,$j=1; $i<$rows; $i++,$j++) {
				$r = sql_fetch_array($rs);
				$sql_search.= "'".$r['member_idx']."'";
				$sql_search.= ($j < $rows) ? "," : "";
			}
			$sql_search.= ") ";
		}
	}


	$sql = "
		SELECT
			B.mb_no, B.mb_id, B.member_type, B.mb_point, B.mb_name, B.mb_co_name, B.mb_hp, B.mb_sms
		FROM
			g5_member B
		WHERE
			$sql_search
		GROUP BY
			B.mb_hp
		ORDER BY
			B.mb_datetime DESC";
	if($sh_term=="imsi") $sql.= " LIMIT 100";


	// 상환완료문자 발송시 (회원중복되지 않도록 처리)
	if($products) {
		$PRODUCTS = explode(",", $products);
		$product_count = count($PRODUCTS);
		if( $product_count ) {

			$sql = "
				SELECT
					B.mb_no, B.mb_id, B.member_type, B.mb_point, B.mb_name, B.mb_co_name, B.mb_hp, B.mb_sms
				FROM
					cf_product_invest A
				LEFT JOIN
					g5_member B  ON (A.member_idx=B.mb_no)
				WHERE 1
					AND A.product_idx IN(".$products.") AND A.invest_state='Y'
				GROUP BY
					B.mb_hp
				ORDER BY
					B.mb_10 DESC,
					A.member_idx ASC";

		}
	}

	$res = sql_query($sql);
	$rows = $res->num_rows;

	for($i=0; $i<$rows; $i++) {
		$R = sql_fetch_array($res);
		$R['mb_hp'] = masterDecrypt($R['mb_hp'], false);

		$LIST[] =	$R;
	}

	sql_free_result($res);

}

$list_count = count($LIST);

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

.checkbox_area { width:380px;float:left;font-size:12px }
</style>

<div class="tbl_frm01 tbl_wrap tbl_head02" style="background-color:white;">

	<form name="fmember" id="fmember" action="/adm/smtnt_sms_all_send.php" method="get" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="sh_term" id="sh_term" value="<?=$sh_term?>">

	<!-- 검색영역 START -->

	<div style="padding-top:10px;margin-bottom:10px;">
	  투자자용 문자 → 품번배열 (쉼표(,)로 구분)
		<textarea name="products" id="products" class="form-control" style="width:100%;height:44px;font-size:12px;background:#FFEFEF;"><?=$products?></textarea>
	</div>

<? if(false) { ?>
	<div style="margin-bottom:10px;">
		<select name="product_idx" id="product_idx" alt="투자상품리스트" onChange="document.fmember.submit();" class="form-control" style="width:500px;font-size:12px">
			<option value="">::투자상품선택::</option>
			<?
			$res = sql_query("SELECT idx, title, start_date, state FROM cf_product WHERE display='Y' AND isTest='' AND loan_start_date >= '".date('Y-m-d', strtotime("-1 year"))."' ORDER BY start_num DESC");
			while($PRDT = sql_fetch_array($res)) {
				$selected = ($PRDT['idx']==$product_idx) ? "selected" : "";
				$print_state = "";
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
				echo "<option value='".$PRDT['idx']."' $selected>".stripSlashes($PRDT['title'])." :::: ".$print_state."</option>\n";
			}
			?>
		</select>
	</div>
<? } ?>

	<div style="margin-bottom:10px;">
		<label class="radio-inline"><input type="radio" name="sh_term" value="all" <?=($sh_term=='all')?'checked':''?>> 전체가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="30" <?=($sh_term=='30')?'checked':''?>> 30일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="60" <?=($sh_term=='60')?'checked':''?>> 60일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="90" <?=($sh_term=='90')?'checked':''?>> 90일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="180" <?=($sh_term=='180')?'checked':''?>> 180일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="365" <?=($sh_term=='365')?'checked':''?>> 365일이내 가입자</label>
		<label class="radio-inline">
			<select name="is_rest" class="form-control input-sm">
				<option value='N' <?=($is_rest=='N')?'selected':''?>>비휴면계정</option>
				<option value='Y' <?=($is_rest=='Y')?'selected':''?>>휴면계정</option>
				<option value='both' <?=($is_rest=='both')?'selected':''?>>무관</option>
			</select>
		</label>
	</div>

	<ul class="col-sm-10 list-inline" style="padding-left:0;margin-bottom:5px">
		<li style="vertical-align:middle;">
			<select name="member_group" class="form-control input-sm">
				<option value="">::회원그룹::</option>
				<option value="F" <?=($member_group=='F')?'selected':'';?>>투자회원</option>
				<option value="L" <?=($member_group=='L')?'selected':'';?>>대출회원</option>
			</select>
		</li>
		<li style="vertical-align:middle;">
			<select name="member_type" class="form-control input-sm">
				<option value="">::회원구분::</option>
				<option value="1" <?=($member_type == '1')?'selected':'';?>>개인회원</option>
				<option value="2" <?=($member_type == '2')?'selected':'';?>>법인회원</option>
				<option value="3" <?=($member_type == '3')?'selected':'';?>>SNS회원</option>
			</select>
		</li>
		<li style="margin-left:20px;margin-right:20px;vertical-align:middle;">
			<select class="form-control input-sm" name="syndi_id">
				<option>::신디케이션 선택::</option>
				<?
				foreach ($CONF['SYNDICATOR'] as $syndi_key => $syndi_text) {
				?>
				<option value="<?=$syndi_key?>" <?=$syndi_id==$syndi_key?"selected":""?> ><?=$syndi_text["name"]?></option>
					<?
				}
				?>
			</select>
		</li>
		<li style="vertical-align:middle;">
			<label class="checkbox-inline"><input type="checkbox" name="mb_10" value="1" <?=($mb_10)?'checked':'';?>> 직원</label>
		</li>
		<li></li>
		<li style="vertical-align:middle;">
			<select class="form-control input-sm" name="sms">
				<option value="allower" <?=($sms=='allower')?'selected':''?>>수신허용설정자</option>
				<option value="refuser" <?=($sms=='refuser')?'selected':''?>>수신거부설정자</option>
				<option value="all" <?=($sms=='all')?'selected':''?>>전체(동의여부무관)</option>
			</select>
			<!--<label class="checkbox-inline"><input type="checkbox" name="allsend" value="Y" <?=($allsend)?'checked':'';?>> 비수신 설정자 포함</label>-->
		</li>
		<li></li>
		<li style="vertical-align:middle;">가입일:</li>
		<li style="vertical-align:middle;"><input type="text" name="start_date" value="<?=$start_date?>" class="form-control input-sm datepicker" style="width:100px;"></li>
		<li style="vertical-align:middle;">~</li>
		<li style="vertical-align:middle;"><input type="text" name="end_date" value="<?=$end_date?>" class="form-control input-sm datepicker" style="width:100px"></li>
		<li style="vertical-align:middle;"></li>
		<li style="vertical-align:middle;">예치금:</li>
		<li style="vertical-align:middle;"><input type="text" name="start_point" value="<?=$start_point?>" class="form-control input-sm" style="width:100px"></li>
		<li style="vertical-align:middle;">~</li>
		<li style="vertical-align:middle;"><input type="text" name="end_point" value="<?=$end_point?>" class="form-control input-sm" style="width:100px"></li>
	</ul>

	<ul class="col-sm-10 list-inline" style="padding-left:0;margin-bottom:5px">
		<li style="vertical-align:middle;">
			<select name="key_search"  class="form-control input-sm" style="width:120px">
				<option value="">::선택::</option>
				<option value="B.mb_id" <?=($key_search=='B.mb_id')?'selected':'';?>>아이디</option>
				<option value="B.mb_email" <?=($key_search=='B.mb_email')?'selected':'';?>>이메일</option>
				<option value="B.mb_name" <?=($key_search=='B.mb_name')?'selected':'';?>>이름</option>
				<option value="B.mb_hp" <?=($key_search=='B.mb_hp')?'selected':'';?>>휴대폰</option>
			</select>
		</li>
		<li style="vertical-align:middle;"><input type="text" name="keyword" value="<?=$keyword;?>" class="form-control input-sm" style="width:200px"></li>
		<li style="vertical-align:middle;"><button type="button" id="search_button" class="btn btn-sm btn-warning">검색</button></li>
	</ul>
	<!-- 검색영역 END -->
	</form>
	<script>
	$('#search_button').on('click',function() {
		loading('on');
		$('#list_area').empty();
		$('#fmember').submit();
	});
	</script>



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
							<li style="float:left;padding-top:8px"><span id="send_time_err" class="sms_error"></span></li>
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
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row" style="text-align:center;"><label for="regist_number">SMS 발송회원</label></th>
				<td>
					<div id="list_area" style="width:100%;height:300px;padding:10px; border:1px solid #ccc; overflow-y:scroll;">

						<label><input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)"> <?=number_format($list_count)?>건 전체선택</label><br>
<?
if($list_count) {

	$num = $list_count;

	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$print_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];
		$print_mb_hp = substr($LIST[$i]['mb_hp'], 0, strlen($LIST[$i]['mb_hp'])-4) . "●●●●";
		$mb_sms_txt = ($LIST[$i]['mb_sms']=='1') ? "허용" : "<font color=red>거부</font>";

		echo "<div class='checkbox_area'><input type=\"checkbox\" id=\"checkbox_{$i}\" name=\"chk[]\" value=\"".$LIST[$i]['mb_hp']."\"> " . $print_mb_hp . " / " . $LIST[$i]['mb_id'] . " / " . $print_name . " </div>";
		if(($j%4)==0) echo "\n";

	}

}
?>

					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- SMS 영역 E N D -->
	</form>

</div>

<script>
$(document).ready(function() {
	check_sms_time('r');
});
</script>

<script>
function bytePrint_back() {
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
function bytePrint() {
	strlength = byteCheck($('#sms_msg'));
	$('#sms_msg_length').val(strlength);
	if(Number(strlength) <= 90) {
		$('#btn_submit').removeClass('btn-danger').addClass('btn-primary');
		$('#btn_submit').text('SMS 발송');
	}
	else {
		$('#btn_submit').removeClass('btn-primary').addClass('btn-danger');
		$('#btn_submit').text('LMS 발송');
	}
}

function byteCheck_back(el) {
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
		else if( oneChar == "%0A" ) {   // 엔터는 2바이트
			codeByte+=2;
		}
		else if( oneChar.indexOf("%") != -1 ) {
			codeByte++;
		}
	}

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
	//console.log($('input[name="chk[]"]:checked').length);
	//console.log($("input:checkbox[name='member_all']").is(":checked"));

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

	var msg_count = "";
	if ($("input:checkbox[name='member_all']").is(":checked")) msg_count = "전체가입자";
	else msg_count = $('input[name="chk[]"]:checked').length+"명";

	var fdata = $('#fsms').serialize();

	if( confirm('대량발송의 경우, 처리시간이 길어질수 있습니다.\n\n완료 메세지가 나올때까지 기다려주세요.\n\n'+msg_count+'에게 SMS 발송을 진행 하시겠습니까?') ) {
		$.ajax({
			url:'smtnt_sms_all_send_proc.php',
			type:'post',
			data:fdata,
			dataType:'JSON',
			success: function(data) {
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
	//$('#fmember').submit();
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

