<?

include_once("../syndication_config.php");
include_once('./head.php');

if(!$is_admin && !$_SESSION['syndi_admin_login']) {
	msg_go('로그인 하십시요.', './');
}

$syndi_id = $_CONF['SYNDI_ID'];


$g5['title'] = '관리자 > 가입.투자 통계';

foreach($_REQUEST as $k=>$v) {
	$$_REQUEST[$k] = $v;
}

$nowym = date('Y-m');

$sym = (trim($sym)) ? $sym : date('Y-m', strtotime('first day of -5 month', strtotime($nowym.'-01')));
$eym = (trim($eym)) ? $eym : date("Y-m");

$sql_search = " 1=1 ";
$sql_search.= " AND A.invest_state='Y' ";
//$sql_search.= " AND B.chosun_userid!='' ";
$sql_search.= " AND A.syndi_id='$syndi_id' ";
//$sql_search.= " AND B.syndi_userid='$syndi_id' ";
if($sym && $eym) {
	if($sym < $eym) {
		$sql_search.= " AND LEFT(A.insert_date, 7) BETWEEN '$sym' AND '$eym' ";
		$sql_search2 = " AND LEFT(mb_datetime, 7) BETWEEN '$sym' AND '$eym' ";
	}
	else {
		$sql_search.= " AND LEFT(A.insert_date, 7)='$sym' ";
		$sql_search2 = " AND LEFT(mb_datetime, 7)='$sym' ";
	}
}

// 자동투자상품의 투자이력은 제외해야하므로 자동투자상품상품번호 배열을 가져와서 회원의 투자이력에서 해당 상품의 투자내역은 제외하도록 처리
$sql0 = "SELECT idx FROM cf_product WHERE display='Y' AND ai_grp_idx!='' AND category='3' ORDER BY idx";
$res0 = sql_query($sql0);
$auto_invest_idx = "";
while($row = sql_fetch_array($res0)) {
	if($row) {
		$auto_invest_idx.= "'". $row['idx'] . "',";
	}
}
$auto_invest_idx = substr($auto_invest_idx, 0, -1);


$sql = "
	SELECT
		A.idx, A.amount, LEFT(A.insert_date, 7) AS insert_date
	FROM
		cf_product_invest A
	LEFT JOIN
		g5_member B
	ON
		A.member_idx=B.mb_no
	WHERE
		$sql_search
	ORDER BY
		A.insert_date desc,
		A.idx";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql);
$rcount = sql_num_rows($result);
$LIST = array();
for($i=0; $i<$rcount; $i++) {
	$ROW = sql_fetch_array($result);
	$TMP[$ROW['insert_date']]['date'] = $ROW['insert_date'];
	$TMP[$ROW['insert_date']]['invest_cnt'] += 1;
	$TMP[$ROW['insert_date']]['invest_amount'] += $ROW['amount'];
}

if (is_array($TMP)) 
	$ARR_KEYS = array_keys($TMP);

for($i=0; $i<count($TMP); $i++) {
	$LIST[$i] = $TMP[$ARR_KEYS[$i]];

	$ROW = sql_fetch("SELECT COUNT(mb_no) AS join_cnt FROM g5_member WHERE (1) AND syndi_id='$syndi_id' AND LEFT(mb_datetime, 7)='".$LIST[$i]['date']."'");
	$LIST[$i]['join_cnt'] = $ROW['join_cnt'];


	// 2018년 6월 이후데이터 부터 확정매출채권(자동투자)상품에 대한 투자건수,금액 제외 (시킨사람 류재영차장 2018-07-26)
	if($LIST[$i]['date'] > '2018-06') {

		$AUTO_SQL = "SELECT
				COUNT(A.idx) AS invest_cnt,
				IFNULL(SUM(A.amount),0) AS sum_invest_amount
			FROM
				cf_product_invest A
			LEFT JOIN
				g5_member B  ON A.member_idx=B.mb_no
			WHERE 1
				AND A.syndi_id='".$_CONF['SYNDI_ID']."'
				AND A.invest_state='Y'
				AND A.product_idx IN($auto_invest_idx)
				AND LEFT(A.insert_date,7)='".$LIST[$i]['date']."'";
		//echo "$AUTO_SQL<br/>";
		$AUTO = sql_fetch($AUTO_SQL);
	}


	$LIST[$i]['invest_cnt'] -= $AUTO['invest_cnt'];
	$LIST[$i]['invest_amount'] -= $AUTO['sum_invest_amount'];
	$LIST[$i]['invest_cms'] = $LIST[$i]['invest_amount'] * 0.008;

	$TOTAL['join_cnt']      += $LIST[$i]['join_cnt'];
	$TOTAL['invest_cnt']    += $LIST[$i]['invest_cnt'];
	$TOTAL['invest_amount'] += $LIST[$i]['invest_amount'];
	$TOTAL['invest_cms']    += $LIST[$i]['invest_cms'];

}

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
					<li data-gubun="tab1" onClick="location.href='product_status.php'">상품 투자 현황</li>
					<li data-gubun="tab2" onClick="location.href='member_status.php'">가입자 현황</li>
					<li data-gubun="tab3" class="on">가입.투자 통계</li>
					<li data-gubun="tab4" style="float:right;text-align:right;border:0;background:#FFF;"><button type="button" class="btn_gray" onClick="location.href='./'">로그아웃</button></li>
				</ul>

				<div class="tabArea" style="display:block;padding:30px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">

					<div style="margin-bottom:10px;">
						<form id="member_list_frm" method="get">
						<span style="margin-right:15px;">
							검색기간
							<select name="sym" value="<?=$sym?>" class="frm_input" style="margin-left:10px;margin-right:10px;width:100px">
