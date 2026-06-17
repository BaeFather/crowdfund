<?
///////////////////////////////////////////////////////////////////////////////
// 이벤트 목록 관리
///////////////////////////////////////////////////////////////////////////////
include_once("_common.php");


$sql = "SELECT COUNT(idx) AS cnt FROM recommend_event_config WHERE (1) $where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 10;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", mb_no DESC";
}
else {
	$sql_order.= " event_no DESC, idx DESC";
}


$sql = "
	SELECT
		*
	FROM
		recommend_event_config
	WHERE (1)
		$where
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $rows";
//if($member['mb_id']=='admin_sori9th') { print_rr($sql,'font-size:12px;line-height:14px;'); }

$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
}

//if($member['mb_id']=='admin_sori9th') { print_rr($LIST,'font-size:12px;line-height:14px;'); }

$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;


function getRewardGift($no) {
	switch($no) {
		case '1' : $gift = '예치금'; break;
		case '2' : $gift = '포인트'; break;
		case '3' : $gift = '상품권'; break;
		default  : $gift = ''; break;
	}
	return $gift;
}

function getProductCname($ca) {
	switch($ca) {
		case '1'   : $cname = '동산담보대출'; break;
		case '2'   : $cname = '부동산대출상품'; break;
		case '2-1' : $cname = '부동산PF'; break;
		case '2-2' : $cname = '주택담보대출'; break;
		case '3'   : $cname = '매출채권'; break;
		default    : $cname = '전체상품'; break;
	}
	return $cname;
}

?>
<div>

	<div style="margin:10px; text-align:right;">
		<button id="formOpen" type="button" class="btn btn-sm btn-primary" style="width:100px">이벤트 등록</button>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1200px;">
		<colgroup>
			<col style="width:80px">
			<col style="width:250px">
			<col style="width:%">
			<col style="width:180px">
			<col style="width:150px">
			<col style="width:100px">
			<col style="width:150px">
			<col style="width:100px">
			<col style="width:150px">
			<col style="width:100px">
			<col style="width:100px">
		</colgroup>
		<thead>
			<tr>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">NO</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">이벤트 타이틀</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">이벤트 설명</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">시작일</th>
				<th colspan="2" style="background:#3366CC;color:#FFF" class="text-center">피추천인 보상</th>
				<th colspan="2" style="background:#3366CC;color:#FFF" class="text-center">추천인 보상</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">펀딩상품군</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">펀딩기준금액</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">고정추천ID</th>
				<th rowspan="2" style="background:#3366CC;color:#FFF" class="text-center">수정</th>
			</tr>
			<tr>
				<th style="background:#3366CC;color:#FFF" class="text-center">상품</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">금액</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">상품</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">금액</th>
			</tr>
		</thead>
<?php
	if($rcount) {
		for($i=0,$j=1;$i<$rcount;$i++,$j++) {

?>
			<tr align="center">
				<td><?=$num?></td>
				<td><?=$LIST[$i]['event_title']?></td>
				<td align="left"><?=$LIST[$i]['event_summary']?></td>
				<td><?=$LIST[$i]['sdate']?> ~ <?=$LIST[$i]['edate']?></td>

				<td><?=($LIST[$i]['recmdee_reward_goods_name']) ? $LIST[$i]['recmdee_reward_goods_name'] : getRewardGift($LIST[$i]['recmdee_reward_type'])?></td>
				<td><?=number_format($LIST[$i]['recmdee_reward_point']);?></td>

				<td><?=($LIST[$i]['recmder_reward_goods_name']) ? $LIST[$i]['recmder_reward_goods_name'] : getRewardGift($LIST[$i]['recmder_reward_type'])?></td>
				<td><?=number_format($LIST[$i]['recmder_reward_point']);?></td>

				<td><?=getProductCname($LIST[$i]['prdt_ca'])?></td>
				<td><?=number_format($LIST[$i]['use_point'])?></td>
				<td><?=$LIST[$i]['only_rec_id']?></td>
				<td><button type="button" class="btn btn-sm btn-primary" onClick="dataEdit('<?=$LIST[$i]['event_no']?>');">수정</button></td>
			</tr>
<?
			$num--;
		}
	}
	else {
			echo "<tr><td colspan='12' align='center'>데이터가 없습니다.</td></tr>\n";
	}
