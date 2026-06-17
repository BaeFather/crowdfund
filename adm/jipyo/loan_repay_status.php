<?
exit;
///////////////////////////////////////////////////////
//	대출 실행 상환 일대사
///////////////////////////////////////////////////////

include_once('./_common.php');


$g5['title'] = '대출실행/상환 일대사';


while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k)) ${$k} = trim($v); }

$G_TYPE = array(
	'A' => '전체',
	'1' => '부동산PF',
	'2' => '주택담보',
	'3' => '헬로페이',
	'4' => '동산',
);

if(!$g_type) $g_type = 'A';


$where = "1=1";
$where.= " AND g_type='$g_type'";

if($sdate && $edate) {
	if($sdate > $edate) { msg_go('대상일 범위가 정상적이지 않습니다.'); }
	if($sdate) $where.= " AND tDate >= '$sdate'";
	if($edate) $where.= " AND tDate <= '$edate'";
}
else {
	$sdate = date('Y-m').'-01';
	$edate = date('Y-m-d');
	$where.= " AND tDate BETWEEN '$sdate' AND '$edate'";
}

$sort = ($sort) ? $sort : 'DESC';

// 정렬우선순위 : 모집중 > 모집완료 > 이후
$sql = "
	SELECT
		*
	FROM
		cf_loan_repay_status
	WHERE
		$where
	ORDER BY
		tDate $sort";
$result = sql_query($sql);
$rcount = $result->num_rows;

for($i=0; $i<$rcount; $i++) {

	$LIST[$i] = sql_fetch_array($result);

	// 세금합계
	$LIST[$i]['tax'] = $LIST[$i]['interest_tax'] + $LIST[$i]['local_tax'];
	$LIST[$i]['tax_sum'] = $LIST[$i]['interest_tax_sum'] + $LIST[$i]['local_tax_sum'];

	// 세후이자
	$LIST[$i]['after_tax_interest'] = $LIST[$i]['interest'] - $LIST[$i]['local_tax'] - $LIST[$i]['fee'];
	$LIST[$i]['after_tax_interest_sum'] = $LIST[$i]['interest_sum'] - $LIST[$i]['local_tax_sum'] - $LIST[$i]['fee_sum'];

	//echo "<div style='font-size:11px;margin-bottom:4px;'>"; print_r($LIST[$i]); echo "</div>\n";

}

sql_free_result($result);


include_once(G5_ADMIN_PATH.'/admin.head.php');

?>

