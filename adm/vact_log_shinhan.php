<?
exit;

$sub_menu = '500400';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$html_title = "가상계좌입금내역 (신한은행)";
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.php');

/* 검색 필드 조합 START */

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) { ${$_GET[$k]} = (is_array($_GET[$k])) ? $v : trim($v); }

if(!$sdate) $sdate = date('Y-m-d');
if(!$edate) $edate = date('Y-m-d');

if($sdate) $_sdate = preg_replace("/-/", "", $sdate);
if($edate) $_edate = preg_replace("/-/", "", $edate);
if($_sdate && $_edate) {
	if($_sdate > $_edate) alert("일자검색조건이 정상적이지 않습니다.");
}



$where = "";
if($member_type) $where.= " AND B.member_type = '$member_type'";
if($syndication) $where.= " AND B.{$syndication}_userid!=''";
if($TR_AMT_GBN)  $where.= " AND A.TR_AMT_GBN = '$TR_AMT_GBN'";
if($ACCT_NB)     $where.= " AND A.ACCT_NB = '$ACCT_NB'";
if($BANK_ID)     $where.= " AND A.BANK_ID = '$BANK_ID'";
if($_sdate)      $where.= " AND A.SR_DATE >= '".$_sdate."'";
if($_edate)      $where.= " AND A.SR_DATE <= '".$_edate."'";
//if($_sdate)      $where.= " AND LEFT(A.ERP_TRANS_DT, 8) >= '".$_sdate."'";
//if($_edate)      $where.= " AND LEFT(A.ERP_TRANS_DT, 8) <= '".$_edate."'";
if($key_search && $keyword) $where .= " AND {$key_search} LIKE '%{$keyword}%' ";
//$where = " AND C.isTest = ''";

$sql = "
	SELECT
		COUNT(*) AS cnt,
		IFNULL(SUM(A.TR_AMT),0) AS total_amount
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID = B.mb_no
	LEFT JOIN
		cf_product C  ON (A.ACCT_NB = C.repay_acct_no AND C.isTest = '')
	WHERE (1)
		$where";
//print_rr($sql,'font-size:12px;line-height:14px;');
$ROW = sql_fetch($sql);
$total_count  = $ROW['cnt'];
$total_amount = $ROW['total_amount'];
unset($ROW);


$rows = 50;
//$rows = $config['cf_page_rows'];

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "
	SELECT
		A.SR_DATE, A.FB_SEQ, A.BANK_ID, A.ACCT_NB, A.TR_AMT, A.REMITTER_NM, A.MEDIA_GBN, A.TR_AMT_GBN, A.TR_NB, A.ERP_TRANS_DT, A.trans_to_point, A.repay_prd_idx,
		B.mb_no, B.mb_id, B.mb_name, B.mb_co_name, B.member_type,
		C.idx AS product_idx, C.gr_idx, C.title
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID = B.mb_no
	LEFT JOIN
		cf_product C  ON A.ACCT_NB = C.repay_acct_no
	WHERE (1)
		$where
	ORDER BY
		ERP_TRANS_DT DESC
	LIMIT
		$from_record, $rows";
//if($member['mb_id']=='admin_sori9th') print_rr($sql,'font-size:12px;line-height:14px;');
$result = sql_query($sql);
$rcount = $result->num_rows;

$page_total_amount = 0;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$page_total_amount+= $LIST[$i]['TR_AMT'];
}

//print_rr($LIST);

$num = $total_count - $from_record;

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js

?>

