<?
// 추천인(추천하는 사람) 리스트

include_once("_common.php");

$where = "";
$where.= " AND A.member_group='F' AND A.mb_level NOT IN('9','10')";
$where.= " AND A.mb_datetime BETWEEN '".$EVENT_CONF['sdate']." 00:00:00' AND '".$EVENT_CONF['edate']." 23:59:59'";
$where.= " AND A.pid='naverpay'";

if($state) {
	if($state=='null') {
		$where.= " AND B.approved='' AND B.paid='' AND B.invalid=''";
	}
	else {
		if($state=='invalid') $where.= " AND B.invalid='1'";
		else if($state=='approved') $where.= " AND B.approved='1'";
		else if($state=='paid') $where.= " AND B.paid='1'";
	}
}
if($date_field) {
	if($sdate) $where.= " AND LEFT($date_field, 10)>='".$sdate."'";
	if($edate) $where.= " AND LEFT($date_field, 10)<='".$edate."'";
}
if($field && $keyword) {
	$where.= ($field=='A.mb_hp') ? " AND $field = '".masterEncrypt($keyword, false)."'" : " AND $field = '".$keyword."'";
}

if($mkd) {
	if($mkd == '1') {
		$where.= " AND (".date('Y')." - LEFT(mb_birth,4)) >= 19";			// 대상자
	}
	else if($mkd == '2') {
		$where.= " AND (".date('Y')." - LEFT(mb_birth,4)) < 19 ";			// 비 대상자
	}
}

$q1 = "
SELECT st1.cnt AS CNT1 , st2.cnt AS CNT2 FROM
(
	SELECT '1' AS kind, count(*) AS cnt from
	(
	SELECT mb_no from g5_member
	where pid='naverpay'
	and (".date('Y')."-left(mb_birth,4)) >= 19
	 and left(mb_datetime,7)='".$EVENT_CONF['sdate']."'
	) t1
) st1
LEFT JOIN
(
	SELECT '1' AS kind, count(*) AS cnt from
	(
	SELECT mb_no from g5_member
	where pid='naverpay'
	and (".date('Y')."-left(mb_birth,4)) < 19
	 and left(mb_datetime,7)='".$EVENT_CONF['sdate']."'
	) t1
) st2
ON st1.kind=st2.kind";
//print_rr($Q2, 'font-size:12px');
$r1 = sql_fetch($q1);

$Q2 = "
	SELECT t1.CNT AS CNT1, t2.CNT AS CNT2 FROM
	(
	SELECT '1' AS kind, COUNT(*) AS CNT FROM
				(
					SELECT mb_no FROM g5_member
					 WHERE member_group='F' AND mb_level NOT IN('9','10')
					 AND LEFT(mb_datetime, 10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					 AND pid='naverpay'
					 AND (".date('Y')."-left(mb_birth,4)) >= 19

				) A
				LEFT JOIN
				(SELECT idx, rcidx, member_idx FROM recommend_reward_log WHERE event_no='".$event_no."' AND position='recmder') B
				ON A.mb_no=B.member_idx
				WHERE B.rcidx is null OR B.rcidx=0
	) t1 LEFT JOIN
	(
	SELECT '1' AS kind, COUNT(*) AS CNT FROM recommend_reward_log where event_no='".$event_no."' and rcidx > 0

	) t2 ON t1.kind=t2.kind
";

$r2 = sql_fetch($Q2);

$sql = "
	SELECT
		COUNT(A.mb_no) AS cnt
	FROM
		g5_member A
	LEFT JOIN
		recommend_reward_log B  ON A.mb_no=B.member_idx
	WHERE (1)
		$where";
//print_rr($sql, 'font-size:12px');
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 20;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", mb_no DESC";
}
else {
	$sql_order.= " recmder_invest_amount DESC, mb_datetime DESC";
}


//$sql_order = " ( CASE WHEN approved='' THEN 1 ELSE 2 END) DESC, mb_datetime DESC";

