<?

include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "950100";
$g5['title'] = $menu['menu950'][1][1];

include_once ('../../admin.head.php');

##############################  
#			검색조건 
############################## 
$where = "";

$where .= "AND A.member_idx='48343' AND A.invest_state='Y'";

if (!$srch_y) $srch_y = date("Y");
if (!$srch_m) $srch_m = date("m");

$ymd = date('Y-m-d');
$before_ymd = date('Y-m-d', strtotime($day.' -1 day'));
$srch_ym = $srch_y."-".$srch_m;
$last_day = $srch_ym.'-'.date('t', strtotime($srch_ym.'-01'));
$srch_where = " AND start_date >= '$srch_ym' OR end_date >= '$srch_ym'";


$ST = $_REQUEST['srch_state'];
$st_count = count($ST);


if($srch_state) {
	$where.= " AND B.state IN(";
	$st_str.="&";
	
	for($i=0,$j=1; $i<$st_count; $i++,$j++) {
		$where.= "'".$ST[$i]."'";
		$where.= ($j < $st_count) ? ",":"";

		$st_str.= "ST[]={$ST[$i]}";
		$st_str.= ($j < $st_count) ? "&" : "";
	}

	$where.= ")";
} 


if($date_field && ($sdate || $edate)) {
	if($date_field == 'insert_date') {
		$date_where = "LEFT(A.insert_date,10)";
	} else if($date_field == 'loan_start_date') {
		$date_where = "B.loan_start_date";
	} else if($date_field == 'loan_end_date') {
		$date_where = "B.loan_end_date";
	}

	if($sdate) $where.= " AND $date_where >= '$sdate'";
	if($edate) $where.= " AND $date_where <= '$edate'";
}

if($category) {
	if(in_array($category, array('1','2','3'))) {
		$where.= " AND B.category='".$category."'";
	}
	else {
		if($category=='2A') $where.= " AND B.category='2' AND B.mortgage_guarantees=''";
		if($category=='2B') $where.= " AND B.category='2' AND B.mortgage_guarantees='1'";
	}
}

if($title) {
	$where.= " AND B.title LIKE '%".$title."%'";
}

if($loan_mb_name) {
	$where.= " AND C.mb_name LIKE '%".$loan_mb_name."%'";
}

############################## 
#			자기자본
############################## 
$DATA = "
	SELECT
		idx, start_date, end_date, price, reg_date, mod_date
	FROM
		hello_self_invest
	WHERE 1
		$srch_where
";
$DATA_ROW = sql_fetch($DATA);

