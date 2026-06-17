<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = '800400';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "회원 메일발송";
$g5['title'] = $html_title.' 설정';

include_once (G5_ADMIN_PATH.'/admin.head.php');

while( list($k, $v)=each($_REQUEST) ) { ${$k} = trim($v); }

if(!$sh_term) $sh_term = '30';			//$sh_term = 'imsi';
if(!$member_group) $member_group = 'F';
if(!$mailling) $mailling = 'allower';

if($mode=='search') {

	$sql_search = " 1=1 ";
	$sql_search.= " AND B.mb_level IN(1,2,3,4,5) ";
	if($member_group) $sql_search.= " AND B.member_group='$member_group' ";
	if($member_type)  $sql_search.= " AND B.member_type='$member_type' ";
	if($mb_10) $sql_search.= " AND B.mb_10='$mb_10' ";
	$sql_search.= " AND B.is_rest='N' ";

	if($mailling=='allower') {
		$sql_search.= " AND B.mb_mailling='1'";		// 수신설정자
	}
	else {
		if($mailling=='all') $sql_search.= "";		// 전체대상발송
		else if($mailling=='refuser') $sql_search.= " AND B.mb_mailling='0'";		// 비수신설정자
	}

	if($email_co) {

		if($email_co=='naver')      $sql_search.= " AND mb_email LIKE '%@naver.com' AND is_rest='N'";
		else if($email_co=='daum')  $sql_search.= " AND (mb_email LIKE '%@daum.net' OR mb_email LIKE '%@hanmail.net') AND is_rest='N'";
		else if($email_co=='gmail') $sql_search.= " AND (mb_email LIKE '%@gmail.%' OR mb_email LIKE '%@google.%') AND is_rest='N'";
		else if($email_co=='nate')  $sql_search.= " AND mb_email LIKE '%@nate.com' AND is_rest='N'";

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
			B.mb_no, B.mb_id, B.member_type, B.mb_point, B.mb_name, B.mb_co_name, B.mb_email, B.mb_hp, B.mb_mailling, B.mb_sms, B.mb_datetime
		FROM
			g5_member B
		WHERE
			$sql_search
		GROUP BY
			B.mb_email
		ORDER BY
			B.mb_datetime DESC";

	if($sh_term=="imsi") $sql.= " LIMIT 100";

	$res = sql_query($sql);
	$rows = $res->num_rows;

	for($i=0; $i<$rows; $i++) {
		$R = sql_fetch_array($res);
		$R['mb_hp'] = $R['mb_email'];

		if(preg_match("/@/", $R['mb_email'])) $LIST[] =	$R;
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
.sms_msg textarea { border:1px solid #EEEEEE; width:200px; font-size:12px; }
.sms_title { text-align:center; font-weight:bold; color:#FFFFFF; padding:10px; background-color:#3C5B9B; border-radius:3px 3px 0 0; }
.sms_use { padding-top:10px; text-align:center; }
.sms_error { color:red; }
</style>

<div class="tbl_frm01 tbl_wrap tbl_head02" style="background-color:white;">

	<form name="fmember" id="fmember" action="/adm/email_all_send.php" method="get" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="sh_term" id="sh_term" value="<?=$sh_term?>">

	<!-- 검색영역 START -->
	<div style="padding-top:10px;margin-bottom:10px;">
		<select name="product_idx" id="product_idx" alt="투자상품리스트" onChange="document.fmember.submit();" class="form-control" style="width:500px;font-size:12px">
			<option value="">::투자상품선택::</option>
			<?
			//$res = sql_query("SELECT idx, title, start_date, state FROM cf_product WHERE display='Y' ORDER BY idx DESC, title DESC");
			$res = sql_query("SELECT idx, title, start_date, state FROM cf_product WHERE display='Y' ORDER BY start_num desc");
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

	<div style="margin-bottom:10px;">
		<label class="radio-inline"><input type="radio" name="sh_term" value="all" <?=($sh_term=='all')?'checked':''?>> 전체가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="30" <?=($sh_term=='30')?'checked':''?>> 30일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="60" <?=($sh_term=='60')?'checked':''?>> 60일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="90" <?=($sh_term=='90')?'checked':''?>> 90일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="180" <?=($sh_term=='180')?'checked':''?>> 180일이내 가입자</label>
		<label class="radio-inline"><input type="radio" name="sh_term" value="365" <?=($sh_term=='365')?'checked':''?>> 365일이내 가입자</label>
		<!--
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='all')?'active':''?>" onclick="term_search('all');" style="margin-right:2px;">전체가입자</button>
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='30')?'active':''?>" onclick="term_search('30');" style="margin-right:2px;">30일이내 가입자</button>
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='60')?'active':''?>" onclick="term_search('60');" style="margin-right:2px;">60일이내 가입자</button>
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='90')?'active':''?>" onclick="term_search('90');" style="margin-right:2px;">90일이내 가입자</button>
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='180')?'active':''?>" onclick="term_search('180');" style="margin-right:2px;">6개월이내 가입자</button>
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='365')?'active':''?>" onclick="term_search('365');" style="margin-right:2px;">12개월이내 가입자</button>
		<button type="button" class="btn-default btn btn-sm <?=($sh_term=='imsi')?'active':''?>" onclick="imsi_search();" style="margin-right:2px;">임시</button>
		-->
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
		<li></li>
		<li style="vertical-align:middle;">
			<select name="email_co"  class="form-control input-sm" style="width:120px">
				<option value="">::메일선택::</option>
				<option value="naver" <?if($email_co=='naver')echo'selected';?>>네이버</option>
				<option value="gmail"<?if($email_co=='gmail')echo'selected';?>>구글</option>
				<option value="daum" <?if($email_co=='daum')echo'selected';?>>다음/카카오</option>
				<option value="nate" <?if($email_co=='nate')echo'selected';?>>네이트</option>
			</select>
		</li>
		<li></li>
		<li style="vertical-align:middle;">
			<label class="checkbox-inline"><input type="checkbox" name="mb_10" value="1" <?=($mb_10)?'checked':'';?>> 직원</label>
		</li>
		<li></li>
		<li style="vertical-align:middle;">
			<select class="form-control input-sm" name="mailling">
				<option value="allower" <?=($mailling=='allower')?'selected':''?>>수신허용설정자</option>
				<option value="refuser" <?=($mailling=='refuser')?'selected':''?>>수신거부설정자</option>
				<option value="all" <?=($mailling=='all')?'selected':''?>>전체(동의여부무관)</option>
			</select>
			<!--<label class="checkbox-inline"><input type="checkbox" name="allsend" value="Y" <?=($allsend)?'checked':'';?>> 비수신 설정자 포함</label>-->
		</li>
		<li></li>
		<li style="vertical-align:middle;">가입일:</li>
		<li style="vertical-align:middle;"><input type="text" name="start_date" value="<?=$start_date?>" class="form-control input-sm datepicker" style="width:100px;" <?=($sh_term && $sh_term=='all')?'':'disabled'?>></li>
		<li style="vertical-align:middle;">~</li>
		<li style="vertical-align:middle;"><input type="text" name="end_date" value="<?=$end_date?>" class="form-control input-sm datepicker" style="width:100px" <?=($sh_term && $sh_term=='all')?'':'disabled'?>></li>
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
		<li style="vertical-align:middle;"><input type="submit" class="btn btn-sm btn-warning" value="검색"></li>
	</ul>
	<!-- 검색영역 END -->
	</form>

	<form name="femail" id="femail">
		<table>
			<caption><?=$g5['title']; ?></caption>
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th scope="row" style="text-align:center;"><label for="email_history">과거메일 불러오기</label></th>
					<td>
						<ul class="col-sm-10 list-inline" style="padding:0 0 0 5px;margin:0">
							<li style="padding:0;">
								<select name="email_history" id="email_history" class="form-control">
									<option value="">선택하세요</option>
									<option value="1">>>> 메일 표준 레이아웃 호출</option>