$sql = "
	SELECT
		mb_no, mb_id, mb_name, mb_hp, mb_datetime, rec_mb_no, rec_mb_id,
		approved, approved_datetime, paid, paid_datetime, invalid, invalid_datetime,
		bank_code, bank_acct, bank_private_name,recmder_invest_count,recmder_invest_amount,
		cnumber, mem_date, use_date
	FROM
	(
		SELECT
			A.mb_no, A.mb_id, A.mb_name, A.mb_hp, A.mb_datetime, A.rec_mb_no, A.rec_mb_id,
			B.approved, B.approved_datetime, B.paid, B.paid_datetime, B.invalid, B.invalid_datetime,
			B.bank_code, B.bank_acct, B.bank_private_name,
			IFNULL(C.cnumber,'') AS cnumber, IFNULL(C.mem_date,'') AS mem_date, IFNULL(C.use_date,'') AS use_date,
			(
				SELECT
					COUNT(CPI.idx)
				FROM
					cf_product_invest CPI
				LEFT JOIN
					cf_product CP  ON CPI.product_idx=CP.idx
				WHERE (1)
					AND CPI.invest_state IN('Y','R')
					AND CPI.member_idx=A.mb_no
					AND CPI.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					AND CP.state IN('1','2','5','7')
					AND CP.category='2'
			) AS recmder_invest_count,
			(
				SELECT
					IFNULL(SUM(CPI.amount),0)
				FROM
					cf_product_invest CPI
				LEFT JOIN
					cf_product CP  ON CPI.product_idx=CP.idx
				WHERE (1)
					AND CPI.invest_state IN('Y','R')
					AND CPI.member_idx=A.mb_no
					AND CPI.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					AND CP.state IN('1','2','5','7')
					AND CP.category='2'
			) AS recmder_invest_amount
		FROM
			g5_member A
		LEFT JOIN
			(SELECT * FROM recommend_reward_log WHERE event_no='".$event_no."' AND position='recmder') AS B  ON A.mb_no=B.member_idx
		LEFT JOIN
			hloan_cupoint_reg C  ON B.rcidx=C.rcidx
		WHERE (1)
			$where
	) t1

	ORDER BY
		$sql_order
	LIMIT
		$from_record, $rows";
//print_rr($sql,'font-size:12px');
$result = sql_query($sql);
$rcount = $result->num_rows;

for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);

	if($LIST[$i]['recmder_invest_amount'] >= $EVENT_CONF['use_point']) {
		$LIST[$i]['reward_amount'] = $EVENT_CONF['use_point'];
	}
}
$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

?>

