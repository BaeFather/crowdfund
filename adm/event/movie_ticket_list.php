<?

include_once('./_common.php');

$sub_menu = "900400";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$where = " 1=1";
$where.= ($randge_all) ? "" : " AND A.mb_no!=''";
$where.= ($event_no) ? " AND A.event_no='$event_no'" : "";
if($date_field) {
	if($sdate) $where.= ($date_field=='A.give_date') ? " AND LEFT($date_field, 10)>='$sdate'" : " AND $date_field>='$sdate'";
	if($edate) $where.= ($date_field=='A.give_date') ? " AND LEFT($date_field, 10)<='$edate'" : " AND $date_field<='$edate'";
}
if($field && $keyword) {
	$where.= " AND $field LIKE BINARY '%$keyword%'";
}


$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		event_reward_coupon A
	LEFT JOIN
		g5_member B
	ON
		A.mb_no=B.mb_no
	WHERE
		$where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page_rows  = 50;
$total_page = ceil($total_count / $page_rows);							// 전체 페이지 계산
if ($page < 1) $page = 1;																		// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;										// 시작 열을 구함


$sql = "
	SELECT
		A.idx, A.serial, A.coupon_no, A.valid_date, A.rdate, A.event_no, A.event_title, A.event_caption, A.give_date, A.memo,
		B.mb_id, B.mb_name, B.mb_hp, B.mb_email
	FROM
		event_reward_coupon A
	LEFT JOIN
		g5_member B
	ON
		A.mb_no=B.mb_no
	WHERE
		$where
	ORDER BY
		A.give_date DESC,
		A.idx	DESC
	LIMIT
		$from_record, $page_rows";
$res = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);

	$avail_days = floor((strtotime($LIST[$i]['valid_date'])-time())/86400)+1;
	$LIST[$i]['avail_days'] = ($avail_days > 0) ? $avail_days : 0;

}
//print_rr($LIST, "font-size:11px");


$sql_common = "SELECT COUNT(idx) AS cnt FROM event_reward_coupon";

// 전체 카운트
$sql = $sql_common;
$DATA = sql_fetch($sql);
$PCNT[0] = $DATA['cnt'];

// 지급완료 쿠폰 카운트
$sql = $sql_common . " WHERE event_no IS NOT NULL AND mb_no!='' OR give_date IS NOT NULL";
$DATA = sql_fetch($sql);
$PCNT[1] = $DATA['cnt'];

// 미지급 쿠폰 카운트
$sql = $sql_common . " WHERE event_no IS NULL AND mb_no='' AND give_date IS NULL";
$DATA = sql_fetch($sql);
$PCNT[2] = $DATA['cnt'];

// 미지급 유효 쿠폰 카운트
$sql = $sql_common . " WHERE valid_date>NOW() AND event_no IS NULL AND mb_no='' AND give_date IS NULL";
$DATA = sql_fetch($sql);
$PCNT[3] = $DATA['cnt'];



$g5['title'] = $menu["menu900"][6][1];

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">

			<ul style="list-style:none; display:inline-block;width:100%;margin:0 0 10px 0;padding:0">
				<li style="float:left;margin-right:6px;">쿠폰전체:</li>
				<li style="float:left;margin-right:20px;"><?=number_format($PCNT[0])?>개</li>
				<li style="float:left;margin-right:6px;">지급완료쿠폰:</li>
				<li style="float:left;margin-right:20px;"><?=number_format($PCNT[1])?>개</li>
				<li style="float:left;margin-right:6px;">미지급쿠폰:</li>
				<li style="float:left;margin-right:20px;"><?=number_format($PCNT[2])?>개 (<span style="color:#FF2222">유효쿠폰: <?=number_format($PCNT[3])?>개</span>)</li>
			</ul>

			<div class="form-group">
				<form method="get" class="form-horizontal" style="margin:0">
				<ul style="list-style:none; display:inline-block;width:100%;margin:0;padding:0">
					<li style="float:left;margin-right:20px;">
						<select name="event_no" class="form-control">
							<option value=''>:: 이벤트 선택 ::</option>
<?
	$res = sql_query("SELECT event_no, event_title FROM event_reward_coupon WHERE event_title!='' GROUP BY event_no ORDER BY idx DESC");
	while($row = sql_fetch_array($res)) {
		$selected = ($row['event_no']==$event_no) ? 'selected' : '';
		echo "<option value='".$row['event_no']."' $selected>".$row['event_title']."</option>\n";
	}
