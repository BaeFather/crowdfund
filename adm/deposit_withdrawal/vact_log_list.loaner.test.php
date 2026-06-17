<?

if($sdate) $_sdate = preg_replace("/-/", "", $sdate);
if($edate) $_edate = preg_replace("/-/", "", $edate);
if($_sdate && $_edate) {
	if($_sdate > $_edate) alert("일자검색조건이 정상적이지 않습니다.");
}



$where = " AND A.TR_AMT_GBN = '20'";
if($_sdate && $_edate) {
	$where.= " AND A.SR_DATE BETWEEN '".$_sdate."' AND '".$_edate."'";
}
else {
	if($_sdate) $where.= " AND A.SR_DATE >= '".$_sdate."'";
	if($_edate) $where.= " AND A.SR_DATE <= '".$_edate."'";
}

if($key_search && $keyword) {

	if( $key_search == 'A.CUST_ID') {
		if( preg_match("/\,/", $keyword) ) {
			$where.= " AND {$key_search} IN(".preg_replace("/( )/", "", $keyword).")";
		}
		else {
			$where.= " AND {$key_search} = '".$keyword."'";
		}
	}
	else if($key_search == 'mb_id') {
		$srchsql = "SELECT mb_no AS mb_no FROM g5_member WHERE member_group='L' AND mb_id = '".$keyword."'";
		$srchres = sql_query($srchsql);
		$srch_mb_no = '';
		for($i=0,$j=1; $i<$srchres->num_rows; $i++,$j++) {
			$TMP_R = sql_fetch_array($srchres);
			$srch_mb_no.= $TMP_R['mb_no'];
			$srch_mb_no.= ($j<$srchres->num_rows) ? ',' : '';
		}
		//print_rr($srchsql, 'color:red');
		$where.= ($srch_mb_no) ? " AND A.CUST_ID IN(".$srch_mb_no.")" : " AND A.CUST_ID = '0'";
	}
	else if($key_search == 'prd_idx') {
		$srchsql = "SELECT DISTINCT(loan_mb_no) AS loan_mb_no FROM cf_product WHERE idx = '".$keyword."'";
		$srchres = sql_query($srchsql);
		$srch_mb_no = '';
		for($i=0,$j=1; $i<$srchres->num_rows; $i++,$j++) {
			$TMP_R = sql_fetch_array($srchres);
			$srch_mb_no.= $TMP_R['loan_mb_no'];
			$srch_mb_no.= ($j<$srchres->num_rows) ? ',' : '';
		}
		//print_rr($srchsql, 'color:blue');
		$where.= ($srch_mb_no) ? " AND A.CUST_ID IN(".$srch_mb_no.")" : " AND A.CUST_ID = '0'";
	}
	else if($key_search == 'prd_title') {
		$srchsql = "SELECT DISTINCT(loan_mb_no) AS loan_mb_no FROM cf_product WHERE title LIKE '%".$keyword."%'";
		$srchres = sql_query($srchsql);
		$srch_mb_no = '';
		for($i=0,$j=1; $i<$srchres->num_rows; $i++,$j++) {
			$TMP_R = sql_fetch_array($srchres);
			$srch_mb_no.= $TMP_R['loan_mb_no'];
			$srch_mb_no.= ($j<$srchres->num_rows) ? ',' : '';
		}
		//print_rr($srchsql, 'color:green');
		$where.= ($srch_mb_no) ? " AND A.CUST_ID IN(".$srch_mb_no.")" : " AND A.CUST_ID = '0'";
	}
	else {
		$where .= " AND ".$key_search." LIKE '%".$keyword."%'";
	}

}



$sql = "
	SELECT
		COUNT(A.FB_SEQ) AS cnt,
		IFNULL(SUM(A.TR_AMT),0) AS total_amount
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID = B.mb_no
	WHERE 1
		$where";
//print_rr($sql);
$ROW = sql_fetch($sql);
$total_count  = $ROW['cnt'];
$total_amount = $ROW['total_amount'];
unset($ROW);


$rows = 30;
//$rows = $config['cf_page_rows'];

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "
	SELECT
		A.SR_DATE, A.FB_SEQ, A.CUST_ID, A.BANK_ID, A.ACCT_NB, A.TR_AMT, A.REMITTER_NM, A.MEDIA_GBN, A.TR_AMT_GBN, A.TR_NB, A.ERP_TRANS_DT,
		B.mb_id, member_type, IF(member_type='2', mb_co_name, mb_name) AS mb_title
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID = B.mb_no
	WHERE 1
		$where
	ORDER BY
		ERP_TRANS_DT DESC";
$sql.= ($mode == 'download') ? "" : " LIMIT $from_record, $rows";
//print_rr($sql);
$result = sql_query($sql);
$rcount = $result->num_rows;

