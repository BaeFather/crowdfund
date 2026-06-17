<?

include_once('./_common.php');

$sub_menu = '700100';
$g5['title'] = $menu['menu700'][2][1];

include_once('./admin.head.php');

auth_check($auth[$sub_menu], 'w');
if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


$WHERE['1A']	= " AND B.start_datetime < NOW() AND B.end_datetime > NOW() AND B.invest_end_date=''";	// 투자금 모집중
$WHERE['1B']  = " AND B.state='' AND B.invest_end_date!=''";																					// 투자금 모집완료
$WHERE['3']   = " AND (B.state='3' OR (B.end_datetime < NOW() AND B.invest_end_date=''))";						// 투자금 모집실패
$WHERE['1']   = " AND B.state='1'";																																		// 이자상환중
$WHERE['2']   = " AND B.state='2'";																																		// 상품마감(정상상환)
$WHERE['5']   = " AND B.state='5'";																																		// 상품마감(중도상환)
$WHERE['4']   = " AND B.state='4'";																																		// 부실
$WHERE['6']   = " AND B.state='6'";																																		// 대출취소(기표전)
$WHERE['7']   = " AND B.state='7'";																																		// 대출취소(기표후)
$WHERE['2+5'] = " AND B.state IN('2','5')";																														// 상품마감(전체)
$WHERE['6+7'] = " AND B.state IN('6','7')";																														// 대출취소(전체)
$WHERE['8']   = " AND B.state='8'";																																		// 연체
$WHERE['9']   = " AND B.state='9'";																																		// 부도(상환불가)

$where = " 1=1";
$where.= " AND A.invest_state IN('Y','R')";  // 정상투자금 및 대출취소건에 대한 투자금
$where.= ($_REQUEST['state']) ? $WHERE[$_REQUEST['state']] : "";

if($_REQUEST['category']) {
	switch($_REQUEST['category']) {
		case 'A'  : $where .= " AND B.category='2' AND mortgage_guarantees=''"; break;
		case 'A2' : $where .= " AND B.category='2' AND mortgage_guarantees='1'"; break;
		case 'B'  : $where .= " AND B.category='1'"; break;
		case 'C'  : $where .= " AND B.category='3'"; break;
		default   : $where .= ""; break;
	}
}

if($_REQUEST['field'] && $_REQUEST['keyword']) {
	$where.= " AND ( ";
	switch($_REQUEST['field']) {
		case 'prd_idx'     : $where.= " B.idx='".$_REQUEST['keyword']."'"; break;
		case 'start_num'   : $where.= " B.start_num='".$_REQUEST['keyword']."'"; break;
		case 'title'       : $where.= " ".$_REQUEST['field']." LIKE '%".$_REQUEST['keyword']."%'"; break;
		case 'insert_date' : $where.= " ".$_REQUEST['field']."='".$_REQUEST['keyword']."') "; break;
	}
	$where.= " )";
}

if($_REQUEST['invest_capa']) {
	if($_REQUEST['invest_capa']=='_99') {
		$where.= " AND (SELECT COUNT(idx) FROM cf_product_invest WHERE invest_state IN('Y','R') AND product_idx=A.product_idx) < 100 ";
	}
	else if($_REQUEST['invest_capa']=='100-199') {
		$where.= " AND (SELECT COUNT(idx) FROM cf_product_invest WHERE invest_state IN('Y','R') AND product_idx=A.product_idx) BETWEEN 100 AND 199 ";
	}
	else if($_REQUEST['invest_capa']=='200-500') {
		$where.= " AND (SELECT COUNT(idx) FROM cf_product_invest WHERE invest_state IN('Y','R') AND product_idx=A.product_idx) BETWEEN 200 AND 499 ";
	}
	else if($_REQUEST['invest_capa']=='500-1000') {
		$where.= " AND (SELECT COUNT(idx) FROM cf_product_invest WHERE invest_state IN('Y','R') AND product_idx=A.product_idx) BETWEEN 300 AND 999 ";
	}
	else if($_REQUEST['invest_capa']=='1000_') {
		$where.= " AND (SELECT COUNT(idx) FROM cf_product_invest WHERE invest_state IN('Y','R') AND product_idx=A.product_idx) >= 1000 ";
	}
}