<style>
.table th.border_r { border-right:1px solid #999; }
.table td.border_r { border-right:1px solid #999; }
input::placeholder { text-align:center; }
</style>

<div class="tbl_head02 tbl_wrap" style="min-width:1500px;">

	<!-- 검색영역 START -->
	<div style="display:inline-block; width:100%;">
		<form id="frmSearch" method="get" action="<?=$_SERVER['PHP_SELF']?>">
		<ul class="col-sm-10 list-inline">
			<li>상품구분</li>
			<li>
				<select name="g_type" class="form-control input-sm" style="width:150px">
<?
$G_TYPE_KEY = array_keys($G_TYPE);
for($i=0; $i<count($G_TYPE); $i++) {
	$selected = ($G_TYPE_KEY[$i]==$g_type) ? 'selected' : '';
	echo "<option value='".$G_TYPE_KEY[$i]."' $selected>".$G_TYPE[$G_TYPE_KEY[$i]]."</option>\n";
}
?>
				</select>
			</li>
			<li></li>
			<li>대상일</li>
			<li><input type="text" name="sdate" value="<?=$sdate?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
			<li>~</li>
			<li><input type="text" name="edate" value="<?=$edate?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
			<li></li>
			<li>
				<select name="sort" class="form-control input-sm" style="width:150px" onChange="return document.frmSearch.submit();">
					<option value='ASC' <?=($sort=='ASC')?'selected':''?>>대상일 오름차수▲</option>
					<option value='DESC' <?=($sort=='DESC')?'selected':''?>>대상일 내림차순▼</option>
				</select>
			</li>
			<li><input type="submit" class="btn btn-sm btn-primary" value="검색"></li>
		</ul>
		</form>
	</div>

	<table id="dataList" class="table table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<colgroup>
			<col style="width:*">
			<col style="width:6%">
			<col style="width:5%">
			<col style="width:6%">
			<col style="width:6%">
			<col style="width:6%">
			<col style="width:6%">
			<col style="width:6%">
			<col style="width:7%">
			<col style="width:5.5%">
			<col style="width:6%">
			<col style="width:5.5%">
			<col style="width:6%">
			<col style="width:5.5%">
			<col style="width:6%">
			<col style="width:5.5%">
			<col style="width:6%">
		</colgroup>
		<thead>
			<tr>
				<th rowspan="2" style="background:#F0F8FF" class="border_r">대상일</th>
				<th rowspan="2" style="background:#F0F8FF" class="border_r">상품구분</th>
				<th colspan="2" style="background:#F0F8FF" class="border_r">대출실행건수</th>
				<th colspan="2" style="background:#F0F8FF" class="border_r">대출실행금액</th>
				<th colspan="2" style="background:#F0F8FF" class="border_r">원금상환금액</th>
				<th rowspan="2" style="background:#F0F8FF" class="border_r">대출잔액</th>
				<th colspan="2" style="background:#F0F8FF" class="border_r">세전이자</th>
				<th colspan="2" style="background:#F0F8FF" class="border_r">원천징수</th>
				<th colspan="2" style="background:#F0F8FF" class="border_r">플랫폼이용료</th>
				<th colspan="2" style="background:#F0F8FF">세후이자</th>
			</tr>
			<tr>
				<!-- 대출실행건수 -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF" class="border_r">누적</th>
				<!-- 대출실행금액 -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF" class="border_r">누적</th>
				<!-- 원금상환금액 -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF" class="border_r">누적</th>
				<!-- 지급이자(세전) -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF" class="border_r">누적</th>
				<!-- 원천징수 -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF" class="border_r">누적</th>
				<!-- 플랫폼이용료 -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF" class="border_r">누적</th>
				<!-- 세후이자 -->
				<th style="background:#F0F8FF">일별</th>
				<th style="background:#F0F8FF">누적</th>
			</tr>
		</thead>
		<tbody>
<?
if($rcount) {
	for($i=0,$j=1; $i<$rcount; $i++,$j++) {

		$style = ( date('w', strtotime($LIST[$i]['tDate']))=='0' ) ? 'background:#FFDDDD' : '';

		$FCOLOR['loan_cnt'] = ($LIST[$i]['loan_cnt'] > 0) ? '' : '#BBB';
		$FCOLOR['loan_cnt_sum'] = ($LIST[$i]['loan_cnt_sum'] > 0) ? '' : '#BBB';
		$FCOLOR['loan_amt'] = ($LIST[$i]['loan_amt'] > 0) ? '' : '#BBB';
		$FCOLOR['loan_amt_sum'] = ($LIST[$i]['loan_amt_sum'] > 0) ? '' : '#BBB';
		$FCOLOR['principal'] = ($LIST[$i]['principal'] > 0) ? '' : '#BBB';
		$FCOLOR['principal_sum'] = ($LIST[$i]['principal_sum'] > 0) ? '' : '#BBB';
		$FCOLOR['remain_amt'] = ($LIST[$i]['remain_amt'] > 0) ? '' : '#BBB';
		$FCOLOR['interest'] = ($LIST[$i]['interest'] > 0) ? '' : '#BBB';
		$FCOLOR['interest_sum'] = ($LIST[$i]['interest_sum'] > 0) ? '' : '#BBB';
		$FCOLOR['tax'] = ($LIST[$i]['tax'] > 0) ? '' : '#BBB';
		$FCOLOR['tax_sum'] = ($LIST[$i]['tax_sum'] > 0) ? '' : '#BBB';
		$FCOLOR['fee'] = ($LIST[$i]['fee'] > 0) ? '' : '#BBB';
		$FCOLOR['fee_sum'] = ($LIST[$i]['fee_sum'] > 0) ? '' : '#BBB';
		$FCOLOR['after_tax_interest'] = ($LIST[$i]['after_tax_interest'] > 0) ? '' : '#BBB';
		$FCOLOR['after_tax_interest_sum'] = ($LIST[$i]['after_tax_interest_sum'] > 0) ? '' : '#BBB';

?>
			<tr style='<?=$style?>'>
				<td style="text-align:center" class="border_r"><?=$LIST[$i]['tDate']?></td>
				<td style="text-align:center" class="border_r"><?=$G_TYPE[$LIST[$i]['g_type']]?></td>
				<td style="text-align:right;color:<?=$FCOLOR['loan_cnt']?>"><?=$LIST[$i]['loan_cnt']?></td>
				<td style="text-align:right;color:<?=$FCOLOR['loan_cnt_sum']?>" class="border_r"><?=number_format($LIST[$i]['loan_cnt_sum'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['loan_amt']?>"><?=number_format($LIST[$i]['loan_amt'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['loan_amt_sum']?>" class="border_r"><?=number_format($LIST[$i]['loan_amt_sum'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['principal']?>"><?=number_format($LIST[$i]['principal'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['principal_sum']?>" class="border_r"><?=number_format($LIST[$i]['principal_sum'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['remain_amt']?>" class="border_r"><?=number_format($LIST[$i]['remain_amt'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['interest']?>"><?=number_format($LIST[$i]['interest'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['interest_sum']?>" class="border_r"><?=number_format($LIST[$i]['interest_sum'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['tax']?>"><?=number_format($LIST[$i]['tax'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['tax_sum']?>" class="border_r"><?=number_format($LIST[$i]['tax_sum'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['fee']?>"><?=number_format($LIST[$i]['fee'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['fee_sum']?>" class="border_r"><?=number_format($LIST[$i]['fee_sum'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['after_tax_interest']?>"><?=number_format($LIST[$i]['after_tax_interest'])?></td>
				<td style="text-align:right;color:<?=$FCOLOR['after_tax_interest_sum']?>"><?=number_format($LIST[$i]['after_tax_interest_sum'])?></td>
			</tr>
<?
	}
}
else {
	echo "			<tr><td align='center' colspan='20'>데이터가 없습니다.</td></tr>\n";
}
?>
		</tbody>
	</table>

</div>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>

<script>
$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>