<?
// 과거메일 리스트
$sql2 = "SELECT * FROM g5_mailling_list WHERE idx>'1' ORDER BY regdate DESC, email_title ASC";
$res2 = sql_query($sql2);
for($i=0; $row2=sql_fetch_array($res2); $i++) {
	echo '<option value="'.$row2['idx'].'">['.substr($row2['regdate'],0,16).'] '.$row2['email_title'].'</option>' . PHP_EOL;
}
?>
								</select>
							</li>
							<li style="padding:0;"><button type="button" class="btn btn-success" onclick="show_history();">메일선택</button></li>
							<li style="padding:0;"><button type="button" class="btn btn-info" onclick="reset_history();">선택취소</button></li>
							<li style="padding:0;"><button type="button" class="btn btn-danger" onclick="delete_history();">메일삭제</button></li>
						</ul>
					</td>
				</tr>

				<tr>
					<th scope="row" style="text-align:center;"><label for="email_title">메일링제목</label></th>
					<td>
						<input type="text" name="email_title" id="email_title" value="" class="form-control required">
					</td>
				</tr>

				<tr>
					<th scope="row" style="text-align:center;"><label for="email_contents">내용</label></th>
					<td>
						<?=editor_html('email_contents', ''); ?>
					</td>
				</tr>
				<tr>
					<th scope="row" style="text-align:center;"><label for="regist_number">메일 발송회원</label></th>
					<td>
						<div style="width:100%;height:300px;padding:0; border:1px solid #ccc; overflow-y:scroll;">

							<table class="table_hover" style="width:98%;margin:1%;">
								<caption><?=$g5['title']; ?> 목록</caption>
								<thead>
								<tr>
									<th scope="col" style="text-align:center;"><label><input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)"></label></th>
									<th scope="col" style="text-align:center;">번호</th>
									<th scope="col" style="text-align:center;">아이디</th>
									<th scope="col" style="text-align:center;">성명/법인명</th>
									<th scope="col" style="text-align:center;">이메일</th>
									<th scope="col" style="text-align:center;">메일수신설정</th>
									<th scope="col" style="text-align:center;">등록일</th>
								</tr>
								</thead>
								<tbody style="font-size:12px;">
