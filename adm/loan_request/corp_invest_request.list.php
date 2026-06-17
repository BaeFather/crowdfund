<?

$sql_search = "AND is_drop=''";
if($start_date && $end_date) {
	$sql_search.= " AND LEFT(A.regdate,10) BETWEEN '$start_date' AND '$end_date'";
}
else {
	if($start_date) $sql_search.= " AND LEFT(A.regdate,10)>='$start_date' ";
	if($end_date)  $sql_search .= " AND LEFT(A.regdate,10)<='$end_date' ";
}
if($key_search && $keyword) {
	$sql_search .= " AND $key_search LIKE '%$keyword%' ";
}

$sql = "SELECT COUNT(A.idx) AS cnt FROM cf_care_service_request A WHERE 1=1 $sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 20;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;


$sql = "
	SELECT
		A.*,
		B.mb_id,
		(SELECT mb_name FROM g5_member WHERE mb_id=A.check_admin_id) AS admin_name,
		(SELECT COUNT(idx) FROM cf_care_service_request_comment WHERE req_idx=A.idx) AS comment_cnt
	FROM
		cf_care_service_request A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE 1=1
		$sql_search
	ORDER BY
		A.idx DESC
	LIMIT
		$from_record, $rows";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql);
$rcount = $result->num_rows;

$num = $total_count - $from_record;

?>

<style>
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }
</style>

	<!-- 검색영역 START -->
	<div style="display:inline-block;width:100%;line-height:28px;margin-bottom:8px;">
		<form id="frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px;">
			<li>등록일</li>
			<li><input type="text" name="start_date" value="<?=$start_date?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
			<li>~</li>
			<li><input type="text" name="end_date" value="<?=$end_date?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
		</ul>
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px;">
			<li>
				<select id="key_search" name="key_search" class="form-control input-sm" style="width:150px">
					<option value="">:: 필드선택 ::</option>
					<option value="A.name" <? if($key_search == 'A.name'){echo 'selected';} ?>>문의자명</option>
					<option value="A.phone" <? if($key_search == 'A.phone'){echo 'selected';} ?>>연락처</option>
					<option value="A.content" <? if($key_search == 'A.content'){echo 'selected';} ?>>문의내용</option>
					<option value="A.admin_memo" <? if($key_search == 'A.admin_memo'){echo 'selected';} ?>>관리자메모</option>
					<option value="A.check_admin_id" <? if($key_search == 'A.check_admin_id'){echo 'selected';} ?>>관리자ID</option>
				</select>
			</li>
			<li><input type="text" id="keyword" name="keyword" value="<?=$keyword?>" class="form-control input-sm" style="width:250px"></li>
			<li><button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button></li>
		</ul>
		</form>
	</div>
	<!-- 검색영역 E N D -->


	<!-- 리스트 START -->

	<div style="float:right; display:inline-block; line-height:20px;width:100%;">
		<span style="float:left">▣ 등록 : <?=number_format($total_count);?>건</span>
		<span style="float:right"><?=$page?> / <?=$total_page?> Page<span>
	</div>
	<table id="dataList" class="table table-striped table-bordered table-hover" style="padding-top:0; font-size:13px">
		<thead>
		<tr>
			<th scope="col" style="text-align:center;width:60px">NO.</th>
			<th scope="col" style="text-align:center;">문의내용</th>
			<th scope="col" style="text-align:center;width:10%">문의자명</th>
			<th scope="col" style="text-align:center;width:20%">연락처</th>
			<th scope="col" style="text-align:center;width:10%">등록일시</th>
			<th scope="col" style="text-align:center;width:10%">확인관리자</th>
			<th scope="col" style="text-align:center;width:10%">PROC</th>
		</tr>
		</thead>
		<tbody>
<?
if($num > 0) {
	for ($i=0; $i<$rcount; $i++) {
		$ROW = sql_fetch_array($result);

		$new_mark = (time()-strtotime($ROW['regdate']) < 86400) ? '<span class="new_mark">new</span>' : '';
		$print_title = cut_str($ROW['content'], 40, '...');
		if($ROW['comment_cnt']) $print_title.= " <span style='font-size:12px;color:#AAA'>(".$ROW['comment_cnt'].")</font>";

		if($ROW['check_admin_id']) {
			$print_admin_info = $ROW['admin_name'] . "<br/>\n(".$ROW['check_admin_id'].")";
		}
		else {
			$print_admin_info	= '';
		}


?>
		<tr style="background:<?=($ROW['idx']==$idx)?'#FFDDDD':''?>">
			<td align="center"><?=$num?></td>
			<td align="left">
				<?=$new_mark?>
				<?=$print_title?>
			</td>
			<td align="center"><?=$ROW['name']?></td>
			<td align="center"><?=$ROW['phone']?></td>
			<td align="center"><?=substr($ROW['regdate'],0,16)?></td>
			<td align="center"><?=$print_admin_info?></td>
			<td align="center">
				<button type="button" onClick="location.href='?<?=$qstr?>&idx=<?=$ROW['idx']?>&page=<?=$page?>'" class="btn btn-sm <?=($ROW['idx']==$idx)?'':'btn-default'?>" style="margin-top:2px;width:80px">상세보기</button>
				<button type="button" onClick="dropData('<?=$ROW['idx']?>')" class="btn btn-sm btn-danger" style="margin-top:2px;width:80px">삭제</button>
			</td>
		</tr>
<?
		$num--;
	}
}else {
?>

		<tr>
			<td colspan="10" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?
}
?>
	</table>
	<!-- 리스트 E N D -->

<?
	echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, 'corp_invest_request.php?'.$qstr.'&amp;page=');
?>

	<script>
	dropData = function(idx) {
		if(confirm('게시글을 삭제 하시겠습니까?')) {
			$.ajax({
				url : "corp_invest_request.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data:{mode:'drop',idx:idx},
				success:function(data) {
					if(data.result=='SUCCESS') { alert('삭제 되었습니다.'); window.location.reload(); }
					else { alert(data.message); }
				},
				error:function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}

	$(document).ready(function() {
		$('#dataList').floatThead();
	});
	</script>