$page_total_amount = 0;

for($i=0; $i<$rcount; $i++) {

	$LIST[$i] = sql_fetch_array($result);

	$sql0 = "
		SELECT
			idx, category, category2, mortgage_guarantees, title, recruit_amount, state, loan_start_date, loan_end_date
		FROM
			cf_product
		WHERE 1
			AND loan_mb_no = '".$LIST[$i]['CUST_ID']."'
			AND state NOT IN('','3','6','7')
			AND loan_start_date < '".$edate."' AND loan_end_date >= '".$sdate."'
		ORDER BY
			loan_start_date ASC,
			start_num ASC";
	//print_rr($sql0, 'color:magenta');
	$res0 = sql_query($sql0);
	if($res0->num_rows) {
		while($PLIST = sql_fetch_array($res0)) {
			$LIST[$i]['PLIST'][] = $PLIST;
		}
	}

	$page_total_amount+= $LIST[$i]['TR_AMT'];

}

//print_rr($LIST);

$num = $total_count - $from_record;

?>

<? if($mode != 'download') { ?>
	<!-- 검색영역 START -->
	<div>
		<form id="frmSearch" name="frmSearch" class="form-horizontal" style="margin:0;">
			<input type="hidden" id="view" name="view" value="<?=$view?>">
		<div class="form-group">
			<ul class="col-sm-10 list-inline" style="margin-bottom: 4px;">
				<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" placeholder="ERP 전송일(시작)" autocomplete="off"></li>
				<li>~</li>
				<li><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" placeholder="ERP 전송일(종료)" autocomplete="off"></li>
			</ul>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li>
					<select name="key_search" class="form-control input-sm">
						<option value="">검색항목</option>
						<option value="mb_id" <?=($key_search=='mb_id')?'selected':''; ?>>차주아이디</option>
						<option value="A.CUST_ID" <?=($key_search=='A.CUST_ID')?'selected':''; ?>>차주회원번호</option>
						<option value="A.ACCT_NB" <?=($key_search=='A.ACCT_NB')?'selected':''; ?>>가상계좌번호</option>
						<option value="A.REMITTER_NM" <?=($key_search=='A.REMITTER_NM')?'selected':''; ?>>의뢰인(입금자)명</option>
						<option value="A.FB_SEQ" <?=($key_search=='A.FB_SEQ')?'selected':''; ?>>거래고유번호</option>
						<option value="A.TR_NB" <?=($key_search=='A.TR_NB')?'selected':''; ?>>입금거래번호</option>
						<option value="prd_idx" <?=($key_search=='prd_idx')?'selected':''; ?>>상품번호</option>
						<option value="prd_title" <?=($key_search=='prd_title')?'selected':''; ?>>상품명</option>
					</select>
				</li>
				<li><input type="text" class="form-control input-sm" name="keyword" size="30" value="<?=$keyword?>" placeholder="키워드"></li>
				<li><button type="button" onClick="fSubmit(1);" class="btn btn-sm btn-primary">검색</button></li>
				<li><button type="button" onClick="fSubmit(2);" class="btn btn-sm btn-success">엑셀저장</button></li>
			</ul>
		</div>
		</form>
	</div>

	<script>
	function fSubmit(arg) {
		var f = document.frmSearch;
		if(arg==1) {
			f.action = 'vact_log.php?view=<?=$view?>';
			f.method = 'get';
			f.target = '_self';
		}
		else {
			f.action = 'vact_log.php?view=<?=$view?>&mode=download';
			f.method = 'post';
			f.target = '_blank';
		}
		f.submit();
	}
	</script>
<? } ?>

	<!-- 리스트 START -->
	<table id="dataList" class="table table-striped table-bordered table-hover"  <? if($mode=='download') {?>border="1"<? } ?>>
		<thead>
			<tr>
				<th scope="col" style="text-align:center;background:#F8F8EF">NO.</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">입금구분</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">회원구분</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">회원번호</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">아이디</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">성명/법인명</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">가상계좌번호</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">상품카테고리</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">(품번) 상품명</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">입금액</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">입금자명</th>
				<th scope="col" style="text-align:center;background:#F8F8EF">ERP 전송일시</th>
				<?if($mode != 'download'){?><th scope="col" style="text-align:center;background:#F8F8EF">거래고유번호</th><?}?>
				<?if($mode != 'download'){?><th scope="col" style="text-align:center;background:#F8F8EF">전문전송일</th><?}?>
				<?if($mode != 'download'){?><th scope="col" style="text-align:center;background:#F8F8EF">입금거래번호</th><?}?>
				<?if($mode != 'download'){?><th scope="col" style="text-align:center;background:#F8F8EF">입금자<br/>단일내역</th><?}?>
			</tr>
			<tr>
				<td style="text-align:center;background:#EEEEFF"><span style="color:brown">합계</span></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<td style="text-align:right;background:#EEEEFF"><?=number_format($page_total_amount);?></td>
				<td style="text-align:right;background:#EEEEFF"><?=number_format($total_amount);?></td>
				<td style="background:#EEEEFF"></td>
				<td style="background:#EEEEFF"></td>
				<?if($mode != 'download'){?><td style="background:#EEEEFF"></td><?}?>
				<?if($mode != 'download'){?><td style="background:#EEEEFF"></td><?}?>
				<?if($mode != 'download'){?><td style="background:#EEEEFF"></td><?}?>
				<?if($mode != 'download'){?><td style="background:#EEEEFF"></td><?}?>
			</tr>
		</thead>
		<tbody style="font-size:9pt">
