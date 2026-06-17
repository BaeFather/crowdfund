<?

include_once("_common.php");

/*
참조
(구)쿠폰DB : hloan_cupoint_reg 참조
(구)쿠폰발송로그 테이블 : hloan_partner_event_log 참조
*/

include_once("_common.php");

$res = sql_query("SELECT * FROM {$TABLE['event_config']} ORDER BY idx");
while( $row = sql_fetch_array($res) ) {
	$EVENT[$row['event_no']] = $row;
}
sql_free_result($res);


if($event_no) $TARGET_EVENT = $EVENT[$event_no];
//print_rr($TARGET_EVENT);

$where = "1=1";
if($event_no) $where.= " AND A.event_no = '".$event_no."'";


if($state) {
	if($state=='null') {
		$where.= " AND A.approved='' AND A.paid='' AND A.invalid=''";
	}
	else {
		if($state=='invalid')       $where.= " AND A.invalid='1'";
		else if($state=='approved') $where.= " AND A.approved='1'";
		else if($state=='paid')     $where.= " AND A.paid='1'";
	}
}
if($date_field) {
	if($sdate) $where.= " AND LEFT($date_field, 10)>='".$sdate."'";
	if($edate) $where.= " AND LEFT($date_field, 10)<='".$edate."'";
}
if($field && $keyword) {
	$where.= ($field=='B.mb_hp') ? " AND $field = '".masterEncrypt($keyword, false)."'" : " AND $field = '".$keyword."'";
}


$total_count = sql_fetch("
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		cf_partner_event_reward_log A
		LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE
		$where")['cnt'];
$rows = ($mode=='download') ? $total_count : 50;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", idx DESC";
}
else {
	if($TARGET_EVENT['idx']) {
		$sort_field = 'invest_amount';
		$sort = 'DESC';
		$sql_order.= $sort_field." ".$sort.", idx DESC";
	}
	else {
		$sql_order.= " A.idx DESC";
	}
}


$sql = "
	SELECT
		A.*,
		(SELECT give_dt FROM cf_partner_coupon_bank WHERE event_no=A.event_no AND coupon_serial_no=A.coupon_serial_no) AS coupon_set_dt,
		(SELECT sms_dt FROM cf_partner_coupon_bank WHERE event_no=A.event_no AND coupon_serial_no=A.coupon_serial_no) AS coupon_send_dt,
		B.mb_id, B.mb_hp, B.pid, B.mb_datetime, B.mb_level, B.mb_ci,
		CF.sdate, CF.edate, CF.invest_prdt_ca,
		IF(B.member_type='2', mb_co_name, mb_name) AS mb_title,
		(
			SELECT
				COUNT(AA.idx)
			FROM
				cf_product_invest AA
			LEFT JOIN
				cf_product BB ON AA.product_idx=BB.idx
			WHERE 1
				AND AA.invest_state='Y' AND AA.member_idx=A.member_idx
				AND AA.insert_date BETWEEN CF.sdate AND CF.edate
				AND (
					CASE
						WHEN CF.invest_prdt_ca='' THEN (BB.category != '')
						WHEN CF.invest_prdt_ca='2-1' THEN (BB.category = '2' AND BB.mortgage_guarantees = '')
						WHEN CF.invest_prdt_ca='2-2' THEN (BB.category = '2' AND BB.mortgage_guarantees = '1')
						ELSE BB.category = CF.invest_prdt_ca
					END
				)
		) AS invest_count,
		(
			SELECT
				IFNULL(SUM(AA.amount),0)
			FROM
				cf_product_invest AA
			LEFT JOIN
				cf_product BB ON AA.product_idx=BB.idx
			WHERE 1
				AND AA.invest_state='Y' AND AA.member_idx=A.member_idx
				AND AA.insert_date BETWEEN CF.sdate AND CF.edate
				AND (
					CASE
						WHEN CF.invest_prdt_ca='' THEN (BB.category != '')
						WHEN CF.invest_prdt_ca='2-1' THEN (BB.category = '2' AND BB.mortgage_guarantees = '')
						WHEN CF.invest_prdt_ca='2-2' THEN (BB.category = '2' AND BB.mortgage_guarantees = '1')
						ELSE BB.category = CF.invest_prdt_ca
					END
				)
		) AS invest_amount
	FROM
		cf_partner_event_reward_log A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	LEFT JOIN
		cf_partner_event_config CF  ON A.event_no = CF.event_no
	WHERE
		$where
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $rows";
//if($member['mb_id']=='admin_sori9th') { print_rr($sql); }

