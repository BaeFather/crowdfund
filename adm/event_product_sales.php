<?
$sub_menu = "700300";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql_common = " FROM cf_event_product_invest a, cf_event_product b ";
$sql_search = " WHERE a.product_idx = b.idx and a.invest_state = 'Y' ";
switch ($_GET['state']) {
	case '1' : $sql_search .= " AND b.end_datetime < now() AND b.invest_end_date = '' "; break;
	case '2' : $sql_search .= " AND b.state = '4' "; break;
	case '3' : $sql_search .= " AND start_datetime < now() AND end_datetime > now() AND invest_end_date = '' "; break;
	case '4' : $sql_search .= " AND state = '' AND invest_end_date != '' ";	break;
	case '5' : $sql_search .= " AND b.state = '1' "; break;
	case '6' : $sql_search .= " AND b.state = '2' "; break;
}

if ($_GET['category']) {
	$sql_search .= " AND b.category = '".$_GET['category']."' ";
}

if ($_GET['keyword']) {
    $sql_search .= " AND ( ";
    switch ($_GET['field']) {
        case 'title' :
            $sql_search .= " ({$_GET['field']} like '%{$_GET['keyword']}%') ";
        break;

        case 'insert_date' :
            $sql_search .= " ({$_GET['field']} = '{$_GET['keyword']}') ";
        break;
    }

    $sql_search .= " ) ";
}