$nujuk_invest_amt = sql_fetch("
	SELECT
		IFNULL(SUM(A.amount),0) AS amount
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.member_idx='48343' AND A.invest_state='Y'
		AND B.state IN('1','2','4','5','8','9')
		AND LEFT(B.loan_start_date, 7) <= '".$srch_ym."'
	")['amount'];

$paid_principal = sql_fetch("
	SELECT
		IFNULL(SUM(principal),0) AS principal
	FROM
		cf_product_give
	WHERE 1
		AND member_idx='48343'
		AND LEFT(banking_date, 7) <= '".$srch_ym."'
	")['principal'];

// 자기자본 투자잔액
$invest_remain = $nujuk_invest_amt - $paid_principal;
 
// 자기자본 투자비율
if(!$DATA_ROW['price']) { 
	$DATA_ROW['price'] = 0;
	$invest_perc = 0;
} else { 
	$invest_perc = ($invest_remain/$DATA_ROW['price'])*100; 
}


##############################  
#		   전체 연체율 
############################## 
if($srch_ym == date('Y-m')) {
	$sql2 = "
		SELECT 
			overdue_perc
		FROM
			cf_loan_repay_status
		WHERE (1)
			AND tDate = '".$before_ymd."'
			AND	g_type = 'A'
	";
	$row2 = sql_fetch($sql2);
	
} else {
	$sql2 = "
		SELECT 
			overdue_perc
		FROM
			cf_loan_repay_status
		WHERE (1)
			AND tDate = '".$last_day."'
			AND	g_type = 'A'
	";
	$row2 = sql_fetch($sql2);
}


############################## 
#		Main List SQL 
############################## 
$sql = "
	SELECT
		A.product_idx, A.amount AS invest_amount, A.insert_date,
		B.state, B.title, B.category, B.mortgage_guarantees, B.loan_start_date, B.loan_end_date, B.recruit_amount, B.loan_mb_no, B.ltv,
		((A.amount / B.recruit_amount) * 100) AS invest_perc,
		( SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE product_idx=A.product_idx AND member_idx='48343' AND (banking_date IS NOT NULL OR banking_date > '0000-00-00 00:00:00')) AS paid_amount,
		C.mb_name
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	LEFT JOIN
		g5_member  C  ON B.loan_mb_no=C.mb_no
	WHERE (1)
		$where
	ORDER BY
		B.loan_start_date DESC,
		B.start_num DESC,
		A.idx DESC
";

//echo '<pre>'.$sql.'</pre>';

$res = sql_query($sql);
$rows = sql_num_rows($res);

$num = $rows;

for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);

	$remain_price = $LIST[$i]['invest_amount'] - $LIST[$i]['paid_amount'];	
	$tot_remain += $remain_price;
	$tot_recruit_price += $LIST[$i]['recruit_amount'];
	$tot_invest_price += $LIST[$i]['invest_amount'];
}


add_stylesheet('<link rel="stylesheet" href="css/hello_invest.css" />', 0);
?>


