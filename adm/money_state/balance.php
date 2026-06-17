<?
$sub_menu = "500100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$datetime_s = $_GET['mb_datetime_start'] . " 00:00:00";
$datetime_e = $_GET['mb_datetime_end'] . " 23:59:59";

foreach($_GET as $k=>$v) { ${$k} = trim($v); }

$sql_common = " FROM {$g5['member_table']} ";

$sql_search = " AND member_group='F'";
$sql_search.= " AND mb_level BETWEEN 1 AND 8";
$sql_search.= ($_GET['field'] && $_GET['keyword']) ? " AND (".$_GET['field']." LIKE '%".$_GET['keyword']."%')" : "";
$search_field = array('member_type','mb_mailling','mb_sms');
foreach ($_GET as $key => $val) {
	$sql_search.= (in_array($key, $search_field) && trim($val)) ? " AND {$key}='".trim($val)."'" : "";
}

if($_GET['mb_datetime_start'] && $_GET['mb_datetime_end']) {
	$sql_search.= " AND mb_datetime BETWEEN '".$datetime_s."' AND '".$datetime_e."'";
//$sql_search.= " AND LEFT(mb_datetime, 10) BETWEEN '".$_GET['mb_datetime_start']."' AND '".$_GET['mb_datetime_end']."'";
}
else {
	if($_GET['mb_datetime_start']) $sql_search.= " AND mb_datetime>='".$datetime_s."'";
	if($_GET['mb_datetime_end'])   $sql_search.= " AND mb_datetime<='".$datetime_e."'";
}

if($_GET['mb_point_start'] && $_GET['mb_point_end']) {
	$sql_search.= " AND mb_point BETWEEN '".$_GET['mb_point_start']."' AND '".$_GET['mb_point_end']."'";
}
else {
	if($_GET['mb_point_start']) $sql_search.= " AND mb_point>='".$_GET['mb_point_start']."'";
	if($_GET['mb_point_end'])   $sql_search.= " AND mb_point<='".$_GET['mb_point_end']."'";
}

$order = " mb_point ";
$sort  = " DESC";

$sql = "SELECT COUNT(mb_no) AS cnt FROM ".$g5['member_table']." WHERE (1) $sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 50;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$total = array();

$sql = "SELECT COUNT(mb_no) AS cnt FROM ".$g5['member_table']." WHERE (1) {$sql_search} AND member_type='1'";
$row = sql_fetch($sql);
$total['indi_member'] = $row['cnt'];

$sql = "SELECT COUNT(mb_no) AS cnt FROM ".$g5['member_table']." WHERE (1) {$sql_search} AND member_type='2'";
//echo $sql;
$row = sql_fetch($sql);
$total['co_member'] = $row['cnt'];

$sql = "SELECT COUNT(mb_no) AS cnt FROM ".$g5['member_table']." WHERE (1) {$sql_search} AND member_type='3'";
$row = sql_fetch($sql);
$total['sms_member'] = $row['cnt'];


$sql = "SELECT SUM(mb_point) AS sum FROM ".$g5['member_table']." WHERE (1) {$sql_search}";
$row = sql_fetch($sql);
$total['point'] = $row['sum'];



$g5['title'] = '예치금관리';
include_once('./admin.head.php');

$sql = "
	SELECT
		*,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=mb_no AND invest_state IN ('Y','R')) AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE member_idx=mb_no AND invest_state IN ('Y','R')) AS invest_amount
	FROM
		".$g5['member_table']."
	WHERE (1)
		{$sql_search}
	ORDER BY
		$order $sort
LIMIT
	$from_record, $rows";
//echo $sql;
$result = sql_query($sql);

$param = array();
foreach ((array)$_REQUEST as $key => $val) {
	if (empty($val) || in_array($key, array('page'))) {
		continue;
	}
	$param[] = $key.'='.$val;
}

$qstr = join('&amp;', $param);
?>