<?
$list_count = count($LIST);

if($list_count > 0) {
	for ($i=0; $i<$list_count; $i++) {

		$print_member_type = ($LIST[$i]['member_type']=='2') ? "법인" : '<font style="color:#AAA">개인</font>';

		if($LIST[$i]['PLIST'][0]['category']=='2') {
			$category = ($LIST[$i]['PLIST'][0]['mortgage_guarantees']=='1') ? '주택담보' : '부동산';
		}
		else if($LIST[$i]['PLIST'][0]['category']=='3') {
			$category = 'SCF';
		}
		else {
			$category = '동산';
		}

		if(!$LIST[$i]['TR_AMT']) $tr_style = "background-color:#FFEEEE";

		$print_gubun = ($LIST[$i]['TR_AMT_GBN']=='20') ? '<font style="color:#FF2222">상환금</font>' : '<font style="color:#2222FF">예치금</font>';
		$trans_date = date("Y.m.d H:i", strtotime($LIST[$i]['ERP_TRANS_DT']));

		$print_title = '('.$LIST[$i]['PLIST'][0]['idx'].') ' . $LIST[$i]['PLIST'][0]['title'];
		if( count($LIST[$i]['PLIST']) > 1 ) $print_title.= ' (외 ' . (count($LIST[$i]['PLIST'])-1) .'건)';
		/*
		$print_title = '';
		for($k=0, $_k=1; $k<count($LIST[$i]['PLIST']); $k++,$_k++) {
			$print_title.= '(' . $LIST[$i]['PLIST'][$k]['idx'] . ')' . $LIST[$i]['PLIST'][$k]['title'];
			$print_title.= ( $_k < count($LIST[$i]['PLIST']) ) ? '<br/>' : '';
		}
		*/

?>
			<tr align="center" style="<?=$tr_style?>">
				<td><?=number_format($num);?></td>
				<td><?=$print_gubun?></td>
				<td><?=$print_member_type?></td>
				<td><a href="<?=G5_URL?>/adm/member/member_view.php?&mb_id=<?=urlencode($LIST[$i]['mb_id'])?>"><?=$LIST[$i]['CUST_ID']?></a></td>
				<td><a href="<?=G5_URL?>/adm/member/member_view.php?&mb_id=<?=urlencode($LIST[$i]['mb_id'])?>"><?=$LIST[$i]['mb_id']?></a></td>
				<td><?=$LIST[$i]['mb_title']?></td>
				<td style="mso-number-format:'@';"><?=$LIST[$i]['ACCT_NB']?></td>
				<td align="center"><?=$category?></td>
				<td align="left"><?=$print_title?></td>
				<td align="right"><?=number_format($LIST[$i]['TR_AMT'])?></td>
				<td><?=$LIST[$i]['REMITTER_NM']?></td>
				<td><?=$trans_date?></td>
				<?if($mode != 'download'){?><td><?=$LIST[$i]['FB_SEQ']?></td><?}?>
				<?if($mode != 'download'){?><td><?=$LIST[$i]['SR_DATE']?></td><?}?>
				<?if($mode != 'download'){?><td><?=$LIST[$i]['TR_NB']?></td><?}?>
				<?if($mode != 'download'){?><td><button onClick="location.href='?view=<?=$view?>&sdate=<?=$sdate?>&edate=<?=$edate?>&key_search=A.CUST_ID&keyword=<?=urlencode($LIST[$i]['CUST_ID'])?>'" class="btn btn-sm btn-default" style="line-height:12px;">내역보기</button></td><?}?>
			</tr>
<?
		$num--;
	}
}else {
?>

			<tr>
				<td colspan="15" align="center" height="300px";>검색된 데이터가 없습니다.</td>
			</tr>

<?
}
?>
		</tbody>
	</table>
	<!-- 리스트 E N D -->

<?
if($mode != 'download') {
	$qstr = preg_replace("/&page=[0-9]/", "", $_SERVER['QUERY_STRING']);
	echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");
}
?>