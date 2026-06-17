<?
/**
 * 투자상품 목록
 */
$sub_menu = "600100";
include_once('./_common.php');

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$CATEGORY['1']  = " AND A.category='1'";
$CATEGORY['2']  = " AND A.category='2'";
$CATEGORY['2A'] = " AND A.category='2' AND A.mortgage_guarantees=''";
$CATEGORY['2B'] = " AND A.category='2' AND A.mortgage_guarantees='1'";
$CATEGORY['3']  = " AND A.category='3'";
$CATEGORY['3A'] = " AND A.category='3' AND A.category2='1'";
$CATEGORY['3B'] = " AND A.category='3' AND A.category2='2'";

$ST = $_REQUEST['ST'];
$st_count = count($ST);

$sql_search = " 1=1 ";
if($ai_grp_idx) $sql_search.= " AND A.ai_grp_idx='$ai_grp_idx'";
if($gr_idx)    $sql_search.= " AND A.gr_idx='$gr_idx'";

$date = date('Y-m-d H:i:s');
if ($prd_ready=="Y") {
	$sql_srch1 = " (A.open_datetime <= '$date' AND A.invest_end_date='' AND A.end_datetime >= '$date' AND A.start_datetime>'$date') " ; // 대기중 상품
}
if ($prd_ready2=="Y") {
	$sql_srch2 = " (A.open_datetime > '$date') " ; // 상품 준비중
	if ($sql_srch1) $sql_srch2 = " OR ".$sql_srch2;
}
if ($prd_inving=="Y") {
	$sql_srch3 = " (A.open_datetime <= '$date' AND A.start_datetime<'$date' AND A.end_datetime>'$date' AND A.state='' AND A.invest_end_date='') " ; // 투자금 모집중
	if ($sql_srch1 || $sql_srch2) $sql_srch3 = " OR ".$sql_srch3;
}
if ($prd_invend=="Y") {
	$sql_srch4 = " (A.open_datetime <= '$date' AND A.start_datetime<'$date' AND A.end_datetime>'$date' AND A.state='' AND A.invest_end_date!='') " ; // 투자금 모집완료
	if ($sql_srch1 || $sql_srch2 || $sql_srch3) $sql_srch4 = " OR ".$sql_srch4;
}
if ($sql_srch1 || $sql_srch2 || $sql_srch3 || $sql_srch4) {
	if (!$st_count) {
		$sql_search .= " AND ($sql_srch1 $sql_srch2 $sql_srch3 $sql_srch4) ";
	} else {
		$n_stat_str = " OR ($sql_srch1 $sql_srch2 $sql_srch3 $sql_srch4) ";
	}
}

$st_str = "";
if($st_count) {
	$sql_search.= " AND (A.state IN(";
	$st_str.="&";
	for($i=0,$j=1;$i<$st_count;$i++,$j++) {
		$sql_search.= "'".$ST[$i]."'";
		$sql_search.= ($j<$st_count) ? ",":"";

		$st_str.= "ST[]={$ST[$i]}";
		$st_str.= ($j<$st_count) ? "&" : "";
	}
	$sql_search.= ") ".$n_stat_str . " )";
}

if($category) $sql_search.= $CATEGORY[$category];
if($loan_interest_type) {	$sql_search.= ($loan_interest_type=='def') ? " AND A.loan_interest_type='0'" : " AND A.loan_interest_type='$loan_interest_type'"; } // 대출이자수급방식
if($loan_usefee_type) $sql_search.= " AND A.loan_usefee_type='$loan_usefee_type'";					// 대출자 플랫폼수수료 징수방식
if($invest_usefee_type) $sql_search.= " AND A.invest_usefee_type='$invest_usefee_type'";		// 투자자 플랫폼수수료 징수방식
if($loan_mb_no) $sql_search.= " AND A.loan_mb_no = '$loan_mb_no'";
if($display) $sql_search.= " AND A.display = '$display'";
if($platform) {
	$sql_search.= ($flatform=='null') ? " AND A.platform=''" : " AND A.platform LIKE '%".$platform."%'";
}
if($auto_invest) $sql_search.= " AND A.ai_grp_idx!=''";		//자동투자
if($purchase_guarantees) $sql_search.= " AND A.purchase_guarantees='$purchase_guarantees'";		//채권매입보증
if($advanced_payment) $sql_search.= " AND A.advanced_payment='$advanced_payment'";			//선지급상품
if($portfolio) $sql_search.= " AND A.portfolio='$portfolio'";			//선지급상품
if($success_example) $sql_search.= " AND A.success_example='$success_example'";	 //투자성공사례지정상품
if($popular_goods) $sql_search.= " AND A.popular_goods='$popular_goods'";	 //인기상품
if($advance_invest) $sql_search.= " AND A.advance_invest='$advance_invest'";	 //사전투자설정상품
if($isConsor) $sql_search.= " AND A.isConsor='$isConsor'";	 //사전투자설정상품
if($ib_trust) $sql_search.= " AND A.ib_trust='$ib_trust'";	 //신한예치금신탁운용상품
if($only_vip) $sql_search.= " AND A.only_vip='$only_vip'";	 //투자자지정상품
if($isTest) $sql_search.= " AND A.isTest='$isTest'";	 //테스트상품여부
if($ptl_repay_prdt) $sql_search.= " AND (SELECT COUNT(idx) FROM cf_partial_redemption WHERE product_idx=A.idx) > 0";	 //원금일부상환상품
if($samount) $sql_search.= " AND A.recruit_amount >= $samount";
if($eamount) $sql_search.= " AND A.recruit_amount <= $eamount";
if($date_field) {
	if($sdate) $sql_search.= " AND $date_field >= '$sdate' ";
	if($edate) $sql_search.= " AND $date_field <= '$edate' ";
}
if($field && $keyword) {
	if( in_array($field, array('A.idx','A.start_num')) ) {
		$sql_search.= ( preg_match("/\,/", $keyword) ) ? " AND $field IN(".$keyword.")" : " AND $field='".$keyword."'";
	}
	else if($field=='B.mb_id') {
		$sql_search.= " AND B.mb_id LIKE '%".$keyword."%'";
	}
	else if($field=='mb_title') {
		$sql_search.= " AND (B.mb_name LIKE '%".$keyword."%' OR B.mb_co_name LIKE '%".$keyword."%')";
	}
	else {
		$sql_search.= ($field=='address') ? " AND (A.address LIKE '%$keyword%' OR A.address_detail LIKE '%$keyword%')" : " AND $field LIKE '%$keyword%' ";
	}
}

