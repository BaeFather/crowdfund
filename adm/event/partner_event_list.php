<?
///////////////////////////////////////////////////////////////////////////////
// 파트너 이벤트 목록 관리
//   이벤트 기간중 해당 이벤트의 pid를 가지고 가입한 회원은
//   가입시 cf_partner_event_reward_log 에 기록이 된다.
///////////////////////////////////////////////////////////////////////////////
include_once("_common.php");


$where = "1=1";

$total_count = sql_fetch("SELECT COUNT(idx) AS cnt FROM {$TABLE['event_config']} WHERE $where")['cnt'];

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
		A.*,
		( SELECT COUNT(idx) FROM cf_partner_event_reward_log WHERE event_no=A.event_no ) AS join_count
	FROM
		cf_partner_event_config A
	WHERE
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

	$LIST[$i]['invest_rwd_target_count'] = 0;

	if( ($LIST[$i]['sdate'] && $LIST[$i]['sdate'] > '0000-00-00') && ($LIST[$i]['edate'] && $LIST[$i]['edate'] > '0000-00-00') ) {
		$sdt = $LIST[$i]['sdate'] . " 00:00:00";
		$edt = $LIST[$i]['edate'] . " 23:59:59";

		if($LIST[$i]['invest_rwd_give']=='1') {

			$where3 = "";

			$join_mb_arr = '';
			$res = sql_query("SELECT member_idx FROM cf_partner_event_reward_log WHERE event_no='".$LIST[$i]['event_no']."'");
			while( $R = sql_fetch_array($res) ) {
				$join_mb_arr.= $R['member_idx'].",";
			}
			$join_mb_arr = substr($join_mb_arr, 0, -1);

			if($LIST[$i]['invest_rwd_cond']=='2') {
				// 보상기준:2 = 목표 누적투자금 달성시 지급 타입

				if($LIST[$i]['invest_prdt_ca']) {
					if($LIST[$i]['invest_prdt_ca'] == '2-1')			$where3 = "AND BB.category = '2' AND BB.mortgage_guarantees = ''";
					else if($LIST[$i]['invest_prdt_ca'] == '2-2')	$where3 = "AND BB.category = '2' AND BB.mortgage_guarantees = '1'";
					else																					$where3 = "AND BB.category = '".$LIST[$i]['invest_prdt_ca']."'";
				}

				$sql3 = "
					SELECT
						A.member_idx
						/*
						,(
							SELECT
								IFNULL(SUM(amount),0)
							FROM
								cf_product_invest AA
							LEFT JOIN
								cf_product BB  ON AA.product_idx = BB.idx
							WHERE 1
								AND AA.member_idx = A.member_idx
								AND AA.insert_date BETWEEN '".$LIST[$i]['sdate']."' AND '".$LIST[$i]['edate']."'
								AND (AA.invest_state = 'Y' OR (AA.invest_state = 'N' AND AA.cancel_by IN('admin','system')))
								$where3
						) AS nujuk_invest_amt
						*/
					FROM
						cf_product_invest A
					WHERE 1
						AND A.member_idx IN($join_mb_arr)
						AND (
							SELECT
								IFNULL(SUM(amount),0)
							FROM
								cf_product_invest AA
							LEFT JOIN
								cf_product BB  ON AA.product_idx = BB.idx
							WHERE 1
								AND AA.member_idx = A.member_idx
								AND AA.insert_date BETWEEN '".$LIST[$i]['sdate']."' AND '".$LIST[$i]['edate']."'
								AND (AA.invest_state = 'Y' OR (AA.invest_state = 'N' AND AA.cancel_by IN('admin','system')))
								$where3
						) >= '".$LIST[$i]['invest_use_amt']."'
					GROUP BY
						A.member_idx";
				//if($member['mb_id']=='admin_sori9th') print_rr($sql3);
				$res3 = sql_query($sql3);

				$LIST[$i]['invest_rwd_target_count'] = $res3->num_rows;

			}
			else {
				// 보상기준:1 = 목표금액 투자건 발생시 지급 타입

				if($LIST[$i]['invest_prdt_ca']) {
					if($LIST[$i]['invest_prdt_ca'] == '2-1')			$where3.= "AND B.category = '2' AND B.mortgage_guarantees = ''";
					else if($LIST[$i]['invest_prdt_ca'] == '2-2')	$where3.= "AND B.category = '2' AND B.mortgage_guarantees = '1'";
					else																					$where3.= "AND B.category = '".$LIST[$i]['invest_prdt_ca']."'";
				}

				$sql3 = "
					SELECT
						DISTINCT(A.member_idx)
					FROM
						cf_product_invest A
					LEFT JOIN
						cf_product B  ON A.product_idx = B.idx
					WHERE 1
						AND A.member_idx IN($join_mb_arr)
						AND A.insert_date BETWEEN '".$LIST[$i]['sdate']."' AND '".$LIST[$i]['edate']."'
						AND (A.invest_state = 'Y' OR (A.invest_state = 'N' AND A.cancel_by IN('admin','system')))
						AND A.amount >= '".$LIST[$i]['invest_use_amt']."'
						{$where3}";
				//if($member['mb_id']=='admin_sori9th') print_rr($sql3);
				$res3 = sql_query($sql3);

				$LIST[$i]['invest_rwd_target_count'] = $res3->num_rows;

			}

		}
	}

}

