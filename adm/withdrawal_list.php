<?
$sub_menu = '500200';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu500'][3][1];
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

foreach($_REQUEST as $k=>$v) { ${$k} = trim($v); }

$state = ($state) ? $state : '2';
$sdate = ($sdate) ? $sdate : date('Y-m-d');
$edate = ($edate) ? $edate : date('Y-m-d');

$date_s = $sdate . ' 00:00:00';
$date_e = $edate . ' 23:59:59';

if($sdate && $edate) {
	if($sdate > $edate) alert("일자검색조건이 정상적이지 않습니다.");
}


$common_sql = "
	SELECT
		COUNT(A.idx) AS cnt,
		IFNULL(SUM(A.req_price),0) AS sum_price
	FROM
		g5_withdrawal A
	LEFT JOIN
		g5_member B  ON A.mb_no=B.mb_no
	WHERE 1";

//일반 출금내역 합계용 조건절
$where = "";
if($state) { $where.= " AND A.state='$state' "; }
if($sdate && $edate) {
	$where.= " AND A.regdate BETWEEN '$date_s' AND '$date_e' ";
}
else {
	if($sdate) { $where.= " AND A.regdate>='$date_s' "; }
	if($edate) { $where.= " AND A.regdate<='$date_e' "; }
}
if($field && $keyword) {
	if($field == 'mb_no') {
		if( preg_match("/\,/", $keyword) ) {
			$where.= " AND B.mb_no IN(".preg_replace("/( )/", "", $keyword).") ";
		}
		else {
			$where.= " AND B.mb_no='$keyword' ";
		}
	}
	else if($field == 'mb_id')   $where.= " AND B.mb_id LIKE '%{$keyword}%' ";
	else if($field == 'mb_name') $where.= " AND (B.mb_name LIKE '%{$keyword}%' OR B.mb_co_name LIKE '%{$keyword}%')";
	else if($field == 'mb_hp')   $where.= " AND B.mb_hp='".masterEncrypt($keyword, false)."' ";
}

//원리금 지급내역 합계용 조건절
$where2 = "";
//if($ib_regist) $where2 = " AND B.ib_regist='1'";
if($sdate && $edate) {
	$where2.= " AND A.banking_date BETWEEN '$date_s' AND '$date_e' ";
}
else {
	if($sdate) { $where2.= " AND A.banking_date>='$date_s' "; }
	if($edate) { $where2.= " AND A.banking_date<='$date_e' "; }
}
if($field && $keyword) {
	if($field == 'mb_no') {
		if( preg_match("/\,/", $keyword) ) {
			$where2.= " AND C.mb_no IN(".preg_replace("/( )/", "", $keyword).") ";
		}
		else {
			$where2.= " AND C.mb_no='$keyword' ";
		}
	}
	else if($sql_search == 'C.mb_id') {
		$where2.= " AND $key_search='$keyword' ";
	}
	else if($field == 'mb_name') $where2.= " AND (C.mb_name LIKE '%{$keyword}%' OR C.mb_co_name LIKE '%{$keyword}%')";
	else if($field == 'mb_hp')   $where2.= " AND C.mb_hp LIKE '%{$keyword}%' ";
}


//$WITHDRAWAL['REQ_SUM'] = sql_fetch($common_sql . " WHERE (1) $where");
$WITHDRAWAL['REQ']     = sql_fetch($common_sql . " $where AND state='1'");
$WITHDRAWAL['SUCC']    = sql_fetch($common_sql . " $where AND state='2'");
$WITHDRAWAL['HOLD']    = sql_fetch($common_sql . " $where AND state='3'");
$WITHDRAWAL['TOTAL']['cnt'] = $WITHDRAWAL['REQ']['cnt'] + $WITHDRAWAL['SUCC']['cnt'] + $WITHDRAWAL['HOLD']['cnt'];
$WITHDRAWAL['TOTAL']['sum_price'] = $WITHDRAWAL['REQ']['sum_price'] + $WITHDRAWAL['SUCC']['sum_price'] + $WITHDRAWAL['HOLD']['sum_price'];


