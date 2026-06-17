<?
$sub_menu = '800520';
include_once('./_common.php');

$link3      = sql_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3);
$select_db3 = sql_select_db(G5_MYSQL_DB3, $link3) or die('MySQL DB Error!!!');
sql_set_charset('utf8', $link3);

auth_check($auth[$sub_menu], "w");

$html_title = "회원 SMS 발송 (SMTNT)";
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) { ${$_GET[$k]} = $v; }


$MSG_RESULT_CODE = array(
	'0' => '성공',
	'1' => 'API 버전오류',
	'2' => '인증실패',
	'3' => 'BIND 미수행',
	'4' => '호스팅 시스템 내부오류',
	'5' => '메시지 형식 오류',
	'6' => '유효기간 만료',
	'7' => '결번',
	'8' => '단말기 Power Off',
	'9' => '음영',
	'10' => '전송건수 초과',
	'11' => '전송속도 초과',
	'12' => '번호이동',
	'13' => 'NPDB 불일치',
	'14' => '호 처리 실패',
	'15' => '단말기 전송 실패',
	'16' => '파일이 없음',
	'17' => '키사 스팸 차단',
	'18' => '전달 메시지 없음',
	'19' => '입력 데이터 오류',
	'20' => '메시지 저장개수 초과',
	'21' => '잘못된 파라메터',
	'22' => '발신 프로필 키가 유효하지 않음',
	'23' => '발신 프로필을 찾을 수 없음',
	'24' => '삭제된 발신프로필',
	'25' => '차단 상태의 발신프로필',
	'26' => '차단 상태의 옐로아이디',
	'27' => '닫힘 상태의 옐로아이디',
	'28' => '삭제 상태의 옐로아이디',
	'29' => '메시지 전송 실패',
	'30' => '템필릿 일치 확인 시 오류',
	'31' => '메시지 수신확인 안됨',
	'32' => '내부 시스템 오류',
	'33' => '전화번호 오류',
	'35' => '메시지 길이 제한 오류',
	'36' => '템플릿을 찾을 수 없음',
	'37' => '메시지에 포함된 이미지를 전송할 수 없음',
	'315' => '세칙 미준수',
	'316' => '발신번호 미등록',
	'800' => '발송메시지에 허용 되지 않은 문구가 포함됨',
	'900' => '대량전송 동보갯수 초과',
	'901' => '발송 가능시간이 아님',
	'902' => '로그인 상태가 아님',
	'903' => '발송속도 초과',
	'904' => '월 제한건수 초과',
	'905' => '일 제한건수 초과',
	'906' => '보유 금액 부족',
	'907' => '시스템 과부하',
	'908' => '특정필드가 허용된 길이를 초과함',
	'909' => '첨부파일 파일갯수 오류',
	'910' => '첨부파일 크기 오류',
	'911' => '수신데이터 오류입니다.',
	'997' => '자체 스팸 차단',
	'998' => '분배정보 없음',
	'999' => '기타 오류'
);
$MSG_RESULT_CODE_KEY = array_keys($MSG_RESULT_CODE);


if(!$msgtype) $msgtype = 'mms';
if(!in_array($msgtype, array('mms','sms'))) { alert('!!!!'); }
if(!$target_period) $target_period = 'now';


$db_table = ($target_period=='now') ? 'Msg_Tran' : 'Msg_Log_'.preg_replace("/-/", "", $reqdateM);

if ($db_table=="Msg_Log_") {
	$reqdateM = date("Y-m");
	$db_table = $db_table.date("Ym");
}

$where = " 1=1 ";

$where.= ($msgtype=='mms') ? " AND Msg_Type='6' " : " AND Msg_Type='4' ";
//if($isReserved) $where.= " AND isReserved='$isReserved' ";
if($date_field && $reqdateM) {
	if( $reqdateM < date('Y-m') ) {
		//$db_table = 'agent_msgresult_' . preg_replace("/-/", "", $reqdateM);
		$db_table = 'Msg_Log_' . preg_replace("/-/", "", $reqdateM);
	}
	else {
		$where.= " AND LEFT($date_field, 7)='$reqdateM' ";
	}
}
if($receiveNo) $where.= " AND Phone_No LIKE '%$receiveNo%' ";
if($cont) $where.= " AND message LIKE '%$cont%' ";
if($callback_no) $where.= " AND Callback_No='$callback_no' ";

