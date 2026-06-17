<?
include_once('./_common.php');

if($member['mb_id']=='seintax') {
	header("Location: invitaion_event.php");
}


$g5['title'] = '관리자메인';
include_once('./admin.head.php');

?>

<div class="row">
	<h2 style="margin-left:10px;">정산관리</h2>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center">상품명</th>
							<th class="text-center">대출금액</th>
							<th class="text-center">이자율(연)</th>
							<th class="text-center">대출자<br>이자구분</th>
							<th class="text-center">기간</th>
							<th class="text-center">대출실행일</th>
							<th class="text-center">대출만기일</th>
							<th class="text-center">잔여일수</th>
							<th class="text-center">회차</th>
							<th class="text-center">당월 대출자<br>입금현황</th>
							<th class="text-center">당월 투자자<br>지급현황</th>
						</tr>
					</thead>
					<tbody>
<?
$sql = "
	SELECT
		A.idx, A.category, A.title, A.loan_interest_rate, A.loan_interest_type, A.loan_advanced_count, A.invest_period, A.loan_start_date, A.loan_end_date, A.recruit_amount,
		(SELECT IFNULL(MAX(turn),0) AS max_turn FROM cf_product_success WHERE product_idx=A.idx) AS payed_count
	FROM
		cf_product A
	WHERE 1
		AND A.state='1' AND A.display='Y'
	ORDER BY
		A.loan_end_date ASC,
		A.start_num DESC";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$arr = 0;
for($i=0; $i<$rows; $i++) {
	$row  = sql_fetch_array($res);
	$row2 = sql_fetch("SELECT loan_interest_state, invest_give_state FROM cf_product_success WHERE product_idx='".$row['idx']."' AND LEFT(`date`, 7)='".date('Y-m')."'");

	if($row2['loan_interest_state']=='' || $row2['invest_give_state']=='') {
		$LIST[$arr] = $row;
		$LIST[$arr]['loan_interest_state'] = $row2['loan_interest_state'];
		$LIST[$arr]['invest_give_state']   = $row2['invest_give_state'];
		$arr++;
	}
}
sql_free_result($res);

$list_count = count($LIST);


for($i=0; $i<$list_count; $i++) {

	$loan_start_date_day = substr($LIST[$i]['loan_start_date'], -2);
	$total_repay_count = ((int)$loan_start_date_day < 5) ? $LIST[$i]['invest_period'] : $LIST[$i]['invest_period'] + 1;
	$payed_count = ($LIST[$i]['payed_count']) ? $LIST[$i]['payed_count'] : 0;
	switch($LIST[$i]['loan_interest_type']) {
		case 1 : $loan_interest_type = "<font color='red'>선이자</font>"; break;
		case 2 : $loan_interest_type = "<font color='red'>부분선이자(" . $LIST[$i]['loan_advanced_count'] . "회차)</font>"; break;
		default : $loan_interest_type = "월이자"; break;
	}

	$loan_interest_state = ($LIST[$i]['loan_interest_state']=='Y') ? '입금완료' : '미입금';
	$invest_give_state = ($LIST[$i]['invest_give_state']=='Y') ? '지급완료' : '미지급';

	$finish_day_count = ceil(((strtotime($LIST[$i]['loan_end_date']) - time()) / 86400));
	if($finish_day_count <= 0) $finish_day_count = 0;
	$tr_color = ($LIST[$i]['invest_give_state']=='' && $finish_day_count <= 10) ? "#FCE9D5" : "";

?>
						<tr class="odd" style="background-color:<?=$tr_color?>">
							<td><a href="product_calculate.php?idx=<?=$LIST[$i]['idx']?>"><?=$LIST[$i]['title']?></a></td>
							<td style="text-align:right"><?=number_format($LIST[$i]['recruit_amount'])?>원</td>
							<td class="text-center"><?=$LIST[$i]['loan_interest_rate']?>%</td>
							<td class="text-center"><?=$loan_interest_type?></td>
							<td class="text-center"><?=$LIST[$i]['invest_period']?>개월</td>
							<td class="text-center"><?=$LIST[$i]['loan_start_date']?></td>
							<td class="text-center"><?=$LIST[$i]['loan_end_date']?></td>
							<td class="text-center"><?=$finish_day_count?>일</td>
							<td class="text-center"><font color="<?=($payed_count)?'':'#aaaaaa'?>"><?=$payed_count?></font> / <?=$total_repay_count?></td>
							<td class="text-center"><?=$loan_interest_state?></td>
							<td class="text-center"><?=$invest_give_state?></td>
						</tr>
<?
}
?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<h2 style="margin-left:10px;">최근 상품 등록현황</h2>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center">번호</th>
							<th class="text-center">상품명</th>
							<th class="text-center">진행상태</th>
							<th class="text-center">등록일</th>
							<th class="text-center">모집기간</th>
							<th class="text-center">투자자수</th>
							<th class="text-center">모집금액</th>
							<th class="text-center">모집율</th>
						</tr>
					</thead>
					<tbody>
