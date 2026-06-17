<?
$sub_menu = '800500';
include_once('./_common.php');

$link3      = sql_connect(G5_MYSQL_HOST3, G5_MYSQL_USER3, G5_MYSQL_PASSWORD3, G5_MYSQL_DB3);
$select_db3 = sql_select_db(G5_MYSQL_DB3, $link3) or die('MySQL DB Error!!!');
sql_set_charset('utf8', $link3);

function update_sms_res() {
	global $link3;

	echo "update_sms_res ing<br/>";

	$sql = "select * from cf_agent_msgqueue where state<>'3'";

	$res = sql_query($sql,G5_DISPLAY_SQL_ERROR,$link3);
	$cnt = sql_num_rows($res);

	$agent_table = "agent_msgqueue_test";
	$agentresult_table = "agent_msgresult";

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		$sql2 = "select * from $agentresult_table where id = '$row[id]'";
		$res2 = sql_query($sql2, G5_DISPLAY_SQL_ERROR , $link3);
		$cnt2 = sql_num_rows($res2);

		unset($row2);
		if ($cnt2) {
			$row2 = sql_fetch_array($res2);
		} else {
			$sql2 = "select * from $agent_table where id = '$row[id]'";
			$res2 = sql_query($sql2, G5_DISPLAY_SQL_ERROR , $link3);
			$cnt2 = sql_num_rows($res2);
			if ($cnt2) {
				$row2 = sql_fetch_array($res2);
			}
		}

		if (isset($row2)) {
			$new_state = $row2['state'];
			$new_result = $row2['result'];
			$new_sendTime = $row2['sendTime'];
			$new_responseTime = $row2['responseTime'];
			$new_resultTime = $row2['resultTime'];

			$up_sql = "update cf_agent_msgqueue set 
							state = '$new_state',
							result = '$new_result',
							sendTime = '$new_sendTime',
							responseTime = '$new_responseTime',
							resultTime = '$new_resultTime'
						where idx = '$row[idx]'";

			sql_query($up_sql, G5_DISPLAY_SQL_ERROR , $link3);
			//echo "$up_sql<br/>";
		}
	}
	
}
update_sms_res();

auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu800'][6][1];
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) { ${$_GET[$k]} = $v; }

if(!$msgtype) $msgtype = 'mms';
if(!in_array($msgtype, array('mms','sms'))) { alert('!!!!'); }

if(!$target_period) $target_period = 'now';

//$db_table = 'agent_msgresult';
//$db_table = ($target_period=='now') ? 'agent_msgqueue' : 'agent_msgresult';
$db_table = 'cf_agent_msgqueue';

$where = " 1=1 ";
$where.= ( preg_match('/dev\.hello/i', $_SERVER['HTTP_HOST']) ) ? " AND etc1='dev' " : "";
$where.= ($msgtype=='mms') ? " AND kind='1' " : " AND kind='0' ";
if($isReserved) $where.= " AND isReserved='$isReserved' ";
if($date_field) {
	if($reqdateS) $where.= " AND LEFT($date_field, 10)>='$reqdateS' ";
	if($reqdateE) $where.= " AND LEFT($date_field, 10)<='$reqdateE' ";
}
if($receiveNo) $where.= " AND receiveNo LIKE '%$receiveNo%' ";
if($cont) $where.= " AND message LIKE '%$cont%' ";

//$order = 'id';
$order = 'idx';
$row = sql_fetch("SELECT COUNT(*) AS cnt FROM $db_table WHERE $where", G5_DISPLAY_SQL_ERROR, $link3);
$total_count = $row['cnt'];
$page_rows   = 100;
$total_page  = ceil($total_count / $page_rows);
$page        = ($page) ? $page : 1;
$from_record = ($page - 1) * $page_rows;



$sql  = "SELECT * FROM $db_table WHERE $where ORDER BY $order DESC LIMIT $from_record, $page_rows";
//echo $sql;
$res  = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link3);
$rows = sql_num_rows($res);

$delete_able = false;
for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);

	if($LIST[$i]['state']>'0') $delete_able = true;
}
$list_count = count($LIST);

if($target_period=='now') {
	if($delete_able) $select_delete_button = '<button type="button" id="select_delete" class="btn btn-danger" style="width:120px">선택삭제</button>';
	if($total_count) $search_delete_button = '<button type="button" id="search_delete" class="btn btn-danger" style="width:120px">조회분삭제</button>';
}

?>

<link href="/adm/css/bootstrap.min.css" rel="stylesheet">
<link href="/adm/css/jquery-ui.min.css" rel="stylesheet">
<script src="/adm/js/jquery-ui.min.js"></script>
<script src="/adm/js/jquery.form.js"></script>

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