<div>

	<div style="border:1px solid #DDD;background:#FAFAFA;width:100%;display:inline-block">
		<form name="f_srch" id="f_srch">
			<input type="hidden" name="target" value="pid">
			<input type="hidden" name="mkd" value="">

		<ul class="col-sm-10 list-inline" style="margin-top:10px">
			<li><select name="event_no" id="event_no" class="form-control input-sm">
					<option value="">::이벤트 조회::</option>
					<?
					$resx = sql_query("SELECT event_no, event_title FROM recommend_event_config ORDER BY event_no DESC");
					while( $row = sql_fetch_array($resx) ) {
						$selected = ($row['event_no']==$event_no) ? 'selected' : '';
						echo "<option value='".$row['event_no']."' $selected>".$row['event_title']."</option>";
					}
					?>
				</select>
			</li>
			<li><select id="date_field" name="date_field" class="form-control input-sm">
					<option value="mem_date">::쿠폰발급일::</option>
				</select>
			</li>
			<li><input type="text" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
			<li><input type="text" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
		</ul>
		<ul class="col-sm-10 list-inline">
			<li><select id="field" name="field" class="form-control input-sm">
					<option value="">::검색항목선택::</option>
					<option value="A.mb_no" <?=($field=='A.mb_no')?'selected':''?>>회원번호</option>
					<option value="A.mb_id" <?=($field=='A.mb_id')?'selected':''?>>아이디</option>
					<option value="A.mb_name" <?=($field=='A.mb_name')?'selected':''?>>성명</option>
					<option value="A.mb_co_name" <?=($field=='A.mb_co_name')?'selected':''?>>상호명</option>
					<option value="A.mb_hp" <?=($field=='A.mb_hp')?'selected':''?>>연락처</option>
					<option value="A.rec_mb_id" <?=($field=='A.rec_mb_id')?'selected':''?>>추천ID</option>
					<option value="B.target_member_idx" <?=($field=='B.target_member_idx')?'selected':''?>>피추천인번호</option>
				</select>
			</li>
			<li><input type="text" id="keyword" name="keyword" value="<?=$keyword?>" class="form-control input-sm"></li>
			<li><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
			<li><input type="button" class="btn btn-sm " id="utarget" value="대상자 검색 (<?php ECHO $r1["CNT1"];?>)"></li>
			<li><input type="button" class="btn btn-sm " id="btarget"  value="비 대상자 검색 (<?php ECHO $r1["CNT2"];?>)"></li>
		</ul>
		</form>
		<ul class="col-sm-10 list-inline">
			<li>
				<select id="sort_field" class="form-control input-sm">
					<option value="">::정렬필드선택::</option>
					<option value="recmder_invest_count" <?=($sort_field=='recmder_invest_count')?'selected':''?>>누적투자건수</option>
					<option value="recmder_invest_amount" <?=($sort_field=='recmder_invest_amount')?'selected':''?>>누적투자금액</option>
				</select>
			</li>
			<li>
				<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
				<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
			</li>
			<li><button type="button" class="btn btn-sm btn-success" onClick="excel_down();">검색결과 시트저장</button></li>
			<li><button type="button" class="btn btn-sm btn-danger" onClick="coupon_send('<?php ECHO $r2["CNT1"];?>');">대상자 쿠폰발급 (SMS발송) (<?=$r2['CNT1']?>)</button></li>
		</ul>
	</div>

	<div style="margin:10px 10px 4px; font-size:12px;">
		<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin:10px 0 5px">
			<li>
				선택된 항목을
				<select id="trigger" class="input-sm">
					<option value="">::선택::</option>
					<option value="approved">보상확정</option>
					<option value="invalid">무효처리</option>
					<option value="paid">지급등록</option>
					<!--<option value="paid_cancel">지급등록취소</option>-->
				</select>
				합니다.
			</li>
			<li><button type="button" id="btnSubmit" class="btn btn-sm btn-danger">확인</button><li>
			<li style="padding-top:8px;color:brown">"누적투자수, 누적투자금"은 이벤트 기간중의 누적데이터 내역임.</li>
			<li style="float:right;padding-top:8px;">
				<span style="color:#FF6633">쿠폰발급완료자 : <?=number_format($r2["CNT2"]);?> 명</span> |
				<span style="color:#FF6633">잔여 당첨금 : <?=number_format($balance_point);?>원</span> |
				<span style="color:#3366FF">지급 당첨금 : <?=number_format($paid_point);?>원</span> |
				<span style="color:brown">무효수 : <?=number_format($invalid_count);?>건</span>
			</li>
		</ul>
	</div>

	<table class="table table-striped table-bordered table-hover">
		<colgroup>
			<col style="width:60px">
			<col style="width:60px">
		</colgroup>
		<thead>
			<tr>
				<th style="background:#3366CC;color:#FFF" class="text-center"><input type="checkbox" id="chkall" value="1"></th>
				<th style="background:#3366CC;color:#FFF" class="text-center">NO</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">회원번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">아이디</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">성명</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">연락처</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">가입일</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">쿠폰번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">쿠폰발급일</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">누적투자수(건)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">누적투자금(원)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">보상예치금(원)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">보상확정일시</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">무효처리일시</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">지급일시</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">은행</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">계좌번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">예금주</th>
			</tr>
		</thead>
		<form id="form1" name="form1">
			<input type="hidden" id="action" name="action">
			<input type="hidden" id="event_no" name="event_no" value="<?=$event_no?>">
		<tbody>
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		if( in_array($member['mb_id'], array('admin_supermario','admin_andcl76')) ) {
			$blind_mb_hp = $LIST[$i]['mb_hp'];
		}
		else {
			$blind_mb_hp = substr($LIST[$i]['mb_hp'], 0, strlen($LIST[$i]['mb_hp'])-4) . "****";
		}

		$link = "../member/member_view.php?member_group=F&mb_id=" . $LIST[$i]['mb_id'];


		$print_bank = $print_bank_acct = $print_bank_private_name = '';
		if($LIST[$i]['invalid']=='1') {
			$print_bank = $print_bank_acct = $print_bank_private_name = '';
		}
		else {
			if($LIST[$i]['approved']=='1' || $LIST[$i]['paid']=='1') {
				$print_bank              = $BANK[$LIST[$i]['bank_code']];
				$print_bank_acct         = $LIST[$i]['bank_acct'];
				$print_bank_private_name = $LIST[$i]['bank_private_name'];
			}
		}

?>
			<tr align="center">
				<td><? if($LIST[$i]['reward_amount'] && empty($LIST[$i]['invalid']) && empty($LIST[$i]['paid'])){ ?><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['mb_no']?>"><? } ?></td>
				<td><?=$num?></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_no']?></a></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_id']?></a></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_name']?></a></td>
				<td><?=$blind_mb_hp?></td>
				<td><?=substr($LIST[$i]['mb_datetime'],0,16)?></td>
				<td><a href="#none" OnClick="copyToClipboard('<?php ECHO urlencode($LIST[$i]['cnumber'])?>');"><?=$LIST[$i]['cnumber']?></a></td>
				<td><a href="#none" OnClick="check_send_re('<?=$LIST[$i]['cnumber']?>');"><?=$LIST[$i]['mem_date']?></a></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_amount'])?></td>
				<td align="right"><?=$LIST[$i]['reward_amount']?></td>
				<td><?=($LIST[$i]['approved']=='1') ? substr($LIST[$i]['approved_datetime'],0,16) : ''; ?></td>
				<td style="color:#FF2222"><?=($LIST[$i]['invalid']=='1') ? substr($LIST[$i]['invalid_datetime'],0,16) : ''; ?></td>
				<td><?=($LIST[$i]['paid']=='1') ? substr($LIST[$i]['paid_datetime'],0,16) : ''; ?></td>
				<td><?=$print_bank?></td>
				<td><?=$print_bank_acct?></td>
				<td><?=$print_bank_private_name?></td>
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