?>
		</tbody>
		</form>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 20px 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>


<style>
#registDiv { display:none; position:fixed; z-index:9999; margin-left:-42px; width:100%; height:100%; top:0; }
#registDiv .formCoverDiv { margin:200px auto; width:700px; padding:8px; background:#FFF; border:2px solid #222; }
#registDiv .textbox { height:24px; font-size:14px; line-height:24px; padding-left:4px; padding-right:4px; width:120px; border:1px solid #CCC; background:#FFF; }
#registDiv .align_right { text-align:right; }
</style>
<div id="registDiv">
	<div class="formCoverDiv">
		<form name="registForm" id="registForm">
			<input type="hidden" name="event_no" id="event_no">
		<h3 id="formTitle">이벤트 등록</h3>
		<table>
			<colgroup>
				<col width="18%">
				<col width="32%">
				<col width="18%">
				<col width="32%">
			</colgroup>
			<tr>
				<td align="center" style="background:#F8F8EF">이벤트 타이틀</td>
				<td colspan="3">
					<input type="text" name="event_title" id="event_title" class="textbox" style="width:85%">
					&nbsp;
					<input type="checkbox" name="is_real" id="is_real" value='1' style="margin-top:-4px;"> <label for="is_real">개시</label>
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">이벤트 요약</td>
				<td colspan="3"><textarea name="event_summary" id="event_summary" class="textbox" style="font-size:12px;line-height:18px;width:100%;height:48px"></textarea></td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">이벤트 기간</td>
				<td colspan="3">
					<input type="text" name="sdate" id="sdate" placeholder="시작일" readonly autocomplete="off" class="textbox datepicker" style="text-align:center"> ~
					<input type="text" name="edate" id="edate" placeholder="종료일" readonly autocomplete="off" class="textbox datepicker" style="text-align:center">
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">추천인 보상</td>
				<td colspan="3">
					<select name="recmder_reward_type" id="recmder_reward_type" class="textbox" style="width:150px">
						<option value="">::보상품 선택::</option>
						<option value="1">예치금</option>
						<option value="2">포인트</option>
						<option value="3">상품권/쿠폰</option>
					</select> &nbsp;
					<input type="text" name="recmder_reward_goods_name" id="recmder_reward_goods_name" placeholder="상세보상품명" class="textbox" style="width:120px;">
					<input type="text" name="recmder_reward_point" id="recmder_reward_point" autocomplete="off" placeholder="금액" onKeyUp="onlyDigit(this);" class="textbox align_right" style="width:150px;">
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">피추천인 보상</td>
				<td colspan="3">
					<select name="recmdee_reward_type" id="recmdee_reward_type" class="textbox" style="width:150px">
						<option value="">::보상품 선택::</option>
						<option value="1">예치금</option>
						<option value="2">포인트</option>
						<option value="3">상품권/쿠폰</option>
					</select> &nbsp;
					<input type="text" name="recmdee_reward_goods_name" id="recmdee_reward_goods_name" placeholder="상세보상품명" class="textbox" style="width:120px;">
					<input type="text" name="recmdee_reward_point" id="recmdee_reward_point" placeholder="금액" onKeyUp="onlyDigit(this);" class="textbox align_right" style="width:150px;">
				</td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">지정 추천인ID</td>
				<td colspan="3">
					<input type="text" name="only_rec_id" id="only_rec_id" class="textbox" style="width:200px;">
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center" style="background:#eee">보상지급 투자조건</td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">펀딩상품군</td>
				<td>
					<select name="prdt_ca" id="prdt_ca" class="textbox" style="width:180px">
						<option value="">전체상품</option>
						<option value="2">부동산대출상품</option>
						<option value="2-1">- 부동산PF</option>
						<option value="2-2">- 주택담보대출</option>
						<option value="3">매출채권</option>
						<option value="1">동산담보대출</option>
					</select>
				</td>
				<td align="center" style="background:#F8F8EF">펀딩기준금액</td>
				<td><input type="text" name="use_point" id="use_point" value="0" onKeyUp="onlyDigit(this);" class="textbox align_right"></td>
			</tr>
		</table>
		<div style="margin:10px; text-align:center;">
			<button id="formSubmit" type="button" class="btn btn-sm btn-primary" style="width:100px">등 록</button>
			<button id="formClose" type="button" class="btn btn-sm btn-default" style="width:100px">닫 기</button>
		</div>
		</form>
	</div>
