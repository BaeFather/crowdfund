<?
include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "920100";
$g5['title'] = $menu['menu920'][1][1];

include_once ('../../admin.head.php');

$where = ' AND A.group_idx = A.product_idx';

// 진행현황 조건
$STATE = $_REQUEST['srch_state'];
$state_cnt = count($STATE);
$state_str = '';
if($srch_state) {
	$where.= " AND B.state IN(";
	$state_str.="&";
	
	for($i=0,$j=1; $i<$state_cnt; $i++,$j++) {
		$where.= stripslashes("'".$STATE[$i]."'");
		$where.= ($j < $state_cnt) ? ",":"";

		$state_str.= "STATE[]={$STATE[$i]}";
		$state_str.= ($j < $state_cnt) ? "&" : "";
	}
	$where.= ")";
} 

// 필드선택 조건
if($field) {
	if($field=='B.title') {
		$where.= " AND B.title LIKE '%".$keyword."%'";
	} else if($field=='mb_title') {
		$where.= " AND (C.mb_name LIKE '%".$keyword."%' OR C.mb_co_name LIKE '%".$keyword."%')";
	}
}

// COUNT
$sql = "
	SELECT 
		COUNT(A.idx) AS cnt
	FROM
		cf_pf_accounts_rcv A
	LEFT JOIN
		cf_product B ON A.product_idx = B.idx 
	LEFT JOIN
		g5_member C ON B.loan_mb_no = C.mb_no
	WHERE 1
		$where
";
$row = sql_fetch($sql);
$total_count = $row['cnt'];  // 조회한 쿼리 결과 count
$rows = 10;  // row 개수
$total_page = ceil($total_count / $rows);  // 전체 페이지 계산
if($page < 1) $page = 1;  // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows;  // 시작 열을 구함

// SELECT
$sql = "
	SELECT 
		A.idx, A.group_idx, A.contract_amount, A.loan_amount, A.yet_amount, A.period, A.note, A.loan_end_date,
		B.title, B.state, B.loan_interest_rate, B.overdue_rate, B.loan_start_date,
		C.mb_no, C.member_type, C.mb_name, C.mb_co_name
	FROM
		cf_pf_accounts_rcv A
	LEFT JOIN
		cf_product B ON A.product_idx = B.idx 
	LEFT JOIN
		g5_member C ON B.loan_mb_no = C.mb_no
	WHERE 1
		$where
	ORDER BY
		A.idx DESC
	LIMIT
		$from_record, $rows
";
$result = sql_query($sql);
$rcount = $result->num_rows;


$num = $total_count - $from_record;
$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);  // 페이징

add_stylesheet('<link rel="stylesheet" href="css/pf_rcv.css" />', 0);

?>


