<?php
$sub_menu = "700100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql_common = " from cf_product_invest a, cf_product b ";
if (!$_GET['start_date'] && !$_GET['end_date']) {
	$_GET['start_date'] = date('Y-m-d', strtotime('-1 month'));
	$_GET['end_date'] = date('Y-m-d');
}

$sql_search = " where a.product_idx = b.idx and a.insert_date >= '".$_GET['start_date']."' and a.insert_date <= '".$_GET['end_date']."' ";
if ($_GET['keyword']) {
    $sql_search .= " and ( ";
    switch ($_GET['field']) {
        case 'mb_name' :
            $sql_search .= " ({$_GET['field']} = '%{$_GET['keyword']}%') ";
        break;
        case 'mb_tel' :
        case 'mb_hp' :
            $sql_search .= " ({$_GET['field']} like '%{$_GET['keyword']}') ";
        break;
        default :
            $sql_search .= " ({$_GET['field']} like '{$_GET['keyword']}%') ";
        break;
    }

    $sql_search .= " ) ";
}

$search_field = array('mb_1', 'mb_mailing', 'mb_sms');
foreach ($_GET as $key => $val) {
	if (in_array($key, $search_field) && $val) {
		$sql_search .= " and {$key} = '{$val}' ";
	}
}

if ($_GET['mb_datetime_start'] && $_GET['mb_datetime_end']) {
	$sql_search .= " and date_format(mb_datetime, '%Y-%m-%d') >= '".$_GET['mb_datetime_start']."' and date_format(mb_datetime, '%Y-%m-%d') <= '".$_GET['mb_datetime_end']."' ";
}

if ($_GET['mb_point_start'] && $_GET['mb_point_end']) {
	$sql_search .= " and mb_point >= '".$_GET['mb_point_start']."' and mb_point <= '".$_GET['mb_point_end']."' ";
}

$sql_group = " group by a.insert_date ";

$sql = " select count(a.idx) as cnt {$sql_common} {$sql_search} {$sql_group} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$g5['title'] = '매출통계';
include_once('./admin.head.php');

$sql = " select count(a.idx) as cnt, a.insert_date, sum(a.amount * (b.invest_usefee / 100)) as invest_usefee, sum(a.amount * (b.withhold_tax_rate / 100)) as withhold, sum(a.amount * (b.invest_return / 100)) as invest_return {$sql_common} {$sql_search} {$sql_group} limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/jquery-ui.min.css" rel="stylesheet">

<script src="js/jquery-ui.min.js"></script>

<div class="row">
	<div class="col-lg-12">
		<form method="get" class="form-horizontal">
		<div class="panel-body">
			<div class="form-group">
				<ul class="list-inline col-sm-12">
					<li>검색일자</li>
					<li>
						<input type="text" name="start_date" value="<?php echo $_GET['start_date']; ?>" class="form-control datepicker">
					</li>
					<li>~</li>
					<li>
						<input type="text" name="end_date" value="<?php echo $_GET['end_date']; ?>" class="form-control datepicker">
					</li>
					<li>
						<button type="submit" class="btn btn-primary">검색</button>
					</li>
				</ul>
			</div>
		</div>
		</form>
	</div>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center" rowspan="2">날짜</th>
							<th class="text-center" colspan="2">대출</th>
							<th class="text-center" colspan="3">대출상환(대출자)</th>
							<th class="text-center" colspan="2">투자상환(투자)</th>
							<th class="text-center" colspan="2">플랫폼 이용료</th>
							<th class="text-center" colspan="2">원천징수</th>
							<th class="text-center">이자차액</th>
							<th class="text-center">수익</th>
						</tr>
						<tr>
							<th class="text-center">횟수</th>
							<th class="text-center">금액</th>
							<th class="text-center">건수</th>
							<th class="text-center">이자</th>
							<th class="text-center">원금</th>
							<th class="text-center">이자</th>
							<th class="text-center">원금</th>
							<th class="text-center">대출자</th>
							<th class="text-center">투자자</th>
							<th class="text-center">명수</th>
							<th class="text-center">금액</th>
							<th class="text-center"></th>
							<th class="text-center"></th>
						</tr>
						<tr>
							<th class="text-center">합계</th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						for ($i=0; $row=sql_fetch_array($result); $i++) {
							$list_num = $total_count - ($page - 1) * $page_rows - $i;
						?>
						<tr class="odd">
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo $row['insert_date']; ?></td>
							<td align="center"><?php echo number_format($row['invest_usefee'] * $row['cnt']); ?></td>
							<td align="center"><?php echo number_format($row['withhold']); ?></td>
							<td align="center"><?php echo number_format($row['cnt']); ?></td>
							<td align="center"><?php echo number_format($row['invest_return']); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		</form>
		<!-- /.panel-body -->
		<div style="width: 100%; text-align: center;">
			<ul class="pagination">
				<?php echo $pagination; ?>
			</ul>
		</div>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(function() {
	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});
</script>

<?php
include_once ('./admin.tail.php');
?>