<div id="helloInvest" class="tbl_head02 tbl_wrap">
	<div class="srch-wrap01">
		<form id="frmSearchSum" name="frmSearchSum" method="get" class="form-horizontal">
			<!-- 자기자본 관리 영역 -->
			<ul class="col col-md-* list-inline">
				<li>
					<select name="srch_y" class="form-control input-sm">
						<option value="">연도 선택</option>
						<option value="2022" <?=$srch_y=="2022"?"selected":""?> >2022</option>
						<option value="2021" <?=$srch_y=="2021"?"selected":""?> >2021</option>
						<option value="2020" <?=$srch_y=="2020"?"selected":""?> >2020</option>
					</select>
				</li>
				<li>
					<select name="srch_m" class="form-control input-sm">
						<option value="">월 선택</option>
						<option value="01" <?=$srch_m=="01"?"selected":""?> >1</option>
						<option value="02" <?=$srch_m=="02"?"selected":""?> >2</option>
						<option value="03" <?=$srch_m=="03"?"selected":""?> >3</option>
						<option value="04" <?=$srch_m=="04"?"selected":""?> >4</option>
						<option value="05" <?=$srch_m=="05"?"selected":""?> >5</option>
						<option value="06" <?=$srch_m=="06"?"selected":""?> >6</option>
						<option value="07" <?=$srch_m=="07"?"selected":""?> >7</option>
						<option value="09" <?=$srch_m=="09"?"selected":""?> >9</option>
						<option value="10" <?=$srch_m=="10"?"selected":""?> >10</option>
						<option value="11" <?=$srch_m=="11"?"selected":""?> >11</option>
						<option value="12" <?=$srch_m=="12"?"selected":""?> >12</option>
					</select>
				</li>
			</ul>
			<ul class="hello-set-btn list-inline">
				<li><button class="btn btn-sm btn-primary" onclick="windowPop('./hello_self_set_list.php', 'Setting');">자기자본 관리</button></li>
			</ul>
		</form>
	</div>

	<table class="table tbl-tot-data">
		<thead>
			<tr>
				<th rowspan='2'>자기자본</th>
				<th colspan='2'>자기계산 투자</th>
				<th rowspan='2'>전체 연체율</th>
			</tr>
			<tr>
				<th>투자 잔액</th>
				<th>투자 비율</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?=number_format($DATA_ROW['price'])?>원</td>
				<td><?=number_format($invest_remain)?>원</td>
				<td><?=floatRtrim(floatCutting($invest_perc, 2))?>%</td>
				<td><?=$row2['overdue_perc']?>%</td>
			</tr>
		</tbody>
	</table>
	
	<div class="srch-wrap02">
		<form id="frmSearch" name= "frmSearch" method="get" class="form-horizontal">
			<table class="table srch-table">
				<tr>
					<th>날짜</th>
					<td>
						<ul class="col col-md-* list-inline">
							<li>
								<select name="date_field" class="form-control input-sm">
									<option value="">날짜 선택</option>
									<option value="insert_date" <?=($date_field=='insert_date')?'selected':'';?>>투자일</option>
									<option value="loan_start_date" <?=($date_field=='loan_start_date')?'selected':'';?>>대출실행일</option>
									<option value="loan_end_date" <?=($date_field=='loan_end_date')?'selected':'';?>>대출종료일</option>
								</select>
							</li>
							<li class="date"><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
							<li>~</li>
							<li class="date"><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
						</ul>
					</td>
					<th>카테고리</th>
					<td>
						<select name="category" class="form-control input-sm">
							<option value="">카테고리 선택</option>
							<option value="2" <?if($category=='2') echo"selected";?>>부동산</option>
							<option value="2A" <?if($category=='2A') echo"selected";?>>- PF</option>
							<option value="2B" <?if($category=='2B') echo"selected";?>>- 주택담보대출</option>
							<option value="3" <?if($category=='3') echo"selected";?>>헬로페이</option>
							<option value="1" <?if($category=='1') echo"selected";?>>동산</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>상품명</th>
					<td><input type="text" name="title" value="<?=$title?>" class="form-control input-sm" /></td>
					<th>차입자</th>
					<td><input type="text" name="loan_mb_name" value="<?=$loan_mb_name?>" class="form-control input-sm input-width" /></td>
				</tr>
				<tr>
					<th>진행현황</th>
					<td colspan='3'>
						<label class="checkbox-inline"><input type="checkbox" name="srch_state_all" id="stateAll" value="A" <? if($srch_state_all == 'A') echo 'checked'; ?> /> 전체</label>
						<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="1" <?=(@in_array('1', $ST))?'checked':'';?> /> 이자상환중</label>
						<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="2" <?=(@in_array('2', $ST))?'checked':''?> /> 상환완료</label>
						<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="5" <?=(@in_array('5', $ST))?'checked':''?> /> 중도상환</label>
						<label class="checkbox-inline"><input type="checkbox" name="srch_state[]" class="state_opt" value="8" <?=(@in_array('8', $ST))?'checked':''?> /> 연체중</label>
					</td>
				</tr>
			</table>
			<div class="srch-btn">
				<button id="listSearch" class="btn btn-sm btn-warning">검색</button>
				<button type="button" class="btn btn-sm btn-default" onClick="location.href='<?=$_SERVER['PHP_SELF']?>';">초기화</button>
			</div>
		</form>
	</div>
	
	<div class="excel-btn">
		<button class="btn btn-sm btn-success" onclick="exel_down();">엑셀 다운로드</button>
	</div>

	<form id="frmSearchList" name= "frmSearchList" class="form-horizontal">
	<input type="hidden" name="sql" value="<?=$sql?>" />
		<table class="table tbl-list-data">
			<thead>
				<tr>
					<th>NO</th>
					<th>카테고리</th>
					<th>품번</th>
					<th>상품명</th>
					<th>차입자</th>
					<th>LTV</th>
					<th>진행상태</th>
					<th>투자기간</th>
					<th>투자일</th>
					<th>모집금액</th>
					<th>투자금액</th>
					<th>투자비율</th>
					<th>투자잔액</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tot">
					<td colspan='9'>총 <?=$num?>건</td>
					<td><?=number_format($tot_recruit_price)?></td>
					<td><?=number_format($tot_invest_price)?></td>
					<td></td>
					<td><?=number_format($tot_remain)?></td>
				</tr>
				<? for($i=0, $j=$num; $i<$rows; $i++,$j--) { 

					if($LIST[$i]['state']) {
						switch($LIST[$i]['state']) {
							case '1': $state='상환중'; break;
							case '2': $state='정상상환'; break;
							case '3': $state='모집실패'; break;
							case '4': $state='부실'; break;
							case '5': $state='중도상환'; break;
							case '6': $state='대출취소(기표전)'; break;
							case '7': $state='대출취소(기표후)'; break;
							case '8': $state='연체'; break;
							case '9': $state='부도(상환불가)'; break;
						}
					}

					if($LIST[$i]['category'] == '2' && $LIST[$i]['mortgage_guarantees'] == '') {
						$category = "PF";
					} else if($LIST[$i]['category'] == '2' && $LIST[$i]['mortgage_guarantees'] == '1') {
						$category = "주택담보대출";
					} else if($LIST[$i]['category'] == '3') {
						$category = "매출채권";
					} else if($LIST[$i]['category'] == '1') {
						$category = "동산";
					}

					$remain_price = $LIST[$i]['invest_amount'] - $LIST[$i]['paid_amount'];
					
				?>
				<tr>
					<td><?=$j?></td>
					<td><?=$category?></td>
					<td><?=$LIST[$i]['product_idx']?></td>
					<td><?=$LIST[$i]['title']?></td>
					<td><?=$LIST[$i]['mb_name']?></td>
					<td><?=$LIST[$i]['ltv']?>%</td>
					<td><?=$state?></td>
					<td><?=$LIST[$i]['loan_start_date'].' ~ '.$LIST[$i]['loan_end_date']?></td>
					<td><?=$LIST[$i]['insert_date']?></td>
					<td><?=number_format($LIST[$i]['recruit_amount'])?></td>
					<td><?=number_format($LIST[$i]['invest_amount'])?></td>
					<td><?=floatRtrim(floatCutting($LIST[$i]['invest_perc'], 2))?>%</td>
					<td><?=number_format($remain_price)?></td>
				</tr>
				<?
					unset($LIST[$i]);  // 변수 제거
				}
				?>	
			</tbody>
		</table>
	</form>
