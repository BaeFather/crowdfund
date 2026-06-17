<?
// 추천인(추천하는 사람) 리스트
include_once("_common.php");

include_once(G5_LIB_PATH.'/crypt.lib.php');

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename=recmdeepid".TIME().".xls" );
header( "Content-description: PHP5 Generated Data" );

$ECONF = sql_fetch("SELECT * FROM recommend_event_config WHERE event_no='".$event_no."'");

$where = "";
$where.= " AND A.member_group='F' AND A.mb_level NOT IN('9','10')";
$where.= " AND LEFT(A.mb_datetime, 10) BETWEEN '".$ECONF["sdate"]."' AND '".$ECONF["edate"]."'";
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

$from_record = 0;
$num = $total_count - $from_record;


$sql_order = " recmder_invest_amount DESC, mb_datetime DESC";

$sql = "
SELECT 	mb_no, mb_id, mb_name, mb_hp, mb_datetime, rec_mb_no, rec_mb_id,
		approved, approved_datetime, paid, paid_datetime, invalid, invalid_datetime,
		bank_code, bank_acct, bank_private_name,recmder_invest_count,recmder_invest_amount,
		cnumber, mem_date, use_date
FROM
(
	SELECT
		A.mb_no, A.mb_id, A.mb_name, A.mb_hp, A.mb_datetime, A.rec_mb_no, A.rec_mb_id,
		B.approved, B.approved_datetime, B.paid, B.paid_datetime, B.invalid, B.invalid_datetime,
		B.bank_code, B.bank_acct, B.bank_private_name,
		IFNULL(C.cnumber,'') as cnumber, IFNULL(C.mem_date,'') as mem_date, IFNULL(C.use_date,'') as use_date,
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
				AND CPI.insert_date BETWEEN '".$ECONF["sdate"]."' AND '".$ECONF["edate"]."'
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
				AND CPI.insert_date BETWEEN '".$ECONF["sdate"]."' AND '".$ECONF["edate"]."'
				AND CP.state IN('1','2','5','7')
				AND CP.category='2'
		) AS recmder_invest_amount
	FROM
		g5_member A
	LEFT JOIN
		(SELECT * FROM recommend_reward_log WHERE event_no='".$event_no."') as B  ON A.mb_no=B.member_idx
	LEFT JOIN
		hloan_cupoint_reg C
		ON B.rcidx=C.rcidx
	WHERE (1)
		$where
) t1
";

$sql .= "
ORDER BY
	$sql_order
";
//echo $sql."<BR>";
//print_rr($sql,'font-size:12px');
$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);

	if($LIST[$i]['recmder_invest_amount']>=$strTitle[0][6]) {
		$LIST[$i]['reward_amount'] = $strTitle[0][5];
	}

}
$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

?>

<div>

	<table border=1>
		<thead>
			<tr>
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
				<th style="background:#CC3366;color:#FFF" class="text-center">주민등록번호</th>
			</tr>
		</thead>
		<form id="form1" name="form1">
			<input type="hidden" id="action" name="action">
			<input type="hidden" id="event_no" name="event_no" value="<?=$event_no?>">
		<tbody>
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		UNSET($register_num);

		$blind_mb_hp = $LIST[$i]['mb_hp'];

		$print_bank = $print_bank_acct = $print_bank_private_name = '';
		if($LIST[$i]['invalid']=='1') {
			$print_bank = $print_bank_acct = $print_bank_private_name = '';
		}
		else {
			if($LIST[$i]['approved']=='1' || $LIST[$i]['paid']=='1') {
				$print_bank              = $BANK[$LIST[$i]['bank_code']];
				$print_bank_acct         = $LIST[$i]['bank_acct'];
				$print_bank_private_name = $LIST[$i]['bank_private_name'];
				$register_num = getJumin($LIST[$i]["mb_no"]);
			}
		}
?>
			<tr align="center">
				<td><?=$num?></td>
				<td><?=$LIST[$i]['mb_no']?></td>
				<td><?=$LIST[$i]['mb_id']?></td>
				<td><?=$LIST[$i]['mb_name']?></td>
				<td><?=$blind_mb_hp?></td>
				<td><?=substr($LIST[$i]['mb_datetime'],0,16)?></td>
				<td><a href="#none" OnClick="check_send_re('<?=$LIST[$i]['cnumber']?>');"><?=$LIST[$i]['cnumber']?></td>
				<td><?=$LIST[$i]['mem_date']?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_amount'])?></td>
				<td align="right"><?=number_format($LIST[$i]['reward_amount'])?></td>
				<td><?=($LIST[$i]['approved']=='1') ? substr($LIST[$i]['approved_datetime'],0,16) : ''; ?></td>
				<td style="color:#FF2222"><?=($LIST[$i]['invalid']=='1') ? substr($LIST[$i]['invalid_datetime'],0,16) : ''; ?></td>
				<td><?=($LIST[$i]['paid']=='1') ? substr($LIST[$i]['paid_datetime'],0,16) : ''; ?></td>
				<td><?=$print_bank?></td>
				<td><?=$print_bank_acct?></td>
				<td><?=$print_bank_private_name?></td>
				<td><?=$register_num?></td>
			</tr>
<?
		$num--;
	}
}
else {
	echo "<tr><td colspan='18' align='center'>데이터가 없습니다.</td></tr>\n";
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
	var f = document.f_srch;
	f.target = "_blank";
	f.action = "recmder_list_download.php";
	f.submit();

	f.target = "";
	f.action = "";
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