$common_sql2 = "
	SELECT
		COUNT(A.idx) AS cnt,
		( IFNULL(SUM(A.interest),0) + IFNULL(SUM(A.principal),0) ) AS sum_price
	FROM
		cf_product_give A
	--	LEFT JOIN cf_product_invest B  ON A.invest_idx=B.idx
	LEFT JOIN
		g5_member C  ON A.member_idx=C.mb_no
	WHERE 1
		$where2";

//print_rr($common_sql2 );

$INVEST_GIVE['BANKING'] = sql_fetch($common_sql2 . " $where2 AND A.receive_method='1'");
if($receive_method_all=='Y') $INVEST_GIVE['POINT'] = sql_fetch($common_sql2 . " $where2 AND A.receive_method='2'");

$INVEST_GIVE['TOTAL']['cnt']       = $INVEST_GIVE['BANKING']['cnt'] + $INVEST_GIVE['POINT']['cnt'];
$INVEST_GIVE['TOTAL']['sum_price'] = $INVEST_GIVE['BANKING']['sum_price'] + $INVEST_GIVE['POINT']['sum_price'];

$PAYMENT['TOTAL']['cnt']       = $WITHDRAWAL['TOTAL']['cnt'] + $INVEST_GIVE['TOTAL']['cnt'];
$PAYMENT['TOTAL']['sum_price'] = $WITHDRAWAL['TOTAL']['sum_price'] + $INVEST_GIVE['TOTAL']['sum_price'];

?>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<div style="display:inline-block;width:100%;">
		<div style="float:left;width:40%;">
			<form id="member_list_frm" method="get" class="form-horizontal">
				<ul class="list-inline">
					<li>처리현황:</li>
					<li>
						<select name="state" class="form-control" style="width:120px">
							<option value="all" <? if($state=='all'){echo 'selected';} ?>>전체</option>
							<option value="1" <? if($state=='1'){echo 'selected';} ?>>출금 신청</option>
							<option value="2" <? if($state=='2'){echo 'selected';} ?>>출금 완료</option>
							<option value="3" <? if($state=='3'){echo 'selected';} ?>>출금 신청 취소</option>
						</select>
					</li>
				</ul>
				<ul class="list-inline">
					<li>검색기간:</li>
					<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control datepicker" style="width:120px"></li>
					<li>~</li>
					<li><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control datepicker" style="width:120px"></li>
				</ul>
				<ul class="list-inline">
					<li>조건검색:</li>
					<li>
						<select name="field" class="form-control">
							<option value="">필드선택</option>
							<option value="mb_no" <?=($field=='mb_no')?'selected' : '';?>>회원번호</option>
							<option value="mb_id" <?=($field=='mb_id')?'selected' : '';?>>아이디</option>
							<option value="mb_name" <?=($field=='mb_name')?'selected' : '';?>>성명/법인명</option>
							<option value="mb_hp" <?=($field=='mb_hp')?'selected' : '';?>>휴대폰</option>
						</select>
					</li>
					<li><input type="text" class="form-control" name="keyword" size="30" value="<?=$keyword?>"></li>
				</ul>
				<ul class="list-inline">
					<li><label class="checkbox-inline"><input type="checkbox" name="receive_method_all" value="Y" <?=($receive_method_all=='Y')?'checked':''?>> 예치금으로 지급한 내역 포함</label></li>
					<li style="margin-right:30px;"><label class="checkbox-inline"><input type="checkbox" name="ib_regist" value="1" <?=($ib_regist=='1')?'checked':''?>> 신한은행 예치금계좌 출금 내역만 보기</label></li>
					<li><button type="submit" class="btn btn-primary" style="width:100px;">검색</button></li>
				</ul>
			</form>
		</div>
		<!-- 검색영역 E N D -->

		<div style="float:left;margin-left:1%;width:59%;">
			<table>
				<colgroup>
					<col width="33.33%">
					<col width="33.33%">
					<col width="33.33%">
				</colgroup>
				<tr>
					<td align="center" bgcolor="#F8F8EF"><strong>전체</strong></td>
					<td align="right"><strong><?=number_format($PAYMENT['TOTAL']['cnt'])?>건</strong></td>
					<td align="right"><strong><?=number_format($PAYMENT['TOTAL']['sum_price'])?>원</strong></td>
				</tr>
				<tr>
					<td align="center" bgcolor="#F8F8EF">일반출금 완료</td>
					<td align="right"><?=number_format($WITHDRAWAL['SUCC']['cnt'])?>건</td>
					<td align="right"><?=number_format($WITHDRAWAL['SUCC']['sum_price'])?>원</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#F8F8EF">일반출금 신청(미지급)</td>
					<td align="right"><?=number_format($WITHDRAWAL['REQ']['cnt'])?>건</td>
					<td align="right"><?=number_format($WITHDRAWAL['REQ']['sum_price'])?>원</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#F8F8EF">일반출금 신청취소</td>
					<td align="right"><?=number_format($WITHDRAWAL['HOLD']['cnt'])?>건</td>
					<td align="right"><?=number_format($WITHDRAWAL['HOLD']['sum_price'])?>원</td>
				</tr>
				<tr>
					<td align="center" bgcolor="#F8F8EF">원리금지급 (환급계좌)</td>
					<td align="right"><?=number_format($INVEST_GIVE['BANKING']['cnt'])?>건</td>
					<td align="right"><?=number_format($INVEST_GIVE['BANKING']['sum_price'])?>원</td>
				</tr>
