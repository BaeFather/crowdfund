<?

include_once('_common.php');

while(list($k,$v)=each($_REQUEST)) { ${$k} = trim($v); }

$where = "";
$where.= " AND A.state='1' AND A.display='Y'";
if($ca) {
	if($ca!='all') {
		if($ca=="2A")      $where.= " AND A.category='2' AND mortgage_guarantees=''";			//부동산(PF,기타)
		else if($ca=="2B") $where.= " AND A.category='2' AND mortgage_guarantees='1'";		//부동산(주택담보)
		else if($ca=="3A") $where.= " AND A.category='3' AND title LIKE '%면세점 확정매출채권%'";     //헬로페이(면세점)
		else if($ca=="3B") $where.= " AND A.category='3' AND title LIKE '%소상공인 확정매출채권%'";     //헬로페이(소상공인)
		else							 $where.= " AND A.category='".$ca."'";
	}
}


$sql = "
	SELECT
		A.idx, A.category, A.title, A.loan_interest_rate, A.loan_interest_type, A.loan_advanced_count, A.invest_period, A.loan_start_date, A.loan_end_date, A.recruit_amount,
		(SELECT IFNULL(MAX(turn),0) AS max_turn FROM cf_product_success WHERE product_idx=A.idx) AS payed_count
	FROM
		cf_product A
	WHERE 1
		$where
	ORDER BY
		A.loan_end_date ASC,
		A.start_num DESC";
//print_rr($sql);
$res  = sql_query($sql);
$rows = $res->num_rows;

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

sql_close();

sleep(1);

?>


				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr style="background:#F8F8EF">
							<th class="text-center">NO</th>
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
if($list_count) {
	$num = $list_count;
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
							<td class="text-center"><?=$num?></td>
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
		$num--;
	}
}
else {
?>
						<tr>
							<td colspan="12" class="text-center">데이터가 없습니다.</td>
						</tr>
<?
}
?>
					</tbody>
				</table>