$result = sql_query($sql);
$rcount = $result->num_rows;

for($i=0; $i<$rcount; $i++) {

	$LIST[$i] = sql_fetch_array($result);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);

	$LIST[$i]['iam_rwd_target'] = '';
	if($EVENT[$LIST[$i]['event_no']]['invest_rwd_cond']) {
		//echo $LIST[$i]['invest_amount'] ." : " . $EVENT[$LIST[$i]['event_no']]['invest_use_amt'] . "<br>\n";
		if($LIST[$i]['invest_amount'] >= $EVENT[$LIST[$i]['event_no']]['invest_use_amt']) $LIST[$i]['iam_rwd_target'] = '1';
	}

}

$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

if($mode=='download') {
	include("partner_event_reward_list.download.php");
}

?>


<div>

	<div style="border:1px solid #DDD;background:#FAFAFA;width:100%;display:inline-block">
		<form name="f_srch">
			<input type="hidden" name="view" value="reward">
		<ul class="col-sm-10 list-inline" style="margin-top:10px">
			<li><select name="event_no" id="event_no" class="form-control input-sm">
					<option value="">::이벤트 조회::</option>
					<?
					$resx = sql_query("SELECT event_no, event_title FROM cf_partner_event_config WHERE 1 ORDER BY event_no DESC");
					while( $row = sql_fetch_array($resx) ) {
						$selected = ($row['event_no']==$event_no) ? 'selected' : '';
						echo "<option value='".$row['event_no']."' $selected>(" . $row['event_no'] . ") " . $row['event_title'] . "</option>";
					}
					?>
				</select>
			</li>
			<li><select id="state" name="state" class="form-control input-sm">
					<option value="">::확정 및 지급현황::</option>
					<option value="null" <?=($state=='null')?'selected':''?>>미설정</option>
					<option value="approved" <?=($state=='approved')?'selected':''?>>보상확정</option>
					<option value="paid" <?=($state=='paid')?'selected':''?>>지급완료</option>
					<option value="invalid" <?=($state=='invalid')?'selected':''?>>무효</option>
				</select>
			</li>
			<li></li>
			<li><select id="date_field" name="date_field" class="form-control input-sm">
					<option value="">::데이트필드선택::</option>
					<option value="A.rdatetime" <?=($date_field=='A.rdatetime')?'selected':''?>>이벤트참여일</option>
					<option value="A.approved_datetime" <?=($date_field=='B.approved_datetime')?'selected':''?>>보상확정일</option>
					<option value="A.paid_datetime" <?=($date_field=='B.paid_datetime')?'selected':''?>>지급처리일</option>
					<option value="A.invalid_datetime" <?=($date_field=='B.invalid_datetime')?'selected':''?>>무효처리일</option>
				</select>
			</li>
			<li><input type="text" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
			<li><input type="text" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
		</ul>
		<ul class="col-sm-10 list-inline">
			<li><select id="field" name="field" class="form-control input-sm">
					<option value="">::검색항목선택::</option>
					<option value="A.member_idx" <?=($field=='A.member_idx')?'selected':''?>>회원번호</option>
					<option value="B.mb_id" <?=($field=='B.mb_id')?'selected':''?>>아이디</option>
					<option value="B.mb_name" <?=($field=='B.mb_name')?'selected':''?>>성명</option>
					<option value="B.mb_co_name" <?=($field=='B.mb_co_name')?'selected':''?>>상호명</option>
					<option value="B.mb_hp" <?=($field=='B.mb_hp')?'selected':''?>>연락처</option>
					<option value="A.coupon_serial_no" <?=($field=='A.coupon_serial_no')?'selected':''?>>쿠폰번호</option>
				</select>
			</li>
			<li><input type="text" id="keyword" name="keyword" value="<?=$keyword?>" class="form-control input-sm"></li>
			<li><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
		</ul>
		</form>
		<ul class="col-sm-10 list-inline">
			<li>
				<select id="sort_field" class="form-control input-sm">
					<option value="">::정렬필드선택::</option>
					<option value="invest_count" <?=($sort_field=='invest_count')?'selected':''?>>누적투자건수</option>
					<option value="invest_amount" <?=($sort_field=='invest_amount')?'selected':''?>>누적투자금액</option>
				</select>
			</li>
			<li>
				<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
				<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
			</li>
			<li><? if($TARGET_EVENT['idx']) { ?><button type="button" class="btn btn-sm btn-success" onClick="excel_down();">검색결과 시트저장</button><? } ?></li>
			<li></li>
