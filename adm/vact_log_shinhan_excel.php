<?
$sub_menu = '500400';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "헬로펀딩 가상계좌입금내역(신한은행)";

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) {
	$$_GET[$k] = $v;
}

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
if($_sdate)      $where.= " AND LEFT(A.ERP_TRANS_DT, 8) >= '".$_sdate."'";
if($_edate)      $where.= " AND LEFT(A.ERP_TRANS_DT, 8) <= '".$_edate."'";
if($key_search && $keyword) $where .= " AND {$key_search} LIKE '%{$keyword}%' ";


$sql = "
	SELECT
		A.FB_SEQ, A.BANK_ID, A.ACCT_NB, A.TR_AMT, A.REMITTER_NM, A.MEDIA_GBN, A.TR_AMT_GBN, A.TR_NB, A.ERP_TRANS_DT, A.trans_to_point,
		B.mb_no, B.mb_id, B.mb_name, B.mb_co_name, B.member_type
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID = B.mb_no
	WHERE (1)
		$where
	ORDER BY
		ERP_TRANS_DT DESC";
$result = sql_query($sql);
$rcount = sql_num_rows($result);

if(!$rcount) {
	echo "<script>alert('검색 조건과 일치하는 데이터가 없습니다.')</script>\n";
	exit;
}

$total_amt = 0;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$total_amt += $LIST[$i]['TR_AMT'];
}

$file_name = $html_title . " " . date('Ymd_Hi') . ".xls";
$file_name = iconv("utf-8", "euc-kr", $file_name);

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-description: PHP5 Generated Data" );

?>
<table border="1">
	<tr>
		<th align="center" bgcolor="#EEEEEE">법인명</th>
		<th align="center" bgcolor="#EEEEEE">아이디</th>
		<th align="center" bgcolor="#EEEEEE">성명/담당자명</th>
		<th align="center" bgcolor="#EEEEEE">가상계좌은행</th>
		<th align="center" bgcolor="#EEEEEE">가상계좌번호</th>
		<th align="center" bgcolor="#EEEEEE">입금액</th>
		<th align="center" bgcolor="#EEEEEE">입금구분</th>
		<th align="center" bgcolor="#EEEEEE">입금자명</th>
		<th align="center" bgcolor="#EEEEEE">거래번호</th>
		<th align="center" bgcolor="#EEEEEE">ERP 전송일시</th>
	</tr>
	<tr>
		<td align="center" bgcolor="#FFCCCC">합계</td>
		<td align="center" colspan="3" bgcolor="#FFCCCC"></td>
		<td align="right" colspan="2" bgcolor="#FFCCCC" style="color:brown">(<?=number_format($rcount)?>건) <?=number_format($total_amt)?>원</td>
		<td align="center" colspan="4" bgcolor="#FFCCCC"></td>
	</tr>
<?
$num = $rcount;

for ($i=0, $j=$num; $i<$rcount; $i++,$j--) {

	if($LIST[$i]['member_type']=='2') {
		$mb_co_name = $LIST[$i]['mb_co_name'];
	}
	else {
		$mb_co_name = ($LIST[$i]['member_type']=='3') ? '<span style="color:#AAA">SNS회원</span>' : '<span style="color:#AAA">개인회원</span>';
	}

	switch($LIST[$i]['TR_AMT_GBN']) {
		case 10  : $state_txt = '예치금';		break;
		case 20  : $state_txt = '상환금';		break;
		default  : $state_txt = 'Unknown';	break;
	}

	$trans_date = date("Y-m-d H:i:s", strtotime($LIST[$i]['ERP_TRANS_DT']));
?>
	<tr>
		<td align="center"><?=$mb_co_name?></td>
		<td align="center"><?=$LIST[$i]['mb_id']?></td>
		<td align="center"><?=$LIST[$i]['mb_name']?></td>
		<td align="center"><?=$BANK[$LIST[$i]['BANK_ID']]?></td>
		<td align="center" style="mso-number-format:'@';"><?=$LIST[$i]['ACCT_NB']?></td>
		<td align="right"><?=number_format($LIST[$i]['TR_AMT'])?>원</td>
		<td align="center"><?=$state_txt?></td>
		<td align="center"><?=$LIST[$i]['REMITTER_NM']?></td>
		<td align="center"><?=$LIST[$i]['FB_SEQ']?></td>
		<td align="center" style="mso-number-format:'@';"><?=$trans_date?></td>
	</tr>
<?
}
?>
</table>

<?
exit;
?>