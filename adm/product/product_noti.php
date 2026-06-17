<?
###############################################################################
## 상품오픈안내설정
###############################################################################

$sub_menu = "600600";
include_once('./_common.php');


auth_check($auth[$sub_menu], 'w');



$html_title = "상품오픈안내설정";
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k)) ${$k} = trim($v); }


$where = " AND isFired=''";
if($gubun) $where.= " AND A.gubun='".$gubun."'";
if($member_idx) $where.= " AND A.member_idx='".$member_idx."'";
if($sdate && $edate) {
	$where.= " AND LEFT(A.rdate,10) BETWEEN '$sdate' AND '$edate'";
}
else {
	if($sdate) $where.= " AND LEFT(A.rdate,10)>='$sdate' ";
	if($edate) $where.= " AND LEFT(A.rdate,10)<='$edate' ";
}

$sql = "
	SELECT
		COUNT(idx) AS cnt
	FROM
		cf_sms_noti A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE 1
		$sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 20;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;


$sql = "
	SELECT
		A.*,
		B.mb_id, B.mb_name
	FROM
		cf_sms_noti A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE 1
		$where
	ORDER BY
		A.idx DESC";
//print_rr($sql, 'font-size:12px');
$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[] = sql_fetch_array($result);
}
sql_free_result($result);

$list_count = count($LIST);
$num = $total_count - $from_record;

?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel-body" style="padding:0 1% 0 1%;">

<?
if($mode=='new' || $idx) {
	include_once("product_noti_write.inc.php");
}
?>

			<!-- 검색영역 START -->
			<div style="line-height:28px;">
			<form id="frmSearch" name="frmSearch" method="get">
				<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select name="gubun" class="form-control input-sm" style="width:180px;">
							<option value="">:: 공지구분 ::</option>
							<option value="1" <?=($gubun=="1")?"selected":""?> >상품안내</option>
							<option value="2" <?=($gubun=="2")?"selected":""?> >일반공지</option>
							<option value="3" <?=($gubun=="3")?"selected":""?> >긴급공지</option>
						</select>
					</li>
					<li>
						<select name="member_idx" class="form-control input-sm" style="width:180px;">
							<option value="">:: 게시자 ::</option>
<?
$res = sql_query("
	SELECT
		A.member_idx, B.mb_id, B.mb_name
	FROM
		cf_sms_noti A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	GROUP BY
		A.member_idx
	ORDER BY
		A.member_idx");
while( $row = sql_fetch_array($res) ) {
	$selected = ($row['member_idx']==$member_idx) ? "selected" : "";

	echo "							<option value='".$row['member_idx']."' $selected>".$row['mb_name']."</option>\n";

}
?>
						</select>
					</li>
					<li></li>
					<li>등록일시</li>
					<li><input type='text' name='sdate' value="<?=$sdate?>" class="form-control input-sm datepicker" autocomplete="off" style="width:90px;display:inline;" /></li>
					<li>~</li>
					<li><input type='text' name='edate' value="<?=$edate?>" class="form-control input-sm datepicker" autocomplete="off" style="width:90px;display:inline;" /></li>
					<li><input type='submit' class="btn btn-sm btn-warning" value=' 검색 '></li>
					<li style="float:right"><button type='button' onClick="location.href='?mode=new'" class="btn btn-sm btn-primary">게시글 등록</button></li>
				</ul>
			</form>
			</div>
			<!-- 검색영역 E N D -->

			<div class="dataTable_wrapper" style="margin-top:15px;">
				<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;">
					<thead style="font-size:13px;">
						<tr>
							<th class="text-center" style="background-color:#F8F8EF">NO.</th>
							<th class="text-center" style="background-color:#F8F8EF">구분</th>
							<th class="text-center" style="background-color:#F8F8EF">제목</th>
							<th class="text-center" style="background-color:#F8F8EF">URL</th>
							<th class="text-center" style="background-color:#F8F8EF">조회</th>
							<th class="text-center" style="background-color:#F8F8EF">등록</th>
							<th class="text-center" style="background-color:#F8F8EF">등록일시</th>
						</tr>
					</thead>
					<tbody>
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		$view_link = G5_URL."/noti/".$LIST[$i]['idx'];
		$print_view_link = preg_replace("/https:\/\/www\./", "", $view_link);

		switch($LIST[$i]['gubun']) {
			case '2' : $print_gubun = "일반공지"; break;
			case '3' : $print_gubun = "긴급공지"; break;
			default  : $print_gubun = "상품안내"; break;
		}

?>
						<tr align="center" <? if($LIST[$i]['idx']==$idx) echo "style='background:#EEE'"; ?>>
							<td><?=$num?></td>
							<td><?=$print_gubun?></td>
							<td align="left"><a href="?<?=preg_replace("/&idx=([0-9]){1,6}/", "", $_SERVER['QUERY_STRING']); ?>&idx=<?=$LIST[$i]['idx']?>"><?=$LIST[$i]['subject']?></a></td>
							<td><a href="javascript:;" onClick="window.open('<?=$view_link?>','noti','width=400px,height=680');"><?=$print_view_link?></a></td>
							<td><?=number_format($LIST[$i]['view'])?></td>
							<td><?=$LIST[$i]['mb_name']?></td>
							<td><?=$LIST[$i]['rdate']?></td>
						</tr>
<?
		$num--;
	}
}
else {
?>
						<tr align="center">
							<td colspan="10">데이터가 없습니다.</td>
						</tr>
<?
}
?>
					</tbody>
				</table>
			</div>
			<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

		</div><!-- /.panel-body -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<? include_once ('../admin.tail.php'); ?>