$sql_group = " GROUP BY a.product_idx ";
$sql_order = " ORDER BY a.product_idx DESC";

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt, b.* FROM cf_event_product_invest a, cf_event_product b {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt, b.* FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx ";
$row = sql_fetch($sql);
$state = $row['cnt'];

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND state = '1' ";
$state1 = sql_fetch($sql);

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND state = '2' ";
$state2 = sql_fetch($sql);

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND state = '3' ";
$state3 = sql_fetch($sql);

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND state = '4' ";
$state4 = sql_fetch($sql);

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND end_datetime < now() AND invest_end_date = '' ";
$state5 = sql_fetch($sql);

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND start_datetime < now() AND end_datetime > now() AND invest_end_date = '' ";
$state6 = sql_fetch($sql);

$sql = " SELECT COUNT(distinct a.product_idx) AS cnt FROM cf_event_product_invest a, cf_event_product b WHERE a.product_idx = b.idx AND state = '' AND invest_end_date != '' ";
$state7 = sql_fetch($sql);

$rows = 20;
if ($_GET['rows']) {
	$rows = $_GET['rows'];
}

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


//$sql = " select a.*, b.title, b.start_date, b.end_date, b.recruit_amount, b.withhold_tax_rate, b.loan_interest_rate, b.invest_return, b.invest_period, b.loan_usefee, b.invest_usefee, sum(a.amount) AS amount, sum(a.amount * (b.invest_return / 100) / 12 * b.invest_period) AS invest_interest, sum(a.amount * (b.loan_interest_rate / 100) / 12 * b.invest_period) AS loan_interest FROM cf_event_product_invest a, cf_event_product b {$sql_search} {$sql_group} limit {$from_record}, {$rows} ";
$sql = "SELECT
					a.*, COUNT(a.idx) AS cnt_invest, SUM(a.amount) AS amount,
					b.*
				FROM
					cf_event_product_invest a,
					cf_event_product b
				{$sql_search}
				{$sql_group}
				{$sql_order}
				LIMIT
					{$from_record}, {$rows} ";
//echo $sql;
//$sql = " SELECT COUNT(a.idx) AS cnt, a.insert_date, sum(a.amount * (b.invest_usefee / 100)) AS invest_usefee, sum(a.amount * (b.withhold_tax_rate / 100)) AS withhold, sum(a.amount * (b.invest_return / 100)) AS invest_return FROM cf_event_product_invest a, cf_event_product b {$sql_search} {$sql_group} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$g5['title'] = '대출ㆍ상환 현황 (이벤트)';
include_once('./admin.head.php');

?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="font-size:13px;">
					<thead>
						<tr>
							<th class="text-center">전체</th>
							<th class="text-center">투자금 모집실패</th>
							<th class="text-center">부실</th>
							<th class="text-center">투자모집중</th>
							<th class="text-center">대기중</th>
							<th class="text-center">수익금 상환중</th>
							<th class="text-center">상품마감</th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd">
							<td align="center"><?=$state?></td>
							<td align="center"><?=$state5['cnt']?></td>
							<td align="center"><?=$state4['cnt']?></td>
							<td align="center"><?=$state6['cnt']?></td>
							<td align="center"><?=$state7['cnt']?></td>
							<td align="center"><?=$state1['cnt']?></td>
							<td align="center"><?=$state2['cnt']?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<form method="get" class="form-horizontal">
		<div class="panel-body">
			<div class="form-group">
				<ul class="list-inline col-sm-12">
					<li><select name="state" class="form-control">
							<option value="">진행상태</option>
							<option value="1" <?=($_GET['state'] == '1') ? 'selected' : ''; ?>>투자금 모집실패</option>
							<option value="2" <?=($_GET['state'] == '2') ? 'selected' : ''; ?>>부실</option>
							<option value="3" <?=($_GET['state'] == '3') ? 'selected' : ''; ?>>투자모집중</option>
							<option value="4" <?=($_GET['state'] == '4') ? 'selected' : ''; ?>>대기중</option>
							<option value="5" <?=($_GET['state'] == '5') ? 'selected' : ''; ?>>수익금 상환중</option>
							<option value="6" <?=($_GET['state'] == '6') ? 'selected' : ''; ?>>상품마감</option>
						</select>
					</li>
					<li><select name="category" class="form-control">
							<option value="">상품형태</option>
							<option value="1" <?=($_GET['category'] == '1') ? 'selected' : ''; ?>>동산</option>
							<option value="2" <?=($_GET['category'] == '2') ? 'selected' : ''; ?>>부동산</option>
						</select>
					</li>
					<li><select name="field" class="form-control">
							<option value="">필드선택</option>
							<option value="title" <?=($_GET['field'] == 'title') ? 'selected' : ''; ?>>상품명</option>
							<option value="insert_date" <?=($_GET['field'] == 'insert_date') ? 'selected' : ''; ?>>등록일</option>
						</select>
					</li>
					<li><input type="text" name="keyword" value="<?=$_GET['keyword']?>" class="form-control"></li>
					<li><button type="submit" class="btn btn-primary">검색</button></li>
					<li>
						<select name="rows" class="form-control">
							<option value="20">20개씩</option>
							<option value="50" <?=($_GET['rows'] == '50') ? 'selected' : ''; ?>>50개씩</option>
							<option value="100" <?=($_GET['rows'] == '100') ? 'selected' : ''; ?>>100개씩</option>
						</select>
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
							<th class="text-center" rowspan="2">NO</th>
							<th class="text-center" rowspan="2">상품명</th>
							<th class="text-center" rowspan="2">모집금액</th>
							<th class="text-center" rowspan="2">진행상태</th>
							<th class="text-center" rowspan="2">기간</th>
							<th class="text-center" rowspan="2">투자자<br />수익율</th>
							<th class="text-center" colspan="3">펀딩참여</th>
							<th class="text-center" colspan="3">정산</th>
							<th class="text-center" rowspan="2">관리</th>
						</tr>
						<tr>
							<th class="text-center">참여인원</th>
							<th class="text-center">참여금액 합계</th>
							<th class="text-center">참여율</th>

							<th class="text-center" rowspan="2">지급예정일</th>
							<th class="text-center" rowspan="2">수익금</th>
							<th class="text-center" rowspan="2">지급예정합계</th>
						</tr>
					</thead>
					<tbody>
						<?
						for ($i=0; $row=sql_fetch_array($result); $i++) {

							$list_num = $total_count - ($page - 1) * $page_rows - $i;

							$state = '';
							$date = date('Y-m-d H:i:s');
							if ($row['open_datetime'] > $date) {
								$state = '투자대기중';
							}

							if ($row['start_datetime'] < $date && $row['end_datetime'] > $date && $row['invest_end_date'] == '') {
								$state = '투자모집중';
							}

							if ($row['end_datetime'] < $date && $row['invest_end_date'] == '') {
								$state = '투자금 모집실패';
							}

							if ($row['invest_end_date'] != '' && $row['state'] == '') {
								$state = '대기중';
								$state_code = '1';
							}

							if ($row['state'] == '1') {
								$state = '수익금 상환중';
								$state_code = '2';
							}

							if ($row['state'] == '2') {
								$state = '상품마감';
							}

							if ($row['state'] == '4') {
								$state = '부실';
							}

							if ($row['state'] == '5') {
								$state = '중도일시상환';
								$state_code = '2';
							}

						?>
						<tr class="odd">
							<td align="center" alt="NO"><?=$list_num?></td>
							<td align="center" alt="상품명"><a href="<?=G5_URL?>/event_invest/event_invest.php?prd_idx=<?=$row['product_idx']?>" target="_blank"><?=$row['title']?></a></td>
							<td align="center" alt="모집금액"><?=number_format($row['recruit_amount'])?>원</td>
							<td align="center" alt="진행상태"><?=$state?></td>
							<td align="center" alt="기간"><?=$row['recruit_period_start'].' ~ '.$row['recruit_period_end']?></td>
							<td align="center" alt="투자자수익률"><?=$row['invest_return']?>%</td>

							<td align="center" alt="참여인원"><?=number_format($row['cnt_invest'])?>명</td>
							<td align="center" alt="금액합계"><?=number_format($row['amount'])?>원</td>
							<td align="center" alt="참여율"><?=floor($row['amount'] / $row['recruit_amount'] * 100); ?>%</td>

							<td align="center" alt="지급예정일"><?=$row['repay_day']?></td>
							<td align="center" alt="수익금"><?=number_format(($row['amount']*$row['invest_return'])/100)?>원</td>
							<td align="center" alt="지급예정금액"><?=number_format($row['amount'] + (($row['amount']*$row['invest_return'])/100))?>원</td>

							<td align="center">
								<a href="<?=G5_URL?>/event_invest/event_invest.php?prd_idx=<?=$row['product_idx']?>" target="_blank" class="btn btn-success">보기</a>
								<a href="./event_product_calculate.php?idx=<?=$row['product_idx']?>" class="btn btn-primary">정산</a>
							</td>
						</tr>
						<?
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
	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});
</script>

<?
include_once ('./admin.tail.php');
?>