$sql = "
	SELECT
		COUNT(DISTINCT A.product_idx) AS cnt,
		B.idx
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE
		$where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = ($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;		// 페이지당 나열 수
$total_page  = ceil($total_count / $rows);							// 전체페이지
$page = ($page > 0) ? $page : 1;
$from_record = ($page - 1) * $rows;											// 시작 열

$num = $total_count - $from_record;


$PCNT = array();

$sql_common = "
	SELECT
		COUNT(DISTINCT A.product_idx) AS cnt
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1=1
		AND A.invest_state IN('Y','R')";

// 상품수: 전체
$sql  = $sql_common;
$DATA = sql_fetch($sql);
$PCNT['0'] = $DATA['cnt'];
//if($_REQUEST['state']=='') echo $sql."\n";

// 상품수 : 투자금 모집중
$sql  = $sql_common . $WHERE['1A'];
$DATA = sql_fetch($sql);
$PCNT['1A'] = $DATA['cnt'];
//if($_REQUEST['state']=='1A') echo $sql."\n";

// 상품수 : 투자금 모집완료
$sql  = $sql_common . $WHERE['1B'];
$DATA = sql_fetch($sql);
$PCNT['1B'] = $DATA['cnt'];
//if($_REQUEST['state']=='1B') echo $sql."\n";

// 상품수: 이자상환중
$sql  = $sql_common . $WHERE['1'];
$DATA = sql_fetch($sql);
$PCNT['1'] = $DATA['cnt'];
//if($_REQUEST['state']=='1') echo $sql."\n";

// 상품수: 정상상환
$sql  = $sql_common . $WHERE['2'];
$DATA = sql_fetch($sql);
$PCNT['2'] = $DATA['cnt'];
//if($_REQUEST['state']=='2') echo $sql."\n";

// 상품수: 투자금 모집실패
$sql  = $sql_common . $WHERE['3'];
$DATA = sql_fetch($sql);
$PCNT['3'] = $DATA['cnt'];
//if($_REQUEST['state']=='3') echo $sql."\n";

// 상품수 : 부실
$sql  = $sql_common . $WHERE['4'];
$DATA = sql_fetch($sql);
$PCNT['4'] = $DATA['cnt'];
//if($_REQUEST['state']=='4') echo $sql."\n";

// 상품수 : 중도상환
$sql  = $sql_common . $WHERE['5'];
$DATA = sql_fetch($sql);
$PCNT['5'] = $DATA['cnt'];
//if($_REQUEST['state']=='5') echo $sql."\n";

// 상품수 : 대출취소(기표전)
$sql  = $sql_common . $WHERE['6'];
$DATA = sql_fetch($sql);
$PCNT['6'] = $DATA['cnt'];
//if($_REQUEST['state']=='6') echo $sql."\n";

// 상품수 : 대출취소(기표후)
$sql  = $sql_common . $WHERE['7'];
$DATA = sql_fetch($sql);
$PCNT['7'] = $DATA['cnt'];
//if($_REQUEST['state']=='7') echo $sql."\n";

// 상품수 : 대출취소(기표후)
$sql  = $sql_common . $WHERE['6+7'];
$DATA = sql_fetch($sql);
$PCNT['6+7'] = $DATA['cnt'];
//if($_REQUEST['state']=='6+7') echo $sql."\n";


$date = date('Y-m-d H:i:s');
$static_repay_day = 5;


$sql = "
	SELECT
		A.*,
		COUNT(A.idx) AS invest_count,
		IFNULL(SUM(A.amount),0) AS amount,
		B.idx, B.gr_idx, B.state, B.category, B.title, B.recruit_amount, B.invest_period, B.invest_days,
		B.invest_return, B.loan_interest_rate, B.withhold_tax_rate, B.loan_interest_type, B.loan_advanced_count, B.loan_usefee, B.invest_usefee, B.invest_usefee_type,
		B.recruit_period_start, B.recruit_period_end, B.repay_type, B.start_datetime, B.start_date, B.end_datetime, B.end_date, B.invest_end_date, B.loan_start_date, B.loan_end_date, B.cancel_date,
		B.purchase_guarantees, B.advanced_payment, B.success_example, B.popular_goods, B.advance_invest, B.advance_invest_ratio, B.ib_trust, B.insert_date
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE
		$where
	GROUP BY
		A.product_idx
	ORDER BY
		B.open_datetime DESC,
		A.product_idx DESC
	LIMIT
		$from_record, $rows";
//print_rr($sql,'font-size:12px'); exit;
$result = sql_query($sql);
$rcount = sql_num_rows($result);

for($i=0; $i<$rcount; $i++) {

	$LIST[$i] = sql_fetch_array($result);
	//print_rr($LIST[$i]);

	$SDATE_OBJ = new DateTime($LIST[$i]['loan_start_date']);
	$EDATE_OBJ = ($LIST[$i]['loan_end_date'] > '0000-00-00') ? new DateTime($LIST[$i]['loan_end_date']) : new DateTime(date('Y-m-d', strtotime($LIST[$i]['loan_start_date'].' +'.$LIST[$i]['invest_period'].' month')));

	$LIST[$i]['repay_count'] = ((int)substr($SDATE_OBJ->format('Y-m-d'), -2) <= sprintf('%02d', $static_repay_day)) ? $LIST[$i]['invest_period'] : $LIST[$i]['invest_period'] + 1;

	$LIST[$i]['loan_end_date'] = $EDATE_OBJ->format('Y-m-d');

	$TOTAL_DATE = date_diff($SDATE_OBJ, $EDATE_OBJ);
	$LIST[$i]['total_days'] = $TOTAL_DATE->days;

	$LIST[$i]['SUCC'] = sql_fetch("SELECT * FROM cf_product_success WHERE product_idx = '".$LIST[$i]['idx']."' ORDER BY turn DESC LIMIT 1");

	if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
		//print_r($SUCC); echo "<br>\n";
	}

	$sql = "
		SELECT
			A.*,
			C.mb_id, C.mb_name, C.bank_name, C.account_num, C.member_type
		FROM
			cf_product_invest A
		LEFT JOIN
			g5_member C  ON A.member_idx=C.mb_no
		WHERE 1=1
			AND A.product_idx='".$LIST[$i]['product_idx']."'
			AND A.invest_state='Y'
			ORDER BY
			A.insert_date";
	$result2 = sql_query($sql);
	while( $row2 = sql_fetch_array($result2) ) {

		for($j=0,$k=1; $j<=$LIST[$i]['invest_period']; $j++,$k++) {
			$EDATE_OBJ = new DateTime(date('Y-m-d', strtotime($SDATE_OBJ->format('Y-m').' last day next month')));
			$DIFF     = date_diff($SDATE_OBJ, $EDATE_OBJ);
			$last_day = $DIFF->days + 1;

			if ($EDATE_OBJ->format('Y-m-d') < $LIST[$i]['loan_end_date']) {
				$ymd = $SDATE_OBJ->format('Y-m').'-'.sprintf('%02d', $static_repay_day);
				$ymd = date('Y-m-d', strtotime($ymd.' +1 month'));
			}
			else {
				$LOAN_DATE = new DateTime($LIST[$i]['loan_end_date']);
				$DIFF      = date_diff($SDATE_OBJ, $LOAN_DATE);
				$last_day  = $DIFF->days;
				$LOAN_DATE->modify('-1 day');
				$ymd       = $LOAN_DATE->format('Y-m-d');
				$LIST[$i]['plus_turn'] = $k;
			}

			$SDATE_OBJ->modify('first day of next month');

			if($LIST[$i]['SUCC']['turn']==$k) {
				$loan_interest   = floor($row2['amount'] * ($LIST[$i]['loan_interest_rate'] / 100) / 365 * $LIST[$i]['total_days']);
				$invest_interest = floor($row2['amount'] * ($LIST[$i]['invest_return'] / 100) / 365 * $LIST[$i]['total_days']);
				$loan_charge     = floor($row2['amount'] * ($LIST[$i]['loan_usefee'] / 100));
				$invest_charge   = floor($row2['amount'] * ($LIST[$i]['invest_usefee'] / 100));
				$invest_withhold = floor(($invest_interest - $invest_charge) * ($LIST[$i]['withhold_tax_rate'] / 100));
				if($row2['member_type']=='4') $invest_withhold = 0;

				$month_loan_interest   = floor($row2['amount'] * ($LIST[$i]['loan_interest_rate'] / 100) / 365 * $last_day);
				$month_invest_interest = floor($row2['amount'] * ($LIST[$i]['invest_return'] / 100) / 365 * $last_day);
				$month_loan_charge     = floor($loan_charge / 365 * $last_day);
				$month_invest_charge   = floor($invest_charge / 365 * $last_day);
				$month_withhold        = ($month_invest_interest - $month_invest_charge) * ($LIST[$i]['withhold_tax_rate'] / 100);

				$LIST[$i]['plus_loan_interest']   += $month_loan_interest;
				$LIST[$i]['plus_invest_interest'] += $month_invest_interest;
				$LIST[$i]['plus_loan_charge']     += $month_loan_charge;
				$LIST[$i]['plus_invest_charge']   += $month_invest_charge;
				$LIST[$i]['plus_invest_withhold'] += $month_withhold;
			}

			if ($LIST[$i]['plus_turn']) {
				break;
			}
		}

	}

	$LIST[$i]['loan_interest']   = floor($LIST[$i]['amount'] * ($LIST[$i]['loan_interest_rate'] / 100) / 365 * $LIST[$i]['total_days']);
	$LIST[$i]['invest_interest'] = floor($LIST[$i]['amount'] * ($LIST[$i]['invest_return'] / 100) / 365 * $LIST[$i]['total_days']);
	$LIST[$i]['loan_charge']     = floor($LIST[$i]['amount'] * ($LIST[$i]['loan_usefee'] / 100) / 365 * $LIST[$i]['total_days']);
	$LIST[$i]['invest_charge']   = floor($LIST[$i]['amount'] * ($LIST[$i]['invest_usefee'] / 100) / 365 * $LIST[$i]['total_days']);
	$LIST[$i]['invest_withhold'] = floor(($LIST[$i]['invest_interest'] - $LIST[$i]['invest_charge']) * ($LIST[$i]['withhold_tax_rate'] / 100));

}

