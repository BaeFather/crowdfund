<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = "700300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$prd_idx = trim($_REQUEST['idx']);  //상품번호기준

$sql = "
	SELECT
		COUNT(distinct A.product_idx) AS cnt,
		B.idx, B.state, B.category, B.title, B.invest_amount, B.invest_profit, B.invest_return, B.invest_usefee, B.invest_period, B.invest_end_date, B.total_return_amount, B.withhold_tax_rate,
		B.recruit_period_start, B.recruit_period_end, B.recruit_amount, B.repay_type, B.repay_day, B.start_date, B.end_date, B.display,
		SUM(A.amount) AS amount,
		SUM(A.amount * (B.invest_return / 100)) AS invest_interest
	FROM
		cf_event_product_invest A
	LEFT JOIN
		cf_event_product B  ON A.product_idx=B.idx
	LEFT JOIN
		g5_member C  ON A.member_idx=C.mb_no
	WHERE (1)
		AND A.product_idx='".$prd_idx."'
		AND A.invest_state='Y'";
$PRDT = sql_fetch($sql);
//print_rr($PRDT, "font-size:8pt");

$today = date('Y-m-d');
$time  = time();
$ymd   = $today;

$sql = "
	SELECT
		A.*,
		B.withhold_tax_rate, B.invest_return, B.invest_period, B.invest_usefee,
		C.mb_no, C.mb_id, C.mb_name, C.bank_name, C.mb_co_name, C.account_num, C.bank_private_name, C.member_type
	FROM
		cf_event_product_invest A
	LEFT JOIN
		cf_event_product B  ON A.product_idx=B.idx
	LEFT JOIN
		g5_member C  ON A.member_idx=C.mb_no
	WHERE (1)
		AND A.product_idx='".$PRDT['idx']."'
		AND A.invest_state='Y'
	ORDER BY
		A.idx DESC";
$result = sql_query($sql);

$sql = "SELECT * FROM cf_event_product_success WHERE product_idx='".$PRDT['idx']."' ORDER BY turn DESC LIMIT 1";
$success = sql_fetch($sql);
$rows = array();
$loan = array();

while( $row = sql_fetch_array($result) ) {
	$row['account_num'] = masterDecrypt($row['account_num'], false);

	$JUM[$row['member_idx']] = getJumin($row['member_idx']);		// 주민등록번호 추출
	$LIST[] = $row;
}

$state = '';
$date = date('Y-m-d H:i:s');

if ($PRDT['open_datetime'] > $date) {
	$state = '투자대기중';
}

if ($PRDT['start_datetime'] < $date && $PRDT['end_datetime'] > $date && $PRDT['invest_end_date'] == '') {
	$state = '투자모집중';
}

if ($PRDT['end_datetime'] < $date && $PRDT['invest_end_date'] == '') {
	$state = '투자금 모집실패';
	$state_code = '3';
}

if ($PRDT['invest_end_date'] != '' && $PRDT['state'] == '') {
	$state = '대기중';
	$state_code = '1';
}

if ($PRDT['state'] == '1') {
	$state = '수익금 상환중';
	$state_code = '2';
}

if ($PRDT['state'] == '2') {
	$state = '상품마감';
}

if ($PRDT['state'] == '4') {
	$state = '부실';
}

if ($PRDT['state'] == '5') {
	$state = '중도일시상환';
	$state_code = '2';
}


$g5['title'] = '이벤트 상품 정산상세 - '.$state;
include_once('./admin.head.php');
?>

