<?
/**
 * 관리자 > 예치금 관리
 */
$sub_menu = "500100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') {
	alert('최고관리자만 접근 가능합니다.');
}

while(list($k, $v)=each($_REQUEST)) { ${$k} = trim($v); }

$datetime_s = $sdate . ' 00:00:00';
$datetime_e = $edate . ' 23:59:59';

$where = " 1=1 ";

if($mb_point_start && $mb_point_end) {
	$where.= " AND A.po_point BETWEEN '{$mb_point_start}' AND '{$mb_point_end}' ";
}
else {
	if($mb_point_start) $where.= " AND A.po_point >= '{$mb_point_start}' ";
	if($mb_point_end)   $where.= " AND A.po_point <= '{$mb_point_end}' ";
}

// 처리일
if($sdate && $edate) {
	$where.= " AND A.po_datetime BETWEEN '{$datetime_s}' AND '{$datetime_e}' ";
}
else {
	if($sdate) $where.= " AND A.po_datetime >= '{$datetime_s}' ";
	if($edate) $where.= " AND A.po_datetime <= '{$datetime_e}' ";
}

if($member_type) $where.= " AND B.member_type = '{$member_type}' ";

// 출금, 충전 검색
if($po_point_use_type) {
	if($po_point_use_type == 1) {
		$where.= " AND A.po_point > 0 "; // 충전
	}
	else if($po_point_use_type == 2) {
		$where.= " AND A.po_point < 0 "; // 출금
	}
}

if($is_repay) {
	$where.= " AND po_rel_table IN('@repay','@overdue_repay','@partial_repay')";
//$where.= " AND A.po_content LIKE '%회차 원리금%'";
}

if($field && $keyword) {
	if( in_array($field, array('mb_no','mb_id')) ) {
		$where.= " AND B.{$field} = '{$keyword}' ";
	}
	else if ( in_array($field, array('po_content','po_memo')) ) {
		if($field=='po_content') {
			$where.= " AND A.{$field} LIKE '%{$keyword}%' ";
		}
		else {
			$where.= " AND B.{$field} LIKE '%{$keyword}%' ";
		}
	}
	else {
		$where.= " AND $field = '{$keyword}' ";
	}
}

if($syndi_id) {
	$ARR_KEYS = array_keys($CONF['SYNDICATOR']);
	for($i=0; $i<count($CONF['SYNDICATOR']); $i++) {
		if($ARR_KEYS[$i]==$syndi_id) {
			$where.= ($ARR_KEYS[$i]=='hktvwowstar') ? " AND B.wowstar_userid!=''" : " AND B.{$ARR_KEYS[$i]}_userid!=''";
			break;
		}
	}
}


$sql = "
	SELECT
		COUNT(A.po_id) AS cnt,
		SUM(A.po_point) AS sum
	FROM
		g5_point A
	LEFT JOIN
		g5_member B  ON A.mb_no = B.mb_no
	WHERE
		$where";
//print_rr($sql, 'font-size:12px');
$row = sql_fetch($sql);

$total_point = number_format($row['sum'] + 0);
$total_count = $row['cnt'];
$rows = 50;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;

$num = $total_count - $from_record;


$sql = "
	SELECT
		A.po_id, A.mb_id, A.po_datetime, A.po_content, A.po_point, A.po_use_point, A.po_mb_point, A.po_memo, B.mb_no, B.mb_level, B.member_type, B.member_investor_type, B.mb_point, B.mb_name, B.mb_co_name
	FROM
		g5_point A
	LEFT JOIN
		g5_member B  ON A.mb_no = B.mb_no
	WHERE
		$where
	ORDER BY
		A.po_datetime DESC,
		A.po_id DESC
	LIMIT
		$from_record, $rows";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql);
$list_count = $result->num_rows;

$page_total_point = 0;
for($i=0; $i<$list_count; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$page_total_point += $LIST[$i]['po_point'];
}

$param = array();
foreach ((array)$_REQUEST as $key => $val) {
	if (empty($val) || in_array($key, array('page'))) {
		continue;
	}
	$param[] = $key.'='.$val;
}

$qstr = join('&amp;', $param);


$g5['title'] = '예치금 상세내역';
include_once('./admin.head.php');

