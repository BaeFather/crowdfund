<?

$event_idx = trim($_REQUEST['event_idx']);
$field     = trim($_REQUEST['field']);
$keyword   = trim($_REQUEST['keyword']);
$device    = trim($_REQUEST['device']);
$view_flag = trim($_REQUEST['view_flag']);

$where = " 1=1 ";
$where.= ($member['mb_id']=='seintax') ? " AND event_idx='3'" : "";		// 세인법무법인 관리자는 법인설립안내센터 상담신청 관련된 데이터만 보기
$where.= ($event_idx) ? " AND event_idx='$event_idx'" : "";
$where.= ($device) ? " AND device='$device' " : "";
$where.= ($view_flag) ? " AND view_flag='$view_flag' " : "";
$where.= ($field && $keyword) ? " AND $field LIKE '%$keyword%' " : "";

$sql = "
	SELECT
		COUNT(idx) AS cnt_idx
	FROM
		invitation_event_request
	WHERE
		$where";
$row = sql_fetch($sql);
$total_count = $row['cnt_idx'];

$page_rows  = $config['cf_page_rows'];
$total_page = ceil($total_count / $page_rows);							// 전체 페이지 계산
if ($page < 1) $page = 1;																		// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;										// 시작 열을 구함

$sql = "
	SELECT
		*
	FROM
		invitation_event_request
	WHERE
		$where
	ORDER BY
		event_idx	DESC, idx DESC
	LIMIT
		$from_record, $page_rows";

$res = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$LIST[] = sql_fetch_array($res);
}
//print_rr($LIST, "font-size:11px");

?>

			<div class="form-group">
				<form method="get" class="form-horizontal" style="margin:0;">
				<ul style="list-style:none;">
					<li style="float:left;">
						<select name="event_idx" class="form-control">
							<option value=''>::전체 이벤트::</option>
<?
$res1 = sql_query("SELECT idx, title, sdate, edate FROM invitation_event ORDER BY idx DESC");
$event_rows = $res1->num_rows;
for($i=0; $i<$event_rows; $i++) {
	$EVENT[$i] = sql_fetch_array($res1);
	$event_title[$EVENT[$i]['idx']] = $EVENT[$i]['title'];
	$selected = ($event_idx==$EVENT[$i]['idx']) ? 'selected' : '';

	echo "<option value='".$EVENT[$i]['idx']."' $selected>".$EVENT[$i]['title']." (".$EVENT[$i]['sdate']." ~ ".$EVENT[$i]['edate'].")</option>\n";
}
?>
						</select>
					</li>
					<li style="float:left;margin-left:4px;">
						<select name="field" class="form-control">
							<option value="">::항목선택::</option>
							<option value="nm_co_name" <?=($field=='nm_co_name')?'selected':''?>>업체명</option>
							<option value="nm_name"  <?=($field=='nm_name')?'selected':''?>>성명</option>
							<option value="nm_phone"  <?=($field=='nm_phone')?'selected':''?>>연락처</option>
						</select>
					</li>
					<li style="float:left;margin-left:4px;"><input type="text" name="keyword" value="<?=$keyword?>" class="form-control"></li>
					<li style="float:left;margin-left:4px;"><button type="submit" class="btn btn-primary">검색</button></li>
				</ul>
				</form>
			</div>
			<br><br>

			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center"><input type="checkbox" name="chkall" value="1"></th>
							<th class="text-center">NO</th>
							<th class="text-center">이벤트명</th>
							<th class="text-center">업체명</th>
							<th class="text-center">성명</th>
							<th class="text-center">연락처</th>
							<th class="text-center">상담신청일시</th>
							<th class="text-center">관리자확인</th>
							<th class="text-center">플랫폼</th>
							<th class="text-center">IP</th>
							<th class="text-center">등록일시</th>
							<th class="text-center">관리툴</th>
						</tr>
					</thead>
					<form id="fList">
					<tbody>
<?
if(count($LIST)) {
	$list_num = $total_count - ($page - 1) * $page_rows;
	for($i=0,$j=1; $i<count($LIST);$i++) {

		if($LIST[$i]['mb_no']) {
			$MEM = sql_fetch("SELECT mb_name, mb_co_name, mb_hp FROM g5_member WHERE mb_no='".$LIST[$i]['mb_no']."'");
			$name    = $MEM['mb_name'];
			$co_name = $MEM['mb_co_name'];
			$phone   = masterDecrypt($MEM['mb_hp'], false);
		}
		else {
			$name    = $LIST[$i]['nm_name'];
			$co_name = $LIST[$i]['nm_co_name'];
			$phone   = $LIST[$i]['nm_phone'];
		}

		$print_view_flag = ($LIST[$i]['view_flag']=='Y') ? '확인' : '<font style="color:#FF2222">미확인</font>';

?>
						<tr class="odd">
							<td align="center"><input type="checkbox" name="chk[]" value="<?=$LIST[$i]['idx']?>"></td>
							<td align="center"><?=$list_num?></td>
							<td align="center"><?=$event_title[$LIST[$i]['event_idx']]?></td>
							<td align="center"><?=$co_name?></td>
							<td align="center"><?=$name?></td>
							<td align="center"><?=$phone?></td>
							<td align="center"><?=substr(preg_replace("/-/", ".", $LIST[$i]['schedule_req_date']), 0, 16)?></td>
							<td align="center"><?=$print_view_flag?></td>
							<td align="center"><?=$LIST[$i]['device']?></td>
							<td align="center"><?=$LIST[$i]['ip']?></td>
							<td align="center"><?=preg_replace("/-/", ".", substr($LIST[$i]['rdate'], 0, 16))?></td>
							<td align="center"><a href="javascript:;" onClick="detail(<?=$LIST[$i]['idx']?>);" class="btn btn-info">메모등록</a></td>
						</tr>
<?
		$list_num--;
	}
}
else {
	echo '<tr class="odd"><td colspan="11" align="center">데이터가 없습니다.</td></tr>' . PHP_EOL;
}
?>
					</tbody>
					</form>
				</table>
			</div>

			<div style="width:100%; text-align: center;">
				<ul class="pagination">
<?
$qstr = @preg_replace("/?page=([0-9]){1,10}|&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
$qstr = @preg_replace("/?idx=([0-9]){1,10}|&idx=([0-9]){1,10}/", "", $qstr);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
				</ul>
			</div>

			<script>
			$(function() {
				$("input[name=chkall]").click(function() {
					$("input[name='chk[]']").prop('checked', this.checked);
				});
			});

			detail = function(no) {
				<?
					$qstr2 = @preg_replace("/&idx=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
				?>
				location.href='?<?=$qstr2?>&idx=' + no;
			}
			</script>
