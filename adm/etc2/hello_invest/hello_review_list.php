<?

include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "950100";
$g5['title'] = $menu['menu950'][2][1];

include_once ('../../admin.head.php');

$where = '';

if($sdate || $edate) {
	if($sdate) $where.= " AND reg_date >= '$sdate'";
	if($edate) $where.= " AND reg_date <= '$edate'";
}

if($category) {
	if(in_array($category, array('1','2','3','4'))) {
		$where.= " AND h2_type='".$category."'";
	}
}

if($prd_name) { $where.= " AND h2_title LIKE '%".$prd_name."%'"; }
if($loan_name) { $where.= " AND h2_loan_mb_name LIKE '%".$loan_name."%'"; }

$sql = "
	SELECT
		idx, h2_type, h2_title, h2_loan_mb_name, h2_ltv, h2_recruit_amount, 
		h2_request_price, h2_hello_perc, resYN, reg_date, mod_date
	FROM 
		hello_self_review
	WHERE (1)
		$where
	ORDER BY
		idx desc
";

$result = sql_query($sql);
$count = sql_num_rows($result);
$num = $count;


add_stylesheet('<link rel="stylesheet" href="css/hello_invest.css" />', 0);

?>
<div id="helloReviewWrap" class="tbl_head02 tbl_wrap">
	<div class="scrh-wrap">
		<form name="helloReview" id="helloReview" method="get" class="form-horizontal">
			<table class="table srch-table">
				<tbody>
					<tr>
						<th>작성일</th>
						<td>
							<ul class="col col-md-* list-inline">
								<li class="date"><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="작성일자(시작)"></li>
								<li>~</li>
								<li class="date"><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="작성일자(종료)"></li>
							</ul>
						</td>
						<th>카테고리</th>
						<td>
							<select name="category" class="form-control input-sm">
								<option value="">카테고리 선택</option>
								<option value="1" <?if($category=='1') echo"selected";?>>주택담보대출</option>
								<option value="2" <?if($category=='2') echo"selected";?>>매출채권</option>
								<option value="3" <?if($category=='3') echo"selected";?>>PF</option>
								<option value="4" <?if($category=='4') echo"selected";?>>동산</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>상품명</th>
						<td><input type="text" name="prd_name" class="form-control input-sm" /></td>
						<th>차입자</th>
						<td><input type="text" name="loan_name" class="form-control input-sm" /></td>
					</tr>
				</tbody>
			</table>
		</form>
		<div class="srch-btn">
			<input type="button" id="listSearch" class="btn btn-sm btn-warning" value="검색" />
			<button type="button" class="btn btn-sm btn-default" onClick="location.href='<?=$_SERVER['PHP_SELF']?>';">초기화</button>
		</div>
	</div>
	
	<div class="list-wrap">
		<input type="button" class="btn btn-sm btn-primary write-btn" value="검토서 작성" onclick="location.href='./hello_review_form.php'"/>
		<table class="srch-list">
			<thead>
				<tr>
					<th>No</th>
					<th>카테고리</th>
					<th>상품명</th>
					<th>차입자</th>
					<th>LTV</th>
					<th>모집금액</th>
					<th>투자요청금액</th>
					<th>투자비율</th>
					<th>검토결과</th>
					<th>작성일</th>
					<th>수정일</th>
					<th>비고</th>
				</tr>
			</thead>
			<tbody>
				<? for($i=0; $i<$count; $i++) { 
					$R[$i] = sql_fetch_array($result);

					if($R[$i]['h2_type'] == '1') {
						$category = "주택담보대출";
					} else if($R[$i]['h2_type'] == '2') {
						$category = "매출채권";
					} else if($R[$i]['h2_type'] == '3') {
						$category = "PF";
					} else if($R[$i]['h2_type'] == '4') {
						$category = "동산";
					} else {
						$category = "";
					}

					if($R[$i]['resYN'] == 'Y') {
						$resYn = "적정";
					} else {
						$resYn = "부적정";
					}

				?>
				<tr>
					<td><?=$num--;?></td>
					<td><?=$category?></td>
					<td><?=$R[$i]['h2_title']?></td>
					<td><?=$R[$i]['h2_loan_mb_name']?></td>
					<td><?=$R[$i]['h2_ltv']?>%</td>
					<td><?=$R[$i]['h2_recruit_amount']?></td>
					<td><?=number_format($R[$i]['h2_request_price'])?></td>
					<td><?=$R[$i]['h2_hello_perc']?>%</td>
					<td><?=$resYn?></td>
					<td><?=$R[$i]['reg_date']?></td>
					<td><?=$R[$i]['mod_date']?></td>
					<td>
						<input type="button" value="수정" class="mod-btn" onclick="location.href='./hello_review_form.php?idx=<?=$R[$i]['idx']?>&mode=modify'" />
						<input type="button" value="삭제" class="del-btn" id="delBtn" onclick="deleteList(<?=$R[$i]['idx']?>, 'delete');"/>
					</td>
				</tr>
				<? 
					
				} 
				?>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
// 검색 submit
$('#listSearch').click(function() {
	var f = document.helloReview;
	f.method = 'get';
	f.target = '_self';
	f.submit();
});

// 삭제
function deleteList(idx, mode) {
	if(confirm('정말로 삭제하시겠습니까?')) {
		window.location.href = './hello_review_update.php?idx='+idx+'&mode='+mode;
	}
}

// enter 눌렀을 때 검색 버튼 클릭 될 수 있게끔 설정
function press(f) {
	if(event.keyCode == 13){    // 13이 enter키를 의미
		$("#listSearch").click();
	}
}

</script>


<? include_once ('../../admin.tail.php'); ?>