<div class="row">
	<div class="col-lg-12">
		<form method="post" action="./event_register_process.php" class="form-horizontal">
		<input type="hidden" name="action" value="calculate_state_update">
		<input type="hidden" name="idx" value="<?=$PRDT['idx']?>">
		<input type="hidden" name="state" value="<?=$state_code?>">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center" style="width:6%">번호</th>
							<th class="text-center">상품명</th>
							<th class="text-center" style="width:6%">현재회차</th>
							<th class="text-center" style="width:6%">총회차</th>
							<th class="text-center" style="width:15%">기간</th>
							<th class="text-center" style="width:15%"></th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd">
							<td align="center"><?=$PRDT['idx']?></td>
							<td align="center"><?=$PRDT['title']?></td>
							<td align="center"><?=number_format($success['turn'])?></td>
							<td align="center"><?=number_format($loan['plus_turn'])?></td>
							<td align="center"><?=$PRDT['recruit_period_start'].' ~ '.$PRDT['recruit_period_end']?></td>
							<td align="center">
								<ul class="list-inline">
									<? if ($PRDT['invest_end_date'] && $PRDT['state'] == '') { ?>
									<li><input type="text" name="date" value="" class="form-control datepicker" required></li>
									<li><button type="submit" class="btn btn-success">마감처리시작</button></li>
									<? } ?>
									<? if (in_array($PRDT['state'], array('1', '5'))) { ?>
									<li><button type="submit" class="btn btn-success" onclick="if (!confirm('원금납입완료 처리 하시겠습니까?')) return false;">원금납입완료</button></li>
									<? } ?>
									<? if ($PRDT['end_datetime'] < $date && $PRDT['invest_end_date'] == '' && $PRDT['state'] == '') { ?>
									<li><button type="submit" class="btn btn-primary" onclick="">예치금반환</button></li>
									<? } ?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		</form>
	</div>

<?
$sql = "SELECT * FROM cf_event_product_success WHERE product_idx = '".$PRDT['idx']."' AND date = '".$ymd."'";
$success = sql_fetch($sql);
?>
	<div class="col-lg-12">
		<div class="panel-body" style="padding-bottom: 0;">
			지급<?=($PRDT['repay_day'] > $today)?'예정':''?>일: <?=$PRDT['repay_day']?>
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="font-size:12px; margin-bottom:0;">
					<thead>
						<tr>
							<th class="text-center" colspan="<?=($_SESSION['ss_accounting_admin'])?'12':'11'?>">투자자</th>
							<th class="text-center">지급여부</th>
						</tr>
						<tr>
							<td class="text-center">NO</td>
							<td class="text-center">회원번호</td>
							<td class="text-center">ID</td>
							<td class="text-center">이름</td>
							<? if($_SESSION['ss_accounting_admin']) { ?><td class="text-center">주민번호</td><? } ?>
							<td class="text-center">투자금</td>
							<td class="text-center">수익금</td>
							<td class="text-center">수익률</td>
							<td class="text-center">지급액</td>
							<td class="text-center">지급은행</td>
							<td class="text-center">계좌번호</td>
							<td class="text-center">예금주</td>
							<td class="text-center">
								<? if (in_array($PRDT['state'], array('1', '5')) && $success['loan_interest_state'] == 'Y' && $success['invest_give_state'] == '') { ?>
								  <button type="submit" class="btn btn-primary" onclick="allGiveState('<?=$ymd?>');">전체지급</button>
								<? } ?>
							</td>
						</tr>
					</thead>
					<tbody>
