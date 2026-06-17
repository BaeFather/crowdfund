<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = "700300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$sql_common = " FROM cf_event_product_invest a, cf_event_product b, g5_member c ";
$sql_search = " WHERE a.product_idx = b.idx AND a.member_idx = c.mb_no AND a.product_idx = '".$_GET['idx']."' AND a.invest_state = 'Y' ";
$sql_order  = " ORDER BY a.idx DESC";

$sql = "
	SELECT
		a.*,
		b.withhold_tax_rate, b.invest_return, b.invest_period, b.invest_usefee,
		c.mb_id, c.mb_name, c.bank_name, c.account_num, c.bank_private_name, c.member_type
	FROM
		cf_event_product_invest a,
		cf_event_product b,
		g5_member c
	{$sql_search}
	{$sql_order}";
$result = sql_query($sql);

$sql = "
	SELECT
		COUNT(distinct a.product_idx) AS cnt,
		b.*,
		SUM(a.amount) AS amount,
		SUM(a.amount * (b.invest_return / 100)) AS invest_interest
	FROM
		cf_event_product_invest a,
		cf_event_product b,
		g5_member c
	{$sql_search}";
$product = sql_fetch($sql);


$today = date('Y-m-d');
$time  = time();

$ymd = $today;

$sql = "SELECT * FROM cf_event_product_success WHERE product_idx='".$product['idx']."' ORDER BY turn DESC LIMIT 1";
$success = sql_fetch($sql);
$rows = array();
$loan = array();

while( $row = sql_fetch_array($result) ) {
	$row['account_num'] = masterDecrypt($row['account_num'], false);

	$JUM[$row['member_idx']] = getJumin($row['member_idx']);		// 주민등록번호 추출
	$rows[] = $row;
}

$state = '';
$date = date('Y-m-d H:i:s');

if ($product['open_datetime'] > $date) {
	$state = '투자대기중';
}

if ($product['start_datetime'] < $date && $product['end_datetime'] > $date && $product['invest_end_date'] == '') {
	$state = '투자모집중';
}

if ($product['end_datetime'] < $date && $product['invest_end_date'] == '') {
	$state = '투자금 모집실패';
	$state_code = '3';
}

if ($product['invest_end_date'] != '' && $product['state'] == '') {
	$state = '대기중';
	$state_code = '1';
}

if ($product['state'] == '1') {
	$state = '수익금 상환중';
	$state_code = '2';
}

if ($product['state'] == '2') {
	$state = '상품마감';
}

if ($product['state'] == '4') {
	$state = '부실';
}

if ($product['state'] == '5') {
	$state = '중도일시상환';
	$state_code = '2';
}

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename=정산".$now_date."(".$product['title'].").xls" );
header( "Content-description: PHP4 Generated Data" );

$sql = "SELECT * FROM cf_event_product_success WHERE product_idx = '".$product['idx']."' AND date = '".$ymd."'";
$success = sql_fetch($sql);
?>
<table border="1">
	<tr align="center" bgcolor="#F6F6F6">
		<td class="text-center">NO</td>
		<td class="text-center">ID</td>
		<td class="text-center">이름</td>
		<? if($_SESSION['ss_accounting_admin']) { ?><td class="text-center">주민번호</td><? } // 주민번호열람 <경영지원팀(고상희차장) 허용> ?>
		<td class="text-center">투자금</td>
		<td class="text-center">수익금</td>
		<td class="text-center">수익률</td>
		<td class="text-center">원천징수액</td>
		<td class="text-center">플랫폼이용료</td>
		<td class="text-center">지급액</td>
		<td class="text-center">지급은행</td>
		<td class="text-center">계좌번호</td>
		<td class="text-center">예금주</td>
		<td class="text-center">지급여부</td>
	</tr>

<?
for ($i=0; $i<count($rows); $i++) {
	$row = $rows[$i];

	$sql = "SELECT COUNT(idx) AS cnt FROM cf_event_product_give WHERE product_idx = '".$row['product_idx']."' AND invest_idx = '".$row['idx']."' AND date = '".$product['repay_day']."'";
	$row['give'] = sql_fetch($sql);

	$list_num = count($rows) - $i;

	$invest_usefee =	floor( $row['invest_profit'] * ($row['invest_usefee'] / 100) );  //참여자 플랫폼 이용요율
	$withhold = floor( $row['invest_profit'] * ($row['withhold_tax_rate'] / 100) );  //원천징수
	if ($row['member_type'] == '4') $withhold = 0;

	$total_return_amount = $product['total_return_amount'] - $invest_usefee - $withhold;

?>
	<tr>
		<td align="center"><?=$list_num?></td>
		<td align="center"><?=$row['mb_id']?></td>
		<td align="center"><?=$row['mb_name']?></td>
		<? if($_SESSION['ss_accounting_admin']) { ?><td align="center"><?=$JUM[$row['member_idx']]?></td><? } ?>
		<td align="right"><?=number_format($row['amount'])?></span></td>
		<td align="right"><?=number_format($product['invest_profit'])?></span></td>
		<td align="right"><?=number_format($product['invest_return'])?>%</span></td>
		<td align="right"><?=number_format($withhold)?></span></td>
		<td align="right"><?=number_format($invest_usefee)?></span></td>
		<td align="right"><?=number_format($total_return_amount)?></span></td>
		<td align="center"><?=$row['bank_name']?></td>
		<td align="center" style="mso-number-format:'@';"><?=preg_replace("/-/", "", $row['account_num'])?></td>
		<td align="center"><?=$row['bank_private_name']?></td>
		<td align="center"><?=($row['give']['cnt'] > 0) ? '지급' : '미지급'; ?></td>
	</tr>
<?
}
?>
</table>