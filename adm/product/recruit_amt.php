<?
/**
 * 투자상품 목록
 */
$sub_menu = "600500";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_REQUEST)) {
	if(!is_array(${$key})) ${$key} = trim($value);
}

if(!$srch_gubun) $srch_gubun = 'loan_end_date';
if(!$todate) $todate = date('Y-m-d');

$where = " AND A.display='Y' AND A.isTest=''";
$where.= ($srch_gubun=='state') ? " AND A.state IN('1','4','8','9')" : "";

if ($srch_cat=="ma") $where .= " AND A.category='3'";
else if ($srch_cat=="do") $where .= " AND A.category='1'";
else {
	$where.= " AND A.category='2'";

	if($srch_cat) {
		$where.= ($srch_cat=="ju") ? " AND A.mortgage_guarantees='1'" : " AND A.mortgage_guarantees=''";
	}
}
//if($todate) $where.= " AND (A.loan_start_date<='".$todate."' AND A.loan_end_date>='".$todate."')";
if($todate) $where.= " AND (A.loan_start_date<='".$todate."' AND A.loan_end_date>'".$todate."')";


if($sort_field=='') $sort_field = 'A.loan_end_date';
if($sort=='') $sort = 'ASC';

$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort."";
	if(in_array($sort_field, array('A.loan_start_date','A.loan_end_date'))) {
		$sql_order.= ", A.start_num ASC";
	}
}
else {
	$sql_order.= " A.loan_end_date ASC,";
	$sql_order.= " A.loan_start_date ASC,";
	$sql_order.= " A.start_num ASC";
}



$sql = "
	SELECT
		A.*,
		(SELECT `date` FROM cf_product_success WHERE product_idx=A.idx AND invest_principal_give='Y') AS finished_date,
		(SELECT SUM(amount) FROM cf_partial_redemption WHERE product_idx=A.idx AND account_day <= '".$todate."') AS paid_amount
		-- (SELECT SUM(principal) FROM cf_product_give WHERE product_idx=A.idx AND LEFT(banking_date,10) <= '".$todate."') AS paid_amount
	FROM
		cf_product A
	WHERE 1
		$where
	ORDER BY
		$sql_order";

$sql = "
	SELECT
		A.*,
		(SELECT MAX(`date`) FROM cf_product_success WHERE product_idx=A.idx AND invest_principal_give='Y' ) AS finished_date,
		(SELECT SUM(amount) FROM cf_partial_redemption WHERE product_idx=A.idx AND account_day <= '".$todate."') AS paid_amount
		-- (SELECT SUM(principal) FROM cf_product_give WHERE product_idx=A.idx AND LEFT(banking_date,10) <= '".$todate."') AS paid_amount
	FROM
		cf_product A
	WHERE 1
		$where
	ORDER BY
		$sql_order";

//print_rr($sql,'font-size:12px;line-height:14px;');
$res = sql_query($sql);
$rows = $res->num_rows;

$TOTAL['count'] = $rows;
$TOTAL['recruit_amount'] = 0;
for ($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['balance_amount'] = $LIST[$i]['recruit_amount'] - $LIST[$i]['paid_amount'];

	$TOTAL['recruit_amount'] += $LIST[$i]['recruit_amount'];
	$TOTAL['paid_amount']    += $LIST[$i]['paid_amount'];				// 조윤주대리 요청으로 부분상환 등록금액을 기준으로 잔액 반영함 (투자자 지급기준 아님 - 배부장)
	$TOTAL['balance_amount'] += $LIST[$i]['balance_amount'];
}

$list_count = count($LIST);
$num = $list_count;