?>

	<div class="row">
		<div class="col-lg-12">
			<div class="col-sm-6">
				<form method="get" class="form-horizontal">
					<ul class="list-inline">
						<li>예치금</li>
						<li>
							<div class="form-inline">
								<input type="number" name="mb_point_start" value="<?=$mb_point_start?>" class="form-control input-sm" placeholder="0"> ~
								<input type="number" name="mb_point_end" value="<?=$mb_point_end?>" class="form-control input-sm" placeholder="10,000,000">
							</div>
						</li>
						<li></li>
						<li>처리일</li>
						<li>
							<div class="form-inline">
								<input type="text" id="sdate" name="sdate" value="<?=($sdate) ? $sdate : $po_datetime;?>" class="form-control input-sm datepicker" autocomplete="off" placeholder="대상일자(시작)"> ~
								<input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" autocomplete="off" placeholder="대상일자(종료)">
							</div>
						</li>
					</ul>
					<ul class="list-inline">
						<li>
							<select name="member_type" class="form-control input-sm">
								<option value="">회원구분</option>
								<option value="1" <?=($member_type == '1') ? 'selected' : ''; ?>>개인회원</option>
								<option value="2" <?=($member_type == '2') ? 'selected' : ''; ?>>법인회원</option>
								<option value="3" <?=($member_type == '3') ? 'selected' : ''; ?>>SNS회원</option>
							</select>
						</li>
						<li>
							<select name="syndi_id" class="form-control input-sm">
								<option value="">::신디케이션회원::</option>
<?
$ARR_KEYS = array_keys($CONF['SYNDICATOR']);
for($i=0; $i<count($CONF['SYNDICATOR']); $i++) {
?>
								<option value="<?=$ARR_KEYS[$i]?>" <?=($ARR_KEYS[$i]==$_GET['syndi_id'])?'selected':'';?>><?=$CONF['SYNDICATOR'][$ARR_KEYS[$i]]['name']?></option>
<?
}
?>
							</select>
						</il>
						<li></li>
						<li>
							<div class="form-inline">
								<label class="checkbox-inline"><input type="checkbox" name="po_point_use_type" value="1" <?=($po_point_use_type == 1) ? "checked" : "" ?>> 충전내역</label>
								<label class="checkbox-inline"><input type="checkbox" name="po_point_use_type" value="2" <?=($po_point_use_type == 2) ? "checked" : "" ?>> 차감내역</label>
								<label class="checkbox-inline"><input type="checkbox" name="is_repay" value="1" <?=($is_repay) ? "checked" : "" ?>> 원리금지급내역만 보기</label>
							</div>
						</li>
					</ul>
					<ul class="list-inline">
						<li>
							<select name="field" class="form-control input-sm">
								<option value="">선택</option>
								<option value="mb_no" <?=($field == 'mb_no') ? 'selected' : ''; ?>>회원번호</option>
								<option value="mb_id" <?=($field == 'mb_id') ? 'selected' : ''; ?>>아이디</option>
								<option value="mb_name" <?=($field == 'mb_name') ? 'selected' : ''; ?>>이름</option>
								<option value="mb_co_name" <?=($field == 'mb_co_name') ? 'selected' : ''; ?>>업체명(법인회원)</option>
								<option value="mb_email" <?=($field == 'mb_email') ? 'selected' : ''; ?>>이메일</option>
								<option value="mb_hp" <?=($field == 'mb_hp') ? 'selected' : ''; ?>>휴대폰</option>
								<option value="mb_tel" <?=($field == 'mb_tel') ? 'selected' : ''; ?>>전화번호</option>
								<option value="po_content" <?=($field == 'po_content') ? 'selected' : ''; ?>>충전.차감 사유</option>
								<option value="po_memo" <?=($field == 'po_memo') ? 'selected' : ''; ?>>메모</option>
							</select>
						</li>
						<li>
							<input type="text" name="keyword" value="<?=$keyword?>" class="form-control input-sm">
						</li>
						<li>
							<button type="submit" class="btn btn-primary btn-sm">검색</button>
						</li>
					</ul>
				</form>
			</div>

			<div class="panel-body" style="clear:both">
			<ul class="list-inline col-sm-12">
					<li>등록 : <?=number_format($total_count)?>건</li>
				</ul>
				<div class="dataTable_wrapper">
					<table id="dataList" class="table table-striped table-bordered table-hover table-condensed" style="font-size:12px">
						<thead>
							<tr class="bg-primary" style="font-size:13px">
								<th class="text-center"><input type="checkbox" name="chkall" id="chkall" value="1"></th>
								<th class="text-center">NO.</th>
								<th class="text-center">회원구분</th>
								<th class="text-center">ID</th>
								<th class="text-center">상호명</th>
								<th class="text-center">성명.담당자명</th>
								<th class="text-center">구분</th>
								<th class="text-center">금액</th>
								<th class="text-center">충전.차감 사유</th>
								<th class="text-center">잔여액</th>
								<th class="text-center">메모</th>
								<th class="text-center">처리일시</th>
							</tr>
						</thead>
						<tbody>
							<tr style="background:#EFEFEF;color:royalblue">
								<td colspan="2" align="center">전체합계</td>
								<td colspan="5"></td>
								<td align="right"><? echo $total_point;?></td>
								<td colspan="4"></td>
							</tr>
