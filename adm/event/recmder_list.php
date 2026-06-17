<?
// 추천인(추천하는 사람) 리스트

include_once('_common.php');


$where = "";
$where.= " AND A.member_group='F' AND A.mb_level NOT IN('9','10')";
$where.= " AND LEFT(A.mb_datetime, 10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'";
$where.= " AND A.rec_mb_no IS NOT NULL";
$where.= " AND B.`position` = 'recmder'";

// 2021-11-12 추가 ----------------------------------------------------------------
if($EVENT_CONF['only_rec_id']){
	$where.= " AND A.rec_mb_id='".$EVENT_CONF['only_rec_id']."'";
}
else {
	$where.= " AND A.rec_mb_id!='hello'";
}
// 2021-11-12 추가 ----------------------------------------------------------------

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

$rows = 20;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;


$sql_order = "";
$sql_order.= " ( CASE WHEN approved='' THEN 1 ELSE 2 END) DESC,";
$sql_order.= " recmder_invest_amount DESC, recmder_invest_count DESC,";
$sql_order.= " mb_no DESC";


//**** 이벤트에 따른 대상상품군 선별 ****//
if($EVENT_CONF['prdt_ca']=='2') {
	$sqlto = " AND CP.category='2' ";																		// 부동산 전체를 대상으로..
}
else if($EVENT_CONF['prdt_ca']=='2-1') {
	$sqlto = " AND CP.category='2' AND CP.mortgage_guarantees='' ";			// PF만
}
else if($EVENT_CONF['prdt_ca']=='2-2') {
	$sqlto = " AND CP.category='2' AND CP.mortgage_guarantees='1' ";		// 주담대만
}
else if($EVENT_CONF['prdt_ca']=='3') {
	$sqlto = " AND CP.category='3' ";																		// 매출채권 대상
}
else if($EVENT_CONF['prdt_ca']=='1') {
	$sqlto = " AND CP.category='1' ";																		// 동산
}
else {
	//
}
//**** 이벤트에 따른 대상상품군 선별 ****//


$sql = "
	SELECT
		mb_no, mb_id, mb_name, mb_co_name, mb_hp, mb_datetime, rec_mb_no, rec_mb_id,
		approved, approved_datetime, paid, paid_datetime, invalid, invalid_datetime,
		bank_code, bank_acct, bank_private_name,recmder_invest_count,recmder_invest_amount
	FROM
	(
		SELECT
			A.mb_no, A.mb_id, A.mb_name, A.mb_co_name, A.mb_hp, A.mb_datetime, A.rec_mb_no, A.rec_mb_id,
			B.approved, B.approved_datetime, B.paid, B.paid_datetime, B.invalid, B.invalid_datetime,
			B.bank_code, B.bank_acct, B.bank_private_name,
			(
				SELECT
					COUNT(CPI.idx)
				FROM
					cf_product_invest CPI
				LEFT JOIN
					cf_product CP  ON CPI.product_idx=CP.idx
				WHERE (1)
					AND CPI.member_idx=A.mb_no
					AND CPI.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					AND ( (CPI.invest_state IN('Y','R') AND CP.state IN('','1','2','5','7','8')) OR (CPI.invest_state='N' AND CPI.cancel_by IN ('admin','system')) )
					".$sqlto."
			) AS recmder_invest_count,
			(
				SELECT
					IFNULL(SUM(CPI.amount),0)
				FROM
					cf_product_invest CPI
				LEFT JOIN
					cf_product CP  ON CPI.product_idx=CP.idx
				WHERE (1)
					AND CPI.member_idx=A.mb_no
					AND CPI.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					AND ( (CPI.invest_state IN('Y','R') AND CP.state IN('','1','2','5','7','8')) OR (CPI.invest_state='N' AND CPI.cancel_by IN ('admin','system')) )
					".$sqlto."
			) AS recmder_invest_amount
		FROM
			g5_member A
		LEFT JOIN
			recommend_reward_log B  ON A.mb_no=B.member_idx
		WHERE (1)
			$where
	) t1
