<?
include_once('./_common.php');

foreach($_POST as $k=>$v) { ${$k} = trim( @urldecode($v) ); }

$date_s = $sdate . ' 00:00:00';
$date_e = $edate . ' 23:59:59';

$where = "";
//$where.= ($ib_regist=='1') ? " AND B.ib_regist='1' " : "";
$where.= ($receive_method_all=='Y') ? "" : " AND A.receive_method='1' ";

if($sdate && $edate) {
	$where.= " AND A.banking_date BETWEEN '$date_s' AND '$date_e' ";
}
else {
	if($sdate) { $where.= " AND A.banking_date>='$date_s' "; }
	if($edate) { $where.= " AND A.banking_date<='$date_e' "; }
}

if($field && $keyword) {
	if($field == 'mb_no') {
		if( preg_match("/\,/", $keyword) ) {
			$where.= " AND C.mb_no IN(".preg_replace("/( )/", "", $keyword).") ";
		}
		else {
			$where.= " AND C.mb_no='$keyword' ";
		}
	}
	else if($field == 'mb_id')   $where.= " AND C.mb_id LIKE '%{$keyword}%' ";
	else if($field == 'mb_name') $where.= " AND (C.mb_name LIKE '%{$keyword}%' OR C.mb_co_name LIKE '%{$keyword}%')";
	else if($field == 'mb_hp')   $where.= " AND C.mb_hp LIKE '%{$keyword}%' ";
}

$sql = "
	SELECT
		COUNT(A.idx) AS cnt
	FROM
		cf_product_give A
--	LEFT JOIN
--		cf_product_invest B  ON A.invest_idx=B.idx
	WHERE (1)
		$where";
//print_rr($sql,'font-size:12px;');
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$page_rows   = 10;
$total_page  = ceil($total_count / $page_rows);
$page        = ($page) ? $page : 1;
$from_record = ($page - 1) * $page_rows;

$sql = "
	SELECT
		A.date, A.receive_method, A.invest_amount, A.interest, A.principal, A.member_idx, A.invest_idx, A.product_idx, A.turn, A.bank_name, A.bank_private_name, A.account_num, A.banking_date, A.GUAR_SEQ,
		C.member_type, C.mb_id, C.mb_name, C.mb_co_name, C.mb_hp,
		(SELECT title FROM cf_product WHERE idx=A.product_idx) AS title
	FROM
		cf_product_give A
--	LEFT JOIN
--		cf_product_invest B  ON A.invest_idx=B.idx
	LEFT JOIN
		g5_member C  ON  A.member_idx=C.mb_no
	WHERE (1)
		$where
	ORDER BY
		A.idx DESC
	LIMIT
		$from_record, $page_rows";
//print_rr($sql);
$res  = sql_query($sql);
$rcount = $res->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['sum_amt'] = $LIST[$i]['interest'] + $LIST[$i]['principal'];
}

//print_rr($LIST);

?>

<style>
#paging_span2 { margin:0; padding:0; text-align:center; }
#paging_span2 span.arrow { padding:0; border:0; line-height:0; }
#paging_span2 span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span2 span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

	<!-- 리스트 START -->
	<table class="table-striped table-hover" style="font-size:12px">
		<colgroup>
			<col style="width:5%">
			<col style="width:%">
			<col style="width:5%">
			<col style="width:8%">
			<col style="width:8%">
			<col style="width:6%">
			<col style="width:8%">
			<col style="width:8%">
			<col style="width:6%">
			<col style="width:6%">
			<col style="width:6%">
			<col style="width:8%">
			<col style="width:6%">
		</colgroup>
		<thead>
		<tr align="center" style="background:#F8F8EF">
			<th scope="col" rowspan="2">NO</th>
			<th scope="col" rowspan="2">상품</th>
			<th scope="col" rowspan="2">지급회차</th>
			<th scope="col" rowspan="2">아이디</th>
			<th scope="col" rowspan="2">성명/법인명</th>
			<th scope="col" colspan="3">입금계좌</th>
			<th scope="col" colspan="3">지급액</th>
			<th scope="col" rowspan="2">지급일시</th>
			<th scope="col" rowspan="2">관리툴</th>
		</tr>
		<tr style="background:#F8F8EF">
			<th scope="col" style="text-align:center;">은행</th>
			<th scope="col" style="text-align:center;">계좌번호</th>
			<th scope="col" style="text-align:center;">예금주</th>
			<th scope="col" style="text-align:center;">원금</th>
			<th scope="col" style="text-align:center;">이자</th>
			<th scope="col" style="text-align:center;">합계</th>
		</tr>
		</thead>
		<tbody>
<?
$num = $total_count - $from_record;
if($rcount > 0) {
	for($i=0; $i<$rcount; $i++) {

		if($LIST[$i]['member_type']=='2') {
			$print_mb_name = $LIST[$i]['mb_co_name'];
			$print_mb_hp   = $LIST[$i]['mb_hp'];
			$print_account_num = $LIST[$i]['account_num'];
		}
		else {
			$print_mb_name = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_name'] : hanStrMasking($LIST[$i]['mb_name']);
			$print_mb_hp   = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_hp'] : substr($LIST[$i]['mb_hp'],0,strlen($LIST[$i]['mb_hp'])-4)."****";;
			$print_account_num = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['account_num'] : substr($LIST[$i]['account_num'],0,strlen($LIST[$i]['account_num'])-4)."****";;
		}

?>
		<tr align="center">
			<td><?=number_format($num)?></td>
			<td><a href="/adm/product_calculate.php?idx=<?=$LIST[$i]['product_idx']?>"><?=$LIST[$i]['title']?></td>
			<td><?=$LIST[$i]['turn']?>회차</td>
			<td><a href="member/member_view.php?mb_id=<?=$LIST[$i]['mb_id']?>"><?=$LIST[$i]['mb_id']?></td>
			<td><?=$print_mb_name?></td>
<?
		if($LIST[$i]['receive_method']=='2') {
?>
			<td colspan="3" style="color:#FF2222">예치금으로 지급</td>
<?
		}
		else {
?>
			<td><?=$LIST[$i]['bank_name']?></td>
			<td><?=$print_account_num?></td>
			<td><?=($_SESSION['ss_accounting_admin'])?$LIST[$i]['bank_private_name']:'';?></td>
<?
		}
?>
			<td align="right"><?=number_format($LIST[$i]['principal'])?>원</td>
			<td align="right"><?=number_format($LIST[$i]['interest'])?>원</td>
			<td align="right"><?=number_format($LIST[$i]['sum_amt'])?>원</td>
			<td><?=substr($LIST[$i]['banking_date'], 0, 16)?></td>
			<td><button type="button" onClick="location.href='/adm/product_calculate.php?idx=<?=$LIST[$i]['product_idx']?>&mb_id=<?=$LIST[$i]['mb_id']?>'" class="btn btn-sm btn-info">정산내역</button></td>
		</tr>
<?
		$num--;
	}
}else {
?>

		<tr>
			<td colspan="15" align="center" height="100px";>검색된 데이터가 없습니다.</td>
		</tr>

<?
}
?>
		<tbody>
	</table>

	<div id="paging_span2" style="width:100%; margin:10px 0 0 0; text-align:center;">
<?
	paging($total_count, $page, $page_rows, 10);

//$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
//echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
	</div>
