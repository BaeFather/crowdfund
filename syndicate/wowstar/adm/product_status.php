<?

include_once("../syndication_config.php");
include_once('../html/member/head.php');

if(!$is_admin && !$_SESSION['syndi_admin_login']) {
	msg_go('로그인 하십시요.', './');
}

$syndi_id = $_CONF['SYNDI_ID'];


$g5['title'] = '관리자 > 투자현황';


$sql_search = " AND B.display='Y' AND B.scrap_out='' AND B.isTest='' AND B.only_vip=''";
$sql_search.= " AND (B.category IN(1,2) OR (B.category='3' AND B.category2='2'))";
$sql_search.= " AND (A.invest_state = 'Y' OR A.invest_state = 'R')";		// 정상투자금 및 대출취소건에 대한 투자금

$sql_group = " GROUP BY A.product_idx ";
$sql_order = " ORDER BY B.open_datetime DESC, B.start_num DESC, A.product_idx DESC";

$sql = "
	SELECT
		COUNT(A.product_idx) AS cnt
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		$sql_search
		$sql_group";
$res = sql_query($sql);
$total_count = sql_num_rows($res);


$rows = 10;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "
	SELECT
		A.*,
		IFNULL(SUM(A.amount),0) AS amount,
		B.state, B.category, B.gr_idx, B.title, B.invest_return, B.invest_period, B.recruit_amount, B.repay_type, B.loan_start_date, B.loan_end_date
	FROM
		cf_product_invest A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		$sql_search
		$sql_group
		$sql_order
	LIMIT
		$from_record, $rows";
$result = sql_query($sql);
$rcount = sql_num_rows($result);
for ($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);

	$sql2 = "
		SELECT
			COUNT(A.idx) AS invest_count,
			IFNULL(SUM(A.amount),0) AS invest_amount
		FROM
			cf_product_invest A
		INNER JOIN
			g5_member B  ON A.member_idx=B.mb_no
		WHERE 1
			AND A.product_idx='".$LIST[$i]['product_idx']."'
			AND A.invest_state='Y'
			AND B.syndi_id='".$syndi_id."'";
	if(substr($LIST[$i]['insert_date'], 0, 7) > '2018-06') {		// 2018년 6월 이후부터 확정매출채권 자동투자내역은 투자통계에서 제외
		$sql2.= " AND A.category!='3'";
	}


	$row = sql_fetch($sql2);

	$LIST[$i][$syndi_id]['invest_count']  = $row['invest_count'];
	$LIST[$i][$syndi_id]['invest_amount'] = $row['invest_amount'];
}

$num = $total_count - $from_record;