<?
	if($receive_method_all=='Y') {
?>
				<tr>
					<td align="center" bgcolor="#F8F8EF">원리금 지급 (예치금)</td>
					<td align="right"><?=number_format($INVEST_GIVE['POINT']['cnt'])?>건</td>
					<td align="right"><?=number_format($INVEST_GIVE['POINT']['sum_price'])?>원</td>
				</tr>
<?
	}
?>
			</table>
		</div>
	</div>

	<h3>[일반 출금내역]</h3>
	<div id="list_area1" style="margin-bottom:30px"></div>

	<h3>[원리금 지급내역(환급계좌 출금)]</h3>
	<div id="list_area2" style="margin-bottom:30px"></div>

</div>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>

<script>
$(document).ready(function() {
	$.ajax({
		url  : "withdrawal_list.ajax.php",
		type : "POST",
		data : { state:'<?=$state?>', sdate:'<?=$sdate?>', edate:'<?=$edate?>', field:'<?=$field?>', keyword:'<?=urlencode($keyword)?>', page:'<?=$page?>' },
		success: function(data) {
			$('#list_area1').html(data);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});

	$.ajax({
		url  : "withdrawal_list2.ajax.php",
		type : "POST",
		data : { state:'<?=$state?>', sdate:'<?=$sdate?>', edate:'<?=$edate?>', field:'<?=$field?>', keyword:'<?=urlencode($keyword)?>', receive_method_all:'<?=$receive_method_all?>', ib_regist:'<?=$ib_regist?>', keyword:'<?=urlencode($keyword)?>' },
		success: function(data) {
			$('#list_area2').html(data);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
});

$(document).on('click', '#paging_span span.btn_paging', function() {
	$.ajax({
		url  : "withdrawal_list.ajax.php",
		type : "POST",
		data : { state:'<?=$state?>', sdate:'<?=$sdate?>', edate:'<?=$edate?>', field:'<?=$field?>', keyword:'<?=urlencode($keyword)?>', page:$(this).attr('data-page') },
		success: function(data){
			$('#list_area1').html(data);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
});

$(document).on('click', '#paging_span2 span.btn_paging', function() {
	$.ajax({
		url  : "withdrawal_list2.ajax.php",
		type : "POST",
		data : { state:'<?=$state?>', sdate:'<?=$sdate?>', edate:'<?=$edate?>', field:'<?=$field?>', keyword:'<?=urlencode($keyword)?>', receive_method_all:'<?=$receive_method_all?>', ib_regist:'<?=$ib_regist?>', page:$(this).attr('data-page') },
		success: function(data){
			$('#list_area2').html(data);
		},
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});
});
</script>