$g5['title'] = $menu['menu600'][4][1];
include_once('../admin.head.php');
?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel-body" style="padding:0 1% 0 1%;">

			<!-- 검색영역 START -->
			<div style="line-height:28px;">
				<form id="frmSearch" name="frmSearch" method="get">
				<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select name="srch_gubun" class="form-control input-sm" style="width:180px;">
							<option value="">:: 조회기준 ::</option>
							<option value="loan_end_date" <?=($srch_gubun=="loan_end_date")?"selected":""?> >대출만료일기준</option>
							<option value="state" <?=($srch_gubun=="state")?"selected":""?> >상태값기준</option>
						</select>
					</li>
					<li>
						<select name="srch_cat" class="form-control input-sm" style="width:180px;">
							<option value="">부동산 전체</option>
							<option value="ju" <?=($srch_cat=="ju")?"selected":""?> >-부동산(주택담보대출)</option>
							<option value="bu" <?=($srch_cat=="bu")?"selected":""?> >-부동산(PF대출)</option>
							<option value="ma" <?=($srch_cat=="ma")?"selected":""?> >-매출채권</option>
							<option value="do" <?=($srch_cat=="do")?"selected":""?> >-동산</option>
						</select>
					</li>
					<li></li>
					<li>조회기준일</li>
					<li><input type='text' name='todate' value="<?=$todate?>" class="form-control input-sm datepicker" style="width:90px;display:inline;" placeholder="조회기준일" autocomplete='no' /></li>
					<li><input type='submit' class="btn btn-sm btn-warning" value=' 검색 '></li>
				</ul>
				</form>
				<ul class="col col-md-* list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
					<li>
						<select id="sort_field" class="form-control input-sm" style="width:150px;">
							<option value="">:: 정렬필드 ::</option>
							<option value="A.idx" <? if($sort_field == 'A.idx'){echo 'selected';} ?>>품번</option>
							<option value="A.start_num" <? if($sort_field == 'A.start_num'){echo 'selected';} ?>>호번</option>
							<option value="A.loan_start_date" <? if($sort_field == 'A.loan_start_date'){echo 'selected';} ?>>대출실행일</option>
							<option value="A.loan_end_date" <? if($sort_field == 'A.loan_end_date'){echo 'selected';} ?>>대출만료일</option>
						</select>
					</li>
					<li>
						<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
						<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
					</li>
				</ul>
			</div>
			<!-- 검색영역 E N D -->

			<script>
			function sortList(param) {
				if(document.getElementById('sort_field').value!='') {
					url = '?srch_gubun=<?=$srch_gubun?>'
							+ '&srch_cat=<?=$srch_cat?>'
							+ '&todate=<?=$todate?>'
							+ '&sort_field=' + document.getElementById('sort_field').value
							+ '&sort=' + param
					location.href= url;
				}
				else {
					alert('정렬필드를 선택하십시요.'); document.getElementById('sort_field').focus();
				}
			}
			</script>

			<div class="dataTable_wrapper" style="margin-top:15px;">
				<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;">
					<thead style="font-size:13px">
						<tr>
							<th class="text-center" style="background-color:#F8F8EF">NO.</th>
							<th class="text-center" style="background-color:#F8F8EF">품번</th>
							<th class="text-center" style="background-color:#F8F8EF">상품명</th>
							<th class="text-center" style="background-color:#F8F8EF">기표금액</th>
							<th class="text-center" style="background-color:#F8F8EF">일부상환금액</th>
							<th class="text-center" style="background-color:#F8F8EF">대출잔액</th>
							<th class="text-center" style="background-color:#F8F8EF">대출실행일</th>
							<th class="text-center" style="background-color:#F8F8EF">대출만료일</th>
							<th class="text-center" style="background-color:#F8F8EF">잔여일</th>
							<th class="text-center" style="background-color:#F8F8EF">상환완료일</th>
							<th class="text-center" style="background-color:#F8F8EF">상태구분</th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd" style="background:#FFDDDD">
							<td align="center">합계</td>
							<td colspan="2" align="right"><?=number_format($TOTAL['count'])?>건</td>
							<td align="right" title="<?=price_cutting($TOTAL['recruit_amount'])?>원"><?=number_format($TOTAL['recruit_amount'])?>원</td>
							<td align="right"><?=number_format($TOTAL['paid_amount'])?>원</td>
							<td align="right"><?=number_format($TOTAL['balance_amount'])?>원</td>
							<td colspan="5" align="center"></td>
						</tr>
<?
for($i=0; $i<$list_count; $i++) {

	$rest_day_count = ceil((strToTime($LIST[$i]['loan_end_date'])-time())/86400);
	$fcolor = ($rest_day_count > 0) ? '' : '#AAA';
	switch($LIST[$i]['state']) {
		case '1' : $print_state = '이자상환중'; break;
		case '2' : $print_state = '상환완료(투자종료)'; break;
		case '3' : $print_state = '투자금모집실패'; break;
		case '4' : $print_state = '부실'; break;
		case '5' : $print_state = '중도상환'; break;
		case '6' : $print_state = '대출취소(기표전)'; break;
		case '7' : $print_state = '대출취소(기표후)'; break;
		case '8' : $print_state = '연체'; break;
		case '9' : $print_state = '부도(상환불가)'; break;
	}


?>
						<tr class="odd" style="color:<?=$fcolor?>">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$LIST[$i]["idx"]?></td>
							<td align="left"><?=$LIST[$i]["title"]?></td>
							<td align="right" title="<?=price_cutting($LIST[$i]['recruit_amount'])?>원"><?=number_format($LIST[$i]['recruit_amount'])?>원</td>
							<td align="right"><?=number_format($LIST[$i]['paid_amount'])?>원</td>
							<td align="right"><?=number_format($LIST[$i]['balance_amount'])?>원</td>
							<td align="center"><?=$LIST[$i]["loan_start_date"]?></td>
							<td align="center"><?=$LIST[$i]["loan_end_date"]?></td>
							<td align="center"><?=max(0,$rest_day_count)?>일</td>
							<td align="center"><?=$LIST[$i]['finished_date']?></td>
							<td align="center"><?=$print_state?></td>
						</tr>
<?
	$num--;
}
?>
					</tbody>
				</table>
			</div>
			<!--<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>-->
		</div>
		<!-- /.panel-body -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<script>
$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<? include_once ('../admin.tail.php'); ?>