sql_free_result($result);

?>

<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">

				<table class="table table-striped table-bordered table-hover">
					<colgroup>
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
						<col style="width:10%">
					</colgroup>
					<thead>
						<tr style="background-color:#F8F8EF;">
							<th rowspan="2" class="text-center">전체</th>
							<th rowspan="2" class="text-center">투자금 모집중</th>
							<th rowspan="2" class="text-center">투자금 모집완료</th>
							<th rowspan="2" class="text-center">투자금 모집실패</th>
							<th rowspan="2" class="text-center">부실</th>
							<th rowspan="2" class="text-center">이자상환중</th>
							<th colspan="2" class="text-center">상품마감</th>
							<th colspan="2" class="text-center">대출취소</th>
						</tr>
						<tr style="background-color:#F8F8EF;">
							<th>정상상환</th>
							<th>중도상환</th>
							<th>기표전</th>
							<th>기표후</th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd">
							<td align="center" alt="전체"><?=number_format($PCNT['0'])?></td>
							<td align="center" alt="투자금 모집중"><?=number_format($PCNT['1A'])?></td>
							<td align="center" alt="투자금 모집완료"><?=number_format($PCNT['1B'])?></td>
							<td align="center" alt="투자금 모집실패"><?=number_format($PCNT['3'])?></td>
							<td align="center" alt="부실"><?=number_format($PCNT['4'])?></td>
							<td align="center" alt="이자상환중"><?=number_format($PCNT['1'])?></td>

							<td align="center" alt="정상상환"><?=number_format($PCNT['2'])?></td>
							<td align="center" alt="중도상환"><?=number_format($PCNT['5'])?></td>

							<td align="center" alt="대출취소(기표전)"><?=number_format($PCNT['6'])?></td>
							<td align="center" alt="대출취소(기표후)"><?=number_format($PCNT['7'])?></td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<form method="get" class="form-horizontal">
		<div class="panel-body" style="padding:0;">
			<div class="form-group">
				<ul style="list-style:none;">
					<li style="float:left;">
						<select name="state" class="form-control input-sm">
							<option value="">::진행상태::</option>
							<option value="1A" <?=($_REQUEST['state']=='1A') ? 'selected' : ''; ?>>투자금 모집중</option>
							<option value="1B" <?=($_REQUEST['state']=='1B') ? 'selected' : ''; ?>>투자금 모집완료</option>
							<option value="3" <?=($_REQUEST['state']=='3') ? 'selected' : ''; ?>>투자금 모집실패</option>
							<option value="1" <?=($_REQUEST['state']=='1') ? 'selected' : ''; ?>>이자상환중</option>
							<option value="2+5" <?=($_REQUEST['state']=='2+5') ? 'selected' : ''; ?>>상품마감(전체)</option>
							<option value="2" <?=($_REQUEST['state']=='2') ? 'selected' : ''; ?>>상품마감(정상상환)</option>
							<option value="5" <?=($_REQUEST['state']=='5') ? 'selected' : ''; ?>>상품마감(중도상환)</option>
							<option value="4" <?=($_REQUEST['state']=='4') ? 'selected' : ''; ?>>부실</option>
							<option value="6+7" <?=($_REQUEST['state']=='6+7') ? 'selected' : ''; ?>>대출취소(전체)</option>
							<option value="6" <?=($_REQUEST['state']=='6') ? 'selected' : ''; ?>>대출취소(기표전)</option>
							<option value="7" <?=($_REQUEST['state']=='7') ? 'selected' : ''; ?>>대출취소(기표후)</option>
						</select>
					</li>
					<li style="float:left;margin-left:4px;">
						<select name="category" class="form-control input-sm">
							<option value="">::상품구분::</option>
							<option value="A"  <?=($_REQUEST['category']=='A') ? 'selected' : ''; ?>>부동산(PF)</option>
							<option value="A2" <?=($_REQUEST['category']=='A2') ? 'selected' : ''; ?>>부동산(주택담보)</option>
							<option value="B"  <?=($_REQUEST['category']=='B') ? 'selected' : ''; ?>>동산</option>
							<option value="C"  <?=($_REQUEST['category']=='C') ? 'selected' : ''; ?>>헬로페이</option>
						</select>
					</li>
					<li style="float:left;margin-left:4px;">
						<select name="invest_capa" class="form-control input-sm">
							<option value="">::투자자수::</option>
							<option value="_99"      <?=($_REQUEST['invest_capa']=='_99') ? 'selected' : ''; ?>>100 미만</option>
							<option value="100-199"  <?=($_REQUEST['invest_capa']=='100-199') ? 'selected' : ''; ?>>100 이상 200 미만</option>
							<option value="200-500"  <?=($_REQUEST['invest_capa']=='200-500') ? 'selected' : ''; ?>>200 이상 500 미만</option>
							<option value="500-1000" <?=($_REQUEST['invest_capa']=='500-1000') ? 'selected' : ''; ?>>500 이상 1000 미만</option>
							<option value="1000_"    <?=($_REQUEST['invest_capa']=='1000_') ? 'selected' : ''; ?>>1000 이상</option>
						</select>
					</li>
					<li style="float:left;margin-left:4px;">
						<select name="field" class="form-control input-sm">
							<option value="">::필드선택::</option>
							<option value="prd_idx" <?=($_REQUEST['field']=='prd_idx') ? 'selected' : ''; ?>>품번</option>
							<option value="start_num" <?=($_REQUEST['field']=='start_num') ? 'selected' : ''; ?>>호번</option>
							<option value="title" <?=($_REQUEST['field']=='title') ? 'selected' : ''; ?>>상품명</option>
							<option value="insert_date" <?=($_REQUEST['field']=='insert_date') ? 'selected' : ''; ?>>등록일</option>
						</select>
					</li>
					<li style="float:left;margin-left:4px;"><input type="text" name="keyword" value="<?=($_REQUEST['field'] && $_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';?>" class="form-control input-sm"></li>
					<li style="float:left;margin-left:4px;"><button type="submit" class="btn btn-primary btn-sm">검색</button></li>
					<li style="float:left;margin-left:8px;">
						<select name="rows" class="form-control input-sm">
							<option value="10" <?=($_REQUEST['rows']=="" || $_REQUEST['rows']=='10') ? 'selected' : ''; ?>>10개씩</option>
							<option value="20" <?=($_REQUEST['rows']=='20') ? 'selected' : ''; ?>>20개씩</option>
							<option value="50" <?=($_REQUEST['rows']=='50') ? 'selected' : ''; ?>>50개씩</option>
							<option value="100" <?=($_REQUEST['rows']=='100') ? 'selected' : ''; ?>>100개씩</option>
						</select>
					</li>
				</ul>
			</div>
		</div>
		</form>
	</div>
	<div class="col-lg-12">
		<div class="panel-body">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" style="font-size:9pt;">
					<thead>
						<tr style="background-color:#F8F8EF;">
							<th class="text-center" rowspan="2">NO</th>
							<th class="text-center" rowspan="2">품번</th>
							<th class="text-center" rowspan="2">상품명</th>
							<th class="text-center" rowspan="2">대출금액</th>
							<th class="text-center" rowspan="2">진행상태</th>
							<th class="text-center" colspan="2">기간</th>
							<th class="text-center" rowspan="2">연 이율</th>
							<th class="text-center" rowspan="2">월 플랫폼<br>이용료율</th>
							<th class="text-center" colspan="2">펀딩</th>
							<th class="text-center" rowspan="2">전체정산</th>
							<th class="text-center" rowspan="2">이자상환</th>
							<th class="text-center" rowspan="2">관리</th>
						</tr>
						<tr style="background-color:#F8F8EF;">
							<th class="text-center">월수</th>
							<th class="text-center">날짜</th>
							<th class="text-center">투자자수</th>
							<th class="text-center">참여금액</th>
						</tr>
					</thead>
					<tbody>