<?
$sql = "
	SELECT
		A.*,
		COUNT(B.idx) AS cnt,
		IFNULL(SUM(B.amount),0) AS invest_amount
	FROM
		cf_product A
	LEFT JOIN
		cf_product_invest B  ON A.idx=B.product_idx
	WHERE 1
		AND A.display='Y' AND A.isTest=''
		AND A.state NOT IN('3','6','7')
		AND B.invest_state='Y'
	GROUP BY
		A.idx
	ORDER BY
		A.start_num DESC
	LIMIT 5";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$row = sql_fetch_array($res);

	$new_mark = (time()-strtotime($row['open_datetime']) < 86400) ? '<span class="new_mark">new</span>' : '';

	$state = '';

	if($row['state']=='') {
		if($row['open_datetime'] > G5_TIME_YMDHIS)   $state = '오픈대기중';
		if($row['open_datetime'] <= G5_TIME_YMDHIS && $row['start_datetime'] > G5_TIME_YMDHIS) $state = '투자대기중';
		if($row['start_datetime'] <= G5_TIME_YMDHIS && $row['invest_end_date'] == '')          $state = '투자모집중';
		if($row['start_datetime'] <= G5_TIME_YMDHIS && $row['invest_end_date'])                $state = '투자모집완료';
	}
	else {
		switch($row['state']) {
			case '1' : $state = '이자상환중';   break;
			case '2' : $state = '상품마감';	    break;
			case '4' : $state = '부실';         break;
			case '5' : $state = '중도일시상환'; break;
			case '6' : $state = '대출계약취소'; break;
		}
	}

	$invest_perc = sprintf("%.2f", ( $row['invest_amount'] / $row['recruit_amount'] ) * 100);

?>
						<tr class="odd">
							<td align="center"><?=$row['idx']?></td>
							<td align="left">
								<?=$new_mark?>
								<?=$row['title']?>
							</td>
							<td align="center"><?=$state?></td>
							<td align="center"><?=substr($row['insert_date'], 0, 10)?></td>
							<td align="center"><?=$row['recruit_period_start']?> ~ <?=$row['recruit_period_end']?></td>
							<td align="center"><?=$row['cnt']?></td>
							<td align="right"><?=number_format($row['invest_amount'])?> / <?=number_format($row['recruit_amount'])?></td>
							<td align="right"><?=floatRtrim($invest_perc)?>%</td>
						</tr>
<?
}
sql_free_result($res);
?>
					</tbody>
				</table>
			</div>
		</div>	<!-- /.panel-body -->
		<div style="width: 100%; text-align: center;">
			<ul class="pagination"><?=$pagination?></ul>
		</div>
	</div>	<!-- /.col-lg-12 -->
</div>	<!-- /.row -->

<div class="row">
	<h2 style="margin-left:10px;">최근 회원가입 현황</h2>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-center">번호</th>
							<th class="text-center">아이디</th>
							<th class="text-center">회원구분</th>
							<th class="text-center">투자유형구분</th>
							<th class="text-center">성명/법인명</th>
							<th class="text-center">가입일시</th>
							<th class="text-center">신디케이션</th>
							<th class="text-center">관리</th>
						</tr>
					</thead>
					<tbody>