<?
if($TARGET_EVENT['idx']) {

	$coupon_set_button  = "<button type='button' class='btn btn-sm btn-gray'>쿠폰발급</button>";
	$coupon_send_button = "<button type='button' class='btn btn-sm btn-gray'>쿠폰SMS발송</button>";

	$coupon_set_count  = sql_fetch("SELECT COUNT(A.idx) AS cnt FROM cf_partner_event_reward_log A WHERE A.event_no='".$TARGET_EVENT['event_no']."' AND A.coupon_serial_no!=''")['cnt'];
	$coupon_send_count = sql_fetch("SELECT COUNT(A.idx) AS cnt FROM cf_partner_coupon_bank A WHERE A.event_no='".$TARGET_EVENT['event_no']."' AND A.sms_dt > '0000-00-00 00:00:00'")['cnt'];

	if($coupon_set_count)  {
		if($coupon_set_count < $total_count) {
			$coupon_set_button  = "<button type='button' class='btn btn-sm btn-danger' onClick=\"couponSet('".$TARGET_EVENT['event_no']."');\">쿠폰발급</button>";
		}
		else {
			if($coupon_send_count < $total_count) {
				$coupon_send_button = "<button type='button' class='btn btn-sm btn-danger' onClick=\"couponSend('".$TARGET_EVENT['event_no']."');\">쿠폰SMS발송</button>";
			}
		}
	}
	else {
		$coupon_set_button  = "<button type='button' class='btn btn-sm btn-danger' onClick=\"couponSet('".$TARGET_EVENT['event_no']."');\">쿠폰발급</button>";
	}

	// 발급완료시 발급버튼 비활성, 쿠폰발송 버튼 활성
	if($TARGET_EVENT['coupon_set']=='1') {
		$coupon_set_button  = "<button type='button' class='btn btn-sm btn-gray'>쿠폰발급완료</button>";
		if($TARGET_EVENT['coupon_send']=='1') {
			$coupon_send_button = "<button type='button' class='btn btn-sm btn-gray'>쿠폰SMS발송완료</button>";
		}
		else {
			$coupon_send_button = "<button type='button' class='btn btn-sm btn-danger' onClick=\"couponSend('".$TARGET_EVENT['event_no']."');\">쿠폰SMS발송</button>";
		}
	}

}