<style>
.btn_area { text-align:left; }
</style>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<div>
		<form id="frmSearch" name="frmSearch" action="vact_log_shinhan.php" method="get" class="form-horizontal" style="margin:0;">
		<div class="form-group">
			<ul class="col-sm-10 list-inline" style="margin-bottom: 4px;">
				<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" placeholder="ERP 전송일(시작)" autocomplete="off"></li>
				<li>~</li>
				<li><input type="text" id="edate" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" placeholder="ERP 전송일(종료)" autocomplete="off"></li>
			</ul>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li>
					<select name="member_type" class="form-control input-sm">
						<option value="">회원구분</option>
						<option value="1" <?=($member_type=='1')?'selected':''; ?>>개인회원</option>
						<option value="2" <?=($member_type=='2')?'selected':''; ?>>법인회원</option>
					</select>
				</li>
				<li>
					<select name="syndication" class="form-control input-sm">
						<option value="">신디케이션회원</option>
						<option value="finnq" <?=($syndication=='finnq')?'selected':''; ?>>핀크</option>
						<option value="wowstar" <?=($syndication=='wowstar')?'selected':''; ?>>한경TV</option>
						<option value="chosun" <?=($syndication=='chosun')?'selected':''; ?>>땅집고</option>
						<option value="r114" <?=($syndication=='r114')?'selected':''; ?>>부동산114</option>
					</select>
				</li>
				<li>
					<select name="TR_AMT_GBN" class="form-control input-sm">
						<option value="">입금구분</option>
						<option value="10" <?=($TR_AMT_GBN=='10')?'selected':''; ?>>예치금</option>
						<option value="20" <?=($TR_AMT_GBN=='20')?'selected':''; ?>>상환금</option>
					</select>
				</li>
				<li>
					<select name="key_search" class="form-control input-sm">
						<option value="">검색항목</option>
						<option value="A.CUST_ID" <?=($key_search=='A.CUST_ID')?'selected':''; ?>>회원번호</option>
						<option value="B.mb_id" <?=($key_search=='B.mb_id')?'selected':''; ?>>아이디</option>
						<option value="B.mb_name" <?=($key_search=='B.mb_name')?'selected':''; ?>>성명/법인명</option>
						<option value="A.ACCT_NB" <?=($key_search=='A.ACCT_NB')?'selected':''; ?>>가상계좌번호</option>
						<option value="A.REMITTER_NM" <?=($key_search=='A.REMITTER_NM')?'selected':''; ?>>의뢰인(입금자)명</option>
						<option value="A.FB_SEQ" <?=($key_search=='A.FB_SEQ')?'selected':''; ?>>거래번호</option>
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
		f.action = '/adm/vact_log_shinhan.php';
		f.target = '_self';
	}
	else {
		f.action = '/adm/vact_log_shinhan_excel.php';
		f.target = 'axFrame';
	}
	f.submit();
}
</script>


	<!-- 검색영역 E N D -->

	<!-- 리스트 START -->
	<table class="table table-striped table-bordered table-hover" style="font-size:12px">
		<thead>
			<tr>
				<th scope="col" style="text-align:center;">번호</th>
				<th scope="col" style="text-align:center;">회원번호</th>
				<th scope="col" style="text-align:center;">회원구분</th>
				<th scope="col" style="text-align:center;">아이디</th>
				<th scope="col" style="text-align:center;">성명/법인명</th>
				<th scope="col" style="text-align:center;">가상계좌번호</th>
				<th scope="col" style="text-align:center;">상품명</th>
				<th scope="col" style="text-align:center;">입금액</th>
				<th scope="col" style="text-align:center;">입금구분</th>
				<th scope="col" style="text-align:center;">입금자명</th>
				<th scope="col" style="text-align:center;">거래고유번호</th>
				<th scope="col" style="text-align:center;">전문전송일</th>
				<th scope="col" style="text-align:center;">입금거래번호</th>
				<th scope="col" style="text-align:center;">ERP 전송일시</th>
				<th scope="col" style="text-align:center;">단일내역</th>
			</tr>
		</thead>
		<tbody>
			<tr bgcolor="#EEEEFF">
				<td colspan="2" align="center"><span style="color:brown">전체 <?=number_format($total_count)?>건</span></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan="2" align="right"><?=number_format($page_total_amount);?>원 / <?=number_format($total_amount);?>원</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
<?
$list_count = count($LIST);

if($list_count > 0) {
	for ($i=0; $i<$list_count; $i++) {

		if($LIST[$i]['member_type']=='2') {
			$print_member_type = "법인회원";
			$print_mb_name = $LIST[$i]['mb_co_name'];
		}
		else {
			$print_member_type = ($LIST[$i]['member_type']=='3') ? '<font style="color:#AAA">SNS회원</font>' : '<font style="color:#AAA">개인회원</font>';
			$print_mb_name = $LIST[$i]['mb_name'];
		}

		switch($LIST[$i]['inp_st']) {
			case 1  : $state_txt = '입금';		break;
			case 2  : $state_txt = '<font style="color:#FF2222">취소</font>';		break;
			case 3  : $state_txt = '정산';		break;
			default : $state_txt = 'Unknown';		break;
		}

		if(!$LIST[$i]['TR_AMT']) $tr_style = "background-color:#FFEEEE";

		$print_gubun = ($LIST[$i]['TR_AMT_GBN']=='20') ? '<font style="color:#FF2222">상환금</font>' : '<font style="color:#2222FF">예치금</font>';

		$trans_date = date("Y.m.d H:i", strtotime($LIST[$i]['ERP_TRANS_DT']));
?>
			<tr align="center" style="<?=$tr_style?>">
				<td><?=number_format($num);?></td>
				<td><a href="/adm/member/member_view.php?&mb_id=<?=urlencode($LIST[$i]['mb_id'])?>"><?=$LIST[$i]['mb_no']?></a></td>
				<td><?=$print_member_type?></td>
				<td><a href="/adm/member/member_view.php?&mb_id=<?=urlencode($LIST[$i]['mb_id'])?>"><?=$LIST[$i]['mb_id']?></a></td>
				<td><a href="vact_log_shinhan.php?key_search=B.mb_name&keyword=<?=urlencode($print_mb_name)?>"><?=$print_mb_name?></a></td>
				<td><?=$LIST[$i]['ACCT_NB']?></td>
				<td align="left"><a href=""><?=$LIST[$i]['title']?></a></td>
				<td align="right"><?=number_format($LIST[$i]['TR_AMT'])?>원</td>
				<td><?=$print_gubun?></td>
				<td><?=$LIST[$i]['REMITTER_NM']?></td>
				<td><?=$LIST[$i]['FB_SEQ']?></td>
				<td><?=$LIST[$i]['SR_DATE']?></td>
				<td><?=$LIST[$i]['TR_NB']?></td>
				<td><?=$trans_date?></td>
				<td><button onClick="location.href='vact_log_shinhan.php?key_search=B.mb_id&keyword=<?=urlencode($LIST[$i]['mb_id'])?>'" class="btn btn-sm btn-default" style="line-height:12px;">내역보기</button></td>
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

</div>

<?
$qstr = preg_replace("/&page=[0-9]/", "", $_SERVER['QUERY_STRING']);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");
?>


<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>