<?
$list_count = count($LIST);
for ($i=0,$num=$list_count; $i<$list_count; $i++,$num--) {
	$GIVE = sql_fetch("SELECT idx, invest_amount, bank_name, bank_private_name, account_num, banking_date FROM cf_event_product_give WHERE product_idx='".$LIST[$i]['product_idx']."' AND invest_idx='".$LIST[$i]['idx']."'");

	$interest = floor($LIST[$i]['amount'] * ($LIST[$i]['invest_return'] / 100) / 365 * $total_days);
	$charge   = floor($LIST[$i]['amount'] * ($LIST[$i]['invest_usefee'] / 100));
	$withhold = floor(($interest - $charge) * ($LIST[$i]['withhold_tax_rate'] / 100));
	if ($LIST[$i]['member_type']=='4') $withhold = 0;

	$bgcolor= ($PRDT['state']=='2' && $GIVE['idx']=='') ? '#FFDDDD' : '';

?>
						<tr style="background:<?=$bgcolor?>">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$LIST[$i]['member_idx']?></td>
							<td align="center"><a href="member/member_view.php?&mb_id=<?=$LIST[$i]['mb_id']?>"><?=$LIST[$i]['mb_id']?></a></td>
							<td align="center"><?=$LIST[$i]['mb_name']?></td>
							<? if($_SESSION['ss_accounting_admin']) { ?><td align="center"><?=$JUM[$LIST[$i]['member_idx']]?></td><? } ?>
							<td align="right"><?=number_format($LIST[$i]['amount'])?></span></td>
							<td align="right"><?=number_format($PRDT['invest_profit'])?></span></td>
							<td align="right"><?=sprintf("%.2f", $PRDT['invest_return'])?>%</span></td>
							<td align="right"><?=number_format($PRDT['total_return_amount'])?></span></td>
							<td align="center"><?=($GIVE['bank_name'])?$GIVE['bank_name']:$LIST[$i]['bank_name']?></td>
							<td align="center"><?=preg_replace("/-/", "", $LIST[$i]['account_num'])?></td>
							<td align="center"><?=$LIST[$i]['bank_private_name']?></td>
							<td align="center">
								<? if($PRDT['state']=='2') { echo ($GIVE['idx']) ? '지급<br>'.substr($GIVE['banking_date'],0,10) : '<span style="color:red">미지급</span>'; } ?>

								<? if($PRDT['state']=='1' && $success['loan_interest_state']=='Y' && $success['invest_give_state']=='') { ?>
								<select name="give_state" class="form-control" data-invest_idx="<?=$LIST[$i]['idx']?>" data-product_idx="<?=$PRDT['idx']?>" data-date="<?=$ymd?>" data-invest_amount="<?=$PRDT['total_return_amount']?>">
									<option value="N">미지급</option>
									<option value="Y" <?=($GIVE['idx'])?'selected':'';?>>지급</option>
								</select>
								<? } ?>
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

		<div class="panel-body pull-right">
			<a href="./event_product_calculate_excel.php?idx=<?=$PRDT['idx']?>" target="_blank" class="btn btn-primary">엑셀다운</a>
			<?	if (in_array($PRDT['state'], array('1', '5'))) {	?>
			<?		if ($success['loan_interest_state'] == '') {	?>
			<button type="submit" class="btn btn-danger" onclick="loanInterestSuccess('<?=$PRDT['idx']?>', '<?=$ymd?>', '<?=$j+1?>');">대출이자 수급완료</button>
			<?		} ?>
			<?		if ($success['invest_give_state'] == '') { ?>
			<button type="submit" class="btn btn-danger" onclick="investGiveSuccess('<?=$PRDT['idx']?>', '<?=$ymd?>');">투자지급완료</button>
			<?		} ?>
			<?	}	?>
		</div>
	</div>

</div>
<!-- /.row -->

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});

	$("select[name=give_state]").change(function() {
		$this = $(this);
		$.post('./event_register_process.php', {
			action: 'loan_interest_give',
			invest_idx: $this.data('invest_idx'),
			product_idx: $this.data('product_idx'),
			date: $this.data('date'),
			invest_amount:
			$this.data('invest_amount'),
			give_state: $this.val()
		});
	});
});

function allGiveState(date) {
	if(confirm("전체 지급 처리 하시겠습니까?")) {
		$("select[name=give_state][data-date="+date+"]").val('Y').trigger('change');
	}
}

function loanInterestSuccess(idx, date, turn) {
	if(confirm("'대출이자 수급완료' 처리를 시작 하시겠습니까?")) {
		$.post('./event_register_process.php', { action: 'loan_interest_success', idx: idx, date: date, turn: turn }, function() {
			alert('정상적으로 처리되었습니다.');
			location.reload();
		});
	}
	else {
		return false;
	}
}

function investGiveSuccess(idx, date) {
	if(confirm("'투자지급완료' 처리를 시작 하시겠습니까?")) {
		$.post('./event_register_process.php', { action: 'invest_give_success', idx: idx, date: date }, function() {
			alert('정상적으로 처리되었습니다.');
			location.reload();
		});
	}
	else {
		return false;
	}
}
</script>

<?
include_once ('./admin.tail.php');
?>