";

$sql.= "
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $rows";

//if($member['mb_id']=='admin_sori9th') { print_rr($sql,'font-size:12px;line-height:14px;'); }

$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {

	$LIST[$i] = sql_fetch_array($result);

	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);
	$LIST[$i]['reward_goods']  = '';
	$LIST[$i]['reward_amount'] = 0;
	$LIST[$i]['register_num']  = '';

	if($LIST[$i]['recmder_invest_amount'] >= $EVENT_CONF['use_point']) {			// 투자금액을 충족할 경우

		if($EVENT_CONF['recmder_reward_goods_name']) {
			$LIST[$i]['reward_goods'] = $EVENT_CONF['recmder_reward_goods_name'];
		}
		else {
			switch($EVENT_CONF['recmder_reward_type']) {
				case '2' : $LIST[$i]['reward_goods'] = '포인트'; break;
				case '3' : $LIST[$i]['reward_goods'] = '쿠폰'; break;
				case '1' :
				default  : $LIST[$i]['reward_goods'] = '예치금'; break;
			}
		}

		$LIST[$i]['reward_goods'].= "<br/>\n(".number_format($EVENT_CONF['recmder_reward_point'])."원)";
		$LIST[$i]['reward_amount'] = $EVENT_CONF['recmder_reward_point'];

		// 승인 또는 지급완료 플래그가 있을 경우
		if($LIST[$i]['approved']=='1' || $LIST[$i]['paid']=='1') {
			if($LIST[$i]["member_type"] == '2') {
				$register_num = preg_replace("/-/", "", $LIST[$i]['mb_co_reg_num']);
				$LIST[$i]['register_num'] = substr($register_num,0,3)."-".substr($register_num,3,2)."-".substr($register_num,-5);
			}
			else {
				$register_num = getJumin($LIST[$i]['mb_no']);
				$LIST[$i]['register_num'] = substr($register_num,0,6)."-".substr($register_num,-7);
			}

			if($EVENT_CONF['recmder_reward_type']=='1') {
				$LIST[$i]['bank_name']         = $BANK[$LIST[$i]['bank_code']];
				$LIST[$i]['bank_acct']         = $LIST[$i]['bank_acct'];
				$LIST[$i]['bank_private_name'] = $LIST[$i]['bank_private_name'];
			}
			else {
				$LIST[$i]['bank_name'] = $LIST[$i]['bank_acct'] = $LIST[$i]['bank_private_name'] = '';
			}
		}

	}

}

$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

?>