<div class="tbl_head02 tbl_wrap">
	<form id="frmPfRcvSearch" name="frmPfRcvSearch" method="get" class="form-horizontal">
		<div class="srch-option">
			<p class="srch-option-title">진행현황 : </p>
			<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="1" <?=(@in_array('1', $STATE))?'checked':''?> /> 이자상환중</label>
			<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="2" <?=(@in_array('2', $STATE))?'checked':''?> /> 상환완료</label>
			<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="5" <?=(@in_array('5', $STATE))?'checked':''?> /> 중도상환</label>
			<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="8" <?=(@in_array('8', $STATE))?'checked':''?> /> 연체중</label>
			<ul class="col col-md-* list-inline">
				<li>
					<select name="field" class="form-control input-sm">
						<option value="">필드 선택</option>
						<option value="B.title" <?=($field=='B.title')?'selected':'';?>>상품명</option>
						<option value="mb_title" <?=($field=='mb_title')?'selected':'';?>>차주명</option>
					</select>
				</li>
				<li><input type="text" class="form-control input-sm" name="keyword" size="30" value="<?=$keyword?>" onkeypress="JavaScript:press(this.form);"></li>
				<li><button type="button" id="search_button" class="btn btn-sm btn-warning">검색</button></li>
				<li style="float: right;"><button type="button" class="btn btn-sm btn-primary" onClick="document.location.href='./pf_rcv_form.php';">상품등록</button></li>
			</ul>
		</div>
		<table class="table srch-list">
			<thead>
				<tr>
					<th>NO</th>
					<th>상품명</th>
					<th>차주명</th>
					<th>상품상태</th>
					<th>약정금액</th>
					<th>대출금액</th>
					<th>미집행금액</th>
					<th>정상이자</th>
					<th>연체이자</th>
					<th>약정기간</th>
					<th>대출기간</th>
					<th>비고</th>
				</tr>
			</thead>
			<tbody>
				<?
				for($i=0; $i<$rcount; $i++) {
					$LIST[$i] = sql_fetch_array($result);

					
					if($LIST[$i]) {
						// 총 대출금액 - 체크 항목 값 = 대출금액
						$asql = "
							SELECT
								B.group_idx, B.contract_amount,
								(SELECT SUM(recruit_amount) AS total_amount FROM cf_product WHERE gr_idx='".$LIST[$i]['group_idx']."' AND recruit_amount > 10000) AS tot_amt,
								SUM(A.recruit_amount) AS chk_amount
							FROM 
								cf_product A 
							LEFT JOIN 
								cf_pf_accounts_rcv B ON A.idx = B.product_idx
							WHERE 
								B.group_idx='".$LIST[$i]['group_idx']."' AND B.exec_yn='Y'
						";
						//echo $asql.'<br>';
						
						$ares = sql_query($asql);
						$arows = $ares->num_rows;

						for($j=0; $j<=$arows; $j++) {
							$AMT[$j] = sql_fetch_array($ares);

							$loan_amount = ($AMT[$j]['tot_amt']-$AMT[$j]['chk_amount']);
							//echo '대출금액 : '.$loan_amount.'<br>';

							$yet_amount = ($LIST[$i]['contract_amount']-$loan_amount);
							//echo '미집행금액 : '.$yet_amount.'<br>';

							break;
						}
						
					}


					// 개인, 법인 회원 구분
					$loan_name = '';
					if($LIST[$i]['member_type'] == '1') {
						$loan_name = $LIST[$i]['mb_name'];
					} else if($LIST[$i]['member_type'] == '2') {
						$loan_name = $LIST[$i]['mb_co_name'];
					}

					// 상품상태
					$state = '';
					if($LIST[$i]['state'] == '1') {
						$state = '이자상환중';
					} else if($LIST[$i]['state'] == '2') {
						$state = '상환완료';
					} else if($LIST[$i]['state'] == '5') {
						$state = '중도상환';
					} else if($LIST[$i]['state'] == '8') {
						$state = '연체';
					}

					
				?>

				<tr>
					<td><?=$num--?></td>
					<td><a href="./pf_rcv_form.php?idx=<?=$LIST[$i]['idx']?>"><?=(!$LIST[$i]['title']) ? '선택된 상품이 없습니다.':$LIST[$i]['title'] ?></a></td>
					<td><?=$loan_name?></td>
					<td><?=$state?></td>
					<td class="txt-right"><?=number_format($LIST[$i]['contract_amount'])?></td>
					<td class="txt-right"><?=number_format($loan_amount)?></td>
					<td class="txt-right"><?=number_format($yet_amount)?></td>
					<td><?=$LIST[$i]['loan_interest_rate']?>%</td>
					<td><?=$LIST[$i]['overdue_rate']?>%</td>
					<td><?=$LIST[$i]['period']?>개월</td>
					<td><?=$LIST[$i]['loan_start_date'].'~'.$LIST[$i]['loan_end_date']?></td>
					<td><?=$LIST[$i]['note']?></td>
				</tr>
				<? 
					
				} 
				?>
			</tbody>
		</table>
	</form>
</div>

<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>


 
<script type="text/javascript">
// 페이징 처리
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

function press(f) {
	console.log(event.keyCode);
	if(event.keyCode == 13){    // 13이 enter키를 의미함
		$("#search_button").click();
	}
}

$('#search_button').click(function() {
	var f = document.frmPfRcvSearch;

	f.method = 'get';
	f.target = '_self';
	f.action = '<?=$_SERVER['PHP_SELF']?>';
	f.submit();
});
</script>

<? include_once ('../../admin.tail.php'); ?>