//if($member['mb_id']=='admin_sori9th') { print_rr($LIST,'font-size:12px;line-height:14px;'); }

$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

?>
<style>
.table th.border_r { border-right:1px solid #999; }
.table td.border_r { border-right:1px solid #999; }
.table th.border_l { border-left:1px solid #999; }
.table td.border_; { border-left:1px solid #999; }
.btnX1 { width:40px;height:40px; padding:0;line-height:14px }
</style>

<div>

	<div style="padding:10px 0 15px;">
		등록: <?=number_format($total_count)?>건
		<button id="formOpen" type="button" class="btn btn-sm btn-primary" style="width:100px;float:right">이벤트 등록</button>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1200px;">
		<colgroup>
			<col style="width:80px">
			<col style="width:200px">
			<col style="width:220px">
			<col style="width:120px">
			<col style="width:170px">
			<col style="width:200px">
			<col style="width:80px">
			<col style="width:70px">
			<col style="width:160px">
			<col style="width:120px">
			<col style="width:80px">
			<col style="width:80px">
			<col style="width:80px">
			<col style="min-width:110px">
		</colgroup>
		<thead>
			<tr>
				<th rowspan="2" style="background:#F8F8EF" class="text-center border_r">고유번호</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">이벤트 타이틀</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">이벤트 설명</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">공급자<br/>(PID)</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">기간</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center">가입보상품목</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center border_r">참여자수</th>
				<th colspan="6" style="background:#F8F8EF" class="text-center border_r">기간내 투자보상</th>
				<th rowspan="2" style="background:#F8F8EF" class="text-center border_l">EDIT</th>
			</tr>
			<tr>
				<th style="background:#F8F8EF" class="text-center">시행여부</th>
				<th style="background:#F8F8EF" class="text-center">보상조건</th>
				<th style="background:#F8F8EF" class="text-center">대상상품군</th>
				<th style="background:#F8F8EF" class="text-center">보상예치금</th>
				<th style="background:#F8F8EF" class="text-center">보상포인트</th>
				<th style="background:#F8F8EF" class="text-center border_r">대상건수</th>
			</tr>
		</thead>
<?
	if($rcount) {
		for($i=0,$j=1;$i<$rcount;$i++,$j++) {

			$invest_rwd_give = $invest_use_amt  = $invest_prdt_ca  = $invest_rwd_amt = $invest_rwd_point = $invest_rwd_target_count = '';

			if($LIST[$i]['invest_rwd_give']=='1') {
				$invest_rwd_give  = '시행';

				if($LIST[$i]['invest_rwd_cond']=='1') {
					$invest_use_amt.= "단일상품<br/>\n" . price_cutting($LIST[$i]['invest_use_amt']) . "원 이상 투자시";
				}
				else if($LIST[$i]['invest_rwd_cond']=='2') {
					$invest_use_amt.= "누적투자<br/>\n" . price_cutting($LIST[$i]['invest_use_amt']) . "원 달성시";
				}

				$invest_prdt_ca   = getProductCname($LIST[$i]['invest_prdt_ca']);
				$invest_rwd_amt   = ($LIST[$i]['invest_rwd_amt']) ? number_format($LIST[$i]['invest_rwd_amt']) . "원" : "-";
				$invest_rwd_point = ($LIST[$i]['invest_rwd_point']) ? number_format($LIST[$i]['invest_rwd_point']) . "p" : "-";
				$invest_rwd_target_count = ($LIST[$i]['invest_rwd_target_count']) ? number_format($LIST[$i]['invest_rwd_target_count']) . "" : "-";
			}

			$fcolor = ($LIST[$i]['is_real']=='1') ? '#000' : '#CCC';

?>
			<tr align="center" style="font-size:12px;color:<?=$fcolor?>">
				<td class="border_r"><?=$LIST[$i]['event_no']?></td>
				<td><?=$LIST[$i]['event_title']?></td>
				<td><?=$LIST[$i]['event_summary']?></td>
				<td><?=$LIST[$i]['provider_name']?><br/>(<?=$LIST[$i]['pid']?>)</td>
				<td><?=preg_replace("/-/", ".", $LIST[$i]['sdate'])?> ~ <?=preg_replace("/-/", ".", $LIST[$i]['edate'])?></td>
				<td><?=$LIST[$i]['coupon_name']?></td>
				<td class="border_r"><?=number_format($LIST[$i]['join_count'])?></td>

				<td><?=$invest_rwd_give?></td>
				<td><?=$invest_use_amt?></td>
				<td><?=$invest_prdt_ca?></td>
				<td><?=$invest_rwd_amt?></td>
				<td><?=$invest_rwd_point?></td>
				<td class="border_r"><?=$invest_rwd_target_count?></td>

				<td>
					<button type="button" class="btn btn-sm btn-success btnX1" onClick="location.href='?view=reward&event_no=<?=$LIST[$i]['event_no']?>';">참여<br/>내역</button>
					<button type="button" class="btn btn-sm btn-primary btnX1" onClick="dataEdit('<?=$LIST[$i]['event_no']?>');">수정</button>
				</td>
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
#registDiv .textbox { height:24px; font-size:12px; line-height:24px; padding-left:4px; padding-right:4px; width:120px; border:1px solid #CCC; background:#FFF; }
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
				<td align="center" style="background:#F8F8EF">파트너ID</td>
				<td><input type="text" name="pid" id="pid" autocomplete="off" class="textbox"></td>
				<td align="center" style="background:#F8F8EF">공급자명</td>
				<td><input type="text" name="provider_name" id="provider_name" autocomplete="off" class="textbox"></td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">쿠폰명</td>
				<td><input type="text" name="coupon_name" id="coupon_name" class="textbox" style="width:200px;"></td>
				<td align="center" style="background:#F8F8EF">쿠폰금액</td>
				<td><input type="text" name="coupon_point" id="coupon_point" autocomplete="off" placeholder="쿠폰금액" onKeyUp="onlyDigit(this);" class="textbox align_right" style="width:150px;"></td>
			</tr>

			<tr>
				<td colspan="4" align="center" style="background:#eee">기간내 투자 보상 설정</td>
			</tr>
			<tr>
				<td align="center" style="background:#F8F8EF">시행여부</td>
				<td>
					<label for="invest_rwd_give"><input type="checkbox" id="invest_rwd_give" name="invest_rwd_give" value="1"> 시행</label>
				</td>
				<td align="center" style="background:#F8F8EF">펀딩상품군</td>
				<td>
					<select name="invest_prdt_ca" id="invest_prdt_ca" class="textbox" style="width:180px">
						<option value="">전체상품</option>
						<option value="2">부동산대출상품</option>
						<option value="2-1">- 부동산PF</option>
						<option value="2-2">- 주택담보대출</option>
						<option value="3">매출채권</option>
						<option value="1">동산담보대출</option>
					</select>
				</td>
			</tr>

			<tr>
				<td align="center" style="background:#F8F8EF">보상발생구분</td>
				<td>
					<select name="invest_rwd_cond" id="invest_rwd_cond" class="textbox" style="width:180px">
						<option value="1">단일상품투자금액</option>
						<option value="2">누적투자금액</option>
					</select>
				</td>
				<td align="center" style="background:#F8F8EF">펀딩기준금액</td>
				<td><input type="text" name="invest_use_amt" id="invest_use_amt" value="0" onKeyUp="onlyDigit(this);" class="textbox align_right"></td>
			</tr>

			<tr>
				<td align="center" style="background:#F8F8EF">보상예치금</td>
				<td><input type="text" name="invest_rwd_amt" id="invest_rwd_amt" value="0" onKeyUp="onlyDigit(this);" class="textbox align_right"></td>
				<td align="center" style="background:#F8F8EF">보상포인트</td>
				<td><input type="text" name="invest_rwd_point" id="invest_rwd_point" value="0" onKeyUp="onlyDigit(this);" class="textbox align_right" disabled></td>
			</tr>

			<tr>
				<td align="center" style="background:#F8F8EF">발송메세지<br>(변환용)</td>
				<td colspan="3"><textarea name="message" id="message" value="" class="textbox" style="width:100%;height:150px"></textarea></td>
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
			url : 'ajax_partner_event_regist.proc.php',
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
		url : 'ajax_get_partner_event.php',
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
			$('#registForm #pid').val(data.pid);
			$('#registForm #provider_name').val(data.provider_name);
			$('#registForm #coupon_name').val(data.coupon_name);
			$('#registForm #coupon_point').val(data.coupon_point);
			if(data.invest_rwd_give=='1') { $('#registForm #invest_rwd_give').prop('checked', true); }
			$('#registForm #invest_prdt_ca').val(data.invest_prdt_ca);
			$('#registForm #invest_rwd_cond').val(data.invest_rwd_cond);
			$('#registForm #invest_use_amt').val(data.invest_use_amt);

			$('#registForm #invest_rwd_amt').val(data.invest_rwd_amt);
			//$('#registForm #invest_rwd_point').val(data.invest_rwd_point);

			$('#registForm #message').val(data.message);

		},
		error: function (jqXHR, textStatus, errorThrown)	{
			console.log(jqXHR);
		}
	});
}
</script>