<!--
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/jquery-ui.min.css" rel="stylesheet">
<script src="js/jquery-ui.min.js"></script>
//-->

<div class="row">
	<div class="col-lg-12">
		<ul class="list-inline col-sm-12">
			<li>등록회원 : <?=number_format($total_count)?>명</li>
			<li>[개인회원 : <?=number_format($total['indi_member'])?>명 | 법인회원 : <?=number_format($total['co_member'])?>명 | SNS회원 : <?=number_format($total['sns_member'])?>명]</li>
		</ul>
		<ul class="list-inline col-sm-12">
			<li>예치금 : <?=number_format($total['point'])?>원</li>
		</ul>
	</div>
	<div class="col-lg-12">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading text-center">검색설정</div>
				<div class="panel-body">
	        <form method="get" class="form-horizontal">
						<ul class="list-inline col-sm-12">
							<li>
								<select name="member_group" class="form-control">
									<option value="F" <?=($member_group=='F') ? 'selected' : ''; ?>>투자회원</option>
									<!--<option value="L" <?=($member_group == 'L') ? 'selected' : ''; ?>>대출회원</option>-->
								</select>
							</li>
							<li>
								<select name="member_type" class="form-control">
									<option value="">회원형태</option>
									<option value="1" <?=($_GET['member_type'] == '1') ? 'selected' : ''; ?>>개인회원</option>
									<option value="2" <?=($_GET['member_type'] == '2') ? 'selected' : ''; ?>>법인회원</option>
									<option value="3" <?=($_GET['member_type'] == '3') ? 'selected' : ''; ?>>SNS회원</option>
								</select>
							</li>
							<li>예치금</li>
							<li><input type="text" name="mb_point_start" value="<?=$_GET['mb_point_start']?>" class="form-control" style="width:100px;" onKeyUp="onlyDigit(this);"></li>
							<li>~</li>
							<li><input type="text" name="mb_point_end" value="<?=$_GET['mb_point_end']?>" class="form-control" style="width:100px;" onKeyUp="onlyDigit(this);"></li>
						</ul>
						<ul class="list-inline col-sm-12">
							<li>
								<select name="field" class="form-control">
									<option value="">필드선택</option>
									<option value="mb_id" <?=($_GET['field'] == 'mb_id') ? 'selected' : ''; ?>>아이디</option>
									<option value="mb_name" <?=($_GET['field'] == 'mb_name') ? 'selected' : ''; ?>>이름</option>
									<option value="mb_co_name" <?=($_GET['field'] == 'mb_co_name') ? 'selected' : ''; ?>>업체명(법인회원)</option>
									<option value="mb_email" <?=($_GET['field'] == 'mb_email') ? 'selected' : ''; ?>>이메일</option>
									<option value="mb_hp" <?=($_GET['field'] == 'mb_hp') ? 'selected' : ''; ?>>휴대폰</option>
									<option value="mb_tel" <?=($_GET['field'] == 'mb_tel') ? 'selected' : ''; ?>>전화번호</option>
								</select>
							</li>
							<li>
								<input type="text" name="keyword" value="<?=$_GET['keyword']?>" class="form-control">
							</li>
							<li>
								<button type="submit" class="btn btn-primary" style="margin-top:-2px;">검색</button>
							</li>
						</ul>
					</form>
				</div>
			</div>
		</div>

		<form name="point_form" id="point_form" class="form-horizontal">
			<input type="hidden" name="action"            value="balance_update">
			<input type="hidden" name="mb_1"              value="<?=$_GET['mb_1']?>">
			<input type="hidden" name="field"             value="<?=$_GET['field']?>">
			<input type="hidden" name="mb_mailing"        value="<?=$_GET['mb_mailing']?>">
			<input type="hidden" name="mb_sms"            value="<?=$_GET['mb_sms']?>">
			<input type="hidden" name="mb_datetime_start" value="<?=$_GET['mb_datetime_start']?>">
			<input type="hidden" name="mb_datetime_end"   value="<?=$_GET['mb_datetime_end']?>">
			<input type="hidden" name="mb_point_start"    value="<?=$_GET['mb_point_start']?>">
			<input type="hidden" name="mb_point_end"      value="<?=$_GET['mb_point_end']?>">
			<input type="hidden" name="keyword"           value="<?=$_GET['keyword']?>">
			<input type="hidden" name="total_count"       value="<?=$row['cnt']?>">

		<div class="col-sm-6 text-center">
			<div class="panel panel-default">
				<div class="panel-heading">예치금설정</div>
				<div class="panel-body">
					<ul class="list-inline">
						<li>
							<select name="member_select" class="form-control" required>
								<option value="">:: 대상 ::</option>
								<option value="3">선택한 회원</option>
								<option value="1">전체 회원</option>
							</select>
						</li>
						<li>
							<input type="text" name="balance" class="form-control" required style="width:100px;" onKeyUp="onlyDigit(this);">
						</li>
						<li>원</li>
						<li>
							<select name="balance_select" class="form-control" required>
								<option value="">::지급/차감 선택::</option>
								<option value="1">지급</option>
								<option value="2">차감</option>
							</select>
						</li>
						<li>
							<span id="submit1" class="btn btn-danger" style="cursor:pointer;margin-top:-2px;">확인</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<p class="text-right">
				<a href="./member/drop_member_list.php" class="btn btn-default">탈퇴회원보기</a>
				<!--<a href="./member_list.php?sst=mb_leave_date&sod=desc&sfl=&stx=" class="btn btn-default">탈퇴회원보기</a>-->
			</p>
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="2%" class="text-center;"><input type="checkbox" name="chkall" id="chkall" value="1"></th>
							<th width="5%" class="text-center;">번호</th>
							<th width="12%" class="text-center;">아이디</th>
							<th width="12%" class="text-center;">이름</th>
							<th width="12%" class="text-center;">형태</th>
							<th width="12%" class="text-center;">예치금</th>
							<th width="12%" class="text-center;">누적투자수</th>
							<th width="12%" class="text-center;">누적투자금액</th>
							<th width="%" class="text-center;">관리</th>
						</tr>
					</thead>
					<tbody>
						<?
						for ($i=0; $row=sql_fetch_array($result); $i++) {
							$list_num = $total_count - ($page - 1) * $rows;

							//$sql    = "SELECT COUNT(idx) AS cnt FROM cf_product_invest WHERE member_idx='".$row['mb_no']."' AND invest_state IN ('Y','R')";
							//$INVEST = sql_fetch($sql);

							if($row['member_type'] == 1)      $member_type = '개인회원';
							else if($row['member_type'] == 2) $member_type = '법인회원';
							else                              $member_type = 'SNS회원';

						?>
						<tr class="odd">
							<td align="center"><input type="checkbox" name="chk[]" value="<?=$row['mb_no']?>"></td>
							<td align="center"><?=($total_count - ($page - 1) * $rows - $i)?></td>
							<td align="center"><a href="./member/member_view.php?&mb_id=<?=$row['mb_id']?>"><?=$row['mb_id']?></td>
							<td align="center"><?=$row['mb_name']?></td>
							<td align="center"><?=$member_type?></td>
							<td align="right"><?=number_format($row['mb_point'])?>원</td>
							<td align="right"><?=number_format($row['invest_count'])?>건</td>
							<td align="right"><?=number_format($row['invest_amount'])?>원</td>
							<td align="center"><a href="./member_form.php?sst=&sod=&sfl=&stx=&page=&w=u&mb_id=<?=$row['mb_id']?>" class="btn btn-info">수정</a></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
		</div>
		</form>

		<!-- /.panel-body -->
		<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=')?>
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		monthNames: [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ],
		dayNamesShort: [ "일", "월", "화", "수", "목", "금", "토" ]
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
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
</script>

<?
include_once ('./admin.tail.php');
?>