?>
			<li><?=$coupon_set_button?></li>
			<li><?=$coupon_send_button?></li>
		</ul>
	</div>
	<script>
	couponSet = function(event_no) {
		if( confirm('본 이벤트의 전체 참여자에게 쿠폰 배분을 시작합니다.\n진행 하시겠습니까? 탈퇴자는 제외됩니다.') ) {
			$.ajax({
				url: "partner_event_coupon.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data: {
					mode: 'coupon_set',
					event_no: event_no,
				},
				success:function(data) {
					if(data.result=='SUCCESS') {
						alert(data.message); location.reload();
					}
					else {
						alert(data.message);
					}
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}

	function couponSend(event_no) {
		if( confirm('본 이벤트의 전체 참여자에게 쿠폰 발송을 시작합니다.\n진행 하시겠습니까? 탈퇴자는 제외됩니다.') ) {
			$.ajax({
				url: "partner_event_coupon.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data: {
					mode: 'coupon_send',
					event_no: event_no,
				},
				success:function(data) {
					if(data.result=='SUCCESS') {
						alert(data.message); location.reload();
					}
					else {
						alert(data.message);
					}
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
	</script>

	<div style="margin:10px 10px 4px; font-size:12px;display:<?=($TARGET_EVENT['idx'])?'':'none'?>">
		<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin:10px 0 5px">
			<li>
				선택된 항목에 대하여
				<select id="trigger" class="input-sm">
					<option value="">::선택::</option>
					<option value="approved">보상확정</option>
					<option value="invalid">무효처리</option>
					<option value="paid">지급등록</option>
				</select>
				합니다.
			</li>
			<li><button type="button" id="btnSubmit" class="btn btn-sm btn-danger">확인</button><li>
		</ul>
	</div>

	<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px">
		<colgroup>
			<col style="width:40px">
			<col style="width:60px">
			<col style="width:60px">
			<col style="width:60px">
		</colgroup>
		<thead>
			<tr>
				<th rowspan="2" style="background:#F8F8EF" class="text-center"><input type="checkbox" id="chkall" value="1"></th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">NO</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">이벤트<br/>번호</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">회원<br/>번호</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">아이디</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">성명</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">연락처</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">참여일</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">PID</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">쿠폰번호</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">쿠폰<br/>발급/발송일</th>
				<th colspan="7" style="background:#F8F8EF" class="text-center">2차 투자보상</th>
			</tr>
			<tr>
				<th style="background:#F8F8EF" class="text-center">누적투자수(건)</th>
				<th style="background:#F8F8EF" class="text-center">누적투자금(원)</th>
				<th style="background:#F8F8EF" class="text-center">보상품</th>
				<th style="background:#F8F8EF" class="text-center">보상확정일시</th>
				<th style="background:#F8F8EF" class="text-center">은행</th>
				<th style="background:#F8F8EF" class="text-center">계좌번호</th>
				<th style="background:#F8F8EF" class="text-center">지급일시</th>
			</tr>
		</thead>
		<form id="form1" name="form1">
			<input type="hidden" id="action" name="action">
			<input type="hidden" id="event_no" name="event_no" value="<?=$TARGET_EVENT['event_no']?>">
		<tbody>

<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		$mb_id = (in_array($LIST[$i]['mb_level'], array('1','2','3','4','5'))) ? $LIST[$i]['mb_id'] : '';

		if( in_array($member['mb_id'], array('admin_supermario','admin_andcl76')) ) {
			$blind_mb_hp = $LIST[$i]['mb_hp'];
		}
		else {
			$blind_mb_hp = ($LIST[$i]['mb_hp']) ? substr($LIST[$i]['mb_hp'], 0, strlen($LIST[$i]['mb_hp'])-4) . "●●●●" : "";
		}

		$link = "../member/member_view.php?member_group=F&mb_id=" . $LIST[$i]['mb_id'];


		$print_bank = $print_bank_acct = $print_bank_private_name = '';

		if($LIST[$i]['invalid']=='') {
			if( in_array($LIST[$i]['mb_level'], array('1','2','3','4','5')) ) {
				if($LIST[$i]['approved']=='1') {
					$print_bank              = $BANK[$LIST[$i]['bank_code']];
					$print_bank_acct         = substr($LIST[$i]['bank_acct'], 0, -2) . "●●";
					$print_bank_private_name = $LIST[$i]['bank_private_name'];
				}
			}
			else {
				if($LIST[$i]['paid']=='1') {
					$print_bank              = $BANK[$LIST[$i]['bank_code']];
					$print_bank_acct         = substr($LIST[$i]['bank_acct'], 0, -2) . "●●";
					$print_bank_private_name = $LIST[$i]['bank_private_name'];
				}
				else {
					$print_bank = $print_bank_acct = $print_bank_private_name = '';
				}
			}
		}

		$bgColor = (!in_array($LIST[$i]['mb_level'],array('1','2','3','4','5'))) ? '#FFDDDD' : '';
		$fcolor1 = ($LIST[$i]['invest_count'] > 0) ? '' : '#CCC';
		$fcolor2 = ($LIST[$i]['invest_amount'] > 0) ? '' : '#CCC';

		$reward_target = ($TARGET_EVENT['idx'] && $TARGET_EVENT['is_real'] && $LIST[$i]['iam_rwd_target'] && $LIST[$i]['invalid']=='' && $LIST[$i]['paid']=='') ? true : false;

		if($reward_target) $bgColor = '#FFFFCC';

		$reward_goods = '';
		if($EVENT[$LIST[$i]['event_no']]['invest_rwd_give']=='1' && $LIST[$i]['iam_rwd_target']) {
			if($EVENT[$LIST[$i]['event_no']]['invest_rwd_amt'] > 0 || $EVENT[$LIST[$i]['event_no']]['invest_rwd_point'] > 0) {
				$reward_goods.= ($EVENT[$LIST[$i]['event_no']]['invest_rwd_amt']) ? $EVENT[$LIST[$i]['event_no']]['invest_rwd_amt'] . '원' : $EVENT[$LIST[$i]['event_no']]['invest_rwd_point'] . 'P';
			}
		}

		$coupon_set_send_dd = ($LIST[$i]['coupon_send_dt']) ? substr($LIST[$i]['coupon_send_dt'], 0, 10) : "<span style='color:#AAA'>" . substr($LIST[$i]['coupon_set_dt'], 0, 10) . "</span>";

?>
			<tr align="center" style="background:<?=$bgColor?>">
				<td><? if($reward_target) { ?><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['member_idx']?>"><? } ?></td>
				<td><?=$num?></td>
				<td><?=$LIST[$i]['event_no']?></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['member_idx']?></a></td>
				<td><?=$mb_id?></td>
				<td><?=$LIST[$i]['mb_title']?></td>
				<td><?=$blind_mb_hp?></td>
				<td><?=substr($LIST[$i]['rdatetime'],0,10)?></td>
				<td><?=$LIST[$i]['pid']?></td>
				<td><span style="color:#FF2222"><?=$LIST[$i]['coupon_serial_no']?></span></td>
				<td><?=$coupon_set_send_dd?></td>
				<td align="right" style="color:<?=$fcolor1?>"><?=number_format($LIST[$i]['invest_count'])?></td>
				<td align="right" style="color:<?=$fcolor2?>"><?=number_format($LIST[$i]['invest_amount'])?></td>
				<td><span style="color:#FF2222"><?=$reward_goods?></span></td>
				<td><?=($LIST[$i]['invalid']=='1') ? "<span style='color:brown'>".substr($LIST[$i]['invalid_datetime'],0,16)."</span>" : substr($LIST[$i]['approved_datetime'],0,16); ?></td>
				<td><?=$print_bank?></td>
				<td><?=$print_bank_acct?></td>
				<td><?=($LIST[$i]['paid']=='1') ? substr($LIST[$i]['paid_datetime'],0,16) : ''; ?></td>
			</tr>
<?
		$num--;
	}
}
else {
	echo "<tr><td colspan='20' align='center'>데이터가 없습니다.</td></tr>\n";
}
?>
		</tbody>
		</form>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 20px 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script type="text/javascript">
$(function() {
	$("input[id=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

function formSubmit() {
	var _trigger = $('#trigger').val();

	if(!_trigger) {
		alert('선택된 항목을 처리할 기준을 선택하셔야 합니다.');
		return false;
	}

	if(_trigger) {
		if(_trigger == 'approved')     msg = "선택된 회원의 투자 보상을 확정 하시겠습니까?";
		else if(_trigger == 'invalid') msg = "선택된 회원의 투자 보상을 무효처리 하시겠습니까?";
		else if(_trigger == 'paid')    msg = "선택된 회원의 투자 보상 예치금이 지급되었음을 등록 하시겠습니까?";

		if(confirm(msg)) {
			$('#action').val(_trigger);

			form_data = $('#form1').serialize();
			$.ajax({
				url : 'partner_event_reward.proc.ajax.php',
				type: 'POST',
				data:form_data,
				dataType: 'json',
				success:function(data, textStatus, jqXHR) {
					if(data.result=='SUCCESS') { alert(data.message); location.reload(); }
					else if(data.result=='ACTION_EMPTY') { alert('처리항목을 선택하십시요.'); $('#trigger').focus(); }
					else if(data.result=='CHK_EMPTY') { alert('대상자를 선택하십시요.'); $('input[id=chkall]').focus(); }
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
}

$('#btnSubmit').click(function() {
	formSubmit();
});

// 상품정렬
function sortList(param) {
	if($('#sort_field').val()!='') {
		url = '<?=$_SERVER['PHP_SELF']?>'
		    + '?<?=$qstr?>'
		    + '&sort_field=' + $('#sort_field').val()
		    + '&sort=' + param
		$(location).attr('href', url);
	}
	else {
		alert('정렬필드를 선택하십시요.'); $('#sort_field').focus();
	}
}

$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = "<?=$_SERVER['PHP_SELF']?>?<?=$qstr?>&page=" + $(this).attr('data-page');
		$(location).attr('href', url);
});

function excel_down() {
	if(confirm('다운로드 하시겠습니까?')) {
		url = "<?=$_SERVER['PHP_SELF']?>?<?=$qstr?>&mode=download";
		$(location).attr('href', url);
	}
}

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>