<div>

	<div style="padding:4px 10px 4px 10px; font-weight:bold">
		이벤트명 : <?=$EVENT_CONF['event_title'] . " :: " . $event_sub_title?><br/>
		시행기간 : <?=preg_replace("/-/", ".", $EVENT_CONF['sdate'])?> ~ <?=preg_replace("/-/", ".", $EVENT_CONF['edate'])?>
	</div>

	<div style="border:1px solid #DDD;background:#FAFAFA;width:100%;display:inline-block">
		<form id="f_srch" name="f_srch" method="get" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="target" value="recmder">
		<ul class="col-sm-10 list-inline" style="margin-top:10px">
			<li><select name="event_no" id="event_no" class="form-control input-sm">
					<option value="">::이벤트 조회::</option>
					<?
					$resx = sql_query("SELECT event_no, event_title FROM recommend_event_config WHERE is_real='1' ORDER BY event_no DESC");
					while( $row = sql_fetch_array($resx) ) {
						$selected = ($row['event_no']==$event_no) ? 'selected' : '';
						echo "<option value='".$row['event_no']."' $selected>".$row['event_title']."</option>";
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
			<li><select id="date_field" name="date_field" class="form-control input-sm">
					<option value="">::데이트필드선택::</option>
					<option value="A.mb_datetime" <?=($date_field=='A.mb_datetime')?'selected':''?>>회원가입일</option>
					<option value="B.approved_datetime" <?=($date_field=='B.approved_datetime')?'selected':''?>>보상확정일</option>
					<option value="B.paid_datetime" <?=($date_field=='B.paid_datetime')?'selected':''?>>지급처리일</option>
					<option value="B.invalid_datetime" <?=($date_field=='B.invalid_datetime')?'selected':''?>>무효처리일</option>
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
		</ul>
	</div>

	<div style="margin:10px 10px 4px; font-size:12px;">
		<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin:10px 0 5px">
			<li>
				선택된 항목에 대하여
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
				<span style="color:#FF6633">잔여 당첨금 : <?=number_format($balance_point);?>원</span> |
				<span style="color:#3366FF">지급 당첨금 : <?=number_format($paid_point);?>원</span> |
				<span style="color:brown">무효수 : <?=number_format($invalid_count);?>건</span>
			</li>
		</ul>
	</div>

	<table class="table table-striped table-bordered table-hover" style="font-size:12px">
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
				<th style="background:#3366CC;color:#FFF" class="text-center">추천ID</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">피추천인번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">누적투자수(건)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">누적투자금(원)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">보상품</th>
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
			$blind_mb_hp = substr($LIST[$i]['mb_hp'], 0, strlen($LIST[$i]['mb_hp'])-4) . "●●●●";
		}

		$link = "../member/member_view.php?member_group=F&mb_id=" . $LIST[$i]['mb_id'];


		$print_bank = $print_bank_acct = $print_bank_private_name = '';

		if( $LIST[$i]['invalid']=='' && in_array($EVENT_CONF['recmder_reward_type'], array('1','2')) ) {
			if($LIST[$i]['approved']=='1' || $LIST[$i]['paid']=='1') {
				$print_bank              = $BANK[$LIST[$i]['bank_code']];
				$print_bank_acct         = $LIST[$i]['bank_acct'];
				$print_bank_private_name = $LIST[$i]['bank_private_name'];
			}
		}

		$reward_goods = ( in_array($EVENT_CONF['recmder_reward_type'], array('1','2')) ) ? number_format($LIST[$i]['reward_amount']).'원' : $EVENT_CONF['recmder_reward_goods_name'];

		$recmdee_detail_list_link = "recommend_event.php?event_no=$event_no&field=A.mb_no&keyword=" . $LIST[$i]['rec_mb_no'];

?>
			<tr align="center">
				<td><? if($LIST[$i]['reward_amount'] && $LIST[$i]['invalid']=='' && $LIST[$i]['paid']=='') { ?><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['mb_no']?>"><? } ?></td>
				<td><?=$num?></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_no']?></a></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_id']?></a></td>
				<td><a href="<?=$link?>"><?=$LIST[$i]['mb_name']?><?if($LIST[$i]['mb_co_name']){ echo "<br/>\n(".$LIST[$i]['mb_co_name'].")"; }?></a></td>
				<td><?=$blind_mb_hp?></td>
				<td><?=substr($LIST[$i]['mb_datetime'],0,16)?></td>
				<td><a href="<?=$recmdee_detail_list_link?>"><?=$LIST[$i]['rec_mb_id']?></a></td>
				<td><a href="<?=$recmdee_detail_list_link?>"><?=$LIST[$i]['rec_mb_no']?></a></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_amount'])?></td>
				<td><?=$LIST[$i]['reward_goods']?></td>
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
		if(_trigger == 'approved')     msg = "선택된 회원의 추천인과 피추천인의 보상을 확정 하시겠습니까?";
		else if(_trigger == 'invalid') msg = "선택된 회원의 추천인 내역을 무효처리 하시겠습니까?";
		else if(_trigger == 'paid')    msg = "선택된 회원의 추천보상예치금이 지급되었음을 등록 하시겠습니까?";

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

$('#btnSubmit').click(function() {
	formSubmit();
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


$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

function excel_down() {
	if( confirm('다운로드 하시겠습니까?') ) {
		var f = document.f_srch;
		f.target = "axFrame";
		f.action = "recmder_list_download.php";
		f.submit();

		f.target = f.action = '';
	}
}
</script>