<?
$sub_menu = "601300";
include_once('./_common.php');


$g5['title'] = $menu['menu601'][3][1];
include_once (G5_ADMIN_PATH.'/admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$sql_search = "1=1";
if($exec_mode=='real') $sql_search.= " AND A.is_real='1' ";
else if($exec_mode=='test') $sql_search.= " AND A.is_real='' ";
if($product_idx) $sql_search.= " AND B.idx='$product_idx' ";
if($ai_grp_idx) $sql_search.= " AND B.ai_grp_idx='$ai_grp_idx' ";
if($date_field) {
	if($sdate) $sql_search.= " AND LEFT($date_field, 10)>='$sdate' ";
	if($edate) $sql_search.= " AND LEFT($date_field, 10)<='$edate' ";
}
if($key_search && $keyword) {
	$sql_search .= " AND $key_search LIKE '%$keyword%' ";
}

$sql = "
	SELECT
		COUNT(*) AS cnt
	FROM
		cf_auto_invest_log A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	LEFT JOIN
		cf_auto_invest_config C  ON B.ai_grp_idx=C.idx
	WHERE
		$sql_search";
//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') print_rr($sql, 'font-size:12px');

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page_rows = 50;
$total_page = ceil($total_count / $page_rows);		// 전체 페이지 계산
$page = ($page) ? $page : 1;											// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;					// 시작 열을 구함

$sql_order = "A.exec_date DESC";

$sql = "
	SELECT
		A.exec_date, A.product_idx, A.target_cnt, A.exec_cnt, A.drop_cnt, A.is_real, A.rdate, A.fdate
		,B.ai_grp_idx, B.title
		,C.grp_title
	FROM
		cf_auto_invest_log A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	LEFT JOIN
		cf_auto_invest_config C  ON B.ai_grp_idx=C.idx
	WHERE
		$sql_search
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $page_rows";
//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') print_rr($sql, 'font-size:12px');

$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);

	$R = sql_fetch("SELECT IFNULL(SUM(amount),0) AS invest_amount FROM cf_auto_invest_log_detail WHERE exec_date='".$LIST[$i]['exec_date']."' AND product_idx='".$LIST[$i]['product_idx']."' AND rcode='0'");
	$LIST[$i]['invest_amount'] = $R['invest_amount'];

}
$list_count = count($LIST);
$num = $total_count - $from_record;


if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	//sql_close();
	//exit;
}

?>

<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<div style="line-height:28px;">
		<form id="frmSearch" name= "frmSearch" method="get" class="form-horizontal">

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select id="실행모드" name="exec_mode" class="form-control input-sm">
					<option value="">::실행구분::</option>
					<option value="real" <? if($exec_mode=='real'){echo 'selected';} ?>>정상실행</option>
					<option value="test" <? if($exec_mode=='test'){echo 'selected';} ?>>테스트</option>
				</select>
			</li>
			<li>
				<select name="ai_grp_idx" id="ai_grp_idx" class="form-control input-sm">
					<option value="">::자동투자그룹::</option>
<?
	$res = sql_query("SELECT idx, grp_title FROM cf_auto_invest_config ORDER BY idx");
	while($ROW = sql_fetch_array($res)) {
		$selected = ($ROW['idx']==$ai_grp_idx) ? 'selected' : '';
		echo '<option value="'.$ROW['idx'].'" '.$selected.'>'.$ROW['grp_title'].'</option>' . PHP_EOL;
	}
?>
				</select>
			</li>
			<li>
				<select name="product_idx" class="form-control input-sm">
					<option value="">::자동투자상품::</option>
<?
	$res = sql_query("SELECT idx, title FROM cf_product WHERE ai_grp_idx > 0 AND display='Y' ORDER BY start_datetime ASC");
	while($ROW = sql_fetch_array($res)) {
		$selected = ($ROW['idx']==$product_idx) ? 'selected' : '';
		echo '<option value="'.$ROW['idx'].'" '.$selected.'>'.$ROW['title'].'</option>' . PHP_EOL;
	}
?>
				</select>
			</li>
		</ul>
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li style="vertical-align:middle;">
				<select name="date_field" class="form-control input-sm">
					<option value="">::데이트 필드선택::</option>
					<option value="A.exec_date" <?=($date_field=='A.rdate')?'selected':'';?>>실행일</option>
					<option value="A.rdate" <?=($date_field=='A.edate')?'selected':'';?>>종료일</option>
					<option value="B.start_datetime" <?=($date_field=='B.start_datetime')?'selected':'';?>>투자모집시작일</option>
				</select>
			</li>
			<li style="vertical-align:middle;"><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
			<li style="vertical-align:middle;">~</li>
			<li style="vertical-align:middle;"><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
			<li style="vertical-align:middle;"><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
			<li style="vertical-align:middle;"><a href="auto_invest_log_detail.php" class="btn btn-sm btn-default">상세내역전체</a></li>
		</ul>

		</form>
	</div>
	<!-- 검색영역 E N D -->

	<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;">
		<colgroup>
			<col style="width:5%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
		</colgroup>
		<thead style="font-size:13px">
		<tr>
			<th rowspan="2" style="background:#F8F8EF">NO</th>
			<th rowspan="2" style="background:#F8F8EF">실행일시</th>
			<th rowspan="2" style="background:#F8F8EF">실행구분</th>
			<th rowspan="2" style="background:#F8F8EF">자동투자그룹</th>
			<th rowspan="2" style="background:#F8F8EF">상품명</th>
			<th rowspan="2" style="background:#F8F8EF">자동투자대상</th>
			<th colspan="2" style="background:#F8F8EF">투자처리</th>
			<th rowspan="2" style="background:#F8F8EF">실패처리</th>
			<th rowspan="2" style="background:#F8F8EF">종료일시</th>
			<th rowspan="2" style="background:#F8F8EF">실행내역</th>
		</tr>
		<tr>
			<th style="background:#F8F8EF">건수</th>
			<th style="background:#F8F8EF">금액</th>
		</tr>
		</thead>
<?
if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$exec_mode = ($LIST[$i]['is_real']=='1') ? '정상실행' : '테스트';

?>
		<tr align="center">
			<td><?=$num?></td>
			<td><?=$LIST[$i]['exec_date']?></td>
			<td><?=$exec_mode?></td>
			<td><?=$LIST[$i]['grp_title']?></td>
			<td align="left"><?=$LIST[$i]['title']?></td>
			<td align="right"><?=number_format($LIST[$i]['target_cnt'])?>건</td>
			<td align="right"><span style="color:#2222FF;"><?=number_format($LIST[$i]['exec_cnt'])?>건</span></td>
			<td align="right"><span style="color:#2222FF;"><?=number_format($LIST[$i]['invest_amount'])?>원</span></td>
			<td align="right"><span style="color:#999"><?=number_format($LIST[$i]['drop_cnt'])?>건</span></td>
			<td><?=$LIST[$i]['fdate']?></td>
			<td><a href="auto_invest_log_detail.php?ai_grp_idx=<?=$LIST[$i]['ai_grp_idx']?>&product_idx=<?=$LIST[$i]['product_idx']?>&key_search=A.exec_date&keyword=<?=$LIST[$i]['exec_date']?>">[상세보기]</a></td>
		</tr>
<?
		$num--;
	}
}
else {
	echo '
		<tr>
			<td colspan="10" align="center">데이터가 없습니다.</th>
		</tr>' . PHP_EOL;
}
?>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

</div>

<script>
$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['PHP_SELF']?>'
	        + '?page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<?

include_once ('../admin.tail.php');

?>