</div>

<script type="text/javascript">
// 자기자본 관리 년, 월 select option 선택시 값 변경
$('select[name=srch_y], select[name=srch_m]').change(function () {
	$('#frmSearchSum').submit();
});

// 검색 submit
$('#listSearch').click(function() {
	var f = document.frmSearch;
	f.method = 'get';
	f.target = '_self';
	f.submit();
});

// 자기자본 관리
function windowPop(url, name) {
	var popOption = 'top=10, left=10, width=900, height=600, status=no, menubar=no, toolbar=no, resizable=no';
	window.open(url, name, popOption);
}

// enter 눌렀을 때 검색 버튼 클릭 될 수 있게끔 설정
function press(f) {
	if(event.keyCode == 13){    // 13이 enter키를 의미
		$("#listSearch").click();
	}
}

// 체크박스 전체 선택
$("#stateAll").on("click", function () {
    $('.state_opt').prop("checked", $(this).is(":checked"));
}); 

// 체크박스 개별 선택
$(".state_opt").on("click", function() {
    var is_checked = true;

    $(".state_opt").each(function(){
        is_checked = is_checked && $(this).is(":checked");
    });

    $("#stateAll").prop("checked", is_checked);
});

// 엑셀 다운로드
function exel_down() {
	if(confirm('검색결과를 엑셀로 다운로드 받으시겠습니까?')) {
		var f = document.frmSearchList;
		f.method = 'post';
		f.action = 'hello_invest_list.excel.php';
		f.target = '_blank';
		f.submit();
	}
}
</script>

<? include_once ('../../admin.tail.php'); ?>