$sql = "
	SELECT
		COUNT(A.idx) AS cnt,
		IFNULL(SUM(A.recruit_amount),0) AS recruit_amount,
		(SELECT IFNULL(SUM(principal),0) FROM cf_product_give WHERE product_idx=A.idx AND banking_date IS NOT NULL) AS paid_principal
	FROM
		cf_product A
	LEFT JOIN
		g5_member B  ON A.loan_mb_no=B.mb_no
	LEFT JOIN
		cf_product_container C  ON A.idx=C.product_idx
	WHERE
		$sql_search";
//print_rr($sql, 'font-size:12px');
$row = sql_fetch($sql);
$total_count = $row['cnt'];		// 전체 상품수
$total_recruit_amount   = $row['recruit_amount'];		// 투자모집금액 합계
$total_paid_principal   = $row['paid_principal'];		// 상환원금 합계
$total_remain_principal = $row['recruit_amount'] - $row['paid_principal'];		// 상환원금합계 = 투자모집금액 - 상환원금

$rows = 20;
//$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql_order = "";
if($sort_field) $sql_order.= $sort_field." ".$sort.", ";
//$sql_order.= "A.start_num DESC, A.idx DESC";
$sql_order.= "A.idx DESC";


$sql = "
	SELECT
		A.*
		, B.mb_id
		, IF(B.member_type=2,B.mb_co_name,B.mb_name) AS mb_title
		, ( SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS invest_count
		, ( SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS invest_amount
		, ( (A.recruit_amount/(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y'))*100 ) AS invest_percent
		, ( SELECT IFNULL(SUM(amount),0) FROM cf_partial_redemption WHERE product_idx=A.idx ) AS ptl_repay_amount
	FROM
		cf_product A
	LEFT JOIN
		g5_member B  ON A.loan_mb_no=B.mb_no
	LEFT JOIN
		cf_product_container C  ON A.idx=C.product_idx
	WHERE
		$sql_search
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $rows";
//print_rr($sql, 'font-size:12px');
$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[] = sql_fetch_array($result);
}
sql_free_result($result);

$list_count = count($LIST);

$num = $total_count - $from_record;



$g5['title'] = $menu['menu600'][1][1];
include_once('../admin.head.php');
?>

<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background:#000; border:1px solid #000; cursor:default }

span.flag { padding:2px 6px; font-size:11px; border-radius:3px; color:#FFFFFF; cursor:pointer; }
span.p01  { background:#0000FF; }
span.p02  { background:purple;  }
span.p03  { background:#FF2222; }
span.p04  { background:#FF7419; }
span.p05  { background:#5CB85C; }
span.p06  { background:#3366FF; }
span.p07  { background:green;   }
span.p08  { background:orange;  }
span.p09  { background:#000000; }
span.p10  { background:#FF9900; }
</style>

<div style="width:100%">
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<!-- 검색영역 START -->
			<div style="line-height:28px;">
				<form id="frmSearch" name="frmSearch" method="get" class="form-horizontal">
				<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select name="ai_grp_idx" id="ai_grp_idx" class="form-control input-sm">
							<option value="">::자동투자그룹::</option>
<?
$res = sql_query("SELECT idx, grp_title FROM cf_auto_invest_config WHERE display='Y' ORDER BY idx");
while($ROW = sql_fetch_array($res)) {
	$selected = ($ROW['idx']==$ai_grp_idx) ? 'selected' : '';
	echo '<option value="'.$ROW['idx'].'" '.$selected.'>'.$ROW['grp_title'].'</option>' . PHP_EOL;
}
?>
						</select>
					</li>
					<li>
						<select name="gr_idx" id="gr_idx" class="form-control input-sm">
							<option value="">::동일차주상품::</option>
<?
$res = sql_query("SELECT gr_idx, title, COUNT(idx) AS cnt FROM cf_product GROUP BY gr_idx ORDER BY cnt DESC, gr_idx DESC");
while($ROW = sql_fetch_array($res)) {
		if($ROW['cnt']>1) {
				$selected = ($gr_idx==$ROW['gr_idx']) ? 'selected' : '';
				echo '<option value="'.$ROW['gr_idx'].'" '.$selected.'>'.$ROW['title'].' :: ('.$ROW['cnt'].'건 등록)</option>' . PHP_EOL;
		}
}
?>
						</select>
					</li>
				</ul>
				<ul class="col col-md-* list-inline" style="padding:0;margin-bottom:5px">
					<li><label class="checkbox-inline"><input type="checkbox" name="auto_invest" value="Y" <?=($auto_invest=='Y')?'checked':''?>>자동투자</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="purchase_guarantees" value="Y" <?=($purchase_guarantees=='Y')?'checked':''?>>채권매입보증</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="advanced_payment" value="Y" <?=($advanced_payment=='Y')?'checked':''?>>선지급</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="portfolio" value="Y" <?=($portfolio=='Y')?'checked':''?>>포트폴리오</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="success_example" value="Y" <?=($success_example=='Y')?'checked':''?>>투자성공사례</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="popular_goods" value="Y" <?=($popular_goods=='Y')?'checked':''?>>인기상품</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="advance_invest" value="Y" <?=($advance_invest=='Y')?'checked':''?>>사전투자</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="ib_trust" value="Y" <?=($ib_trust=='Y')?'checked':''?>>예치금신탁</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="isConsor" value="1" <?=($isConsor=='1')?'checked':''?>>컨소시엄상품</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="only_vip" value="1" <?=($only_vip=='1')?'checked':''?>>법인전용상품</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="isTest" value="1" <?=($isTest=='1')?'checked':''?>>테스트상품</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="ptl_repay_prdt" value="1" <?=($ptl_repay_prdt=='1')?'checked':''?>>원금일부상환상품</label></li>
				</ul>
				<ul class="col col-md-* list-inline" style="padding:0;margin-bottom:5px">
					<li>
						<ul class="list-inline">
							<li>진행현황 :</li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="1" <?=(@in_array('1', $ST))?'checked':'';?>>이자상환중</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="2" <?=(@in_array('2', $ST))?'checked':'';?>>정상상환</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="5" <?=(@in_array('5', $ST))?'checked':'';?>>중도상환</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="8" <?=(@in_array('8', $ST))?'checked':'';?>>연체중</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="9" <?=(@in_array('9', $ST))?'checked':'';?>>부실</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="4" <?=(@in_array('4', $ST))?'checked':'';?>>매각</label></li>
							<li></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="3" <?=(@in_array('3', $ST))?'checked':'';?>>투자금모집실패</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="6" <?=(@in_array('6', $ST))?'checked':'';?>>대출취소(기표전)</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="7" <?=(@in_array('7', $ST))?'checked':'';?>>대출취소(기표후)</label></li>
							<li></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="prd_ready" value="Y" <?=$prd_ready=='Y'?'checked':'';?>>대기중</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="prd_ready2" value="Y" <?=$prd_ready2=='Y'?'checked':'';?>>준비중</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="prd_inving" value="Y" <?=$prd_inving=='Y'?'checked':'';?>>모집중</label></li>
							<li><label class="checkbox-inline"><input type="checkbox" name="prd_invend" value="Y" <?=$prd_invend=='Y'?'checked':'';?>>모집완료</label></li>
						</ul>
					</li>
				</ul>
				<ul class="col col-md-* list-inline" style="padding:0;margin-bottom:5px">
					<li>
						<select id="category" name="category" class="form-control input-sm">
							<option value="">::카테고리::</option>
							<option value="2" <?=($category=='2')?'selected':'';?>>부동산</option>
							<option value="2A" <?=($category=='2A')?'selected':'';?>>- PF</option>
							<option value="2B" <?=($category=='2B')?'selected':'';?>>- 주택담보</option>
							<option value="1" <?=($category=='1')?'selected':'';?>>동산</option>
							<option value="3" <?=($category=='3')?'selected':'';?>>헬로페이</option>
							<option value="3A" <?=($category=='3A')?'selected':'';?>> - 소상공인 확정매출채권</option>
							<option value="3B" <?=($category=='3B')?'selected':'';?>> - 면세점 확정매출채권</option>
						</select>
					</li>
					<li>
						<select id="loan_interest_type" name="loan_interest_type" class="form-control input-sm">
							<option value="">::대출이자수급방식::</option>
							<option value="def" <?=($loan_interest_type=='def')?'selected':'';?>>월별수취</option>
							<option value="1" <?=($loan_interest_type=='1')?'selected':'';?>>선수취</option>
							<option value="2" <?=($loan_interest_type=='2')?'selected':'';?>>부분수취</option>
						</select>
					</li>
					<li>
						<select id="invest_usefee_type" name="invest_usefee_type" class="form-control input-sm">
							<option value="">::투자자플랫폼이용료징수방식::</option>
							<option value="A" <?=($invest_usefee_type=='A')?'selected':'';?>>월별분할징수</option>
							<option value="B" <?=($invest_usefee_type=='B')?'selected':'';?>>만기일시징수</option>
						</select>
					</li>
					<li>
						<select id="loan_usefee_type" name="loan_usefee_type" class="form-control input-sm">
							<option value="">::대출자플랫폼이용료징수방식::</option>
							<option value="B" <?=($loan_usefee_type=='B')?'selected':'';?>>선취(일시징수)</option>
							<option value="A" <?=($loan_usefee_type=='A')?'selected':'';?>>후취(분할징수)</option>
						</select>
					</li>
					<li>
						<select id="display" name="display" class="form-control input-sm">
							<option value="">::출력설정::</option>
							<option value="Y" <?=($display=='Y')?'selected':'';?>>노출</option>
							<option value="N" <?=($display=='N')?'selected':'';?>>비노출</option>
						</select>
					</li>
					<li>
						<select name="platform" class="form-control input-sm">
							<option value="">::신디케이션플랫폼::</option>
<?
	$scount = count($CONF['SYNDICATOR']);
	$skey = array_keys($CONF['SYNDICATOR']);
	for($i=0; $i<$scount; $i++) {
		$selected = ($skey[$i]== $platform) ? "selected" : "";
		echo "					<option value='".$skey[$i]."' $selected>".$CONF['SYNDICATOR'][$skey[$i]]['name']."</option>\n";
	}
?>
						</select>
					</li>
				</ul>
				<ul class="col col-md-* list-inline" style="padding:0;margin-bottom:5px">
					<li>
						<select name="date_field" class="form-control input-sm">
							<option value="">::데이트 필드선택::</option>
							<option value="A.open_date" <?=($date_field=='A.open_date')?'selected':'';?>>상품출력시작일</option>
							<option value="A.recruit_period_start" <?=($date_field=='A.recruit_period_start')?'selected':'';?>>모집시작일</option>
							<option value="A.recruit_period_end" <?=($date_field=='A.recruit_period_end')?'selected':'';?>>모집종료일</option>
							<option value="A.loan_start_date" <?=($date_field=='A.loan_start_date')?'selected':'';?>>대출시작일</option>
							<option value="A.loan_end_date" <?=($date_field=='A.loan_end_date')?'selected':'';?>>대출종료일</option>
						</select>
					</li>
					<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
					<li>~</li>
					<li><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
				</ul>
				<ul class="col col-md-* list-inline" style="padding:0;margin-bottom:5px">
					<li>
						<select name="field" class="form-control input-sm">
							<option value="">::필드선택::</option>
							<option value="A.idx" <?=($field=='A.idx')?'selected':'';?>>품번</option>
							<option value="A.start_num" <?=($field=='A.start_num')?'selected':'';?>>호번</option>
							<option value="A.title" <?=($field=='A.title')?'selected':'';?>>상품명</option>
							<option value="A.repay_acct_no" <?=($field=='A.repay_acct_no')?'selected':'';?>>상환용 가상계좌</option>
							<option value="mb_title" <?=($field=='mb_title')?'selected':'';?>> - 대출회원명</option>
							<option value="B.mb_id" <?=($field=='C.mb_id')?'selected':'';?>> - 대출회원ID</option>
							<option value="A.loan_mb_no" <?=($field=='A.loan_mb_no')?'selected':'';?>> - 대출회원번호</option>
							<option value="A.address" <?=($field=='A.address')?'selected':'';?>>물건주소</option>
							<option value="C.extend_1" <?=($field=='C.extend_1')?'selected':'';?>>담보분석 및 평가</option>
							<option value="C.extend_2" <?=($field=='C.extend_2')?'selected':'';?>>신용 및 부채정보</option>
							<option value="C.extend_3" <?=($field=='C.extend_3')?'selected':'';?>>투자 구조도</option>
							<option value="C.extend_4" <?=($field=='C.extend_4')?'selected':'';?>>투자자 보호장치</option>
							<option value="C.extend_5" <?=($field=='C.extend_5')?'selected':'';?>>평가기관 의견</option>
							<option value="C.extend_6" <?=($field=='C.extend_6')?'selected':'';?>>채권매입보증</option>
							<option value="C.extend_7" <?=($field=='C.extend_7')?'selected':'';?>>투자유의사항(FAQ)</option>
							<option value="C.extend_8" <?=($field=='C.extend_8')?'selected':'';?>>업데이트현황</option>
							<option value="C.extend_9" <?=($field=='C.extend_9')?'selected':'';?>>증빙자료</option>
							<option value="C.invest_summary" <?=($field=='C.invest_summary')?'selected':'';?>>투자요약</option>
							<option value="C.core_invest_point" <?=($field=='C.core_invest_point')?'selected':'';?>>핵심투자포인트</option>
							<option value="C.security_loan" <?=($field=='C.security_loan')?'selected':'';?>>기존담보대출내역</option>
							<option value="C.special_info" <?=($field=='C.special_info')?'selected':'';?>>전문정보</option>
							<option value="C.screening" <?=($field=='C.screening')?'selected':'';?>>심사총평</option>
							<option value="C.receiver" <?=($field=='C.receiver')?'selected':'';?>>접수자</option>
							<option value="C.broker" <?=($field=='C.broker')?'selected':'';?>>중개자</option>
						</select>
					</li>
					<li><input type="text" class="form-control input-sm" name="keyword" size="30" value="<?=$keyword?>"  onkeypress="JavaScript:press(this.form);"></li>
					<li><button type="button" id="search_button" class="btn btn-sm btn-warning">검색</button></li>
					<li><button type="button" onClick="location.replace('<?=$_SERVER['PHP_SELF']?>');" class="btn btn-sm btn-default">초기화</button></li>
					<li></li>
					<li>
						<select id="sort_field" class="form-control input-sm">
							<option value="">::정렬필드::</option>
							<option value="A.idx" <?=($sort_field=='A.idx')?'selected':'';?>>품번</option>
							<option value="A.start_num" <?=($sort_field=='A.start_num')?'selected':'';?>>호번</option>
							<option value="A.state" <?=($sort_field=='A.state')?'selected':'';?>>진행상태</option>
							<option value="A.recruit_amount" <?=($sort_field=='A.recruit_amount')?'selected':'';?>>목표금액</option>
							<option value="invest_count" <?=($sort_field=='invest_count')?'selected':'';?>>투자자수</option>
							<option value="invest_amount" <?=($sort_field=='invest_amount')?'selected':'';?>>투자금액</option>
							<option value="A.invest_period" <?=($sort_field=='A.invest_period')?'selected':'';?>>투자개월수</option>
							<option value="A.open_datetime" <?=($sort_field=='A.open_datetime')?'selected':'';?>>상품노출시작일</option>
							<option value="A.start_date" <?=($sort_field=='A.start_date')?'selected':'';?>>모집시작일</option>
							<option value="A.loan_start_date" <?=($sort_field=='A.loan_start_date')?'selected':'';?>>대출시작일</option>
							<option value="A.loan_end_date" <?=($sort_field=='A.loan_end_date')?'selected':'';?>>대출종료일</option>
						</select>
					</li>
					<li><button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button></li>
					<li><button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button></li>
					<li></li>
					<!--<li><button type="button" id="excelDownToProduct" class="btn btn-sm btn-success" style="width:150px">검색결과 다운로드</button></li>-->
					<li><button type="button" id="excelDownToProduct2" class="btn btn-sm btn-success" style="width:150px">보고자료 다운로드</button></li>
					<li><button type="button" id="excelDownToProduct3" class="btn btn-sm btn-success" style="width:170px">대외보고자료 다운로드</button></li>
					<li style="float:right"><button type="button" class="btn btn-sm btn-primary" onClick="document.location.href='./product_form.php';">상품등록</button></li>
				</ul>

				<div class="clearfix"></div>
				</form>
			</div>
			<!-- 검색영역 E N D -->

			<div class="dataTable_wrapper">
				<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;">
					<colgroup>
						<col width="4%">
						<col width="4%">
						<col width="6%">
						<col width="">
						<col width="7%">
						<col width="12%">
						<col width="5%">
						<col width="7%">
						<col width="5%">
						<col width="8%">
						<col width="4%">
						<col width="7%">
						<col width="7%">
						<col width="8%">
					<colgroup>
					<thead style="font-size:13px">
						<tr>
							<th class="text-center" style="background:#F8F8EF">NO.</th>
							<th class="text-center" style="background:#F8F8EF">품번</th>
							<th class="text-center" style="background:#F8F8EF">진행상태</th>
							<th class="text-center" style="background:#F8F8EF">상품명</th>
							<th class="text-center" style="background:#F8F8EF">목표금액</th>
							<th class="text-center" style="background:#F8F8EF;padding:0;">
								<ul class="list-inline" style="margin:0 0;">
									<li style="width:100%;padding:5px;border-bottom:1px solid #e0e0e0">모집기간</li>
									<li style="width:100%;padding:5px;">투자기간</li>
								</ul>
							</th>
							<th class="text-center"  style="padding:0;background:#F8F8EF">
								<ul class="list-inline" style="margin:0 0;">
									<li style="width:100%;padding:5px;border-bottom:1px solid #e0e0e0">이자율/연</li>
									<li style="width:100%;padding:5px;">투자일수</li>
								</ul>
							</th>
							<th class="text-center" style="background:#F8F8EF">마감일</th>
							<th class="text-center" style="background:#F8F8EF">투자자수</th>
							<th class="text-center" style="background:#F8F8EF">투자금액</th>
							<th class="text-center" style="background:#F8F8EF">기표</th>
							<th class="text-center" style="background:#F8F8EF">상환용<br>가상계좌</th>
							<th class="text-center" style="background:#F8F8EF">등록일</th>
							<th class="text-center" style="background:#F8F8EF">PROC</th>
						</tr>
					</thead>
					<tbody>
<? if($page==1) { ?>
						<tr style="background:#DDDDFF;color:navy">
							<td align="center">합계</td>
							<td><?=number_format($total_count)?>건</td>
							<td></td>
							<td></td>
							<td align="right"><?=number_format($total_recruit_amount)?>원</td>
							<td colspan="3" align="center">
								상환액 : <?=number_format($total_paid_principal)?>원 &nbsp;
								대출잔액 : <?=number_format($total_remain_principal)?>원
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
<? } ?>

<?
for ($i=0; $i<$list_count; $i++) {

		$INVEST = sql_fetch("SELECT COUNT(idx) AS cnt, SUM(amount) AS amount FROM cf_product_invest WHERE product_idx='".$LIST[$i]['idx']."' AND invest_state='Y'");

		$pstate  = '';
		$date    = date('Y-m-d H:i:s');
		$bgcolor = "";

		if($LIST[$i]['state']) {
			if($LIST[$i]['state'] == '1') {
				$pstate = '이자상환중';
				$pstate_code = '2';
			}
			else if($LIST[$i]['state'] == '2') {
				$pstate = '상품마감<br>(정상상환)';
			}
			else if($LIST[$i]['state'] == '3') {
				$pstate = '투자금<br>모집실패';
				$bgcolor = "#FFDDDD";
			}
			else if($LIST[$i]['state'] == '4') {
				$pstate = '부실';
				$bgcolor = "#FFDDDD";
			}
			else if($LIST[$i]['state'] == '5') {
				$pstate = '상품마감<br><span style="color:blue">(중도상환)</span>';
				$pstate_code = '2';
			}
			else if($LIST[$i]['state'] == '6') {
				$pstate = '대출계약취소<br>(기표전)';
				$pstate_code = '8';
				$bgcolor = "#FFDDDD";
			}
			else if($LIST[$i]['state'] == '7') {
				$pstate = '대출계약취소<br>(기표후)';
				$pstate_code = '9';
				$bgcolor = "#FFDDDD";
			}
			else if($LIST[$i]['state'] == '8') {

				$pstate = '연체중';
				$pstate_code = '9';
				$bgcolor = "#FFDDDD";

				// 매출채권K 상품 연체 예외처리
				if( in_array($LIST[$i]['idx'], array('8068','8081','8109')) ) {
					$pstate.= '<br/>(표기예외적용)';
				}

			}
		}
		else {
			if($LIST[$i]['recruit_amount'] > 0 || $LIST[$i]['isEtcCost']=='1') {
				if($LIST[$i]['open_datetime'] > $date) {
					$pstate = '상품준비중';
				}
				else {
					if( $LIST[$i]['invest_end_date'] ) {
						$pstate = ($LIST[$i]['recruit_amount'] <> $INVEST['amount']) ? '모집완료<br>(오버펀딩)' : '투자금<br>모집완료';
					}
					else {
						if($LIST[$i]['recruit_amount'] == $INVEST['amount']) {
							$pstate = '투자금<br>모집완료';
						}
						else {
							if($LIST[$i]['start_datetime'] < $date && $LIST[$i]['end_datetime'] > $date) {
								$pstate = '투자금<br>모집중';
							}
							else {
								// 모집기간 전후의 상태값
								if($LIST[$i]['end_datetime'] < $date) {
									$pstate = '투자금<br>모집실패';
									$bgcolor = "#FFDDDD";
								}
								else {
									$pstate = '대기중';
									$pstate_code = '1';
								}
							}
						}
					}
				}
			}
		}

		//대출정보 - 대출금 지급 처리 (펌뱅킹 대출금 입금 통지내역 정의 테이블 조회)
		$ROW3 = sql_fetch("SELECT IFNULL(SUM(DCA_IP_AMT),0) AS SUM_DCA_IP_AMT FROM IB_FB_P2P_DC_IP WHERE DC_NB='".$LIST[$i]['idx']."' AND EXEC_YN='Y' AND ERR_CD='00000000'");
		//echo "SELECT IFNULL(SUM(DCA_IP_AMT),0) AS SUM_DCA_IP_AMT FROM IB_FB_P2P_DC_IP WHERE DC_NB='".$LIST[$i]['idx']."' AND EXEC_YN='Y' AND ERR_CD='00000000'<br>\n";

		if($ROW3['SUM_DCA_IP_AMT']) {
				$dc_ip_result = ($ROW3['SUM_DCA_IP_AMT']==$LIST[$i]['recruit_amount']) ? '<strong style="color:green">완료</strong>' : '<strong style="color:red">금액오류</strong>';
		}
		else {
				$dc_ip_result = "";
		}


		if($LIST[$i]['invest_days'] > 0 && $LIST[$i]['invest_days'] < 30) {
			$invest_period = $LIST[$i]['invest_days'] . '일';
		}
		else {
			$invest_period = $LIST[$i]['invest_period'] . '개월';
		}

		$loan_start_datetime = get_loan_start_time($LIST[$i]['idx'],substr($LIST[$i]['loan_start_date'],0,4));

		$sms_btn_dis="disabled";
		if ($LIST[$i]["state"]=="2" or $LIST[$i]["state"]=="5") {
			$sms_row = sql_fetch("SELECT * FROM cf_product_sms WHERE product_idx='".$LIST[$i]['idx']."'");
			if ( ($sms_row["sms_end"]==NULL or $sms_row["sms_end"]=="N") and $LIST[$i]['loan_end_date']==date("Y-m-d") ) $sms_btn_dis="";
		}
?>
						<tr class="odd" style="background:<?=$bgcolor?>">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$LIST[$i]['idx']?></td>
							<td align="center">
								<?=$pstate?>
								<? if ( ($LIST[$i]["state"]=="2" or $LIST[$i]["state"]=="5") and $LIST[$i]['loan_end_date']==date("Y-m-d")) { ?>
									<a class="btn btn-sm btn-primary" style="margin-top:4px;" onclick="go_end_sms('<?=$LIST[$i][idx]?>');" <?=$sms_btn_dis?> >상환문자</a>
								<? } ?>
							</td>
							<td align="left">
								<? if($LIST[$i]['ib_trust']=='Y') { ?><span class="flag p01">예치금신탁</span><? } ?>
								<? if($LIST[$i]['ai_grp_idx']!='') { ?><span class="flag p02">자동투자</span><? } ?>
								<? if($LIST[$i]['purchase_guarantees']=='Y') { ?><span class="flag p03">채권매입보증</span><? } ?>
								<? if($LIST[$i]['advanced_payment']=='Y') { ?><span class="flag p04">이자선지급</span><? } ?>
								<? if($LIST[$i]['success_example']=='Y') { ?><span class="flag p05">투자성공사례</span><? } ?>
								<? if($LIST[$i]['popular_goods']=='Y') { ?><span class="flag p06">인기상품</span><? } ?>
								<? if($LIST[$i]['advance_invest']=='Y') { ?><span class="flag p07">사전투자</span><? } ?>
								<? if($LIST[$i]['portfolio']=='Y') { ?><span class="flag p08">포트폴리오</span><? } ?>
								<? if($LIST[$i]['isConsor']=='1') { ?><span class="flag p09">컨소시엄</span><? } ?>
								<? if($LIST[$i]['only_vip']=='1') { ?><span class="flag p10">법인전용</span><? } ?>
								<div><?=$LIST[$i]['title']?></div>
							</td>
							<td align="right">
								<?=price_cutting($LIST[$i]['recruit_amount'])?>원
							</td>
							<td align="center" style="padding:0">
								<ul class="list-inline" style="margin:0 0;">
									<li style="width:100%;padding:5px;border-bottom:1px solid #e0e0e0"><?=preg_replace("/-/", ".", $LIST[$i]['recruit_period_start'])?> ~ <?=preg_replace("/-/", ".", $LIST[$i]['recruit_period_end'])?></li>
									<li style="width:100%;padding:5px;"><a title="<?=$loan_start_datetime?>" style="color:#333333;"><?=($LIST[$i]['loan_start_date'] && $LIST[$i]['loan_start_date']!='0000-00-00') ? preg_replace("/-/", ".", $LIST[$i]['loan_start_date']).'</a> ~ '.preg_replace("/-/", ".", $LIST[$i]['loan_end_date']) : ''?></li>
								</ul>
							</td>
							<td align="center" style="padding:0">
								<ul class="list-inline" style="margin:0 0;">
									<li style="width:100%;padding:5px;border-bottom:1px solid #e0e0e0"><?=$LIST[$i]['loan_interest_rate'];?>%</li>
									<li style="width:100%;padding:5px;"><?=$invest_period?></li>
								</ul>
							</td>
							<td align="center"><?=$LIST[$i]['invest_end_date']?></td>
							<td align="right"><span style="color:<?=($INVEST['cnt']>0)?'blue':'gray';?>"><?=number_format($INVEST['cnt'])?>명</span></td>
							<td align="right">
								<span style="color:<?=($INVEST['cnt']>0)?'blue':'gray';?>"><?=number_format($INVEST['amount'])?>원</span>
								<? if($LIST[$i]['ptl_repay_amount']&& $LIST[$i]['ptl_repay_amount'] < $LIST[$i]['recruit_amount']){ ?><br/><span style="color:#FF2222">일부상환 <?=price_cutting($LIST[$i]['ptl_repay_amount'])?>원</span><? } ?>
							</td>
							<td align="center"><?=$dc_ip_result?></td>
							<td align="center">
								<?=$LIST[$i]['repay_acct_no']?>
								<? if($LIST[$i]['loan_mb_no']) { ?>
								<div style="margin-top:4px;padding:8px 0 0;font-size:12px;line-height:13px;border-top:1px dotted #AAA">
									<?=$LIST[$i]['mb_title']?><br>
									( <?=$LIST[$i]['mb_id']?> )
								</div>
								<? } ?>
							</td>
							<td align="center">
								<?=substr($LIST[$i]['insert_date'], 0, 10)?>
								<?
								if( in_array($member['mb_id'], array('admin_romrom','admin_sori9th','admin_hellosiesta','admin_sundol4','admin_foolish34')) ) {
								?>
								<a onclick="go_p2pctr(<?=$LIST[$i]['idx']?>)" class="btn btn-sm btn-<?=$LIST[$i]['loan_register_id']?'default':'warning';?>" style="margin-top:4px;">중앙기록관리</a>
								<?
								}
								?>
							</td>
							<td align="center" style="min-width:120px">
								<a href="/adm/product_investment_status.php?idx=<?=$LIST[$i]['idx']?>"" class="btn btn-sm btn-default">투자자통계</a><br />
								<a href="/adm/product/product_form.php?idx=<?=$LIST[$i]['idx']?>" class="btn btn-sm btn-primary" style="margin-top:4px;">수정</a>
								<a href="/adm/repayment/repay_calculate.php?idx=<?=$LIST[$i]['idx']?>" class="btn btn-sm btn-default" style="margin-top:4px;">정산</a>
								<!--
								<? if(!$pstate) { ?><a href="javascript:;" class="btn btn-danger" onclick="if(confirm('정말 상품을 삭제하시겠습니까?')) { location.href='./register_process.php?action=product_delete&idx=<?=$LIST[$i]['idx']?>' } ">삭제</a><? } ?>
								//-->
							</td>
						</tr>

<?
		$num--;
	}
?>

					</tbody>
				</table>
			</div>
			<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

		</div>
	</div>
</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script type="text/javascript">
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
		var f = document.frmSearch;
		f.method = 'get';
		f.target = '_self';
		f.action = '<?=$_SERVER['PHP_SELF']?>';
		f.submit();
});

$('#excelDownToProduct2').click(function() {
	if(confirm('검색결과를 엑셀시트로 다운로드 받으시겠습니까?')) {
		var f = document.frmSearch;
		f.method = 'get';
		f.target = '_blank';
		f.action = 'product_list_download.php';
		f.submit();
	}
});

// 대외보고자료 다운로드
$('#excelDownToProduct3').click(function() {
	if(confirm('검색결과를 엑셀시트로 다운로드 받으시겠습니까?')) {
		var f = document.frmSearch;
		f.method = 'get';
		f.target = '_blank';
		f.action = 'product_list_download_external.php';
		f.submit();
	}
});

// 상품정렬
function sortList(param)
{
	if($('#sort_field').val()!='') {
		url = '<?=$_SERVER['PHP_SELF']?>'
				+ '?ai_grp_idx=<?=$ai_grp_idx?>'
				+ '&gr_idx=<?=$gr_idx?>'
		    + '<?=$st_str?>'
				+ '&category=<?=$category?>'
		    + '&state=<?=$state?>'
		    + '&loan_interest_type=<?=$loan_interest_type?>'
		    + '&invest_usefee_type=<?=$invest_usefee_type?>'
		    + '&display=<?=$display?>'
		    + '&purchase_guarantees=<?=$purchase_guarantees?>'
		    + '&advanced_payment=<?=$advanced_payment?>'
		    + '&portfolio=<?=$portfolio?>'
		    + '&popular_goods=<?=$popular_goods?>'
		    + '&ib_trust=<?=$ib_trust?>'
		    + '&only_vip=<?=$only_vip?>'
		    + '&isTest=<?=$isTest?>'
		    + '&ptl_repay_prdt=<?=$ptl_repay_prdt?>'
				+ '&date_field=<?=$date_field?>'
		    + '&sdate=<?=$sdate?>'
		    + '&edate=<?=$edate?>'
		    + '&field=<?=$field?>'
		    + '&keyword=<?=$keyword?>'
		    + '&sort_field=' + $('#sort_field').val()
		    + '&sort=' + param
		$(location).attr('href', url);
	}
	else {
		alert('정렬필드를 선택하십시요.'); $('#sort_field').focus();
	}
}

// 투자상품 목록 엑셀정리 저장
function excelDownToProduct(obj) {
	if( confirm("투자상품 취급물건을 Excel 문서로 다운로드 받으시겠습니까?") ) {
		obj.buttonLoader('start');

		var searchParams = window.location.search.substr(1) || {};
		var href = g5_admin_url + '/product/productExcelDownload.php';
		var token = get_ajax_token();

		setTimeout(function(){
			$.ajax({
				url : href,
				type : 'post',
				data : {searchParams: searchParams, token: token},
				cache : true,
				dataType: "json",
				async: false,
				success : function(data)
				{
					if (data.error) {
						alert(data.message);
						return false;
					}
					else if (data.success) {
						obj.buttonLoader('stop');
						var form = document.createElement("form");
						form.method = "post";
						form.action = data.excelFileUrl;
						document.body.appendChild(form);
						form.submit();
						document.body.removeChild(form);
						return true;
					}
				}
			});
		}, 1100);
	}
}

$('#excelDownToProduct').click(function() {
		var btn = $(this);
		excelDownToProduct($(btn));
});

$('#excelDownToProduct').attr("disabled", false);
$.fn.buttonLoader = function (action) {
		var self = $(this);
		if (action == 'start') {
				if ($(self).attr("disabled") == "disabled") {
						return false;
				}
				$('#excelDownToProduct').attr("disabled", true);
				$(self).attr('data-btn-text', $(self).text());

				var text = '다운중';

				$(self).html('<span class="spinner"><img src="/shop/img/loading.gif" alt="Loading..." width="14px" style="margin-right: 8px;"/></img></span>' + text);
				$(self).addClass('active');

				console.log($(self).attr("disabled"));

		}
		if (action == 'stop') {
				$(self).html($(self).attr('data-btn-text'));
				$(self).removeClass('active');
				$('#excelDownToProduct').attr("disabled", false);
		}
}

function go_end_sms(prd_idx) {
	//var yn = confirm("작업중입니다.\n계속 진행하시겠습니까?");
	//if (yn) {
		var w_sms = window.open(g5_admin_url+"/sms_prd_end.php?prd_idx="+prd_idx , "sms", "left=500px, top=100px, width=500,height=700");
	//}
}

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<? include_once ('../admin.tail.php'); ?>