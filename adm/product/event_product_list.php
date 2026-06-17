<?php
$sub_menu = "600200";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql = "SELECT count(idx) AS cnt FROM cf_event_product ORDER by insert_date DESC";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1;                   // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows;         // 시작 열을 구함

$g5['title'] = '이벤트 상품';
include_once('../admin.head.php');

$sql = "SELECT * FROM cf_event_product ORDER BY insert_date DESC LIMIT $from_record, $rows";
$result = sql_query($sql);
?>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/jquery-ui.min.css" rel="stylesheet">

<script src="js/jquery-ui.min.js"></script>

<div class="row">
	<div class="col-lg-12">
		<div class="panel-body">
			<p class="text-right">
				<a href="./event_product_form.php" class="btn btn-success">등록하기</a>
			</p>
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="font-size:13px;">
					<thead>
						<tr>
							<!--<th class="text-center"><input type="checkbox" name="chkall" value="1"></th>-->
							<th class="text-center">번호</th>
							<th class="text-center">진행상태</th>
							<th class="text-center">상품명</th>
							<th class="text-center">모집기간</th>
							<th class="text-center">목표금액</th>
							<th class="text-center">참여인원</th>
							<th class="text-center">등록일</th>
							<th class="text-center">관리</th>
						</tr>
					</thead>
					<tbody>
<?php
for ($i=0; $row=sql_fetch_array($result); $i++) {
	$list_num = $total_count - ($page - 1) * $page_rows - $i;

	$sql = "SELECT COUNT(idx) as cnt FROM cf_event_product_invest WHERE product_idx = '".$row['idx']."' AND invest_state = 'Y' GROUP BY member_idx";
	$res = sql_query($sql);
	$invest['cnt'] = 0;
	while($tmp_row = sql_fetch_array($res)) {
		$invest['cnt']+= $tmp_row['cnt'];
	}


	$state = '';
	$date = date('Y-m-d H:i:s');

	if($row['state']) {
		if($row['state'] == '1')      $state = '모집완료';
		else if($row['state'] == '2') $state = '상품마감';
		else if($row['state'] == '4') $state = '부실';
		else if($row['state'] == '5') $state = '지급완료';
	}
	else {
		if ($row['open_datetime'] > $date) $state = '대기중';
		if ($row['start_datetime'] < $date && $row['end_datetime'] > $date) $state = '모집중';
		if ($row['end_datetime'] < $date) $state = '모집실패';
	}


?>
						<tr class="odd">
							<!--<td align="center"><input type="checkbox" name="chk[]" value="<?=$row['mb_no']?>"></td>-->
							<td align="center"><?=$list_num?></td>
							<td align="center"><?=$state?></td>
							<td align="center"><a href="/event_invest/event_invest.php?prd_idx=<?=$row['idx']?>" target="_blank"><?=$row['title']?></a></td>
							<td align="center"><?=$row['recruit_period_start']?> <span style="color:#aaa">~</span> <?=$row['recruit_period_end']?></td>
							<td align="center"><?=number_format($row['recruit_amount'])?>원</td>
							<td align="center"><?=number_format($invest['cnt'])?></td>
							<td align="center"><?=substr($row['insert_date'], 0, 10)?></td>
							<td align="center">
								<a href="./event_product_form.php?idx=<?=$row['idx']?>" class="btn btn-primary">수정</a>
                <a href="javascript:;" class="btn btn-danger" onClick="if(confirm('정말 상품을 삭제하시겠습니까?')) { location.href='./event_register_process.php?action=product_delete&idx=<?=$row['idx']?>' } ">삭제</a>
							</td>
						</tr>
<?php
}
?>
					</tbody>
				</table>
			</div>
		</div>
		</form>
		<!-- /.panel-body -->
		<div style="width: 100%; text-align: center;">
			<ul class="pagination">
				<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
			</ul>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});
</script>

<?php
include_once ('../admin.tail.php');
?>