<?
$sql = "
	SELECT
		mb_no, mb_id, mb_level, member_type, member_investor_type, is_creditor, mb_name, mb_co_name, mb_datetime, syndi_id
	FROM
		g5_member
	WHERE 1
		AND mb_level='1' AND member_group='F'
		AND LEFT(mb_datetime,10)>= NOW()-INTERVAL 2 DAY
	ORDER BY
		mb_datetime DESC";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$member_type   = array('1' => '개인회원', '2' => '법인회원', '3' => 'SNS회원');
$investor_type = array('1' => '일반투자자', '2' => '소득적격투자자', '3' => '전문투자자');
$syndi         = array('hktvwowstar' => '와우스타(한경TV)	', 'finnq' => '핀크', 'chosun' => '땅집고(조선일보)', 'TvTalk' => '티비톡');

for($i=0,$num=$rows; $i<$rows; $i++,$num--) {
	$row = sql_fetch_array($res);

	$new_mark = (time()-strtotime($row['mb_datetime']) < 86400) ? '<span class="new_mark">new</span>' : '';

	$print_name = ($row['member_type']=='2') ? $row['mb_co_name'] : $row['mb_name'];
?>
						<tr class="odd">
							<td align="center"><?=$num?></td>
							<td>
								<?=$new_mark?>
								<?=$row['mb_id']?>
							</td>
							<td align="center"><?=$member_type[$row['member_type']]?></td>
							<td align="center"><?=$investor_type[$row['member_investor_type']]?></td>
							<td align="center"><?=$print_name?></td>
							<td align="center"><?=substr($row['mb_datetime'], 0, 16)?></td>
							<td align="center"><?=$syndi[$row['syndi_id']]?></td>
							<td align="center"><a href="./member/member_view.php?mb_id=<?=$row['mb_id']?>" class="btn btn-sm btn-default">상세보기</a></td>
						</tr>
<?
}
sql_free_result($res);
?>
					</tbody>
				</table>
			</div>
		</div>	<!-- /.panel-body -->
		<div style="width: 100%; text-align: center;">
			<ul class="pagination">
				<?=$pagination; ?>
			</ul>
		</div>
	</div>	<!-- /.col-lg-12 -->
</div>	<!-- /.row -->


<div class="row" id="virtual_account">
	<h2 style="margin-left:10px;">가상계좌 현황</h2>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<colgroup>
						<col style="width:33.33%">
						<col style="width:33.33%">
						<col style="width:33.33%">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">발급은행</th>
							<th class="text-center">전체보유분</th>
							<th class="text-center">미사용분</th>
						</tr>
					</thead>
					<tbody>
<?
	$row  = sql_fetch("SELECT COUNT(org_cd) AS cnt FROM IB_vact WHERE acct_no!=''");
	$row2 = sql_fetch("SELECT COUNT(org_cd) AS cnt FROM IB_vact WHERE acct_no!='' AND acct_st='0'");
	$fcolor = ($row2['cnt'] <= 100) ? '#FF2222' : '';
	if($row2['cnt'] <= 100) $arlim_start = true;
?>
						<tr class="odd">
							<td align="center" style="color:<?=$fcolor?>"><?=$BANK['088']?></td>
							<td align="center" style="color:<?=$fcolor?>"><?=number_format($row['cnt'])?></td>
							<td align="center" style="color:<?=$fcolor?>"><?=number_format($row2['cnt'])?></td>
						</tr>
<?
	$VBANK_KEYS = array_keys($VBANK);
	for($i=0; $i<count($VBANK); $i++) {
		$row  = sql_fetch("SELECT COUNT(org_cd) AS cnt FROM vacs_vact WHERE bank_cd='".$VBANK_KEYS[$i]."' AND acct_no!=''");
		$row2 = sql_fetch("SELECT COUNT(org_cd) AS cnt FROM vacs_vact WHERE bank_cd='".$VBANK_KEYS[$i]."' AND acct_no!='' AND acct_st='0'");
		$fcolor = '#ccc';
?>
						<tr class="odd">
							<td align="center" style="color:<?=$fcolor?>"><?=$VBANK[$VBANK_KEYS[$i]]?></td>
							<td align="center" style="color:<?=$fcolor?>"><?=number_format($row['cnt'])?></td>
							<td align="center" style="color:<?=$fcolor?>"><?=number_format($row2['cnt'])?></td>
						</tr>
<?
	}
