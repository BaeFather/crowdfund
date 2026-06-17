<?
include_once('./_common.php');

while(list($key, $value) = each($_REQUEST)) { ${$key} = trim($value); }

$idx = $_POST['opt'];

$sql = "
	SELECT 
		A.idx, A.state, A.title, A.recruit_amount, A.loan_start_date, A.loan_end_date, A.invest_period, A.loan_interest_rate, A.overdue_rate, A.repay_acct_no,
		B.group_idx, B.product_idx, B.exec_date, B.exec_yn
	FROM
		cf_product A
	LEFT JOIN
		cf_pf_accounts_rcv B ON A.idx = B.product_idx
	WHERE
		A.category='2' AND A.mortgage_guarantees='' AND A.gr_idx='".$idx."' AND A.recruit_amount>=10000 AND A.isTest='' AND A.state IN(1,2,5,8)
	ORDER BY 
		A.idx desc
";

$res = sql_query($sql);
$rows = $res -> num_rows;
?>

<table id="PrdList" class="table tbl-list-data">
	<colgroup>
		<col width="5%"/>
		<col width="7%"/>
		<col width="5%"/>
		<col width="20%"/>
		<col width="10%"/>
		<col width="12%"/>
		<col width="7%"/>
		<col width="8%"/>
		<col width="5%"/>
		<col width="5%"/>
		<col width="10%"/>
		<col width="4%"/>
	</colgroup>
	<thead>
		<tr>
			<th>No</th>
			<th>진행상태</th>
			<th>IDX</th>
			<th>상품명</th>
			<th>대출금액</th>
			<th>투자기간</th>
			<th>투자일수</th>
			<th>자금집행일</th>
			<th>이자율</th>
			<th>투자자수</th>
			<th>가상계좌</th>
			<th>체크박스</th>
		</tr>
	</thead>

<?
	
for($i=0, $num=$rows; $i<$rows; $i++, $num--) {

	$LIST[$i] = sql_fetch_array($res);

	// 투자자수
	$INVEST_CNT = sql_fetch("
		SELECT COUNT(idx) AS cnt FROM cf_product_invest WHERE product_idx='".$LIST[$i]['idx']."' AND invest_state='Y'
	");

	// 상품상태
	$state = '';
	if($LIST[$i]['state'] == '1') {
		$state = '이자상환중';
	} else if($LIST[$i]['state'] == '2') {
		$state = '상환완료';
	} else if($LIST[$i]['state'] == '5') {
		$state = '중도상환';
	} else if($LIST[$i]['state'] == '8') {
		$state = '연체중';
	}

	// 이자율
	if($LIST[$i]['state']=='8') {
		$interest_rate = $LIST[$i]['overdue_rate']; 
	} else {
		$interest_rate = $LIST[$i]['loan_interest_rate']; 
	}

?>	
	<tr>
		<td><?=$num?></td>
		<td><?=$state?></td>
		<td value='<?=$LIST[$i]['idx']?>'><?=$LIST[$i]['idx']?></td>
		<td><?=$LIST[$i]['title']?></td>
		<td><?=number_format($LIST[$i]['recruit_amount'])?></td>
		<td><?=$LIST[$i]['loan_start_date'].' ~ '.$LIST[$i]['loan_end_date']?></td>
		<td><?=$LIST[$i]['invest_period']?>개월</td>
		<td><input type="text" name="exec_date" value="<?=$LIST[$i]['exec_date']?>" class="form-control input-sm datepicker" onchange="exdateInsert(<?=$idx?>, <?=$LIST[$i]['idx']?>, this.value)" <?=($action=='update')?'':'disabled'?> /></td>
		<td><?=$interest_rate?></td>
		<td><?=$INVEST_CNT['cnt']?>명</td>
		<td><?=$LIST[$i]['repay_acct_no']?></td>
		<td><input type="checkbox" name="exec_yn" class="checkbox-test" value="<?=($LIST[$i]['exec_yn']=='Y')?'Y':'N';?>"<?=($LIST[$i]['exec_yn']=='Y')?'checked':'';?> <?=($action=='update')?'':'disabled'?> onclick="exynInsert(<?=$idx?>, <?=$LIST[$i]['idx']?>, this.value)" /></td>
	</tr>
<?
}
?>
</table>


<script type="text/javascript">
// datepicker
$(".datepicker").datepicker({
	dateFormat:'yy-mm-dd',
	dayNamesMin:['월', '화', '수', '목', '금', '토', '일'],
	monthNamesShort:['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
	monthNames:['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월']
});

// 자금집행일
function exdateInsert(gidx, pidx, exdate) {

	$.ajax({
		type: "POST",
		url: "ajax_exec_date_ins.php",
		data: {'gidx':gidx, 'pidx':pidx, 'exdate':exdate},
		success: function(data) {
			console.log(data);
		},
		error: function(xhr, status, error) {
			alert('통신 오류 입니다. 잠시 후 다시 시도하십시오.');
		}
	});
}



// 체크박스 클릭시 	
function exynInsert(gidx, pidx, yn) {

	$.ajax({
		type: "POST",
		url: "ajax_exec_yn_chk.php",
		data: {'gidx':gidx, 'pidx':pidx, 'yn':yn},
		success: function(data) {
			console.log(data);
			location.reload(true);
            self.close();
		},
		error: function(xhr, status, error) {
			alert('통신 오류 입니다. 잠시 후 다시 시도하십시오.');
		}
	});
	
}



</script>