<?php
	$qstr = "target=".$target."&event_no=".$event_no."&state=".$state."&date_field=".$date_filed."&sdate=".$sdate."&edate=".$edate."&field=".$field."&keyword=".$keyword;

	$qstrfull = $qstr."&sort_field=".$sort_field."&sort=".$sort;
?>

<script>
function copyToClipboard(val) {
  var t = document.createElement("textarea");
  document.body.appendChild(t);
//  t.value = decodeURIComponent(val);
  t.value = val;
  t.select();
  document.execCommand('copy');
  document.body.removeChild(t);
  alert('복사 되었습니다');
}
</script>

<script type="text/javascript">
$(function() {
	$("input[id=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

function formSubmit() {
	var _trigger = $('#trigger').val();
	if(_trigger) {
		if(_trigger == 'approved')         msg = "선택된 회원의 추천인과 피추천인의 보상을 확정 하시겠습니까?";
		else if(_trigger == 'invalid')     msg = "선택된 회원의 추천인 내역을 무효처리 하시겠습니까?";
		else if(_trigger == 'paid')        msg = "선택된 회원의 추천보상예치금이 지급되었음을 등록 하시겠습니까?";

		if(confirm(msg)) {
			$('#action').val(_trigger);
			form_data = $('#form1').serialize();
			$.ajax({
				url : 'recommend_event_proc.ajax.php',
				type: 'POST',
				data:form_data,
				dataType: 'json',
				success:function(data, textStatus, jqXHR) {
					if(data.result=='SUCCESS') { alert(data.message); window.location.reload(); }
					else if(data.result=='ACTION_EMPTY') { alert('처리항목을 선택하십시요.'); $('#trigger').focus(); }
					else if(data.result=='CHK_EMPTY') { alert('대상자를 선택하십시요.'); $('input[id=chkall]').focus(); }
				},
				error: function (jqXHR, textStatus, errorThrown)	{
					console.log(jqXHR);
				}
			});
		}
	}
}


function check_send_re(obj)
{
	if(obj)
	{
		if(confirm(obj+'의 쿠폰내용을 문자로 재 발송 하시겠습니까?'))
		{
			form_data = "cnumber="+obj;
			$.ajax({
				url : 'recommend_coupon_re_proc.ajax.php',
				type: 'POST',
				data:form_data,
				dataType: 'json',
				success:function(data, textStatus, jqXHR) {
					if(data.result=='SUCCESS') { alert(data.message); window.location.reload(); }
					else if(data.result=='ACTION_EMPTY') { alert('처리항목을 선택하십시요.'); $('#trigger').focus(); }
					else if(data.result=='CHK_EMPTY') { alert('대상자를 선택하십시요.'); $('input[id=chkall]').focus(); }
				},
				error: function (jqXHR, textStatus, errorThrown)	{
					console.log(jqXHR);
				}
			});
		}
	} else {

	}
}

$('#btnSubmit').click(function() {
	formSubmit();
});

$('#utarget').click(function() {
	$("input[name='mkd']").val("1");
	$("#f_srch").submit();
});

$('#btarget').click(function() {
	$("input[name='mkd']").val("2");
	$("#f_srch").submit();
});


$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstrfull?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

// 상품정렬
function sortList(param)
{
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

function excel_down() {
	if(confirm('다운로드 하시겠습니까?')) {
		var f = document.f_srch;
		f.target = "_blank";
		f.action = "pid_list_download.php";
		f.submit();
	}
}

function coupon_send(cnt)
{
	if(parseInt(cnt) == 0)
	{
		alert('발급할 대상이 없습니다');
		return false;
	} else {
		if(confirm('정말 쿠폰발급을 하시겠습니까?'))
		{
			$('#action').val('save');
			form_data = $('#form1').serialize();
			$.ajax({
				url : 'recommend_coupon_proc.ajax.php',
				type: 'POST',
				data:form_data,
				dataType: 'json',
				success:function(data, textStatus, jqXHR) {
					if(data.result=='SUCCESS') { alert(data.message); window.location.reload(); }
					else if(data.result=='ACTION_EMPTY') { alert('처리항목을 선택하십시요.'); $('#trigger').focus(); }
					else if(data.result=='CHK_EMPTY') { alert('대상자를 선택하십시요.'); $('input[id=chkall]').focus(); }
				},
				error: function (jqXHR, textStatus, errorThrown)	{
					console.log(jqXHR);
				}
			});
		}
	}
}
</script>