<?
for ($i=0; $i<$list_count; $i++) {

	$print_member_type = '';
	switch($LIST[$i]['member_type']) {
		case '1' : $print_member_type = '개인'; $fcolor = "royalblue"; break;
		case '2' : $print_member_type = '법인'; $fcolor = "red";	   break;
		case '3' : $print_member_type = 'SNS';  $fcolor = "green";	 break;
	}
	if($LIST[$i]['is_creditor']=='Y') $print_member_type.= '-대부';

	$print_flag = ($LIST[$i]["po_point"] > 0) ? '<label class="label label-success">충전</label>' : '<label class="label label-info">차감</label>';
?>
							<tr class="odd">
								<td align="center"><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['po_id'];?>"></td>
								<td align="center"><?=$num?></td>
								<td align="center"><span style="color:<?=$fcolor?>"><?=$print_member_type?></span></td>
								<td align="center"><a href="./member/member_view.php?&mb_id=<?=$LIST[$i]['mb_id'];?>"><?=$LIST[$i]['mb_id'];?></a></td>
								<td align="center"><?=$LIST[$i]['mb_co_name'];?></td>
								<td align="center"><?=$LIST[$i]['mb_name'];?></td>
								<td align="center"><?=$print_flag?></td>
								<td align="right"><?=number_format($LIST[$i]["po_point"]);?></td>
								<td align="left"><?=strip_tags($LIST[$i]["po_content"]);?></span></td>
								<td align="right"><?=number_format($LIST[$i]["po_mb_point"]);?></td>
								<td align="left"><span style="color:#FF2222;font-size:12px;"><?=nl2br(strip_tags($LIST[$i]["po_memo"]));?></span></td>
								<td align="center"><?=substr($LIST[$i]["po_datetime"], 0, 16);?></td>
							</tr>
<?
	$num--;
}
?>

<? if($total_page > 1) { ?>
							<tr style="background:#EFEFEF;color:red">
								<td colspan="2" align="center">페이지합계</td>
								<td colspan="5"></td>
								<td align="right"><?=number_format($page_total_point);?></td>
								<td colspan="4"></td>
							</tr>
<? } ?>
						</tbody>
					</table>
				</div>
			</div>

			<? echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');?>
		</div>
	</div>

	<script type="text/javascript">
		$(function() {
			$("input[name=chkall]").click(function() {
				$("input[name='chk[]']").prop('checked', this.checked);
			});
		});

		$('input:checkbox[name="po_point_use_type"]').on('change', function() {
			$('input:checkbox[name="po_point_use_type"]').not(this).prop('checked', false);
		});

		$("#submit1").on('click', function() {
			f = document.point_form;
			if(f.member_select.value=='') { alert('회원범위를 선택하십시요.'); f.member_select.focus(); }
			else if(f.balance.value=='') { alert('금액을 입력하십시요.'); f.balance.focus(); }
			else if(f.balance_select.value=='') { alert('지급 또는 차감 선택하십시요.'); f.balance_select.focus(); }
			else {
				if(confirm(' 실행 하시겠습니까? ')) {
					f.method = 'post';
					f.action = 'register_process.php';
					f.submit();
				}
			}
		});

		$(document).ready(function() {
			$('#dataList').floatThead();
		});
	</script>

<? include_once ('./admin.tail.php'); ?>