?>

		<link href="/adm/css/bootstrap.min.css" rel="stylesheet">
		<style>
		.tblX { width:100%; border:1px solid #ccc }
		.tblX th,
		.tblX td { padding:8px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
		.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
		.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
		.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
		.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
		.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
		.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }
		span.left  { float:left; }
		span.right { float:right; }
		.new {padding:0 6px 2px 6px; font-size:8pt; color:#fff; border:0px; background-color:red; border-radius:10px; margin:0 4px;}
		</style>

		<div id="content" style="position:absolute;">
			<div class="content investment" style="width:98%;margin:-50px auto;">

				<ul class="tab_type03" style="margin:0">
					<li data-gubun="tab1" class="on">상품 투자 현황</li>
					<li data-gubun="tab2" onClick="location.href='member_status.php'">가입자 현황</li>
					<li data-gubun="tab3" onClick="location.href='invest_status.php'">가입.투자 통계</li>
					<li data-gubun="tab4" style="float:right;text-align:right;border:0;background:#FFF;"><button type="button" class="btn_gray" onClick="location.href='./'">로그아웃</button></li>
				</ul>

				<div class="tabArea" style="display:block;padding:30px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">

					<table class="tblX">
						<colgroup>
							<col width="60">
							<col width="">
							<col width="12%">
							<col width="8%">
							<col width="6%">
							<col width="15%">
							<col width="8%">
							<col width="6%">
							<col width="6%">
							<col width="6%">
						</colgroup>
						<thead>
						<tr style="background-color:#EFEFEF">
							<th scope="col" rowspan="2" style="text-align:center;width:60px">번호</th>
							<th scope="col" rowspan="2" style="text-align:center;">펀딩상품명</th>
							<th scope="col" rowspan="2" style="text-align:center;">모집금액</th>
							<th scope="col" rowspan="2" style="text-align:center;">금리(연)</th>
							<th scope="col" colspan="2" style="text-align:center;">투자기간</th>
							<th scope="col" rowspan="2" style="text-align:center;">진행현황</th>
							<th scope="col" rowspan="2" style="text-align:center;">모집율</th>
							<th scope="col" colspan="2" style="text-align:center;"><b><?=$syndi_id?></b></th>
						</tr>
						<tr style="background-color:#EFEFEF">
							<th scope="col" style="text-align:center;width:80px">개월</th>
							<th scope="col" style="text-align:center;">날짜</th>
							<th scope="col" style="text-align:center;">참여자수</th>
							<th scope="col" style="text-align:center;">투자금액</th>
						</tr>
						</thead>
						<tbody>
<?
if($num > 0) {
	for($i=0,$j=1; $i<$rcount; $i++,$j++) {
		$bgcolor = ($j%2==1) ? '' : '#FAFAFA';


		if($LIST[$i]['state']) {
			if ($LIST[$i]['state'] == '1')     $state = '이자상환중';
			else if($LIST[$i]['state'] == '2') $state = '상품마감<br>(정상상환)';
			else if($LIST[$i]['state'] == '3') $state = '투자금<br>모집실패';
			else if($LIST[$i]['state'] == '4') $state = '부실';
			else if($LIST[$i]['state'] == '5') $state = '상품마감<br><span style="color:blue">(중도상환)</span>';
			else 	if ($LIST[$i]['state'] == '6') {
				$state = '대출계약취소';
				$state_code = '8';
				$bgcolor = "#FFDDDD";
			}
		}
		else {
			if($LIST[$i]['open_datetime'] > $date) {
				$state = '상품준비중';
			}
			else {
				if($LIST[$i]['invest_end_date'] == '') {
					if($LIST[$i]['end_datetime'] < $date){
						$state = '투자금<br>모집실패';
						$bgcolor = "#FFDDDD";
					}
					else {
						$state = '대기중';
					}
				}
				if($LIST[$i]['start_datetime'] < $date && $LIST[$i]['end_datetime'] > $date) {
					$state = ($LIST[$i]['recruit_amount'] == $LIST[$i]['amount']) ? '투자금<br>모집완료' : '투자금<br>모집중';
				}
			}
		}


		if( preg_match("/0000-00-00/", $LIST[$i]['loan_start_date']) || preg_match("/0000-00-00/", $LIST[$i]['loan_end_date']) ) {
			$loan_date_range = "";
		}
		else {
			$loan_date_range = preg_replace("/-/", ".", $LIST[$i]['loan_start_date'])." ~ ".preg_replace("/-/", ".", $LIST[$i]['loan_end_date']);
		}

		$invest_perc = @sprintf('%.2f', $LIST[$i]['amount']/$LIST[$i]['recruit_amount']*100);

?>
							<tr height="53" bgcolor="<?=$bgcolor?>">
								<td style="text-align:center;"><?=$num?></td>
								<td><?=$LIST[$i]['title']?></td>
								<td style="text-align:right;"><?=number_format($LIST[$i]['recruit_amount'])?>원</td>
								<td style="text-align:center;"><?=$LIST[$i]['invest_return']?>%</td>
								<td style="text-align:center;"><?=$LIST[$i]['invest_period']?>개월</td>
								<td style="text-align:center;"><?=$loan_date_range?></td>
								<td style="text-align:center;"><?=$state?></td>
								<td style="text-align:center;"><?=$invest_perc?>%</td>
								<td style="text-align:right;"><?=number_format($LIST[$i][$syndi_id]['invest_count'])?>명</td>
								<td style="text-align:right;"><?=number_format($LIST[$i][$syndi_id]['invest_amount'])?>원</td>
							</tr>
<?
		$num--;
	}
}
else {
?>
							<tr height="53">
								<td colspan="10" style="text-align:center">데이터가 없습니다.</td>
							</tr>
<?
}
?>
						</tbody>
					</table>

					<div style="padding-top:10px">
<?
$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
					</div>

				</div>

			</div>
		</div>

<?
include_once('../html/member/tail.php');
?>