<div class="tbl_head02 tbl_wrap">
	<div class="content" style="margin:30px auto">
		<ul class="tabX" style="width:100%;list-style:none;padding-left:20px;margin:0;">
			<li onClick="location.href='?msgtype=mms'" <?=($msgtype=='' || $msgtype=='mms')?'class="on"':''?>>MMS</li>
			<li onClick="location.href='?msgtype=sms'" <?=($msgtype=='sms')?'class="on"':''?>>SMS</li>
		</ul>
		<div class="tabXarea">

			<form name="form1" id="form1" method="get">
				<input type="hidden" name="msgtype" value="<?=$msgtype?>">
				<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select id="target_period" name="target_period" class="form-control">
							<option value="now" <?=($target_period=='now')?'selected':''?>>현재처리중</option>
							<option value="old" <?=($target_period=='old')?'selected':''?>>처리완료</option>
						</select>
					</li>
					<li>
						<select id="isReserved" name="isReserved" class="form-control">
							<option value="">::발송구분::</option>
							<option value="Y" <?=($isReserved=='Y')?'selected':''?>>예약발송</option>
							<option value="N" <?=($isReserved=='N')?'selected':''?>>즉시발송</option>
						</select>
					</li>
					<li></li>
					<li>수신번호:</li>
					<li><input type="text" name="receiveNo" value="<?=$receiveNo?>" onKeyUp="onlyDigit(this);" class="form-control" style="width:120px"></li>
					<li></li>
					<li>메세지내용:</li>
					<li><input type="text" name="cont" value="<?=$cont?>" class="form-control" style="width:300px"></li>
				</ul>
				<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li style="float:left;"><select name="date_field" class="form-control" style="width:160px">
							<option value="">::데이트필드::</option>
							<option value="reservedTime" <?=($date_field=='reservedTime')?'selected':'';?>>예약일</option>
							<option value="sendTime" <?=($date_field=='sendTime')?'selected':'';?>>발송일</option>
							<option value="registTime" <?=($date_field=='registTime')?'selected':'';?>>등록일</option>
						</select>
					</li>
					<li style="float:left;"><input type="text" name="reqdateS" value="<?=($date_field)?$reqdateS:'';?>" class="form-control datepicker" style="width:100px;"></li>
					<li style="float:left;">~</li>
					<li style="float:left;"><input type="text" name="reqdateE" value="<?=($date_field)?$reqdateE:'';?>" class="form-control datepicker" style="width:100px"></li>
					<li style="float:left;"></li>
					<li style="float:left;"><button type="submit" class="btn btn-warning">검색</button></li>
					<li style="float:left;"><?=$select_delete_button?> <?=$search_delete_button?> 
						<button type="button" id="search_cancel" class="btn btn-danger" style="width:120px">예약취소</button>
					</li>
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
					<th>발송상태</th>
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
		switch($LIST[$i]['state']) {
			case '1' : $print_state = "전송";    break;
			case '2' : $print_state = "접수";    break;
			case '3' : $print_state = "완료";    break;
			default  : $print_state = "미전송";  break;
		}
		$state_color = ($LIST[$i]['state']<'1') ? 'red' : '';

		// 발송결과
		switch($LIST[$i]['result']) {
			case '0'  : $print_result = "성공";    break;
			case '-1' : $print_result = "미처리";  break;
			default   : $print_result = "실패";    break;
		}
		$result_color = ($LIST[$i]['result']<>'0') ? 'red' : '';

		$dev_gubun = ($LIST[$i]['etc1']=='dev') ? '테스트' : '서비스';
?>
				<tr align="center">
					<td>
						<? if($LIST[$i]['state']>'100'){ ?><input type="checkbox" id="chk<?=$i?>" name="chk[]" value="<?=$LIST[$i]['id']?>"><? } ?>
						<input type="checkbox" id="chk<?=$i?>" name="chk[]" value="<?=$LIST[$i]['id']?>">
					</td>
					<td><?=number_format($num)?></td>
					<td><?=$print_reserved?></td>
					<td><?=substr($LIST[$i]['reservedTime'], 0, 16)?></td>
					<td><?=substr($LIST[$i]['sendTime'], 0, 16)?></td>
					<td><?=$LIST[$i]['receiveNo']?></td>
					<td align="left" id="cont"><div id="cont_<?=$i?>" class="off" onClick="onoffzone('cont_<?=$i?>')" style="width:100%;cursor:pointer"><?=nl2br($LIST[$i]['message'])?></div></td>
					<td><span style="color:<?=$state_color?>"><?=$print_state?></span></td>
					<td><span style="color:<?=$result_color?>"><?=$print_result?></span></td>
					<td><?=$dev_gubun?></td>
					<td><?=substr($LIST[$i]['registTime'], 0, 16)?></td>
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
				url : "sms_schedule_proc.php",
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
			url : "sms_schedule_proc.php",
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

$("#search_cancel").on('click', function() {
	var yn = confirm('조회된 건에 대하여 취소 처리 하시겠습니까?');
	if (yn){
		var fdata = $('#form2').serialize();
		console.log(fdata);
		$.ajax({
			url  : "sms_schedule_proc.php",
			type : "post",
			data : 'mode=search_cancel&msgtype=<?=$msgtype?>&' + fdata,
			success : function(data) {
				$('#ajax_return_txt').val(data);
				alert(data);
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
		});
	}
});


$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['PHP_SELF']?>'
					+ '?msgtype=<?=$msgtype?>'
	        + '&target_period=<?=$target_period?>'
					+ '&isReserved=<?=$isReserved?>'
					+ '&receiveNo=<?=$receiveNo?>'
					+ '&cont=<?=urlencode($cont)?>'
					+ '&date_field=<?=$date_field?>'
					+ '&reqdateS=<?=$reqdateS?>'
					+ '&reqdateE=<?=$reqdateE?>'
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
</script>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>