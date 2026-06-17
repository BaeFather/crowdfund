<?
$sub_menu = "601200";
include_once('./_common.php');


$g5['title'] = $menu['menu601'][2][1];
include_once (G5_ADMIN_PATH.'/admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$sql_search = "1=1";
if($member_type) $sql_search.= " AND B.member_type='$member_type' ";
if($ai_grp_idx) $sql_search.= " AND A.ai_grp_idx='$ai_grp_idx' ";
if($category) $sql_search.= " AND C.category='$category' ";
if($date_field) {
	if($sdate) $sql_search.= " AND LEFT($date_field, 10)>='$sdate' ";
	if($edate) $sql_search.= " AND LEFT($date_field, 10)<='$edate' ";
}
if($key_search && $keyword) {
	if( in_array($key_search, array('B.mb_no','A.setup_amount')) ) {
		$sql_search .= " AND $key_search='$keyword' ";
	}
	else if($key_search=='B.mb_name') {
		$sql_search .= " AND (B.mb_name LIKE '%$keyword%' OR B.mb_co_name LIKE '%$keyword%')";
	}
	else {
		$sql_search .= " AND $key_search LIKE '%$keyword%' ";
	}
}


$sql = "
	SELECT
		COUNT(A.idx) AS cnt,
		IFNULL(SUM(A.setup_amount), 0) AS amount,
		IFNULL(SUM(A.setup_amount2), 0) AS amount2
	FROM
		cf_auto_invest_config_user A
	LEFT JOIN
		(SELECT mb_no,member_type,mb_name,mb_co_name FROM g5_member WHERE mb_level='1') B
	ON
		A.member_idx=B.mb_no
	LEFT JOIN
		cf_auto_invest_config C
	ON
		A.ai_grp_idx=C.idx
	WHERE
		$sql_search";

$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_setup_amount = $row['amount'];
$total_setup_amount2 = $row['amount2'];



$page_rows = 50;
$total_page = ceil($total_count / $page_rows);		// 전체 페이지 계산
$page = ($page) ? $page : 1;											// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;					// 시작 열을 구함

$sql_order = "";
if($sort_field) {
	if($sort_field=='A.idx') {
		$sql_order.= $sort_field." ".$sort." ";
	}
	else {
		$sql_order.= $sort_field." ".$sort.", A.idx DESC ";
	}
}
else {
	$sql_order.= " A.idx DESC ";
}

$sql = "
	SELECT
		A.*,
		B.mb_no, B.mb_id, B.mb_name, B.member_type, B.mb_co_name, B.mb_point,
		C.category, C.grp_title
	FROM
		cf_auto_invest_config_user A
	LEFT JOIN
		g5_member B
	ON
		A.member_idx=B.mb_no
	LEFT JOIN
		cf_auto_invest_config C
	ON
		A.ai_grp_idx=C.idx
	WHERE
		$sql_search
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $page_rows";
//echo $sql;
$result = sql_query($sql);
$rcount = $result->num_rows;

$page_total_setup_amount = 0;
$page_total_setup_amount2 = 0;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$page_total_setup_amount+=$LIST[$i]['setup_amount'];
	$page_total_setup_amount2+=$LIST[$i]['setup_amount2'];
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
				<select name="ai_grp_idx" id="ai_grp_idx" class="form-control" style="min-width:300px">
					<option value="">::자동투자그룹::</option>
<?
	$res = sql_query("SELECT idx, grp_title FROM cf_auto_invest_config WHERE display='Y' ORDER BY idx");
	while($ROW = sql_fetch_array($res)) {
		$selected = ($ROW['idx']==$ai_grp_idx) ? 'selected' : '';
		echo '<option value="'.$ROW['idx'].'" '.$selected.'>'.$ROW['grp_title'].'</option>' . PHP_EOL;
	}
?>
				</select>
			</li>
		</ul>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select id="member_type" name="member_type" class="form-control input-sm">
					<option value="">::회원구분::</option>
					<option value="1" <? if($member_type=='1'){echo 'selected';} ?>>개인회원</option>
					<option value="2" <? if($member_type=='2'){echo 'selected';} ?>>법인회원</option>
					<option value="3" <? if($member_type=='3'){echo 'selected';} ?>>SNS회원</option>
				</select>
			</li>
			<li>
				<select id="category" name="category" class="form-control input-sm">
					<option value="">::담보형태::</option>
					<option value="2" <? if($category=='2'){echo 'selected';} ?>>부동산</option>
					<option value="1" <? if($category=='1'){echo 'selected';} ?>>동산</option>
					<option value="3" <? if($category=='3'){echo 'selected';} ?>>확정매출채권</option>
				</select>
			</li>
			<li></li>
			<li>
				<select name="date_field" class="form-control input-sm">
					<option value="">::데이트 필드선택::</option>
					<option value="A.rdate" <?=($date_field=='A.rdate')?'selected':'';?>>설정등록일</option>
					<option value="A.edate" <?=($date_field=='A.edate')?'selected':'';?>>설정수정일</option>
					<option value="B.mb_datetime" <?=($date_field=='B.mb_datetime')?'selected':'';?>>회원가입일</option>
				</select>
			</li>
			<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
			<li>~</li>
			<li><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
		</ul>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select name="key_search" class="form-control input-sm">
					<option value="">::필드선택::</option>
					<option value="B.mb_no" <? if($key_search == 'B.mb_no'){echo 'selected';} ?>>회원번호</option>
					<option value="B.mb_id" <? if($key_search == 'B.mb_id'){echo 'selected';} ?>>아이디</option>
					<option value="B.mb_name" <? if($key_search == 'B.mb_name'){echo 'selected';} ?>>성명/사업자명</option>
					<option value="A.setup_amount" <? if($key_search == 'A.setup_amount'){echo 'selected';} ?>>설정금액</option>
				</select>
			</li>
			<li><input type="text" class="form-control input-sm" name="keyword" size="30" value="<?=$keyword;?>"></li>
			<li><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
			<li></li>
			<li>
				<select id="sort_field" class="form-control input-sm" style="width:150px;">
					<option value="">::정렬필드::</option>
					<option value="A.idx" <?=($sort_field=='A.idx')?'selected':'';?>>설정순</option>
					<option value="A.rdate" <?=($sort_field=='A.rdate')?'selected':'';?>>등록일시</option>
					<option value="A.edate" <?=($sort_field=='A.edate')?'selected':'';?>>수정일시</option>
					<option value="A.setup_amount" <?=($sort_field=='A.setup_amount')?'selected':'';?>>설정금액</option>
					<option value="B.mb_point" <?=($sort_field=='B.mb_point')?'selected':'';?>>보유예치금</option>
				</select>
			</li>
			<li>
				<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
				<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
			</li>
		</ul>
		</form>
	</div>
	<script>
	function sortList(param) {
		if(document.getElementById('sort_field').value!='') {
			url = '/adm/auto_invest/auto_invest_users_setup.php'
					+ '?member_type=<?=$member_type?>'
					+ '&ai_grp_idx=<?=$ai_grp_idx?>'
					+ '&category=<?=$category?>'
					+ '&date_field=<?=$date_field?>'
					+ '&sdate=<?=$sdate?>'
					+ '&edate=<?=$edate?>'
					+ '&key_search=<?=$key_search?>'
					+ '&keyword=<?=$keyword?>'
					+ '&sort_field=' + document.getElementById('sort_field').value
					+ '&sort=' + param

			location.href= url;
		}
		else {
			alert('정렬필드를 선택하십시요.'); document.getElementById('sort_field').focus();
		}
	}
	</script>
	<!-- 검색영역 E N D -->

	<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;">
		<colgroup>
			<col style="width:5%">
			<col style="width:5%">
			<col style="width:%">
			<col style="width:5%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
		</colgroup>
		<thead style="font-size:13px;">
		<tr>
			<th style="background:#F8F8EF">예약순번</th>
			<th style="background:#F8F8EF">설정번호</th>
			<th style="background:#F8F8EF">자동투자그룹</th>
			<th style="background:#F8F8EF">회원번호</th>
			<th style="background:#F8F8EF">아이디</th>
			<th style="background:#F8F8EF">성명/사업자명</th>
			<th style="background:#F8F8EF">보유예치금</th>
			<th style="background:#F8F8EF">최소 설정금액</th>
			<th style="background:#F8F8EF">최대 설정금액</th>
			<th style="background:#F8F8EF">투자위험고지동의</th>
			<th style="background:#F8F8EF">등록일시</th>
			<th style="background:#F8F8EF">수정일시</th>
		</tr>
		</thead>
		<!-- 합계 -->
		<tr style="background:#EEEEFF;color:red">
			<td align="center">합계</td>
			<td colspan="6"></td>
			<td align="right" style="font-size:12px;">
				전체. <?=number_format($total_setup_amount)?>원<br>
				<span style="color:#FF6633">페이지. <?=number_format($page_total_setup_amount)?>원</span>
			</td>
			<td align="right" style="font-size:12px;">
				전체. <?=number_format($total_setup_amount2)?>원<br>
				<span style="color:#FF6633">페이지. <?=number_format($page_total_setup_amount2)?>원</span>
			</td>
			<td colspan="3"></td>
		</tr>
		<!-- 합계 -->
<?
if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		$print_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];
		$print_invest_warning_agree = ($LIST[$i]['invest_warning_agree']=='1') ? '동의함' : '';

?>
		<tr align="center">
			<td><?=$num?></td>
			<td><?=$LIST[$i]['idx']?></td>
			<td><a href="/adm/auto_invest/auto_invest_group_form.php?idx=<?=$LIST[$i]['ai_grp_idx']?>"><?=$LIST[$i]['grp_title']?></a></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>"><?=$LIST[$i]['mb_no']?></a></td>
			<td><?=$LIST[$i]['mb_id']?></td>
			<td><?=$print_name?></td>
			<td align="right"><?=number_format($LIST[$i]['mb_point'])?>원</td>
			<td align="right"><?=number_format($LIST[$i]['setup_amount'])?>원</td>
			<td align="right"><?=number_format($LIST[$i]['setup_amount2'])?>원</td>
			<td><?=$print_invest_warning_agree?></td>
			<td><?=substr($LIST[$i]['rdate'],0,16)?></td>
			<td><?=substr($LIST[$i]['edate'],0,16)?></td>
		</tr>
<?
		$num--;
	}
}
else {
	echo '
		<tr>
			<td colspan="12" align="center">데이터가 없습니다.</th>
		</tr>' . PHP_EOL;
}
?>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script>
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