<?
$fym = '2017-08-01';
$x = true;
$y = 0;
while($x > 0) {
	$ym = date('Y-m', strtotime('first day of +'.$y.' month', strtotime($fym)));
	$selected = ($ym==$sym) ? 'selected' : '';
	echo '<option value="'.$ym.'" '.$selected.'>'.$ym.'</option>' . PHP_EOL;
	if($ym==$nowym) break;
	$y++;
}
?>
							</select> ~
							<select name="eym" value="<?=$eym?>" class="frm_input" style="margin-left:10px;width:100px">
<?
$fym = '2017-08-01';
$x = true;
$y = 0;
while($x > 0) {
	$ym = date('Y-m', strtotime('first day of +'.$y.' month', strtotime($fym)));
	$selected = ($ym==$eym) ? 'selected' : '';
	echo '<option value="'.$ym.'" '.$selected.'>'.$ym.'</option>' . PHP_EOL;
	if($ym==$nowym) break;
	$y++;
}
?>
							</select>
							<button type="submit" class="btn_blue">검 색</button>
							<button type="button" class="btn_gray" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">초기화</button>
						</span>
						</form>
					</div>

					<table class="tblX">
						<thead>
							<tr style="background-color:#EFEFEF">
								<th scope="col" style="width:20%;text-align:center;">Date</th>
								<th scope="col" style="width:20%;text-align:center;">가입자수</th>
								<th scope="col" style="width:20%;text-align:center;">투자건수</th>
								<th scope="col" style="width:20%;text-align:center;">투자금액</th>
								<th scope="col" style="width:20%;text-align:center;">수수료</th>
							</tr>
						</thead>
						<tbody>
<?
$list_count = count($LIST);
if($list_count > 0) {
	?>
							<tr bgcolor="#FFDDDD">
								<td style="text-align:center;color:brown;">합계</td>
								<td style="text-align:right;color:brown;"><?=number_format($TOTAL['join_cnt'])?> 건</td>
								<td style="text-align:right;color:brown;"><?=number_format($TOTAL['invest_cnt'])?> 건</td>
								<td style="text-align:right;color:brown;"><?=number_format($TOTAL['invest_amount'])?> 원</td>
								<td style="text-align:right;color:brown;"><?=number_format($TOTAL['invest_cms'])?> 원</td>
							</tr>
<?
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {		
		?>
							<tr bgcolor="<?=$tr_bgcolor?>">
								<td style="text-align:center;"><?=$LIST[$i]['date']?></td>
								<td style="text-align:right;"><?=number_format($LIST[$i]['join_cnt'])?> 건</td>
								<td style="text-align:right;"><?=number_format($LIST[$i]['invest_cnt'])?> 건</td>
								<td style="text-align:right;"><?=number_format($LIST[$i]['invest_amount'])?> 원</td>
								<td style="text-align:right;"><?=number_format($LIST[$i]['invest_cms'])?> 원</td>
							</tr>
<?
	}
}
else {
?>
							<tr>
								<td colspan="5" style="text-align:center">데이터가 없습니다.</td>
							</tr>
<?
}
?>
						</tbody>
					</table>

				</div>

			</div>
		</div>

<?
include_once('./tail.php');
?>