?>
						</select>
					</li>
					<li style="float:left;margin-right:6px;"><label class="checkbox-inline"><input type="checkbox" name="randge_all" <?=($randge_all)?'checked':''?>>미지급쿠폰포함</label></li>
				</ul>
				<ul style="list-style:none; display:inline-block;width:100%; margin:6px 0 0 0;padding:0">
					<li style="float:left;margin-right:6px;">
						<select name="date_field" class="form-control">
							<option value=''>:: 일별검색 선택 ::</option>
							<option value='A.valid_date' <?=($date_field=='A.valid_date')?'selected':'';?>>만료일</option>
							<option value='A.give_date' <?=($date_field=='A.give_date')?'selected':'';?>>지급일</option>
							<option value='A.rdate' <?=($date_field=='A.rdate')?'selected':'';?>>등록일</option>
						</select>
					</li>
					<li style="float:left;margin-right:6px;">
						<input type="text" name="sdate" value="<?=$sdate?>" class="form-control datepicker" autocomplete="off" style="width:100px">
					</li>
					<li style="float:left;margin-right:6px;">~</li>
					<li style="float:left;margin-right:6px;">
						<input type="text" name="edate" value="<?=$edate?>" class="form-control datepicker" autocomplete="off" style="width:100px">
					</li>
				</ul>
				<ul style="list-style:none; display:inline-block;width:100%; margin:6px 0 0 0;padding:0">
					<li style="float:left;margin-right:6px;">
						<select name="field" class="form-control">
							<option value="">:: 검색조건 선택 ::</option>
							<option value="A.coupon_no" <?=($field=='A.coupon_no')?'selected':''?>>쿠폰번호</option>
							<option value="A.serial" <?=($field=='A.serial')?'selected':''?>>발행번호</option>
							<option value="A.event_title" <?=($field=='A.event_title')?'selected':''?>>이벤트명</option>
							<option value="A.event_caption" <?=($field=='A.event_caption')?'selected':''?>>이벤트요약</option>
							<option value="A.memo" <?=($field=='A.memo')?'selected':''?>>메모</option>
							<option value="B.mb_id" <?=($field=='B.mb_id')?'selected':''?>>아이디</option>
							<option value="B.mb_name"  <?=($field=='B.mb_name')?'selected':''?>>성명</option>
							<option value="B.mb_hp"  <?=($field=='B.mb_hp')?'selected':''?>>연락처</option>
						</select>
					</li>
					<li style="float:left;margin-right:6px;"><input type="text" name="keyword" value="<?=$keyword?>" class="form-control"></li>
					<li style="float:left;margin-right:6px;"><button type="submit" class="btn btn-primary">검색</button></li>
				</ul>
				</form>
			</div>

			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="font-size:12px">
					<colgroup>
						<col style="width:5%">
						<col style="width:10%"><col style="width:7%"><col style="width:7%"><col style="width:5%">
						<col style="width:"><col style="width:">
						<col style="width:8%"><col style="width:7%"><col style="width:10%"><col style="width:8%">
						<col style="width:7%">
					</colgroup>
					<thead>
						<tr bgcolor="#F8F8EF">
							<th class="text-center" rowspan="2">NO</th>
							<th class="text-center" colspan="4">쿠폰등록정보</th>
							<th class="text-center" colspan="2">이벤트정보</th>
							<th class="text-center" colspan="4">지급정보</th>
							<th class="text-center" rowspan="2">등록일</th>
						</tr>
						<tr bgcolor="#F8F8EF">
							<th class="text-center">쿠폰번호</th>
							<th class="text-center">발행번호</th>
							<th class="text-center">만료일</th>
							<th class="text-center">잔여일수</th>

							<th class="text-center">이벤트명</th>
							<th class="text-center">이벤트요약</th>

							<th class="text-center">아이디</th>
							<th class="text-center">성명</th>
							<th class="text-center">연락처</th>
							<th class="text-center">지급일시</th>
						</tr>
					</thead>
					<tbody>
<?
$num = $total_count - ($page - 1) * $page_rows;
for($i=0,$j=1; $i<count($LIST);$i++) {

?>
						<tr class="odd">
							<td align="center" <?if(trim($LIST[$i]['memo'])!='')echo'rowspan="2"';?>><?=$num?></td>
							<td align="center"><?=$LIST[$i]['coupon_no']?></td>
							<td align="center"><?=$LIST[$i]['serial']?></td>
							<td align="center"><?=$LIST[$i]['valid_date']?></td>
							<td align="center"><?=$LIST[$i]['avail_days']?>일</td>

							<td align="center"><?=$LIST[$i]['event_title']?></td>
							<td align="center"><?=$LIST[$i]['event_caption']?></td>

							<td align="center"><?=$LIST[$i]['mb_id']?></td>
							<td align="center"><?=$LIST[$i]['mb_name']?></td>
							<td align="center"><?=$LIST[$i]['mb_hp']?></td>
							<td align="center"><?=substr($LIST[$i]['give_date'], 0, 16)?></td>
							<td align="center"><?=$LIST[$i]['rdate']?></td>
						</tr>
<?
	if(trim($LIST[$i]['memo'])!='') {
?>
						<tr class="odd">
							<td colspan="11" align="center" style="background:#eee;color:red"><?=$LIST[$i]['memo']?></td>
						</tr>
<?
	}
	$num--;
}
?>
					</tbody>
				</table>
			</div>
			<div style="width:100%; text-align:center;">
				<ul class="pagination">
<?
$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
				</ul>
			</div>

		</div><!-- /.panel-body -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<?
include_once (G5_ADMIN_PATH . '/admin.tail.php');
?>