if(isset($result_code)) {
	if($result_code==='0') {
		$where.= " AND Result='0'";
	}
	else if($result_code=='ERROR_ALL') {
		$where.= " AND Result<>'0'";
	}
	else {
		if($result_code<>0) $where.= " AND Result='$result_code'";
	}
}




//$order = 'Msg_Id';
$order = "Save_Time";

$row = sql_fetch("SELECT COUNT(*) AS cnt FROM $db_table WHERE $where", G5_DISPLAY_SQL_ERROR, $link3);
$total_count = $row['cnt'];
$page_rows   = 100;
$total_page  = ceil($total_count / $page_rows);
$page        = ($page) ? $page : 1;
$from_record = ($page - 1) * $page_rows;



$sql  = "
	SELECT
		*
	FROM
		$db_table
	WHERE
		$where
	ORDER BY
		$order DESC
	LIMIT
		$from_record, $page_rows";
//print_rr($sql);
$res  = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);
$rows = $res->num_rows;

$delete_able = false;
for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);

	if($LIST[$i]['state']>'0') $delete_able = true;
}
$list_count = count($LIST);

if($target_period=='now') {
	if($delete_able) $select_delete_button = '<button type="button" id="select_delete" class="btn btn-sm btn-danger" style="width:120px">선택삭제</button>';
	if($total_count) $search_delete_button = '<button type="button" id="search_delete" class="btn btn-sm btn-danger" style="width:120px">조회분삭제</button>';
}

?>