?>
					</tbody>
				</table>
			</div>
		</div>	<!-- /.panel-body -->
	</div>	<!-- /.col-lg-12 -->
</div>	<!-- /.row -->


<div class="row" id="loan_request">
	<h2 style="margin-left:10px;">담보대출 신청현황</h2>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th scope="col" style="text-align:center;width:60px">NO.</th>
							<th scope="col" style="text-align:center;">구분</th>
							<th scope="col" style="text-align:center;">성명.법인명</th>
							<th scope="col" style="text-align:center;">연락처</th>
							<th scope="col" style="text-align:center;">이메일</th>
							<th scope="col" style="text-align:center;">물건소재지</th>
							<th scope="col" style="text-align:center;">희망대출금</th>
							<th scope="col" style="text-align:center;">대출기간</th>
							<th scope="col" style="text-align:center;">대출목적</th>
							<th scope="col" style="text-align:center;">등록일시</th>
							<th scope="col" style="text-align:center;">내용</th>
						</tr>
					</thead>
					<tbody>
<?
$TYPE     = array('1'=>'아파트담보대출신청', '2'=>'취급법인유동화신청');
$RELATION = array('1'=>'본인', '2'=>'가족', '3'=>'중개인');
$PERIOD   = array('6'=>'6개월', '9'=>'9개월', '12'=>'12개월', '12+'=>'12개월 초과');
$PURPOSE  = array('1'=>'기대출상환', '2'=>'기대출상환 및 추가대출', '3'=>'선순위대출', '4'=>'사업자금', '5'=>'전세퇴거자금', '6'=>'기타');

$sql = "
	SELECT
		*
	FROM
		cf_apat_loan_request
	WHERE 1=1
		AND blind=''
	ORDER BY
		idx DESC
	LIMIT 5";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

for($i=0,$num=$rows; $i<$rows; $i++,$num--) {
	$row = sql_fetch_array($res);

	$new_mark = (time()-strtotime($row['regdate']) < 86400) ? '<span class="new_mark">new</span>' : '';

	$print_type = $TYPE[$row['type']];
	$print_hp  = masterDecrypt($row['hp'], false);
	$print_hp  = substr($print_hp, 0, strlen($print_hp)-4) . "****";

	$print_loc = $print_wamt = $print_purpose = $print_period = $print_wtime = '';

	if($row['type']=='1') {
		$print_name    = $row['name'];
		$print_loc     = $row['loc'];
		$print_wamt    = price_cutting($row['wamt']).'원';
		$print_purpose = $PURPOSE[$row['purpose']];
		$print_period  = $PERIOD[$row['period']];
		$print_wtime   = $row['wtime'];
	}
	else {
		$print_name = $row['co_name'];
	}

?>
						<tr>
							<td align="center"><?=$num?></td>
							<td>
								<?=$new_mark?>
								<?=$print_type?>
							</td>
							<td align="center"><?=$print_name?></td>
							<td align="center"><?=$print_hp?></td>
							<td align="center"><?=$row['email']?></td>
							<td align="center"><?=$print_loc?></td>
							<td align="center"><?=$print_wamt?></td>
							<td align="center"><?=$print_period?></td>
							<td align="center"><?=$print_purpose?></td>
							<td align="center"><?=substr($row['regdate'],0,16)?></td>
							<td align="center"><button type="button" onClick="location.href='/adm/loan_request/request.php?idx=<?=$row['idx']?>'" class="btn btn-sm btn-default">상세보기</button></td>
						</tr>
<?
}
sql_free_result($res);
?>

					</tbody>
				</table>
			</div>
		</div>	<!-- /.panel-body -->
	</div>	<!-- /.col-lg-12 -->
</div>	<!-- /.row -->



<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

<? if($arlim_start) { ?>
$(document).ready(function() {
	alert('가상계좌 여유분이 부족합니다. 추가 발급 요청하십시요.');
	location.href = "#virtual_account";
});
<? } ?>
</script>

<?
include_once ('./admin.tail.php');
?>