<?

for($i=0; $i<$rcount; $i++) {

	$state_str = "";
	$bgcolor = "";

	if($LIST[$i]['state']) {
		if($LIST[$i]['state']=='1')      { $state_str = '이자상환중'; $state_code = '2'; }
		else if($LIST[$i]['state']=='2') { $state_str = '상품마감<br>(정상상환)'; }
		else if($LIST[$i]['state']=='3') { $state_str = '투자금<br>모집실패'; $bgcolor = "#FFDDDD"; }
		else if($LIST[$i]['state']=='4') { $state_str = '부실'; $bgcolor = "#FFDDDD"; }
		else if($LIST[$i]['state']=='5') { $state_str = '상품마감<br><span style="color:blue">(중도상환)</span>'; $state_code = '2'; }
		else if($LIST[$i]['state']=='6') { $state_str = '대출계약취소<br>(기표전)'; $state_code = '8'; $bgcolor = "#FFDDDD"; }
		else if($LIST[$i]['state']=='7') { $state_str = '대출계약취소<br>(기표후)'; $state_code = '8'; $bgcolor = "#FFDDDD"; }
	}
	else {
		if($LIST[$i]['open_datetime'] > $date) {
			$state_str = '상품준비중';
		}
		else {
			if($LIST[$i]['invest_end_date']=='') {
				if($LIST[$i]['end_datetime'] < $date) { $state_str = '투자금<br>모집실패'; $bgcolor = "#FFDDDD"; }
				else { $state_str = '대기중'; $state_code = '1'; }
			}
			if($LIST[$i]['start_datetime'] < $date && $LIST[$i]['end_datetime'] > $date) {
				if($LIST[$i]['recruit_amount']==$LIST[$i]['amount']) { $state_str = '투자금<br>모집완료'; }
				else { $state_str = '투자금<br>모집중'; }
			}
		}
	}

	$invest_usefee = ($LIST[$i]['invest_usefee']>'0') ? sprintf('%.2f', $LIST[$i]['invest_usefee']/12).'%' : '면제';

	$loan_date_range = ($LIST[$i]['loan_start_date']=='' || $LIST[$i]['loan_start_date']=='0000-00-00') ? '' : preg_replace("/-/", ".", $LIST[$i]['loan_start_date'])." ~ ".preg_replace("/-/", ".", $LIST[$i]['loan_end_date']);

?>
						<tr class="odd" style="font-size:9pt;background-color:<?=$bgcolor?>">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$LIST[$i]['idx']?></td>
							<td align="left">
								<span style="margin-left:0;padding:0;font-size:11px">&nbsp;</span>
								<? if($LIST[$i]['ib_trust']=='Y') { ?><span style="margnin-left:2px;padding:2px 6px;font-size:11px;border-radius:10px;color:#fff;background-color:blue">예치금신탁</span><? } ?>
								<? if($LIST[$i]['advance_invest']=='Y') { ?><span style="margin-left:2px;padding:2px 6px;font-size:11px;border-radius:10px;color:#fff;background-color:green">사전투자</span><? } ?>
								<? if($LIST[$i]['advanced_payment']=='Y') { ?><span style="margin-left:2px;padding:2px 6px;font-size:11px;border-radius:10px;color:#fff;background-color:#ff6633">이자선지급</span><? } ?>
								<? if($LIST[$i]['purchase_guarantees']=='Y') { ?><span style="margin-left:2px;padding:2px 6px;font-size:11px;border-radius:10px;color:#fff;background-color:red">채권매입보증</span><? } ?>
								<? if($LIST[$i]['success_example']=='Y') { ?><!--<span style="margin-left:2px;padding:2px 6px;font-size:11px;border-radius:10px;color:#fff;background-color:#5cb85c">투자성공사례</span>--><? } ?>
								<? if($LIST[$i]['popular_goods']=='Y') { ?><!--<span style="margin-left:2px;padding:2px 6px;font-size:11px;border-radius:10px;color:#fff;background-color:#3366ff">인기상품</span>--><? } ?>
								<div><a href="<?=G5_URL?>/investment/investment.php?prd_idx=<?=$LIST[$i]['product_idx']?>"><?=$LIST[$i]['title']?></a></div>
							</td>
							<td align="right"><?=number_format($LIST[$i]['recruit_amount'])?></td>
							<td align="center"><?=$state_str?></td>
							<td align="center"><?=($LIST[$i]['invest_days'])?$LIST[$i]['invest_days'].'일' : $LIST[$i]['invest_period'].'개월';?> <?if($loan_date_range){?><br>(<?=$LIST[$i]['total_days']?>일)<?}?></td>
							<td align="center"><?=$loan_date_range?></td>
							<td align="center"><?=$LIST[$i]['invest_return']?>%</td>
							<td align="center"><?=$invest_usefee?></td>
							<td align="right"><?=number_format($LIST[$i]['invest_count'])?></td>
							<td align="right"><?=number_format($LIST[$i]['amount'])?></td>
							<td align="center" style="padding:4px">
								<table>
									<colgroup>
										<col style="width:120px">
										<col style="width:80px">
									</colgroup>
									<tr><td align="center" style="font-size:9pt;color:gray">누적회차</td><td align="right"><?=number_format($LIST[$i]['SUCC']['turn'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">대출자 총이자</td><td align="right"><?=number_format($LIST[$i]['loan_interest'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">투자자 총이자</td><td align="right"><?=number_format($LIST[$i]['invest_interest'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">이자 총차액</td><td align="right"><?=number_format($LIST[$i]['loan_interest'] - $LIST[$i]['invest_interest'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">대출자 플랫폼 이용료</td><td align="right"><?=number_format($LIST[$i]['loan_charge'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">투자자 플랫폼 이용료</td><td align="right"><?=number_format($LIST[$i]['invest_charge'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">투자자 원천징수</td><td align="right"><?=number_format($LIST[$i]['invest_withhold'])?></td></tr>
								</table>
							</td>
							<td align="center" style="padding:4px">
								<table>
									<colgroup>
										<col style="width:120px">
										<col style="width:80px">
									</colgroup>
									<tr><td align="center" style="font-size:9pt;color:gray">회차 (현재/전체)</td><td align="right"><?=number_format($LIST[$i]['SUCC']['turn'])?> / <?=number_format($LIST[$i]['repay_count'])?> <!--<?=number_format($LIST[$i]['plus_turn'])?>--></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">대출자 회수</td><td align="right"><?=number_format($LIST[$i]['plus_loan_interest'] + $LIST[$i]['plus_loan_charge'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">투자자 지급</td><td align="right"><?=number_format($LIST[$i]['plus_invest_interest'] - $LIST[$i]['plus_invest_charge'] - $LIST[$i]['plus_invest_withhold'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">이자 차액</td><td align="right"><?=number_format(($LIST[$i]['plus_loan_interest'] + $LIST[$i]['plus_loan_charge']) - ($LIST[$i]['plus_invest_interest'] - $LIST[$i]['plus_invest_charge'] - $LIST[$i]['plus_invest_withhold']))?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">대출자 플랫폼 이용료</td><td align="right"><?=number_format($LIST[$i]['plus_loan_charge'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">투자자 플랫폼 이용료</td><td align="right"><?=number_format($LIST[$i]['plus_invest_charge'])?></td></tr>
									<tr><td align="center" style="font-size:9pt;color:gray">투자자 원천징수</td><td align="right"><?=number_format($LIST[$i]['plus_invest_withhold'])?></td></tr>
								</table>
							</td>
							<td align="center">
								<a href="./product_calculate.php?idx=<?=$LIST[$i]['product_idx']?>" class="btn btn-primary" style="width:100px;margin-bottom:4px;">정산</a><br>
								<a href="./product_investment_status.php?idx=<?=$LIST[$i]['product_idx']?>" class="btn btn-default" style="width:100px;">투자자 통계</a>
							</td>
						</tr>
<?
	$num--;
}
?>
					</tbody>
				</table>
			</div>
		</div>
		</form>
		<!-- /.panel-body -->

		<div id="paging_span" style="width:100%; margin:0 0 10px 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

$(function() {
	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});
</script>

<?
include_once ('./admin.tail.php');
?>