</div>

<script>
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

$('#registForm #formSubmit').on('click',function() {
	if( $('#registForm #event_title').val()=='' ) { alert('이벤트 타이틀을 입력 하십시요.'); $('#registForm #event_title').focus(); return; }
	if( $('#registForm #sdate').val()=='' ) { alert('이벤트 시작일을 입력 하십시요.'); $('#registForm #sdate').focus(); return; }
	if( $('#registForm #edate').val()=='' ) { alert('이벤트 종료일을 입력 하십시요.'); $('#registForm #edate').focus(); return; }
	if( $('#registForm #recmder_reward_type option:selected').val()=='' ) { alert('추천인 보상품 구분을 선택 하십시요.'); $('#registForm #recmder_reward_type').focus(); return; }
	if( $('#registForm #recmdee_reward_type option:selected').val()=='' ) { alert('피추천인 보상품 구분을 선택 하십시요.'); $('#registForm #recmdee_reward_type').focus(); return; }

	var action_str = ( $('#registForm #event_no').val()=='' ) ? '등록' : '수정';

	if( confirm('이벤트를 ' + action_str + ' 하시겠습니까?') ) {
		form_data = $('#registForm').serialize();

		$.ajax({
			url : 'ajax_event_regist.proc.php',
			type: 'POST',
			dataType: 'JSON',
			data: form_data,
			success:function(data) {
				if(data.result=='success') {
					alert(action_str + '완료');window.location.reload();
				}
				else {
					alert(data.message);
				}
			},
			error: function () {
				alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			}
		});
	}
});

$('#formOpen').on('click',function() {
	$('#formTitle').text('이벤트 등록');
	$('#registDiv').fadeIn();
});

$('#formClose').on('click',function() {
	$('#registDiv').fadeOut();
	document.registForm.reset();
});

function dataEdit(event_no) {

	$('#formTitle').text('이벤트 수정');
	$('#registDiv').fadeIn();

	$.ajax({
		url : 'ajax_get_event.php',
		type: 'POST',
		dataType: 'json',
		data:{'event_no':event_no},
		success:function(data) {
			$('#registForm #event_no').val(data.event_no);
			$('#registForm #event_title').val(data.event_title);
			if(data.is_real=='1') { $('#registForm #is_real').prop('checked', true); }
			$('#registForm #event_summary').val(data.event_summary);
			$('#registForm #sdate').val(data.sdate);
			$('#registForm #edate').val(data.edate);
			$('#registForm #recmder_reward_type').val(data.recmder_reward_type);
			$('#registForm #recmder_reward_goods_name').val(data.recmder_reward_goods_name);
			$('#registForm #recmder_reward_point').val(data.recmder_reward_point);
			$('#registForm #recmdee_reward_type').val(data.recmdee_reward_type);
			$('#registForm #recmdee_reward_goods_name').val(data.recmdee_reward_goods_name);
			$('#registForm #recmdee_reward_point').val(data.recmdee_reward_point);
			$('#registForm #prdt_ca').val(data.prdt_ca);
			$('#registForm #use_point').val(data.use_point);
			$('#registForm #only_rec_id').val(data.only_rec_id);
		},
		error: function (jqXHR, textStatus, errorThrown)	{
			console.log(jqXHR);
		}
	});
}
</script>