<style>
table {border-collapse:collapse; font-size:12px}
.content .tabX { height:42px; background:url('/images/tab_bg.gif') repeat-x left bottom; }
.content .tabX li { float:left; width:200px; margin-right:3px; line-height:40px; text-align:center; font-size:16px; color:#202020; background-color:#f7f7f7; border:1px solid #e5e5e5; border-bottom:0; cursor:pointer; }
.content .tabX li.on { border:1px solid #ccc; background-color:#fff; border-bottom-color:#fff; }
.content .tabX li:last-child { margin:0; display:inline-block; }
.content .tabXarea { display:block;margin:0; padding:20px; min-height:400px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
#cont > div     { line-height:16px; padding:0; font-size:12px; }
#cont > div.off { height:17px; overflow:hidden; color:'' }
#cont > div.on  { color:#3366FF }
</style>
<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

<?
$rsv_sql = "select count(idx) as cou from cf_Msg_Tran where moved='N' and deleted<>'Y'";
$rsv_res = sql_query($rsv_sql, G5_DISPLAY_SQL_ERROR , $link3);
$rsv_row = sql_fetch_array($rsv_res);
$rsv_cnt = $rsv_row['cou'];
?>

<div class="tbl_head02 tbl_wrap" >
	<div class="content" style="padding-top:10px;margin-bottom:10px;">
		<ul class="tabX" style="width:100%;list-style:none;padding-left:20px;margin:0;">
			<li onClick="location.href='?msgtype=mms'" <?=($msgtype=='' || $msgtype=='mms')?'class="on"':''?>>MMS</li>
			<li onClick="location.href='?msgtype=sms'" <?=($msgtype=='sms')?'class="on"':''?>>SMS</li>
		</ul>
		<div class="tabXarea">

			<form name="form1" id="form1" method="get">
				<input type="hidden" name="msgtype" value="<?=$msgtype?>">
				<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select id="target_period" name="target_period" class="form-control input-sm">
							<option value="now" <?=($target_period=='now')?'selected':''?>>현재처리중</option>
							<option value="old" <?=($target_period=='old')?'selected':''?>>처리완료</option>
						</select>
					</li>
					<li>
						<select id="isReserved" name="isReserved" class="form-control input-sm">
							<option value="">::발송구분::</option>
							<option value="Y" <?=($isReserved=='Y')?'selected':''?>>예약발송</option>
							<option value="N" <?=($isReserved=='N')?'selected':''?>>즉시발송</option>
						</select>
					</li>
					<li></li>
					<li>수신번호:</li>
					<li><input type="text" name="receiveNo" value="<?=$receiveNo?>" onKeyUp="onlyDigit(this);" class="form-control input-sm" style="width:120px"></li>
					<li></li>
					<li>발신번호:</li>
					<li>
						<select name="callback_no" class="form-control input-sm">
							<option value=""></option>
							<option value="15886760" <?=$callback_no=="15886760"?"selected":""?> >1588-6760</option>
							<option value="15885210" <?=$callback_no=="15885210"?"selected":""?> >1588-5210</option>
						</select>
					</li>
					<li>메세지내용:</li>
					<li><input type="text" name="cont" value="<?=$cont?>" class="form-control input-sm" style="width:300px"></li>
				</ul>
				<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select name="result_code" class="form-control input-sm">
							<option value="">::발송결과선택::</option>
<?
for($i=0; $i<count($MSG_RESULT_CODE); $i++) {
	$selected = ((string)$MSG_RESULT_CODE_KEY[$i]===$result_code) ? 'selected' : '';

	$fcolor = ($i > 0) ? '#FF2222' : '#3366FF';

	echo "<option value='".$MSG_RESULT_CODE_KEY[$i]."' style='color:$fcolor' $selected>".$MSG_RESULT_CODE_KEY[$i].": ".$MSG_RESULT_CODE[$MSG_RESULT_CODE_KEY[$i]]."</option>\n";

}
?>
							<option value="ERROR_ALL" <?=($result_code=='ERROR_ALL')?'selected':'';?> style="color:brown">오류 전체</option>
						<select>
					</li>
					<li style="float:right;">
						<button type="button" id="smtnt_admin" class="btn btn-sm btn-success" style="width:120px;margin-left:0px;" onclick="go_smtnt_admin();">SMTNT Admin</button>
					</li>
				</ul>
				<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li style="float:left;">
						<select name="date_field" class="form-control input-sm" style="width:160px">
							<option value="">::데이트필드::</option>
							<option value="Send_Time" <?=($date_field=='Send_Time')?'selected':'';?>>발송일</option>
							<option value="Save_Time" <?=($date_field=='Save_Time')?'selected':'';?>>등록일</option>
						</select>
					</li>
					<li style="float:left;">
						<select id="reqdateM" name="reqdateM" class="form-control  input-sm">
							<option value="">::대상월선택::</option>
<?
$ini_date = '2020-05-01';
$i = 0;
$x = true;
while($x > 0) {
	$yearmonth = date("Y-m", strtotime("first day of " . $ini_date . " + $i month"));
	$selected = ($yearmonth==$reqdateM) ? "selected" : "";
	echo "<option value='".$yearmonth."' $selected>".$yearmonth."</option>\n";

	if( $yearmonth == date('Y-m') ) break;
	$i++;
}
?>
						</select>
					</li>
					<li style="float:left;"></li>
					<li style="float:left;">
						<button type="submit" class="btn btn-warning btn-sm">검색</button>
						<? if($_REQUEST["jtest"]=="Y" || 2>1) { ?>
						<button type="button" id="search_reserve" class="btn btn-success btn-sm" style="width:140px;margin-left:10px;">예약현황 <?=$rsv_cnt?" ($rsv_cnt)":""?></button>
						<? } ?>
					</li>
					<li style="float:right;"><?=$select_delete_button?> <?=$search_delete_button?></li>
				</ul>
			</form>

			<table class="table-striped table-hover">
				<form id="form2" style="padding:0;">
				<colgroup>
					<col style="width:3%;">
					<col style="width:5%;">
					<col style="width:9%">
					<col style="width:9%">
					<col style="width:9%">
					<col style="width:9%">
					<col style="width:%">
					<col style="width:6%">
					<col style="width:6%">
					<col style="width:6%">
					<col style="width:9%">
				</colgroup>
				<thead>
				<tr align="center">
					<th><input type="checkbox" id="chkall" value="1"></th>
					<th>NO.</th>
					<th>발송구분</th>
					<th>예약일시</th>
					<th>처리일시</th>
					<th>수신번호</th>
					<th>내용</th>
					<th>발송요청상태</th>
					<th>발송결과</th>
					<th>작업구분</th>
					<th>등록일시</th>
				</tr>
				</thead>
				<tbody>
<?
$num = $total_count - $from_record;

if($num > 0) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$print_reserved = ($LIST[$i]['isReserved']=='Y') ? "예약발송" : "즉시발송";

		// 발송상태
		switch($LIST[$i]['Status']) {
			case '0' : $print_state = "발송요청";   break;
			case '1' : $print_state = "전송중";     break;
			case '2' : $print_state = "결과대기";   break;
			case '3' : $print_state = "완료";       break;
			default  : $print_state = "미전송";     break;
		}
		//$state_color = ($LIST[$i]['state']<'1') ? 'red' : '';

		$print_result = '';
		$result_color = '';
		// 발송결과
		if($LIST[$i]['Result']=='0') {
			$print_result = $MSG_RESULT_CODE['S'];
		}
		else {
			if($LIST[$i]['Result']) {
				$print_result = $MSG_RESULT_CODE[$LIST[$i]['Result']];
				$result_color = '#FF2222';
			}
			else {
				$print_result = "결과 대기중";
			}
		}


		$dev_gubun = ($LIST[$i]['etc1']=='dev') ? '테스트' : '서비스';
?>
				<tr align="center">
					<td><? if($LIST[$i]['state']>'0'){ ?><input type="checkbox" id="chk<?=$i?>" name="chk[]" value="<?=$LIST[$i]['id']?>"><? } ?></td>
					<td><?=number_format($num)?></td>
					<td><?=$print_reserved?></td>
					<td><?=substr($LIST[$i]['Save_Time'], 0, 16)?></td>
					<td><?=substr($LIST[$i]['Send_Time'], 0, 16)?></td>
					<td><?=$LIST[$i]['Phone_No']?></td>
					<td align="left" id="cont"><div id="cont_<?=$i?>" class="off" onClick="onoffzone('cont_<?=$i?>')" style="width:100%;cursor:pointer"><?=nl2br($LIST[$i]['Message'])?></div></td>
					<td><span style="color:<?=$state_color?>"><?=$print_state?></span></td>
					<td><span style="color:<?=$result_color?>"><?=$print_result?></span></td>
					<td><?=$dev_gubun?></td>
					<td><?=substr($LIST[$i]['Save_Time'], 0, 16)?></td>
				</tr>
<?
		$num--;
	}
}
else {
	echo '
				<tr align="center" style="background:#F8F8EF">
					<td colspan="11">등록된 데이터가 없습니다.</td>
				</tr>
	' . PHP_EOL;
}
?>
				</tbody>
			</table>

			<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

		</div>
	</div>
</div>

<script>
$("#chkall").click(function() {
	$("input[name='chk[]']").prop('checked', this.checked);
});

$("#select_delete").on('click', function() {
	var fdata = $('#form2').serialize();
	if(fdata) {
		if(confirm('선택된 메세지건에 대하여 삭제 처리 하시겠습니까?')) {
			$.ajax({
				url : "aaa_sms_schedule_proc.php",
				type: "POST",
				data: 'mode=select_delete&msgtype=<?=$msgtype?>&' + fdata,
				success: function(data) {
					$('#ajax_return_txt').val(data);
					alert(data)
					//document.location.reload();
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
	else {
		alert('선택된 항목이 없습니다.');
	}
});

$("#search_delete").on('click', function() {
	var fdata = $('#form1').serialize();
	if(confirm('조회된 건에 대하여 삭제 처리 하시겠습니까?')) {
		$.ajax({
			url : "aaa_sms_schedule_proc.php",
			type: "POST",
			data: 'mode=search_delete&msgtype=<?=$msgtype?>&' + fdata,
			success: function(data) {
				$('#ajax_return_txt').val(data);
				alert(data)
				//document.location.reload();
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
});

$("#search_reserve").on('click', function() {
	self.location.href = "smtnt_sms_reserve_list.php";
});

$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['PHP_SELF']?>'
					+ '?msgtype=<?=$msgtype?>'
	        + '&target_period=<?=$target_period?>'
					+ '&isReserved=<?=$isReserved?>'
					+ '&receiveNo=<?=$receiveNo?>'
					+ '&cont=<?=urlencode($cont)?>'
					+ '&date_field=<?=$date_field?>'
					+ '&reqdateM=<?=$reqdateM?>'
	        + '&page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});

onoffzone = function(obj) {
	$obj = $('#'+obj);
	existClass = $obj.attr('class');
	$obj.removeClass(existClass);
	if(existClass=='off') $obj.addClass('on');
	if(existClass=='on') $obj.addClass('off');
}

function go_smtnt_admin() {
	window.open("http://manager.msgagent.com","SMTNT_ADMIN");
}
</script>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>