<?
$sub_menu = "601300";
include_once('./_common.php');


$g5['title'] = $menu['menu601'][3][1] . " - 상세실행결과";
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
	if( $key_search=='D.mb_no' ) {
		$sql_search .= " AND $key_search='$keyword' ";
	}
	else if($key_search=='D.mb_name') {
		$sql_search .= " AND (D.mb_name LIKE '%$keyword%' OR D.mb_co_name LIKE '%$keyword%')";
	}
	else {
		$sql_search .= " AND $key_search LIKE '%".$keyword."%' ";
	}
}

$sql_search2 = $sql_search;
if($msg_search) {
	$sql_search .= " AND A.msg = '$msg_search' ";
}
//echo $sql_search;
$sql = "
	SELECT
		COUNT(*) AS cnt
	FROM
		cf_auto_invest_log_detail A
	LEFT JOIN
		cf_product B ON A.product_idx=B.idx
	LEFT JOIN
		cf_auto_invest_config C ON B.ai_grp_idx=C.idx
	LEFT JOIN
		g5_member D ON A.member_idx=D.mb_no
	WHERE
		$sql_search";
//echo $sql;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page_rows = 50;
$total_page = ceil($total_count / $page_rows);		// 전체 페이지 계산
$page = ($page) ? $page : 1;											// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;					// 시작 열을 구함

$sql_order = "A.idx DESC";

$sql = "
	SELECT
		A.exec_date, A.product_idx, A.member_idx, A.amount, A.rcode, A.msg, A.is_real,
		B.title,
		C.grp_title,
		D.member_type, D.mb_no, D.mb_id, D.mb_name, D.mb_co_name, D.mb_point,
		E.setup_amount, E.setup_amount2
	FROM
		cf_auto_invest_log_detail A
	LEFT JOIN
		cf_product B ON A.product_idx=B.idx
	LEFT JOIN
		cf_auto_invest_config C ON B.ai_grp_idx=C.idx
	LEFT JOIN
		g5_member D ON A.member_idx=D.mb_no
	LEFT JOIN
		(SELECT member_idx,setup_amount, setup_amount2 FROM cf_auto_invest_config_user WHERE ai_grp_idx='".$ai_grp_idx."') E ON A.member_idx=E.member_idx
	WHERE
		$sql_search
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $page_rows";
//echo $sql;
$result = sql_query($sql);
$rcount = sql_num_rows($result);
for($i=0; $i<$rcount; $i++) {
	$LIST[] = sql_fetch_array($result);
}
$list_count = count($LIST);
$num = $total_count - $from_record;

?>

<link href="/adm/css/bootstrap.min.css" rel="stylesheet">
<link href="/adm/css/jquery-ui.min.css" rel="stylesheet">
<script src="/adm/js/jquery-ui.min.js"></script>
<script src="/adm/js/jquery.form.js"></script>

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
					<option value="B.start_datetime" <?=($date_field=='B.start_datetime')?'selected':'';?>>투자모집시작일</option>
				</select>
			</li>
			<li style="vertical-align:middle;"><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
			<li style="vertical-align:middle;">~</li>
			<li style="vertical-align:middle;"><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
		</ul>
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li style="vertical-align:middle;">
				<select name="key_search" class="form-control input-sm">
					<option value="">::필드선택::</option>
					<option value="D.mb_no" <? if($key_search == 'D.mb_no'){echo 'selected';} ?>>회원번호</option>
					<option value="D.mb_id" <? if($key_search == 'D.mb_id'){echo 'selected';} ?>>아이디</option>
					<option value="D.mb_name" <? if($key_search == 'D.mb_name'){echo 'selected';} ?>>성명/사업자명</option>
					<option value="A.exec_date" <? if($key_search == 'A.exec_date'){echo 'selected';} ?>>실행일시</option>
					<option value="A.msg" <? if($key_search == 'A.msg'){echo 'selected';} ?>>실행결과</option>
				</select>
			</li>
			<li style="vertical-align:middle;"><input type="text" class="form-control input-sm" name="keyword" size="30" value="<?=$keyword;?>"></li>
			<li style="vertical-align:middle;">
				<select name="msg_search" class="form-control input-sm">
					<option value="">실행결과 전체</option>
				<?
				$msg_sql = "SELECT distinct A.msg
								FROM
									cf_auto_invest_log_detail A
								LEFT JOIN
									cf_product B ON A.product_idx=B.idx
								LEFT JOIN
									cf_auto_invest_config C ON B.ai_grp_idx=C.idx
								LEFT JOIN
									g5_member D ON A.member_idx=D.mb_no
								WHERE
									$sql_search2";
				$msg_res = sql_query($msg_sql);
				$msg_cnt = sql_num_rows($msg_res);
				for ($msg_i=0 ; $msg_i<$msg_cnt ; $msg_i++) {
					$msg_row = sql_fetch_array($msg_res);
					?>
					<option value="<?=$msg_row['msg']?>" <?=$msg_row['msg']==$msg_search?"selected":""?> ><?=$msg_row['msg']?></option>
					<?
				}
				?>
				</select>
			</li>
			<li style="vertical-align:middle;"><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
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
		</colgroup>
		<thead style="font-size:12px;">
		<tr>
			<th style="background:#F8F8EF">NO</th>
			<th style="background:#F8F8EF">실행일시</th>
			<th style="background:#F8F8EF">실행구분</th>
			<th style="background:#F8F8EF">자동투자그룹</th>
			<th style="background:#F8F8EF">상품명</th>
			<th style="background:#F8F8EF">회원번호</th>
			<th style="background:#F8F8EF">아이디</th>
			<th style="background:#F8F8EF">성명/사업자명</th>
			<th style="background:#F8F8EF">보유예치금</th>
			<th style="background:#F8F8EF">최소설정금액</th>
			<th style="background:#F8F8EF">최대설정금액</th>
			<th style="background:#F8F8EF">투자금액</th>
			<th style="background:#F8F8EF">실행결과</th>
		</tr>
		</thead>
<?
if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$exec_mode = ($LIST[$i]['is_real']=='1') ? '정상실행' : '테스트';
		$print_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];
		$fcolor = ($LIST[$i]['rcode']>0) ? '#999' : '#3366FF';

?>
		<tr align="center">
			<td><?=$num?></td>
			<td><?=$LIST[$i]['exec_date']?></td>
			<td><?=$exec_mode?></td>
			<td><?=$LIST[$i]['grp_title']?></td>
			<td><?=$LIST[$i]['title']?></td>
			<td><?=$LIST[$i]['mb_no']?></td>
			<td><?=$LIST[$i]['mb_id']?></td>
			<td><?=$print_name?></td>
			<td align="right"><?=number_format($LIST[$i]['mb_point'])?></td>
			<td align="right"><?=number_format($LIST[$i]['setup_amount'])?>원</td>
			<td align="right"><?=number_format($LIST[$i]['setup_amount2'])?>원</td>
			<td align="right"><?=number_format($LIST[$i]['amount'])?>원</td>
			<td><span style="color:<?=$fcolor?>"><?=$LIST[$i]['msg']?></span></td>
		</tr>
<?
		$num--;
	}
}
else {
	echo '
		<tr>
			<td colspan="11" align="center">데이터가 없습니다.</th>
		</tr>' . PHP_EOL;
}
?>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

</div>

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

<?

include_once ('../admin.tail.php');

?>