<?
if($list_count) {
	$num = $list_count;
	for($i=0,$j=$num; $i<$list_count; $i++,$j--) {

		$print_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];
		$mb_mail_txt = ($LIST[$i]['mb_mailling']=='1') ? "허용" : "";
		$send_val = $LIST[$i]['mb_email'].'^'.$print_name;

		echo "
								<tr align=\"center\"><!--onClick=\"on_checkbox('$i')\"-->
									<td headers=\"mb_list_chk\" class=\"td_chk\"><input type=\"checkbox\" id=\"checkbox_{$i}\" name=\"chk[]\" value=\"".$send_val."\"></td>
									<td>".number_format($j)."</td>
									<td>".$LIST[$i]['mb_id']."</td>
									<td>".$print_name."</td>
									<td>".$LIST[$i]['mb_email']."</td>
									<td>".$mb_mail_txt."</td>
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

		<div class="text-center" style="margin-top:25px;">
			<button type="button" id="mailSend" class="btn btn-md btn-success" style="width:100px">전송</button>
			<button type="button" class="btn btn-md btn-default" onclick="fmail_reset();" style="width:100px">취소</button>
		</div>
	</form>

</div>

<div id="actionDiv" style="display:none;width:500px;height:300px;right:1px; bottom:0; position:fixed;z-index:10001">
	<iframe id="axFrameX" name="axFrameX" src="about:blank" frameborder="0" style="width:100%;height:100%;border:1px solid red;background-color:#EFEFEF"></iframe>
</div>

<script>
$('#mailSend').click(function() {

	if($('#email_title').val()=='') {
		alert('제목을 입력 하십시요!');
	}
	else {
		var f = document.femail;
		if(confirm('대량발송의 경우, 처리시간이 길어질수 있습니다.\n\n완료 메세지가 나올때까지 기다려주세요.\n\n선택된 회원들에게 메일발송을 하시겠습니까?')) {

			$('#mailSend').text('전송중>>'); $('#mailSend').attr('disabled','disabled');
			loading('on');
			$('#actionDiv').css('display','block');

			<?=get_editor_js('email_contents')?>
			f.action = '/adm/email_all_send_proc.php';
			f.method = 'post';
			f.enctype = 'multipart/form-data';
			f.target = 'axFrameX';
			f.submit();
		}
	}
});
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

// 과거메일 불러오기
function show_history() {
	var val = $('#email_history').val();
	$.ajax({
		url:'/adm/email_history_list.php',
		type:'post',
		data:{ 'idx':val }
	}).done(function(data) {
		$('#email_title').val($("#email_history option:selected").text());
		changeEditorHTML('email_contents', data);
	});
}

function editerPasteHTML(obj,str) { // 에디터에 내용 삽입
  var sHTML= str;
  oEditors.getById[obj].exec("PASTE_HTML", [sHTML]);
}

function editerResetHTML(obj) { // 에디터 내용 초기화
  oEditors.getById[obj].exec("SET_CONTENTS", [""]);  // 내용초기화
}

function changeEditorHTML(obj,str) {
	var sHTML= str;
	oEditors.getById[obj].exec("SET_CONTENTS", [""]);  // 내용초기화
  oEditors.getById[obj].exec("PASTE_HTML", [sHTML]);
}

// 선택취소
function reset_history() {
	$('#email_title').val('');
	editerResetHTML('email_contents');
}

// 과거메일 삭제
function delete_history() {
	if(confirm('정말 삭제하시겠습니까??')) {
		var val = $('#email_history').val();
		$.ajax({
			url:'/adm/email_history_delete.php',
			type:'post',
			data:{
				'idx':val
			}
		}).done(function(data) {
			$("#email_history option:selected").remove();
		});
	}
}

// 폼 리셋
function fmail_reset() {
	$("form")[1].reset();
}
</script>


<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
